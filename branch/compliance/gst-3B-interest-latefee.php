<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../../app/v1/functions/branch/func-compliance-controller.php");

//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
$queryParamsAction = json_decode(base64_decode(($_GET['action'])));
$authGstinPortalObj = new AuthGstinPortal();
$authObj = $authGstinPortalObj->checkAuth();
$queryParams = $_SESSION['gstr3bSummary']['data']['intr_ltfee'];
// console($queryParams);
?>
<link rel="stylesheet" href="../../public/assets/stock-report-new.css">
<style>
    .is-tax-outward .card {
        margin: 40px auto;
        max-width: 95%;
        border: 1px solid #ccc;
    }

    .is-tax-outward .card .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #fff;
        padding: 15px;
    }

    .is-tax-outward .card .card-header h2 {
        font-size: 0.9rem;
        color: #fff;
        margin-bottom: 0;
    }

    .is-tax-outward .card .card-header ion-icon {
        font-size: 2rem;
    }

    .is-tax-outward .card .card-body {
        border-radius: 12px;
        padding: 15px 0 0;
    }

    .is-tax-outward table tr th {
        background: #ffffff8f;
        color: #000;
        font-size: 0.8rem;
        font-weight: 600;
        border-bottom: 1px solid #ccc;
    }

    .is-tax-outward table tr td {
        border-bottom: 1px solid #ccc;
        border: 1px solid #ccc;
        padding: 20px 15px;
    }


    .is-tax-outward table tr:last-child th,
    .is-tax-outward table tr:last-child td {
        border-bottom: 0;
    }

    .is-tax-outward table tr th:last-child,
    .is-tax-outward table tr td:last-child {
        border-right: 0;
    }

    .is-tax-outward table tr td,
    .is-tax-outward table tr:nth-child(2n+1) td {
        background: #fff;
    }
</style>

<div class="content-wrapper is-tax-outward">
    <?php
    // console(($queryParams));
    ?>
    <div class="card">
        <div class="card-header">
            <h2>
                5.1 Interest & late fee payable
            </h2>
            <div class="help-block">
                <ion-icon name="help-circle-outline"></ion-icon>
            </div>
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Integrated Tax (₹)</th>
                        <th>Central Tax (₹)</th>
                        <th>State/UT Tax (₹)</th>
                        <th>CESS (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>
                            <p class="pre-normal">Interest</p>
                        </th>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="intr_ltfee_int_iamt" value="<?= ($queryParams['intr_details']['iamt']) ?>">
                            </div>
                        </td>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="intr_ltfee_int_camt" value="<?= ($queryParams['intr_details']['camt']) ?>">
                            </div>
                        </td>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="intr_ltfee_int_samt" value="<?= ($queryParams['intr_details']['samt']) ?>">
                            </div>
                        </td>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="intr_ltfee_int_csamt" value="<?= ($queryParams['intr_details']['csamt']) ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <p class="pre-normal">Late Fees</p>
                        </th>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="intr_ltfee_iamt" id="intr_ltfee_iamt" value="<?= ($queryParams['ltfee_details']['iamt'])  ?>">
                            </div>
                        </td>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="intr_ltfee_camt" value="<?= ($queryParams['ltfee_details']['camt']) ?>">
                            </div>
                        </td>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="intr_ltfee_samt" value="<?= ($queryParams['ltfee_details']['samt']) ?>">
                            </div>
                        </td>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="intr_ltfee_csamt" value="<?= ($queryParams['ltfee_details']['csamt']) ?>">
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-end gap-3 my-3 mr-2">
                <button type="button" class="btn btn-primary gap-2" id="confirmBtn">Confirm</button>
                <button type="button" class="btn btn-danger gap-2" id="cancelBtn">Cancel</button>
            </div>
        </div>
    </div>
</div>

<?php
require_once("../common/footer.php");
?>
<script>
    $(document).on("click", "#cancelBtn", function() {
        let action = '<?= json_encode($queryParamsAction) ?>';
        window.location.href = `gst-3b-summary.php?action=${btoa(action)}`;
    })

    $(document).on("click", "#confirmBtn", function(event) {
        event.preventDefault(); // Prevent the form from submitting immediately
        // var serializedData = $('#submitForm').serialize(); // Serialize the form data
        let queryParams = '<?= json_encode($queryParamsAction) ?>';

        const serializedData = {
            "intr_details": {
                "iamt": (isNaN(parseFloat($('[id="intr_ltfee_int_iamt"]').val())) ? 0 : parseFloat($('[id="intr_ltfee_int_iamt"]').val())) +
                    (isNaN(parseFloat($('[id="intr_ltfee_iamt"]').val())) ? 0 : parseFloat($('[id="intr_ltfee_iamt"]').val())),

                "camt": (isNaN(parseFloat($('[id="intr_ltfee_int_camt"]').val())) ? 0 : parseFloat($('[id="intr_ltfee_int_camt"]').val())) +
                    (isNaN(parseFloat($('[id="intr_ltfee_camt"]').val())) ? 0 : parseFloat($('[id="intr_ltfee_camt"]').val())),

                "samt": (isNaN(parseFloat($('[id="intr_ltfee_int_samt"]').val())) ? 0 : parseFloat($('[id="intr_ltfee_int_samt"]').val())) +
                    (isNaN(parseFloat($('[id="intr_ltfee_samt"]').val())) ? 0 : parseFloat($('[id="intr_ltfee_samt"]').val())),

                "csamt": (isNaN(parseFloat($('[id="intr_ltfee_int_csamt"]').val())) ? 0 : parseFloat($('[id="intr_ltfee_int_csamt"]').val())) +
                    (isNaN(parseFloat($('[id="intr_ltfee_csamt"]').val())) ? 0 : parseFloat($('[id="intr_ltfee_csamt"]').val()))
            }
        };

        console.log(serializedData);


        var encodedData = serializedData;
        $.ajax({
            type: 'POST',
            url: `ajaxs/ajax-save-gstr3b-filedata.php`,
            data: {
                act: 'intr_ltfee',
                encodedData,
                queryParams,
            },
            dataType: "json",
            beforeSend: function() {
                console.log("Loading...");
            },
            success: function(response) {
                console.log(response);

            },
            complete: function() {
                console.log("Response completed");
                //remove the loading
            }
        })
        // // Redirect to the summary page with the serialized data
        // window.location.href = `gst-3b-summary.php?action=${btoa('<?= json_encode($queryParamsAction) ?>')}`;
    });
</script>