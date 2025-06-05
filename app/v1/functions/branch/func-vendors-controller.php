<?php
class VendorController
{
    function getAllDataVendor()
    {
        global $dbCon;
        global $company_id;
        global $branch_id;
        $returnData = [];
        $sql = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE  `vendor_status`='active' AND `company_id`=$company_id";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $returnData['status'] = "success";
                $returnData['message'] = "Data found";
                $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Data not found";
                $returnData['data'] = [];
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
            $returnData['data'] = [];
        }
       // echo $returnData;
        return $returnData;
    }
    function getDataVendorDetails($id)
    {
        global $dbCon;
        $returnData = [];
        $sql = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `vendor_id`=$id ";
        if ($res = mysqli_query($dbCon, $sql)) {
            if (mysqli_num_rows($res) > 0) {
                $returnData['status'] = "success";
                $returnData['message'] = "Data found";
                $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
            } else {
                $returnData['status'] = "warning";
                $returnData['message'] = "Data not found";
                // $returnData['data'] = [];
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
            $returnData['data'] = [];
        }
        return $returnData;
    }
}
