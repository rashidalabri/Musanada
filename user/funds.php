<?php
/**
 * Created by PhpStorm.
 * User: Rashid
 * Date: 29-Jan-17
 * Time: 1:45 PM
 */

require_once('../includes/config.php');
require_once('../includes/common.php');
require_once('../includes/db.php');

requireLoggedIn();

// STILL UNDER DEVELOPMENT
?>
<?= getHead('Add funds') ?>
<br><br>
<div class="container">
    <h3>Sorry, this page has been disabled.</h3><br>
    <a href="/user/index.php" class="btn btn-primary btn-sm"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i> Back
        to dashboard</a>
</div>
<?= getFoot() ?>
