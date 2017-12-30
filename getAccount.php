<?php
include("inc/mysql.php");
include("inc/classes.php");

if ($argc!=2) die("Invalid parameters\n");
$user = new CryptoUser($argv[1]);
var_export($user);
echo "\n";
?>
