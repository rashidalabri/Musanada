<?php
/**
 * Created by PhpStorm.
 * User: Rashid
 * Date: 26-Jan-17
 * Time: 12:50 PM
 */

function getHead($title, $append = '')
{
    $site_name = SITE_NAME;
    $button = '<li><div class="btn-nav"><a class="btn btn-success navbar-btn" href="/user/apply.php"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Apply for a loan</a></div></li>';
    if (isset($_SESSION['user_id'])) {
        $button = '<li><div class="btn-nav"><a class="btn btn-success navbar-btn" href="/user/index.php"><i class="fa fa-user" aria-hidden="true"></i> Dashboard</a></div></li>';
    }
    $head = <<<HEAD
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$site_name} - {$title}</title>
    <link href="/assets/vendor/bootswatch/yeti/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/main.css" rel="stylesheet">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="https://use.fontawesome.com/213ee88f88.js"></script>
    
    {$append}
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">{$site_name}</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="/"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Home</a></li>
                <li><a href="/about.php"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;About us</a></li>
                <li><a href="/loans.php"><i class="fa fa-money" aria-hidden="true"></i>&nbsp;&nbsp; Loans</a></li>
            </ul>
            <ul class="nav nav-navbar navbar-right">
                {$button}
            </ul>
        </div>
    </div>
</nav>
HEAD;
    return $head;
}

function getFoot($append = '')
{
    $site_name = SITE_NAME;
    $foot = <<<FOOT
<div class="container footer">
    <div class="panel panel-default">
        <div class="panel-body">
            &copy; All Rights Reserved to {$site_name}.
            <span class="pull-right">
                Crafted with <i class="fa fa-heart" aria-hidden="true"></i> by Rashid Al Abri. Site is still in alpha stage.
            </span>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="/assets/vendor/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
FOOT;
    return $foot;
}

function handleSqlErrors(PDOException $e)
{
    $message = 'Error Handling Request.<br>';
    if (DEBUG) {
        $message .= <<<MESSAGE
Exception message: {$e->getMessage()}
MESSAGE;
    }
    echo $message;
    exit();
}

function safeOutput($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function generateAlertsHtml(array $messages)
{
    $errors_html = '';
    foreach ($messages as $message) {
        $errors_html .= <<<ERROR
<div class="alert alert-dismissible alert-{$message['type']}">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  {$message['text']}
</div>
ERROR;
    }
    return $errors_html;
}

function redirect($page)
{
    header('Location: ' . ROOT_URL . $page);
}

function requireLoggedIn()
{
    if (!isset($_SESSION['user_id'])) {
        redirect('/user/login.php?notloggedin');
        exit();
    }

    try {
        $db = $GLOBALS['db'];
        $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $rows = $stmt->fetch(PDO::FETCH_NUM);
        $count = $rows[0];
        if ($count !== 1) {
            unset($_SESSION['user_id']);
            redirect('/user/login.php');
            exit();
        }
    } catch (PDOException $e) {
        handleSqlErrors($e);
    }
}

function getUserDetails($user_id)
{
    $user_details = false;
    try {
        $db = $GLOBALS['db'];
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$user_id]);
        $rows = $stmt->fetchAll();
        if (count($rows) < 1) {
            return false;
        }
        $user_details = $rows[0];
    } catch (PDOException $e) {
        handleSqlErrors($e);
    }
    return $user_details;
}

function getLoanDetails($loan_slug)
{
    $loan_details = false;
    try {
        $db = $GLOBALS['db'];
        $stmt = $db->prepare('SELECT * FROM loans WHERE slug = ?');
        $stmt->execute([$loan_slug]);
        $rows = $stmt->fetchAll();
        if (count($rows) == 1) {
            $loan_details = $rows[0];
        }
    } catch (PDOException $e) {
        handleSqlErrors($e);
    }
    return $loan_details;
}


function generateLoanSlug($length = 5)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    do {

        // Generate slug
        $slug = '';
        for ($i = 0; $i < $length; $i++) {
            $slug .= $characters[rand(0, $charactersLength - 1)];
        }

        // Check if slug is unique
        try {
            $db = $GLOBALS['db'];
            $stmt = $db->prepare('SELECT COUNT(*) FROM loans WHERE slug = ?');
            $stmt->execute([$slug]);
            $rows = $stmt->fetch(PDO::FETCH_NUM);
            $count = $rows[0];
            $unique = ($count == 0);
        } catch (PDOException $e) {
            handleSqlErrors($e);
            break;
        }
    } while ($unique == false);

    return $slug;
}

function sendMoney($from_user_id, $to_user_id, $amount)
{
    $db = $GLOBALS['db'];

    // Check if from_user exits and has enough balance
    $from_user = getUserDetails($from_user_id);
    if ($from_user == false || $from_user['balance'] < $amount) {
        return false;
    }

    // Check if to_user exists
    $to_user = getUserDetails($to_user_id);
    if ($to_user == false) {
        return false;
    }

    // Create transaction
    try {
        $stmt = $db->prepare('INSERT INTO transactions (from_user_id, to_user_id, amount, time) VALUES (?, ?, ?, ?)');
        $stmt->execute([$from_user_id, $to_user_id, $amount, time()]);
    } catch (PDOException $e) {
        handleSqlErrors($e);
    }

    // Update from_user balance
    try {
        $stmt = $db->prepare('UPDATE users SET balance = balance - ? WHERE id = ?');
        $stmt->execute([$amount, $from_user_id]);
    } catch (PDOException $e) {
        handleSqlErrors($e);
    }

    // Update to_user balance
    try {
        $stmt = $db->prepare('UPDATE users SET balance = balance + ? WHERE id = ?');
        $stmt->execute([$amount, $to_user_id]);
    } catch (PDOException $e) {
        handleSqlErrors($e);
    }

    return true;
}

function lend($loan_slug, $lender_id, $amount)
{
    $db = $GLOBALS['db'];

    $loan = getLoanDetails($loan_slug);

    if ($loan === false) {
        redirect('/index.php?lend_error');
    }

    $lender = getUserDetails($lender_id);

    try {
        $stmt = $db->prepare('UPDATE loans SET amount_raised = amount_raised + ? WHERE id = ?');
        $stmt->execute([$amount, $loan['id']]);
    } catch (PDOException $e) {
        handleSqlErrors($e);
    }

    sendMoney($lender_id, $loan['user_id'], $amount);

    if (($amount + $loan['amount_raised']) >= $loan['amount']) {
        try {
            $stmt = $db->prepare('UPDATE loans SET ended = 1 WHERE id = ?');
            $stmt->execute([$loan['id']]);
        } catch (PDOException $e) {
            handleSqlErrors($e);
        }
    }

}