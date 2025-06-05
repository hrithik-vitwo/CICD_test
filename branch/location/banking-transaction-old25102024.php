<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-branch-pr-controller.php");
require_once("../../app/v1/functions/branch/bankReconciliationStatement.controller.php");
// console($_SESSION);
?>


<!-- <link rel="stylesheet" href="../../public/assets/manage-rfq.css">
<link rel="stylesheet" href="../../public/assets/animate.css"> -->

<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/stock-report-new.css">
<link rel="stylesheet" href="../../public/assets/banking.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">


<style>
    .body-container {
        align-items: flex-start !important;
    }

    .custom-select-inner {
        max-width: 50px;
    }

    .filter-list.transaction-filter-list {
        top: 0;
        left: 0;
    }

    .filter-list.transaction-filter-list a.btn.active {
        background: #003060;
        color: #fff;
    }

    .text-style {
        color: white !important;
        background-color: #28a745;
        padding: 3px 5px;
        border-radius: 5px;
        font-weight: 600;
    }

    .banking-transaction-modal .btn-section {
        background-color: #fff;
        padding: 5px 0;
        gap: 10px;
    }

    #mannual-transaction .tab-pane-body {
        height: calc(100vh - 340px);
        overflow-y: auto;
        overflow-x: hidden;
    }
</style>

<?php

$allbtnActive = "";
$recognisedbtnActive = "";
$unrecognisedbtnActive = "";

$bankId = isset($_GET["bank"]) ? base64_decode(base64_decode(base64_decode($_GET["bank"]))) : 0;


$tnxType = "";
if (isset($_GET["act"]) && $_GET["act"] == "recognised") {
    $tnxType = "recognised";
    $recognisedbtnActive = "active";
} elseif (isset($_GET["act"]) && $_GET["act"] == "unrecognised") {
    $unrecognisedbtnActive = "active";
    $tnxType = "unrecognised";
} else {
    $tnxType = "all";
    $allbtnActive = "active";
}

