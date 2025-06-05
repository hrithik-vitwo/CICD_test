<?php
require_once("../../../../app/v1/connection-branch-admin.php");

class CustomerDueInvoice
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

    public function getList($customer_id)
    {
        $dbObj = new Database();
        return $dbObj->queryGet('SELECT * FROM `erp_branch_sales_order_invoices` AS s LEFT JOIN `erp_status_master` AS status_master ON status_master.`code` = s.`invoiceStatus` WHERE s.`company_id`=' . $this->company_id . ' AND s.`branch_id`=' . $this->branch_id . ' AND s.`location_id`=' . $this->location_id . ' AND s.`customer_id`=' . $customer_id . ' AND s.`invoiceStatus`!=4', true);
    }
}


if (isset($_POST['customer_id']) && $_POST['customer_id'] > 0) {
    $customer_id = $_POST['customer_id'];
    $customerDueInvoiceObj = new CustomerDueInvoice();
    $listObj = $customerDueInvoiceObj->getList($customer_id);

    $table = "<table class='table defaultDataTable table-nowrap classic-view'>
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
    // console($listObj);
    $dynamic_data = [];
    foreach ($listObj["data"] as $listItem) {

        $dynamic_data[] = [
            "status" => $listItem["label"],
            "all_total_amt" => inputValue($listItem["all_total_amt"]),
            "invoice_no" => $listItem["invoice_no"],
            "label" => $listItem["label"],
            "all_total_amt" => inputValue($listItem["all_total_amt"]),
            "due_amount" => inputValue($listItem["due_amount"]),
            "so_invoice_id" => $listItem["so_invoice_id"],
            "customer_id" => $customer_id,
            "invoice_date"=>$listItem['invoice_date'] 
            
         ];
        

        $table .= 
            "<tr>
                <input type='hidden' name='invoice[".$listItem["so_invoice_id"]."]['invoice_id']' value='".$listItem["so_invoice_id"]."'>
                <input type='hidden' name='invoice[".$listItem["so_invoice_id"]."]['invoice_number']' value='".$listItem["invoice_no"]."'>
                <input type='hidden' name='invoice[".$listItem["so_invoice_id"]."]['status']' value='".$listItem["invoiceStatus"]."'>
                <input type='hidden' name='invoice[".$listItem["so_invoice_id"]."]['credit_period']' value='".$listItem["credit_period"]."'>
                <input type='hidden' name='invoice[".$listItem["so_invoice_id"]."]['invoice_amt']' value='".$listItem["all_total_amt"]."'>
                <input type='hidden' name='invoice[".$listItem["so_invoice_id"]."]['due_amt']' value='".$listItem["due_amount"]."'>
                <td>".$listItem["invoice_no"]."</td>
                <td><span class='text-uppercase status-danger'>".$listItem["label"]."</span></td>
                <td>2023-10-14</td>
                <td class='invAmt invoiceAmt text-right' id='invoiceAmt_".$listItem["so_invoice_id"]."'>".$listItem["all_total_amt"]."</td>
                <td class='dueAmt text-right' id='dueAmt_".$listItem["so_invoice_id"]."'>".$listItem["due_amount"]."</td>
                <td>
                    <div class='input-group m-0'>
                        <div class='input-group-prepend'>
                            <span class='input-group-text' style='font-family:'Font Awesome 5 Free'' id='basic-addon1'>₹</span>
                        </div>
                        <input class='form-control custNonAccInput' min='0' data-invamt='".$listItem['all_total_amt']."' data-customer_id='".$customer_id."' data-status='".$listItem["label"]."' data-inv_no='".$listItem["invoice_no"]."' data-id='".$listItem["so_invoice_id"]."' data-dueamount='".$listItem["due_amount"]."' data-customerarray='".$customer_id."' type='number' name='customernonacc' >
                    </div>
                    <small style='display: none;' class='text-danger mt-n4 warningMsg' id='warningMsg_".$listItem["so_invoice_id"]."'>Amount Exceeded </small>
                </td>
                <td class='duePercentage' id='duePercentage_".$listItem["so_invoice_id"]."'>100%</td>
            </tr>";
    }

    $table .= "</tbody>
    </table>";

    $responseArr = [
        "status" => "success",
        "message" => "Data found successfully!",
        "data" => $dynamic_data,
        "table" => $table
    ];

    http_response_code(200);
    echo json_encode($responseArr, true);


} else {
    header("Content-Type: application/json");
    http_response_code(400);
    echo json_encode([
        "status" => "warning",
        "message" => "Somthing went wrong, please try again!",
        "data" => []
    ]);
}
