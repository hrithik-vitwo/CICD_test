<?php

function createVariant($POST){
       
  
       
 
    global $dbCon;
    global $company_id;
    $returnData = [];
    $isValidate = validate($POST, [
        "start_date_year" => "required",
        "end_date_year" => "required"
    ]);

   

//die();
    if ($isValidate["status"] == "success") {
       
     $start_year = date('Y-m', strtotime($POST['start_date_year']));
    $end = date_create($POST['end_date_year']);
    $end_year = date_format($end,"Y-m");
    $year_variant_name = $POST['start_year_name'];
 
     $ins = "INSERT INTO `".ERP_YEAR_VARIANT."` SET `year_variant_name`='".$year_variant_name."', `year_start`='".$start_year."', `year_end`='".$end_year."', `company_id`=$company_id ";
    
   if (mysqli_query($dbCon, $ins)) {
        $last_id = mysqli_insert_id($dbCon);
        $months = $POST['month'];
        foreach($months as $month){
            
            $name = $month['name'];
            $start = date_format(date_create(str_replace('/','-',$month['start_date'])),'Y-m-d');
            $end = date_format(date_create(str_replace('/','-',$month['end_date'])),'Y-m-d');
           // console($end);
        //    exit();
           $ins_month = "INSERT INTO `".ERP_MONTH_VARIANT."` SET `month_variant_name`='".$name."', `month_start`='".$start."', `month_end`='".$end."', `company_id`=$company_id,`year_id`=$last_id ";
            $returnData = queryInsert($ins_month);
           // exit();
    
        }

    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Year Variant Addition failed";
    }

   }
    else{

        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];

    }
    return $returnData;

}


?>