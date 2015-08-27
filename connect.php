<?php
$mysqli = new mysqli('localhost', 'MYSQL_LOGIN', 'MYSQL_PW', 'apitems');
if ($mysqli->connect_errno) {
    die('Could not connect: ' . $mysqli->connect_error);
}


?>