<?php
include("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
include("../common/header.php");
include("../common/navbar.php");
include("../common/sidebar.php");
require_once("../common/pagination.php");
include("../../app/v1/functions/company/func-branches.php");
include("../../app/v1/functions/branch/func-branch-pr-controller.php");

require_once("../../app/v1/functions/branch/bankReconciliationStatement.controller.php");

?>



<!-- <link rel="stylesheet" href="../../public/assets/manage-rfq.css">
<link rel="stylesheet" href="../../public/assets/animate.css"> -->

<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/banking.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">



<div class="content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-12">
                <div class="header-bg">
                    <h2>Partners Banks Fetch feeds directly</h2>
                    <div class="banks-card">
                        <div class="card">
                            <div class="card-body">
                                <img src="<?= BASE_URL ?>public/assets/img/bank-logo/icici-bank-logo.png" alt="">
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <img src="<?= BASE_URL ?>public/assets/img/bank-logo/standard-chartered.png" alt="">
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <img src="<?= BASE_URL ?>public/assets/img/bank-logo/HSBC.png" alt="">
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <img src="<?= BASE_URL ?>public/assets/img/bank-logo/kotak-mahindra.png" alt="">
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <img src="<?= BASE_URL ?>public/assets/img/bank-logo/Yes_Bank.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-12">
                <div class="card supported-bank-card">
                    <div class="card-header">
                        <div class="left-blocks">
                            <h2>Automatic Bank Feeds Supported Banks</h2>
                            <p>Connect your bank accounts and fetch the bank feeds using one of our third-party bank feeds service providers</p>
                        </div>
                        <div class="right-blocks">
                            <button class="btn btn-primary connect-btn">Connect Now</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="bank-name-blocks">
                            <button data-toggle="modal" data-target="#openBankDetailModal">
                                <div class="box box-paypal">
                                    <img src="<?= BASE_URL ?>public/assets/img/bank-logo/paypal.png" alt="">
                                    <p>Paypal</p>
                                </div>
                                <span class="error">Service is in under Progress</span>
                            </button>
                            <button data-toggle="modal" data-target="#openBankDetailModal">
                                <div class="box box-icici">
                                    <img src="<?= BASE_URL ?>public/assets/img/bank-logo/icici_2.png" alt="">
                                    <p>ICICI Bank(India)</p>
                                </div>
                                <span class="error">Service is in under Progress</span>
                            </button>
                            <button data-toggle="modal" data-target="#openBankDetailModal">
                                <div class="box box-hdfc">
                                    <img src="<?= BASE_URL ?>public/assets/img/bank-logo/hdfc.png" alt="">
                                    <p>HDFC Bank(India)</p>
                                </div>
                                <span class="error">Service is in under Progress</span>
                            </button>
                            <button data-toggle="modal" data-target="#openBankDetailModal">
                                <div class="box box-sbi">
                                    <img src="<?= BASE_URL ?>public/assets/img/bank-logo/sbi.png" alt="">
                                    <p>State bank of India (India) - Banking</p>
                                </div>
                                <span class="error">Service is in under Progress</span>
                            </button>
                            <button data-toggle="modal" data-target="#openBankDetailModal">
                                <div class="box box-kotak">
                                    <img src="<?= BASE_URL ?>public/assets/img/bank-logo/kotak_2.png" alt="">
                                    <p>Kotak Mahindra Bank(India)</p>
                                </div>
                                <span class="error">Service is in under Progress</span>
                            </button>
                            <button data-toggle="modal" data-target="#openBankDetailModal">
                                <div class="box box-axis">
                                    <img src="<?= BASE_URL ?>public/assets/img/bank-logo/axis.png" alt="">
                                    <p>Axis Bank(India)</p>
                                </div>
                                <span class="error">Service is in under Progress</span>
                            </button>
                            <button data-toggle="modal" data-target="#openBankDetailModal">
                                <div class="box box-hdfcCredit">
                                    <img src="<?= BASE_URL ?>public/assets/img/bank-logo/hdfc.png" alt="">
                                    <p>HDFC Bank(India) - Credit Card</p>
                                    <span class="po-list-icon">
                                        c
                                    </span>
                                </div>
                                <span class="error">Service is in under Progress</span>
                            </button>
                            <button data-toggle="modal" data-target="#openBankDetailModal">
                                <div class="box box-cityCredit">
                                    <img src="<?= BASE_URL ?>public/assets/img/bank-logo/city.png" alt="">
                                    <p>Citibank (India) - Credit Card</p>
                                    <span class="po-list-icon">
                                        c
                                    </span>
                                </div>
                                <span class="error">Service is in under Progress</span>
                            </button>
                            <button data-toggle="modal" data-target="#openBankDetailModal">
                                <div class="box box-sbiCredit">
                                    <img src="<?= BASE_URL ?>public/assets/img/bank-logo/sbi.png" alt="">
                                    <p>State Bank of India Credit Cards (India)</p>
                                    <span class="po-list-icon">
                                        c
                                    </span>
                                </div>
                                <span class="error">Service is in under Progress</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-12">
                <div class="bottom-block">
                    <div class="left-blocks">
                        <h2>Add bank accounts manually</h2>
                        <p>Unable to Connect your bank accounts through our service providers? Add your bank account manually by entering yout account details</p>
                    </div>
                    <div class="right-blocks">
                        <button class="btn btn-primary connect-btn waves-effect waves-light">Add Man</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bankDetailsAdd" id="openBankDetailModal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Selct Account Type</h5>
                    <button type="button" class="btn-close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Select your <strong>ICICI Bank</strong> account type and help us optimize your feeds accordingly </p>
                    <div class="acount-types-block">
                        <button data-toggle="modal" data-target="#bankingFormModal">
                            <div class="typebox personal-acc">
                                <i class="fa fa-user"></i>
                                <span>Personal Account</span>
                            </div>
                        </button>
                        <button data-toggle="modal" data-target="#bankingFormModal">
                            <div class="typebox corporate-acc">
                                <i class="fas fa-university"></i>
                                <span>Personal Account</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade right bankFormModal" id="bankingFormModal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-lg-6 col-md-6">
                            <div class="form-left-block">
                                <nav class="logo">
                                    <img src="<?= BASE_URL ?>public/assets/img/bank-logo/icici-bank-logo.png" alt="">
                                </nav>
                                <div class="desc">
                                    <h5>Transaction Charges</h5>
                                    <p>Transaction charges are applicable as per your agreement with ICICI Bank.</p>
                                </div>
                                <ul>
                                    <h5>Benefits</h5>
                                    <li>
                                        <p>
                                            Initiate payments directly from Vitwo.ai widthout logging into ICICI bank CIB portal
                                        </p>
                                    </li>
                                    <li>
                                        <p>
                                            Payment status gets automatically updated in Vtwo.ai
                                        </p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 col-md-6">
                            <div class="form-right-block">
                                <div class="form-input">
                                    <label for="">Use self created Login ID. if not created use CorporateID UserID</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="form-input">
                                    <label for="">Use self created Login ID. if not created use CorporateID UserID</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="form-input">
                                    <label for="">Use self created Login ID. if not created use CorporateID UserID</label>
                                    <input type="text" class="form-control">
                                </div>

                                <p><strong>Note: </strong> This is a user-level integration. Other Users of your Organization will not beable to initiate payments on your behalf.</p>
                                <p>By Cliking SAve, you agree to our <a href="#">Terms and Conditions</a></p>

                                <hr>

                                <div class="btn-section float-right">
                                    <button class="btn btn-primary">Save</button>
                                    <button class="btn btn-danger">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
include("../common/footer.php");
?>