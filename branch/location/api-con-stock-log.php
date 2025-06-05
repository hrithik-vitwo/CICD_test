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
    <title>Stock LOG Data Sanitization</title>
</head>

<body>

    <div class="content-wrapper report-wrapper is-sales-orders vitwo-alpha-global">
        <section class="content">
            <div class="container-fluid">

                <div class="content-header">
                    <h2>Stock LOG Data Sanitization </h2>
                    <p>Data Sanitization with accounting using sheet</p>

                    <?php
                    // $sql = "SELECT * FROM erp_temp_sanitization_stock_log as slog WHERE slog.update_status IN ('not update') AND  AND slog.mvt_type NOT IN ('CONSUMPTION(BOOK-PHYSICAL)') GROUP BY slog.item_code";
                    $sql = "SELECT * FROM erp_inventory_temp_sanitization_stock_log as slog  WHERE  slog.mvt_type NOT IN ('CONSUMPTION(BOOK-PHYSICAL)') GROUP BY slog.item_code;";
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
                                        <th>SL NO</th>
                                        <th>Document No</th>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>Sheet status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $sl = 0;
                                    if ($rowCount > 0) {
                                        foreach ($res['data'] as $data) {
                                            $sl = $sl + 1;
                                    ?>
                                            <tr>
                                                <td><?= $sl ?></td>
                                                <td><?= $data['document_no'] ?></td>
                                                <td><?= $data['item_code'] ?></td>
                                                <td style="max-width: 200px; white-space: normal; word-break: break-word;"><?= $data['item_name'] ?></td>
                                                <td><?= $data['update_status'] ?></td>

                                                <?php if ($data['update_status'] == "not update") { ?>
                                                    <td><a class="btn btn-primary fixData" data-id="<?= $data['item_code'] ?>"> FIX </a></td>
                                                <?php } else { ?>
                                                    <td><a class="btn btn-success " data-id="<?= $data['item_code'] ?>"> Already Clicked </a></td>
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
    $(document).on("click", ".fixData", function() {
        let itemCode = $(this).data("id");
        let $this = $(this);

        $this.prop("disabled", true);

        $this.removeClass("btn-primary").addClass("btn-secondary");
        if (count == 0) {
            $.ajax({
                type: "POST",
                url: `<?= LOCATION_URL ?>ajaxs/ajax-api-con-stock-log.php`,
                data: {
                    act: "logData",
                    itemCode
                },
                beforeSend: function() {
                    count = 1;
                    $("#logDataRes").html("<p>Loading Data...</p>");
                    $("#clear-box").hide();
                },
                success: function(response) {
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