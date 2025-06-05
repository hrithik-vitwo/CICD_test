<?php
class CustomerDiscountGroupController
{

  function create_customer_discount_group($POST, $company_id, $created_by)
  {
    //    console($branch_id);
    // console($POST);
    // exit();
    global $branch_id;
    global $location_id;

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

            `branch_id` = '" . $branch_id . "',

            `location_id` = '" . $location_id . "',
          

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
    $auditTrail['basicDetail']['table_name'] = 'ERP_CUSTOMER_DISCOUNT_GROUP';
    $auditTrail['basicDetail']['column_name'] = 'customer_discount_group_id'; // Primary key column
    $auditTrail['basicDetail']['document_id'] = $lastId;  // primary key
    $auditTrail['basicDetail']['action_code'] = $action_code;
    $auditTrail['basicDetail']['action_referance'] = '';
    $auditTrail['basicDetail']['action_title'] = ' Customer Discount Group Created';  //Action comment
    $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
    $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
    $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
    $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
    // $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert);
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

    global $branch_id;
    global $location_id;
    global $updated_by;
    

    $isValidate = validate($POST, [
      "name" => "required"




    ]);

    if ($isValidate["status"] != "success") {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
      return $returnData;
    }



      $id = $POST['id'];

    $ins = "UPDATE `erp_customer_discount_group` 
        SET
            `customer_discount_group`='" .  $POST["name"] . "',

            `company_id`='" .  $company_id  . "',

            `branch_id` = '" . $branch_id . "',

            `location_id` = '" . $location_id . "',
        
            `updated_by`='" . $updated_by . "'
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
    $auditTrail['basicDetail']['table_name'] = 'ERP_CUSTOMER_DISCOUNT_GROUP';
    $auditTrail['basicDetail']['column_name'] = 'customer_discount_group_id'; // Primary key column
    $auditTrail['basicDetail']['document_id'] = $id;  // primary key
    $auditTrail['basicDetail']['action_code'] = $action_code;
    $auditTrail['basicDetail']['action_referance'] = '';
    $auditTrail['basicDetail']['action_title'] = ' Customer Discount Group Updated';  //Action comment
    $auditTrail['basicDetail']['action_name'] = 'Edit';     //	Add/Update/Deleted
    $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
    $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
    $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
    //$auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert);
    $auditTrail['basicDetail']['others'] = '';
    $auditTrail['basicDetail']['remark'] = '';

    $auditTrail['action_data']['Customer Discount Group Details']['customer_discount_group_name'] = $POST["name"];
    $auditTrail['action_data']['Customer Discount Group Details']['created_by'] = getCreatedByUser($created_by);

    $auditTrailreturn = generateAuditTrail($auditTrail);


    return $returnData;
  }


  function create_item_discount_group($POST, $company_id, $created_by)
  {

    global $branch_id;
    global $location_id;
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

      `branch_id` = '" . $branch_id . "',

      `location_id` = '" . $location_id . "',
      

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
    $auditTrail['basicDetail']['table_name'] = 'ERP_ITEM_DISCOUNT_GROUP';
    $auditTrail['basicDetail']['column_name'] = 'item_discount_group_id'; // Primary key column
    $auditTrail['basicDetail']['document_id'] = $lastId;  // primary key
    $auditTrail['basicDetail']['action_code'] = $action_code;
    $auditTrail['basicDetail']['action_referance'] = '';
    $auditTrail['basicDetail']['action_title'] = ' Item Discount Group Created';  //Action comment
    $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
    $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
    $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
    $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
  //  $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert);
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
    global $branch_id;
    global $location_id;

    $isValidate = validate($POST, [
      "name" => "required"




    ]);

    if ($isValidate["status"] != "success") {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
      return $returnData;
    }
    $id = $POST['id'];

    $ins = "UPDATE `erp_item_discount_group` 
    SET
        `item_discount_group`='" .  $POST["name"] . "',

        `company_id`='" .  $company_id  . "',

        `branch_id` = '" . $branch_id . "',

        `location_id` = '" . $location_id . "',
    
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
    $auditTrail['basicDetail']['table_name'] = 'ERP_ITEM_DISCOUNT_GROUP';
    $auditTrail['basicDetail']['column_name'] = 'item_discount_group_id'; // Primary key column
    $auditTrail['basicDetail']['document_id'] = $id;  // primary key
    $auditTrail['basicDetail']['action_code'] = $action_code;
    $auditTrail['basicDetail']['action_referance'] = '';
    $auditTrail['basicDetail']['action_title'] = ' Item Discount Group Updated';  //Action comment
    $auditTrail['basicDetail']['action_name'] = 'Edit';     //	Add/Update/Deleted
    $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
    $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
    $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
    //$auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert);
    $auditTrail['basicDetail']['others'] = '';
    $auditTrail['basicDetail']['remark'] = '';

    $auditTrail['action_data']['Item Discount Group Details']['item_discount_group_name'] = $POST["name"];
    $auditTrail['action_data']['Item Discount Group Details']['created_by'] = getCreatedByUser($created_by);

    $auditTrailreturn = generateAuditTrail($auditTrail);


    return $returnData;
  }

