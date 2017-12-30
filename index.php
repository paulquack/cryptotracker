<?php
include('inc/mysql.php');
include('inc/classes.php');
include('inc/header.php');
$username='quackau';
$user = new CryptoUser('quackau');
echo "<pre>";
var_dump($user);
echo "</pre>";

include('inc/footer.php')
?>
