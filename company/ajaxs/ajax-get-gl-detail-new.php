<?php
include_once("../../app/v1/connection-company-admin.php");
include("../../app/v1/functions/company/func-ChartOfAccounts.php");
$type = $_GET['type'];
$glsttype = $_GET['glsttype'];
$glid = $_GET['glid'];
$pid = $_GET['pid'];
$coatype = $_GET['coatype'];


$gldetailsfnc = getChartOfAccountsDataDetails($glid);
$gldetails = $gldetailsfnc['data'];
$lastAccsql = queryGet("SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE `company_id`='" . $company_id . "' AND typeAcc='" . $gldetails['typeAcc'] . "' AND `glStType`='account' ORDER BY `id` DESC");
$newAccCode = getCOASerialNumber($lastAccsql['data']['gl_code'], $gldetails['typeAcc']);
// console($gldetails);
if ($type == 'edit') {
    if ($gldetails['glStType'] == 'group') { ?>

        <div class="col-md-6 col-12">
            <input type="hidden" name="editdata" id="editdata" value="group">
            <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id; ?>">
            <input type="hidden" name="id" id="id" value="<?php echo $glid; ?>">
            <div class="form-input">

                <label for="">Select Parent*</label>
                <select id="gp_id" name="exp_id" class="form-control" disabled required>
                    <?php if ($gldetails['p_id'] != 0) { ?>
                        <option value="">Select Parent*</option>
                        <?php
                        $listResult = getAllChartOfAccounts_listGroup($company_id);
                        if ($listResult["status"] == "success") {
                            foreach ($listResult["data"] as $listRow) {
                        ?>

                                <option value="<?php echo $listRow['id']; ?>" <?php if ($gldetails['p_id'] == $listRow['id']) {
                                                                                    echo 'selected';
                                                                                } ?>><?php echo $listRow['gl_label']; ?>
                                </option>
                        <?php }
                        }
                    } else { ?>
                        <option value="0">Not Required</option>
                    <?php } ?>
                </select>
                <input type="hidden" name="p_id" id="p_id" value="<?= $gldetails['p_id']; ?>">
                <input type="hidden" name="gl_code" id="ggl_code" value="<?= $gldetails['gl_code']; ?>">
                <input type="hidden" name="personal_glcode_lvl" id="gpersonal_glcode_lvl" value="<?= $gldetails['lvl']; ?>">
                <input type="hidden" name="typeAcc" id="gpersonal_typeAcc" value="<?= $gldetails['typeAcc']; ?>">
                <input type="hidden" class="form-control" id="ggl_code_preview" name="gl_code_preview" value="<?= $gldetails['gl_code']; ?>" readonly required>
            </div>

            <span class="error " id="gp_id_error"></span>
        </div>


        <div class="col-lg-6 col-md-6 col-sm-12">

            <div class="form-input">

                <label for="">Group Name</label>

                <input type="text" class="form-control" id="gl_label" name="gl_label" value="<?= $gldetails['gl_label']; ?>" required>
                <span class="error"></span>

            </div>

        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">

            <div class="form-input">

                <label for="">Group Description</label>

                <input type="text" class="form-control" id="remark" name="remark" value="<?= $gldetails['remark']; ?>">

                <span class="error"></span>

            </div>

        </div>
        <div class="col-md-12">
            <div class="note mt-3">
                <p class="text-xs"><i>Note: Only the name and description can be edited. And delete will be allowed only in case the group or any associate group or GL code is not in used and in unlock status.</i></p>
            </div>
        </div>

    <?php } else { ?>

        <div class="col-lg-6 col-md-6 col-sm-12">
            <input type="hidden" name="editdata" id="editdata" value="account">
            <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id; ?>">
            <input type="hidden" name="id" id="id" value="<?php echo $glid; ?>">
            <div class="form-input">

                <label for="">Select Parent*</label>

                <select id="ap_id" name="p_id" class="form-control" required>
                    <option value="">Select Parent*</option>
                    <?php
                    // $coaSql = "SELECT coaTable.* FROM
                    //                     " . ERP_ACC_CHART_OF_ACCOUNTS . " coaTable
                    //                     WHERE
                    //                         coaTable.company_id = $company_id 
                    //                         AND coaTable.glsttype = 'group'
                    //                         AND coaTable.typeAcc = '".$gldetails['typeAcc']."'
                    //                         AND coaTable.id NOT IN (
                    //                             SELECT
                    //                                 innerTable.`p_id`
                    //                             FROM
                    //                             " . ERP_ACC_CHART_OF_ACCOUNTS . " innerTable
                    //                             WHERE innerTable.glsttype = 'group' AND innerTable.`p_id`=coaTable.`id` 
                    //                             ) 
                    //                         AND coaTable.`status` != 'deleted'
                    //                     ORDER BY coaTable.gl_code";
                    $coaSql = "SELECT coaTable.* FROM
                                    " . ERP_ACC_CHART_OF_ACCOUNTS . " coaTable
                                    WHERE
                                        coaTable.company_id = $company_id 
                                        AND coaTable.glsttype = 'group'
                                        AND coaTable.typeAcc = '" . $gldetails['typeAcc'] . "'                                        
                                        AND coaTable.`status` != 'deleted'
                                    ORDER BY coaTable.gl_code";
                    $listResult = queryGet($coaSql, true);

                    if ($listResult["status"] == "success") {
                        foreach ($listResult["data"] as $listRow) {
                    ?>
                            <option value="<?php echo $listRow['id']; ?>" <?php if ($gldetails['p_id'] == $listRow['id']) {
                                                                                echo 'selected';
                                                                            } ?>><?php echo $listRow['gl_label']; ?>
                            </option>
                    <?php }
                    } ?>
                </select>

                <input type="hidden" name="gl_code" id="agl_code" value="<?= $gldetails['gl_code']; ?>">
                <input type="hidden" name="personal_glcode_lvl" id="apersonal_glcode_lvl" value="<?= $gldetails['lvl']; ?>">
                <input type="hidden" name="typeAcc" id="apersonal_typeAcc" value="<?= $gldetails['typeAcc']; ?>">
            </div>

            <span class="error " id="ap_id_error"></span>

        </div>

        <div class="col-lg-6 col-md-6 col-sm-12">

            <div class="form-input">

                <label for="">A/C Code</label>

                <input type="text" class="form-control" id="agl_code_preview" name="gl_code_preview" value="<?= $gldetails['gl_code']; ?>" readonly required>

                <span class="error"></span>

            </div>

        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">

            <div class="form-input">

                <label for="">Account Name</label>

                <input type="text" class="form-control" id="gl_label" name="gl_label" value="<?= $gldetails['gl_label']; ?>" required>
                <span class="error"></span>

            </div>

        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">

            <div class="form-input">

                <label for="">Account Description</label>

                <input type="text" class="form-control" id="remark" name="remark" value="<?= $gldetails['remark']; ?>">

                <span class="error"></span>

            </div>

        </div>
        <div class="col-md-12">
            <div class="note mt-3">
                <p class="text-xs"><i>Note: The name and description can be edited and if the hierarchical position is changed. And delete will be allowed only in case the GL code is not in used and in unlock status.</i></p>
            </div>
        </div>

    <?php }
} else if ($type == 'delete') {

    $changeStatusSql = queryUpdate("UPDATE `" . ERP_ACC_CHART_OF_ACCOUNTS . "` SET `status`='deleted' WHERE `id`=" . $glid);

    echo json_encode($changeStatusSql);
} else if ($type == 'ordering') {
    $level = $_GET['level']-1;
    if ($pid != 0 || !empty($pid)) {
        $queryObj = queryGet("SELECT * FROM  `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE `id`=" . $pid . " ");
        if ($queryObj['data']['glStType'] != 'account') {
            $updatelavel = updateGlTreeLabel($glid, $pid, $level, $coatype);
            if ($updatelavel['status'] == 'success') {
                $changeOrderSql["status"] = "success";
                $changeOrderSql["message"] = "Ordering Changed";
                $changeOrderSql["updatelavel"] = $updatelavel;
            } else {
                $changeOrderSql["status"] = "warning";
                $changeOrderSql["message"] = "Try again!";
                $changeOrderSql["updatelavel"] = $updatelavel;
            }
        } else {
            $changeOrderSql["status"] = "warning";
            $changeOrderSql["message"] = "Not allow to change order under any GL";
        }
    } else {
        $changeOrderSql["status"] = "warning";
        $changeOrderSql["message"] = "Ordering position wrong!";
    }

    echo json_encode($changeOrderSql);

} else {
    $lvl = $gldetails['lvl'];
    if ($type == 'child') {
        $lvl = $lvl + 1;
    }

    ?>
    <div class="col-lg-6 col-md-6 col-sm-12">
        <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id; ?>">
        <div class="form-input">

            <label for="">Select Type*</label>
            <?php if ($glsttype == 'account') { ?>
                <select id="createdataDrop" name="createdataDrop" class="form-control" required <?php if ($glsttype == 'account') {
                                                                                                    echo 'disabled';
                                                                                                } ?>>
                    <option value="">Select Type*</option>
                    <option value="group">Group</option>
                    <option value="account" <?php if ($glsttype == 'account') {
                                                echo 'selected';
                                            } ?>>Account</option>
                </select>

                <input type="hidden" name="createdata" value="account">
            <?php } else { ?>
                <select id="createdata" name="createdata" class="form-control createdata" required>
                    <option value="">Select Type*</option>
                    <option value="group">Group</option>
                    <option value="account" <?php if ($glsttype == 'account') {
                                                echo 'selected';
                                            } ?>>Account</option>
                </select>

            <?php } ?>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-12">
        <div class="form-input">
            <label for="">Select Parent*</label>

            <select id="parent_id" name="parent_id" class="form-control" disabled required>
                <option value="">Select Parent*</option>
                <?php
                $coaSql = "SELECT coaTable.* FROM
                                    " . ERP_ACC_CHART_OF_ACCOUNTS . " coaTable
                                    WHERE
                                        coaTable.company_id = $company_id 
                                        AND coaTable.glsttype = 'group'
                                        AND coaTable.typeAcc = '" . $gldetails['typeAcc'] . "'                                        
                                        AND coaTable.`status` != 'deleted'
                                    ORDER BY coaTable.gl_code";
                $listResult = queryGet($coaSql, true);

                if ($listResult["status"] == "success") {
                    foreach ($listResult["data"] as $listRow) {
                ?>
                        <option value="<?php echo $listRow['id']; ?>" <?php if ($pid == $listRow['id']) {
                                                                            echo 'selected';
                                                                        } ?>><?php echo $listRow['gl_label']; ?>
                        </option>
                <?php }
                } ?>
            </select>

            <!-- <input type="hidden" name="gl_code" id="gl_code" value="<?= $gldetails['gl_code']; ?>"> -->
            <input type="hidden" name="parent" id="parent" value="<?= $pid; ?>">
            <input type="hidden" name="personal_glcode_lvl" id="personal_glcode_lvl" value="<?= $lvl; ?>">
            <input type="hidden" name="coatype" id="coatype" value="<?= $coatype; ?>">
            <input type="hidden" name="typeAcc" id="personal_typeAcc" value="<?= $gldetails['typeAcc']; ?>">
        </div>

        <span class="error " id="p_id_error"></span>

    </div>

    <div class="col-lg-12 col-md-12 col-sm-12 ac_code" style="display:<?php if ($glsttype != 'account') {
                                                                            echo 'none';
                                                                        } ?>">

        <div class="form-input">

            <label for="" style="font-weight: bold;">A/C Code : <?= $newAccCode; ?></label>

            <!-- <input type="text" class="form-control" id="gl_code_preview" name="gl_code_preview" value="<?= $gldetails['gl_code']; ?>" readonly required>

            <span class="error"></span> -->

        </div>

    </div>

    <div class="col-lg-12 col-md-12 col-sm-12">

        <div class="form-input">

            <label for="">Name*</label>

            <input type="text" class="form-control" id="gl_label" name="gl_label" value="" required>
            <span class="error"></span>

        </div>

    </div>

    <div class="col-lg-12 col-md-12 col-sm-12">

        <div class="form-input">

            <label for="">Description</label>

            <input type="text" class="form-control" id="remark" name="remark" value="">

            <span class="error"></span>

        </div>

    </div>
    <div class="col-md-12">
        <div class="note mt-3">
            <p class="text-xs"><i>Note: The name and description can be edited and if the hierarchical position is changed then the Code will be automatically updated as per it's position. And delete will be allowed only in case the GL code is not in used and in unlock status.</i></p>
        </div>
    </div>



<?php } ?>