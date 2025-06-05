<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

// Add Functions
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");
include_once("../../app/v1/functions/branch/func-grn-controller.php");
require_once("bom/controller/bom.controller.php");
include_once("../../app/v1/functions/branch/func-branch-failed-accounting-controller.php");


$dbObj = new Database();
$accountObj = new Accounting();
$bomControllerObj = new BomController();

if (isset($_GET['pay_id'])) {
    $pay_id = base64_decode($_GET['pay_id']);
}
$cond = "AND id =" . $pay_id . "";

$sql_list = "SELECT * FROM `erp_production_declarations` WHERE 1 " . $cond . "  AND`branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=" . $company_id . " AND `prod_declaration_journal_id`  IS NULL AND `fgsfg_declaration_journal_id`  IS NULL   ORDER BY id desc";

$sqlMainQryObj = $dbObj->queryGet($sql_list, true);
$MainData = $sqlMainQryObj['data'];
// console($MainData);
$sql_itemcode = $dbObj->queryGet("SELECT * FROM `erp_production_order_sub` WHERE `sub_prod_id`=" . $MainData[0]['sub_prod_id'] . "");
$itemcodeobj = $sql_itemcode['data'];
$itemId = $itemcodeobj['itemId'];
// console($itemcode);
$bomDetailObj = $bomControllerObj->getBomDetailsByItemId($itemId);
// console($bomDetailObj);
$grnDebitCreditAccListObjproduction = $accountObj->getCreditDebitAccountsList("ProductiondeclarationInventoryissuance");
// console($grnDebitCreditAccListObjproduction);
$grnDebitAccListproduction = $grnDebitCreditAccListObjproduction["debitAccountsList"];
$grnCreditAccListproduction = $grnDebitCreditAccListObjproduction["creditAccountsList"];

$grnDebitCreditAccListObjfgsfg =  $accountObj->getCreditDebitAccountsList("FGSFGDeclaration");
// console($grnDebitCreditAccListObjfgsfg);
$grnDebitAccListfgsfg = $grnDebitCreditAccListObjfgsfg["debitAccountsList"];
$grnCreditAccListfgsfg = $grnDebitCreditAccListObjfgsfg["creditAccountsList"];
$production_code=$MainData[0]['code'];


