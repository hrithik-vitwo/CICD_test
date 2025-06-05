<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// error_log("An error occurred", 3, "/var/log/php_errors.log");

require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-customers-controller.php");
?>

hello


<br>
<br>
hjbfh

=========================================================

<?php

$verient_id=1;
// function getinvoiceNo($verient_id){
// global $company_id;


$getQuery = queryGet("SELECT * FROM `erp_iv_varient` WHERE `company_id`='$company_id' AND `id`='$verient_id'", false);
if($getQuery["status"]=='success'){
$queryData = $getQuery["data"];


$unserialized_datas = unserialize($queryData["verient_serialized"]);
// console($unserialized_datas);

foreach ($unserialized_datas as $key => $unserialized_data) {  
  foreach ($unserialized_data as $prefixKey => $prefixdata) {
    $slug = $prefixKey;
    $value = $prefixdata;

    
    $financial_year = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`='$company_id' ORDER BY `created_at` DESC", false);
    $proper_finacial_year = $financial_year["data"]["year_variant_name"];



  }
}



}else{
  $getQueryDefult = queryGet("SELECT * FROM `erp_iv_varient` WHERE `company_id`='$company_id' AND `flag_default`=0 ", false);
  if($getQueryDefult["status"]=='success'){
    $queryData = $getQueryDefult["data"];
    

  }else{


  }
  


}

// return ($getQuery);



// }


// $invoiceno=getinvoiceNo($verient_id);

// console($invoiceno);


?>




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


<script>
  $(document).ready(function() {
    $(document).on("click", ".generateEInvoice", function(e) {
      // $(this).html(`<i class="fa fa-circle-o-notch fa-spin"></i>Loading`);
      let invId = parseInt(($(this).attr("id")).split("_")[1]);
      // console.log("invId",invId);

      $.ajax({
        url: '<?= BASE_URL ?>branch/ajaxs/compliance/ajax-create-e-invoice.php',
        type: 'POST',
        data: {
          invoiceId: invId
        },
        success: function(response, status, xhr) {
          let responseData = JSON.parse(response);
          console.log(responseData);
          alert(`${invId}-> ${responseData["message"]}`);
        },
        error: function(jqXhr, textStatus, errorMessage) {
          console.log(errorMessage);
          alert(`${invId}-> ${errorMessage}`);
        }
      });

    });
  });
</script>