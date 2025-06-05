<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../app/v1/functions/branch/func-bom-controller.php");
require_once("pagination/common-pagination.php");

require_once("../../common/exportexcel-new.php");

$headerData = array('Content-Type: application/json');



// print_r($_POST);
$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);
$taxableAmount = 0;
$countrycode = $_SESSION["logedBranchAdminInfo"]["companyCountry"];

if ($_POST['act'] == 'creditnote') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $limit_per_Page = isset($_POST['limit']) && $_POST['limit'] != '' ? $_POST['limit'] : 25;

        $page_no = isset($_POST['pageNo']) ? (int)$_POST['pageNo'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $formObj = $_POST['formDatas'];
        $cond = "";
        // $cond = "AND DATE(so_date) BETWEEN '" . $previousDate . "' AND '" . $currentDate . "'";


        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            global $decimalValue;
            if ($slag === 'postingDate') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif ($slag === "total") {
                $cleanedValue = str_replace(',', '', $data['value']);

                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');

                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            } else if ($slag === 'so.created_by' || $slag === 'created_by') {

                if (in_array($data['operatorName'], ['LIKE', 'NOT LIKE'])) {
                    $opr = ($data['operatorName'] === 'LIKE') ? 'LIKE' : 'NOT LIKE';
                    $resultList = getAdminUserIdByName($data['value']);
                    // $new_slag = 'varient.' . $slag;

                    if (strpos($resultList, ',') !== false) {
                        $resultList = (!empty($resultList)) ? $resultList : '0';
                        $opr = ($opr === 'LIKE') ? 'IN' : 'NOT IN';
                        $conds .= $slag . " $opr (" . $resultList . ")";
                    } else {
                        $resultList = (!empty($resultList)) ? $resultList : '0';
                        $conds .= $slag . " $opr '%" . $resultList . "%'";
                    }
                }
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sts = " AND stock.`status` !='deleted'";

        $sql_list = "SELECT * FROM `erp_credit_note` WHERE 1 " . $cond . "  AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=" . $company_id . "  ORDER BY cr_note_id desc ";

        $sql_Mainqry = $sql_list . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);

        $dynamic_data = [];
        $num_list = $sqlMainQryObj['numRows'];
        $sql_data = $sqlMainQryObj['data'];
        $output = "";
        $limitText = "";
        $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        if ($num_list > 0) {
            foreach ($sql_data as $data) {

                $bill_id = $data['creditNoteReference'];
                $creditor_type = $data['creditors_type'];
                if ($creditor_type == 'customer') {
                    $iv = queryGet("SELECT * FROM `erp_branch_sales_order_invoices` WHERE `so_invoice_id`=$bill_id");
                    // console($iv);
                    $ref = $iv['data']['invoice_no'];
                    $source_address_sql = queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_address_id`= '" . $data['source_address'] . "' ")['data'];
                    // console($source_address_sql);

                    // $source_address = $source_address_sql['customer_address_building_no'] . ',' . $source_address_sql['customer_address_flat_no'] . ',' . $source_address_sql['customer_address_street_name'] . ',' . $source_address_sql['customer_address_pin_code'] . ',' . $source_address_sql['customer_address_location'] . ',' . $source_address_sql['customer_address_city'] . ',' . $source_address_sql['customer_address_district'] . ',' . $source_address_sql['customer_address_country'] . ',' . $source_address_sql['customer_address_state'];
                    $source_address = getCompanyAddress($company_id, $branch_id, $location_id, $countryCode);

                    $destination_address_sql =  queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_id`='" . $data['party_id'] . "' AND `customer_address_state_code`= '" . $data['destination_address'] . "' ")['data'];

                    if (empty($destination_address_sql)) {
                        $destination_address_sql =  queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_id`='" . $data['party_id'] . "'")['data'];
                    }
                    $destination_address = $destination_address_sql['customer_address_building_no'] . ',' . $destination_address_sql['customer_address_flat_no'] . ',' . $destination_address_sql['customer_address_street_name'] . ',' . $destination_address_sql['customer_address_pin_code'] . ',' . $destination_address_sql['customer_address_location'] . ',' . $destination_address_sql['customer_address_city'] . ',' . $destination_address_sql['customer_address_district'] . ',' . $destination_address_sql['customer_address_country'] . ',' . $destination_address_sql['customer_address_state'];

                    $party_name = queryGet("SELECT * FROM `erp_customer` WHERE company_id=$company_id AND company_branch_id=$branch_id AND location_id=$location_id AND customer_id='" . $data['party_id'] . "' AND `customer_code`= '" . $data['party_code'] . "'");
                    //  echo $party_name['data']['trade_name'];
                } else {
                    $iv = queryGet("SELECT * FROM `erp_grninvoice` WHERE `grnIvId`=$bill_id");
                    $ref = $iv['data']['grnCode'];

                    $source_address_sql = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE  `vendor_id`='" . $data['party_id'] . "' AND  `state_code`= '" . $data['destination_address'] . "' ")['data'];
                    // console($source_address_sql);
                    if (empty($source_address_sql)) {
                        $source_address_sql = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE  `vendor_id`='" . $data['party_id'] . "'")['data'];
                        // console($source_address_sql);
                    }

                    $source_address = $source_address_sql['vendor_business_building_no'] . ',' . $source_address_sql['vendor_business_flat_no'] . ',' . $source_address_sql['vendor_business_street_name'] . ',' . $source_address_sql['vendor_business_pin_code'] . ',' . $source_address_sql['vendor_business_location'] . ',' . $source_address_sql['vendor_business_city'] . ',' . $source_address_sql['vendor_business_district'] . ',' . $source_address_sql['vendor_business_country'] . ',' . $source_address_sql['vendor_business_state'];

                    // $destination_address_sql =  queryGet("SELECT * FROM `erp_customer_address` WHERE `vendor_business_id`= '" . $data['destination_address'] . "' ")['data'];

                    $destination_address = getCompanyAddress($company_id, $branch_id, $location_id, $countryCode);

                    // $destination_address = $destination_address_sql['vendor_business_building_no'] . ',' . $destination_address_sql['vendor_business_flat_no'] . ',' . $destination_address_sql['vendor_business_street_name'] . ',' . $destination_address_sql['vendor_business_pin_code'] . ',' . $destination_address_sql['vendor_business_location'] . ',' . $destination_address_sql['vendor_business_city'] . ',' . $destination_address_sql['vendor_business_district'] . ',' . $destination_address_sql['vendor_business_country'] . ',' . $destination_address_sql['vendor_business_state'];
                    $party_name = queryGet("SELECT * FROM `erp_vendor_details` WHERE  company_id=$company_id AND company_branch_id=$branch_id AND location_id=$location_id AND vendor_id='" . $data['party_id'] . "' AND `vendor_code`= '" . $data['party_code'] . "'");
                    // console($party_name['data']['trade_name']);
                }

                // if ($data['status'] == "active") {
                //     $status = '<div class="status-bg status-open">Active</div>';
                // } elseif ($data['status'] == "reverse") {
                //     $status = '<div class="status-bg status-pending">Reversed</div>';
                // } elseif ($data['status'] == "reposted") {
                //     $status = '<div class="status-bg status-closed">Reposted</div>';
                // } elseif ($data['status'] == "inactive") {
                //     $status = '<div class="status-bg status-pending">Pending</div>';
                // }
                $taxComponents = json_decode($data['taxComponents'], true);
                if ($countrycode == 103) {
                    if ($data['igst'] > 0) {
                        $taxableAmount = $data['total'] - ($data['igst'] + $data['adjustment']);
                    } else {
                        $taxableAmount = $data['total'] - ($data['cgst'] + $data['sgst'] + $data['adjustment']);
                    }
                } else {
                    $taxtotal = 0;
                    foreach ($taxComponents as $tax) {
                        $taxtotal += $tax['taxAmount'];
                    }
                    $taxableAmount = $data['total'] - ($taxtotal + $data['adjustment']);
                }



                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "cr_note_id" => $data['cr_note_id'],
                    "credit_note_no" => $data['credit_note_no'],
                    "creditors_type" => $creditor_type,
                    "party_name" => $party_name['data']['trade_name'] . '(' . $data['party_code'] . ')',
                    "ref" => $ref,
                    "postingDate" => formatDateORDateTime($data['postingDate']),
                    "remark" => $data['remark'],
                    "source_address" => strip_tags($source_address),
                    "destination_address" => strip_tags($destination_address),
                    "total" => $data['total'],
                    "created_by" => getCreatedByUser($data['created_by']),
                    "created_at" => formatDateORDateTime($data['created_at']),
                    "updated_by" => getCreatedByUser($data['updated_by']),
                    "updated_at" => formatDateORDateTime($data['updated_at']),
                    "status" => $data['status'],
                    "goods_journal_id" => $data['goods_journal_id'],
                    "cn_status" => $data['status'],
                    "cgst" => $data['cgst'],
                    "sgst" => $data['sgst'],
                    "igst" => $data['igst'],
                    "taxableAmount" => $taxableAmount,
                    "adjust" => $data['adjustment'],
                    "taxComponents" => $data['taxComponents']
                ];
                $sl++;
            }
            $output .= "</table>";
            $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";
            $queryset = queryGet($sqlRowCount);
            $totalRows = $queryset['data']['row_count'];
            $total_page = ceil($totalRows / $limit_per_Page);
            $output .= pagiNation($page_no, $total_page);

            $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';
            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "pagination" => $output,
                "limitTxt" => $limitText,
                "sqlMain" => $sqlMainQryObj

            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sql_list
            ];
        }

        echo json_encode($res);
    }
}
if ($_POST['act'] == 'alldata') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $formObj = $_POST['formDatas'];
        $cond = "";
        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            global $decimalValue;
            if ($slag === 'posting_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif ($slag === "total") {
                $cleanedValue = str_replace(',', '', $data['value']);

                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');

                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            } else if ($slag === 'so.created_by' || $slag === 'created_by') {

                $resultList = getAdminUserIdByName($data['value']);
                $conds .= $slag . " IN  " . " (" . $resultList . ")";
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sts = " AND stock.`status` !='deleted'";

        $sql_list = "SELECT * FROM `erp_credit_note` WHERE 1 " . $cond . "  AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=" . $company_id . "  ORDER BY cr_note_id desc ";

        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];
        if ($num_list > 0) {
            $sl = 1;
            foreach ($sql_data_all as $data) {

                $bill_id = $data['creditNoteReference'];
                $creditor_type = $data['creditors_type'];
                if ($creditor_type == 'customer') {
                    $iv = queryGet("SELECT * FROM `erp_branch_sales_order_invoices` WHERE `so_invoice_id`=$bill_id");
                    // console($iv);
                    $ref = $iv['data']['invoice_no'];
                    $source_address_sql = queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_address_id`= '" . $data['source_address'] . "' ")['data'];
                    // console($source_address_sql);

                    // $source_address = $source_address_sql['customer_address_building_no'] . ',' . $source_address_sql['customer_address_flat_no'] . ',' . $source_address_sql['customer_address_street_name'] . ',' . $source_address_sql['customer_address_pin_code'] . ',' . $source_address_sql['customer_address_location'] . ',' . $source_address_sql['customer_address_city'] . ',' . $source_address_sql['customer_address_district'] . ',' . $source_address_sql['customer_address_country'] . ',' . $source_address_sql['customer_address_state'];
                    $source_address = getCompanyAddress($company_id, $branch_id, $location_id, $countryCode);

                    $destination_address_sql =  queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_id`='" . $data['party_id'] . "' AND `customer_address_state_code`= '" . $data['destination_address'] . "' ")['data'];

                    if (empty($destination_address_sql)) {
                        $destination_address_sql =  queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_id`='" . $data['party_id'] . "'")['data'];
                    }
                    $destination_address = $destination_address_sql['customer_address_building_no'] . ',' . $destination_address_sql['customer_address_flat_no'] . ',' . $destination_address_sql['customer_address_street_name'] . ',' . $destination_address_sql['customer_address_pin_code'] . ',' . $destination_address_sql['customer_address_location'] . ',' . $destination_address_sql['customer_address_city'] . ',' . $destination_address_sql['customer_address_district'] . ',' . $destination_address_sql['customer_address_country'] . ',' . $destination_address_sql['customer_address_state'];

                    $party_name = queryGet("SELECT * FROM `erp_customer` WHERE company_id=$company_id AND company_branch_id=$branch_id AND location_id=$location_id AND customer_id='" . $data['party_id'] . "' AND `customer_code`= '" . $data['party_code'] . "'");
                    //  echo $party_name['data']['trade_name'];
                } else {
                    $iv = queryGet("SELECT * FROM `erp_grninvoice` WHERE `grnIvId`=$bill_id");
                    $ref = $iv['data']['grnCode'];

                    $source_address_sql = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE  `vendor_id`='" . $data['party_id'] . "' AND  `state_code`= '" . $data['destination_address'] . "' ")['data'];
                    // console($source_address_sql);
                    if (empty($source_address_sql)) {
                        $source_address_sql = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE  `vendor_id`='" . $data['party_id'] . "'")['data'];
                        // console($source_address_sql);
                    }

                    $source_address = $source_address_sql['vendor_business_building_no'] . ',' . $source_address_sql['vendor_business_flat_no'] . ',' . $source_address_sql['vendor_business_street_name'] . ',' . $source_address_sql['vendor_business_pin_code'] . ',' . $source_address_sql['vendor_business_location'] . ',' . $source_address_sql['vendor_business_city'] . ',' . $source_address_sql['vendor_business_district'] . ',' . $source_address_sql['vendor_business_country'] . ',' . $source_address_sql['vendor_business_state'];

                    // $destination_address_sql =  queryGet("SELECT * FROM `erp_customer_address` WHERE `vendor_business_id`= '" . $data['destination_address'] . "' ")['data'];

                    $destination_address = getCompanyAddress($company_id, $branch_id, $location_id, $countryCode);

                    // $destination_address = $destination_address_sql['vendor_business_building_no'] . ',' . $destination_address_sql['vendor_business_flat_no'] . ',' . $destination_address_sql['vendor_business_street_name'] . ',' . $destination_address_sql['vendor_business_pin_code'] . ',' . $destination_address_sql['vendor_business_location'] . ',' . $destination_address_sql['vendor_business_city'] . ',' . $destination_address_sql['vendor_business_district'] . ',' . $destination_address_sql['vendor_business_country'] . ',' . $destination_address_sql['vendor_business_state'];
                    $party_name = queryGet("SELECT * FROM `erp_vendor_details` WHERE  company_id=$company_id AND company_branch_id=$branch_id AND location_id=$location_id AND vendor_id='" . $data['party_id'] . "' AND `vendor_code`= '" . $data['party_code'] . "'");
                    // console($party_name['data']['trade_name']);
                }

                // if ($data['status'] == "active") {
                //     $status = '<div class="status-bg status-open">Active</div>';
                // } elseif ($data['status'] == "reverse") {
                //     $status = '<div class="status-bg status-pending">Reversed</div>';
                // } elseif ($data['status'] == "reposted") {
                //     $status = '<div class="status-bg status-closed">Reposted</div>';
                // } elseif ($data['status'] == "inactive") {
                //     $status = '<div class="status-bg status-pending">Pending</div>';
                // }
                $taxComponents = json_decode($data['taxComponents'], true);
                if ($countrycode == 103) {
                    if ($data['igst'] > 0) {
                        $taxableAmount = $data['total'] - ($data['igst'] + $data['adjustment']);
                    } else {
                        $taxableAmount = $data['total'] - ($data['cgst'] + $data['sgst'] + $data['adjustment']);
                    }
                } else {
                    $taxtotal = 0;
                    foreach ($taxComponents as $tax) {
                        $taxtotal += $tax['taxAmount'];
                    }
                    $taxableAmount = $data['total'] - ($taxtotal + $data['adjustment']);
                }



                $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "cr_note_id" => $data['cr_note_id'],
                    "credit_note_no" => $data['credit_note_no'],
                    "creditors_type" => $creditor_type,
                    "party_name" => $party_name['data']['trade_name'] . '(' . $data['party_code'] . ')',
                    "ref" => $ref,
                    "postingDate" => formatDateORDateTime($data['postingDate']),
                    "remark" => $data['remark'],
                    "source_address" => strip_tags($source_address),
                    "destination_address" => strip_tags($destination_address),
                    "total" => $data['total'],
                    "created_by" => getCreatedByUser($data['created_by']),
                    "created_at" => formatDateORDateTime($data['created_at']),
                    "updated_by" => getCreatedByUser($data['updated_by']),
                    "updated_at" => formatDateORDateTime($data['updated_at']),
                    "status" => $data['status'],
                    "goods_journal_id" => $data['goods_journal_id'],
                    "cn_status" => $data['status'],
                    "cgst" => $data['cgst'],
                    "sgst" => $data['sgst'],
                    "igst" => $data['igst'],
                    "taxableAmount" => $taxableAmount,
                    "adjust" => $data['adjustment'],
                    "taxComponents" => $data['taxComponents']
                ];
                $sl++;
            }

            $dynamic_data_all = json_encode($dynamic_data_all);
            $exportToExcelAll = exportToExcelAll($dynamic_data_all, $_POST['coloum'], $_POST['sql_data_checkbox']);
            $res = [
                "status" => true,
                "msg" => "CSV all generated",
                'csvContentall' => $exportToExcelAll,
                "sql" => $sql_list,
            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sql_list
            ];
        }
        echo json_encode($res);
    }
}