?>
<div class="content-wrapper is-sales-orders is-banking-transaction vitwo-alpha-global">
    <section class="content banking-import-statement">

        <!-- main content list start -->

        <div class="container-fluid">
            <div class="head">
                <h2 class="text-lg font-bold">Bank Transactions</h2>
            </div>
            <?php
            $brsObj = new BankReconciliationStatement($bankId, $tnxType);
            $bankTnxObj = $brsObj->getBankStatements();
            $uncategorized_count = $brsObj->getUncategorizedCount($bankId);
            // console($uncategorized_count);
            // $branchSoObj = new BranchSo();
            $amountInBook = 130600.00;
            $amountInBank = $bankTnxObj["totalAmount"];
            $amountInUnrecognised = $amountInBook - $amountInBank;
            // console($bankTnxObj);
            ?>
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <div class="body-container mt-4 px-0">
                        <div class="table-title px-5 d-flex w-100 justify-content-between">
                            <div class="title">
                                <h2 class="text-sm font-bold">Preview Transaction</h2>
                                <span class="text-xs text-danger"><?= $uncategorized_count["numRows"] ?> transaction <?php if ($uncategorized_count["numRows"] > 1) echo "(s) are";
                                                                                                                        else echo " is"; ?> in the uncategorized status</span>
                            </div>
                            <div class="filter-list transaction-filter-list">
                                <a href="<?= LOCATION_URL ?>banking-transaction.php?act=all&bank=<?= base64_encode(base64_encode(base64_encode($bankId))) ?>" class="btn <?= $allbtnActive ?> element-to-pulse pulsing waves-effect waves-light"><i class="fa fa-stream mr-2 active"></i>
                                    All Transactions
                                </a>
                                <a href="<?= LOCATION_URL ?>banking-transaction.php?act=recognised&bank=<?= base64_encode(base64_encode(base64_encode($bankId))) ?>" class="btn <?= $recognisedbtnActive ?>  waves-effect waves-light"><i class="fa fa-clock mr-2"></i>
                                    Categorized Transactions
                                </a>
                                <a href="<?= LOCATION_URL ?>banking-transaction.php?act=unrecognised&bank=<?= base64_encode(base64_encode(base64_encode($bankId))) ?>" class="btn <?= $unrecognisedbtnActive ?>  waves-effect waves-light"><i class="fa fa-exclamation-circle mr-2"></i>
                                    Uncategorized Transactions
                                </a>
                            </div>
                        </div>
                        <div class="transaction-list">
                            <table class="table list-table preview-table mt-4">
                                <thead>
                                    <tr>
                                        <!-- <th width="5%"><input type="checkbox"></th> -->
                                        <th>Date</th>
                                        <th>Details</th>
                                        <th>Account</th>
                                        <!-- <th>Party</th> -->
                                        <th class="text-right">Deposits</th>
                                        <th class="text-right">Withdrawal</th>
                                        <th class="text-right">Yet to settle</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // console($branchTnxObj);
                                    foreach ($bankTnxObj["data"] as $sl => $listItem) {
                                        // console($listItem);
                                        $tnxAmount = $listItem["remaining_amt"];
                                    ?>
                                        <tr>
                                            <!-- <td><input type="checkbox"></td> -->
                                            <td><?= date_format(date_create($listItem["tnx_date"]), "d/m/Y"); ?></td>
                                            <td>
                                                <p class="pre-normal mb-2">Reference# : <?= $listItem["particular"] ?> </p>
                                                <!-- <p class="text-sm mb-2">Description: MMT/IMPS/33456669875698/IMPS/GOOGLEINDI/Axis Bank </p> -->
                                            </td>
                                            <td><?= $listItem["bank_name"] ?> ( <?= $listItem["account_no"] ?? "-" ?> )</td>

                                            <!-- <td>
                                                <?php
                                                $partyNameToBePrint = "";
                                                if ($listItem["reconciled_status"] != "pending") {
                                                    $utr_number = $listItem["utr_number"];
                                                    if ($listItem["deposit_amt"] > 0) {
                                                        // Vendor Name
                                                        $vendorPartyName = $brsObj->getVendorPartyName($utr_number)["data"];
                                                        if ($brsObj->getVendorPartyName($utr_number)["numRows"] != 0) {
                                                            $partyNameToBePrint = $vendorPartyName["trade_name"] . "(" . $vendorPartyName["vendor_code"] . ")";
                                                        }
                                                    } else {
                                                        //Customer Name
                                                        $customerPartyName = $brsObj->getCustomerPartyName($utr_number)["data"];
                                                        if ($brsObj->getCustomerPartyName($utr_number)["numRows"] != 0) {
                                                            $partyNameToBePrint = $customerPartyName["trade_name"] . "(" . $customerPartyName["customer_code"] . ")";
                                                        }
                                                    }
                                                }
                                                echo $partyNameToBePrint != "" ? $partyNameToBePrint : ($listItem["expected_party"] . " (" . $listItem["expected_party_code"] . ")");
                                                ?>
                                            </td> -->
                                            <td class="text-right"><?= $listItem["deposit_amt"] > 0 ? "Rs. " . number_format($listItem["deposit_amt"], 2) : "" ?></td>
                                            <td class="text-right"><?= $listItem["withdrawal_amt"] > 0 ? "Rs. " . number_format($listItem["withdrawal_amt"], 2) : "" ?></td>
                                            <td class="text-right">
                                                <p class="pending-amount"><?= $listItem["remaining_amt"] > 0 ? "Rs. " . number_format($listItem["remaining_amt"], 2) : "" ?></p>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if ($listItem["reconciled_status"] == "pending" && $tnxType == "unrecognised") {
                                                ?>
                                                    <ion-icon name="eye" class="eye_button" data-toggle="modal" id="unrecognisedTnxTblRow" style="cursor:pointer;" data-tnx="<?= base64_encode(json_encode($listItem)) ?>"></ion-icon>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade right global-view-modal banking-transaction-modal" id="unrecognisedTnxModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="d-flex justify-content-between py-2">
                            <div class="banking-amount">
                                <div id="price_value_hidden" style="display: none;"></div>
                                <h3><span class="rupee-symbol">₹</span><span id="price_value"></span></h3>
                                <div class="text-dark mb-2" id="calculativevalue"></div>
                                <span id="warning_text"></span>
                            </div>
                            <div class="right-btns d-flex gap-3 nav nav-tabs my-0" id="nav-tab" role="tablist">
                                <button class="btn active" id="matchTransaction" data-bs-toggle="tab" data-bs-target="#match-transaction" type="button" role="tab">Match Transaction</button>
                                <button class="btn" id="catergorizeMannualy" data-bs-toggle="tab" data-bs-target="#mannual-transaction" type="button" role="tab">Categorize Manually </button>
                            </div>
                        </div>
                        <div class="row pt-2 banking-number-info">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <p class="text-sm text-right mb-2"><span id="utrNumber"></span></p>
                                <p class="text-xs text-right mb-2"><span id="particular"></span></p>
                                <p class="text-xs text-right mb-2">Date: <span id="dateModal"></span></p>
                            </div>
                        </div>
                        <ul class="nav nav-tabs match-transactions" id="myMatchTransactionTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#accountantTab" type="button" role="tab" aria-controls="home" aria-selected="true">Non Accounted</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#nonaccountantTab" type="button" role="tab" aria-controls="profile" aria-selected="false">Accounted</button>
                            </li>
                        </ul>
                    </div>

                    <div class="modal-body">
                        <div class="tab-content">
                            <div class="tab-pane fade" id="mannual-transaction" role="tabpanel" tabindex="0">
                                <div class="btn-section d-flex justify-content-end">
                                    <!-- <button class="btn btn-danger py-2">Cancel</button> -->
                                    <button class="btn btn-primary match-btn py-2" id="manualtransactionbutton">Match</button>
                                </div>
                                <div class="tab-pane-body">
                                    <div class="row pb-4">
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <label for="">Select Transaction Category</label>
                                            <select name="typeofselect" id="selectTransactionCategory" class="form-control selectTransactionCategory">
                                                <option value="">Select Category</option>
                                                <option value="vendor_payment">Vendor Payment</option>
                                                <option value="customer_payment">Receive from Customer</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12" id="transactionCategorySubDropdownDiv">
                                            <div id="transCategoryVendor">
                                                <p style="font-size: small;">Select Vendor</p>
                                                <select name="" id="selectVendorDropdown" class="form-control selectVendorDropdown">

                                                </select>

                                            </div>

                                            <div id="transCategoryCustomer">
                                                <p style="font-size: small;">Select Customer</p>
                                                <select name="" id="selectCustomerDropdown" class="form-control selectCustomerDropdown">

                                                </select>

                                            </div>

                                        </div>
                                    </div>
                                    <div id="reconciliationFormDiv"></div>
                                </div>

                            </div>


                            <div class="tab-pane fade show active" id="match-transaction" role="tabpanel" tabindex="0">
                                <div class="tab-pane-body match-transaction-tab">
                                    <div class="tab-content match-transac-tab-content pt-0" id="myTabContent">
                                        <div class="tab-pane fade show active" id="accountantTab" role="tabpanel" aria-labelledby="home-tab">
                                            <div class="tab-pane-body acc-nonacc-panebody">

                                                <!-- Non accountant innertab customer and vendor start -->

                                                <ul class="nav nav-tabs my-0" id="myTab" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active" id="customerWiseBanking-tab" data-bs-toggle="tab" data-bs-target="#customerWiseBanking" type="button" role="tab" aria-controls="customerWiseBanking" aria-selected="true"><ion-icon name="person"></ion-icon>Customer Wise</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" id="vendorWisebanking-tab" data-bs-toggle="tab" data-bs-target="#vendorWisebanking" type="button" role="tab" aria-controls="vendorWisebanking" aria-selected="false"><ion-icon name="person"></ion-icon>Vendor Wise</button>
                                                    </li>
                                                </ul>
                                                <div class="tab-content acc-tab-content" id="myTabContent">
                                                    <div class="tab-pane fade show active" id="customerWiseBanking" role="tabpanel" aria-labelledby="customerWiseBanking-tab">
                                                        <div class="possible-match-head d-flex justify-content-between">
                                                            <div class="btn-section d-flex justify-content-start match-transac-btn">
                                                                <!-- <button class="btn btn-danger py-2">Cancel</button> -->
                                                                <button class="btn btn-primary match-btn py-2" id="matchtransactionbutton">Match</button>
                                                            </div>
                                                            <div class="left">
                                                                <!-- <h3 class="text-sm font-bold">Possible Matches</h3> -->
                                                            </div>

                                                        </div>
                                                        <hr>

                                                        <!-- for customer wise -->
                                                        <div class="customerWiseBankingDiv" id="profile_" role="tabpanel" aria-labelledby="pills-contact-tab">
                                                            <div class="length-row inner-length-row d-flex align-items-center text-xs gap-2">
                                                                <span>Show</span>
                                                                <select name="" id="" class="form-control custom-select-inner" value="25">
                                                                    <option value="10">10</option>
                                                                    <option value="25" selected="selected">25</option>
                                                                    <option value="50">50</option>
                                                                    <option value="100">100</option>
                                                                    <option value="200">200</option>
                                                                    <option value="250">250</option>
                                                                </select>
                                                                <span>Entries</span>
                                                            </div>

                                                            <table class="table table-hover baking-transaction-wise classic-view" id="stockLogsTable" data-responsive="false">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Sl No</th>
                                                                        <th>#</th>
                                                                        <th>Customer Code</th>
                                                                        <th>Trade Name</th>
                                                                        <th>Due Amount</th>
                                                                        <th>Invoice Date</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody class="stock-log-body">
                                                                </tbody>
                                                            </table>

                                                            <div class="row custom-table-footer">
                                                                <div class="col-lg-6 col-md-6 col-12">
                                                                    <div id="limitTextinner" class="limit-text">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 col-md-6 col-12">
                                                                    <div id="yourDataTable_paginateinner">
                                                                        <div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade" id="vendorWisebanking" role="tabpanel" aria-labelledby="vendorWisebanking-tab">
                                                        <div class="possible-match-head d-flex justify-content-between">
                                                            <div class="btn-section d-flex justify-content-start match-transac-btn">
                                                                <!-- <button class="btn btn-danger py-2">Cancel</button> -->
                                                                <button class="btn btn-primary match-btn py-2" id="matchtransactionVendorbutton">Match</button>
                                                            </div>
                                                            <div class="left">
                                                                <!-- <h3 class="text-sm font-bold">Possible Matches</h3> -->
                                                            </div>
                                                        </div>
                                                        <hr>

                                                        <!-- for vendor wise -->
                                                        <div class="customerWiseBankingDiv vendorWiseBankingDiv" id="profile_" role="tabpanel" aria-labelledby="pills-contact-tab">
                                                            <div class="length-row inner-length-row d-flex align-items-center text-xs gap-2">
                                                                <span>Show</span>
                                                                <select name="" id="" class="form-control custom-select-inner" value="25">
                                                                    <option value="10">10</option>
                                                                    <option value="25" selected="selected">25</option>
                                                                    <option value="50">50</option>
                                                                    <option value="100">100</option>
                                                                    <option value="200">200</option>
                                                                    <option value="250">250</option>
                                                                </select>
                                                                <span>Entries</span>
                                                            </div>

                                                            <table class="table table-hover baking-transaction-wise classic-view" id="stockLogsTableVendor" data-responsive="false">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Sl No</th>
                                                                        <th>#</th>
                                                                        <th>Vendor Code</th>
                                                                        <th>Vendor Name</th>
                                                                        <th>Due Amount</th>
                                                                        <th>Posting Date</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody class="stock-log-bodyVendor">
                                                                </tbody>
                                                            </table>

                                                            <div class="row custom-table-footer">
                                                                <div class="col-lg-6 col-md-6 col-12">
                                                                    <div id="limitTextinnerVendor" class="limit-text">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 col-md-6 col-12">
                                                                    <div id="yourDataTable_paginateinnerVendor">
                                                                        <div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </div>
                                                </div>

                                                <!--non  accountant innertab customer and vendor finish -->



                                                <div id="nonAccountsLists"></div>

                                            </div>

                                        </div>
                                        <div class="tab-pane fade" id="nonaccountantTab" role="tabpanel" aria-labelledby="profile-tab">

                                            <div class="tab-pane-body acc-nonacc-panebody noacc-tab-body">
                                                <ul class="nav nav-tabs my-0" id="myTab" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active" id="nonAccCustomerWiseBanking-tab" data-bs-toggle="tab" data-bs-target="#nonAccCustomerWiseBanking" type="button" role="tab" aria-controls="nonAccCustomerWiseBanking" aria-selected="true">Customer Wise</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" id="nonAccVendorWisebanking-tab" data-bs-toggle="tab" data-bs-target="#nonAccVendorWisebanking" type="button" role="tab" aria-controls="nonAccVendorWisebanking" aria-selected="false">Vendor Wise</button>
                                                    </li>
                                                </ul>
                                                <div class="tab-content acc-tab-content" id="myTabContent">
                                                    <div class="tab-pane fade show active" id="nonAccCustomerWiseBanking" role="tabpanel" aria-labelledby="nonAccCustomerWiseBanking-tab">
                                                        <div class="possible-match-head d-flex justify-content-between">
                                                            <div class="btn-section d-flex justify-content-start match-transac-btn">
                                                                <!-- <button class="btn btn-danger py-2">Cancel</button> -->
                                                                <button class="btn btn-primary match-btn py-2" id="matchtransactionbuttonAcc">Match</button>
                                                            </div>
                                                            <div class="left">
                                                                <!-- <h3 class="text-sm font-bold">Possible Matches Non Account</h3> -->
                                                            </div>

                                                        </div>
                                                        <hr>

                                                        <!-- for  acc Customer wise -->
                                                        <div class="" id="profile_" role="tabpanel" aria-labelledby="pills-contact-tab">
                                                            <div class="length-row inner-length-row d-flex align-items-center text-xs gap-2">
                                                                <span>Show</span>
                                                                <select name="" id="" class="form-control custom-select-innerNonAccCust" value="25">
                                                                    <option value="10">10</option>
                                                                    <option value="25" selected="selected">25</option>
                                                                    <option value="50">50</option>
                                                                    <option value="100">100</option>
                                                                    <option value="200">200</option>
                                                                    <option value="250">250</option>
                                                                </select>
                                                                <span>Entries</span>
                                                            </div>

                                                            <table class="table table-hover stockDetailsTableNonAccCust baking-transaction-wise classic-view" id="stockLogsTableNonAccCust" data-responsive="false">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Sl No</th>
                                                                        <th>#</th>
                                                                        <th>Posting Date</th>
                                                                        <th>Party</th>
                                                                        <th>Type</th>
                                                                        <th>Invoice Date</th>
                                                                        <th>Amount</th>
                                                                        <th>Customer Code</th>
                                                                        <th>Customer Name</th>
                                                                        <th>Transaction Type</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody class="stock-log-bodyNonAccCust">
                                                                </tbody>
                                                            </table>

                                                            <div class="row custom-table-footer">
                                                                <div class="col-lg-6 col-md-6 col-12">
                                                                    <div id="limitTextinnerNonAccCust" class="limit-text">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 col-md-6 col-12">
                                                                    <div id="yourDataTable_paginateinnerNonAccCust">
                                                                        <div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade" id="nonAccVendorWisebanking" role="tabpanel" aria-labelledby="nonAccVendorWisebanking-tab">
                                                        <div class="possible-match-head d-flex justify-content-between">
                                                            <div class="btn-section d-flex justify-content-start match-transac-btn">
                                                                <!-- <button class="btn btn-danger py-2">Cancel</button> -->
                                                                <button class="btn btn-primary match-btn py-2" id="matchtransactionVendorbuttonAcc">Match</button>
                                                            </div>
                                                            <div class="left">
                                                                <!-- <h3 class="text-sm font-bold">Possible Matches</h3> -->
                                                            </div>
                                                        </div>
                                                        <hr>

                                                        <!-- for  acc Vendor wise -->
                                                        <div class="" id="profile_" role="tabpanel" aria-labelledby="pills-contact-tab">
                                                            <div class="length-row inner-length-row">
                                                                <span>Show</span>
                                                                <select name="" id="" class="form-control custom-select-innerNonAccVendor" value="25">
                                                                    <option value="10">10</option>
                                                                    <option value="25" selected="selected">25</option>
                                                                    <option value="50">50</option>
                                                                    <option value="100">100</option>
                                                                    <option value="200">200</option>
                                                                    <option value="250">250</option>
                                                                </select>
                                                                <span>Entries</span>
                                                            </div>

                                                            <table class="table table-hover stockDetailsTableNonAccVendor baking-transaction-wise classic-view" id="stockLogsTableNonAccVendor" data-responsive="false">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Sl No</th>
                                                                        <th>#</th>
                                                                        <th>Posting Date</th>
                                                                        <th>Party</th>
                                                                        <th>Type</th>
                                                                        <th>Invoice Date</th>
                                                                        <th>Amount</th>
                                                                        <th>Vendor Code</th>
                                                                        <th>Vendor Name</th>
                                                                        <th>Transaction Type</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody class="stock-log-bodyNonAccVendor">
                                                                </tbody>
                                                            </table>

                                                            <div class="row custom-table-footer">
                                                                <div class="col-lg-6 col-md-6 col-12">
                                                                    <div id="limitTextinnerNonAccVendor" class="limit-text">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6 col-md-6 col-12">
                                                                    <div id="yourDataTable_paginateinnerNonAccVendor">
                                                                        <div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>



                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- global list modal end -->

    </section>
</div>
<?php
require_once("../common/footer2.php");

?>
<script>
    // some varible name can be confusing because the last moment chnage of function

    let statement_id = 0;
    $(document).ready(function() {
        $('.select2').select2();
        const log = console.log;

        
        // Main modal event to start every modal related acticvity
        $(document).on("click", ".eye_button", function() {
            let price_value = 0;
            $("#calculativevalue").html("");
            $("#warning_text").hide();
            // modal heading start
            let listDetail = $(this).data('tnx');
            listDetail = atob(listDetail);
            listDetail = JSON.parse(listDetail);
            statement_id = listDetail.id;
            console.log(listDetail);

            $("#price_value_hidden").html(decimalAmount(listDetail.remaining_amt));
            $("#price_value").html(decimalAmount(listDetail.remaining_amt));
            $("#particular").html(listDetail.particular);
            $("#utrNumber").html(listDetail.utr_number);
            $("#dateModal").html(formatDate(listDetail.tnx_date));
            $("#transCategoryCustomer").hide();
            $("#transCategoryVendor").hide();

            price_value = parseFloat($(`#price_value_hidden`).html());
            let totalValuetoshw = 0;

            let idarray = [];
            let customeridarray = [];

            let idVendorArray = [];
            let vendorIdArray = [];
            // accunted customer

            let idarrayAcc = [];
            let customeridarrayAcc = [];

            let idVendorArrayAcc = [];
            let vendorIdArrayAcc = [];

            $(document).on("change", 'input[name="match_trxn_checkbx"]', function() {
                if ($(this).is(':checked')) {
                    totalValuetoshw += parseFloat($(this).data('amt'));
                    console.log(totalValuetoshw);
                    idarray.push(this.value);
                    let customer_id_array = $(this).data('customerarray');
                    customeridarray.push(customer_id_array);
                    $(`#calculativevalue`).html("<span class='rupee-symbol'>₹</span>" + decimalAmount(totalValuetoshw));
                    if (totalValuetoshw > price_value) {
                        $(`#warning_text`).show();
                        $(`#warning_text`).html("Price is Exceeding");
                        $('#matchtransactionbutton').prop('disabled', true);
                    } else {
                        $(`#warning_text`).hide();
                        $('#matchtransactionbutton').prop('disabled', false);

                    }
                } else {
                    // var stat_id = $(this).data('statement_id');
                    var itemToRemove = this.value;
                    idarray = jQuery.grep(idarray, function(value) {
                        return value != itemToRemove;
                    });

                    let cust_id_remove = $(this).data('customerarray');
                    customeridarray = jQuery.grep(customeridarray, function(value) {
                        return value != cust_id_remove;
                    });

                    totalValuetoshw -= parseFloat($(this).data('amt'));
                    $(`#calculativevalue`).html("<span class='rupee-symbol'>₹</span>" + totalValuetoshw.toFixed(2));
                    if (totalValuetoshw > price_value) {
                        $(`#warning_text`).show();
                        $(`#warning_text`).html("Price is Exceeding");
                        $('#matchtransactionbutton').prop('disabled', true);

                    } else {
                        $(`#warning_text`).hide();
                        $('#matchtransactionbutton').prop('disabled', false);

                    }
                }
                // alert(customeridarray);
            });

            $(document).on("change", 'input[name="match_trxn_checkbx_vendor"]', function() {
                if ($(this).is(':checked')) {
                    totalValuetoshw += parseFloat($(this).data('amt'));
                    // console.log(totalValuetoshw);
                    idVendorArray.push(this.value);
                    let vendor_id_array = $(this).data('vendorarray');
                    vendorIdArray.push(vendor_id_array);
                    $(`#calculativevalue`).html("<span class='rupee-symbol'>₹</span>" + decimalAmount(totalValuetoshw));
                    // alert(totalValuetoshw);
                    if (totalValuetoshw > price_value) {
                        $(`#warning_text`).show();
                        $(`#warning_text`).html("Price is Exceeding");
                        $('#matchtransactionVendorbutton').prop('disabled', true);

                    } else {
                        $(`#warning_text`).hide();
                        $('#matchtransactionVendorbutton').prop('disabled', false);

                    }
                } else {
                    // var stat_id = $(this).data('statement_id');
                    let itemToRemove = this.value;
                    idVendorArray = jQuery.grep(idVendorArray, function(value) {
                        return value != itemToRemove;
                    });

                    let cust_id_remove = $(this).data('vendorarray');
                    vendorIdArray = jQuery.grep(vendorIdArray, function(value) {
                        return value != cust_id_remove;
                    });

                    totalValuetoshw -= parseFloat($(this).data('amt'));
                    $(`#calculativevalue`).html("<span class='rupee-symbol'>₹</span>" + totalValuetoshw.toFixed(2));
                    if (totalValuetoshw > price_value) {
                        $(`#warning_text`).show();
                        $(`#warning_text`).html("Price is Exceeding");
                        $('#matchtransactionVendorbutton').prop('disabled', true);

                    } else {
                        $(`#warning_text`).hide();
                        $('#matchtransactionVendorbutton').prop('disabled', false);

                    }
                }
                // alert(vendorIdArray);
            });

            // accounted 
            $(document).on("change", 'input[name="match_trxn_checkbx_acc_cust"]', function() {
                if ($(this).is(':checked')) {
                    totalValuetoshw += parseFloat($(this).data('amt'));
                    console.log(totalValuetoshw);
                    idarrayAcc.push(this.value);
                    let customer_id_array = $(this).data('customerarray');
                    customeridarrayAcc.push(customer_id_array);
                    $(`#calculativevalue`).html("<span class='rupee-symbol'>₹</span>" + decimalAmount(totalValuetoshw));
                    if (totalValuetoshw > price_value) {
                        $(`#warning_text`).show();
                        $(`#warning_text`).html("Price is Exceeding");
                        $('#matchtransactionbuttonAcc').prop('disabled', true);

                    } else {
                        $(`#warning_text`).hide();
                        $('#matchtransactionbuttonAcc').prop('disabled', false);

                    }
                } else {
                    // var stat_id = $(this).data('statement_id');
                    var itemToRemove = this.value;
                    idarrayAcc = jQuery.grep(idarrayAcc, function(value) {
                        return value != itemToRemove;
                    });

                    let cust_id_remove = $(this).data('customerarray');
                    customeridarrayAcc = jQuery.grep(customeridarrayAcc, function(value) {
                        return value != cust_id_remove;
                    });

                    totalValuetoshw -= parseFloat($(this).data('amt'));
                    $(`#calculativevalue`).html("<span class='rupee-symbol'>₹</span>" + decimalAmount(totalValuetoshw));
                    if (totalValuetoshw > price_value) {
                        $(`#warning_text`).show();
                        $(`#warning_text`).html("Price is Exceeding");
                        $('#matchtransactionbuttonAcc').prop('disabled', true);

                    } else {
                        $(`#warning_text`).hide();
                        $('#matchtransactionbuttonAcc').prop('disabled', false);

                    }
                }
                // alert(customeridarray);
            });

            $(document).on("change", 'input[name="match_trxn_checkbx_acc_vendor"]', function() {
                if ($(this).is(':checked')) {
                    totalValuetoshw += parseFloat($(this).data('amt'));
                    // console.log(totalValuetoshw);
                    idVendorArrayAcc.push(this.value);
                    let vendor_id_array = $(this).data('vendorarray');
                    vendorIdArrayAcc.push(vendor_id_array);
                    $(`#calculativevalue`).html("<span class='rupee-symbol'>₹</span>" + decimalAmount(totalValuetoshw));
                    // alert(totalValuetoshw);
                    if (totalValuetoshw > price_value) {
                        $(`#warning_text`).show();
                        $(`#warning_text`).html("Price is Exceeding");
                        $('#matchtransactionVendorbuttonAcc').prop('disabled', true);

                    } else {
                        $(`#warning_text`).hide();
                        $('#matchtransactionVendorbuttonAcc').prop('disabled', false);

                    }
                } else {
                    // var stat_id = $(this).data('statement_id');
                    let itemToRemove = this.value;
                    idVendorArrayAcc = jQuery.grep(idVendorArrayAcc, function(value) {
                        return value != itemToRemove;
                    });

                    let cust_id_remove = $(this).data('vendorarray');
                    vendorIdArrayAcc = jQuery.grep(vendorIdArrayAcc, function(value) {
                        return value != cust_id_remove;
                    });

                    totalValuetoshw -= parseFloat($(this).data('amt'));
                    $(`#calculativevalue`).html("<span class='rupee-symbol'>₹</span>" + totalValuetoshw.toFixed(2));
                    if (totalValuetoshw > price_value) {
                        $(`#warning_text`).show();
                        $(`#warning_text`).html("Price is Exceeding");
                        $('#matchtransactionVendorbuttonAcc').prop('disabled', true);

                    } else {
                        $(`#warning_text`).hide();
                        $('#matchtransactionVendorbuttonAcc').prop('disabled', false);

                    }
                }
                // alert(vendorIdArray);
            });

            $(document).on("click", "#matchtransactionbutton", function() {
                let totalValue = 0;
                $('#matchtransactionbutton').prop('disabled', true);

                // var statement_id = $('input[name="match_trxn_checkbx"]:checked').data("statement_id");
                //check same customer
                const allEqual = arr => arr.every(val => val === arr[0]);
                const result = allEqual(customeridarray) // output: false


                if (result == true) {

                    $.ajax({
                        type: "POST",
                        url: 'ajaxs/reconciliation/ajax-match-transaction.php',
                        data: {
                            act: "customer",
                            flag: "accounting",
                            listDetail,
                            idarray,
                            statement_id
                        },
                        success: function(response) {
                            // console.log(response);

                            let responseObj = JSON.parse(response);
                            // console.log(responseObj);
                            if (responseObj.status == "success") {
                                Swal.fire({
                                    icon: responseObj.status,
                                    title: responseObj.status,
                                    text: responseObj.message,
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: responseObj.status,
                                    title: responseObj.status,
                                    text: responseObj.message,
                                }).then(function() {
                                    location.reload();
                                });
                            }

                        },
                        complete: function(xhr, status) {
                            $('#matchtransactionbutton').prop('disabled', false);

                        },
                        error: function(err) {
                            console.error(err);
                        }
                    });

                } else {
                    alert("choose invoices of single customer");
                }

            });

            $(document).on("click", "#matchtransactionVendorbutton", function() {
                $('#matchtransactionVendorbutton').prop('disabled', true);

                let totalValue = 0;
                // var statement_id = $('input[name="match_trxn_checkbx"]:checked').data("statement_id");
                //check same customer
                const allEqual = arr => arr.every(val => val === arr[0]);
                const result = allEqual(vendorIdArray) // output: false

                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: 'ajaxs/reconciliation/ajax-match-transaction.php',
                        data: {
                            act: "vendor",
                            flag: "accounting",
                            listDetail,
                            idarray: idVendorArray,
                            statement_id
                        },
                        success: function(response) {
                            console.log(response);

                            let responseObj = JSON.parse(response);
                            console.log(responseObj);
                            if (responseObj.status == "success") {
                                Swal.fire({
                                    icon: responseObj.status,
                                    title: responseObj.status,
                                    text: responseObj.message,
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: responseObj.status,
                                    title: responseObj.status,
                                    text: responseObj.message,
                                }).then(function() {
                                    location.reload();
                                });
                            }

                        },
                        complete: function(xhr, status) {
                            if (xhr.status != 200) {
                                $(`#reconciliationFormDiv_${row_id}`).html("Something went wrong, please try again!");
                            }
                            log('Customer Invoice details request completed with status code:', xhr.status);
                        }
                    });
                } else {
                    alert("choose invoices of single customer");
                }

            });

            // accounted click event fot customer
            $(document).on("click", "#matchtransactionbuttonAcc", function() {

                $('#matchtransactionbuttonAcc').prop('disabled', true);
                let totalValue = 0;
                // var statement_id = $('input[name="match_trxn_checkbx"]:checked').data("statement_id");
                //check same customer
                const allEqual = arr => arr.every(val => val === arr[0]);
                const result = allEqual(customeridarrayAcc) // output: false

                if (result == true) {

                    $.ajax({
                        type: "POST",
                        url: 'ajaxs/reconciliation/ajax-match-transaction.php',
                        data: {
                            act: "customerWithAcc",
                            idarray: idarrayAcc,
                            statement_id
                        },
                        success: function(response) {
                            console.log(response);

                            let responseObj = JSON.parse(response);
                            console.log(responseObj);
                            if (responseObj.status == "success") {
                                Swal.fire({
                                    icon: responseObj.status,
                                    title: responseObj.status,
                                    text: responseObj.message,
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: responseObj.status,
                                    title: responseObj.status,
                                    text: responseObj.message,
                                }).then(function() {
                                    location.reload();
                                });
                            }

                        },
                        complete: function() {
                            $('#matchtransactionbuttonAcc').prop('disabled', false);
                        },
                        error: function(err) {
                            console.error(err);
                        }
                    });
                } else {
                    alert("choose invoices of single customer");
                }
            });

            $(document).on("click", "#matchtransactionVendorbuttonAcc", function() {
                $('#matchtransactionVendorbuttonAcc').prop('disabled', true);

                let totalValue = 0;
                // var statement_id = $('input[name="match_trxn_checkbx"]:checked').data("statement_id");
                //check same customer
                const allEqual = arr => arr.every(val => val === arr[0]);
                const result = allEqual(vendorIdArrayAcc) // output: false

                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: 'ajaxs/reconciliation/ajax-match-transaction.php',
                        data: {
                            act: "vendorWithAcc",
                            idarray: idVendorArrayAcc,
                            statement_id
                        },
                        success: function(response) {
                            let responseObj = JSON.parse(response);
                            console.log(responseObj);
                            if (responseObj.status == "success") {
                                Swal.fire({
                                    icon: responseObj.status,
                                    title: responseObj.status,
                                    text: responseObj.message,
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: responseObj.status,
                                    title: responseObj.status,
                                    text: responseObj.message,
                                }).then(function() {
                                    location.reload();
                                });
                            }
                        },
                        complete: function(xhr, status) {
                            if (xhr.status != 200) {
                                $(`#reconciliationFormDiv_${row_id}`).html("Something went wrong, please try again!");
                            }
                            log('Customer Invoice details request completed with status code:', xhr.status);
                        }
                    });
                } else {
                    alert("choose invoices of single customer");
                }

            });


            // $(document).on("click", ".unrecognisedTnxTblRow", function() {
            //     const tnxId = ($(this).attr("id")).split("_")[1];
            //     const tnxDetails = JSON.parse(atob($(this).attr("data-tnx")));
            //     log("Tnx Row is clicked and the tnx id is " + tnxId);
            //     log(tnxDetails);
            // });

            // This is for Catagorize Manual Transaction

            $(document).on("click", "#manualtransactionbutton", function() {
                let totalValue = 0;
                let tnx_category = $('.selectTransactionCategory :selected').val();
                let type = "";
                if (tnx_category == "vendor_payment") {
                    type = "vendor";
                } else {
                    type = "customer";
                }

                const idarray = [];
                $(document).find('.recAmt').each(function(index, element) {
                    let rowId = ($(element).attr("id")).split("_")[1];
                    let value = $(element).val();
                    if (value != '')
                        idarray.push({
                            "id": rowId,
                            "value": value
                        });
                });

                $.ajax({
                    type: "POST",
                    url: 'ajaxs/reconciliation/ajax-manual-transaction.php',
                    data: {
                        idarray,
                        statement_id,
                        type
                    },
                    success: function(response) {
                        let responseObj = JSON.parse(response);
                        if (responseObj.status == "success") {
                            Swal.fire({
                                icon: responseObj.status,
                                title: responseObj.status,
                                text: responseObj.message,
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: responseObj.status,
                                title: responseObj.status,
                                text: responseObj.message,
                            }).then(function() {
                                location.reload();
                            });
                        }
                    },
                    complete: function(xhr, status) {
                        if (xhr.status != 200) {
                            $(`#reconciliationFormDiv_${row_id}`).html("Something went wrong, please try again!");
                        }
                        log('Customer Invoice details request completed with status code:', xhr.status);
                    }
                });
            });

            $(document).on("keyup", ".receiveAmt", function() {
                let recAmt = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                let invoiceAmt = $(`#invoiceAmt`).text();
                let dueAmt = (parseFloat($(`#dueAmt`).text()) > 0) ? parseFloat($(`#dueAmt`).text()) : 0;
                //   let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
                let duePercentage = ((parseFloat(dueAmt) - parseFloat(recAmt)) / parseFloat(invoiceAmt)) * 100;
                $(`#duePercentage`).text(`${Math.round(duePercentage,2)}%`);

                var totalDueAmt = 0;
                var totalRecAmt = 0;

                $(".receiveAmt").each(function() {
                    totalRecAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                });

                $(`#calculativevalue`).html("<span class='rupee-symbol'>₹</span>" + totalRecAmt.toFixed(2));

                if (recAmt <= dueAmt) {
                    $(`#warningMsg`).hide();
                } else {
                    $(`#warningMsg`).show();
                }

                if (totalRecAmt > price_value) {
                    $(`#warning_text`).show();
                    $(`#warning_text`).html("Price is Exceeding");

                } else {
                    $(`#warning_text`).hide();
                }

                // console.log(dueAmt);

            });

            $(document).on("change", ".selectTransactionCategory", function() {
                $("#transCategoryCustomer").hide();
                $("#transCategoryVendor").hide();
                $(`#reconciliationFormDiv`).html("");

                let row_id = statement_id;
                let tnx_category = $(this).val();
                if (tnx_category == "vendor_payment") {

                    $("#transCategoryCustomer").hide();
                    $("#transCategoryVendor").show();

                    $.ajax({
                        type: "GET",
                        url: "ajaxs/brs/ajax-bank-transaction-modal-list.php",
                        dataType: 'json',
                        data: {
                            act: "vendorManualSelect",
                            tnxType: "<?= $tnxType ?>",
                            bankId: "<?= $bankId ?>"
                        },
                        beforeSend: function() {},
                        success: function(value) {
                            console.log(value);
                            let response = value.data;
                            let output = [];
                            output.push(`<option value="">Select Vendor</option>`);
                            $.each(response, function(key, value) {
                                output.push(`<option value="${value.vendor_id}">${value.vendor_code} - ${value.vendor_name}</option>`);
                            });
                            $('#selectVendorDropdown').html(output.join(''));

                        },
                        complete: function() {},
                        error: function(error) {
                            console.log(error);
                        }
                    });

                } else if (tnx_category == "customer_payment") {

                    $("#transCategoryCustomer").show();
                    $("#transCategoryVendor").hide();

                    $.ajax({
                        type: "GET",
                        url: "ajaxs/brs/ajax-bank-transaction-modal-list.php",
                        dataType: 'json',
                        data: {
                            act: "customerManualSelect",
                            tnxType: "<?= $tnxType ?>",
                            bankId: "<?= $bankId ?>"
                        },
                        beforeSend: function() {},
                        success: function(value) {
                            console.log(value);
                            let response = value.data;
                            let output = [];
                            output.push(`<option value="">Select Customer</option>`);
                            $.each(response, function(key, value) {
                                output.push(`<option value="${value.customer_id}">${value.customer_code} - ${value.customer_name}</option>`);
                            });
                            $('#selectCustomerDropdown').html(output.join(''));
                        },
                        complete: function() {},
                        error: function(error) {
                            console.log(error);
                        }
                    });

                } else {
                    $("#transCategoryCustomer").hide();
                    $("#transCategoryVendor").hide();
                    $(`#reconciliationFormDiv`).html("");
                }
            });

            $(document).on("change", "#selectCustomerDropdown", function() {
                let customer_id = $(this).val();
                $.ajax({
                    type: "POST",
                    url: 'ajaxs/reconciliation/ajax-get-customer-due-invoice-list.php',
                    data: {
                        customer_id
                    },
                    beforeSend: function() {
                        $(`#reconciliationFormDiv`).html("Loading, Please wait...");
                    },
                    success: function(response) {
                        let responseObj = JSON.parse(response);
                        console.log(responseObj);
                        $(`#reconciliationFormDiv`).html(responseObj);
                        // log('Data received:', response);
                    },
                    complete: function(xhr, status) {
                        if (xhr.status != 200) {
                            $(`#reconciliationFormDiv`).html("Something went wrong, please try again!");
                        }
                        log('Customer Invoice details request completed with status code:', xhr.status);
                    }
                });
            });

            $(document).on("change", "#selectVendorDropdown", function() {
                let vendor_id = $(this).val();
                $.ajax({
                    type: "POST",
                    url: 'ajaxs/reconciliation/ajax-get-vendor-due-invoice-list.php',
                    data: {
                        vendor_id
                    },
                    beforeSend: function() {
                        $(`#reconciliationFormDiv`).html("Loading, Please wait...");
                    },
                    success: function(response) {
                        // let responseObj = JSON.parse(response);
                        console.log(response);
                        $(`#reconciliationFormDiv`).html(response);
                        //   log('Data received:', response);
                    },
                    complete: function(xhr, status) {
                        if (xhr.status != 200) {
                            $(`#reconciliationFormDiv`).html("Something went wrong, please try again!");
                        }
                        log('Vendor Invoice details request completed with status code:', xhr.status);
                    }
                });
            });

            $(document).on("click", "#customerWiseBanking-tab", function() {
                totalValuetoshw = 0;
                $("#calculativevalue").html("");
                $("#warning_text").hide();
                $('input[name="match_trxn_checkbx_vendor"]').prop('checked', false);

            });

            $(document).on("click", "#vendorWisebanking-tab", function() {
                totalValuetoshw = 0;
                $("#calculativevalue").html("");
                $("#warning_text").hide();
                $('input[name="match_trxn_checkbx"]').prop('checked', false);
            });

            $(document).on("click", "#profile-tab", function() {
                totalValuetoshw = 0;
                $("#calculativevalue").html("");
                $("#warning_text").hide();
                $('input[name="match_trxn_checkbx"]').prop('checked', false);
                $('input[name="match_trxn_checkbx_vendor"]').prop('checked', false);
            });

            $(document).on("click", "#home-tab", function() {
                totalValuetoshw = 0;
                $("#calculativevalue").html("");
                $("#warning_text").hide();
                $('input[name="match_trxn_checkbx_acc_vendor"]').prop('checked', false);
                $('input[name="match_trxn_checkbx_acc_cust"]').prop('checked', false);
            });

            $(document).on("click", "#nonAccCustomerWiseBanking-tab", function() {
                totalValuetoshw = 0;
                $("#calculativevalue").html("");
                $("#warning_text").hide();
                $('input[name="match_trxn_checkbx_acc_vendor"]').prop('checked', false);

            });

            $(document).on("click", "#nonAccVendorWisebanking-tab", function() {
                totalValuetoshw = 0;
                $("#calculativevalue").html("");
                $("#warning_text").hide();
                $('input[name="match_trxn_checkbx_acc_cust"]').prop('checked', false);

            });

            $(document).on("click", "#vendorWisebanking-tab", function() {
                totalValuetoshw = 0;
                $("#calculativevalue").html("");
                $("#warning_text").hide();
                $('input[name="match_trxn_checkbx"]').prop('checked', false);
            });

            $(document).on("click", "#catergorizeMannualy", function() {
                totalValuetoshw = 0;
                $("#calculativevalue").html("");
                $("#warning_text").hide();
                $('input[name="match_trxn_checkbx"]').prop('checked', false);
                $('input[name="match_trxn_checkbx_vendor"]').prop('checked', false);
                $('input[name="match_trxn_checkbx_acc_vendor"]').prop('checked', false);
                $('input[name="match_trxn_checkbx_acc_cust"]').prop('checked', false);
            });

            innerTableCustomer(maxlimit = "", page_id = "");
            innerTableVendor(maxlimit = "", page_id = "");

            $(`#unrecognisedTnxModal`).modal('show');
            innerTableAccCust(maxlimit = "", page_id = "");
            innerTableAccVendor(maxlimit = "", page_id = "");
        });
    });
