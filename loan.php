<?php
/**
 * Created by PhpStorm.
 * User: Rashid
 * Date: 28-Jan-17
 * Time: 3:08 PM
 */

require_once('includes/config.php');
require_once('includes/common.php');
require_once('includes/db.php');

if (!isset($_GET['id']) || !ctype_alnum($_GET['id'])) {
    redirect('/loans.php');
}

$loan_slug = $_GET['id'];
$loan = getLoanDetails($loan_slug);
$user = getUserDetails($loan['user_id']);

$alerts = array();

// Find the percentage of raised amount
$raised_percent_unformated = ($loan['amount_raised'] / $loan['amount']) * 100;
$raised_percent = number_format((float)$raised_percent_unformated, 2, '.', '');

$days_left = ceil(($loan['end_date'] - time()) / (24 * 60 * 60));

if($days_left < 0) {
    $days_left = 0;
}

$amount_remaining = $loan['amount'] - $loan['amount_raised'];

if (isset($_GET['thanks'])) {
    $alerts[] = ['text' => '<b>Thanks for supporting ' . $user['first_name'] . '. </b>You will start receiving payments once the loan has been fully funded.', 'type' => 'success'];
}
?>
<?= getHead($loan['title']) ?>
<br><br>
<div class="container">
    <?= generateAlertsHtml($alerts) ?>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="col-md-6"><img class="img-responsive" src="/uploads/<?= $loan['image'] ?>"></div>
            <div class="col-md-6">
                <div class="well">
                    <h2><?= $raised_percent ?>% funded</h2>
                    <h5><?= $days_left ?> days left<span class="pull-right"><?= $amount_remaining ?> OMR to go</span>
                    </h5>
                    <div class="progress progress-striped active">
                        <div class="progress-bar" style="width: <?= $raised_percent ?>%"></div>
                    </div>
                    <p><b>Total loan:</b> <?= $loan['amount'] ?> OMR</p>
                    <p><b>Loan length:</b> <?= $loan['length'] ?> months</p>
                </div>

            </div>
        </div>
    </div>
    <br><br>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="jumbotron">
                <h2><?= safeOutput($loan['title']) ?></h2>
                <p><?= safeOutput($loan['short_description']) ?></p>
                <p><a href="/lend.php?id=<?=$loan_slug?>" class="btn btn-primary btn-lg">Lend <?= $user['first_name'] ?></a></p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <h2><?= $user['first_name'] ?>'s story</h2>
            <hr>
            <p><?= safeOutput($loan['long_description']) ?></p>
        </div>
    </div>


</div>
<?= getFoot() ?>
