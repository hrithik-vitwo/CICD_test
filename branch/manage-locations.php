<?php
require_once("../app/v1/connection-branch-admin.php");
administratorAuth();
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar.php");
require_once("common/pagination.php");
require_once("../app/v1/functions/branch/func-others-location.php");

if (isset($_POST["visit_location"])) {
  $newStatusObj = VisitLocation($_POST);
  if ($newStatusObj["status"] == "success") {
    redirect(LOCATION_URL);
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
  } else {
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
  }
}

 $countrycode = $_SESSION['visitCompanyAdminInfo']['companyCountry'];
//edit loc

if (isset($_POST['edit_branch_location'])) {

  $companyId = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
  $branchId = $_SESSION['logedBranchAdminInfo']['fldAdminBranchId'];
  $location_id = $_POST['location_id'];

  $locationName = $_POST['locationName'];
  $flatNo = $_POST['flatNo'];
  $buildingNo = $_POST['buildingNo'];
  $streetName = $_POST['streetName'];
  $pinCode = $_POST['pinCode'];
  $location = $_POST['location'];
  $city = $_POST['city'];
  $district = $_POST['district'];
  $state = $_POST['state'];

  $lat = $_POST['lat'];
  $lng = $_POST['lng'];


  $compFunc = implode(',', $_POST['compFunc']);

  $adminName = $_POST['adminName'];
  $adminEmail = $_POST['adminEmail'];
  $adminPhone = $_POST['adminPhone'];
  $adminPassword = $_POST['adminPassword'];
  $adminDesignation = $_POST['designation'];
  $adminUserName = $_POST['userName'];

  $admin_id = $_POST['admin_location_id'];

  $locationInsert = queryUpdate("UPDATE `erp_branch_otherslocation` 
                        SET 
                           
                            `othersLocation_primary_flag`='0',
                            `companyFunctionalities`='$compFunc',
                            `othersLocation_name`='$locationName',
                            `othersLocation_building_no`='$buildingNo',
                            `othersLocation_flat_no`='$flatNo',
                            `othersLocation_street_name`='$streetName',
                            `othersLocation_pin_code`='$pinCode',
                            `othersLocation_location`='$location',
                            `othersLocation_city`='$city',
                            `othersLocation_district`='$district',
                            `othersLocation_lat`='$lat',
                            `othersLocation_lng`='$lng',
                            `othersLocation_state`='$state' WHERE `othersLocation_id` = $location_id");

  //  console($locationInsert);
  $check_admin = queryGet("SELECT * FROM  `tbl_branch_admin_details` WHERE `fldAdminKey`=$admin_id");
  if ($check_admin['numRows'] > 0) {

    $ins = queryUpdate("UPDATE `tbl_branch_admin_details` 
                                SET 
                                  `fldAdminCompanyId`='$companyId',
                                  `fldAdminBranchId`='$branchId',
                                  `fldAdminBranchLocationId`='$location_id',
                                  `fldAdminName`='$adminName',
                                  `fldAdminEmail`='$adminEmail',
                                  `fldAdminPhone`='$adminPhone',
                                  `fldAdminPassword`='$adminPassword',
                                  `fldAdminUserName` = '$adminUserName',
                                  `flAdminDesignation` = '$adminDesignation',
                                  `fldAdminRole`='2' WHERE `fldAdminKey`=$admin_id");
  } else {
    $ins = queryGet("INSERT INTO `tbl_branch_admin_details` 
                                SET 
                                  `fldAdminCompanyId`='$companyId',
                                  `fldAdminBranchId`='$branchId',
                                  `fldAdminBranchLocationId`='$location_id',
                                  `fldAdminName`='$adminName',
                                  `fldAdminEmail`='$adminEmail',
                                  `fldAdminPhone`='$adminPhone',
                                  `fldAdminPassword`='$adminPassword',
                                  `fldAdminUserName` = '$adminUserName',
                                  `flAdminDesignation` = '$adminDesignation',
                                  `fldAdminRole`='2'");
  }
  //  console($ins);
  //exit();
  if ($locationInsert['status'] == "success") {
    swalAlert($locationInsert['status'], ucfirst($locationInsert['status']), 'Updated Successfully', BASE_URL . "branch/manage-locations.php");
  } else {
    swalAlert($locationInsert['status'], ucfirst($locationInsert['status']), 'Something went wrong', BASE_URL . "branch/manage-locations.php");
  }
}

/*
if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"],);
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
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}
*/
// fetch company name by ID 
function fetchCompanyNameById($id = '')
{
  global $dbCon;
  $returnData = [];
  $sql = "SELECT * FROM `erp_companies` WHERE company_id='$id'";
  if ($res = $dbCon->query($sql)) {
    if ($res->num_rows > 0) {
      $row = $res->fetch_assoc();
      $returnData['status'] = "success";
      $returnData['data'] = $row['company_name'];
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "data not found";
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
  }
  return $returnData;
}

// fetch branch name by ID 
function fetchBranchById($id = '')
{
  global $dbCon;
  $returnData = [];
  $sql = "SELECT * FROM `erp_branches` WHERE branch_id='$id'";
  if ($res = $dbCon->query($sql)) {
    if ($res->num_rows > 0) {
      $row = $res->fetch_assoc();
      $returnData['status'] = "success";
      $returnData['data'] = $row['branch_name'];
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "data not found";
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
  }
  return $returnData;
}

// fetch functionalities name by ID 
function fetchFunctionalitiesNameById($functionalitiIds = '1,2,3')
{
  global $dbCon;
  $returnData = [];
  $exp = explode(",", $functionalitiIds);
  $returnData['data'] = "";

  foreach ($exp as $key => $rowId) {
    $sql = "SELECT * FROM `erp_company_functionalities` WHERE `functionalities_id`='$rowId'";
    if ($res = $dbCon->query($sql)) {
      if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $returnData['status'] = "success";
        $returnData['data'] .= $row['functionalities_name'] . ",";
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "data not found";
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Somthing went wrong";
    }
  }
  return $returnData;
}

// console(fetchFunctionalitiesNameById()['data']);
// console($_POST);
if (isset($_POST['add_branch_location'])) {
  $companyId = $company_id;
  $branchId = $branch_id;
  $check_last = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `company_id`=$company_id AND `branch_id`=$branch_id ORDER BY `othersLocation_id` DESC LIMIT 1");
  $lastsl = $check_last['data']['othersLocation_code'];


  $locationCode =  getLocationSerialNumber($lastsl);
  $locationName = $_POST['locationName'];
  $flatNo = $_POST['flatNo'];
  $buildingNo = $_POST['buildingNo'];
  $streetName = $_POST['streetName'];
  $pinCode = $_POST['pinCode'];
  $location = $_POST['location'];
  $city = $_POST['city'];
  $district = $_POST['district'];
  $state = $_POST['state'];
  $lat = $_POST['lat'];
  $lng = $_POST['lng'];
  // //lat lng fetch
  //$address = $streetName.",".$location.",".$city.",".$district.",".$state.",". $pinCode;
  // $add = "White House, Pennsylvania Avenue Northwest, Washington, DC, United States";
  // echo $add = str_replace(' ', '+', $address);
  // echo $url = "http://maps.google.com/maps/api/geocode/json?address=White House,+ Pennsylvania Avenue Northwest,+ Washington, DC,+ United States";
  // // send api request
  // $geocode = file_get_contents('');
  // $json = json_decode($geocode);
  // $data['lat'] = $json->results[0]->geometry->location->lat;
  // $data['lng'] = $json->results[0]->geometry->location->lng;
  // console($data);
  // exit();
  $compFunc = implode(',', $_POST['compFunc']);

  $adminName = $_POST['adminName'];
  $adminEmail = $_POST['adminEmail'];
  $adminPhone = $_POST['adminPhone'];
  $adminPassword = $_POST['adminPassword'];
  $adminDesignation = $_POST['designation'];
  $adminUserName = $_POST['userName'];

  $locationInsert = "INSERT INTO `erp_branch_otherslocation` 
                        SET 
                            `othersLocation_code`='$locationCode',
                            `company_id`='$companyId',
                            `branch_id`='$branchId',
                            `othersLocation_primary_flag`='0',
                            `companyFunctionalities`='$compFunc',
                            `othersLocation_name`='$locationName',
                            `othersLocation_building_no`='$buildingNo',
                            `othersLocation_flat_no`='$flatNo',
                            `othersLocation_street_name`='$streetName',
                            `othersLocation_pin_code`='$pinCode',
                            `othersLocation_location`='$location',
                            `othersLocation_city`='$city',
                            `othersLocation_district`='$district',
                            `othersLocation_lat`='$lat',
                            `othersLocation_lng`='$lng',
                            `othersLocation_state`='$state'";
  if ($dbCon->query($locationInsert)) {
    // console($locationUpdate);
    $lastId = $dbCon->insert_id;
    $ins = "INSERT INTO `tbl_branch_admin_details` 
                                SET 
                                  `fldAdminCompanyId`='$companyId',
                                  `fldAdminBranchId`='$branchId',
                                  `fldAdminBranchLocationId`='$lastId',
                                  `fldAdminName`='$adminName',
                                  `fldAdminEmail`='$adminEmail',
                                  `fldAdminPhone`='$adminPhone',
                                  `fldAdminPassword`='$adminPassword',
                                  `fldAdminUserName` = '$adminUserName',
                                  `flAdminDesignation` = '$adminDesignation',
                                  `fldAdminRole`='2'";
    //exit();
    if ($dbCon->query($ins)) {
      // console($ins);
      global $current_userName;
      global $companyNameNav;

      $whatsapparray = [];
      $whatsapparray['templatename'] = 'after_location_is_created';
      $whatsapparray['to'] = $adminPhone;
      $whatsapparray['companyname'] = $companyNameNav;
      $whatsapparray['location_name'] = $locationName;
      $whatsapparray['username'] = $adminUserName;
      $whatsapparray['password'] = $adminPassword;
      $whatsapparray['quickcontact'] = null;
      $whatsapparray['current_userName'] = $current_userName;

      SendMessageByWhatsappTemplate($whatsapparray);

      $sub = "Congratulations on the Successful Launch of $locationName Location!";
      $msg = "Dear " . $admin['adminName'] . ",<br>           
          I am thrilled to announce that $locationName Location is now officially open and serving our customers! This marks a significant milestone in our company's growth and expansion, and I want to take a moment to thank each and every one of you for your hard work and dedication in making this possible.<br>
          I am confident that $locationName Location will bring new opportunities and experiences for our customers and employees, and I am proud to have such a talented and committed team in place to make this branch a success.<br>
          To ensure a smooth transition, please find below some important information that will be helpful:<br>
          <b>Team members:</b> A detailed list of the team members assigned to $locationName Location will be sent to you shortly.<br>           
          <b>Branch operations:</b> The branch operations manual and training materials are available for reference and will be sent to you shortly.<br>
          <b>Communication channels:</b> To ensure seamless communication, we have set up dedicated email addresses and phone numbers for $locationName Location.<br>
          <b>Customer support:</b> Our customer support team is available to assist you with any questions or concerns you may have. Please do not hesitate to reach out to them for any support. <br>
          <b>Your Login Credentials are:</b><br>                      
          <b>Url: </b>" . BRANCH_URL . "<br>
          <b>User Name: </b>" . $adminUserName . "<br>
          <b>Password: </b>" . $adminPassword . "<br>   
          Let's work together to make $locationName Location a success. If there is anything else we can do to help, please do not hesitate to contact us.           
          <br>    
          Best regards,  $companyNameNav";

      $emailReturn = SendMailByMySMTPmailTemplate($adminEmail, $sub, $msg, $tmpId = null);
      // redirect($_SERVER['PHP_SELF']);


    } else {
      echo "somthing went wrong! 01";
    }
  } else {
    echo "somthing went wrong! 02";
    // console($locationInsert);
  }
} ?>

<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
  .matrix-card .row:nth-child(1):hover {

    pointer-events: none;

  }

  .matrix-card .row:hover {

    border-radius: 0 0 10px 10px;

  }

  .matrix-card .row:nth-child(1) {

    background: #fff;

  }

  .matrix-card .row .col {

    display: flex;

    align-items: center;

  }

  .matrix-accordion button {

    color: #fff;

    border-radius: 15px !important;

    margin: 20px 0;

  }

  .accordion-button:not(.collapsed) {

    color: #fff;

  }

  .accordion-button::after {

    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");

  }

  .accordion-button:not(.collapsed)::after {

    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='white'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");

  }

  .accordion-item {

    border-radius: 15px !important;

    margin-bottom: 2em;

  }

  .location-info-form-view .form-input {

    margin: 3px 0;

    justify-content: space-between;

  }

  .location-info-form-view .form-input .d-flex {

    align-items: center;

    gap: 7px;

  }

  .info-h4 {

    font-size: 20px;

    font-weight: 600;

    color: #003060;

    padding: 0px 10px;

  }

  .info-h6 {

    font-size: 11.25px;

    color: #fff;

  }

  .card.location-creation-card {

    min-height: 630px;

    height: auto;

  }

  .company-func-card-body .row {

    border: 1px dashed #647484;

    border-radius: 7px;

    padding: 19px 7px 20px;

    margin: 23px 5px 5px;

  }

  .company-func-card-body .row .row {

    border: 0;

    padding: 0;

    margin: 0;

  }


  .list-map-tab {
    position: relative;
    top: 0;
    left: 0;
    width: 100%;
    justify-content: center;
  }

  .list-map-tab a.active {
    background: #003060;
    color: #fff;
  }

  label.poc-details-basic-label {
    padding: 3px 5px;
    position: absolute;
    top: -31px;
    left: 23px;
    background: #dbe5ee;
  }

  .note-col p {
    font-size: 10px;
    color: #003060;
  }

  .location-modal .modal-header {
    height: 330px;
  }

  .func-detail {
    font-size: 9px;
  }


  @media (max-width: 575px) {
    .vendor-modal .modal-body {
      padding: 20px !important;
    }

    .card.location-creation-card {
      min-height: auto;
    }
  }
</style>

<?php
if (isset($_GET['create'])) {
?>

  <style>
    .noAccess_security {
      display: none;
    }
  </style>

  <!-- ############### branch location ################################# -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <?php if (isset($msg)) { ?>
        <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
          <?= $msg ?>
        </div>
      <?php } ?>
      <div class="container-fluid">

        <ol class="breadcrumb">

          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i>Home</a></li>

          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Branch Location</a></li>

          <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-plus po-list-icon"></i>Add Branch Location</a></li>

          <li class="back-button">

            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">

              <i class="fa fa-reply po-list-icon"></i>

            </a>

          </li>

        </ol>


        <!-- <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
              <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Branch Location</a></li>
              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Add Branch Location</a></li>
            </ol> -->

      </div>
    </div>
    <!-- /.content-header -->
    <?php
    $state_sql = queryGet("SELECT * FROM `erp_branches` WHERE `branch_id`=$branch_id");
    // console($state_sql);
    $state = $state_sql['data']['state'];

    if (checkAccess('add')) {
      noAccess(100);
    } else {
      echo 'test';
    } ?>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
          <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">
          <input type="hidden" name="add_branch_location" value="add_branch_location">


          <div class="row">

            <div class="col-lg-4 col-md-4 col-sm-4">

              <div class="row">

                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="card location-creation-card so-creation-card po-creation-card" style="height: auto;">

                    <div class="card-header">

                      <h4>Address Details

                        <!-- <span class="text-danger">*</span> -->

                      </h4>

                    </div>

                    <div class="card-body location-card-body others-info vendor-info so-card-body location-details-card-body" style="height: auto;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row location-info-form-view customer-info-form-view">

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input text-right d-flex mt-2 mb-3">
                                <div class="d-flex">
                                  <input type="checkbox" class="" name="locationCheck" id="locationCheck" value="1">
                                  <label for="" class="mb-0 test-xs font-bold">Same as Branch Address </label>
                                </div>
                                <div class="d-flex">
                                  <!-- <label for="" class="mb-0">Location Name :</label> -->
                                  <label for="" class="text-xs font-bold font-italic mb-0" style="display: none;">Test</label>
                                </div>
                              </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Branch</label>

                                <input type="text" name="branchId" value="<?= fetchBranchById($getBranchId = $_SESSION['logedBranchAdminInfo']['fldAdminBranchId'])['data'] ?>" class="form-control" id="locationCode" readonly>

                              </div>

                            </div>
                            <!-- <div class="col-lg-4 col-md-4 col-sm-4">

                              <div class="form-input">

                                <label for="">Location code</label>

                                <input type="text" name="locationCode" class="form-control" id="location_code">

                              </div>

                            </div> -->
                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Location Name</label>

                                <input type="text" name="locationName" class="form-control" id="locationName" value="">

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Flat Number</label>

                                <input type="text" name="flatNo" class="form-control" id="flatNo" value="">

                              </div>

                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Building Number</label>

                                <input type="text" name="buildingNo" class="form-control" id="buildingNo" value="">

                              </div>

                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Street Name</label>

                                <input type="text" name="streetName" class="form-control" id="streetName" value="">

                              </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Location</label>

                                <input type="text" name="location" class="form-control" id="location" value="">

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">City</label>

                                <input type="text" name="city" class="form-control" id="city" value="">

                              </div>

                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Pin Code</label>

                                <input type="number" name="pinCode" class="form-control" id="pincode" value="">

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">District</label>

                                <input type="text" name="district" class="form-control" id="district" value="">

                              </div>


                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">State</label>
                                

                                <select id="state" name="state" class="form-control stateDropDown">
                                  <?php
                                  $select = 0;
                                  $state_sql = queryGet("SELECT * FROM `erp_gst_state_code` WHERE country_id = " . $countrycode . "", true);
                                  $state_data = $state_sql['data'];
                                  foreach ($state_data as $data) {
                                  ?>

                                    <option value="<?= $data['gstStateCode'] ?>" <?php if ($data['gstStateName'] == $state) {
                                                                                    $select = 1;
                                                                                    echo "selected";
                                                                                  } ?> disabled><?= $data['gstStateName'] ?></option>
                                  <?php
                                  }
                                  ?>
                                </select>
                                <input type="hidden" name="state" id="stateHidden">

                                <script>
                                  function updateHiddenState() {
                                    let stateDropdown = document.getElementById('state');
                                    let selectedOption = stateDropdown.options[stateDropdown.selectedIndex].value;
                                    document.getElementById('stateHidden').value = selectedOption;
                                  }

                                  // Set initial value on page load
                                  document.addEventListener('DOMContentLoaded', function() {
                                    updateHiddenState();
                                  });
                                </script>

                                <!-- <input type="text" name="state" class="form-control" id="state" value=""> -->

                              </div>

                            </div>


                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Latitude</label>

                                <input type="text" name="lat" class="form-control" id="lat" value="">

                              </div>


                            </div>


                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Longitude</label>

                                <input type="text" name="lng" class="form-control" id="lng" value="">

                              </div>


                            </div>




                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input mt-3">

                                <label for="">Functional Area :</label>

                                <p class="font-italic func-detail mb-2">[ Select Functional area / Business verticals on which this branch is doing business ]</p>

                                <div class="display-flex location-comp-funch-flex">
                                  <div class="row">
                                    <?php
                                    $sql = "SELECT * FROM `erp_company_functionalities` WHERE company_id=$company_id AND `functionalities_status`='active'";
                                    $res = $dbCon->query($sql);
                                    while ($row = $res->fetch_assoc()) {
                                    ?>
                                      <div class="col-lg-6 col-md-6 col-sm-12">
                                        <input class="func_check" type="checkbox" name="compFunc[]" id="compFunc_<?= $row['functionalities_id'] ?>" value="<?= $row['functionalities_id'] ?>">
                                        <label for=""> <?= $row['functionalities_name'] ?></label>
                                      </div>
                                    <?php } ?>
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

            </div>

            <div class="col-lg-4 col-md-4 col-sm-4">
              <div class="row">

                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="card location-creation-card so-creation-card po-creation-card">

                    <div class="card-header">

                      <h4> POC Details
                        <!-- <span class="text-danger">*</span> -->

                      </h4>

                    </div>

                    <div class="card-body location-card-body others-info vendor-info so-card-body company-func-card-body">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <label for="" class="poc-details-basic-label">Basic Details</label>

                          <div class="row location-info-form-view customer-info-form-view">

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Full Name</label>

                                <input type="text" name="adminName" class="form-control" id="adminName">

                              </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Designation</label>

                                <input type="text" name="designation" class="form-control" id="designation">

                              </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Email</label>

                                <input type="email" name="adminEmail" class="form-control" id="adminEmail">

                              </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Phone</label>

                                <input type="number" name="adminPhone" class="form-control" id="adminPhone" value="">

                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <label for="" class="poc-details-basic-label">Admin Details</label>

                          <div class="row location-info-form-view customer-info-form-view">

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Login ID</label>

                                <input type="text" name="userName" class="form-control" id="user_name" readonly>

                              </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Password</label>

                                <input type="text" name="adminPassword" class="form-control" id="adminPassword" value="<?php echo rand(1111, 9999); ?>">

                              </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 note-col">

                              <p class="mt-4 mb-2 font-bold">Note : Login crediantials will be sent to email.</p>

                            </div>

                          </div>

                        </div>


                      </div>

                    </div>

                  </div>
                </div>



              </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-4">

              <div class="col-lg-12 col-md-12 col-sm-12 p-0">

                <div class="card location-creation-card so-creation-card po-creation-card" style="height: auto;">

                  <div class="card-header">

                    <h4> Existing Location
                      <!-- <span class="text-danger">*</span> -->

                    </h4>

                  </div>

                  <div class="card-body location-card-body others-info vendor-info so-card-body existing-location-card-body">

                    <div class="row">

                      <div class="col-lg-12 col-md-12 col-sm-12">

                        <div class="row location-info-form-view customer-info-form-view">

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <?php

                            $branchId = $_SESSION['logedBranchAdminInfo']['fldAdminBranchId'];

                            $sql = "SELECT * FROM `erp_branch_otherslocation` WHERE `branch_id`=$branchId";
                            if ($res = $dbCon->query($sql)) {
                              if ($res->num_rows > 0) {
                                while ($row = $res->fetch_assoc()) {
                            ?>
                                  <div class="card">
                                    <ul class="list-group list-group-flush">
                                      <li class="list-group-item text-xs">Location Name: <?= $row['othersLocation_name'] ?></li>
                                      <li class="list-group-item text-xs">Street Name: <?= $row['othersLocation_street_name'] ?></li>
                                      <li class="list-group-item text-xs">Status: <?= $row['othersLocation_status'] ?></li>
                                    </ul>
                                  </div>
                                <?php
                                }
                              } else { ?>
                                <div class="alert alert-danger" style="font-size:1.2em"><strong>Location not found!</strong> <span>In this branch.</span></div>
                              <?php
                              }
                            } else { ?>
                              <div class="alert alert-secondary" style="font-size:1.2em"><strong>Select A Branch!</strong></div>
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


            <div class="btn-section mt-2 mb-2">
              <button type="submit" name="add_branch_location1" class="btn btn-primary add_branch_location float-right" value="add_branch_location1"> Final Submit</button>
            </div>

          </div>






          <div class="row">
          </div>
        </form>

        <!-- modal -->
        <div class="modal" id="myModal3">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Heading</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <div class="col-md-12 mb-3">
                  <div class="form-input">
                    <select name="goodsGroup" class="form-control form-control-border borderColor">
                      <option value="">Branches Group</option>
                      <option value="A">A</option>
                      <option value="B">B</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-input">
                    <input type="text" name="itemCode" class="form-control" id="exampleInputBorderWidth2">
                    <label>Item Code</label>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-input btn-col">
                    <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                  </div>
                </div>
              </div>
              <!-- <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div> -->
            </div>
          </div>
        </div>
        <!-- modal end -->
        <!-- modal -->
        <div class="modal" id="myModal4">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Heading4</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <div class="col-md-12 mb-3">
                  <div class="form-input">
                    <select name="goodsGroup" class="form-control form-control-border borderColor">
                      <option value="">Branches Group</option>
                      <option value="A">A</option>
                      <option value="B">B</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-input">
                    <input type="text" name="itemCode" class="form-control" id="exampleInputBorderWidth2">
                    <label>Item Code</label>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-input btn-col">
                    <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                  </div>
                </div>
              </div>
              <!-- <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div> -->
            </div>
          </div>
        </div>
        <!-- modal end -->
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- ############### branch location ################################# -->

<?php
} else if (isset($_GET['edit']) && $_GET["edit"] > 0) {
?>
  <style>
    .noAccess_security {
      display: none;
    }
  </style>

  <!-- ############### branch location ################################# -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <?php if (isset($msg)) { ?>
        <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
          <?= $msg ?>
        </div>
      <?php } ?>
      <div class="container-fluid">

        <ol class="breadcrumb">

          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i>Home</a></li>

          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Branch Location</a></li>

          <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-plus po-list-icon"></i>Add Branch Location</a></li>

          <li class="back-button">

            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">

              <i class="fa fa-reply po-list-icon"></i>

            </a>

          </li>

        </ol>


        <!-- <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
              <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Branch Location</a></li>
              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Add Branch Location</a></li>
            </ol> -->

      </div>
    </div>
    <!-- /.content-header -->
    <?php
    $state_sql = queryGet("SELECT * FROM `erp_branches` WHERE `branch_id`=$branch_id");
    // console($state_sql);
    $state = $state_sql['data']['state'];

    if (checkAccess('add')) {
      noAccess(100);
    } else {
      echo 'test';
    }
    $location_id = $_GET['edit'];
    $location_sql = queryGet("SELECT * FROM `erp_branch_otherslocation` as loc LEFT JOIN `tbl_branch_admin_details` as admin_details ON loc.othersLocation_id = admin_details.fldAdminBranchLocationId WHERE loc.`othersLocation_id` = $location_id");
    //  console($location_sql);
    $func = $location_sql['data']['companyFunctionalities'];

    $func_array = explode(",", $func);
    ?>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="edit_form" name="edit_form">
          <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">
          <input type="hidden" name="location_id" value="<?= $location_sql['data']['othersLocation_id'] ?>">

          <input type="hidden" name="admin_location_id" value="<?= $location_sql['data']['fldAdminKey'] ?>">




          <div class="row">

            <div class="col-lg-4 col-md-4 col-sm-4">

              <div class="row">

                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="card location-creation-card so-creation-card po-creation-card" style="height: auto;">

                    <div class="card-header">

                      <h4>Address Details

                        <!-- <span class="text-danger">*</span> -->

                      </h4>

                    </div>

                    <div class="card-body location-card-body others-info vendor-info so-card-body location-details-card-body" style="height: auto;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row location-info-form-view customer-info-form-view">

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input text-right d-flex mt-2 mb-3">
                                <div class="d-flex">
                                  <!-- <input type="checkbox" class="" name="locationCheck" id="locationCheck" value="1">
                                  <label for="" class="mb-0 test-xs font-bold">Same as Branch Address </label> -->
                                </div>
                                <div class="d-flex">
                                  <!-- <label for="" class="mb-0">Location Name :</label> -->
                                  <label for="" class="text-xs font-bold font-italic mb-0" style="display: none;">Test</label>
                                </div>
                              </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Branch</label>

                                <input type="text" name="branchId" value="<?= fetchBranchById($getBranchId = $_SESSION['logedBranchAdminInfo']['fldAdminBranchId'])['data'] ?>" class="form-control" id="locationCode" readonly>

                              </div>

                            </div>
                            <!-- <div class="col-lg-4 col-md-4 col-sm-4">

                              <div class="form-input">

                                <label for="">Location code</label>

                                <input type="text" name="locationCode" class="form-control" id="location_code">

                              </div>

                            </div> -->
                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Location Name</label>

                                <input type="text" name="locationName" class="form-control" id="locationName" value="<?= $location_sql['data']['othersLocation_name'] ?>">

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Flat Number</label>

                                <input type="text" name="flatNo" class="form-control" id="flatNo" value="<?= $location_sql['data']['othersLocation_flat_no'] ?>">

                              </div>

                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Building Number</label>

                                <input type="text" name="buildingNo" class="form-control" id="buildingNo" value="<?= $location_sql['data']['othersLocation_building_no'] ?>">

                              </div>

                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Street Name</label>

                                <input type="text" name="streetName" class="form-control" id="streetName" value="<?= $location_sql['data']['othersLocation_street_name'] ?>">

                              </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Location</label>

                                <input type="text" name="location" class="form-control" id="location" value="<?= $location_sql['data']['othersLocation_location'] ?>">

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">City</label>

                                <input type="text" name="city" class="form-control" id="city" value="<?= $location_sql['data']['othersLocation_city'] ?>">

                              </div>

                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Pin Code</label>

                                <input type="number" name="pinCode" class="form-control" id="pincode" value="<?= $location_sql['data']['othersLocation_pin_code'] ?>">

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">District</label>

                                <input type="text" name="district" class="form-control" id="district" value="<?= $location_sql['data']['othersLocation_district'] ?>">

                              </div>

                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">State</label>

                                <select id="state" name="state" class="form-control stateDropDown">
                                  <?php
                                  $state_sql = queryGet("SELECT * FROM `erp_gst_state_code` WHERE country_id = " . $countrycode . "", true);
                                  $state_data = $state_sql['data'];
                                  foreach ($state_data as $data) {
                                  ?>

                                    <option value="<?= $data['gstStateCode'] ?>" <?php if ($data['gstStateCode'] ==  $location_sql['data']['othersLocation_state']) {
                                                                                    echo "selected";
                                                                                  } ?>><?= $data['gstStateName'] ?></option>
                                  <?php
                                  }
                                  ?>
                                </select>

                                <!-- <input type="text" name="state" class="form-control" id="state" value=""> -->

                              </div>

                            </div>


                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Latitude</label>

                                <input type="text" name="lat" class="form-control" id="lat" value="<?= $location_sql['data']['othersLocation_lat'] ?>">

                              </div>


                            </div>


                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Longitude</label>

                                <input type="text" name="lng" class="form-control" id="lng" value="<?= $location_sql['data']['othersLocation_lng'] ?>">

                              </div>


                            </div>



                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input mt-3">

                                <label for="">Functional Area :</label>

                                <p class="font-italic func-detail mb-2">[ Select Functional area / Business verticals on which this branch is doing business ]</p>

                                <div class="display-flex location-comp-funch-flex">
                                  <div class="row">
                                    <?php

                                    $sql = "SELECT * FROM `erp_company_functionalities` WHERE company_id=$company_id AND `functionalities_status`='active'";
                                    $res = $dbCon->query($sql);
                                    while ($row = $res->fetch_assoc()) {
                                    ?>
                                      <div class="col-lg-6 col-md-6 col-sm-12">
                                        <input class="func_check" type="checkbox" name="compFunc[]" id="compFunc_<?= $row['functionalities_id'] ?>" value="<?= $row['functionalities_id'] ?>" <?php if (in_array($row['functionalities_id'], $func_array)) {
                                                                                                                                                                                                echo "checked";
                                                                                                                                                                                              } else {
                                                                                                                                                                                              } ?>>
                                        <label for=""> <?= $row['functionalities_name'] ?></label>
                                      </div>
                                    <?php } ?>
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

            </div>

            <div class="col-lg-4 col-md-4 col-sm-4">
              <div class="row">

                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="card location-creation-card so-creation-card po-creation-card">

                    <div class="card-header">

                      <h4> POC Details
                        <!-- <span class="text-danger">*</span> -->

                      </h4>

                    </div>

                    <div class="card-body location-card-body others-info vendor-info so-card-body company-func-card-body">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <label for="" class="poc-details-basic-label">Basic Details</label>

                          <div class="row location-info-form-view customer-info-form-view">

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Full Name</label>

                                <input type="text" name="adminName" class="form-control" id="" value="<?= $location_sql['data']['fldAdminName'] ?>">

                              </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Designation</label>

                                <input type="text" name="designation" class="form-control" id="" value="<?= $location_sql['data']['flAdminDesignation'] ?>">

                              </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Email</label>

                                <input type="email" name="adminEmail" class="form-control" id="email" value="<?= $location_sql['data']['fldAdminEmail'] ?>">

                              </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Phone</label>

                                <input type="number" name="adminPhone" class="form-control" id="" value="<?= $location_sql['data']['fldAdminPhone'] ?>">

                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <label for="" class="poc-details-basic-label">Admin Details</label>

                          <div class="row location-info-form-view customer-info-form-view">

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Login ID</label>

                                <input type="text" name="userName" class="form-control" id="user_name" value="<?= $location_sql['data']['fldAdminUserName'] ?>" readonly>

                              </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Password</label>

                                <input type="text" name="adminPassword" class="form-control" id="adminPassword" value="<?= $location_sql['data']['fldAdminPassword'] ?>">

                              </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 note-col">

                              <p class="mt-4 mb-2 font-bold">Note : Login crediantials will be sent to email.</p>

                            </div>

                          </div>

                        </div>


                      </div>

                    </div>

                  </div>
                </div>



              </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-4">

              <div class="col-lg-12 col-md-12 col-sm-12 p-0">

                <div class="card location-creation-card so-creation-card po-creation-card" style="height: auto;">

                  <div class="card-header">

                    <h4> Existing Location
                      <!-- <span class="text-danger">*</span> -->

                    </h4>

                  </div>

                  <div class="card-body location-card-body others-info vendor-info so-card-body existing-location-card-body">

                    <div class="row">

                      <div class="col-lg-12 col-md-12 col-sm-12">

                        <div class="row location-info-form-view customer-info-form-view">

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <?php

                            $branchId = $_SESSION['logedBranchAdminInfo']['fldAdminBranchId'];

                            $sql = "SELECT * FROM `erp_branch_otherslocation` WHERE `branch_id`=$branchId";
                            if ($res = $dbCon->query($sql)) {
                              if ($res->num_rows > 0) {
                                while ($row = $res->fetch_assoc()) {
                            ?>
                                  <div class="card">
                                    <ul class="list-group list-group-flush">
                                      <li class="list-group-item text-xs">Location Name: <?= $row['othersLocation_name'] ?></li>
                                      <li class="list-group-item text-xs">Street Name: <?= $row['othersLocation_street_name'] ?></li>
                                      <li class="list-group-item text-xs">Status: <?= $row['othersLocation_status'] ?></li>
                                    </ul>
                                  </div>
                                <?php
                                }
                              } else { ?>
                                <div class="alert alert-danger" style="font-size:1.2em"><strong>Location not found!</strong> <span>In this branch.</span></div>
                              <?php
                              }
                            } else { ?>
                              <div class="alert alert-secondary" style="font-size:1.2em"><strong>Select A Branch!</strong></div>
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


            <div class="btn-section mt-2 mb-2">
              <button type="submit" name="edit_branch_location" class="btn btn-primary edit_branch_location float-right" value="edit_branch_location">Update</button>
            </div>

          </div>






          <div class="row">
          </div>
        </form>

        <!-- modal -->
        <div class="modal" id="myModal3">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Heading</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <div class="col-md-12 mb-3">
                  <div class="form-input">
                    <select name="goodsGroup" class="form-control form-control-border borderColor">
                      <option value="">Branches Group</option>
                      <option value="A">A</option>
                      <option value="B">B</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-input">
                    <input type="text" name="itemCode" class="form-control" id="exampleInputBorderWidth2">
                    <label>Item Code</label>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-input btn-col">
                    <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                  </div>
                </div>
              </div>
              <!-- <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div> -->
            </div>
          </div>
        </div>
        <!-- modal end -->
        <!-- modal -->
        <div class="modal" id="myModal4">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Heading4</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <div class="col-md-12 mb-3">
                  <div class="form-input">
                    <select name="goodsGroup" class="form-control form-control-border borderColor">
                      <option value="">Branches Group</option>
                      <option value="A">A</option>
                      <option value="B">B</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-input">
                    <input type="text" name="itemCode" class="form-control" id="exampleInputBorderWidth2">
                    <label>Item Code</label>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-input btn-col">
                    <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                  </div>
                </div>
              </div>
              <!-- <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div> -->
            </div>
          </div>
        </div>
        <!-- modal end -->
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- ############### branch location ################################# -->
<?php
} else if (isset($_GET['view']) && $_GET["view"] > 0) {
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
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
              <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
              <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Branches</a></li>
              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">View Branches</a></li>
            </ol>
          </div>
          <div class="col-md-6" style="display: flex;">
            <a href="<?= basename($_SERVER['PHP_SELF']); ?>"><button class="btn btn-danger btnstyle ml-2">Back</button></a>
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
                          <div class="form-input">
                            <select id="" name="goodsType" class="select2 form-control form-control-border borderColor">
                              <option value="">Branches Type</option>
                              <option value="A">A</option>
                              <option value="B">B</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="form-input">
                            <select name="goodsGroup" class="select4 form-control form-control-border borderColor">
                              <option value="">Branches Group</option>
                              <option value="A">A</option>
                              <option value="B">B</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <select name="purchaseGroup" class="select2 form-control form-control-border borderColor">
                              <option value="">Purchase Group</option>
                              <option value="">A</option>
                              <option value="">B</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <input type="text" name="branh" class="form-control" id="exampleInputBorderWidth2">
                            <label>Branches</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
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
                          <div class="form-input">
                            <input type="text" name="itemCode" class="form-control" id="exampleInputBorderWidth2">
                            <label>Item Code</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <input type="text" name="itemName" class="form-control" id="exampleInputBorderWidth2">
                            <label>Item Name</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <input type="text" name="netWeight" class="form-control" id="exampleInputBorderWidth2">
                            <label>Net Weight</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
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
                          <textarea type="text" name="itemDesc" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Item Description"></textarea>
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
} else { ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- row -->
        <div class="row p-0 m-0">
          <div class="col-12 mt-2 p-0">
            <div class="card card-tabs">
              <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                <li class="pt-2 px-3 my-2 d-flex justify-content-between align-items-center" style="width:100%">
                  <h3 class="card-title text-nowrap">Manage Locations</h3>
                  <div class="list-map-tab filter-list">
                    <a href="manage-locations.php" class="btn active"><i class="fa fa-clock mr-2 active"></i>Location List</a>
                    <a href="locations.php" class="btn "><i class="fa fa-lock-open mr-2 "></i>Location Map View</a>
                  </div>
                  <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary"><i class="fa fa-plus" style="margin-right: 0;"></i></a>
                </li>
              </ul>

              <table class="table defaultDataTable table-hover">
                <thead>
                  <tr>
                    <th>SL.No</th>
                    <th>Location Code</th>
                    <th>Company</th>
                    <th>Branch</th>
                    <th>Location Name</th>
                    <th>Functional Area</th>
                    <th>Status</th>
                    <th>Action </th>
                  </tr>
                </thead>

                <tbody>
                  <?php
                  $sql = "SELECT * FROM `erp_branch_otherslocation` WHERE `company_id`='" . $company_id . "' AND  `branch_id`='" . $branch_id . "' AND `othersLocation_status`!='deleted'";
                  $res = $dbCon->query($sql);
                  $i = 1;
                  while ($row = $res->fetch_assoc()) {
                    $functionality = fetchFunctionalitiesNameById($row['companyFunctionalities'])['data'];
                  ?>
                    <tr>
                      <td><?= $i++ ?></td>
                      <td><?= $row['othersLocation_code'] ?></td>
                      <td><?= fetchCompanyNameById($row['company_id'])['data'] ?></td>
                      <td><?= fetchBranchById($row['branch_id'])['data'] ?></td>
                      <td><?= $row['othersLocation_name'] ?></td>
                      <td class="text-capitalize"><?= str_replace(",", "/", rtrim($functionality, ',')) ?></td>
                      <td>
                        <div class="status text-sm"><?= $row['othersLocation_status'] ?></div>
                      </td>
                      <td class="d-flex">
                        <a href="manage-locations.php?edit=<?= $row['othersLocation_id'] ?>" title="Edit" name="">
                          <i class="fa fa-edit po-list-icon"></i></a>
                        <button class="btn btn-sm text-info" data-toggle="modal" data-target="#locationModal_<?= $row['othersLocation_id'] ?>"><i class="fa fa-eye po-list-icon"></i></button>
                        <?php
                        if (isset($_SESSION["logedBranchAdminInfo"]["adminRole"]) && $_SESSION["logedBranchAdminInfo"]["adminRole"] == 1) { ?>
                          <form action="" method="POST" class="btn btn-sm">
                            <input type="hidden" name="fldAdminBranchLocationId" value="<?php echo $row['othersLocation_id'] ?>">
                            <input type="hidden" name="fldAdminBranchId" value="<?php echo $row['branch_id'] ?>">
                            <input type="hidden" name="fldAdminCompanyId" value="<?php echo $row['company_id'] ?>">
                            <input type="hidden" name="visit_location" value="visit_location">
                            <button class="btn btn-sm" title="Visit Location" type="submit" onclick="return confirm('Are you sure to Visit?')" style="cursor: pointer;"><i class="fa fa-share po-list-icon" aria-hidden="true"></i></button>
                          </form>
                        <?php } ?>
                      </td>
                    </tr>
                    <!-- Modal -->
                    <div class="modal right fade customer-modal location-modal" id="locationModal_<?= $row['othersLocation_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <p class="heading lead mt-3 mb-2" id="myModalLabel2"><?= fetchCompanyNameById($row['company_id'])['data'] ?></p>
                            <p class="text-sm mt-2 mb-2" id="myModalLabel2"><?= $row['othersLocation_name'] ?></p>
                            <p class="text-sm mt-2 mb-2">Location Code : <?= $row['othersLocation_code'] ?></p>
                            <p class="text-sm mt-2 mb-2">Branch : <?= fetchBranchById($row['branch_id'])['data'] ?></p>
                            <p class="text-sm mt-2 mb-2">Functionality : <?= fetchFunctionalitiesNameById($row['companyFunctionalities'])['data'] ?></p>

                            <div class="display-flex-space-between mt-4 mb-3">
                              <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                  <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= str_replace('/', '-', $row['othersLocation_code']) ?>">Info</a>
                                </li>
                                <!-- -------------------Audit History Button Start------------------------- -->
                                <li class="nav-item">
                                  <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $row['othersLocation_code']) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $row['othersLocation_code']) ?>" href="#history<?= str_replace('/', '-', $row['othersLocation_code']) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $row['othersLocation_code']) ?>" aria-selected="false"><i class="fa fa-history mr-2"></i> Trail</a>
                                </li>
                                <!-- -------------------Audit History Button End------------------------- -->
                              </ul>

                              <div class="action-btns display-flex-gap justify-end mt-3 mb-3" id="action-navbar">
                                <?php $locationId = base64_encode($row['othersLocation_id']) ?>
                                <form action="" method="POST">
                                  <a href="#" name="vendorEditBtn">
                                    <i title="Edit" class="fa fa-edit po-list-icon-invert"></i>
                                  </a>
                                  <a href="#">
                                    <i title="Delete" class="fa fa-trash po-list-icon-invert"></i>
                                  </a>
                                  <a href="">
                                    <i title="Toggle" class="fa fa-toggle-on po-list-icon-invert"></i>
                                  </a>
                                </form>
                              </div>
                            </div>

                            <!-- <h4 class="modal-title" ><?= $row['othersLocation_name'] ?></h4> -->
                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
                          </div>

                          <div class="modal-body pl-4 pr-4 pt-3">

                            <div class="tab-content" id="myTabContent">
                              <div class="tab-pane fade show active" id="home<?= str_replace('/', '-', $row['othersLocation_code']) ?>" role="tabpanel" aria-labelledby="home-tab">
                                <div class="row">
                                  <div class="col-lg-12 col-md-12 col-sm-12">

                                    <h4 class="info-h4">
                                      Info
                                      <hr class="mt-1 mb-1">
                                    </h4>


                                    <!--------Address Details--------->
                                    <div class="accordion accordion-flush location-accordion matrix-accordion p-0" id="accordionFlushExample">
                                      <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingOne">
                                          <button class="accordion-button btn btn-primary collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#address" aria-expanded="false" aria-controls="flush-collapseOne">
                                            Address
                                          </button>
                                        </h2>
                                        <div id="address" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                          <div class="accordion-body p-0">

                                            <div class="card">
                                              <div class="card-body p-3">

                                                <div class="display-flex-space-between">
                                                  <p class="font-bold text-xs">Building No :</p>
                                                  <p class="font-bold text-xs"><?= $row['othersLocation_building_no'] ?></p>
                                                </div>
                                                <div class="display-flex-space-between">
                                                  <p class="font-bold text-xs">Flat No :</p>
                                                  <p class="font-bold text-xs"><?= $row['othersLocation_flat_no'] ?></p>
                                                </div>
                                                <div class="display-flex-space-between">
                                                  <p class="font-bold text-xs">Street Name :</p>
                                                  <p class="font-bold text-xs"><?= $row['othersLocation_street_name'] ?></p>
                                                </div>
                                                <div class="display-flex-space-between">
                                                  <p class="font-bold text-xs">Location :</p>
                                                  <p class="font-bold text-xs"><?= $row['othersLocation_location'] ?></p>
                                                </div>
                                                <div class="display-flex-space-between">
                                                  <p class="font-bold text-xs">City :</p>
                                                  <p class="font-bold text-xs"><?= $row['othersLocation_city'] ?></p>
                                                </div>
                                                <div class="display-flex-space-between">
                                                  <p class="font-bold text-xs">District :</p>
                                                  <p class="font-bold text-xs"><?= $row['othersLocation_district'] ?></p>
                                                </div>
                                                <div class="display-flex-space-between">
                                                  <p class="font-bold text-xs">State :</p>
                                                  <p class="font-bold text-xs"><?= $row['othersLocation_state'] ?></p>
                                                </div>
                                              </div>
                                            </div>



                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                    <div class="accordion accordion-flush location-accordion matrix-accordion p-0" id="accordionFlushExample">
                                      <div class="accordion-item">


                                        <div class="card">
                                          <div class="card-header pt-3 pb-3 pl-4">
                                            <h6 class="info-h6 mb-0">
                                              Other Details
                                            </h6>
                                          </div>
                                          <div class="card-body p-3">

                                            <div class="display-flex-space-between">
                                              <p class="font-bold text-xs">Created at :</p>
                                              <p class="font-bold text-xs"><?php
                                                                            $date = date_create($row['othersLocation_created_at']);
                                                                            echo date_format($date, "F j, Y, g:i a");
                                                                            ?></p>
                                            </div>
                                            <div class="display-flex-space-between">
                                              <p class="font-bold text-xs">Created By :</p>
                                              <p class="font-bold text-xs"><?= $row['othersLocation_created_by'] ?></p>
                                            </div>
                                            <div class="display-flex-space-between">
                                              <p class="font-bold text-xs">Updated at :</p>
                                              <p class="font-bold text-xs"><?php
                                                                            $date = date_create($row['othersLocation_created_at']);
                                                                            echo date_format($date, "F j, Y, g:i a");
                                                                            ?></p>
                                            </div>
                                            <div class="display-flex-space-between">
                                              <p class="font-bold text-xs">Updated by :</p>
                                              <p class="font-bold text-xs"><?= $row['othersLocation_updated_by'] ?></p>
                                            </div>
                                          </div>
                                        </div>

                                      </div>
                                    </div>

                                  </div>
                                </div>



                                <div class="row px-3 p-0 m-0" style="place-items: self-start;">



                                </div>
                              </div>
                              <!-- -------------------Audit History Tab Body Start------------------------- -->
                              <div class="tab-pane fade" id="history<?= str_replace('/', '-', $row['othersLocation_code']) ?>" role="tabpanel" aria-labelledby="history-tab">

                                <div class="audit-head-section mb-3 mt-3 ">
                                  <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($row['createdBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['createdAt']) ?></p>
                                  <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($row['updatedBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['updatedAt']) ?></p>
                                </div>
                                <hr>
                                <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $row['othersLocation_code']) ?>">

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
                            </div><!-- modal-content -->
                          </div><!-- modal-dialog -->
                        </div>
                      <?php } ?>

                </tbody>
              </table>
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
require_once("common/footer.php");
?>
<script>
  $("#selectedBranch").on("change", function() {
    let seletedValue = $(this).val();

    $.ajax({
      url: "ajaxs/ajax-branchLocation.php",
      type: "POST",
      data: {
        branchId: seletedValueadd_frm
      },
      beforeSend: function() {
        $(".aiBranchDetails1").html(`<p class="h6 text-secondary ">Loading...</h5>`);
      },
      success: function(resp) {
        $(".aiBranchDetails1").html(resp);
      }
    });

  })

  // $(".add_data").click(function() {

  //   var data = this.value;
  //   $("#createdata").val(data);
  //   confirm('Are you sure to Submit?')

  //   $("#add_frm").submit();
  // });
  $(".edit_data").click(function() {
    var data = this.value;
    $("#editdata").val(data);
    alert(data);
    //$( "#edit_frm" ).submit();
  });

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


    $(document).on("keyup paste keydown", "#branch_gstin", function() {
      var branch_gstin = $("#branch_gstin").val();
      var leng_gstin = branch_gstin.length;
      if (leng_gstin > 14) {
        $("#vendorPanNo").val(branch_gstin.substr(2, 10));

        $.ajax({
          type: "GET",
          url: `ajaxs/ajax-gst-details.php?gstin=${branch_gstin}`,
          beforeSend: function() {
            $('#gstinloder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          },
          success: function(response) {

            $('#gstinloder').html("");
            responseObj = JSON.parse(response);
            if (responseObj["status"] == "success") {
              responseData = responseObj["data"];

              console.log(responseData);

              $("#branch_name").val(responseData["tradeNam"]);
              $("#con_business").val(responseData["ctb"]);
              $("#build_no").val(responseData['pradr']['addr']['bno']);
              $("#flat_no").val(responseData['pradr']['addr']['flno']);
              $("#street_name").val(responseData['pradr']['addr']['st']);
              $("#pincode").val(responseData['pradr']['addr']['pncd']);
              $("#location").val(responseData['pradr']['addr']['loc']);
              $("#city").val(responseData['pradr']['addr']['city']);
              $("#district").val(responseData['pradr']['addr']['dst']);
              $("#state").val(responseData['pradr']['addr']['stcd']);

              //$("#status").val(responseData["sts"]);

            } else {
              let Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
              });
              Toast.fire({
                icon: `warning`,
                title: `&nbsp;Invalid GSTIN No!`
              });
            }
          }
        });
      }

    });


    $('.select2')
      .select2()
      .on('select2:open', () => {
        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal3">
    Add New
  </a></div>`);
      });
    //**************************************************************
    $('.select4')
      .select4()
      .on('select4:open', () => {
        $(".select4-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal4">
    Add New
  </a></div>`);
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
  $(document).on("click", "#locationCheck", function() {
    let checkVal = $('input[name="locationCheck"]:checked').val();
    let b_id = <?= $branch_id ?>;

    //console.log(checkVal);
    if (checkVal == 1) {
      console.log(checkVal);
      $.ajax({
        type: "GET",
        url: `ajaxs/ajax-location.php`,
        data: {
          act: "location",
          b_id
        },
        beforeSend: function() {
          //$("#itemsDropDown").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          //console.log(response);
          var obj = jQuery.parseJSON(response);
          console.log(obj);
          $("#flatNo").val(obj['flat_no']);
          $("#buildingNo").val(obj['build_no']);
          $("#streetName").val(obj['street_name']);
          $("#pincode").val(obj['pincode']);
          $("#location").val(obj['location']);
          $("#city").val(obj['city']);
          $("#district").val(obj['district']);
          //$("#state").val(obj['state']);

        }
      });
    } else {
      console.log(8);
      $("#flatNo").val('');
      $("#buildingNo").val('');
      $("#streetName").val('');
      $("#pincode").val('');
      $("#location").val('');
      $("#city").val('');
      $("#district").val('');
      // $("#state").val('');

    }
  });
  $("#adminEmail").blur(function() {
    let email = $(this).val();

    $.ajax({
      type: "GET",
      url: `ajaxs/ajax-location.php`,
      data: {
        act: "user_id",
        email
      },
      beforeSend: function() {
        //$("#itemsDropDown").html(`<option value="">Loding...</option>`);
      },
      success: function(response) {
        //alert(response);
        $("#user_name").val(response);



      }
    });


  });
</script>

<!-- <script src="<?= BASE_URL; ?>public/validations/locationValidation.js"></script> -->