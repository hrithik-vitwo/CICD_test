<?php
require_once("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/company/func-migration.php");

$message = "";
if (isset($_POST["send_data"])) {

    $newSendObj = sendEmail($_POST);
    if ($newSendObj["status"] == "success") {
        swalAlert($newSendObj["status"], 'Success', $newSendObj["message"]);
    } else {
        swalAlert($newSendObj["status"], 'Warning', $newSendObj["message"]);
    }
}


?>
<style>
    .accordionGlRowHover:hover {
        background-color: #00306026 !important;
    }

    td.font-bold.bg-alter {
        background: #afc1d2;
    }

    td.bg-grey.text-white {
        background: #003060;
    }

    .blnc-sheet-card {
        background: #fff;
    }

    .blnc-sheet-card table thead tr th {
        vertical-align: middle;
    }

    .blnc-sheet-card table tbody tr td {
        background: #fff;
    }

    .blnc-sheet-card table tbody tr:nth-child(2n+1) td {
        background: #e7f2fd;
        border-color: #e7f2fd;
    }


    .filter-date {
        max-width: 200px;
        margin: 0 0;
        float: right;
    }

    div#form-container .card-footer {
        background: #003060;
    }

    .card.action-card input[type=checkbox] {
        height: 0;
        width: 0;
        visibility: hidden;
    }

    .card.action-card label {
        cursor: pointer;
        text-indent: -9999px;
        width: 35px;
        height: 20px;
        background: #00306042;
        display: block;
        border-radius: 100px;
        position: relative;
        margin-bottom: 0 !important;
    }

    .card.action-card label:after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 13px;
        height: 13px;
        background: #fff;
        border-radius: 90px;
        transition: 0.3s;
    }

    .card.action-card input:checked+label {
        background: #003060;
    }

    .card.action-card input:checked+label:after {
        left: calc(100% - 4px);
        transform: translateX(-100%);
    }

    .card.action-card label:active:after {
        width: 40px;
    }

    .card.action-card ul {
        padding-left: 0;
    }

    .card.action-card ul li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        list-style: armenian;
    }

    .card.action-card ul li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        list-style-type: circle !important;
        background: #cfd8e1;
        padding: 10px 20px;
        border-radius: 12px;
        margin: 10px 0 20px;
    }

    .action-switch {
        display: flex;
        align-items: center;
        gap: 15px;
    }




    .step-container {
        position: relative;
        text-align: center;
        transform: translateY(-43%);
    }

    .step-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #fff;
        border: 2px solid #e9ecef;
        line-height: 30px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        cursor: pointer;
        /* Added cursor pointer */
    }

    .step-circle.first {
        background-color: #003060;
        color: #fff;
    }

    .step-line {
        position: absolute;
        top: 16px;
        left: 50px;
        width: calc(100% - 100px);
        height: 2px;
        background-color: #007bff;
        z-index: -1;
    }

    #multi-step-form {
        overflow-x: hidden;
    }

    div#progress-bar {
        background: #003060;
        transition-duration: 0.5s;
    }

    label.float-label {
        position: relative;
        top: -26px;
        left: 0;
        background: #fff;
        padding: 0px 8px;
        font-size: 20px;
    }

    .step {
        border: 1px solid #ccc;
        padding: 10px 20px 50px;
        border-radius: 7px;
        margin: 20px 0;
    }

    .step i {
        color: #343434;
        font-size: 15px;
        cursor: pointer;
    }
