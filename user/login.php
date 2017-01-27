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

$alerts = array();

if ($_POST) {

    $securimage = new Securimage();

    if ($securimage->check($_POST['captcha_code']) == false) {
        $alerts[] = ['text' => 'The captcha you entered is incorrect.', 'type' => 'danger'];
    }

    // Store POST details
    $email_address = $_POST['email_address'];
    $raw_password = $_POST['password'];

    // Validate email address
    if (!filter_var($email_address, FILTER_VALIDATE_EMAIL) || strlen($email_address) > 255 || empty($email_address)) {
        $alerts[] = ['text' => 'The email address is invalid.', 'type' => 'danger'];
    }

    // Validate password
    if (strlen($raw_password) > 255 || empty($raw_password)) {
        $alerts[] = ['text' => 'Password is invalid.', 'type' => 'danger'];
    }

    if (count($alerts) == 0) {

        try {
            $stmt = $db->prepare('SELECT id, password_hash FROM users WHERE email_address = ?');
            $stmt->execute([$email_address]);
            $rows = $stmt->fetchAll();

            if (count($rows) == 1) {
                $user_id = $rows[0]['id'];
                $password_hash = $rows[0]['password_hash'];

                if (password_verify($raw_password, $password_hash)) {
                    $_SESSION['user_id'] = $user_id;
                    redirect('/user/index.php');
                } else {
                    $alerts[] = ['text' => 'Your email address or password is incorrect.', 'type' => 'danger'];
                }

            } else {
                $alerts[] = ['text' => 'Your email address or password is incorrect.', 'type' => 'danger'];
            }

        } catch (PDOException $e) {
            handle_sql_errors($e);
        }
    }
}

if (isset($_GET['register'])) {
    $alerts[] = ['text' => 'You have been successfully registered.', 'type' => 'success'];
}
?>
<?= get_head('Login') ?>
    <br><br>
    <div class="container">
        <div class="well col-md-6 col-md-offset-3">
            <form class="form-horizontal" method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
                <fieldset>
                    <legend>Log into your account</legend>
                    <?= generate_alerts_html($alerts) ?>
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
                            <input type="text" class="form-control" id="inputCaptchaCode" name="captcha_code"
                                   maxlength="6">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-10 col-lg-offset-2">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
<?= get_foot() ?>