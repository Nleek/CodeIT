<?php
/*
File: database.php
Desc: The database setup file. Establishes a connection to the databse.
*/

$db_addr = "localhost";
$db_user = "bungle_diycode";
$db_pass = "2016|BPA!";
$db_name = "bungle_diycode";
$charset = 'utf8';

$con = mysqli_connect("$db_addr","$db_user","$db_pass",$db_name)or die("cannot connect");
$connection = new mysqli("$db_addr","$db_user","$db_pass",$db_name)or die("cannot connect");

$dsn = "mysql:host=$db_addr;dbname=$db_name;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $db_user, $db_pass, $opt);
