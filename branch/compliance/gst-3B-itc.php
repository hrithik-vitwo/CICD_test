<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../../app/v1/functions/branch/func-compliance-controller.php");
require_once("../compliance/controller/gstr3b.controller.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

$queryParamsAction = json_decode(base64_decode(($_GET['action'])));
$authGstinPortalObj = new AuthGstinPortal();
$gstr3bCon = new ComplianceGSTR3b();
$authObj = $authGstinPortalObj->checkAuth();
// $queryParamsData = $_SESSION['gstr3bSummary'];
// $queryParams = $queryParamsData['data']['itc_elg'];
// $getGSTR3bData = $gstr3bCon->getGSTR2bData($queryParamsAction->period);// main code
$getGSTR3bData = $gstr3bCon->getGSTR2bData($queryParamsAction->period)['data']; // for testing
$totalIGSTAmt = 0;
$totalCGSTAmt = 0;
$totalSGSTAmt = 0;
$totalCESSAmt = 0;
$totalITCAmt = 0;
foreach ($getGSTR3bData as $onedata) {
    $totalITCAmt += $onedata['cgst_amount'] + $onedata['sgst_amount'] + $onedata['igst_amount'] + $onedata['cess_amount'];
    $totalIGSTAmt +=  $onedata['igst_amount'];
    $totalCGSTAmt += $onedata['cgst_amount'];
    $totalSGSTAmt += $onedata['sgst_amount'];
    $totalCESSAmt +=  $onedata['cess_amount'];
}
// console($totalITCAmt);
// console($_POST);


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
    <div class="card">
        <?php
        // console($totalITCAmt);
        ?>
        <div class="card-header">
            <h2>4. Eligible ITC </h2>
            <div class="help-block">
                <ion-icon name="help-circle-outline"></ion-icon>
            </div>
        </div>
        <div class="card-body">
            <form action="" id="itcForm" method="POST">
                <table>
                    <thead>
                        <tr>
                            <th>Details</th>
                            <th>Integrated Tax (₹)</th>
                            <th>Central Tax (₹)</th>
                            <th>State / UT Tax (₹)</th>
                            <th>CESS (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>
                                <p class="pre-normal">(A) ITC Available(whether in full or part)</p>
                            </th>
                        </tr>
                        <tr id="impOfGoods">
                            <th>
                                <p class="pre-normal">(1) Import of goods.</p>
                            </th>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_avl_impg_iamt" value="<?= $queryParams['itc_avl'][0]['iamt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <!-- <input type="number" class="form-control" name="" id=""> -->
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <!-- <input type="number" class="form-control" name="" id=""> -->
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_avl_impg_csamt" value="<?= $queryParams['itc_avl'][0]['csamt'] ?>">
                                </div>
                            </td>
                        </tr>
                        <tr id="impOfServices">
                            <th>
                                <p class="pre-normal">(2) Import of services.</p>
                            </th>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_avl_imps_iamt" value="<?= $queryParams['itc_avl'][1]['iamt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <!-- <input type="number" class="form-control" name="" id=""> -->
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <!-- <input type="number" class="form-control" name="" id=""> -->
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_avl_imps_csamt" value="<?= $queryParams['itc_avl'][1]['csamt'] ?>">
                                </div>
                            </td>
                        </tr>
                        <tr id="liable">
                            <th>
                                <p class="pre-normal">(1) Inward supplies liable to reverse charge (Other than 1 & 2 above).</p>
                            </th>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_avl_isrc_iamt" value="<?= $queryParams['itc_avl'][2]['iamt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_avl_isrc_camt" value="<?= $queryParams['itc_avl'][2]['camt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_avl_isrc_samt" value="<?= $queryParams['itc_avl'][2]['samt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_avl_isrc_csamt" value="<?= $queryParams['itc_avl'][2]['csamt'] ?>">
                                </div>
                            </td>
                        </tr>
                        <tr id="isd">
                            <th>
                                <p class="pre-normal">(4) Inward supplies from ISD</p>
                            </th>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_avl_isd_iamt" value="<?= $queryParams['itc_avl'][3]['iamt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_avl_isd_camt" value="<?= $queryParams['itc_avl'][3]['camt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_avl_isd_samt" value="<?= $queryParams['itc_avl'][3]['samt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_avl_isd_csamt" value="<?= $queryParams['itc_avl'][3]['csamt'] ?>">
                                </div>
                            </td>
                        </tr>
                        <tr id="allItc">
                            <th>
                                <p class="pre-normal">(5) All other ITC.</p>
                            </th>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_avl_oth_iamt" value="<?= decimalValuePreview($totalIGSTAmt) ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_avl_oth_camt" value="<?= $totalCGSTAmt ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_avl_oth_samt" value="<?= $totalSGSTAmt ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_avl_oth_csamt" value="<?= $totalCESSAmt ?>">
                                </div>
                            </td>
                        </tr>
                        <tr id="itcRev">
                            <th>
                                <p class="pre-normal">(B) ITC Reversed.</p>
                            </th>
                            <td>
                                <div class="form-input"> </div>
                            </td>
                            <td>
                            </td>
                            <td>

                            </td>
                            <td>
                                <div class="form-input">
                                </div>
                            </td>
                        </tr>
                        <tr id="itcRevCGST">
                            <th>
                                <p class="pre-normal">(1) As per rules 38,42 & 43 of CGST Rules and section 17(5).</p>
                            </th>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_rev_rul_iamt" value="<?= $queryParams['itc_rev'][0]['iamt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_rev_rul_camt" value="<?= $queryParams['itc_rev'][0]['camt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_rev_rul_samt" value="<?= $queryParams['itc_rev'][0]['samt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_rev_rul_csamt" value="<?= $queryParams['itc_rev'][0]['csamt'] ?>">
                                </div>
                            </td>
                        </tr>
                        <tr id="itcRevOther">
                            <th>
                                <p class="pre-normal">(2) Others.</p>
                            </th>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_rev_oth_iamt" value="<?= $queryParams['itc_rev'][1]['iamt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_rev_oth_camt" value="<?= $queryParams['itc_rev'][1]['camt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_rev_oth_samt" value="<?= $queryParams['itc_rev'][1]['samt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_rev_oth_csamt" value="<?= $queryParams['itc_rev'][1]['csamt'] ?>">
                                </div>
                            </td>
                        </tr>
                        <tr id="netITC">
                            <th>
                                <p class="pre-normal">(C) Net ITC Available (A)-(B).</p>
                            </th>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_net_iamt" value="<?= $queryParams['itc_net']['iamt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_net_camt" value="<?= $queryParams['itc_net']['camt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_net_samt" value="<?= $queryParams['itc_net']['samt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_net_csamt" value="<?= $queryParams['itc_net']['csamt'] ?>">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <p class="pre-normal">(D) Other Details.</p>
                            </th>
                            <td>
                                <div class="form-input">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                </div>
                            </td>

                        </tr>
                        <tr>
                            <th>
                                <p class="pre-normal">(1) ITC reclaimed which was reversed under Tbale 4(B)(2) in earlier tax period.</p>
                            </th>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_inelg_rul_iamt" value="<?= $queryParams['itc_inelg'][0]['iamt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_inelg_rul_camt" value="<?= $queryParams['itc_inelg'][0]['camt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_inelg_rul_samt" value="<?= $queryParams['itc_inelg'][0]['samt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_inelg_rul_csamt" value="<?= $queryParams['itc_inelg'][0]['csamt'] ?>">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <p class="pre-normal">(2) Ineligible ITC Under section 16(4) & ITC restricted due to PoS rules.</p>
                            </th>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_inelg_oth_iamt" value="<?= $queryParams['itc_inelg'][1]['iamt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_inelg_oth_camt" value="<?= $queryParams['itc_inelg'][1]['camt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_inelg_oth_samt" value="<?= $queryParams['itc_inelg'][1]['samt'] ?>">
                                </div>
                            </td>
                            <td>
                                <div class="form-input">
                                    <input type="number" class="form-control" name="" id="itc_inelg_oth_csamt" value="<?= $queryParams['itc_inelg'][1]['csamt'] ?>">
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex justify-content-end gap-3 my-3 mr-2">
                    <button type="submit" class="btn btn-primary gap-2" id="confirmBtn">Confirm</button>
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

        const serializedData = {
            "itc_avl": [{
                    "ty": "IMPS",
                    "iamt": parseFloat($('[id="itc_avl_imps_iamt"]').val()) || 0,
                    "camt": 0,
                    "samt": 0,
                    "csamt": 0
                },
                {
                    "ty": "OTH",
                    "iamt": parseFloat($('[id="itc_avl_oth_iamt"]').val()) || 0,
                    "camt": parseFloat($('[id="itc_avl_oth_camt"]').val()) || 0,
                    "samt": parseFloat($('[id="itc_avl_oth_samt"]').val()) || 0,
                    "csamt": 0
                },
                {
                    "ty": "ISRC",
                    "iamt": parseFloat($('[id="itc_avl_isrc_iamt"]').val()) || 0,
                    "camt": parseFloat($('[id="itc_avl_isrc_camt"]').val()) || 0,
                    "samt": parseFloat($('[id="itc_avl_isrc_samt"]').val()) || 0,
                    "csamt": 0
                },
                {
                    "ty": "IMPG",
                    "iamt": parseFloat($('[id="itc_avl_impg_iamt"]').val()) || 0,
                    "camt": 0,
                    "samt": 0,
                    "csamt": 0
                },
                {
                    "ty": "ISD",
                    "iamt": parseFloat($('[id="itc_avl_isd_iamt"]').val()) || 0,
                    "camt": 0,
                    "samt": 0,
                    "csamt": 0
                }
            ],
            "itc_rev": [{
                    "ty": "RUL",
                    "iamt": parseFloat($('[id="itc_rev_rul_iamt"]').val()) || 0,
                    "camt": 0,
                    "samt": 0,
                    "csamt": 0
                },
                {
                    "ty": "OTH",
                    "iamt": parseFloat($('[id="itc_rev_oth_iamt"]').val()) || 0,
                    "camt": 0,
                    "samt": 0,
                    "csamt": 0
                }
            ],
            "itc_net": {
                "iamt": parseFloat($('[id="itc_net_iamt"]').val()) || 0,
                "camt": parseFloat($('[id="itc_net_camt"]').val()) || 0,
                "samt": parseFloat($('[id="itc_net_samt"]').val()) || 0,
                "csamt": parseFloat($('[id="itc_net_csamt"]').val()) || 0
            },
            "itc_inelg": [{
                    "ty": "RUL",
                    "iamt": parseFloat($('[id="itc_inelg_rul_iamt"]').val()) || 0,
                    "camt": 0,
                    "samt": 0,
                    "csamt": 0
                },
                {
                    "ty": "OTH",
                    "iamt": parseFloat($('[id="itc_inelg_oth_iamt"]').val()) || 0,
                    "camt": 0,
                    "samt": 0,
                    "csamt": 0
                }
            ]
        };


        let encodedData =(serializedData)

        $.ajax({
            type: 'POST',
            url: `ajaxs/ajax-save-gstr3b-filedata.php`,
            data: {
                act: 'itc_elg',
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