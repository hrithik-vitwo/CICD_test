<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("pagination/common-pagination.php");

// require_once("../../common/exportexcel.php");
require_once("../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');



// // print_r($_POST);
// $currentDate = date('Y-m-d');
// $timestampPreviousDay = strtotime($fromd . ' -1 day');
// $previousDate = date('Y-m-d', $timestampPreviousDay);
session_start();

if ($_POST['act'] == 'tdata') {
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
    $implodeFrom = implode('', array_map(function ($slag, $data) {
      $conds = "";
      global $decimalValue;
      if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'salesInvoice.invoice_date') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      } elseif (strcasecmp($data['value'], 'Goods') === 0) {
        $data['value'] = 'material';
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } elseif ($slag === "salesInvoice.all_total_amt" || $slag === "salesInvoice.total_tax_amt") {

        $cleanedValue = str_replace(',', '', $data['value']);


        // Single value case
        $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
        $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
      } else if ($slag === 'so.created_by' || $slag === 'created_by' || $slag === 'salesInvoice.created_by') {
        if ($data['value'] !== "auto") {
          if (in_array($data['operatorName'], ['LIKE', 'NOT LIKE'])) {
            $opr = ($data['operatorName'] === 'LIKE') ? 'LIKE' : 'NOT LIKE';
            $resultList = getAdminUserIdByName($data['value']);
            // $new_slag = 'varient.' . $slag;
  
            if (strpos($resultList, ',') !== false) {
                $opr = ($opr === 'LIKE') ? 'IN' : 'NOT IN';
                $conds .= $slag . " $opr (" . $resultList . ")";
            } else {
                $conds .= $slag . " $opr '%" . $resultList . "%'";
            }
        }
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
        $mailSt = $mailStMap[strtolower($data['value'])] ?? 0;
        $value = $mailSt > 0 ? $mailSt : $data['value'];
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $value . "%'";
      } else if ($slag === 'salesInvoice.totalItems' || $slag === 'salesInvoice.all_total_amt') {
        $conds .= $slag . " " . $data['operatorName'] . " " . $data['value'] . "";
      } else if ($slag == 'salesInvoice.status') {
        if ($data['operatorName'] == 'LIKE') {
          $type = '=';
        } else {
          $type = '!=';
        }
        $data['value'] = strtolower($data['value']);
        if ($data['value'] == 'reverse' || $data['value'] == 'reposted') {
          $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
        } else if ($data['value'] == 'pending') {
          $conds .= "salesInvoice.invoiceStatus " . $type . "14";
        } else if ($data['value'] == 'rejected') {
          if ($data['operatorName'] == 'LIKE') {
            $conds .= "salesInvoice.invoiceStatus = 17 AND salesInvoice.status!='reverse'";
          } else {
            $conds .= "salesInvoice.invoiceStatus != 17 OR salesInvoice.status IN ('reverse','reposted')";
          }
        } else {
          if ($data['operatorName'] == 'LIKE') {
            $conds .= "salesInvoice.invoiceStatus NOT IN (14,17) AND salesInvoice.status NOT IN ('reverse','reposted')";
          } else {
            $conds .= "salesInvoice.invoiceStatus IN (14,17) OR salesInvoice.status IN ('reverse','reposted')";
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

    $sts = " AND `status` !='deleted'";

    // $sql_list = "SELECT salesInvoice.*, eInv.ack_no, eInv.ack_date, eInv.irn, eInv.signed_qr_code,  cust.trade_name, cust.customer_picture FROM `erp_branch_sales_order_invoices` as salesInvoice  LEFT JOIN erp_customer as cust ON cust.customer_id=salesInvoice.customer_id LEFT JOIN erp_e_invoices as eInv ON salesInvoice.so_invoice_id = eInv.invoice_id WHERE salesInvoice.company_id = '" . $company_id . "' AND salesInvoice.branch_id = '" . $branch_id . "' AND salesInvoice.location_id = '" . $location_id . "' AND salesInvoice.`status` != 'deleted' ORDER BY salesInvoice.invoice_date";

    $sql_list = "SELECT salesInvoice.*, eInv.`ack_no`, eInv.`ack_date`, eInv.`irn`, eInv.`signed_qr_code`, cust.trade_name, cust.customer_picture FROM `erp_branch_sales_order_invoices` as salesInvoice LEFT JOIN `erp_e_invoices` as eInv ON salesInvoice.so_invoice_id = eInv.invoice_id LEFT JOIN erp_customer as cust ON cust.customer_id=salesInvoice.customer_id WHERE 1 $cond AND salesInvoice.company_id = '$company_id' AND salesInvoice.branch_id = '$branch_id' AND salesInvoice.location_id = '$location_id' AND salesInvoice.`status` != 'deleted' ORDER BY salesInvoice.invoice_date DESC , salesInvoice.so_invoice_id DESC , salesInvoice.invoice_no ASC";

    // $sql_Mainqry = $sql_list . "  ORDER BY so.so_id DESC LIMIT " . $offset . "," . $limit_per_Page . ";";
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
        $mailStatusDiv_1 = '';
        $mailStatus_1 = '';
        if ($data['mailStatus'] == 1) {
          $mailStatus = '<p>SENT</p> <div class="round">
                        <ion-icon name="checkmark-sharp"></ion-icon>
                      </div>';
          $mailStatus_1 = 'SENT';
        } elseif ($data['mailStatus'] == 2) {
          $mailStatus = '<span class="text-primary">VIEW</span> <div class="round text-primary">
                        <ion-icon name="checkmark-done-sharp"></ion-icon>
                      </div>';
          $mailStatus_1 = 'VIEW';
        } else if ($data['mailStatus'] == 0) {
          $mailStatus = '<p>NOT SENT</p>';
          $mailStatus_1 = 'NOT SENT';
        }

        if ($data['mailStatus'] == 0) {
          $mailStatusDiv = '<div>' . $mailStatus . '</div>';
          $mailStatusDiv_1 = $mailStatus_1;
        } else {
          $mailStatusDiv = '<div>' . $mailStatus . ' <p class="status-date">' . $data['updated_at'] . '</p></div>';
          $mailStatusDiv_1 = $mailStatus_1 . " " . $data['updated_at'];
        }

        //Due date 
        $dueDate = '';
        if ($data['status'] == 'reverse') {
          $dueDate = '--';
          $dueDate_download = '--';
        } else {
          if ($data['invoiceStatus'] != 4) {
            $dueDate = '<p class="' . $dueInDaysClass . ' text-xs text-center">' . $oneInvDueDays . '</p>';
            $dueDate_download = $oneInvDueDays;
          } else {
            $dueDate = '<p class="status-light text-xs text-center"><i class="fa fa-check-circle"></i> Received</p>';
            $dueDate_download = 'Received';
          }
        }
        $invStatus = '';

        if ($data['status'] === 'reverse') {
          $invStatus = 'Reverse';
        } else if ($data['status'] === 'reposted') {
          $invStatus = 'Reposted';
        } else {

          if ($data['invoiceStatus'] == 14) {
            $invStatus = 'Pending';
          } else if ($data['invoiceStatus'] == 17) {
            $invStatus = 'Rejected';
          } else {
            $invStatus = 'Approved';
          }
        }
        $total_discount = $data['totalDiscount'] + $data['totalCashDiscount'] ?? 0;

        $dynamic_data[] = [
          "sl_no" => $sl,
          "soInvoiceId" => $data['so_invoice_id'],
          "cust.trade_name" => $data['trade_name'],
          "salesInvoice.invoice_no" => $data['invoice_no'],
          "salesInvoice.totalItems" => decimalQuantityPreview($data['totalItems']),
          "salesInvoice.invoice_date" => formatDateWeb($data['invoice_date']),
          "duedate" => $dueDate,
          "salesInvoice.duedate" => $dueDate_download,
          "mailStatus" => $mailStatusDiv,
          "salesInvoice.mailStatus" => $mailStatusDiv_1,
          "salesInvoice.all_total_amt" => decimalValuePreview($data['all_total_amt']),
          "salesInvoice.total_tax_amt" => decimalValuePreview($data['total_tax_amt']),
          "taxable_amount" => decimalValuePreview($data['sub_total_amt'] - $total_discount),
          "invoiceStatus" => $data['invoiceStatus'],
          "salesInvoice.created_by" => $data['created_by'] != "auto" ? getCreatedByUser($data['created_by']) : "auto",
          "status" => $data['status'],
          "ackNo" => $data['ack_no'],
          "ackDate" => $data['ack_date'],
          "irn" => $data['irn'],
          "so_number" => $data['so_number'],
          "salesInvoice.status" => $invStatus
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

      // $csvContent = exportToExcelAll($sql_list, json_encode($columnMapping));
      // $csvContentBypagination = exportToExcelByPagin($sql_Mainqry, json_encode($columnMapping));

      $res = [
        "status" => true,
        "msg" => "Success",
        "data" => $dynamic_data,
        "pagination" => $output,
        "limitTxt" => $limitText,
        "limit_per_Page" => $limit_per_Page,
        // "csvContent" => $csvContent,
        // "csvContentBypagination" => $csvContentBypagination,
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
  }
}


if ($_POST['act'] == 'alldata') {
  $formObj = $_POST['formDatas'];
  $cond = "";
  $implodeFrom = implode('', array_map(function ($slag, $data) {
    $conds = "";
    global $decimalValue;

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

      $cleanedValue = str_replace(',', '', $data['value']);


      // Single value case
      $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
      $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
    } else if ($slag === 'so.created_by' || $slag === 'created_by' || $slag === 'salesInvoice.created_by') {
      if ($data['value'] !== "auto") {

        if (in_array($data['operatorName'], ['LIKE', 'NOT LIKE'])) {
          $opr = ($data['operatorName'] === 'LIKE') ? 'LIKE' : 'NOT LIKE';
          $resultList = getAdminUserIdByName($data['value']);
          // $new_slag = 'varient.' . $slag;

          if (strpos($resultList, ',') !== false) {
              $opr = ($opr === 'LIKE') ? 'IN' : 'NOT IN';
              $conds .= $slag . " $opr (" . $resultList . ")";
          } else {
              $conds .= $slag . " $opr '%" . $resultList . "%'";
          }
      }
      } else {
        $conds .= $slag . " LIKE '%" . $data['value'] . "%'";
      }
    } else if ($slag === 'salesInvoice.mailStatus') {
      $mailStMap = [
        "not sent" => 0,
        "sent" => 1,
        "view" => 2,
      ];
      $mailSt = $mailStMap[strtolower($data['value'])] ?? 0;
      $value = $mailSt > 0 ? $mailSt : $data['value'];
      $conds .= $slag . " " . $data['operatorName'] . " '%" . $value . "%'";
    } else if ($slag === 'salesInvoice.totalItems' || $slag === 'salesInvoice.all_total_amt') {
      $conds .= $slag . " " . $data['operatorName'] . " " . $data['value'] . "";
    } else if ($slag == 'salesInvoice.status') {
      $data['value'] = strtolower($data['value']);
      if ($data['value'] == 'reverse' || $data['value'] == 'reposted') {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } else if ($data['value'] == 'pending') {
        $conds .= "salesInvoice.invoiceStatus = 14";
      } else if ($data['value'] == 'rejected') {
        $conds .= "salesInvoice.invoiceStatus = 17 AND salesInvoice.status!='reverse'";
      } else {
        $conds .= "salesInvoice.invoiceStatus NOT IN (14,17) AND salesInvoice.status NOT IN ('reverse','reposted')";
      }
    } else {
      $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
    }

    return !empty($data['value']) ? " AND " . $conds : "";
  }, array_keys($formObj), $formObj));


  if (!empty($implodeFrom)) {
    $cond .= $implodeFrom;
  }

  $sts = " AND `status` !='deleted'";

  $sql_list = "SELECT salesInvoice.*, eInv.`ack_no`, eInv.`ack_date`, eInv.`irn`, eInv.`signed_qr_code`, cust.trade_name, cust.customer_picture FROM `erp_branch_sales_order_invoices` as salesInvoice LEFT JOIN `erp_e_invoices` as eInv ON salesInvoice.so_invoice_id = eInv.invoice_id LEFT JOIN erp_customer as cust ON cust.customer_id=salesInvoice.customer_id WHERE 1 $cond AND salesInvoice.company_id = '$company_id' AND salesInvoice.branch_id = '$branch_id' AND salesInvoice.location_id = '$location_id' AND salesInvoice.`status` != 'deleted' ORDER BY salesInvoice.invoice_date DESC , salesInvoice.so_invoice_id DESC , salesInvoice.invoice_no ASC";
  $dynamic_data_all = [];
  $sqlMainQryObjall = queryGet($sql_list, true);
  $sql_data_all = $sqlMainQryObjall['data'];
  $num_list =  $sqlMainQryObjall['numRows'];
  if ($num_list > 0) {
    foreach ($sql_data_all as $data) {

      $temDueDate = date_create($data["invoice_date"]);
      $dateInShow = date_add($temDueDate, date_interval_create_from_date_string($data["credit_period"] . " days"));
      $todayDate = new DateTime(date("Y-m-d"));
      $oneInvDueDays = $todayDate->diff(new DateTime(date_format($temDueDate, "Y-m-d")))->format("%r%a");
      $dueInDaysClass = ($oneInvDueDays >= 0) ? (($oneInvDueDays == 0) ? "status-info" : "status") : "status-danger";

      $oneInvDueDays = ($oneInvDueDays >= 0) ? (($oneInvDueDays >= 1) ? (($oneInvDueDays == 1) ? "Due in 1 day" : "Due in " . $oneInvDueDays . " days") : "Due Today") : (($oneInvDueDays == -1) ? "Overdue by 1 day" : "Overdue by " . abs($oneInvDueDays) . " days");
      $mailStatusDiv = '';
      $mailStatus = '';
      $mailStatusDiv_1 = '';
      $mailStatus_1 = '';
      if ($data['mailStatus'] == 1) {
        $mailStatus = '<p>SENT</p> <div class="round">
                        <ion-icon name="checkmark-sharp"></ion-icon>
                      </div>';
        $mailStatus_1 = 'SENT';
      } elseif ($data['mailStatus'] == 2) {
        $mailStatus = '<span class="text-primary">VIEW</span> <div class="round text-primary">
                        <ion-icon name="checkmark-done-sharp"></ion-icon>
                      </div>';
        $mailStatus_1 = 'VIEW';
      } else if ($data['mailStatus'] == 0) {
        $mailStatus = '<p>NOT SENT</p>';
        $mailStatus_1 = 'NOT SENT';
      }

      if ($data['mailStatus'] == 0) {
        $mailStatusDiv = '<div>' . $mailStatus . '</div>';
        $mailStatusDiv_1 = $mailStatus_1;
      } else {
        $mailStatusDiv = '<div>' . $mailStatus . ' <p class="status-date">' . $data['updated_at'] . '</p></div>';
        $mailStatusDiv_1 = $mailStatus_1 . " " . $data['updated_at'];
      }

      //Due date 
      $dueDate = '';
      if ($data['status'] == 'reverse') {
        $dueDate = '--';
        $dueDate_download = '--';
      } else {
        if ($data['invoiceStatus'] != 4) {
          $dueDate = '<p class="' . $dueInDaysClass . ' text-xs text-center">' . $oneInvDueDays . '</p>';
          $dueDate_download = $oneInvDueDays;
        } else {
          $dueDate = '<p class="status-light text-xs text-center"><i class="fa fa-check-circle"></i> Received</p>';
          $dueDate_download = 'Received';
        }
      }
      $invStatus = '';

      if ($data['status'] === 'reverse') {
        $invStatus = 'Reverse';
      } else if ($data['status'] === 'reposted') {
        $invStatus = 'Reposted';
      } else {

        if ($data['invoiceStatus'] == 14) {
          $invStatus = 'Pending';
        } else if ($data['invoiceStatus'] == 17) {
          $invStatus = 'Rejected';
        } else {
          $invStatus = 'Approved';
        }
      }

      $dynamic_data_all[] = [
        "sl_no" => $sl,
        "soInvoiceId" => $data['so_invoice_id'],
        "cust.trade_name" => $data['trade_name'],
        "salesInvoice.invoice_no" => $data['invoice_no'],
        "salesInvoice.totalItems" => decimalQuantityPreview($data['totalItems']),
        "salesInvoice.invoice_date" => formatDateWeb($data['invoice_date']),
        "duedate" => $dueDate,
        "salesInvoice.duedate" => $dueDate_download,
        "mailStatus" => $mailStatusDiv,
        "salesInvoice.mailStatus" => $mailStatusDiv_1,
        "salesInvoice.all_total_amt" => decimalValuePreview($data['all_total_amt']),
        "salesInvoice.total_tax_amt" => decimalValuePreview($data['total_tax_amt']),
        "taxable_amount" => decimalValuePreview($data['all_total_amt'] - $data['total_tax_amt'] - $total_discount),
        "invoiceStatus" => $data['invoiceStatus'],
        "salesInvoice.created_by" => $data['created_by'] != "auto" ? getCreatedByUser($data['created_by']) : "auto",
        "status" => $data['status'],
        "ackNo" => $data['ack_no'],
        "ackDate" => $data['ack_date'],
        "irn" => $data['irn'],
        "so_number" => $data['so_number'],
        "salesInvoice.status" => $invStatus
      ];
    }
    $dynamic_data_all = json_encode($dynamic_data_all);
    $exportToExcelAll = exportToExcelAll($dynamic_data_all, $_POST['coloum'], $_POST['sql_data_checkbox']);
    $res = [
      "status" => true,
      "msg" => "alldataSuccess",
      "all_data" => $dynamic_data_all,
      "sql" => $sql_list,
    ];
  } else {
    $res = [
      "status" => false,
      "msg" => "Error!",
      "sql" => $sql_list
    ];
  }

  echo json_encode([
    'status' => 'success',
    'message' => 'CSV allgenerated',
    'csvContentall' => $exportToExcelAll // Encoding CSV content to handle safely in JSON
  ]);
}
