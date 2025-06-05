<?php
include("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/branch/func-cost-center.php");
include("../app/v1/functions/company/func-company-cash-accounts.php");


if(isset($_POST["submitInvNumber"]))
{
  // console($_POST);
  $location = $_POST["location"];
  $functionalities = $_POST["functionalities"];
  $prefix = $_POST["prefix"];

  foreach($_POST["branch"] as $key => $branch)
  {
        $branch_id = $branch;
        $location_id = $location[$key];
        $functional_id = $functionalities[$key];
        $prefix_id = $prefix[$key];

        $check = queryGet("SELECT * FROM `erp_branch_func_varient_map` WHERE `company_id`='$company_id' AND `branch_id`='$branch_id' AND `functional_area_id`='$functional_id' AND `location_id`='$location_id' AND `status`='1'",false);

        if($check["numRows"] > 0) {
            //Update
            $update = queryUpdate("UPDATE `erp_branch_func_varient_map` SET 
            `iv_varient_id`='" . $prefix_id . "',
            `updated_by`='" . $updated_by . "' WHERE `company_id`='" . $company_id . "' AND `branch_id`='" . $branch_id . "' AND `location_id`='" . $location_id . "' AND `functional_area_id`='" . $functional_id . "'");

        } else{
          //insert
          $insert = queryInsert("INSERT INTO `erp_branch_func_varient_map` SET 
                   `company_id`='" . $company_id . "',
                   `branch_id`='" . $branch_id . "',
                   `location_id`='" . $location_id . "',
                   `iv_varient_id`='" . $prefix_id . "',
                   `functional_area_id`='" . $functional_id . "',
                   `created_by`='" . $created_by . "',
                   `updated_by`='" . $updated_by . "'");
        }
  }

  // if ($insert["status"] == "success") {
  //       swalToast($insert["status"], $insert["message"]);
  //     }
  //     else
  //     {
  //       swalToast($insert["status"], $insert["message"]);
  //     }
}

?>

<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../public/assets/sales-order.css">
<style>
  .select2-results .btn-row a.add-btn {
    display: none;
  }

  .tick-icon {
    align-self: center;
  }

  span.select2-container.select2-container--default,
  span.select2-container.select2-container--default.select2-container--open {
    z-index: 9999;
    width: 100% !important;
  }

  /* .dividerDiv {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0px;
    border-radius: 5px;
  } */

  .previewDiv {
    margin: 50px 18px;
    box-shadow: 0 0 5px #e6e6e6;
  }

  .div002 {
    padding: 0px 10px;
    border: 1px solid #d1d1d1;
    border-radius: 5px;
  }
  .invoice-format-card .card-header:after {
    display: none;
  }
</style>

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

            <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

            <li class="breadcrumb-item"><a href="#" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Invoice Mapping</a></li>

            <li class="back-button">
            </li>

          </ol>
          <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
          <table class="table defaultDataTable table-hover">
                            <thead>
                                <tr class="alert-light">
                                    <th class="borderNone">Sl. No.</th>
                                    <th class="borderNone">Branch</th>
                                    <th class="borderNone">Location</th>
                                    <th class="borderNone">Functionalities</th>
                                    <th class="borderNone">Invoice Varient</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sl = 1;
                                        
                                        $branch_query = queryGet("SELECT branch.branch_name,branch.branch_id,loc.othersLocation_name,loc.othersLocation_id,loc.companyFunctionalities AS func_area FROM erp_branch_otherslocation AS loc LEFT JOIN erp_branches AS branch ON loc.branch_id=branch.branch_id WHERE branch.company_id='$company_id';",true);
                                        $branch_datas =  $branch_query["data"];
                                        // console($branch_query["numRows"]);
                                        foreach($branch_datas as $branch_data)
                                        {
                                            $functionalities = $branch_data["func_area"];
                                            $location_query = queryGet("SELECT func.functionalities_name,func.functionalities_id FROM `erp_company_functionalities` AS func WHERE functionalities_id IN (".$functionalities.");",true);
                                            $location_datas = $location_query["data"];
                                            // console($location_query);

                                            foreach($location_datas as $location_data)
                                            {
                                            
                                        ?>
                                        <tr>
                                            <td><?= $sl ?></td>
                                            <td><?= $branch_data["branch_name"] ?></td>
                                            <td><?= $branch_data["othersLocation_name"] ?></td>
                                            <td><?= $location_data["functionalities_name"] ?></td>
                                            <td>
                                               <input type="hidden" name="branch[]" value="<?= $branch_data["branch_id"] ?>">
                                               <input type="hidden" name="location[]" value="<?= $branch_data["othersLocation_id"] ?>">
                                               <input type="hidden" name="functionalities[]" value="<?= $location_data["functionalities_id"] ?>">
                                            <select class="form-control prefixDropDown" name = "prefix[]" required>
                                                <?php
                                                    $query = queryGet("SELECT * FROM `erp_iv_varient` WHERE `company_id`='$company_id' AND `status`='active'",true);
                                                    $datas = $query["data"];
                                                    foreach($datas as $data)
                                                    {
                                                      $branch_id = $branch_data["branch_id"];
                                                      $functional_id = $location_data["functionalities_id"];
                                                      $location_id = $branch_data["othersLocation_id"];
                                                      $selected = queryGet("SELECT * FROM `erp_branch_func_varient_map` WHERE `company_id`='$company_id' AND `branch_id`='$branch_id' AND `functional_area_id`='$functional_id' AND `location_id`='$location_id' AND `status`='1'",false);
                                                      $selected_data = $selected["data"];
                                                ?>
                                                    <option value="<?= $data["id"] ?>" <?php if($selected_data["iv_varient_id"] == $data["id"]) echo "selected"; ?>><?= $data["title"] ?></option>
                                                <?php
                                                    }
                                                ?>
                                                </select>
                                            </td>
                                            
                                        </tr>

                        
                                <?php 
                                $sl++;
                                                }
                                            }
                                ?>

                            </tbody>
                        </table>

                        <div class="row">
                   
                    <button type="submit" name = "submitInvNumber" value="Submit Number" class="btn btn-primary float-right mt-3 mb-3">Map</button>
                  </div>
          </form>

        </div>
      </div>
      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          ...
        </div>
      </section>
    </div>
  <?php
include("common/footer.php");
  ?>