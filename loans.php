<?php
/**
 * Created by PhpStorm.
 * User: Rashid
 * Date: 27-Jan-17
 * Time: 7:03 PM
 */

require_once('includes/config.php');
require_once('includes/common.php');
require_once('includes/db.php');

// Set pagination options
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$limit = 12;

// Get total number of records
$total = 0;
try {
    $stmt = $db->prepare('SELECT COUNT(*) FROM loans');
    $stmt->execute();
    $rows = $stmt->fetch(PDO::FETCH_NUM);
    $total = $rows[0];
} catch (PDOException $e) {
    handleSqlErrors($e);
}

// Create pagination buttons
$previous_btn = '';
$next_btn = '';

if ($page > 1) {
    $previous_page = $page - 1;
    $previous_btn = <<<PREVIOUS
<li class="previous"><a href="loans.php?page={$previous_page}">&larr; Previous Page</a></li>    
PREVIOUS;
}

if ($page < ceil($total / $limit)) {
    $next_page = $page + 1;
    $next_btn = <<<NEXT
<li class="next"><a href="loans.php?page={$next_page}">Next Page &rarr;</a></li>    
NEXT;
}

$pagination_btns = $previous_btn . $next_btn;


// Get loans from database
$loans = [];
try {
    $stmt = $db->prepare('SELECT * FROM loans ORDER BY start_date DESC LIMIT ? OFFSET ?');
    $stmt->execute([$limit, ($page - 1) * $limit]);
    $loans = $stmt->fetchAll();
} catch (PDOException $e) {
    handleSqlErrors($e);
}

// Generate loan list HTML
$loans_html = '';
foreach ($loans as $loan) {

    // Get user details
    $user = getUserDetails($loan['user_id']);

    // Find the percentage of raised amount
    $raised_percent = ($loan['amount_raised'] / $loan['amount']) * 100;

    $safe_short_description = safeOutput($loan['short_description']);

    if ($loan['ended'] == 0) {
        $days_left = ceil(($loan['end_date'] - time()) / (24 * 60 * 60));
        $loans_html .= <<<LOAN
<div class="col-md-3 col-sm-6">
    <img class="img-responsive" src="/uploads/{$loan['image']}">
    <div class="panel panel-default">
        <div class="panel-body">
            <p class="text-primary"><b>{$user['first_name']} {$user['last_name']}</b> <span class="label label-primary pull-right">{$days_left} days left</span></p>
            <p>{$safe_short_description}</p>
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
    } else {
        $loans_html .= <<<LOAN
<div class="col-md-3 col-sm-6">
    <img class="img-responsive" src="/uploads/{$loan['image']}">
    <div class="panel panel-default">
        <div class="panel-body">
            <p class="text-primary"><b>{$user['first_name']} {$user['last_name']}</b> <span class="label label-primary pull-right">Ended</span></p>
            <p>{$safe_short_description}</p>
            <div class="progress progress-striped active">
                <div class="progress-bar" style="width: {$raised_percent}%"></div>
            </div>
            <div class="btn-group btn-group-justified">
                <a href="/loan.php?id={$loan['slug']}" class="btn btn-success" data-toggle="modal" data-target="#learnMoreModal">Learn More</a>
            </div>
        </div>
    </div>
</div>
LOAN;
    }
}
?>
<?= getHead('Home') ?>
<div class="container">
    <h2>Loans</h2>
    <hr>
    <div class="row">
        <?= $loans_html ?>
    </div>
    <ul class="pager">
        <?=$pagination_btns?>
    </ul>

</div>
<?= getFoot() ?>

