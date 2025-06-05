<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row create-grn-step">
                <div id="uploadGrnInvoiceDiv" class="create-grn">
                    <form id="uploadGrnInvoiceFileForm" action="" method="post" enctype="multipart/form-data">
                        <div class="upload-files-container">
                            <div class="card">
                                <div class="card-header">
                                    <div class="head">
                                        <h4>Upload Invoice</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="drag-file-area">
                                        <i class="fa fa-arrow-up po-list-icon text-center m-auto"></i>
                                        <br>
                                        <input type="file" class="form-control" id="invoiceFileInput" name="grnInvoiceFile" placeholder="Invoice Upload" required />
                                    </div>
                                    <div class="file-block">
                                        <div class="progress-bar"> </div>
                                    </div>
                                    <button type="submit" class="upload-button btn btn-primary" id="uploadGrnInvoiceFileFormSubmitBtn"> Upload </button>
                                </div>
                            </div>
                        </div>
                        <!-- <span class="text-light">Select Invoice</span>
                        <input type="file" id="invoiceFileInput" name="grnInvoiceFile" class="form-control p-2 pl-3" placeholder="Invoice Upload" required />
                        <button type="submit" class="form-control mt-1" id="uploadGrnInvoiceFileFormSubmitBtn">Upload</button> -->
                    </form>
                </div>
                <div id="processingGrnInvoiceDiv" class="create-grn">
                    <div class="card">
                        <div class="card-body">
                            <p>Please wait, while we are processing your invoice!</p>
                            <img src="../../public/assets/gif/OCR-2.gif" class="ocr-reading-animation">
                            <p class="text-center text-muted"> <i class="fa fa-clock" style="color:#003060"></i> <span id="orcTimer" class="text-bold" style="color:#003060">00 s</span></p>
                        </div>
                    </div>

                </div>

                <div id="goBackBtn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item" style="visibility: hidden;"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                        <li class="breadcrumb-item active" style="visibility: hidden;"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Sales Order List</a></li>
                        <li class="breadcrumb-item active" style="visibility: hidden;"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                            </a></li>

                        <li class="back-button">
                            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                                <i class="fa fa-reply po-list-icon"></i>
                            </a>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <?php
                if (isset($_POST["vendorCode"]) && $_POST["vendorCode"] != "") {
                    
                    $grnObj = new GrnController();
                    $createGrnObj = $grnObj->createGrn($_POST);

                    if ($createGrnObj["status"] == "success") {
                        swalAlert($createGrnObj["status"], ucfirst($createGrnObj["status"]),$createGrnObj["message"], BASE_URL . "branch/location/manage-grn.php");
                    } else {
                        swalAlert($createGrnObj["status"], ucfirst($createGrnObj["status"]), $createGrnObj["message"]);
                    }
                    
                    //console("Hello grn process");
                    //console($createGrnObj);

                }

                // function getStorageLocationListForGrn() {
                //     global $company_id; global $branch_id; global $location_id; global $created_by; global $updated_by;
                //     return queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` WHERE `company_id`='.$company_id.' AND `branch_id`='.$branch_id.' AND `location_id`='.$location_id.' AND `storage_location_type`="RM-WH" AND `storage_location_material_type`="RM" AND `storage_location_storage_type`="Open" AND `status`="active"');
                // }
                // console(getStorageLocationListForGrn());
                // console($_SESSION);
                ?>
            </div>
            <div class="row" id="invoiceProcessedForm" style="overflow-y:auto;">
            </div>

            <!-- modal -->
            <div class="modal" id="mapInvoiceItemCode">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header py-1" style="background-color: #003060; color:white;">
                            <h5 class="modal-title">Map Item</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="col-md-12 mb-3">
                                <div class="input-group">
                                    <input type="text" name="itemName" class="m-input" required>
                                    <label>Item Name</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="text" name="itemDesc" class="m-input" required>
                                    <label>Item Description</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input-group btn-col">
                                    <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- modal end -->
        </div>
    </section>
</div>


<script>
    $(document).ready(function() {
        $("#processingGrnInvoiceDiv").hide();
        $("#goBackBtn").hide();

        let timerObj;
        function startTimer(){
            let ocrProccessingTime = 0;
            timerObj=setInterval(function() {
                ocrProccessingTime+=1;
                let timerText = "";
                if (ocrProccessingTime < 10) {
                    timerText = "0" + ocrProccessingTime;
                } else {
                    timerText = ocrProccessingTime;
                }
                $("#orcTimer").html(`${timerText} s`);
            }, 1000);
        }
        function stopTimer(){
            clearInterval(timerObj);
        }

        $("#uploadGrnInvoiceFileForm").on('submit', (function(e) {
            e.preventDefault();
            $.ajax({
                url: "ajaxs/grn/ajax-grn-invoice-process-new.php",
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    $("#uploadGrnInvoiceFileFormSubmitBtn").html("Processing...");
                    $("#uploadGrnInvoiceDiv").hide();
                    $("#processingGrnInvoiceDiv").show();
                	startTimer();
                    console.log("uploading.........");

                },
                success: function(data) {
                    $("#uploadGrnInvoiceFileFormSubmitBtn").html("Upload New Invoice");
                    $("#processingGrnInvoiceDiv").hide();
                    $("#invoiceProcessedForm").html(data);
                    $("#goBackBtn").show();
                	stopTimer();
                    console.log("Ocr bill result:");
                    console.log(data);
                },
                error: function(e) {
                    $("#uploadGrnInvoiceFileFormSubmitBtn").html("Try again!");
                    $("#goBackBtn").show();
                    console.log("error: " + e.message);
                }
            });
        }));


        $("#goBackBtn").click(function() {
            window.location.reload();
        });


        //js



    });
</script>