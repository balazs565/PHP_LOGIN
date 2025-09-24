<?php
$dbhost="localhost";
$dbuser="root";
$dbpass="";
$dbname="login_db";

if(!$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname))
{
    die("Nu se poate conecta la baza de date");
}