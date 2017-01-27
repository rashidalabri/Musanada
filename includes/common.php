<?php
/**
 * Created by PhpStorm.
 * User: Rashid
 * Date: 26-Jan-17
 * Time: 12:50 PM
 */

function get_head($title, $append = '')
{
    $site_name = SITE_NAME;
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
                <li><a href=""><i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;About us</a></li>
                <li><a href=""><i class="fa fa-cogs" aria-hidden="true"></i>&nbsp;&nbsp;How it Works</a></li>
                <li><a href=""><i class="fa fa-comments" aria-hidden="true"></i>&nbsp;&nbsp;Contact Us</a></li>
            </ul>
            <ul class="nav nav-navbar navbar-right">
                <li><div class="btn-nav"><a class="btn btn-success navbar-btn" href="#">Apply for a loan</a></div></li>
            </ul>
        </div>
    </div>
</nav>
HEAD;
    return $head;
}

function get_foot($append = '')
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

function handle_sql_errors(PDOException $e)
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

function safe_output($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function generate_alerts_html(array $messages)
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

function require_logged_in()
{
    if (!isset($_SESSION['user_id'])) {
        redirect('/user/login.php?notloggedin');
    }
}

function get_user_details($user_id)
{
    $user_details = false;
    try {
        $db = $GLOBALS['db'];
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$user_id]);
        $rows = $stmt->fetchAll();
        $user_details = $rows[0];
    } catch (PDOException $e) {
        handle_sql_errors($e);
    }
    return $user_details;
}