  function createCoupon($POST)
  {
    //console($POST);
    // exit();

    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;

    $isValidate = validate($POST, [
      "coupon_code" => "required",
      "coupon_serial" => "required"
    ]);

    if ($isValidate["status"] != "success") {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
      return $returnData;
    }

    $check = queryGet("SELECT * FROM `erp_discount_coupon` WHERE `discount_coupon_serial` = '" . $POST['coupon_serial'] . "' WHERE `location_id` = $location_id AND `company_id` = $company_id");

    if ($check['numRows'] > 0) {

      $returnData['status'] = 'warning';
      $returnData['message'] = 'Duplicate Serial Number found !! ';
      return $returnData;
    }

    $insCustomerDiscountGroup = "INSERT INTO `erp_discount_coupon`  
  		SET
      `discount_coupon_code`='" .  addslashes($POST["coupon_code"]) . "',
    
      `discount_coupon_serial`='" .  addslashes($POST["coupon_serial"]) . "',
      `company_id` = $company_id,
      `branch_id` = $branch_id,
      `location_id` = $location_id,
      `created_by`='" . $created_by . "' ";


    $insert = queryInsert($insCustomerDiscountGroup);
    //   console($insert); 
    //   exit();
    if ($insert['status'] == "success") {
      $returnData['status'] = 'success';
      $returnData['message'] = ' inserted successfully';
    } else {
      $returnData['status'] = 'warning';
      $returnData['message'] = 'something went wrong';
    }
    return $returnData;
  }

  function editCoupon($POST)
  {
    //console($POST);
    // exit();

    global $company_id;
    global $branch_id;
    global $location_id;
    global $updated_by;

    $isValidate = validate($POST, [
      "coupon_code" => "required",
      "coupon_serial" => "required"
    ]);

    if ($isValidate["status"] != "success") {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
      return $returnData;
    }

    $check = queryGet("SELECT * FROM `erp_discount_coupon` WHERE `discount_coupon_serial` = '" . $POST['coupon_serial_hidden'] . "' WHERE `location_id` = $location_id AND `company_id` = $company_id");

    if ($check['numRows'] > 0) {

      $returnData['status'] = 'warning';
      $returnData['message'] = 'Duplicate Serial Number found !! ';
      return $returnData;
    }


    $id = $POST['id'];

    $insCustomerDiscountGroup = "UPDATE `erp_discount_coupon`  
		SET
    `discount_coupon_code`='" .  addslashes($POST["coupon_code"]) . "',
  
    `discount_coupon_serial`='" .  addslashes($POST["coupon_serial"]) . "',
    `company_id` = $company_id,
    `branch_id` = $branch_id,
    `location_id` = $location_id,
    `updated_by`='" . $updated_by . "' WHERE `discount_coupon_id` = $id ";


    $insert = queryInsert($insCustomerDiscountGroup);
    //   console($insert); 
    //   exit();
    if ($insert['status'] == "success") {
      $returnData['status'] = 'success';
      $returnData['message'] = 'updated successfully';
    } else {
      $returnData['status'] = 'warning';
      $returnData['message'] = 'something went wrong';
    }
    return $returnData;
  }


