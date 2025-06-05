<?php
include_once("../../app/v1/connection-company-admin.php");
include("../../app/v1/functions/company/func-ChartOfAccounts.php");
$glid = $_GET['glid'];
$gldetailsfnc = getChartOfAccountsDataDetails($glid);
$gldetails=$gldetailsfnc['data'];
// console($gldetails);

if ($gldetails['glStType'] == 'group') {
?>
    
    <div class="col-md-6 col-12">             
        <input type="hidden" name="editdata" id="editdata" value="group">
        <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id; ?>">
        <input type="hidden" name="id" id="id" value="<?php echo $glid; ?>">
        <div class="form-input">

            <label for="">Select Parent*</label>
            <select id="gp_id" name="p_id" class="form-control" disabled required>
                <option value="">Select Parent*</option>
                <?php
                $listResult = getAllChartOfAccounts_listGroup($company_id);
                if ($listResult["status"] == "success") {
                    foreach ($listResult["data"] as $listRow) {
                ?>

                        <option value="<?php echo $listRow['id']; ?>" <?php if($gldetails['p_id']==$listRow['id']){ echo 'selected';}?>><?php echo $listRow['gl_label']; ?>
                        </option>
                <?php }
                } ?>
            </select>
            <input type="hidden" name="gl_code" id="ggl_code" value="<?= $gldetails['gl_code'];?>">
            <input type="hidden" name="personal_glcode_lvl" id="gpersonal_glcode_lvl" value="<?= $gldetails['lvl'];?>">
            <input type="hidden" name="typeAcc" id="gpersonal_typeAcc" value="<?= $gldetails['typeAcc'];?>">
            <input type="hidden" class="form-control" id="ggl_code_preview" name="gl_code_preview" value="<?= $gldetails['gl_code'];?>" readonly required>
        </div>

        <span class="error " id="gp_id_error"></span>
    </div>

    
    <div class="col-lg-6 col-md-6 col-sm-12">

        <div class="form-input">

            <label for="">Group Name</label>

            <input type="text" class="form-control" id="gl_label" name="gl_label" value="<?= $gldetails['gl_label'];?>" required>
            <span class="error"></span>

        </div>

    </div>

    <div class="col-lg-12 col-md-12 col-sm-12">

        <div class="form-input">

            <label for="">Group Description</label>

            <input type="text" class="form-control" id="remark" name="remark" value="<?= $gldetails['remark'];?>">

            <span class="error"></span>

        </div>

    </div>
    <div class="col-md-12">
        <div class="note mt-3">
            <p class="text-xs"><i>Note: Only the name and description can be edited but hierarchical position can't be changed. And delete will be allowed only in case the group or any associate group or GL code is not in used and in unlock status.</i></p>
        </div>
    </div>

<?php } else { ?>

    <div class="col-lg-6 col-md-6 col-sm-12">        
        <input type="hidden" name="editdata" id="editdata" value="account">
        <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id; ?>">
        <input type="hidden" name="id" id="id" value="<?php echo $glid; ?>">
        <div class="form-input">

            <label for="">Select Parent*</label>

            <select id="ap_id" name="p_id" class="form-control"  required>
                <option value="">Select Parent*</option>
                <?php
                // $coaSql = "SELECT coaTable.* FROM
                //                     " . ERP_ACC_CHART_OF_ACCOUNTS . " coaTable
                //                     WHERE
                //                         coaTable.company_id = $company_id 
                //                         AND coaTable.glStType = 'group'
                //                         AND coaTable.typeAcc = '".$gldetails['typeAcc']."'
                //                         AND coaTable.id NOT IN (
                //                             SELECT
                //                                 innerTable.`p_id`
                //                             FROM
                //                             " . ERP_ACC_CHART_OF_ACCOUNTS . " innerTable
                //                             WHERE innerTable.glStType = 'group' AND innerTable.`p_id`=coaTable.`id` 
                //                             ) 
                //                         AND coaTable.`status` != 'deleted'
                //                     ORDER BY coaTable.gl_code";
                $coaSql = "SELECT coaTable.* FROM
                                    " . ERP_ACC_CHART_OF_ACCOUNTS . " coaTable
                                    WHERE
                                        coaTable.company_id = $company_id 
                                        AND coaTable.glStType = 'group'
                                        AND coaTable.typeAcc = '".$gldetails['typeAcc']."'                                        
                                        AND coaTable.`status` != 'deleted'
                                    ORDER BY coaTable.gl_code";                  
                $listResult = queryGet($coaSql, true);

                if ($listResult["status"] == "success") {
                    foreach ($listResult["data"] as $listRow) {
                ?>
                        <option value="<?php echo $listRow['id']; ?>" <?php if($gldetails['p_id']==$listRow['id']){ echo 'selected';}?>><?php echo $listRow['gl_label']; ?>
                        </option>
                <?php }
                } ?>
            </select>

            <input type="hidden" name="gl_code" id="agl_code" value="<?= $gldetails['gl_code'];?>">
            <input type="hidden" name="personal_glcode_lvl" id="apersonal_glcode_lvl" value="<?= $gldetails['lvl'];?>">
            <input type="hidden" name="typeAcc" id="apersonal_typeAcc" value="<?= $gldetails['typeAcc'];?>">
        </div>

        <span class="error " id="ap_id_error"></span>

    </div>

    <div class="col-lg-6 col-md-6 col-sm-12">

        <div class="form-input">

            <label for="">A/C Code</label>

            <input type="text" class="form-control" id="agl_code_preview" name="gl_code_preview" value="<?= $gldetails['gl_code'];?>" readonly required>

            <span class="error"></span>

        </div>

    </div>

    <div class="col-lg-12 col-md-12 col-sm-12">

        <div class="form-input">

            <label for="">Account Name</label>

            <input type="text" class="form-control" id="gl_label" name="gl_label" value="<?= $gldetails['gl_label'];?>" required>
            <span class="error"></span>

        </div>

    </div>

    <div class="col-lg-12 col-md-12 col-sm-12">

        <div class="form-input">

            <label for="">Account Description</label>

            <input type="text" class="form-control" id="remark" name="remark" value="<?= $gldetails['remark'];?>">

            <span class="error"></span>

        </div>

    </div>
    <div class="col-md-12">
        <div class="note mt-3">
            <p class="text-xs"><i>Note: The name and description can be edited and if the hierarchical position is changed then the Code will be automatically updated as per it's position. And delete will be allowed only in case the GL code is not in used and in unlock status.</i></p>
        </div>
    </div>

<?php } ?>