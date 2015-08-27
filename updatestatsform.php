<!DOCTYPE html>
<html>
<head>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="jumbotron" style="margin-bottom:0px;">
<div class="page-header">
<h1 class="text-center">Match fetching form</h1>
</div>
<div class="container">
<div class="text-center">
<form action="updatestats.php" method="POST">
Number of matches <input name="matches"><br>
<span class="lead">BR<input type="checkbox" name="br"> EUNE<input type="checkbox" name="eune"> EUW<input type="checkbox" name="euw"> KR<input type="checkbox" name="kr"> LAN<input type="checkbox" name="lan"> 
LAS<input type="checkbox" name="las"> NA<input type="checkbox" name="na"> OCE<input type="checkbox" name="oce"> RU<input type="checkbox" name="ru"> TR<input type="checkbox" name="tr"> <br>
Select All <input type="checkbox" id="select_all"></span><br>
<input type="submit" class="btn btn-lg btn-primary">
</form>
</div>
</div>
</div>
<div class="jumbotron text-center" style="background-color: #13e682 !important;">
<h2><span class="label label-default">Number of results already in the DB (includes both patches)</span></h2>
<h3>
<?php
require "connect.php";
require "regionsrecorded.php";
foreach($regionsrecorded as $rr)
{
	$reg = $rr[0];
	$query = $mysqli->query("SELECT * FROM `scannedmatches` WHERE `region` = '$reg' AND `useful` = 1");
	if($query)
	{
		$count = $query->num_rows;
		echo "<span class=\"label label-primary text-uppercase\" style=\"margin-right:8px;\">$reg: $count</span>";
	}
}
?>
</h3>
<h2><span class="label alert-danger">Including useless matches</span></h2>
<h3>
<?php
foreach($regionsrecorded as $rr)
{
	$reg = $rr[0];
	$query = $mysqli->query("SELECT * FROM `scannedmatches` WHERE `region` = '$reg'");
	if($query)
	{
		$count = $query->num_rows;
		echo "<span class=\"label label-warning text-uppercase\" style=\"margin-right:8px;\">$reg: $count</span>";
	}
}
?>
</h3>
</div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-2.1.4.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
	<script src="js/selectall.js"></script>
</body>
</html>