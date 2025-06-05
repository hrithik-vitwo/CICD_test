<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-goods-controller.php");

//$adminId = $_SESSION['adminId'];
//  $check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant ");
// $check_var_data = $check_var_sql['data'];
// $max = $check_var_data['month_end'];
// $min = $check_var_data['month_start'];

// if(isset($_POST['transfer'])){
//     console($_POST);
// }

$today = date("Y-m-d");
if (isset($_POST["changeStatus"])) {
    $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
    swalToast($newStatusObj["status"], $newStatusObj["message"],);
}


require_once("../../app/v1/functions/branch/func-items-controller.php");
$goodsController = new GoodsController();
if (isset($_POST['createData'])) {

    $addNewObj = $goodsController->post_stock($_POST);

    //console($addNewObj);

    swalToast($addNewObj["status"], $addNewObj["message"]);
}

if (isset($_POST["visit"])) {
    $newStatusObj = VisitBranches($_POST);
    redirect(BRANCH_URL);
}


//$sql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE company_branch_id=".$branch_id." AND company_id=".$company_id." `vendor_status`!='deleted'";

// if (isset($_POST["createdata"])) {
//     $addNewObj = createDataBranches($_POST);
//     if ($addNewObj["status"] == "success") {
//         $branchId = base64_encode($addNewObj['branchId']);
//         redirect($_SERVER['PHP_SELF'] . "?branchLocation=" . $branchId);
//         swalToast($addNewObj["status"], $addNewObj["message"]);
//         // console($addNewObj);
//     } else {
//         swalToast($addNewObj["status"], $addNewObj["message"]);
//     }
// }

// if (isset($_POST["editdata"])) {
//     $editDataObj = updateDataBranches($_POST);

//     swalToast($editDataObj["status"], $editDataObj["message"]);
// }

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

// if (isset($_POST["editNewPOFormSubmitBtn"])) {
//     // console($_POST);
//     $editBranchPo = $BranchPoObj->editBranchPo($_POST);

//     swalToast($editBranchPo["status"], $editBranchPo["message"]);
// }



?>
<style>
    .item {
        display: none;
    }

    .sl {
        display: none;
    }

    .item_sl {
        display: none;
    }
</style>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<script src="http://alphadev.vitwo.ai/public/assets/plugins/jqvmap/select2.css"></script>

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">


            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Stock Transfer List</a></li>
                <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Stock Transfer</a></li>
                <li class="back-button">
                    <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                        <i class="fa fa-reply po-list-icon"></i>
                    </a>
                </li>
            </ol>

            <form action="" method="POST" id="transfer" name="transfer">

                <input type="hidden" name="createData" id="createData" value="">





                <div class="row">

                    <div class="row po-form-creation">

                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card so-creation-card po-creation-card">
                                <div class="card-header">
                                    <div class="row others-info-head">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="head">
                                                <i class="fa fa-info"></i>
                                                <h4>Item Post</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>




                                <div class="row info-form-view">

                                <div class="col-lg-4 col-md-4 col-sm-12 form-inline">
                                                    <label for="">Group</label>
                                <select name="group" id="group"  class="select2 form-control group_1 group">
                                    <option value="">Groups</option>
                                    <?php
                                    $funcList = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE  `companyId`=$company_id ", true);
                                    console($funcList);

                                    foreach ($funcList["data"] as $func) {
                                    ?>
                                        <option value="<?= $func['goodGroupId'] ?>">
                                            <?= $func['goodGroupName'] ?></option>
                                    <?php } ?>
                                </select>
                                                </div>


                                                <div class="col-lg-4 col-md-4 col-sm-12 form-inline">
                                                    <label for="">Item</label>
                                <select name="item" id="itemsDropDown"  class="select2 form-control itemsDropDown_1 itemsDropDown">
                                    <option value="">Items</option>
                                    <?php
                                    $funcList = queryGet("SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `erp_inventory_items` as goods ON stock.itemId=goods.itemId WHERE stock.location_id=$location_id AND goods.itemId != '' ORDER BY stock.stockSummaryId desc", true);
                                    console($funcList);

                                    foreach ($funcList["data"] as $func) {
                                    ?>
                                        <option value="<?= $func['itemId'] ?>">
                                            <?= $func['itemName'] ?>(<?= $func['itemCode'] ?>)</option>
                                    <?php } ?>
                                </select>
                                                </div>
                                
                                                <div class="col-lg-4 col-md-4 col-sm-12 form-inline">
                                                    <label for="">Storage Location</label>

                                <select name="storageLocation"  id="storagelocation" class="select2 form-control storagelocation storagelocation_1">
                                    <option value="">Storage Location</option>


                                </select>
                                                </div>
                                </div>
                                <div class="row info-form-view">

                                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                                        <label for="date">Quantity</label>
                                                        <input step="0.01" type="number" name="quantity" id="quantity" class="form-control ">
                                                    </div>

                                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                                        <label for="date">Moving Weighted Price</label>
                                                        <input step="0.01" type="number"  name="price" id="price" class="form-control price">
                                                    </div>

                                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                                        <label for="date">Total</label>
                                                        <input step="0.01" type="number"  name="total" id="total" class="form-control price">
                                                    </div>

                                                </div>





                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">

                            <button type="submit" name="addNewPOFormSubmitBtn" class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Save & Close</button>

                        </div>
                    </div>
                </div>


            </form>

        </div>
    </section>
