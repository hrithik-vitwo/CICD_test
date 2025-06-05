<?php
require_once("../../../../app/v1/connection-branch-admin.php");
if (isset($_GET['act']) && $_GET['act'] == 'debit-note') {
    $id = $_GET['id'];
    $creditorsType = $_GET['creditorsType'];

    $sql = queryGet("SELECT DISTINCT
                        dn.*,vend_inv.grnIvCode as invoice_code
                    FROM
                        erp_debit_note AS dn 
                    LEFT JOIN debit_note_item AS dn_item ON dn.dr_note_id = dn_item.debit_note_id
                    LEFT JOIN erp_grninvoice AS vend_inv ON dn.debitor_type = '".$creditorsType."' AND dn_item.invoice_id = vend_inv.grnIvId
                    WHERE
                        dn.debitor_type = '".$creditorsType."' AND  dn.party_id = '" . $id . "' AND dn.company_id='" . $company_id . "' AND dn.branch_id='" . $branch_id . "' AND dn.location_id='" . $location_id . "';", true);

    $sql_data =  $sql['data'];
    $sql_numRows = $sql['numRows'];
    // console($sql);
    // exit();
    if ($sql_numRows > 0) {
        foreach ($sql_data as $data) {
        ?>
            <tr>
                <td><?= $data['debit_note_no'] ?></td>
                <td><?= $data['party_code'] ?></td>
                <td><?= $data['party_name'] ?></td>
                <td><?php echo ($data['invoice_code'] != null) ? $data['invoice_code'] : "-";?></td>
                <td><?= number_format($data['total'],2) ?></td>
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
