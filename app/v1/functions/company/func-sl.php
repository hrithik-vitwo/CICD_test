<?php
class SLController
{

  function createSl($POST, $company_id, $created_by)
  {

    //    console($branch_id);

    //exit();
    $isValidate = validate($POST, [
      "name" => "required",
      "desc" => "required",
      "gl" => "required",

    ]);

    if ($isValidate["status"] != "success") {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
      return $returnData;
    }

    $name = $POST['name'];
    $desc = $POST['desc'];
    $gl = $POST['gl'];

    $glcode = 'SLR' . rand(1111, 9999) . rand(2111, 99999);



    $insSl = "INSERT INTO `erp_extra_sub_ledger`  
        SET
            `parentGlId`= $gl ,
            `sl_name`='" . $name . "',
            `sl_code` = '" . $glcode . "',
            `sl_description`='" . $desc . "',
            `company_id`='" .  $company_id  . "',
            `updated_by`='" . $created_by . "',
            `created_by`='" . $created_by . "' ";

    $insert = queryInsert($insSl);
    // console($insert);
    // exit();
    //console($location_id);
    //exit();
    if ($insert['status'] == "success") {
      $returnData['status'] = 'success';
      $returnData['message'] = 'sl inserted successfully';
    } else {
      $returnData['status'] = 'warning';
      $returnData['message'] = 'something went wrong';
    }






    return $returnData;
  }

  function editSl($POST, $company_id, $created_by)
  {

    // console($POST);
    // exit();

    $isValidate = validate($POST, [
      "name" => "required",
      "desc" => "required"

    ]);

    if ($isValidate["status"] != "success") {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
      return $returnData;
    }

    $name = $POST['name'];
    $desc = $POST['desc'];
    $id = $POST['slId'];




    $insSl = "UPDATE `erp_extra_sub_ledger`  
  SET
   
      `sl_name`='" . $name . "',
      `sl_description`='" . $desc . "',
      `company_id`='" .  $company_id  . "',
      `updated_by`='" . $created_by . "'
       WHERE `sl_id` = $id 
      ";

    $insert = queryUpdate($insSl);

    //console($location_id);
    //exit();
    if ($insert['status'] == "success") {
      $returnData['status'] = 'success';
      $returnData['message'] = 'sl updated successfully';
    } else {
      $returnData['status'] = 'warning';
      $returnData['message'] = 'something went wrong';
    }






    return $returnData;
  }
  function deleteSl($POST){
       
    // exit();
     $id = $POST;
 
    $delete_sl = queryDelete("DELETE FROM `erp_extra_sub_ledger` WHERE `sl_id` = '$id'");

    if($delete_sl['status'] == "success"){

      $returnData['status'] = 'success';
      $returnData['message'] = 'sl Deleted successfully';
    } else {
      $returnData['status'] = 'warning';
      $returnData['message'] = 'something went wrong';
    }
    return $returnData;

  }
}
