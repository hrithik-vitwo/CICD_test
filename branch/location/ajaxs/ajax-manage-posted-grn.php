<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("pagination/common-pagination.php");

require_once("../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');

// // print_r($_POST);
// $currentDate = date('Y-m-d');
// $timestampPreviousDay = strtotime($fromd . ' -1 day');
// $previousDate = date('Y-m-d', $timestampPreviousDay);
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
    $implodeFrom = implode('', array_map(function ($slag, $data) {
      $conds = "";
      global $decimalValue;
  
      if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
          if ($data['operatorName'] === 'BETWEEN') {
              $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
          } else {
              $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
          }
      } elseif (strcasecmp($data['value'], 'Goods') === 0)  {
          $data['value'] = 'material';
          $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }elseif($slag==="grnTotalAmount"){
        $cleanedValue = str_replace(',', '', $data['value']);

        $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');

        $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
      } else if($slag === 'so.created_by' || $slag==='grnCreatedBy'){


        $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";

    } else {
          $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }
  
      return !empty($data['value']) ? " AND " . $conds : "";
  }, array_keys($formObj), $formObj));
  

    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }
    
    $sts = " AND `grnStatus` !='deleted'";

    $sql_list="SELECT * FROM `". ERP_GRN . "` WHERE `companyId`=$company_id  AND `branchId`=$branch_id  AND `locationId`=$location_id " .$cond. $sts . " ORDER BY grnId DESC";
    $sql_Mainqry = $sql_list." LIMIT ". $offset . "," . $limit_per_Page . ";";
    $sqlMainQryObj = queryGet($sql_Mainqry, true);

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
          "grnId"=>$data['grnId'],
          "vendorDocumentNo"=>$data['vendorDocumentNo'],
          "grnPoNumber"=>$data['grnPoNumber'],
          "vendorCode"=>$data['vendorCode'],
          "vendorName"=>$data['vendorName'],
          "vendorGstin"=>$data['vendorGstin'],
          "grnTotalAmount"=>decimalValuePreview($data['grnTotalAmount']),
          "grnType"=>$data['grnType'],
          "grnStatus"=>$data['grnStatus'],
          "grnCode"=>$data['grnCode'],
          "grnCreatedBy" => getCreatedByUser($data['grnCreatedBy'])
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
      
      // $csvContent=exportToExcelAll($sql_list,json_encode($columnMapping));
      // $csvContentBypagination=exportToExcelByPagin($sql_Mainqry,json_encode($columnMapping));

      $res = [
        "status" => true,
        "msg" => "Success",
        "data" => $dynamic_data,
        "pagination" => $output,
        "limitTxt" => $limitText,
        "limit_per_Page" => $limit_per_Page,
        "csvContent"=>$csvContent,
        "csvContentBypagination"=>$csvContentBypagination,
        "sql" => $sqlMainQryObj['sql']
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
    
        if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
            if ($data['operatorName'] === 'BETWEEN') {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
            } else {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
            }
        } elseif (strcasecmp($data['value'], 'Goods') === 0)  {
            $data['value'] = 'material';
            $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
        }elseif($slag==="grnTotalAmount"){
          $cleanedValue = str_replace(',', '', $data['value']);
  
          $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
  
          $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
        } else if($slag === 'so.created_by' || $slag==='created_by'){
  

          $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
  
      } else {
            $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
        }
    
        return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));
    
  
      if (!empty($implodeFrom)) {
        $cond .= $implodeFrom;
      }
      
      $sts = " AND `grnStatus` !='deleted'";
  
      $sql_list="SELECT * FROM `". ERP_GRN . "` WHERE `companyId`=$company_id  AND `branchId`=$branch_id  AND `locationId`=$location_id " .$cond. $sts . " ORDER BY grnId DESC";
      $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];
        if ($num_list > 0) {
          foreach ($sql_data_all as $data) {
            $dynamic_data_all[] = [
              "sl_no" => $sl,
              "grnId"=>$data['grnId'],
              "vendorDocumentNo"=>$data['vendorDocumentNo'],
              "grnPoNumber"=>$data['grnPoNumber'],
              "vendorCode"=>$data['vendorCode'],
              "vendorName"=>$data['vendorName'],
              "vendorGstin"=>$data['vendorGstin'],
              "grnTotalAmount"=>decimalValuePreview($data['grnTotalAmount']),
              "grnType"=>$data['grnType'],
              "grnStatus"=>$data['grnStatus'],
              "grnCode"=>$data['grnCode'],
              "grnCreatedBy" => getCreatedByUser($data['grnCreatedBy'])
            ];
            $sl++;
          }
          $dynamic_data_all=json_encode($dynamic_data_all);
          $exportToExcelAll =exportToExcelAll($dynamic_data_all,$_POST['coloum'],$_POST['sql_data_checkbox']);
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