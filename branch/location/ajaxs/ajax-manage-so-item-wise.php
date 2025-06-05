<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("pagination/common-pagination.php");
// require_once("../../common/exportexcel.php");
require_once("../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');

// print_r($_POST);
// $currentDate = date('Y-m-d');
// $timestampPreviousDay = strtotime($fromd . ' -1 day');
// $previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'soapprove') {
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
            global $decimalQuantity;
            if ($slag === 'so.so_date' || $slag === 'so.delivery_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            }elseif($slag==="items.totalPrice"){

                $cleanedValue = str_replace(',', '', $data['value']);


                // Single value case
                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            } 
            else if($slag==="items.qty" || $slag==="items.tax"||$slag==="items.totalDiscount")
            {
                $cleanedValue = str_replace(',', '', $data['value']);


                // Single value case
                $roundedValue = number_format(round((float)$cleanedValue, $decimalQuantity), $decimalQuantity, '.', '');
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $roundedValue;   
            }  else if($slag === 'so.created_by' || $slag==='created_by'){


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

        $sts = " AND `so`.status !='deleted'";

                                
             $sql_list = "SELECT
                                so.so_id AS so_id,
                                so.so_number AS so_number,
                                so.delivery_date AS delivery_date,
                                so.customer_id AS customer_id,
                                cust.trade_name AS customer_name,
                                cust.customer_code AS customer_code,
                                so.billingAddress AS billing_address,
                                so.shippingAddress AS shipping_address,
                                so.so_date AS so_date,
                                so.credit_period AS credit_period,
                                items.so_item_id AS so_item_id,
                                items.itemCode AS itemCode,
                                items.itemName AS itemName,
                                items.qty AS total_quantity,
                                items.uom AS uom_id,
                                uoms.uomName AS uom_name,
                                items.tax AS tax,
                                items.totalDiscount AS total_discount,
                                items.totalPrice AS item_total_price,
                                delivery.so_delivery_id,
                                delivery.delivery_date AS delivery_date_schedule,
                                delivery.deliveryStatus,
                                delivery.qty AS delivery_qty,
                                so.created_by
                            FROM
                                erp_branch_sales_order AS so
                            LEFT JOIN
                                erp_customer AS cust ON so.customer_id = cust.customer_id
                            JOIN
                                erp_branch_sales_order_items AS items ON so.so_id = items.so_id
                            LEFT JOIN
                                erp_inventory_mstr_uom AS uoms ON items.uom = uoms.uomId
                            JOIN
                                erp_branch_sales_order_delivery_schedule AS delivery ON items.so_item_id = delivery.so_item_id
                            WHERE 
                                so.so_id = items.so_id 
                                AND so.company_id = $company_id
                                AND so.branch_id = $branch_id
                                AND so.location_id = $location_id
                                AND so.approvalStatus != 14 
                                AND items.so_item_id = delivery.so_item_id " . $cond . " " . $sts . "
                            ORDER BY 
                                items.so_item_id DESC";
                                             

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

                if ($data['deliveryStatus'] == "open") {
                    $delvStatis = '<div class="status-bg status-open">Open</div>';
                  } elseif ($data['deliveryStatus'] == "pending") {
                    $delvStatis = '<div class="status-bg status-pending">Pending</div>';
                  } elseif ($data['deliveryStatus'] == "pgi") {
                    $delvStatis = '<div class="status-bg status-approved">Pgi</div>';
                  } elseif ($data['deliveryStatus'] == "Delivery Created") {
                    $delvStatis = '<div class="status-bg status-pending">Delivery Created</div>';
                  }

                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "so.so_number" => $data['so_number'],
                    "so.so_date" => $data['so_date'],
                    "so.delivery_date" => $data['delivery_date'],
                    "cust.customer_code"=>$data['customer_code'],          
                    "cust.trade_name" =>  $data['customer_name'],
                    "items.itemName" =>$data['itemName'],
                    "items.itemCode"=>$data['itemCode'],
                    "items.qty"=> $data['total_quantity'],
                    "uoms.uomName"=> $data['uom_name'],
                    "items.tax"=>decimalQuantityPreview($data['tax']),
                    "items.totalDiscount"=>decimalQuantityPreview($data['total_discount']),
                    "items.totalPrice"=>decimalValuePreview($data['item_total_price']),
                    "delvStatus"=>$delvStatis,
                    "delivery.deliveryStatus"=>$data['deliveryStatus'],
                    "delivery.qty"=>$data['delivery_qty'],
                    "so.created_by" => getCreatedByUser($data['created_by']),
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
                // "csvContent"=>$csvContent,
                // "csvContentBypagination"=>$csvContentBypagination

            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sql_list,
                "sqlMain" => $sqlMainQryObj
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
            global $decimalQuantity;
            if ($slag === 'so.so_date' || $slag === 'so.delivery_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            }elseif($slag==="items.totalPrice"){

                $cleanedValue = str_replace(',', '', $data['value']);


                // Single value case
                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            } 
            else if($slag==="items.qty" || $slag==="items.tax"||$slag==="items.totalDiscount")
            {
                $cleanedValue = str_replace(',', '', $data['value']);


                // Single value case
                $roundedValue = number_format(round((float)$cleanedValue, $decimalQuantity), $decimalQuantity, '.', '');
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $roundedValue;   
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

        $sts = " AND `so`.status !='deleted'";

                                
             $sql_list = "SELECT
                                so.so_id AS so_id,
                                so.so_number AS so_number,
                                so.delivery_date AS delivery_date,
                                so.customer_id AS customer_id,
                                cust.trade_name AS customer_name,
                                cust.customer_code AS customer_code,
                                so.billingAddress AS billing_address,
                                so.shippingAddress AS shipping_address,
                                so.so_date AS so_date,
                                so.credit_period AS credit_period,
                                items.so_item_id AS so_item_id,
                                items.itemCode AS itemCode,
                                items.itemName AS itemName,
                                items.qty AS total_quantity,
                                items.uom AS uom_id,
                                uoms.uomName AS uom_name,
                                items.tax AS tax,
                                items.totalDiscount AS total_discount,
                                items.totalPrice AS item_total_price,
                                delivery.so_delivery_id,
                                delivery.delivery_date AS delivery_date_schedule,
                                delivery.deliveryStatus,
                                delivery.qty AS delivery_qty,
                                so.created_by
                            FROM
                                erp_branch_sales_order AS so
                            LEFT JOIN
                                erp_customer AS cust ON so.customer_id = cust.customer_id
                            JOIN
                                erp_branch_sales_order_items AS items ON so.so_id = items.so_id
                            LEFT JOIN
                                erp_inventory_mstr_uom AS uoms ON items.uom = uoms.uomId
                            JOIN
                                erp_branch_sales_order_delivery_schedule AS delivery ON items.so_item_id = delivery.so_item_id
                            WHERE 
                                so.so_id = items.so_id 
                                AND so.company_id = $company_id
                                AND so.branch_id = $branch_id
                                AND so.location_id = $location_id
                                AND so.approvalStatus != 14 
                                AND items.so_item_id = delivery.so_item_id " . $cond . " " . $sts . "
                            ORDER BY 
                                items.so_item_id DESC";
                                             
     $dynamic_data_all = [];
     $sqlMainQryObjall = queryGet($sql_list, true);
     $sql_data_all = $sqlMainQryObjall['data'];
     $num_list =  $sqlMainQryObjall['numRows'];
     if ($num_list > 0) {
     foreach ($sql_data_all as $data) {
  
      $goodsType = "";
          if ($data['goodsType'] === "material") {
            $goodsType .= '<p class="goods-type type-goods">GOODS</p>';
            $goods='GOODS';
          } elseif ($data['goodsType'] === "service") {
            $goodsType .= '<p class="goods-type type-service">SERVICE</p>';
            $goods='SERVICE';
          } elseif ($data['goodsType'] === "both") {
            $goodsType .= '<p class="goods-type type-goods">BOTH</p>';
            $goods='BOTH';
            
          } elseif ($data['goodsType'] === "project") {
            $goodsType .= '<p class="goods-type type-project">PROJECT</p>';
            $goods='PROJECT';
          }
  
          if ($data['label'] == "open") {
            $approvalStatus = '<div class="status-bg status-open">Open</div>';
            $approve='Open';
          } elseif ($data['label'] == "pending") {
            $approvalStatus = '<div class="status-bg status-pending">Pending</div>';
            $approve='Pending';
          } elseif ($data['label'] == "exceptional") {
            $approvalStatus = '<div class="status-bg status-exceptional">Exceptional</div>';
            $approve='Exceptional';
          } elseif ($data['label'] == "closed") {
            $approvalStatus = '<div class="status-bg status-closed">Closed</div>';
            $approve='Closed';
          } elseif ($data['label'] == "rejected") {
            $approvalStatus = '<div class="status-bg status-closed">Rejected</div>';
            $approve='Rejected';
          }
          
      $dynamic_data_all[]= [
       "sl_no" => $sl,
                    "so.so_number" => $data['so_number'],
                    "so.so_date" => $data['so_date'],
                    "so.delivery_date" => $data['delivery_date'],
                    "cust.customer_code"=>$data['customer_code'],          
                    "cust.trade_name" =>  $data['customer_name'],
                    "items.itemName" =>$data['itemName'],
                    "items.itemCode"=>$data['itemCode'],
                    "items.qty"=> $data['total_quantity'],
                    "uoms.uomName"=> $data['uom_name'],
                    "items.tax"=>decimalQuantityPreview($data['tax']),
                    "items.totalDiscount"=>decimalQuantityPreview($data['total_discount']),
                    "items.totalPrice"=>decimalValuePreview($data['item_total_price']),
                    "delivery.deliveryStatus"=>$data['deliveryStatus'],
                    "delivery.qty"=>$data['delivery_qty'],
                    "so.created_by" => getCreatedByUser($data['created_by']),
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
  