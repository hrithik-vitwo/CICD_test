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
        place-content: start;
        gap: 17px;
        padding-left: 21px;
    }

    .otp-popup .proceedToFile {
        display: grid;
        place-content: center;
        padding-left: 0;
    }

    .proceedToFile img {
        max-width: 150px;
        margin: 20px auto;
    }

    .proceedToFile .text {
        display: flex;
        align-items: center;
        flex-direction: column;
        gap: 10px;
    }

    .evc-selection .box {
        border: 1px solid #929292;
        padding: 25px;
        background: #ccc;
        border-radius: 12px;
        width: 200px;
        cursor: pointer;
    }

    .evc-selection .box.active-box {
        border-color: #003060;
        background: #003060;
    }

    .evc-selection .box p {
        font-size: 12px;
        margin: 7px 0;
        text-align: center;
    }

    .evc-selection .box.active-box p {
        color: #fff;
    }

    .evc-selection .box.active-box:before {
        border-color: #fff;
    }

    .evc-selection .box:before {
        content: "";
        position: relative;
        top: -22px;
        display: inline-block;
        transform: translateX(-14px);
        width: 7px;
        height: 7px;
        border: 2px solid #929292;
        border-radius: 50%;
    }

    .otp-inputs input {
        width: 30px;
        text-align: center;
    }

    .success-popup .modal-body {
        display: grid;
        gap: 0px;
    }

    .success-popup .modal-body img {
        margin: 0 auto;
    }

    .success-popup .modal-body p {
        padding: 0 45px 25px;
    }

    .success-popup .modal-body button {
        width: 70px;
        margin: 0 auto 25px;
        font-size: 15px !important;
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
            <div class="card-header p-3 rounded-top">
                <h3 class="text-sm text-white mb-0 pl-3">Pending Filling</h3>
            </div>
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-8 col-sm-8 col-sm-8">
                        <h4 class="text-sm font-bold m-4">
                            Generate EVC
                        </h4>
                        <div class="proceedToFile h-75">
                            <p class="text-sm">Select Any one</p>
                            <div class="evc-selection d-flex gap-2">
                                <div class="box">
                                    <p>Ritesh Saha</p>
                                    <p>ADDR34536TY8</p>
                                </div>
                                <div class="box">
                                    <p>Ritesh Saha</p>
                                    <p>ADDR34536TY8</p>
                                </div>
                                <div class="box active-box">
                                    <p>Ritesh Saha</p>
                                    <p>ADDR34536TY8</p>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary float-right" data-toggle="modal" data-target="#oTpPopup">Generate EVC</button>
                        <div class="modal otp-popup fade" id="oTpPopup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-body py-4">
                                        <div class="proceedToFile">
                                            <img src="../../public/assets/img/password-otp.png" class="my-0" width="75" alt="">
                                            <div class="text">
                                                <p class="text-sm">Please enter the otp and processed</p>
                                                <div id="OtpInputs" class="otp-inputs d-flex gap-2 my-3">
                                                    <input class="input form-control" type="text" inputmode="numeric" maxlength="1" />
                                                    <input class="input form-control" type="text" inputmode="numeric" maxlength="1" />
                                                    <input class="input form-control" type="text" inputmode="numeric" maxlength="1" />
                                                    <input class="input form-control" type="text" inputmode="numeric" maxlength="1" />
                                                    <input class="input form-control" type="text" inputmode="numeric" maxlength="1" />
                                                    <input class="input form-control" type="text" inputmode="numeric" maxlength="1" />
                                                </div>
                                                <script>
                                                    // script.js
                                                    const inputs = document.getElementById("OtpInputs");

                                                    inputs.addEventListener("input", function(e) {
                                                        const target = e.target;
                                                        const val = target.value;

                                                        if (isNaN(val)) {
                                                            target.value = "";
                                                            return;
                                                        }

                                                        if (val != "") {
                                                            const next = target.nextElementSibling;
                                                            if (next) {
                                                                next.focus();
                                                            }
                                                        }
                                                    });

                                                    inputs.addEventListener("keyup", function(e) {
                                                        const target = e.target;
                                                        const key = e.key.toLowerCase();

                                                        if (key == "backspace" || key == "delete") {
                                                            target.value = "";
                                                            const prev = target.previousElementSibling;
                                                            if (prev) {
                                                                prev.focus();
                                                            }
                                                            return;
                                                        }
                                                    });
                                                </script>
                                                <button class="btn btn-primary" data-toggle="modal" data-target="#successPopup">Submit</button>
                                                <div class="modal success-popup fade" id="successPopup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-body">
                                                                <img src="../../public/assets/img/correct 2.jpg" alt="">
                                                                <p class="text-lg text-center font-bold">Congratulations</p>
                                                                <p class="text-center">Your EVC is successful created. It has generated the below acknowledgment as a confirmation of your return submission</p>
                                                                <button class="btn btn-primary" data-dismiss="modal">Ok</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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