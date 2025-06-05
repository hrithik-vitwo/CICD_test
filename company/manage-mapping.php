<?php
include("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/company/func-ChartOfAccounts.php");
include("../app/v1/functions/admin/func-company.php");

?>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../public/assets/mapping.css">
<link rel="stylesheet" href="../public/assets/listing.css">
<?php
$company_id = $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"];
if (isset($_POST["update_frm"])) {
    $editDataObj = updateDataGLMapping($_POST, $company_id);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

$MappingTblArr = getAllfetchAccountingMappingTbl($company_id);
if ($MappingTblArr['status'] == "success") {
    $fetchAccountingMappingTbl = $MappingTblArr['data']['0'];
}

$company_data = getCompanyDataDetails($company_id);
$gl_account_length = $company_data['data']['gl_account_length'];
?>
<style>
    .multisteps-form__form {
        height: auto !important;
    }
</style>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <div class="container-fluid">

        <h3 class="card-title mb-4">G/L Mapping</h3>

        <div class="card mapping-section">

            <div class="card-header card-mapping p-2">

                <div class="row">

                    <div class="col-lg-6 col-md-6 col-sm-6">

                        <h4 class="text-white mb-0 text-xs">Heads</h4>

                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6">

                        <h4 class="text-white mb-0 text-xs">G/L Accounts</h4>

                    </div>

                </div>

            </div>

            <div class="card-body">

                <form class="multisteps-form__form" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="update_frm" name="update_frm">

                    <div class="row" style="row-gap: 15px;">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Vendor G/L</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="vendor_gl" id="vendor_gl" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                //console($listResult);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['vendor_gl']) && ($fetchAccountingMappingTbl['vendor_gl'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Customer G/L</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="customer_gl" id="customer_gl" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['customer_gl']) && ($fetchAccountingMappingTbl['customer_gl'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>

                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Item(RM G/L)</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="itemsRM_gl" id="itemsRM_gl" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['itemsRM_gl']) && ($fetchAccountingMappingTbl['itemsRM_gl'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Item (FG G/L)</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="itemsFG_gl" id="itemsFG_gl" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['itemsFG_gl']) && ($fetchAccountingMappingTbl['itemsFG_gl'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>

                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Item (SFG G/L)</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="itemsSFG_gl" id="itemsSFG_gl" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['itemsSFG_gl']) && ($fetchAccountingMappingTbl['itemsSFG_gl'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Billable Project G/L</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="billable_project_gl" id="billable_project_gl" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['billable_project_gl']) && ($fetchAccountingMappingTbl['billable_project_gl'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Bank G/L</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="bank_gl" id="bank_gl" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['bank_gl']) && ($fetchAccountingMappingTbl['bank_gl'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Cash G/L</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="cash_gl" id="cash_gl" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['cash_gl']) && ($fetchAccountingMappingTbl['cash_gl'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
<hr>

                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Round-Off G/L</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="roundoff_gl" id="roundoff_gl" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['roundoff_gl']) && ($fetchAccountingMappingTbl['roundoff_gl'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                        
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Gain/Loss on Foreign Exchange G/L</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="foreignexchange_gl" id="foreignexchange_gl" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['foreignexchange_gl']) && ($fetchAccountingMappingTbl['foreignexchange_gl'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                        
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Liabilities Written Back G/L</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="writtenback_gl" id="writtenback_gl" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['writtenback_gl']) && ($fetchAccountingMappingTbl['writtenback_gl'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                        
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Bank Charges G/L</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="bankcharges_gl" id="bankcharges_gl" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['bankcharges_gl']) && ($fetchAccountingMappingTbl['bankcharges_gl'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                        
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Sales Goods (Domestic) G/L</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="sales_goods_domestic" id="sales_goods_domestic" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['sales_goods_domestic']) && ($fetchAccountingMappingTbl['sales_goods_domestic'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>


                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Sales Goods (Export) G/L</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="sales_goods_export" id="sales_goods_export" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['sales_goods_export']) && ($fetchAccountingMappingTbl['sales_goods_export'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>


                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Sales Services (Domestic) G/L</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="sales_services_domestic" id="sales_services_domestic" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['sales_services_domestic']) && ($fetchAccountingMappingTbl['sales_services_domestic'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>


                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Sales Services (Export) G/L</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="sales_services_export" id="sales_services_export" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['sales_services_export']) && ($fetchAccountingMappingTbl['sales_services_export'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>


                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Price Difference G/L</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="price_difference_gl" id="price_difference_gl" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['price_difference_gl']) && ($fetchAccountingMappingTbl['price_difference_gl'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <p class="text-xs">Stock Difference G/L</p>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <select name="stock_difference_gl" id="stock_difference_gl" class="form-control select2 mapping-hidden-btn" required>
                                <option value="">Select Account</option>
                                <?php
                                $listResult = getAllChartOfAccountsByconditionForMapping($company_id);
                                if ($listResult["status"] == "success") {
                                    foreach ($listResult["data"] as $listRow) {
                                ?>
                                        <option <?php if (isset($fetchAccountingMappingTbl['stock_difference_gl']) && ($fetchAccountingMappingTbl['stock_difference_gl'] == $listRow['id'])) {
                                                    echo "selected";
                                                } ?> value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label'] . '[' . $listRow['gl_code'] . ']'; ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>



                    </div>


            </div>


          
            <div class="btn-update mt-0 mr-4 mb-4">
                <button name="update_frm" class="btn btn-primary update-btn">Update</button>
            </div>
            </form>
        </div>
    </div>


    <!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->


<?php
include("common/footer.php");
?>
<script>
    $(".edit_data").click(function() {
        var data = this.value;
        $("#editdata").val(data);
        alert(data);
        //$( "#edit_frm" ).submit();
    });
</script>
<script>
    function leaveInput(el) {
        if (el.value.length > 0) {
            if (!el.classList.contains('active')) {
                el.classList.add('active');
            }
        } else {
            if (el.classList.contains('active')) {
                el.classList.remove('active');
            }
        }
    }

    var inputs = document.getElementsByClassName("m-input");
    for (var i = 0; i < inputs.length; i++) {
        var el = inputs[i];
        el.addEventListener("blur", function() {
            leaveInput(this);
        });
    }

    $('.select2').select2();

    // *** autocomplite select *** //
    wow = new WOW({
        boxClass: 'wow', // default
        animateClass: 'animated', // default
        offset: 0, // default
        mobile: true, // default
        live: true // default
    })
    wow.init();
</script>