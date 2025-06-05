<?php
require_once("../../app/v1/connection-branch-admin.php");

administratorLocationAuth();

require_once("../common/header.php");

require_once("../common/navbar.php");

require_once("../common/sidebar.php");

require_once("../common/pagination.php");

require_once("../../app/v1/functions/branch/func-goods-controller.php");

require_once("../../app/v1/functions/branch/func-bom-controller.php");

require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");

require_once("../../app/v1/functions/branch/func-brunch-po-controller.php");

$accMapp = getAllfetchAccountingMappingTbl($company_id);

$paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['billable_project_gl']);
// console($paccdetails);
$parentGlId = $paccdetails['data']['id'];
$parentGLname = $paccdetails['data']['gl_label'];


$goodsController = new GoodsController();
$BranchPoObj = new BranchPo();

$goodsBomController = new GoodsBomController();






// $funcList = $BranchPoObj->fetchFunctionality()['data'];
// console($funcList);


if (isset($_POST["creategoodsdata"])) {


  $addNewObj = $goodsController->createGoods($_POST + $_FILES);


  if ($addNewObj["status"] == "success") {
    // console($_POST);
    // exit();
    swalAlert($addNewObj["status"], ucfirst($addNewObj["status"]), $addNewObj["message"], BASE_URL . "branch/location/goods.php");
  } else {
    swalAlert($addNewObj["status"], ucfirst($addNewObj["status"]), $addNewObj["message"]);
  }

  //swalToast($addNewObj["status"], $addNewObj["message"], $_SERVER['PHP_SELF']);
}

if (isset($_POST["createLocationItem"])) {
  $addNewObj = $goodsController->createGoodsLocation($_POST);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}




if (isset($_POST["editgoodsdata"])) {
  // console($_POST+$_FILES);
  $addNewObj = $goodsController->editGoods($_POST + $_FILES);
  swalToast($addNewObj["status"], $addNewObj["message"], BASE_URL . "branch/location/goods.php");
}



if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST,  $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

?>



<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">
<link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.0.45/css/materialdesignicons.min.css">
<link rel="stylesheet" href="../../public/assets/comboTreeWrapper.css">