  function createDiscountVarient($POST)
  {
    // console($POST);
  // exit();
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $returnData = [];
  

    $isValidate = validate($POST, [
      "customer_group" => "required",
      "item_group" => "required",
      "valid_from" => "required",
      "valid_upto" => "required"
    ]);

    if ($isValidate["status"] != "success") {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputs";
      $returnData['errors'] = $isValidate["errors"];
      return $returnData;
    }
     
    if(empty($POST['discount_percentage']) && empty($POST['discount_val'])){
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid Discount Percentage/Value";
      return $returnData;

    }

    $customer_group = $POST['customer_group'];
    $item_group = $POST['item_group'];
    $discount_type = $POST['discount_type'];  
    $discount_percentage = $_POST['discount_percentage'] !== '' ? $POST['discount_percentage'] : 0;
    $discount_max_val = $POST['discount_max_val'] !== '' ? $_POST['discount_max_val'] : 0;
    $discount_val = $POST['discount_val'] != '' ? $_POST['discount_val'] : 0;
    $term_of_payment = isset($_POST['term_of_payment']) ? $_POST['term_of_payment'] : 0;
    $valid_from = $POST['valid_from'];
    $valid_upto = $POST['valid_upto'];
    $min_val = $POST['min_val'] != '' ? $_POST['min_val'] : 0;
    $condition = $POST['condition'];
    $min_qty = $POST['min_qty'] != '' ? $_POST['min_qty'] : 0;
    $coupon_code = $POST['coupon_code'];



    $insert = queryInsert("INSERT INTO `erp_discount_variant_master` 
                SET 
                `item_discount_group_id`=$item_group,
                `customer_discount_group_id`=$customer_group,
                `discount_type`='$discount_type',                
                `discount_percentage`='".$discount_percentage."',
                `discount_max_value`='".$discount_max_val."',
                `discount_value`='".$discount_val."',                
                `valid_from`='$valid_from',
                `valid_upto`='$valid_upto',
                `term_of_payment`='".$term_of_payment."',
                `minimum_qty`='$min_qty',
                `condition`='" . $condition . "',
                `minimum_value`='$min_val',
                `coupon`= '" . $coupon_code . "',
                `created_by` = '" . $created_by . "',
                `company_id` = $company_id,
                `branch_id` = $branch_id,
               `location_id` = $location_id 
                ");
  
// console($insert);
// exit();
    if ($insert['status'] == 'success') {
      $returnData['status'] = 'success';
      $returnData['message'] = 'Inserted Successfully';
    } else {
      $returnData['status'] = 'warning';
      $returnData['message'] = 'something went wrong';
    }

    return $returnData;
  }





function editDiscountVarient($POST)
  {
    // console($POST);
  // exit();
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $returnData = [];

    $isValidate = validate($POST, [
      "customer_group" => "required",
      "item_group" => "required",
      "valid_from" => "required",
      "valid_upto" => "required"
    ]);

    if ($isValidate["status"] != "success") {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
      return $returnData;
    }

    if(empty($POST['discount_percentage']) && empty($POST['discount_val'])){
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid Discount Percentage/Value";
      return $returnData;

    }

    $customer_group = $POST['customer_group'];
    $item_group = $POST['item_group'];
    $discount_type = $POST['discount_type'];  
    $discount_percentage = $POST['discount_percentage'];
    $discount_max_val = $POST['discount_max_val'];
    $discount_val = $POST['discount_val'];
    $term_of_payment = $POST['term_of_payment'];
    $valid_from = $POST['valid_from'];
    $valid_upto = $POST['valid_upto'];
    $min_val = $POST['min_val'];
    $condition = $POST['condition'];
    $min_qty = $POST['min_qty'];
    $coupon_code = $POST['coupon_code'];
	$discount_variant_id=$POST['discount_variant_id'];




    $res = queryUpdate("UPDATE  `erp_discount_variant_master` 
                SET 
                `item_discount_group_id`=$item_group,
                `customer_discount_group_id`=$customer_group,
                `discount_type`='$discount_type',                
                `discount_percentage`='$discount_percentage',
                `discount_max_value`='$discount_max_val',
                `discount_value`='$discount_val',                
                `valid_from`='$valid_from',
                `valid_upto`='$valid_upto',
                `term_of_payment`='$term_of_payment',
                `minimum_qty`='$min_qty',
                `condition`='" . $condition . "',
                `minimum_value`='$min_val',
                `coupon`= '" . $coupon_code . "'
                WHERE `discount_variant_id`=$discount_variant_id AND  `company_id` = $company_id AND `branch_id` = $branch_id AND `location_id` = $location_id  
                ");

    if ($res['status'] == 'success') {
      $returnData['status'] = 'success';
      $returnData['message'] = 'Updated Successfully';
    } else {
      $returnData['status'] = 'warning';
      $returnData['message'] = 'something went wrong';
    }

    return $returnData;
  }
}
