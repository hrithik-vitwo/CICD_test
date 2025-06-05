<?php
require_once("../../../app/v1/connection-branch-admin.php");
$dbObj = new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if($_GET['act']=="formData"){
        $res=[];
        // Main Query 
        $sql="SELECT 
    j.jv_no,
    j.documentNo,
    j.documentDate,
    j.postingDate,
    j.journalEntryReference AS entry_type,
    COALESCE(d.glId, c.glId) AS gl_id,
    coa.gl_label,
    IFNULL(d.debit_id, 0) AS debit_id,
    IFNULL(d.debit_amount, 0) AS debit_amount,
    IFNULL(c.credit_id, 0) AS credit_id,
    IFNULL(c.credit_amount, 0) AS credit_amount
FROM 
    erp_acc_journal j
LEFT JOIN 
    erp_acc_debit d ON d.journal_id = j.id
LEFT JOIN 
    erp_acc_credit c ON c.journal_id = j.id 
                     AND (d.glId = c.glId OR d.glId IS NULL OR c.glId IS NULL)
JOIN 
    erp_acc_coa_1_table AS coa ON coa.id = COALESCE(d.glId, c.glId)
WHERE 
    COALESCE(d.glId, c.glId) IN (
        SELECT DISTINCT masterList.parentGlId 
        FROM (
            SELECT customer_code AS subGlCode, trade_name AS subGlName, parentGlId, 'Customer' AS type 
            FROM erp_customer 
            WHERE company_id = $company_id 
            UNION ALL 
            SELECT vendor_code AS subGlCode, trade_name AS subGlName, parentGlId, 'Vendor' AS type 
            FROM erp_vendor_details 
            WHERE company_id = $company_id 
            UNION ALL 
            SELECT itemCode AS subGlCode, itemName AS subGlName, parentGlId, 'Item' AS type 
            FROM erp_inventory_items 
            WHERE company_id = $company_id 
            UNION ALL 
            SELECT acc_code AS subGlCode, bank_name AS subGlName, parent_gl AS parentGlId, 'Bank' AS type 
            FROM erp_acc_bank_cash_accounts 
            WHERE company_id = $company_id 
            UNION ALL 
            SELECT sl_code AS subGlCode, sl_name AS subGlName, parentGlId, 'SubGL' AS type 
            FROM erp_extra_sub_ledger 
            WHERE company_id = $company_id
        ) AS masterList
    )
    AND j.company_id = $company_id 
    AND j.branch_id = $branch_id 
    AND j.location_id = $location_id 
    AND IFNULL(d.subGlCode, '') = '' 
    AND IFNULL(c.subGlCode, '') = '' 
ORDER BY 
    j.postingDate";

        $queryRes=$dbObj->queryGet($sql,true);

        console($queryRes);
        if($queryRes['numRows']>0){
            $res = [
                "status" => TRUE,
                "msg" => "Data Founded",
                "data" => $queryRes['data'],
                "sql" => $sql
              ];
        }else{
            $res = [
                "status" => FALSE,
                "msg" => "No Data Found",
                "sql" => $sql
              ];
        }
        echo json_encode($res);
    }
    if($_GET['act']=="subLedgerByGlId"){
        $gl = $_GET['glId'];

        $subchartOfAcc = queryGet("SELECT customer_code AS code, trade_name AS name, parentGlId, 'Customer' AS type
          FROM erp_customer WHERE `parentGlId` = $gl AND company_id =$company_id
          UNION ALL
          SELECT vendor_code AS code, trade_name AS name, parentGlId, 'Vendor' AS type
          FROM erp_vendor_details WHERE `parentGlId` = $gl AND company_id =$company_id
          UNION ALL
          SELECT itemCode AS code, itemName AS name, parentGlId, 'Item' AS type
          FROM erp_inventory_items WHERE `parentGlId` = $gl AND company_id =$company_id
          UNION ALL
          SELECT acc_code AS code, bank_name AS name, parent_gl AS parentGlId, 'Bank' AS type
          FROM erp_acc_bank_cash_accounts WHERE `parent_gl` = $gl AND company_id =$company_id
          UNION ALL
          SELECT sl_code AS code, sl_name AS name, parentGlId, 'SubGL' AS type
          FROM erp_extra_sub_ledger WHERE `parentGlId` = $gl AND company_id =$company_id ", true);
      
        echo json_encode($subchartOfAcc);        
    }
}


if($_SERVER['REQUEST_METHOD'] == "POST"){

    if($_POST['act']=="updateSubGl"){
        $id=$_POST['id'];
        $glId=$_POST['glId'];
        $subGlName=$_POST['subGlName'];
        $subGlCode=$_POST['subGlCode'];
        $sql="";
        if($_POST['type']=="cr"){
            $sql="UPDATE `erp_acc_credit` SET `subGlCode` = '".$subGlCode."', `subGlName` = '".$subGlName."' WHERE `credit_id` = '".$id."' AND glId = '".$glId."'";
        }else if($_POST['type']=="dr"){
            $sql="UPDATE `erp_acc_debit` SET `subGlCode` = '".$subGlCode."', `subGlName` = '".$subGlName."' WHERE `debit_id` = '".$id."' AND glId='".$glId."'";
        }
      
        $res=$dbObj->queryUpdate($sql);
        echo json_encode($res);
    }

}
?>