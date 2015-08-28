<?php
	require("itemsrecorded.php");
	require("patchesrecorded.php");
	require_once("password.php");
	if(isset($_POST["pw"]))
	{
	if(md5($_POST["pw"]) == $password)
	{
	foreach($itemsrecorded as $ir)
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
				$irname = $ir[0];
				$img = "images/$irname.png";
				if($pr == "5.11.1" && $irname == 3050)
				{
					$img = "images/$irname"."_z.png"; //zeke's herald, different icon
					echo $img."<br>";
				}
				if(!file_exists($img))
				{
					$url = "http://ddragon.leagueoflegends.com/cdn/$pr/img/item/$irname.png";
					
					fopen($img, "w");
					file_put_contents($img, file_get_contents($url));
				}
			}
			
		}
	}
	echo "finished!";
	}
	else
	{
		echo "wrong pw<br><a href=\"downloaditemimages.php\">Try again?</a>";
	}
	}
	else
	{
		echo "<form method=\"POST\">Password:<input type=\"password\"name=\"pw\"><br><input type=\"submit\">";
	}
?>