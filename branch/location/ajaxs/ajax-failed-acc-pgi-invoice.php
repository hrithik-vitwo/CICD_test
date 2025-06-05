<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("pagination/common-pagination.php");

require_once("../../common/exportexcel.php");
$headerData = array('Content-Type: application/json');



// // print_r($_POST);
// $currentDate = date('Y-m-d');
// $timestampPreviousDay = strtotime($fromd . ' -1 day');
// $previousDate = date('Y-m-d', $timestampPreviousDay);
session_start();

if ($_POST['act'] == 'failedpgi') {
  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $_SESSION['columnMapping'] = $_POST['columnMapping'];
    if (isset($_SESSION['columnMapping'])) {
      $columnMapping = $_SESSION['columnMapping'];
    }

    $limit_per_Page = isset($_POST['limit']) && $_POST['limit'] != '' ? $_POST['limit'] : 25;

    $page_no = isset($_POST['pageNo']) ? (int)$_POST['pageNo'] : 1;
    $page_no = max(1, $page_no);

    $offset = ($page_no - 1) * $limit_per_Page;
    $maxPagesl = $page_no * $limit_per_Page;
    $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
    $formObj = $_POST['formDatas'];
    $cond = "";
    $type = $_POST['invoicetype'];
    $implodeFrom = implode('', array_map(function ($slag, $data) {
      $conds = "";

      if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'salesInvoice.invoice_date') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      } elseif (strcasecmp($data['value'], 'Goods') === 0) {
        $data['value'] = 'material';
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } elseif ($slag === "all_total_amt") {
        $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
      } else if ($slag === 'so.created_by' || $slag === 'created_by' || $slag === 'salesInvoice.created_by') {
        if ($data['value'] !== "auto") {
          $resultList = getAdminUserIdByName($data['value']);
          $conds .= $slag . " IN  " . " (" . $resultList . ")";
        } else {
          $conds .= $slag . " LIKE '%" . $data['value'] . "%'";
        }
      } else if ($slag === 'salesInvoice.mailStatus') {
        $mailStMap = [
          "not sent" => 0,
          "not" => 0,
          "sent" => 1,
          "view" => 2,
        ];
        $mailSt = $mailStMap[$data['value']] ?? 0;
        $value = $mailSt > 0 ? $mailSt : $data['value'];
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $value . "%'";
      } else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));


    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    $sts = " AND `status` !='deleted'";

    if ($type == 'active') {
      $sql_list = "SELECT so_inv.invoice_no, so_inv.invoice_date, so_inv.all_total_amt, so_inv.so_invoice_id, so_inv.status, so_inv.created_by, so_inv.totalItems, cust.trade_name, cust.customer_code FROM `erp_branch_sales_order_invoices` AS so_inv LEFT JOIN `erp_e_invoices` ON so_inv.so_invoice_id = `erp_e_invoices`.invoice_id LEFT JOIN `erp_customer` AS cust ON so_inv.customer_id = cust.customer_id WHERE 1 $cond AND (so_inv.pgi_journal_id = 0 OR so_inv.pgi_journal_id IS NULL) AND `type` NOT IN ('pgi_to_invoice', 'service') AND so_inv.company_id = '$company_id' AND so_inv.branch_id = '$branch_id' AND so_inv.location_id = '$location_id' AND so_inv.`status` IN ('active') AND EXISTS (SELECT 1 FROM erp_branch_sales_order_invoice_items AS inv_items INNER JOIN erp_inventory_items AS inv ON inv.itemId = inv_items.inventory_item_id WHERE inv_items.so_invoice_id = so_inv.so_invoice_id AND inv.goodsType IN (3,4)) ORDER BY so_inv.invoice_date DESC, so_inv.so_invoice_id DESC";
    } else {
      $sql_list = "SELECT so_inv.invoice_no, so_inv.invoice_date, so_inv.all_total_amt, so_inv.so_invoice_id, so_inv.status, so_inv.created_by, so_inv.totalItems, cust.trade_name, cust.customer_code FROM `erp_branch_sales_order_invoices` AS so_inv LEFT JOIN `erp_e_invoices` ON so_inv.so_invoice_id = `erp_e_invoices`.invoice_id LEFT JOIN `erp_customer` AS cust ON so_inv.customer_id = cust.customer_id WHERE 1 $cond AND (so_inv.pgi_journal_id != 0 AND so_inv.pgi_journal_id IS NOT NULL) AND (so_inv.rev_pgi_journal_id = 0 OR so_inv.rev_pgi_journal_id IS NULL) AND `type` NOT IN ('pgi_to_invoice', 'service') AND so_inv.company_id = '$company_id' AND so_inv.branch_id = '$branch_id' AND so_inv.location_id = '$location_id' AND so_inv.`status` IN ('reverse')  AND EXISTS (SELECT 1 FROM erp_branch_sales_order_invoice_items AS inv_items INNER JOIN erp_inventory_items AS inv ON inv.itemId = inv_items.inventory_item_id WHERE inv_items.so_invoice_id = so_inv.so_invoice_id AND inv.goodsType IN (3,4))AND  so_inv.created_at > '2025-05-01' ORDER BY so_inv.invoice_date DESC, so_inv.so_invoice_id DESC";
    }




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
        $customerPic = $data['customer_picture'];
        $customerName = $data['trade_name'];
        $customerInitial = mb_substr($customerName, 0, 1);
        $customerLink = '<a class="soModal" href="#" data-id="' . $data['so_invoice_id'] . '">' . $customerName . '</a>';
        $customerPicture = '';

        if ($customerPic != '') {
          $customerPicture = '<img src="' . BASE_URL . 'public/storage/avatar/' . $customerPic . '" class="img-fluid avatar rounded-circle" alt="Customer Picture">';
        } else {
          $customerPicture = '<div class="img-fluid avatar rounded-circle d-flex justify-content-center align-items-center" style="border: 1px solid grey;">' . $customerInitial . '</div>';
        }

        $temDueDate = date_create($data["invoice_date"]);
        $dateInShow = date_add($temDueDate, date_interval_create_from_date_string($data["credit_period"] . " days"));
        $todayDate = new DateTime(date("Y-m-d"));
        $oneInvDueDays = $todayDate->diff(new DateTime(date_format($temDueDate, "Y-m-d")))->format("%r%a");
        $dueInDaysClass = ($oneInvDueDays >= 0) ? (($oneInvDueDays == 0) ? "status-info" : "status") : "status-danger";

        $oneInvDueDays = ($oneInvDueDays >= 0) ? (($oneInvDueDays >= 1) ? (($oneInvDueDays == 1) ? "Due in 1 day" : "Due in " . $oneInvDueDays . " days") : "Due Today") : (($oneInvDueDays == -1) ? "Overdue by 1 day" : "Overdue by " . abs($oneInvDueDays) . " days");

        // Invoice Status
        $mailStatusDiv = '';
        $mailStatus = '';

        if ($data['mailStatus'] == 1) {
          $mailStatus = '<p>SENT</p> <div class="round">
                        <ion-icon name="checkmark-sharp"></ion-icon>
                      </div>';
        } elseif ($data['mailStatus'] == 2) {
          $mailStatus = '<span class="text-primary">VIEW</span> <div class="round text-primary">
                        <ion-icon name="checkmark-done-sharp"></ion-icon>
                      </div>';
        } else if ($data['mailStatus'] == 0) {
          $mailStatus = '<p>NOT SENT</p>';
        }

        if ($data['mailStatus'] == 0) {
          $mailStatusDiv = '<div>' . $mailStatus . '</div>';
        } else {
          $mailStatusDiv = '<div>' . $mailStatus . ' <p class="status-date">' . $data['updated_at'] . '</p></div>';
        }

        //Due date 
        $dueDate = '';
        if ($data['status'] == 'reverse') {
          $dueDate = '--';
        } else {
          if ($data['invoiceStatus'] != 4) {
            $dueDate = '<p class="' . $dueInDaysClass . ' text-xs text-center">' . $oneInvDueDays . '</p>';
          } else {
            $dueDate = '<p class="status-light text-xs text-center"><i class="fa fa-check-circle"></i> Received</p>';
          }
        }

        $dynamic_data[] = [
          "sl_no" => $sl,
          "soInvoiceId" => $data['so_invoice_id'],
          "soInvoiceIdaction" => base64_encode($data['so_invoice_id']),
          "tradeName" => $data['trade_name'],
          "cust.customer_code" => $data['customer_code'],
          "invNo" => $data['invoice_no'],
          "totalItems" => $data['totalItems'],
          "invDate" => $data['invoice_date'],
          "delivery_no" => $data['delivery_no'],
          "dueDate" => $dueDate,
          "mailStatus" => $mailStatusDiv,
          "totalAmt" => $data['all_total_amt'],
          "invoiceStatus" => $data['status'],
          "createdBy" => $data['created_by'] != "auto" ? getCreatedByUser($data['created_by']) : "auto",
          "status" => $data['status'],
          "so_number" => $data['so_number']
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

      $csvContent = exportToExcelAll($sql_list, json_encode($columnMapping));
      $csvContentBypagination = exportToExcelByPagin($sql_Mainqry, json_encode($columnMapping));

      $res = [
        "status" => true,
        "msg" => "Success",
        "data" => $dynamic_data,
        "pagination" => $output,
        "limitTxt" => $limitText,
        "limit_per_Page" => $limit_per_Page,
        "csvContent" => $csvContent,
        "csvContentBypagination" => $csvContentBypagination,
        "sql" => $sql_list,
        "type" =>$type
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
