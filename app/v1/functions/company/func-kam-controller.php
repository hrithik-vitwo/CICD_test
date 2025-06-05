<?php
class KAMController
{

    function createKam($POST,$company_id,$created_by)
    {
     //    console($branch_id);

        //exit();
          $isValidate = validate($POST, [
            "name" => "required",
            "description" => "required",
            "contact"=>"required",

            "email" => "required",
            "emp_code" => "required",
            "designation"=>"required",
      
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        // $lastQuery = "SELECT * FROM `" . ERP_WAREHOUSE . "` ORDER BY `warehouse_id` DESC LIMIT 1";
        // $last = queryGet($lastQuery);
        // $lastRow = $last['data'] ?? "";
        // $lastid = $lastRow['warehouse_code'] ?? ""; 
        // $returnWarehouseCode = getWHSerialNumber($lastid);
        

        //getWHSerialNumber($lastsl)

        $p_id = $POST["p_id"] ?? 0;
      
        $admin = array();
        $admin["name"] = $POST["name"];
        $admin["description"] = $POST["description"];

        $email = $POST['email'];
        $contact = $POST['contact'];
        $emp_code = $POST['emp_code'];
        $designation = $POST['designation'];
        
        if($p_id != ""){
            $admin["p_id"] = $p_id;
        }
       else{
        $admin["p_id"] = 0;
       }

        $status = $POST["createKam"] == "add_draft" ? "draft" : "active";
      //   $last_sql = queryGet("SELECT * FROM `".ERP_KAM."` ORDER BY `kamId` DESC LIMIT 1 ");
      //  // console($last_sql);
      //    $last1 = $last_sql['data']['kamCode'];
      //  // exit();
      $kam_code =   'KAM'.time();
  

   $insKam = "INSERT INTO `" . ERP_KAM . "`  
        SET
            `kamName`='" .  $admin["name"] . "',
            `description`='" . $admin["description"] . "',
            `parentId`='" .$admin["p_id"]. "',
            `company_id`='" .  $company_id  . "',
          
            `status`='" . $status . "',
            `kamCode`= '".$kam_code."',
            `contact` ='".$contact."',

             `email`='" .  $email  . "',
          
            `emp_code`='" . $emp_code . "',
            `designation`= '".$designation."',

            `created_by`='" . $created_by . "' "; 
       
        $insert = queryInsert($insKam);
        //console($location_id);
        //exit();
      if($insert['status'] == "success"){
        $returnData['status'] = 'success';
        $returnData['message'] = 'kam inserted successfully';
      }
      else{
        $returnData['status'] = 'warning';
        $returnData['message'] = 'something went wrong';
      }

        $lastId = $insert['insertedId'];

        $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
        $auditTrail = array();
        $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
        $auditTrail['basicDetail']['table_name'] = ERP_KAM;
        $auditTrail['basicDetail']['column_name'] = 'kamId'; // Primary key column
        $auditTrail['basicDetail']['document_id'] = $lastId;  // primary key
        $auditTrail['basicDetail']['document_number'] = $kam_code;
        $auditTrail['basicDetail']['action_code'] = $action_code;
        $auditTrail['basicDetail']['action_referance'] = '';
        $auditTrail['basicDetail']['action_title'] = ' KAM Created';  //Action comment
        $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
        $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
        $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
        $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
        $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert);
        $auditTrail['basicDetail']['others'] = '';
        $auditTrail['basicDetail']['remark'] = '';

        $auditTrail['action_data']['Kam Details']['kamName'] = $admin["name"];
        $auditTrail['action_data']['Kam Details']['description'] = $admin["description"];
        $auditTrail['action_data']['Kam Details']['parentId'] = $admin["p_id"];
        $auditTrail['action_data']['Kam Details']['status'] = $status;
        $auditTrail['action_data']['Kam Details']['kamCode'] = $kam_code;
        $auditTrail['action_data']['Kam Details']['created_by'] = getCreatedByUser($created_by);

        $auditTrailreturn = generateAuditTrail($auditTrail);



        return $returnData;
    }

    function editKam($POST, $company_id, $created_by)
    {
        // console($branch_id);


        $isValidate = validate($POST, [
            "name" => "required",
           
            "description" => "required",
         
           

      
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }


        // $lastQuery = "SELECT * FROM `" . ERP_WAREHOUSE . "` ORDER BY `warehouse_id` DESC LIMIT 1";
        // $last = queryGet($lastQuery);
        // $lastRow = $last['data'] ?? "";
        // $lastid = $lastRow['warehouse_code'] ?? "";
        // $returnWarehouseCode = getWHSerialNumber($lastid);
        
$select = queryGet("SELECT * FROM `" . ERP_KAM . "` WHERE `kamId`= $kam_id");
$kam_code = $select['data']['kamCode'];
        //getWHSerialNumber($lastsl)
        $admin = array();
        $admin["name"] = $POST["name"];
        $admin["description"] = $POST["description"];
        $admin["p_id"] = $POST["p_id"];
       $kam_id = $POST['kam_id'];

        $status = $POST["editKam"] == "add_draft" ? "draft" : "active";

    $ins = "UPDATE `" . ERP_KAM . "` 
        SET
            `kamName`='" .  $admin["name"] . "',
            `description`='" . $admin["description"] . "',
            `parentId`='" . $admin["p_id"] . "',
            `company_id`='" .  $company_id  . "',
          
            `status`='" . $status . "',
            `created_by`='" . $created_by . "'
            WHERE `kamId`='".$kam_id."'
            ";
        $insertItem = queryUpdate($ins);
        
        if($insertItem['status'] == "success"){
            $returnData['status'] = 'success';
            $returnData['message'] = 'kam inserted successfully';
          }
          else{
            $returnData['status'] = 'warning';
            $returnData['message'] = 'something went wrong';
          }

        $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
        $auditTrail = array();
        $auditTrail['basicDetail']['trail_type'] = 'EDIT';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
        $auditTrail['basicDetail']['table_name'] = ERP_KAM;
        $auditTrail['basicDetail']['column_name'] = 'kamId'; // Primary key column
        $auditTrail['basicDetail']['document_id'] = $kam_id;  // primary key
        $auditTrail['basicDetail']['document_number'] = $kam_code;
        $auditTrail['basicDetail']['action_code'] = $action_code;
        $auditTrail['basicDetail']['action_referance'] = '';
        $auditTrail['basicDetail']['action_title'] = ' KAM Updated';  //Action comment
        $auditTrail['basicDetail']['action_name'] = 'Edit';     //	Add/Update/Deleted
        $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
        $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
        $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
        $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert);
        $auditTrail['basicDetail']['others'] = '';
        $auditTrail['basicDetail']['remark'] = '';

        $auditTrail['action_data']['Kam Details']['kamName'] = $admin["name"];
        $auditTrail['action_data']['Kam Details']['description'] = $admin["description"];
        $auditTrail['action_data']['Kam Details']['parentId'] = $admin["p_id"];
        $auditTrail['action_data']['Kam Details']['status'] = $status;
        $auditTrail['action_data']['Kam Details']['created_by'] = getCreatedByUser($created_by);
        
        $auditTrailreturn = generateAuditTrail($auditTrail);


        return $returnData;
    }
    
   
}
