<?php
include('inc/mysql.php');
include('inc/classes.php');
include('inc/header.php');
$username='quackau';
$user = new CryptoUser('quackau');
$accounts = $user->getAccounts();

foreach ($accounts as $a){
    echo "<h2>{$a->getNickname()}</h2>\n<pre>\n";
    var_export($a->getDailyBalance('2017-12-01','2017-12-31'));
    echo "</pre>";
}

include('inc/footer.php')
?>
