<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-po-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../../app/v1/functions/common/templates/template-purchase-order.php");

$headerData = array('Content-Type: application/json');
$dbObj = new Database();
$BranchPoObj = new BranchPo();
$ItemsObj = new ItemsController();
$templatePoObj = new TemplatePoController();
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if ($_GET['act'] == 'modaldata') {
        $poId = $_GET['po_id'];
        $poIdBash = base64_encode($poId);
        $cond = "po.company_id=$company_id AND po.branch_id=$branch_id AND po.location_id=$location_id AND po.po_id=$poId AND po.status!='deleted'";
        $sql_list = "SELECT po.*, ven.vendor_code, ven.vendor_pan, ven.vendor_currency, ven.vendor_gstin, ven.trade_name AS vendor_name, ven.vendor_authorised_person_email AS vendor_email, ven.vendor_id, ven.vendor_authorised_person_phone AS vendor_phone, addr.* FROM `erp_branch_purchase_order` AS po JOIN `erp_vendor_details` AS ven ON ven.vendor_id = po.vendor_id LEFT JOIN `erp_vendor_bussiness_places` AS addr ON addr.vendor_id = ven.vendor_id AND addr.vendor_business_primary_flag = 1 WHERE $cond";

        $sqlMainQryObj = $dbObj->queryGet($sql_list);

        $data = $sqlMainQryObj['data'];
        $num_list = $sqlMainQryObj['numRows'];

        $dynamic_data = [];

        if ($num_list > 0) {
            $dynamic_data = [];

            $itemDetails = $BranchPoObj->fetchBranchPoItems($poId)['data'];

            $items = [];
            $allSubTotal = 0;
            $totalDis = 0;

            $termsandcondqry = queryGet("SELECT * FROM `erp_applied_terms_and_conditions` WHERE slug='po' AND slug_id=" . $poId . "")['numRows'];
            $enabilitiesObj = queryGet("SELECT `company_terms_and_cond`,`print_terms_and_cond` FROM `erp_company_enabilities` WHERE `company_id`=" . $company_id . "")['data'];
            $checkcompantTC = $enabilitiesObj['company_terms_and_cond'];
            $printChkTC = $enabilitiesObj['print_terms_and_cond'];
            $showChkbox = 0;
            if ($termsandcondqry > 0 && $checkcompantTC == 1) {
                $showChkbox = 1;
            }

            foreach ($itemDetails as $oneItem) {
                // console($oneItem);

                $gstAmount = 0;
                $itemTotalAmount = 0;
                $subTotal = $oneItem['unitPrice'] * $oneItem['qty'];
                // $taxAbleAmount = $subTotal - $oneItem['itemTotalDiscount'];
                if ($oneItem['gst'] == 0) {
                    $itemTotalAmount = $subTotal;
                } else {
                    $gstAmount = ($subTotal * $oneItem['gst']) / 100;
                    $itemTotalAmount = $subTotal + $gstAmount;
                }

                $allSubTotal += $subTotal;

                $items[] = [
                    "itemCode" => $oneItem['itemCode'],
                    "itemName" => $oneItem['itemName'],
                    "qty" => $oneItem['qty'],
                    "remainingQty" => $oneItem['remainingQty'],
                    "unitPrice" => $oneItem['unitPrice'],
                    "subTotal" => $subTotal,
                    "tax" => $oneItem['gst'],
                    "uomName" => $oneItem['uom'],
                    "gstAmount" => $gstAmount,
                    "itemTotalAmount" => $itemTotalAmount
                ];
            }


            $addressSql = $dbObj->queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE othersLocation_id='" . $data['bill_address'] . "'")['data'];

            $billAddress = $addressSql['othersLocation_building_no'] . ', ' . $addressSql['othersLocation_flat_no'] . ', ' . $addressSql['othersLocation_street_name'] . ', ' . $addressSql['othersLocation_pin_code'] . ', ' . $addressSql['othersLocation_location'] . ', ' . $addressSql['othersLocation_district'] . ', ' . $addressSql['othersLocation_city'] . ' ,' . $addressSql['othersLocation_state'];

            $addressSql = $dbObj->queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE othersLocation_id='" . $data['ship_address'] . "'")['data'];
            $shipAddresss = $addressSql['othersLocation_building_no'] . ', ' . $addressSql['othersLocation_flat_no'] . ', ' . $addressSql['othersLocation_street_name'] . ', ' . $addressSql['othersLocation_pin_code'] . ', ' . $addressSql['othersLocation_location'] . ', ' . $addressSql['othersLocation_district'] . ', ' . $addressSql['othersLocation_city'] . ' ,' . $addressSql['othersLocation_state'];

            $vendorCur = $dbObj->queryGet("SELECT currency_name FROM `erp_currency_type` WHERE currency_id='" . $data['currency'] . "'")['data']['currency_name'];

            $parentPo = $dbObj->queryGet("SELECT * FROM `erp_branch_purchase_order` WHERE `po_id`='" . $data['parent_id'] . "' ")['data'];

            $functionalAreaName = $dbObj->queryGet("SELECT functionalities_name FROM `erp_company_functionalities` WHERE functionalities_id='" . $data['functional_area'] . "'")['data']['functionalities_name'];

            // navBar Button 
            $btn = '';
            if ($data['po_status'] == 9) {
                if ($data['use_type'] == 'servicep') {
                    $btn .= '<a class="nav-link btn btn-primary text-white py-2 px-3" id="" data-toggle="" href="manage-manual-grn.php?view=' . $data['po_number'] . '&type=srn">SRN</a>';
                    $btn .= '<a class="nav-link btn btn-danger text-white py-2 px-3" id="closepo" data-id="'.$poIdBash.'" data-po="' . $data['po_number'] . '"> Close PO</a>';
                } else {
                    $btn .= '<a class="nav-link btn btn-primary text-white py-2 px-3" id="" data-toggle="" href="manage-manual-grn.php?view=' . $data['po_number'] . '&type=grn">GRN</a>';
                    $btn .= '<a class="nav-link btn btn-danger text-white py-2 px-3" id="closepo" data-id="'.$poIdBash.'" data-po="' . $data['po_number'] . '"> Close PO</a>';
                }
            } else if ($data['po_status'] == 14) {
                $btn .= '<a class="nav-link approve-po btn btn-danger text-white float-right p-2"  id="rejectpo" data-id="'.$poIdBash.'" data-po="' . $data['po_number'] . '"  role="" aria-controls="profile" aria-selected="false">Reject PO</a>';
                $btn .= '<a class="nav-link approve-po btn btn-success text-white float-right p-2" id="approvepo" data-id="'.$poIdBash.'"  data-po="' . $data['po_number'] . '"  role="" aria-controls="profile" aria-selected="false">Approve PO</a>';
            }
            $navBtn = '<div class="action-btns display-flex-gap create-delivery-btn-sales gap-2" id="action-navbar">' . $btn . '</div>';

            $dynamic_data = [
                "dataObj" => $data,
                "companyCurrency" => getSingleCurrencyType($company_currency),
                "vendorCur" => $vendorCur,
                "parentPoNo" => $parentPo['po_number'],
                "functionalAreaName" => $functionalAreaName,
                "billAddress" => $billAddress,
                "shipAddress" => $shipAddresss,
                "currecy_name_words" => number_to_words_indian_rupees($data['totalAmount']),
                "currecy_name_wordsVendorCur" => number_to_words_indian_rupees($data['totalAmount'] * $data['conversion_rate']),
                "created_by" => getCreatedByUser($data['created_by']),
                "created_at" => formatDateORDateTime($data['created_at']),
                "updated_by" => getCreatedByUser($data['updated_by']),
                "updated_at" => formatDateORDateTime($data['updated_at']),
                "items" => $items,
                "allSubTotal" => $allSubTotal,
                "navBtn" => $navBtn
            ];

            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "showChkbox" => $showChkbox,
                "printChkTC" => $printChkTC

            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sqlMainQryObj
            ];
        }
        echo json_encode($res);
    } else if ($_GET['act'] == 'deletepo') {
        $poId = $_GET['po_id'];
        $sql = "UPDATE `erp_branch_purchase_order` SET STATUS = 'deleted' WHERE po_id = '" . $poId . "';";
        $querry = $dbObj->queryUpdate($sql);
        echo json_encode($querry);
    } else if ($_GET['act'] == 'classicview') {
        $poId = $_GET['po_id'];
        $templatePoObj->printPoItems($poId);
    } else if ($_GET['act'] == 'approvepo') {
        $po_id = base64_decode($_GET["id"]);
        $po = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` as po, `" . ERP_VENDOR_DETAILS . "` as vendor WHERE po.vendor_id=vendor.vendor_id  AND `po_id`=$po_id ";
        $poGet = $dbObj->queryGet($po);
        $status = 9;
        $update = "UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` SET `po_status`=$status WHERE `po_id`=$po_id";

        $updatePO = $dbObj->queryUpdate($update);
        $check_service = $dbObj->queryGet("SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE `ref_no` = '" . $po_no . "'", true);
        foreach ($check_service['data'] as $data) {
            $s_po_id = $data['po_id'];
            $update_service = $dbObj->queryUpdate("UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` SET `po_status`=$status WHERE `po_id`=$s_po_id");
        }
        

        $encodePo_id = base64_encode($po_id);
        $ref_no = $poGet['data']['ref_no'];
        $del_date = $poGet['data']['delivery_date'];
        $total_amount = $poGet['data']['totalAmount'];
        $po_no = $poGet['data']['po_number'];
        $to = $poGet['data']['vendor_authorised_person_email'];
        $sub = 'PO approved';
        $user_name = $poGet['data']['vendor_authorised_person_name'];
        $trade_name = $poGet['data']['trade_name'];
        $gst = $poGet['data']['vendor_gstin'];

        $msg = ' 
                <div>
                <div><strong>Dear ' . $user_name . ',</strong>(GSTIN:' . $gst . ')</div>
                <p>
                Your Purchase Order (' . $po_no . ') has been approved.
                </p>
                <strong>
                    PO details:
                </strong>
                <div style="display:grid">
                    <span>
                        Refernce Number: ' . $ref_no . '
                    </span>
                    <span>
                       Total Amount: ' . $total_amount . '
                    </span>
                    <span>
                        Delivery Date: <strong>' . $del_date . '</strong>
                    </span>
                </div>
               
                <div style="display:grid">
                    Best regards for, <span><b>' . $trade_name . '</b></span>
                </div>
                
                <p>
                <a href="' . BASE_URL . 'branch/location/branch-po-view.php?po_id=' . $encodePo_id . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View PO</a>
                
                </p>
                </div>
                        ';





        $emailReturn = SendMailByMySMTPmailTemplate($to, $sub, $msg, $tmpId = null);

        $res = [];
        if ($emailReturn == true) {
            // $res = ["status" => "success", "msg" => "Email sent"];


            $poId_sql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE po_id = " . $po_id;

            $po_Id_data = queryGet($poId_sql)['data'];

            $vendorId = $po_Id_data['vendor_id'];
            $poNumber = $po_Id_data['po_number'];
            $poDate = $po_Id_data['po_date'];
            $totalItems = $po_Id_data['totalItems'];
            $totalAmount = $po_Id_data['totalAmount'];
            $use_type = $po_Id_data['use_type'];
            $updated_at = $po_Id_data['updated_at'];

            global $updated_by;


            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrailDelv = array();
            $auditTrail['basicDetail']['trail_type'] = 'APPROVED';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_PURCHASE_ORDER;
            $auditTrail['basicDetail']['column_name'] = 'po_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $po_id;  // primary key
            $auditTrail['basicDetail']['party_type'] = 'vendor';
            $auditTrail['basicDetail']['party_id'] = $vendorId;
            $auditTrail['basicDetail']['document_number'] = $poNumber;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Purchase Order Approve';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Update';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($update['query']);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';


            $auditTrail['action_data']['Purchase Order Details']['PO Date'] = formatDateWeb($poDate);
            $auditTrail['action_data']['Purchase Order Details']['Total Items'] = decimalQuantityPreview($totalItems);
            $auditTrail['action_data']['Purchase Order Details']['Total Amount'] = decimalValuePreview($totalAmount);
            $auditTrail['action_data']['Purchase Order Details']['Use Type'] = $use_type;

            $auditTrail['action_data']['Purchase Order Details']['Approved By'] = getCreatedByUser($updated_by);
            $auditTrail['action_data']['Purchase Order Details']['Approved At'] = formatDateWeb($updated_at);

            $auditTrailreturn = generateAuditTrail($auditTrail);

            $res = ["status" => "success", "msg" => "Email sent" , "auditReturn" => $auditTrailreturn['POSTAdut']];

        } else {
            $res = ["status" => "warning", "msg" => "Mail Not Sent"];
        }
        echo json_encode($res);
    } else if ($_GET['act'] == 'rejectpo') {
        $po_id = base64_decode($_GET["id"]);
        $res = [];
        $update = $dbObj->queryUpdate("UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` SET `po_status`= 17 WHERE `po_id` = $po_id");
        if ($update['status'] == 'success') {
            $res = ["status" => "success", "msg" => "PO has been rejected"];

            $poId_sql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE po_id = " . $po_id;

            $po_Id_data = queryGet($poId_sql)['data'];

            global $updated_by;

            $vendorId = $po_Id_data['vendor_id'];
            $poNumber = $po_Id_data['po_number'];
            $poDate = $po_Id_data['po_date'];
            $totalItems = $po_Id_data['totalItems'];
            $totalAmount = $po_Id_data['totalAmount'];
            $use_type = $po_Id_data['use_type'];
            $updated_by_name = $po_Id_data['updated_by'];
            $updated_at = $po_Id_data['updated_at'];



            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrailDelv = array();
            $auditTrail['basicDetail']['trail_type'] = 'REJECT';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_PURCHASE_ORDER;
            $auditTrail['basicDetail']['column_name'] = 'po_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $po_id;  // primary key
            $auditTrail['basicDetail']['party_type'] = 'vendor';
            $auditTrail['basicDetail']['party_id'] = $vendorId;
            $auditTrail['basicDetail']['document_number'] = $poNumber;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Purchase Order Rejected';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Update';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($update['query']);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';


            $auditTrail['action_data']['Purchase Order Details']['PO Date'] = formatDateWeb($poDate);
            $auditTrail['action_data']['Purchase Order Details']['Total Items'] = decimalQuantityPreview($totalItems);
            $auditTrail['action_data']['Purchase Order Details']['Total Amount'] = decimalValuePreview($totalAmount);
            $auditTrail['action_data']['Purchase Order Details']['Use Type'] = $use_type;

            $auditTrail['action_data']['Purchase Order Details']['Rejected By'] = getCreatedByUser($updated_by);
            $auditTrail['action_data']['Purchase Order Details']['Rejected At'] = formatDateWeb($updated_at);

            $auditTrailreturn = generateAuditTrail($auditTrail);
        } else {
            $res = ["status" => "error", "msg" => "PO Rejection Failed"];
        }
        echo json_encode($res);
    } else if ($_GET['act'] == 'closepo') {
        $po_id = base64_decode($_GET['id']);
        $res = [];
        $update = $dbObj->queryUpdate("UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` SET `po_status`=10 WHERE `po_id`=$po_id");
        if ($update['status'] == "success") {
            $res = ["status" => "success", "msg" => "PO Closed Successfully"];
        } else {
            $res = ["status" => "error", "msg" => $update['message']];
        }
        echo json_encode($res);
    }
}
