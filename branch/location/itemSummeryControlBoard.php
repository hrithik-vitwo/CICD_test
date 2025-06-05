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
    <title>Item Summery Control Board</title>
    <style>
        #dataModal .modal-dialog {
            width: 60vw;
            /* Set the width to 60% of the viewport width */
            max-width: none;
            /* Remove the default max-width so custom width takes precedence */
            margin: 0;
            /* Remove the default margin */
            position: fixed;
            /* Fix the modal position relative to the viewport */
            top: 50%;
            /* Position the top at 50% of the screen height */
            left: 50%;
            /* Position the left at 50% of the screen width */
            transform: translate(-50%, -50%);
            /* Offset the modal to be exactly centered */
        }

        #dataModal .modal-content {
            height: 40vh;
            /* Set the height to 40% of the viewport height */
            overflow-y: auto;
            /* Allow scrolling if content overflows */
        }
    </style>
</head>

<body>

    <div class="content-wrapper report-wrapper is-sales-orders vitwo-alpha-global">
        <section class="content">
            <div class="container-fluid">

                <div class="content-header">
                    <h2>Item Summery Control Board</h2>
                    <p>Only Item Valuation Class is here </p>

                    <?php
                    $cmpId = $company_id;
                    $sql = "SELECT i.itemId,i.itemCode,i.itemName,s.movingWeightedPrice AS map,s.itemTotalQty AS itemQtySummery,(SELECT g.goodTypeName FROM erp_inventory_mstr_good_types AS g WHERE g.goodTypeId=i.goodsType) AS itemType,i.createdAt FROM erp_inventory_stocks_summary AS s LEFT JOIN erp_inventory_items AS i ON s.itemId=i.itemId WHERE s.company_id=$cmpId AND i.company_id=$cmpId AND s.priceType IN ('v','V') AND (s.movingWeightedPrice > 0 OR s.itemTotalQty > 0)  ORDER BY s.stockSummaryId DESC";
                    $dbObj = new Database();
                    $res = $dbObj->queryGet($sql, true);
                    $rowCount = $res['numRows'] ?? 0;
                    ?>

                    <p class="text-muted">Total Item Count: <span class="invCount"><?= $rowCount ?></span></p>

                    <br>
                </div>
                <div class="" style="display: flex; gap: 10px;">

                    <div style="overflow-x: auto; max-width: 100%; width: 100%;">
                        <div id="logdatatable">
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>Current MWP <span> (Summery wise)</span> </th>
                                        <th>Current QTY <span>(Summery wise)</span></th>
                                        <th>Item Type</th>
                                        <th>Created At</th>
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
                                                <td><?= $data['itemCode'] ?></td>
                                                <td style="max-width: 200px; white-space: normal; word-break: break-word;"><?= $data['itemName'] ?></td>
                                                <td><?= $data['map'] ?></td>
                                                <td><?= $data['itemQtySummery'] ?></td>
                                                <td><?= $data['itemType'] ?></td>
                                                <td><?= $data['createdAt'] ?></td>
                                                <td><a class="btn btn-primary summeryData" data-no="<?= $data['itemCode'] ?>" data-id="<?= $data['itemId'] ?>" data-smap="<?= $data['map'] ?>" data-sqty="<?= $data['itemQtySummery'] ?>"> <ion-icon name="create-outline" class="ion-view"></ion-icon>View </a></td>
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

                </div>

            </div>
        </section>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dataModalLabel">Item Summary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="logDataRes">
                        <!-- Dynamic content will be inserted here -->
                    </div>
                    <p id="modalMsg" class="text-muted"><strong></strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


</body>

</html>
<?php require_once("../common/footer.php"); ?>
<script>
    let count = 0;
    $(document).on("click", ".summeryData", function() {
        let id = $(this).data("id");

        let itemCode = $(this).data("no");
        let sMap = $(this).data("smap");
        let sQty = $(this).data("sqty");

        sMap = Number(sMap) ?? 0;
        sQty = Number(sQty) ?? 0;

        if (count == 0) {
            $.ajax({
                type: "POST",
                url: `<?= LOCATION_URL ?>ajaxs/ajax-itemSummeryControlBoard.php`,
                data: {
                    act: "summeryData",
                    id
                },
                beforeSend: function() {
                    count = 1;
                    $("#logDataRes").html("<p>Calulating Data...</p>");
                    $("#modalMsg").html("");
                    $('#dataModal').modal('show');
                },
                success: function(res) {
                    try {
                        let responseObj = JSON.parse(res);
                        if (responseObj.status == "success") {

                            let status = "Not Action Needed";
                            let btn = '';
                            let itemLogCount = responseObj.data.countOfLog;
                            if (sMap != responseObj.data.finalMap) {
                                status = "Action Needed";
                                btn = `<a class="btn btn-warning summeryDataUpdate" data-id="${id}" data-newmap="${responseObj.data.finalMap}">Update</a>`;
                            }

                            let tableHtml = `
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Item Code</th>
                                                <th>Item Movement Count </th>
                                                <th>Current MAP</th>
                                                <th>Calculated MAP</th>
                                                <th>Summery QTY</th>
                                                <th>Calculated QTY</th>
                                                <th>Status</th>
                                                <th>Action</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>${itemCode}</td>
                                                <td>${itemLogCount}</td>
                                                <td>${sMap}</td>
                                                <td>${responseObj.data.finalMap}</td>
                                                <td>${sQty}</td>
                                                <td>${responseObj.data.finalQty}</td>
                                                <td>${status}</td>
                                                <td>${btn}</td>                                                
                                            </tr>
                                        </tbody>
                                    </table>
                                `;

                            $("#logDataRes").html(tableHtml);
                            $("#modalMsg").html("*Press 'Update' to replace the current MAP value with the new one.");

                        } else {
                            $('#dataModal').modal('show');
                            $("#logDataRes").html(response.message);
                        }

                        // $('#dataModal').modal('show');
                        // $("#logDataRes").html(res);

                    } catch (error) {
                        console.error(error);
                    }

                },
                complete: function(response) {
                    count = 0;
                    $("#clear-box").show();
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    });

    let Updatecount = 0;
    $(document).on("click", ".summeryDataUpdate", function() {
        let id = $(this).data("id");
        let newmap = $(this).data("newmap");
        newmap = Number(newmap) ?? 0;


        if (Updatecount == 0) {

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: `Are you sure to Update Map to  ${newmap} ?`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Update'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        type: "POST",
                        url: `<?= LOCATION_URL ?>ajaxs/ajax-itemSummeryControlBoard.php`,
                        data: {
                            act: "summeryDataUpdate",
                            id,
                            newmap
                        },
                        beforeSend: function() {
                            Updatecount = 1;
                            $("#clear-box").hide();
                        },
                        success: function(res) {
                            try {
                                let response = JSON.parse(res);
                                if (response.status == "success") {
                                    Swal.fire({
                                        icon: response.status,
                                        title: response.message,
                                        timer: 1000,
                                        showConfirmButton: false,
                                    }).then(() => {
                                        location.reload();
                                    })
                                } else {
                                    console.log(response);
                                }

                            } catch (error) {
                                console.log(res);
                                console.error(error);
                            }

                        },
                        complete: function(response) {
                            Updatecount = 0;
                            $("#clear-box").show();
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                }

            });

        }

    });
</script>