<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-warehouse-controller.php");
if(isset($_GET['v_id'])){
    $party_id = $_GET['v_id'];
    
    $type= $_GET['type'];
?>




<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <div class="p-0 pt-1 my-2">
                        <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                            <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                <h3 class="card-title">Manage RECONCILIATION</h3>
                                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add New</a>
                            </li>
                        </ul>
                    </div>

                    <div class="filter-list">
            
              <a href="reconciliation.php?type=<?= $type ?>&v_id=<?= $party_id ?>" class="btn"><i class="fa fa-list mr-2"></i>Files</a>
              <a href="manage-reconciliation.php?type=<?= $type ?>&party_id=<?= $party_id ?>" class="btn"><i class="fa fa-clock mr-2"></i>Reconciliation</a>
            </div>


                    <div class="card card-tabs" style="border-radius: 20px;">
                        <!-- <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get">
                            <div class="card-body">
                                <div class="row filter-serach-row">
                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                        <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-lg-10 col-md-10 col-sm-12">
                                        <div class="section serach-input-section">

                                            <div class="collapsible-content">
                                                <div class="filter-col">

                                                    <div class="row">
                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                            <div class="input-group-manage-vendor">
                                                                <select name="vendor_status_s" id="vendor_status_s" class="form-control">
                                                                    <option value="">--- Status --</option>
                                                                    <option value="active" <?php if (isset($_REQUEST['vendor_status_s']) && 'active' == $_REQUEST['vendor_status_s']) {
                                                                                                echo 'selected';
                                                                                            } ?>>Active</option>
                                                                    <option value="inactive" <?php if (isset($_REQUEST['vendor_status_s']) && 'inactive' == $_REQUEST['vendor_status_s']) {
                                                                                                    echo 'selected';
                                                                                                } ?>>Inactive</option>
                                                                    <option value="draft" <?php if (isset($_REQUEST['vendor_status_s']) && 'draft' == $_REQUEST['vendor_status_s']) {
                                                                                                echo 'selected';
                                                                                            } ?>>Draft</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                            <div class="input-group-manage-vendor"> <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                                                                echo $_REQUEST['form_date_s'];
                                                                                                                                                                                            } ?>" />
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                            <div class="input-group-manage-vendor"> <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                                                                echo $_REQUEST['form_date_s'];
                                                                                                                                                                                            } ?>" />
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                            <div class="input-group-manage-vendor">
                                                                <input type="text" name="keyword" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php if (isset($_REQUEST['keyword'])) {
                                                                                                                                                                                        echo $_REQUEST['keyword'];
                                                                                                                                                                                    } ?>">
                                                            </div>
                                                        </div>


                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                            <button type="submit" class="btn btn-primary btnstyle">Search</button>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger btnstyle">Reset</a>
                                                        </div>
                                                    </div>






                                                </div>
                                            </div>
                                            <button type="button" class="collapsible btn-search-collpase" id="btnSearchCollpase">
                                                <i class="fa fa-search po-list-icon"></i>
                                            </button>
                                        </div>

                                    </div>
                                </div>

                        </form> -->
                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                <?php
                                $cond = '';

                                $sts = " AND `status` !='deleted'";
                                if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                    $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                }

                                if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                    $cond .= " AND createdAt between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                }

                                if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                    $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword'] . "%' OR `itemName` like '%" . $_REQUEST['keyword'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword'] . "%')";
                                }

                                //$sql_list = "SELECT * FROM " . ERP_BIN . " as bin ,".ERP_WAREHOUSE." as warehouse ,".ERP_STORAGE_LOCATION." as sl WHERE 1 AND sl.storage_location_id=bin.storage_location_id  ORDER BY bin.bin_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                                $batch = queryGet("SELECT * FROM `erp_reconciliation` WHERE `type`='".$type."' AND `code`= $party_id AND `company_id` = $company_id AND `location_id` = $location_id limit 0,25 ", true);

                               //  console($batch);

                                //   $qry_list = mysqli_query($dbCon, $sql_list);
                                //   $num_list = mysqli_num_rows($qry_list);

                                //AND  sl.'warehouse_id'=warehouse.'warehouse_id' 
                                //as sl ,".ERP_WAREHOUSE." as warehouse
                                $countShow = "SELECT count(*) FROM `" . ERP_BIN . "` WHERE 1 " . $cond . " ";
                                $countQry = mysqli_query($dbCon, $countShow);
                                $rowCount = mysqli_fetch_array($countQry);
                                $count = $rowCount[0];
                                $cnt = $GLOBALS['start'] + 1;
                                $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_INVENTORY_ITEMS", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                $settingsCheckbox = unserialize($settingsCh);
                                if ($batch['numRows'] > 0) { ?>
                                    <table class="table defaultDataTable table-hover text-nowrap p-0 m-0" id="export_batch">
                                        <thead>
                                            <tr class="alert-light">
                                                <th>#</th>
                                                <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                    <th>Type</th>

                                                <?php }

                                                if (in_array(2, $settingsCheckbox)) { ?>

                                                    <th> Reconciliation Type</th>
                                                <?php }
                                                if (in_array(3, $settingsCheckbox)) { ?>

                                                    <th>Code</th>
                                                <?php }
                                                if (in_array(4, $settingsCheckbox)) { ?>

                                                    <th> File Name</th>
                                                <?php }


                                                ?>



                                                <th>View</th>




                                            </tr>
                                        </thead>



                                        <tbody>


                                            <?php
                                            // console($BranchPrObj->fetchBranchSoListing()['data']);

                                            foreach ($batch['data'] as $data) {
                                                //  console($data);

                                            ?>


                                                <tr style="cursor:pointer">
                                                    <td><?= $cnt++ ?></td>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <td><?= $data['type'] ?>

                                                        </td>

                                                    <?php }
                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <td><?= $data['reconciliationType'] ?>

                                                        </td>

                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <td><?= $data['code'] ?>

                                                        </td>

                                                    <?php }

                                                    if (in_array(4, $settingsCheckbox)) { ?>
                                                        <td><?= $data['files'] ?>
                                                        </td>


                                                    <?php
                                                    }
                                                    ?>
                                                    <td><a href="#" class="viewBtn" data-attr="<?= $data['id'] ?>">View CSV</a></td>


                                                    <div id="previewModal" class="modal add-stock-list-modal previewModal_<?= $data['id'] ?>">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h3 class="card-title">Excel Preview</h3>
                                                                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="excelData_<?= $data['id'] ?>">

                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button class="btn btn-primary insertButton_<?= $data['id'] ?>" id="insertButton" >Insert into Database</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                </tr>


                                            <?php

                                            }
                                            ?>
                                        </tbody>
                                        <tbody>
                                            <tr>
                                                <td colspan="<?= $settingsCheckboxCount + 2; ?>">
                                                    <!-- Start .pagination -->

                                                    <?php
                                                    if ($count > 0 && $count > $GLOBALS['show']) {
                                                    ?>
                                                        <div class="pagination align-right">
                                                            <?php pagination($count, "frm_opts"); ?>
                                                        </div>

                                                        <!-- End .pagination -->

                                                    <?php } ?>

                                                    <!-- End .pagination -->
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>


                                    <?php
                                    if ($count > 0 && $count > $GLOBALS['show']) {
                                    ?>
                                        <div class="pagination align-right">
                                            <?php pagination($count, "frm_opts"); ?>
                                        </div>

                                        <!-- End .pagination -->

                                    <?php  } ?>

                                <?php } else { ?>
                                    <table class="table defaultDataTable table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <td>

                                                </td>
                                            </tr>
                                        </thead>
                                    </table>
                            </div>
                        <?php } ?>
                        </div>
                        <!---------------------------------Table settings Model Start--------------------------------->
                        <div class="modal" id="myModal2">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Table Column Settings</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                                        <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                        <input type="hidden" name="pageTableName" value="ERP_INVENTORY_ITEMS" />
                                        <div class="modal-body">
                                            <div id="dropdownframe"></div>
                                            <div id="main2">
                                                <table>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                            Storage Location Name</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                            Storage Location Code</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                            Warehouse</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                                            Storage Control</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                                            Temp Control</td>
                                                    </tr>



                                                </table>
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

                        <!---------------------------------Table Model End--------------------------------->

                    </div>
                </div>
            </div>
        </div>
