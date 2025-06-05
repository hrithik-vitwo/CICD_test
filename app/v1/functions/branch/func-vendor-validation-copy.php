<?php
include("app/v1/functions/common/func-common.php");
function validation($data)
{
    global $dbCon;
     $decode = base64_decode(base64_decode(base64_decode(base64_decode($data))));

     if(strlen($decode) == 0)
     {
         return false;
     }

     else
     {
        $array = explode("|", $decode);

        if(count($array)!= 3)
        {
            return false;
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
                return false;
            }
            else
            {
                $get_query = "SELECT * FROM erp_rfq_vendor_list WHERE `rfqVendorId`='".$id."' AND `vendor_email` = '$email' AND `vendor_type` = '$type' AND `status`='active'";
                $dataset=queryGet($get_query, false);
                $rfqCode = $dataset["data"]["rfqCode"];
                
                $query_check = "SELECT * FROM erp_vendor_response WHERE `rfq_code`='".$rfqCode."' AND `vendor_email` = '$email'";

                $result = mysqli_query($dbCon, $query_check) or die(mysqli_error($dbCon));

                if(mysqli_num_rows($result) == 0)
                {
                    return true;
                }
                else
                {
                    return false;
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

    $itemcode = "SELECT * FROM erp_rfq_items LEFT JOIN erp_branch_purchase_request_items ON erp_branch_purchase_request_items.itemId = erp_rfq_items.itemId LEFT JOIN erp_inventory_items ON erp_inventory_items.itemId = erp_branch_purchase_request_items.itemId WHERE erp_rfq_items.rfqId = '$rfq_list_id' AND erp_branch_purchase_request_items.prId = '$pr_id'";

    // $item_list = "SELECT * FROM erp_rfq_items WHERE erp_rfq_items.rfqId = '$rfq_list_id'";

    $itemset = queryGet($itemcode, true);

    // foreach($itemset["data"] as $item)
    // {

    // }

    return $itemset['data'];




}

?>