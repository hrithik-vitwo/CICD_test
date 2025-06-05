<?php
require_once("../../../../app/v1/connection-branch-admin.php");
header("Content-Type: application/json");

class VendorDueInvoice
{
    private $company_id;
    private $branch_id;
    private $location_id;
    private $created_by;
    private $updated_by;

    function __construct()
    {
        global $company_id, $branch_id, $location_id, $created_by, $updated_by;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->location_id = $location_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }

    public function getList($vendor_id)
    {
        $dbObj = new Database();
        return $dbObj->queryGet('SELECT * FROM `erp_grninvoice` AS v LEFT JOIN `erp_status_master` AS status_master ON status_master.`code` = v.`paymentStatus` WHERE v.`companyId`=' . $this->company_id . ' AND v.`branchId`=' . $this->branch_id . ' AND v.`locationId`=' . $this->location_id . ' AND v.`vendorId`=' . $vendor_id . ' AND v.`paymentStatus`!=4', true);
        
    }
}


if (isset($_POST['vendor_id']) && $_POST['vendor_id'] > 0) {
    $vendor_id = $_POST['vendor_id'];
    $VendorDueInvoiceObj = new VendorDueInvoice();
    $listObj = $VendorDueInvoiceObj->getList($vendor_id);

    $table = "<table class='table defaultDataTable table-nowrap recon-classic-table classic-view'>
    <thead>
        <tr>
            <th>Invoice No</th>
            <th>Status</th>
            <th>Due Dates</th>
            <th>Invoice Amt.</th>
            <th>Due Amt.</th>
            <th>Rec. Amt.</th>
            <th>Due %</th>
        </tr>
    </thead>
    <tbody>";

    $i = 0;
    $dynamic_data = [];
    foreach ($listObj["data"] as $listItem) {

        $dynamic_data[] = [
            "grnivno"=>$listItem['grnIvCode'],
            "vendorDocumentNo" => $listItem["vendorDocumentNo"],
            "postingDate"=>$listItem['postingDate'],
            "label" => $listItem["label"],
            "grnIvId" => $listItem["grnIvId"],
            "grnTotalAmount" => inputValue($listItem["grnTotalAmount"]),
            "dueAmt" => inputValue($listItem["dueAmt"]),
            "paymentStatus" => $listItem["paymentStatus"],
            "vendorId"=>$listItem['vendorId']
        ];

        

        $table .= 
            "<tr>
                <input type='hidden' name='invoice[".$listItem["grnIvId"]."]['invoice_id']' value='".$listItem["grnIvId"]."'>
                <input type='hidden' name='invoice[".$listItem["grnIvId"]."]['invoice_number']' value='".$listItem["vendorDocumentNo"]."'>
                <input type='hidden' name='invoice[".$listItem["grnIvId"]."]['status']' value='".$listItem["paymentStatus"]."'>
                <input type='hidden' name='invoice[".$listItem["grnIvId"]."]['invoice_amt']' value='".$listItem["grnTotalAmount"]."'>
                <input type='hidden' name='invoice[".$listItem["grnIvId"]."]['due_amt']' value='".$listItem["dueAmt"]."'>
                <td>".$listItem["vendorDocumentNo"]."</td>
                <td><span class='text-uppercase status-danger'>".$listItem["label"]."</span></td>
                <td>2023-10-14</td>
                <td class='invAmt invoiceAmt text-right' id='invoiceAmt_".$listItem["grnIvId"]."'>".$listItem["grnTotalAmount"]."</td>
                <td class='dueAmt text-right' id='dueAmt_".$listItem["grnIvId"]."'>".$listItem["dueAmt"]."</td>
                <td>
                    <div class='input-group m-0'>
                        <div class='input-group-prepend'>
                            <span class='input-group-text' style='font-family:'system-ui'' id='basic-addon1'>₹</span>
                        </div>
                        <input type='text' name='".$listItem["grnIvId"]["recAmt"]."' class='form-control receiveAmt px-3 text-right recAmt' id='receiveAmt_".$listItem["grnIvId"]."' placeholder='Amount'>
                    </div>
                    <small style='display: none;' class='text-danger mt-n4 warningMsg' id='warningMsg_".$listItem["grnIvId"]."'>Amount Exceeded </small>
                </td>
                <td class='duePercentage' id='duePercentage_".$listItem["grnIvId"]."'>100%</td>
            </tr>";
    }

    $table .= "</tbody>
    </table>";

    $responseArr = [
        "status" => "success",
        "message" => "Vendor due invoice list",
        "data" =>  $dynamic_data,
        "table" => $table
    ];


    http_response_code(200);
    echo json_encode($responseArr, true);
} else {
    http_response_code(400);
    echo json_encode([
        "status" => "warning",
        "message" => "Somthing went wrong, please try again!",
        "data" => []
    ]);
}