</script>

<!-- non accounted  customer wise list unrecognized modal -->
<script>
    let tableCustomer;
    tableCustomer = $('#stockLogsTable').DataTable({
        dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
        "lengthMenu": [10, 25, 50, 100, 200],
        "ordering": false,
        info: false,
        "pageLength": true,

        buttons: [
            //     {
            //     extend: 'collection',
            //     text: '<ion-icon name="download-outline"></ion-icon> Export',
            //     buttons: [{
            //         extend: 'excel',
            //         text: '<ion-icon name="document-outline" class="ion-excel"></ion-icon> Excel',
            //         filename: 'bank'
            //     }]
            // }
        ],
        // select: true,
        "bPaginate": false,
    });

    function innerTableCustomer(maxlimit = "", page_id = "") {
        let amount = parseFloat($(`#price_value_hidden`).html());

        $.ajax({
            type: "GET",
            url: `ajaxs/brs/ajax-bank-transaction-modal-list.php`,
            dataType: "json",
            data: {
                act: "bankTrans",
                maxlimit: maxlimit,
                page_id: page_id,
                amount:amount
            },
            beforeSend: function() {
                $(`.stock-log-body`).html(` <tr>
                        <td colspan=17 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>
                    </tr>`);
            },
            success: function(res) {
                console.log(res);
                let resObj = res.data;
                tableCustomer.clear().draw();
                $("#limitTextinner").html(res.limitTxt);
                $("#yourDataTable_paginateinner").html(res.pagination);

                $.each(resObj, function(index, value) {
                    // console.log(value.sl_no);
                    let inputCol = `<input type="checkbox" name="match_trxn_checkbx" value="${value.so_invoice_id}" id="${value.so_invoice_id}" data-amt="${value.due_amount}" data-customerarray="${value.customer_code}" data-statement_id="${statement_id}">`;

                    tableCustomer.row.add([
                        `<p class="text-center">${value.sl_no}</p>`,
                        `<p class="text-center">${inputCol}</p>`,
                        `<p class="text-center">${value.customer_code}</p>`,
                        `<p class="pre-normal">${value.trade_name}</p>`,
                        `<p class="text-right">${decimalAmount(value.due_amount)}</p>`,
                        `<p class="text-center">${formatDate(value.invoice_date)}</p>`,
                    ]).draw(false);

                });
                // //console.log(res);

            },
            complete: function() {}
        });
    }

    $(document).on("change", ".custom-select-inner", function(e) {
        var maxlimit = $(this).val();
        innerTableCustomer(maxlimit, page_id = "");
    });

    $(document).on("click", "#paginationinner a", function(e) {
        e.preventDefault();
        var page_id = $(this).attr('id');
        var limitDisplay = $(".custom-select-inner").val();
        innerTableCustomer(maxlimit = limitDisplay, page_id = page_id);
    });

    $(document).on("click", "#dateSearchInner", function(e) {
        innerTableCustomer(maxlimit = "", page_id = "");
    });
