<?php
class CustomerDiscountGroupController
{

  function create_customer_discount_group($POST, $company_id, $created_by)
  {
    //    console($branch_id);
    // console($POST);
    // exit();
    $isValidate = validate($POST, [
      "name" => "required",


    ]);

    if ($isValidate["status"] != "success") {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
      return $returnData;
    }






    $insCustomerDiscountGroup = "INSERT INTO `erp_customer_discount_group`  
        SET
            `customer_discount_group`='" .  addslashes($POST["name"]) . "',
          
            `company_id`='" .  $company_id  . "',
          

            `created_by`='" . $created_by . "' ";

    $insert = queryInsert($insCustomerDiscountGroup);
    // console($insert); 
    // exit();
    if ($insert['status'] == "success") {
      $returnData['status'] = 'success';
      $returnData['message'] = ' inserted successfully';
    } else {
      $returnData['status'] = 'warning';
      $returnData['message'] = 'something went wrong';
    }

    $lastId = $insert['insertedId'];

    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
    $auditTrail = array();
    $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
    $auditTrail['basicDetail']['table_name'] = ERP_CUSTOMER_DISCOUNT_GROUP;
    $auditTrail['basicDetail']['column_name'] = 'customer_discount_group_id'; // Primary key column
    $auditTrail['basicDetail']['document_id'] = $lastId;  // primary key
    $auditTrail['basicDetail']['action_code'] = $action_code;
    $auditTrail['basicDetail']['action_referance'] = '';
    $auditTrail['basicDetail']['action_title'] = ' Customer Discount Group Created';  //Action comment
    $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
    $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
    $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
    $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
    $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert);
    $auditTrail['basicDetail']['others'] = '';
    $auditTrail['basicDetail']['remark'] = '';

    $auditTrail['action_data']['Customer Discount Group Details']['customer_discount_group_name'] = $POST["name"];
    $auditTrail['action_data']['Customer Discount Group Details']['created_by'] = getCreatedByUser($created_by);

    $auditTrailreturn = generateAuditTrail($auditTrail);



