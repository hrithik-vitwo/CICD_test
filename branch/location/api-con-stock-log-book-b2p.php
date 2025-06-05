<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book To Physical Sanitization</title>
</head>

<body>

    <div class="content-wrapper report-wrapper is-sales-orders vitwo-alpha-global">
        <section class="content">
            <div class="container-fluid">

                <div class="content-header">
                    <h2>Book To Physical Sanitization</h2>
                    <p>Data Sanitization with accounting using sheet</p>

                    <?php
                    $sql = "SELECT s.temp_sheet_id AS sheetId, l.stockLogId, j.id AS JournalEntryId, l.refNumber AS DocumentNo,s.update_status AS updateStaus, s.qty, s.calQty, s.rate, s.calrate, s.uom, s.caluom, l.createdAt, j.journal_created_at, CASE WHEN s.qty != s.calQty THEN 'DIFFERENT' ELSE 'SAME' END AS QtyDiff, CASE WHEN s.rate != s.calrate THEN 'DIFFERENT' ELSE 'SAME' END AS RateDiff, CASE WHEN s.uom != s.caluom THEN 'DIFFERENT' ELSE 'SAME' END AS UomDiff FROM erp_inventory_temp_sanitization_stock_log AS s LEFT JOIN erp_inventory_stocks_log AS l ON l.stockLogId = s.log_id LEFT JOIN erp_acc_journal AS j ON ( (j.documentNo = l.refNumber OR j.refarenceCode = l.refNumber) AND j.parent_slug = 'stockDifferenceBookToPhysical' AND j.company_id = 11 AND DATE_FORMAT(j.journal_created_at, '%Y-%m-%d %H:%i') = DATE_FORMAT(l.createdAt, '%Y-%m-%d %H:%i') AND j.journal_status = 'active' ) WHERE s.mvt_type IN ('CONSUMPTION(BOOK-PHYSICAL)') AND s.document_no IS NOT NULL GROUP BY s.document_no ORDER BY s.temp_sheet_id DESC;";
                    $dbObj = new Database();
                    $res = $dbObj->queryGet($sql, true);
                    $rowCount = $res['numRows'] ?? 0;
                    ?>

                    <p class="text-muted">Total Item To Fixed: <span class="invCount"><?= $rowCount ?></span></p>

                    <br>
                    <br>
                </div>
                <div class="" style="display: flex; gap: 10px;">

                    <div style="overflow-x: auto; max-width: 60%; width: 60%;">
                        <div id="logdatatable">
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Document No</th>
                                        <th>JOURNAL Status</th>
                                        <th>CAL DIFF</th>
                                        <th>CAL RATE DIFF</th>
                                        <th>CAL QTY DIFF status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $sl = 0;
                                    if ($rowCount > 0) {
                                        foreach ($res['data'] as $data) {
                                            $sl = $sl + 1;
                                            $jStatus=($data['JournalEntryId'] == null) ? "Not Created" : "Created";
                                    ?>
                                            <tr>
                                                <td><?= $sl ?></td>
                                                <td><?= $data['DocumentNo'] ?></td>
                                                <td><?=$jStatus  ?></td>
                                                <td><?= $data['QtyDiff'] ?></td>
                                                <td><?= $data['RateDiff'] ?></td>
                                                <td><?= $data['UomDiff'] ?></td>

                                                <?php if ($data['updateStaus'] == "not update") { ?>
                                                    <td><a class="btn btn-primary fixDataBook" data-id="<?= $data['DocumentNo'] ?>" data-jstatus="<?= $jStatus ?>"> FIX </a></td>
                                                <?php } else { ?>
                                                    <td><a class="btn btn-success"> Already Clicked </a></td>
                                                <?php } ?>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No Data Found</td></tr>";
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div style="width: 40%; background-color: #f0f0f0; padding: 10px; border-radius: 4px;">
                        <div style="display: flex; justify-content: space-between;">
                            <h3>Response</h3>
                            <a class="btn btn-danger" id="clear-box">Clear Box</a>
                        </div>
                        <br>
                        <br>
                        <div id="logDataRes" style="overflow-x: auto; height:800px;">
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

</body>

</html>
<?php require_once("../common/footer.php"); ?>
<script>
    let count = 0;
    $(document).on("click", ".fixDataBook", function() {
        let documentNo = $(this).data("id");
        let jStatus = $(this).data("jstatus");
        let $this = $(this);

        $this.prop("disabled", true);
        $this.removeClass("btn-primary").addClass("btn-secondary");
        if (count == 0) {
            $.ajax({
                type: "POST",
                url: `<?= LOCATION_URL ?>ajaxs/ajax-api-con-stock-log-book-b2p.php`,
                data: {
                    act: "logDataBookAdd",
                    documentNo,
                    jStatus
                },
                beforeSend: function() {
                    count = 1;
                    $("#logDataRes").html("<p>Loading Data...</p>");
                    $("#clear-box").hide();

                },
                success: function(response) {
                    console.log(response);
                    $("#logDataRes").html(response);
                },
                complete: function(response) {
                    count = 0;
                    $("#clear-box").show();
                    $this.removeClass("btn-secondary").addClass("btn-success");
                    $this.text("Complete");
                },
                error: function(error) {
                    console.log(error);
                    $("#logDataRes").html(error);

                }
            });

        }

    });

    $(document).on("click", "#clear-box", function() {
        $("#logDataRes").html('');
    });
</script>