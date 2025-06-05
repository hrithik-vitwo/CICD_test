<?php
class WarehouseController
{ //---------------------RackSerialNumberStart------------------
    function getRackSerialNumber($lastsl)
    {
        $now_prefix = 'R';
        $old_prefix = substr($lastsl, 0, 1);
        if ($now_prefix === $old_prefix) {
            $sl = (int)substr($lastsl, 1);
        } else {
            $sl = 0;
        }
        $id = $sl + 1;
        return 'R' . str_pad($id, 4, '0', STR_PAD_LEFT);
    }

    //---------------------RackSerialNumberEnd----------------------
    //---------------------LayerSerialNumberStart------------------
    function getLayerSerialNumber($lastsl)
    {
        $now_prefix = 'L';
        $old_prefix = substr($lastsl, 0, 1);
        if ($now_prefix === $old_prefix) {
            $sl = (int)substr($lastsl, 1);
        } else {
            $sl = 0;
        }
        $id = $sl + 1;
        return 'L' . str_pad($id, 4, '0', STR_PAD_LEFT);
    }

    //---------------------LayerSerialNumberEnd----------------------

    function createWarehouse($POST)
    {
        // console($branch_id);
        global $company_id;
        global $location_id;
        global $created_by;
        global $branch_id;
        global $updated_by;


        $isValidate = validate($POST, [
            "name" => "required",
            "address" => "required",
            "description" => "required",
            "lat" => "required",
            "lng" => "required",


        ], [
            "name" => "Enter good type",
            "address" => "Enter good group",
            "description" => "Enter purchase group",
            "lat" => "Enter availability check",
            "lng" => "Enter item name",
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }


        $lastQuery = "SELECT * FROM `" . ERP_WAREHOUSE . "` WHERE company_id=$company_id  ORDER BY `warehouse_id` DESC LIMIT 1";
        $last = queryGet($lastQuery);
        $lastRow = $last['data'] ?? "";
        $lastid = $lastRow['warehouse_code'] ?? 0;
        $returnWarehouseCode = getWHSerialNumber($lastid);


        //getWHSerialNumber($lastsl)
        $admin = array();
        $admin["name"] = $POST["name"];
        $admin["address"] = $POST["address"];
        $admin["description"] = $POST["description"];
        $admin["lat"] = $POST["lat"];
        $admin["lng"] = $POST["lng"];
        $flg = 1;
        $status = $POST["createWarehouse"] == "add_draft" ? "draft" : "active";

        $ins = "INSERT INTO `" . ERP_WAREHOUSE . "` 
        SET
            `warehouse_name`='" .  $admin["name"] . "',
            `warehouse_description`='" . $admin["description"] . "',
            `warehouse_address`='" . $admin["address"] . "',
            `warehouse_lat`='" . $admin["lat"] . "',
            `warehouse_lng`='" . $admin["lng"] . "',
            `company_id`='" .  $company_id  . "',
            `branch_id`='" . $branch_id . "',
            `location_id`='" . $location_id . "',
            `status`='" . $status . "',
            `warehouse_code`= '" . $returnWarehouseCode . "', 
            `created_by`='" . $created_by . "', 
            `updated_by`='" . $created_by . "'
            ";
        $warehouse = queryInsert($ins);
        if ($warehouse['status'] == 'success') {
            $warehouse_id = $warehouse['insertedId'];


            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_WAREHOUSE;
            $auditTrail['basicDetail']['column_name'] = 'warehouse_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $warehouse_id;  // primary key
            $auditTrail['basicDetail']['document_number'] = $returnWarehouseCode;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['party_id'] = 0;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'New warehouse added';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($ins);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['Warehouse Details']['code'] = $returnWarehouseCode;
            $auditTrail['action_data']['Warehouse Details']['name'] = $admin["name"];
            $auditTrail['action_data']['Warehouse Details']['description'] = $admin["description"];
            $auditTrail['action_data']['Warehouse Details']['address'] = $admin["address"];
            $auditTrail['action_data']['Warehouse Details']['lat'] = $admin["lat"];
            $auditTrail['action_data']['Warehouse Details']['lng'] = $admin["lng"];
            $auditTrail['action_data']['Warehouse Details']['status'] = $status;
            $auditTrail['action_data']['Warehouse Details']['created_by'] = getCreatedByUser($created_by);

            $auditTrailreturn1 = generateAuditTrail($auditTrail);

            $lastQuery = "SELECT * FROM `" . ERP_STORAGE_LOCATION . "` WHERE company_id=$company_id ORDER BY `storage_location_id` DESC LIMIT 1";
            $last = queryGet($lastQuery);
            $lastRow = $last['data'] ?? 0;
            $lastid = $lastRow['storage_location_code'] ?? 0;
            $SLLastId = getSLSerialNumber($lastid);

            //----------------------SL1
            $slcode = $this->storageLocationCode($SLLastId, 1);
            $storageLocationsql = "INSERT INTO `" . ERP_STORAGE_LOCATION . "` 
                SET
                    `company_id`='" .  $company_id  . "',
                    `branch_id`='" . $branch_id . "',
                    `location_id`='" . $location_id . "',
                    `warehouse_id`='" . $warehouse_id . "',
                    `storage_location_code`='" . $slcode . "',
                    `storage_location_name`='RM WH Open',
                    `storage_location_type`= 'RM-WH',
                    `storage_location_material_type`= 'RM',
                    `storage_location_storage_type`= 'Open',
                    `storageLocationTypeSlug`= 'rmWhOpen',
                    `storage_control`= '0', 
                    `temp_control`= '0',
                    `created_by`='" . $created_by . "',
                    `updated_by`='" . $updated_by . "', 
                    `status`='active'
                ";

            $storageLocation = queryInsert($storageLocationsql);
            if ($storageLocation['status'] == 'success') {
                $storage_location_id = $storageLocation['insertedId'];
                ///---------------------------------Audit Log Start---------------------
                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_STORAGE_LOCATION;
                $auditTrail['basicDetail']['column_name'] = 'storage_location_id'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $storage_location_id;  // primary key
                $auditTrail['basicDetail']['document_number'] =  $slcode;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['party_id'] = 0;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = 'New Storage Location added';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($storageLocationsql);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';

                $auditTrail['action_data']['Storage Location Details']['code'] = $slcode;
                $auditTrail['action_data']['Storage Location Details']['name'] = 'RM WH Open';
                $auditTrail['action_data']['Storage Location Details']['type'] = 'RM-WH';
                $auditTrail['action_data']['Storage Location Details']['material_type'] = 'RM';
                $auditTrail['action_data']['Storage Location Details']['storage_type'] = 'Open';
                $auditTrail['action_data']['Storage Location Details']['storageLocationTypeSlug'] = 'rmWhOpen';
                $auditTrail['action_data']['Storage Location Details']['control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['temp_control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['created_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['updated_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['visibility'] = 'show';
                $auditTrail['action_data']['Storage Location Details']['status'] = 'active';

                $auditTrailreturn = generateAuditTrail($auditTrail);
            }


            //----------------------SL2
            $slcode = $this->storageLocationCode($SLLastId, 2);
            $storageLocationsql = "INSERT INTO `" . ERP_STORAGE_LOCATION . "` 
                    SET
                        `company_id`='" .  $company_id  . "',
                        `branch_id`='" . $branch_id . "',
                        `location_id`='" . $location_id . "',
                        `warehouse_id`='" . $warehouse_id . "',
                        `storage_location_code`='" . $slcode . "',
                        `storage_location_name`='RM WH Reserve',
                        `storage_location_type`= 'RM-WH',
                        `storage_location_material_type`= 'RM',
                        `storage_location_storage_type`= 'Reserve',
                        `storageLocationTypeSlug`= 'rmWhReserve',
                        `storage_control`= '0', 
                        `temp_control`= '0',
                        `created_by`='" . $created_by . "',
                        `updated_by`='" . $updated_by . "', 
                        `status`='active'
                        ";

            $storageLocation = queryInsert($storageLocationsql);
            if ($storageLocation['status'] == 'success') {
                $storage_location_id = $storageLocation['insertedId'];
                ///---------------------------------Audit Log Start---------------------
                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_STORAGE_LOCATION;
                $auditTrail['basicDetail']['column_name'] = 'storage_location_id'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $storage_location_id;  // primary key
                $auditTrail['basicDetail']['document_number'] =  $slcode;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['party_id'] = 0;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = 'New Storage Location added';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($storageLocationsql);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';

                $auditTrail['action_data']['Storage Location Details']['code'] = $slcode;
                $auditTrail['action_data']['Storage Location Details']['name'] = 'RM WH Reserve';
                $auditTrail['action_data']['Storage Location Details']['type'] = 'RM-WH';
                $auditTrail['action_data']['Storage Location Details']['material_type'] = 'RM';
                $auditTrail['action_data']['Storage Location Details']['storage_type'] = 'Reserve';
                $auditTrail['action_data']['Storage Location Details']['storageLocationTypeSlug'] = 'rmWhReserve';
                $auditTrail['action_data']['Storage Location Details']['control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['temp_control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['created_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['updated_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['visibility'] = 'hide';
                $auditTrail['action_data']['Storage Location Details']['status'] = 'active';

                $auditTrailreturn = generateAuditTrail($auditTrail);
            }



            //----------------------SL3
            $slcode = $this->storageLocationCode($SLLastId, 3);
            $storageLocationsql = "INSERT INTO `" . ERP_STORAGE_LOCATION . "` 
                    SET
                        `company_id`='" .  $company_id  . "',
                        `branch_id`='" . $branch_id . "',
                        `location_id`='" . $location_id . "',
                        `warehouse_id`='" . $warehouse_id . "',
                        `storage_location_code`='" . $slcode . "',
                        `storage_location_name`='RM PRODUCTION Open',
                        `storage_location_type`= 'RM-PROD',
                        `storage_location_material_type`= 'RM',
                        `storage_location_storage_type`= 'Open',
                        `storageLocationTypeSlug`= 'rmProdOpen',
                        `storage_control`= '0', 
                        `temp_control`= '0',
                        `created_by`='" . $created_by . "',
                        `updated_by`='" . $updated_by . "', 
                        `status`='active'
                        ";

            $storageLocation = queryInsert($storageLocationsql);
            if ($storageLocation['status'] == 'success') {
                $storage_location_id = $storageLocation['insertedId'];
                ///---------------------------------Audit Log Start---------------------
                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_STORAGE_LOCATION;
                $auditTrail['basicDetail']['column_name'] = 'storage_location_id'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $storage_location_id;  // primary key
                $auditTrail['basicDetail']['document_number'] =  $slcode;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['party_id'] = 0;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = 'New Storage Location added';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($storageLocationsql);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';

                $auditTrail['action_data']['Storage Location Details']['code'] = $slcode;
                $auditTrail['action_data']['Storage Location Details']['name'] = 'RM PRODUCTION Open';
                $auditTrail['action_data']['Storage Location Details']['type'] = 'RM-PROD';
                $auditTrail['action_data']['Storage Location Details']['material_type'] = 'RM';
                $auditTrail['action_data']['Storage Location Details']['storage_type'] = 'Open';
                $auditTrail['action_data']['Storage Location Details']['storageLocationTypeSlug'] = 'rmProdOpen';
                $auditTrail['action_data']['Storage Location Details']['control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['temp_control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['created_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['updated_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['visibility'] = 'show';
                $auditTrail['action_data']['Storage Location Details']['status'] = 'active';

                $auditTrailreturn = generateAuditTrail($auditTrail);
            }



            //----------------------SL4
            $slcode = $this->storageLocationCode($SLLastId, 4);
            $storageLocationsql = "INSERT INTO `" . ERP_STORAGE_LOCATION . "` 
                    SET
                        `company_id`='" .  $company_id  . "',
                        `branch_id`='" . $branch_id . "',
                        `location_id`='" . $location_id . "',
                        `warehouse_id`='" . $warehouse_id . "',
                        `storage_location_code`='" . $slcode . "',
                        `storage_location_name`='RM PRODUCTION Reserve',
                        `storage_location_type`= 'RM-PROD',
                        `storage_location_material_type`= 'RM',
                        `storage_location_storage_type`= 'Reserve',
                        `storageLocationTypeSlug`= 'rmProdReserve',
                        `storage_control`= '0', 
                        `temp_control`= '0',
                        `created_by`='" . $created_by . "',
                        `updated_by`='" . $updated_by . "', 
                        `status`='active'
                        ";

            $storageLocation = queryInsert($storageLocationsql);
            if ($storageLocation['status'] == 'success') {
                $storage_location_id = $storageLocation['insertedId'];
                ///---------------------------------Audit Log Start---------------------
                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_STORAGE_LOCATION;
                $auditTrail['basicDetail']['column_name'] = 'storage_location_id'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $storage_location_id;  // primary key
                $auditTrail['basicDetail']['document_number'] =  $slcode;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['party_id'] = 0;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = 'New Storage Location added';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($storageLocationsql);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';

                $auditTrail['action_data']['Storage Location Details']['code'] = $slcode;
                $auditTrail['action_data']['Storage Location Details']['name'] = 'RM PRODUCTION Reserve';
                $auditTrail['action_data']['Storage Location Details']['type'] = 'RM-PROD';
                $auditTrail['action_data']['Storage Location Details']['material_type'] = 'RM';
                $auditTrail['action_data']['Storage Location Details']['storage_type'] = 'Reserve';
                $auditTrail['action_data']['Storage Location Details']['storageLocationTypeSlug'] = 'rmProdReserve';
                $auditTrail['action_data']['Storage Location Details']['control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['temp_control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['created_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['updated_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['visibility'] = 'hide';
                $auditTrail['action_data']['Storage Location Details']['status'] = 'active';


                $auditTrailreturn = generateAuditTrail($auditTrail);
            }


            //----------------------SL5
            $slcode = $this->storageLocationCode($SLLastId, 5);
            $storageLocationsql = "INSERT INTO `" . ERP_STORAGE_LOCATION . "` 
                    SET
                        `company_id`='" .  $company_id  . "',
                        `branch_id`='" . $branch_id . "',
                        `location_id`='" . $location_id . "',
                        `warehouse_id`='" . $warehouse_id . "',
                        `storage_location_code`='" . $slcode . "',
                        `storage_location_name`='SFG STOCK Open',
                        `storage_location_type`= 'SFG-STOCK',
                        `storage_location_material_type`= 'SFG',
                        `storage_location_storage_type`= 'Open',
                        `storageLocationTypeSlug`= 'sfgStockOpen',
                        `storage_control`= '0', 
                        `temp_control`= '0',
                        `created_by`='" . $created_by . "',
                        `updated_by`='" . $updated_by . "', 
                        `status`='active'
                        ";

            $storageLocation = queryInsert($storageLocationsql);
            if ($storageLocation['status'] == 'success') {
                $storage_location_id = $storageLocation['insertedId'];
                ///---------------------------------Audit Log Start---------------------
                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_STORAGE_LOCATION;
                $auditTrail['basicDetail']['column_name'] = 'storage_location_id'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $storage_location_id;  // primary key
                $auditTrail['basicDetail']['document_number'] =  $slcode;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['party_id'] = 0;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = 'New Storage Location added';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($storageLocationsql);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';

                $auditTrail['action_data']['Storage Location Details']['code'] = $slcode;
                $auditTrail['action_data']['Storage Location Details']['name'] = 'SFG STOCK Open';
                $auditTrail['action_data']['Storage Location Details']['type'] = 'SFG-STOCK';
                $auditTrail['action_data']['Storage Location Details']['material_type'] = 'SFG';
                $auditTrail['action_data']['Storage Location Details']['storage_type'] = 'Open';
                $auditTrail['action_data']['Storage Location Details']['storageLocationTypeSlug'] = 'sfgStockOpen';
                $auditTrail['action_data']['Storage Location Details']['control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['temp_control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['created_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['updated_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['visibility'] = 'show';
                $auditTrail['action_data']['Storage Location Details']['status'] = 'active';


                $auditTrailreturn = generateAuditTrail($auditTrail);
            }


            //----------------------SL6
            $slcode = $this->storageLocationCode($SLLastId, 6);
            $storageLocationsql = "INSERT INTO `" . ERP_STORAGE_LOCATION . "` 
                    SET
                        `company_id`='" .  $company_id  . "',
                        `branch_id`='" . $branch_id . "',
                        `location_id`='" . $location_id . "',
                        `warehouse_id`='" . $warehouse_id . "',
                        `storage_location_code`='" . $slcode . "',
                        `storage_location_name`='SFG Stock Reserve',
                        `storage_location_type`= 'SFG-STOCK',
                        `storage_location_material_type`= 'SFG',
                        `storage_location_storage_type`= 'Reserve',
                        `storageLocationTypeSlug`= 'sfgStockReserve',
                        `storage_control`= '0', 
                        `temp_control`= '0',
                        `created_by`='" . $created_by . "',
                        `updated_by`='" . $updated_by . "', 
                        `status`='active'
                        ";

            $storageLocation = queryInsert($storageLocationsql);
            if ($storageLocation['status'] == 'success') {
                $storage_location_id = $storageLocation['insertedId'];
                ///---------------------------------Audit Log Start---------------------
                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_STORAGE_LOCATION;
                $auditTrail['basicDetail']['column_name'] = 'storage_location_id'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $storage_location_id;  // primary key
                $auditTrail['basicDetail']['document_number'] =  $slcode;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['party_id'] = 0;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = 'New Storage Location added';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($storageLocationsql);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';

                $auditTrail['action_data']['Storage Location Details']['code'] = $slcode;
                $auditTrail['action_data']['Storage Location Details']['name'] = 'SFG STOCK Reserve';
                $auditTrail['action_data']['Storage Location Details']['type'] = 'SFG-STOCK';
                $auditTrail['action_data']['Storage Location Details']['material_type'] = 'SFG';
                $auditTrail['action_data']['Storage Location Details']['storage_type'] = 'Reserve';
                $auditTrail['action_data']['Storage Location Details']['storageLocationTypeSlug'] = 'sfgStockReserve';
                $auditTrail['action_data']['Storage Location Details']['control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['temp_control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['created_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['updated_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['visibility'] = 'hide';
                $auditTrail['action_data']['Storage Location Details']['status'] = 'active';


                $auditTrailreturn = generateAuditTrail($auditTrail);
            }


            //----------------------SL7
            $slcode = $this->storageLocationCode($SLLastId, 7);
            $storageLocationsql = "INSERT INTO `" . ERP_STORAGE_LOCATION . "` 
                    SET
                        `company_id`='" .  $company_id  . "',
                        `branch_id`='" . $branch_id . "',
                        `location_id`='" . $location_id . "',
                        `warehouse_id`='" . $warehouse_id . "',
                        `storage_location_code`='" . $slcode . "',
                        `storage_location_name`='FG WH Open',
                        `storage_location_type`= 'FG-WH',
                        `storage_location_material_type`= 'FG',
                        `storage_location_storage_type`= 'Open',
                        `storageLocationTypeSlug`= 'fgWhOpen',
                        `storage_control`= '0', 
                        `temp_control`= '0',
                        `created_by`='" . $created_by . "',
                        `updated_by`='" . $updated_by . "', 
                        `status`='active'
                        ";

            $storageLocation = queryInsert($storageLocationsql);
            if ($storageLocation['status'] == 'success') {
                $storage_location_id = $storageLocation['insertedId'];
                ///---------------------------------Audit Log Start---------------------
                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_STORAGE_LOCATION;
                $auditTrail['basicDetail']['column_name'] = 'storage_location_id'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $storage_location_id;  // primary key
                $auditTrail['basicDetail']['document_number'] =  $slcode;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['party_id'] = 0;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = 'New Storage Location added';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($storageLocationsql);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';

                $auditTrail['action_data']['Storage Location Details']['code'] = $slcode;
                $auditTrail['action_data']['Storage Location Details']['name'] = 'FG WH Open';
                $auditTrail['action_data']['Storage Location Details']['type'] = 'FG-WH';
                $auditTrail['action_data']['Storage Location Details']['material_type'] = 'FG';
                $auditTrail['action_data']['Storage Location Details']['storage_type'] = 'Open';
                $auditTrail['action_data']['Storage Location Details']['storageLocationTypeSlug'] = 'fgWhOpen';
                $auditTrail['action_data']['Storage Location Details']['control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['temp_control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['created_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['updated_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['visibility'] = 'show';
                $auditTrail['action_data']['Storage Location Details']['status'] = 'active';


                $auditTrailreturn = generateAuditTrail($auditTrail);
            }


            //----------------------SL8
            $slcode = $this->storageLocationCode($SLLastId, 8);
            $storageLocationsql = "INSERT INTO `" . ERP_STORAGE_LOCATION . "` 
                    SET
                        `company_id`='" .  $company_id  . "',
                        `branch_id`='" . $branch_id . "',
                        `location_id`='" . $location_id . "',
                        `warehouse_id`='" . $warehouse_id . "',
                        `storage_location_code`='" . $slcode . "',
                        `storage_location_name`='FG WH Reserve',
                        `storage_location_type`= 'FG-WH',
                        `storage_location_material_type`= 'FG',
                        `storage_location_storage_type`= 'Reserve',
                        `storageLocationTypeSlug`= 'fgWhReserve',
                        `storage_control`= '0', 
                        `temp_control`= '0',
                        `created_by`='" . $created_by . "',
                        `updated_by`='" . $updated_by . "', 
                        `status`='active'
                        ";

            $storageLocation = queryInsert($storageLocationsql);
            if ($storageLocation['status'] == 'success') {
                $storage_location_id = $storageLocation['insertedId'];
                ///---------------------------------Audit Log Start---------------------
                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_STORAGE_LOCATION;
                $auditTrail['basicDetail']['column_name'] = 'storage_location_id'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $storage_location_id;  // primary key
                $auditTrail['basicDetail']['document_number'] =  $slcode;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['party_id'] = 0;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = 'New Storage Location added';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($storageLocationsql);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';

                $auditTrail['action_data']['Storage Location Details']['code'] = $slcode;
                $auditTrail['action_data']['Storage Location Details']['name'] = 'FG WH Reserve';
                $auditTrail['action_data']['Storage Location Details']['type'] = 'FG-WH';
                $auditTrail['action_data']['Storage Location Details']['material_type'] = 'FG';
                $auditTrail['action_data']['Storage Location Details']['storage_type'] = 'Reserve';
                $auditTrail['action_data']['Storage Location Details']['storageLocationTypeSlug'] = 'fgWhReserve';
                $auditTrail['action_data']['Storage Location Details']['control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['temp_control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['created_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['updated_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['visibility'] = 'hide';
                $auditTrail['action_data']['Storage Location Details']['status'] = 'active';


                $auditTrailreturn = generateAuditTrail($auditTrail);
            }


            //----------------------SL9
            $slcode = $this->storageLocationCode($SLLastId, 9);
            $storageLocationsql = "INSERT INTO `" . ERP_STORAGE_LOCATION . "` 
                    SET
                        `company_id`='" .  $company_id  . "',
                        `branch_id`='" . $branch_id . "',
                        `location_id`='" . $location_id . "',
                        `warehouse_id`='" . $warehouse_id . "',
                        `storage_location_code`='" . $slcode . "',
                        `storage_location_name`='FG Market Open',
                        `storage_location_type`= 'FG-MKT',
                        `storage_location_material_type`= 'FG',
                        `storage_location_storage_type`= 'Open',
                        `storageLocationTypeSlug`= 'fgMktOpen',
                        `storage_control`= '0', 
                        `temp_control`= '0',
                        `created_by`='" . $created_by . "',
                        `updated_by`='" . $updated_by . "', 
                        `status`='active'
                        ";

            $storageLocation = queryInsert($storageLocationsql);
            if ($storageLocation['status'] == 'success') {
                $storage_location_id = $storageLocation['insertedId'];
                ///---------------------------------Audit Log Start---------------------
                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_STORAGE_LOCATION;
                $auditTrail['basicDetail']['column_name'] = 'storage_location_id'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $storage_location_id;  // primary key
                $auditTrail['basicDetail']['document_number'] =  $slcode;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['party_id'] = 0;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = 'New Storage Location added';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($storageLocationsql);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';

                $auditTrail['action_data']['Storage Location Details']['code'] = $slcode;
                $auditTrail['action_data']['Storage Location Details']['name'] = 'FG Market Open';
                $auditTrail['action_data']['Storage Location Details']['type'] = 'FG-MKT';
                $auditTrail['action_data']['Storage Location Details']['material_type'] = 'FG';
                $auditTrail['action_data']['Storage Location Details']['storage_type'] = 'Open';
                $auditTrail['action_data']['Storage Location Details']['storageLocationTypeSlug'] = 'fgMktOpen';
                $auditTrail['action_data']['Storage Location Details']['control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['temp_control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['created_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['updated_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['visibility'] = 'show';
                $auditTrail['action_data']['Storage Location Details']['status'] = 'active';


                $auditTrailreturn = generateAuditTrail($auditTrail);
            }


            //----------------------SL10
            $slcode = $this->storageLocationCode($SLLastId, 10);
            $storageLocationsql = "INSERT INTO `" . ERP_STORAGE_LOCATION . "` 
                    SET
                        `company_id`='" .  $company_id  . "',
                        `branch_id`='" . $branch_id . "',
                        `location_id`='" . $location_id . "',
                        `warehouse_id`='" . $warehouse_id . "',
                        `storage_location_code`='" . $slcode . "',
                        `storage_location_name`='FG Market Reserve',
                        `storage_location_type`= 'FG-MKT',
                        `storage_location_material_type`= 'FG',
                        `storage_location_storage_type`= 'Reserve',
                        `storageLocationTypeSlug`= 'fgMktReserve',
                        `storage_control`= '0', 
                        `temp_control`= '0',
                        `created_by`='" . $created_by . "',
                        `updated_by`='" . $updated_by . "', 
                        `status`='active'
                        ";

            $storageLocation = queryInsert($storageLocationsql);
            if ($storageLocation['status'] == 'success') {
                $storage_location_id = $storageLocation['insertedId'];
                ///---------------------------------Audit Log Start---------------------
                $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_STORAGE_LOCATION;
                $auditTrail['basicDetail']['column_name'] = 'storage_location_id'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $storage_location_id;  // primary key
                $auditTrail['basicDetail']['document_number'] =  $slcode;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['party_id'] = 0;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = 'New Storage Location added';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($storageLocationsql);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = '';

                $auditTrail['action_data']['Storage Location Details']['code'] = $slcode;
                $auditTrail['action_data']['Storage Location Details']['name'] = 'FG Market Reserve';
                $auditTrail['action_data']['Storage Location Details']['type'] = 'FG-MKT';
                $auditTrail['action_data']['Storage Location Details']['material_type'] = 'FG';
                $auditTrail['action_data']['Storage Location Details']['storage_type'] = 'Reserve';
                $auditTrail['action_data']['Storage Location Details']['storageLocationTypeSlug'] = 'fgMktReserve';
                $auditTrail['action_data']['Storage Location Details']['control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['temp_control'] = '0';
                $auditTrail['action_data']['Storage Location Details']['created_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['updated_by'] = getCreatedByUser($created_by);
                $auditTrail['action_data']['Storage Location Details']['visibility'] = 'hide';
                $auditTrail['action_data']['Storage Location Details']['status'] = 'active';


                $auditTrailreturn = generateAuditTrail($auditTrail);
            }


            if ($flg == 1) {
                $returnData = $warehouse;
                $returnData['sql'] = $ins;
                $returnData['trail'] = $auditTrailreturn1;
            } else {
                $returnData = $warehouse;
                $returnData['message'] = 'Warehouse was successfully created but Storage location creation failed!';
                $returnData['sql'] = $storageLocationsql;
            }
        } else {
            $returnData = $warehouse;
            $returnData['message'] = 'Warehouse creation failed!';
            $returnData['sql'] = $ins;
        }

        return $returnData;
    }
    //---------------------CustomSLSerialNumberStart------------------
    function storageLocationCode($lastsl, $sls)
    {
        $now_prefix = 'SL';
        $old_prefix = substr($lastsl, 0, 2);

        if ($now_prefix == $old_prefix) {
            $sl = substr($lastsl, 4);
        } else {
            $sl = 0;
        }
        $id = $sl + $sls;
        return $invoice_no = 'SL' . str_pad($id, 4, 0, STR_PAD_LEFT);
    }
    //---------------------CustomSLSerialNumberEnd----------------------

    function getAllWarehouse()
    {

        global $dbCon;
        global $company_id;
        $returnData = [];
        $selectSql = "SELECT * FROM `" . ERP_WAREHOUSE . "` WHERE `company_id`=$company_id";

        if ($res = mysqli_query($dbCon, $selectSql)) {
            $returnData['status'] = "success";
            $returnData['message'] = mysqli_num_rows($res) . " records found.";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong, try again";
            $returnData['data'] = [];
        }
        return $returnData;
    }


    function editWarehouse($POST, $branch_id, $company_id, $location_id, $created_by)
    {



        $isValidate = validate($POST, [
            "name" => "required",
            "address" => "required",
            "description" => "required",
            "lat" => "required",
            "lng" => "required",



        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }



        $warehouse_id = $_POST['warehouse_id'];

        //getWHSerialNumber($lastsl)
        $admin = array();
        $admin["name"] = $POST["name"];
        $admin["address"] = $POST["address"];
        $admin["description"] = $POST["description"];
        $admin["lat"] = $POST["lat"];
        $admin["lng"] = $POST["lng"];

        $status = $POST[" "] == "add_draft" ? "draft" : "active";

        $ins = "UPDATE `" . ERP_WAREHOUSE . "` 
                    SET
                        `warehouse_name`='" .  $admin["name"] . "',
                        `warehouse_description`='" . $admin["description"] . "',
                        `warehouse_address`='" . $admin["address"] . "', 
                        `warehouse_lat`='" . $admin["lat"] . "',
                        `warehouse_lng`='" . $admin["lng"] . "',
                        `company_id`='" .  $company_id  . "',
                        `branch_id`='" . $branch_id . "',
                        `location_id`='" . $location_id . "',
                        `status`='" . $status . "',
                        `created_by`='" . $created_by . "' WHERE `warehouse_id`='" . $warehouse_id . "'
                        ";
        $warehouse = queryUpdate($ins);
        if ($warehouse['status'] == 'success') {


            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'EDIT';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_WAREHOUSE;
            $auditTrail['basicDetail']['column_name'] = 'warehouse_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $warehouse_id;  // primary key
            $auditTrail['basicDetail']['document_number'] = $POST['warehouse_code'];
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['party_id'] = 0;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Update warehouse';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Update';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($ins);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['Warehouse Details']['name'] = $admin["name"];
            $auditTrail['action_data']['Warehouse Details']['description'] = $admin["description"];
            $auditTrail['action_data']['Warehouse Details']['address'] = $admin["address"];
            $auditTrail['action_data']['Warehouse Details']['lat'] = $admin["lat"];
            $auditTrail['action_data']['Warehouse Details']['lng'] = $admin["lng"];
            $auditTrail['action_data']['Warehouse Details']['status'] = $status;
            $auditTrail['action_data']['Warehouse Details']['created_by'] = getCreatedByUser($created_by);

            $auditTrailreturn = generateAuditTrail($auditTrail);
        }
        $returnData = $warehouse;
        return $returnData;
    }

    function createStorageLocation($POST, $branch_id, $company_id, $location_id, $created_by)
    {

        $isValidate = validate($POST, [
            "name" => "required",
            "warehouse" => "required",
            "storage_control" => "required",
            "temp" => "required",
            "sl_type" => "required"


        ], [
            "name" => "Enter Storage Location Name",
            "warehouse" => "Select Warehouse",
            "storage_control" => "Enter storage_control",
            "temp" => "Enter temp",
            "sl_type" => "Enter storage location type"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        $lastQuery = "SELECT * FROM `" . ERP_STORAGE_LOCATION . "` WHERE company_id=$company_id ORDER BY `storage_location_id` DESC LIMIT 1";
        $last = queryGet($lastQuery);
        $lastRow = $last['data'] ?? 0;
        $lastid = $lastRow['storage_location_code'] ?? 0;
        $returnSlCode = getSLSerialNumber($lastid);
        // getSLSerialNumber($lastsl)
        //console($returnSlCode);
        //exit();
        $admin = array();
        $admin["name"] = $POST["name"];
        $admin["warehouse"] = $POST["warehouse"];
        $admin["storage_control"] = $POST["storage_control"];
        $admin["temp"] = $POST["temp"];
        $sl_type = $POST['sl_type'];
        $explodes = explode('||', $sl_type);

        $storage_location_type = $explodes[0];
        $storage_location_material_type = $explodes[1];
        $storage_location_storage_type = $explodes[2];
        $storageLocationTypeSlug = $explodes[3];

        $status = "active";


        $storageLocationsql = "INSERT INTO `" . ERP_STORAGE_LOCATION . "` 
        SET
            `company_id`='" .  $company_id  . "',
            `branch_id`='" . $branch_id . "',
            `location_id`='" . $location_id . "',
            `warehouse_id`='" . $admin["warehouse"] . "',
            `storage_location_code`= '" . $returnSlCode . "', 
            `storage_location_name`='" .  $admin["name"] . "',
            `storage_location_type` ='" . $storage_location_type . "',
            `storage_location_material_type` ='" . $storage_location_material_type . "',
            `storage_location_storage_type` ='" . $storage_location_storage_type . "',
            `storageLocationTypeSlug` ='" . $storageLocationTypeSlug . "',
            `storage_control`='" . $admin["storage_control"] . "',
            `temp_control`='" . $admin["temp"] . "',
            `created_by`='" . $created_by . "',
            `updated_by` ='" . $created_by . "',
            `status`='" . $status . "'
            ";

        $storageLocation = queryInsert($storageLocationsql);

        if ($storageLocation['status'] == 'success') {
            $storage_location_id = $storageLocation['insertedId'];
            $whName = queryGet("SELECT  w.`warehouse_name`  AS warehouse_name
            FROM    `erp_storage_location` AS sl 
            JOIN    `erp_storage_warehouse` AS w 
            ON      sl.`warehouse_id` = w.`warehouse_id`
            WHERE   sl.`storage_location_id` = $storage_location_id")['data'];
            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_STORAGE_LOCATION;
            $auditTrail['basicDetail']['column_name'] = 'storage_location_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $storage_location_id;  // primary key
            $auditTrail['basicDetail']['document_number'] =  $returnSlCode;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['party_id'] = 0;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'New Storage Location added';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($storageLocationsql);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['Storage Location Details']['code'] = $returnSlCode;
            $auditTrail['action_data']['Storage Location Details']['name'] = $admin["name"];
            $auditTrail['action_data']['Storage Location Details']['Warehouse name'] = $whName['warehouse_name'];
            $auditTrail['action_data']['Storage Location Details']['type'] = $storage_location_type;
            $auditTrail['action_data']['Storage Location Details']['material_type'] = $storage_location_material_type;
            $auditTrail['action_data']['Storage Location Details']['storage_type'] = $storage_location_storage_type;
            $auditTrail['action_data']['Storage Location Details']['storageLocationTypeSlug'] = $storageLocationTypeSlug;
            $auditTrail['action_data']['Storage Location Details']['control'] = $admin["storage_control"];
            $auditTrail['action_data']['Storage Location Details']['temp_control'] = $admin["temp"];
            $auditTrail['action_data']['Storage Location Details']['created_by'] = getCreatedByUser($created_by);
            $auditTrail['action_data']['Storage Location Details']['updated_by'] = getCreatedByUser($created_by);
            // $auditTrail['action_data']['Storage Location Details']['visibility'] = 'hide';
            $auditTrail['action_data']['Storage Location Details']['status'] = 'active';


            $auditTrailreturn = generateAuditTrail($auditTrail);
        }

        $returnData = $storageLocation;
        return $returnData;
    }


    function editStorageLocation($POST, $branch_id, $company_id, $location_id, $created_by)
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $isValidate = validate($POST, [
            "name" => "required",
            "warehouse" => "required",
            "storage_control" => "required",
            "temp" => "required",


        ], [
            "name" => "Enter Storage Name",
            "warehouse" => "Selecte Warehouse",
            "storage_control" => "Enter Storage Controler",
            "temp" => "Enter Temp",

        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }
        $storage_location_id = $_POST['storage_location_id'];
        // getSLSerialNumber($lastsl)
        //console($returnSlCode);
        //exit();
        $lastQuery = queryGet("SELECT * FROM `" . ERP_STORAGE_LOCATION . "`WHERE `storage_location_id`=$storage_location_id and `company_id`=$company_id and `branch_id`=$branch_id and `location_id`=$location_id")['data'];
        $storage_location_type = $lastQuery['storage_location_type'];
        $storage_location_material_type = $lastQuery['storage_location_material_type'];
        $storage_location_storage_type = $lastQuery['storage_location_type'];
        $storageLocationTypeSlug = $lastQuery['storageLocationTypeSlug'];
        $storage_control = $lastQuery['storage_control'];


        $admin = array();
        $admin["name"] = $POST["name"];
        $admin["warehouse"] = $POST["warehouse"];
        $admin["storage_control"] = $POST["storage_control"];
        $admin["temp"] = $POST["temp"];

        $status = $POST["editStorageLocation"] == "add_draft" ? "draft" : "active";


        $ins = "UPDATE `" . ERP_STORAGE_LOCATION . "` 
        SET
            `storage_location_name`='" .  $admin["name"] . "',
            `warehouse_id`='" . $admin["warehouse"] . "',
            `storage_control`='" . $admin["storage_control"] . "',
            `temp_control`='" . $admin["temp"] . "',
            `company_id`='" .  $company_id  . "',
            `branch_id`='" . $branch_id . "',
            `location_id`='" . $location_id . "',
            `status`='" . $status . "',
            `created_by`='" . $created_by . "'
            WHERE `storage_location_id`='" . $storage_location_id . "'
            ";
        $insertItem = queryUpdate($ins);
        if ($insertItem['status'] == 'success') {
            $whName = queryGet("SELECT  w.`warehouse_name`  AS warehouse_name
                FROM    `erp_storage_location` AS sl 
                JOIN    `erp_storage_warehouse` AS w 
                ON      sl.`warehouse_id` = w.`warehouse_id`
                WHERE   sl.`storage_location_id` = $storage_location_id;")['data'];
            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'EDIT';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_STORAGE_LOCATION;
            $auditTrail['basicDetail']['column_name'] = 'storage_location_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $storage_location_id;  // primary key
            $auditTrail['basicDetail']['document_number'] = $POST['storage_location_code'];
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['party_id'] = 0;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Update storage location';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Update';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($ins);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['Storage Location Details']['code'] = $POST['storage_location_code'];
            $auditTrail['action_data']['Storage Location Details']['name'] = $admin["name"];
            $auditTrail['action_data']['Storage Location Details']['Warehouse name'] = $whName['warehouse_name'];
            $auditTrail['action_data']['Storage Location Details']['type'] = $storage_location_type;
            $auditTrail['action_data']['Storage Location Details']['material_type'] = $storage_location_material_type;
            $auditTrail['action_data']['Storage Location Details']['storage_type'] = $storage_location_type;
            $auditTrail['action_data']['Storage Location Details']['storageLocationTypeSlug'] = $storageLocationTypeSlug;
            $auditTrail['action_data']['Storage Location Details']['control'] = $admin["storage_control"];
            $auditTrail['action_data']['Storage Location Details']['temp_control'] = $admin["temp"];
            $auditTrail['action_data']['Storage Location Details']['created_by'] = getCreatedByUser($created_by);
            $auditTrail['action_data']['Storage Location Details']['updated_by'] = getCreatedByUser($created_by);
            // $auditTrail['action_data']['Storage Location Details']['visibility'] = 'hide';
            $auditTrail['action_data']['Storage Location Details']['status'] = 'active';


            $auditTrailreturn = generateAuditTrail($auditTrail);
        }

        $returnData = $insertItem;
        return $returnData;
    }




    function getAllSL()
    {


        global $dbCon;
        global $company_id;
        global $branch_id;
        global $location_id;
        $returnData = [];
        $selectSql = "SELECT * FROM `" . ERP_STORAGE_LOCATION . "` as sl, `" . ERP_WAREHOUSE . "` as warehouse WHERE sl.warehouse_id=warehouse.warehouse_id AND sl.company_id=$company_id AND sl.branch_id=$branch_id AND sl.location_id=$location_id AND storage_location_storage_type='Reserve' ";

        if ($res = mysqli_query($dbCon, $selectSql)) {
            $returnData['status'] = "success";
            $returnData['message'] = mysqli_num_rows($res) . " records found.";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Something went wrong, try again";
            $returnData['data'] = [];
        }
        return $returnData;
    }

    function createBin($POST, $branch_id, $company_id, $location_id, $created_by)
    {
    
        $isValidate = validate($POST, [
            "name" => "required",
            "layer" => "required",
            "max_temp" => "required",
            "min_temp" => "required",
            "max_weight" => "required",
            "max_vol" => "required",
            "spec1" => "required",
            "spec2" => "required",
            "spec3" => "required",
        ], [
            "name" => "Enter Bin Name",
            "layer" => "Select Layer",
            "max_temp" => "Enter Maximum Temperature",
            "min_temp" => "Enter Minimum Temperature",
            "max_weight" => "Enter Maximum weight capacity",
            "max_vol" => "Enter Maximun volume capacity",
            "spec1" => "Enter Spec 1",
            "spec2" => "Enter Spec 2",
            "spec3" => "Enter Spec 3",
        ]);
        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        $lastQuery = "SELECT * FROM `" . ERP_BIN . "` ORDER BY `bin_id` DESC LIMIT 1";
        $last = queryGet($lastQuery);
        $lastRow = $last['data'] ?? "";
        $lastid = $lastRow['bin_code'] ?? 0;
        $returnBinCode = getBinSerialNumber($lastid);
        $admin = array();
        $admin["name"] = $POST["name"];
        $admin["layer"] = $POST["layer"];
        $admin["max_temp"] = $POST["max_temp"];
        $admin["min_temp"] = $POST["min_temp"];
        $admin["max_weight"] = $POST["max_weight"];
        $admin["max_vol"] = $POST["max_vol"];
        $admin["spec1"] = $POST["spec1"];
        $admin["spec2"] = $POST["spec2"];
        $admin["spec3"] = $POST["spec3"];

        $status = "active";

        $ins = "INSERT INTO `" . ERP_BIN . "` 
        SET
            `bin_name`='" .  $admin["name"] . "',
            `layer_id`='" .  $admin["layer"] . "',
            `max_temperature`='" . $admin["max_temp"] . "',
            `min_temperature`='" . $admin["min_temp"] . "',
            `max_weight`='" . $admin["max_weight"] . "',
            `max_volume`='" . $admin["max_vol"] . "',
            `spec_one`='" .  $admin["spec1"]  . "',
            `spec_two`='" .  $admin["spec2"]  . "',
            `spec_three`='" .  $admin["spec3"]  . "',
            `bin_code`= '" . $returnBinCode . "', 
            `created_by`='" . $created_by . "',
            `updated_by` ='" . $created_by . "',
            `company_id`='" . $company_id . "',
            `branch_id`='" . $branch_id . "',
            `location_id`='" . $location_id . "',
            `status`='" . $status . "'
            ";
        $insertBin = queryInsert($ins);
        if ($insertBin['status'] == 'success') {
            $bin_id = $insertBin['insertedId'];
            $layer_id = $admin["layer"];
            $storage_data = queryGet("SELECT  l.`layer_name` AS Layer_Name,r.`rack_name` , sl.`storage_location_name` AS storage_location, w.`warehouse_name`  AS warehouse_name FROM    `erp_layer` AS l
                                                    JOIN    `erp_rack` AS r
                                                    ON      l.`rack_id`=r.`rack_id`
                                                    JOIN    `erp_storage_location` AS sl
                                                    ON      r.`storage_location_id`=sl.`storage_location_id`
                                                    JOIN    `erp_storage_warehouse` AS w
                                                    ON      sl.`warehouse_id`=w.`warehouse_id`
                                                    WHERE   l.`layer_id`= $layer_id");
            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_BIN;
            $auditTrail['basicDetail']['column_name'] = 'bin_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $bin_id;  // primary key
            $auditTrail['basicDetail']['document_number'] =  $returnBinCode;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['party_id'] = 0;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'New Bin Location added';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($ins);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';
            $auditTrail['action_data']['bin Location Details']['code'] = $returnBinCode;
            $auditTrail['action_data']['bin Location Details']['bin_name'] = $admin["name"];
            $auditTrail['action_data']['bin Location Details']['storage location'] = $storage_data['data'];
            $auditTrail['action_data']['bin Location Details']['max_temperature'] = $admin["max_temp"];
            $auditTrail['action_data']['bin Location Details']['min_temperature'] = $admin["min_temp"];
            $auditTrail['action_data']['bin Location Details']['max_weight'] = decimalQuantityPreview($admin["max_weight"]);
            $auditTrail['action_data']['bin Location Details']['max_volume'] = decimalQuantityPreview($admin["max_vol"]);
            $auditTrail['action_data']['bin Location Details']['spec_one'] = $admin["spec1"];
            $auditTrail['action_data']['bin Location Details']['spec_two'] = $admin["spec2"];
            $auditTrail['action_data']['bin Location Details']['spec_three'] = $admin["spec3"];
            $auditTrail['action_data']['bin Location Details']['created_by'] = getCreatedByUser($created_by);
            $auditTrail['action_data']['bin Location Details']['updated_by'] = getCreatedByUser($created_by);
            $auditTrail['action_data']['bin Location Details']['visibility'] = 'hide';
            $auditTrail['action_data']['bin Location Details']['status'] = 'active';

            $auditTrailreturn = generateAuditTrail($auditTrail);
        }
        $returnData = $insertBin;
        return $returnData;
    }




    function editBin($POST, $branch_id, $company_id, $location_id, $created_by)
    {

        $isValidate = validate($POST, [
            "name" => "required",
            "layer" => "required",
            "max_temp" => "required",
            "min_temp" => "required",
            "max_weight" => "required",
            "max_vol" => "required",
            "spec1" => "required",
            "spec2" => "required",
            "spec3" => "required",
        ], [
            "name" => "Enter Bin Name",
            "layer" => "Select Layer",
            "max_temp" => "Enter Maximum Temperature",
            "min_temp" => "Enter Minimum Temperature",
            "max_weight" => "Enter Maximum weight capacity",
            "max_vol" => "Enter Maximun volume capacity",
            "spec1" => "Enter Spec 1",
            "spec2" => "Enter Spec 2",
            "spec3" => "Enter Spec 3",
        ]);
        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }


        $bin_id = $POST['editBin'];

        $status = "active";

        // $lastQuery = "SELECT * FROM `" . ERP_BIN . "` ORDER BY `bin_id` DESC LIMIT 1";
        // $last = queryGet($lastQuery);
        // $lastRow = $last['data'] ?? "";
        // $lastid = $lastRow['bin_code'] ?? 0;
        // $returnBinCode = getSLSerialNumber($lastid);
        $admin = array();
        $admin["name"] = $POST["name"];
        $admin["layer"] = $POST["layer"];
        $admin["max_temp"] = $POST["max_temp"];
        $admin["min_temp"] = $POST["min_temp"];
        $admin["max_weight"] = $POST["max_weight"];
        $admin["max_vol"] = $POST["max_vol"];
        $admin["spec1"] = $POST["spec1"];
        $admin["spec2"] = $POST["spec2"];
        $admin["spec3"] = $POST["spec3"];

        $ins = "UPDATE `" . ERP_BIN . "` 
        SET
            `bin_name`='" .  $admin["name"] . "',
            `layer_id`='" .  $admin["layer"] . "',
            `max_temperature`='" . $admin["max_temp"] . "',
            `min_temperature`='" . $admin["min_temp"] . "',
            `max_weight`='" . $admin["max_weight"] . "',
            `max_volume`='" . $admin["max_vol"] . "',
            `spec_one`='" .  $admin["spec1"]  . "',
            `spec_two`='" .  $admin["spec2"]  . "',
            `spec_three`='" .  $admin["spec3"]  . "',
            `created_by`='" . $created_by . "',
            `updated_by` ='" . $created_by . "',
            `company_id`='" . $company_id . "',
            `branch_id`='" . $branch_id . "',
            `location_id`='" . $location_id . "',
            `status`='" . $status . "'
            WHERE `bin_id`='" . $bin_id . "'
            ";
        $updatetBin = queryInsert($ins);
        if ($updatetBin['status'] == 'success') {
            $layer_id = $admin["layer"];
            $storage_data = queryGet("SELECT  l.`layer_name` AS Layer_Name,r.`rack_name` , sl.`storage_location_name` AS storage_location, w.`warehouse_name`  AS warehouse_name FROM    `erp_layer` AS l
                                                    JOIN    `erp_rack` AS r
                                                    ON      l.`rack_id`=r.`rack_id`
                                                    JOIN    `erp_storage_location` AS sl
                                                    ON      r.`storage_location_id`=sl.`storage_location_id`
                                                    JOIN    `erp_storage_warehouse` AS w
                                                    ON      sl.`warehouse_id`=w.`warehouse_id`
                                                    WHERE   l.`layer_id`= $layer_id ");
            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'EDIT';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = ERP_BIN;
            $auditTrail['basicDetail']['column_name'] = 'bin_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $bin_id;  // primary key
            $auditTrail['basicDetail']['document_number'] =  $POST['editBinCode'];
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['party_id'] = 0;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Bin Updated';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($ins);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['bin Location Details']['code'] =  $POST['editBinCode'];
            $auditTrail['action_data']['bin Location Details']['bin_name'] = $admin["name"];
            $auditTrail['action_data']['bin Location Details']['storage location'] = $storage_data['data'];
            $auditTrail['action_data']['bin Location Details']['max_temperature'] = $admin["max_temp"];
            $auditTrail['action_data']['bin Location Details']['min_temperature'] = $admin["min_temp"];
            $auditTrail['action_data']['bin Location Details']['max_weight'] = $admin["max_weight"];
            $auditTrail['action_data']['bin Location Details']['max_volume'] = $admin["max_vol"];
            $auditTrail['action_data']['bin Location Details']['spec_one'] = $admin["spec1"];
            $auditTrail['action_data']['bin Location Details']['spec_two'] = $admin["spec2"];
            $auditTrail['action_data']['bin Location Details']['spec_three'] = $admin["spec3"];
            $auditTrail['action_data']['bin Location Details']['created_by'] = getCreatedByUser($created_by);
            $auditTrail['action_data']['bin Location Details']['updated_by'] = getCreatedByUser($created_by);
            // $auditTrail['action_data']['bin Location Details']['visibility'] = 'hide';
            $auditTrail['action_data']['bin Location Details']['status'] = 'active';


            $auditTrailreturn = generateAuditTrail($auditTrail);
        }
        $returnData = $auditTrailreturn;

        return $returnData;
    }

    function getAllStorageLocation()
    {

        global $company_id;

        $sl = queryGet("SELECT * FROM `erp_storage_location` WHERE `company_id` = $company_id", true);
        return $sl;
    }

    function createRack($POST)
    {
        global $company_id;
        global $location_id;
        global $created_by;
        global $branch_id;
        global $updated_by;
        $returnData = [];

        $isValidate = validate($POST, [
            "name" => "required",
            "sl" => "required",
            "rack_desc" => "required",
        ], [
            "name" => "Enter Rack Name",
            "sl" => "Select Storage Location",

            "rack_desc" => "Enter Rack Description"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }
        $lastQuery = "SELECT * FROM `erp_rack` WHERE company_id=$company_id  ORDER BY `rack_id` DESC LIMIT 1";
        $last = queryGet($lastQuery);
        $lastRow = $last['data'] ?? "";
        $lastid = $lastRow['rack_code'] ?? 'R0000';
        $returnRackCode = $this->getRackSerialNumber($lastid);

        $sl = $POST['sl'];
        $name = $POST['name'];
        $rack_desc = $POST['rack_desc'];

        $insert_qry = "INSERT INTO `erp_rack` SET `rack_name`='" . $name . "', `rack_description`='" . $rack_desc . "' , `storage_location_id`= $sl, `rack_code`='" . $returnRackCode . "',`created_by`='" . $created_by . "',`updated_by`='" . $updated_by . "',`company_id`=$company_id,`branch_id`=$branch_id,`location_id`=$location_id";
        $insert = queryInsert($insert_qry);
        //console($insert);
        if (isset($insert['status']) == "success") {
            $sl_name = queryGet("SELECT  sl.`storage_location_name` AS storage_location, w.`warehouse_name`  AS warehouse_name
                                 FROM    `erp_storage_location` AS sl 
                                 JOIN    `erp_storage_warehouse` AS w 
                                 ON      sl.`warehouse_id` = w.`warehouse_id`
                                 WHERE   sl.`storage_location_id` = $sl;");
            $rack_id = $insert['insertedId'];
            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = 'erp_rack';
            $auditTrail['basicDetail']['column_name'] = 'rack_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $rack_id;  // primary key
            $auditTrail['basicDetail']['document_number'] =   $returnRackCode;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['party_id'] = 0;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'New Rack added';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert_qry);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['Rack Details']['rack_name'] = $name;
            $auditTrail['action_data']['Rack Details']['rack_description'] = $rack_desc;
            $auditTrail['action_data']['Rack Details']['Storage_location'] = $sl_name['data'];
            $auditTrail['action_data']['Rack Details']['created_by'] = getCreatedByUser($created_by);
            $auditTrail['action_data']['Rack Details']['updated_by'] = getCreatedByUser($updated_by);


            $auditTrailreturn = generateAuditTrail($auditTrail);

            $returnData['status'] = "Success";
            $returnData['message'] = "Rack Created Successfully";
            $returnData['trail'] = $auditTrailreturn;
        } else {

            $returnData['status'] = "Warning";
            $returnData['message'] = "Something Went Wrong";
        }
        return $returnData;
    }

    function editManageRack($POST)
    {
        global $company_id;
        global $location_id;
        global $created_by;
        global $branch_id;
        global $updated_by;
        $returnEditData = [];

        $isValidate = validate($POST, [
            "name" => "required",
            "sl" => "required",
            "rack_desc" => "required",
        ]);

        if ($isValidate["status"] != "success") {
            $returnEditData['status'] = "warning";
            $returnEditData['message'] = "Invalid form inputes";
            $returnEditData['errors'] = $isValidate["errors"];
            return $returnEditData;
        }

        $rack_id = $_POST['rack_id'];
        $sl = $POST['sl'];
        $name = $POST['name'];
        $rack_desc = $POST['rack_desc'];

        $status = $POST["editManageRack"] == "add_draft" ? "draft" : "active";

        $ins = "UPDATE `erp_rack` 
                       SET `rack_name`='" . $name . "', 
                           `rack_description`='" . $rack_desc . "', 
                           `storage_location_id`= $sl, 
                           `created_by`='" . $created_by . "',
                           `updated_by`='" . $updated_by . "',
                           `company_id`=$company_id,
                           `branch_id`=$branch_id,
                           `location_id`=$location_id
                       WHERE `rack_id` = $rack_id";
        $insertItem = queryUpdate($ins);

        if (isset($insertItem['status']) == "success") {
            $sl_name = queryGet("SELECT  sl.`storage_location_name` AS storage_location, w.`warehouse_name`  AS warehouse_name
                                 FROM    `erp_storage_location` AS sl 
                                 JOIN    `erp_storage_warehouse` AS w 
                                 ON      sl.`warehouse_id` = w.`warehouse_id`
                                 WHERE   sl.`storage_location_id` = $sl;");
            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'EDIT';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = 'erp_rack';
            $auditTrail['basicDetail']['column_name'] = 'rack_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $rack_id;  // primary key
            $auditTrail['basicDetail']['document_number'] = $POST['rack_code'];
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['party_id'] = 0;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Update Rack';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'edit';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($ins);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['Rack Details']['rack_name'] = $name;
            $auditTrail['action_data']['Rack Details']['rack_description'] = $rack_desc;
            $auditTrail['action_data']['Rack Details']['Storage_location'] = $sl_name['data'];
            $auditTrail['action_data']['Rack Details']['created_by'] = getCreatedByUser($created_by);
            $auditTrail['action_data']['Rack Details']['updated_by'] = getCreatedByUser($updated_by);


            $auditTrailreturn = generateAuditTrail($auditTrail);

            $returnEditData['status'] = "Success";
            $returnEditData['message'] = "Rack edited Successfully";
            $returnEditData['trail'] = $auditTrailreturn;
        } else {

            $returnEditData['status'] = "Warning";
            $returnEditData['message'] = "Something Went Wrong";
        }

        // $returnData = $insertItem;
        return $returnEditData;
    }


    function getAllRack()
    {

        global $company_id;

        $sl = queryGet("SELECT * FROM `erp_rack` WHERE `company_id` = $company_id", true);
        return $sl;
    }


    function createLayer($POST)
    {
        global $company_id;
        global $location_id;
        global $created_by;
        global $branch_id;
        global $updated_by;
        $returnData = [];

        $isValidate = validate($POST, [
            "name" => "required",
            "rack" => "required",
            "layer_desc" => "required",



        ], [
            "name" => "Enter Rack Name",
            "rack" => "Select Storage Location",

            "layer_desc" => "Enter Rack Description"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }
        $lastQuery = "SELECT * FROM `erp_layer` WHERE company_id=$company_id  ORDER BY `layer_id` DESC LIMIT 1";
        $last = queryGet($lastQuery);
        $lastRow = $last['data'] ?? "";
        $lastid = $lastRow['layer_code'] ?? 'L0000';
        $returnLayerCode = $this->getLayerSerialNumber($lastid);


        $rack = $POST['rack'];
        $name = $POST['name'];
        $layer_desc = $POST['layer_desc'];

        $insert_sql = "INSERT INTO `erp_layer` SET `layer_name`='" . $name . "', `layer_desc`='" . $layer_desc . "' , `rack_id`= $rack,`layer_code`='" . $returnLayerCode . "', `created_by`='" . $created_by . "',`updated_by`='" . $updated_by . "',`company_id`=$company_id,`branch_id`=$branch_id,`location_id`=$location_id";
        $insert = queryInsert($insert_sql);
        // console($insert);
        if (isset($insert['status']) == "success") {

            $storage_data = queryGet("SELECT  r.`rack_name` , sl.`storage_location_name` AS storage_location, w.`warehouse_name`  AS warehouse_name
                                 FROM    `erp_rack` AS r
                                 JOIN   `erp_storage_location` AS sl
                                 ON      r.`storage_location_id` = sl.`storage_location_id`
                                 JOIN    `erp_storage_warehouse` AS w
                                 ON      sl.`warehouse_id`=w.`warehouse_id`
                                 WHERE   r.`rack_id`=$rack");

            $layer_id = $insert['insertedId'];
            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = 'erp_layer';
            $auditTrail['basicDetail']['column_name'] = 'layer_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $layer_id;  // primary key
            $auditTrail['basicDetail']['document_number'] = $returnLayerCode;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['party_id'] = 0;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'New Layer added';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert_sql);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['Layer Details']['layer_name'] = $name;
            $auditTrail['action_data']['Layer Details']['layer_description'] = $layer_desc;
            $auditTrail['action_data']['Layer Details']['Storage_location'] = $storage_data['data'];
            $auditTrail['action_data']['Layer Details']['created_by'] = getCreatedByUser($created_by);
            $auditTrail['action_data']['Layer Details']['updated_by'] = getCreatedByUser($updated_by);


            $auditTrailreturn = generateAuditTrail($auditTrail);


            $returnData['status'] = "Success";
            $returnData['message'] = "Layer Created Successfully";
        } else {

            $returnData['status'] = "Warning";
            $returnData['message'] = "Something Went Wrong";
        }
        return $returnData;
    }

    function editLayer($POST, $branch_id, $company_id, $location_id, $created_by)
    {

        $isValidate = validate($POST, [
            "name" => "required",
            "rack" => "required",
            "layer_desc" => "required",

        ], [
            "name" => "Enter Layer Name",
            "rack" => "Select Rack",
            "layer_desc" => "Enter Layer Desc",
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        $layer_id = $_POST['editLayer'];

        $lastQuery = "SELECT * FROM `erp_layer` ORDER BY `layer_id` DESC LIMIT 1";

        $admin = array();
        $admin["rack"] = $POST["rack"];
        $admin["name"] = $POST["name"];
        $admin["layer_desc"] = $POST["layer_desc"];


        $ins = "UPDATE `erp_layer` 
    SET 
    `layer_name`='" . $admin["name"] . "', 
    `layer_desc`='" . $admin["layer_desc"] . "' , 
    `rack_id`='" . $admin["rack"] . "' , 
    `company_id`='" .  $company_id  . "',
    `branch_id`='" . $branch_id . "',
    `location_id`='" . $location_id . "',
    `created_by`='" . $created_by . "'
    WHERE `layer_id`='" . $layer_id . "'
    ";



        $insertItem = queryUpdate($ins);
        if (isset($insertItem['status']) == "success") {
            $rack_id = $admin["rack"];
            $storage_data = queryGet("SELECT  r.`rack_name` , sl.`storage_location_name` AS storage_location, w.`warehouse_name`  AS warehouse_name
                                 FROM    `erp_rack` AS r
                                 JOIN   `erp_storage_location` AS sl
                                 ON      r.`storage_location_id` = sl.`storage_location_id`
                                 JOIN    `erp_storage_warehouse` AS w
                                 ON      sl.`warehouse_id`=w.`warehouse_id`
                                 WHERE   r.`rack_id`=$rack_id");
            ///---------------------------------Audit Log Start---------------------
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'EDIT';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = 'erp_layer';
            $auditTrail['basicDetail']['column_name'] = 'layer_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $layer_id;  // primary key
            $auditTrail['basicDetail']['document_number'] = $POST["editLayerCode"];
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['party_id'] = 0;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Update Layer';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'edit';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($ins);
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['Layer Details']['layer_name'] = $admin["name"];
            $auditTrail['action_data']['Layer Details']['layer_description'] = $admin["layer_desc"];
            $auditTrail['action_data']['Layer Details']['Storage_location'] = $storage_data['data'];
            $auditTrail['action_data']['Layer Details']['created_by'] = getCreatedByUser($created_by);
            $auditTrail['action_data']['Layer Details']['updated_by'] = getCreatedByUser($created_by);

            $auditTrailreturn = generateAuditTrail($auditTrail);
        }
        $returnData = $insertItem;

        return $returnData;
    }

    function getAllLayer()
    {
        global $company_id;

        $layer = queryGet("SELECT * FROM `erp_layer` WHERE `company_id` = $company_id", true);
        return $layer;
    }
}