</div>
<!-- /.row -->
</div>
</section>
<!-- /.content -->
</div>


<!-- /.Content Wrapper. Contains page content -->
<!-- For Pegination------->
<form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                    echo  $_REQUEST['pageNo'];
                                                } ?>">
</form>
<!-- End Pegination from------->


<?php
}
else{
    ?>

    <?php
}
require_once("../common/footer.php");
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script>
    $(document).ready(function() {
        $('.viewBtn').on('click', function() {
            var attr = $(this).data('attr');
            // alert(attr);
            // alert(1);
            $.ajax({
                url: `ajaxs/reconciliation/preview.php?type=reconcile&id=${attr}`,
                type: 'GET',

                success: function(response) {
                    // alert(response);
                    $('.excelData_' + attr).html(response);
                    $('.previewModal_' + attr).show();
                }
            });

           
        function submitForm() {
            //alert(1);
            var tableData = [];
            var table = document.getElementById("previewTable");
            var rows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
            var id = $("#id").val();
            // alert(id);
            // exit();

            for (var i = 0; i < rows.length; i++) {
                var rowData = [];
                var cells = rows[i].getElementsByTagName("td");
                for (var j = 0; j < cells.length; j++) {
                    rowData.push(cells[j].innerHTML);
                }
                tableData.push(rowData);
            }

            $.ajax({
                url: "ajaxs/reconciliation/insert.php",
                type: "POST",
                data: {
                    tableData: JSON.stringify(tableData),
                    id: id
                },
                success: function(response) {
                    var returnData = JSON.parse(response);
                    alert(returnData);
                    console.log(response);
                    // Check the status and message
                    // if (returnData.status === "success" && returnData.message === "Stock Count Inserted") {
                    //     // Display a success alert using alert()
                    //     alert('Stock Count Inserted');
                    // }
                }
            });
        }
        
            $('.insertButton_'+attr).click(function(){
                //alert(1);
                submitForm();
      
      // alert(attr);

    });
        
        });
      

        

        // $(document).on('click','.insertButton', function() {
            
            
        //     console.log("gytfdresedrftgyhujikol");

        //     //submitForm();
        // });

    
    });




   
</script>