<?php
//*************************************/INSERT/******************************************//
function updatefuncMappForm($POST = [], $company_id)
{
  global $dbCon;
  global $created_by;
  $returnData = [];
  $isValidate = validate($POST, [
    "function" => "array"
  ], [
    "function" => "Required"
  ]);

  if ($isValidate["status"] == "success") {
    $flug = 1;
    $mapfunction = $_POST['function'];
    foreach ($mapfunction  as $key => $value) {
      //console($value);
      $function_name = $value['function_name'];
      $slug = preg_replace("![^a-z0-9]+!i", "", $value['function_name']);
      $creditArray = serialize(array_filter($value['credit']));
      $debitArray = serialize(array_filter($value['debit']));
      if (!empty($value['function_id'])) {
        $sql = "SELECT * FROM `" . ERP_ACC_FUNCTIONAL_MAPPING . "` WHERE `company_id`='" . $company_id . "' AND `map_id`='" . $value['function_id'] . "' AND `map_status`!='deleted'";
        if ($res = queryGet($sql, true)) {
          if ($res['numRows'] != 0) {
            $ins = "UPDATE `" . ERP_ACC_FUNCTIONAL_MAPPING . "` 
                      SET
                          `function_name`='" . $function_name . "',
                          `creditArray`='" . $creditArray . "',
                          `debitArray`='" . $debitArray . "',
                          `map_updated_by`='" . $created_by . "'
                      WHERE `company_id`='" . $company_id . "' 
                            AND `map_id`='" . $value['function_id'] . "'";
          } else {
            $flug++;
          }
        } else {
          $flug++;
        }
      } else {
       $ins = "INSERT INTO `" . ERP_ACC_FUNCTIONAL_MAPPING . "` 
                    SET
                        `company_id`='" . $company_id . "',
                        `function_name`='" . $function_name . "',
                        `slug`='" . $slug . "',
                        `creditArray`='" . $creditArray . "',
                        `debitArray`='" . $debitArray . "',
                        `map_created_by`='" . $created_by . "',
                        `map_updated_by`='" . $created_by . "'";
      }
      $rtn=queryInsert($ins);
      if ($rtn['status'] !='success') {
        $flug++;
      }
    }

    if ($flug == 1) {
      $returnData['status'] = "success";
      $returnData['message'] = "Successfully updated";
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Somthing went wrong";
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Invalid form inputes";
    $returnData['errors'] = $isValidate["errors"];
  }
  return $returnData;
}
//*************************************/END/******************************************//