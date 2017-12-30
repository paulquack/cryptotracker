<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Crypto Tracker</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">

        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-datepicker3.min.css">

        <style>
            body {
                padding-top: 50px;
                padding-bottom: 20px;
            }
        </style>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="css/main.css">

        <script src="js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Crypto Tracker</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

<?php
include('inc/mysql.php');
include('inc/classes.php');
$username='quackau';
$user = new CryptoUser('quackau');
//    public function addTransaction($from_account,$from_symbol,$from_amount,$to_account,$to_symbol,$to_amount,$timestamp = false)
?>

    <div class="container">
          <h2>Add Transaction</h2>
          <form action="addTransaction.php" method="post">
            <div class="form-row">
              <div class="col-md-4">
                <label for="from_account">From</label>
                <select class="form-control" name="from_account" id="from_account">
                <?php
                $accounts = $user->getAccounts();
                foreach ($accounts as $a){
                  printf('                <option value="%u">%s (%s)</option>'."\n", $a->getId(), $a->getNickname(), $a->getSymbol());
                }
                ?>
              </select>
            </div>
            <div class="col-md-4">

              <label for="from_amount">Amount</label>
              <input class="form-control" type="text" name="from_amount" id="from_amount">
            </div>
            <div class="form-group">
              <label for="to_account">To</label><br>
              <select name="to_account" id="to_account">
                <?php
                $accounts = $user->getAccounts();
                foreach ($accounts as $a){
                  printf('                <option value="%u">%s (%s)</option>'."\n", $a->getId(), $a->getNickname(), $a->getSymbol());
                }
                ?>
              </select><br>
              <label for="to_amount">Amount</label><br>
              <input type="text" name="to_amount" id="to_amount">
            </div>
            <div class="form-group">
              <label for="datepicker">Date</label><br>
              <div id="datepicker" data-date="0"></div>
              <input type="hidden" id="timestamp" name="timestamp">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>
    </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
        <script src="js/vendor/bootstrap.min.js"></script>
        <script src="js/vendor/bootstrap-datepicker.min.js"></script>
        <script src="js/main.js"></script>
        <script>
        $('#datepicker').datepicker();
        $('#datepicker').on('changeDate', function() {
            $('#timestamp').val(
                $('#datepicker').datepicker('getFormattedDate')
            );
        });
        </script>
    </body>
</html>
