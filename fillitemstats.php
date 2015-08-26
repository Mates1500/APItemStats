<?php
require("connect.php");
require("itemsrecorded.php");
require("regionsrecorded.php");
require("patchesrecorded.php");
require("apikey.php");
set_time_limit(3600);

foreach($itemsrecorded as $ir)
{
	foreach($regionsrecorded as $rr)
	{
		foreach($patchesrecorded as $pr)
		{
			$continue = true;
			if($ir[1] != "both")
			{
				if($ir[1] != $pr)
				{
				$continue = false;
				}
			}
			if($continue)
			{
			$id = $ir[0];
			$reg = $rr[0];
			$query = $mysqli->query("SELECT * FROM `itemstats` WHERE `item_id` = $id AND `region` = '$reg' AND `patch` = '$pr'");
			if(!$query)
			{
				echo $mysqli->error;
			}
			if($query->num_rows < 1)
			{
					
					$jsonurl = "https://global.api.pvp.net/api/lol/static-data/eune/v1.2/item/$id?version=$pr&api_key=$apikey";
					$json = file_get_contents($jsonurl);
					if($json)
					{
						$obj = json_decode($json, true);
						$name = $obj["name"];
						$description = $obj["description"];
						$name = $mysqli->real_escape_string($name);
						$description = $mysqli->real_escape_string($description);
						$qstring = "INSERT INTO `itemstats`(`item_id`, `item_name`, `item_description`, `region`, `winrate`, `popularity`, `purchase_timestamps`, `patch`) VALUES($id, '$name', '$description', '$reg', 0, 0, 0, '$pr')";
						$query = $mysqli->query($qstring);
						if($query)
						{
							echo "Successfully added $name for the $reg region, patch $pr<br>";
						}
						else
						{
							echo "Query error:".$mysqli->error."<br>";
						}
					}
					else
					{
						echo "Error while sending the request <br>";
					}
					
			}
			}
			
		}
	}
}
echo "FINISHED!<br>";
?>