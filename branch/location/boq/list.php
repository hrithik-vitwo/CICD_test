<div class="row p-0 m-0">
    <div class="col-12 mt-2 p-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>branch/location/" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
            <li class="breadcrumb-item active"><a href="<?= BASE_URL ?>branch/location/boq/boq.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage BOQ</a></li>
            <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-list po-list-icon"></i>BOQ List</a></li>
            <li class="back-button">
                <a href="<?= BASE_URL ?>branch/location/boq/boq.php">
                    <i class="fa fa-reply po-list-icon"></i>
                </a>
            </li>
        </ol>
        
        <p>
            <?php
            $boqListObj = $boqControllerObj->getBoqList();
            // $boqListObj = [];
            // console($boqListObj);
            ?>
        </p>
        
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

            <table class="table defaultDataTable table-hover">
                <thead>
                    <tr class="alert-light">
                        <th class="borderNone">#</th>
                        <th class="borderNone">Service Code</th>
                        <th class="borderNone">Service Name</th>
                        <th class="borderNone">Prepared Date</th>
                        <th class="borderNone">COGM</th>
                        <th class="borderNone">COSP-M</th>
                        <th class="borderNone">COSP-A</th>
                        <th class="borderNone">COSP-I</th>
                        <th class="borderNone">COGS</th>
                        <th class="borderNone">MSP</th>
                        <th class="borderNone">Status</th>
                        <th class="borderNone">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // console($boqListObj);
                    if ($boqListObj["status"] == "success") {
                        $sl = 0;
                        foreach ($boqListObj["data"] as $oneBoqRow) {
                    ?>
                            <tr>
                                <td><?= $sl += 1 ?></td>
                                <td><?= $oneBoqRow["itemCode"] ?></td>
                                <td><?= $oneBoqRow["itemName"] ?></td>
                                <td><?= $oneBoqRow["preparedDate"] ?></td>
                                <td class="text-right"><?= $oneBoqRow["cogm"] > 0 ? decimalValuePreview($oneBoqRow["cogm"]) : "" ?></td>
                                <td class="text-right"><?= $oneBoqRow["cosp_m"] > 0 ? decimalValuePreview($oneBoqRow["cosp_m"]) : "" ?></td>
                                <td class="text-right"><?= $oneBoqRow["cosp_a"] > 0 ? decimalValuePreview($oneBoqRow["cosp_a"]) : "" ?></td>
                                <td class="text-right"><?= $oneBoqRow["cosp_i"] > 0 ? decimalValuePreview($oneBoqRow["cosp_i"]) : "" ?></td>
                                <td class="text-right"><?= $oneBoqRow["cogs"] > 0 ? decimalValuePreview($oneBoqRow["cogs"]) : "" ?></td>
                                <td class="text-right"><?= $oneBoqRow["msp"] > 0 ? decimalValuePreview($oneBoqRow["msp"]) : "" ?></td>
                                <td><?= ucfirst($oneBoqRow["boqStatus"]) ?></td>
                                <td>
                                    <?php
                                    if ($oneBoqRow["boqCreateStatus"] == 1) {   
                                    ?>
                                        <a style="cursor: pointer" class="btn btn-sm" href="?create=<?= base64_encode($oneBoqRow["itemId"]) ?>"><i class="fa fa-plus po-list-icon"></i></a>
                                    <?php
                                    } else if ($oneBoqRow["boqCreateStatus"] == 2) {
                                    ?>
                                        <a style="cursor: pointer" class="btn btn-sm" href="?view=<?= base64_encode($oneBoqRow["itemId"]) ?>"><i class="fa fa-eye po-list-icon"></i></a>
                                    <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>

            </table>

        </div>
    </div>
</div>