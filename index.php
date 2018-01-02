<?php
include('inc/mysql.php');
include('inc/classes.php');
include('inc/header.php');
$username='quackau';
$user = new CryptoUser('quackau');
echo "<table class=\"table\">
    <tr><th>Account</th><th>Balance</th></tr>\n";
foreach($user->getAccounts() as $account){
    printf("    <tr><td>%s (%s)</td><td>%s</td></tr>\n",
           $account->getNickname(), $account->getSymbol(),
           trim(number_format($account->getBalance(),8),'0.'));
}
echo "</table>\n";
include('inc/footer.php')
?>
