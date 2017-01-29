<?php
/**
 * Created by PhpStorm.
 * User: Rashid
 * Date: 26-Jan-17
 * Time: 2:13 PM
 */

require_once('../includes/config.php');
require_once('../includes/common.php');
require_once('../includes/db.php');

requireLoggedIn();

$user = getUserDetails($_SESSION['user_id']);

try {
    $stmt = $db->prepare('SELECT * FROM transactions WHERE from_user_id = ? OR to_user_id = ?');
    $stmt->execute([$user['id'], $user['id']]);
    $transactions = $stmt->fetchAll();
} catch (PDOException $e) {
    handleSqlErrors($e);
}

if (isset($transactions)) {
    if (count($transactions) < 1) {
        $transactions_html = '<tr><td>No transactions to view</td></tr>';
    } else {
        $transactions_html = '';
        foreach ($transactions as $txn) {
            $id = md5($txn['id']);
            $from_user = getUserDetails($txn['from_user_id']);
            $to_user = getUserDetails($txn['to_user_id']);
            $time = date('d-m-Y H:i:s', $txn['time']);
            $transactions_html .= <<<TXN
<tr>
<td>{$id}</td>
<td>{$from_user['first_name']} {$from_user['last_name']}</td>
<td>{$to_user['first_name']} {$to_user['last_name']}</td>
<td>{$txn['amount']} OMR</td>
<td>{$time}</td>
</tr>
TXN;
        }


    }
}


?>
<?= getHead('Dashboard'); ?>
<br><br>
<div class="container">
    <p style="font-size: 22px;">Hello, <?= $user['first_name'] ?> <?= $user['last_name'] ?>.</p>
    <a href="/user/logout.php" class="btn btn-default btn-xs">Logout</a>
    <hr>
    <p><b><i class="fa fa-money" aria-hidden="true"></i> Current balance: <?= $user['balance'] ?> OMR</b></p>
    <hr>
    <div class="row">
        <div class="col-md-4">
            <h2>Feeling generous?</h2>
            <hr>
            <p>Lenders like you support and sustain the <?= SITE_NAME ?> eco-system. Helping borrowers start their
                business contributes to your community as well as unemployment rates and the GDP. If you're looking to
                support the growth of this country, then this is the perfect opportunity for you.</p>
            <a href="/loans.php" class="btn btn-primary btn">Start Lending</a>
        </div>
        <div class="col-md-4">
            <h2>Need a loan?</h2>
            <hr>
            <p>All businesses need startup costs, and everyone knows that loans from conventional banks are expensive,
                and they usually are not available for the unemployed. Let the community help fund you startup idea, and
                in turn receive their support. Best thing is, it's free! No strings attached.</p>
            <a href="/user/apply.php" class="btn btn-success btn">Apply For a Loan</a>
        </div>
        <div class="col-md-4">
            <h2>Not enough balance?</h2>
            <hr>
            <p>To start lending you need have enough balance in your account. <?= SITE_NAME ?> supports different
                payment methods like PayPal, credit cards, debit cards, bank transfers, and cash deposits. Choose the
                perfect payment method for you and start supporting your community</p>
            <a href="/user/funds.php" class="btn btn-warning btn">Add Funds To My Account</a>
        </div>
    </div>
    <hr>
    <div class="row">
        <h3>Transactions</h3>
        <table class="table table-striped table-hover ">
            <thead>
            <tr>
                <th>Transaction ID</th>
                <th>From</th>
                <th>To</th>
                <th>Amount</th>
                <th>Time</th>
            </tr>
            </thead>
            <tbody>
            <?= $transactions_html ?>
            </tbody>
        </table>

    </div>
</div>
<?= getFoot() ?>
