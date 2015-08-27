<?php
require_once("connect.php");
require_once("regionsrecorded.php");
require_once("itemsrecorded.php");
require_once("patchesrecorded.php");
set_time_limit(3600);
foreach($itemsrecorded as $ir)
{
	foreach($patchesrecorded as $pr)
	{
		foreach($regionsrecorded as $rr)
		{
			$patch = $pr;
			$region = $rr[0];
			$item = $ir[0];
			if($ir[1] == "both" || $ir[1] == $patch)
			{
				$query = $mysqli->query("SELECT * FROM `cacheddata` WHERE `item_id` = $item AND `region` = '$region' AND `patch` = '$patch'");
				if($query)
				{
					if($query->num_rows<1)
					{
						$query2 = $mysqli->query("INSERT INTO `cacheddata`(`item_id`, `region`, `winrate`, `pickrate`, `avgpurchase`, `medpurchase`, `patch`) VALUES($item, '$region', 0, 0, 0, 0, '$patch')");
						if($query2)
						{
							echo "Succesfully added into cacheddata item $item, region $region, patch $patch<br>";
						}
						else
						{
							echo "Problem with query1:".$mysqli->error."<br>";
						}
					}
					
					$query2 = $mysqli->query("SELECT * FROM `itemstats` WHERE `item_id` = $item AND `region` = '$region' AND `patch` = '$patch'");
					if($query2)
					{
						$obj = $query2->fetch_object();
						$timestamps = json_decode($obj->purchase_timestamps);
						$winrate = $obj->winrate;
						$popularity = $obj->popularity;
						$query3 = $mysqli->query("SELECT * FROM `scannedmatches` WHERE `region` = '$region' AND `patch` = '$patch' AND `useful` = 1");
						{
							if($query3)
							{
								$totalmatches = $query3->num_rows;
								if($popularity > 0)
								{
									$winratepercent = $winrate/$popularity*100;
								}
								else
								{
									$winratepercent = 0;
								}
								if($totalmatches > 0)
								{
									$popularitypercent = $popularity/$totalmatches/10*100;
								}
								else
								{
									$popularitypercent = 0;
								}
								sort($timestamps);
								$timestampsadded = 0;
								foreach($timestamps as $t)
								{
									$timestampsadded+=$t;
								}
								$avgpurchase = $timestampsadded/count($timestamps);
								$medpurchase = $timestamps[count($timestamps)/2];
								$query4 = $mysqli->query("UPDATE `cacheddata` SET `winrate` = $winratepercent, `pickrate` = $popularitypercent, `avgpurchase` = $avgpurchase, `medpurchase` = $medpurchase WHERE `item_id` = $item and `region` = '$region'");
								if($query4)
								{
									echo "Successfully updated $item, region $region winrate to $winratepercent, pickrate to $popularitypercent, avgpurchase to $avgpurchase, medpurchase to $medpurchase<br>";
								}
								else
								{
									echo "Problem with query2:".$mysqli->error."<br>";
								}
								
							}
							else
							{
								echo "Problem with query3:".$mysqli->error."<br>";
							}
						}
					}
					else
					{
						echo "Problem with query4:".$mysqli->error."<br>";
					}
				}
				else
				{
					echo "Problem with query5:".$mysqli->error."<br>";
				}
				
			}
		}
	}
}
?>