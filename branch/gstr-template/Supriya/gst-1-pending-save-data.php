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



    .dataTables_scrollBody thead {

        visibility: hidden;

    }



    div.dataTables_wrapper div.dataTables_filter,

    .dataTables_wrapper .row {

        display: flex !important;

        align-items: center;

        justify-content: end;

    }



    /* div.dataTables_wrapper {

      overflow: hidden;

    } */



    div.dataTables_wrapper div.dataTables_filter,

    .dataTables_wrapper .row:nth-child(1),

    div.dataTables_wrapper div.dataTables_filter,

    .dataTables_wrapper .row:nth-child(3) {

        padding: 10px 20px;

    }



    div.dataTables_wrapper div.dataTables_length select {

        width: 60% !important;

        appearance: none !important;

        -webkit-appearance: none;

        -moz-appearance: none;

    }



    .dataTables_scroll {

        position: relative;

        margin-bottom: 10px;

    }



    .dataTables_scrollBody::-webkit-scrollbar {

        background-color: transparent;

        width: 0px;

        height: 0px;

        cursor: pointer;

    }



    .dataTables_scrollBody:hover::-webkit-scrollbar {

        width: 8px;

        height: 8px;

    }



    .dataTables_scrollBody:hover::-webkit-scrollbar-thumb {

        background-color: rgba(0, 0, 0, 0.2);

    }





    div.dataTables_wrapper div.dataTables_filter input {

        margin-left: 10px;

    }



    div.dataTables_scrollFoot>.dataTables_scrollFootInner th {

        border: 0;

    }



    .dataTables_filter {

        padding-right: 0 !important;

    }



    div.dataTables_wrapper div.dataTables_paginate ul.pagination {

        padding: 0;

        border: 0;

    }



    .dt-top-container {

        display: flex;

        align-items: center;

        padding: 0 20px;

        gap: 20px;

        height: 60px;

    }



    .gst-action-center tr td {

        /* white-space: pre-line !important; */

        text-align: center !important;

    }



    .gst-action-center tr th {

        text-align: center !important;

    }



    .dataTables_length {

        margin-left: 4em;

        display: none;

    }



    div#datatable_filter {

        display: none !important;

    }



    a.btn.add-col.setting-menu.waves-effect.waves-light {

        position: absolute !important;

        display: flex;

        justify-content: space-between;

        top: 10px !important;

    }



    div.dataTables_wrapper div.dataTables_length label {

        margin-bottom: 0;

    }



    div.dataTables_wrapper div.dataTables_info {

        padding-left: 20px;

        position: relative;

        top: 0;

    }



    .dataTables_paginate {

        position: relative;

        right: 20px;

        bottom: 20px;

        margin-top: -15px;

    }



    .dt-center-in-div {

        display: block;

        /* order: 3; */

        margin-left: auto;

    }



    .dt-buttons.btn-group.flex-wrap button {

        background-color: #003060 !important;

        border-color: #003060 !important;

        border-radius: 7px !important;

    }



    /* .setting-row .col .btn.setting-menu {

      position: absolute !important;

      right: 255px;

      top: 10px;

    } */



    .dt-buttons.btn-group.flex-wrap {

        gap: 10px;

    }



    .dataTables_scrollBody {

        height: auto !important;

    }





    table.dataTable>thead .sorting:before,

    table.dataTable>thead .sorting::after,

    table.dataTable>thead .sorting_asc:before,

    table.dataTable>thead .sorting_asc::after,

    table.dataTable>thead .sorting_desc:before,

    table.dataTable>thead .sorting_desc::after,

    table.dataTable>thead .sorting_asc_disabled:before,

    table.dataTable>thead .sorting_asc_disabled::after,

    table.dataTable>thead .sorting_desc_disabled:before,

    table.dataTable>thead .sorting_desc_disabled::after {



        display: block !important;



    }





    select.fy-dropdown {

        position: absolute;

        max-width: 100px;

        top: 14px;

        left: 255px;

    }



    .daybook-filter-list.filter-list {

        display: flex;

        gap: 7px;

        justify-content: flex-end;

        position: relative;

        top: 0px;

        left: 18px;

        margin: 15px 0;

        float: right;

    }



    .daybook-filter-list.filter-list a.active {

        background-color: #003060;

        color: #fff;

    }



    .date-range-input {

        gap: 7px;

    }



    .date-range-input .form-input {

        width: 100%;

    }



    .daybook-tabs {

        flex-direction: row-reverse;

        justify-content: space-between;

        align-items: center;

        margin-bottom: 25px;

    }



    ul.nav-preview {

        position: absolute;

        top: 15px;

        left: 100px;

        z-index: 9;

    }



    ul.nav-preview li .nav-link,

    ul.nav-preview li .nav-link:hover {

        display: flex;

        align-items: center;

        color: #000 !important;

    }



    ul.nav-preview li .nav-link.active {

        background-color: #fff;

        color: #000;

    }
