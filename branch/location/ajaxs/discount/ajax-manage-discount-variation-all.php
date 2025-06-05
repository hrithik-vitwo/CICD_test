<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../pagination/common-pagination.php");
// require_once("../../../common/exportexcel.php");
require_once("../../../common/exportexcel-new.php");
require_once("../../../../app/v1/functions/branch/func-discount-controller.php");
$headerData = array('Content-Type: application/json');

$CustomerDiscountControllerObj = new CustomerDiscountGroupController();

if ($_POST['act'] == 'discountVariationTable') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $limit_per_Page = isset($_POST['limit']) && $_POST['limit'] != '' ? $_POST['limit'] : 25;

        $page_no = isset($_POST['pageNo']) ? (int) $_POST['pageNo'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $formObj = $_POST['formDatas'];
        $cond = "";
        // $cond = "AND DATE(so_date) BETWEEN '" . $previousDate . "' AND '" . $currentDate . "'";


        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            global $decimalQuantity;
            global $decimalValue;
            // Handle date fields correctly
            if (in_array($slag, ['updated_at', 'created_at', 'valid_from', 'valid_upto'])) {
                $new_slag = 'varient.' . $slag;
                
                if ($data['operatorName'] === 'BETWEEN' && is_array($data['value'])) {
                    $conds .= " DATE($new_slag) BETWEEN '" . $data['value']['fromDate'] . "' AND '" . $data['value']['toDate'] . "' ";
                } else {
                    $conds .= " DATE($new_slag) " . $data['operatorName'] . " '" . $data['value'] . "' ";
                }
            }
            
            // Handle 'created_by' and 'updated_by' conditions
            else if ($slag === 'created_by' || $slag === 'updated_by') {
                if (in_array($data['operatorName'], ['LIKE', 'NOT LIKE'])) {
                    $opr = ($data['operatorName'] === 'LIKE') ? 'LIKE' : 'NOT LIKE';
                    $resultList = getAdminUserIdByName($data['value']);
                    $new_slag = 'varient.' . $slag;
        
                    if (strpos($resultList, ',') !== false) {
                        $resultList = (!empty($resultList)) ? $resultList : '0';
                        $opr = ($opr === 'LIKE') ? 'IN' : 'NOT IN';
                        $conds .= $new_slag . " $opr (" . $resultList . ")";
                    } else {
                        $resultList = (!empty($resultList)) ? $resultList : '0';
                        $conds .= $new_slag . " $opr '%" . $resultList . "%'";
                    }
                }
            }
        
            // Handle minimum_valueQuantity condition
            else if ($slag === 'minimum_valueQuantity') {
                $conds .= "(";
                $conds .= "minimum_qty " . $data['operatorName'] . " '%" . $data['value'] . "%' ";
                $conds .= "OR minimum_value " . $data['operatorName'] . " '%" . $data['value'] . "%' ";
                $conds .= "OR CONCAT(minimum_qty, '(quantity) ', `condition`, ' ', minimum_value, '(value)') " . $data['operatorName'] . " '%" . $data['value'] . "%'";
                $conds .= ")";
            }
        
            else if ($slag === "discount_percentage") {
                // Remove all thousand separators (,) safely while keeping decimal points (.)
                $cleanedValue = str_replace(',', '', $data['value']);
            
                
                    // Single value case
                    $roundedValue = number_format(round((float)$cleanedValue, $decimalQuantity), $decimalQuantity, '.', '');
                    $conds .= "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $roundedValue;
                
            }

            else if ($slag === "discount_max_value" || $slag === "discount_value") {
                // Remove all thousand separators (,) safely while keeping decimal points (.)
                $cleanedValue = str_replace(',', '', $data['value']);
            
                
                    // Single value case
                    $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
                    $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
                
            }        
            // General fallback condition, EXCLUDING date fields
            else if (!in_array($slag, ['updated_at', 'created_at', 'valid_from', 'valid_upto'])) {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }
        
           return $data['value'] !== '' ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));
        
        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }
        



        // $sts = " AND `so`.status !='deleted'";


        $sql_list = "SELECT varient.*,item_group.item_discount_group_id as itemDisGrpId,item_group.item_discount_group,customer_group.customer_discount_group_id as cusDisGrpID,customer_group.customer_discount_group FROM `erp_discount_variant_master` as varient LEFT JOIN  `erp_item_discount_group` as item_group ON item_group.item_discount_group_id = varient.item_discount_group_id LEFT JOIN `erp_customer_discount_group` as customer_group ON customer_group.customer_discount_group_id = varient.customer_discount_group_id WHERE 1 " . $cond . "  AND varient.`company_id`=$company_id AND varient.`branch_id`=$branch_id AND varient.`location_id`=$location_id ORDER BY discount_variant_id  desc ";

        $sql_Mainqry = $sql_list . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);

        $dynamic_data = [];
        $num_list = $sqlMainQryObj['numRows'];
        $sql_data = $sqlMainQryObj['data'];
        $output = "";
        $limitText = "";
        $sl = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        if ($num_list > 0) {
            foreach ($sql_data as $data) {

                if ($data['discount_type'] == 'percentage') {
                    $dataPercentage = $data['discount_percentage'];
                    $dataDisountMaxvalue = decimalQuantityPreview($data['discount_max_value']);
                } else {
                    $dataPercentage = "-";
                    $dataDisountMaxvalue = '-';
                }




                if ($data['discount_type'] == 'value') {
                    $dataDiscountValue = decimalValuePreview($data['discount_value']);
                } else {
                    $dataDiscountValue = "-";
                }

                if ($data['minimum_value'] != 0 && $data['minimum_qty'] != 0) {
                    $dataminValQty = decimalQuantityPreview($data['minimum_qty']) . '(quantity) ' . $data['condition'] . ' ' . decimalValuePreview($data['minimum_value']) . '(value)';
                } elseif ($data['minimum_value'] != 0 && $data['minimum_qty'] == 0) {
                    $dataminValQty = decimalValuePreview($data['minimum_value']) . '(value)';
                } elseif ($data['minimum_qty'] != 0 && $data['minimum_value'] == 0) {
                    $dataminValQty = decimalQuantityPreview($data['minimum_qty']) . '(quantity)';
                } else {
                    $dataminValQty = '-';
                }

                // echo $data['minimumDetails'];


                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "customer_group.customer_discount_group" => $data['customer_discount_group'] ?? "-",
                    "item_group.item_discount_group" => $data['item_discount_group'] ?? "-",
                    "discount_percentage" => decimalQuantityPreview($dataPercentage) ?? "-",
                    "discount_max_value" => $dataDicountMaxvalue ?? "-",
                    "discount_value" => $dataDiscountValue ?? "-",
                    "term_of_payment" => $data['term_of_payment'] ?? "-",
                    "valid_from" => formatDateWeb($data['valid_from']) ?? "-",
                    "valid_upto" => formatDateWeb($data['valid_upto']) ?? "-",
                    "minimum_valueQuantity" => $dataminValQty ?? "-",
                    "coupon" => $data['coupon'] ?? "-",
                    "created_by" => getCreatedByUser($data['created_by']),
                    "created_at" => formatDateWeb($data['created_at']),
                    "discount_variant_id" => $data['discount_variant_id']

                ];
                $sl++;
            }
            $output .= "</table>";
            $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";

            // $sqlRowCount = ""
            $queryset = queryGet($sqlRowCount);
            $totalRows = $queryset['data']['row_count'];
            $total_page = ceil($totalRows / $limit_per_Page);
            $output .= pagiNation($page_no, $total_page);

            $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';

            // console($sqlRowCount);

            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "pagination" => $output,
                "limitTxt" => $limitText,
                "sqlMain" => $sql_data,
                "the_query" => $sqlMainQryObj['sql'],
                "row_count" => $queryset['sql'],
                "formObj"=>$formObj

            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sql_list,
                "sqlMain" => $sqlMainQryObj,
                // "conds" => console($cond)
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
            global $decimalQuantity;
            global $decimalValue;

            // Handle date fields correctly
            if (in_array($slag, ['updated_at', 'created_at', 'valid_from', 'valid_upto'])) {
                $new_slag = 'varient.' . $slag;

                if ($data['operatorName'] === 'BETWEEN' && is_array($data['value'])) {
                    $conds .= " DATE($new_slag) BETWEEN '" . $data['value']['fromDate'] . "' AND '" . $data['value']['toDate'] . "' ";
                } else {
                    $conds .= " DATE($new_slag) " . $data['operatorName'] . " '" . $data['value'] . "' ";
                }
            }

            // Handle 'created_by' and 'updated_by' conditions
            else if ($slag === 'created_by' || $slag === 'updated_by') {
                if (in_array($data['operatorName'], ['LIKE', 'NOT LIKE'])) {
                    $opr = ($data['operatorName'] === 'LIKE') ? 'LIKE' : 'NOT LIKE';
                    $resultList = getAdminUserIdByName($data['value']);
                    $new_slag = 'varient.' . $slag;

                    if (strpos($resultList, ',') !== false) {
                        $opr = ($opr === 'LIKE') ? 'IN' : 'NOT IN';
                        $conds .= $new_slag . " $opr (" . $resultList . ")";
                    } else {
                        $conds .= $new_slag . " $opr '%" . $resultList . "%'";
                    }
                }
            }

            // Handle minimum_valueQuantity condition
            else if ($slag === 'minimum_valueQuantity') {
                $conds .= "(";
                $conds .= "minimum_qty " . $data['operatorName'] . " '%" . $data['value'] . "%' ";
                $conds .= "OR minimum_value " . $data['operatorName'] . " '%" . $data['value'] . "%' ";
                $conds .= "OR CONCAT(minimum_qty, '(quantity) ', `condition`, ' ', minimum_value, '(value)') " . $data['operatorName'] . " '%" . $data['value'] . "%'";
                $conds .= ")";
            }

            else if ($slag === "discount_percentage") {
                // Remove all thousand separators (,) safely while keeping decimal points (.)
                $cleanedValue = str_replace(',', '', $data['value']);
            
                
                    // Single value case
                    $roundedValue = number_format(round((float)$cleanedValue, $decimalQuantity), $decimalQuantity, '.', '');
                    $conds .= "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $roundedValue;
                
            }

            else if ($slag === "discount_max_value" || $slag === "discount_value") {
                // Remove all thousand separators (,) safely while keeping decimal points (.)
                $cleanedValue = str_replace(',', '', $data['value']);
            
                
                    // Single value case
                    $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
                    $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
                
            }

            // General fallback condition, EXCLUDING date fields
            else if (!in_array($slag, ['updated_at', 'created_at', 'valid_from', 'valid_upto'])) {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }
        $sql_list = "SELECT varient.*,item_group.item_discount_group_id,item_group.item_discount_group,customer_group.customer_discount_group_id,customer_group.customer_discount_group FROM `erp_discount_variant_master` as varient LEFT JOIN  `erp_item_discount_group` as item_group ON item_group.item_discount_group_id = varient.item_discount_group_id LEFT JOIN `erp_customer_discount_group` as customer_group ON customer_group.customer_discount_group_id = varient.customer_discount_group_id WHERE 1 " . $cond . "  AND varient.`company_id`=$company_id AND varient.`branch_id`=$branch_id AND varient.`location_id`=$location_id ORDER BY discount_variant_id  desc ";

        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];
        if ($num_list > 0) {
            $sl = 1;
            foreach ($sql_data_all as $data) {


                if ($data['discount_type'] == 'percentage') {
                    $dataPercentage = $data['discount_percentage'];
                    $dataDisountMaxvalue = decimalValuePreview($data['discount_max_value']);
                } else {
                    $dataPercentage = "-";
                    $dataDisountMaxvalue = '-';
                }




                if ($data['discount_type'] == 'value') {
                    $dataDiscountValue = decimalValuePreview($data['discount_value']);
                } else {
                    $dataDiscountValue = "-";
                }

                if ($data['minimum_value'] != 0 && $data['minimum_qty'] != 0) {
                    $dataminValQty = decimalQuantityPreview($data['minimum_qty']) . '(quantity) ' . $data['condition'] . ' ' . decimalValuePreview($data['minimum_value']) . '(value)';
                } elseif ($data['minimum_value'] != 0 && $data['minimum_qty'] == 0) {
                    $dataminValQty = decimalValuePreview($data['minimum_value']) . '(value)';
                } elseif ($data['minimum_qty'] != 0 && $data['minimum_value'] == 0) {
                    $dataminValQty = decimalQuantityPreview($data['minimum_qty']) . '(quantity)';
                } else {
                    $dataminValQty = '-';
                }

                $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "customer_group.customer_discount_group" => $data['customer_discount_group'] ?? "-",
                    "item_group.item_discount_group" => $data['item_discount_group'] ?? "-",
                    "discount_percentage" => decimalQuantityPreview($dataPercentage) ?? "-",
                    "discount_max_value" => $dataDicountMaxvalue ?? "-",
                    "discount_value" => $dataDiscountValue ?? "-",
                    "term_of_payment" => $data['term_of_payment'] ?? "-",
                    "valid_from" => formatDateWeb($data['valid_from']) ?? "-",
                    "valid_upto" => formatDateWeb($data['valid_upto']) ?? "-",
                    "minimum_valueQuantity" => $dataminValQty ?? "-",
                    "coupon" => $data['coupon'] ?? "-",
                    "created_by" => getCreatedByUser($data['created_by']),
                    "created_at" => formatDateWeb($data['created_at']),
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

if ($_GET["act"] == "updateCusDisGrp") {
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $cusDisgrpid = $_GET["cusdisgrpid"] ?? '';
        $cusdisGrpname = $_GET["cusdisgrpname"] ?? '';

        $postData = [
            "id" => $cusDisgrpid,
            "name" => $cusdisGrpname,
        ];

        $update = $CustomerDiscountControllerObj->edit_customer_discount_group($postData, $company_id, $created_by);


        $res = [];
        if ($update['status'] == 'success') {
            $res = ["status" => "success", "message" => "Discount group updated successfully"];
        } else {
            $res = ["status" => "error", "message" => "Something went wrong"];
        }

        echo json_encode($res);
    }
}


if ($_POST["act"] == "addCusDisGrp") {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $cusdisGrpname = $_POST["cusdisGrpname"] ?? '';

        $postData = [
            "name" => $cusdisGrpname,
        ];

        $insert = $CustomerDiscountControllerObj->create_customer_discount_group($postData, $company_id, $created_by);

        $res = [];
        if ($insert['status'] == 'success') {
            $res = ["status" => "success", "message" => "Discount group created successfully"];
        } else {
            $res = ["status" => "error", "message" => "Something went wrong"];
        }

        echo json_encode($res);
    }
}