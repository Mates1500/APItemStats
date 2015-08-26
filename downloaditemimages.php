<?php
	require("itemsrecorded.php");
	foreach($itemsrecorded as $ir)
	{
		$img = "images/$ir.png";
		if(!file_exists($img))
		{
			$url = "http://ddragon.leagueoflegends.com/cdn/5.16.1/img/item/$ir.png";
			fopen($img, "w");
			file_put_contents($img, file_get_contents($url));
		}
	}
	
?>