<?php
class MRPController
{



    function addMrpGroup($POST) 
    {
        $returnData = [];
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;


        $isValidate = validate($POST, [
            "mrpGroupName" => "required",
        ]);
        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }
        $mrpGroupName = $POST['mrpGroupName'];
        // console($POST);
        $insert = "INSERT INTO `erp_customer_mrp_group`
					SET
					`customer_mrp_group` = '" . $mrpGroupName . "', 
                    `branch_id`=$branch_id,
                    `company_id`=$company_id,
                    `location_id`=$location_id,                           
                    `created_by`='" . $created_by . "',
                    `updated_by`='" . $updated_by . "'                            
                    ";


        $res = queryInsert($insert);
        return $res;
    }



    function editMrpGroup($POST)
    {
        $returnData = [];
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;


        $isValidate = validate($POST, [
            "mrpGroupName" => "required",
        ]);
        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }
        $mrpGroupName = $POST['mrpGroupName'];
        // console($POST);
        $sql = "UPDATE  `erp_customer_mrp_group`
					SET
					`customer_mrp_group` = '" . $mrpGroupName . "',  `updated_by`='" . $updated_by . "'   
                    WHERE `customer_mrp_group_id`='" . $POST['id'] . "' AND `company_id`=$company_id  AND `branch_id`=$branch_id AND `location_id`=$location_id
                          
                    ";


        $res = queryUpdate($sql);
        return $res;
    }





    function createMRPVariant($POST)
    {
        // console($POST);
        // exit();
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        // $listItem = $POST['listItem'];
        // console($listItem);
        // exit(); 

        $isValidate = validate($POST, [


            "valid_from" => "required",
            "valid_till" => "required"


        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }





        $mrp = rand(100,1000);
        $customer_group =  !empty($_POST['customer_group']) ? $_POST['customer_group'] : 0;
        $territory = !empty($_POST['territory']) ? $_POST['territory'] : 0;
        $type = $POST['type'];
        $valid_from = $POST['valid_from'];
        $valid_till = $POST['valid_till'];
        $mrp_name = 'MRP' . time() . $mrp;
        $goodsGroup = $POST['usetypesDropdown'];






        $insert = queryInsert("INSERT INTO `erp_mrp_variant` SET `mrp_variant` = '" . $mrp_name . "', `customer_group`='" . $customer_group . "', `valid_from` = '" . $valid_from . "', `valid_till` = '" . $valid_till . "',`territory` = '" . $territory . "',`status`= 'active',`type`='" . $type . "', `created_by` = '" . $created_by . "', `updated_by` = '" . $updated_by . "',`company_id` = $company_id,`branch_id` = $branch_id,`location_id` = $location_id");
       // console($insert);



        if ($insert['status'] == "success") {
            $mrp_id = $insert['insertedId'];

            $listItem = $POST['listItem'];
            foreach ($listItem as $item) {
                 
            // echo $type;
                    if($type == 'customer'){
                          //  echo 0;
                        $check = queryGet("SELECT * FROM `erp_mrp_variant_items` AS item LEFT JOIN `erp_mrp_variant` AS variant ON item.`mrp_id` = variant.mrp_id WHERE variant.`customer_group` = $customer_group AND item.`item_id` = '" . $item['itemId'] . "' AND variant.`location_id`=$location_id AND variant.`branch_id`=$branch_id AND variant.`company_id`=$company_id",true);
                        // console($check);
                        // exit();
                     

                    }
                    else{
                     //   echo 1;
                        $check = queryGet("SELECT * FROM `erp_mrp_variant_items` AS item LEFT JOIN `erp_mrp_variant` AS variant ON item.`mrp_id` = variant.mrp_id WHERE variant.`territory`=$territory AND item.`item_id` = '" . $item['itemId'] . "' AND variant.`location_id`=$location_id AND variant.`branch_id`=$branch_id AND variant.`company_id`=$company_id",true);
                        // console($check);
                        // exit();
                    }

                   
                //    console($check);
                //    exit();

                    if($check['numRows'] > 0){
                        foreach($check['data'] as $check){
                        $mrp_id_up = $check['mrp_id'];

                       $update =  queryUpdate("UPDATE `erp_mrp_variant_items` SET `status`= 'inactive' WHERE `mrp_id` = $mrp_id_up AND `item_id` = '".$item['itemId']."'");
                       //console($update);
                      
                        }

                        // exit();

                        $insert_item = queryInsert("INSERT INTO `erp_mrp_variant_items` SET `mrp_id` = $mrp_id, `item_group` = $goodsGroup, `item_id` = '" . $item['itemId'] . "', `batch_number` = '" . $item['batchselectionchekbox'] . "', `cost` = '" . $item['cost'] . "', `margin` = '" . $item['margin'] . "', `mrp`= '" . $item['mrp'] . "',`created_by` = '" . $created_by . "', `updated_by` = '" . $updated_by . "',`company_id` = $company_id,`branch_id` = $branch_id,`location_id` = $location_id");
                       // console($insert_item);


                        if($insert_item['status'] == 'success'){
                            $returnData['status'] = 'success';
                            $returnData['message'] = 'Successful';
                        }
                        else{
                            $returnData['status'] = 'warning';
                            $returnData['message'] = 'Something went wrong';
                        }

                
                       

                    }
                    else{

                        $insert_item = queryInsert("INSERT INTO `erp_mrp_variant_items` SET `mrp_id` = $mrp_id, `item_group` = $goodsGroup, `item_id` = '" . $item['itemId'] . "', `batch_number` = '" . $item['batchselectionchekbox'] . "', `cost` = '" . $item['cost'] . "', `margin` = '" . $item['margin'] . "', `mrp`= '" . $item['mrp'] . "',`created_by` = '" . $created_by . "', `updated_by` = '" . $updated_by . "',`company_id` = $company_id,`branch_id` = $branch_id,`location_id` = $location_id");

                       // console($insert_item);

                        if($insert_item['status'] == 'success'){
                            $returnData['status'] = 'success';
                            $returnData['message'] = 'Successful';
                        }
                        else{
                            $returnData['status'] = 'warning';
                            $returnData['message'] = 'Something went wrong';
                        }



                    }
                    

                  
                


            }
        } else {
            $returnData['status'] = 'warning';
            $returnData['message'] = 'Something went wrong';
        }



        return $returnData;
    }


    function editMRPVariant($POST)
    {
        // console($POST);
        // exit();
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        // $listItem = $POST['listItem'];
        // console($listItem);
        // exit(); 

        $isValidate = validate($POST, [


            "valid_from" => "required",
            "valid_till" => "required"


        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }






       $customer_group =  !empty($_POST['customer_group']) ? $_POST['customer_group'] : 0;
       $territory = !empty($_POST['territory']) ? $_POST['territory'] : 0;
        $type = $POST['type'];
        $valid_from = $POST['valid_from'];
        $valid_till = $POST['valid_till'];
       // $mrp_name = MRP . time() . $mrp;
       // $goodsGroup = $POST['usetypesDropdown'];
        $mrp_id = $POST['mrpd'];






        $insert = queryInsert("UPDATE `erp_mrp_variant` SET  `customer_group`='" . $customer_group . "', `valid_from` = '" . $valid_from . "', `valid_till` = '" . $valid_till . "',`territory` = '" . $territory . "', `updated_by` = '" . $updated_by . "',`company_id` = $company_id,`branch_id` = $branch_id,`location_id` = $location_id WHERE `mrp_id`=$mrp_id");
       // console($insert);



        if ($insert['status'] == "success") {
        //    $mrp_id = $insert['insertedId'];
        
            $listItem = $POST['listItem'];
            foreach ($listItem as $item) {
                $mrp_item_id = $item['mrp_item_id'];
                
                        $insert_item = queryInsert("UPDATE `erp_mrp_variant_items` SET `mrp_id` = $mrp_id,  `batch_number` = '" . $item['batchselectionchekbox'] . "', `cost` = '" . $item['cost'] . "', `margin` = '" . $item['margin'] . "', `mrp`= '" . $item['mrp'] . "', `updated_by` = '" . $updated_by . "',`company_id` = $company_id,`branch_id` = $branch_id,`location_id` = $location_id WHERE `mrp_item_id` = $mrp_item_id");

                       // console($insert_item);

                        if($insert_item['status'] == 'success'){
                            $returnData['status'] = 'success';
                            $returnData['message'] = 'Successful';
                        }
                        else{
                            $returnData['status'] = 'warning';
                            $returnData['message'] = 'Something went wrong';
                        }



                    }
                    

                  
                


            
        } else {
            $returnData['status'] = 'warning';
            $returnData['message'] = 'Something went wrong';
        }



        return $returnData;
    }

}
