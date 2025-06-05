<?php

class OcrInvoiceController
{
    function readInvoice($invoiceUrl = 'http://ocrserver.centralindia.cloudapp.azure.com:8000/media/NK.pdf')
    {
        $returnData = [];
        $curl = curl_init();
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'http://ocrserver.centralindia.cloudapp.azure.com:8000/api/v1/ocr/invoice/',
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'POST',
        //     CURLOPT_POSTFIELDS => array('invoice_url' => $invoiceUrl),
        // ));
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ocr.vitwo.ai/api/v1/ocr/invoice/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('invoice_url' => $invoiceUrl),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response, true);
        $_SESSION['ocr_limitation'] = $_SESSION['ocr_limitation'] - 1;
        if (isset($responseData['payload'])) {
            $returnData = [
                "status" => "success",
                "message" => "Successfully read the invoice data",
                "invoiceUrl" => $invoiceUrl,
                "data" => $responseData["payload"]
            ];
        } else {
            $returnData = [
                "status" => "warning",
                "message" => "Failed to read the invoice data, try again",
                "invoiceUrl" => $invoiceUrl,
                "data" => [],
                "responseData" => $response
            ];
        }
        return $returnData;
    }

    function processInvoiceRawData($invoiceRowData = [], $customerGstin = null)
    {
        if (count($invoiceRowData) < 0) {
            return [
                "status" => "error",
                "message" => "Invalid invoice raw data",
                "data" => []
            ];
        }

        $processedInvoiceData = $invoiceRowData;
        $processedItemsData = $invoiceRowData["Items"];

        // identify vendor and customer gstin
        if ($invoiceRowData["gstin_data"][0] == $customerGstin) {
            $processedInvoiceData["CustomerTaxId"] = $invoiceRowData["gstin_data"][0] ?? "";
            $processedInvoiceData["VendorTaxId"] = $invoiceRowData["gstin_data"][1] ?? "";
        } else {
            $processedInvoiceData["CustomerTaxId"] = $invoiceRowData["gstin_data"][1] ?? "";
            $processedInvoiceData["VendorTaxId"] = $invoiceRowData["gstin_data"][0] ?? "";
        }

        // item wise tax and quantity check and amount calculation
        $tmpInvoiceSubTotal = $cgstTempTotalTax = $sgstTempTotalTax = $igstTempTotalTax = 0;
        foreach ($invoiceRowData["Items"] as $oneItemKey => $oneItem) {
            $processedItemsData[$oneItemKey]["Quantity"] = $oneItem["Quantity"] ?? 1;
            if (!isset($oneItem["UnitPrice"])) {
                if (isset($oneItem["Tax"])) {
                    if (substr($processedInvoiceData["CustomerTaxId"], 0, 2) == substr($processedInvoiceData["VendorTaxId"], 0, 2)) {
                        $processedItemsData[$oneItemKey]["UnitPrice"] = $oneItem["Amount"] - ($oneItem["Tax"] * 2);
                    } else {
                        $processedItemsData[$oneItemKey]["UnitPrice"] = $oneItem["Amount"] - $oneItem["Tax"];
                    }
                } else {
                    $processedItemsData[$oneItemKey]["UnitPrice"] = $oneItem["Amount"];
                }
            }
            else
            {
                $processedItemsData[$oneItemKey]["UnitPrice"] = $oneItem["UnitPrice"];
            }

            $processedItemsData[$oneItemKey]["NetAmount"] = $processedItemsData[$oneItemKey]["Quantity"] * $processedItemsData[$oneItemKey]["UnitPrice"];
            // Item Tax Information
            $processedItemsData[$oneItemKey]["cgstTax"] = "";
            $processedItemsData[$oneItemKey]["sgstTax"] = "";
            $processedItemsData[$oneItemKey]["igstTax"] = "";
            

            if ($processedItemsData[$oneItemKey]["NetAmount"] != $oneItem["Amount"]) {



                // if (isset($oneItem["Tax"])) {
                //     $netVal = $processedItemsData[$oneItemKey]["NetAmount"] + $oneItem["Tax"];
                //     $netValcgstSgst = $netVal+$oneItem["Tax"];
                //     if (intval($netVal) == intval($oneItem["Amount"])) {
                //         $igstTempTotalTax += $processedItemsData[$oneItemKey]["igstTax"] = $oneItem["Tax"];
                //     } elseif (intval($netValcgstSgst) == intval($oneItem["Amount"])) {
                //         $cgstTempTotalTax += $processedItemsData[$oneItemKey]["cgstTax"] = $oneItem["Tax"];
                //         $sgstTempTotalTax += $processedItemsData[$oneItemKey]["sgstTax"] = $oneItem["Tax"]; 
                //     }
                // } else {
                //     if (substr($processedInvoiceData["CustomerTaxId"], 0, 2) == substr($processedInvoiceData["VendorTaxId"], 0, 2)) {
                //         $cgstTempTotalTax += $processedItemsData[$oneItemKey]["cgstTax"] = ($oneItem["Amount"] - $processedItemsData[$oneItemKey]["NetAmount"]) / 2;
                //         $sgstTempTotalTax += $processedItemsData[$oneItemKey]["sgstTax"] = ($oneItem["Amount"] - $processedItemsData[$oneItemKey]["NetAmount"]) / 2;
                //     } else {
                //         $igstTempTotalTax += $processedItemsData[$oneItemKey]["igstTax"] = $oneItem["Amount"] - $processedItemsData[$oneItemKey]["NetAmount"];
                //     }
                // }

                if (isset($oneItem["Tax"])) {
                    if (substr($processedInvoiceData["CustomerTaxId"], 0, 2) == substr($processedInvoiceData["VendorTaxId"], 0, 2)) {
                        if (intval($processedItemsData[$oneItemKey]["NetAmount"] + $oneItem["Tax"]) == intval($oneItem["Amount"])) {
                            $cgstTempTotalTax += $processedItemsData[$oneItemKey]["cgstTax"] = $oneItem["Tax"]/2;
                            $sgstTempTotalTax += $processedItemsData[$oneItemKey]["sgstTax"] = $oneItem["Tax"]/2; 
                        } elseif (intval($processedItemsData[$oneItemKey]["NetAmount"] + ($oneItem["Tax"] * 2)) == intval($oneItem["Amount"])) {
                            $cgstTempTotalTax += $processedItemsData[$oneItemKey]["cgstTax"] = $oneItem["Tax"];
                            $sgstTempTotalTax += $processedItemsData[$oneItemKey]["sgstTax"] = $oneItem["Tax"];
                        }
                        elseif($oneItem["Amount"] - ($processedItemsData[$oneItemKey]["NetAmount"] + $oneItem["Tax"]) <= 2)
                        {
                            $cgstTempTotalTax += $processedItemsData[$oneItemKey]["cgstTax"] = $oneItem["Tax"]/2;
                            $sgstTempTotalTax += $processedItemsData[$oneItemKey]["sgstTax"] = $oneItem["Tax"]/2; 
                        }
                        elseif($oneItem["Amount"] - ($processedItemsData[$oneItemKey]["NetAmount"] + ($oneItem["Tax"]*2)) <= 2)
                        {
                            $cgstTempTotalTax += $processedItemsData[$oneItemKey]["cgstTax"] = $oneItem["Tax"];
                            $sgstTempTotalTax += $processedItemsData[$oneItemKey]["sgstTax"] = $oneItem["Tax"];
                        }
                        else
                        {
                            $cgstTempTotalTax += $processedItemsData[$oneItemKey]["cgstTax"] = $oneItem["Tax"];
                            $sgstTempTotalTax += $processedItemsData[$oneItemKey]["sgstTax"] = $oneItem["Tax"];
                        }
                    }
                    else
                    {
                        if (intval($processedItemsData[$oneItemKey]["NetAmount"] + $oneItem["Tax"]) == intval($oneItem["Amount"])) {
                            $igstTempTotalTax += $processedItemsData[$oneItemKey]["igstTax"] = $oneItem["Tax"];
                        } elseif (intval($processedItemsData[$oneItemKey]["NetAmount"] + ($oneItem["Tax"] * 2)) == intval($oneItem["Amount"])) {
                            $igstTempTotalTax += $processedItemsData[$oneItemKey]["igstTax"] = $oneItem["Tax"] * 2;
                        }
                        elseif($oneItem["Amount"] - ($processedItemsData[$oneItemKey]["NetAmount"] + $oneItem["Tax"]) <= 2)
                        {
                            $igstTempTotalTax += $processedItemsData[$oneItemKey]["igstTax"] = $oneItem["Tax"];
                        }
                        elseif($oneItem["Amount"] - ($processedItemsData[$oneItemKey]["NetAmount"] + ($oneItem["Tax"])*2) <= 2)
                        {
                            $igstTempTotalTax += $processedItemsData[$oneItemKey]["igstTax"] = $oneItem["Tax"] * 2;
                        }
                        else
                        {
                            $igstTempTotalTax += $processedItemsData[$oneItemKey]["igstTax"] = $oneItem["Tax"];
                        }
                    }

                }
                else{
                    $processedItemsData[$oneItemKey]["cgstTax"] = "";
                    $processedItemsData[$oneItemKey]["sgstTax"] = "";
                    $processedItemsData[$oneItemKey]["igstTax"] = "";
                }

            }
            $tmpInvoiceSubTotal += $processedItemsData[$oneItemKey]["NetAmount"];

        }

        $processedInvoiceData["cgstTotalTax"] = "";
        $processedInvoiceData["sgstTotalTax"] = "";
        $processedInvoiceData["igstTotalTax"] = "";
        //Rachel Code
        // if(isset($processedInvoiceData["TotalTax"])){
        //     if (intval($processedInvoiceData["SubTotal"] + $processedInvoiceData["TotalTax"]) == intval($processedInvoiceData["InvoiceTotal"])) {
        //         $processedInvoiceData["igstTotalTax"] = $processedInvoiceData["TotalTax"];
        //     } elseif (intval($processedInvoiceData["SubTotal"] + ($processedInvoiceData["TotalTax"] * 2)) == intval($processedInvoiceData["InvoiceTotal"])) {
        //         $processedInvoiceData["cgstTotalTax"] = $processedInvoiceData["sgstTotalTax"] = $processedInvoiceData["TotalTax"];
        //     }
        // }else{
        //     $processedInvoiceData["TotalTax"] = $cgstTempTotalTax + $sgstTempTotalTax + $igstTempTotalTax;
        //     $processedInvoiceData["cgstTotalTax"] = $cgstTempTotalTax > 0 ? $cgstTempTotalTax : "";
        //     $processedInvoiceData["sgstTotalTax"] = $sgstTempTotalTax > 0 ? $sgstTempTotalTax : "";
        //     $processedInvoiceData["igstTotalTax"] = $igstTempTotalTax > 0 ? $igstTempTotalTax : "";
        // }

        //Soumyajit's Code
        $processedInvoiceData["SubTotal"] = $processedInvoiceData["SubTotal"] ?? $tmpInvoiceSubTotal;
        
        if(isset($processedInvoiceData["TotalTax"])){
            if (substr($processedInvoiceData["CustomerTaxId"], 0, 2) == substr($processedInvoiceData["VendorTaxId"], 0, 2)) {
                if (intval($processedInvoiceData["SubTotal"] + $processedInvoiceData["TotalTax"]) == intval($processedInvoiceData["InvoiceTotal"])) {
                    $processedInvoiceData["cgstTotalTax"] = $processedInvoiceData["TotalTax"]/2;
                    $processedInvoiceData["sgstTotalTax"] = $processedInvoiceData["TotalTax"]/2;
                } elseif (intval($processedInvoiceData["SubTotal"] + ($processedInvoiceData["TotalTax"] * 2)) == intval($processedInvoiceData["InvoiceTotal"])) {
                    $processedInvoiceData["cgstTotalTax"] = $processedInvoiceData["sgstTotalTax"] = $processedInvoiceData["TotalTax"];
                }
                elseif($processedInvoiceData["InvoiceTotal"] - ($processedInvoiceData["SubTotal"] + $processedInvoiceData["TotalTax"]) <= 2)
                {
                    $processedInvoiceData["cgstTotalTax"] = $processedInvoiceData["TotalTax"]/2;
                    $processedInvoiceData["sgstTotalTax"] = $processedInvoiceData["TotalTax"]/2;
                }
                elseif($processedInvoiceData["InvoiceTotal"] - ($processedInvoiceData["SubTotal"] + ($processedInvoiceData["TotalTax"]*2)) <= 2)
                {
                    $processedInvoiceData["cgstTotalTax"] = $processedInvoiceData["sgstTotalTax"] = $processedInvoiceData["TotalTax"];
                }
                else
                {
                    $processedInvoiceData["cgstTotalTax"] = $processedInvoiceData["sgstTotalTax"] = $processedInvoiceData["TotalTax"];
                }
            }
            else
            {
                if (intval($processedInvoiceData["SubTotal"] + $processedInvoiceData["TotalTax"]) == intval($processedInvoiceData["InvoiceTotal"])) {
                    $processedInvoiceData["igstTotalTax"] = $processedInvoiceData["TotalTax"];
                } elseif (intval($processedInvoiceData["SubTotal"] + ($processedInvoiceData["TotalTax"] * 2)) == intval($processedInvoiceData["InvoiceTotal"])) {
                    $processedInvoiceData["igstTotalTax"] = $processedInvoiceData["TotalTax"] * 2;
                }
                elseif($processedInvoiceData["InvoiceTotal"] - ($processedInvoiceData["SubTotal"] + $processedInvoiceData["TotalTax"]) <= 2)
                {
                    $processedInvoiceData["igstTotalTax"] = $processedInvoiceData["TotalTax"];
                }
                elseif($processedInvoiceData["InvoiceTotal"] - ($processedInvoiceData["SubTotal"] + ($processedInvoiceData["TotalTax"]*2)) <= 2)
                {
                    $processedInvoiceData["igstTotalTax"] = $processedInvoiceData["TotalTax"] * 2;
                }
                else
                {
                    $processedInvoiceData["igstTotalTax"] = $processedInvoiceData["TotalTax"];
                }
            }
        }
        else
        {
            $processedInvoiceData["TotalTax"] = $cgstTempTotalTax + $sgstTempTotalTax + $igstTempTotalTax;
            $processedInvoiceData["cgstTotalTax"] = $cgstTempTotalTax > 0 ? $cgstTempTotalTax : "";
            $processedInvoiceData["sgstTotalTax"] = $sgstTempTotalTax > 0 ? $sgstTempTotalTax : "";
            $processedInvoiceData["igstTotalTax"] = $igstTempTotalTax > 0 ? $igstTempTotalTax : "";
        }


        $processedInvoiceData["Items"] = $processedItemsData;
        return [
            "status" => "success",
            "message" =>"Data successfully processed",
            "data" => $processedInvoiceData
        ];

    }
}
