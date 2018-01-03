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

$balance=array('USD'=>0,$localcur=>0);

echo "<table class=\"table\">
    <tr><th>Account</th><th class=\"text-right\">Balance</th><th class=\"text-right\">Price</th><th class=\"text-right\">Value</th></tr>\n";
foreach($user->getAccounts() as $account){
    if (count($account->getStatement()) > 0) {
        printf("    <tr>\n        <td><a href=\"accountStatement.php?account=%u\">%s (%s)</a></td><td class=\"text-right\">%s</td>\n"
               ."        <td class=\"text-right\">%s<br><small>%s</small></td><td class=\"text-right\">%s<br><small>%s</small></td>\n    </tr>\n",
                $account->getId(), $account->getNickname(), $account->getSymbol(),
                formatcurrency($account->getBalance()),
                formatcurrency($prices[$account->getSymbol()]->USD),
                formatcurrency($prices[$account->getSymbol()]->$localcur),
                $balance['USD']+=formatcurrency($account->getBalance() * $prices[$account->getSymbol()]->USD),
                $balance[$localcur]+=formatcurrency($account->getBalance() * $prices[$account->getSymbol()]->$localcur));
    }
}
printf("    <tr><th colspan=\"3\">Total</th><th class=\"text-right\">%s<br><small>%s</small></th></tr>\n",
       formatcurrency($balance['USD']), formatcurrency($balance[$localcur]));
echo "</table>\n";
include('inc/footer.php')
?>
