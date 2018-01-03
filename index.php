<?php
include('inc/mysql.php');
include('inc/classes.php');
include('inc/header.php');
$username='quackau';
$user = new CryptoUser('quackau');
echo "<table class=\"table\">
    <tr><th>Account</th><th class=\"text-right\">Balance</th></tr>\n";
foreach($user->getAccounts() as $account){
    if (count($account->getStatement()) > 0) {
        printf("    <tr><td><a href=\"accountStatement.php?account=%u\">%s (%s)</a></td><td class=\"text-right\">%s</td></tr>\n",
                $account->getId(), $account->getNickname(), $account->getSymbol(),
                preg_replace('/(\.?0+)$/', '<span class="text-muted">$1</spam>', number_format($account->getBalance(),8)));
    }
}
echo "</table>\n";
include('inc/footer.php')
?>
