<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">


<style>
    section.gstr-1 {
        padding: 0px 20px;
    }

    .head-btn-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .gst-one-filter {
        left: 0;
        top: 0;
    }

    .gst-one-filter a.active {
        background-color: #003060;
        color: #fff;
    }

    .proceedToFile {
        display: grid;
        align-items: center;
        place-content: center;
        justify-items: center;
        gap: 17px;
    }

    .proceedToFile img {
        max-width: 150px;
        margin: 20px auto;
    }

    .proceedToFile .text {
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>


<div class="content-wrapper">
    <section class="gstr-1">
        <h4 class="text-lg font-bold mt-4 mb-4">GSTR-1</h4>
        <div class="head-btn-section mb-3">
            <div class="filter-list gst-one-filter">
                <a href="./gst-1-action-center_preview.php" class="btn"><i class="fas fa-chart-bar mr-2"></i>Preview</a>
                <a href="./gst-1-action-center_pending.php" class="btn active"><i class="fa fa-list mr-2"></i>Pending Filling</a>
            </div>
        </div>

        <div class="card bg-light">
            <div class="card-header p-3 rounded">
                <h3 class="text-sm text-white mb-0 pl-3">Pending Filling</h3>
            </div>
            <div class="card-body p-0">

                <div class="row">
                    <div class="col-lg-8 col-sm-8 col-sm-8">
                        <h4 class="text-sm font-bold m-4">
                            Generate EVC
                        </h4>
                        <div class="proceedToFile">
                            <!-- <div class="generate-box" id="generateBox">
                                <img src="../../public/assets/img/VitNew 1.png" alt="">
                                <div class="text">
                                    <p class="text-sm">If you want to generate EVC then click on </p>
                                    <button class="btn btn-primary border">Proceed to file</button>
                                </div>
                                <span class="text-sm">Or</span>
                                <div class="text">
                                    <p class="text-sm">If you want to generate EVC then click on </p>
                                    <button class="btn btn-primary reset-btn">Reset file</button>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-4 col-sm-4">
                        <div class="card w-75 ml-auto timeline-card mb-0">
                            <div class="card-body">
                                <div id="content">
                                    <ul class="timeline">
                                        <li class="event progress-success">
                                            <h3>Initiation</h3>
                                            <p>Mr.Guria</p>
                                            <p>16-08-2023</p>
                                            <p>Mr.Guria</p>
                                        </li>
                                        <li class="event progress-success">
                                            <h3>Connect</h3>
                                            <p>Mr.Guria</p>
                                            <p>16-08-2023</p>
                                            <p>Mr.Guria</p>
                                        </li>
                                        <li class="event progress-success border-color-light">
                                            <h3>Save File</h3>
                                            <p>Mr.Guria</p>
                                            <p>16-08-2023</p>
                                            <p>Mr.Guria</p>
                                        </li>
                                        <li class="event progress-disable">
                                            <h3>Generate PVC</h3>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="modal" id="myModal2">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Table Column Settings</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                        <div class="modal-body" style="max-height: 450px;">
                            <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                            <input type="hidden" name="pageTableName" value="ERP_ACC_JOURNAL" />
                            <div class="modal-body">
                                <div id="dropdownframe"></div>
                                <div id="main2">
                                    <div class="checkAlltd d-flex gap-2 mb-2">
                                        <input type="checkbox" class="grand-checkbox" value="" />
                                        <p class="text-xs font-bold">Check All</p>
                                    </div>
                                    <?php $p = 1; ?>
                                    <table class="colomnTable">
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                Sl</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                Period</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                Voucher Court</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                Taxable Amount</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="<?php echo $p; ?>" />
                                                CGST</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?php echo $p; ?>" />
                                                SGST</td>
                                        </tr>

                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="<?php echo $p; ?>" />
                                                IGST</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                                CESS</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="<?php echo $p; ?>" />
                                                Total Tax</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="<?php echo $p; ?>" />
                                                Invoice Amount</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" name="add-table-settings" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </section>
</div>



<?php
require_once("../common/footer.php");
?>