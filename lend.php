<?php
/**
 * Created by PhpStorm.
 * User: Rashid
 * Date: 29-Jan-17
 * Time: 2:05 PM
 */

require_once('includes/config.php');
require_once('includes/common.php');
require_once('includes/db.php');
require_once('libraries/securimage/securimage.php');

requireLoggedIn();

if (!isset($_GET['id']) || !ctype_alnum($_GET['id'])) {
    redirect('/loans.php');
}

$lender = getUserDetails($_SESSION['user_id']);

$loan_slug = $_GET['id'];
$loan = getLoanDetails($loan_slug);

if ($loan === false) {
    redirect('/index.php');
}

$borrower = getUserDetails($loan['user_id']);
$amount_remaining = $loan['amount'] - $loan['amount_raised'];


$amount = '';
$alerts = array();

if ($_POST) {

    $amount = trim($_POST['amount']);

    $securimage = new Securimage();

    if ($securimage->check(trim($_POST['captcha_code'])) == false) {
        $alerts[] = ['text' => 'The captcha you entered is incorrect.', 'type' => 'danger'];
    }

    // Validate amount
    if (!is_numeric($amount)) {
        $alerts[] = ['text' => 'Invalid amount.', 'type' => 'danger'];
    } elseif ($amount > $amount_remaining) {
        // Make sure amount does not exceed needed amount to fully fund loan
        $alerts[] = ['text' => 'Your amount is too generous, the maximum is ' . $amount_remaining, 'type' => 'danger'];

    } elseif ($lender['balance'] < $amount) {
        // Check if logged in user has enough balance
        $alerts[] = ['text' => 'You do not have enough balance', 'type' => 'danger'];
    }

    if (count($alerts) == 0) {
        lend($loan_slug, $lender['id'], $amount);
        redirect('/loan.php?thanks&id=' . $loan_slug);
    }
}
?>
<?= getHead('Lend ' . $borrower['first_name']) ?>
<br><br>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="well">
                <form class="form-horizontal" action="" method="post">
                    <fieldset>
                        <legend>Lend <?= $borrower['first_name'] ?> <?= $borrower['last_name'] ?></legend>
                        <?= generateAlertsHtml($alerts) ?>
                        <div class="form-group">
                            <label for="inputAmount" class="col-lg-2 control-label">Amount</label>
                            <div class="col-lg-10">
                                <input type="number" class="form-control" id="inputAmount" name="amount"
                                       value="<?= $amount ?>">
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
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
<?= getFoot() ?>
