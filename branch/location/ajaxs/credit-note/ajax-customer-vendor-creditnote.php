<?php
require_once("../../../../app/v1/connection-branch-admin.php");
if (isset($_GET['act']) && $_GET['act'] == 'credit-note') {
    $id = $_GET['id'];
    $creditorsType = $_GET['creditorsType'];
    $sql = queryGet("SELECT DISTINCT
                                cn.*,
                                cust_inv.invoice_no AS invoice_code
                            FROM
                                erp_credit_note AS cn
                            LEFT JOIN credit_note_item AS cn_item
                            ON
                                cn.cr_note_id = cn_item.credit_note_id
                            LEFT JOIN erp_branch_sales_order_invoices AS cust_inv
                            ON
                                cn.creditors_type = '" . $creditorsType . "' AND cn_item.invoice_id = cust_inv.so_invoice_id
                            WHERE
                                cn.creditors_type = '" . $creditorsType . "' AND
                            cn.party_id = '" . $id . "' AND cn.company_id='" . $company_id . "' AND cn.branch_id='" . $branch_id . "' AND cn.location_id='" . $location_id . "';
                        ",true);
    // console($sql);
    // exit();
    $sql_data =  $sql['data'];

    $sql_numRows = $sql['numRows'];
    if ($sql_numRows > 0) {
        foreach ($sql_data as $data) {

    ?>
        <tr>
            <td><?= $data['credit_note_no'] ?></td>
            <td><?= $data['party_code'] ?></td>
            <td><?= $data['party_name'] ?></td>
            <td><?php echo ($data['invoice_code'] != null) ? $data['invoice_code'] : "-"; ?></td>
            <td><?= number_format($data['total'], 2) ?></td>
            <td><?= $data['postingDate'] ?></td>

        </tr>
    <?php
        }
    } else {
    ?>
        <tr>
            <td class="text-center" colspan="7">No Data Found</td>
        </tr>
<?php
    }
}
