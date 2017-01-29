<?php
/**
 * Created by PhpStorm.
 * User: Rashid
 * Date: 29-Jan-17
 * Time: 4:42 PM
 */

require_once('includes/config.php');
require_once('includes/common.php');
require_once('includes/db.php');
?>
<?= getHead('About us') ?>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Musanada</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="/"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;&nbsp;Home</a></li>
                <li><a href="/about.php"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;&nbsp;About us</a></li>
                <li><a href="/how-it-works.php"><i class="fa fa-cogs" aria-hidden="true"></i>&nbsp;&nbsp;How it
                        Works</a></li>
                <li><a href="/contact.php"><i class="fa fa-comments" aria-hidden="true"></i>&nbsp;&nbsp;Contact Us</a>
                </li>
            </ul>
            <ul class="nav nav-navbar navbar-right">
                <li>
                    <div class="btn-nav"><a class="btn btn-success navbar-btn" href="/apply.php">Apply for a loan</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="jumbotron">
    <div class="container">
        <p>
            <?=SITE_NAME?> is a nonprofit based in Sohar, Oman. It was founded in 2016, with a mission to connect people
            through lending to alleviate unemployment. We envision an Oman where everyone has the opportunity to
            succeed.
        </p>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <div class="col-md-12 about-box">
                <h1 class="text-center">
                    <i class="fa fa-refresh fa-spin fa-4x" aria-hidden="true"></i>
                </h1>
                <h3 class="text-center">It's a loan, not a donation</h3>
                <p>
                    We believe lending alongside thousands of others is one of the most powerful and sustainable ways to
                    create economic and social good. Lending on <?=SITE_NAME?> creates a partnership of mutual dignity and
                    makes it easy to touch more lives with the same rial. Fund a loan, get repaid, fund another.
                </p>
            </div>
        </div>
        <div class="col-md-5 ">
            <div class="col-md-12 about-box">
                <h1 class="text-center">
                    <i class="fa fa-users fa-4x" aria-hidden="true"></i>
                </h1>
                <h3 class="text-center">Lifting one, to lift many</h3>
                <p>
                    When a <?=SITE_NAME?> loan enables someone to grow a business and create opportunity for themselves, it
                    creates opportunities for others as well. That ripple effect can shape the future for a family or an
                    entire community.
                </p>
            </div>
        </div>
    </div>
</div>
<?= getFoot() ?>

