<!DOCTYPE html>
<html>
<head>
	<link rel="apple-touch-icon" sizes="57x57" href="images/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="images/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="images/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="images/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="images/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="images/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="images/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="images/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="images/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="images/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="images/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
	<link rel="manifest" href="images/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="images/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">
<title>AP Item Stats</title>
<?php
echo "<script>var itemid = '".$_GET['id']."';";
echo "var region = '".$_GET['region_pref']."';</script>";
?>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-2.1.4.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="js/jquery.cookie.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src="js/selectall.js"></script>
	<script src="js/Chart.js"></script>
	<script type="text/javascript" language="javascript">
	var options = {};
         $(document).ready(function() {
			 var refpref = $("ul.navbar-nav > li.active").text().toLowerCase();
			 
				$("ul.navbar-nav > li:not(:contains('Home'))").click(function()
				{
					options = {animateRotate: false};
					$("ul.navbar-nav").children().removeClass("active");
					$(this).addClass("active");
					refpref = $(this).text().toLowerCase();
					$.cookie("region", refpref, {expires:7});
					$.getJSON('itemdetailrequeststats.php?region_pref='+refpref+'&id='+itemid, function(d)
			   {
				   processJsonData(d);
					});;
				})
				
				
				
			if(region!="")
			{
				$("ul.navbar-nav").children().removeClass("active");
				$("ul.navbar-nav").children("li:contains('"+region.toUpperCase()+"')").addClass("active");
			}
				
			
               $.getJSON('itemdetailrequeststats.php?region_pref='+region+'&id='+itemid, function(d)
			   {
				   processJsonData(d);
			   });
			   
				
         });
		 var wr;
		 var po;
		 var av;
		 var me;
		 function processJsonData(obj)
		 {			
					if( wr !== undefined )
					{
					wr.destroy();
					}
					if( po !== undefined )
					{
					po.destroy();
					}
					if(av !== undefined )
					{
					av.destroy();
					}
					if(me !== undefined )
					{
					me.destroy();
					} //destroy the previous instances of the charts, or they will bug out, overlapping each other at times
			 		var winratedata = new Array();
					var popularitydata = new Array();
					var avgpurchasedata = new Array();
					var medpurchasedata = new Array();
				for(var i=0; i<2; i++)
				{
					winratedata.push(Math.round(obj[i].winrate * 1000) / 1000);
					popularitydata.push(Math.round(obj[i].pickrate * 1000) / 1000);
					avgpurchasedata.push(Math.round(obj[i].avgpurchase * 1000) / 1000 / 1000);
					medpurchasedata.push(Math.round(obj[i].medpurchase * 1000) / 1000 / 1000);
					$("span.item"+(i+1)+"desc").html('<img src="images/'+itemid+'.png"><br>'+obj[i].desc);
				}
				
				console.log(obj);
		var wratedata = [
			{
				value: winratedata[0],
				color: "#13e682",
				highlight: "#5cf1ab",
				label: "5.11",
			},
			{
				value: winratedata[1],
				color: "#11c4ff",
				highlight: "#40cfff",
				label: "5.14",
			},

		];
		var ctx1 = $("#wrateChart").get(0).getContext("2d");
		var wrateChart = new Chart(ctx1).Doughnut(wratedata, options);
		wr = wrateChart;
		var legend1 = wrateChart.generateLegend();
		$("#wrateLegend").html(legend1);
		
		var pratedata = [
			{
				value: popularitydata[0],
				color: "#13e682",
				highlight: "#5cf1ab",
				label: "5.11",
			},
			{
				value: popularitydata[1],
				color: "#11c4ff",
				highlight: "#40cfff",
				label: "5.14",
			},

		];
		var ctx2 = $("#prateChart").get(0).getContext("2d");
		var prateChart = new Chart(ctx2).Doughnut(pratedata, options);
		po = prateChart;
		var legend2 = prateChart.generateLegend();
		$("#prateLegend").html(legend2);
		var avgdata = {
			labels: [secondsTimeSpanToMS(avgpurchasedata[0]) + "               " + secondsTimeSpanToMS(avgpurchasedata[1])],
			datasets: [
        {
            label: "5.11",
            fillColor: "rgba(19,230,130,0.75)",
            strokeColor: "rgba(19,223,126,0.9)",
            highlightFill: "rgba(18,222,125,0.9)",
            highlightStroke: "rgba(17,193,109,1)",
            data: [avgpurchasedata[0]]
        },
        {
            label: "5.14",
            fillColor: "rgba(17,196,255,0.75)",
            strokeColor: "rgba(0,153,204,0.9)",
            highlightFill: "rgba(0,190,253,0.9)",
            highlightStroke: "rgba(0,112,149,1)",
            data: [avgpurchasedata[1]]
        }
    ]
};
	var ctx3 = $("#avgChart").get(0).getContext("2d"); 
	var avgChart = new Chart(ctx3).Bar(avgdata);
	av = avgChart;
	var legend3 = avgChart.generateLegend();
	$("#avgLegend").html(legend3);
	
	var meddata = {
			labels: [secondsTimeSpanToMS(medpurchasedata[0]) + "               " + secondsTimeSpanToMS(medpurchasedata[1])],
			datasets: [
        {
            label: "5.11",
            fillColor: "rgba(19,230,130,0.75)",
            strokeColor: "rgba(19,223,126,0.9)",
            highlightFill: "rgba(18,222,125,0.9)",
            highlightStroke: "rgba(17,193,109,1)",
            data: [medpurchasedata[0]]
        },
        {
            label: "5.14",
            fillColor: "rgba(17,196,255,0.75)",
            strokeColor: "rgba(0,153,204,0.9)",
            highlightFill: "rgba(0,190,253,0.9)",
            highlightStroke: "rgba(0,112,149,1)",
            data: [medpurchasedata[1]]
        }
    ]
};
	var ctx4 = $("#medChart").get(0).getContext("2d"); 
	var medChart = new Chart(ctx4).Bar(meddata);
	me = medChart;
	var legend4 = medChart.generateLegend();
	$("#medLegend").html(legend4);
		 }
		 
		 function secondsTimeSpanToMS(s) {
			var m = Math.floor(s/60); //Get remaining minutes
			s -= m*60;
			s = Math.floor(s);
			return m+":"+(s < 10 ? '0'+s : s); //zero padding on minutes and seconds
			}
			
		
      </script>
	</head>