</script>

<!-- Non accounted Vendor wise list unrecognized modal -->
<script>
    let tableVendor;
    tableVendor = $('#stockLogsTableVendor').DataTable({
        dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
        "lengthMenu": [10, 25, 50, 100, 200],
        "ordering": false,
        info: false,
        "pageLength": true,

        buttons: [
            //     {
            //     extend: 'collection',
            //     text: '<ion-icon name="download-outline"></ion-icon> Export',
            //     buttons: [{
            //         extend: 'excel',
            //         text: '<ion-icon name="document-outline" class="ion-excel"></ion-icon> Excel',
            //         filename: 'bank'
            //     }]
            // }
        ],
        // select: true,
        "bPaginate": false,
    });

    function innerTableVendor(maxlimit = "", page_id = "") {
        let amount = parseFloat($(`#price_value_hidden`).html());

        $.ajax({
            type: "GET",
            url: `ajaxs/brs/ajax-bank-transaction-modal-list.php`,
            dataType: "json",
            data: {
                act: "bankTransVendor",
                maxlimit: maxlimit,
                page_id: page_id,
                amount: amount
            },
            beforeSend: function() {
                $(`.stock-log-bodyVendor`).html(` <tr>
                        <td colspan=17 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>
                    </tr>`);
            },
            success: function(res) {
                let resObj = res.data;
                tableVendor.clear().draw();
                $("#limitTextinnerVendor").html(res.limitTxt);
                $("#yourDataTable_paginateinnerVendor").html(res.pagination);

                $.each(resObj, function(index, value) {
                    // console.log(value.sl_no);
                    let inputCol = `<input type="checkbox" name="match_trxn_checkbx_vendor" value="${value.grnIvId}" id="${value.grnIvId}" data-amt="${value.dueAmt}" data-vendorarray="${value.vendorId}" data-statement_id="">`;

                    tableVendor.row.add([
                        `<p class="text-center">${value.sl_no}</p>`,
                        `<p class="text-center">${inputCol}</p>`,
                        `<p class="text-center">${value.vendorCode}</p>`,
                        `<p class="pre-normal">${value.vendorName}</p>`,
                        `<p class="text-right">${decimalAmount(value.dueAmt)}</p>`,
                        `<p class="text-center">${formatDate(value.postingDate)}</p>`,
                    ]).draw(false);

                });
                // //console.log(res);

            },
            complete: function() {}
        });
    }

    $(document).on("change", ".custom-select-innerVendor", function(e) {
        var maxlimit = $(this).val();
        innerTableVendor(maxlimit, page_id = "");
    });

    $(document).on("click", "#paginationinner2 a", function(e) {
        e.preventDefault();

        var page_id = $(this).attr('id');
        var limitDisplay = $(".custom-select-innerVendor").val();
        // alert(`${page_id} - ${limitDisplay}`);
        innerTableVendor(maxlimit = limitDisplay, page_id = page_id);
    });

    $(document).on("click", "#dateSearchInner", function(e) {
        innerTableVendor(maxlimit = "", page_id = "");
    });