<style>
  .form-input {
    border: 0;
  }

  #menu {
    position: fixed;
    top: 0;
    right: 0;
    width: 300px;
    height: 100%;
    background: #f2b33d;
  }

  #menu ul {
    margin: 0;
    padding: 0;
  }

  #menu ul li {
    margin: 0 10px;
    list-style: none;
    display: block;
    line-height: 30px;
    border-bottom: 1px solid #d39b33;
  }

  #menu ul li.active {
    background: #fbce52;
  }

  #menu ul li a {
    padding: 6px 0;
    color: #fff;
    font-size: 14px;
    font-weight: bold;
    text-transform: uppercase;
    display: block;
    text-decoration: none;
    -webkit-transition-property: all;
    -webkit-transition-duration: 0.1s;
    -webkit-transition-timing-function: ease-out;
    -moz-transition-property: all;
    -moz-transition-duration: 0.1s;
    -moz-transition-timing-function: ease-out;
    -ms-transition-property: all;
    -ms-transition-duration: 0.1s;
    -ms-transition-timing-function: ease-out;
    -o-transition-property: all;
    -o-transition-duration: 0.1s;
    -o-transition-timing-function: ease-out;
    transition-property: all;
    transition-duration: 0.1s;
    transition-timing-function: ease-out;
  }

  #menu ul li:hover {
    border-bottom: 1px solid #000;
  }

  #menu ul li a:hover {
    color: #000;
  }

  .menu_icon {
    position: fixed;
    right: 20px;
    top: 20px;
    background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAD4AAAASCAYAAAADr20JAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAe5JREFUeNrUl0tLQkEUx+eGUPSggoi+Qd8hiiKyKBVJE7QXLWoV9FGCIChol1AQJlS7Fu0LWgRtKoJa9cBMxWdZt/+Jc2GymyncuXAHfgrOnPGeex7/GU3X9SchRLNQNy5Av4X70bOugTOwVadtCPjAitDVjysgLGSV982DSB12E+CVbaMueH8CmhRG/Nri/Y7BHOgC24B8iP5j4wW7oBVkwZGwOBp2MUAlKkU+XGXtGEjy2gzw0e9OdZwYBi/sUAlMm6wZB2npBQWMOSc7TgyBZ8mxkDTnll4MRdov22r46EHOawpr/B0kFO4/AvZAJ8iDGZAB+6ADFLknxH5YwfGs4q5+blPaG5HPcYSNSAfMbBrge4tQO9qE+kHKNAWSrPP0nwUwD+JmBi5OgUaFD3Ur7BkflMByMoPyX4u17w7n/DHIdd7NNf3JkU9z1A9/WTi8qxudPSF19iAYlWSMan6y0s7pTtNB5pEdLFZoOWl4iufo21MpZ72K5YxS705Resf46PoGFk2OrnQh2eGjao6l7sBI9bJiObtUEOk+8MD7F8BslbUe6XKS4suKsON2dmOx027pnE5BW6jBxs+1bmh7kORsU7Gc3Vu8n5e7dwks1XAzE5zeEb7NtYOAE+WM6nUdnIKNOm3DwA+WvwQYAAWqZfqMuV0VAAAAAElFTkSuQmCC) no-repeat;
    width: 30px;
    height: 18px;
  }

  .item_desc {
    display: block;
    width: 100%;
    padding: 0.375rem 0.75rem;
    font-size: 11px;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    border: 1px solid rgb(201 201 201);
    background-color: #fff;
    background-clip: padding-box;
    appearance: none;
    border-radius: 0.25rem;
    transition: box-shadow .15s ease-in-out;
  }

  .label-hidden {
    visibility: hidden;
  }

  .calculate-hsn-row {
    align-items: baseline;
    padding-right: 0;
  }

  .btn-transparent {
    position: absolute;
    top: 23px;
    left: 9px;
    height: 35px;
    z-index: 9;
    width: 92%;
    background: transparent !important;
  }

  .hsn-dropdown-modal .modal-dialog {
    max-width: 700px;
  }

  .hsn-dropdown-modal .modal-dialog .modal-header h4 {
    font-size: 15px;
    margin-bottom: 0;
    white-space: nowrap;
  }

  .hsn-dropdown-modal .modal-dialog .modal-header input {
    max-width: 300px;
    font-size: 12px;
    height: 30px;
    margin: 0;
    margin: 0;
    border: 1px solid #c3c3c3;
    box-shadow: none;
  }

  input.serachfilter-hsn {
    width: 40% !important;
  }

  .hsn-dropdown-modal .modal-body {
    overflow: hidden;
  }

  .hsn-dropdown-modal .modal-body .card {
    background: none;
  }

  .hsn-dropdown-modal .modal-body .card .card-body {
    background: #dbe5ee;
    box-shadow: 3px 5px 11px -1px #0000004d;
  }

  .hsn-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
  }

  .hsn-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 15px;
  }

  .hsn-title h5 {
    margin-bottom: 0;
    font-size: 15px;
    font-weight: 600;
  }

  .tax-per p {
    font-size: 11px;
    font-style: italic;
    font-weight: 600;
    color: #343434;
  }

  .hsn-description p {
    font-size: 12px;
  }

  .highlight {
    background-color: yellow
  }

  .select2-container {
    width: 100% !important;
  }

  .hsn-modal-table tbody td {
    white-space: pre-line !important;
  }

  .hsn-modal-table tbody tr:nth-child(even) td {
    background-color: #b4c7d9;
  }

  .card-body.hsn-code div.dataTables_wrapper div.dataTables_filter,
  .dataTables_wrapper .row:nth-child(3) {
    display: flex;
    position: relative;
    top: 0;
    right: 0;
    justify-content: end;
    padding: 15px;
  }

  .card-body.hsn-code div.dataTables_wrapper div.dataTables_info {
    display: none;
  }

  .card-body.hsn-code div.dataTables_wrapper div.dataTables_filter input {
    margin-left: 0;
    display: inline-block;
    width: auto;
    padding-left: 30px;
    border: 1px solid #8f8f8f;
    color: #1B2559;
    height: 30px;
    border-radius: 8px;
    margin-left: 10px;
  }

  .row.calculate-row {
    justify-content: end;
  }

  .hsn-column {
    padding-right: 0;
  }


  .hsn-dropdown-modal .modal-body {

    max-height: 100%;

    height: 500px;

  }

  .hsn-dropdown-modal .icons-container {
    position: absolute;
    top: 18px;
    right: 0;
    bottom: 0;
    width: 70px;
    height: 30px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .icons-container i {
    color: #9b9b9b;
    font-size: 14px;
  }

  .icon-close {
    position: absolute;
    display: flex;
    align-items: center;
    gap: 5px;
    right: 30px;
  }

  .modal-content.card {
    box-shadow: 1px 1px 19px #4f4f4f;
  }

  p.hsn-description-info {
    /* display: none; */
    max-height: 60px;
    font-size: 10px !important;
    overflow: auto;
  }

  .unit-measure-col,
  .hsn-modal-col {
    border: 1px dashed #8192a3;
    padding-bottom: 11px;
    border-radius: 12px;
    width: 49%;
  }

  .row.basic-info-form-view {
    justify-content: center;
  }

  .dash-border-row {
    justify-content: space-between;
  }


  .serach-input-section button {
    position: absolute;
    border: none;
    display: block;
    width: 15px;
    height: 15px;
    line-height: 16px;
    font-size: 12px;
    border-radius: 50%;
    top: -47em;
    bottom: 0;
    right: 27px;
    margin: auto;
    background: #ddd;
    padding: 0;
    outline: none;
    cursor: pointer;
    transition: .1s;
  }

  .suggestion-item {
    background: #fff;
    margin: 5px 0;
    padding: 10px 15px 10px;
    display: none;
    position: absolute;
    width: 97%;
    z-index: 9;
    height: auto;
    max-height: 250px;
    overflow: auto;
  }

  .suggestion-item li {
    list-style: none;
    padding: 5px 15px 5px;
    border-bottom: 1px solid #ccc;
  }

  .add-btn-hsn {
    height: 50vh;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  #my-link {
    color: gray;
    /* change color to indicate disabled state */
    cursor: default;
    /* remove pointer cursor */
    text-decoration: none;
    /* remove underline */
    opacity: 0.5;
    /* reduce opacity to further indicate disabled state */
  }

  .modal.add-new-hsn .modal-dialog {
    width: 40%;
    /* transform: translateY(30%); */
  }

  .modal.add-new-hsn .modal-dialog .modal-content {
    height: auto;
    border-radius: 12px;
    background: #dbe5ee;
  }

  .row.hsn-details .col {
    height: 75px;
  }

  .row.hsn-details .col .selct-hsn-type {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 2em;
    justify-content: flex-end;
  }

  .modal.add-new-hsn .modal-dialog .modal-body {
    display: flex;
    align-items: baseline;
    height: 250px;
    box-shadow: none;
  }

  .modal.add-new-hsn .modal-dialog .modal-footer {
    background: #b2c8db;
  }


  #dragRoot {
    -moz-user-select: none;
    -ms-user-select: none;
    -webkit-user-select: none;
    user-select: none;
    cursor: default;
    margin: 10px;
    padding: 10px;
    overflow-y: scroll;
    white-space: nowrap;
  }

  #dragRoot ul {
    display: block;
    margin: 0;
    padding: 0 0 0 20px;
  }

  #dragRoot li {
    display: block;
    margin: 2px;
    padding: 2px 2px 2px 0;
  }

  #dragRoot li [class*="node"] {
    display: inline-block;
  }

  #dragRoot li [class*="node"].hover {
    background-color: navy;
    color: white;
  }

  #dragRoot li .node-facility {
    color: #000;
  }

  #dragRoot li .node-cpe {
    color: black;
    cursor: pointer;
  }

  #dragRoot li li {
    border-left: 1px solid silver;
  }

  #dragRoot li li:before {
    color: silver;
    font-weight: 300;
    content: "— ";
  }

  #dragRoot .sidebar {
    width: 450px;
    height: 100%;
    background-color: #e8f4ff;
    position: absolute;
    top: 0;
    right: -550px;
    transition: right 0.5s;
  }

  #dragRoot .sidebar.active {
    right: 0;
  }

  .treeviewModal .modal-dialog {
    max-width: 75%;
  }

  .check-dropdown.dropdown {
    position: relative;
    font-size: 14px;
    color: #333;
  }

  .check-dropdown.dropdown .dropdown-list {
    padding: 12px;
    background: #fff;
    position: absolute;
    top: 30px;
    left: 2px;
    right: 2px;
    box-shadow: 0 1px 2px 1px rgba(0, 0, 0, 0.15);
    transform-origin: 50% 0;
    transform: scale(1, 0);
    transition: transform 0.15s ease-in-out 0.15s;
    max-height: 203px;
    overflow-y: scroll;
    z-index: 9;
  }

  .check-dropdown.dropdown .dropdown-option {
    display: block;
    padding: 8px 12px;
    opacity: 0;
    transition: opacity 0.15s ease-in-out;
  }

  .check-dropdown.dropdown .dropdown-label {
    display: block;
    height: 30px;
    background: #fff;
    border: 1px solid #ccc;
    padding: 6px 12px;
    line-height: 1;
    cursor: pointer;
  }

  .check-dropdown.dropdown .dropdown-label:before {
    content: ">";
    float: right;
  }

  .check-dropdown.dropdown.on .dropdown-list {
    transform: scale(1, 1);
    transition-delay: 0s;
  }

  .check-dropdown.dropdown.on .dropdown-list .dropdown-option {
    opacity: 1;
    transition-delay: 0.2s;
  }

  .check-dropdown.dropdown.on .dropdown-label:before {
    content: "<";
  }

  .check-dropdown.dropdown [type=checkbox] {
    position: relative;
    top: -1px;
    margin-right: 4px;
  }

  .accordion-card-details .display-flex-space-between p.group-desc {
    width: 170px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .text-elipse {
    width: 350px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  @media(max-width: 575px) {
    .hsn-column {
      padding-left: 0;
      padding-right: 15px;
    }

    .base-measure {
      padding-right: 15px !important;
    }

    .calculate-row .col {
      width: 20%;
      padding: 0;
    }

    .calculate-row .col input {
      width: 20px !important;
    }

    .calculate-parent-row .col:nth-child(1) {
      padding-left: 15px;
    }

    .calculate-row {
      padding: 0 15px;
      justify-content: center !important;
    }
  }

  .image-container {
    position: absolute;
    width: 90%;
    max-width: 100%;
    height: 203px;
    top: 20px;
    right: 0;
    left: 0;
    background-color: #fff;
    border-radius: 7px;
    bottom: 0;
    display: grid;
    place-content: center;
    margin: 20px;
  }


  .image-container .error-message {
    position: absolute;
    top: 200px;
    margin: 5px 0;
    text-align: center;
    width: 100%;
    font-size: 12px;
  }


  .img-container-action-btns {
    position: absolute;
    top: 0;
    transform: scale(0.5);
    transition: transform 0.2s ease;
    ;
  }



  .image-container:hover .img-container-action-btns {
    position: absolute;
    width: 100%;
    height: 100%;
    background: #000000a6;
    opacity: 1 !important;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    color: #fff;
    font-size: 30px;
    border-radius: 7px;
    transform: scale(1);
  }

  .image-grid {
    width: 200px;
    height: 200px;
    object-fit: contain;
    border-radius: 8px;
  }

  .image-grid-output {
    max-width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 8px;
  }

  input.spec_input {
    width: 200px;
    height: 200px;
    font-size: 0;
  }


  .file-upload {
    display: grid;
    justify-content: center;
    justify-items: center;
    gap: 20px;
    /* background: #fff; */
    padding: 30px;
    border-radius: 8px;
    width: 200px;
    height: 200px;
    margin-top: 1em;
  }


  .upload-btn-wrapper {
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    border: 1px solid var(--template-color);
    border-radius: 7px;
    width: 160px;
    background: var(--template-color);
  }


  .upload-btn-wrapper .btn {
    border: 2px solid gray;
    color: white;
    background-color: transparent;
    padding: 8px 20px;
    border-radius: 8px;
    font-size: 20px;
    font-weight: 500;
  }

  .upload-btn-wrapper input[type=file] {
    font-size: 100px;
    position: absolute;
    left: -19px;
    top: -133px;
    opacity: 0;
  }

  .upload-btn-wrapper .icon {
    border-right: 1px solid #fff;
    padding-right: 5px;
    vertical-align: middle;
    display: flex;
  }

  .image-container ion-icon {
    cursor: pointer;
  }

  .upload-btn-wrapper .icon ion-icon {
    color: #fff;
    font-size: 20px;
  }

  .image-block-section {
    display: grid;
    justify-items: center;
    grid-template-columns: 3fr 3fr 3fr;
    gap: 30px;
  }


  .image-block {
    width: 100%;
    height: 100%;
    display: inline-block;
    background: #fff;
    box-shadow: rgba(0, 0, 0, 0.16) 0px 3px 6px, rgba(0, 0, 0, 0.23) 0px 3px 6px;
    border-radius: 12px;
    padding: 13px;
    transition-duration: 0.3s;
  }

  .image-block:hover {
    box-shadow: rgba(0, 0, 0, 0.3) 0px 19px 38px, rgba(0, 0, 0, 0.22) 0px 15px 12px;
  }

  .error-message.image-validation {
    position: absolute;
    bottom: 0;
    width: 100%;
    text-align: center;
    font-size: 12px;
    margin: 5px 0;
  }

  .boq-checkbox {
    max-width: 300px;
    margin-left: auto;
  }

  .boq-checkbox ul li {
    list-style: disc;
    margin: 3px 17px;
  }

  .service-sales-card-body {
    height: auto !important;
  }

  .head-title p {
    margin: 15px 0;
    line-height: 25px;
  }

  .head-title p.heading.lead {
    font-size: 14px;
    font-weight: 300;
  }

  .head-title .item-desc {
    line-height: 18px;
    font-size: 11px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
  }

  .item-specification-row .item-img {
    margin-left: 0;
    width: 200px;
    height: 100%;
    position: relative;
    top: 0;
  }

  .item-specification-row .item-img img {
    max-width: 100%;
  }

  .item-specification-row .service-img {
    margin-left: auto;
    width: 100%;
    height: 200px;
    position: relative;
    top: 0;
    padding: 20px;
    text-align: center;
  }

  .item-specification-row .service-img .service-icon {
    max-width: 50%;
  }

  .modal.goods-modal .modal-header {
    height: 375px;
  }

  .image-border-right .form-input {
    border-right: 1px solid #ccc;
    display: grid;
    margin-right: -21px;
    place-content: center;
  }

  .image-border-right .form-input label {
    text-align: center;
  }

  tr.service-list th {
    font-weight: 200 !important;
    border-right: 1px solid #efefef !important;
  }

  tr.service-list td {
    font-weight: 600 !important;
  }

  .graphical-view {
    position: sticky;
    top: 0;
  }


  .upload-icon {
    cursor: pointer;
  }

  .is-goods .card-body.goods-card-body.others-info.vendor-info.so-card-body.classification-card-body {
    height: auto;
  }

  .is-goods .modal-body.goods-service-material .accordion-card-details .display-flex-space-between p:last-child {
    text-align: left;
  }
</style>
<?php
if (isset($_GET['create'])) {
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper is-goods is-goods-create">
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card">
          <div class="modal-header card-header py-2 px-3">
            <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
            <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
          </div>
          <div id="notesModalBody" class="modal-body card-body">
            <p class="font-monospace text-danger notesServiceTargetPrice"></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Content Header (Page header) -->

    <div class="content-header">

      <?php if (isset($msg)) { ?>

        <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">

          <?= $msg ?>

        </div>

      <?php } ?>

      <div class="container-fluid">

        <ol class="breadcrumb">

          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Item List</a></li>

          <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Item</a></li>

          <li class="back-button">

            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">

              <i class="fa fa-reply po-list-icon"></i>

            </a>

          </li>

        </ol>

      </div>
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="goodsSubmitForm" name="goodsSubmitForm" enctype="multipart/form-data">
          <input type="hidden" name="creategoodsdata" id="creategoodsdata" value="">

          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">
                    <div class="card-header">
                      <h4>Classification
                        <span class="text-danger">*</span>
                      </h4>
                    </div>
                    <div class="card-body goods-card-body others-info vendor-info so-card-body classification-card-body service-sales-card-body">
                      <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <div class="row goods-info-form-view customer-info-form-view">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <div class="form-input">
                                <select id="goodTypeDropDown" name="goodsType" class="form-control">
                                  <option value="">Select Item Type</option>
                                </select>
                              </div>
                            </div>
                            <!-- <span id = "asset_cl"> -->
                            <div class="col-lg-6 col-md-6 col-sm-6" id="asset_classification" style="display:none;">
                              <div class="form-input">
                                <select id="asset_classification_select" name="asset_classification[]" class="form-control asset_classificationDropDown" data-classattr="asset_classification_new">
                                  <option value="">Select Asset Classification</option>
                                  <?php
                                  $asset_class = queryGet("SELECT * FROM `erp_depreciation_table` WHERE `company_id`=$company_id ", true);

                                  foreach ($asset_class['data'] as $data) {
                                  ?>
                                    <option value="<?= $data['depreciation_id'] ?>"><?= $data['asset_class'] ?></option>
                                  <?php
                                  }
                                  ?>

                                </select>





                              </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6" id="asset_gl" style="display:none;">
                              <div class="form-input">
                                <label>GL Code </label>



                                <select id="glCodeAsset" name="glCodeAsset" class="form-control">
                                  <option value="">SELECT GL Code</option>

                                </select>
                              </div>
                            </div>


                            <!-- <span id="asset_classification_new" class="asset_classification_new" style="display:none; display: inline-flex;  ">

                            </span> -->
                            <!-- </span> -->

                            <div class="col-lg-6 col-md-6 col-sm-6" id="goodsGroup" style="display:none;">

                              <div class="form-input">
                                <select id="goodGroupDropDown" name="goodsGroup[]" class="form-control goodGroupDropDown" data-classattr="group_parent_new">
                                  <option value="">Select Group</option>
                                </select>
                              </div>



                              <!-- <div class="container">
                                <div class="row mt-0">
                                  <div class="col-lg-12 form-input px-0">
                                    <input type="text" class="form-control" id="justAnotherInputBox" placeholder="Select" autocomplete="off" />
                                  </div>

                                  dont touch
                                  <div class="col-lg-6" style="display: none;">
                                    <h3>Multi Selection With Cascade Option Select</h3>
                                    <input type="text" id="justAnInputBox1" placeholder="Select" autocomplete="off" />
                                  </div>


                                  
                                </div>
                              </div> -->



                            </div>


                            <!-----modal------>
                            <!-- <div class="modal treeviewModal" id="treeModal">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                  
                                      <div class="modal-header">
                                        <h4 class="modal-title">Classification</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                      </div>
                                      <div class="modal-body">
                                       
                                        <ul id="dragRoot">
                                          <li><i class="icon-building"></i> <span class="node-facility">TEST
                                              <button type="button" class="btn btn-primary" id="addTreeSidebarBtn">
                                                <ion-icon name="add-outline"></ion-icon>
                                              </button>

                                              <div class="sidebar" id="goodGroupSidebar">
                                                <form action="">
                                                  <h3 class="text-md font-bold my-3 mx-2">Add Classification</h3>
                                                  <hr>
                                                  <div class="form-input">
                                                    <label for="">Group Name</label>
                                                    <input type="text" class="form-control">
                                                  </div>
                                                  <div class="form-input">
                                                    <label for="">Group Description</label>
                                                    <input type="text" class="form-control">
                                                  </div>
                                                  <div class="form-input">
                                                    <label for="" class="label-hidden">Group Description</label>
                                                    <input type="hidden" class="form-control">
                                                  </div>
                                                  <div class="form-input">
                                                    <label for="" class="label-hidden">Group Description</label>
                                                    <input type="hidden" class="form-control">
                                                  </div>
                                                  <div class="sub-btn form-input">
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                  </div>
                                                </form>
                                              </div>
                                            </span>
                                            <ul>
                                              <li><i class="icon-hdd"></i> <span class="node-cpe">test-1<ion-icon name="add-outline" class="ml-2"></ion-icon></span>
                                                <ul>
                                                  <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-1<ion-icon name="add-outline" class="ml-2"></ion-icon></span>

                                                    <ul>
                                                      <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-1<ion-icon name="add-outline" class="ml-2"></ion-icon></span></li>
                                                      <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-2<ion-icon name="add-outline" class="ml-2"></ion-icon></span></li>
                                                      <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-3<ion-icon name="add-outline" class="ml-2"></ion-icon></span>

                                                        <ul>
                                                          <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-1<ion-icon name="add-outline" class="ml-2"></ion-icon></span></li>
                                                          <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-2<ion-icon name="add-outline" class="ml-2"></ion-icon></span></li>
                                                          <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-3<ion-icon name="add-outline" class="ml-2"></ion-icon></span></li>
                                                        </ul>
                                                      </li>
                                                    </ul>
                                                  </li>
                                                  <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-2<ion-icon name="add-outline" class="ml-2"></ion-icon></span></li>
                                                  <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-3<ion-icon name="add-outline" class="ml-2"></ion-icon></span></li>
                                                </ul>
                                              </li>
                                            </ul>
                                          </li>
                                        </ul>
                                      
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                      </div>
                                    </div>
                                  </div>
                                </div> -->




                            <div class="row group_parent_new" style="display:none; display: flex;">

                            </div>

                            <!-- <span class="group_parent_new" style="display:none; display: inline-flex;  ">

                            </span> -->


                            <div class="col-lg-6 col-md-6 col-sm-6" id="purchaseGroup" style="display:none;">
                              <div class="form-input">
                                <select id="purchaseGroupDropDown" name="purchaseGroup" class="form-control">
                                  <option value="">Select Purchase Group</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6" id="availability" style="display:none;">
                              <div class="form-input">
                                <select id="avl_check" name="availabilityCheck" class="form-control">
                                  <option value="">Availability Check</option>
                                  <option value="Daily">Daily</option>
                                  <option value="Weekly">Weekly</option>
                                  <option value="By Weekly">By Weekly</option>
                                  <option value="Monthly">Monthly</option>
                                  <option value="Qtr">Qtr</option>
                                  <option value="Half Y">Half Y</option>
                                  <option value="Year">Year</option>
                                </select>
                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6" id="discount_group" style="display:none;">
                              <div class="form-input">
                                <label for="" class="label-hidden">Discount</label>
                                <div class="dropdown check-dropdown" data-control="checkbox-dropdown">
                                  <label class="dropdown-label">Select Item Discount Group</label>

                                  <div class="dropdown-list">
                                    <a href="#" data-toggle="check-all" class="dropdown-option">
                                      Check All
                                    </a>

                                    <?php
                                    $discountsql = queryGet("SELECT * FROM `erp_item_discount_group` WHERE company_id = $company_id", true);
                                    // console($discountsql);
                                    foreach ($discountsql['data'] as $discount) {
                                    ?>


                                      <label class="dropdown-option">
                                        <input type="checkbox" name="discount_group[]" value="<?= $discount['item_discount_group_id'] ?>" />
                                        <?= $discount['item_discount_group'] ?>
                                      </label>
                                    <?php
                                    }
                                    ?>
                                  </div>
                                </div>
                              </div>
                            </div>





                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-inline float-right" id="bomCheckBoxDiv">


                              </div>
                              <div class="form-inline float-right" id="bomRadioDiv">



                              </div>

                              <div class="boq-checkbox" id="boqCheckBoxDiv" style="display:none;">

                                <div class="d-flex mb-3">

                                  <input type="checkbox" id="boqcheckbox" name="boqRequired" style="width: auto; margin-bottom: 0;">

                                  <label class="mb-0 ml-2">Mark as project</label>

                                </div>

                                <p class="text-xs"><span class="text-danger pr-2">*</span><b>What is the significant</b></p>
                                <ul>
                                  <li>
                                    <p class="text-xs">This code will be used for any project </p>
                                  </li>
                                  <li>
                                    <p class="text-xs">which will have the BOQ (Bill of Quantity).</p>
                                  </li>
                                  <li>
                                    <p class="text-xs">To activate or complete the code, the BOQ need to be created.</p>
                                  </li>

                                </ul>

                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>
              <div class="row" id="storageDetails" style="display: none;">

                <div class="col-lg-12 col-md-12 col-sm-12">

                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                    <div class="card-header">

                      <h4>Storage Details</h4>


                    </div>

                    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row goods-info-form-view customer-info-form-view">

                            <div class="col-lg-4 col-md-4 col-sm-4">

                              <div class="form-input">

                                <label for="">Storage Control</label>

                                <input type="text" name="storageControl" class="form-control">

                              </div>

                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4">

                              <div class="form-input">

                                <label for="">Max Storage Period</label>

                                <input type="text" name="maxStoragePeriod" class="form-control">

                              </div>

                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4">
                              <div class="form-input">
                                <label class="label-hidden" for="">Min Time Unit</label>
                                <select id="minTime" name="minTime" class="select2 form-control">
                                  <option value="">Min Time Unit</option>
                                  <option value="Day">Day</option>
                                  <option value="Month">Month</option>
                                  <option value="Hours">Hours</option>

                                </select>
                              </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4">
                              <div class="form-input">
                                <label for="">Default Storage Location</label>
                                <select id="default_storage" name="default_storage" class="select2 form-control">


                                </select>
                              </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4">

                              <div class="form-input">

                                <label for="">Minimum Remain Self life</label>

                                <input type="text" name="minRemainSelfLife" class="form-control">

                              </div>

                            </div>
                            <!-- //class="label-hidden" -->
                            <div class="col-lg-4 col-md-4 col-sm-4">
                              <div class="form-input">
                                <label for="">Max Time Unit</label>
                                <select id="maxTime" name="maxTime" class="select2 form-control">
                                  <option value="">Max Time Unit</option>
                                  <option value="Day">Day</option>
                                  <option value="Month">Month</option>
                                  <option value="Hours">Hours</option>

                                </select>
                              </div>
                            </div>

                          </div>

                          <div class="col-lg-12 col-md-12 col-sm-12 px-0">

                            <div class="row goods-info-form-view customer-info-form-view">

                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label for=""> Minimum Stock</label>

                                  <input step="0.01" type="number" name="min_stock" id="stock" class="form-control stock" id="exampleInputBorderWidth2">

                                </div>

                              </div>

                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label for=""> Maximum Stock </label>

                                  <input step="0.01" type="number" name="max_stock" id="stock" class="form-control stock" id="exampleInputBorderWidth2">

                                </div>

                              </div>
                              <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="form-inline">
                                  <input type="checkbox" name="qaEnable" id="qaEnable" style="width: auto; margin-bottom: 0;" value="1"><label class="mb-0">Quality Enable</label>

                                </div>
                              </div>
                              <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="form-input" id="qa_storage">
                                  <!-- <label for="">QA Storage Location</label>
                                <select id="qa_storage" name="qa_storage" class="select2 form-control">


                                </select> -->
                                </div>
                              </div>

                            </div>
                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>




              <div class="row" id="serviceStock" style="display:none;">

                <div class="col-lg-12 col-md-12 col-sm-12">

                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                    <div class="card-header">

                      <h4>Service Stock</h4>

                    </div>

                    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row goods-info-form-view customer-info-form-view">

                            <div class="col-lg-3 col-md-3 col-sm-3">

                              <div class="form-input">

                                <label for=""> Service Quantity</label>

                                <input step="0.01" type="number" name="service_stock" id="service_stock" class="form-control stock" id="exampleInputBorderWidth2" value="0">

                              </div>

                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-3">

                              <div class="form-input">

                                <label for="">Service Unit Price</label><label id="buom_per"> </label>

                                <!-- <input step="0.01" type="number" name="service_rate" id="service_rate" class="form-control rate" id="exampleInputBorderWidth2" value="0"> -->



                              </div>

                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-3">

                              <div class="form-input">

                                <label for="">Value</label>

                                <input step="0.01" type="number" name="service_total" id="service_total" class="form-control total" id="exampleInputBorderWidth2" value="0" readonly>

                              </div>

                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-3">

                              <div class="form-input">

                                <label for=""> Dated on </label>

                                <input type="date" name="service_stock_date" id="service_stock_date" class="form-control stock" id="exampleInputBorderWidth2">

                              </div>

                            </div>

                          </div>

                        </div>




                      </div>

                    </div>

                  </div>

                </div>

              </div>


            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">

              <div class="row" id="basicDetails" style="display: none;">

                <div class="col-lg-12 col-md-12 col-sm-12">

                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                    <div class="card-header">

                      <h4>Basic Details

                        <span class="text-danger">*</span>

                      </h4>

                    </div>

                    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row goods-info-form-view basic-info-form-view">

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label>Item Name</label>

                                <input type="text" name="itemName" class="form-control item_name" id="exampleInputBorderWidth2">
                                <p id="duplicate-name-error" class="error"></p>
                                <ul class="suggestion-item" id="suggestedNames">

                                </ul>

                              </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label>Additional Description</label>

                                <textarea class="item_desc" rows="3" name="itemDesc" id="exampleInputBorderWidth2" placeholder="Additional Description"></textarea>

                              </div>

                            </div>


                            <div class="row">

                              <div class="col-12">

                                <div class="row dash-border-row">

                                  <div class="col-lg-6 col-md-6 col-sm-6 col-12 unit-measure-col">

                                    <div class="row mb-4">

                                      <div class="col-lg-6 col-md-6 col-sm-6 col pr-0 base-measure">

                                        <div class="form-input">

                                          <label>Base UOM</label>

                                          <select id="buomDrop" name="baseUnitMeasure" class="form-control">

                                            <option value="">Base Unit of Measurement</option>

                                            <?php


                                            $uomList_sql = getUomList('material');
                                            $uomList = $uomList_sql['data'];





                                            foreach ($uomList as $oneUomList) {

                                            ?>

                                              <option value="<?= $oneUomList['uomId'] ?>"><?= $oneUomList['uomName'] . '  ||  ' .  $oneUomList['uomDesc']  ?> </option>

                                            <?php

                                            }

                                            ?>

                                          </select>

                                        </div>

                                      </div>

                                      <div class="col-lg-6 col-md-6 col-sm-6 col">

                                        <div class="form-input">

                                          <label>Alternate UOM</label>

                                          <select id="iuomDrop" name="issueUnit" class="form-control">

                                            <option value="">Alternate Unit of Measurement</option>

                                            <?php

                                            $uomList_sql = getUomList('material');
                                            $uomList = $uomList_sql['data'];



                                            foreach ($uomList as $oneUomList) {

                                            ?>

                                              <option value="<?= $oneUomList['uomId'] ?>"><?= $oneUomList['uomName'] . '  ||  ' .  $oneUomList['uomDesc'] ?></option>

                                            <?php

                                            }

                                            ?>

                                          </select>

                                        </div>

                                      </div>

                                    </div>

                                    <div class="row calculate-row">

                                      <div class="col-lg-1 col-md-1 col-sm-1 col p-0">

                                        <input type="text" class="form-control bg-none p-0" placeholder="1" readonly>

                                      </div>

                                      <div class="col-lg-3 col-md-3 col-sm-3 col">

                                        <!-- <input type="text" name="netWeight" class="form-control bg-none p-0" id="buom" placeholder="unit" readonly> -->
                                        <input type="text" name="netWeight" class="form-control bg-none p-0" id="buom" value="unit" readonly>

                                      </div>

                                      <div class="col-lg-1 col-md-1 col-sm-1 col">

                                        <p class="equal-style mt-1">=</p>

                                      </div>

                                      <div class="col-lg-3 col-md-3 col-sm-3 col">

                                        <input type="text" name="rel" class="form-control item_rel" id="rel">

                                      </div>

                                      <div class="col-lg-3 col-md-3 col-sm-3 col">

                                        <!-- <input type="text" name="netWeight" class="form-control bg-none p-0" placeholder="unit" id="ioum" readonly> -->
                                        <input type="text" name="netWeight" id="ioum" class="form-control bg-none p-0" value="unit" readonly>

                                      </div>

                                    </div>

                                  </div>

                                  <div class="col-lg-6 col-md-6 col-sm-6 col-12 hsn-modal-col">

                                    <div class="row calculate-parent-row mb-4">

                                      <div class="col-lg-12 col-md-12 col-sm-12 col hsn-column pr-3">

                                        <div class="form-input">

                                          <label>HSN </label>

                                          <!-- data-target="#goodsHSNModal" data-toggle="modal" -->
                                          <button class="btn btn-primary btn-transparent goodsHSNModalCls" type="button"></button>


                                          <select id="hsnDropDown" name="" class="form-control">

                                            <option id="hsnlabelOne" value="">HSN</option>

                                          </select>

                                        </div>

                                      </div>

                                    </div>




                                    <div class="row calculate-hsn-row mt-3 mb-2">

                                      <div class="col-lg-12 col-md-12 col-sm-12">

                                        <div class="form-input">

                                          <!-- <label class="label-hidden">HSN</label> -->

                                          <p class="hsn-description-info" id="hsnDescInfo"></p>

                                        </div>

                                      </div>

                                    </div>

                                  </div>


                                  <div class="col-lg-12 col-md-12 col-sm-12 px-0 pt-3">

                                    <div class="form-input" style="display:none;" id="mwp">

                                      <label for="">Moving Weighted Price</label><label id="buom_per"> </label>

                                      <input step="0.01" type="number" name="rate" id="rate" class="form-control rate" id="exampleInputBorderWidth2" value="0">

                                    </div>

                                  </div>

                                </div>

                              </div>

                            </div>


                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <div class="cost-center" id="cost-center" style="display:none;">
                                <label for="">Cost Center</label>
                                <select id="cost_center" name="costCenter" class="form-control">
                                  <option value="">Cost Center</option>
                                  <?php
                                  $funcList = $BranchPoObj->fetchFunctionality()['data'];
                                  foreach ($funcList as $func) {
                                  ?>
                                    <option value="<?= $func['CostCenter_id'] ?>">
                                      <?= $func['CostCenter_code'] . '[' . $func['CostCenter_desc'] . ']' ?></option>
                                  <?php } ?>
                                </select>
                              </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <div class="cost-center" id="depKey" style="display:none;">
                                <label for="">Asset Depreciation Key <span id="despkey_id"></span></label>
                                <input type="hidden" id="dep_key_val" name="dep_key">

                                </select>
                              </div>
                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>


              </div>

              <div class="row" id="pricing" style="display:none;">

                <div class="col-lg-12 col-md-12 col-sm-12">

                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                    <div class="card-header">

                      <h4>Pricing and Discount

                        <span class="text-danger">*</span>

                      </h4>

                    </div>

                    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row goods-info-form-view customer-info-form-view">

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Default MRP</label>

                                <input step="0.01" type="number" name="price" value="0" class="form-control price" id="exampleInputBorderWidth2" placeholder="price">

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Default Discount (%)</label>

                                <input step="0.01" type="number" name="discount" value="0" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">

                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>

              <div class="row" id="service_sales_details" style="display: none;">

                <div class="col-lg-12 col-md-12 col-sm-12">

                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                    <div class="card-header">

                      <h4>Service Details

                        <span class="text-danger">*</span>

                      </h4>

                    </div>

                    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row goods-info-form-view customer-info-form-view">

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label>Service Name</label>

                                <input type="text" name="serviceName" class="form-control item_name service_name" id="exampleInputBorderWidth2">

                              </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label>Service Description</label>

                                <textarea class="item_desc service_desc" rows="3" name="serviceDesc" id="exampleInputBorderWidth2" placeholder="Service Description"></textarea>

                              </div>

                            </div>



                            <div class="col-lg-6 col-md-6 col-sm-6 col-12 hsn-modal-col">

                              <div class="row calculate-parent-row mb-4">

                                <div class="col-lg-12 col-md-12 col-sm-12 col hsn-column pr-3">

                                  <div class="form-input">

                                    <label>HSN </label>

                                    <!-- data-target="#goodsHSNModal" data-toggle="modal" -->
                                    <button class="btn btn-primary btn-transparent goodsHSNModalCls" type="button"></button>


                                    <select id="hsnDropDown2" name="" class="form-control">

                                      <option id="hsnlabelOne2" value="">HSN</option>

                                    </select>

                                  </div>

                                </div>

                              </div>




                              <div class="row calculate-hsn-row mt-3 mb-2">

                                <div class="col-lg-12 col-md-12 col-sm-12">

                                  <div class="form-input">

                                    <!-- <label class="label-hidden">HSN</label> -->

                                    <p class="hsn-description-info" id="hsnDescInfo2"></p>

                                  </div>

                                </div>

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6" id="tds" style="display:none;">

                              <div class="form-input">

                                <label>TDS </label>

                                <button class="btn btn-primary btn-transparent" type="button" data-toggle="modal" data-target="#tdsmodal"></button>

                                <select id="tdsDropDown" name="tds" class="form-control">
                                  <option id="tdslabel" value="">SELECT TDS</option>
                                  <!-- <?php
                                        $tds_sql = queryGet("SELECT * FROM `erp_tds_details`", true);
                                        $tds_data = $tds_sql['data'];
                                        foreach ($tds_data as $tds) {
                                        ?>
                                    <option id="" value="<?= $tds['id'] ?>"><?= $tds['section'] . "[nature-" . $tds['natureOfTransaction'] . ", threshold-" . $tds['thresholdLimit'] . "]" ?></option>
                                  <?php
                                        }
                                  ?> -->
                                </select>
                              </div>
                            </div>



                            <div class="col-lg-6 col-md-6 col-sm-6" id="servicegl">
                              <div class="form-input">
                                <label>GL Code </label>
                                <select id="glCode" name="glCode" class="form-control">
                                  <option value="">SELECT GL Code</option>
                                </select>
                              </div>
                            </div>
                            <?php
                            $uomList = getUomList('service');
                            ?>
                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label>Service Unit</label>

                                <!-- <input type="text" name="serviceUnit" class="form-control service_unit" id="exampleInputBorderWidth2"> -->

                                <select id="serviceUnitDrop" name="serviceUnit" class="form-control serviceUnitDrop">

                                  <option>Service Unit of Measurement</option>

                                  <?php





                                  foreach ($uomList['data'] as $oneUomList) {

                                  ?>

                                    <option value="<?= $oneUomList['uomId'] ?>"><?= $oneUomList['uomName'] . '||' .  $oneUomList['uomDesc'] ?> </option>

                                  <?php

                                  }

                                  ?>

                                </select>

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6" id="service_target" style="display:none;">

                              <div class="form-input">

                                <label>Service Target Price</label>

                                <input type="text" name="service_target_price" class="form-control service_target_price" id="service_target_price">
                                <span class="error gds_service_target_price"></span>

                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>


            </div>

            <div class="col-lg-12 col-md-12 col-sm-12">

              <div class="card goods-creation-card so-creation-card po-creation-card h-auto" id="goodsImage" style="height: auto; display: none;">

                <div class="card-header">

                  <h4>Image and Document

                    <!-- <span class="text-danger">*</span> -->

                  </h4>

                </div>

                <div class="card-body">
                  <div class="row othe-cost-infor justify-content-center">
                    <div class="row othe-cost-infor image-border-right pl-0 pr-0 ">

                      <div class="col-lg-2 col-md-2 col-sm-2">

                        <div class="form-input mb-3 mt-2">

                          <label class="my-2">Image</label>

                          <div class="file-upload">
                            <ion-icon name="cloud-upload-outline" class="po-list-icon upload-icon md hydrated" role="img" aria-label="cloud upload outline"></ion-icon>

                            <div class="upload-btn-wrapper">
                              <div class="icon">
                                <ion-icon name="attach-outline" role="img" class="md hydrated" aria-label="attach outline"></ion-icon>
                              </div>
                              <button class="btn waves-effect waves-light">Upload a file</button>
                              <input type="file" name="pic[]" accept="image/*" data-attr="1" class="form-control spec_vldtn spec_input file_1" id="file-input-1">
                            </div>

                          </div>

                          <div class="image-container" style="display: none;">
                            <div class="img-container-action-btns" style="opacity: 0;">
                              <ion-icon class="close-img" name="close-circle-outline"></ion-icon>
                            </div>
                            <img class="image-grid" src="">
                          </div>

                          <div class="error-message image-validation" style="color: red;"></div>

                        </div>

                      </div>

                      <div class="col-lg-2 col-md-2 col-sm-2">

                        <div class="form-input mb-3 mt-2">

                          <label class="my-2">Image</label>


                          <div class="file-upload">
                            <ion-icon name="cloud-upload-outline" class="po-list-icon upload-icon md hydrated" role="img" aria-label="cloud upload outline"></ion-icon>

                            <div class="upload-btn-wrapper">
                              <div class="icon">
                                <ion-icon name="attach-outline" role="img" class="md hydrated" aria-label="attach outline"></ion-icon>
                              </div>
                              <button type="button" class="btn waves-effect waves-light">Upload a file</button>
                              <input type="file" name="pic[]" accept="image/*" data-attr="2" class="form-control spec_vldtn spec_input file_1" id="file-input-2">
                            </div>
                          </div>

                          <div class="image-container" style="display: none;">

                            <div class="img-container-action-btns" style="opacity: 0;">



                              <ion-icon class="close-img" name="close-circle-outline"></ion-icon>

                            </div>


                            <img class="image-grid" src="">

                          </div>

                          <div class="error-message image-validation" style="color: red;"></div>



                        </div>

                      </div>

                      <div class="col-lg-2 col-md-2 col-sm-2">

                        <div class="form-input mb-3 mt-2">

                          <label class="my-2">Image</label>


                          <div class="file-upload">
                            <ion-icon name="cloud-upload-outline" class="po-list-icon upload-icon md hydrated" role="img" aria-label="cloud upload outline"></ion-icon>

                            <div class="upload-btn-wrapper">
                              <div class="icon">
                                <ion-icon name="attach-outline" role="img" class="md hydrated" aria-label="attach outline"></ion-icon>
                              </div>
                              <button class="btn waves-effect waves-light">Upload a file</button>
                              <input type="file" name="pic[]" accept="image/*" data-attr="1" class="form-control spec_vldtn spec_input file_1" id="file-input-1">
                            </div>
                          </div>

                          <div class="image-container" style="display: none;">

                            <div class="img-container-action-btns" style="opacity: 0;">



                              <ion-icon class="close-img" name="close-circle-outline"></ion-icon>

                            </div>


                            <img class="image-grid" src="">

                          </div>

                          <div class="error-message image-validation" style="color: red;"></div>


                        </div>


                      </div>

                      <div class="col-lg-2 col-md-2 col-sm-2">

                        <div class="form-input mb-3 mt-2">

                          <label class="my-2">Image</label>


                          <div class="file-upload">
                            <ion-icon name="cloud-upload-outline" class="po-list-icon upload-icon md hydrated" role="img" aria-label="cloud upload outline"></ion-icon>

                            <div class="upload-btn-wrapper">
                              <div class="icon">
                                <ion-icon name="attach-outline" role="img" class="md hydrated" aria-label="attach outline"></ion-icon>
                              </div>
                              <button class="btn waves-effect waves-light">Upload a file</button>
                              <input type="file" name="pic[]" accept="image/*" data-attr="1" class="form-control spec_vldtn spec_input file_1" id="file-input-1">
                            </div>
                          </div>

                          <div class="image-container" style="display: none;">

                            <div class="img-container-action-btns" style="opacity: 0;">



                              <ion-icon class="close-img" name="close-circle-outline"></ion-icon>

                            </div>


                            <img class="image-grid" src="">

                          </div>

                          <div class="error-message image-validation" style="color: red;"></div>


                        </div>

                      </div>

                      <div class="col-lg-2 col-md-2 col-sm-2">

                        <div class="form-input mb-3 mt-2">

                          <label class="my-2">Image</label>


                          <div class="file-upload">
                            <ion-icon name="cloud-upload-outline" class="po-list-icon upload-icon md hydrated" role="img" aria-label="cloud upload outline"></ion-icon>

                            <div class="upload-btn-wrapper">
                              <div class="icon">
                                <ion-icon name="attach-outline" role="img" class="md hydrated" aria-label="attach outline"></ion-icon>
                              </div>
                              <button class="btn waves-effect waves-light">Upload a file</button>
                              <input type="file" name="pic[]" accept="image/*" data-attr="1" class="form-control spec_vldtn spec_input file_1" id="file-input-1">
                            </div>
                          </div>

                          <div class="image-container" style="display: none;">

                            <div class="img-container-action-btns" style="opacity: 0;">



                              <ion-icon class="close-img" name="close-circle-outline"></ion-icon>

                            </div>


                            <img class="image-grid" src="">

                          </div>

                          <div class="error-message image-validation" style="color: red;"></div>


                        </div>

                      </div>

                      <div class="col-lg-2 col-md-2 col-sm-2">

                        <div class="form-input mb-3 mt-2">

                          <label class="my-2">Image</label>


                          <div class="file-upload">
                            <ion-icon name="cloud-upload-outline" class="po-list-icon upload-icon md hydrated" role="img" aria-label="cloud upload outline"></ion-icon>

                            <div class="upload-btn-wrapper">
                              <div class="icon">
                                <ion-icon name="attach-outline" role="img" class="md hydrated" aria-label="attach outline"></ion-icon>
                              </div>
                              <button class="btn waves-effect waves-light">Upload a file</button>
                              <input type="file" name="pic[]" accept="image/*" data-attr="1" class="form-control spec_vldtn spec_input file_1" id="file-input-1">
                            </div>
                          </div>

                          <div class="image-container" style="display: none;">

                            <div class="img-container-action-btns" style="opacity: 0;">



                              <ion-icon class="close-img" name="close-circle-outline"></ion-icon>

                            </div>


                            <img class="image-grid" src="">

                          </div>

                          <div class="error-message image-validation" style="color: red;"></div>

                        </div>

                      </div>

                    </div>

                    <div class="row doc-upload-file">

                      <div class="col-lg-6 col-md-6 col-sm-6">

                        <div class="form-input">
                          <label>Document</label>
                          <input type="file" name="doc[]" data-attr="1" class="form-control " id="file-input-1">
                        </div>
                      </div>

                      <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="form-input">
                          <label>Document</label>
                          <input type="file" name="doc[]" data-attr="1" class="form-control " id="file-input-1">
                        </div>
                      </div>

                    </div>
                  </div>
                </div>


                <script>
                  var uploadIcons = document.querySelectorAll(".upload-icon");

                  uploadIcons.forEach(function(uploadIcon) {
                    uploadIcon.addEventListener("click", function() {
                      var fileInput = uploadIcon.nextElementSibling.querySelector(".file_1");

                      fileInput.click();
                    });
                  });
                </script>

              </div>
            </div>


            <div class="col-lg-12 col-md-12 col-sm-12">

              <div class="card goods-creation-card so-creation-card po-creation-card" id="specificationDetails" style="height: auto; display: none;">

                <div class="card-header">

                  <h4>Specification Details

                    <!-- <span class="text-danger">*</span> -->

                  </h4>

                </div>

                <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                  <div class="row">

                    <div class="col-lg-12 col-md-12 col-sm-12">

                      <div class="row goods-info-form-view customer-info-form-view">


                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">

                            <label for="">Net Weight</label>

                            <input step="0.01" type="number" name="netWeight" class="form-control net_weight" id="net_weight">

                          </div>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">
                            <label class="" for="">Net Weight Unit</label>
                            <select name="net_unit" class="form-control " id="net_unit">
                              <option value="">Select net weight unit</option>
                              <option value="kg">kg</option>
                              <option value="g">g</option>
                              <option value="ton">ton</option>

                            </select>
                          </div>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">

                            <label for="">Gross Weight</label>

                            <input step="0.01" type="number" name="grossWeight" class="form-control gross_weight" id="gross_weight">
                            <span id="gross_span"></span>

                          </div>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">
                            <label class="" for="">Gross Weight Unit</label>
                            <select name="gross_unit" class="form-control " id="gross_unit" disabled="">
                              <option value="">Select gross weight unit</option>
                              <option value="kg">kg</option>
                              <option value="g">g</option>
                              <option value="ton">ton</option>

                            </select>
                          </div>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">

                            <label for="">Height</label>

                            <input step="0.01" type="number" name="height" class="form-control calculate_volume" id="height">

                          </div>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">

                            <label for="">Width</label>

                            <input step="0.01" type="number" name="width" class="form-control calculate_volume" id="width">

                          </div>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">

                            <label for="">Length</label>

                            <input step="0.01" type="number" name="length" class="form-control calculate_volume" id="length">

                          </div>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">
                            <label class="" for="">Unit</label>
                            <select name="measure_unit" class="form-control volume_unit" id="volume_unit">

                              <option value="cm">cm</option>
                              <option value="m">m</option>

                            </select>
                          </div>

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">

                          <div class="form-input">

                            <label>Volume In CM<sup>3</sup></label>

                            <input type="text" name="volumeCubeCm" class="form-control" id="volcm" readonly="">

                          </div>

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">

                          <div class="form-input">

                            <label>Volume In M<sup>3</sup></label>

                            <input type="text" name="volume" class="form-control" id="volm" readonly="">

                          </div>

                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <label for="" class="font-bold mt-3">Technical Specification</label>

                          <hr class="my-2">

                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row othe-cost-infor modal-add-row_537 px-0">

                            <div class="row othe-cost-infor">


                              <div class="col-lg-3 col-md-3 col-sm-3">

                                <div class="form-input py-0">

                                  <label>Specification</label>

                                  <input type="text" name="spec[1][spec_name]" data-attr="1" class="form-control specification_1" id="" placeholder="Name">

                                </div>

                              </div>
                              <div class="col-lg-8 col-md-8 col-sm-8">

                                <div class="form-input py-0">

                                  <label>Specification Details</label>

                                  <input type="text" name="spec[1][spec_detail]" data-attr="1" class="form-control spec_dtls_vldtn specificationDetails_1" id="" placeholder="Description">

                                </div>

                              </div>

                              <div class="col-lg-1 col-md-1 col-sm-1">

                                <div class="add-btn-plus justify-content-end">

                                  <a style="cursor: pointer" class="btn btn-primary" onclick="addMultiQtyf(537)">

                                    <i class="fa fa-plus"></i>

                                  </a>

                                </div>

                              </div>

                            </div>


                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>

            </div>




            <!-----hsn modal start------->


            <div class="modal fade hsn-dropdown-modal goodsHSNModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
              <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4>Choose HSN</h4>
                    <!-- <b id="hsnLoding"></b> -->
                    <!-- <input class="form-control" id="searchbar" type="text" name="search" placeholder="Search.."> -->

                    <div class="section serach-input-section">
                      <input type="text" class="dataTables_filter" id="searchbar" placeholder="" class="field serachfilter-hsn form-control">
                      <button type="reset">&times;</button>
                      <!-- <div class="icons-container">
                        <div class="icon-close">
                          <i class="fa fa-spinner fa-spin hsnSearchSpinner"></i>
                          <i style="cursor: pointer" type="reset" class="fa fa-times hsnSearchclear"></i>
                        </div>
                        <div class="icon-search">
                          <i class="fa fa-search" id="myBtn"></i>
                          <script>
                            var input = document.getElementById("myInput");
                            input.addEventListener("keypress", function(event) {
                              if (event.key === "Enter") {
                                event.preventDefault();
                                document.getElementById("myBtn").click();
                              }
                            });
                            $(".hsnSearchclear").click(function() {
                              $("#searchbar").val('');
                            });
                          </script>
                        </div>
                      </div> -->
                    </div>

                  </div>
                  <div class="modal-body">

                    <div class="card">

                      <div class="card-body m-0 p-0 hsn-code">

                        <div class="hsn-list" style="height: 500px; overflow-y: scroll;" id="myPopup">

                          <table class="table table-hover hsn-modal-table" id="myPopupTable">
                            <thead>
                              <th></th>
                              <th>Code</th>
                              <th>Description</th>
                              <th>Rate</th>
                            </thead>
                            <tbody class="hsn_tbody">

                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="hsnsavebtn" data-dismiss="modal">Select</button>
                  </div>
                </div>
              </div>
            </div>


            <!-----hsn modal end------->



            <script>
              // Get all file input elements with the class "form-control spec_input"
              const fileInputs = document.querySelectorAll('.form-control.spec_input');

              // Add an event listener to each file input for validation
              fileInputs.forEach(function(fileInput) {
                fileInput.addEventListener('change', function() {
                  const selectedFile = this.files[0];
                  if (selectedFile) {
                    const fileType = selectedFile.type;
                    if (!fileType.startsWith('image/')) {
                      // Display an error message specific to this input box
                      const errorMessage = this.closest('.form-input').querySelector('.error-message');
                      errorMessage.textContent = 'Please select an image file.';
                      this.value = ''; // Clear the file input
                      return;
                    }
                    // Clear any previous error message for this input box
                    this.closest('.form-input').querySelector('.error-message').textContent = '';
                  }
                });
              });
            </script>






            <script>
              var popup = $('#myPopup');
              var popupTable = $('#myPopupTable');
              popup.scroll(function() {
                console.log(popup.scrollTop());
                console.log(popupTable[0].scrollHeight * 0.9);

                if (popup.scrollTop() >= popupTable[0].scrollHeight * 0.9) {
                  // Load AJAX content
                  // $.ajax({
                  //   url: 'ajax/content.html',
                  //   success: function(data) {
                  //     // Insert the content into the popup container


                  //     popup.append(data);
                  //   }
                  // });
                  console.log('trdx');
                } else {
                  console.log('trdx22');


                }
              });

              // $("#myPopup").scroll(function() {
              //   console.log($("#myPopup").scrollTop());
              //   console.log($("#myPopup").height());

              //   console.log($("#myPopup").scrollTop() + $("#myPopup").height());
              //   console.log($("#myPopup").height() * 0.8);
              //   if ($("#myPopup").scrollTop() + $("#myPopup").height() <= $("#myPopup").height() * 0.8) {
              //     // Load Ajax content l
              //     console.log('cgfc');
              //   } else {
              //     console.log('test');
              //   }
              // });
            </script>


            <!-----tds modal start------->


            <div class="modal fade hsn-dropdown-modal" id="tdsmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
              <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                <div class="modal-content">
                  <div class="modal-body" style="height: 500px; overflow: auto;">
                    <h4 class="text-sm pl-2">Choose TDS</h4>

                    <div class="section serach-input-section">
                      <input type="text" class="dataTables_filter" id="searchbar_tds" placeholder="" class="field serachfilter-hsn form-control">
                      <button type="reset">&times;</button>
                      <!-- <div class="icons-container">
                        <div class="icon-close">
                          <i class="fa fa-spinner fa-spin hsnSearchSpinner"></i>
                          <i style="cursor: pointer" type="reset" class="fa fa-times hsnSearchclear"></i>
                        </div>
                        <div class="icon-search">
                          <i class="fa fa-search" id="myBtn"></i>
                          <script>
                            var input = document.getElementById("myInput");
                            input.addEventListener("keypress", function(event) {
                              if (event.key === "Enter") {
                                event.preventDefault();
                                document.getElementById("myBtn").click();
                              }
                            });
                            $(".hsnSearchclear").click(function() {
                              $("#searchbar").val('');
                            });
                          </script>
                        </div>
                      </div> -->
                    </div>


                    <div class="card">
                      <div class="card-body m-2 p-0 hsn-code">
                        <table class="table defaultDataTable table-hover tds-modal-table hsn-modal-table ">
                          <thead>
                            <th></th>
                            <th>Section</th>
                            <th>Nature</th>
                            <th>Threshold</th>
                            <th>Rate</th>
                          </thead>
                          <tbody id="data-table">
                            <?php
                            $tds_sql = queryGet("SELECT * FROM `erp_tds_details`", true);
                            $tds_data = $tds_sql['data'];
                            foreach ($tds_data as $tds) {
                              // console($hsn); 
                            ?>
                              <tr>
                                <td> <input type="radio" id="tds" name="tds" data-attr="<?= $tds['section']  ?>" value="<?= $tds['id']  ?>"></td>
                                <td>
                                  <p id="section_<?= $tds['id'] ?>"><?= $tds['section'] ?></p>
                                </td>
                                <td>
                                  <p id="nature_<?= $tds['id'] ?>"><?= $tds['natureOfTransaction'] ?></p>
                                </td>
                                <td>
                                  <p id="threshold<?= $tds['id'] ?>"><?= $tds['thresholdLimit'] ?></p>
                                </td>
                                <td>
                                  <p id="rate<?= $tds['id'] ?>"><?= $tds['TDSRate'] ?>%</p>
                                </td>
                              </tr>
                            <?php
                            }
                            ?>
                          </tbody>
                        </table>
                        <!-- <div class="hsn-header">
                            <div class="hsn-title">
                              <input type="radio" id="hsn" name="hsn" value="<?= $hsn['hsnCode']  ?>">
                              <h5 id="hsnCode_<?= $hsn['hsnId'] ?>"><?= $hsn['hsnCode'] ?></h5>
                            </div>
                            <div class="tax-per">
                              <p id="taxPercentage_<?= $hsn['hsnId'] ?>"><?= $hsn['taxPercentage'] ?>%</p>
                            </div>
                          </div>
                          <div class="hsn-description">
                            <p id="hsnDescription_<?= $hsn['hsnId'] ?>"><?= $hsn['hsnDescription'] ?></p>
                          </div>-->


                      </div>






                      <div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="tdssavebtn" data-dismiss="modal">Save changes</button>
                  </div>
                </div>
              </div>
            </div>


            <!-----tds modal end------->


            <div class="btn-section mt-2 mb-2">

              <button class="btn btn-primary save-close-btn btn-xs float-right add_data" id="submit_btn" value="add_post" style="display:none;">Submit</button>

              <!-- <button class="btn btn-danger save-close-btn btn-xs float-right add_data" id="draft_btn" value="add_draft" style="display:none;">Save as Draft</button> -->

            </div>
          </div>



        </form>



        <!-- modal -->

        <div class="modal" id="addNewGoodTypesFormModal">

          <div class="modal-dialog">

            <div class="modal-content">

              <div class="modal-header py-1" style="background-color: #003060; color:white;">

                <h4 class="modal-title text-white">Add New Item Type</h4>

                <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->

              </div>

              <div class="modal-body">

                <form action="" method="post" id="addNewGoodTypesForm">

                  <div class="col-md-12 mb-3">

                    <div class="input-group">

                      <input type="text" name="goodTypeName" class="form-control">

                      <label>Type Name</label>

                    </div>

                  </div>

                  <div class="col-md-12">

                    <div class="input-group">

                      <input type="text" name="goodTypeDesc" class="form-control">

                      <label>Type Description</label>

                    </div>

                  </div>

                  <div class="col-md-12 flex-radio" style="display: flex; align-items: center; gap: 5px;">

                    <input type="radio" name="type" value="RM" style="margin-bottom: 0; width: auto; padding-right: 5px;">Raw Material

                    <input type="radio" name="type" value="SFG" style="margin-bottom: 0; width: auto; padding-right: 5px;">Semi Finished Good

                    <input type="radio" name="type" value="FG" style="margin-bottom: 0; width: auto; padding-right: 5px;">Finished Good

                  </div>

                  <div class="col-md-12">

                    <div class="input-group btn-col">

                      <button type="submit" id="addNewGoodTypesFormSubmitBtn" class="btn btn-primary btnstyle">Submit</button>

                    </div>

                  </div>

                </form>

              </div>

            </div>

          </div>

        </div>

        <!-- modal end -->
      </div>

      <!-- end --->




      <!-- modal -->


      <div class="modal fade  addNewGoodGroup addNewGoodGroupFormModal" id="addNewGoodGroupFormModal">
        <div class="modal-dialog" role="document">
          <div class="modal-content card">
            <div class="modal-header card-header p-3">
              <h4 class="modal-title" id="exampleModalLabel">Add Group</h4>

            </div>
            <form action="" method="post" id="addNewGoodGroupForm">

              <div class="modal-body card-body">

                <div class="col-md-12">

                  <div class="form-input mb-2">

                    <label>Group Name</label>

                    <input type="text" name="goodGroupName" class="form-control goodGroupName">


                  </div>

                </div>

                <div class="col-md-12">

                  <div class="form-input mb-2">

                    <label>Select Parent</label>
                    <select id="parent_group_dropdown" class="form-control" name="group_parent">



                    </select>
                  </div>

                </div>

                <div class="col-md-12">

                  <div class="form-input mb-2">

                    <input type="text" id="goodType_input" name="goodType_name" class="form-control" readonly>

                    <input type="hidden" id="goodType_id" name="goodType_id" class="form-control" readonly>

                  </div>

                </div>

                <div class="col-md-12">

                  <div class="form-input mb-2">

                    <label>Group Description</label>

                    <input type="text" name="goodGroupDesc" class="form-control goodGroupDesc">

                  </div>

                </div>

              </div>

              <div class="modal-footer">

                <div class="input-group btn-col">

                  <button type="submit" id="addNewGoodGroupFormSubmitBtn" class="btn btn-primary btnstyle">Submit</button>

                </div>

              </div>

            </form>

          </div>
        </div>
      </div>






      <!-- modal end -->










      <!-- purchase group -->



      <!-- modal -->

      <div class="modal addNewPurchaseGroupFormModal" id="addNewPurchaseGroupFormModal">

        <div class="modal-dialog">

          <div class="modal-content">

            <div class="modal-header py-1" style="background-color: #003060; color:white;">

              <h4 class="modal-title text-sm text-white">Add New Purchase Group</h4>

              <button type="button" class="close text-white" data-dismiss="modal">x</button>

            </div>

            <form action="" method="post" id="addNewPurchaseGroupForm">

              <div class="modal-body">

                <div class="col-md-12 mb-3">

                  <div class="form-input">

                    <label>Purchase Group Name</label>

                    <input type="text" name="purchaseGroupName" class="form-control">

                  </div>

                </div>

                <div class="col-md-12">

                  <div class="form-input">

                    <label>Purchase Group Description</label>

                    <input type="text" name="purchaseGroupDesc" class="form-control">

                  </div>

                </div>

              </div>

              <div class="modal-footer">

                <div class="btn-col">

                  <button type="submit" id="addNewPurchaseGroupFormSubmitBtn" class="btn btn-primary float-right">Submit</button>

                </div>

              </div>

            </form>


          </div>

        </div>

      </div>

      <!-- modal end -->



      <!-- end purchase group -->

  </div>

  </section>

  <!-- /.content -->

  </div>

<?php
} else if (isset($_GET['edit'])) {
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper is-goods is-goods-edit">
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card">
          <div class="modal-header card-header py-2 px-3">
            <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
            <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
          </div>
          <div id="notesModalBody" class="modal-body card-body">
            <p class="font-monospace text-danger notesServiceTargetPrice"></p>
          </div>
        </div>
      </div>
    </div>
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <?php if (isset($msg)) { ?>
        <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
          <?= $msg ?>
        </div>
      <?php } ?>
      <div class="container-fluid">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Item List</a></li>
          <li class="breadcrumb-item"><a class="text-dark"><i class="fa fa-edit po-list-icon"></i> Edit Item</a></li>
          <li class="back-button">
            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
              <i class="fa fa-reply po-list-icon"></i>
            </a>
          </li>
        </ol>
      </div>
    </div>
    <!-- /.content-header -->
    <?php
    $itemId = base64_decode($_GET['edit']);
    $sql = "SELECT * FROM `erp_inventory_items` as item LEFT JOIN `erp_inventory_mstr_good_types` as type ON item.goodsType= type.goodTypeId   LEFT JOIN `erp_inventory_mstr_purchase_groups` as purchase ON item.purchaseGroup = purchase.purchaseGroupId LEFT JOIN `erp_inventory_mstr_good_groups` as groups ON item.goodsGroup= groups.goodGroupId WHERE  `item`.`itemId` = $itemId";

    $resultObj = queryGet($sql);
    $row = $resultObj["data"];
    //console($row);
    $goodsGroup = $row['goodTypeName'];

    $getPricing = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId` = $itemId AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id");
    // console($getPricing);

    ?>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="goodsEditForm" name="goodsEditForm" enctype="multipart/form-data">
          <input type="hidden" name="editgoodsdata" id="editgoodsdata" value="<?= $itemId ?>">
          <input type="hidden" name="goodsType" value="<?= $row['goodTypeId'] ?>">
          <input type="hidden" name="id" value="<?= $itemId ?>">
          <input type="hidden" id="goodsTypeName" name="goodsTypeName" value="<?= $row['goodTypeName'] ?>">

          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">
                    <div class="card-header">
                      <h4>Classification
                        <span class="text-danger">*</span>
                      </h4>
                    </div>
                    <div class="card-body goods-card-body others-info vendor-info so-card-body classification-card-body">
                      <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <div class="row goods-info-form-view customer-info-form-view">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <div class="form-input">
                                <select id="goodTypeDropDown_edit" name="goodstype" class="form-control goodTypeDropDown" disabled>
                                  <option value=""><?= $row['goodTypeName'] ?></option>

                                </select>
                              </div>
                            </div>
                            <?php
                            if ($row['goodsType'] != 9) {

                            ?>
                              <div class="col-lg-6 col-md-6 col-sm-6" id="goodsGroup">
                                <div class="form-input">
                                  <?php
                                  $good_group = queryGet("SELECT * FROM `erp_inventory_mstr_good_groups` WHERE  `companyId`=$company_id AND `goodGroupId` = '" . $row['goodsGroup'] . "'");
                                  //console($good_group );
                                  ?>
                                  <select id="" name="goodsGroup" class="form-control" disabled>
                                    <option value="<?= $good_group['data']['goodGroupId'] ?>"><?= $good_group['data']['goodGroupName'] ?></option>
                                  </select>
                                </div>
                              </div>





                            <?php
                            }

                            if ($row['goodsType'] != 7 && $row['goodsType'] != 5 && $row['goodsType'] != 9 &&  $row['goodsType'] != 10) {
                              //  echo 1;
                            ?>
                              <div class="col-lg-6 col-md-6 col-sm-6" id="purchaseGroup">
                                <div class="form-input">
                                  <select id="" name="purchaseGroup" class="form-control" disabled>
                                    <!-- <option value="">Select Purchase Group</option> -->
                                    <?php
                                    $purchase_group = queryGet("SELECT * FROM `erp_inventory_mstr_purchase_groups` WHERE`companyId`=$company_id", true);
                                    foreach ($purchase_group['data'] as $purchasesgroup) {
                                    ?>

                                      <option value="<?= $purchasesgroup['purchaseGroupId'] ?>" <?php if ($purchasesgroup['purchaseGroupId'] == $row['purchaseGroup']) {
                                                                                                  echo "selected";
                                                                                                } ?>><?php echo $purchasesgroup['purchaseGroupName']; ?></option>

                                    <?php
                                    }
                                    ?>

                                  </select>
                                </div>
                              </div>

                              <div class="col-lg-6 col-md-6 col-sm-6" id="availability" disabled>
                                <div class="form-input">
                                  <select id="avl_check" name="availabilityCheck" class="form-control">
                                    <option value="">Availability Check</option>
                                    <option value="Daily" <?php if ($row['availabilityCheck'] == "Daily") {
                                                            echo "selected";
                                                          } ?>>Daily</option>
                                    <option value="Weekly" <?php if ($row['availabilityCheck'] == "Weekly") {
                                                              echo "selected";
                                                            } ?>>Weekly</option>
                                    <option value="By Weekly" <?php if ($row['availabilityCheck'] == "By Weekly") {
                                                                echo "selected";
                                                              } ?>>By Weekly</option>
                                    <option value="Monthly" <?php if ($row['availabilityCheck'] == "Monthly") {
                                                              echo "selected";
                                                            } ?>>Monthly</option>
                                    <option value="Qtr" <?php if ($row['availabilityCheck'] == "Qtr") {
                                                          echo "selected";
                                                        } ?>>Qtr</option>
                                    <option value="Half Y" <?php if ($row['availabilityCheck'] == "Half Y") {
                                                              echo "selected";
                                                            } ?>>Half Y</option>
                                    <option value="Year" <?php if ($row['availabilityCheck'] == "Year") {
                                                            echo "selected";
                                                          } ?>>Year</option>
                                  </select>
                                </div>

                              </div>















                              <?php
                            } elseif ($row['goodsType'] == 9) {
                              $array_ex = (explode(",", $row['asset_classes']));
                              // console($array_ex);
                              foreach ($array_ex as $arr) {
                                $clas_sql = queryGet("SELECT * FROM `erp_depreciation_table` WHERE `depreciation_id`=$arr");

                              ?>
                                <div class="col-lg-6 col-md-6 col-sm-6 asset_classification_edit" id="asset_classification_edit">
                                  <div class="form-input">
                                    <input type="text" name="" class="form-control" value="<?= $clas_sql['data']['asset_class'] ?> " readonly>
                                  </div>
                                </div>

                                <span id="asset_classification_new" class="asset_classification_new" style="display:none;">
                                </span>





                                <div class="col-lg-6 col-md-6 col-sm-6">

                                  <div class="form-input">

                                    <label>GL Code </label>
                                    <?php

                                    $responce = getAllChartOfAccounts_list_by_p($company_id, 1);

                                    ?>

                                    <select id="glCode" name="glCode" class="form-control" disabled>
                                      <?php
                                      foreach ($responce["data"] as $responce) {
                                      ?>
                                        <option value=<?= $responce["id"] ?> <?php if ($responce["id"] == $row['parentGlId']) {
                                                                                echo "selected";
                                                                              } ?>><?= $responce["gl_code"] . "|" . $responce["gl_label"] ?></option>
                                      <?php
                                      }
                                      ?>
                                    </select>

                                  </div>

                                </div>

                            <?php
                              }
                            }
                            ?>

                            <?php

                            if ($row['goodsType'] == 3 || $row['goodsType'] == 4 || $row['goodsType'] == 5 || $row['goodsType'] == 9) {

                            ?>

                              <div class="col-lg-6 col-md-6 col-sm-6" id="discount_group">
                                <div class="form-input">
                                  <div class="dropdown check-dropdown" data-control="checkbox-dropdown">
                                    <label class="dropdown-label">Select Item Discount Group</label>

                                    <div class="dropdown-list">
                                      <a href="#" data-toggle="check-all" class="dropdown-option">
                                        Check All
                                      </a>

                                      <?php
                                      $discountsql = queryGet("SELECT * FROM `erp_item_discount_group` WHERE company_id = $company_id", true);
                                      // console($discountsql);
                                      foreach ($discountsql['data'] as $discount) {
                                      ?>


                                        <label class="dropdown-option">
                                          <input type="checkbox" name="discount_group[]" value="<?= $discount['item_discount_group_id'] ?>" <?php if (in_array($discount['item_discount_group_id'], json_decode($row['discountGroup']))) {
                                                                                                                                              echo 'checked';
                                                                                                                                            } ?> />
                                          <?= $discount['item_discount_group'] ?>
                                        </label>
                                      <?php
                                      }
                                      ?>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            <?php
                            }
                            ?>

                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <?php

                              if ($row['goodsType'] == 2) {


                              ?>

                                <div class="form-inline float-right" id="bomCheckBoxDiv">
                                  <input type="checkbox" name="bomRequired" style="width: auto; margin-bottom: 0;" checked disabled><label class="mb-0">Required BOM</label>

                                </div>
                              <?php
                              } elseif ($row['goodsType'] == 3 || $row['goodsType'] == 4) {
                              ?>
                                <div class="form-inline float-right" id="bomRadioDiv">

                                  <div class="goods-input for-manufac d-flex">

                                    <input type="radio" name="bomRequired_radio" value="1" <?php if ($row['isBomRequired'] == 1) {
                                                                                              echo 'checked';
                                                                                            } ?> disabled>

                                    <label for="" class="mb-0 ml-2">For Manufacturing</label>

                                  </div>

                                  <div class="goods-input for-trading d-flex">

                                    <input type="radio" name="bomRequired_radio" value="0" <?php if ($row['isBomRequired'] == 0) {
                                                                                              echo 'checked';
                                                                                            } ?> disabled>

                                    <label for="" class="mb-0 ml-2">For Trading</label>

                                  </div>



                                </div>
                              <?php
                              } elseif ($row['goodsType'] == 5 || $row['goodsType'] == 10) {
                              ?>

                                <div class="boq-checkbox" id="boqCheckBoxDiv">

                                  <div class="d-flex mb-3">

                                    <input type="checkbox" name="boqRequired" style="width: auto; margin-bottom: 0;" <?php if ($row['isBomRequired'] == 1) {
                                                                                                                        echo 'checked';
                                                                                                                      } ?> disabled>

                                    <label class="mb-0 ml-2">Mark as project</label>

                                  </div>

                                  <p class="text-xs"><span class="text-danger pr-2">*</span><b>What is the significant</b></p>
                                  <ul>
                                    <li>
                                      <p class="text-xs">This code will be used for any project </p>
                                    </li>
                                    <li>
                                      <p class="text-xs">which will have the BOQ (Bill of Quantity).</p>
                                    </li>
                                    <li>
                                      <p class="text-xs">To activate or complete the code, the BOQ need to be created.</p>
                                    </li>

                                  </ul>

                                </div>
                              <?php
                              }
                              ?>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>
              <?php
              if ($row['goodsType'] != 7 && $row['goodsType'] != 5 && $row['goodsType'] != 10) {
                $storage_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_STORAGE . "`  WHERE `item_id`=$itemId");
                // console($storage_sql);
              ?>
                <div class="row" id="storageDetails">

                  <div class="col-lg-12 col-md-12 col-sm-12">

                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                      <div class="card-header">

                        <h4>Storage Details</h4>

                      </div>

                      <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                        <div class="row">

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="row goods-info-form-view customer-info-form-view">

                              <div class="col-lg-4 col-md-4 col-sm-4">

                                <div class="form-input">

                                  <label for="">Storage Control</label>

                                  <input type="text" name="storageControl" class="form-control" value="<?= $storage_sql['data']['storageControl'] ?>">

                                </div>

                              </div>

                              <div class="col-lg-4 col-md-4 col-sm-4">

                                <div class="form-input">

                                  <label for="">Max Storage Period</label>

                                  <input type="text" name="maxStoragePeriod" class="form-control" value="<?= $storage_sql['data']['maxStoragePeriod'] ?>">

                                </div>

                              </div>

                              <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="form-input">
                                  <label class="label-hidden" for="">Min Time Unit</label>
                                  <select id="minTime" name="minTime" class="select2 form-control">
                                    <option value="">Min Time Unit</option>
                                    <option value="Day" <?php if ($storage_sql['data']['minRemainSelfLifeTimeUnit'] == "Day") {
                                                          echo "selected";
                                                        }  ?>>Day</option>
                                    <option value="Month" <?php if ($storage_sql['data']['minRemainSelfLifeTimeUnit'] == "Month") {
                                                            echo "selected";
                                                          }  ?>>Month</option>
                                    <option value="Hours" <?php if ($storage_sql['data']['minRemainSelfLifeTimeUnit'] == "Hours") {
                                                            echo "selected";
                                                          }  ?>>Hours</option>

                                  </select>
                                </div>
                              </div>

                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label for="">Minimum Remain Self life</label>

                                  <input type="text" name="minRemainSelfLife" class="form-control" value="<?= $storage_sql['data']['minRemainSelfLife'] ?>">

                                </div>

                              </div>

                              <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-input">
                                  <label class="label-hidden" for="">Max Time Unit</label>
                                  <select id="maxTime" name="maxTime" class="select2 form-control">
                                    <option value="">Max Time Unit</option>
                                    <option value="Day" <?php if ($storage_sql['data']['maxStoragePeriodTimeUnit'] == "Day") {
                                                          echo "selected";
                                                        }  ?>>Day</option>
                                    <option value="Month" <?php if ($storage_sql['data']['maxStoragePeriodTimeUnit'] == "Month") {
                                                            echo "selected";
                                                          }  ?>>Month</option>
                                    <option value="Hours" <?php if ($storage_sql['data']['maxStoragePeriodTimeUnit'] == "Hours") {
                                                            echo "selected";
                                                          }  ?>>Hours</option>

                                  </select>
                                </div>
                              </div>

                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label for=""> Minimum Stock</label>

                                  <input step="0.01" type="number" name="min_stock" id="stock" class="form-control stock" id="exampleInputBorderWidth2" value="<?= $getPricing['data']['min_stock'] ?>">

                                </div>

                              </div>

                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label for=""> Maximum Stock </label>

                                  <input step="0.01" type="number" name="max_stock" id="stock" class="form-control stock" id="exampleInputBorderWidth2" value="<?= $getPricing['data']['max_stock'] ?>">

                                </div>

                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              <?php
              }
              if ($row['goodsType'] != 7 && $row['goodsType'] != 5 &&  $row['goodsType'] != 10) {

                $stock_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`='" . $row['itemId'] . "' AND `location_id`=$location_id");
                // console($stock_sql);
              ?>
                <!-- <div class="row" id="stockRate">

                  <div class="col-lg-12 col-md-12 col-sm-12">

                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                      <div class="card-header">

                        <h4>Stock Position</h4>

                      </div>

                      <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                        <div class="row">

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="row goods-info-form-view customer-info-form-view">

                              <div class="col-lg-4 col-md-4 col-sm-4">

                                <div class="form-input">

                                  <label for=""> Stock Quantity</label>

                                  <input step="0.01" type="number" name="stock" id="stock" class="form-control stock" id="exampleInputBorderWidth2" value="<?= $stock_sql['data']['itemTotalQty'] ?>">

                                </div>

                              </div>

                              <div class="col-lg-4 col-md-4 col-sm-4">

                                <div class="form-input">

                                  <label for="">Unit Price</label><label id="buom_per"> </label>

                                  <input step="0.01" type="number" name="rate" id="rate" class="form-control rate" id="exampleInputBorderWidth2" value="<?= $stock_sql['data']['movingWeightedPrice'] ?>">

                                </div>

                              </div>

                              <div class="col-lg-4 col-md-4 col-sm-4">

                                <div class="form-input">

                                  <label for="">Value</label>

                                  <input step="0.01" type="number" name="total" id="total" class="form-control total" id="exampleInputBorderWidth2" value="<?= $stock_sql['data']['movingWeightedPrice'] * $stock_sql['data']['itemTotalQty'] ?>">

                                </div>

                              </div>

                            </div>

                          </div>

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="row goods-info-form-view customer-info-form-view">

                              <div class="col-lg-4 col-md-4 col-sm-4">

                                <div class="form-input">

                                  <label for=""> Minimum Stock</label>

                                  <input step="0.01" type="number" name="min_stock" id="stock" class="form-control stock" id="exampleInputBorderWidth2" value="<?= $stock_sql['data']['min_stock'] ?>">

                                </div>

                              </div>

                              <div class="col-lg-4 col-md-4 col-sm-4">

                                <div class="form-input">

                                  <label for=""> Maximum Stock </label>

                                  <input step="0.01" type="number" name="max_stock" id="stock" class="form-control stock" id="exampleInputBorderWidth2" value="<?= $stock_sql['data']['max_stock'] ?>">

                                </div>

                              </div>
                              <div class="col-lg-4 col-md-4 col-sm-4">

                                <div class="form-input">

                                  <label for=""> Stock dated on </label>

                                  <input type="date" name="stock_date" id="stock_date" class="form-control stock" id="exampleInputBorderWidth2" value="<?= $stock_sql['data']['stock_date'] ?>">

                                </div>

                              </div>
                            </div>
                          </div>



                        </div>

                      </div>

                    </div>

                  </div>

                </div> -->

              <?php
              }
              ?>





            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
              <?php
              if ($row['goodsType'] != 7 && $row['goodsType'] != 5 &&  $row['goodsType'] != 10) {
              ?>
                <div class="row" id="basicDetails">

                  <div class="col-lg-12 col-md-12 col-sm-12">

                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                      <div class="card-header">

                        <h4>Basic Details

                          <span class="text-danger">*</span>

                        </h4>

                      </div>

                      <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                        <div class="row">

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="row goods-info-form-view basic-info-form-view">

                              <div class="col-lg-12 col-md-12 col-sm-12">

                                <div class="form-input">

                                  <label>Item Name</label>

                                  <input type="text" name="itemName" class="form-control item_name" id="exampleInputBorderWidth2" value="<?= $row['itemName'] ?>">
                                  <ul class="suggestion-item" id="suggestedNames">

                                  </ul>

                                </div>

                              </div>
                              <div class="col-lg-12 col-md-12 col-sm-12">

                                <div class="form-input">

                                  <label>Additional Description</label>

                                  <textarea class="item_desc" rows="3" name="itemDesc" id="exampleInputBorderWidth2" placeholder="Additional Description"><?= $row['itemDesc'] ?></textarea>

                                </div>

                              </div>
                              <?php
                              $uomList_sql = getUomList('material');
                              $uomList = $uomList_sql['data'];


                              ?>
                              <div class="row">

                                <div class="col-12">

                                  <div class="row dash-border-row">

                                    <div class="col-lg-6 col-md-6 col-sm-6 unit-measure-col">

                                      <div class="row mb-4">

                                        <div class="col-lg-6 col-md-6 col-sm-6 col pr-0 base-measure">

                                          <div class="form-input">

                                            <label>Base UOM </label>


                                            <select id="buomDrop" name="baseUnitMeasure" class="form-control">

                                              <option value="">Base Unit of Measurement</option>

                                              <?php





                                              foreach ($uomList as $oneUomList) {

                                              ?>

                                                <option value="<?= $oneUomList['uomId'] ?>" <?php if ($oneUomList['uomId'] == $row['baseUnitMeasure']) {
                                                                                              echo "selected";
                                                                                            } ?>><?= $oneUomList['uomName'] . '  ||  ' .  $oneUomList['uomDesc']  ?> </option>

                                              <?php

                                              }

                                              ?>

                                            </select>

                                          </div>

                                        </div>

                                        <div class="col-lg-6 col-md-6 col-sm-6 col">

                                          <div class="form-input">

                                            <label>Alternate UOM</label>

                                            <select id="iuomDrop" name="issueUnit" class="form-control">

                                              <option value="">Alternate Unit of Measurement</option>

                                              <?php




                                              foreach ($uomList as $oneUomLists) {

                                              ?>

                                                <option value="<?= $oneUomLists['uomId'] ?>" <?php if ($oneUomLists['uomId'] == $row['issueUnitMeasure']) {
                                                                                                echo "selected";
                                                                                              } ?>><?= $oneUomLists['uomName'] . '  ||  ' . $oneUomLists['uomDesc']  ?> </option>

                                              <?php

                                              }

                                              ?>

                                            </select>

                                          </div>

                                        </div>

                                      </div>

                                      <div class="row calculate-row">

                                        <div class="col-lg-1 col-md-1 col-sm-1 col p-0">

                                          <input type="text" class="form-control bg-none p-0" placeholder="1" readonly>

                                        </div>
                                        <?php
                                        $buom_id = $row['baseUnitMeasure'];
                                        $auom_id = $row['issueUnitMeasure'];

                                        $buom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$buom_id ");
                                        $buom = $buom_sql['data']['uomName'];
                                        $buom_desc = $buom_sql['data']['uomDesc'];

                                        $service_unit_sql =  queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`='" . $row['service_unit'] . "' ");
                                        //  console($buom);
                                        $auom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$auom_id ");
                                        $auom = $buom_sql['data']['uomName'];
                                        $auom_desc = $auom_sql['data']['uomDesc']

                                        ?>

                                        <div class="col-lg-3 col-md-3 col-sm-3 col">

                                          <!-- <input type="text" name="netWeight" class="form-control bg-none p-0" id="buom" placeholder="unit" readonly> -->
                                          <input type="text" name="netWeight" class="form-control bg-none p-0" id="buom" value="<?= $buom . '||' . $buom_desc ?>" readonly>

                                        </div>

                                        <div class="col-lg-1 col-md-1 col-sm-1 col">

                                          <p class="equal-style mt-1">=</p>

                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-3 col">

                                          <input type="text" name="rel" class="form-control item_rel" id="rel" value="<?= $row['uomRel'] ?>">

                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-3 col">

                                          <!-- <input type="text" name="netWeight" class="form-control bg-none p-0" placeholder="unit" id="ioum" readonly> -->
                                          <input type="text" name="netWeight" id="ioum" class="form-control bg-none p-0" value="<?= $auom . '||' . $auom_desc ?>" readonly>

                                        </div>

                                      </div>

                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-6 hsn-modal-col">

                                      <div class="row calculate-parent-row mb-4">

                                        <div class="col-lg-12 col-md-12 col-sm-12 col hsn-column pr-3">

                                          <div class="form-input">

                                            <label>HSN </label>

                                            <!-- <button class="btn btn-primary btn-transparent" type="button" data-toggle="modal" data-target="#goodsHSNModal" ></button> -->

                                            <select id="hsnDropDown" name="hsn" class="form-control">

                                              <option id="hsnlabelOne" value="<?= $row['hsnCode'] ?>"><?= $row['hsnCode'] ?></option>

                                            </select>

                                          </div>

                                        </div>

                                      </div>

                                      <?php
                                      $hsn_desc = queryGet("SELECT `hsnDescription` FROM `erp_hsn_code` WHERE `hsnCode`='" . $row['hsnCode'] . "'");
                                      ?>


                                      <div class="row calculate-hsn-row mt-3 mb-2">

                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                          <div class="form-input">

                                            <!-- <label class="label-hidden">HSN</label> -->

                                            <p class="hsn-description-info" id="hsnDescInfo"><?= $hsn_desc['data']['hsnDescription'] ?></p>

                                          </div>

                                        </div>

                                      </div>

                                    </div>


                                  </div>

                                </div>

                              </div>

                              <?php

                              if ($row['goodsType'] == "9") {

                              ?>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                  <div class="cost-center" id="cost-center">
                                    <label for="">Cost Center</label>
                                    <select name="costCenter" class="form-control" disabled>
                                      <option value="">Cost Center</option>
                                      <?php
                                      $funcList = $BranchPoObj->fetchFunctionality()['data'];
                                      foreach ($funcList as $func) {
                                      ?>
                                        <option value="<?= $func['CostCenter_id'] ?>" <?php if ($row['cost_center'] == $func['CostCenter_id']) {
                                                                                        echo "selected";
                                                                                      } ?>>
                                          <?= $func['CostCenter_code'] . '[' . $func['CostCenter_desc'] . ']' ?></option>
                                      <?php } ?>
                                    </select>
                                  </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-6">
                                  <div class="cost-center" id="depKey">
                                    <label for="">Asset Depreciation Key <span id="despkey_id"><?= $row['dep_key'] ?></span></label>
                                    <input type="hidden" id="dep_key_val" name="dep_key">

                                    </select>
                                  </div>
                                </div>

                              <?php
                              }

                              if ($row['goodsType'] == 1) {

                              ?>
                                <div class="col-lg-12 col-md-12 col-sm-12 px-0 pt-3">

                                  <div class="form-input" id="mwp">

                                    <label for="">Moving Weighted Price</label><label id=""> </label>

                                    <input step="0.01" type="number" name="rate" id="rate" class="form-control rate" id="exampleInputBorderWidth2" value="<?= $getPricing['data']['movingWeightedPrice'] ?>">

                                  </div>

                                </div>



                              <?php
                              }

                              ?>


                              <!-- <div class="row othe-cost-infor modal-add-row_537">
                                <div class="row othe-cost-infor pl-0 pr-0">

                                  <div class="col-lg-5 col-md-5 col-sm-5">

                                    <div class="form-input">

                                      <label>Specification</label>

                                      <input type="text" name="spec[1][spec_name]" data-attr="1" class="form-control spec_vldtn specification_1" id="">

                                    </div>

                                  </div>
                                  <div class="col-lg-5 col-md-5 col-sm-5">

                                    <div class="form-input">

                                      <label>Specification Details</label>

                                      <input type="text" name="spec[1][spec_detail]" data-attr="1" class="form-control spec_dtls_vldtn specificationDetails_1" id="">

                                    </div>

                                  </div>



                                  <div class="col-lg col-md-2 col-sm-2">
                                    <div class="add-btn-plus">
                                      <a style="cursor: pointer" class="btn btn-primary" onclick="addMultiQtyf(537)">
                                        <i class="fa fa-plus"></i>
                                      </a>
                                    </div>
                                  </div>
                                </div>
                              </div> -->






                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>


                </div>

              <?php

              }

              if ($row['goodTypeId'] == 3 || $row['goodTypeId'] == 4) {


              ?>

                <div class="row" id="pricing">

                  <div class="col-lg-12 col-md-12 col-sm-12">

                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                      <div class="card-header">

                        <h4>Pricing and Discount

                          <span class="text-danger">*</span>

                        </h4>

                      </div>

                      <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                        <div class="row">

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="row goods-info-form-view customer-info-form-view">

                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label for="">Default MRP</label>

                                  <input step="0.01" type="number" name="price" class="form-control price" id="exampleInputBorderWidth2" value="<?= $getPricing['data']['itemPrice'] ?>" placeholder="price">

                                </div>

                              </div>

                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label for="">Default Discount (%)</label>

                                  <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" value="<?= $getPricing['data']['itemMaxDiscount'] ?>" placeholder=" Discount">

                                </div>

                              </div>

                              <!-- <div class="col-lg-4 col-md-4 col-sm-4">

                                <div class="form-input">

                                  <label for="">Cost</label>

                                  <input step="0.01" type="number" name="cost" class="form-control cost" id="exampleInputBorderWidth2" value="0" placeholder="">

                                </div>

                              </div> -->



                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              <?php
              }
              if ($row['goodsType'] == 7 || $row['goodsType'] == 5 ||  $row['goodsType'] == 10) {
              ?>
                <div class="row" id="service_sales_details">

                  <div class="col-lg-12 col-md-12 col-sm-12">

                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                      <div class="card-header">

                        <h4>Service Details

                          <span class="text-danger">*</span>

                        </h4>

                      </div>

                      <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                        <div class="row">

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="row goods-info-form-view customer-info-form-view">

                              <div class="col-lg-12 col-md-12 col-sm-12">

                                <div class="form-input">

                                  <label>Service Name</label>

                                  <input type="text" name="serviceName" class="form-control item_name service_name" id="exampleInputBorderWidth2" value="<?= $row['itemName'] ?>">

                                </div>

                              </div>

                              <div class="col-lg-12 col-md-12 col-sm-12">

                                <div class="form-input">

                                  <label>Service Description</label>

                                  <textarea class="item_desc service_desc" rows="3" name="serviceDesc" id="exampleInputBorderWidth2"><?= $row['itemDesc'] ?></textarea>

                                </div>

                              </div>



                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label> HSN </label>

                                  <!-- <button class="btn btn-primary btn-transparent" type="button" data-toggle="modal" data-target="#goodsHSNModal"></button> -->

                                  <!-- <select id="" name="service_hsn" class="form-control "> -->

                                  <!-- <option id="hsnlabelservice" value="<?= $row['hsnCode'] ?>"><?= $row['hsnCode'] ?></option> -->

                                  <input type="text" name="hsn" class="form-control" id="" value="<?= $row['hsnCode'] ?>" readonly>


                                  <!-- </select> -->

                                </div>

                              </div>

                              <?php
                              if ($row['goodsType'] == 7) {
                              ?>
                                <div class="col-lg-6 col-md-6 col-sm-6" id="tds">

                                  <div class="form-input">

                                    <label>TDS </label>

                                    <!-- <button class="btn btn-primary btn-transparent" type="button" data-toggle="modal" data-target="#tdsmodal"></button> -->
                                    <select id="tdsDropDown" name="tds" class="form-control" disabled>
                                      <?php
                                      $tds_sql = queryGet("SELECT * FROM `erp_tds_details`", true);
                                      $tds_data = $tds_sql['data'];

                                      foreach ($tds_data as $tds) {

                                      ?>
                                        <option id="tdslabel" value="<?= $tds['id'] ?>" <?php if ($tds['id'] == $row['tds']) {
                                                                                          echo "selected";
                                                                                        } ?>><?= $tds['section'] . "[ RATE-" . $tds['TDSRate'] . "]" ?></option>
                                      <?php
                                      }
                                      ?>




                                    </select>

                                  </div>

                                </div>
                                <?php
                              } else {
                                if ($row['goodsType'] != 10) {
                                ?>

                                  <div class="col-lg-6 col-md-6 col-sm-6" id="service_target">

                                    <div class="form-input">

                                      <label>Service Target Price</label>

                                      <input type="text" name="price" class="form-control service_target_price" id="service_target_price" value="<?= $getPricing['data']['itemPrice']  ?>">
                                      <span class="error gds_service_target_price"></span>

                                    </div>

                                  </div>

                              <?php
                                }
                              }

                              ?>



                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label>GL Code </label>
                                  <?php
                                  if ($row['goodsType'] == 5) {
                                    $accType = 3;
                                  } else {
                                    $accType = 4;
                                  }
                                  // console($row);
                                  $responce = getAllChartOfAccounts_list_by_p($company_id, $accType);
                                  //  console($responce);

                                  ?>

                                  <select id="glCode" name="glCode" class="form-control" disabled>
                                    <?php
                                    foreach ($responce["data"] as $responce) {
                                    ?>
                                      <option value=<?= $responce["id"] ?> <?php if ($responce["id"] == $row['parentGlId']) {
                                                                              echo "selected";
                                                                            } ?>><?= $responce["gl_code"] . "|" . $responce["gl_label"] ?></option>
                                    <?php
                                    }
                                    ?>
                                  </select>

                                </div>

                              </div>

                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label>Service Unit</label>

                                  <!-- <input type="text" name="serviceUnit" class="form-control service_unit" id="exampleInputBorderWidth2" value="<?= $row['service_unit'] ?>"> -->

                                  <?php
                                  $uomList = getUomList('service');
                                  // console($uomList);
                                  // $row['issueUnitMeasure'];
                                  // echo $row['baseUnitMeasure']

                                  ?>
                                  <div class="col-lg-6 col-md-6 col-sm-6">

                                    <div class="form-input">



                                      <!-- <input type="text" name="serviceUnit" class="form-control service_unit" id="exampleInputBorderWidth2"> -->

                                      <select id="serviceUnitDrop" name="baseUnitMeasure" class="form-control serviceUnitDrop">

                                        <option>Service Unit of Measurement</option>

                                        <?php





                                        foreach ($uomList['data'] as $oneUomList) {


                                        ?>

                                          <option value="<?= $oneUomList['uomId'] ?>" <?php if ($oneUomList['uomId'] == $row['baseUnitMeasure']) {
                                                                                        echo "selected";
                                                                                      } ?>><?= $oneUomList['uomName'] . '  ||  ' .  $oneUomList['uomDesc']  ?> </option>

                                        <?php

                                        }

                                        ?>

                                      </select>

                                    </div>

                                  </div>
                                </div>

                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              <?php }
              ?>

            </div>
            <?php

            if ($row['goodsType'] != 7 && $row['goodsType'] != 5 &&  $row['goodsType'] != 10) {
            ?>
              <div class="col-lg-12 col-md-12 col-sm-12">

                <div class="card goods-creation-card so-creation-card po-creation-card h-auto" id="goodsImage">

                  <div class="card-header">

                    <h4>Image and Document

                      <!-- <span class="text-danger">*</span> -->

                    </h4>

                  </div>

                  <div class="card-body">
                    <div class="row othe-cost-infor justify-content-center">
                      <div class="row othe-cost-infor image-border-right pl-0 pr-0">


                        <?php

                        $itemImageObj = queryGet("SELECT * FROM `erp_inventory_item_images` WHERE `item_id`=$itemId AND `location_id`=$location_id", true);

                        for ($itemNum = 0; $itemNum < 6; $itemNum++) {
                          $imageName = $itemImageObj["data"][$itemNum]["image_name"] ?? "";
                          $imageLink = $imageName != "" ? COMP_STORAGE_URL . '/others/' . $imageName : "";

                        ?>
                          <div class="col-lg-2 col-md-2 col-sm-2">
                            <div class="form-input mb-3 mt-2">
                              <label class="my-2">Image</label>
                              <div class="file-upload">
                                <ion-icon name="cloud-upload-outline" class="po-list-icon md hydrated" role="img" aria-label="cloud upload outline"></ion-icon>
                                <div class="upload-btn-wrapper">
                                  <div class="icon">
                                    <ion-icon name="attach-outline" role="img" class="md hydrated" aria-label="attach outline"></ion-icon>
                                  </div>
                                  <button type="button" class="btn waves-effect waves-light">Upload a file</button>
                                  <input type="hidden" name="prevPic[]" class="form-control prevPicsInput" id="prevPicsInput_<?= $itemNum ?>" value="<?= $imageName ?>">
                                  <input type="file" name="pic[]" data-attr="1" class="form-control spec_vldtn file_1" id="file-input-1" value="<?= $imageName ?>">
                                </div>
                              </div>
                              <div class="image-container" style="<?= $imageLink == "" ? 'display: none;' : '' ?>">
                                <div class="img-container-action-btns deleteImageBtn" data-id="<?= $itemNum ?>" style="opacity: 0;">
                                  <ion-icon class="close-img" name="close-circle-outline"></ion-icon>
                                </div>
                                <img class="image-grid" src="<?= $imageLink ?>">
                              </div>
                            </div>
                          </div>
                        <?php
                        }
                        ?>



                      </div>

                      <div class="row doc-upload-file">

                        <div class="col-lg-6 col-md-6 col-sm-6">

                          <div class="form-input">
                            <label>Document</label>
                            <input type="file" name="doc[]" data-attr="1" class="form-control " id="file-input-1">
                          </div>
                        </div>


                        <div class="col-lg-6 col-md-6 col-sm-6">

                          <div class="form-input">
                            <label>Document</label>
                            <input type="file" name="doc[]" data-attr="1" class="form-control " id="file-input-1">
                          </div>
                        </div>


                      </div>
                    </div>
                  </div>
                </div>
              </div>



              <div class="col-lg-12 col-md-12 col-sm-12">

                <div class="card goods-creation-card so-creation-card po-creation-card" id="specificationDetails" style="height: auto;">

                  <div class="card-header">

                    <h4>Specification Details

                      <!-- <span class="text-danger">*</span> -->


                    </h4>

                  </div>

                  <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                    <div class="row">

                      <div class="col-lg-12 col-md-12 col-sm-12">

                        <div class="row goods-info-form-view customer-info-form-view">


                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">

                              <label for="">Net Weight</label>

                              <input step="0.01" type="number" name="netWeight" class="form-control net_weight" id="net_weight" value="<?= $row['netWeight'] ?>">

                            </div>

                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">
                              <label class="" for="">Net Weight Unit</label>
                              <select name="net_unit" class="form-control " id="net_unit">
                                <option value="">Select net weight unit</option>
                                <option value="kg" <?php if ($row['weight_unit'] == "kg") {
                                                      echo "selected";
                                                    } ?>>kg</option>
                                <option value="g" <?php if ($row['weight_unit'] == "g") {
                                                    echo "selected";
                                                  } ?>>g</option>
                                <option value="ton" <?php if ($row['weight_unit'] == "ton") {
                                                      echo "selected";
                                                    } ?>>ton</option>

                              </select>
                            </div>

                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">

                              <label for="">Gross Weight</label>

                              <input step="0.01" type="number" name="grossWeight" class="form-control gross_weight" id="gross_weight" value="<?= $row['grossWeight'] ?>">
                              <span id="gross_span"></span>

                            </div>

                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">
                              <label class="" for="">Gross Weight Unit</label>
                              <select name="gross_unit" class="form-control " id="gross_unit" disabled="">
                                <option value="">Select gross weight unit</option>
                                <option value="kg" <?php if ($row['weight_unit'] == "kg") {
                                                      echo "selected";
                                                    } ?>>kg</option>
                                <option value="g" <?php if ($row['weight_unit'] == "g") {
                                                    echo "selected";
                                                  } ?>>g</option>
                                <option value="ton" <?php if ($row['weight_unit'] == "ton") {
                                                      echo "selected";
                                                    } ?>>ton</option>

                              </select>
                            </div>

                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">

                              <label for="">Height</label>

                              <input step="0.01" type="number" name="height" class="form-control calculate_volume" id="height" value="<?= $row['height'] ?>">

                            </div>

                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">

                              <label for="">Width</label>

                              <input step="0.01" type="number" name="width" class="form-control calculate_volume" id="width" value="<?= $row['width'] ?>">

                            </div>

                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">

                              <label for="">Length</label>

                              <input step="0.01" type="number" name="length" class="form-control calculate_volume" id="length" value="<?= $row['length'] ?>">

                            </div>

                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">
                              <label class="" for="">Unit</label>
                              <select name="measure_unit" class="form-control volume_unit" id="volume_unit">

                                <option value="cm" <?php if ($row['measuring_unit'] == "cm") {
                                                      echo "selected";
                                                    } ?>>cm</option>
                                <option value="m" <?php if ($row['measuring_unit'] == "m") {
                                                    echo "selected";
                                                  } ?>>m</option>

                              </select>
                            </div>

                          </div>

                          <div class="col-lg-6 col-md-6 col-sm-6">

                            <div class="form-input">

                              <label>Volume In CM<sup>3</sup></label>

                              <input type="text" name="volumeCubeCm" class="form-control" id="volcm" readonly="" value="<?= $row['volumeCubeCm'] ?>">

                            </div>

                          </div>

                          <div class="col-lg-6 col-md-6 col-sm-6">

                            <div class="form-input">

                              <label>Volume In M<sup>3</sup></label>

                              <input type="text" name="volume" class="form-control" id="volm" readonly="" value="<?= $row['volume'] ?>">

                            </div>

                          </div>

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <label for="" class="font-bold mt-3">Technical Specification</label>

                            <hr class="my-2">

                          </div>

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="row othe-cost-infor modal-add-row_537 px-0">

                              <?php

                              $specification = queryGet("SELECT * FROM `erp_item_specification` WHERE `item_id`= $itemId AND `specification` != ''", true);
                              //console($select);
                              foreach ($specification['data'] as $specification) {
                                $rand = rand(10, 100);

                              ?>
                                <div class="row othe-cost-infor">

                                  <div class="col-lg-3 col-md-3 col-sm-3">

                                    <div class="form-input">

                                      <label>Specification</label>

                                      <input type="text" name="spec[<?= $rand ?>][spec_name]" data-attr="1" class="form-control specification_1" id="" value="<?= $specification['specification'] ?>" placeholder="Name">

                                    </div>

                                  </div>
                                  <div class="col-lg-8 col-md-8 col-sm-8">

                                    <div class="form-input">

                                      <label>Specification Details</label>

                                      <input type="text" name="spec[<?= $rand ?>][spec_detail]" data-attr="1" class="form-control spec_dtls_vldtn specificationDetails_1" id="" value="<?= $specification['specification_detail'] ?>" placeholder="Description">

                                    </div>

                                  </div>



                                  <div class="col-lg-1 col-md-1 col-sm-1">
                                    <div class="add-btn-minus justify-content-end">
                                      <a style="cursor: pointer" class="btn btn-danger">
                                        <i class="fa fa-minus"></i>
                                      </a>
                                    </div>
                                  </div>
                                </div>
                              <?php
                              }
                              ?>

                              <div class="row othe-cost-infor">

                                <div class="col-lg-3 col-md-3 col-sm-3">

                                  <div class="form-input">

                                    <label>Specification</label>

                                    <input type="text" name="spec[1][spec_name]" data-attr="1" class="form-control spec_vldtn specification_1" id="" placeholder="Name">

                                  </div>

                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-8">

                                  <div class="form-input">

                                    <label>Specification Details</label>

                                    <input type="text" name="spec[1][spec_detail]" data-attr="1" class="form-control spec_dtls_vldtn specificationDetails_1" id="" placeholder="Description">

                                  </div>

                                </div>



                                <div class="col-lg-1 col-md-1 col-sm-1">
                                  <div class="add-btn-plus justify-content-end">
                                    <a style="cursor: pointer" class="btn btn-primary" onclick="addMultiQtyf(537)">
                                      <i class="fa fa-plus"></i>
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </div>


                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>


            <?php
            }

            ?>



            <script>
              var popup = $('#myPopup');
              var popupTable = $('#myPopupTable');
              popup.scroll(function() {
                console.log(popup.scrollTop());
                console.log(popupTable[0].scrollHeight * 0.9);

                if (popup.scrollTop() >= popupTable[0].scrollHeight * 0.9) {
                  // Load AJAX content
                  // $.ajax({
                  //   url: 'ajax/content.html',
                  //   success: function(data) {
                  //     // Insert the content into the popup container


                  //     popup.append(data);
                  //   }
                  // });
                  console.log('trdx');
                } else {
                  console.log('trdx22');


                }
              });

              // $("#myPopup").scroll(function() {
              //   console.log($("#myPopup").scrollTop());
              //   console.log($("#myPopup").height());

              //   console.log($("#myPopup").scrollTop() + $("#myPopup").height());
              //   console.log($("#myPopup").height() * 0.8);
              //   if ($("#myPopup").scrollTop() + $("#myPopup").height() <= $("#myPopup").height() * 0.8) {
              //     // Load Ajax content l
              //     console.log('cgfc');
              //   } else {
              //     console.log('test');
              //   }
              // });
            </script>


            <!-----tds modal start------->


            <div class="modal fade hsn-dropdown-modal" id="tdsmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
              <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                <div class="modal-content">
                  <div class="modal-body" style="height: 500px; overflow: auto;">
                    <h4 class="text-sm pl-2">Choose TDSs</h4>
                    <div class="card">
                      <div class="card-body m-2 p-0 hsn-code">
                        <table class="table defaultDataTable table-hover tds-modal-table hsn-modal-table ">
                          <thead>
                            <th></th>
                            <th>Section</th>
                            <th>Nature</th>
                            <th>Threshold</th>
                            <th>Rate</th>
                          </thead>
                          <tbody>
                            <?php
                            $tds_sql = queryGet("SELECT * FROM `erp_tds_details`", true);
                            $tds_data = $tds_sql['data'];
                            foreach ($tds_data as $tds) {
                              // console($hsn); 
                            ?>
                              <tr>
                                <td> <input type="radio" id="tds" name="tds" data-attr="<?= $tds['section']  ?>" value="<?= $tds['id']  ?>"></td>
                                <td>
                                  <p id="section_<?= $tds['id'] ?>"><?= $tds['section'] ?></p>
                                </td>
                                <td>
                                  <p id="nature_<?= $tds['id'] ?>"><?= $tds['natureOfTransaction'] ?></p>
                                </td>
                                <td>
                                  <p id="threshold<?= $tds['id'] ?>"><?= $tds['thresholdLimit'] ?></p>
                                </td>
                                <td>
                                  <p id="rate<?= $tds['id'] ?>"><?= $tds['TDSRate'] ?>%</p>
                                </td>
                              </tr>
                            <?php
                            }
                            ?>
                          </tbody>
                        </table>
                        <!-- <div class="hsn-header">
                            <div class="hsn-title">
                              <input type="radio" id="hsn" name="hsn" value="<?= $hsn['hsnCode']  ?>">
                              <h5 id="hsnCode_<?= $hsn['hsnId'] ?>"><?= $hsn['hsnCode'] ?></h5>
                            </div>
                            <div class="tax-per">
                              <p id="taxPercentage_<?= $hsn['hsnId'] ?>"><?= $hsn['taxPercentage'] ?>%</p>
                            </div>
                          </div>
                          <div class="hsn-description">
                            <p id="hsnDescription_<?= $hsn['hsnId'] ?>"><?= $hsn['hsnDescription'] ?></p>
                          </div>-->


                      </div>






                      <div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="tdssavebtn" data-dismiss="modal">Save changes</button>
                  </div>
                </div>
              </div>
            </div>


            <!-----tds modal end------->


            <div class="btn-section mt-2 mb-2">

              <button class="btn btn-primary save-close-btn btn-xs float-right add_data" id="" value="add_post">Update</button>

              <!-- <button class="btn btn-danger save-close-btn btn-xs float-right add_data" id="draft_btn" value="add_draft" style="display:none;">Save as Draft</button> -->

            </div>
          </div>






          <!-- modal -->

          <div class="modal" id="addNewGoodTypesFormModal">

            <div class="modal-dialog">

              <div class="modal-content">

                <div class="modal-header py-1" style="background-color: #003060; color:white;">

                  <h4 class="modal-title text-white">Add New Item Type</h4>

                  <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>

                <div class="modal-body">

                  <form action="" method="post" id="addNewGoodTypesForm">

                    <div class="col-md-12 mb-3">

                      <div class="input-group">

                        <input type="text" name="goodTypeName" class="form-control">

                        <label>Type Name</label>

                      </div>

                    </div>

                    <div class="col-md-12">

                      <div class="input-group">

                        <input type="text" name="goodTypeDesc" class="form-control">

                        <label>Type Description</label>

                      </div>

                    </div>

                    <div class="col-md-12">

                      <div class="input-group btn-col">

                        <button type="submit" id="addNewGoodTypesFormSubmitBtn" class="btn btn-primary btnstyle">Submit</button>

                      </div>

                    </div>

                  </form>

                </div>

              </div>

            </div>

          </div>

          <!-- modal end -->






          <!-- modal -->



          <!-- modal end -->



          <!-- purchase group -->



          <!-- modal -->


          <!-- modal end -->







          <!-- end purchase group -->

      </div>
    </section>
    <!-- /.content -->
  </div>

  <script>
    $(document).ready(function() {
      $(".deleteImageBtn").click(function() {
        let index = $(this).data("id");
        $(`#prevPicsInput_${index}`).val('');
        console.log("clicked on delete btn", index);
      });
    });
  </script>


<?php

} else if (isset($_GET['view']) && $_GET["view"] > 0) {
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper is-goods is-goods-view">
    <!-- Content Header (Page header) -->
    <div class="content-header mb-2 p-0  border-bottom">
      <?php if (isset($msg)) { ?>
        <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
          <?= $msg ?>
        </div>
      <?php } ?>

      <div class="container-fluid">

        <div class="row pt-2 pb-2">

          <div class="col-md-6">

            <ol class="breadcrumb">

              <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>

              <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Item</a></li>

              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">View Item</a></li>

            </ol>

          </div>

          <div class="col-md-6" style="display: flex;">

            <button class="btn btn-primary btnstyle gradientBtn ml-2"><i class="fa fa-plus fontSize"></i> Back</button>

          </div>

        </div>

      </div>

    </div>

    <!-- /.content-header -->



    <!-- Main content -->

    <section class="content">

      <div class="container-fluid">

        <form action="" method="POST">

          <div class="row">

            <div class="col-md-8">

              <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa-cog" aria-hidden="true"></i></button>

              <div id="accordion">

                <div class="card card-primary">

                  <div class="card-header cardHeader">

                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseOne"> Classification </a> </h4>

                  </div>

                  <div id="collapseOne" class="collapse show" data-parent="#accordion">

                    <div class="card-body">

                      <div class="row">

                        <div class="col-md-6 mb-3">

                          <div class="input-group">

                            <select id="goodsType" name="goodsType" class="select2 form-control form-control-border borderColor">

                              <option value="" data-goodType="">Goods Type</option>

                              <option value="A">A</option>

                              <option value="B">B</option>

                            </select>

                          </div>

                        </div>

                        <div class="col-md-6 mb-3">

                          <div class="input-group">

                            <select name="goodsGroup" class="select4 form-control form-control-border borderColor">

                              <option value=""> Group</option>

                              <option value="A">A</option>

                              <option value="B">B</option>

                            </select>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="input-group">

                            <select name="purchaseGroup" class="select2 form-control form-control-border borderColor">

                              <option value="">Purchase Group</option>

                              <option value="">A</option>

                              <option value="">B</option>

                            </select>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="input-group">

                            <input type="text" name="branh" class="form-control" id="exampleInputBorderWidth2">

                            <label>Branch</label>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="input-group">

                            <select name="availabilityCheck" class="select2 form-control form-control-border borderColor">

                              <option value="">Availability Check</option>

                              <option value="Daily">Daily</option>

                              <option value="Weekly">Weekly</option>

                              <option value="By Weekly">By Weekly</option>

                              <option value="Monthly">Monthly</option>

                              <option value="Qtr">Qtr</option>

                              <option value="Half Y">Half Y</option>

                              <option value="Year">Year</option>

                            </select>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

                <div class="card card-danger">

                  <div class="card-header cardHeader">

                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseTwo"> Basic Details </a> </h4>

                  </div>

                  <div id="collapseTwo" class="collapse" data-parent="#accordion">

                    <div class="card-body">

                      <div class="row">

                        <div class="col-md-6">

                          <div class="input-group">

                            <input type="text" name="itemCode" class="form-control" id="exampleInputBorderWidth2">

                            <label>Item Code</label>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="input-group">

                            <input type="text" name="itemName" class="form-control" id="exampleInputBorderWidth2">

                            <label>Item Name</label>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="input-group">

                            <input type="text" name="netWeight" class="form-control" id="exampleInputBorderWidth2">

                            <label>Net Weight</label>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="input-group">

                            <input type="text" name="grossWeight" class="form-control" id="exampleInputBorderWidth2">

                            <label>Gross Weight</label>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Volume :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="volume" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="volume">

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">height :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="height" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="height">

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">width :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="width" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="width">

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">length :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="length" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="length">

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Base Unit Of Measure :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="baseUnitMeasure" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="baseUnitOfMeasure">

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Issue Unit :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="issueUnit" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="issueUnit">

                            </div>

                          </div>

                        </div>

                        <div class="col-md-12">

                          <textarea rows="3" name="itemDesc" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Item Description"></textarea>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

                <div class="card card-success">

                  <div class="card-header cardHeader">

                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseThree"> Storage Details </a> </h4>

                  </div>

                  <div id="collapseThree" class="collapse" data-parent="#accordion">

                    <div class="card-body">

                      <div class="row">

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Storage Bin :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="storageBin" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Storage Bin">

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Picking Area :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="pickingArea" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Picking Area">

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Temp Control :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="tempControl" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Temp Control">

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Storage Control :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="storageControl" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Storage Control">

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Max Storage Period :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="maxStoragePeriod" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Max Storage Period">

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Time Unit :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="timeUnit" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Time Unit">

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Min Remain Self Life :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="minRemainSelfLife" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Min Remain Self Life">

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

                <div class="card card-success">

                  <div class="card-header cardHeader">

                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseFour"> Purchase Details </a> </h4>

                  </div>

                  <div id="collapseFour" class="collapse" data-parent="#accordion">

                    <div class="card-body">

                      <div class="row">

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Purchasing Value Key :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="purchasingValueKey" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Purchasing Value Key">

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>

            </div>

          </div>

        </form>

      </div>

    </section>

    <!-- /.content -->

  </div>

<?php

} else {
?>



  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper is-goods">

    <!-- Content Header (Page header) -->



    <!-- Main content -->

    <section class="content">

      <div class="container-fluid">





        <!-- row -->

        <div class="row p-0 m-0">

          <div class="col-12 mt-2 p-0">



            <!-- <ol class="breadcrumb bg-transparent">

              <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Items</a></li>


            </ol> -->

            <div class="p-0 pt-1 my-2">

              <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                  <h3 class="card-title">
                    Item Master
                  </h3>


                  <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>

                </li>

              </ul>

            </div>

            <div class="card card-tabs" style="border-radius: 20px;">

              <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">

                <div class="card-body">

                  <div class="row filter-serach-row">

                    <div class="col-lg-1 col-md-1 col-sm-12">

                      <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                    </div>

                    <div class="col-lg-11 col-md-11 col-sm-12">

                      <div class="row table-header-item">

                        <div class="col-lg-11 col-md-11 col-sm-11">

                          <div class="filter-search">

                            <div class="filter-list">
                              <a href="goods.php" class="btn active"><i class="fa fa-stream mr-2 active"></i>All</a>
                              <a href="goods-type-items.php" class="btn"><i class="fa fa-list mr-2"></i>Raw Materials</a>
                              <a href="goods-type-items.php?sfg" class="btn"><i class="fa fa-clock mr-2"></i>SFG</a>
                              <a href="goods-type-items.php?fg" class="btn"><i class="fa fa-lock-open mr-2"></i>FG</a>
                              <a href="goods-type-items.php?service" class="btn"><i class="fa fa-lock mr-2"></i>Services</a>
                              <a href="manage-assets.php" class="btn"><i class="fa fa-lock mr-2"></i>Assets</a>
                            </div>
                            <div class="dropdown filter-dropdown" id="filterDropdown">

                              <button type="button" class="dropbtn" id="dropBtn">
                                <i class="fas fa-filter po-list-icon"></i>
                              </button>

                              <div class="dropdown-content">
                                <a href="goods.php" class="btn active"><i class="fa fa-stream mr-2 active"></i>All</a>
                                <a href="goods-type-items.php" class="btn"><i class="fa fa-list mr-2"></i>Raw Materials</a>
                                <a href="goods-type-items.php?sfg" class="btn"><i class="fa fa-clock mr-2"></i>SFG</a>
                                <a href="goods-type-items.php?fg" class="btn"><i class="fa fa-lock-open mr-2"></i>FG</a>
                                <a href="goods-type-items.php?service" class="btn"><i class="fa fa-lock mr-2"></i>Services</a>
                                <a href="manage-assets.php" class="btn"><i class="fa fa-lock mr-2"></i>Assets</a>
                              </div>
                            </div>

                            <div class="section serach-input-section">

                              <input type="text" id="myInput" name="keyword" placeholder="" class="field form-control" value="<?php echo $keywd; ?>" />

                              <div class="icons-container">

                                <div class="icon-search">

                                  <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>

                                </div>

                                <div class="icon-close">

                                  <i class="fa fa-search po-list-icon" id="myBtn"></i>



                                </div>

                              </div>

                            </div>

                          </div>

                        </div>

                        <div class="col-lg-1 col-md-1 col-sm-1">

                          <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>

                        </div>

                      </div>



                    </div>

                    <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Filter Items</h5>

                          </div>
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <input type="text" name="keyword2" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php /*if (isset($_REQUEST['keyword2'])) {
                                                                                                                                                      echo $_REQUEST['keyword2'];
                                                                                                                                                    } */ ?>">
                              </div>
                              <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <select name="status_s" id="status_s" class="fld form-control" style="appearance: auto;">
                                  <option value=""> Status </option>
                                  <option value="active" <?php if (isset($_REQUEST['status_s']) && 'active' == $_REQUEST['status_s']) {
                                                            echo 'selected';
                                                          } ?>>Active
                                  </option>
                                  <option value="inactive" <?php if (isset($_REQUEST['status_s']) && 'inactive' == $_REQUEST['status_s']) {
                                                              echo 'selected';
                                                            } ?>>Inactive
                                  </option>
                                  <option value="draft" <?php if (isset($_REQUEST['status_s']) && 'draft' == $_REQUEST['status_s']) {
                                                          echo 'selected';
                                                        } ?>>Draft</option>
                                </select>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-6 col-md-6 col-sm-6">
                                <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                          echo $_REQUEST['form_date_s'];
                                                                                                                        } ?>" />
                              </div>
                              <div class="col-lg-6 col-md-6 col-sm-6">
                                <input class="fld form-control" type="date" name="to_date_s" id="to_date_s" value="<?php if (isset($_REQUEST['to_date_s'])) {
                                                                                                                      echo $_REQUEST['to_date_s'];
                                                                                                                    } ?>" />
                              </div>
                            </div>

                          </div>
                          <div class="modal-footer">
                            <!-- <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync "></i>Reset</a>-->
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                              Search</button>
                          </div>
                        </div>
                      </div>
                    </div>

              </form>
              <script>
                var input = document.getElementById("myInput");
                input.addEventListener("keypress", function(event) {
                  if (event.key === "Enter") {
                    event.preventDefault();
                    document.getElementById("myBtn").click();
                  }
                });
                var form = document.getElementById("search");

                document.getElementById("myBtn").addEventListener("click", function() {
                  form.submit();
                });
              </script>

              <div class="col-lg-12 col-md-12 col-sm-12">

                <div class="tab-content pt-0" id="custom-tabs-two-tabContent">

                  <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">

                    <?php

                    $cond = '';



                    $sts = " AND `status` !='deleted'";

                    if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {

                      $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                    }




                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {

                      $cond .= " AND createdAt between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                    }

                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                      $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword2'] . "%' OR `itemName` like '%" . $_REQUEST['keyword2'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword2'] . "%')";
                    } else {

                      if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {

                        $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword'] . "%' OR `itemName` like '%" . $_REQUEST['keyword'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword'] . "%')";
                      }
                    }




                    $sql_list = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE 1 " . $cond . "  AND `company_id`=$company_id ORDER BY itemId desc  limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";

                    $qry_list = mysqli_query($dbCon, $sql_list);

                    $num_list = mysqli_num_rows($qry_list);





                    $countShow = "SELECT count(*) FROM `" . ERP_INVENTORY_ITEMS . "` WHERE 1 " . $cond . "  AND `company_id`=$company_id  ";

                    $countQry = mysqli_query($dbCon, $countShow);

                    $rowCount = mysqli_fetch_array($countQry);

                    $count = $rowCount[0];

                    $cnt = $GLOBALS['start'] + 1;

                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_INVENTORY_ITEMS", $_SESSION["logedBranchAdminInfo"]["adminId"]);

                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);

                    $settingsCheckbox = unserialize($settingsCh);

                    if ($num_list > 0) { ?>

                      <table class="table defaultDataTable table-hover text-nowrap">

                        <thead>

                          <tr class="alert-light">

                            <?php if (in_array(1, $settingsCheckbox)) { ?>

                              <th>Item Code</th>

                            <?php }

                            if (in_array(2, $settingsCheckbox)) { ?>

                              <th>Item Name</th>

                            <?php }

                            if (in_array(3, $settingsCheckbox)) { ?>

                              <th> UOM</th>

                            <?php  }

                            if (in_array(4, $settingsCheckbox)) { ?>

                              <th>Group</th>

                            <?php }
                            if (in_array(5, $settingsCheckbox)) { ?>

                              <th>Type</th>

                            <?php

                            }

                            if (in_array(6, $settingsCheckbox)) { ?>

                              <th>Moving Weighted Price</th>

                            <?php  }

                            if (in_array(7, $settingsCheckbox)) { ?>

                              <th>Valuation Class</th>

                            <?php

                            }


                            if (in_array(8, $settingsCheckbox)) { ?>

                              <th> Target Price</th>

                            <?php

                            }




                            ?>

                            <th>BOM Status</th>

                            <th>Status</th>

                            <th>Action</th>
                            <th>Add</th>

                          </tr>

                        </thead>

                        <tbody>

                          <?php

                          $customerModalHtml = "";

                          while ($row = mysqli_fetch_assoc($qry_list)) {
                            //console($row);
                            $itemId = $row['itemId'];
                            $itemCode = $row['itemCode'];

                            $itemName = $row['itemName'];

                            $netWeight = $row['netWeight'];

                            $volume = $row['volume'];

                            $goodsType = $row['goodsType'];

                            $grossWeight = $row['grossWeight'];

                            $buom_id = $row['baseUnitMeasure'];
                            $auom_id = $row['issueUnitMeasure'];

                            $buom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$buom_id ");
                            $buom = $buom_sql['data']['uomName'];

                            $service_unit_sql =  queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`='" . $row['service_unit'] . "' ");
                            //  console($buom);
                            $auom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$auom_id ");
                            $auom = $buom_sql['data']['uomName'];


                            $goodTypeId = $row['goodsType'];
                            $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
                            $type_name = $type_sql['data']['goodTypeName'] ? $type_sql['data']['goodTypeName'] : '-';



                            $goodGroupId = $row['goodsGroup'];
                            $group_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE `goodGroupId`=$goodGroupId ");
                            $group_name = $group_sql['data']['goodGroupName'] ? $group_sql['data']['goodGroupName'] : '-';

                            $purchaseGroupId = $row['purchaseGroup'];
                            $purchase_group_sql = queryGet("SELECT * FROM `erp_inventory_mstr_purchase_groups` WHERE `purchaseGroupId` = $purchaseGroupId ");
                            $purchase_group = isset($purchase_group_sql['data']['purchaseGroupName']) ? $purchase_group_sql['data']['purchaseGroupName'] : '-';


                            $summary_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId");
                            $mwp = $summary_sql['data']['movingWeightedPrice'];
                            $val_class = $summary_sql['data']['priceType'] ? $summary_sql['data']['priceType'] : '-';
                            $min_stock = $summary_sql['data']['min_stock'] ? $summary_sql['data']['min_stock'] : '-';
                            $max_stock = $summary_sql['data']['max_stock'] ? $summary_sql['data']['max_stock'] : '-';

                            
                            $gldetails=getChartOfAccountsDataDetails($row['parentGlId'])['data'];
                            $glName=$gldetails['gl_label'];
                            $glCode = $gldetails['gl_code'];

                          ?>

                            <tr>

                              <!-- <td><?= $cnt++ ?></td> -->

                              <?php if (in_array(1, $settingsCheckbox)) { ?>

                                <td><?= $row['itemCode'] ?></td>

                              <?php }

                              if (in_array(2, $settingsCheckbox)) { ?>

                                <td>
                                  <p class="pre-normal"><?= $row['itemName'] ?></p>
                                </td>

                              <?php }

                              if (in_array(3, $settingsCheckbox)) { ?>

                                <td><?php
                                    if ($row['goodsType'] == 5 || $row['goodsType'] == 7 || $row['goodsType'] == 10) {
                                      echo $service_unit_sql['data']['uomName'];
                                    } else {
                                      echo $buom;
                                    } ?> </td>

                              <?php }

                              if (in_array(4, $settingsCheckbox)) { ?>

                                <td>
                                  <p class="pre-normal"><?= $group_name ?></p>
                                </td>

                              <?php }
                              if (in_array(5, $settingsCheckbox)) { ?>

                                <td><?= $type_name ?></td>

                              <?php }
                              if (in_array(6, $settingsCheckbox)) { ?>

                                <td><?= round($mwp, 2) ?></td>

                              <?php }

                              if (in_array(7, $settingsCheckbox)) { ?>

                                <td><?= $val_class  ?></td>

                              <?php }

                              if (in_array(8, $settingsCheckbox)) { ?>

                                <td class="text-right"><?= round($summary_sql['data']['itemPrice'], 2) ?></td>

                              <?php }


                              ?>



                              <td>

                                <?php

                                if ($row['isBomRequired'] == 1) {



                                  echo '<span class="status">Required</span>';
                                } else {

                                  echo '<span class="status-danger">Not Required</span>';
                                }

                                ?>

                              </td>



                              <td>

                                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">

                                  <input type="hidden" name="id" value="<?php echo $row['itemId'] ?>">

                                  <input type="hidden" name="changeStatus" value="active_inactive">

                                  <button <?php if ($row['status'] == "draft") { ?> type="button" style="cursor: inherit; border:none" <?php } else { ?>type="submit" onclick="return confirm('Are you sure to change status?')" style="cursor: pointer; border:none" <?php } ?> class="p-0 m-0 ml-2" data-toggle="tooltip" data-placement="top" title="<?php echo $row['status'] ?>">

                                    <?php if ($row['status'] == "active") { ?>

                                      <span class="status"><?php echo ucfirst($row['status']); ?></span>

                                    <?php } else if ($row['status'] == "inactive") { ?>

                                      <span class="status-danger"><?php echo ucfirst($row['status']); ?></span>

                                    <?php } else if ($row['status'] == "draft") { ?>

                                      <span class="status-warning"><?php echo ucfirst($row['status']); ?></span>

                                    <?php } ?>



                                  </button>

                                </form>

                              </td>

                              <td>



                                <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $row['itemId'] ?>" class="btn btn-sm">

                                  <i class="fa fa-eye po-list-icon"></i>

                                </a>


                                <!-- right modal start here  -->

                                <div class="modal fade right goods-modal customer-modal classic-view-modal" id="fluidModalRightSuccessDemo_<?= $row['itemId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">

                                  <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">

                                    <!--Content-->

                                    <div class="modal-content">

                                      <!--Header-->

                                      <div class="modal-header pt-4">

                                        <div class="row item-specification-row">

                                          <div class="col-lg-4 col-md-4 col-sm-4">



                                            <?php
                                            if ($row['goodsType'] == 5 || $row['goodsType'] == 7 || $row['goodsType'] == 10) {

                                              //console($row);

                                            ?>
                                              <div class="service-img">
                                                <img src="../../public/assets/img/icons/goods-service.png" class="service-icon" title="goods-iem-image" alt="goods_item_image">
                                              </div>
                                            <?php

                                            } else {

                                            ?>
                                              <div class="item-img">
                                                <img src="../../public/assets/img/image/goods-item-image.png" title="goods-iem-image" alt="goods_item_image">
                                              </div>
                                            <?php
                                            }

                                            ?>



                                          </div>

                                          <div class="col-lg-8 col-md-8 col-sm-8">

                                            <div class="head-title">

                                              <p class="heading lead text-elipse" title='Item Name : <?= $itemName ?>'>Item Name : <?= $itemName ?></p>

                                              <p class="item-code">Item Code : <?= $itemCode ?></p>

                                              <p class="item-desc text-elipse" title='Description : <?= $row['itemDesc'] ?>'> Description : <?= $row['itemDesc'] ?></p>

                                              <p class="item-type">Item Type : <?= $type_name ?></p>


                                            </div>


                                          </div>

                                        </div>
                                        <div class="display-flex-space-between mt-4 mb-3 location-master-action">
                                          <ul class="nav nav-tabs" id="myTab" role="tablist">

                                            <li class="nav-item">
                                              <a class="nav-link active" id="home-tab<?= $row['itemId'] ?>" data-toggle="tab" href="#home<?= $row['itemId'] ?>" role="tab" aria-controls="home<?= $row['itemId'] ?>" aria-selected="true">Info</a>
                                            </li>

                                            <li class="nav-item">
                                              <a class="nav-link" id="classic-view-tab" data-toggle="tab" href="#classic-view<?= $row['itemId'] ?>" role="tab" aria-controls="classic-view" aria-selected="false"><ion-icon name="apps-outline" class="mr-2"></ion-icon> Classic View</a>
                                            </li>

                                            <!-- -------------------Audit History Button Start------------------------- -->
                                            <li class="nav-item">
                                              <a class="nav-link auditTrail" id="history-tab<?= $row['itemId'] ?>" data-toggle="tab" data-ccode="<?= $row['itemCode'] ?>" href="#history<?= $row['itemId'] ?>" role="tab" aria-controls="history<?= $row['itemId'] ?>" aria-selected="false"><i class="fas fa-history mr-2"></i>Trail</a>
                                            </li>
                                            <!---------------------Audit History Button End--------------------------->
                                          </ul>


                                          <div class="action-btns display-flex-gap goods-flex-btn" id="action-navbar">

                                            <?php $itemId = base64_encode($row['itemId']) ?>

                                            <form action="" method="POST" class="d-flex gap-3">

                                              <?php
                                              $check_item = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`='" . $row['itemId'] . "' AND `location_id`!=$location_id", true);
                                              // console($check_item);
                                              if ($check_item['numRows'] > 0) {
                                              ?>
                                                <a href="" id="my-link" name="customerEditBtn" disabled>

                                                  <i class="fa fa-edit po-list-icon-invert" title="This item is uneditable because this item has already been used by some other location"></i>

                                                </a>
                                              <?php
                                              } else {



                                              ?>

                                                <a href="goods.php?edit=<?= $itemId ?>" name="customerEditBtn">

                                                  <i title="Edit" class="fa fa-edit po-list-icon-invert"></i>

                                                </a>
                                              <?php
                                              }
                                              ?>

                                              <!-- <a href="">

                                              <i title="Delete" class="fa fa-trash po-list-icon-invert"></i>

                                            </a> -->

                                              <a href="">

                                                <i title="Toggle" class="fa fa-toggle-on po-list-icon-invert"></i>

                                              </a>

                                            </form>

                                          </div>

                                        </div>

                                      </div>



                                      <!--Body-->

                                      <div class="modal-body goods-service-material">
                                        <div class="tab-content" id="myTabContent">
                                          <div class="tab-pane fade show active" id="home<?= $row['itemId'] ?>" role="tabpanel" aria-labelledby="home-tab">
                                            <div class="row px-3">
                                              <div class="col-lg-12 col-md-12 col-sm-12">
                                                <?php
                                                if ($row['goodsType'] == 5 || $row['goodsType'] == 7 || $row['goodsType'] == 10) {

                                                  //console($row);

                                                ?>

                                                  <!-- service tab -->
                                                  <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                    <div class="accordion-item">
                                                      <h2 class="accordion-header" id="flush-headingOne">
                                                        <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#classifications" aria-expanded="true" aria-controls="flush-collapseOne">
                                                          Service Details
                                                        </button>
                                                      </h2>
                                                      <div id="classifications" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                        <div class="accordion-body p-0">
                                                          <div class="card">
                                                            <div class="card-body accordion-card-details p-0">
                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs"> Name</p>
                                                                <p class="font-bold text-xs group-desc" title="Name : <?= $row['itemName'] ?>">: <?= $row['itemName'] ?></p>
                                                              </div>
                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs"> Description</p>
                                                                <p class="font-bold text-xs group-desc" title="Description : <?= $row['itemDesc'] ?>">: <?= $row['itemDesc'] ?></p>
                                                              </div>
                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">HSN</p>
                                                                <p class="font-bold text-xs">: <?= $row['hsnCode'] ?></p>
                                                              </div>
                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">GL Details</p>
                                                                <p class="font-bold text-xs">: <?= $glName ?> [<?= $glCode ?>]</p>
                                                              </div>
                                                              <?php
                                                              $tds_id = $row['tds'];
                                                              $tds_sql = queryGet("SELECT * FROM `erp_tds_details` WHERE `id`= $tds_id");
                                                              $tds_data = $tds_sql['data'];

                                                              ?>
                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">TDS </p>
                                                                <p class="font-bold text-xs">: <?php if ($tds_sql['numRows'] > 0) {
                                                                                                  echo $tds_data['section'] . '(' . $tds_data['TDSRate'] . ')';
                                                                                                } else {
                                                                                                  echo '-';
                                                                                                } ?></p>
                                                              </div>
                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">Service Unit</p>
                                                                <p class="font-bold text-xs">: <?= $service_unit_sql['data']['uomName']    ?></p>
                                                              </div>
                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">Service Target Price</p>
                                                                <p class="font-bold text-xs">: <?= round($summary_sql['data']['itemPrice'], 2)  ?></p>
                                                              </div>
                                                            </div>
                                                          </div>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>

                                                <?php

                                                } else {

                                                ?>
                                                  <!-------Classification------>
                                                  <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                    <div class="accordion-item">
                                                      <h2 class="accordion-header" id="flush-headingOne">
                                                        <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#classifications" aria-expanded="true" aria-controls="flush-collapseOne">
                                                          Classification
                                                        </button>
                                                      </h2>
                                                      <div id="classifications" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                        <div class="accordion-body p-0">
                                                          <div class="card">
                                                            <div class="card-body accordion-card-details p-0">
                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">GL details </p>
                                                                <p class="font-bold text-xs">: <?= $glName ?> [<?= $glCode ?>]</p>
                                                              </div>
                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">Goods Type </p>
                                                                <p class="font-bold text-xs">: <?= $type_name ?></p>
                                                              </div>
                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs"> Group</p>
                                                                <p class="font-bold text-xs group-desc" title="Group : <?= $group_name ?>">: <?= $group_name ?></p>
                                                              </div>
                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">Availablity Check</p>
                                                                <p class="font-bold text-xs">: <?= $row['availabilityCheck'] ?></p>
                                                              </div>
                                                            </div>
                                                          </div>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>

                                                  <!-------Basic Details------>
                                                  <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                    <div class="accordion-item">
                                                      <h2 class="accordion-header" id="flush-headingOne">
                                                        <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#basicDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                          Basic Details
                                                        </button>
                                                      </h2>
                                                      <div id="basicDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                        <div class="accordion-body p-0">

                                                          <div class="card">

                                                            <div class="card-body accordion-card-details p-0">

                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">HSN </p>
                                                                <p class="font-bold text-xs">: <?= $row['hsnCode'] ?></p>
                                                              </div>

                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs"> UOM </p>
                                                                <p class="font-bold text-xs">: <?= $buom ?></p>
                                                              </div>

                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs"> Moving Price </p>
                                                                <p class="font-bold text-xs">: <?= round($mwp, 2)  ?></p>
                                                              </div>

                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs"> Target Price </p>
                                                                <p class="font-bold text-xs">: <?= round($summary_sql['data']['itemPrice'], 2) ?></p>
                                                              </div>

                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">Net Weight </p>
                                                                <p class="font-bold text-xs">: <?= $row['netWeight'] . "  " . $row['weight_unit'] ?></p>
                                                              </div>

                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">Gross Weight </p>
                                                                <p class="font-bold text-xs">: <?= $row['grossWeight'] . "  " . $row['weight_unit'] ?></p>
                                                              </div>

                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">Volume </p>
                                                                <p class="font-bold text-xs">: <?= $row['volume'] ?> m<sup>3</sup></p>
                                                              </div>

                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">Height </p>
                                                                <p class="font-bold text-xs">: <?= $row['height'] . " " . $row['measuring_unit'] ?></p>
                                                              </div>

                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">Width </p>
                                                                <p class="font-bold text-xs">: <?= $row['width'] . "  " . $row['measuring_unit'] ?></p>
                                                              </div>

                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">Length </p>
                                                                <p class="font-bold text-xs">: <?= $row['length'] . "  " . $row['measuring_unit'] ?></p>
                                                              </div>

                                                            </div>
                                                          </div>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>

                                                  <!-------Storage Details------>
                                                  <?php
                                                  $item_id = $row['itemId'];
                                                  $storage_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_STORAGE . "` WHERE `item_id`=$item_id AND `location_id`=$location_id");
                                                  $storage_data = $storage_sql['data'];


                                                  ?>
                                                  <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                    <div class="accordion-item">
                                                      <h2 class="accordion-header" id="flush-headingOne">
                                                        <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#storageDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                          Storage Details
                                                        </button>
                                                      </h2>
                                                      <div id="storageDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                        <div class="accordion-body p-0">

                                                          <div class="card">

                                                            <div class="card-body accordion-card-details p-0">

                                                              <!-- <div class="display-flex-space-between">
                                                              <p class="font-bold text-xs">Storage Bin :</p>
                                                              <p class="font-bold text-xs"><?= $row['storageBin'] ?></p>
                                                            </div>

                                                            <div class="display-flex-space-between">
                                                              <p class="font-bold text-xs">Picking Area :</p>
                                                              <p class="font-bold text-xs"><?= $row['pickingArea'] ?></p>
                                                            </div>

                                                            <div class="display-flex-space-between">
                                                              <p class="font-bold text-xs">Temp Control :</p>
                                                              <p class="font-bold text-xs"><?= $row['tempControl'] ?></p>
                                                            </div> -->

                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">Storage Control </p>
                                                                <p class="font-bold text-xs">: <?= $storage_data['storageControl'] ?></p>
                                                              </div>

                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">Max Storage Period </p>
                                                                <p class="font-bold text-xs">: <?= $storage_data['maxStoragePeriod'] ?></p>
                                                              </div>

                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">Max Storage Period Time </p>
                                                                <p class="font-bold text-xs">: <?= $storage_data['maxStoragePeriodTimeUnit'] ?></p>
                                                              </div>

                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">Min Remain Self Life Time Unit </p>
                                                                <p class="font-bold text-xs">: <?= $storage_data['minRemainSelfLife'] ?></p>
                                                              </div>

                                                              <div class="display-flex-space-between">
                                                                <p class="text-xs">Min Remain Self Life </p>
                                                                <p class="font-bold text-xs">: <?= $storage_data['minRemainSelfLifeTimeUnit'] ?></p>
                                                              </div>

                                                            </div>
                                                          </div>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>

                                                  <!-- specifications -->

                                                  <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                    <div class="accordion-item">
                                                      <h2 class="accordion-header" id="flush-headingOne">
                                                        <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#specificationDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                          Specification Details
                                                        </button>
                                                      </h2>
                                                      <div id="specificationDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                        <div class="accordion-body p-0">

                                                          <div class="card">

                                                            <div class="card-body accordion-card-details p-0">

                                                              <table class="table table-hover">
                                                                <thead>
                                                                  <tr>
                                                                    <th class="bg-light font-bold" style="width: 30%;">
                                                                      Specification
                                                                    </th>
                                                                    <th class="bg-light font-bold">
                                                                      Specification Details
                                                                    </th>
                                                                  </tr>
                                                                </thead>
                                                                <tbody>
                                                                  <?php

                                                                  $select_spec = queryGet("SELECT * FROM `erp_item_specification` WHERE `item_id`= $item_id AND `specification` != '' ", true);
                                                                  // console($select_spec);
                                                                  foreach ($select_spec['data'] as $specs) {
                                                                    //    console($specs['specification']);

                                                                  ?>
                                                                    <tr>
                                                                      <td class="border-right">
                                                                        <p><?= $specs['specification'] ?></p>
                                                                      </td>
                                                                      <td>
                                                                        <p style="white-space: pre-wrap;"><?= $specs['specification_detail'] ?></p>
                                                                      </td>
                                                                    </tr>
                                                                  <?php
                                                                  }

                                                                  ?>

                                                                </tbody>
                                                              </table>

                                                              <!-- 
                                                            <div class="display-flex-space-between">
                                                              <p class="font-bold text-xs">Specification :</p>
                                                              <p class="font-bold text-xs"><?= $specs['specification'] ?></p>
                                                            </div>

                                                            <div class="display-flex-space-between">
                                                              <p class="font-bold text-xs"> Specification Details :</p>
                                                              <p class="font-bold text-xs"><?= $specs['specification_detail'] ?></p>
                                                            </div> -->


                                                            </div>
                                                          </div>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>

                                                  <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                    <div class="accordion-item">
                                                      <h2 class="accordion-header" id="flush-headingOne">
                                                        <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#specificationDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                          Image Details
                                                        </button>
                                                      </h2>
                                                      <div id="specificationDetails" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                        <div class="accordion-body p-0">

                                                          <div class="card">

                                                            <div class="card-body p-3">

                                                              <div class="image-block-section">

                                                                <?php

                                                                $select_image = queryGet("SELECT * FROM `erp_inventory_item_images` WHERE `item_id`= $item_id ", true);
                                                                //console($select_image);
                                                                foreach ($select_image['data'] as $image) {
                                                                  //    console($specs['specification']);

                                                                ?>
                                                                  <div class="image-block">
                                                                    <img src='<?php echo COMP_STORAGE_URL; ?>/others/<?= $image['image_name'] ?>' alt="" class="image-grid-output">
                                                                  </div>
                                                                  image url: <?=COMP_STORAGE_URL?>
                                                                <?php
                                                                }

                                                                ?>

                                                              </div>
                                                            </div>
                                                          </div>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>

                                                <?php
                                                }

                                                ?>

                                              </div>

                                              <!-- <div class="col-lg-6 col-md-6 col-sm-6">
                                                <div class="graphical-view">
                                                  <div id="chartDivRealTimeDataSort" class="chartContainer"></div>
                                                </div>
                                              </div> -->

                                            </div>

                                          </div>


                                          <div class="tab-pane fade" id="classic-view<?= $row['itemId'] ?>" role="tabpanel" aria-labelledby="profile-tab">
                                            <div class="card classic-view bg-transparent">
                                              <div class="card-body classic-view-so-table" style="overflow: auto;">
                                                <!-- <button type="button" class="btn btn-primary classic-view-btn float-right" id="printButton">Print Table</button> -->
                                                <button type="button" class="btn btn-primary classic-view-btn float-right" onclick="window.print(); return false;">Print</button>
                                                <div class="printable-view">
                                                  <h3 class="h3-title text-center font-bold text-sm mb-4">Items</h3>
                                                  <?php
                                                  $companyData = $BranchPoObj->fetchCompanyDetailsById($company_id)['data'];

                                                  //console($row);

                                                  //console($companyData);

                                                  ?>
                                                  <table class="classic-view table-bordered">

                                                    <?php
                                                    if ($row['goodsType'] == 5 || $row['goodsType'] == 7 || $row['goodsType'] == 10) {

                                                      //console($row);

                                                    ?>

                                                      <tbody>
                                                        <tr>
                                                          <td class="border-right">
                                                            <p class="font-bold"><?= $companyData['company_name'] ?></p>
                                                            <p><?= $companyData['company_flat_no'] ?>, <?= $companyData['company_building'] ?></p>
                                                            <p><?= $companyData['company_district'] ?>,<?= $companyData['company_location'] ?>,<?= $companyData['company_pin'] ?></p>
                                                            <p><?= $companyData['company_city'] ?></p>
                                                            <!-- <p>GSTIN/UIN: <?= $companyData['company_name'] ?></p> -->
                                                            <p>Company’s PAN: <?= $companyData['company_pan'] ?></p>
                                                            <p>State Name :<?= $companyData['company_state'] ?></p>
                                                            <!-- <p>E-Mail : <?= $companyData['company_name'] ?></p>  -->
                                                          </td>
                                                          <td>
                                                            <p class="font-bold"><?= $row['itemName'] ?></p>
                                                            <p><?= $row['itemDesc'] ?></p>
                                                          </td>
                                                        </tr>
                                                      </tbody>




                                                      <!-- service-tab-pdf -->
                                                      <tbody>
                                                        <tr>
                                                          <th colspan="2" class="text-left">Service Details</th>
                                                        </tr>
                                                        <tr class="service-list">
                                                          <th class="bg-transparent text-left">HSN</th>
                                                          <td class="text-left border-left">
                                                            <p><?= $row['hsnCode'] ?></p>
                                                          </td>
                                                        </tr>
                                                        <tr class="service-list">
                                                          <th class="bg-transparent text-left">GL Details</th>
                                                          <td class="text-left border-left">
                                                            <p><?= $glName ?> [<?= $glCode ?>]</p>
                                                          </td>
                                                        </tr>
                                                        <tr class="service-list">
                                                          <th class="bg-transparent text-left">TDS</th>
                                                          <td class="text-left border-left">
                                                            <p><?= $row['tds'] ?></p>
                                                          </td>
                                                        </tr>
                                                        <tr class="service-list">
                                                          <th class="bg-transparent text-left">TDS Percentage</th>
                                                          <td class="text-left border-left">
                                                            <p><?= $row['tds'] ?></p>
                                                          </td>
                                                        </tr>
                                                        <tr class="service-list">
                                                          <th class="bg-transparent text-left">Service Unit</th>
                                                          <td class="text-left border-left">
                                                            <p><?= $row['service_unit'] ?></p>
                                                          </td>
                                                        </tr>
                                                        <tr class="service-list">
                                                          <th class="bg-transparent text-left">Service Target Price</th>
                                                          <td class="text-left border-left">
                                                            <p>-</p>
                                                          </td>
                                                        </tr>
                                                      </tbody>

                                                    <?php

                                                    } else {

                                                    ?>
                                                      <tbody>
                                                        <tr>
                                                          <td colspan="7" class="border-right">
                                                            <p class="font-bold"><?= $companyData['company_name'] ?></p>
                                                            <p><?= $companyData['company_flat_no'] ?>, <?= $companyData['company_building'] ?></p>
                                                            <p><?= $companyData['company_district'] ?>,<?= $companyData['company_location'] ?>,<?= $companyData['company_pin'] ?></p>
                                                            <p><?= $companyData['company_city'] ?></p>
                                                            <!-- <p>GSTIN/UIN: <?= $companyData['company_name'] ?></p> -->
                                                            <p>Company’s PAN: <?= $companyData['company_pan'] ?></p>
                                                            <p>State Name :<?= $companyData['company_state'] ?></p>
                                                            <!-- <p>E-Mail : <?= $companyData['company_name'] ?></p>  -->
                                                          </td>

                                                        </tr>
                                                      </tbody>
                                                      <tbody>
                                                        <tr>
                                                          <th class="bg-transparent text-left" colspan="7">Basic Details</th>
                                                        </tr>
                                                        <tr>
                                                          <th>Item Name</th>
                                                          <th colspan="2">Description</th>
                                                          <th>Base UOM</th>
                                                          <th>Alternate UOM</th>
                                                          <th>HSN</th>
                                                          <th>Moving Weighted Price</th>
                                                        </tr>
                                                        <tr>
                                                          <td class="text-left">
                                                            <p><?= $row['itemName'] ?></p>
                                                          </td>
                                                          <td class="text-left" colspan="2">
                                                            <p><?= $row['itemDesc'] ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?php
                                                                if ($row['goodsType'] == 5 || $row['goodsType'] == 7 || $row['goodsType'] == 10) {
                                                                  echo $service_unit_sql['data']['uomName'];
                                                                } else {
                                                                  echo $buom;
                                                                } ?> </p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?php
                                                                if ($row['goodsType'] == 5 || $row['goodsType'] == 7 || $row['goodsType'] == 10) {
                                                                  echo '-';
                                                                } else {
                                                                  echo $auom;
                                                                } ?> </p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $row['hsnCode'] ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= round($mwp, 2)  ? $mwp : '-'; ?></p>
                                                          </td>
                                                        </tr>

                                                        <tr>
                                                          <th colspan="4">Specification</th>
                                                          <th colspan="3">Specification Details</th>
                                                        </tr>
                                                        <?php
                                                        foreach ($select_spec['data'] as $specs_each) {
                                                        ?>
                                                          <tr>
                                                            <td class="text-left" colspan="4">
                                                              <p><?= $specs_each['specification'] ?></p>
                                                            </td>
                                                            <td class="text-left" colspan="3">
                                                              <p><?= $specs_each['specification_detail'] ?></p>
                                                            </td>
                                                          </tr>
                                                        <?php
                                                        }
                                                        ?>
                                                        <tr>
                                                          <th class="bg-transparent text-left" colspan="7">Storage Details</th>
                                                        </tr>
                                                        <tr>
                                                          <th colspan="2">Storage Control</th>
                                                          <th>Max Storage Period</th>
                                                          <th colspan="2">Minimum Remain Self Life</th>
                                                          <th>Minimum Stock</th>
                                                          <th>Maximum Stock</th>
                                                        </tr>
                                                        <tr>
                                                          <td colspan="2" class="text-center">
                                                            <p><?= $storage_data['storageControl'] ? $storage_data['storageControl']  : '-'; ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $storage_data['maxStoragePeriod'] ?  $storage_data['maxStoragePeriod']  : '-'; ?></p>
                                                          </td>
                                                          <td class="text-center" colspan="2">
                                                            <p><?= $storage_data['minRemainSelfLife'] ? $storage_data['minRemainSelfLife']  : '-'; ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $min_stock ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $max_stock ?></p>
                                                          </td>
                                                        </tr>
                                                        <tr>
                                                          <th class="bg-transparent text-left" colspan="7">Specification Details</th>
                                                        </tr>
                                                        <tr>
                                                          <th>Net Weight</th>
                                                          <th>Gross Weight</th>
                                                          <th>Width</th>
                                                          <th>Height</th>
                                                          <th>Length</th>
                                                          <th>Volume In CM</th>
                                                          <th>Volume In M</th>
                                                        </tr>
                                                        <tr>
                                                          <td class="text-center">
                                                            <p><?= $row['netWeight'] . "  " . $row['weight_unit'] ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $row['grossWeight'] . "  " . $row['weight_unit'] ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $row['width'] . "  " . $row['measuring_unit'] ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $row['height'] . " " . $row['measuring_unit'] ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $row['length'] . "  " . $row['measuring_unit'] ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $row['volume'] ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $row['volumeCubeCm'] ?></p>
                                                          </td>
                                                        </tr>
                                                        <tr>
                                                          <th class="bg-transparent text-left" colspan="7">Classification </th>
                                                        </tr>
                                                        <tr>
                                                          <th colspan="3">Goods Type</th>
                                                          <th>Group Type</th>
                                                          <th colspan="2">Purchase Group Type</th>
                                                          <th>Availability Check</th>
                                                        </tr>
                                                        <tr>
                                                          <td colspan="3" class="text-left">
                                                            <p><?= $type_name ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $group_name ?></p>
                                                          </td>
                                                          <td colspan="2" class="text-center">
                                                            <p><?= $purchase_group ?> </p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $row['availabilityCheck']  ? $row['availabilityCheck'] : '-';  ?></p>
                                                          </td>
                                                        </tr>
                                                      </tbody>
                                                    <?php
                                                    }

                                                    ?>
                                                  </table>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <!-- -------------------Audit History Tab Body Start------------------------- -->

                                          <div class="tab-pane fade" id="history<?= $row['itemId'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                            <div class="audit-head-section mb-3 mt-3 ">
                                              <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($row['createdBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['createdAt']) ?></p>
                                              <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($row['updatedBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['updatedAt']) ?></p>
                                            </div>
                                            <hr>
                                            <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $row['itemCode'] ?>">

                                              <ol class="timeline">

                                                <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                  <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                  <div class="new-comment font-bold">
                                                    <p>Loading...
                                                    <ul class="ml-3 pl-0">
                                                      <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                    </ul>
                                                    </p>
                                                  </div>
                                                </li>
                                                <p class="mt-0 mb-5 ml-5">Loading...</p>

                                                <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                  <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                  <div class="new-comment font-bold">
                                                    <p>Loading...
                                                    <ul class="ml-3 pl-0">
                                                      <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                    </ul>
                                                    </p>
                                                  </div>
                                                </li>
                                                <p class="mt-0 mb-5 ml-5">Loading...</p>


                                              </ol>
                                            </div>
                                          </div>
                                          <!-- -------------------Audit History Tab Body End------------------------- -->




                                        </div>

                                      </div>

                                    </div>

                                    <!--/.Content-->

                                  </div>

                                </div>

                                <!-- right modal end here  -->


                              </td>

                              <td>
                                <?php
                                $item_id = $row['itemId'];
                                $check_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE  `location_id`=$location_id  AND `itemId`=$item_id ", true);
                                if ($check_sql['status'] == "success") {

                                ?>
                                  <button class="btn btn-success" type="button">Added</button>

                                  <?php

                                } else {

                                  if ($goodTypeId == 5 || $goodTypeId == 7) {

                                  ?>
                                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#addToLocation_service_<?= $row['itemId'] ?>">Add</button>

                                  <?php

                                  } else {

                                  ?>


                                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#addToLocation_<?= $row['itemId'] ?>">Add</button>
                                <?php
                                  }
                                }

                                ?>
                              </td>

                            </tr>

                            <!------add to location modal------>

                            <div class="modal fade addtolocation-service-modal" id="addToLocation_service_<?= $row['itemId'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                  <div class="modal-header"></div>
                                  <div class="modal-body">Do You Want To Add This Item To Your Location ?</div>
                                  <div class="modal-footer">
                                    <form method="POST" action="">
                                      <input type="hidden" name="createLocationItem" id="createLocationItem" value="">
                                      <input type="hidden" name="item_id" value="<?= $row['itemId'] ?>">
                                      <input type="hidden" name="bomStatus" value="0">
                                      <div class="col-lg-12 col-md-12 col-sm-12">
                                        <button class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Confirm</button>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>




                            <!-----add form modal start --->
                            <div class="modal fade hsn-dropdown-modal" id="addToLocation_<?= $row['itemId'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                              <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <form method="POST" action="">
                                      <input type="hidden" name="createLocationItem" id="createLocationItem" value="">
                                      <input type="hidden" name="item_id" value="<?= $row['itemId'] ?>">


                                      <div class="row">


                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                          <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                            <div class="card-header">

                                              <h4>Storage Details</h4>

                                            </div>

                                            <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                              <div class="row">

                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                  <div class="row goods-info-form-view customer-info-form-view">









                                                    <div class="col-lg-4 col-md-4 col-sm-4">

                                                      <div class="form-input">

                                                        <label for="">Storage Control</label>

                                                        <input type="text" name="storageControl" class="form-control">

                                                      </div>

                                                    </div>

                                                    <div class="col-lg-4 col-md-4 col-sm-4">

                                                      <div class="form-input">

                                                        <label for="">Max Storage Period</label>

                                                        <input type="text" name="maxStoragePeriod" class="form-control">

                                                      </div>

                                                    </div>

                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                      <div class="form-input">
                                                        <label class="label-hidden" for="">Min Time Unit</label>
                                                        <select id="minTime" name="minTime" class="select2 form-control">
                                                          <option value="">Min Time Unit</option>
                                                          <option value="Day">Day</option>
                                                          <option value="Month">Month</option>
                                                          <option value="Hours">Hours</option>

                                                        </select>
                                                      </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-6">

                                                      <div class="form-input">

                                                        <label for="">Minimum Remain Self life</label>

                                                        <input type="text" name="minRemainSelfLife" class="form-control">

                                                      </div>

                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                      <div class="form-input">
                                                        <label class="label-hidden" for="">Max Time Unit</label>
                                                        <select id="maxTime" name="maxTime" class="select2 form-control">
                                                          <option value="">Max Time Unit</option>
                                                          <option value="Day">Day</option>
                                                          <option value="Month">Month</option>
                                                          <option value="Hours">Hours</option>

                                                        </select>
                                                      </div>
                                                    </div>

                                                  </div>

                                                </div>

                                              </div>

                                            </div>

                                          </div>

                                        </div>




                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                          <?php
                                          //  }
                                          if ($type_name == "Finished Good" || $type_name == "Service Sales" || $type_name == "FG Trading") {
                                          ?>

                                            <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                              <div class="card-header">

                                                <h4>Pricing and Discount

                                                  <span class="text-danger">*</span>

                                                </h4>

                                              </div>

                                              <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                                <div class="row">

                                                  <div class="col-lg-12 col-md-12 col-sm-12">

                                                    <div class="row goods-info-form-view customer-info-form-view">

                                                      <div class="col-lg-6 col-md-6 col-sm-6">

                                                        <div class="form-input">

                                                          <label for="">Default MRP</label>

                                                          <input step="0.01" type="number" name="price" class="form-control price" id="exampleInputBorderWidth2" placeholder="price">

                                                        </div>

                                                      </div>

                                                      <div class="col-lg-6 col-md-6 col-sm-6">

                                                        <div class="form-input">

                                                          <label for="">Default Discount</label>

                                                          <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">

                                                        </div>

                                                        <!-- </div>

                                                    <div class="col-lg-4 col-md-4 col-sm-4">

<div class="form-input">

  <label for="">Cost</label>

  <input step="0.01" type="number" name="cost" class="form-control cost" id="exampleInputBorderWidth2" value="0" placeholder="">

</div>

</div> -->


                                                      </div>

                                                    </div>

                                                  </div>

                                                </div>

                                              </div>
                                            <?php }
                                            ?>

                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                              <button class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Submit</button>
                                            </div>


                                            </div>






                                        </div>












                                    </form>

                                  </div>
                                  <div class="modal-body" style="height: 500px; overflow: auto;">
                                    <div class="card">

                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>


                            <!---end modal --->



                          <?php } ?>




                        </tbody>





                      </table>

                      <?php

                      if ($count > 0 && $count > $GLOBALS['show']) {

                      ?>

                        <div class="pagination align-right">

                          <?php pagination($count, "frm_opts"); ?>

                        </div>



                        <!-- End .pagination -->



                      <?php  } ?>



                    <?php } else { ?>

                      <table class="table defaultDataTable table-hover text-nowrap">

                        <thead>

                          <tr>

                            <td>



                            </td>

                          </tr>

                        </thead>

                      </table>

                  </div>

                <?php } ?>

                </div>

              </div>

            </div>

          </div>






          <!---------------------------------Table settings Model Start--------------------------------->

          <div class="modal" id="myModal2">

            <div class="modal-dialog">

              <div class="modal-content">

                <div class="modal-header">

                  <h4 class="modal-title">Table Column Settings</h4>

                  <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>

                <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">

                  <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />

                  <input type="hidden" name="pageTableName" value="ERP_INVENTORY_ITEMS" />

                  <div class="modal-body">

                    <div id="dropdownframe"></div>

                    <div id="main2">

                      <table>

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

                            Base UOM</td>

                        </tr>

                        <tr>

                          <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />

                            Group</td>

                        </tr>

                        <tr>

                          <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />

                            Type</td>

                        </tr>

                        <tr>

                          <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />

                            Moving Weighted Price</td>

                        </tr>

                        <tr>

                          <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />

                            Valuation Class</td>

                        </tr>

                        <tr>

                          <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox8" value="8" />

                            Target Price</td>

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



          <!---------------------------------Table Model End--------------------------------->



        </div>

      </div>

  </div>

  </div>

  </div>

  <!-- /.row -->

  </div>

  </section>
  <!-- /.content -->
  </div>

  <!-- /.Content Wrapper. Contains page content -->

  <!-- For Pegination------->

  <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">

    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {

                                                echo  $_REQUEST['pageNo'];
                                              } ?>">

  </form>

  <!-- End Pegination from------->





<?php

}
?>


<!------------uom drop----------------->


<div class="modal fade addNewUOM addNewUOMFormModal" id="addNewUOMFormModal">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header p-3">
        <h4 class="modal-title text-sm font-bold" id="exampleModalLabel">Add UOM</h4>

      </div>
      <form action="" method="post" id="addNewUOMForm">
        <input type="hidden" name="uomType" value="material" readonly>

        <div class="modal-body">

          <div class="col-md-12">

            <p class="note-text font-italic text-black mb-3">Note: As all the UOM short codes are listed as per government e-invoice rule,new UOM creations will be under OTH only</p>

          </div>

          <div class="col-md-12">

            <div class="form-input mb-2">
              <label>UOM Short Name</label>

              <input type="text" name="uomName" class="form-control uomName" value="OTH" readonly>


            </div>

          </div>



          <div class="col-md-12">

            <div class="form-input mb-2">

              <label>UOM Name</label>

              <input type="text" name="uomDesc" class="form-control uomDesc">

            </div>

          </div>

        </div>

        <div class="modal-footer">

          <button type="button" id="addNewUOMFormSubmitBtn" class="btn btn-primary">Submit</button>

        </div>

      </form>

    </div>
  </div>
</div>





<div class="modal fade addServiceNewUOM addNewServiceUOMFormModal" id="addNewServiceUOMFormModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content card">
      <div class="modal-header card-header p-3">
        <h4 class="modal-title" id="exampleModalLabel">Add Services UOM</h4>

      </div>
      <form action="" method="post" id="addNewServiceUOMForm">
        <input type="hidden" name="uomType" value="service" readonly>

        <div class="modal-body card-body">

          <div class="col-md-12">

            <div class="form-input mb-2">

              <label>UOM Short Name</label>

              <input type="text" name="uomName" class="form-control uomName_service" value="OTH" readonly>


            </div>

          </div>



          <div class="col-md-12">

            <div class="form-input mb-2">

              <label>UOM Name</label>

              <input type="text" name="uomDesc" class="form-control uomDesc_service">

            </div>

          </div>

        </div>

        <div class="modal-footer">

          <div class="input-group btn-col">

            <button type="button" id="addNewServiceUOMFormSubmitBtn" class="btn btn-primary btnstyle">Submit</button>

          </div>

        </div>

      </form>

    </div>
  </div>
</div>




<!--------- end uom drop---------------->




</form>

<?php
require_once("../common/footer.php");

?>

<script src="../../public/assets/core.js"></script>
<script src="../../public/assets/charts.js"></script>
<script src="../../public/assets/animated.js"></script>
<script src="../../public/assets/forceDirected.js"></script>
<script src="../../public/assets/sunburst.js"></script>



<script>
  // const fileUploader = document.get('file-input');
  // const reader = new FileReader();
  // const imageGrid = document.getElementById('image-grid');


  // fileUploader.addEventListener('change', (event) => {
  //   const files = event.target.files;
  //   const file = files[0];
  //   reader.readAsDataURL(file);

  //   reader.addEventListener('load', (event) => {
  //     // Clear the existing images in the imageGrid
  //     while (imageGrid.firstChild) {
  //       imageGrid.removeChild(imageGrid.firstChild);
  //     }

  //     const img = document.createElement('img');
  //     imageGrid.appendChild(img);
  //     img.src = event.target.result;
  //     img.alt = file.name;
  //   });
  // });
</script>

<!-- <script>
  $(document).ready(function() {
    $('#searchValue').on('click', function() {
      console.log("clickked......")
      $('.add-new-hsn').toggleClass("show");
    });
  })
</script> -->

<script>
  $(document).ready(function() {
    $("#dropBtn").on("click", function(e) {
      e.stopPropagation(); // Stop the event from propagating to the document
      console.log("clickedddd");
      $("#filterDropdown .dropdown-content").addClass("active");
      $("#filterDropdown").addClass("active");
    });

    $(document).on("click", function() {
      $("#filterDropdown .dropdown-content").removeClass("active");
      $("#filterDropdown").removeClass("active");
    });

    // Close the dropdown when clicking inside it
    $("#filterDropdown .dropdown-content").on("click", function(e) {
      e.stopPropagation(); // Prevent the event from reaching the document
    });

    // $(window).resize(function() {
    //     if ($(window).width() > 768) {
    //         $("#filterDropdown .dropdown-content").hide();
    //     }
    // });
  });
</script>

<script>
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



  $(document).ready(function() {


    $(document).on('change', ".spec_vldtn", function(event) {
      const container = $(this).closest('.form-input');
      const img = container.find('.image-grid');

      // Update the source of the closest image element
      img.attr('src', URL.createObjectURL(event.target.files[0]));
      container.find(".image-container").toggle(500);
    });



    $(document).on('click', ".image-container .close-img", function(event) {
      const container = $(this).closest('.form-input');
      const img = container.find('.image-grid');
      const imageContainer = container.find(".image-container");

      // Clear the image source and hide the image container
      img.attr('src', '');
      imageContainer.hide();
    });



    // $('#goodTypeDropDown')

    //   .select2()

    //   .on('select2:open', () => {

    //     // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewGoodTypesFormModal">Add New</a></div>`);

    //   });

    // glCode
    function loadGLCode(accType) {
      // console.log(1);
      $.ajax({

        type: "POST",

        url: `ajaxs/accounting/ajax-getglbyp.php`,
        data: {
          accType: accType
        },
        beforeSend: function() {

          $("#glCode").html(`<option value="">Loding...</option>`);

        },

        success: function(response) {
          // alert(response);
          if (accType == 1) {
            $("#glCodeAsset").html(response);
          } else {
            $("#glCode").html(response);
          }

        }

      });

    }


    $(document).on("change", "#goodTypeDropDown", function() {

      // alert(1);



      let dataAttrVal = $("#goodTypeDropDown").find(':selected').data('goodtype');

      if (dataAttrVal == "RM") {

        console.log(1);

        $("#bomCheckBoxDiv").html("");
        $("#cost-center").hide("");
        $("#bomRadioDiv").html("");
        $("#pricing").hide();
        $("#purchase").html();
        $("#basicDetails").show();
        $("#storageDetails").show();
        $("#service_sales_details ").hide();
        $("#stockRate").show();
        $("#service_target").hide();
        $("#goodsGroup").show();
        $("#purchaseGroup").show();
        $("#availability").show();
        $("#mwp").show();
        $('#qaEnable').prop("checked", false);
        $('#qa_storage').html('');

        $("#submit_btn").show();
        $("#draft_btn").show();

        $(".error").hide();
        $(".error").html("");

        $("#notesModalBody").html("");

        $("#specificationDetails").show("");
        $("#goodsImage").show();
        $("#serviceStock").hide("");


        $("#asset_classification").hide("");
        $("#asset_classification_new").html("");
        $("#asset_classification_new").hide("");
        $("#depKey").hide("");
        $("#asset_gl").html();
        $("#asset_gl").hide();
        $("#boqCheckBoxDiv").hide();
        $("#discount_group").hide();
        storage_location('RM');

      } else if (dataAttrVal == "SFG") {

        $("#bomRadioDiv").html("");
        $("#serviceStock").hide("");
        $("#cost-center").hide("");
        $("#basicDetails").show();
        $("#storageDetails").show();
        $("#service_sales_details ").hide();
        $("#goodsGroup").show();
        $("#purchaseGroup").show();
        $("#availability").show();
        $("#stockRate").show();
        $("#mwp").hide();
        $("#bomCheckBoxDiv").html(`<input type="checkbox" name="bomRequired" style="width: auto; margin-bottom: 0;" checked disabled ><label class="mb-0">Required BOM</label>`);
        $("#boqCheckBoxDiv").hide();
        $("#pricing").hide();
        $("#goodsImage").show();
        $("#submit_btn").show();
        $("#draft_btn").show();
        $("#service_target").hide();
        $(".error").hide();
        $(".error").html("");
        $('#qaEnable').prop("checked", false);
        $('#qa_storage').html('');
        $("#notesModalBody").html("");
        $("#specificationDetails").show("");

        $("#asset_classification").hide("");
        $("#asset_classification_new").hide("");
        $("#depKey").hide("");

        $("#asset_gl").html();
        $("#asset_gl").hide();
        $("#discount_group").hide();


        storage_location('SFG');

      } else if (dataAttrVal == "FG") {
        $("#serviceStock").hide("");
        $("#mwp").hide();
        $("#bomCheckBoxDiv").html(``);
        $("#cost-center").hide("");
        $("#purchase").html("");
        $("#basicDetails").show();
        $("#storageDetails").show();
        $("#service_sales_details ").hide();
        $("#goodsGroup").show();
        $("#purchaseGroup").show();
        $("#availability").show();
        $("#stockRate").show();
        $("#goodsImage").show();
        $("#service_target").hide();
        $("#submit_btn").show();
        $("#draft_btn").show();
        $("#boqCheckBoxDiv").hide();
        $("#discount_group").show();


        $("#bomRadioDiv").html(`<div class="goods-input for-manufac d-flex">

          <input type="radio" name="bomRequired_radio" value="1">

          <label for="" class="mb-0 ml-2">For Manufacturing</label>

        </div>

        <div class="goods-input for-trading d-flex">

          <input type="radio" name="bomRequired_radio" value="0">

          <label for="" class="mb-0 ml-2">For Trading</label>

        </div>`);

        $("#pricing").show();

        $(".error").hide();
        $(".error").html("");

        $("#notesModalBody").html("");
        $("#specificationDetails").show("");
        $('#qaEnable').prop("checked", false);
        $('#qa_storage').html('');

        $("#asset_classification").hide("");
        $("#asset_classification_new").html("");
        $("#asset_classification_new").hide("");
        $("#depKey").hide("");
        $("#asset_gl").html();
        $("#asset_gl").hide();

        storage_location('FG');

      } else if (dataAttrVal == "SERVICES") {
        // $("#serviceStock").show("");
        $('#boqcheckbox').prop('checked', false);
        $("#mwp").hide();
        $("#submit_btn").show();
        $("#draft_btn").show();
        $("#bomCheckBoxDiv").html(``);
        $("#purchase").hide();
        $("#bomRadioDiv").html("");
        $("#goodsGroup").show();
        $("#purchaseGroup").hide();
        $("#availability").hide();

        $("#boqCheckBoxDiv").show();
        $("#goodsImage").hide();
        $("#service_sales_details ").show();
        $("#tds ").hide();
        $("#service_target").show();
        $("#basicDetails").hide();
        $("#storageDetails").hide();
        $("#pricing").hide();
        $("#stockRate").hide();
        $("#discount_group").show();


        loadGLCode(3); //INCOME GL: 3


        $(".error").hide();
        $(".error").html("");

        $("#notesModalBody").html("");
        $("#specificationDetails").hide("");

        $("#asset_classification").hide("");
        $("#asset_classification_new").html("");
        $("#asset_classification_new").hide("");
        $("#depKey").hide("");
        $("#asset_gl").html();
        $("#asset_gl").hide();



      } else if (dataAttrVal == "SERVICEP") {
        // $("#serviceStock").show("");
        $("#mwp").hide();
        $("#submit_btn").show();
        $("#draft_btn").show();
        $("#pricing").hide();
        $("#bomCheckBoxDiv").html(``);
        $("#purchase").hide();
        $("#bomRadioDiv").html("");
        $("#goodsGroup").show();
        $("#purchaseGroup").hide();
        $("#availability").hide();
        $("#specificationDetails").hide("");
        $("#service_sales_details ").show();
        $("#basicDetails").hide();
        $("#storageDetails").hide();
        $("#tds ").show();
        $("#stockRate").hide();
        $("#goodsImage").hide();
        loadGLCode(4); //EXPENSE GL:4
        $("#boqCheckBoxDiv").hide();
        $(".error").hide();
        $(".error").html("");
        $("#service_target").hide();
        $("#notesModalBody").html("");
        $("#servicegl").show();
        $("#asset_classification").hide("");
        $("#asset_classification_new").html("");
        $("#asset_classification_new").hide("");
        $("#depKey").hide("");
        $("#asset_gl").html();
        $("#asset_gl").hide();
        $("#discount_group").hide();

      } else if (dataAttrVal == "ASSET") {
        loadGLCode(1); //ASSET GL:1
        $("#serviceStock").hide("");
        $("#cost-center").show("");
        $("#submit_btn").show();
        $("#draft_btn").show();
        $("#bomCheckBoxDiv").html("");
        $("#mwp").hide();
        $("#bomRadioDiv").html("");
        $("#pricing").hide();
        $("#purchase").html();
        $("#basicDetails").show();
        $("#storageDetails").show();
        $("#service_sales_details ").hide();
        $("#stockRate").show();
        $("#service_target").hide();
        $("#boqCheckBoxDiv").hide();
        $("#goodsGroup").hide();
        $("#purchaseGroup").hide();
        $("#availability").hide();
        $(".error").hide();
        $(".error").html("");
        $("#goodsImage").show();
        $("#notesModalBody").html("");
        $("#specificationDetails").show("");

        $("#depKey").show("");


        $("#asset_gl").show();

        $("#asset_classification").show();

        $("#discount_group").hide();



      } else {

        $("#submit_btn").hide();
        $("#draft_btn").hide();
        $("#mwp").hide();
        $("#bomCheckBoxDiv").html(``);
        $("#purchase").html("");
        $("#bomRadioDiv").html("");
        $("#basicDetails").hide();
        $("#storageDetails").hide();
        $("#pricing").hide();
        $("#boqCheckBoxDiv").hide();
        $(".error").hide();
        $(".error").html("");
        $("#goodsImage").hide();
        $("#service_sales_details ").hide();
        $("#notesModalBody").html("");
        $("#serviceStock").hide("");
        $("#specificationDetails").hide("");
        $("#asset_classification").hide("");
        $("#asset_classification_new").html("");
        $("#asset_classification_new").hide("");
        $("#depKey").hide("");
        $("#asset_gl").html();
        $("#asset_gl").hide();
        $("#service_target").hide();

        $("#bomCheckBoxDiv").html(``);
        $("#purchase").hide();
        $("#bomRadioDiv").html("");
        $("#goodsGroup").hide();
        $("#purchaseGroup").hide();
        $("#availability").hide();
        $("#discount_group").hide();
      }


      function storage_location(type) {


        $.ajax({

          type: "GET",

          url: `ajaxs/items/ajax-storage-type.php`,

          data: {
            type
          },

          beforeSend: function() {

            $("#default_storage").html('<option>Loading...</option>');

          },

          success: function(response) {
            //alert(response);
            $("#default_storage").html(response);


          }



        });



      }



      $("#qaEnable").change(function() {

        if ($(this).is(':checked')) {
          // alert(1);
          var type_id = $('#goodTypeDropDown').val();
          if (type_id == 1) {

            type = 'RM';

          } else if (type_id == 2) {
            type = 'SFG';
          } else if (type_id == 3) {
            type = 'FG';
          }


          $.ajax({

            type: "GET",

            url: `ajaxs/items/ajax-qa-storage-type.php`,

            data: {
              type
            },

            beforeSend: function() {

              // $("#default_storage").html('<option>Loading...</option>');

            },

            success: function(response) {
              // alert(response);
              let obj = JSON.parse(response);
              //  alert(obj['qa_storage']);
              //  $("#default_storage").html(response);

              let num = obj['numRows'];
              if (num > 0) {

                $('#qa_storage').html(obj['qa_storage']);

              } else {
                $('#qa_storage').html(obj['qa_storage']);
                $('#qaEnable').prop("checked", false);

              }



            }



          });



        } else {
          $('#qa_storage').html('');
        }

      });

      $("#boqcheckbox").change(function() {
        // Check if the checkbox is checked
        if ($(this).is(':checked')) {
          // Checkbox is checked
          //  alert('Checkbox is checked');
          // Perform actions when checkbox is checked
          <?php
          $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['billable_project_gl']);
          $parentGlId = $paccdetails['data']['id'];
          ?>

          $("#glCode").html(`
        <option value="<?= $parentGlId ?>" selected><?= $parentGLname ?></option>`);
        } else {
          // Checkbox is unchecked
          //alert('Checkbox is unchecked');
          loadGLCode(3);
          // Perform actions when checkbox is unchecked
        }
      });


      let typeId = $(this).val();
      // alert(typeId);
      loadGoodGroup(typeId);
      load_group_modal(typeId);


      function load_group_modal(typeId) {
        //console.log("hiiiiii");
        // alert(typeId);
        $.ajax({

          type: "GET",

          url: `ajaxs/items/ajax-group-modal.php`,

          data: {
            typeId
          },

          beforeSend: function() {

            // $("#goodGroupDropDown").html(`<option value="">Loding...</option>`);
            $("#goodType_input").html(``);
            $("#goodType_id").html(``);


          },

          success: function(response) {

            //alert(response);
            var obj = jQuery.parseJSON(response);
            $("#goodType_input").val(obj['type_name']);
            $("#goodType_id").val(obj['type_id']);

            $.ajax({

              type: "GET",

              url: `ajaxs/items/ajax-good-groups.php`,

              data: {
                typeId
              },
              success: function(response) {
                $("#parent_group_dropdown").html(response);
              }

            });

          }



        });




      }



      function loadGoodGroup(typeId) {



        $.ajax({

          type: "GET",

          url: `ajaxs/items/ajax-good-groups.php`,

          data: {
            typeId
          },



          beforeSend: function() {

            $("#goodGroupDropDown").html(`<option value="">Loding...</option>`);

          },

          success: function(response) {
            console.log(response);

            $("#goodGroupDropDown").html(response);

            <?php

            if (isset($row["goodGroupId"])) {

            ?>


              $(`#goodGroupDropDown option[value=<?= $row["goodGroupId"] ?>]`).attr('selected', 'selected');

            <?php

            }

            ?>

          }

        });

      }

      //  loadGoodGroup();
      $(document).ready(function() {



        $('#addNewGoodGroupFormSubmitBtn').click(function(e) {

          //  $(document).on('submit', '#addNewGoodGroupForm', function(event) {

          event.preventDefault();

          let formData = $("#addNewGoodGroupForm").serialize();
          // console.log(formData);
          $.ajax({

            type: "POST",

            url: `ajaxs/items/ajax-good-groups.php`,

            data: formData,

            beforeSend: function() {

              $("#addNewGoodGroupFormSubmitBtn").toggleClass("disabled");

              $("#addNewGoodGroupFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

            },

            success: function(response) {
              console.log(response);
              $("#goodGroupDropDown").html(response);

              $('#addNewGoodsGroupForm').trigger("reset");

              // $("#addNewGoodGroupFormModal").modal('toggle');

              $("#addNewGoodGroupFormSubmitBtn").html("Submit");

              $("#addNewGoodGroupFormSubmitBtn").toggleClass("disabled");

              $(".addNewGoodGroupFormModal").hide();


              // $("#goodGroupDropDown").html(response);

              // $('#addNewGoodGroupForm').trigger("reset");

              // $("#addNewGoodGroupFormModal").modal('toggle');

              // $("#addNewGoodGroupFormSubmitBtn").html("Submit");

              // $("#addNewGoodGroupFormSubmitBtn").toggleClass("disabled");


            }

          });

        });
      });


    });







    //**************************************************************

    $('#goodGroupDropDown')

      .select2()

      .on('select2:open', () => {

        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewGoodGroupFormModal">Add New</a></div>`);

      });

    $('#asset_classificationDropDown')

      .select2()

      .on('select2:open', () => {

        $(".select2-results:not(:has(a))").append(``);

      });





    $('#buomDrop')

      .select2()

      .on('select2:open', () => {

        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewUOMFormModal">Add New</a></div>`);

      });


    $('#iuomDrop')

      .select2()

      .on('select2:open', () => {

        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewUOMFormModal">Add New</a></div>`);

      });




    $('#serviceUnitDrop')


      .select2()

      .on('select2:open', () => {

        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewServiceUOMFormModal">Add New</a></div>`);

      });




    // $('#hsnDropDown')

    //   .select2()

    //   .on('select2:open', () => {

    //     $(".select2-results:not(:has(a))").append(`<div class="col-md-12 mb-12"></div>`);

    //   });





    $('#purchaseGroupDropDown')

      .select2()

      .on('select2:open', () => {

        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewPurchaseGroupFormModal">Add New</a></div>`);

      });

  });
</script>

<script>
  function leaveInput(el) {

    if (el.value.length > 0) {

      if (!el.classList.contains('active')) {

        el.classList.add('active');

      }

    } else {

      if (el.classList.contains('active')) {

        el.classList.remove('active');

      }

    }

  }



  var inputs = document.getElementsByClassName("form-control");

  for (var i = 0; i < inputs.length; i++) {

    var el = inputs[i];

    el.addEventListener("blur", function() {

      leaveInput(this);

    });

  }



  // *** autocomplite select *** //

  wow = new WOW({

    boxClass: 'wow', // default

    animateClass: 'animated', // default

    offset: 0, // default

    mobile: true, // default

    live: true // default

  })

  wow.init();
</script>



<script>
  $(document).ready(function() {

    function loadGoodTypes() {

      $.ajax({

        type: "GET",

        url: `ajaxs/items/ajax-good-types.php`,

        beforeSend: function() {

          $("#goodTypeDropDown").html(`<option value="">Loding...</option>`);

        },

        success: function(response) {

          $("#goodTypeDropDown").html(response);



          <?php

          if (isset($row["goodTypeId"])) {

          ?>

            $(`#goodTypeDropDown option[value=<?= $row["goodTypeId"] ?>]`).attr('selected', 'selected');

          <?php

          }

          ?>

        }

      });

    }

    loadGoodTypes();

    $(document).on('submit', '#addNewGoodTypesForm', function(event) {

      event.preventDefault();

      let formData = $("#addNewGoodTypesForm").serialize();

      $.ajax({

        type: "POST",

        url: `ajaxs/items/ajax-good-types.php`,

        data: formData,

        beforeSend: function() {

          $("#addNewGoodTypesFormSubmitBtn").toggleClass("disabled");

          $("#addNewGoodTypesFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

        },

        success: function(response) {

          $("#goodTypeDropDown").html(response);

          $('#addNewGoodTypesForm').trigger("reset");

          $("#addNewGoodTypesFormModal").modal('toggle');

          $("#addNewGoodTypesFormSubmitBtn").html("Submit");

          $("#addNewGoodTypesFormSubmitBtn").toggleClass("disabled");

        }

      });

    });

    // $(document).on("change", ".goodTypeDropDown", function() {
    // //  alert(1);
    //   let typeId = $(this).val();
    //   loadGoodGroup(typeId);
    //   load_group_modal(typeId);

    // });

















    function loadhsn(pageNo, limit, keyword = null) {
      $.ajax({
        method: 'POST',
        data: {
          pageNo: pageNo,
          limit: limit,
          keyword: keyword,
        },
        url: `ajaxs/items/ajax-hsn.php`,
        beforeSend: function() {
          $(".hsnSearchSpinner").show();
          $(".hsn_tbody").html('<tr><td colspan="4"><span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Loading ...</td></tr>');
        },
        success: function(response) {
          $(".hsn_tbody").html(response);
          $(".hsnSearchSpinner").hide();

        }

      });

    }

    $(document).ready(function() {

      $('#searchValue').on('click', function() {
        console.log("clickked......")
        $('.add-new-hsn').toggleClass("show");
      });
    })

    loadhsn(0, 50);

    $(document).ready(function() {
      $(".hsnSearchSpinner").hide();
      $('#searchbar').on('keyup keydown paste', function() {
        var keyword = $(this).val();
        var pageNo = 0;
        var limit = 50;
        loadhsn(pageNo, limit, keyword);
      });
    });

    $(document).on('submit', '#addNewhsnForm', function(event) {

      event.preventDefault();

      let formData = $("#addNewhsnForm").serialize();

      $.ajax({

        type: "POST",

        url: `ajaxs/items/ajax-hsn.php`,

        data: formData,

        beforeSend: function() {

          $("#addNewhsnFormSubmitBtn").toggleClass("disabled");

          $("#addNewhsnFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

        },

        success: function(response) {

          $("#hsnDropDown").html(response);

          $('#addNewhsnForm').trigger("reset");

          $("#addNewhsnFormModal").modal('toggle');

          $("#addNewhsnFormSubmitBtn").html("Submit");

          $("#addNewhsnFormSubmitBtn").toggleClass("disabled");

        }

      });

    });



    function loadPurchaseGroup() {

      $.ajax({

        type: "GET",

        url: `ajaxs/items/ajax-purchase-groups.php`,

        beforeSend: function() {

          $("#purchaseGroupDropDown").html(`<option value="">Loding...</option>`);

        },

        success: function(response) {

          $("#purchaseGroupDropDown").html(response);

          <?php

          if (isset($row["purchaseGroupId"])) {

          ?>

            $(`#purchaseGroupDropDown option[value=<?= $row["purchaseGroupId"] ?>]`).attr('selected', 'selected');

          <?php

          }

          ?>

        }

      });

    }

    loadPurchaseGroup();


    $(document).ready(function() {

      /*@ Registration start */
      $('#addNewPurchaseGroupFormSubmitBtn').click(function(e) {


        //  $(document).on('submit', '#addNewPurchaseGroupForm', function(event) {

        event.preventDefault();

        let formData = $("#addNewPurchaseGroupForm").serialize();

        // console.log(formData);
        $.ajax({

          type: "POST",

          url: `ajaxs/items/ajax-purchase-groups.php`,

          data: formData,

          beforeSend: function() {

            $("#addNewPurchaseGroupFormSubmitBtn").toggleClass("disabled");

            $("#addNewPurchaseGroupFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

          },

          success: function(response) {

            // $("#purchaseGroupDropDown").html(response);

            // $('#addNewPurchaseGroupForm').trigger("reset");


            // // $("#addNewPurchaseGroupFormModal").modal('toggle');

            // $("#addNewPurchaseGroupFormSubmitBtn").html("Submit");

            // $("#addNewPurchaseGroupFormSubmitBtn").toggleClass("disabled");
            // ("#addNewPurchaseGroupFormModal").hide();

            $("#purchaseGroupDropDown").html(response);

            $('#addNewPurchaseGroupForm').trigger("reset");

            // $("#addNewGoodGroupFormModal").modal('toggle');

            $("#addNewPurchaseGroupFormSubmitBtn").html("Submit");

            $("#addNewPurchaseGroupFormSubmitBtn").toggleClass("disabled");

            $(".addNewPurchaseGroupFormModal").hide();

          }

        });

      });
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





    $(".add_data").click(function() {

      var data = this.value;

      $("#creategoodsdata").val(data);

      //confirm('Are you sure to Submit?')

      $("#goodsSubmitForm").submit();

    });





    $(".edit_data").click(function() {

      var data = this.value;

      $("#editgoodsdata").val(data);

      //confirm('Are you sure to Submit?')

      $("#goodsEditForm").submit();

    });





    //volume calculation

    function calculate_volume() {


      let height = $("#height").val();

      let width = $("#width").val();

      let length = $("#length").val();
      let vol_unit = $(".volume_unit").val();
      //console.log(vol_unit);
      if (vol_unit == "m") {


        let resm = height * length * width;

        let res = resm * 1000000;

        $("#volcm").val(res);

        $("#volm").val(resm);

      } else {

        let res = height * length * width;

        let resm = res * 0.000001;
        $("#volcm").val(res);

        $("#volm").val(resm);
      }


      //console.log(res);

      // $("#volcm").val(res);

      // $("#volm").val(resm);





    }



    // $(document).on("keyup", ".calculate_volume", function(){

    //  calculate_volume();

    // });



    $("#height").keyup(function() {

      calculate_volume();

    });

    $("#width").keyup(function() {

      calculate_volume();

    });

    $("#length").keyup(function() {

      calculate_volume();

    });






    function calculate_amount() {


      let stock = $("#stock").val();

      let rate = $("#rate").val();



      let res = stock * rate;

      $("#total").val(res);





      //console.log(res);

      // $("#volcm").val(res);

      // $("#volm").val(resm);





    }


    $("#stock").keyup(function() {

      calculate_amount();

    });

    $("#rate").keyup(function() {

      calculate_amount();

    });



    $(".volume_unit").change(function() {
      let vol_unit = $(".volume_unit").val();
      console.log(vol_unit);
      calculate_volume();

    });


    function compare() {


      let gross = $("#gross_weight").val();
      let net = $("#net_weight").val();

      if (Number(gross) < Number(net)) {
        $("#gross_span").html(`<span class="text-danger text-xs" id="gross_span">Gross weight can not Be lesser than net weight</small></span>`);



      } else {
        $("#gross_span").html("");
      }


    }

    $("#gross_weight").keyup(function() {

      compare();

    });

    $("#net_weight").keyup(function() {

      compare();

    });

    $("#gross_weight").keyup(function() {

      compare();

    });



    $("#buomDrop").change(function() {

      // let res = $(this).html();

      let res = $(this).find(":selected").text();

      $("#buom").val(res);
      $("#buom_per").html('<label id="buom_per">/' + res + '<label>')

      console.log("buomDrop", res);

    });



    $("#iuomDrop").change(function() {

      // let rel = $(this).html();

      let rel = $(this).find(":selected").text();

      $("#ioum").val(rel);

      console.log("iuomDrop", rel);

    });



    $("#goodGroupDropDown").select2({

      customClass: "Myselectbox",

    });

  });

  $('#minTime').change(function() {
    $("#maxTime option").eq($(this).find(':selected').index()).prop('selected', true);
  });
  $('#net_unit').change(function() {
    $("#gross_unit option").eq($(this).find(':selected').index()).prop('selected', true);
  });


  $(".goodsHSNModalCls").click(function() {
    $(".goodsHSNModal").modal('show');

  });
  $("#hsnsavebtn").click(function() {

    //console.log("clickinggggggggg");
    let radioBtnVal = $('input[name="hsn"]:checked').val();
    let hsncode = ($(`#hsnCode_${radioBtnVal}`).html());
    let hsndesc = ($(`#hsnDescription_${radioBtnVal}`).html());
    // console.log(hsndesc);
    // let hsnpercentage = ($(`#taxPercentage_${radioBtnVal}`).html()).trim();
    //salert(radioBtnVal);
    $("#hsnlabelOne").html(radioBtnVal);
    $("#hsnlabelservice").html(radioBtnVal);


    $("#hsnDescInfo").html(hsndesc);

    $("#hsnlabelOne2").html(radioBtnVal);
    $("#hsnlabelservice2").html(radioBtnVal);
    $("#hsnDescInfo2").html(hsndesc);

    // alert();
    $(".goodsHSNModal").modal('toggle');

  });




  $(document).on("click", "#tdssavebtn", function() {



    //console.log("clickinggggggggg");
    let radioBtnVal = $('input[name="tds"]:checked').val();
    let sec = $('input[name="tds"]:checked').attr("data-attr");
    //console.log(sec);
    let section = ($(`#section_${radioBtnVal}`).html());
    // let hsndesc = ($(`#hsnDescription_${radioBtnVal}`).html()).trim();
    // let hsnpercentage = ($(`#taxPercentage_${radioBtnVal}`).html()).trim();
    console.log(radioBtnVal);
    $("#tdslabel").html(sec);

  });





  //uom add


  $('#addNewUOMFormSubmitBtn').click(function(e) {
    //alert(1);

    //  $(document).on('submit', '#addNewGoodGroupForm', function(event) {

    event.preventDefault();

    let formData = $("#addNewUOMForm").serialize();

    // alert(formData);



    $.ajax({

      type: "POST",

      data: formData,

      url: `ajaxs/items/ajax-uom.php`,




      beforeSend: function() {

        $("#addNewUOMFormSubmitBtn").toggleClass("disabled");

        $("#addNewUOMFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

      },

      success: function(response) {
        // alert(data);
        // alert(response);
        console.log(response);
        $("#buomDrop").html(response);
        $("#iuomDrop").html(response);

        $('.UOMName').val('');
        $('.UOMDesc').val('');

        //$("#addNewUOMFormModal").modal('toggle');

        $("#addNewUOMFormSubmitBtn").html("Submit");

        $("#addNewUOMFormSubmitBtn").toggleClass("disabled");
        $('.addNewUOM').hide();


      }

    });

  });


  //end uom

  //service uom

  $('#addNewServiceUOMFormSubmitBtn').click(function(e) {

    //  $(document).on('submit', '#addNewGoodGroupForm', function(event) {

    event.preventDefault();

    let formData = $("#addNewServiceUOMForm").serialize();
    // alert(formData);

    $.ajax({

      type: "POST",

      data: formData,

      url: `ajaxs/items/ajax-service-uom.php`,




      beforeSend: function() {

        $("#addNewServiceUOMFormSubmitBtn").toggleClass("disabled");

        $("#addNewServiceUOMFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

      },

      success: function(response) {
        // alert(data);
        // alert(response);
        console.log(response);
        $("#serviceUnitDrop").html(response);



        //$("#addNewUOMFormModal").modal('toggle');

        $("#addNewServiceUOMFormSubmitBtn").html("Submit");

        $("#addNewServiceUOMFormSubmitBtn").toggleClass("disabled");
        $('.addServiceNewUOM').hide();


      }

    });

  });

  //end service uom
  function addMultiQtyf(id) {
    let addressRandNo = Math.ceil(Math.random() * 100000);
    $(`.modal-add-row_${id}`).append(`
                              <div class="row othe-cost-infor">

                                <div class="col-lg-3 col-md-3 col-sm-3">

                                  <div class="form-input py-0">

                                    <label>Specification</label>

                                    <input type="text" name="spec[${addressRandNo}][spec_name]" data-attr="${addressRandNo}" class="form-control spec_vldtn specification_${addressRandNo}" id="" placeholder="Name">

                                  </div>

                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-8">

                                  <div class="form-input py-0">

                                    <label>Specification Details</label>

                                    <input type="text" name="spec[${addressRandNo}][spec_detail]" data-attr="${addressRandNo}" class="form-control spec_dtls_vldtn specificationDetails_${addressRandNo}" id="" placeholder="Description">

                                  </div>

                                </div>



                                <div class="col-lg-1 col-md-1 col-sm-1">
                                                                    <div class="add-btn-minus justify-content-end">
                                                                        <a style="cursor: pointer" class="btn btn-danger">
                                                                            <i class="fa fa-minus"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                              </div>
                           `);
  }



  $(document).on("click", ".add-btn-minus", function() {
    $(this).parent().parent().remove();
  });



  // function search_hsn() {




  //   let input = document.getElementById('searchbar').value
  //   input = input.toLowerCase();
  //   let x = document.getElementsByClassName('hsn-code');

  //   for (i = 0; i < x.length; i++) {
  //     if (!x[i].innerHTML.toLowerCase().includes(input)) {
  //       console.log(0);
  //       x[i].style.display = "none";
  //     } else {
  //       console.log(1);
  //       x[i].style.display = "block";
  //     }
  //   }
  // }



  $(document).on("keyup", ".form-control-sm", function() {
    // alert(1);
    var search_term = $('#searchbar').val();
    console.log(search_term)
    $('.hsn-code').removeHighlight().highlight(search_term);
  });
</script>

<script>
  $('#DataTables_Table_0').dataTable({
    "filter": true,
    "length": false
  });
</script>
<script>
  $('.item_name').keyup(function() {
    $("#suggestedNames").slideToggle(200);
    // $("#suggestedNames").css("padding", "15px 20px");
    // {alert('oi');}
    var item_name = $(this).val();


    //alert(item_name);
    $.ajax({

      type: "GET",
      url: `ajaxs/items/ajax-suggestions.php`,
      data: {
        item_name: item_name
      },
      beforeSend: function() {

        // $("#glCode").html(`<option value="">Loding...</option>`);

      },

      success: function(response) {
        console.log(response);
        var obj = jQuery.parseJSON(response);
        //alert(obj['duplicate']);
        if (obj['duplicate'] == 1) {
          document.getElementById("submit_btn").disabled = true;
          $("#duplicate-name-error").html('Duplicate item name');
        } else {
          document.getElementById("submit_btn").disabled = false;
          $("#duplicate-name-error").html('');
        }
        $("#suggestedNames").html(obj['item_sugg']);

      }
    })
  });


  $('.item_name').keydown(function() {
    // var padding = 10;
    // $("#suggestedNames").css("padding","0");

    // {alert('oi');}
    var item_name = $(this).val();

    $("#suggestedNames").css("padding", "0", "display", "none");
    //alert(item_name);
    $.ajax({

      type: "GET",
      url: `ajaxs/items/ajax-suggestions.php`,
      data: {
        item_name: item_name
      },
      beforeSend: function() {

        // $("#glCode").html(`<option value="">Loding...</option>`);

      },

      success: function(response) {
        console.log(response);
        var obj = jQuery.parseJSON(response);
        if (obj['duplicate'] == 1) {
          document.getElementById("submit_btn").disabled = true;
          $("#duplicate-name-error").html('Duplicate item name');
        } else {
          document.getElementById("submit_btn").disabled = false;
          $("#duplicate-name-error").html('');
        }
        $("#suggestedNames").html(obj['item_sugg']);

      }
    })
  });
</script>

<!-- Note that this code snippet uses AJAX to retrieve the suggested names from the PHP script without reloading the page. -->

<script>
  // function HSNfunction(){
  //   console.log('oi');
  //   var hsnSearch = $("#hsnSearch").val();
  //  // alert(hsnSearch);
  // $("#hsnName").val(hsnSearch);
  // //  document.getElementById("hsnAdd").showModal(); 
  //  $("#hsnAdd").modal('toggle');
  // }


  // $(document).on("click", "#searchValue", function(e) {
  //   e.preventDefault();
  //   // alert(1);
  //   console.log('oi')
  //   var hsnSearch = $("#hsnSearch").val();
  //   $("#hsnName").val(hsnSearch);
  //   $("#hsnAdd").modal('show');


  // });

  $(document).on("click", '#addNewHSNFormSubmitBtn', function(e) {
    //alert(1);
    //  $(document).on('submit', '#addNewGoodGroupForm', function(event) {

    event.preventDefault();

    var code = $("#hsnName").val();
    var desc = $("#hsnDesc").val();
    var rate = $("#hsnRate").val();
    var public = $("#hsnPublic").val();

    alert(public);
    //console.log(formData);
    $.ajax({

      type: "POST",

      data: {
        hsnCode: code,
        hsnDesc: desc,
        hsnRate: rate,
        hsnPublic: public
      },

      url: `ajaxs/items/ajax-hsn-submit.php`,




      beforeSend: function() {

        $("#addNewHSNFormSubmitBtn").toggleClass("disabled");

        $("#addNewHSNFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

      },

      success: function(response) {
        console.log(response);
        // $("#buomDrop").html(response);
        // $("#iuomDrop").html(response);

        // $('.UOMName').val('');
        // $('.UOMDesc').val('');

        //$("#addNewUOMFormModal").modal('toggle');

        $("#addNewHSNFormSubmitBtn").html("Submit");

        $("#addNewHSNFormSubmitBtn").toggleClass("disabled");
        $('#hsnAdd').hide();


      }

    });

  });

  function calculate_service_amount() {


    let stock = $("#service_stock").val();

    let rate = $("#service_rate").val();



    let res = stock * rate;

    $("#service_total").val(res);


  }

  $("#service_stock").keyup(function() {

    calculate_service_amount();

  });

  $("#service_rate").keyup(function() {

    calculate_service_amount();

  });



  var link = document.getElementById("my-link");

  link.addEventListener("click", function(event) {
    if (link.getAttribute("href") === "#") {
      event.preventDefault(); // prevent default action of navigating to #
    }
  });

  //   $("#asset_classificationDropDown").change(function() {
  // console.log(1);
  // // let dataAttrVal = $("#asset_classificationDropDown").find(':selected').val();
  // // alert(dataAttrVal);



  //   }); 
</script>

<script>
  // Function to show the Bootstrap modal
  function showModal() {
    $('#treeModal').modal('show');
  }

  // Event listener for the opening of the Select2 dropdown
  $('#goodGroupDropDown').on('select2:open', showModal);
</script>



<script>
  $(document).ready(function() {

    function addAssetClll(val, valclass) {

      $.ajax({

        type: "GET",

        url: `ajaxs/items/ajax-asset-classification.php`,
        data: {
          val
        },


        beforeSend: function() {

          $(`.${valclass}`).html("");

        },

        success: function(response) {
          //  console.log(response);
          $(`.${valclass}`).show();
          $(`.${valclass}`).append(response);

        }


      });
    }

    function checkdepkey(val) {


      $.ajax({

        type: "GET",

        url: `ajaxs/items/ajax-asset-classification.php`,
        data: {
          val: val,
          act: "key"
        },


        beforeSend: function() {

          // $(`.${valclass}`).html("");

        },

        success: function(response) {
          //console.log(response);
          $("#despkey_id").html(response);
          $("#dep_key_val").val(response);



        }


      });


    }


    $(document).on("change", ".asset_classificationDropDown", function() {



      let valclass = $(this).data('classattr');
      // alert(valclass);
      $(`.${valclass}`).html("");
      let val = $(this).val();
      addAssetClll(val, valclass);
      checkdepkey(val);

    });



  });



  function GroupChild(val, valclass) {



    $.ajax({

      type: "GET",

      url: `ajaxs/items/ajax-group-child.php`,
      data: {
        val: val,
        act: "key"
      },


      beforeSend: function() {

        // $(`.${valclass}`).html("");

      },

      success: function(response) {
        // alert(response);
        $(`.${valclass}`).html(response);


      }

    });


  }
  $(document).on("change", ".goodGroupDropDown", function() {

    let valclass = $(this).data('classattr');
    $(`.${valclass}`).html("");
    let val = $(this).val();
    GroupChild(val, valclass)

  });



  var DragAndDrop = (function(DragAndDrop) {
    function shouldAcceptDrop(item) {
      var $target = $(this).closest("li");
      var $item = item.closest("li");

      if ($.contains($item[0], $target[0])) {
        // can't drop on one of your children!
        return false;
      }

      return true;
    }

    function itemOver(event, ui) {}

    function itemOut(event, ui) {}

    function itemDropped(event, ui) {
      var $target = $(this).closest("li");
      var $item = ui.draggable.closest("li");

      var $srcUL = $item.parent("ul");
      var $dstUL = $target.children("ul").first();

      // destination may not have a UL yet
      if ($dstUL.length == 0) {
        $dstUL = $("<ul></ul>");
        $target.append($dstUL);
      }

      $item.slideUp(50, function() {
        $dstUL.append($item);

        if ($srcUL.children("li").length == 0) {
          $srcUL.remove();
        }

        $item.slideDown(50, function() {
          $item.css("display", "");
        });
      });
    }

    DragAndDrop.enable = function(selector) {
      $(selector).find(".node-cpe").draggable({
        helper: "clone"
      });

      $(selector).find(".node-cpe, .node-facility").droppable({
        activeClass: "active",
        hoverClass: "hover",
        accept: shouldAcceptDrop,
        over: itemOver,
        out: itemOut,
        drop: itemDropped,
        greedy: true,
        tolerance: "pointer"
      });
    };

    return DragAndDrop;
  })(DragAndDrop || {});

  (function($) {
    $.fn.beginEditing = function(whenDone) {
      if (!whenDone) {
        whenDone = function() {};
      }

      var $node = this;
      // var $editor = $(
      //   "<input type='text' class='form-control'>"
      // );
      var currentValue = $node.text();

      function commit() {
        $editor.remove();
        $node.text($editor.val());
        whenDone($node);
      }

      function cancel() {
        $editor.remove();
        $node.text(currentValue);
        whenDone($node);
      }

      $editor.val(currentValue);
      $editor.blur(function() {
        commit();
      });
      $editor.keydown(function(event) {
        if (event.which == 27) {
          cancel();
          return false;
        } else if (event.which == 13) {
          commit();
          return false;
        }
      });

      $node.empty();
      $node.append($editor);
      $editor.focus();
      $editor.select();
    };
  })(jQuery);

  $(function() {
    DragAndDrop.enable("#dragRoot");

    $(document).on("dblclick", "#dragRoot *[class*=node]", function() {
      $(this).beginEditing();
    });
  });

  $(document).ready(function() {
    // Function to toggle the sidebar
    function toggleSidebar() {
      $('#goodGroupSidebar').toggleClass('active');
    }

    // Event listener for the button click to toggle the sidebar
    $('#addTreeSidebarBtn').on('click', function() {
      toggleSidebar();
    });
  });
</script>




<script>
  $(document).ready(function() {
    // Get references to the important elements
    const $searchInput = $("#searchbar_tds");
    const $tableRows = $("#data-table tr");

    // Add event listener for keyup on search input
    $searchInput.on("keyup", function() {
      // alert(1);
      const searchText = $(this).val().toLowerCase(); // Get search input value

      // Loop through each table row and show/hide based on search input
      $tableRows.each(function() {
        const $row = $(this);

        const rowData = $row.text().toLowerCase(); // Get row text content
        //alert(rowData);

        // Check if the search text is present in the row data
        if (rowData.includes(searchText)) {
          //alert(0);

          $row.show();
        } else {
          // alert(1);
          $row.hide();
        }
      });
    });
  });
</script>

<script src="../../public/assets/comboTreePlugin.js"></script>


<script type="text/javascript">
  var SampleJSONData = [{
    id: 0,
    title: 'Horse'
  }, {
    id: 1,
    title: 'Birds',
    isSelectable: false,
    subs: [{
      id: 10,
      title: 'Pigeon',
      isSelectable: false
    }, {
      id: 11,
      title: 'Parrot'
    }, {
      id: 12,
      title: 'Owl'
    }, {
      id: 13,
      title: 'Falcon'
    }]
  }, {
    id: 2,
    title: 'Rabbit'
  }, {
    id: 3,
    title: 'Fox'
  }, {
    id: 5,
    title: 'Cats',
    subs: [{
      id: 50,
      title: 'Kitty'
    }, {
      id: 51,
      title: 'Bigs',
      subs: [{
        id: 510,
        title: 'Cheetah'
      }, {
        id: 511,
        title: 'Jaguar'
      }, {
        id: 512,
        title: 'Leopard'
      }]
    }]
  }, {
    id: 6,
    title: 'Fish'
  }];
  var SampleJSONData2 = [{
    id: 1,
    title: 'Four Wheels',
    subs: [{
      id: 10,
      title: 'Car'
    }, {
      id: 11,
      title: 'Truck'
    }, {
      id: 12,
      title: 'Transporter'
    }, {
      id: 13,
      title: 'Dozer'
    }]
  }, {
    id: 2,
    title: 'Two Wheels',
    subs: [{
      id: 20,
      title: 'Cycle'
    }, {
      id: 21,
      title: 'Motorbike'
    }, {
      id: 22,
      title: 'Scooter'
    }]
  }, {
    id: 2,
    title: 'Van'
  }, {
    id: 3,
    title: 'Bus'
  }];


  var comboTree1, comboTree2;

  jQuery(document).ready(function($) {

    comboTree1 = $('#justAnInputBox').comboTree({
      source: SampleJSONData,
      isMultiple: true,
      cascadeSelect: false,
      collapse: true,
      selectableLastNode: true,
      withSelectAll: true
    });

    comboTree3 = $('#justAnInputBox1').comboTree({
      source: SampleJSONData,
      isMultiple: true,
      cascadeSelect: true,
      collapse: false
    });

    comboTree3.setSource(SampleJSONData2);


    comboTree2 = $('#justAnotherInputBox').comboTree({
      source: SampleJSONData,
      isMultiple: false
    });
  });
</script>
<!-- <script>
   $(document).on("submit", "#goodsSubmitForm", function (e) {
    alert(1);
   });
</script> -->

<script>
  // ====================================== Real Time Data Sorting ======================================
  am4core.ready(function() {

    // Themes begin
    am4core.useTheme(am4themes_animated);
    // Themes end

    var chart = am4core.create("chartDivRealTimeDataSort", am4charts.XYChart);
    chart.logo.disabled = true;

    chart.data = [{
        "country": "USA",
        "visits": 2025
      },
      {
        "country": "China",
        "visits": 1882
      },
      {
        "country": "Japan",
        "visits": 1809
      },
      {
        "country": "Germany",
        "visits": 1322
      },
      {
        "country": "UK",
        "visits": 1122
      },
      {
        "country": "France",
        "visits": 1114
      },
      {
        "country": "India",
        "visits": 984
      },
      {
        "country": "Spain",
        "visits": 711
      },
      {
        "country": "Netherlands",
        "visits": 665
      },
      {
        "country": "Russia",
        "visits": 580
      },
      {
        "country": "South Korea",
        "visits": 443
      },
      {
        "country": "Canada",
        "visits": 441
      }
    ];

    chart.padding(40, 40, 40, 40);

    var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.renderer.grid.template.location = 0;
    categoryAxis.dataFields.category = "country";
    categoryAxis.renderer.minGridDistance = 60;
    categoryAxis.renderer.inversed = true;
    categoryAxis.renderer.grid.template.disabled = true;

    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.min = 0;
    valueAxis.extraMax = 0.1;
    //valueAxis.rangeChangeEasing = am4core.ease.linear;
    //valueAxis.rangeChangeDuration = 1500;

    var series = chart.series.push(new am4charts.ColumnSeries());
    series.dataFields.categoryX = "country";
    series.dataFields.valueY = "visits";
    series.tooltipText = "{valueY.value}"
    series.columns.template.strokeOpacity = 0;
    series.columns.template.column.cornerRadiusTopRight = 10;
    series.columns.template.column.cornerRadiusTopLeft = 10;
    //series.interpolationDuration = 1500;
    //series.interpolationEasing = am4core.ease.linear;
    var labelBullet = series.bullets.push(new am4charts.LabelBullet());
    labelBullet.label.verticalCenter = "bottom";
    labelBullet.label.dy = -10;
    labelBullet.label.text = "{values.valueY.workingValue.formatNumber('#.')}";

    chart.zoomOutButton.disabled = true;

    // as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
    series.columns.template.adapter.add("fill", function(fill, target) {
      return chart.colors.getIndex(target.dataItem.index);
    });

    setInterval(function() {
      am4core.array.each(chart.data, function(item) {
        item.visits += Math.round(Math.random() * 200 - 100);
        item.visits = Math.abs(item.visits);
      })
      chart.invalidateRawData();
    }, 2000)

    categoryAxis.sortBySeries = series;

  });
  // ++++++++++++++++++++++++++++++++++++++ Real Time Data Sorting ++++++++++++++++++++++++++++++++++++++
</script>


<script>
  // Define a function to initialize the CheckboxDropdown

  function initializeCheckboxDropdown() {
    var CheckboxDropdown = function(el) {
      var _this = this;
      this.isOpen = false;
      this.areAllChecked = false;
      this.$el = $(el);
      this.$label = this.$el.find(".dropdown-label");
      this.$checkAll = this.$el.find('[data-toggle="check-all"]').first();
      this.$inputs = this.$el.find('[type="checkbox"]');

      this.onCheckBox();

      this.$label.on("click", function(e) {
        e.preventDefault();
        _this.toggleOpen();
      });

      this.$checkAll.on("click", function(e) {
        e.preventDefault();
        _this.onCheckAll();
      });

      this.$inputs.on("change", function(e) {
        _this.onCheckBox();
      });
    };

    CheckboxDropdown.prototype.onCheckBox = function() {
      this.updateStatus();
    };

    CheckboxDropdown.prototype.updateStatus = function() {
      var checked = this.$el.find(":checked");

      this.areAllChecked = false;
      this.$checkAll.html("Check All");

      if (checked.length <= 0) {
        this.$label.html("Select Discount Group");
      } else if (checked.length === 1) {
        this.$label.html(checked.parent("label").text());
      } else if (checked.length === this.$inputs.length) {
        this.$label.html("All Selected");
        this.areAllChecked = true;
        this.$checkAll.html("Uncheck All");
      } else {
        this.$label.html(checked.length + " Selected");
      }
    };

    CheckboxDropdown.prototype.onCheckAll = function(checkAll) {
      if (!this.areAllChecked || checkAll) {
        this.areAllChecked = true;
        this.$checkAll.html("Uncheck All");
        this.$inputs.prop("checked", true);
      } else {
        this.areAllChecked = false;
        this.$checkAll.html("Check All");
        this.$inputs.prop("checked", false);
      }

      this.updateStatus();
    };

    CheckboxDropdown.prototype.toggleOpen = function(forceOpen) {
      var _this = this;

      if (!this.isOpen || forceOpen) {
        this.isOpen = true;
        this.$el.addClass("on");
        $(document).on("click", function(e) {
          if (!$(e.target).closest("[data-control]").length) {
            _this.toggleOpen();
          }
        });
      } else {
        this.isOpen = false;
        this.$el.removeClass("on");
        $(document).off("click");
      }
    };

    // Apply CheckboxDropdown to elements with the attribute 'data-control="checkbox-dropdown"'
    var checkboxesDropdowns = document.querySelectorAll(
      '[data-control="checkbox-dropdown"]'
    );
    for (var i = 0, length = checkboxesDropdowns.length; i < length; i++) {
      new CheckboxDropdown(checkboxesDropdowns[i]);
    }
  }



  // Call the function to initialize CheckboxDropdown initially
  initializeCheckboxDropdown();
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>

<script src="<?= BASE_URL; ?>public/validations/goodsValidation.js"></script>
<!-- <script src="https://johannburkard.de/resources/Johann/jquery.highlight-4.js"></script> -->