</style>


<div class="content-wrapper">
    <section class="gstr-1">
        <h4 class="text-lg font-bold mt-4 mb-4">GSTR-1</h4>
        <div class="head-btn-section mb-3">
            <div class="filter-list gst-one-filter">
                <a href="./gst-1-action-center-preview.php" class="btn"><i class="fas fa-chart-bar mr-2"></i>Preview</a>
                <a href="#" class="btn active"><i class="fa fa-list mr-2"></i>File</a>
            </div>


        </div>

        <div class="card bg-light">
            <div class="card-header p-3 rounded-top">
                <h3 class="text-sm text-white mb-0 pl-3">Pending Filing</h3>
            </div>
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-8 col-sm-8 col-sm-8">
                        <h4 class="text-sm font-bold m-4">
                            Save File For The Month Of 16-08-2023
                        </h4>
                        <div class="proceedToFile">
                            <!-- <div class="section-box">
                                <div class="box mb-4">
                                    <p class="text-sm mb-2"><b>Total Of GST</b> - 2,00,000</p>
                                    <p class="text-sm mb-2"><b>CGST</b> - 2,00,000</p>
                                    <p class="text-sm mb-2"><b>SGST</b> - 2,00,000</p>
                                    <p class="text-sm mb-2"><b>IGST</b> - 2,00,000</p>
                                    <p class="text-sm mb-2"><b>Total Items</b> - 25,000</p>
                                    <img src="../../public/assets/img/VitNew 1.png" alt="">
                                </div>
                                <div class="text">
                                    <p class="text-sm font-bold">All the data will be saved when you clicking the save button. </p>
                                    <p class="text-sm font-bold">Would you like to save data in GST Server?</p>
                                    <div class="btns-group my-3">
                                        <button class="btn btn-primary border mr-2" data-toggle="modal" data-target="#successPopup">Yes</button>
                                        <button class="btn btn-primary reset-btn">No</button>
                                    </div>
                                    <div class="modal success-popup fade" id="successPopup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <img src="../../public/assets/img/correct 2.jpg" alt="">
                                                    <p class="text-center">Your EVC is successful created. It has generated the below acknowledgment as a confirmation of your return submission</p>
                                                    <button class="btn btn-primary" data-dismiss="modal">Ok</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                                            <h3 class="text-success">Initiation</h3>
                                            <p>Mr.Guria</p>
                                            <p>16-08-2023</p>
                                            <p>Mr.Guria</p>
                                        </li>
                                        <li class="event progress-success border-color-light">
                                            <h3 class="text-success">Connect</h3>
                                            <p>Mr.Guria</p>
                                            <p>16-08-2023</p>
                                            <p>Mr.Guria</p>
                                        </li>
                                        <li class="event">
                                            <h3>Save File</h3>
                                            <p>Mr.Guria</p>
                                            <p>16-08-2023</p>
                                            <p>Mr.Guria</p>
                                        </li>
                                        <li class="event">
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