<body>
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-2">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" >Region Selection</a>
    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
      <ul class="nav navbar-nav">
        <li><a href="#">ALL</a></li>
        <li class="active"><a href="#">NA</a></li>
		<li><a href="#">EUW</a></li>
		<li><a href="#">EUNE</a></li>
		<li><a href="#">KR</a></li>
		<li><a href="#">OCE</a></li>
		<li><a href="#">BR</a></li>
		<li><a href="#">LAN</a></li>
		<li><a href="#">LAS</a></li>
		<li><a href="#">RU</a></li>
		<li><a href="#">TR</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="landingpage.php">Home</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class = "container">
<div class="text-center"><div class="page-header"><h1>AP Item Usage between 5.11 and 5.14</h1></div>
<div id="content">
<div class="jumbotron col-xs-12" style="background-color:#f5f5f5">
<div class="col-lg-6 item1">
<h4><strong>5.11</strong></h4>
<span class="item1desc">
</span>
</div>

<div class="col-lg-6 item2">
<h4><strong>5.14</strong></h4>
<span class="item2desc">
</span>
</div>
</div>
<div class="col-lg-6">
<h3>Win Rate</h3>
<canvas id="wrateChart" width="300" height="300"></canvas>
<span id="wrateLegend"></span>
</div>
<div class="col-lg-6">
<h3>Pick Rate</h3>
<canvas id="prateChart" width="300" height="300"></canvas>
<span id="prateLegend"></span>
</div>
<div class="col-lg-6">
<h3>Average Purchase Time</h3>
<canvas id="avgChart" width="300" height="300"></canvas>
<span id="avgLegend"></span>
</div>
<div class="col-lg-6">
<h3>Median Purchase Time</h3>
<canvas id="medChart" width="300" height="300"></canvas>
<span id="medLegend"></span>
</div>
</div>

</div>
</div>

<footer class="footer text-center" style="background-color: #008cba !important; margin: 35px 0 0 0; padding-top: 20px; padding-bottom: 20px">
<div class="content">
<h2 style="margin-bottom:26px;"><span class="label label-info">Count of relevant matches in the DB</span></h2>
<h3 style="display:block;">
<?php
require_once("connect.php");
require_once("regionsrecorded.php");

foreach($regionsrecorded as $rr)
{
	$reg = $rr[0];
	$query = $mysqli->query("SELECT * FROM `scannedmatches` WHERE `region` = '$reg' AND `useful` = 1");
	if($query)
	{
		$count = $query->num_rows;
		echo "<span class=\"label label-success text-uppercase\" style=\"margin-right:8px; margin-bottom:8px;\">$reg: $count</span>";
	}
}
?>
</h3>
<span class="label label-default">Made by Mates1500, 2015</span>
</div>
</footer>

</body>
</html>