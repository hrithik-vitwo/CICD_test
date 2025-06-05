<?php
include_once("../../../../app/v1/connection-branch-admin.php");

$responseData = [];

$reconMonth = date('m');
$reconYear = date('Y');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $reconData = $_POST["reconData"];

    $listSuccessIndex = [];
    foreach ($reconData as $reconKey => $oneReconData) {

        // Check if the record already exists
        $checkSql = 'SELECT COUNT(*) AS count FROM `erp_branch_gstr2b_reconciliation` WHERE 
            `company_id`=' . $company_id . ' AND 
            `branch_id`=' . $branch_id . ' AND 
            `reconMonth`=' . $reconMonth . ' AND 
            `reconYear`=' . $reconYear . ' AND 
            `localVendorInvNo`="' . $oneReconData["localInvoiceNo"] . '" AND 
            `portalVendorInvNo`="' . $oneReconData["portalInvoiceNo"] . '"';

        $checkResult = queryGet($checkSql);
        // console($checkResult);
        // exit();

        if ($checkResult["data"]["count"] == 0) {
            // Record does not exist, proceed with insertion
            $sql = 'INSERT INTO `erp_branch_gstr2b_reconciliation` SET 
                    `company_id`=' . $company_id . ',
                    `branch_id`=' . $branch_id . ',
                    `reconMonth`=' . $reconMonth . ',
                    `reconYear`=' . $reconYear . ',
                    `reconStatus`="pending",
                    `portalVendorGstin`="' . $oneReconData["portalVendorGstin"] . '",
                    `portalVendorDocDate`="' . $oneReconData["portalInvoiceDate"] . '",
                    `portalVendorName`="' . $oneReconData["portalVendorName"] . '",
                    `portalVendorInvNo`="' . $oneReconData["portalInvoiceNo"] . '",
                    `portalVendorInvAmt`=' . $oneReconData["portalInvoiceAmt"] . ',
                    `portalVendorTaxAmt`=' . $oneReconData["portalInvoiceTaxAmt"] . ',
                    `localVendorGstin`="' . $oneReconData["localVendorGstin"] . '",
                    `localVendorName`="' . $oneReconData["localVendorName"] . '",
                    `localVendorInvNo`="' . $oneReconData["localInvoiceNo"] . '",
                    `localVendorInvAmt`=' . $oneReconData["localInvoiceAmt"] . ',
                    `localVendorTaxAmt`=' . $oneReconData["localInvoiceTaxAmt"] . ',
                    `localVendorDocDate`="' . $oneReconData["localInvoiceDate"] . '",
                    `matchedPercentage`=' . $oneReconData["reconPercentage"];

            $reconObj = queryInsert($sql);

            if ($reconObj["status"] == "success") {
                $listSuccessIndex[] = $reconKey;
            }
        }
    }

    // Count pending records
    $countPendingReconDataSql = 'SELECT COUNT(`id`) AS pendingNo, IFNULL(SUM(`portalVendorTaxAmt`), 0) AS totalPendingInvTax FROM `erp_branch_gstr2b_reconciliation` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `reconMonth`=' . $reconMonth . ' AND `reconYear`=' . $reconYear . ' AND `reconStatus`="pending"';
    $countPendingReconDataObj = queryGet($countPendingReconDataSql);

    // Prepare the response
    if (count($listSuccessIndex) > 0) {
        $responseData = [
            "status" => "success",
            "message" => "Invoices added to Reconciliation list successfully",
            "data" => $listSuccessIndex,
            "listCounter" => $countPendingReconDataObj["data"]["pendingNo"],
            "listTotalTax" => $countPendingReconDataObj["data"]["totalPendingInvTax"]
        ];
    } else {
        $responseData = [
            "status" => "warning",
            "message" => "Invoices failed to add to Reconciliation list",
            "data" => $reconData,
            "listCounter" => 0,
            "listTotalTax" => 0
        ];
    }

    echo json_encode($responseData, true);
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $countPendingReconDataSql = 'SELECT COUNT(`id`) AS pendingNo, IFNULL(SUM(`portalVendorTaxAmt`), 0) AS totalPendingInvTax FROM `erp_branch_gstr2b_reconciliation` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `reconMonth`=' . $reconMonth . ' AND `reconYear`=' . $reconYear . ' AND `reconStatus`="pending"';
    $countPendingReconDataObj = queryGet($countPendingReconDataSql);
?>
    <div class="modal-header">
        <div class="row mt-2">
            <div class="col-lg-12 col-md-12 col-sm-12">
                GSTR-2B Reconciliation List
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 mt-1">
            <div class="div w-100 p-3 d-flex justify-content-between">
                <div>
                    <p>Carry Forworded Credit</p>
                    <p style="font-size: 20px;"><i class="fas fa-rupee-sign"></i> 9,55,000.00</p>
                </div>
                <div>
                    <p>List Credit</p>
                    <p style="font-size: 20px;"><i class="fas fa-rupee-sign"></i><span class="reconListAmountSpan"><?= number_format($countPendingReconDataObj["data"]["totalPendingInvTax"], 2) ?></span></p>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-body px-0">
        <section class="content">
            <div class="container-fluid my-4">
                <div class="row p-0 m-0">
                    <?php
                    $countPendingReconDataSql = 'SELECT * FROM `erp_branch_gstr2b_reconciliation` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `reconMonth`=' . date("m") . ' AND `reconYear`=' . date("Y") . ' AND `reconStatus`="pending"';
                    $countPendingReconDataObj = queryGet($countPendingReconDataSql, true);
                    // console($countPendingReconDataObj);
                    ?>
                    <div class="col-12">
                        <table class="table">
                            <thead>
                                <th>Date</th>
                                <th>GSTIN</th>
                                <th>VENDOR NAME</th>
                                <th>PORTAL INVOICE NO</th>
                                <th>LOCAL INVOICE NO</th>
                                <th>INV AMOUNT</th>
                                <th>TAX AMOUNT</th>
                                <th>ITC</th>
                                <th style="background-color: #011a3c!important; color:white">MATCH</th>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($countPendingReconDataObj["data"] as $oneData) {
                                ?>
                                    <tr>
                                        <td class=""><?= $oneData["portalVendorDocDate"] ?></td>
                                        <td class=""><?= $oneData["portalVendorGstin"] ?></td>
                                        <td class=""><?= substr($oneData["portalVendorName"], 0, 15) ?></td>
                                        <td class=""><?= $oneData["portalVendorInvNo"] ?></td>
                                        <td class=""><?= $oneData["localVendorInvNo"] ?></td>
                                        <td class="text-right"><?= $oneData["portalVendorInvAmt"] ?></td>
                                        <td class="text-right"><?= $oneData["portalVendorTaxAmt"] ?></td>
                                        <td><?= ($oneData["isItcAvailable"]) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>'; ?></td>
                                        <td><?= $oneData["matchedPercentage"] . "%"; ?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-right"><button class="btn btn-primary">Confirm Reconciliation</button></p>
                </div>
            </div>
        </section>
    </div>
<?php
} else {
    $responseData = [
        "status" => "warning",
        "message" => "Method not allowed",
        "data" => [],
        "listCounter" => 0,
    ];
    echo json_encode($responseData, true);
}
