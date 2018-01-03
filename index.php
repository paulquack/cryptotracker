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

echo "<table class=\"table\">
    <tr><th>Account</th><th>Symbol</th><th class=\"text-right\">Balance</th><th class=\"text-right\">Price</th><th class=\"text-right\">Value</th><th></th></tr>\n";
foreach($user->getAccounts() as $account){
    if (count($account->getStatement()) > 0) {
        $price_usd = $prices[$account->getSymbol()]->USD;
        $price_local = $prices[$account->getSymbol()]->$localcur;
        $value_usd = $price_usd * $account->getBalance();
        $value_local = $price_local * $account->getBalance();
        $balance_usd += $value_usd;
        $balance_local += $value_local;

        printf("    <tr>\n        <td><a href=\"accountStatement.php?account=%u\">%s</a></td><td>%s</td><td class=\"text-right\">%s</td>\n"
               ."        <td class=\"text-right\">%s<br><small>%s</small></td><td class=\"text-right\">%s<br><small>%s</small></td><td>USD<br><small>%s</small></td>\n    </tr>\n",
                $account->getId(), $account->getNickname(), $account->getSymbol(), formatcurrency($account->getBalance()),
                formatcurrency($price_usd,2),formatcurrency($price_local,2),formatcurrency($value_usd,2),formatcurrency($value_local,2),$localcur);
    }
}
printf("    <tr><th colspan=\"3\">Total</th><th class=\"text-right\">%s<br><small>%s</small></th><th>USD<br><small>%s</small></th></tr>\n",
       formatcurrency($balance_usd,2), formatcurrency($balance_local,2), $localcur);
echo "</table>\n";
include('inc/footer.php')
?>
