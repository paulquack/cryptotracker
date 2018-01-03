<?php
include('inc/mysql.php');
include('inc/classes.php');
include('inc/header.php');
$username='quackau';
$user = new CryptoUser('quackau');

function formatcurrency($a){
    return(preg_replace('/(\.?0+)$/', '<span class="text-muted">$1</span>', number_format($a,8)));
}

foreach($user->getAccounts() as $account){
    $symbols[] = $account->getSymbol();
}

$symbols = implode(",",array_unique($symbols));
$localcur = "AUD";
$prices = (array)json_decode(file_get_contents("https://min-api.cryptocompare.com/data/pricemulti?fsyms=$symbols&tsyms=USD,$localcur"));
$balance_usd = $balance_local = 0;

echo "<table class=\"table\">
    <tr><th>Account</th><th class=\"text-right\">Balance</th><th class=\"text-right\">Price</th><th class=\"text-right\">Value</th></tr>\n";
foreach($user->getAccounts() as $account){
    if (count($account->getStatement()) > 0) {
        $price_usd = $prices[$account->getSymbol()]->USD;
        $price_local = $prices[$account->getSymbol()]->$localcur;
        $value_usd = $price_usd * $account->getBalance();
        $value_local = $price_local * $account->getBalance();
        $balance_usd += $value_usd;
        $balance_local += $value_local;

        printf("    <tr>\n        <td><a href=\"accountStatement.php?account=%u\">%s (%s)</a></td><td class=\"text-right\">%s</td>\n"
               ."        <td class=\"text-right\">%s<br><small>%s</small></td><td class=\"text-right\">%s<br><small>%s</small></td>\n    </tr>\n",
                $account->getId(), $account->getNickname(), $account->getSymbol(), formatcurrency($account->getBalance()),
                formatcurrency($price_usd),formatcurrency($price_aud),formatcurrency($value_usd),formatcurrency($value_aud));
    }
}
printf("    <tr><th colspan=\"3\">Total</th><th class=\"text-right\">%s<br><small>%s</small></th></tr>\n",
       formatcurrency($balance_usd), formatcurrency($balance_local));
echo "</table>\n";
include('inc/footer.php')
?>
