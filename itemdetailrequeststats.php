<?php
header('Content-Type: application/json');
require_once("connect.php");
require_once("itemsrecorded.php");
require_once("patchesrecorded.php");
require_once("regionsrecorded.php");
require_once("exceptions.php");
$item_id = 0;
$result_arr = array(); //current result
$result_all = array(); //all the results
if(isset($_GET["id"]))
{
	$item_id = $_GET["id"];
}
$refpref = 0;
if(isset($_GET["region_pref"]))
{
	$refpref = $_GET["region_pref"];
}




foreach($patchesrecorded as $pr)
{
	foreach($exceptions as $e) //the boots' enchantments different item IDs in 5.11 from 5.14 fuck up things, this fixes it
			{
				if($e[1] == $item_id && $pr == "5.11.1")
				{
					$item_id = $e[0];
				}
				if($e[0] == $item_id && $pr == "5.14.1")
				{
					$item_id = $e[1];
				}
			}
	$query = $mysqli->query("SELECT `item_name`, `item_description` FROM `itemstats` WHERE `region` = 'na' AND `patch` = '$pr' AND `item_id` = $item_id");
	if($query)
	{
		$obj = $query->fetch_object();
		$result_arr['name'] = $obj->item_name;
		$result_arr['desc'] = $obj->item_description;
			$additional = " AND `region` = '$refpref'";
			if($refpref=="all")
			{
				$additional = "";
			}
		$totalwinrate = 0;
		$wrcount = 0;
		$totalpickrate = 0;
		$prcount = 0;
		$totalavgpurchase = 0;
		$avgpcount = 0;
		$totalmedpurchase = 0;
		$avgmcount = 0;
		foreach($regionsrecorded as $rr)
		{
		if(($refpref != "all" && $rr[0] == $refpref) || $refpref == "all") //region preference, refpref is just a typo and I was too lazy to change it
		{
			$reg = $rr[0];
			$relevance = 1; //again, relevance is the proper way to figure out the average values, not simply dividing by 10 (number of regions)
			$additional2 = ""; //let's just not mess with the first $additional var, it would fuck up things
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
			$additional2 = " AND `region` = '$reg'"; 
			$query2 = $mysqli->query("SELECT `winrate`, `pickrate`, `avgpurchase`, `medpurchase` FROM `cacheddata` WHERE `patch` = '$pr' AND `item_id` = $item_id$additional2");
			//echo "SELECT `winrate`, `pickrate`, `avgpurchase`, `medpurchase` FROM `cacheddata` WHERE `patch` = '$pr' AND `item_id` = $item_id$additional2<br>";
			if($query2)
			{
			$obj2 = $query2->fetch_object();
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
			else
			{
				echo "Mysql error:".$mysqli->error."<br>";
			}
		}
		}
				$a_totalwinrate = divideOrZero($totalwinrate, $wrcount);
				$a_totalpickrate = divideOrZero($totalpickrate, $prcount);
				$a_totalavgpurchase = divideOrZero($totalavgpurchase, $avgpcount);
				$a_totalmedpurchase = divideOrZero($totalmedpurchase, $avgmcount);

				$result_arr['winrate'] = $a_totalwinrate;
				$result_arr['pickrate'] = $a_totalpickrate;
				$result_arr['avgpurchase'] = $a_totalavgpurchase;
				$result_arr['medpurchase'] = $a_totalmedpurchase;
				$result_arr['patch'] = $pr;
				array_push($result_all, $result_arr);
	}
	else
	{
		echo "Mysql error:".$mysqli->error."<br>";
	}
	
}
//array_push($result_all, $refpref);
echo json_encode($result_all);
function divideOrZero($n1, $n2) //division by 0 throws errors, this does not
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