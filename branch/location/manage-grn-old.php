<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");



?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.rawgit.com/tonystar/bootstrap-float-label/v4.0.1/dist/bootstrap-float-label.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">



<div class="content-wrapper py-4">
    <section class="content">
        <div class="container-fluid">
            <div class="card rounded">
                <div class="card-header">
                    <!-- <div id="drop_zone" ondrop="dropHandler(event);">
                        <p>Drag one or more files to this <i>drop zone</i>.</p>
                    </div> -->
                    <div class="row m-5">
                        <div class="col-md-6 ml-auto mr-auto" id="uploadGrnInvoiceDiv">
                            <form id="uploadGrnInvoiceFileForm" action="" method="post" enctype="multipart/form-data">
                                <span class="text-light">Select Invoice</span>
                                <input type="file" id="invoiceFileInput" name="grnInvoiceFile" class="form-control p-2 pl-3" placeholder="Invoice Upload" required />
                                <button type="submit" class="form-control mt-1" id="uploadGrnInvoiceFileFormSubmitBtn">Upload</button>
                            </form>
                        </div>
                        <div class="ml-auto mr-auto" id="processingGrnInvoiceDiv" style="max-width: 250px;">
                            <small class="text-light">Please wait, while we are processing your invoice!</small>
                            <img src="../../public/assets/gif/ocr-processing.gif" style="max-width: 250px;">
                        </div>
                        
                        <div class="ml-auto mr-0" id="goBackBtn">
                            <a class="btn btn-sm btn-secondary">Back</a>
                        </div>
                        
                    </div>
                </div>
                <div class="card-body p-0 pt-2">
                    <div class="row m-0 p-0">
                        <?= console($_POST); ?>
                    </div>
                    <div class="row m-0 p-0" id="invoiceProcessedForm" style="overflow-y:auto;">
                    </div>
                </div>
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


<?php
require_once("../common/footer.php");
?>
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


        $("#goBackBtn").click(function(){
            window.location.reload();
        });
    })
</script>