
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
			$itemsinfo[$ij][2] = 0; //winrate
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
						
						$itemsinfo[$key][1]++; //we need to change the value of the original array
						$foundonplayer = true;
					}
				
				}
			}
			$totalplayers++;
		}
		echo "IT WORKS! <br>";
		}
		//echo "Rabadon popularity: ". $rabadonpop/$totalplayers*100 . "%($rabadonpop/$totalplayers)<br>";
		foreach($itemsinfo as $if)
		{
			echo $if[0]." popularity is ". $if[1]/$totalplayers*100 ."% (".$if[1]."/$totalplayers)<br>";
		}


?>
