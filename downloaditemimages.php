<?php
	require("itemsrecorded.php");
	require("patchesrecorded.php");
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
				if(!file_exists($img))
				{
					$url = "http://ddragon.leagueoflegends.com/cdn/$pr/img/item/$irname.png";
					fopen($img, "w");
					file_put_contents($img, file_get_contents($url));
				}
			}
			
		}
	}
	
?>