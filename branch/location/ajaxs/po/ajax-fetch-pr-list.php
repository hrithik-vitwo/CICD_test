<?php
require_once("../../../../app/v1/connection-branch-admin.php");
header('Content-Type: application/json');

if ($_GET['act'] === 'vendQuotPRList') {
    $limit      = (int)($_GET['limit'] ?? 10);
    $page       = max(1, (int)($_GET['page'] ?? 1));
    $offset     = ($page - 1) * $limit;

    $search     = trim($_GET['prSearch']     ?? '');
    $searchSql  = '';
    if ($search !== '') {
        $s = addslashes($search);
        $searchSql = " AND (prCode LIKE '%$s%')";
    }

    // SQL Query using queryGet
    $pr_sql = "
        SELECT
            purchaseRequestId,
            prCode,
            expectedDate,
            refNo,
            pr_status
        FROM `" . ERP_BRANCH_PURCHASE_REQUEST . "`
        WHERE company_id = $company_id
          AND pr_status = 9
          $searchSql
        ORDER BY purchaseRequestId DESC
        LIMIT $offset, $limit
    ";

    $pr_get  = queryGet($pr_sql, true);
    $pr_data = $pr_get['data'];

    if (empty($pr_data)) {
        echo json_encode([
            'status' => 'error',
            'html'   => "<tr><td colspan='5'><p class='text-center'>No Purchase Requests Found</p></td></tr>"
        ]);
        exit;
    }

    ob_start();
    foreach ($pr_data as $row) {
        $encodedId = base64_encode($row['purchaseRequestId']);
        $statusTxt = ($row['pr_status'] == 10) ? 'Closed' : 'Open';
        ?>
        <tr>
            <td><input type="radio" name="pr-po-creation" value="<?= $encodedId ?>" class="form prId"></td>
            <td><?= htmlspecialchars($row['prCode']) ?></td>
            <td><?= htmlspecialchars(formatDateORDateTime($row['expectedDate'])) ?></td>
            <td><?= htmlspecialchars($row['refNo']) ?></td>
            <td><?= $statusTxt ?></td>
        </tr>
        <?php
    }
    $html = ob_get_clean();

    echo json_encode([
        'status' => 'success',
        'html'   => $html,
        'count'  => count($pr_data)
    ]);
    exit;
}