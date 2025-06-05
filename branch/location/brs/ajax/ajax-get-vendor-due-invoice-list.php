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
        // return $dbObj->queryGet('SELECT * FROM `erp_branch_sales_order_invoices` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND `customer_id`=' . $customer_id . ' AND `invoiceStatus`=4', true);
        return [
            "status" => "success",
            "message" => "successfully",
            "data" => [
                "vendor_id" => $vendor_id
            ]
        ];
    }
}


if (isset($_POST['vendor_id']) && $_POST['vendor_id'] > 0) {
    $vendor_id = $_POST['vendor_id'];
    $VendorDueInvoiceObj = new VendorDueInvoice();
    $listObj = $VendorDueInvoiceObj->getList($vendor_id);
    http_response_code(200);
    echo json_encode($listObj, true);
} else {
    http_response_code(400);
    echo json_encode([
        "status" => "warning",
        "message" => "Somthing went wrong, please try again!",
        "data" => []
    ]);
}