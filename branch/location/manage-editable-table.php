<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// error_log("An error occurred", 3, "/var/log/php_errors.log");

require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-customers-controller.php");


// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

$BranchSoObj = new BranchSo();
$customerDetailsObj = new CustomersController();


$company = $BranchSoObj->fetchCompanyDetails()['data'];
$currencyIcon = $BranchSoObj->fetchCurrencyIcon($company['company_currency'])['data']['currency_icon'];

if (isset($_POST['addNewPgiFormSubmitBtn'])) {
    // console($_POST);
    $addBranchSoDeliveryPgi = $BranchSoObj->insertBranchPgi($_POST);
    // console($addBranchSoDeliveryPgi);
    if ($addBranchSoDeliveryPgi['success'] == "true") {
        $addBranchSoDeliveryPgiItems = $BranchSoObj->insertBranchPgiItems($_POST, $addBranchSoDeliveryPgi['lastID']);
        if ($addBranchSoDeliveryPgiItems['success'] == "true") {
            swalToast($addBranchSoDeliveryPgiItems["success"], $addBranchSoDeliveryPgiItems["message"]);
        } else {
            swalToast($addBranchSoDeliveryPgiItems["success"], $addBranchSoDeliveryPgiItems["message"]);
        }
    } else {
        // console($addBranchSoDeliveryPgi);
        swalToast($addBranchSoDeliveryPgi["success"], $addBranchSoDeliveryPgi["message"]);
    }
}

$companyDetails = $BranchSoObj->fetchCompanyDetailsById($company_id)['data'];
$branchDetails = $BranchSoObj->fetchBranchDetailsById($branch_id)['data'];
$branchAdminDetails = $BranchSoObj->fetchBranchAdminDetailsById($branch_id)['data'];
$locationDetails = $BranchSoObj->fetchBranchLocalionDetailsById($location_id)['data'];
$bankDetails = $BranchSoObj->fetchCompanyBankDetails()['data'];

// console($singleSoDetails);
?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<style>
    p.editable-data-area {
        background: #fff;
        padding: 7px;
        border-radius: 5px;
    }
</style>

<?php
if (isset($_GET['create-pgi'])) {
?>
    <h1>Hello</h1>
<?php } else { ?>


    <div class="content-wrapper">
        <section class="content">
            <table class="table defaultDataTable table-hover">
                <thead>
                    <tr>
                        <th>test</th>
                        <th>test</th>
                        <th>test</th>
                        <th>test</th>
                        <th>test</th>
                        <th>test</th>
                        <th>test</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                        <td>
                            <p class="editable-data-area" contenteditable="true">fghjkl</p>
                        </td>
                    </tr>
                </tbody>
            </table>
    </div>
    </div>
    </section>
    </div>
    <!-- End Pegination from------->
<?php } ?>

<?php
require_once("../common/footer.php");
?>
<script>
    function rm() {
        $(event.target).closest("tr").remove();
    }

    function addMultiQty(id) {
        let addressRandNo = Math.ceil(Math.random() * 100000);
        $(`.modal-add-row_${id}`).append(`<tr><td><span class='has-float-label'><input type='date' name='listItem[${id}][deliverySchedule][${addressRandNo}][multiDeliveryDate]' class='form-control' placeholder='delivery date'><label>Delivery date</label></span></td><td><span class='has-float-label'><input type='text' name='listItem[${id}][deliverySchedule][${addressRandNo}][quantity]' class='form-control' placeholder='quantity'><label>quantity</label></span></td><td><a class='btn btn-danger' onclick='rm()'><i class='fa fa-minus'></i></a></td></tr>`);
    }
</script>
<script>
    $(document).ready(function() {
        $('#itemsDropDown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });
        $('#customerDropDown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });
        // customers ********************************
        function loadCustomers() {
            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-customers.php`,
                beforeSend: function() {
                    $("#customerDropDown").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    $("#customerDropDown").html(response);
                }
            });
        }
        loadCustomers();
        // get customer details by id
        $("#customerDropDown").on("change", function() {
            let itemId = $(this).val();

            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-customers-list.php`,
                data: {
                    act: "listItem",
                    itemId
                },
                beforeSend: function() {
                    $("#customerInfo").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    // console.log(response);
                    $("#customerInfo").html(response);
                }
            });
        });
        // **************************************
        function loadItems() {
            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-items.php`,
                beforeSend: function() {
                    $("#itemsDropDown").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    $("#itemsDropDown").html(response);
                }
            });
        }
        loadItems();

        // get item details by id
        $("#itemsDropDown").on("change", function() {
            let itemId = $(this).val();

            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-items-list.php`,
                data: {
                    act: "listItem",
                    itemId
                },
                beforeSend: function() {
                    //  $("#itemsTable").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    console.log(response);
                    $("#itemsTable").append(response);
                }
            });
        });
        $(document).on("click", ".delItemBtn", function() {
            // let id = ($(this).attr("id")).split("_")[1];
            // $(`#delItemRowBtn_${id}`).remove();
            $(this).parent().parent().remove();
        })

        $(document).on('submit', '#addNewItemForm', function(event) {
            event.preventDefault();
            let formData = $("#addNewItemsForm").serialize();
            $.ajax({
                type: "POST",
                url: `ajaxs/so/ajax-items.php`,
                data: formData,
                beforeSend: function() {
                    $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
                    $("#addNewItemsFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');
                },
                success: function(response) {
                    $("#goodTypeDropDown").html(response);
                    $('#addNewItemsForm').trigger("reset");
                    $("#addNewItemsFormModal").modal('toggle');
                    $("#addNewItemsFormSubmitBtn").html("Submit");
                    $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
                }
            });
        });

        $(document).on("keyup change", ".qty", function() {
            let id = $(this).val();
            var sls = $(this).attr("sls");
            alert(sls);
            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-items-list.php`,
                data: {
                    act: "totalPrice",
                    itemId: "ss",
                    id
                },
                beforeSend: function() {
                    $(".totalPrice").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    console.log(response);
                    $(".totalPrice").html(response);
                }
            });
        })

        $(".deliveryScheduleQty").on("change", function() {
            let qtyVal3 = ($(this).attr("id")).split("_")[1];
            let qtyVal = $(this).find(":selected").data("quantity");
            // let qtyVal2 = $(this).find(":selected").data("deliverydate");
            // let qtyVal = $(this).find(":selected").children("span");
            // $( "#myselect option:selected" ).text();
            console.log(qtyVal);
            $(`#itemQty_${qtyVal3}`).val(qtyVal);
        })

    })
</script>
<script>
    $('.hamburger').click(function() {
        $('.hamburger').toggleClass('show');
        $('#overlay').toggleClass('show');
        $('.nav-action').toggleClass('show');
    });
</script>