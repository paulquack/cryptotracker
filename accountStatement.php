<?php
include('inc/mysql.php');
include('inc/classes.php');
include('inc/header.php');
$username='quackau';
$user = new CryptoUser('quackau');
$accounts = $user->getAccounts();

if (isset($_GET) and array_key_exists('account',$_GET) and array_key_exists($_GET['account'],$accounts)) {
    $curAccount = $accounts[$_GET['account']];
} else {
    $curAccount = false;
}
?>
<form class="form-inline" action="accountStatement.php" method="get" id="accountform">

    <h3>Select account</h3>
    <div class="form-group">
        <div class="input-group">
            <div class="input-group-addon">Account <?php echo $curAccount->getId(); ?></div>
            <select class="form-control" name="account" id="selectaccount" aria-label="Account" aria-describedby="account_addon">
                <?php
                foreach ($accounts as $a){
                    printf('                <option value="%u"%s>%s (%s)</option>'."\n", $a->getId(),
                           ($curAccount and $curAccount->getId() == $a->getId())?' selected="selected"':'', $a->getNickname(), $a->getSymbol());
                }
                ?>
            </select>
        </div>
    </div>
</form>
<?php
$footer_script = "$('#selectaccount').change(function() {\n  $('#accountform').submit();\n});";

if ($curAccount) {
    $statement = $curAccount->getStatement();
    printf("<h1>Statement for %s (%s)</h1>\n",$curAccount->getNickname(),$curAccount->getSymbol());
    echo "<table class=\"table\">
        <tr><th>Date</th><th>Description</th><th>Amount</th><th>Balance</th></tr>\n";
    foreach($statement as $t){
        printf("    <tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>\n", $t['timestamp'], $t['description'],
               rtrim(number_format($t['amount'],8),'0.'), rtrim(number_format($t['balance'],8),'0.'));
    }
    echo "</table>\n";
}
include('inc/footer.php')
?>
