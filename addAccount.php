<?php
include('inc/mysql.php');
include('inc/classes.php');
include('inc/header.php');

$username='quackau';
$user = new CryptoUser('quackau');

if (isset($_POST) and empty(array_diff(array('symbol','nickname'),array_keys($_POST)))){
  $symbol=$_POST['symbol'];
  $nickname=$_POST['nickname'];
  $result = $user->addAccount($symbol,$nickname);
  if ($result) {
    echo '<div class="alert alert-success alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Success!</strong> Account inserted into database.
</div>';
  } else {
    echo '<div class="alert alert-danger alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Failed!</strong> Account not inserted into database.
</div>';
  }
}
?>

      <h2>Add Account</h2>
      <form class="form-inline" action="addAccount.php" method="post">
      <div class="form-group">
        <div class="input-group">
          <div class="input-group-addon">Symbol</div>
          <input class="form-control" type="text" name="symbol" id="symbol" maxlength="3" required>
        </div>
        <div class="input-group">
          <div class="input-group-addon">Nickname</div>
          <input class="form-control" type="text" name="nickname" id="nickname" required>
        </div>
      </div>
      <br><br>
      <button type="submit" class="btn btn-primary">Submit</button>
      </form>

<?php include('inc/footer.php');
