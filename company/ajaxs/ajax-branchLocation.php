<?php
include_once("../../app/v1/connection-company-admin.php");

$branchId = $_POST['branchId'];

$sql = "SELECT * FROM `erp_branch_otherslocation` WHERE `branch_id`=$branchId";
if($res = $dbCon->query($sql)){
    if($res->num_rows > 0 ){
        while($row = $res->fetch_assoc()){
        ?>
        <div class="card">
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Location Name: </strong><?=$row['othersLocation_name']?></li>
            <li class="list-group-item"><strong>Street Name: </strong><?=$row['othersLocation_street_name']?></li>
            <li class="list-group-item"><strong>Status: </strong><?=$row['othersLocation_status']?></li>
        </ul>
        </div>
        <?php
        }
    }else{ ?>
        <div class="alert alert-danger" style="font-size:1.2em"><strong>Location not found!</strong> <span>In this branch.</span></div>
        <?php
    }
}else{ ?>
    <div class="alert alert-secondary" style="font-size:1.2em"><strong>Select A Branch!</strong></div>
<?php
}
?>