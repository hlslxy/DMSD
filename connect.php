<?php

/*
 * Connect to the database 
 */

$dbhost = 'localhost';  // mysql host
$dbuser = 'root';            // mysql username
$dbpass = '20191024';          // mysql password\
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if(! $conn )
{
    die('Could not connect to database: ' . mysqli_error());
}

//echo "Database connected.";
?>
