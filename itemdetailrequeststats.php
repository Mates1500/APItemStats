<?php
header('Content-Type: application/json');
require_once("connect.php");
require_once("itemsrecorded.php");
require_once("patchesrecorded.php");
require_once("regionsrecorded.php");
$item_id = 0;
$result_arr = array();
$result_all = array();
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
	$additional = " AND `region` = '$refpref'";
	if($refpref=="all")
	{
		$additional = "";
	}
	$query = $mysqli->query("SELECT `item_name`, `item_description` FROM `itemstats` WHERE `region` = '$refpref' AND `patch` = '$pr' AND `item_id` = $item_id");
	if($query)
	{
		$obj = $query->fetch_object();
		$query2 = $mysqli->query("SELECT `winrate`, `pickrate`, `avgpurchase`, `medpurchase` FROM `cacheddata` WHERE `patch` = '$pr' AND `item_id` = $item_id AND `region` = '$refpref'");
		if($query2)
		{
			$obj2 = $query2->fetch_object();
			$result_arr['name'] = $obj->item_name;
			$result_arr['desc'] = $obj->item_description;
			$result_arr['winrate'] = $obj2->winrate;
			$result_arr['pickrate'] = $obj2->pickrate;
			$result_arr['avgpurchase'] = $obj2->avgpurchase;
			$result_arr['medpurchase'] = $obj2->medpurchase;
			$result_arr['patch'] = $pr;
			array_push($result_all, $result_arr);
		}
		else
		{
			echo "Mysql error:".$mysqli->error."<br>";
		}
	}
	else
	{
		echo "Mysql error:".$mysqli->error."<br>";
	}
	
}
array_push($result_all, $refpref);
echo json_encode($result_all);
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