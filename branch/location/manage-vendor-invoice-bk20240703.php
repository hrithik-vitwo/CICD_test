<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-grn-controller.php");


// console($_SESSION);

if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"]);
}

if (isset($_POST["visit"])) {
  $newStatusObj = VisitBranches($_POST);
  redirect(BRANCH_URL);
}

if (isset($_POST["createdata"])) {
  $addNewObj = createDataBranches($_POST);
  if ($addNewObj["status"] == "success") {
    $branchId = base64_encode($addNewObj['branchId']);
    redirect($_SERVER['PHP_SELF'] . "?branchLocation=" . $branchId);
    swalToast($addNewObj["status"], $addNewObj["message"]);
    // console($addNewObj);
  } else {
    swalToast($addNewObj["status"], $addNewObj["message"]);
  }
}

if (isset($_POST["editdata"])) {
  $editDataObj = updateDataBranches($_POST);

  swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

$grnObj = new GrnController();
$BranchSoObj = new BranchSo();
$fetchInvoiceByCustomer = $grnObj->fetchGRNInvoice()['data'];


if (isset($_POST['addNewSOFormSubmitBtn'])) {
  // console($_POST);
  // exit;
  $addBranchSo = $BranchSoObj->addBranchSo($_POST);
  // console($addBranchSo);
  if ($addBranchSo['status'] == "success") {
    $addBranchSoItems = $BranchSoObj->addBranchSoItems($_POST, $addBranchSo['lastID']);
    //console($addBranchSoItems);
    if ($addBranchSoItems['status'] == "success") {
      // swalToast($addBranchSoItems["status"], $addBranchSoItems["message"]);
      swalToast($addBranchSoItems["status"], $addBranchSoItems["message"], $_SERVER['PHP_SELF']);
    } else {
      swalToast($addBranchSoItems["status"], $addBranchSoItems["message"]);
    }
  } else {
    swalToast($addBranchSo["status"], $addBranchSo["message"]);
  }
}

?>

<style>
  /* .customer-modal .nav.nav-tabs li.nav-item a.nav-link {
    font-size: 12px;
  }

  .display-flex-gap {
    gap: 0 !important;
  }

  .card-body.others-info.vendor-info.so-card-body {
    height: 250px !important;
  }

  .fob-section div {
    align-items: center;
    gap: 3px;
  }

  .so-delivery-create-btn {
    display: flex;
    align-items: center;
    gap: 20px;
    max-width: 250px;
    margin-left: auto;
  }

  .customer-modal .modal-header {
    height: 250px !important;
  }


  .display-flex-space-between p {
    width: 77%;
    text-align: left;
  }

  @media (max-width: 575px) {

    .filter-serach-row {
      align-items: center;
      padding-top: 9px;
      margin-bottom: 0 !important;
    }

    .customer-modal .nav.nav-tabs li.nav-item a.nav-link {
      padding: 7px;
    }

    .card-body.others-info.vendor-info.so-card-body {
      height: auto !important;
    }

    .customer-modal .modal-header {
      height: 285px !important;
    }

    .customer-modal .nav.nav-tabs {
      top: 0 !important;
    }

  } */


  .content-wrapper table tr:nth-child(2n+1) td {
    background: #b5c5d3;
  }

  tfoot.individual-search tr th {
    padding: 5px !important;
    border-right: 1px solid #fff !important;
  }

  .vertical-align {
    vertical-align: middle;
  }

  /* .green-text {
    color: #14ca14 !important;
    font-weight: 600;
  }

  .red-text {
    color: red !important;
    font-weight: 600;
  } */

  .dataTables_scrollHeadInner tr th {
    position: sticky;
    top: -1px;
  }

  div.dataTables_wrapper div.dataTables_filter,
  .dataTables_wrapper .row {
    display: flex !important;
    align-items: center;
    justify-content: end;
  }

  /* div.dataTables_wrapper {
    overflow: hidden;
  } */

  div.dataTables_wrapper div.dataTables_filter,
  .dataTables_wrapper .row:nth-child(1),
  div.dataTables_wrapper div.dataTables_filter,
  .dataTables_wrapper .row:nth-child(3) {
    padding: 10px 20px;
  }

  div.dataTables_wrapper div.dataTables_length select {
    width: 60% !important;
    appearance: none !important;
    -webkit-appearance: none;
    -moz-appearance: none;
  }

  .dataTables_scroll {
    position: relative;
    margin-bottom: 10px;
  }

  .dataTables_scroll::-webkit-scrollbar {
    visibility: hidden;
  }

  .dataTables_scrollBody tfoot th {
    background: none !important;
  }

  .dataTables_scrollHead {
    margin-bottom: 40px;
  }

  .dataTables_scrollBody {
    max-height: 75vh !important;
    height: 75% !important;
    overflow: scroll !important;
  }

  .dataTables_scrollFoot {
    position: absolute;
    top: 37px;
    height: 50px;
    overflow: scroll;
  }

  div.dataTables_wrapper div.dataTables_filter input {
    margin-left: 10px;
  }

  div.dataTables_scrollFoot>.dataTables_scrollFootInner th {
    border: 0;
  }

  .dataTables_filter {
    padding-right: 0 !important;
  }

  div.dataTables_wrapper div.dataTables_paginate ul.pagination {
    padding: 0;
    border: 0;
  }

  .dt-top-container {
    display: flex;
    align-items: center;
    padding: 0 20px;
    gap: 20px;
  }

  .transactional-book-table tr td {
    white-space: pre-line !important;
  }

  .dataTables_length {
    margin-left: 4em;
  }

  a.btn.add-col.setting-menu.waves-effect.waves-light {
    position: absolute !important;
    display: flex;
    justify-content: space-between;
    top: 10px !important;
  }

  div.dataTables_wrapper div.dataTables_length label {
    margin-bottom: 0;
  }

  div.dataTables_wrapper div.dataTables_info {
    padding-left: 20px;
    position: relative;
    top: 0;
  }

  .dataTables_paginate {
    position: relative;
    right: 20px;
    bottom: 20px;
    margin-top: -15px;
  }

  .dt-center-in-div {
    display: block;
    /* order: 3; */
    margin-left: auto;
  }

  .dt-buttons.btn-group.flex-wrap button {
    background-color: #003060 !important;
    border-color: #003060 !important;
    border-radius: 7px !important;
  }

  /* .setting-row .col .btn.setting-menu {
    position: absolute !important;
    right: 255px;
    top: 10px;
  } */

  .dt-buttons.btn-group.flex-wrap {
    gap: 10px;
  }


  table.dataTable>thead .sorting:before,
  table.dataTable>thead .sorting:after,
  table.dataTable>thead .sorting_asc:before,
  table.dataTable>thead .sorting_asc:after,
  table.dataTable>thead .sorting_desc:before,
  table.dataTable>thead .sorting_desc:after,
  table.dataTable>thead .sorting_asc_disabled:before,
  table.dataTable>thead .sorting_asc_disabled:after,
  table.dataTable>thead .sorting_desc_disabled:before,
  table.dataTable>thead .sorting_desc_disabled:after {

    display: block !important;

  }

  .dataTable thead tr th,
  .dataTable tfoot.individual-search tr th {
    padding-right: 30px !important;
    border-right: 0 !important;
  }

  select.fy-dropdown {
    position: absolute;
    max-width: 100px;
    top: 14px;
    left: 255px;
  }

  .daybook-filter-list.filter-list {
    display: flex;
    gap: 7px;
    justify-content: flex-end;
    position: relative;
    top: -35px;
    left: -75px;
    float: right;
  }

  .daybook-filter-list.filter-list a.active {
    background-color: #003060;
    color: #fff;
  }

  .vendor-invoice-tab.filter-list {
    display: flex;
    gap: 7px;
    justify-content: flex-start;
    position: relative;
    top: 0;
    left: 0;
  }

  .vendor-invoice-tab.filter-list a.active {
    background-color: #003060;
    color: #fff;
  }



  @media (max-width: 769px) {
    .dt-buttons.btn-group.flex-wrap {
      gap: 10px;
      position: absolute;
      top: -39px;
      right: 60px;
    }

    .dt-buttons.btn-group.flex-wrap button {
      max-width: 60px;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
      margin-top: -10px;
    }


  }

  @media (max-width :575px) {
    .dataTables_scrollFoot {
      position: absolute;
      top: 28px;
    }

    .dt-top-container {
      display: flex;
      align-items: baseline;
      padding: 0 20px;
      gap: 20px;
      flex-direction: column-reverse;
      flex-wrap: nowrap;
    }

    .dataTables_length {
      margin-left: 0;
      margin-bottom: 1em;
    }

    select.fy-dropdown {
      position: absolute;
      max-width: 125px;
      top: 155px;
      left: 189px;
    }

    div.dataTables_wrapper div.dataTables_length select {
      width: 164px !important;
    }

    .dt-center-in-div {
      margin: 3px auto;
    }

    div.dataTables_filter {
      right: 0;
      margin-top: 0;
      position: relative;
      right: -43px;
    }

    .dt-buttons.btn-group.flex-wrap {
      gap: 10px;
      position: relative;
      top: 0;
      right: 0;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
      margin-top: 40px;
    }

    .dataTables_length label {
      font-size: 0;
    }
  }

  @media (max-width: 376px) {
    div.dataTables_wrapper div.dataTables_filter {
      margin-top: 0;
      padding-left: 0 !important;
    }

    select.fy-dropdown {
      position: absolute;
      max-width: 109px;
      top: 144px;
      left: 189px;
    }

    div.dataTables_wrapper div.dataTables_filter input {
      max-width: 150px;
    }

    select.fy-dropdown {
      max-width: 100px;
    }

    /* div.dataTables_wrapper div.dataTables_length select {
      width: 164px !important;
    } */
  }
</style>


<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<!-- <link rel="stylesheet" href="../../public/assets/accordion.css"> -->
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<?php
if(isset($_GET["value"]) && $_GET["value"] == "all")
{
  require_once("components/vendor/pending-payment.php");
}
elseif(isset($_GET["value"]) && $_GET["value"] == "payable")
{
  require_once("components/vendor/payable-payment.php");
}
elseif(isset($_GET["value"]) && $_GET["value"] == "due")
{
  require_once("components/vendor/due-payment.php");
}
elseif(isset($_GET["value"]) && $_GET["value"] == "overdue")
{
  require_once("components/vendor/overdue-payment.php");
}
elseif(isset($_GET["value"]) && $_GET["value"] == "paid")
{
  require_once("components/vendor/paid-payment.php");
}
else
{
  require_once("components/vendor/pending-payment.php");
}
?>
  
  <!-- End Pegination from------->


  <script>

function srch_frm() {
    if ($('#form_date_s').val().trim() != '' && $('#to_date_s').val().trim() === '') { //$("#phone_r_err").css('display','block');
      //$("#phone_r_err").html("Your Phone Number");
      alert("Enter To Date");
      $('#to_date_s').focus();
      return false;
    }
    if ($('#to_date_s').val().trim() != '' && $('#form_date_s').val().trim() === '') { //$("#phone_r_err").css('display','block');
      //$("#phone_r_err").html("Your Phone Number");
      alert("Enter From Date");
      $('#form_date_s').focus();
      return false;
    }

  }

    $(document).ready(function() {

      $("#initiate_id").click(function(e) {

        if ($("input:checkbox[class=checkbx]:checked").length === 0) {
                alert("Select Atleast one check-box");
            } else {
              var yourArray = [];
                $("input:checkbox[class=checkbx]:checked").each(function() {
                    var id = $(this).val();
                    yourArray.push($(`#id_${id}`).val());
                });

                var array = JSON.stringify(yourArray);
                //ajax
                $.ajax({
                url: "ajaxs/vendor/ajax-vendor-status-change.php?ids=" + array,
                type: "GET",
                beforeSend: function() {},
                success: function(responseData) {
                    var responseObj = JSON.parse(responseData);

                    if(responseObj.status == "success")
                    {
                        Swal.fire({
                          icon: responseObj.status,
                          title: responseObj.code,
                          text: responseObj.message,
                      }).then(function() {
                        location.reload();
                      });
                    }
                    else
                    {
                      Swal.fire({
                          icon: responseObj.status,
                          title: responseObj.code,
                          text: responseObj.message,
                      }).then(function() {
                        location.reload();
                      });
                    }

                    // location.reload();
                }
            });

            }

      });




      $('.reverseGRNIV').click(function(e) {
        e.preventDefault(); // Prevent default click behavior

        var dep_keys = $(this).data('id');
        var $this = $(this); // Store the reference to $(this) for later use

        Swal.fire({
          icon: 'warning',
          title: 'Are you sure?',
          text: 'You want to reverse this?',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Reverse'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              type: 'POST',
              data: {
                dep_keys: dep_keys,
                dep_slug: 'reverseGRNIV'
              },
              url: 'ajaxs/ajax-reverse-post.php',
              beforeSend: function() {
                $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
              },
              success: function(response) {
                var responseObj = JSON.parse(response);
                console.log(responseObj);

                if (responseObj.status == 'success') {
                  $this.parent().parent().find('.listStatus').html('reversed');
                  $this.parent().parent().find('.listStatus').addClass("status-warning");
                  $this.parent().html('');
                } else {
                  $this.html('<i class="far fa-undo po-list-icon"></i>');
                }

                let Toast = Swal.mixin({
                  toast: true,
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 4000
                });
                Toast.fire({
                  icon: responseObj.status,
                  title: '&nbsp;' + responseObj.message
                }).then(function() {
                  // location.reload();
                });
              }
            });
          }
        });
      });

      $("#dataTable tfoot th").each(function() {
        var title = $(this).text();
        $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
      });

      // DataTable
      var columnSl = 0;
      var table = $("#dataTable").DataTable({
        dom: '',
        buttons: ['copy', 'csv', 'excel', 'print'],
        "lengthMenu": [
          [1000, 5000, 10000, -1],
          [1000, 5000, 10000, 'All'],
        ],
        "scrollY": 200,
        "scrollX": true,
        "ordering": false,
      });


      $('.pay_btn').on('click', function() {
        var image = "<?= BASE_URL ?>public/assets/img/logo/vitwo-logo.png";
        var attr = $(this).data("amount");
        var amount = $(".attr_" + attr).val();

        // alert(image);

        var options = {
          "key": "rzp_test_zdoyJ0Amdyg3HB", // Enter the Key ID generated from the Dashboard
          "amount": amount * 100, // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
          "currency": "INR",
          "name": "VITWO",
          "description": "Test Transaction",
          "image": "http://devalpha.vitwo.ai//public/storage/logo/165985132599981.ico",
          "callback_url": "https://eneqd3r9zrjok.x.pipedream.net/",
          // "prefill": {
          //     "name": "Gaurav Kumar",
          //     "email": "gaurav.kumar@example.com",
          //     "contact": "9000090000"
          // },
          "notes": {
            "address": "Razorpay Corporate Office"
          },
          "theme": {
            "color": "#3399cc"
          }
        };
        var rzp1 = new Razorpay(options);

        rzp1.open();
        e.preventDefault();
      });



    });
  </script>

<?php
require_once("../common/footer.php");
?>