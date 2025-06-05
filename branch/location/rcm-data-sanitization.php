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
    <title>RCM Data Sanitization</title>
</head>

<body>

    <div class="content-wrapper report-wrapper is-sales-orders vitwo-alpha-global">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <h1>RCM Data Sanitization</h1>
                <div style="overflow-x: auto; max-width: 100%;">
                    <table>
                        <thead>
                            <tr>
                                <th>SL NO</th>
                                <th>IV NUMBER</th>
                                <th>SRN NUMBER</th>
                                <th>PART CODE</th>
                                <th>PARTY NAME</th>
                                <th>DOCUMENT NO</th>
                                <th>DOCUMENT DATE</th>
                                <th>POSTING DATE</th>
                                <th>SUB TOTAL</th>
                                <th>TDS</th>
                                <th>TCS</th>
                                <th>Total GST</th>
                                <th>ROUND OFF</th>
                                <th>TOTAL AMOUNT</th>
                                <th>DUE AMOUNT</th>
                                <th>Update Rcm</th>
                                <th>Update Round Off</th>
                            </tr>
                        <tbody id="rcmTableBody">
                        </tbody>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>

</body>

</html>
<?php require_once("../common/footer.php"); ?>
<script>
    function loadData() {
        $.ajax({
            type: "GET",
            url: `<?= LOCATION_URL ?>ajaxs/ajax-api-rcm-data-sanitization.php`,
            data: {
                act: "rcm",
            },
            beforeSend: function() {
                $("#rcmTableBody").html("");
            },
            success: function(response) {
                try {
                    let res = JSON.parse(response);
                    let data = res.data;
                    let tableBody = $('#rcmTableBody');
                    data.forEach((item) => {

                        let actBtn = ``;
                        let actBtnRoundOff = ``;
                        // main logic for btn
                        tds = item.grnTotalTds;
                        sub = item.grnSubTotal;
                        tcs = item.grnTotalTcs;
                        gst = item.gst;

                        sub = Number(sub);
                        tcs = Number(tcs);
                        tds = Number(tds);
                        roundoff = item.roundoff;
                        roundoff = Number(roundoff);
                        total = item.grnTotalAmount;
                        total = Number(total);

                        let leftSide = (sub + tcs - tds);
                        let rigtSide = (total - gst);

                        if (roundoff == 0) {
                            if (leftSide == rigtSide) {
                                actBtn = `<a data-id="${item.grnIvId}" id="actBtn" class="btn btn-primary">Update RCM</a>`;
                            }
                        } else {

                            // if (leftSide == rigtSide) {
                            //     actBtn = `<a data-id="${item.grnIvId}" id="actBtn" class="btn btn-primary">Update RCM</a>`;
                            // }
                            console.log(`Round off issue fixing ${roundoff} value ${leftSide+roundoff} Total side ${rigtSide}`)

                            if (leftSide+roundoff == rigtSide) {
                                actBtn = `<a data-id="${item.grnIvId}" id="actBtn" class="btn btn-primary">Update RCM</a>`;
                            }else{
                                if ((leftSide + roundoff) != total) {
                                actBtnRoundOff = `<a data-id="${item.grnIvId}" id="actBtnRoundOFF" class="btn btn-primary">Update Round Off</a>`;
                                } 
                            }
                        }

                        tableBody.append(`
                            <tr>
                                <td>${item.sl}</td>
                                <td>${item.grnIvCode}</td>
                                <td>${item.grnCode} (${item.grnIvId})</td>
                                <td>${item.vendorCode}</td>
                                <td>${item.vendorName}</td>
                                <td>${item.vendorDocumentNo}</td>
                                <td>${item.vendorDocumentDate}</td>
                                <td>${item.postingDate}</td>
                                <td>${item.grnSubTotal}</td>
                                <td>${item.grnTotalTds}</td>
                                <td>${item.grnTotalTcs}</td>
                                <td>${item.gst}</td>
                                <td>${item.roundoff}</td>
                                <td>${item.grnTotalAmount}</td>
                                <td>${item.dueAmt}</td>
                                <td>${actBtn}</td>
                                <td>${actBtnRoundOff}</td>
                            </tr>
                        `);
                    });
                } catch (e) {
                    console.log(e);
                }
            }
        });
    }
    loadData();


    let count = 0;
    $(document).on("click", "#actBtn", function() {
        let id = $(this).data("id");
        var $this = $(this);
        $this.prop('disabled', true);
        if (count == 0) {
            $.ajax({
                type: "POST",
                url: `<?= LOCATION_URL ?>ajaxs/ajax-api-rcm-data-sanitization.php`,
                data: {
                    act: "updateRcmValue",
                    id,
                },
                beforeSend: function() {
                    console.log("update API called");
                    count = 1;
                },
                success: function(response) {

                    let responseObj = JSON.parse(response);
                    console.log(responseObj);
                    let Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000
                    });
                    Toast.fire({
                        icon: responseObj.status,
                        title: '&nbsp;' + responseObj.msg
                    }).then(function() {
                        count = 0;
                        loadData();
                        $this.prop('disabled', false);

                    });
                }
            });

        }

    });


    let fixRoundCount = 0;
    $(document).on("click", "#actBtnRoundOFF", function() {

        let id = $(this).data("id");
        var $this = $(this);
        $this.prop('disabled', true);
        if (fixRoundCount == 0) {
            $.ajax({
                type: "POST",
                url: `<?= LOCATION_URL ?>ajaxs/ajax-api-rcm-data-sanitization.php`,
                data: {
                    act: "updateRoundOff",
                    id,
                },
                beforeSend: function() {
                    console.log("Round off update API called");
                    fixRoundCount = 1;
                },
                success: function(response) {

                    let responseObj = JSON.parse(response);
                    console.log(responseObj);
                    let Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000
                    });
                    Toast.fire({
                        icon: responseObj.status,
                        title: '&nbsp;' + responseObj.msg
                    }).then(function() {
                        fixRoundCount = 0;
                        loadData();
                        $this.prop('disabled', false);

                    });
                }
            });

        }

    });
</script>