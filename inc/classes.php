<?php

class CryptoUser {
    private $id;
    private $username;
    private $accounts;

    public function __construct($username){
        $a = mysql_query(sprintf("SELECT `id`,`username` FROM `users` WHERE `username`='%s'",mysql_real_escape_string($username)));
        if ($a!==false and mysql_num_rows($a) == 1) {
            $row=mysql_fetch_assoc($a);
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->populateAccounts();
        } else { echo mysql_error()."\n"; }
    }

    public function populateAccounts(){
        $this->accounts = array();
        $a = mysql_query(sprintf("SELECT `id`,`symbol`,`nickname` FROM `accounts` WHERE `user_id`=%u",intval($this->id)));
        while ($row = mysql_fetch_assoc($a)) {
            $this->accounts[$row['id']] = new CryptoAccount($row['id'], $row['symbol'], $row['nickname']);
        }
    }

    public function addAccount($symbol,$nickname){
        $result = false;
        mysql_query(sprintf("INSERT INTO `accounts`(`user_id`,`symbol`,`nickname`) VALUES(%u,'%s','%s')", $this->id, mysql_real_escape_string($symbol), mysql_real_escape_string($nickname)));
        if (mysql_affected_rows()==1){
          $result = true;
          $id = mysql_insert_id();
          $this->accounts[$id] = new CryptoAccount($id, $symbol, $nickname);
        } else { echo mysql_error(); }
        return $result;
    }

    public function addTransaction($from_account,$from_amount,$to_account,$to_amount,$timestamp = false,$notes = ''){
        $result = false;
        if (!$timestamp) $timestamp = date('Y-m-d H:i:s');
        if (array_key_exists($from_account, $this->accounts) and array_key_exists($to_account, $this->accounts)){
            $from_symbol = $this->accounts[$from_account]->getSymbol();
            $to_symbol = $this->accounts[$to_account]->getSymbol();

            mysql_query(sprintf("INSERT INTO `transactions`(`from_account`,`from_symbol`,`from_amount`,`to_account`,`to_symbol`,`to_amount`,`timestamp`,`notes`) VALUES (%u,'%s',%f,%u,'%s',%f,'%s','%s')",
                                intval($from_account),mysql_real_escape_string($from_symbol),floatval($from_amount),
                                intval($to_account),mysql_real_escape_string($to_symbol),floatval($to_amount),
                                $timestamp,mysql_real_escape_string($notes)));
            if (mysql_affected_rows()==1) $result = true;
            echo mysql_error();
        }
        $this->accounts[$from_account]->populateTransactions();
        $this->accounts[$to_account]->populateTransactions();
        return $result;
    }

    public function getId(){
        return $this->id;
    }

    public function getAccounts(){
        return $this->accounts;
    }
}


class CryptoAccount {
    private $id;
    private $symbol;
    private $nickname;
    private $transactions;
    private $balance;

    public function __construct($id, $symbol, $nickname){
        $this->id = intval($id);
        $this->symbol = $symbol;
        $this->nickname = $nickname;
        $this->transactions = array();
        $this->balance = 0;
        $this->populateTransactions();
    }

    public function populateTransactions(){
        $this->balance = 0;
        $this->transactions = array();
        $debits = mysql_query(sprintf("SELECT `id`,`timestamp`,`from_amount`,`to_account`,`to_symbol`,`to_amount`,`notes` FROM `transactions` WHERE `from_account`=%u ORDER BY `timestamp` ASC", $this->id));
        while ($row = mysql_fetch_assoc($debits)){
            $description = sprintf("%f %s to \"%s\"", $row['to_amount'], $this->getSymbol($row['to_account']), $this->getNickname($row['to_account']));
            if (!empty($row['notes'])) $description.=" - {$row['notes']}";
            $this->transactions[] = new CryptoTransaction($row['id'], $row['timestamp'], -$row['from_amount'], $description);
            $this->balance -= $row['from_amount'];
        }
        $credits = mysql_query(sprintf("SELECT `id`,`timestamp`,`to_amount`,`from_account`,`from_symbol`,`from_amount`,`notes` FROM `transactions` WHERE `to_account`=%u ORDER BY `timestamp` ASC", $this->id));
        while ($row = mysql_fetch_assoc($credits)){
            $description = sprintf("%f %s from \"%s\"", $row['from_amount'], $this->getSymbol($row['from_account']), $this->getNickname($row['from_account']));
            if (!empty($row['notes'])) $description.=" - {$row['notes']}";
            $this->transactions[] = new CryptoTransaction($row['id'], $row['timestamp'], $row['to_amount'], $description);
            $this->balance += $row['to_amount'];
        }
        usort($this->transactions, function($a, $b)
        {
            return strcmp($a->name, $b->name);
        });
    }

    public function getNickname($id = false){
        if (!$id) return($this->nickname);
        $a = mysql_query(sprintf("SELECT `nickname` FROM `accounts` WHERE `id` = %u", $id));
        if (mysql_num_rows($a)) {
            $row = mysql_fetch_row($a);
            return($row[0]);
        }
        return(false);
    }

    public function getSymbol($id = false){
        if (!$id) return $this->symbol;
        $a = mysql_query(sprintf("SELECT `symbol` FROM `accounts` WHERE `id` = %u", $id));
        if (mysql_num_rows($a)) {
            $row = mysql_fetch_row($a);
            return($row[0]);
        }
        return false;
    }

    public function getBalance($atTime = false){
        $balance = 0;
        if (!$atTime) return $this->balance;
        foreach ($this->transactions as $t){
            if (strcmp($t->timestamp,$atTime) > 0) return $balance;
            $balance += $t->amount;
        }
        return $balance;
    }

    public function getDailyBalance($start, $end){
        $statement = $this->getStatement();
        $i = 0;
        $startDay = floor(strtotime($start)/86400);
        $endDay = floor(strtotime($end)/86400);
        $result=array();
        for ($curDay = $startDay; $curDay <= $endDay; $curDay++){

            while ($i < count($statement) and floor(strtotime($statement[$i]['timestamp'])/86400) <= $curDay){
                $i++;
            }
            echo "Using transaction " . ($i-1) . " Date: " . floor(strtotime($statement[$i]['timestamp'])/86400) . " > $curDay\n";
            $result[date('Y-m-d',$curDay*86400)] = (($i>0) ? round($statement[$i-1]['balance'],8) : 0);
        }
        return $result;
    }

    public function getId(){
        return $this->id;
    }

    public function getStatement(){
        $result = array();
        $balance = 0;
        foreach ($this->transactions as $t){
            $balance += $t->amount;
            $result[] = array(
                'timestamp'=>$t->timestamp,
                'amount'=>$t->amount,
                'description'=>$t->desciption,
                'balance'=>$balance);
        }
        return $result;
    }
}


class CryptoTransaction {
    private $id;
    public $timestamp;
    public $amount;
    public $description;

    public function __construct($id, $timestamp, $amount, $description){
        $this->id = $id;
        $this->timestamp = $timestamp;
        $this->amount = $amount;
        $this->description = $description;
    }
}
?>
