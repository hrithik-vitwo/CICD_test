<?php
include("app/v1/functions/common/func-common.php");
function validation($data)
{
    global $dbCon;
    $returnData=[];
     $decode = base64_decode(base64_decode(base64_decode(base64_decode($data))));

     if(strlen($decode) == 0)
     {
         $returnData['status'] = "warning";
         $returnData['message'] = "Url Wrong!";
         
         return $returnData;
     }

     else
     {
        $array = explode("|", $decode);

        if(count($array)!= 3)
        {
            
         $returnData['status'] = "warning";
         $returnData['message'] = "Url cannot be decoded!";
         
         return $returnData;
        }
        else
        {
            $id = $array[0];
            $email = $array[1];
            $type = $array[2];

            $query = "SELECT * FROM erp_rfq_vendor_list WHERE `rfqVendorId`='".$id."' AND `vendor_email` = '$email' AND `vendor_type` = '$type' AND `status`='active'";


            $result = mysqli_query($dbCon, $query) or die(mysqli_error($dbCon));

            if(mysqli_num_rows($result) == 0)
            {                
                $returnData['status'] = "warning";
                $returnData['message'] = "Email Not Exist !";
                $returnData['sql'] = $query;
                
                return $returnData;
            }
            else
            {
                $get_query = "SELECT * FROM erp_rfq_vendor_list WHERE `rfqVendorId`='".$id."' AND `vendor_email` = '$email' AND `vendor_type` = '$type' AND `status`='active'";
                $dataset=queryGet($get_query, false);
                $rfqCode = $dataset["data"]["rfqCode"];
                $rfqId = $dataset["data"]["rfqItemListId"];
                
                $query_check = "SELECT * FROM erp_vendor_response WHERE `rfqId`='".$rfqId."' AND `vendor_email` = '$email'";

                $result = mysqli_query($dbCon, $query_check) or die(mysqli_error($dbCon));

                if(mysqli_num_rows($result) == 0)
                {
                    $closing_query = "SELECT * FROM erp_rfq_list WHERE `rfqId`='".$rfqId."'";
                    $closing_execute = queryGet($closing_query, false);
                    $closing_date = $closing_execute["data"]["closing_date"];
                    $date1=date_create($closing_date);
                    $date2=date_create(date());
                    $diff=date_diff($date1,$date2);
                    $days = $diff->format("%R%a");

                    if($days > 0)
                    {
                        
                        $returnData['status'] = "warning";
                        $returnData['message'] = "Date Expired!";
                        $returnData['sql'] = $query_check;
                        
                        return $returnData;
                    }
                    else
                    {
                        $returnData['status'] = "success";
                        $returnData['message'] = "Allow to responce!";
                        
                        return $returnData;
                    }
                }
                else
                {
                    $returnData['status'] = "warning";
                    $returnData['message'] = "Already Responded!";
                    $returnData['sql'] = $query_check;
                    
                    return $returnData;
                }
            }

        }


     }

}

function get_vendor_details($data)
{
    global $dbCon;
    $decode = base64_decode(base64_decode(base64_decode(base64_decode($data))));
    $array = explode("|", $decode);
    $id = $array[0];
    $email = $array[1];
    $type = $array[2];

    $query = "SELECT * FROM `erp_rfq_vendor_list` WHERE `rfqVendorId`='$id' AND `vendor_email` = '$email' AND `vendor_type` = '$type' AND `status`='active'";
    $dataset=queryGet($query, false);


    if($dataset['data']['vendor_type'] == "existing")
    {
        $query1 = "SELECT * FROM erp_rfq_vendor_list LEFT JOIN erp_vendor_details ON erp_vendor_details.vendor_id = erp_rfq_vendor_list.vendorId LEFT JOIN erp_vendor_bussiness_places ON erp_vendor_bussiness_places.vendor_id = erp_rfq_vendor_list.vendorId WHERE erp_rfq_vendor_list.rfqVendorId = '$id' AND erp_rfq_vendor_list.vendor_email = '$email' AND erp_rfq_vendor_list.vendor_type = '$type' AND erp_rfq_vendor_list.status = 'active'";
        $datasets=queryGet($query1, false);
        return $datasets['data'];
    }
    else
    {
        return $dataset['data'];
    }


}


function item_details($data)
{
    $decode = base64_decode(base64_decode(base64_decode(base64_decode($data))));
    $array = explode("|", $decode);
    $id = $array[0];
    $email = $array[1];
    $type = $array[2];

    $query = "SELECT * FROM erp_rfq_vendor_list  WHERE erp_rfq_vendor_list.rfqVendorId = '$id' AND erp_rfq_vendor_list.vendor_email = '$email' AND erp_rfq_vendor_list.vendor_type = '$type' AND erp_rfq_vendor_list.status = 'active'";
    $datasets=queryGet($query, false);
    $rfq_list_id = $datasets['data']['rfqItemListId'];

    $prQuery = "SELECT * FROM erp_rfq_list  WHERE rfqId = '$rfq_list_id'";

    $pr_db = queryGet($prQuery, false);

    $pr_id = $pr_db["data"]["prId"];
    $itemcode="SELECT * FROM erp_rfq_items LEFT JOIN erp_purchase_register_item_delivery_schedule ON erp_rfq_items.deliverySceduleId = erp_purchase_register_item_delivery_schedule.pr_delivery_id LEFT JOIN erp_inventory_items ON erp_rfq_items.ItemId = erp_inventory_items.itemId LEFT JOIN erp_inventory_mstr_uom ON erp_inventory_items.baseUnitMeasure = erp_inventory_mstr_uom.uomId WHERE
    erp_rfq_items.rfqId = '$rfq_list_id' AND erp_purchase_register_item_delivery_schedule.pr_id = '$pr_id'";

    // $item_list = "SELECT * FROM erp_rfq_items WHERE erp_rfq_items.rfqId = '$rfq_list_id'";

    $itemset = queryGet($itemcode, true);

    // foreach($itemset["data"] as $item)
    // {

    // }

    return $itemset['data'];




}

?>