</div>




<?php
require_once("../common/footer.php");
?>
<script>
    $(document).on("click", ".add-btn-minus", function() {
        $(this).parent().parent().remove();
    });


    $(document).on("click", "#shipToAddressSaveBtn", function() {
        document.getElementById("addresscheckbox").checked = false;

        console.log("clickinggggggggg");
        let radioBtnVal = $('input[name="shipToAddress"]:checked').val();
        let addressHead = ($(`#shipToAddressHeadText_${radioBtnVal}`).html()).trim();
        let addressBody = ($(`#shipToAddressBodyText_${radioBtnVal}`).html()).trim();
        console.log(addressBody);
        $("#shipToAddressDiv").html(addressBody);
    });

    // $(document).on("click","#addresscheckbox", function(){
    //   console.log("clickinggggggggg");
    //     let radioBtnVal = $('input[name="shipToAddress"]:checked').val();
    //     let addressHead = ($(`#shipToAddressHeadText_${radioBtnVal}`).html()).trim();
    //     let addressBody = ($(`#shipToAddressBodyText_${radioBtnVal}`).html()).trim();
    //     console.log(addressBody);
    //     $("#shipToAddressDiv").html(addressBody);
    // });

 


    // function loadItems(itemId) {
      
    //     $.ajax({
    //         type: "GET",
    //         itemId,
    //         url: `ajaxs/transfer/ajax-items.php`,
    //         beforeSend: function() {
    //             $("#itemsDropDown").html(`<option value="">Loding...</option>`);
    //         },
    //         success: function(response) {
    //             $("#itemsDropDown").html(response);
    //         }
    //     });
    // }
 
