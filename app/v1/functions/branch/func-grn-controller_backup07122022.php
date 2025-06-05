<?php

class GrnController
{
    // ERP_GRN
    // ERP_GRN_GOODS


    function getCompanyDetails(){
        $loginCompanyId = $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] ?? "";
        $loginBranchId = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"] ?? "";
        $loginLocationId = $_SESSION["logedBranchAdminInfo"]["fldAdminLocationId"] ?? "";
        $loginAdminId = $_SESSION["logedBranchAdminInfo"]["adminId"] ?? "";
        $loginAdminType = $_SESSION["logedBranchAdminInfo"]["adminType"] ?? "";
        $getCompanyDetails = queryGet('SELECT * FROM `erp_companies` WHERE `company_id`="'.$loginCompanyId.'"');
        return $getCompanyDetails["data"];
    }

    function createGrn($INPUTS){
        $returnData = [];
        $isValidate = validate($INPUTS, [
            "invoicePostingDate" => "required",
            "invoiceDueDate" => "required",
            "vendorCode" => "required",
            "vendorName" => "required",
            "vendorGstin" => "required",
            "totalInvoiceCGST" => "required",
            "totalInvoiceSGST" => "required",
            "totalInvoiceIGST" => "required",
            "totalInvoiceSubTotal" => "required",
            "totalInvoiceTotal" => "required",
            "grnItemCode" => "array",
            "grnItemHsn" => "array",
            "grnItemName" => "array",
            "grnItemQty" => "array",
            "grnItemTax" => "array",
            "grnItemUnitPrice" => "array",
            "grnItemTotalPrice" => "array",
            "grnItemReceivedQty" => "array"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        // $logedBranchAdminInfo = Array
        // (
        //     [adminId] => 7,
        //     [adminName] => nb bh
        //     [adminEmail] => kolkata@gmail.com
        //     [adminPhone] => 12345678
        //     [adminRole] => 2
        //     [fldAdminCompanyId] => 1
        //     [fldAdminBranchId] => 1
        //     [fldAdminLocationId] => 8
        //     [adminType] => location
        // )


        $loginCompanyId = $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"];
        $loginBranchId = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"];
        $loginLocationId = $_SESSION["logedBranchAdminInfo"]["fldAdminLocationId"];
        $loginAdminId = $_SESSION["logedBranchAdminInfo"]["adminId"];
        $loginAdminType = $_SESSION["logedBranchAdminInfo"]["adminType"];
        

        $grnPoNumber = $INPUTS["invoicePoNumber"];
        $grnCode = $INPUTS["grnCode"];
        $documentNo = $INPUTS["documentNo"];
        $documentDate = $INPUTS["documentDate"];
        $invoicePostingDate = $INPUTS["invoicePostingDate"];
        $invoiceDueDate = $INPUTS["invoiceDueDate"];
        $invoiceDueDays = $INPUTS["invoiceDueDays"] ?? "";
        $vendorCode = $INPUTS["vendorCode"];
        $vendorName = $INPUTS["vendorName"];
        $vendorGstin = $INPUTS["vendorGstin"];
        $totalInvoiceCGST = $INPUTS["totalInvoiceCGST"];
        $totalInvoiceSGST = $INPUTS["totalInvoiceSGST"];
        $totalInvoiceIGST = $INPUTS["totalInvoiceIGST"];
        $totalInvoiceSubTotal = $INPUTS["totalInvoiceSubTotal"];
        $totalInvoiceTotal = $INPUTS["totalInvoiceTotal"];
        $grnItemCode = $INPUTS["grnItemCode"];
        $grnItemHsn = $INPUTS["grnItemHsn"];
        $grnItemName = $INPUTS["grnItemName"];
        $grnItemQty = $INPUTS["grnItemQty"];
        $grnItemTax = $INPUTS["grnItemTax"];
        $grnItemUnitPrice = $INPUTS["grnItemUnitPrice"];
        $grnItemTotalPrice = $INPUTS["grnItemTotalPrice"];
        $grnItemReceivedQty = $INPUTS["grnItemReceivedQty"];

            
        $grnApprovedStatus="pending";
        if($grnItemReceivedQty==$grnItemQty){
            $isCompanyPoEnabled = $this->getCompanyDetails()["isPoEnabled"] ?? "";
            if($isCompanyPoEnabled){
                if($grnPoNumber!=""){
                    $grnApprovedStatus = "approved";
                }
            }else{
                $grnApprovedStatus = "approved";
            }
        }else{
            $grnApprovedStatus="pending";
        }
        

        $inserGrnObj = queryInsert('INSERT INTO `'.ERP_GRN.'` SET 
                            `companyId`="'.$loginCompanyId.'",
                            `branchId`="'.$loginBranchId.'",
                            `locationId`="'.$loginLocationId.'",
                            `functionalAreaId`="",
                            `grnCode`="'.$grnCode.'",
                            `grnPoNumber`="'.$grnPoNumber.'",
                            `vendorId`="",
                            `vendorCode`="'.$vendorCode.'",
                            `vendorGstin`="'.$vendorGstin.'",
                            `vendorName`="'.$vendorName.'",
                            `vendorDocumentNo`="'.$documentNo.'",
                            `vendorDocumentDate`="'.$documentDate.'",
                            `postingDate`="'. $invoicePostingDate.'",
                            `dueDate`="'.$invoiceDueDate.'",
                            `dueDays`="'.$invoiceDueDays.'",
                            `grnSubTotal`="'.$totalInvoiceSubTotal.'",
                            `grnTotalCgst`="'.$totalInvoiceCGST.'",
                            `grnTotalSgst`="'.$totalInvoiceSGST.'",
                            `grnTotalIgst`="'.$totalInvoiceIGST.'",
                            `grnTotalAmount`="'.$totalInvoiceTotal.'",
                            `grnCreatedBy`="'.$loginAdminId.'",
                            `grnUpdatedBy`="'.$loginAdminId.'",
                            `grnApprovedStatus`="'.$grnApprovedStatus.'"');
        

        if($inserGrnObj["status"]!="success"){
            return $inserGrnObj;
        }else{
            $grnId = $inserGrnObj["insertedId"];
            $noItem = count($grnItemCode); 
            
            $errorsInGrnItemsAdd = 0;

            $sqls = "";

            // insert all GRN items
            for($itemKey = 0; $itemKey < $noItem; $itemKey++){
                $oneItemCode = $grnItemCode[$itemKey];
                $oneItemHsn = $grnItemHsn[$itemKey];
                $oneItemName = $grnItemName[$itemKey];
                $oneItemQty = $grnItemQty[$itemKey];
                $oneItemTax = $grnItemTax[$itemKey];
                $oneItemUnitPrice = $grnItemUnitPrice[$itemKey];
                $oneItemTotalPrice = $grnItemTotalPrice[$itemKey];
                $oneItemReceivedQty = $grnItemReceivedQty[$itemKey];

                $oneItemInsertQuery = 'INSERT INTO `'.ERP_GRN_GOODS.'` SET `grnId`="'.$grnId.'",`grnCode`="'.$grnCode.'",`goodName`="'.$oneItemName.'",`goodDesc`="",`goodCode`="'.$oneItemCode.'",`goodHsn`="'.$oneItemHsn.'",`goodQty`="'.$oneItemQty.'",`receivedQty`="'.$oneItemReceivedQty.'",`unitPrice`="'.$oneItemUnitPrice.'",`totalAmount`="'.$oneItemTotalPrice.'",`grnGoodCreatedBy`="'.$loginAdminId.'",`grnGoodUpdatedBy`="'.$loginAdminId.'"';

                $sqls.=" => ".$oneItemInsertQuery;
                $oneItemInsertObj = queryInsert($oneItemInsertQuery);

                if($oneItemInsertObj["status"]!="success"){
                    $errorsInGrnItemsAdd++;
                }
                
            }

            // Row material stocks entry
            $errorsInGrnIndivisualItemStockAdd = 0;  

            $rmStockStatus = ($grnApprovedStatus=="approved") ? "active" : "pending";

            if($errorsInGrnItemsAdd==0){
                for($itemKey = 0; $itemKey < $noItem; $itemKey++){
                    $oneItemCode = $grnItemCode[$itemKey];
                    $oneItemHsn = $grnItemHsn[$itemKey];
                    $oneItemName = $grnItemName[$itemKey];
                    $oneItemQty = $grnItemQty[$itemKey];
                    $oneItemTax = $grnItemTax[$itemKey];
                    $oneItemUnitPrice = $grnItemUnitPrice[$itemKey];
                    $oneItemTotalPrice = $grnItemTotalPrice[$itemKey];
                    $oneItemReceivedQty = $grnItemReceivedQty[$itemKey];

                    $oneItemRmStockObj = queryInsert('INSERT INTO `'.ERP_RM_STOCKS.'` SET `itemCode`="'.$oneItemCode.'",`purchaseOrderNo`="'.$grnPoNumber.'",`productionOrderNo`="",`lotNo`="",`batchNo`="",`storageLocation`="",`itemQuantity`="'.$oneItemReceivedQty.'",`baseUnit`="",`itemPrice`="'.$oneItemTotalPrice.'",`stockStatus`="open", `createdBy`="'.$loginAdminId.'",`updatedBy`="'.$loginAdminId.'", `rmStockStatus`="'.$rmStockStatus.'"');
                    
                    if($oneItemRmStockObj["status"]!="success"){
                        $errorsInGrnIndivisualItemStockAdd++;
                    }
                }
            }

            // Update Row material stocks
            $errorsInGrnIndivisualItemStockUpdate = 0;
            if($errorsInGrnIndivisualItemStockAdd==0 && $grnApprovedStatus=="approved"){
                for($itemKey = 0; $itemKey < $noItem; $itemKey++){
                    $oneItemCode = $grnItemCode[$itemKey];
                    $oneItemHsn = $grnItemHsn[$itemKey];
                    $oneItemName = $grnItemName[$itemKey];
                    $oneItemQty = $grnItemQty[$itemKey];
                    $oneItemTax = $grnItemTax[$itemKey];
                    $oneItemUnitPrice = $grnItemUnitPrice[$itemKey];
                    $oneItemTotalPrice = $grnItemTotalPrice[$itemKey];
                    $oneItemReceivedQty = $grnItemReceivedQty[$itemKey];

                    $updateItemRmStockObj = queryUpdate('UPDATE `'.ERP_INVENTORY_ITEMS.'` SET `itemOpenStocks`=(`itemOpenStocks`+'.$oneItemReceivedQty.') WHERE `itemCode`="'.$oneItemCode.'"');
                    
                    if($updateItemRmStockObj["status"]!="success"){
                        $errorsInGrnIndivisualItemStockUpdate++;
                    }
                }
            }
            

            if($errorsInGrnItemsAdd == 0 && $errorsInGrnIndivisualItemStockAdd == 0 && $errorsInGrnIndivisualItemStockUpdate==0){
                return [
                    "status"=> "success",
                    "message"=> ($grnApprovedStatus=="pending")?"GRN posted successfully, waiting for 'approval'":"GRN posted successfully."
                ];
            }else{
                return [
                    "status"=> "warning",
                    "message"=> "GRN posted failed, try again!",
                    "errorsInGrnItemsAdd"=>$errorsInGrnItemsAdd,
                    "errorsInGrnIndivisualItemStockAdd"=>$errorsInGrnIndivisualItemStockAdd,
                    "errorsInGrnIndivisualItemStockUpdate"=>$errorsInGrnIndivisualItemStockUpdate

                ];
            }
        }
    }

    
}


?>