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
        return $dbObj->queryGet('SELECT * FROM `erp_branch_sales_order_invoices` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND `customer_id`=' . $customer_id . ' AND `invoiceStatus`=4', true);
    }
}


if (isset($_POST['customer_id']) && $_POST['customer_id'] > 0) {
    $customer_id = $_POST['customer_id'];
    $customerDueInvoiceObj = new CustomerDueInvoice();
    $listObj = $customerDueInvoiceObj->getList($customer_id);

    http_response_code(200);

?>
    <table class="table defaultDataTable table-nowrap classic-view">
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
        <tbody>
            <tr>
                <input type="hidden" name="paymentInvoiceDetails[75][invoiceId]" value="255">
                <input type="hidden" name="paymentInvoiceDetails[75][invoiceNo]" value="INV-0000000232">
                <input type="hidden" name="paymentInvoiceDetails[75][invoiceStatus]" value="sent">
                <input type="hidden" name="paymentInvoiceDetails[75][creditPeriod]" value="50">
                <input type="hidden" name="paymentInvoiceDetails[75][invAmt]" value="13305.60">
                <input type="hidden" name="paymentInvoiceDetails[75][dueAmt]" value="13305.60">
                <td>INV-0000000232</td>
                <td><span class="text-uppercase status-danger">sent</span></td>
                <td>2023-10-14</td>
                <td class="invAmt invoiceAmt text-right" id="invoiceAmt_255">13305.60</td>
                <td class="dueAmt text-right" id="dueAmt_255">13305.60</td>
                <td>
                    <div class="input-group m-0">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1">₹</span>
                        </div>
                        <input type="text" name="paymentInvoiceDetails[75][recAmt]" class="form-control receiveAmt px-3 text-right" id="receiveAmt_255" placeholder="Amount">
                    </div>
                    <small style="display: none;" class="text-danger mt-n4 warningMsg" id="warningMsg_255">Amount Exceeded </small>
                </td>
                <td class="duePercentage" id="duePercentage_255">100%</td>
            </tr>
            <tr>
                <input type="hidden" name="paymentInvoiceDetails[76][invoiceId]" value="264">
                <input type="hidden" name="paymentInvoiceDetails[76][invoiceNo]" value="INV-0000000238">
                <input type="hidden" name="paymentInvoiceDetails[76][invoiceStatus]" value="sent">
                <input type="hidden" name="paymentInvoiceDetails[76][creditPeriod]" value="5010">
                <input type="hidden" name="paymentInvoiceDetails[76][invAmt]" value="3540.00">
                <input type="hidden" name="paymentInvoiceDetails[76][dueAmt]" value="3540.00">
                <td>INV-0000000238</td>
                <td><span class="text-uppercase status-danger">sent</span></td>
                <td>2037-05-16</td>
                <td class="invAmt invoiceAmt text-right" id="invoiceAmt_264">3540.00</td>
                <td class="dueAmt text-right" id="dueAmt_264">3540.00</td>
                <td>
                    <div class="input-group m-0">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1">₹</span>
                        </div>
                        <input type="text" name="paymentInvoiceDetails[76][recAmt]" class="form-control receiveAmt px-3 text-right" id="receiveAmt_264" placeholder="Amount">
                    </div>
                    <small style="display: none;" class="text-danger mt-n4 warningMsg" id="warningMsg_264">Amount Exceeded</small>
                </td>
                <td class="duePercentage" id="duePercentage_264">100%</td>
            </tr>
        </tbody>
    </table>
<?php
    // echo json_encode($listObj, true);
} else {
    header("Content-Type: application/json");
    http_response_code(400);
    echo json_encode([
        "status" => "warning",
        "message" => "Somthing went wrong, please try again!",
        "data" => []
    ]);
}