</style>
<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../public/assets/sales-order.css">
<link rel="stylesheet" href="../public/assets/accordion.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="" class="text-dark"><i class="fa fa-list po-list-icon"></i>Communication Setup</a></li>
                <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-list po-list-icon"></i>Whatsapp Setup</a></li>
                <!-- <li class="breadcrumb-item active">
                    <a href="manage-inventory.php?post-grn" class="text-dark"><i class="fa fa-plus po-list-icon"></i>Add New</a>
                </li> -->
                <li class="back-button">
                    <a href="">
                        <i class="fa fa-reply po-list-icon"></i>
                    </a>
                </li>
            </ol>
        </div>
        <div class="container-fluid">
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <div id="container" class="container mt-5">
                        <div class="progress px-1" style="height: 3px;">
                            <div id="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="step-container d-flex justify-content-between">
                            <div class="step-circle first" onclick="displayStep(1)">1</div>
                            <div class="step-circle second" onclick="displayStep(2)">2</div>
                        </div>

                        <form id="multi-step-form">
                            <div class="step step-1">
                                <label for="" class="float-label">Setup</label>
                                <div class="form-input mb-3">
                                    <label for="field1" class="form-label">API Key</label>
                                    <input type="text" class="form-control" id="field1" name="field1">
                                </div>
                                <div class="form-input mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                                <button type="button" class="btn btn-primary next-step float-right mt-2">Submit and Proceed</button>
                            </div>

                            <div class="step step-2">
                                <label for="" class="float-label">Action</label>
                                <div class="card action-card bg-transparent mb-0">
                                    <div class="card-body p-0">
                                        <ul id="checkbox-list">
                                            <li>
                                                <div class="label d-flex gap-3">
                                                    <span>1.</span>
                                                    <p class="text-sm font-bold">So Creation mail send</p>
                                                </div>
                                                <div class="action-switch">
                                                    <i class="far fa-edit" data-bs-toggle="modal" data-bs-target="#editAction"></i>
                                                    <!-- <ion-icon name="create" size="small" data-bs-toggle="modal" data-bs-target="#editAction"></ion-icon> -->
                                                    <input type="checkbox" id="switch" />
                                                    <label for="switch">Toggle</label>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary prev-step">Get Back</button>
                            </div>
                        </form>

                        <div class="modal fade right" id="editAction" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-white" id="exampleModalLabel">Modal title</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        ...
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                <script>
                    var currentStep = 1;
                    var updateProgressBar;

                    function displayStep(stepNumber) {
                        if (stepNumber >= 1 && stepNumber <= 2) {
                            $(".step-" + currentStep).hide();
                            $(".step-" + stepNumber).show();
                            currentStep = stepNumber;
                            updateProgressBar();
                        }
                    }

                    $(document).ready(function() {
                        $("#multi-step-form").find(".step").slice(1).hide();

                        $(".next-step").click(function() {
                            if (currentStep < 2) {
                                $(".step-" + currentStep).addClass(
                                    "animate__animated animate__fadeOut"
                                );
                                currentStep++;
                                setTimeout(function() {
                                    $(".step").removeClass("animate__animated animate__fadeOut").hide();
                                    $(".step-" + currentStep)
                                        .show()
                                        .addClass("animate__animated animate__fadeIn");
                                    updateProgressBar();
                                }, 500);
                            }
                        });

                        $(".prev-step").click(function() {
                            if (currentStep > 1) {
                                $(".step-" + currentStep).addClass(
                                    "animate__animated animate__fadeOut"
                                );
                                currentStep--;
                                setTimeout(function() {
                                    $(".step")
                                        .removeClass("animate__animated animate__fadeOut")
                                        .hide();
                                    $(".step-" + currentStep)
                                        .show()
                                        .addClass("animate__animated animate__fadeIn");
                                    updateProgressBar();
                                }, 500);
                            }
                        });

                        updateProgressBar = function() {
                            var progressPercentage = (currentStep - 1) * 100;
                            $("#progress-bar").css("width", progressPercentage + "%");
                            $(".step-circle").css({
                                "background": "#003060",
                                "color": "#fff",
                                "font-weight": "500"
                            });
                        };
                    });
                </script>

                <script>
                    const listContainer = document.getElementById("checkbox-list");
                    const itemCount = 6; // Number of list items

                    for (let i = 2; i <= itemCount; i++) {
                        const listItem = document.createElement("li");
                        listItem.innerHTML = `
                                    <div class="label d-flex gap-3">
                                        <span>${i}.</span>
                                        <p class="text-sm font-bold">So Creation mail send</p>
                                    </div>
                                    <div class="action-switch">
                                        <i class="far fa-edit" data-bs-toggle="modal" data-bs-target="#editAction"></i>
                                        <input type="checkbox" id="switch-${i}" />
                                        <label for="switch-${i}">Toggle</label>
                                    </div>
                                    `;
                        listContainer.appendChild(listItem);
                    }
                </script>


            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->
<?php
include("common/footer.php");
?>