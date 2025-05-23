<?php

/*
 * Connect to the database 
 */

$dbhost = 'localhost';  // mysql host
$dbuser = 'xyliu';            // mysql username
$dbpass = '20191024';          // mysql password\
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
if(! $conn )
{
    die('Could not connect to database. ' . mysqli_error());
}

?>