</script>

<!--  account Customer wise list unrecognized modal -->
<script>
    let tableNonCustomer;
    tableNonCustomer = $('#stockLogsTableNonAccCust').DataTable({
        dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
        "lengthMenu": [10, 25, 50, 100, 200],
        "ordering": false,
        info: false,
        "pageLength": true,

        buttons: [
            //     {
            //     extend: 'collection',
            //     text: '<ion-icon name="download-outline"></ion-icon> Export',
            //     buttons: [{
            //         extend: 'excel',
            //         text: '<ion-icon name="document-outline" class="ion-excel"></ion-icon> Excel',
            //         filename: 'bank'
            //     }]
            // }
        ],
        // select: true,
        "bPaginate": false,
    });

    function innerTableAccCust(maxlimit = "", page_id = "") {
        $.ajax({
            type: "GET",
            url: `ajaxs/brs/ajax-bank-transaction-modal-list.php`,
            dataType: "json",
            data: {
                act: "bankTransNonAccCustomer",
                maxlimit: maxlimit,
                page_id: page_id
            },
            beforeSend: function() {
                $(`.stock-log-bodyNonAccCust`).html(` <tr>
                        <td colspan=17 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>
                    </tr>`);
            },
            success: function(res) {
                let resObj = res.data;
                tableNonCustomer.clear().draw();
                $("#limitTextinnerNonAccCust").html(res.limitTxt);
                $("#yourDataTable_paginateinnerNonAccCust").html(res.pagination);

                $.each(resObj, function(index, value) {
                    let inputCol = `<input type="checkbox" name="match_trxn_checkbx_acc_cust" value="${value.invoice_id}" id="${value.invoice_id}" data-amt="${value.amount}" data-customerarray="${value.customer_code}" data-statement_id="${statement_id}">`;

                    tableNonCustomer.row.add([
                        `<p class="text-center">${value.sl_no}</p>`,
                        inputCol,
                        `<p class="text-center">${value.postingDate}</p>`,
                        `<p class="pre-normal">${value.party}</p>`,
                        `<p class="text-center">${value.type}</p>`,
                        `<p class="text-center">${formatDate(value.invoice_date)}</p>`,
                        `<p class="text-center">${decimalAmount(value.amount)}</p>`,
                        `<p class="text-center">${value.customer_code}</p>`,
                        `<p class="pre-normal">${value.customer_name}</p>`,
                        `<p class="text-center">${value.transaction_type}</p>`,
                    ]).draw(false);

                });
            },
            complete: function() {}
        });
    }

    $(document).on("change", ".custom-select-innerNonAccCust", function(e) {
        var maxlimit = $(this).val();
        innerTableAccCust(maxlimit, page_id = "");
    });

    $(document).on("click", "#paginationinner3 a ", function(e) {
        e.preventDefault();
        var page_id = $(this).attr('id');
        var limitDisplay = $(".custom-select-innerNonAccCust").val();
        innerTableAccCust(maxlimit = limitDisplay, page_id = page_id);
    });

    $(document).on("click", "#dateSearchInnerNonAccCust", function(e) {
        innerTableAccCust(maxlimit = "", page_id = "");
    });