$declearItemQty = $MainData[0]['quantity'];
$finalProductDetails = [];
$consumpProductData = [];
$total = 0;
$parentGlId = '';
if (!empty($bomDetailObj["data"]["bom_data"])) {
    $finalProductDetails['parentGlId'] = $bomDetailObj["data"]["bom_data"]['parentGlId'];
    $finalProductDetails['itemCode'] = $bomDetailObj["data"]["bom_data"]['itemCode'];
    $finalProductDetails['itemName'] = $bomDetailObj["data"]["bom_data"]['itemName'];
    $finalProductDetails['cogm_m'] = $bomDetailObj["data"]["bom_data"]['cogm_m'] * $declearItemQty;
    $finalProductDetails['cogm_a'] = $bomDetailObj["data"]["bom_data"]['cogm_a'] * $declearItemQty;
    $parentGlId = $bomDetailObj["data"]["bom_data"]['parentGlId'];
    $total = $finalProductDetails['cogm_m'] + $finalProductDetails['cogm_a'];
    foreach ($bomDetailObj["data"]["bom_material_data"] as $keyss => $bomOneItem) {
        $stockLogTransferQty = $bomOneItem["totalConsumption"] * $declearItemQty;

        if ($bomOneItem["priceType"] == "V") {
            $consumpProductData[$keyss]["type"] = $bomOneItem['type'];
            $consumpProductData[$keyss]["stockLogTransferQty"] = $stockLogTransferQty;
            $consumpProductData[$keyss]["parentGlId"] = $bomOneItem['parentGlId'];
            $consumpProductData[$keyss]["itemCode"] = $bomOneItem['itemCode'];
            $consumpProductData[$keyss]["itemName"] = $bomOneItem['itemName'];
            $consumpProductData[$keyss]["unitprice"] = $bomOneItem['movingWeightedPrice'];
            $consumpProductData[$keyss]["price"] = $bomOneItem['movingWeightedPrice'] * $stockLogTransferQty;
            $total += $consumpProductData[$keyss]["price"];
        } else {
            $consumpSfgProductSql = "SELECT bom.`cogm` as cogmprice FROM `erp_bom` WHERE `locationId`=" . $location_id . " AND bomStatus` = 'active' AND `itemId`=" . $bomOneItem["item_id"] . " ORDER BY bomId DESC";

            $consumpSfgProductObj = $dbObj->queryGet($consumpSfgProductSql);

            if ($consumpSfgProductObj["status"] == "success") {
                $consumpProductData[$keyss]["type"] = $bomOneItem['type'];
                $consumpProductData[$keyss]["stockLogTransferQty"] = $stockLogTransferQty;
                $consumpProductData[$keyss]["parentGlId"] = $bomOneItem['parentGlId'];
                $consumpProductData[$keyss]["itemCode"] = $bomOneItem['itemCode'];
                $consumpProductData[$keyss]["itemName"] = $bomOneItem['itemName'];
                $consumpProductData[$keyss]["unitprice"] = $consumpSfgProductObj['data']['cogmprice'];
                $consumpProductData[$keyss]["price"] = $consumpSfgProductObj['data']['cogmprice'] * $stockLogTransferQty;
                $total += $consumpProductData[$keyss]["price"];
            } else {
                $consumpProductData[$keyss]["type"] = $bomOneItem['type'];
                $consumpProductData[$keyss]["stockLogTransferQty"] = $stockLogTransferQty;
                $consumpProductData[$keyss]["parentGlId"] = $bomOneItem['parentGlId'];
                $consumpProductData[$keyss]["itemCode"] = $bomOneItem['itemCode'];
                $consumpProductData[$keyss]["itemName"] = $bomOneItem['itemName'];
                $consumpProductData[$keyss]["unitprice"] = $bomOneItem['movingWeightedPrice'];
                $consumpProductData[$keyss]["price"] = $bomOneItem['movingWeightedPrice'] * $stockLogTransferQty;
                $total += $consumpProductData[$keyss]["price"];
            }
        }
    }
}
// console($finalProductDetails);
// console($consumpProductData);
$productionDeclareDate = formatDateWeb($MainData[0]['created_at']);
$declearItemCode = $itemcodeobj['itemCode'];
$consumptionInputData = [
    "BasicDetails" => [
        "documentNo" => $MainData[0]['code'],
        "documentDate" => (new DateTime($productionDeclareDate))->format('Y-m-d'),
        "postingDate" =>  date("Y-m-d"),
        "reference" => '',
        "remarks" => "Production declaration for - " . $declearItemCode,
        "journalEntryReference" => "Production"
    ],
    "finalProductData" => $finalProductDetails,
    "consumpProductData" => $consumpProductData
];
// console($consumptionInputData);

