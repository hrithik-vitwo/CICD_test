<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../app/v1/functions/branch/func-bom-controller.php");
require_once("pagination/common-pagination.php");

require_once("../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');

$goodsController = new GoodsController();
$goodsBomController = new GoodsBomController();
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

        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            global $decimalQuantity;
            global $decimalValue;
            if ($slag === 'so_date' || $slag === 'use_date' || $slag === 'delivery_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } 
            elseif ($slag === "depreciated_asset_value" || $slag === "total_value" || $slag === "rate") {
                $cleanedValue = str_replace(',', '', $data['value']);
    
                
                    // Single value case
                    $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
                    $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
              }
              elseif ($slag === "qty") {
                $cleanedValue = str_replace(',', '', $data['value']);
    
                
                    $roundedValue = number_format(round((float)$cleanedValue, $decimalQuantity), $decimalQuantity, '.', '');
                    $conds .= "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $roundedValue;
                
              }else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sql_list = "SELECT
                    itemId,
                    itemCode,
                    itemName,
                    netWeight,
                    volume,
                    goodsType,
                    grossWeight,
                    baseUnitMeasure,
                    goodsGroup,
                    items.status,
                    use_asset_id,
                    asset_code,
                    use_date,
                    rate,
                    qty,
                    total_value,
                    cost_center_id,
                    depreciated_asset_value
                FROM `erp_asset_use` AS uses
                    LEFT JOIN `erp_inventory_items` AS `items` ON items.itemId = uses.asset_id
                    WHERE 1 $cond
                    AND items.`goodsType` = 9
                    AND items.`company_id` = $company_id
                    ORDER BY use_asset_id DESC";
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
                $itemId = $data['itemId'];
                $itemCode = $data['itemCode'];
                $itemName = $data['itemName'];
                $netWeight = $data['netWeight'];
                $volume = $data['volume'];
                $goodsType = $data['goodsType'];
                $grossWeight = $data['grossWeight'];
                $buom_id = $data['baseUnitMeasure'];

                $buom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$buom_id ");
                $buom = $buom_sql['data']['uomName'];
                //  console($buom);
                $goodTypeId = $data['goodsType'];
                $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
                $type_name = $type_sql['data']['goodTypeName'];



                $goodGroupId = $data['goodsGroup'];
                $group_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE `goodGroupId`=$goodGroupId ");
                $group_name = $group_sql['data']['goodGroupName']??"";


                $summary_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId");
                $mwp = $summary_sql['data']['movingWeightedPrice'];
                $val_class = $summary_sql['data']['priceType'];
                $costcenter_id=$data['cost_center_id'];
                $costcenter = queryGet("SELECT `CostCenter_code`,`CostCenter_desc` FROM `erp_cost_center` WHERE `CostCenter_id`=$costcenter_id AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id");
                

                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "itemId"=>$data['itemId'],
                    "itemCode" => $data['itemCode'],
                    "itemName" => $data['itemName'],
                    "cost_center"=>$costcenter['data']['CostCenter_desc'].'('.$costcenter['data']['CostCenter_code'].')',
                    "uom"=>$buom,
                    "group_name"=>$group_name,
                    "type_name"=>$type_name,
                    "mwp"=>$mwp,
                    "val_class"=>$val_class,
                    "items.status"=>$data['status'],
                    "use_asset_id"=>$data["use_asset_id"],
                    "use_date"=>formatDateWeb($data['use_date']),
                    "rate"=>decimalValuePreview($data['rate']),
                    "qty"=>decimalQuantityPreview($data['qty']),
                    "total_value"=>decimalValuePreview($data['total_value']),
                    "depreciated_asset_value"=>decimalValuePreview($data['depreciated_asset_value']),
                    "asset_code" => $data['asset_code']

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
                "sqlMain" => $sqlMainQryObj,
                "SqlRowcount" => $sqlRowCount,


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
      $havingCond = "";
      $implodeFrom = implode('', array_map(function ($slag, $data) {
        $conds = "";
        global $decimalQuantity;
        global $decimalValue;
        if ($slag === 'so_date' || $slag === 'use_date' || $slag === 'delivery_date') {
            if ($data['operatorName'] === 'BETWEEN') {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
            } else {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
            }
        } 
        elseif ($slag === "depreciated_asset_value" || $slag === "total_value" || $slag === "rate") {
            $cleanedValue = str_replace(',', '', $data['value']);

            
                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            
          }
          elseif ($slag === "qty") {
            $cleanedValue = str_replace(',', '', $data['value']);

            
                $roundedValue = number_format(round((float)$cleanedValue, $decimalQuantity), $decimalQuantity, '.', '');
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $roundedValue;
          }else {
            $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
        }

        return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    if (!empty($implodeFrom)) {
        $cond .= $implodeFrom;
    }

    $sql_list = "SELECT
                    itemId,
                    itemCode,
                    itemName,
                    netWeight,
                    volume,
                    goodsType,
                    grossWeight,
                    baseUnitMeasure,
                    goodsGroup,
                    items.status,
                    use_asset_id,
                    asset_code,
                    use_date,
                    rate,
                    qty,
                    total_value,
                    cost_center_id,
                    depreciated_asset_value
                FROM `erp_asset_use` AS uses
                LEFT JOIN `erp_inventory_items` AS `items` ON items.itemId = uses.asset_id
                WHERE 1 $cond
                AND items.`goodsType` = 9
                AND items.`company_id` = $company_id
                ORDER BY use_asset_id DESC";


    $dynamic_data_all = [];
    $sqlMainQryObjall = queryGet($sql_list, true);
    $sql_data_all = $sqlMainQryObjall['data'];
    $num_list = $sqlMainQryObjall['numRows'];


    if ($num_list > 0) {
        foreach ($sql_data_all as $data) {
            $itemId = $data['itemId'];
            $itemCode = $data['itemCode'];
            $itemName = $data['itemName'];
            $netWeight = $data['netWeight'];
            $volume = $data['volume'];
            $goodsType = $data['goodsType'];
            $grossWeight = $data['grossWeight'];
            $buom_id = $data['baseUnitMeasure'];

            $buom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$buom_id ");
            $buom = $buom_sql['data']['uomName'];
            //  console($buom);
            $goodTypeId = $data['goodsType'];
            $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
            $type_name = $type_sql['data']['goodTypeName'];



            $goodGroupId = $data['goodsGroup'];
            $group_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE `goodGroupId`=$goodGroupId ");
            $group_name = $group_sql['data']['goodGroupName']??"";


            $summary_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId");
            $mwp = $summary_sql['data']['movingWeightedPrice'];
            $val_class = $summary_sql['data']['priceType'];
            $costcenter_id=$data['cost_center_id'];
            $costcenter = queryGet("SELECT `CostCenter_code`,`CostCenter_desc` FROM `erp_cost_center` WHERE `CostCenter_id`=$costcenter_id AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id");
            

            $dynamic_data_all[] = [
                "sl_no" => $sl,
                "itemId"=>$data['itemId'],
                "itemCode" => $data['itemCode'],
                "itemName" => $data['itemName'],
                "cost_center"=>$costcenter['data']['CostCenter_desc'].'('.$costcenter['data']['CostCenter_code'].')',
                "uom"=>$buom,
                "group_name"=>$group_name,
                "type_name"=>$type_name,
                "mwp"=>$mwp,
                "val_class"=>$val_class,
                "items.status"=>$data['status'],
                "use_asset_id"=>$data["use_asset_id"],
                "use_date"=>formatDateWeb($data['use_date']),
                "rate"=>decimalValuePreview($data['rate']),
                "qty"=>decimalQuantityPreview($data['qty']),
                "total_value"=>decimalValuePreview($data['total_value']),
                "depreciated_asset_value"=>decimalValuePreview($data['depreciated_asset_value']),

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