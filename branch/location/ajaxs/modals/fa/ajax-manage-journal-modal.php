<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/common/templates/template-manage-journal.php");

$headerData = array('Content-Type: application/json');

$dbObj = new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET" &&  $_GET["act"] == "modalData") {
    $id = $_GET['id'];
    $sql_list = "SELECT temp_table1.id, temp_table1.jv_no, temp_table1.party_code, temp_table1.party_name, temp_table1.parent_id, temp_table1.parent_slug, temp_table1.refarenceCode, temp_table1.documentNo, temp_table1.documentDate, temp_table1.postingDate, temp_table1.remark, temp_table1.total_debit, temp_table1.reverse_jid, temp_table1.journalEntryReference,SUM(credit.credit_amount) AS total_credit,temp_table1.created_by, temp_table1.created_at,temp_table1.updated_at, temp_table1.updated_by FROM ( SELECT journal.id, journal.jv_no, journal.party_code, journal.party_name, journal.parent_id, journal.parent_slug, journal.refarenceCode, journal.documentNo, journal.documentDate, journal.postingDate, journal.remark, journal.reverse_jid, journal.journalEntryReference,journal.journal_created_at as created_at, journal.journal_created_by as created_by,journal.journal_updated_at AS updated_at, journal.journal_updated_by AS updated_by,SUM(debit.debit_amount) AS total_debit FROM `" . ERP_ACC_JOURNAL . "` AS journal LEFT JOIN `" . ERP_ACC_DEBIT . "` AS debit ON journal.id = debit.journal_id WHERE journal.parent_slug = 'journal' $cond AND journal.journal_status = 'active' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.id=$id GROUP BY journal.id, journal.jv_no, journal.party_code, journal.party_name, journal.parent_id, journal.parent_slug, journal.refarenceCode, journal.documentNo, journal.documentDate, journal.postingDate, journal.remark, journal.reverse_jid, journal.journalEntryReference ) AS temp_table1 LEFT JOIN `" . ERP_ACC_CREDIT . "` AS credit ON temp_table1.id = credit.journal_id GROUP BY temp_table1.id, temp_table1.jv_no, temp_table1.party_code, temp_table1.party_name, temp_table1.parent_id, temp_table1.parent_slug, temp_table1.refarenceCode, temp_table1.documentNo, temp_table1.documentDate, temp_table1.postingDate, temp_table1.remark, temp_table1.reverse_jid, temp_table1.journalEntryReference, temp_table1.total_debit";
    $sqlMainQryObj = $dbObj->queryGet($sql_list);
    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    if ($num_list > 0) {
        $dynamic_data = [];

        $creditDetail = $dbObj->queryGet("SELECT * FROM `erp_acc_credit` WHERE journal_id =$id", true);
        $debitDetail = $dbObj->queryGet("SELECT * FROM `erp_acc_debit` WHERE journal_id=$id", true);
        $dynamic_data = [
            "dataObj" => $data,
            "creditDetail" => $creditDetail['data'],
            "debitDetail" => $debitDetail['data'],
            "totalDebitInWord" => number_to_words_indian_rupees($data['total_debit']),
            "totalCreditInWord" => number_to_words_indian_rupees($data['total_credit']),
            "createdBy" => getCreatedByUser($data['created_by']),
            "updatedBy" => getCreatedByUser($data['updated_by']),
            "createdAt" => formatDateORDateTime($data['created_at']),
            "updateAt" => formatDateORDateTime($data['updated_at']),
            "companyCurrency" =>getSingleCurrencyType($company_currency)
        ];
        $res = [
            "status" => true,
            "sql" => $sql_list,
            "msg" => "success",
            "data" => $dynamic_data
        ];
    } else { 
        $res = [
            "status" => false,
            "msg" => "Error!",
            "sql" => $sql_list
        ];
    }
    echo json_encode($res);
}
if($_SERVER['REQUEST_METHOD'] == 'GET'&& $_GET["act"]=="classicView"){
    $id = $_GET['id'];
    $templateJournal = new TemplateJournal();
    $templateJournal->printManageJouranl($id);

}
