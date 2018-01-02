<?php
include('inc/mysql.php');
include('inc/classes.php');
$username='quackau';
$user = new CryptoUser('quackau');
$accounts = $user->getAccounts();
date_default_timezone_set('UTC');

if (isset($_GET) and empty(array_diff(array('startdate','enddate'),$_GET))){
    $end = date('Y-m-d',strtotime($_GET['enddate']));
    $start = date('Y-m-d',strtotime($_GET['startdate']));
} else {
    $end = date('Y-m-d');
    $start = date('Y-m-d',strtotime("-1 month"));
}

if (isset($_GET) and array_key_exists("groupbysymbol",$_GET) and $_GET['groupbysymbol']==true) {
    $groupbysymbol = true;
} else {
    $groupbysymbol = false;
}

foreach ($accounts as $a){
    $bal = $a->getDailyBalance($start,$end);
    foreach ($bal as $date => $balance){
        addDataPoint($a, $date, $balance);
    }
}
header('Cache-Control: max-age=300');
header('Content-type: text/csv');
header('Content-disposition: attachment;filename=cryptoTracker_Balance.csv');
$f = fopen('php://output', 'w');
fputcsv($f, array_keys($balances[$start]));
foreach ($balances as $row) fputcsv($f, $row);
fclose($f);

function addDataPoint($account, $date, $balance){
    global $groupbysymbol, $balances;
    if (!array_key_exists($date, $balances)) $balances[$date] = array('Date'=>$date);
    if ($groupbysymbol){
        $key = $account->getSymbol();
        if (!array_key_exists($key, $balances[$date])) $balances[$date][$key] = 0;
        $balances[$date][$key] += $balance;
    } else {
        $key = sprintf("%s (%s)", $account->getNickname(), $account->getSymbol());
        $balances[$date][$key] = $balance;
    }
}

?>