if (isset($_POST['act'])) {
    $prod_status = '';
    $fgsfg_status = '';
    $proid = $MainData[0]['code'];
    $allItems = queryGet("SELECT `itemId` as itemId, ABS(`itemQty`) as qty FROM erp_inventory_stocks_log WHERE refNumber = '$proid' AND refActivityName= 'PROD-OUT'", true)['data'];
    $allItems = array_map(function ($item) use ($proid) {
        $item['type'] = 'prodin';
        $item['id'] = $proid;
        return $item;
    }, $allItems);
    $allItems2 = queryGet("SELECT `itemId` as itemId, -ABS(`itemQty`) as qty FROM erp_inventory_stocks_log WHERE logRef = '$proid' and refActivityName = 'PROD-IN'", true)['data'];
    $allItems2 = array_map(function ($item) use ($proid) {
        $item['type'] = 'prodin';
        $item['id'] = $proid;
        return $item;
    }, $allItems2);
    // console($allItems2);
    // console($allItems);
    // exit;
    $journal_check_sql_prod = queryGet("SELECT * FROM `erp_acc_journal` WHERE `refarenceCode`='" . $MainData[0]['code'] . "' AND `parent_slug`='ProductiondeclarationInventoryissuance' AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=" . $company_id ."");
    // console($journal_check_sql_prod);
    if ($journal_check_sql_prod['status'] == 'success') {
        $prodJournalId = $journal_check_sql_prod['data']['id'];
        $queryObj = $dbObj->queryUpdate("UPDATE `erp_production_declarations` SET `prod_declaration_journal_id`='$prodJournalId' WHERE `id`='$pay_id'");
        if ($queryObj['status'] = 'success') {
            $prod_status = 'success';
        } else {
            $prod_status = 'error';
        }
    } else {
        //**************************Production Declaration Accounting Start****************************** */
        $respproductionDeclaration = $accountObj->productionDeclarationAccountingPosting($consumptionInputData, 'ProductiondeclarationInventoryissuance', 0);
        //**************************Production Declaration Accounting End****************************** */
        $prodJournalId = $respproductionDeclaration["journalId"];
        $queryObj = $dbObj->queryUpdate("UPDATE `erp_production_declarations` SET `prod_declaration_journal_id`='$prodJournalId' WHERE `id`='$pay_id'");
        if ($queryObj['status'] = 'success') {

            $prod_status = 'success';
        } else {
            $prod_status = 'error';
        }
    }
    $journal_check_sql_fgsfg = queryGet("SELECT * FROM `erp_acc_journal` WHERE `refarenceCode`='" . $MainData[0]['code'] . "' AND `parent_slug`='FGSFGDeclaration' AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=" . $company_id ." ");
    // console($journal_check_sql_fgsfg);
    if ($journal_check_sql_fgsfg['status'] == 'success') {
        $fgsfgJournalId = $journal_check_sql_fgsfg['data']['id'];
        $queryObj = $dbObj->queryUpdate("UPDATE `erp_production_declarations` SET `fgsfg_declaration_journal_id`='$fgsfgJournalId' WHERE `id`='$pay_id'");
        if ($queryObj['status'] = 'success') {
            $fgsfg_status = 'success';
        } else {
            $fgsfg_status = 'error';
        }
    } else {
        //**************************FG/SFG Declaration Accounting Start****************************** */
        $respfgsfgDeclaration = $accountObj->FGSFGDeclarationAccountingPosting($consumptionInputData, 'FGSFGDeclaration', 0);
        //**************************FG/SFG Declaration Accounting End****************************** */
        $fgsfgJournalId = $respfgsfgDeclaration["journalId"];
        $queryObj = $dbObj->queryUpdate("UPDATE `erp_production_declarations` SET `fgsfg_declaration_journal_id`='$fgsfgJournalId' WHERE `id`='$pay_id'");
        if ($queryObj['status'] = 'success') {
            $fgsfg_status = 'success';
        } else {
            $fgsfg_status = 'error';
        }
    }
    if ($prod_status == "success" && $fgsfg_status == "success") {
        stockQtyImpact($allItems2, "repost");
        stockQtyImpact($allItems, "repost");
        $update=updatelogAccountingFailure($production_code);
        swalAlert("success", 'Success', "Production Accounting Success !", 'failed-accounting-production-declaration.php');
    }else {
        swalAlert("warning", 'Failed', "Production Accounting Failed !");
     }
}

