<?php
/**
 * Created by PhpStorm.
 * User: Rashid
 * Date: 26-Jan-17
 * Time: 11:19 AM
 */

require_once('includes/config.php');
require_once('includes/db.php');
require_once('includes/common.php');

$alerts = array();

if (isset($_GET['logout'])) {
    $alerts[] = ['text' => 'You have been logged out.', 'type' => 'success'];
}
?>
<?= get_head('Home') ?>
<div class="jumbotron jumbotron-home">
    <div class="container">
        <h2>Eradicating Unemployment, One Loan At A Time.</h2>
        <p>At <?= SITE_NAME ?>, we believe that every Omani has the right to live and realize their true potential.
            Financial services are seldom offered to the unemployed, and we aim to change that by creating a sustainable
            social lending platform.</p>
        <p><a class="btn btn-success" href="#" role="button">Learn more &raquo;</a></p>
    </div>
</div>

<div class="container">
    <?= generate_alerts_html($alerts) ?>
    <h1 id="start-lending" class="text-center">Start Lending</h1>

    <hr>

    <div class="row">

        <div class="col-md-3 col-sm-6">
            <img class="img-responsive" src="/uploads/borrower-1.jpg">
            <div class="panel panel-default">
                <div class="panel-body">
                    <p class="text-primary"><b>Ahmed Al Marzoqi</b></p>
                    <p>A loan of 600 OMR helps Ahmed start his new company Dishdasha - an online male clothing line that
                        revolutionizes how Omanis tailor their dishdashas.</p>
                    <div class="progress progress-striped active">
                        <div class="progress-bar" style="width: 45%"></div>
                    </div>
                    <div class="btn-group btn-group-justified">
                        <a href="#" class="btn btn-success" data-toggle="modal" data-target="#lendModal">Lend</a>
                        <a href="#" class="btn btn-success" data-toggle="modal" data-target="#learnMoreModal">Learn
                            more</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal -->
    <div id="lendModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Choose Payment Method</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-dismissible alert-warning">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <p><b>Oh noes!</b> The site administrator has disabled payments. Maybe check next time?</p>
                    </div>
                    <div class="btn-group btn-group-justified">
                        <a href="#" class="btn btn-primary disabled" data-toggle="modal" data-target="#lendModal"><i
                                    class="fa fa-paypal" aria-hidden="true"></i>&nbsp;&nbsp;Paypal</a>
                        <a href="#" class="btn btn-success disabled"><i class="fa fa-money" aria-hidden="true"></i>&nbsp;&nbsp;Send
                            check by mail</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal -->
    <div id="learnMoreModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Under Construction</h4>
                </div>
                <div class="modal-body text-center">
                    <h2>
                        <i class="fa fa-gear fa-spin fa-2x" aria-hidden="true"></i>
                        <i class="fa fa-gear fa-spin fa-4x" aria-hidden="true"></i>
                    </h2>
                    <h2>This page is under <i>heavy</i> construction.</h2>
                    <p style="font-size: 7px">You really do have sharp eyes.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12 text-center">
            <a href="#" class="btn btn-default">View more</a>
        </div>
    </div>
</div>


<div class="container-fluid stats">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-sm-4 box text-center">
                <div class="col-md-12">
                    <h1>0.00%</h1>
                    <p>Loan repayment rate</p>
                </div>
            </div>

            <div class="col-md-4 col-sm-4 box text-center">
                <div class="col-md-12">
                    <h1>4</h1>
                    <p>Loans posted online</p>
                </div>
            </div>

            <div class="col-md-4 col-sm-4 box text-center">
                <div class="col-md-12">
                    <h1>980 OMR</h1>
                    <p>Raised in total</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= get_foot() ?>
