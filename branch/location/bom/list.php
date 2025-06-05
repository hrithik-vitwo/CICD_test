<div class="row p-0 m-0">
    <?php
    // $itemIdForBom = 0;
    // if (isset($_GET["item"]) && $_GET["item"] != "") {
    //     $itemIdForBom = base64_decode($_GET["item"]);
    // }
    // $bomListObj = $goodsBomController->getAllBoms($itemIdForBom);

    if (isset($_POST["add-table-settings"])) {
        $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
        swalToast($editDataObj["status"], $editDataObj["message"]);
    }
    ?>
    <div class="col-12 mt-2 p-0">
        <p>
            <?php
            include_once("controller/bom.controller.php");
            $bomControllerObj = new BomController();
            $bomListObj = $bomControllerObj->getBomList();
            // console($bomListObj);
            // $bomListObj = [];
            ?>
        </p>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>branch/location/" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
            <li class="breadcrumb-item active"><a href="<?= BASE_URL ?>branch/location/bom/bom.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage BOM</a></li>
            <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-list po-list-icon"></i>BOM List</a></li>
            <li class="back-button">
                <a href="<?= BASE_URL ?>branch/location/bom/bom.php">
                    <i class="fa fa-reply po-list-icon"></i>
                </a>
            </li>
        </ol>

        <div class="card card-tabs" style="border-radius: 20px;">
            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                <div class="card-body">
                    <div class="row filter-serach-row">
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                        </div>
                        <div class="col-lg-10 col-md-10 col-sm-12">
                            <div class="section serach-input-section">
                                <input type="text" id="myInput" placeholder="" class="field form-control" />
                                <div class="icons-container">
                                    <div class="icon-search">
                                        <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                                    </div>
                                    <div class="icon-close">
                                        <i class="fa fa-search po-list-icon" onclick="javascript:alert('Hello World!')" id="myBtn"></i>
                                        <script>
                                            var input = document.getElementById("myInput");
                                            input.addEventListener("keypress", function(event) {
                                                if (event.key === "Enter") {
                                                    event.preventDefault();
                                                    document.getElementById("myBtn").click();
                                                }
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Filter Vendors</h5>
                                    </div>
                                    <div class="modal-body">
                                    </div>
                                    <div class="modal-footer">
                                        <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync fa-spin"></i>Reset</a>
                                        <a type="button" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>Search</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>


            <?php
            // $cnt = $GLOBALS['start'] + 1;
            $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BOM_DETAILS", $_SESSION["logedBranchAdminInfo"]["adminId"]);
            $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
            $settingsCheckbox = unserialize($settingsCh);
            ?>
            <?php
            if ($bomListObj['numRows'] > 0) {
            ?>
                <div class="tab-content" id="custom-tabs-two-tabContent">
                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                        <table id="mytable" class="table defaultDataTable table-hover text-nowrap">
                            <thead>
                                <tr class="alert-light">
                                    <th class=" ">#</th>
                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                        <th class="">Item Code</th>
                                    <?php }
                                    if (in_array(2, $settingsCheckbox)) { ?>
                                        <th class="">Item Name</th>
                                    <?php }
                                    if (in_array(3, $settingsCheckbox)) { ?>
                                        <th class="">Prepared Date</th>
                                    <?php }
                                    if (in_array(4, $settingsCheckbox)) { ?>
                                        <th class="text-right">COGM-M</th>
                                    <?php }
                                    if (in_array(5, $settingsCheckbox)) { ?>
                                        <th class="text-right">COGM-A</th>
                                    <?php }
                                    if (in_array(6, $settingsCheckbox)) { ?>
                                        <th class="text-right">COGM</th>
                                    <?php }
                                    if (in_array(7, $settingsCheckbox)) { ?>
                                        <th class="text-right">COGS</th>
                                    <?php }
                                    if (in_array(8, $settingsCheckbox)) { ?>
                                        <th class="text-right">MSP</th>
                                    <?php }
                                    if (in_array(9, $settingsCheckbox)) { ?>
                                        <th class=" ">Status</th>
                                    <?php }
                                    if (in_array(10, $settingsCheckbox)) {  ?>
                                        <th class=" ">Action</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($bomListObj["status"] == "success") {
                                    $sl = 1;
                                    foreach ($bomListObj["data"] as $oneBomRow) {
                                ?>
                                        <tr>
                                            <td><?= $sl++ ?></td>
                                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                <td><?= $oneBomRow["itemCode"] ?></td>
                                            <?php } ?>
                                            <?php if (in_array(2, $settingsCheckbox)) { ?>
                                                <td>
                                                    <p class="pre-wrap"><?= $oneBomRow["itemName"] ?></p>
                                                </td>
                                            <?php } ?>
                                            <?php if (in_array(3, $settingsCheckbox)) { ?>
                                                <td><?= $oneBomRow["preparedDate"] ?></td>
                                            <?php } ?>
                                            <?php if (in_array(4, $settingsCheckbox)) { ?>
                                                <td class="text-right"><?= $oneBomRow["cogm_m"] > 0 ? decimalValuePreview($oneBomRow["cogm_m"]) : "" ?></td>
                                            <?php } ?>
                                            <?php if (in_array(5, $settingsCheckbox)) { ?>
                                                <td class="text-right"><?= $oneBomRow["cogm_a"] > 0 ? decimalValuePreview($oneBomRow["cogm_a"]) : "" ?></td>
                                            <?php } ?>
                                            <?php if (in_array(6, $settingsCheckbox)) { ?>
                                                <td class="text-right"><?= $oneBomRow["cogm"] > 0 ? decimalValuePreview($oneBomRow["cogm"]) : "" ?></td>
                                            <?php } ?>
                                            <?php if (in_array(7, $settingsCheckbox)) { ?>
                                                <td class="text-right"><?= $oneBomRow["cogs"] > 0 ? decimalValuePreview($oneBomRow["cogs"]) : "" ?></td>
                                            <?php } ?>
                                            <?php if (in_array(8, $settingsCheckbox)) { ?>
                                                <td class="text-right"><?= $oneBomRow["msp"] > 0 ? decimalValuePreview($oneBomRow["msp"]) : "" ?></td>
                                            <?php } ?>
                                            <?php if (in_array(9, $settingsCheckbox)) { ?>
                                                <td>
                                                    <?php if ($oneBomRow["bomCreateStatus"] != 1) {
                                                        if ($oneBomRow["bomStatus"] == 'active') { ?>
                                                            <p class="status"><?= ucfirst($oneBomRow["bomStatus"]) ?></p>
                                                        <?php } else { ?>
                                                            <p class="status status-danger"><?= ucfirst($oneBomRow["bomStatus"]) ?></p>
                                                    <?php }
                                                    } ?>
                                                </td>
                                            <?php } ?>
                                            <?php if (in_array(10, $settingsCheckbox)) { ?>
                                                <td>
                                                    <?php if ($oneBomRow["bomCreateStatus"] == 1) { ?>
                                                        <a style="cursor: pointer" class="btn btn-sm" href="?create=<?= base64_encode($oneBomRow["itemId"]) ?>"><i class="fa fa-plus po-list-icon"></i></a>
                                                    <?php } else { ?>
                                                        <a style="cursor: pointer" class="btn btn-sm" href="?view=<?= base64_encode($oneBomRow["bomId"]) ?>"><i class="fa fa-eye po-list-icon"></i></a>
                                                        <a style="cursor: pointer" class="btn btn-sm" href="?editBom=<?= base64_encode($oneBomRow["bomId"]) ?>"><i class="fa fa-edit po-list-icon"></i></a>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    <?php
                } else { ?>
                        <table id="mytable" class="table defaultDataTable table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <td>

                                    </td>
                                </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            <?php } ?>



        </div>


        <div class="modal" id="myModal2">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Table Column Settings</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                        <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                        <input type="hidden" name="pageTableName" value="ERP_BOM_DETAILS" />
                        <div class="modal-body">
                            <div id="dropdownframe"></div>
                            <div id="main2">
                                <table class="row-selection">
                                    <tr>
                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                            Item Code</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                            Item Name</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                            Prepared Date</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                            COGM-M</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                            COGM-A</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                                            COGM</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />
                                            COGS</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox8" value="8" />
                                            MSP</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(9, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox9" value="9" />
                                            Status</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(10, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox10" value="10" />
                                            Action</td>
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
    </div>
</div>

<script>
    // $("#bomListTable").dataTable();
    function table_settings() {
        var favorite = [];
        $.each($("input[name='settingsCheckbox[]']:checked"), function() {
            favorite.push($(this).val());
        });
        var check = favorite.length;
        if (check < 5) {
            alert("Please Check Atlast 5");
            return false;
        }

    }
</script>