<?php
/**
 * Created by PhpStorm.
 * User: Rashid
 * Date: 26-Jan-17
 * Time: 2:13 PM
 */

require_once('../includes/config.php');
require_once('../includes/db.php');
require_once('../includes/common.php');

$user = get_user_details($_SESSION['user_id']);

require_logged_in();
?>
<?=get_head('Dashboard');?>
Hello <?=$user['first_name']?> <?=$user['last_name']?>
<?=get_foot()?>
