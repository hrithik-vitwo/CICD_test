<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <!-- <div id="drop_zone" ondrop="dropHandler(event);">
                        <p>Drag one or more files to this <i>drop zone</i>.</p>
                    </div> -->
            <div class="row">
                <div class="col-md-6" id="uploadGrnInvoiceDiv">
                    <form id="uploadGrnInvoiceFileForm" action="" method="post" enctype="multipart/form-data">
                        <span class="text-light">Select Invoice</span>
                        <input type="file" id="invoiceFileInput" name="grnInvoiceFile" class="form-control p-2 pl-3" placeholder="Invoice Upload" required />
                        <button type="submit" class="form-control mt-1" id="uploadGrnInvoiceFileFormSubmitBtn">Upload</button>
                    </form>
                </div>
                <div id="processingGrnInvoiceDiv">
                    <small class="text-light">Please wait, while we are processing your invoice!</small>
                    <img src="../../public/assets/gif/ocr-processing.gif" style="max-width: 250px;">
                </div>

                <div id="goBackBtn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item" style="visibility: hidden;"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                        <li class="breadcrumb-item active" style="visibility: hidden;"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Sales Order List</a></li>
                        <li class="breadcrumb-item active" style="visibility: hidden;"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                                Create Sales Order</a></li>
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
                    console("Hello grn process");
                    console($createGrnObj);
                }
                // console($_SESSION);
                ?>
                <?= console($_POST); ?>
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
        $("#uploadGrnInvoiceFileForm").on('submit', (function(e) {
            e.preventDefault();
            $.ajax({
                url: "ajaxs/grn/ajax-grn-invoice-process.php",
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    $("#uploadGrnInvoiceFileFormSubmitBtn").html("Processing...");
                    $("#uploadGrnInvoiceDiv").hide();
                    $("#processingGrnInvoiceDiv").show();
                    console.log("uploading.........");
                },
                success: function(data) {
                    $("#uploadGrnInvoiceFileFormSubmitBtn").html("Upload New Invoice");
                    $("#processingGrnInvoiceDiv").hide();
                    $("#invoiceProcessedForm").html(data);
                    $("#goBackBtn").show();
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