</script>

<!--  account vendor wise list unrecognized modal -->
<script>
    let tableNonVendor;
    tableNonVendor = $('#stockLogsTableNonAccVendor').DataTable({
        dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
        "lengthMenu": [10, 25, 50, 100, 200],
        "ordering": false,
        info: false,
        "pageLength": true,

        buttons: [
            //     {
            //     extend: 'collection',
            //     text: '<ion-icon name="download-outline"></ion-icon> Export',
            //     buttons: [{
            //         extend: 'excel',
            //         text: '<ion-icon name="document-outline" class="ion-excel"></ion-icon> Excel',
            //         filename: 'bank'
            //     }]
            // }
        ],
        // select: true,
        "bPaginate": false,
    });

    function innerTableAccVendor(maxlimit = "", page_id = "") {
        $.ajax({
            type: "GET",
            url: `ajaxs/brs/ajax-bank-transaction-modal-list.php`,
            dataType: "json",
            data: {
                act: "bankTransNonAccVendor",
                maxlimit: maxlimit,
                page_id: page_id
            },
            beforeSend: function() {
                $(`.stock-log-bodyNonAccVendor`).html(` <tr>
                        <td colspan=17 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>
                    </tr>`);
            },
            success: function(res) {
                let resObj = res.data;
                tableNonVendor.clear().draw();
                $("#limitTextinnerNonAccVendor").html(res.limitTxt);
                $("#yourDataTable_paginateinnerNonAccVendor").html(res.pagination);

                $.each(resObj, function(index, value) {
                    // console.log(value.sl_no);
                    let inputCol = `<input type="checkbox" name="match_trxn_checkbx_acc_vendor" value="${value.grnId}" id="${value.grnId}" data-amt="${value.amount}" data-vendorarray="${value.vendor_code}" data-statement_id="${statement_id}">`;

                    tableNonVendor.row.add([
                        `<p class="text-center">${value.sl_no}</p>`,
                        inputCol,
                        `<p class="pre-normal">${formatDate(value.postingDate)}</p>`,
                        `<p class="pre-normal">${value.party}</p>`,
                        `<p class="text-center">${value.type}</p>`,
                        `<p class="text-center">${formatDate(value.posting_date)}</p>`,
                        `<p class="text-center">${decimalAmount(value.amount)}</p>`,
                        `<p class="text-center">${value.vendor_code}</p>`,
                        `<p class="pre-normal">${value.vendor_name}</p>`,
                        `<p class="text-center">${value.transaction_type}</p>`,
                    ]).draw(false);

                });
            },
            complete: function() {}
        });
    }

    $(document).on("change", ".custom-select-innerNonAccVendor", function(e) {
        var maxlimit = $(this).val();
        innerTableAccVendor(maxlimit, page_id = "");
    });

    $(document).on("click", "#paginationinner4 a ", function(e) {
        e.preventDefault();
        var page_id = $(this).attr('id');
        var limitDisplay = $(".custom-select-innerNonAccVendor").val();
        innerTableAccVendor(maxlimit = limitDisplay, page_id = page_id);
    });

    $(document).on("click", "#dateSearchInnerNonAccVendor", function(e) {
        innerTableAccVendor(maxlimit = "", page_id = "");
    });
