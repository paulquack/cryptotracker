<?php
include('inc/mysql.php');
include('inc/classes.php');
include('inc/header.php');
$username='quackau';
$user = new CryptoUser('quackau');
$accounts = $user->getAccounts();

?>
<form class="form-inline" action="accountStatement.php" method="get" id="accountform">

    <h3>Select account</h3>
    <div class="form-group">
        <div class="input-group">
            <div class="input-group-addon">Account</div>
            <select class="form-control" name="account" id="selectaccount" aria-label="Account" aria-describedby="account_addon">
                <?php
                foreach ($accounts as $a){
                    printf('                <option value="%u">%s (%s)</option>'."\n", $a->getId(), $a->getNickname(), $a->getSymbol());
                }
                ?>
            </select>
        </div>
    </div>
</form>
<script>
$('#selectaccount').change(function() {
  $('#accountform').submit();
});
</script>
<?php
if (isset($_GET) and array_key_exists('account',$_GET) and array_key_exists($_GET['account'],$accounts)) {
    $statement = $account->getStatement();
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
