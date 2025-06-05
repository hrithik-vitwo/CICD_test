<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("pagination/common-pagination.php");

require_once("../../common/exportexcel.php");
$headerData = array('Content-Type: application/json');

$dbobj = new Database();
$BranchSoObj = new BranchSo();
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
    $type = $_POST['invoicetype'];
    $implodeFrom = implode('', array_map(function ($slag, $data) {
      $conds = "";
  
      if ($slag === 'postingDate' || $slag === 'created_at') {
          if ($data['operatorName'] === 'BETWEEN') {
              $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
          } else {
              $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
          }
      }elseif($slag==="collect_payment"){
        $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
      }
      elseif($slag==="status"){
        // if($data['value']==="Collected" || $data['value']==="collected"){
        // $conds .= $slag . " " . $data['operatorName'] . "active";
        // }else{
        //   $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
        // }

        if ($data['value'] === "Collected" || $data['value'] === "collected") {
          $conds .= $slag . " " . $data['operatorName'] . "'active'";
      } else {
          $conds .= $slag . " " . $data['operatorName'] . " '" . $data['value'] . "'";
      }      
      }
      else if($slag === 'so.created_by' || $slag==='created_by'){

        $resultList = getAdminUserIdByName($data['value']);                        
        $conds .= $slag . " IN  " . " (" . $resultList. ")";

    }      
       else {
          $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";

      }
  
      return !empty($data['value']) ? " AND " . $conds : "";
  }, array_keys($formObj), $formObj));
  

    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    //$sts = " AND sopayment.status !='deleted'";
    if ($type == 'active') {
      $sql_list = "SELECT sopayment.*,cust.customer_code , cust.trade_name as customer_name FROM `erp_branch_sales_order_payments` as sopayment  LEFT JOIN erp_customer as cust ON cust.customer_id=sopayment.customer_id WHERE 1 " . $cond . "  AND sopayment.company_id='" . $company_id . "'  AND sopayment.branch_id='" . $branch_id . "'   AND sopayment.location_id='" . $location_id . "' " . $sts . "AND (sopayment.journal_id=0 OR sopayment.journal_id IS NULL) AND sopayment.status ='active' ORDER BY sopayment.payment_id DESC ";
    } else {
      $sql_list = "SELECT sopayment.*,cust.customer_code , cust.trade_name as customer_name FROM `erp_branch_sales_order_payments` as sopayment  LEFT JOIN erp_customer as cust ON cust.customer_id=sopayment.customer_id WHERE 1 " . $cond . "  AND sopayment.company_id='" . $company_id . "'  AND sopayment.branch_id='" . $branch_id . "'   AND sopayment.location_id='" . $location_id . "' " . $sts . "AND (sopayment.journal_id!=0 AND sopayment.journal_id IS NOT NULL) AND (sopayment.reverse_journal_id=0 OR sopayment.reverse_journal_id IS NULL) AND  `created_at`>'2025-05-20' AND sopayment.status ='reverse'  ORDER BY sopayment.payment_id DESC ";
    }
   
   
    $sql_Mainqry = $sql_list." LIMIT ". $offset . "," . $limit_per_Page . ";";
    $sqlMainQryObj = $dbobj->queryGet($sql_Mainqry, true);

    $dynamic_data = [];
    $num_list = $sqlMainQryObj['numRows'];
    $sql_data = $sqlMainQryObj['data'];
    $output = "";
    $limitText = "";
    $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

    if ($num_list > 0) {
      foreach ($sql_data as $data) {
        $dynamic_data[] = [
          "sl_no" => $sl,
          "paymentId" => $data['payment_id'],
          "posting_date" => $data['postingDate'],
          "transactionId"=>$data['transactionId'],
          "paymentCollectType"=>$data['paymentCollectType'],
          "collect_payment"=>$data['collect_payment'],
          "created_at"=>$data['created_at'],
          "customer_code"=>$data['customer_code'],
          "customer_name"=>$data['customer_name'],
          "created_by" => getCreatedByUser($data['created_by']),
          "status"=>$data['status']
        ];
        $sl++;
      }
      $output .= "</table>";
      $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";
      $queryset =$dbobj->queryGet($sqlRowCount);
      $totalRows = $queryset['data']['row_count'];
      $total_page = ceil($totalRows / $limit_per_Page);

      $output .= pagiNation($page_no, $total_page);

      $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';
      
      $csvContent=exportToExcelAll($sql_list,json_encode($columnMapping));
      $csvContentBypagination=exportToExcelByPagin($sql_Mainqry,json_encode($columnMapping));

      $res = [
        "status" => true,
        "msg" => "Success",
        "data" => $dynamic_data,
        "pagination" => $output,
        "limitTxt" => $limitText,
        "limit_per_Page" => $limit_per_Page,
        "csvContent"=>$csvContent,
        "csvContentBypagination"=>$csvContentBypagination,
        "sqlMainQryObj"=>$sqlMainQryObj,
        "type"=>$type

      ];
    } else {
      $res = [
        "status" => false,
        "msg" => "Error!",
        "sql" => $sqlMainQryObj
      ];
    }

    echo json_encode($res);
  }
}
