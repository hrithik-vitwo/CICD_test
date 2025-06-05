<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("pagination/common-pagination.php");
require_once("../../common/exportexcel-new.php");
// require_once("../../common/exportexcel.php");
$headerData = array('Content-Type: application/json');

// // print_r($_POST);
// $currentDate = date('Y-m-d');
// $timestampPreviousDay = strtotime($fromd . ' -1 day');
// $previousDate = date('Y-m-d', $timestampPreviousDay);
// file to be upload from local to

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
  
      if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
          if ($data['operatorName'] === 'BETWEEN') {
              $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
          } else {
              $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
          }
      } elseif (strcasecmp($data['value'], 'Goods') === 0)  {
          $data['value'] = 'material';
          $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }elseif($slag==="totalAmount"){
        $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
      } 
      else if($slag === 'so.created_by' || $slag==='created_by')
      {

        $resultList = getAdminUserIdByName($data['value']);
        if (empty($resultList)) {
          $resultList = 0;
        }
        $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
        $conds .= $slag . " " . $operator . " (" . $resultList . ")";

    } 
      else if($slag === 'vendor_status')
      {

        if ($data['operatorName'] === 'LIKE' || $data['operatorName'] === 'NOT LIKE') {
          $opr = $data['operatorName'] === 'LIKE' ? 'IN' : 'NOT IN';
          $conds .= $slag . " $opr  " . " (" ."'" . $data['value'] ."'" . ")";
      }

    } else if($slag === 'vend.isMailValid'){
      if($data['value']=='Verified'){
          $conds .= $slag . " " . $data['operatorName'] . " 'yes'";
      }else if($data['value']=='Not Verified'){
          $conds .= $slag . " " . $data['operatorName'] . " 'no'";
      }
  }
    else {
          $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }
  
      return !empty($data['value']) ? " AND " . $conds : "";
  }, array_keys($formObj), $formObj));
  

    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    $sts = " AND `vendor_status` !='deleted'";
    $sql_list = "SELECT vend.vendor_id, vend.vendor_code, vend.trade_name, vend.legal_name, vend.vendor_pan, vend.vendor_gstin, vend.constitution_of_business, vend.vendor_opening_balance, vend.vendor_visible_to_all, vend.vendor_website, vend.vendor_credit_period, vend.vendor_picture, vend.vendor_status, vend.vendor_authorised_person_name, vend.vendor_authorised_person_email, vend.vendor_authorised_person_phone, vend.vendor_authorised_alt_phone, vend.vendor_authorised_person_designation, vend.isMailValid, address.vendor_business_id, address.vendor_business_primary_flag, address.vendor_business_gstin, address.vendor_business_legal_name, address.vendor_business_trade_name, address.vendor_business_constitution, address.vendor_business_building_no, address.vendor_business_flat_no, address.vendor_business_street_name, address.vendor_business_pin_code, address.vendor_business_location, address.vendor_business_city, address.vendor_business_district, address.vendor_business_country, address.state_code, address.vendor_business_status, address.vendor_business_created_at, address.vendor_business_created_by, address.vendor_business_updated_at, address.vendor_business_updated_by, address.vendor_business_active_flag, currType.currency_name FROM erp_vendor_details AS vend LEFT JOIN erp_currency_type AS currType ON currType.currency_id = vend.vendor_currency LEFT JOIN erp_vendor_bussiness_places AS address ON vend.vendor_id = address.vendor_id AND address.vendor_business_primary_flag = 1 WHERE 1 $cond AND vend.company_id = $company_id AND vend.company_branch_id = $branch_id AND vend.location_id = $location_id AND vend.vendor_status != 'deleted' ORDER BY vend.vendor_id DESC";

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
        $isMailStatus = ($data['isMailValid'] === 'yes') ? 'Verified' : 'Not Verified';
        $dynamic_data[] = [
            "sl_no" => $sl,
            "vendorId"=>$data['vendor_id'],
            "vendor_code" => $data['vendor_code'],
            "trade_name" => $data['trade_name'],
            "legal_name" => $data['legal_name'],
            "vendor_pan" => $data['vendor_pan'],
            "vendor_gstin" => $data['vendor_gstin'],
            "constitution_of_business" => $data['constitution_of_business'],
            "vendor_opening_balance" => $data['vendor_opening_balance'],
            "currency_name" => $data['currency_name'],
            "vendor_visible_to_all" => $data['vendor_visible_to_all'],
            "vendor_website" => $data['vendor_website'],
            "vendor_credit_period" => $data['vendor_credit_period'],
            "vendor_authorised_person_name" => $data['vendor_authorised_person_name'],
            "vendor_authorised_person_email" => $data['vendor_authorised_person_email'],
            "vendor_authorised_person_phone" => $data['vendor_authorised_person_phone'],
            "vendor_authorised_alt_phone" => $data['vendor_authorised_alt_phone'],
            "vendor_authorised_person_designation" => $data['vendor_authorised_person_designation'],
            "vendor_business_primary_flag" => $data['vendor_business_primary_flag'],
            "vendor_business_building_no" => $data['vendor_business_building_no'],
            "vendor_business_flat_no" => $data['vendor_business_flat_no'],
            "vendor_business_street_name" => $data['vendor_business_street_name'],
            "vendor_business_pin_code" => $data['vendor_business_pin_code'],
            "vendor_business_location" => $data['vendor_business_location'],
            "vendor_business_city" => $data['vendor_business_city'],
            "vendor_status"=>$data['vendor_status'],
            "vend.isMailValid" => $isMailStatus

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
        // "csvContent"=>$csvContent,
        // "csvContentBypagination"=>$csvContentBypagination,
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
  $fromDate=$_POST['fromDate'];
  $toDate=$_POST['toDate'];
  $formObj = $_POST['formDatas'];
  $cond = "";
  $implodeFrom = implode('', array_map(function ($slag, $data) {
    $conds = "";

    if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
        if ($data['operatorName'] === 'BETWEEN') {
            $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
            $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
    } elseif (strcasecmp($data['value'], 'Goods') === 0)  {
        $data['value'] = 'material';
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
    }elseif($slag==="totalAmount"){
      $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
    } 
    else if($slag === 'so.created_by' || $slag==='created_by')
    {

      $resultList = getAdminUserIdByName($data['value']);
        if (empty($resultList)) {
          $resultList = 0;
        }
        $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
        $conds .= $slag . " " . $operator . " (" . $resultList . ")";

  } 
    else if($slag === 'vendor_status')
    {

      if ($data['operatorName'] === 'LIKE' || $data['operatorName'] === 'NOT LIKE') {
        $opr = $data['operatorName'] === 'LIKE' ? 'IN' : 'NOT IN';
        $conds .= $slag . " $opr  " . " (" ."'" . $data['value'] ."'" . ")";
    }

  } else if($slag === 'isMailValid'){
    if($data['value']=='Verified'){
        $conds .= $slag . " " . $data['operatorName'] . " 'yes'";
    }else if($data['value']=='Not Verified'){
        $conds .= $slag . " " . $data['operatorName'] . " 'no'";
    }
}
  else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
    }

    return !empty($data['value']) ? " AND " . $conds : "";
}, array_keys($formObj), $formObj));


  if (!empty($implodeFrom)) {
    $cond .= $implodeFrom;
  }

  $sts = " AND `vendor_status` !='deleted'";
  $sql_list = "SELECT vend.vendor_id, vend.vendor_code, vend.trade_name, vend.legal_name, vend.vendor_pan, vend.vendor_gstin, vend.constitution_of_business, vend.vendor_opening_balance, vend.vendor_visible_to_all, vend.vendor_website, vend.vendor_credit_period, vend.vendor_picture, vend.vendor_status, vend.vendor_authorised_person_name, vend.vendor_authorised_person_email, vend.vendor_authorised_person_phone, vend.vendor_authorised_alt_phone, vend.vendor_authorised_person_designation, vend.isMailValid, address.vendor_business_id, address.vendor_business_primary_flag, address.vendor_business_gstin, address.vendor_business_legal_name, address.vendor_business_trade_name, address.vendor_business_constitution, address.vendor_business_building_no, address.vendor_business_flat_no, address.vendor_business_street_name, address.vendor_business_pin_code, address.vendor_business_location, address.vendor_business_city, address.vendor_business_district, address.vendor_business_country, address.state_code, address.vendor_business_status, address.vendor_business_created_at, address.vendor_business_created_by, address.vendor_business_updated_at, address.vendor_business_updated_by, address.vendor_business_active_flag, currType.currency_name FROM erp_vendor_details AS vend LEFT JOIN erp_currency_type AS currType ON currType.currency_id = vend.vendor_currency LEFT JOIN erp_vendor_bussiness_places AS address ON vend.vendor_id = address.vendor_id AND address.vendor_business_primary_flag = 1 WHERE 1 $cond AND vend.company_id = $company_id AND vend.company_branch_id = $branch_id AND vend.location_id = $location_id AND vend.vendor_status != 'deleted' ORDER BY vend.vendor_id DESC";

   $dynamic_data_all = [];
   $sqlMainQryObjall = queryGet($sql_list, true);
   $sql_data_all = $sqlMainQryObjall['data'];
   $num_list =  $sqlMainQryObjall['numRows'];
   if ($num_list > 0) {
   foreach ($sql_data_all as $data) {
    $isMailStatus = ($data['isMailValid'] === 'yes') ? 'Verified' : 'Not Verified';
    $dynamic_data_all[]= [
            "sl_no" => $sl,
            "vendorId"=>$data['vendor_id'],
            "vendor_code" => $data['vendor_code'],
            "trade_name" => $data['trade_name'],
            "legal_name" => $data['legal_name'],
            "vendor_pan" => $data['vendor_pan'],
            "vendor_gstin" => $data['vendor_gstin'],
            "constitution_of_business" => $data['constitution_of_business'],
            "vendor_opening_balance" => $data['vendor_opening_balance'],
            "currency_name" => $data['currency_name'],
            "vendor_visible_to_all" => $data['vendor_visible_to_all'],
            "vendor_website" => $data['vendor_website'],
            "vendor_credit_period" => $data['vendor_credit_period'],
            "vendor_authorised_person_name" => $data['vendor_authorised_person_name'],
            "vendor_authorised_person_email" => $data['vendor_authorised_person_email'],
            "vendor_authorised_person_phone" => $data['vendor_authorised_person_phone'],
            "vendor_authorised_alt_phone" => $data['vendor_authorised_alt_phone'],
            "vendor_authorised_person_designation" => $data['vendor_authorised_person_designation'],
            "vendor_business_primary_flag" => $data['vendor_business_primary_flag'],
            "vendor_business_building_no" => $data['vendor_business_building_no'],
            "vendor_business_flat_no" => $data['vendor_business_flat_no'],
            "vendor_business_street_name" => $data['vendor_business_street_name'],
            "vendor_business_pin_code" => $data['vendor_business_pin_code'],
            "vendor_business_location" => $data['vendor_business_location'],
            "vendor_business_city" => $data['vendor_business_city'],
            "vendor_status"=>$data['vendor_status'],
            "vend.isMailValid" => $isMailStatus
    ];
  }
  $dynamic_data_all=json_encode($dynamic_data_all);
  $exportToExcelAll =exportToExcelAll($dynamic_data_all,$_POST['coloum'],$_POST['sql_data_checkbox']);
  $res = [
    "status" => true,
    "msg" => "alldataSuccess",
    "all_data"=>$dynamic_data_all,
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
