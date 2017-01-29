<?php
/**
 * Created by PhpStorm.
 * User: Rashid
 * Date: 27-Jan-17
 * Time: 6:21 PM
 */

require_once('../includes/config.php');
require_once('../includes/common.php');
require_once('../includes/db.php');

requireLoggedIn();

unset($_SESSION['user_id']);
redirect('/index.php?logout');