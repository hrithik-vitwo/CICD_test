<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../../app/v1/functions/branch/func-compliance-controller.php");
require_once("./controller/gstr1-json-repositary-controller.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
$queryParamsAction = json_decode(base64_decode(($_GET['action'])));
$authGstinPortalObj = new AuthGstinPortal();
$authObj = $authGstinPortalObj->checkAuth();
$queryParamsData = $_SESSION['gstr3bSummary'];
$queryParams = $queryParamsData['data']['sec_sum'];
// console($queryParams);

$period = $queryParamsAction->period;
$startDate = date("Y-m-d", strtotime($queryParamsAction->startDate));
$endDate = date("Y-m-d", strtotime($queryParamsAction->endDate));
$gstr1JsonRepoObj = new Gstr1JsonRepository($period, $startDate, $endDate);
$gstr1jsonObj = $gstr1JsonRepoObj->generate();
//----------gstr1 b2b data----------------------
$b2bTaxableAmount = 0;
foreach ($gstr1jsonObj['b2b'] as $invoiceItems) {
    foreach ($invoiceItems['inv'] as $rate => $rateWiseItem) {
        foreach ($rateWiseItem['itms'] as $oneItem) {
            // $b2bCGST += $oneItem['itm_det']["camt"];
            // $b2bSGST += $oneItem['itm_det']["samt"];
            // $b2bIGST += $oneItem['itm_det']["iamt"];
            // $b2bCESS += decimalValuePreview($oneItem['itm_det']["csamt"]);
            $b2bTaxableAmount += ($oneItem['itm_det']["txval"]);
        }
    }
}
$hsnSGST = 0;
$hsnCGST = 0;
$hsnIGST = 0;
$hsnCESS = 0;

foreach ($gstr1jsonObj['hsn']["data"] as $oneRow) {
    $hsnSGST += $oneRow["samt"];
    $hsnCGST += $oneRow["camt"];
    $hsnIGST += $oneRow["iamt"];
    $hsnCESS += $oneRow["csamt"];
    // $hsnTaxableAmount += $oneRow["txval"];
    // $hsnItemtotal_value = $oneRow["txval"] + $oneRow["iamt"] + $oneRow["camt"] + $oneRow["samt"] + $oneRow["csamt"];
}

$crurItemtaxableAmt = 0;
foreach ($gstr1jsonObj['cdnr'] as $crDrNotesRegistered) {
    foreach ($crDrNotesRegistered['nt'] as $oneCrNote) {
        foreach ($oneCrNote['itms'] as $onecrItem) {
            $crurItemtaxableAmt += $onecrItem['itm_det']['txval'];
        }
    }
}

$exptaxableAmount = 0;
$expTotalIGST = 0;
$expTotalCESS = 0;
foreach ($gstr1jsonObj['exp'] as $oneExp) {
    foreach ($oneExp['inv'] as $oneExpInv) {
        $expVourchercount += 1;

        // Reset the sum variables for each invoice
        $expItemtaxableAmt = 0;
        $expItemIGSTAmt = 0;
        $expItemCessAmt = 0;

        // Sum the values for the current invoice
        foreach ($oneExpInv['itms'] as $oneItem) {
            $expItemtaxableAmt += $oneItem['txval'];
            $expItemIGSTAmt += $oneItem['iamt'];
            $expItemCessAmt += $oneItem['csamt'];
            $exptaxableAmount += $expItemtaxableAmt;
            $expTotalIGST += $expItemIGSTAmt;
            $expTotalCESS += $expItemCessAmt;
        }
    }
}


//saved outward save 

// $savedOutwardDatasqlObj = queryGet("SELECT * FROM `erp_compliance_gstr3b_docs` WHERE company_id=".$company_id." AND branch_id=".$branch_id." AND `table_name`='sup_details' AND `period`='082024'")['data'];
// $outwardjson_data= $savedOutwardDatasqlObj['json_data'];
// $dataArray = json_decode($outwardjson_data, true);
// console($gstr1jsonObj['cdnr']);
// console($crurItemtaxableAmt);

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
    // console(($gstr1jsonObj));
    ?>
    <div class="card">
        <div class="card-header">
            <h2>
                3.1 Details of Outward Supplies and inward supplies liable to reverse charge (other than those covered by Table 3.1.1)
            </h2>
            <div class="help-block">
                <ion-icon name="help-circle-outline"></ion-icon>
            </div>
        </div>
        <div class="card-body">
            <form action="" id="submitForm">
                <table>
                    <thead>
                        <tr>
                            <th>Nature of supplies</th>
                            <th>Total Taxable Value (₹)</th>
                            <th>Integrated Tax (₹)</th>
                            <th>Central Tax (₹)</th>
                            <th>State / UT Tax (₹)</th>
                            <th>CESS (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>
                                <p class="pre-normal">(a) Outward taxable supplies (other than zero rated, nil rated and exempted).</p>
                            </th>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="outward_taxable_value" value="<?= ($b2bTaxableAmount + $crurItemtaxableAmt) ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="outward_integrated_tax" value="<?= ($hsnIGST) ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="outward_central_tax" value="<?= ($hsnCGST) ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="outward_state_tax" value="<?= ($hsnSGST) ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="outward_cess" value="<?= ($hsnCESS) ?>">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <p class="pre-normal">(b) Outward taxable supplies (zero rated).</p>
                            </th>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="zero_rated_taxable_value" value="<?= round($exptaxableAmount, 2) ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="zero_rated_integrated_tax" value="<?= $expTotalIGST ?>">
                                </div>
                            </td>
                            <td>
                                <!-- Central Tax Input -->
                            </td>
                            <td>
                                <!-- State / UT Tax Input -->
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="zero_rated_cess" value="<?= $expTotalCESS ?>">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <p class="pre-normal">(c) Other outward supplies (Nil rated, exempted).</p>
                            </th>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="nil_exempted_taxable_value" value="0.00">
                                </div>
                            </td>
                            <td><!-- Integrated Tax Input --></td>
                            <td><!-- Central Tax Input --></td>
                            <td><!-- State / UT Tax Input --></td>
                            <td><!-- CESS Input --></td>
                        </tr>
                        <tr>
                            <th>
                                <p class="pre-normal">(d) Inward supplies (liable to reverse charge).</p>
                            </th>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="inward_reverse_charge_taxable_value" value="0.00">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="inward_reverse_charge_integrated_tax" value="0.00">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="inward_reverse_charge_central_tax" value="0.00">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="inward_reverse_charge_state_tax" value="0.00">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="inward_reverse_charge_cess" value="0.00">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <p class="pre-normal">(e) Non-GST outward supplies.</p>
                            </th>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="non_gst_outward_taxable_value" value="0.00">
                                </div>
                            </td>
                            <td><!-- Integrated Tax Input --></td>
                            <td><!-- Central Tax Input --></td>
                            <td><!-- State / UT Tax Input --></td>
                            <td><!-- CESS Input --></td>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex justify-content-end gap-3 my-3 mr-2">
                    <button type="button" class="btn btn-primary gap-2" id="confirmBtn">Confirm</button>
                    <button type="button" class="btn btn-danger gap-2" id="cancelBtn">Cancel</button>
                </div>
            </form>

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

        const serializedArray = {
            "osup_det": {
                "txval": Number($('[name="outward_taxable_value"]').val()) || 0, // Use Number() to ensure numeric conversion
                "iamt": Number($('[name="outward_integrated_tax"]').val()) || 0,
                "camt": Number($('[name="outward_central_tax"]').val()) || 0,
                "samt": Number($('[name="outward_state_tax"]').val()) || 0,
                "csamt": Number($('[name="outward_cess"]').val()) || 0
            },
            "osup_zero": {
                "txval": Number($('[name="zero_rated_taxable_value"]').val()) || 0,
                "iamt": Number($('[name="zero_rated_integrated_tax"]').val()) || 0,
                "csamt": Number($('[name="zero_rated_cess"]').val()) || 0
            },
            "osup_nil_exmp": {
                "txval": Number($('[name="nil_exempted_taxable_value"]').val()) || 0
            },
            "isup_rev": {
                "txval": Number($('[name="inward_reverse_charge_taxable_value"]').val()) || 0,
                "iamt": Number($('[name="inward_reverse_charge_integrated_tax"]').val()) || 0,
                "camt": Number($('[name="inward_reverse_charge_central_tax"]').val()) || 0,
                "samt": Number($('[name="inward_reverse_charge_state_tax"]').val()) || 0,
                "csamt": Number($('[name="inward_reverse_charge_cess"]').val()) || 0
            },
            "osup_nongst": {
                "txval": Number($('[name="non_gst_outward_taxable_value"]').val()) || 0
            }
        };


        // var encodedData = JSON.stringify(serializedArray, null, 2);
        var encodedData = serializedArray;
        $.ajax({
            type: 'POST',
            url: `ajaxs/ajax-save-gstr3b-filedata.php`,
            dataType:'json',
            data: {
                act: 'sup_details',
                encodedData,
                queryParams,
            },
            dataType: "json",
            beforeSend: function() {
                console.log("Loading...");
            },
            success: function(response) {
                console.log(response);
                Swal.fire({
                    icon: response.status,
                    title: response.msg,
                    timer: 1000,
                    showConfirmButton: false,
                })
                .then(() => {
                    // Redirect to the summary page with the serialized data
                    window.location.href = `gst-3B-summary.php?action=${btoa('<?= json_encode($queryParamsAction) ?>')}`;

                });
            },
            complete: function() {
                console.log("Response completed");
            }
        })

    });
</script>