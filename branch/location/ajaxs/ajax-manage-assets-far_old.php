<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../app/v1/functions/branch/func-bom-controller.php");
require_once("pagination/common-pagination.php");

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

        // $sql_list = "SELECT * FROM  `erp_inventory_items` AS `items`  WHERE `goodsType` = 9 AND `company_id` = $company_id ORDER BY itemId DESC";
        $sql_list = "SELECT * FROM `erp_asset_use` AS `uses` LEFT JOIN `erp_inventory_items` AS `items` ON items.itemId = uses.asset_id WHERE 1 AND items.`goodsType` = 9 AND items.`company_id` = $company_id ORDER BY use_asset_id DESC";

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
                $prentGl =$data['parentGlId'];

                $gldetails = queryGet("SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND id=$prentGl");
                $gl_name=$gldetails['data']['gl_label'];
                $gl_code=$gldetails['data']['gl_code'];

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
                

                $batch_no= $data['batch_number'];
                $itemId= $data['itemId'];
                $assetused_id=$data['use_asset_id'];
                $strloc=queryGet("SELECT  LOG.logRef,str_loc.storage_location_name AS storage_location FROM
                                                erp_inventory_stocks_log AS LOG  
                                                LEFT JOIN erp_storage_location AS str_loc
                                            ON
                                                LOG.storageLocationId = str_loc.storage_location_id
                                            WHERE LOG.itemId='". $itemId . "' AND LOG.logRef= '".$batch_no."'")['data'];
                $grn_details=queryGet("SELECT * FROM erp_grn WHERE grnCode='".$batch_no."'");

                $totalTax = array_sum(array_column(json_decode($grn_details['data']['taxComponents'], true), 'taxAmount'));
                
                $depr=queryGet("SELECT * from erp_asset_depreciation WHERE asset_id='". $itemId. "' AND asset_use_id='". $assetused_id. "' ORDER BY asset_depreciation_id DESC LIMIT 1")['data'];
                
                $dep_key=$data['dep_key'];
                $depkey=queryGet("SELECT * from erp_depreciation_table where company_id='".$company_id. "' AND desp_key='". $dep_key."'")['data'];
                $method= $depr['method'];
                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "itemId"=>$data['itemId'],
                    "itemCode" => $data['itemCode'],
                    "itemName" => $data['itemName'],
                    "itemDesc" => $data['itemDesc'],
                    "batch_no" => $data['batch_number'],
                    'storage_loc'=> $strloc['storage_location'],
                    "cost_center"=>$costcenter['data']['CostCenter_desc'].'('.$costcenter['data']['CostCenter_code'].')',
                    "grn_doc_no"=>$grn_details['data']['vendorDocumentNo'],
                    "grn_date" => $grn_details['data']['postingDate'],
                    "inv_no"=> $grn_details['data']['grnPoNumber'],
                    "inv_date"=> $grn_details['data']['po_date'],
                    "vendor_name"=> $grn_details['data']['vendorName'],
                    "vendor_gst"=> $grn_details['data']['vendorGstin'],
                    "vendor_address"=>$grn_details['data']['vendorGstinStateName'],
                    "uom"=>$buom,
                    "basic_value"=>$grn_details['data']['grnSubTotal'],
                    "total_gst"=> $totalTax,
                    "total_with_gst"=> $grn_details['data']['grnTotalAmount'],
                    "group_name"=>$group_name,
                    "type_name"=>$type_name,
                    "mwp"=>$mwp,
                    "val_class"=>$val_class,
                    "status"=>$data['status'],
                    "use_asset_id"=>$data["use_asset_id"],
                    "created_at"=>$data['use_date'],
                    "rate"=>$data['rate'],
                    "qty"=>$data['qty'],
                    "total_value"=>$data['total_value'],
                    "depreciated_asset_value"=>$data['depreciated_asset_value'],
                    "gl_name"=>$gl_name,
                    "gl_code"=>$gl_code,
                    "usefule_life"=> $depkey['asset_life'],
                    "dep_rate"=> $depkey[$method],
                    "dep_method"=> $depr['method'],
                    "wdv"=>$depr['depreciation_on_value'],
                    "lst_wdv"=> $depr['depreciation_on_value'],
                    "lst_run_dep"=> $depr['depreciation_date'] ,
                    "accumulated"=> $depr['asset_value']-$depr['depreciation_date'],
                    

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
