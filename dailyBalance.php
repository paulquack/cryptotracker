<?php
include('inc/mysql.php');
include('inc/classes.php');
include('inc/header.php');
$username='quackau';
$user = new CryptoUser('quackau');
echo "<pre>";
$accounts = $user->getAccounts();
foreach ($accounts as $a){
    var_export($a->getDailyBalance('2017-12-01','2017-12-31'));
}
echo "</pre>";

include('inc/footer.php')
?>
