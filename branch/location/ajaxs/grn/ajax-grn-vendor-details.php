<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-po-controller.php");
$BranchPoObj = new BranchPo();
if (isset($_GET["po"]) && $_GET["po"] != "") {
    global $company_id;
    global $companyCountry;
    global $branch_id;
    global $location_id;

  

    $lable = (getLebels($companyCountry)['data']);
    $lable = json_decode($lable, true);
    // console($lable['fields']['businessTaxID']);
    $tdslable = ($lable['source_taxation']);
    $abnlable = $lable['fields']['businessTaxID'];
    $po = $_GET["po"];


    $poDetailsObj = queryGet("SELECT * FROM `erp_branch_purchase_order` LEFT JOIN `erp_vendor_details` ON erp_vendor_details.vendor_id = erp_branch_purchase_order.vendor_id WHERE erp_branch_purchase_order.po_number = '" . $po . "' AND erp_branch_purchase_order.company_id='" . $company_id . "' AND erp_branch_purchase_order.branch_id='" . $branch_id . "' AND erp_branch_purchase_order.location_id='" . $location_id . "'", false);
    $poDetails = $poDetailsObj["data"] ?? [];



    if ($poDetailsObj["numRows"] == 0) {
    } else {


        $vendorName = $poDetails["trade_name"];
        $vendorId = $poDetails["vendor_id"];
        $vendorCreditPeriod = $poDetails["vendor_credit_period"];
        $vendorCode = $poDetails["vendor_code"];
        $vendorGstin = $poDetails["vendor_gstin"];
        $vendor_id = $poDetails["vendor_id"];

        $loginBranchGstin = "";
        $branchDeails = [];
        $branchDeailsObj = queryGet("SELECT `erp_branches`.*,`erp_companies`.`company_name`, `erp_companies`.`company_pan`,`erp_companies`.`company_const_of_business` FROM `erp_branches`, `erp_companies` WHERE `erp_branches`.`company_id`=`erp_companies`.`company_id` AND `branch_id`=" . $branch_id);
        if ($branchDeailsObj["status"] == "success") {
            $branchDeails = $branchDeailsObj["data"];
            $loginBranchGstin = $branchDeails["branch_gstin"];
        } else {
        }

        $customerGstin = $loginBranchGstin;
        $customerGstinStateCode = substr($customerGstin, 0, 2);

        if ($vendorGstin == "" || $vendorGstin == NULL || !isset($vendorGstin)) {
            $vendorGstinStateCode = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=" . $vendor_id . " AND `vendor_business_primary_flag`='1' ORDER BY `vendor_business_id` DESC", false)["data"]["state_code"] ?? "";
        } else {
            $vendorGstinStateCode = substr($vendorGstin, 0, 2);
        }

        $vendorGstinStateName = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateCode`=" . $vendorGstinStateCode)["data"]["gstStateName"] ?? "";
        $customerGstinStateName = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateCode`=" . $customerGstinStateCode)["data"]["gstStateName"] ?? "";

        $check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
        $check_var_data = $check_var_sql['data'];
        // console($_SESSION);
        // // console($check_var_sql);
        // console($check_var_sql);
        $max = $check_var_data['month_end'];
        $min = $check_var_data['month_start'];


        $curlGst = curl_init();
        curl_setopt_array($curlGst, array(
            CURLOPT_URL => 'https://api.mastergst.com/public/search?email=developer@vitwo.in&gstin=' . $vendorGstin,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'client_id: GSPfc3af0aa-1ae5-45be-8f6f-34d0baa63594',
                'client_secret: GSP6e50f5dd-7787-4c7d-a576-a5f8d9ffe5f6',
                'Accept: application/json'
            ),
        ));

        $resultGst = curl_exec($curlGst);

        $resultGstData = json_decode($resultGst, true);

        $gstSTatus = $resultGstData["data"]["sts"] ?? "Inactive";

        if($companyCountry!=103)
{
    $b_places = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=$vendorId AND `vendor_business_primary_flag` = 1");
                                                            $b_row = $b_places['data'];
                                                            $vendorGstinStateName=$b_row['vendor_business_state'];
                                                            
                                                            $venderabn = queryGet("SELECT * FROM `erp_vendor_details` WHERE vendor_id=$vendorId");
    
                                                $abn = $venderabn['data'];
                                                $vendorGstin=$abn['vendor_gstin'];
}

?>
        <div class="po-grn-vendor-details vendorClass_<?= $vendorId ?>">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <h2><?= $vendorName ?>
                    <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary po-vendor-info-btn" data-bs-toggle="modal" data-bs-target="#vendordetailmodal_<?= $vendorId ?>">
                        <ion-icon name="information-outline"></ion-icon>
                    </button>
                    <button type="button" class="btn btn-primary file-input">
                        <ion-icon name="cloud-upload-outline"></ion-icon>
                        Upload
                        <input type="file" name="invoice_file_name[<?= $vendorId ?>]" id="fileInput" class="form-control">
                        <!-- <span class="button">Choose</span>--->
                        <!-- <span class="label" data-js-label>No file selected</label> -->
                    </button>
                </div>
                </h2>
                </div>
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="vendor-blocks">

                        <div class="doc-detail doc-no">
                            <label for=""><ion-icon name="document-text-outline"></ion-icon>Document No</label>
                            <input type="text" name="documentNo[<?= $vendorId ?>]" id="documentdate<?= $vendorId ?>" value="" class="form-control" required>
                        </div>

                        <div class="doc-detail doc-date">
                            <label for=""><ion-icon name="calendar-outline"></ion-icon>Document Date</label>
                            <input type="date" id="documentdate_<?= $vendorId ?>" name="documentDate[<?= $vendorId ?>]" value="" class="form-control ddate" required>
                           
                            <input type="hidden" id="creditp_<?= $vendorId ?>" value="<?= $vendorCreditPeriod ?>">
                            
                        </div>

                        <div class="doc-detail due-date grn-form-input-text">

                            <?php
                            if ($dueDate == "" && $vendorCreditPeriod != "" && $max != "") {
                                $tempDueDate = date_create($max);
                                date_add($tempDueDate, date_interval_create_from_date_string($vendorCreditPeriod . " days"));
                                $dueDate = date_format($tempDueDate, "Y-m-d");
                            }
                            ?>
                            <label for=""> <ion-icon name="time-outline"></ion-icon>Due Date </label>
                            <input type="date" id="iv_due_date" name="invoiceDueDate[<?= $vendorId ?>]" value="<?= date("Y-m-d", strtotime($dueDate)); ?>" class="form-control" required>
                            <p class="text-danger text-xs" id="postdatelabel"></p>
                        </div>


                    </div>
                </div>
            </div>

            <div class="po-vendor-area">
                <div class="modal fade" id="vendordetailmodal_<?= $vendorId ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Vendor info</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="vendorCode" id="invoiceVendorCodeInput" value="<?= $vendorCode ?>" class="form-control" />
                                <input type="hidden" name="vendorId" id="invoiceVendorIdInput" value="<?= $vendorId ?>" class="form-control" />
                                <input type="hidden" name="vendorName" id="vendorNameInput" value="<?= $vendorName ?>" class="form-control" />
                                <input type="hidden" name="vendorGstin" value="<?= $vendorGstin ?>" class="form-control" />
                                <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Code :&nbsp;</p>
                                    <p id="invoiceVendorCodeSpan"><?= $vendorCode ?></p>
                                </div>
                                <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Name :&nbsp;</p>
                                    <p id="vendorName"><?= $vendorName ?></p>
                                </div>
                                <?php
                                if ($vendorCode != "") {
                                ?>
                                    <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Status :&nbsp;</p>
                                        <p class="status">Active</p>
                                    </div>
                                <?php
                                }
                                ?>
                                <?php if($companyCountry==103){ ?>
                                <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">GSTIN :&nbsp;</p>
                                    <p> <?= $vendorGstin ?></p>
                                </div>
                                <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">GSTIN Status :&nbsp;</p>
                                    <p id="vendorGstinStatus_<?= $vendorId ?>" class="status"><?= $gstSTatus; ?></p>
                                </div>
                                
                                <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">State :&nbsp;</p>
                                    <p><?= $vendorGstinStateName ?>(<?= $vendorGstinStateCode ?>)</p>
                                </div>
                                <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Customer State :&nbsp;</p>
                                    <p><?= $customerGstinStateName ?>(<?= $customerGstinStateCode ?>)</p>
                                </div>
                                <?php } else{ ?>
                                    <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold"><?= $abnlable ?> :&nbsp;</p>
                                                            <p> <?= $vendorGstin ?></p>
                                                        </div>
                                                        
                                                        
                                                        <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">State :&nbsp;</p>
                                                            <p><?= $vendorGstinStateName ?></p>
                                                        </div>
                                    <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php

    }
}

?>