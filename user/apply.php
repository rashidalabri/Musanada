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
require_once('../libraries/securimage/securimage.php');

requireLoggedIn();
$user = getUserDetails($_SESSION['user_id']);

$title = '';
$short_description = '';
$story = '';
$amount = '';
$alerts = array();

if ($_POST) {

    // Store POST details
    $title = trim($_POST['title']);
    $short_description = trim($_POST['short_description']);
    $story = trim($_POST['story']);
    $amount = trim($_POST['amount']);

    // Validate captcha
    $securimage = new Securimage();
    if ($securimage->check($_POST['captcha_code']) == false) {
        $alerts[] = ['text' => 'The captcha you entered is incorrect.', 'type' => 'danger'];
    }

    // Validate title
    if (!ctype_alpha(str_replace(' ', '', $title)) || strlen($title) > 55 || empty($title)) {
        $alerts[] = ['text' => 'The title can only contain letters and spaces. Max length is 55 characters.', 'type' => 'danger'];
    }

    // Validate amount
    if (empty($amount) || !is_numeric($amount) || $amount > MAX_LOAN_AMOUNT) {
        $alerts[] = ['text' => 'Your loan amount is invalid. Max loan amount is ' . MAX_LOAN_AMOUNT, 'type' => 'danger'];
    }

    if (count($alerts) == 0) {

        // Upload script source http://php.net/manual/en/features.file-upload.php#114004
        try {

            // Undefined | Multiple Files | $_FILES Corruption Attack
            // If this request falls under any of them, treat it invalid.
            if (
                !isset($_FILES['upfile']['error']) ||
                is_array($_FILES['upfile']['error'])
            ) {
                throw new RuntimeException('Invalid image parameters.');
            }

            // Check $_FILES['upfile']['error'] value.
            switch ($_FILES['upfile']['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('No image file sent.');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('Exceeded filesize limit for image.');
                default:
                    throw new RuntimeException('Unknown errors for image.');
            }

            // You should also check filesize here.
            if ($_FILES['upfile']['size'] > MAX_IMAGE_SIZE) {
                $size = $_FILES['upfile']['size'];
                throw new RuntimeException('Exceeded filesize limit for image.');
            }

            // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
            // Check MIME Type by yourself.
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            if (false === $ext = array_search(
                    $finfo->file($_FILES['upfile']['tmp_name']),
                    array(
                        'jpg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                    ),
                    true
                )
            ) {
                throw new RuntimeException('Invalid file format for image. Allowed formats are JPEG, PNG and GIF.');
            }

            // You should name it uniquely.
            // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
            // On this example, obtain safe unique name from its binary data.
            $image_name = sprintf('%s.%s', sha1_file($_FILES['upfile']['tmp_name']), $ext);
            if (!move_uploaded_file(
                $_FILES['upfile']['tmp_name'],
                sprintf('../uploads/%s.%s',
                    sha1_file($_FILES['upfile']['tmp_name']),
                    $ext
                )
            )
            ) {
                throw new RuntimeException('Failed to move uploaded image.');
            }

        } catch (RuntimeException $e) {

            $alerts[] = ['text' => $e->getMessage(), 'type' => 'danger'];

        }

        if (count($alerts) == 0 && isset($image_name)) {
            $slug = generateLoanSlug();
            try {
                $stmt = $db->prepare('INSERT INTO loans (slug, user_id, title, short_description, long_description, amount, start_date, end_date, length, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$slug, $user['id'], $title, $short_description, $story, $amount, time(), time() + 30 * 24 * 60 * 60, LOAN_LENGTH, $image_name]);
            } catch (PDOException $e) {
                handleSqlErrors($e);
            }
            redirect('/loan.php?id=' . $slug);
        }
    }
}
?>
<?= getHead('Apply for a loan') ?>
<br><br>
<?=$size?>
<div class="container">
    <div class="col-md-10 col-md-offset-1">
        <form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
            <fieldset>
                <legend>Apply for a loan</legend>
                <?= generateAlertsHtml($alerts) ?>
                <div class="form-group">
                    <label for="inputTitle" class="col-lg-2 control-label">Title</label>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="inputTitle" name="title" value="<?= $title ?>">
                        <span class="help-block">A short one-sentence title that clearly expresses what you need your loan for.</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputShortDescription" class="col-lg-2 control-label">Short description</label>
                    <div class="col-lg-10">
                        <textarea class="form-control" rows="3" id="inputShortDescription"
                                  name="short_description"><?= $short_description ?></textarea>
                        <span class="help-block">Tell us a little about you and why you need the loan in a short paragraph. Make it persuasive, you want people clicking on your loan!</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputStory" class="col-lg-2 control-label">Your story</label>
                    <div class="col-lg-10">
                        <textarea class="form-control" rows="7" id="inputStory" name="story"><?= $story ?></textarea>
                        <span class="help-block">What is your story? What is your background? What are you skills? Do you have any goals and aspirations? Elaborate more on why you need this loan.</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputAmount" class="col-lg-2 control-label">Loan amount</label>
                    <div class="col-lg-10">
                        <input type="number" class="form-control" id="inputAmount" name="amount" value="<?= $amount ?>">
                        <span class="help-block">How much do you need?</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="image" class="col-lg-2 control-label">Your picture</label>
                    <div class="col-lg-10">
                        <input id="image" type="file" class="file" name="upfile">
                        <span class="help-block">Make sure you're smiling! Horizontal images work and look better on our site, keep that in mind.</span>
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
    </div>
</div>

<?= getFoot() ?>
