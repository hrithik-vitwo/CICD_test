<?php
class ComplianceGSTR1Report
{
    private $company_id = null;
    private $branch_id = null;

    function __construct()
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
    }

    function getGstr1FillingTableList(){
        $dbObj = new Database();
        return $dbObj->queryGet('SELECT * FROM erp_compliance_gstr1 WHERE company_id = '.$this->company_id.' AND branch_id = '.$this->branch_id.' ORDER BY id DESC', true);
    }

    function getGstr1FillingDetails($id=0){
        $dbObj = new Database();
        return $dbObj->queryGet('SELECT * FROM erp_compliance_gstr1 WHERE company_id = '.$this->company_id.' AND branch_id = '.$this->branch_id.' AND id='.$id);
    }

}