?>
<style>
    .wrapper,
    body,
    html {
        min-height: 0%;
    }

    .is-failed-account-view .wrapper-account {
        background: #fff;
        margin: 17px;
        padding: 10px 15px;
        border-radius: 7px;
        height: 93%;
        overflow: auto;
    }

    .is-failed-account-view .wrapper-account h2 {
        font-size: 0.8rem;
        text-align: right;
        color: #787878;
    }

    .is-failed-account-view .wrapper-account h2 ion-icon {
        position: relative;
        font-size: 1rem;
        top: 3px;
        margin-right: 5px;
        font-weight: 700;
    }

    .is-failed-account-view .wrapper-account h2 p {
        margin: 8px 0;
        color: #000;
        font-weight: 600;
        font-size: 0.82rem;
    }

    .is-failed-account-view .wrapper-account .account-list {
        position: relative;
    }

    .is-failed-account-view .wrapper-account .account-list label {
        position: absolute;
        top: -10px;
        left: 13px;
        background: #eaeaea;
        padding: 5px 15px;
        border-radius: 5px;
    }

    .is-failed-account-view .wrapper-account .account-list.credit-acc-list label {
        position: absolute;
        top: -10px;
        left: 13px;
        background: #c9e3c8;
        color: #168506;
        padding: 5px 15px;
        border-radius: 5px;
        border: 1px solid #c9e3c8;
    }

    .is-failed-account-view .wrapper-account .account-list.debit-acc-list label {
        position: absolute;
        top: -10px;
        left: 13px;
        background: #edcaca;
        color: #d52d00;
        padding: 5px 15px;
        border-radius: 5px;
        border: 1px solid #edcaca;
    }

    .is-failed-account-view .wrapper-account .account-list .card-border-area {
        border: 1px solid #eaeaea;
        border-radius: 9px;
        padding: 15px 5px;
        margin-bottom: 30px;
    }

    .is-failed-account-view .wrapper-account .account-list .card-border-area table tr th {
        background: #fff;
        color: #000;
        font-size: 0.7rem;
        font-weight: 600;
        border-bottom: 1px solid #eaeaea;
        padding: 12px 10px 15px;
    }

    .is-failed-account-view .wrapper-account .account-list .card-border-area table tr td {
        background: #fff;
        font-size: 0.75rem;
        padding: 2px 10px;
        border-bottom: 1px solid #ececec;
    }

    .is-failed-account-view .wrapper-account .account-list .card-border-area table tr:nth-child(odd) td {
        background: #f2f2f229;
    }

    .is-failed-account-view .wrapper-account .header-block {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #eaeaea;
        margin-bottom: 30px;
        position: sticky;
        top: -11px;
        background: #fff;
        z-index: 9;
        padding: 5px 0;
    }

    .is-failed-account-view .wrapper-account .account-amount {
        border-radius: 6px;
        border: 1px solid #eaeaea;
        margin-bottom: 10px;
        padding: 7px 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .is-failed-account-view .wrapper-account .account-amount .card-border-area {
        display: flex;
        align-items: center;
        max-width: 100%;
        gap: 8px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .is-failed-account-view .wrapper-account .account-amount label {
        background: #eaeaea;
        padding: 5px 15px;
        border-radius: 5px;
        font-weight: 600;
        margin-bottom: 0;
        width: auto;
    }

    .is-failed-account-view .wrapper-account .account-amount .card-border-area select {
        padding: 3px;
        text-align: center;
        height: 26px;
        width: 44px;
        font-size: 0.9rem;
    }

    .is-failed-account-view .wrapper-account .account-amount .card-border-area input {
        padding: 3px;
        text-align: center;
        height: 26px;
        width: 50px;
        font-size: 0.75rem;
    }

    .is-failed-account-view .wrapper-account .account-list.credit-acc-list .card-border-area {
        border: 1px solid #03a50052;
    }

    .is-failed-account-view .wrapper-account .account-list.debit-acc-list .card-border-area {
        border: 1px solid #be000052;
    }

    .is-failed-account-view .paid-btn {
        display: flex;
        justify-content: center;
    }
</style>
<link rel="stylesheet" href="../../public/assets/stock-report-new.css">
<div class="content-wrapper is-failed-account-view vitwo-alpha-global" style="overflow: auto;">

    <div class="container-fluid mt-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BRANCH_URL; ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
            <li class="breadcrumb-item active"><a href="failed-accounting-production-declaration.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Failed Production Declaration List</a></li>
            <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Production Declaration Posting View</a></li>
            <li class="back-button">
                <a href="failed-accounting-payment.php">
                    <i class="fa fa-reply po-list-icon"></i>
                </a>
            </li>
        </ol>
    </div>

    <form method="post" action="">
        <input type="hidden" name="act">
        <div class="wrapper-account">
            <div class="header-block">

                <h2>Failed Production Declaration For : <b><?= $MainData[0]['code'] ?></b>
                    <?php
                    // if (decimalValuePreview($MainData[0]['total']) != decimalValuePreview($itemTotalAmt)) {
                    // swalAlert("warning", 'Reverse', "Amount Issue in this Production Declaration.");
                    ?>
                    <!-- <span class="status-bg status-closed">Amount Issue in this Production Declaration</span>; -->



                </h2>
                <h2><ion-icon name="analytics-outline"></ion-icon>Posting Date : <p><?= formatDateWeb($MainData[0]['created_at']); ?></p>
                </h2>
            </div>
            <div class="account-list credit-acc-list">
                <label for="">Credit account list</label>
                <div class="card-border-area">
                    <table>
                        <thead>
                            <tr>
                                <th width="25%">Ledger</th>
                                <th>Sub Ledger</th>
                                <th class="text-right">Amount(INR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            foreach ($consumpProductData  as $crkey => $value) {
                                if ($value['type'] == 'RM') { ?>
                                    <tr>
                                        <td>
                                            <p class="pre-normal">
                                                <?= $grnCreditAccListproduction[0]['gl_code'] ?>||<?= $grnCreditAccListproduction[0]['gl_label'] ?>
                                            </p>
                                        </td>
                                        <td>
                                            <p class="pre-normal">
                                                <?= $value['itemCode'] ?> || <?= $value['itemName'] ?>
                                            </p>
                                        </td>
                                        <td class="text-right"><?php echo $value['price'] ?></td>

                                    </tr>
                                <?php } else { ?>
                                    <tr>
                                        <td>
                                            <p class="pre-normal">
                                                <?= $grnCreditAccListproduction[1]['gl_code'] ?>||<?= $grnCreditAccListproduction[1]['gl_label'] ?>
                                            </p>
                                        </td>
                                        <td>
                                            <p class="pre-normal">
                                                <?= $value['itemCode'] ?> || <?= $value['itemName'] ?>
                                            </p>
                                        </td>
                                        <td class="text-right"><?php echo $value['price'] ?></td>

                                    </tr>
                                <?php  } ?>
                            <?php
                            } ?>
                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <?= $grnCreditAccListfgsfg[0]['gl_code'] ?>||<?= $grnCreditAccListfgsfg[0]['gl_label'] ?>
                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        <!-- <?= $finalProductDetails['itemCode'] ?> || <?= $finalProductDetails['itemName'] ?> --> --
                                    </p>
                                </td>
                                <td class="text-right"><?php echo $finalProductDetails['cogm_m'] ?></td>

                            </tr>
                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <?= $grnCreditAccListfgsfg[1]['gl_code'] ?>||<?= $grnCreditAccListfgsfg[1]['gl_label'] ?>
                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        <!-- <?= $finalProductDetails['itemCode'] ?> || <?= $finalProductDetails['itemName'] ?> -->--
                                    </p>
                                </td>
                                <td class="text-right"><?php echo $finalProductDetails['cogm_a'] ?></td>

                            </tr>
                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <b>Total</b>
                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        --
                                    </p>
                                </td>
                                <td class="text-right text-bold"><?php echo decimalValuePreview($total) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="account-list debit-acc-list">
                <label for="">Debit account list</label>
                <div class="card-border-area">
                    <table>
                        <thead>
                            <tr>
                                <th width="25%">Ledger</th>
                                <th>Sub Ledger</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($grnDebitAccListfgsfg as $debitfgsfg) {
                                if ($debitfgsfg['id'] == $parentGlId) {

                            ?>
                                    <tr>
                                        <td>
                                            <p class="pre-normal">
                                                <?= $debitfgsfg['gl_code'] ?>||<?= $debitfgsfg['gl_label'] ?>
                                            </p>
                                        </td>
                                        <td>
                                            <p class="pre-normal">
                                                <?= $finalProductDetails['itemCode'] ?> || <?= $finalProductDetails['itemName'] ?>
                                            </p>
                                        </td>
                                        <td class="text-right"><?php echo $finalProductDetails['cogm_m'] + $finalProductDetails['cogm_a'] ?></td>

                                    </tr>
                            <?php }
                            } ?>
                            <?php

                            foreach ($consumpProductData  as $crkey => $value) {
                                if ($value['type'] == 'RM') { ?>
                                    <tr>
                                        <td>
                                            <p class="pre-normal">
                                                <?= $grnDebitAccListproduction[0]['gl_code'] ?>||<?= $grnDebitAccListproduction[0]['gl_label'] ?>
                                            </p>
                                        </td>
                                        <td>
                                            <p class="pre-normal">
                                                <!-- <?= $value['itemCode'] ?> || <?= $value['itemName'] ?> -->--
                                            </p>
                                        </td>
                                        <td class="text-right"><?php echo $value['price'] ?></td>

                                    </tr>
                                <?php } else { ?>
                                    <tr>
                                        <td>
                                            <p class="pre-normal">
                                                <?= $grnDebitAccListproduction[1]['gl_code'] ?>||<?= $grnDebitAccListproduction[1]['gl_label'] ?>
                                            </p>
                                        </td>
                                        <td>
                                            <p class="pre-normal">
                                                <!-- <?= $value['itemCode'] ?> || <?= $value['itemName'] ?> -->--
                                            </p>
                                        </td>
                                        <td class="text-right"><?php echo $value['price'] ?></td>

                                    </tr>
                                <?php  } ?>
                            <?php
                            } ?>
                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <b>Total</b>
                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        --
                                    </p>
                                </td>
                                <td class="text-right text-bold"><?php echo decimalValuePreview($total) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="account-amount deffrence-amount">
                <label for="">Amount Difference</label>
                <div class="card-border-area">
                    <p>0</p>
                </div>
            </div>

            <div class="paid-btn">
                <button type="submit" class="btn btn-primary float-right">Post</button>
            </div>


        </div>
    </form>

</div>

<?php
require_once("../common/footer.php");
?>

<script>
    function initializeDataTable() {
        dataTable = $("#dataTable_detailed_view").DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
            "lengthMenu": [10, 25, 50, 100, 200, 250],
            "ordering": false,
            info: false,
            "initComplete": function(settings, json) {
                $('#dataTable_detailed_view_filter input[type="search"]').attr('placeholder', 'Search....');
            },

            buttons: [],
            // select: true,
            "bPaginate": false,

        });

    }
    $('#dataTable_detailed_view thead tr').append('<th>Action</th>');

    initializeDataTable();
</script>