</script>
<script>
    $(document).ready(function() {

        $(".add_data").click(function() {
            var data = this.value;
            $("#creatData").val(data);
            //confirm('Are you sure to Submit?')
            $("#submitPoForm").submit();
        });
    });
    $(document).ready(function() {
        $('#itemsDropDown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });
        $('#group')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });


        $(document).ready(function() {
            $('input[type="radio"]').click(function() {
                var inputValue = $(this).attr("value");
                var targetBox = $("." + inputValue);
                $(".box").not(targetBox).hide();
                $(targetBox).show();
            });
        });
        // **************************************

        // get item details by id
        $(document).on("change", ".itemsDropDown", function() {
            let itemId = $(this).val();
            let itemRowVal = $(this).data('val');
            //alert(itemRowVal);

            $.ajax({
                type: "GET",
                url: `ajaxs/transfer/ajax-items-list.php`,
                data: {
                    act: "listItem",
                    itemId
                },
                beforeSend: function() {
                 
                    $("#storagelocation").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                  
                    var obj = jQuery.parseJSON(response);
                    
                    $("#price").val(obj['mwp']);
                    $("#storagelocation").html(obj['slocation']);
                    calculate_total();


                }

            });
        });

        $(document).on("change", ".group", function() {
            let itemId = $(this).val();

            $.ajax({
            type: "GET",
            url: `ajaxs/transfer/ajax-items.php`,
            data: {
                  
                    itemId
                },
            beforeSend: function() {
              //  console.log(1);
                $("#itemsDropDown").html(`<option value="">Loding...</option>`);
            },
            success: function(response) {
               
                console.log(response);
                $("#itemsDropDown").html(response);
            }
        });
           // loadItems(itemId);
           // alert(itemId);

        });


       


      
   

        // get item details by id
     
      

        $(document).on('submit', '#addNewItemForm', function(event) {
            event.preventDefault();
            let formData = $("#addNewItemsForm").serialize();
            $.ajax({
                type: "POST",
                url: `ajaxs/po/ajax-items.php`,
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

        // $(document).on("keyup change", ".qty", function() {
        //     let id = $(this).val();
        //     var sls = $(this).attr("sls");
        //     alert(sls);
        //     $.ajax({
        //         type: "GET",
        //         url: `ajaxs/po/ajax-items-list.php`,
        //         data: {
        //             act: "totalPrice",
        //             itemId: "ss",
        //             id
        //         },
        //         beforeSend: function() {
        //             $(".totalPrice").html(`<option value="">Loding...</option>`);
        //         },
        //         success: function(response) {
        //             console.log(response);
        //             $(".totalPrice").html(response);
        //         }
        //     });
        // })


    });


    $(document).on("click", "#btnSearchCollpase", function() {
        sec = document.getElementById("btnSearchCollpase").parentElement;
        coll = sec.getElementsByClassName("collapsible-content")[0];

        if (sec.style.width != '100%') {
            sec.style.width = '100%';
        } else {
            sec.style.width = 'auto';
        }

        if (coll.style.height != 'auto') {
            coll.style.height = 'auto';
        } else {
            coll.style.height = '0px';
        }

        $(this).children().toggleClass("fa-search fa-times");
    });



    $('.hamburger').click(function() {
        $('.hamburger').toggleClass('show');
        $('#overlay').toggleClass('show');
        $('.nav-action').toggleClass('show');
    });
</script>




<script>
    $('#movemenrtypesDropdown').change(function() {
        var select = this.value;
        if ($(this).val() == 'item') {
            $('.item').show();
            $('.sl').hide();
            $('.item_sl').show();

        } else if ($(this).val() == 'storage_location') {
            $('.item').hide();
            $('.sl').show();
            $('.item_sl').hide();

        } else {
            $('.sl').hide();
            $('.item').hide();
            $('.item_sl').hide();

        }
    });

    // $('#item_transfer').change(function(){

    //     var select = this.value;






    // });





    /********************************************** */
    function calculateAllItemsGrandAmount() {
        let grandTotal = 0;
        $(".itemTotalPrice").each(function() {
            let itemTotalPrice = parseFloat($(this).val());
            grandTotal += itemTotalPrice > 0 ? itemTotalPrice : 0;
        });
        $("#grandTotalAmount").html(grandTotal.toFixed(2));
        $("#grandTotalAmountInput").val(grandTotal.toFixed(2));
    }

    function calculateOneItemRowAmount(rowNum) {
        let qty = parseFloat($(`#itemQty_${rowNum}`).val());
        qty = qty > 0 ? qty : 0;
        let unitPrice = parseFloat($(`#itemUnitPrice_${rowNum}`).val());
        unitPrice = unitPrice > 0 ? unitPrice : 0;
        let totalPrice = unitPrice * qty;
        $(`#itemTotalPrice_${rowNum}`).val(totalPrice.toFixed(2));
        calculateAllItemsGrandAmount();
    }

    $(document).on("keyup", ".itemQty", function() {
        let rowNum = ($(this).attr("id")).split("_")[1];
        calculateOneItemRowAmount(rowNum);
    });
    $(document).on("keyup", ".itemUnitPrice", function() {
        let rowNum = ($(this).attr("id")).split("_")[1];
        calculateOneItemRowAmount(rowNum);
    });
   function calculate_total(){
  
        let qty =  $("#quantity").val();
        let price = $("#price").val();
        let res = qty*price;
        $("#total").val(res);
   
    }
   
    $("#quantity").keyup(function() {

        calculate_total();

});

$("#price").keyup(function() {

calculate_total();

});


</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />