<?php
include('inc/mysql.php');
include('inc/classes.php');
include('inc/header.php');
$username='quackau';
$user = new CryptoUser('quackau');

if (isset($_POST) and empty(array_diff(array('from_account','from_amount','to_account','to_amount','notes','timestamp'),array_keys($_POST)))){
  $from_account=intval($_POST['from_account']);
  $from_amount=floatval($_POST['from_amount']);
  $to_account=intval($_POST['to_account']);
  $to_amount=floatval($_POST['to_amount']);
  $timestamp=$_POST['timestamp'];
  $notes=$_POST['notes'];
  $result = $user->addTransaction($from_account,$from_amount,$to_account,$to_amount,$timestamp,$notes);
  if ($result) {
    echo '<div class="alert alert-success alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Success!</strong> Transaction inserted into database.
</div>';
  } else {
    echo '<div class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Failed!</strong> Transaction not inserted into database.
</div>';
  }
}
?>
      <h2>Add Transaction</h2>
      <form class="form-inline" action="addTransaction.php" method="post">

      <h3>From</h3>
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-addon">Account</div>
          <select class="form-control" name="from_account" id="from_account" aria-label="From Account" aria-describedby="from_account_addon">
              <?php
              $accounts = $user->getAccounts();
              foreach ($accounts as $a){
                printf('                <option value="%u">%s (%s)</option>'."\n", $a->getId(), $a->getNickname(), $a->getSymbol());
              }
              ?>
          </select>
        </div>
        <div class="input-group">
          <div class="input-group-addon">Amount</div>
          <input class="form-control" type="text" name="from_amount" id="from_amount" required pattern="[0-9]+\.?[0-9]*">
        </div>
      </div>

      <h3>To</h3>
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-addon">Account</div>
          <select class="form-control" name="to_account" id="to_account">
              <?php
              $accounts = $user->getAccounts();
              foreach ($accounts as $a){
                printf('                <option value="%u">%s (%s)</option>'."\n", $a->getId(), $a->getNickname(), $a->getSymbol());
              }
              ?>
          </select>
        </div>
        <div class="input-group">
          <div class="input-group-addon">Amount</div>
          <input class="form-control" type="text" name="to_amount" id="to_amount" required pattern="[0-9]+\.?[0-9]*">
        </div>
      </div>

      <h3>Date</h3>
      <div class="form-group">
        <div class="input-group">
          <div id="datepicker" data-date="0" data-date-format="yyyy-mm-dd"></div>
          <input class="form-control" type="text" id="timestamp" name="timestamp" required>
        </div>
      </div>

      <h3>Notes</h3>
      <div class="form-group">
        <div class="input-group">
          <input class="form-control" type="text" id="notes" name="notes">
        </div>
      </div>

      <button type="submit" class="btn btn-primary">Submit</button>
      </form>
<?php include('inc/footer.php'); ?>
