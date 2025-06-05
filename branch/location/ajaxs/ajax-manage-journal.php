<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("pagination/common-pagination.php");

require_once("../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');
$BranchSoObj = new BranchSo();
session_start();

if ($_POST['act'] == 'tdata') {
  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $_SESSION['columnMapping'] = $_POST['columnMapping'];
    if (isset($_SESSION['columnMapping'])) {
      $columnMapping = $_SESSION['columnMapping'];
  }

    $limit_per_Page = isset($_POST['limit']) && $_POST['limit'] != '' ? $_POST['limit'] : 25;

    $page_no = isset($_POST['pageNo']) ? (int)$_POST['pageNo'] : 1;
    $page_no = max(1, $page_no);

    $offset = ($page_no - 1) * $limit_per_Page;
    $maxPagesl = $page_no * $limit_per_Page;
    $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
    $formObj = $_POST['formDatas'];
    $cond = "";
    $implodeFrom = implode('', array_map(function ($slag, $data) {
      $conds = "";

      // Apply COALESCE for total_debit and total_credit
      $coalesceFields = ['debit_table.total_debit', 'credit_table.total_credit'];
      $slagFormatted = in_array($slag, $coalesceFields) ? "COALESCE($slag, 0)" : $slag;

      // Ensure journal. prefix where needed
      if (!in_array($slag, $coalesceFields) && strpos($slag, 'journal.') === false && $slag !== 'sl_no') {
        $slagFormatted = "journal." . $slag;
      }

      // Handle date fields correctly
      if (in_array($slag, ['journal.postingDate', 'journal.journal_created_at'])) {
        if ($data['operatorName'] === 'BETWEEN' && is_array($data['value'])) {
          $conds .= " DATE($slagFormatted) BETWEEN '" . $data['value']['fromDate'] . "' AND '" . $data['value']['toDate'] . "' ";
        } else {
          $conds .= " DATE($slagFormatted) " . $data['operatorName'] . " '" . $data['value'] . "' ";
        }
      }
      // Handle 'created_by' and 'updated_by' conditions
      else if ($slag === 'journal.journal_created_by' || $slag === 'journal.updated_by') {
        if (in_array($data['operatorName'], ['LIKE', 'NOT LIKE'])) {
          $opr = ($data['operatorName'] === 'LIKE') ? 'LIKE' : 'NOT LIKE';
          $resultList = getAdminUserIdByName($data['value']);

          if (strpos($resultList, ',') !== false) {
            $opr = ($opr === 'LIKE') ? 'IN' : 'NOT IN';
            $resultList = (!empty($resultList)) ? $resultList : '0';
            $conds .= "$slagFormatted $opr (" . $resultList . ")";
          } else {
            $resultList = (!empty($resultList)) ? $resultList : '0';
            $conds .= "$slagFormatted $opr '%" . $resultList . "%'";
          }
        }
      }
      // General fallback condition, excluding date fields
      else {
        $conds .= "$slagFormatted " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }
  
  

   

    // $sts = " AND `status` !='deleted'";
    // $sql_list = "SELECT temp_table1.id, temp_table1.jv_no, temp_table1.party_code, temp_table1.party_name, temp_table1.parent_id, temp_table1.parent_slug, temp_table1.refarenceCode, temp_table1.documentNo, temp_table1.documentDate, temp_table1.postingDate, temp_table1.remark, temp_table1.total_debit, temp_table1.reverse_jid, temp_table1.journalEntryReference, temp_table1.created_by,
    // temp_table1.created_at, SUM(credit.credit_amount) AS total_credit FROM ( SELECT journal.id, journal.jv_no, journal.party_code, journal.party_name, journal.parent_id, journal.parent_slug, journal.refarenceCode, journal.documentNo, journal.documentDate, journal.postingDate, journal.remark, journal.reverse_jid, journal.journalEntryReference,journal.journal_created_at as created_at,
    //     journal.journal_created_by as created_by,SUM(debit.debit_amount) AS total_debit FROM `" . ERP_ACC_JOURNAL . "` AS journal LEFT JOIN `" . ERP_ACC_DEBIT . "` AS debit ON journal.id = debit.journal_id WHERE journal.parent_slug = 'journal' $cond AND journal.journal_status = 'active' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id GROUP BY journal.id, journal.jv_no, journal.party_code, journal.party_name, journal.parent_id, journal.parent_slug, journal.refarenceCode, journal.documentNo, journal.documentDate, journal.postingDate, journal.remark, journal.reverse_jid, journal.journalEntryReference ) AS temp_table1 LEFT JOIN `" . ERP_ACC_CREDIT . "` AS credit ON temp_table1.id = credit.journal_id GROUP BY temp_table1.id, temp_table1.jv_no, temp_table1.party_code, temp_table1.party_name, temp_table1.parent_id, temp_table1.parent_slug, temp_table1.refarenceCode, temp_table1.documentNo, temp_table1.documentDate, temp_table1.postingDate, temp_table1.remark, temp_table1.reverse_jid, temp_table1.journalEntryReference, temp_table1.total_debit ORDER BY temp_table1.id DESC";  // old query

    $sql_list="SELECT
    journal.id,
    journal.jv_no,
    journal.party_code,
    journal.party_name,
    journal.parent_id,
    journal.parent_slug,
    journal.refarenceCode,
    journal.documentNo,
    journal.documentDate,
    journal.postingDate,
    journal.remark,
    COALESCE(debit_table.total_debit, 0) AS total_debit,
    COALESCE(credit_table.total_credit, 0) AS total_credit,
    journal.reverse_jid,
    journal.journalEntryReference,
    journal.journal_created_by AS created_by,
    journal.journal_created_at AS created_at
FROM
    erp_acc_journal AS journal
LEFT JOIN (
    SELECT journal_id, SUM(debit_amount) AS total_debit
    FROM erp_acc_debit
    GROUP BY journal_id
) AS debit_table ON journal.id = debit_table.journal_id
LEFT JOIN (
    SELECT journal_id, SUM(credit_amount) AS total_credit
    FROM erp_acc_credit
    GROUP BY journal_id
) AS credit_table ON journal.id = credit_table.journal_id
WHERE 1  " . $cond . " AND
    journal.parent_slug = 'journal'
    AND journal.journal_status = 'active'
    AND journal.company_id = $company_id
    AND journal.branch_id = $branch_id
    AND journal.location_id = $location_id
ORDER BY
    journal.id DESC";

    // $sql_Mainqry = $sql_list . "  ORDER BY so.so_id DESC LIMIT " . $offset . "," . $limit_per_Page . ";";
    $sql_Mainqry = $sql_list." LIMIT ". $offset . "," . $limit_per_Page . ";";
    $sqlMainQryObj = queryGet($sql_Mainqry, true);

    $dynamic_data = [];
    $num_list = $sqlMainQryObj['numRows'];
    $sql_data = $sqlMainQryObj['data'];
    $output = "";
    $limitText = "";
    $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

    if ($num_list > 0) {
      foreach ($sql_data as $data) {
        $dynamic_data[] = [
          "sl_no" => $sl,
          "id"=>$data['id'],
          "journal.jv_no" => $data['jv_no'],
          "journal.documentNo" => $data['documentNo'],
          "journal.postingDate" => formatDateWeb($data['postingDate']),
          "journal.party_code" => $data['party_code'],
          "journal.party_name" => $data['party_name'],
          "journal.journal_entry_ref" => $data['journal_entry_ref'],
          "journal.journalEntryReference" => $data['journalEntryReference'],
          "journal.remark" => $data['remark'],
          "debit_table.total_debit" => decimalValuePreview($data['total_debit']),
          "credit_table.total_credit" => decimalValuePreview($data['total_credit']),
          "journal.reverse_jid" => $data['reverse_jid'],
          "journal.journal_created_at" => formatDateWeb($data['created_at']),
          "journal.journal_created_by" => getCreatedByUser($data['created_by']),
        ];
        $sl++;
      }
      $output .= "</table>";
      $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";
      $queryset = queryGet($sqlRowCount);
      $totalRows = $queryset['data']['row_count'];
      $total_page = ceil($totalRows / $limit_per_Page);

      $output .= pagiNation($page_no, $total_page);

      $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';
      
      // $csvContent=exportToExcelAll($sql_list,json_encode($columnMapping));
      // $csvContentBypagination=exportToExcelByPagin($sql_Mainqry,json_encode($columnMapping));

      $res = [
        "status" => true,
        "msg" => "Success",
        "data" => $dynamic_data,
        "pagination" => $output,
        "limitTxt" => $limitText,
        "limit_per_Page" => $limit_per_Page,
        "sql" => $sql_list
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
}
if ($_POST['act'] == 'alldata') {
  if ($_SERVER["REQUEST_METHOD"] == "POST") {

      $formObj = $_POST['formDatas'];
      $cond = "";
    // $cond = "AND DATE(so_date) BETWEEN '" . $previousDate . "' AND '" . $currentDate . "'";


    $implodeFrom = implode('', array_map(function ($slag, $data) {
      $conds = "";

      // Apply COALESCE for total_debit and total_credit
      $coalesceFields = ['debit_table.total_debit', 'credit_table.total_credit'];
      $slagFormatted = in_array($slag, $coalesceFields) ? "COALESCE($slag, 0)" : $slag;

      // Ensure journal. prefix where needed
      if (!in_array($slag, $coalesceFields) && strpos($slag, 'journal.') === false && $slag !== 'sl_no') {
        $slagFormatted = "journal." . $slag;
      }

      // Handle date fields correctly
      if (in_array($slag, ['journal.updated_at', 'journal.created_at', 'journal.valid_from', 'journal.valid_upto'])) {
        if ($data['operatorName'] === 'BETWEEN' && is_array($data['value'])) {
          $conds .= " DATE($slagFormatted) BETWEEN '" . $data['value']['fromDate'] . "' AND '" . $data['value']['toDate'] . "' ";
        } else {
          $conds .= " DATE($slagFormatted) " . $data['operatorName'] . " '" . $data['value'] . "' ";
        }
      }
      // Handle 'created_by' and 'updated_by' conditions
      else if ($slag === 'journal.journal_created_by' || $slag === 'journal.updated_by') {
        if (in_array($data['operatorName'], ['LIKE', 'NOT LIKE'])) {
          $opr = ($data['operatorName'] === 'LIKE') ? 'LIKE' : 'NOT LIKE';
          $resultList = getAdminUserIdByName($data['value']);

          if (strpos($resultList, ',') !== false) {
            $opr = ($opr === 'LIKE') ? 'IN' : 'NOT IN';
            $conds .= "$slagFormatted $opr (" . $resultList . ")";
          } else {
            $conds .= "$slagFormatted $opr '%" . $resultList . "%'";
          }
        }
      }
      // General fallback condition, excluding date fields
      else {
        $conds .= "$slagFormatted " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }
    


    $sql_list="SELECT
    journal.id,
    journal.jv_no,
    journal.party_code,
    journal.party_name,
    journal.parent_id,
    journal.parent_slug,
    journal.refarenceCode,
    journal.documentNo,
    journal.documentDate,
    journal.postingDate,
    journal.remark,
    COALESCE(debit_table.total_debit, 0) AS total_debit,
    COALESCE(credit_table.total_credit, 0) AS total_credit,
    journal.reverse_jid,
    journal.journalEntryReference,
    journal.journal_created_by AS created_by,
    journal.journal_created_at AS created_at
FROM
    erp_acc_journal AS journal
LEFT JOIN (
    SELECT journal_id, SUM(debit_amount) AS total_debit
    FROM erp_acc_debit
    GROUP BY journal_id
) AS debit_table ON journal.id = debit_table.journal_id
LEFT JOIN (
    SELECT journal_id, SUM(credit_amount) AS total_credit
    FROM erp_acc_credit
    GROUP BY journal_id
) AS credit_table ON journal.id = credit_table.journal_id
WHERE 1  " . $cond . " AND
    journal.parent_slug = 'journal'
    AND journal.journal_status = 'active'
    AND journal.company_id = $company_id
    AND journal.branch_id = $branch_id
    AND journal.location_id = $location_id
ORDER BY
    journal.id DESC";


      $dynamic_data_all = [];
      $sqlMainQryObjall = queryGet($sql_list, true);
      $sql_data_all = $sqlMainQryObjall['data'];
      $num_list = $sqlMainQryObjall['numRows'];
      if ($num_list > 0) {
          $sl = 1;
          foreach ($sql_data_all as $data) {

        $dynamic_data_all[] = [
          "sl_no" => $sl,
          "journal.jv_no" => $data['jv_no'],
          "journal.documentNo" => $data['documentNo'],
          "journal.postingDate" => formatDateWeb($data['postingDate']),
          "journal.party_code" => $data['party_code'],
          "journal.party_name" => $data['party_name'],
          "journal.journal_entry_ref" => $data['journal_entry_ref'],
          "journal.journalEntryReference" => $data['journalEntryReference'],
          "journal.remark" => $data['remark'],
          "debit_table.total_debit" => decimalValuePreview($data['total_debit']),
          "credit_table.total_credit" => decimalValuePreview($data['total_credit']),
          "journal.reverse_jid" => $data['reverse_jid'],
          "journal.journal_created_at" => formatDateWeb($data['created_at']),
          "journal.journal_created_by" => getCreatedByUser($data['created_by']),
        ];
        $sl++;
      }
          $dynamic_data_all=json_encode($dynamic_data_all);
          $exportToExcelAll =exportToExcelAll($dynamic_data_all,$_POST['coloum'],$_POST['sql_data_checkbox']);
          $res = [
              "status" => true,
              "msg" => "CSV all generated",
              'csvContentall' => $exportToExcelAll,
              "sql" => $sql_list,
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
}