<?php
require("connect.php");
require("itemsrecorded.php");
require("regionsrecorded.php");
require("patchesrecorded.php");
require("apikey.php");
require_once("password.php");
set_time_limit(3600);

if(isset($_POST["pw"]))
{
if(md5($_POST["pw"]) == $password)
{
echo "Started fetching data";
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
						$description = $mysqli->real_escape_string($description); //apostrophes do not like being in SQL queries, let's "escape" that problem, now insert a Phreak pun here
						$qstring = "INSERT INTO `itemstats`(`item_id`, `item_name`, `item_description`, `region`, `winrate`, `popularity`, `purchase_timestamps`, `patch`) VALUES($id, '$name', '$description', '$reg', 0, 0, '[0]', '$pr')";
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
}
else
{
	echo "Wrong pw<br><a href=\"fillitemstats.php\">Back to Form</a><br>";
}
}
else
{
	echo "<form method=\"POST\">Password:<input type=\"password\"name=\"pw\"><br><input type=\"submit\">";
}
?>