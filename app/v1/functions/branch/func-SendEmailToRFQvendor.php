<?php

function SendEmailToRFQvendor($data = [])
{
    global $dbCon;   
    
    // print_r($data);
    
    foreach($data as $key=> $row){
        // print_r($row);
        $to="";
        $sub="";
        $msg="";
        $id=base64_encode(base64_encode(base64_encode(base64_encode($row['rfqVendorId'].'|'.$row['vendor_email'].'|'.$row['vendor_type']))));
        $link=BASE_URL.'vendor-rfq.php?inf='.$id;
        $to=$row['vendor_email'];
        $new_rfq = $row['rfqCode'];
        $sub='NEW RFQ - '.$new_rfq;

        $vendor_sql = queryGet("SELECT * FROM `erp_vendor_details` WHERE `vendor_id` ='".$row['vendorId']."'");
        $mailValid = $vendor_sql['data']['isMailValid'] ;
        if($mailValid == 'yes'){
           
        
        $msg='Hi, '.$row['vendor_name'].'<br>
        Click The <a href="'.$link.'">link here</a>
        <br>';
        $return=SendMailByMySMTPmailTemplate($to,$sub,$msg);
        
        if(isset($return)){
        $query="UPDATE ".ERP_RFQ_VENDOR_LIST."
            SET `emailSendStatus`= '1'
            WHERE `rfqVendorId`= '".$row['rfqVendorId']."'";
           $message =  queryUpdate($query);
        }
        }
        else{
            $query="UPDATE ".ERP_RFQ_VENDOR_LIST."
            SET `emailSendStatus`= '1'
            WHERE `rfqVendorId`= '".$row['rfqVendorId']."'";
           $message =  queryUpdate($query);  
        }
        // print_r($message);
    
        }


}

?>