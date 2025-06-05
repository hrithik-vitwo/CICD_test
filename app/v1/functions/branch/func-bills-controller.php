<?php

class BillController
{

    function getBillDetailsByCondition($condition = "")
    {
        if ($condition == "") {
            return [
                "status" => "warning",
                "message" => "Condition is required",
                "data" => []
            ];
        }
        global $dbCon;
        $returnData = [];
        $query = "SELECT * FROM " . ERP_PURCHASE_BILLS . " WHERE " . $condition;
        $result = mysqli_query($dbCon, $query);
        if (mysqli_num_rows($result) > 0) {
            $returnData = [
                "status" => "success",
                "message" => "Bills are loaded successfully",
                "data" => mysqli_fetch_assoc($result)
            ];
        } else {
            $returnData = [
                "status" => "warning",
                "message" => "Record not found!",
                "data" => []
            ];
        }
        return $returnData;
    }

    function createNewBill($INPUTS = [])
    {
        global $dbCon;
        $returnData = [];
        $isValidate = validate($INPUTS, [
            "companyId" => "required",
            "branchId" => "required",
            "adminId" => "required",
            "vendorBillNumber" => "required",
            "billGrandTotal" => "required"
        ], [
            "companyId" => "Invalid company",
            "branchId" => "Invalid branch",
            "adminId" => "Invalid admin",
            "vendorBillNumber" => "Bill number must be required",
            "billGrandTotal" => "Grand total must be required"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        $companyId = $INPUTS["companyId"];
        $branchId = $INPUTS["branchId"];
        $adminId = $INPUTS["adminId"];
        $vendorBillNumber = $INPUTS["vendorBillNumber"];
        $billSubTotal = $INPUTS["billSubTotal"];
        $billGrandTotal = $INPUTS["billGrandTotal"];
        $orderNumber = $INPUTS["billOrderNumber"];
        $billRefNumber = $INPUTS["billRefNumber"];
        $billedDate = $INPUTS["billDate"];
        $billDueDate = $INPUTS["billDueDate"];

        $billNote = $INPUTS["billNote"];


        $billTotalIGST = $INPUTS["billTotalIGST"];
        $billTotalCGST = $INPUTS["billTotalCGST"];
        $billTotalSGST = $INPUTS["billTotalSGST"];


        $vendorId = $INPUTS["vendorId"];
        $vendorGstin = $INPUTS["billVendorGSTIN"];
        $currencyCode = "INR";
        $billStatus = $INPUTS["billStatus"];

        $billToGstin = "19GVHJ8GVJAD9";


        //items
        $itemNameList = $INPUTS["itemName"];
        $itemHsnList = $INPUTS["itemHSN"];
        $itemDescriptionList = $INPUTS["itemDescription"];
        $itemQuantityList = $INPUTS["itemQuantity"];
        $itemUnitPriceList = $INPUTS["itemUnitPrice"];
        $itemTotalPriceList = $INPUTS["itemTotalPrice"];



        $sqlBill = "INSERT `" . ERP_PURCHASE_BILLS . "` SET `company_id`='" . $companyId . "',`branch_id`='" . $branchId . "',`bill_number`='" . $vendorBillNumber . "',`bill_ref_number`='" . $billRefNumber . "',`order_number`='" . $orderNumber . "',`billed_date`='" . $billedDate . "',`due_date`='" . $billDueDate . "',`bill_sub_amount`='" . $billSubTotal . "',`bill_total_amount`='" . $billGrandTotal . "',`bill_to_gstin`='" . $billToGstin . "',`currency_code`='" . $currencyCode . "',`vendor_id`='" . $vendorId . "',`vendor_gstin`='" . $vendorGstin . "', `bill_notes`='" . $billNote . "',`bill_created_by`='" . $adminId . "',`bill_updated_by`='" . $adminId . "',`bill_status`='" . $billStatus . "'";

        if ($res = mysqli_query($dbCon, $sqlBill)) {
            $billId = mysqli_insert_id($dbCon);

            $noOfSuccessfullyInsertedItems = 0;
            foreach ($itemNameList as $itemKey => $itemName) {
                $itemId = rand(111111, 999999);
                $itemName = $itemNameList[$itemKey];
                $itemHsn = $itemHsnList[$itemKey];
                $itemDescription = $itemDescriptionList[$itemKey];
                $itemQuantity = $itemQuantityList[$itemKey];
                $itemUnitPrice = $itemUnitPriceList[$itemKey];
                $itemTotalPrice = $itemTotalPriceList[$itemKey];

                $sqlBillItem = "INSERT `" . ERP_PURCHASE_BILLS_ITEMS . "` 
                                    SET 
                                        `bill_id`='" . $billId . "',
                                        `item_id`='" . $itemId . "',
                                        `bill_item_desc`='" . $itemDescription . "',
                                        `bill_item_qty`='" . $itemQuantity . "',
                                        `bill_item_price`='" . $itemUnitPrice . "',
                                        `bill_item_total_price`='" . $itemTotalPrice . "', 
                                        `bill_item_created_by`='" . $adminId . "', 
                                        `bill_item_updated_by`='" . $adminId . "',
                                        `bill_item_status`='active'";

                if (mysqli_query($dbCon, $sqlBillItem)) {
                    $noOfSuccessfullyInsertedItems++;
                }
            }

            if ($noOfSuccessfullyInsertedItems == count($itemDescriptionList)) {
                $returnData = [
                    "status" => "success",
                    "message" => "bill created successfully"
                ];
            } else {
                $returnData = [
                    "status" => "warning",
                    "message" => "bill created as draft, recheck the items and update the status"
                ];
            }
        } else {
            $returnData = [
                "status" => "warning",
                "message" => "bills creation failed"
            ];
        }
        return $returnData;
    }


    function readVendorBills($filePath)
    {
        $returnData = [];
        $fullPath = $filePath;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://ocrserver.centralindia.cloudapp.azure.com:8000/api/v1/ocr/azure/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('file' => new CURLFILE($fullPath)),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $responseData = json_decode($response, true);

        if (isset($responseData['payload'])) {
            $returnData = [
                "status" =>"success",
                "message" => "Successfully read the bill data",
                "data" => $responseData["payload"]
            ];
        }else{
            $returnData = [
                "status" =>"warning",
                "message" => "Failed to read the bill data, try again",
                "data" => [],
                "responseData" => $response
            ];
        }
          
        return $returnData;
    }
    function readVendorBillsNew($filePath, $gstin=null)
    {
        $returnData = [];
        $fullPath = $filePath;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://ocrserver.centralindia.cloudapp.azure.com:8000/api/v1/ocr/azure/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('file' => new CURLFILE($fullPath), 'customer_gstin' => $gstin),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $responseData = json_decode($response, true);

        $_SESSION['ocr_limitation']=$_SESSION['ocr_limitation']-1;
        if (isset($responseData['payload'])) {
            $returnData = [
                "status" =>"success",
                "message" => "Successfully read the bill data",
                "data" => $responseData["payload"]
            ];
        }else{
            $returnData = [
                "status" =>"warning",
                "message" => "Failed to read the bill data, try again",
                "data" => [],
                "responseData" => $response
            ];
        }

        return $returnData;
    }

    /*===============Desigh Purpus================*/


    function readVendorBills2($filePath)
    {
            $returnData = [
                "status" =>"warning",
                "message" => "Failed to read the bill data, try again",
                "data" => [],
                "responseData" => ''
            ];
            
        $_SESSION['ocr_limitation']=$_SESSION['ocr_limitation']-1;
        return $returnData;
    }
    function readVendorBillsNew2($filePath, $gstin=null)
    {
        $returnData = [];
        $returnData = [
            "status" =>"warning",
            "message" => "Failed to read the bill data, try again",
            "data" => [],
            "responseData" => ''
        ];

        $_SESSION['ocr_limitation']=$_SESSION['ocr_limitation']-1;
        return $returnData;
    }

    /*==============End=================*/
}


?>