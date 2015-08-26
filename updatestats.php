
<?php
		require("itemsrecorded.php");
		$matchid = [
			1212063430, 
			1212063983, 
			1212064195, 
			1212064341, 
			1212064378,
			];
		
		$rabadonpop = 0;
		$rabadonwin;
		$rabadonid = 3089;
		$totalplayers = 0;
		
		$itemsinfo = array();
		$ij = 0; //ij so it's not confused with any other loop
		foreach($itemsrecorded as $it)
		{
			$itemsinfo[$ij][0] = $it;//item id
			$itemsinfo[$ij][1] = 0; //popularity
			$itemsinfo[$ij][2] = 0; //won games
			$itemsinfo[$ij][3] = 0; //lost games
			$itemsinfo[$ij][4] = array();
			$ij++;
		}

		foreach($matchid as $m)
		{
		require("apikey.php");
		$region = 'eune';
		$jsonurl = "https://$region.api.pvp.net/api/lol/$region/v2.2/match/$m?includeTimeline=true&api_key=$apikey";
		$json = file_get_contents($jsonurl);
		$obj = json_decode($json, true);
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
							$itemsinfo[$key][3]++;
						}
						
						$itemsinfo[$key][1]++; //we need to change the value of the original array
						$foundonplayer = true;
					}
				
				}
			}
			$totalplayers++;
		}
		//echo "IT WORKS! <br>";
		
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
								array_push($itemsinfo[$key][4], $event["timestamp"]);
							}
						}
					}
				}
			}
		}
		
		}
		foreach($itemsinfo as $if)
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
			asort($if[4]); //sort median purchases
			$totalpurchases = count($if[4]);
			$averagepurchase = convertTimeStamp($totaltimestamp/$totalpurchases);
			$medianpurchase = convertTimeStamp($if[4][$totalpurchases/2]);
			}

			echo "Average purchase is $averagepurchase ($totaltimestamp/$totalpurchases) and Median purchase is $medianpurchase<br>";
			//echo $if[3]."<br>";
			
		}
		//print_r($itemsinfo);
	function convertTimeStamp($t)
	{
		$t/=1000; //from ms to s
		$minutes = (int)($t/60);
		$seconds = $t%60;
		return "$minutes:$seconds";
	}

?>
