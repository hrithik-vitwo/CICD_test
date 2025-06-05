<?php
require_once("../../app/v1/connection-branch-admin.php");

//$otherAddress = $_POST['otherAddressId']; 

// console($_REQUEST);

?>
<!-- <span class="text-danger">Deleted Successfull! <?php echo $_POST['otherAddressId'] ?></span> -->

<?php 
  $del = "DELETE FROM `erp_vendor_bussiness_places` WHERE vendor_business_id='".$_POST['otherAddressId']."'";
  $dbCon->query($del);
?>

