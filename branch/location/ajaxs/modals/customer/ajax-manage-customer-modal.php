<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-customers-controller.php");
$headerData = array('Content-Type: application/json');
$dbObj = new Database();
$customerDetailsObj = new CustomersController();

if ($_SERVER["REQUEST_METHOD"]=="GET") {
    $custId = $_GET['custId'];
    if ($_GET['act'] == "modalData") {
        $sts = " AND `customer_status` !='deleted'";
        $cond = "AND customer_id=$custId";
        $sql_list = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE 1 " . $cond . "  AND company_id='" . $company_id . "' AND company_branch_id='" . $branch_id . "'  AND location_id='" . $location_id . "'   " . $sts . "  ";
        $sqlObject = $dbObj->queryGet($sql_list);
        $num_list = $sqlObject['numRows'];
        if ($num_list > 0) {
            $data = $sqlObject['data'];
            $dynamic_data = [];
            $dynamic_data = [
                "dataObj" => $data,
                "created_by" => getCreatedByUser($data['customer_created_by']),
                "created_at" => formatDateORDateTime($data['customer_created_at']),
                "updated_by" => getCreatedByUser($data['customer_updated_by']),
                "updated_at" => formatDateORDateTime($data['customer_updated_at']),
                "companyCurrency" => getSingleCurrencyType($company_currency),
                "customerCurrency" => getSingleCurrencyType($data['customer_currency']),
            ];
            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "sql" => $sql_list
            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sql_list
            ];
        }
        echo json_encode($res);
    } else if ($_GET['act'] == "custTransInv") {
        $sql_list = "SELECT * FROM `erp_branch_sales_order_invoices` as salesInv WHERE salesInv.customer_id = $custId AND salesInv.company_id = $company_id AND salesInv.branch_id = $branch_id AND salesInv.location_id = $location_id ORDER BY so_invoice_id DESC";
        $sqlObject = $dbObj->queryGet($sql_list, true);
        if ($sqlObject['numRows'] > 0) {
            foreach ($sqlObject['data'] as $invoice) {
                $temDueDate = date_create($invoice["invoice_date"]);
                $todayDate = new DateTime(date("Y-m-d"));
                $oneInvDueDays = $todayDate->diff(new DateTime(date_format($temDueDate, "Y-m-d")))->format("%r%a");
                $customerDtls = $customerDetailsObj->getDataCustomerDetails($invoice['customer_id'])['data'][0];
                $customerPic = $customerDtls['customer_picture'];
                $customerName = $customerDtls['trade_name'];
                $customerPicture = '';
                $customer_name = mb_substr($customerName, 0, 1);
                ($customerPic != '') ? ($customerPicture = '<img src="' . BASE_URL . 'public/storage/avatar/' . $customerPic . '" class="img-fluid avatar rounded-circle" alt="">') : ($customerPicture = '<div class="img-fluid avatar rounded-circle d-flex justify-content-center align-items-center" style="border: 1px solid grey;">' . $customer_name . '</div>');

                        ?>
                                        <tr>
                                            <td>
                                                <p class="company-name mt-1"> <?= $customerPicture ?> </p>
                                            </td>
                                            <td>
                                                <?= $invoice['invoice_no'] ?>
                                            </td>
                                            <td>
                                                <?= $invoice['all_total_amt'] ?>
                                            </td>
                                            <td>
                                                <?= formatDateORDateTime($invoice['invoice_date']) ?>
                                            </td>
                                            <td>
                                                <?= $oneInvDueDays ?>
                                            </td>
                                            <td>
                                                <div class="status-custom w-75 text-secondary">
                                                    <?php if ($invoice['mailStatus'] == 1) {
                                                        echo 'SENT <div class="round">
                                                                                                        <ion-icon name="checkmark-sharp"></ion-icon>
                                                                                                        </div>';
                                                    } elseif ($invoice['mailStatus'] == 2) {
                                                        echo '<span class="text-primary">VIEW</span> <div class="round text-primary">
                                                                                                        <ion-icon name="checkmark-done-sharp"></ion-icon>
                                                                                                        </div>';
                                                    } ?>
                                                </div>
                                                <p class="status-date"><?= formatDateORDateTime($invoice['updated_at']) ?></p>
                                            </td>
                                        </tr>
                        <?php
            }
        }else{
            ?>
            <tr>
                <td colspan="6"><p class="text-center">No Invoice Found</p></td>
            </tr>
            <?php
        }
    } else if ($_GET['act'] == "custTransCollection") {
        $sql_list = "SELECT * FROM `erp_branch_sales_order_payments_log` AS LOG LEFT JOIN `erp_branch_sales_order_payments` AS payment ON LOG.payment_id = payment.payment_id WHERE LOG.`customer_id` = $custId AND LOG.company_id=$company_id AND LOG.branch_id=$branch_id AND LOG.location_id=$location_id";
        $sqlObject = $dbObj->queryGet($sql_list, true);
        $res = [];
        if ($sqlObject['numRows'] > 0) {
            $res = ["status" => "success", "message" => "Data found", "data" => $sqlObject['data']];
        } else {
            $res = ["status" => "warning", "message" => "Data not found", "sql" => $sql_list];
        }
        echo json_encode($res);
    } else if ($_GET['act'] == "custTransEstimate") {
        $sql_list = "SELECT * FROM `erp_branch_quotations` as quot WHERE quot.customer_id=$custId AND quot.company_id=$company_id AND quot.branch_id=$branch_id AND quot.location_id=$location_id ORDER by quotation_id DESC";
        $sqlObject=$dbObj->queryGet($sql_list,true);
        $res = [];
        if ($sqlObject['numRows'] > 0) {
            $res = ["status" => "success", "message" => "Data found", "data" => $sqlObject['data']];
        } else {
            $res = ["status" => "warning", "message" => "Data not found", "sql" => $sql_list];
        }
        echo json_encode($res);
    } else if ($_GET['act'] == "custTransSo") {
        $sql_list = "SELECT * FROM `erp_branch_sales_order` as so WHERE so.customer_id=$custId AND so.company_id=$company_id AND so.branch_id=$branch_id AND so.location_id=$location_id ORDER BY so.so_id DESC";
        $sqlObject=$dbObj->queryGet($sql_list,true);
        $res = [];
        if ($sqlObject['numRows'] > 0) {
            $res = ["status" => "success", "message" => "Data found", "data" => $sqlObject['data']];
        } else {
            $res = ["status" => "warning", "message" => "Data not found", "sql" => $sql_list];
        }
        echo json_encode($res);
    } else if ($_GET['act'] == "custTransJournal") {
        $code=$_GET['code'];
        $sql_list = "SELECT * FROM `erp_acc_journal` as journal WHERE journal.party_code=$code AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.parent_slug='journal' ";
        $sqlObject=$dbObj->queryGet($sql_list,true);
        $res = [];
        if ($sqlObject['numRows'] > 0) {
            $res = ["status" => "success", "message" => "Data found", "data" => $sqlObject['data']];
        } else {
            $res = ["status" => "warning", "message" => "Data not found", "sql" => $sql_list];
        }
        echo json_encode($res);
    }
}
