<?php
/**
 * Created by PhpStorm.
 * User: Rashid
 * Date: 26-Jan-17
 * Time: 11:19 AM
 */

require_once('includes/config.php');
require_once('includes/common.php');
require_once('includes/db.php');

$alerts = array();


// Get featured loans from database
$featured_loans = [];
try {
    $stmt = $db->prepare('SELECT * FROM loans WHERE ended = 0 ORDER BY amount_raised DESC LIMIT 4');
    $stmt->execute();
    $featured_loans = $stmt->fetchAll();
} catch (PDOException $e) {
    handleSqlErrors($e);
}

$featured_loans_html = '';
foreach ($featured_loans as $loan) {
    $user = getUserDetails($loan['user_id']);
    $raised_percent = ($loan['amount_raised'] / $loan['amount']) * 100;
    $days_left = ceil(($loan['end_date'] - time()) / (24 * 60 * 60));
    $featured_loans_html .= <<<LOAN
<div class="col-md-3 col-sm-6">
    <img class="img-responsive" src="/uploads/{$loan['image']}">
    <div class="panel panel-default">
        <div class="panel-body">
            <p class="text-primary"><b>{$user['first_name']} {$user['last_name']}</b> <span class="label label-primary pull-right">{$days_left} days left</span></p>
            <p>{$loan['short_description']}</p>
            <div class="progress progress-striped active">
                <div class="progress-bar" style="width: {$raised_percent}%"></div>
            </div>
            <div class="btn-group btn-group-justified">
                <a href="/lend.php?id={$loan['slug']}" class="btn btn-success">Lend</a>
                <a href="/loan.php?id={$loan['slug']}" class="btn btn-success">Learn More</a>
            </div>
        </div>
    </div>
</div>
LOAN;
}

if (isset($_GET['logout'])) {
    $alerts[] = ['text' => 'You have been logged out.', 'type' => 'success'];
}
?>
<?= getHead('Home') ?>
<div class="jumbotron jumbotron-home">
    <div class="container">
        <h2>Eradicating Unemployment, One Loan At A Time.</h2>
        <p>At <?= SITE_NAME ?>, we believe that every Omani has the right to live and realize their true potential.
            Financial services are seldom offered to the unemployed, and we aim to change that by creating a sustainable
            social lending platform.</p>
        <p><a class="btn btn-success" href="#" role="button">Learn more &raquo;</a></p>
    </div>
</div>

<div class="container">
    <?= generateAlertsHtml($alerts) ?>
    <h1 id="start-lending" class="text-center">Start Lending</h1>

    <hr>

    <div class="row">
        <?= $featured_loans_html ?>
    </div>

    <div class="row">
        <div class="col-md-12 text-center">
            <a href="loans.php" class="btn btn-default">View more</a>
        </div>
    </div>
</div>


<!--<div class="container-fluid stats">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-sm-4 box text-center">
                <div class="col-md-12">
                    <h1>0.00%</h1>
                    <p>Loan repayment rate</p>
                </div>
            </div>

            <div class="col-md-4 col-sm-4 box text-center">
                <div class="col-md-12">
                    <h1>4</h1>
                    <p>Loans posted online</p>
                </div>
            </div>

            <div class="col-md-4 col-sm-4 box text-center">
                <div class="col-md-12">
                    <h1>980 OMR</h1>
                    <p>Raised in total</p>
                </div>
            </div>
        </div>
    </div>
</div>-->
<?= getFoot() ?>
