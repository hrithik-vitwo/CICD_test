<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");

// Db Object For Database Related actions
$dbObj = new Database();
?>
<div class="content-wrapper">
    <div class="container-fluid">
        <section class="gstr-1">
            <h4 class="text-lg font-bold py-3 mt-2 mb-0">Debit / Credit Inconsistent Data List</h4>

            <div class="card mt-2">
                <div class="card-body p-0" style="overflow: auto;">
                    <table id="debitCreditWrongTable" width="100" class="table table-hover defaultDataTable gst-consised-view">
                        <thead>
                            <tr>
                                <th rowspan="2" class="left-sticky position-index">Journal Number</th>
                                <th rowspan="2" class="left-sticky position-index">Document Number</th>
                                <th rowspan="2" class="left-sticky position-index">Entry Type</th>
                                <th rowspan="2" class="left-sticky position-index">Document Date</th>
                                <th rowspan="2" class="left-sticky position-index">Posting Date</th>
                                <th rowspan="2" class="left-sticky position-index">Gl label</th>
                                <th rowspan="2" class="left-sticky position-index">Sub GL label</th>
                                <th rowspan="2" class="left-sticky position-index">Credit/Debit Amount</th>
                                <th rowspan="2" class="left-sticky position-index">Type</th>
                                <th rowspan="2" class="left-sticky position-index">Action</th>
                            </tr>
                        </thead>
                        <tbody id="debitCreditWrongBody">  
                            <?php
                            // $sql = "SELECT j.jv_no, j.documentNo, j.documentDate, j.postingDate, d.glId AS gl_id, coa.gl_label, d.debit_id AS 'credit_debit_id', IFNULL(d.debit_amount, 0) AS 'credit_debit_amount', 'dr' AS type FROM erp_acc_debit d JOIN erp_acc_journal j ON d.journal_id = j.id JOIN erp_acc_coa_1_table AS coa ON coa.id = d.glId WHERE d.glId IN ( SELECT DISTINCT masterList.parentGlId FROM ( SELECT customer_code AS subGlCode, trade_name AS subGlName, parentGlId, 'Customer' AS type FROM erp_customer WHERE company_id = $company_id UNION ALL SELECT vendor_code AS subGlCode, trade_name AS subGlName, parentGlId, 'Vendor' AS type FROM erp_vendor_details WHERE company_id = $company_id UNION ALL SELECT itemCode AS subGlCode, itemName AS subGlName, parentGlId, 'Item' AS type FROM erp_inventory_items WHERE company_id = $company_id UNION ALL SELECT acc_code AS subGlCode, bank_name AS subGlName, parent_gl AS parentGlId, 'Bank' AS type FROM erp_acc_bank_cash_accounts WHERE company_id = $company_id UNION ALL SELECT sl_code AS subGlCode, sl_name AS subGlName, parentGlId, 'SubGL' AS type FROM erp_extra_sub_ledger WHERE company_id = $company_id ) AS masterList) AND j.company_id=$company_id AND j.branch_id=$branch_id AND j.location_id=$location_id AND d.subGlCode = '' UNION ALL SELECT j.jv_no, j.documentNo, j.documentDate, j.postingDate, c.glId AS gl_id, coa.gl_label, c.credit_id AS 'credit_debit_id', IFNULL(c.credit_amount, 0) AS 'credit_debit_amount', 'cr' AS type FROM erp_acc_credit c JOIN erp_acc_journal j ON c.journal_id = j.id JOIN erp_acc_coa_1_table AS coa ON coa.id = c.glId WHERE c.glId IN ( SELECT DISTINCT masterList.parentGlId FROM ( SELECT customer_code AS subGlCode, trade_name AS subGlName, parentGlId, 'Customer' AS type FROM erp_customer WHERE company_id = $company_id UNION ALL SELECT vendor_code AS subGlCode, trade_name AS subGlName, parentGlId, 'Vendor' AS type FROM erp_vendor_details WHERE company_id = $company_id UNION ALL SELECT itemCode AS subGlCode, itemName AS subGlName, parentGlId, 'Item' AS type FROM erp_inventory_items WHERE company_id = $company_id UNION ALL SELECT acc_code AS subGlCode, bank_name AS subGlName, parent_gl AS parentGlId, 'Bank' AS type FROM erp_acc_bank_cash_accounts WHERE company_id = $company_id UNION ALL SELECT sl_code AS subGlCode, sl_name AS subGlName, parentGlId, 'SubGL' AS type FROM erp_extra_sub_ledger WHERE company_id = $company_id ) AS masterList) AND j.company_id=$company_id AND j.branch_id=$branch_id AND j.location_id=$location_id AND c.subGlCode = '' ORDER BY postingDate";
                            $sql="SELECT j.jv_no, j.documentNo, j.documentDate, j.postingDate, j.journalEntryReference AS entry_type, d.glId AS gl_id, coa.gl_label, d.debit_id AS 'credit_debit_id', IFNULL(d.debit_amount, 0) AS 'credit_debit_amount', 'dr' AS type FROM erp_acc_debit d JOIN erp_acc_journal j ON d.journal_id = j.id JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON coa.id = d.glId WHERE d.glId IN ( SELECT DISTINCT masterList.parentGlId FROM ( SELECT customer_code AS subGlCode, trade_name AS subGlName, parentGlId, 'Customer' AS type FROM erp_customer WHERE company_id = $company_id UNION ALL SELECT vendor_code AS subGlCode, trade_name AS subGlName, parentGlId, 'Vendor' AS type FROM erp_vendor_details WHERE company_id = $company_id UNION ALL SELECT itemCode AS subGlCode, itemName AS subGlName, parentGlId, 'Item' AS type FROM erp_inventory_items WHERE company_id = $company_id UNION ALL SELECT acc_code AS subGlCode, bank_name AS subGlName, parent_gl AS parentGlId, 'Bank' AS type FROM erp_acc_bank_cash_accounts WHERE company_id = $company_id UNION ALL SELECT sl_code AS subGlCode, sl_name AS subGlName, parentGlId, 'SubGL' AS type FROM erp_extra_sub_ledger WHERE company_id = $company_id ) AS masterList) AND j.company_id=$company_id AND j.branch_id=$branch_id AND j.location_id=$location_id AND (d.subGlCode = '' OR c.subGlCode = '0') UNION ALL SELECT j.jv_no, j.documentNo, j.documentDate, j.postingDate, j.journalEntryReference AS entry_type, c.glId AS gl_id, coa.gl_label, c.credit_id AS 'credit_debit_id', IFNULL(c.credit_amount, 0) AS 'credit_debit_amount', 'cr' AS type FROM erp_acc_credit c JOIN erp_acc_journal j ON c.journal_id = j.id JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON coa.id = c.glId WHERE c.glId IN ( SELECT DISTINCT masterList.parentGlId FROM ( SELECT customer_code AS subGlCode, trade_name AS subGlName, parentGlId, 'Customer' AS type FROM erp_customer WHERE company_id = $company_id UNION ALL SELECT vendor_code AS subGlCode, trade_name AS subGlName, parentGlId, 'Vendor' AS type FROM erp_vendor_details WHERE company_id = $company_id UNION ALL SELECT itemCode AS subGlCode, itemName AS subGlName, parentGlId, 'Item' AS type FROM erp_inventory_items WHERE company_id = $company_id UNION ALL SELECT acc_code AS subGlCode, bank_name AS subGlName, parent_gl AS parentGlId, 'Bank' AS type FROM erp_acc_bank_cash_accounts WHERE company_id = $company_id UNION ALL SELECT sl_code AS subGlCode, sl_name AS subGlName, parentGlId, 'SubGL' AS type FROM erp_extra_sub_ledger WHERE company_id = $company_id ) AS masterList) AND j.company_id=$company_id AND j.branch_id=$branch_id AND j.location_id=$location_id AND (c.subGlCode = '' OR c.subGlCode = '0') ORDER BY postingDate";

                            $queryRes = $dbObj->queryGet($sql, true);
                            $queryData = $queryRes['data'];
                            $index = 0;
                            foreach ($queryData as $row) {
                                $index++;
                                $gl = $row['gl_id'];
                                $list = '';

                                $subchartOfAcc = queryGet("SELECT customer_code AS code, trade_name AS name, parentGlId, 'Customer' AS type FROM erp_customer WHERE `parentGlId` = $gl AND company_id =$company_id UNION ALL SELECT vendor_code AS code, trade_name AS name, parentGlId, 'Vendor' AS type FROM erp_vendor_details WHERE `parentGlId` = $gl AND company_id =$company_id UNION ALL SELECT itemCode AS code, itemName AS name, parentGlId, 'Item' AS type FROM erp_inventory_items WHERE `parentGlId` = $gl AND company_id =$company_id UNION ALL SELECT acc_code AS code, bank_name AS name, parent_gl AS parentGlId, 'Bank' AS type FROM erp_acc_bank_cash_accounts WHERE `parent_gl` = $gl AND company_id =$company_id UNION ALL SELECT sl_code AS code, sl_name AS name, parentGlId, 'SubGL' AS type FROM erp_extra_sub_ledger WHERE `parentGlId` = $gl AND company_id =$company_id", true);

                                if ($subchartOfAcc['status'] == 'success') {
                                    $numrows = $subchartOfAcc['numRows'];
                                    $list = '<select class="form-control subLedger select2" name="subLedger" id="subLedger_' . $index . '">';
                                    $list .= '<option value="0" selected>Select Sub Ledger</option>';

                                    foreach ($subchartOfAcc['data'] as $subchart) {
                                        $list .= '<option value="' . $subchart['code'] . '" data-parent="' . $subchart['parentGlId'] . '" data-name="' . $subchart['name'] . '" >' . $subchart['name'] . '&nbsp;||&nbsp;' . $subchart['code'] . '</option>';
                                    }

                                    $list .= '</select>';
                                }

                                $btn = '';
                                if ($row['type'] == 'cr') {
                                    $btn = '<button class="updateSubGlBtn btn btn-primary" data-type=' . $row['type'] . ' data-id=' . $row['credit_debit_id'] . ' data-index=' . $index . '  data-glid=' . $row['gl_id'] . '>Update</button>';
                                } else if ($row['type'] == 'dr') {
                                    $btn = '<button class="updateSubGlBtn btn btn-primary" data-type=' . $row['type'] . ' data-id=' . $row['credit_debit_id'] . ' data-index=' . $index . '  data-glid=' . $row['gl_id'] . ' >Update</button>';
                                } else {
                                    $btn = `--`;
                                }

                            ?>
                                <tr>
                                    <td>
                                        <p><?= $row['jv_no'] ?>
                                        <p>
                                    </td>
                                    <td>
                                        <p><?= $row['documentNo'] ?></p>
                                    </td>
                                    <td>
                                        <p><?= $row['entry_type'] ?></p>
                                    </td>
                                    <td>
                                        <p><?= formatDateORDateTime($row['documentDate']) ?></p>
                                    </td>
                                    <td>
                                        <p><?= formatDateORDateTime($row['postingDate']) ?></p>
                                    </td>
                                    <td>
                                        <p><?= $row['gl_label'] ?></p>
                                    </td>
                                    <td><?= $list ?></td>
                                    <td class="text-right">
                                        <p><?= decimalValuePreview($row['credit_debit_amount']) ?></p>
                                    </td>
                                    <td>
                                        <p><?= ($row['type'] == "cr") ? "CR" : "DR" ?></p>
                                    </td>
                                    <td><?= $btn ?></td>
                                <tr>

                                <?php
                            }

                                ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<?php
