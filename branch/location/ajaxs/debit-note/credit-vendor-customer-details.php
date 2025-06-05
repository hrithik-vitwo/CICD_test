<?php

require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
// echo '<option value=""  data-attr = "vendor">'.$_REQUEST['listtype'].'</option>';
// console($_REQUEST);

 $option = $_REQUEST['listtype']??'Customer';
        $responseData = [];
        $start=0;
        $limit=30;
        $cond='';

    if ($option == 'Customer') {
            if(isset($_REQUEST['searchTerm']) && !empty($_REQUEST['searchTerm'])){
                $searchTerm = $_REQUEST['searchTerm'];
               
                $cond .= " AND (`customer_code` like '%" . $searchTerm . "%' OR `trade_name` like '%" . $searchTerm . "%'  OR `customer_pan` like '%" . $searchTerm . "%'  OR `legal_name` like '%" . $searchTerm . "%' OR `customer_authorised_person_name` like '%" . $searchTerm . "%' OR `customer_gstin` like '%" . $searchTerm . "%')";
                $limit=50;      
            }
            if(!empty($_REQUEST['page'])){
                $start=$_REQUEST['page']*$limit;
            }
        
            $custsql="SELECT CONCAT(customer_id,'|customer|', trade_name) as id, CONCAT(customer_code,' | ',trade_name) as text FROM `" . ERP_CUSTOMER . "` WHERE 1 ".$cond." AND company_id='" . $company_id . "' AND `customer_status`='active' LIMIT $start,$limit";
            $query = queryGet($custsql, true);
          if ($query['status'] = "success") {
            
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = $query['data'];
            $datalist=$query['data'];
          } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
            $returnData['data'] = [];
            
            $datalist=[];
          }
    } else {

        if(isset($_REQUEST['searchTerm']) && !empty($_REQUEST['searchTerm'])){
            $searchTerm = $_REQUEST['searchTerm'];
           
            $cond .= " AND (`vendor_code` like '%" . $searchTerm . "%' OR `trade_name` like '%" . $searchTerm . "%'  OR `vendor_pan` like '%" . $searchTerm . "%'  OR `legal_name` like '%" . $searchTerm . "%' OR `vendor_authorised_person_name` like '%" . $searchTerm . "%' OR `vendor_gstin` like '%" . $searchTerm . "%')";
            $limit=50;      
        }
        if(!empty($_REQUEST['page'])){
            $start=$_REQUEST['page']*$limit;
        }
    
        $custsql="SELECT CONCAT(vendor_id,'|vendor|', trade_name) as id, CONCAT(vendor_code,' | ',trade_name) as text FROM `erp_vendor_details` WHERE 1 ".$cond." AND company_id='" . $company_id . "' AND `vendor_status`='active' LIMIT $start,$limit";
        $query = queryGet($custsql, true);
      if ($query['status'] = "success") {
        
        $returnData['status'] = "success";
        $returnData['message'] = "Data found";
        $returnData['data'] = $query['data'];
        $datalist=$query['data'];
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData['data'] = [];
        
        $datalist=[];
      }
    }

    
    echo json_encode($datalist);
