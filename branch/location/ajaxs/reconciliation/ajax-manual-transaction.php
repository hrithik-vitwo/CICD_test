<?php
include_once("../../../../app/v1/connection-branch-admin.php");

$responseData = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    global $created_by;
    global $updated_by;
    $returnData = [];

    $id_array = $_POST['idarray'];
    $statement_id = $_POST['statement_id'];
    $type = $_POST['type'];

    $get_statement = queryGet('SELECT * FROM `erp_bank_statements` WHERE `id`=' . $statement_id);
    $statement_amount = $get_statement["data"]["remaining_amt"];
    $total_given_amt = 0;

    if ($type == "customer") {

        foreach ($id_array as $id) {
            $get_inv_data = queryGet('SELECT * FROM `erp_branch_sales_order_invoices` WHERE `so_invoice_id`=' . $id["id"]);

            $due_amount = $get_inv_data["data"]["due_amount"];
            $given_amount = $id["value"];
            $db_amt = $due_amount - $given_amount;

            if ($total_given_amt <= $statement_amount) {
                //Update due amount
                if ($given_amount < $due_amount) {
                    $status = 2;
                } else {
                    $status = 4;
                }
                $update_due_amt = queryUpdate('UPDATE `erp_branch_sales_order_invoices` SET  `due_amount`= "' . $db_amt . '", `invoiceStatus`="' . $status . '" ,`updated_by`="' . $updated_by . '" WHERE `so_invoice_id`=' . $id["id"]);
                $total_given_amt += $given_amount;
            } else {
                continue;
            }
        }
    } else {
        foreach ($id_array as $id) {
            $get_inv_data = queryGet('SELECT * FROM `erp_grninvoice` WHERE `grnIvId`=' . $id["id"]);

            $due_amount = $get_inv_data["data"]["dueAmt"];
            $given_amount = $id["value"];
            $db_amt = $due_amount - $given_amount;

            if ($total_given_amt <= $statement_amount) {
                //Update due amount
                if ($given_amount < $due_amount) {
                    $status = 2;
                } else {
                    $status = 4;
                }
                $update_due_amt = queryUpdate('UPDATE `erp_grninvoice` SET  `dueAmt`= "' . $db_amt . '", `paymentStatus`="' . $status . '" ,`grnUpdatedBy`="' . $updated_by . '" WHERE `grnIvId`=' . $id["id"]);
                $total_given_amt += $given_amount;
            } else {
                continue;
            }
        }
    }

    if ($total_given_amt == $statement_amount) {
        //update 0 and reconcilled
        // $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `withdrawal_amt`= "0.00", `reconciled_status`="reconciled" ,`updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);
        $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `remaining_amt`= "0.00", `reconciled_status`="reconciled" ,`updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);

        if ($update_ststement_amount["status"] == "success") {
            $returnData['status'] = "success";
            $returnData['message'] = "Reconciled Successfuly";
        } else {
            $returnData['status'] = "Warning";
            $returnData['message'] = "something went wrong 1";
        }
    } elseif ($total_given_amt < $statement_amount) {
        //minus and partially reconciled
        $amt = $statement_amount - $total_given_amt;

        // $update_ststement_amount = queryUpdate('UPDATE `erp_bank_statements` SET  `withdrawal_amt`= "' . $amt . '", `reconciled_status`="pending" ,`updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id);
        $sql = 'UPDATE `erp_bank_statements` SET  `remaining_amt`= "' . $amt . '", `reconciled_status`="pending" ,`updated_by`="' . $updated_by . '" WHERE `id`=' . $statement_id;
        $update_ststement_amount = queryUpdate($sql);

        if ($update_ststement_amount["status"] == "success") {
            $returnData['status'] = "success";
            $returnData['message'] = "Partial Reconciled Successfuly";
        } else {
            $returnData['status'] = "Warning";
            $returnData['message'] = "something went wrong 2";
            $returnData['query'] = $update_ststement_amount;
        }
    } else {
        // return
        $returnData['status'] = "warning";
        $returnData['message'] = "Invoice amount cannot be greater than statement amount";
        $returnData["data"] = [
            "statement_amount" => $statement_amount,
            "total_due_amt" => $total_due_amt,
            "id_array" => $id_array,
            "statement_id" => $statement_id,
            "type" => $type
        ];
    }
    echo json_encode($returnData, true);
}