require_once("../common/footer2.php");
?>

<script>
    $('.subLedger')

        .select2()

        .on('select2:open', () => {


        });
    $(document).ready(function() {
        $(document).on("click", ".updateSubGlBtn", function(e) {
            $(this).prop('disabled', true);
            $(this).text("waiting...");

            let index = $(this).data("index");
            let type = $(this).data("type");

            let id = $(this).data("id");
            let glId = $(this).data("glid");

            let selectedOption = $(`#subLedger_${index} option:selected`);
            let subGlCode = selectedOption.val();

            let subGlName = selectedOption.data("name");
            if (subGlCode == '' || subGlCode == 0) {
                Swal.fire({
                    icon: "warning",
                    title: "Please select a option first",
                    timer: 1000,
                    showConfirmButton: false,
                })
            } else if (subGlCode != "" && subGlName != "" && subGlName != "" && glId != "") {
                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-debit-credit-wrong-data-list.php",
                    dataType: "json",
                    data: {
                        act: 'updateSubGl',
                        type,
                        subGlCode,
                        subGlName,
                        id,
                        glId,
                    },
                    beforeSend: function() {
                        $(this).prop('disabled', true);
                        $(this).removeClass("btn-primary");
                        $(this).addClass("btn-warning");
                        $(this).html("waiting...");
                    },
                    success: function(response) {
                        // console.log(response);
                        if (response.status == "success") {
                            Swal.fire({
                                icon: response.status,
                                title: response.message,
                                timer: 3000,
                                showConfirmButton: false,
                            }).then(() => {
                                location.reload();
                            });
                        }else{
                        $(this).prop('disabled', false);
                        $(this).text("update");
                        location.reload();


                        }
                    },

                });
            } else {
                alert("Invalid parameters");
            }
        });

    });
</script>