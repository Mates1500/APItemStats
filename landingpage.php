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
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-2.1.4.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="js/jquery.cookie.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script src="js/selectall.js"></script>
	<style>
	.label
	{

	}
	</style>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	  <script type="text/javascript">
      google.load("visualization", "1.1", {packages:["table"]});
      google.setOnLoadCallback(drawTable);
		var data;
		var table;
		var formatter;
      function drawTable() {
        data = new google.visualization.DataTable();
		data.addColumn('string', 'Icon');
        data.addColumn('string', 'Item');
        data.addColumn('string', 'WinRate 5.11');
        data.addColumn('string', 'WinRate 5.14');
		data.addColumn('string', 'Popularity 5.11');
		data.addColumn('string', 'Popularity 5.14');
		data.addColumn('string', 'Avg purchase 5.11');
		data.addColumn('string', 'Avg purchase 5.14');
		data.addColumn('string', 'Med purchase 5.11');
		data.addColumn('string', 'Med purchase 5.14');
		/*for(var i=0; i<=53; i++)
		{

			data.addRow(['<span id="img'+i+'"></span>', '<span id="name'+i+'"></span>',
			'<span id="wr511_'+i+'"></span>', '<span id="wr514_'+i+'"></span>',
			'<span id="pop511_'+i+'"></span>', '<span id="pop514_'+i+'"></span>',
			'<span id="avg511_'+i+'"></span>', '<span id="avg514_'+i+'"></span>', '<span id="med511_'+i+'"></span>', '<span id="med514_'+i+'"></span>']);
		}*/
		data.addRows(54);
       /* data.addRows([

          ['Mike',  {v: 10000, f: '$10,000'}, true],
          ['Jim',   {v:8000,   f: '$8,000'},  false],
          ['Alice', {v: 12500, f: '$12,500'}, true],
          ['Bob',   {v: 7000,  f: '$7,000'},  true]
        ]);*/

        table = new google.visualization.Table(document.getElementById('table_div'));
        table.draw(data, {showRowNumber: false, width: '100%', height: '100%', allowHtml: true, sort: 'disable'});
      }
    </script>
	<script type="text/javascript" language="javascript">
         $(document).ready(function() {
			 var refpref = $("ul.navbar-nav > li.active").text().toLowerCase();

			 if($.cookie("region") == null)
			 {
				 $.cookie("region", refpref, {expires:7});
			 }

			 if($.cookie("region") != null)
			 {
				refpref=$.cookie("region");
				$("ul.navbar-nav").children().removeClass("active");
				$("ul.navbar-nav").children("li:contains('"+refpref.toUpperCase()+"')").addClass("active"); //if a cookie exists, unmark all the li's and mark the one equal to the cookie
			 }

				$("ul.navbar-nav > li:not(:contains('Home'))").click(function() //oh we want to exclude the home button, this one had me for almost an hour
				{
					$("ul.navbar-nav").children().removeClass("active");
					$(this).addClass("active");
					refpref = $(this).text().toLowerCase();
					$.cookie("region", refpref, {expires:7});
					$.getJSON('landingpagerequeststats.php?region_pref='+refpref, function(d) //this is what starts all the realtime request magic
			   {
				   processJsonData(d, refpref);
					});;
				})


               $.getJSON('landingpagerequeststats.php?region_pref='+refpref, function(d) //the first one called on page load
			   {
				   processJsonData(d, refpref);
			   });


         });
		 function processJsonData(obj, refpref)
		 {
			 var itemindex = 0;
				  for(var i=0; i<obj.length; i++)
				  {
						if(i%2==0 || i==0)
						{
						/*$('#img'+j).html('<img src="images/'+obj[i].id+'.png">');
						$('#name'+j).html(obj[i].name);*/
						data.setCell(itemindex, 0, '<img src="images/'+obj[i].id+'.png">');
						data.setCell(itemindex, 1, '<a href="itemdetail.php?id='+obj[i].id+'&region_pref='+refpref+'">'+obj[i].name+'</a>');
						data.setCell(itemindex, 2, Math.round(obj[i].winrate*100)/100+"%");
						data.setCell(itemindex, 4, Math.round(obj[i].pickrate*100)/100+"%");
						data.setCell(itemindex, 6, secondsTimeSpanToMS(obj[i].avgpurchase/1000));
						data.setCell(itemindex, 8, secondsTimeSpanToMS(obj[i].medpurchase/1000));
						}
						else
						{
						data.setCell(itemindex, 3, Math.round(obj[i].winrate*100)/100+"%");
						data.setCell(itemindex, 5, Math.round(obj[i].pickrate*100)/100+"%");
						data.setCell(itemindex, 7, secondsTimeSpanToMS(obj[i].avgpurchase/1000));
						data.setCell(itemindex, 9, secondsTimeSpanToMS(obj[i].medpurchase/1000));
						itemindex++;
						}


				  }
				table.draw(data, {showRowNumber: false, width: '100%', height: '100%', allowHtml: true, sort: 'disable'});  //we need to force redrawing the table, or the values won't change
				//console.log(obj); //for debugging
		 }

		 function secondsTimeSpanToMS(s) { //I stole this one from stackoverflow, because I was way too lazy to think already, but the same function in PHP was written by me, no zero padding there tho
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
<div class="text-center">
<img src="images/splash_small.png" class="img-responsive" style="display:inline-block;">
<div class="page-header"><h1>AP Item Usage between 5.11 and 5.14</h1></div>
<div id="table_div">
</div>
</div>
</div>
<footer class="footer text-center" style="background-color: #008cba !important; margin: 35px 0 0 0; padding-top: 20px; padding-bottom: 40px">
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
<br>
<a href="https://github.com/Mates1500/APItemStats" target="_blank" class="btn btn-default pull-right" style="margin-right:20px; margin-top:-20px">View project on Github</a>
</div>
</footer>

</body>
</html>
