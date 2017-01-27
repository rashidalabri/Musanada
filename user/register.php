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
require_once('../libraries/securimage/securimage.php');

$email_address = '';
$raw_password = '';
$first_name = '';
$last_name = '';

$alerts = array();

if ($_POST) {

    // Store POST details
    $email_address = $_POST['email_address'];
    $raw_password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $captcha_code = $_POST['captcha_code'];

    // Validate captcha
    $securimage = new Securimage();
    if ($securimage->check($captcha_code) == false) {
        $alerts[] = ['text' => 'The captcha you entered is incorrect.', 'type' => 'danger'];
    }

    // Validate email address
    if (!filter_var($email_address, FILTER_VALIDATE_EMAIL) || strlen($email_address) > 255 || empty($email_address)) {
        $alerts[] = ['text' => 'The email address is invalid.', 'type' => 'danger'];
    } else {
        // Check if email is not used
        try {
            $stmt = $db->prepare('SELECT email_address FROM users WHERE email_address = ?');
            $stmt->execute([$email_address]);
            $rows = $stmt->fetchAll();
            if (count($rows) !== 0) {
                $alerts[] = ['text' => 'The email address is already used.', 'type' => 'danger'];
            }
        } catch (PDOException $e) {
            handle_sql_errors($e);
        }
    }

    // Validate first name
    if (!ctype_alpha(str_replace(' ', '', $first_name)) || strlen($first_name) > 255 || empty($first_name)) {
        $alerts[] = ['text' => 'Your first name appears to be incorrect.', 'type' => 'danger'];
    }

    // Validate last name
    if (!ctype_alpha(str_replace(' ', '', $last_name)) || strlen($last_name) > 255 || empty($last_name)) {
        $alerts[] = ['text' => 'Your last name appears to be incorrect.', 'type' => 'danger'];

    }

    // Validate password
    if (strlen($raw_password) > 255 || empty($raw_password)) {
        $alerts[] = ['text' => 'Password is invalid.', 'type' => 'danger'];

    } else {
        // Hash password
        $password_hash = password_hash($raw_password, PASSWORD_DEFAULT);
    }


    if (count($alerts) == 0) {
        // Insert data
        try {
            $stmt = $db->prepare('INSERT INTO users (email_address, password_hash, first_name, last_name) VALUES (?, ?, ?, ?)');
            $stmt->execute([$email_address, $password_hash, $first_name, $last_name]);
        } catch (PDOException $e) {
            handle_sql_errors($e);
        }

        // Direct to login page
        redirect('/user/login.php?register');
    }
}
?>
<?= get_head('Register') ?>
<br><br>
<div class="container">
    <div class="well col-md-6 col-md-offset-3">
        <form class="form-horizontal" method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
            <fieldset>
                <legend>Register an account</legend>
                <?= generate_alerts_html($alerts) ?>
                <div class="form-group">
                    <label for="inputFirstName" class="col-lg-2 control-label">First name</label>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="inputFirstName" name="first_name"
                               value="<?= safe_output($first_name) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputLastName" class="col-lg-2 control-label">Last name</label>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="inputLastName" name="last_name"
                               value="<?= safe_output($last_name) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail" class="col-lg-2 control-label">Email</label>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="inputEmail" name="email_address"
                               value="<?= safe_output($email_address) ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword" class="col-lg-2 control-label">Password</label>
                    <div class="col-lg-10">
                        <input type="password" class="form-control" id="inputPassword" name="password">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputCaptchaCode" class="col-lg-2 control-label">Captcha</label>
                    <div class="col-lg-10">
                        <img id="captcha" src="/libraries/securimage/securimage_show.php" alt="CAPTCHA Image"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-2">
                        <input type="text" class="form-control" id="inputCaptchaCode" name="captcha_code" maxlength="6">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-10 col-lg-offset-2">
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<?= get_foot() ?>
