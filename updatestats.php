
<?php
		//ob_end_flush();
		//ob_start();
		require_once("patchesrecorded.php");
		require_once("regionsrecorded.php");
		require_once("itemsrecorded.php");
		require_once("connect.php");
		require_once("password.php");
		set_time_limit(360000);
		date_default_timezone_set("Europe/Prague");
		$matchid = array();
		$numberofregions = 0;
		if(isset($_POST["pw"]))
		{
		if($_POST["pw"] == $password)
		{
		if(isset($_POST["matches"]))
		{
		echo "posted through form<br>";
		foreach($regionsrecorded as $key => $rr)
		{
			$reg = $rr[0];
			if(!isset($_POST["$reg"])) //checkboxes left unchecked are not sent at all, that means they don't exist
			{
				$regionsrecorded[$key][1] = false;
				echo $rr[0]." was disabled<br>";
			}
			else
			{
				$numberofregions++;
			}
			$matchestodownloadineachregion = $_POST["matches"];
		}
		}
		else
		{
			$numberofregions = 10;
			$matchestodownloadineachregion = 5;
		}

		$totalplayers = 0;
		$totalmatchestodownload = $numberofregions*$matchestodownloadineachregion*2; //times patches, this thing is almost never accurate whenever an error is thrown during this entire process (hint:always), but it's a decent estimation
		$matchesdownloadprogress = 0;
		$gamemode = "RANKED_SOLO";
		$itemsinfo = array();
		$ij = 0; //ij so it's not confused with any other loop
		foreach($itemsrecorded as $it)
		{
			$itemsinfo[$ij][0] = $it[0];//item id
			$itemsinfo[$ij][1] = 0; //popularity
			$itemsinfo[$ij][2] = 0; //won games
			$itemsinfo[$ij][3] = 0; //lost games, no longer relevant
			$itemsinfo[$ij][4] = array(); //timestamp purchases array for median calculation
			$ij++;
		}
		$starttime = microtime(true);
		$secondtimelimitdefault = 10;
		$secondtimelimit = $secondtimelimitdefault + 0.25;
		$secondrequestlimit = 10;
		$secondrequestsremaining = $secondrequestlimit;
		$minutetimelimitdefault = 600;
		$minutetimelimit = $minutetimelimitdefault + 0.25;
		$minuterequestlimit = 500;
		$minuterequestsremaining = $minuterequestlimit;
		$prevdeltatime = 0; //some kind of self limit mechanism on the number of API requests with some breathing room, I've never found out if it works properly, since my connection could never go over the dev key threshold
		foreach($regionsrecorded as $rr)
		{
		if($rr[1] == true)
		{
		foreach($patchesrecorded as $pr)
		{
		$matchestogo = $matchestodownloadineachregion;
		$reguppercase = strtoupper($rr[0]);
		$patchtrimmed = substr($pr, 0, 4);
		$matchlistdir = "$patchtrimmed/$gamemode/$reguppercase.json"; //directory of the matches Riot provided
		$matchlist = json_decode(file_get_contents($matchlistdir));
		$validlist = false;
		for($i = 0; $i<count($matchlist) && !$validlist; $i++)
		{
		$m_id = $matchlist[$i];
		$region = $rr[0];
		$query = $mysqli->query("SELECT * FROM `scannedmatches` WHERE `match_id` = $m_id AND `region` = '$region'");
		if($query)
		{
			if($query->num_rows < 1) //prevents scanning matches from the file more than once without having to manipulate the original file
			{
			array_push($matchid, $m_id);
			$matchestogo--;
			if($matchestogo <=0)
			{
				$validlist = true;
			}
			}
		}
		else
		{
			echo "Match db search error:".$mysqli->error."<br>";
		}
		}
		foreach($matchid as $m)
		{
		$region = $rr[0];
		$patch = $pr;
		if(checkDuplicateMatch($m, $region) == false) //we don't want to check duplicate matches rite; do I actually check it twice? I don't even know now
		{
		$objectvalidated = false; //in case the http request goes wrong	
		$deltatime = microtime(true) - $starttime - $prevdeltatime;
		$prevdeltatime += $deltatime; //more of the timer stuff
		$useful = 1;
		require_once("apikey.php");
		$jsonurl = "https://$region.api.pvp.net/api/lol/$region/v2.2/match/$m?includeTimeline=true&api_key=$apikey";
		$json = requestJsonTry($jsonurl, 1, 5);
		if(!$json){
			$useful = 0; //if the request fails, the match is useless, doesn't always mean a 404, but it usually is, so it's fine
		}
		$secondrequestsremaining--;
		$secondtimelimit-=$deltatime;
		//echo "deltatime is $deltatime<br>";
		$minuterequestsremaining--;
		$minutetimelimit-=$deltatime; //more timer mumbo jumbo I don't even know if it works properly
		$obj = json_decode($json, true);
		//echo "finished requesting an object<br>";
		/*ob_flush();
		flush();*/
		foreach($obj["participants"] as $player)
		{
			foreach($itemsinfo as $key => $if)
			{
				$foundonplayer = false; //if we find multiples of the same item, don't count them more than once
				for($i = 0; $i <=6 && !$foundonplayer; $i++)
				{
					if($player["stats"]["item$i"] == $if[0])
					{
						if($player["stats"]["winner"])
						{
							$itemsinfo[$key][2]++;
						}
						else
						{
							$itemsinfo[$key][3]++; //irrelevant now, but hey, who cares
						}
						
						$itemsinfo[$key][1]++; //we need to change the value of the original array
						$foundonplayer = true;
						
					}
				
				}
			}
			$totalplayers++;
			$objectvalidated = true; //object is validated, can mark the match as useful now
		}
		//echo "IT WORKS! <br>";
		$lastkey = -1;
		foreach($obj["timeline"]["frames"] as $frame)
		{
			if(isset($frame["events"]))
			{
				foreach($frame["events"] as $event)
				{
					if($event["eventType"] == "ITEM_PURCHASED")
					{
						foreach($itemsinfo as $key => $if)
						{
							if($event["itemId"] == $if[0])
							{
								array_push($itemsinfo[$key][4], $event["timestamp"]); //check the entire timeline, if the event is equal to "ITEM_PURCHASED", go ahead and add it to the original array
							}
						}
					}
				}
			}
		}
		
		if($objectvalidated)
		{
			foreach($itemsinfo as $key => $if)
			{
				$id = $if[0];
				$cond = "WHERE `item_id` = $id AND `region` = '$region' AND `patch` = '$patch'";
				$statement = "SELECT * FROM `itemstats` $cond";
				$query = $mysqli->query($statement);
				if($query)
				{
					if($query->num_rows > 0)
					{
					$obj = $query->fetch_object();
					}
				}
				else
				{
					echo $mysqli->error;
				}
				//IF THE ACTUAL ITEM IS FOUND
				if(isset($obj))
				{
				
				if($if[1] != 0) //popularity
				{
					$popularity = $obj->popularity+$if[1];
					$query2 = $mysqli->query("UPDATE `itemstats` SET `popularity` = $popularity $cond");
					if($query2)
					{
					//	echo "Successfully updated $id popularity to $popularity<br>";
					}
					else
					{
						echo "Error while updating popularity:".$mysqli->error."<br>";
					}
					$itemsinfo[$key][1] = 0;
				}
				if($if[2] != 0) //wongames
				{
					$winrate = $obj->winrate + $if[2];
					$query2 = $mysqli->query("UPDATE `itemstats` SET `winrate` = $winrate $cond");
					if($query2)
						{
						//	echo "Successfully updated $id winrate to $winrate<br>";
						}
						else
						{
							echo "Error while updating winrate:".$mysqli->error."<br>";
						}
					$itemsinfo[$key][2] = 0;
				}
				if($if[3] != 0) //lostgames
				{
				}
				if(isset($if[4][0])) //timestamps
				{
					$timestamps = json_decode($obj->purchase_timestamps);
					if($timestamps[0] == 0)
					{
						unset($timestamps);
						$timestamps = array(); //[0] is the default value of timestamps in the DB when filled, let's just remake the thing from scratch, why don't we
					}
					foreach($if[4] as $t)
					{
						array_push($timestamps, $t); //then push more info inside
					}
					$jsontimestamps = json_encode($timestamps);
					//print_r($jsontimestamps);
					$query2 = $mysqli->query("UPDATE `itemstats` SET `purchase_timestamps` = '$jsontimestamps' $cond"); //and give it back to the DB, still retaining all the data
					if($query2)
					{
					/*	echo "Successfully updated timestamps for item $id, sending";
						print_r($timestamps);
						echo "<br>";*/
					}
					else
					{
						echo "Error while updating timestamps:".$mysqli->error."<br>";
					}
					unset($itemsinfo[$key][4]);
					$itemsinfo[$key][4] = array(); //unset and make another array for clear values of more timestamps
				}
				}
			}
			$matchesdownloadprogress++;
		}
		$query = $mysqli->query("INSERT INTO `scannedmatches` (`match_id`, `region`, `useful`, `patch`) VALUES ($m, '$region', $useful, '$pr')");
			if($query)
			{
				echo date("H:i:s")." - "; //timestamp for the actual output of the PHP file to see if it doesn't take eternity
				if($useful == 0)
				{
					echo "Marked as USELESS - ";
				}
				echo "Match $m, region $region, patch $pr marked as read to the db successfully. Progress $matchesdownloadprogress/$totalmatchestodownload<br>";
			}
			else
			{
				echo "Unable to add to the db". $mysqli->error. "<br>";
			} //outside of the condition because there are already invalid matches, don't wanna try to download them again if they are not in there obviously
		
		if($secondrequestsremaining < 1)
		{
			if($secondtimelimit > 0)
			{
				echo "Second time limit reached, sleeping for $secondtimelimit seconds<br>";
				sleep($secondtimelimit);
			}
			$secondrequestsremaining = $secondrequestlimit;
			$secondtimelimit = $secondtimelimitdefault;
		}
		if($minuterequestsremaining < 1)
		{
			if($minutetimelimit > 0)
			{
				echo "Minute time limit reached, sleeping for $minutetimelimit seconds";
				sleep($minutetimelimit);
			}
			$minuterequestsremaining = $minuterequestlimit; 
			$minutetimelimit = $minutetimelimitdefault; //more arbitrary timer spaghetti code, yummy
		}
		
		}
		}
		}
		}
		} //too lazy to reformat the entire file because of this
		echo "FINISHED<br>"; //once we're done with the entire fetch queue, make sure the user knows, because the progress notification doesn't work properly anyway
		}
		else
		{
			echo "Wrong pw<br><a href=\"updatestatsform.php\">Back to Form</a><br>";
		}
		}
		else
		{
			echo "You must access this subsite through <a href=\"updatestatsform.php\">this form</a><br>";
		}
		/*foreach($itemsinfo as $if)
		{
			if($if[2]!=0 || $if[3]!=0)
			{
			$winrate = $if[2]/($if[2]+$if[3])*100;
			}
			else
			{
			$winrate = 'undefined';
			}
			$matchesbought = $if[2]+$if[3];
			$img = "<img src=\"images/".$if[0].".png\" height=\"20\" width=\"20\">";
			echo "$img popularity is ". $if[1]/$totalplayers*100 ."% (".$if[1]."/$totalplayers), Win Rate is $winrate%  (".$if[2]."/$matchesbought). \t";
			$medianpurchase = 'undefined';
			$averagepurchase = 'undefined';
			$totalpurchases = 'undefined';
			$totaltimestamp = 0;
			if(isset($if[4][0]))
			{
				
				foreach($if[4] as $itempurchase)
				{
						$totaltimestamp+=$itempurchase;
				}
			sort($if[4]); //sort median purchases
			$totalpurchases = count($if[4]);
			$averagepurchase = convertTimeStamp($totaltimestamp/$totalpurchases);
			$medianpurchase = convertTimeStamp($if[4][$totalpurchases/2]);
			}

			echo "Average purchase is $averagepurchase ($totaltimestamp/$totalpurchases) and Median purchase is $medianpurchase<br>";
			//echo $if[3]."<br>";
			
		}*/
		//print_r($itemsinfo);
	function convertTimeStamp($t) //my own function, when compared to the one from javascript, I don't think it's useful anymore, though, was used at the beginning for testing
	{
		$t/=1000; //from ms to s
		$minutes = (int)($t/60);
		$seconds = $t%60;
		return "$minutes:$seconds";
	}
	
	function checkDuplicateMatch($m, $r)
	{
		global $mysqli;
		$query = $mysqli->query("SELECT * FROM `scannedmatches` WHERE `match_id` = $m AND `region` = '$r'");
		if($query)
		{
		if($query->num_rows>0)
		{
			return true;
		}
		else
		{
			return false;
		}
		}
		else
		{
			echo $mysqli->error."<br>";
		}
	}
	
	function requestJsonTry($url, $tries, $trylimit)
	{
		//echo "requesting file \t";
		$js = file_get_contents($url);
		if($js)
		{
		//	echo "request complete <br>";
			return $js;
		}
		else
		{
			//THIS NEVER WORKED PROPERLY AND I DON'T KNOW WHY, PHP ALWAYS GOT BACK AN EMPTY OBJECT EVEN IF THE QUERY WAS SUCCESSFUL AFTER THE FIRST FAIL, WHAT'S WRONG HERE?
			
			/*if($tries <= $trylimit)
			{
			$triesremaining = $trylimit - $tries;
			echo "Error while requesting the json, trying again... ( $triesremaining tries remaining)<br>";
			requestJsonTry($url, $tries+1, $trylimit);
			}
			else
			{
			echo "Failed, returning null array<br>";
			return null;
			}*/
		}
	}

?>
