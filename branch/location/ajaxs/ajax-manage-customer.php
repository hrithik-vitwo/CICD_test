<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("pagination/common-pagination.php");
$headerData = array('Content-Type: application/json');

// print_r($_POST);
$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'tdata') {
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
            if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sts = " AND `customer_status` !='deleted'";

        $sql_list = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE 1 " . $cond . "  AND company_id='" . $company_id . "' " . $sts . "  ORDER BY customer_id desc";


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
                $cusIcon;
                if ($data['customer_picture'] != "") {
                    $cusIcon = $data['customer_picture'];
                } else {
                    $cusIcon = ucfirst(substr($data['trade_name'], 0, 1));
                }

                $customerId = $data['customer_id'];
                $ordervol = "SELECT * FROM erp_branch_sales_order_invoices WHERE `customer_id`=$customerId";
                $getvol = queryGet($ordervol);

                $ordercustomer = "SELECT SUM( IF( invoiceStatus = '4', all_total_amt, 0 ) ) AS sentInvoiceAmount FROM erp_branch_sales_order_invoices WHERE `customer_id`=$customerId";
                $getorder = queryGet($ordercustomer, true);

                $formObj = '<form action="/branch/location/manage-customers-p.php" method="POST">
                                <input type="hidden" name="id" value="' . $data['customer_id'] . '">
                                <input type="hidden" name="changeStatus" value="active_inactive">
                                <button ';

                        if ($data['customer_status'] == "draft") {
                            $formObj .= 'type="button" style="cursor: inherit; border:none"';
                        } else {
                            $formObj .= 'type="submit" onclick="return confirm(\'Are you sure to change customer status?\')"';
                        }

                        $formObj .= 'class="btn btn-sm" data-toggle="tooltip" data-placement="top" title="' . $data['customer_status'] . '">';

                        if ($data['customer_status'] == "active") {
                            $formObj .= '<div class="status-bg status-approved">' . ucfirst($data['customer_status']) . '</div>';
                        } elseif ($data['customer_status'] == "inactive") {
                            $formObj .= '<p class="status-bg status-closed">' . ucfirst($data['customer_status']) . '</p>';
                        } elseif ($data['customer_status'] == "draft") {
                            $formObj .= '<p class="status-bg status-pending">' . ucfirst($data['customer_status']) . '</p>';
                        }

                        $formObj .= '</button>
                            </form>';

                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "customerId" => $data['customer_id'],
                    "customer_code" => $data['customer_code'],
                    "cusIcon" => $cusIcon,
                    "cusName" => $data['trade_name'],
                    "constitution_of_business" => $data['constitution_of_business'],
                    "customer_gstin" => $data['customer_gstin'],
                    "customer_email" => $data['customer_authorised_person_email'],
                    "customer_phone" => $data['customer_authorised_person_phone'],
                    "orderVolume" => $getvol['numRows'],
                    "receipt_amt"=>$getorder['data'][0]['sentInvoiceAmount'],
                    "status"=>$formObj
                ];
                $sl++;
            }
            $output .= "</table>";
            $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";
            $queryset = queryGet($sqlRowCount);
            $totalRows = $queryset['data']['row_count'];
            $total_page = ceil($totalRows / $limit_per_Page);
            $output .= pagiNation($page_no, $total_page);

            // $output .= '<div class="active" id="pagination">';


            // if ($page_no > 1) {
            //     $output .= "<a id='" . ($page_no - 1) . "' href='?page=" . ($page_no - 1) . "'>Previous</a>";
            // }

            // for ($i = 1; $i <= $total_page; $i++) {
            //     if ($i <= 5 || $i >= $total_page - 1 || ($i >= $page_no - 2 && $i <= $page_no + 2)) {
            //         $output .= "<a id='{$i}' href='?page={$i}'>{$i}</a>";
            //     }
            // }


            // if ($page_no < $total_page) {
            //     $output .= "<a id='" . ($page_no + 1) . "' href='?page=" . ($page_no + 1) . "'>Next</a>";
            //     $output .= "<a id='" . $total_page . "' href='?page=" . ($page_no + 1) . "'>Last</a>";
            // }

            // $output .= '</div>';

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
