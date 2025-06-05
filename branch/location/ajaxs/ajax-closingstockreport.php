<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("pagination/common-pagination.php");
require_once("../../common/exportexcel-new.php");

// require_once("../../common/exportexcel.php");
$headerData = array('Content-Type: application/json');

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
    $havingCond = "";

    // Modify the loop to separate HAVING conditions
    $implodeFrom = implode('', array_map(function ($slag, $data) use (&$havingCond) {
      global $decimalQuantity;
      global $decimalValue;
      // Skip condition if value is empty
      if (empty($data['value'])) {
        return "";
      }

      $conds = "";
      if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ") " . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ") " . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      } elseif (strcasecmp($data['value'], 'Goods') === 0) {
        $data['value'] = 'material';
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } 
      
      
      else if ($slag === "totalAmount" || $slag === "summary.movingWeightedPrice") {

        $cleanedValue = str_replace(',', '', $data['value']);

        $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
    
        $conds .= "TRUNCATE($slag, $decimalValue) {$data['operatorName']} $roundedValue";
    }
    
    
     else if ($slag === 'so.created_by' || $slag === 'created_by') {
        $resultList = getAdminUserIdByName($data['value']);
        $conds .= $slag . " IN (" . $resultList . ")";
      } 
      
      
      else if ($slag === 'opening_quantity' || $slag === 'in_quantity' || $slag === 'out_quantity' || $slag === 'closing_quantity') {
        // Remove all thousand separators (,) safely while keeping decimals (.)
        $cleanedValue = str_replace(',', '', $data['value']);
    
        if (strpos($cleanedValue, ',') !== false) {
            // Handle multiple values
            $values = explode(',', $cleanedValue);
            $roundedValues = array_map(function ($val) use ($decimalQuantity) {
                return round((float)trim($val), $decimalQuantity);
            }, $values);
    
            $havingCond .= (!empty($havingCond) ? " AND " : " HAVING ") . "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") IN (" . implode(',', $roundedValues) . ")";
        } else {
            // Single value case
            $roundedValue = round((float)$cleanedValue, $decimalQuantity);
            $havingCond .= (!empty($havingCond) ? " AND " : " HAVING ") . "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $roundedValue;
        }
        return ""; // Exclude from WHERE conditions
    } 
    
    else if ($slag === 'opening_value' || $slag === 'in_value' || $slag === 'out_value' || $slag === 'closing_value') {
        // Remove all thousand separators (,) safely while keeping decimals (.)
        $cleanedValue = str_replace(',', '', $data['value']);
    
        if (strpos($cleanedValue, ',') !== false) {
            // Handle multiple values
            $values = explode(',', $cleanedValue);
            $roundedValues = array_map(function ($val) use ($decimalValue) {
                return round((float)trim($val), $decimalValue);
            }, $values);
    
            $havingCond .= (!empty($havingCond) ? " AND " : " HAVING ") . "TRUNCATE(" . $slag . ", " . $decimalValue . ") IN (" . implode(',', $roundedValues) . ")";
        } else {
            // Single value case
            $roundedValue = round((float)$cleanedValue, $decimalValue);
            $havingCond .= (!empty($havingCond) ? " AND " : " HAVING ") . "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
        }
        return ""; // Exclude from WHERE conditions
    }
    
     else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return " AND " . $conds;
    }, array_keys($formObj), $formObj));


    // Append WHERE conditions if present
    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    $fromDate=$_POST['fromDate'];
    $toDate=$_POST['toDate'];

    // old query
    // $sql_list="SELECT item.itemCode, item.itemName, groups.goodGroupName AS itemGroup, TRUNCATE( COALESCE(opening.opening_qty, 0), 2 ) AS opening_quantity, TRUNCATE(COALESCE(inqty.in_qty, 0), 2) AS in_quantity, TRUNCATE(COALESCE(outqty.out_qty, 0), 2) AS out_quantity, TRUNCATE( COALESCE(opening.opening_qty, 0) + COALESCE(inqty.in_qty, 0) + COALESCE(outqty.out_qty, 0), 2 ) AS closing_quantity, UOM.uomName AS uom, TRUNCATE( COALESCE(opening.opening_qty, 0) * summary.movingWeightedPrice, 2 ) AS opening_balance, TRUNCATE( COALESCE(inqty.in_qty, 0) * summary.movingWeightedPrice, 2 ) AS in_balance, TRUNCATE( COALESCE(outqty.out_qty, 0) * summary.movingWeightedPrice, 2 ) AS out_balance, TRUNCATE( ( COALESCE(opening.opening_qty, 0) + COALESCE(inqty.in_qty, 0) + COALESCE(outqty.out_qty, 0) ) * summary.movingWeightedPrice, 2 ) AS closing_balance FROM erp_inventory_items AS item LEFT JOIN( SELECT item_id, SUM(last_closing_qty) AS opening_qty FROM ( SELECT esl.item_id, esl.storage_id, esl.total_closing_qty AS last_closing_qty FROM erp_inventory_stocks_log_report AS esl JOIN( SELECT item_id, storage_id, MAX(report_date) AS max_report_date FROM erp_inventory_stocks_log_report WHERE report_date < '".$fromDate."' AND company_id = '".$company_id."' AND branch_id = '".$branch_id."' AND location_id = '".$location_id."' GROUP BY item_id, storage_id ) AS max_dates ON esl.item_id = max_dates.item_id AND esl.storage_id = max_dates.storage_id AND esl.report_date = max_dates.max_report_date ) AS last_closing_per_item_storage GROUP BY item_id ORDER BY item_id ) AS opening ON opening.item_id = item.itemId LEFT JOIN( SELECT itemId, SUM(itemQty) AS in_qty FROM erp_inventory_stocks_log WHERE refActivityName IN( 'GRN', 'REV-GRN', 'MIGRATION', 'MAT-MAT-IN', 'PROD-IN', 'PROD','REV-PROD-OUT','CONSUMPTION(BOOK-PHYSICAL)' ) AND itemQty > 0 AND postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND companyId = '".$company_id."' AND branchId = '".$branch_id."' AND locationId = '".$location_id."' GROUP BY itemId ) AS inqty ON inqty.itemId = item.itemId LEFT JOIN( SELECT itemId, SUM(itemQty) AS out_qty FROM erp_inventory_stocks_log WHERE refActivityName IN( 'INVOICE', 'REV-INVOICE', 'CONSUMPTION(PROD-ORDR)', 'MAT-MAT-OUT', 'PROD-OUT', 'DN', 'CONSUMPTION(COST-CENTER)', 'PROD' ) AND itemQty < 0 AND postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND companyId = '".$company_id."' AND branchId = '".$branch_id."' AND locationId = '".$location_id."' GROUP BY itemId ) AS outqty ON outqty.itemId = item.itemId LEFT JOIN erp_inventory_stocks_summary AS summary ON summary.itemId = item.itemId LEFT JOIN erp_inventory_mstr_good_groups AS groups ON item.goodsGroup = groups.goodGroupId LEFT JOIN erp_inventory_mstr_uom AS UOM ON UOM.uomId = item.baseUnitMeasure WHERE 1 " . $cond . " AND summary.company_id = $company_id AND summary.branch_id = $branch_id AND summary.location_id = $location_id";

    // $sql_list ="SELECT item.itemCode, item.itemName, SUM(CASE WHEN log.postingDate < '".$fromDate."' THEN log.itemQty ELSE 0 END) AS opening_quantity, SUM(CASE WHEN log.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND log.itemQty > 0 THEN log.itemQty ELSE 0 END) AS in_quantity, SUM(CASE WHEN log.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND log.itemQty < 0 THEN ABS(log.itemQty) ELSE 0 END) AS out_quantity, (SUM(CASE WHEN log.postingDate < '".$fromDate."' THEN itemQty ELSE 0 END) + SUM(CASE WHEN log.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND log.itemQty > 0 THEN log.itemQty ELSE 0 END) - SUM(CASE WHEN log.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND log.itemQty < 0 THEN ABS(log.itemQty) ELSE 0 END) ) AS closing_quantity FROM erp_inventory_stocks_log AS log LEFT JOIN erp_inventory_items AS item ON log.itemId = item.itemId WHERE 1 $cond AND log.companyId='".$company_id."' AND log.branchId='".$branch_id."' AND log.locationId='".$location_id."' AND item.company_id= '".$company_id."' AND item.location_id = '".$location_id."' GROUP BY log.itemId ORDER BY log.itemId";

    // $sql_list="SELECT item.itemCode, item.itemName, type.goodTypeName AS itemType, groups.goodGroupName AS itemGroup, SUM( CASE WHEN LOG.postingDate < '".$fromDate."' THEN LOG.itemQty ELSE 0 END ) AS opening_quantity, SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty > 0 THEN LOG.itemQty ELSE 0 END ) AS in_quantity, SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty < 0 THEN ABS(LOG.itemQty) ELSE 0 END ) AS out_quantity, ( SUM( CASE WHEN LOG.postingDate < '".$fromDate."' THEN itemQty ELSE 0 END ) + SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty > 0 THEN LOG.itemQty ELSE 0 END ) - SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty < 0 THEN ABS(LOG.itemQty) ELSE 0 END ) ) AS closing_quantity, summary.movingWeightedPrice as movPrice, UOM.uomName AS uom, SUM( CASE WHEN LOG.postingDate < '".$fromDate."' THEN LOG.itemQty ELSE 0 END ) * summary.movingWeightedPrice AS opening_value, SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty > 0 THEN LOG.itemQty ELSE 0 END ) * summary.movingWeightedPrice AS in_value, SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty < 0 THEN ABS(LOG.itemQty) ELSE 0 END ) * summary.movingWeightedPrice AS out_value, ( SUM( CASE WHEN LOG.postingDate < '".$fromDate."' THEN itemQty ELSE 0 END ) + SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty > 0 THEN LOG.itemQty ELSE 0 END ) - SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty < 0 THEN ABS(LOG.itemQty) ELSE 0 END ) ) * summary.movingWeightedPrice AS closing_value FROM erp_inventory_stocks_log AS LOG LEFT JOIN erp_inventory_items AS item ON LOG.itemId = item.itemId LEFT JOIN erp_inventory_mstr_good_types AS type ON item.goodsType = type.goodTypeId LEFT JOIN erp_inventory_mstr_good_groups AS groups ON item.goodsGroup = groups.goodGroupId LEFT JOIN erp_inventory_stocks_summary AS summary ON summary.itemId = item.itemId LEFT JOIN erp_inventory_mstr_uom AS UOM ON item.baseUnitMeasure = UOM.uomId WHERE 1 " . $cond . " AND LOG.companyId = '".$company_id."' AND LOG.branchId = '".$branch_id."' AND LOG.locationId = '".$location_id."' AND item.company_id = '".$company_id."' AND item.location_id = '".$location_id."' GROUP BY LOG.itemId ORDER BY LOG.itemId";

    $sql_list = "SELECT item.itemCode, item.itemName, type.goodTypeName AS itemType, groups.goodGroupName AS itemGroup, 
    SUM( CASE WHEN LOG.postingDate < '".$fromDate."' THEN LOG.itemQty ELSE 0 END ) AS opening_quantity, 
    SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty > 0 THEN LOG.itemQty ELSE 0 END ) AS in_quantity, 
    SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty < 0 THEN ABS(LOG.itemQty) ELSE 0 END ) AS out_quantity, 
    ( SUM( CASE WHEN LOG.postingDate < '".$fromDate."' THEN itemQty ELSE 0 END ) + 
      SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty > 0 THEN LOG.itemQty ELSE 0 END ) - 
      SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty < 0 THEN ABS(LOG.itemQty) ELSE 0 END ) ) AS closing_quantity, 
    summary.movingWeightedPrice as movPrice, UOM.uomName AS uom, 
    SUM( CASE WHEN LOG.postingDate < '".$fromDate."' THEN LOG.itemQty ELSE 0 END ) * summary.movingWeightedPrice AS opening_value, 
    SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty > 0 THEN LOG.itemQty ELSE 0 END ) * summary.movingWeightedPrice AS in_value, 
    SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty < 0 THEN ABS(LOG.itemQty) ELSE 0 END ) * summary.movingWeightedPrice AS out_value, 
    ( SUM( CASE WHEN LOG.postingDate < '".$fromDate."' THEN itemQty ELSE 0 END ) + 
      SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty > 0 THEN LOG.itemQty ELSE 0 END ) - 
      SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty < 0 THEN ABS(LOG.itemQty) ELSE 0 END ) ) * summary.movingWeightedPrice AS closing_value 
    FROM erp_inventory_stocks_log AS LOG 
    LEFT JOIN erp_inventory_items AS item ON LOG.itemId = item.itemId 
    LEFT JOIN erp_inventory_mstr_good_types AS type ON item.goodsType = type.goodTypeId 
    LEFT JOIN erp_inventory_mstr_good_groups AS groups ON item.goodsGroup = groups.goodGroupId 
    LEFT JOIN erp_inventory_stocks_summary AS summary ON summary.itemId = item.itemId 
    LEFT JOIN erp_inventory_mstr_uom AS UOM ON item.baseUnitMeasure = UOM.uomId 
    WHERE 1 " . $cond . " 
    AND LOG.companyId = '".$company_id."' 
    AND LOG.branchId = '".$branch_id."' 
    AND LOG.locationId = '".$location_id."' 
    AND item.company_id = '".$company_id."' 
    AND item.location_id = '".$location_id."' 
    GROUP BY LOG.itemId " . $havingCond . " 
    ORDER BY LOG.itemId";



    $sql_Mainqry = $sql_list . " LIMIT " . $offset . "," . $limit_per_Page . ";";
    $sqlMainQryObj = queryGet($sql_Mainqry, true);

     $dynamic_data = [];
    $num_list = $sqlMainQryObj['numRows'];
    $sql_data = $sqlMainQryObj['data'];
    $output = "";
    $limitText = "";
    $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

   // $dynamic_data_all = [];   
    // $sqlMainQryObjall = queryGet($sql_list, true);
    // $sql_data_all = $sqlMainQryObjall['data'];

    if ($num_list > 0) {
      foreach ($sql_data as $data) {

        $dynamic_data[] = [
          "sl_no" => $sl,
          "item.itemCode" => $data['itemCode'],
          "item.itemName" => $data['itemName'],
          "opening_quantity" => decimalQuantityPreview($data['opening_quantity']),
          "in_quantity" => decimalQuantityPreview($data['in_quantity']),
          "out_quantity" => decimalQuantityPreview($data['out_quantity']),
          "closing_quantity" => decimalQuantityPreview($data['closing_quantity']),
          "UOM.uomName" => $data['uom']??'-',
          "summary.movingWeightedPrice" => decimalValuePreview($data['movPrice']),
          "opening_value" =>  decimalValuePreview($data['opening_value']),
          "in_value" =>  decimalValuePreview($data['in_value']),
          "out_value" =>  decimalValuePreview($data['out_value']),
          "closing_value" =>  decimalValuePreview($data['closing_value']),
          "curr" =>  getSingleCurrencyType($company_currency)??'-',
          "type.goodTypeName"=>$data['itemType'],
          "groups.goodGroupName"=>$data['itemGroup']
        ];
        $sl++;
      }

      // foreach ($sql_data_all as $data) {
      //   $dynamic_data_all[]= [
      //     "itemCode" => $data['itemCode'],
      //     "itemName" => $data['itemName'],
      //     "openingQuantity" => decimalQuantity($data['opening_quantity']),
      //     "inQuantity" => decimalQuantity($data['in_quantity']),
      //     "outQuantity" => decimalQuantity($data['out_quantity']),
      //     "closingQuantity" => decimalQuantity($data['closing_quantity']),
      //     "uom" => $data['uom']??'-',
      //     "movPrice" => decimalValue($data['movPrice']),
      //     "openingBalance" =>  decimalValue($data['opening_value']),
      //     "inBalance" =>  decimalValue($data['in_value']),
      //     "outBalance" =>  decimalValue($data['out_value']),
      //     "closingBalance" =>  decimalValue($data['closing_value']),
      //     "curr" =>  getSingleCurrencyType($company_currency)??'-',
      //   ];
      // } 

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
       // "all_data"=>$dynamic_data_all,
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
    $havingCond = "";

    // Modify the loop to separate HAVING conditions
    $implodeFrom = implode('', array_map(function ($slag, $data) use (&$havingCond) {
      global $decimalQuantity;
      global $decimalValue;
      // Skip condition if value is empty
      if (empty($data['value'])) {
        return "";
      }

      $conds = "";
      if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ") " . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ") " . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      } elseif (strcasecmp($data['value'], 'Goods') === 0) {
        $data['value'] = 'material';
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } 
      
      
      else if ($slag === "totalAmount" || $slag === "summary.movingWeightedPrice") {
      // Remove all thousand separators (,) safely while keeping decimal points (.)
      $cleanedValue = str_replace(',', '', $data['value']);


      $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
      $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
        
    }
    
    
     else if ($slag === 'so.created_by' || $slag === 'created_by') {
        $resultList = getAdminUserIdByName($data['value']);
        $conds .= $slag . " IN (" . $resultList . ")";
      } 
      
      
      else if ($slag === 'opening_quantity' || $slag === 'in_quantity' || $slag === 'out_quantity' || $slag === 'closing_quantity') {
        // Remove all thousand separators (,) safely while keeping decimals (.)
        $cleanedValue = str_replace(',', '', $data['value']);
    
        if (strpos($cleanedValue, ',') !== false) {
            // Handle multiple values
            $values = explode(',', $cleanedValue);
            $roundedValues = array_map(function ($val) use ($decimalQuantity) {
                return round((float)trim($val), $decimalQuantity);
            }, $values);
    
            $havingCond .= (!empty($havingCond) ? " AND " : " HAVING ") . "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") IN (" . implode(',', $roundedValues) . ")";
        } else {
            // Single value case
            $roundedValue = round((float)$cleanedValue, $decimalQuantity);
            $havingCond .= (!empty($havingCond) ? " AND " : " HAVING ") . "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $roundedValue;
        }
        return ""; // Exclude from WHERE conditions
    } 
    
    else if ($slag === 'opening_value' || $slag === 'in_value' || $slag === 'out_value' || $slag === 'closing_value') {
        // Remove all thousand separators (,) safely while keeping decimals (.)
        $cleanedValue = str_replace(',', '', $data['value']);
    
        if (strpos($cleanedValue, ',') !== false) {
            // Handle multiple values
            $values = explode(',', $cleanedValue);
            $roundedValues = array_map(function ($val) use ($decimalValue) {
                return round((float)trim($val), $decimalValue);
            }, $values);
    
            $havingCond .= (!empty($havingCond) ? " AND " : " HAVING ") . "TRUNCATE(" . $slag . ", " . $decimalValue . ") IN (" . implode(',', $roundedValues) . ")";
        } else {
            // Single value case
            $roundedValue = round((float)$cleanedValue, $decimalValue);
            $havingCond .= (!empty($havingCond) ? " AND " : " HAVING ") . "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
        }
        return ""; // Exclude from WHERE conditions
    }
    
     else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return " AND " . $conds;
    }, array_keys($formObj), $formObj));


    // Append WHERE conditions if present
    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }


    $sql_list = "SELECT item.itemCode, item.itemName, type.goodTypeName AS itemType, groups.goodGroupName AS itemGroup, 
    SUM( CASE WHEN LOG.postingDate < '".$fromDate."' THEN LOG.itemQty ELSE 0 END ) AS opening_quantity, 
    SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty > 0 THEN LOG.itemQty ELSE 0 END ) AS in_quantity, 
    SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty < 0 THEN ABS(LOG.itemQty) ELSE 0 END ) AS out_quantity, 
    ( SUM( CASE WHEN LOG.postingDate < '".$fromDate."' THEN itemQty ELSE 0 END ) + 
      SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty > 0 THEN LOG.itemQty ELSE 0 END ) - 
      SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty < 0 THEN ABS(LOG.itemQty) ELSE 0 END ) ) AS closing_quantity, 
    summary.movingWeightedPrice as movPrice, UOM.uomName AS uom, 
    SUM( CASE WHEN LOG.postingDate < '".$fromDate."' THEN LOG.itemQty ELSE 0 END ) * summary.movingWeightedPrice AS opening_value, 
    SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty > 0 THEN LOG.itemQty ELSE 0 END ) * summary.movingWeightedPrice AS in_value, 
    SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty < 0 THEN ABS(LOG.itemQty) ELSE 0 END ) * summary.movingWeightedPrice AS out_value, 
    ( SUM( CASE WHEN LOG.postingDate < '".$fromDate."' THEN itemQty ELSE 0 END ) + 
      SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty > 0 THEN LOG.itemQty ELSE 0 END ) - 
      SUM( CASE WHEN LOG.postingDate BETWEEN '".$fromDate."' AND '".$toDate."' AND LOG.itemQty < 0 THEN ABS(LOG.itemQty) ELSE 0 END ) ) * summary.movingWeightedPrice AS closing_value 
    FROM erp_inventory_stocks_log AS LOG 
    LEFT JOIN erp_inventory_items AS item ON LOG.itemId = item.itemId 
    LEFT JOIN erp_inventory_mstr_good_types AS type ON item.goodsType = type.goodTypeId 
    LEFT JOIN erp_inventory_mstr_good_groups AS groups ON item.goodsGroup = groups.goodGroupId 
    LEFT JOIN erp_inventory_stocks_summary AS summary ON summary.itemId = item.itemId 
    LEFT JOIN erp_inventory_mstr_uom AS UOM ON item.baseUnitMeasure = UOM.uomId 
    WHERE 1 " . $cond . " 
    AND LOG.companyId = '".$company_id."' 
    AND LOG.branchId = '".$branch_id."' 
    AND LOG.locationId = '".$location_id."' 
    AND item.company_id = '".$company_id."' 
    AND item.location_id = '".$location_id."' 
    GROUP BY LOG.itemId " . $havingCond . " 
    ORDER BY LOG.itemId";



  $dynamic_data_all = [];
   $sqlMainQryObjall = queryGet($sql_list, true);
   $sql_data_all = $sqlMainQryObjall['data'];
   $num_list =  $sqlMainQryObjall['numRows'];
   if ($num_list > 0) {
   foreach ($sql_data_all as $data) {
      $dynamic_data_all[] = [
        "sl_no" => $sl,
        "item.itemCode" => $data['itemCode'],
        "item.itemName" => $data['itemName'],
        "opening_quantity" => decimalQuantityPreview($data['opening_quantity']),
        "in_quantity" => decimalQuantityPreview($data['in_quantity']),
        "out_quantity" => decimalQuantityPreview($data['out_quantity']),
        "closing_quantity" => decimalQuantityPreview($data['closing_quantity']),
        "UOM.uomName" => $data['uom'] ?? '-',
        "summary.movingWeightedPrice" => decimalValuePreview($data['movPrice']),
        "opening_value" =>  decimalValuePreview($data['opening_value']),
        "in_value" =>  decimalValuePreview($data['in_value']),
        "out_value" =>  decimalValuePreview($data['out_value']),
        "closing_value" =>  decimalValuePreview($data['closing_value']),
        "curr" =>  getSingleCurrencyType($company_currency) ?? '-',
        "type.goodTypeName" => $data['itemType'],
        "groups.goodGroupName" => $data['itemGroup']
      ];
  }
  $dynamic_data_all=json_encode($dynamic_data_all);
  $exportToExcelAll =exportToExcelAll($dynamic_data_all,$_POST['coloum'],$_POST['sql_data_checkbox']);
    $res = [
      'status' => 'success',
      'message' => 'CSV allgenerated',
      'csvContentall' => $exportToExcelAll,
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