</script>

<script>
    $('#catergorizeMannualy').on('click', function() {
        $('#myMatchTransactionTab').hide();
    });
    $('#matchTransaction').on('click', function() {
        $('#myMatchTransactionTab').show();
    });
</script>

<script>
    const progress = document.getElementById('progress');
    const prev = document.getElementById('prev');
    const next = document.getElementById('next');
    const circles = document.querySelectorAll('.circle');
    const formSteps = document.querySelectorAll('.form-step');

    let currentActive = 1;

    next.addEventListener('click', () => {
        currentActive++;

        if (currentActive > circles.length) {
            currentActive = circles.length;
        }
        progress.style.width = '100%';
        update();
    });

    prev.addEventListener('click', () => {
        currentActive--;

        if (currentActive < 1) {
            currentActive = 1;
        }
        progress.style.width = '0%';
        update();
    });

    function update() {
        formSteps.forEach((step, idx) => {
            if (idx + 1 === currentActive) {
                step.style.display = 'block';
            } else {
                step.style.display = 'none';
            }
        });

        circles.forEach((circle, idx) => {
            if (idx < currentActive) {
                circle.classList.add('active');
            } else {
                circle.classList.remove('active');
            }
        });

        const actives = document.querySelectorAll('.active');



        if (currentActive === circles.length) {
            next.disabled = false;
        } else {
            next.disabled = false;
        }

        if (currentActive === 1) {
            prev.disabled = true;
        } else {
            prev.disabled = false;
        }
    }

    update(); // Initialize the progress and buttons
</script>