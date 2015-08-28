<?php
header('Content-Type: application/json');
require_once("connect.php");
require_once("itemsrecorded.php");
require_once("patchesrecorded.php");
require_once("regionsrecorded.php");
$query = $mysqli->query("SELECT `item_id`, `item_name` FROM `itemstats` WHERE `region` = 'eune' AND `patch` = '5.14.1'");
$result_arr = array();
$result_all = array();
$refpref = 0;
if(isset($_GET["region_pref"]))
{
	$refpref = $_GET["region_pref"];
}

if($query)
{
	while($obj = $query->fetch_object())
	{
		foreach($patchesrecorded as $pr)
		{
			$additional = " AND `region` = '$refpref'";
			if($refpref=="all")
			{
				$additional = "";
			}
			
			
			$itemid = $obj->item_id;
			$query2 = $mysqli->query("SELECT `winrate`, `pickrate`, `avgpurchase`, `medpurchase`, `region` FROM `cacheddata` WHERE `patch` = '$pr' AND `item_id` = $itemid$additional");
			if($query2)
			{
				
				$totalwinrate = 0;
				$wrcount = 0;
				$totalpickrate = 0;
				$prcount = 0;
				$totalavgpurchase = 0;
				$avgpcount = 0;
				$totalmedpurchase = 0;
				$avgmcount = 0;
				while($obj2 = $query2->fetch_object())
				{
					$reg = $obj2->region;
					$relevance = 1;
					if($additional == "")
					{
						if($relevance == 1)
						{
						$relevance = 0;
						}
						$query3 = $mysqli->query("SELECT * FROM `scannedmatches` WHERE `useful` = 1 and `region` = '$reg'");
						if($query3)
						{
							$relevance = $query3 -> num_rows;
						}
						else
						{
							echo "Query3 error:".$mysqli->error."<br>";
						}
					}
					if($obj2->winrate != 0)
					{
						
						$totalwinrate+=$obj2->winrate*$relevance;
						$wrcount+=$relevance;
					}
					if($obj2->pickrate != 0)
					{
						$totalpickrate+=$obj2->pickrate*$relevance;
						$prcount+=$relevance;
					}
					if($obj2->avgpurchase != 0)
					{
						$totalavgpurchase+=$obj2->avgpurchase*$relevance;
						$avgpcount+=$relevance;
					}
					if($obj2->medpurchase != 0)
					{
						$totalmedpurchase+=$obj2->medpurchase*$relevance;
						$avgmcount+=$relevance;
					}
					
				}
				$a_totalwinrate = divideOrZero($totalwinrate, $wrcount);
				$a_totalpickrate = divideOrZero($totalpickrate, $prcount);
				$a_totalavgpurchase = divideOrZero($totalavgpurchase, $avgpcount);
				$a_totalmedpurchase = divideOrZero($totalmedpurchase, $avgmcount);
				$result_item_arr = [
					"id" => $obj->item_id,
					"name" => $obj->item_name,
					"winrate" => $a_totalwinrate,
					"pickrate" => $a_totalpickrate,
					"avgpurchase" => $a_totalavgpurchase,
					"medpurchase" => $a_totalmedpurchase,
					"patch" => $pr,
					];
				array_push($result_all, $result_item_arr);
			}
			else
			{
				echo "Query2 error:".$mysqli->error."<br>";
			}
		}
		

	}
	echo json_encode($result_all);
	
}
else
{
	echo "Mysql error:".$mysqli->error."<br>";
}

function divideOrZero($n1, $n2)
{
	if($n2 != 0)
	{
		return $n1/$n2;
	}
	else
	{
		return 0;
	}
}


?>