    return $returnData;
  }

  function edit_customer_discount_group($POST, $company_id, $created_by)
  {
    // console($branch_id);


    $isValidate = validate($POST, [
      "name" => "required"




    ]);

    if ($isValidate["status"] != "success") {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
      return $returnData;
    }



    echo  $id = $POST['id'];

    $ins = "UPDATE `erp_customer_discount_group` 
        SET
            `customer_discount_group`='" .  $POST["name"] . "',
        
            `updated_by`='" . $created_by . "'
            WHERE `customer_discount_group_id`='" . $id . "'
            ";
    $insertItem = queryUpdate($ins);
    // console($insertItem);
    // exit();

    if ($insertItem['status'] == "success") {
      $returnData['status'] = 'success';
      $returnData['message'] = ' updated successfully';
    } else {
      $returnData['status'] = 'warning';
      $returnData['message'] = 'something went wrong';
    }

    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
    $auditTrail = array();
    $auditTrail['basicDetail']['trail_type'] = 'EDIT';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
    $auditTrail['basicDetail']['table_name'] = ERP_CUSTOMER_DISCOUNT_GROUP;
    $auditTrail['basicDetail']['column_name'] = 'customer_discount_group_id'; // Primary key column
    $auditTrail['basicDetail']['document_id'] = $id;  // primary key
    $auditTrail['basicDetail']['action_code'] = $action_code;
    $auditTrail['basicDetail']['action_referance'] = '';
    $auditTrail['basicDetail']['action_title'] = ' Customer Discount Group Updated';  //Action comment
    $auditTrail['basicDetail']['action_name'] = 'Edit';     //	Add/Update/Deleted
    $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
    $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
    $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
    $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert);
    $auditTrail['basicDetail']['others'] = '';
    $auditTrail['basicDetail']['remark'] = '';

    $auditTrail['action_data']['Customer Discount Group Details']['customer_discount_group_name'] = $POST["name"];
    $auditTrail['action_data']['Customer Discount Group Details']['created_by'] = getCreatedByUser($created_by);

    $auditTrailreturn = generateAuditTrail($auditTrail);


    return $returnData;
  }










  function create_item_discount_group($POST, $company_id, $created_by)
  {
    //    console($branch_id);
    // console($POST);
    // exit();
    $isValidate = validate($POST, [
      "name" => "required",


    ]);

    if ($isValidate["status"] != "success") {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
      return $returnData;
    }






    $insCustomerDiscountGroup = "INSERT INTO `erp_item_discount_group`  
    SET
        `item_discount_group`='" .  addslashes($POST["name"]) . "',
      
        `company_id`='" .  $company_id  . "',
      

        `created_by`='" . $created_by . "' ";

    $insert = queryInsert($insCustomerDiscountGroup);
    // console($insert); 
    // exit();
    if ($insert['status'] == "success") {
      $returnData['status'] = 'success';
      $returnData['message'] = ' inserted successfully';
    } else {
      $returnData['status'] = 'warning';
      $returnData['message'] = 'something went wrong';
    }

    $lastId = $insert['insertedId'];

    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
    $auditTrail = array();
    $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
    $auditTrail['basicDetail']['table_name'] = ERP_ITEM_DISCOUNT_GROUP;
    $auditTrail['basicDetail']['column_name'] = 'item_discount_group_id'; // Primary key column
    $auditTrail['basicDetail']['document_id'] = $lastId;  // primary key
    $auditTrail['basicDetail']['action_code'] = $action_code;
    $auditTrail['basicDetail']['action_referance'] = '';
    $auditTrail['basicDetail']['action_title'] = ' Item Discount Group Created';  //Action comment
    $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
    $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
    $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
    $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
    $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert);
    $auditTrail['basicDetail']['others'] = '';
    $auditTrail['basicDetail']['remark'] = '';

    $auditTrail['action_data']['Item Discount Group Details']['item_discount_group_name'] = $POST["name"];
    $auditTrail['action_data']['Item Discount Group Details']['created_by'] = getCreatedByUser($created_by);

    $auditTrailreturn = generateAuditTrail($auditTrail);



    return $returnData;
  }

  function edit_item_discount_group($POST, $company_id, $created_by)
  {
    // console($branch_id);


    $isValidate = validate($POST, [
      "name" => "required"




    ]);

    if ($isValidate["status"] != "success") {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
      return $returnData;
    }



    echo  $id = $POST['id'];

    $ins = "UPDATE `erp_item_discount_group` 
    SET
        `item_discount_group`='" .  $POST["name"] . "',
    
        `updated_by`='" . $created_by . "'
        WHERE `item_discount_group_id`='" . $id . "'
        ";
    $insertItem = queryUpdate($ins);
    // console($insertItem);
    // exit();

    if ($insertItem['status'] == "success") {
      $returnData['status'] = 'success';
      $returnData['message'] = ' updated successfully';
    } else {
      $returnData['status'] = 'warning';
      $returnData['message'] = 'something went wrong';
    }

    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
    $auditTrail = array();
    $auditTrail['basicDetail']['trail_type'] = 'EDIT';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
    $auditTrail['basicDetail']['table_name'] = ERP_ITEM_DISCOUNT_GROUP;
    $auditTrail['basicDetail']['column_name'] = 'item_discount_group_id'; // Primary key column
    $auditTrail['basicDetail']['document_id'] = $id;  // primary key
    $auditTrail['basicDetail']['action_code'] = $action_code;
    $auditTrail['basicDetail']['action_referance'] = '';
    $auditTrail['basicDetail']['action_title'] = ' Item Discount Group Updated';  //Action comment
    $auditTrail['basicDetail']['action_name'] = 'Edit';     //	Add/Update/Deleted
    $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
    $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
    $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
    $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert);
    $auditTrail['basicDetail']['others'] = '';
    $auditTrail['basicDetail']['remark'] = '';

    $auditTrail['action_data']['Item Discount Group Details']['item_discount_group_name'] = $POST["name"];
    $auditTrail['action_data']['Item Discount Group Details']['created_by'] = getCreatedByUser($created_by);

    $auditTrailreturn = generateAuditTrail($auditTrail);


    return $returnData;
  }
}
