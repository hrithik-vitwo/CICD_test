<?php
function check_userName($ar){

    $check_user_name = queryGet("SELECT * FROM `tbl_branch_admin_details` WHERE `fldAdminUserName`='".$ar."'");
   
   if($check_user_name['numRows'] == 0){
   echo $ar;
   }
   else{

    $ar_new = $ar.rand(100,1000);
    check_userName($ar_new);



   }


}


?>