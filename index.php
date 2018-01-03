<?php
include('inc/mysql.php');
include('inc/classes.php');
include('inc/header.php');
$username='quackau';
$user = new CryptoUser('quackau');

function formatcurrency($a, $precision = 8){
    return(preg_replace('/(\.?0+)$/', '<span class="text-muted">$1</span>', number_format($a, $precision)));
}

foreach($user->getAccounts() as $account){
    $symbols[] = $account->getSymbol();
}

$symbols = implode(",",array_unique($symbols));
$localcur = "AUD";
$prices = (array)json_decode(file_get_contents("https://min-api.cryptocompare.com/data/pricemulti?fsyms=$symbols&tsyms=USD,$localcur"));
$balance_usd = $balance_local = 0;

echo "<h1>Account Balances</h1>\n<table class=\"table\">
    <tr><th>Account</th><th class=\"text-right\">Balance</th><th></th><th class=\"text-right\">Price</th><th class=\"text-right\">Value</th><th></th></tr>\n";
foreach($user->getAccounts() as $account){
    if ($account->getBalance() > 0) {
        $price_usd = $prices[$account->getSymbol()]->USD;
        $price_local = $prices[$account->getSymbol()]->$localcur;
        $value_usd = $price_usd * $account->getBalance();
        $value_local = $price_local * $account->getBalance();
        $balance_usd += $value_usd;
        $balance_local += $value_local;

        printf("    <tr>\n        <td><a href=\"accountStatement.php?account=%u\">%s</a></td><td class=\"text-right\">%s</td><td>%s</td>\n"
               ."        <td class=\"text-right\">%s<br><small>%s</small></td><td class=\"text-right\">%s<br><small>%s</small></td><td>USD<br><small>%s</small></td>\n    </tr>\n",
                $account->getId(), $account->getNickname(), formatcurrency($account->getBalance()), $account->getSymbol(),
                formatcurrency($price_usd,2),formatcurrency($price_local,2),formatcurrency($value_usd,2),formatcurrency($value_local,2),$localcur);
    }
}
printf("    <tr><th colspan=\"4\">Total</th><th class=\"text-right\">%s<br><small>%s</small></th><th>USD<br><small>%s</small></th></tr>\n",
       formatcurrency($balance_usd,2), formatcurrency($balance_local,2), $localcur);
echo "</table>\n";
include('inc/footer.php')
?>
