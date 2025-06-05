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
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <div class="content-header">
                    <h2>Stock LOG Data Sanitization</h2>
                    <?php 
                    // Just for testing
                    $sql="SELECT COUNT(inv.so_invoice_id) AS invCount FROM erp_branch_sales_order_invoices AS inv WHERE inv.company_id = $company_id AND inv.branch_id=$branch_id AND inv.location_id=$location_id";
                    $res=queryGet($sql,true);
                    $data=$res['data'][0];
                    $invCount=$data['invCount'];
                    ?>
                    <p class="text-muted">Total Invoice Numbers: <span class="invCount"><?= $invCount ?></span></p>
                </div>
                <div style="overflow-x: auto; max-width: 100%;">
                    <a id="actDataSantization" class="btn btn-primary">Data Santization</a>
                    <div id="logDataRes"></div>
                </div>
            </div>
        </section>
    </div>

</body>

</html>
<?php require_once("../common/footer.php"); ?>
<script>
    let count = 0;
    $(document).on("click", "#actDataSantization", function() {
        if (count == 0) {
            $.ajax({
                type: "POST",
                url: `<?= LOCATION_URL ?>ajaxs/ajax-log-data-santization.php`,
                data: {
                    act: "logData",
                },
                beforeSend: function() {
                    console.log("API called");
                    $("#logDataRes").html('');
                    $("#logDataRes").html("<h3>Loading Data...</h3>");
                    count = 1;
                },
                success: function(response) {
                    console.log(response);
                    // try {
                    //     let responseObj = JSON.parse(response);
                    //     console.log(responseObj);
                    //     let Toast = Swal.mixin({
                    //         toast: true,
                    //         position: 'top-end',
                    //         showConfirmButton: false,
                    //         timer: 4000
                    //     });
                    //     Toast.fire({
                    //         icon: responseObj.status,
                    //         title: '&nbsp;' + responseObj.msg
                    //     }).then(function() {
                    //         count = 0;
                    //         $this.prop('disabled', false);

                    //     });
                    // } catch (error) {
                    //     console.log(error);
                    // }
                    $("#logDataRes").html(response);
                },
                complete: function(response) {
                    count = 0;
                },
                error: function(error) {
                    console.log(error);
                }
            });

        }

    });
</script>