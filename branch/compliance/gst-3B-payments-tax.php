<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../../app/v1/functions/branch/func-compliance-controller.php");

//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
$queryParams = json_decode(base64_decode(($_GET['action'])));
$authGstinPortalObj = new AuthGstinPortal();
$authObj = $authGstinPortalObj->checkAuth();
$gstr3bDataObj = $_SESSION['gstr3bSummary']['data'];

// console($gstr3bDataObj); 

?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

<style>
    section.gstr-3B-payments-tag .card {
        max-width: 95%;
        margin: 20px auto;
    }

    section.gstr-3B-payments-tag .card .card-header {
        display: flex;
        align-items: center;
        color: #fff;
        justify-content: space-between;
        padding: 7px 20px;
    }

    section.gstr-3B-payments-tag .card .card-header .help-icon {
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    section.gstr-3B-payments-tag .card .card-header ion-icon {
        font-size: 1rem;
        font-weight: 600;
        background-color: #fff;
        padding: 2px;
        border-radius: 50%;
        color: #003060;
    }

    section.gstr-3B-payments-tag .card .card-body .alert {
        font-size: 0.8rem;
    }

    section.gstr-3B-payments-tag .card .tax-table {
        overflow: auto;
        margin: 25px 0;
    }

    section.gstr-3B-payments-tag .card .tax-table th {
        background: #ccc;
        color: #000;
        padding: 8px 15px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    section.gstr-3B-payments-tag .card .tax-table td {
        background: #fff;
        color: #000;
        padding: 8px 15px;
        font-size: 0.75rem;
    }

    section.gstr-3B-payments-tag .card .tax-table td input {
        background: #8b878738;
    }

    .gstr-3B.action-btns {
        display: flex;
        justify-content: center !important;
        align-items: center;
        gap: 5px;
    }

    section.gstr-3B-payments-tag .card {
        max-width: 95%;
        margin: 20px auto;
        background: #fff;
        border: 1px solid #ccc;
        border-radius: 13px;
    }

    section.gstr-3B-payments-tag .card .card-body {
        border-radius: 13px;
        padding: 20px;
    }
</style>


<div class="content-wrapper is-gstr-3B-payments-wrapper">
    <section class="gstr-3B gstr-3B-payments-tag">
        <div class="card">
            <div class="card-header">
                <h4>6.1 Payment of tax</h4>
                <div class="help-icon">
                    <span>Help</span>
                    <ion-icon name="help-outline"></ion-icon>
                </div>
            </div>
            <div class="card-body bg-white">
                <!-- <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                    <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
                    </symbol>
                </svg>
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="15" height="15" role="img" aria-label="Info:">
                        <use xlink:href="#info-fill" />
                    </svg>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatem tempora corporis necessitatibus repellat blanditiis nulla eum, dignissimos recusandae ullam, suscipit quia quo veritatis! Modi aperiam minus quisquam assumenda? Laudantium mollitia natus at.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="15" height="15" role="img" aria-label="Info:">
                        <use xlink:href="#info-fill" />
                    </svg>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatem tempora corporis necessitatibus repellat blanditiis nulla eum, dignissimos recusandae ullam, suscipit quia quo veritatis! Modi aperiam minus quisquam assumenda? Laudantium mollitia natus at.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="15" height="15" role="img" aria-label="Info:">
                        <use xlink:href="#info-fill" />
                    </svg>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatem tempora corporis necessitatibus repellat blanditiis nulla eum, dignissimos recusandae ullam, suscipit quia quo veritatis! Modi aperiam minus quisquam assumenda? Laudantium mollitia natus at.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="15" height="15" role="img" aria-label="Info:">
                        <use xlink:href="#info-fill" />
                    </svg>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatem tempora corporis necessitatibus repellat blanditiis nulla eum.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div> -->
                <!-- <div class="gstr-3B tax-table">
                    <table>
                        <thead>
                            <tr>
                                <th rowspan="2">Description</th>
                                <th rowspan="2">Other than reverse charge Tax payble (₹)</th>
                                <th colspan="4" class="text-center border-bottom">Paid to ITC</th>
                                <th rowspan="2">Other than reverse charge to be paid in cash (₹)</th>
                                <th rowspan="2">Reverse charge Tax payble (₹)</th>
                                <th rowspan="2">Reverse charge Tax to be paid in Cash (₹)</th>
                                <th rowspan="2">Interest Payble (₹)</th>
                                <th rowspan="2">Interest to be paid in cash (₹)</th>
                                <th rowspan="2">Late Fee Payble (₹)</th>
                                <th rowspan="2">Late Fee to be paid in cash (₹)</th>
                                <th rowspan="2">Utilize / able Cash Balance (₹)</th>
                                <th rowspan="2">Additional Cash required (₹)</th>
                            </tr>
                            <tr>
                                <th>Integrated Tax (₹)</th>
                                <th>Central Tax (₹)</th>
                                <th>State / UI Tax (₹)</th>
                                <th>CESS (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-bold">Tax</td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td class="font-bold">Interest</td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td class="font-bold">Late Fees</td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="15" height="15" role="img" aria-label="Info:">
                        <use xlink:href="#info-fill" />
                    </svg>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Alias quos repudiandae quo sint velit fuga non illum assumenda ea odio, provident sequi aliquid magnam, possimus deserunt pariatur ducimus voluptatum, necessitatibus optio accusantium?Lorem ipsum dolor sit amet consectetur adipisicing elit. Alias quos repudiandae quo sint velit fuga non illum assumenda ea odio, provident sequi aliquid magnam, possimus deserunt pariatur ducimus voluptatum, necessitatibus optio accusantium?

                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Alias quos repudiandae quo sint velit fuga non illum assumenda ea odio, provident sequi aliquid magnam, possimus deserunt pariatur ducimus voluptatum, necessitatibus optio accusantium?
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="15" height="15" role="img" aria-label="Info:">
                        <use xlink:href="#info-fill" />
                    </svg>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Alias quos repudiandae quo sint velit fuga non illum assumenda ea odio, provident sequi aliquid magnam, possimus deserunt pariatur ducimus voluptatum, necessitatibus optio accusantium?
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div> -->
                <div class="gstr-3B tax-table">
                    <table>
                        <thead>
                            <tr>
                                <th rowspan="2">Description</th>
                                <th rowspan="2">Other than reverse charge Tax payble (₹)</th>
                                <th colspan="4" class="text-center border-bottom">Paid to ITC</th>
                                <th rowspan="2">Other than reverse charge to be paid in cash (₹)</th>
                                <th rowspan="2">Reverse charge Tax payble (₹)</th>
                                <th rowspan="2">Reverse charge Tax to be paid in Cash (₹)</th>
                                <th rowspan="2">Interest Payble (₹)</th>
                                <th rowspan="2">Interest to be paid in cash (₹)</th>
                                <th rowspan="2">Late Fee Payble (₹)</th>
                                <th rowspan="2">Late Fee to be paid in cash (₹)</th>
                                <th rowspan="2">Utilize / able Cash Balance (₹)</th>
                                <th rowspan="2">Additional Cash required (₹)</th>
                            </tr>
                            <tr>
                                <th>Integrated Tax (₹)</th>
                                <th>Central Tax (₹)</th>
                                <th>State / UI Tax (₹)</th>
                                <th>CESS (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-bold">Integrated Tax (₹)</td>
                                <td class="text-right">
                                    <input type="number" class="form-control" value="" id="iamt">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="iPaidITC">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="iPaidCT">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="iPaidST">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="iPaidCESS">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="iToBePaidCash">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="iReverseChargePayable">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="iReverseChargeToBePaidCash">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="iInterestPayable">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="iInterestToBePaidCash">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="iLateFeePayable">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="iLateFeeToBePaidCash">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="iUtilizeCashBalance">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="iAdditionalCashRequired">
                                </td>
                            </tr>
                            <tr>
                                <td class="font-bold">Central Tax (₹)</td>
                                <td class="text-right">
                                    <input type="number" class="form-control" value="500" id="camt">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="cPaidITC">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="cPaidCT">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="cPaidST">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="cPaidCESS">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="cToBePaidCash">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="cReverseChargePayable">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="cReverseChargeToBePaidCash">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="cInterestPayable">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="cInterestToBePaidCash">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="cLateFeePayable">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="cLateFeeToBePaidCash">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="cUtilizeCashBalance">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="cAdditionalCashRequired">
                                </td>
                            </tr>
                            <tr>
                                <td class="font-bold">State / UI Tax (₹)</td>
                                <td class="text-right">
                                    <input type="number" class="form-control" value="300" id="samt">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="sPaidITC">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="sPaidCT">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="sPaidST">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="sPaidCESS">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="sToBePaidCash">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="sReverseChargePayable">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="sReverseChargeToBePaidCash">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="sInterestPayable">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="sInterestToBePaidCash">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="sLateFeePayable">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="sLateFeeToBePaidCash">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="sUtilizeCashBalance">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="sAdditionalCashRequired">
                                </td>
                            </tr>
                            <tr>
                                <td class="font-bold">CESS (₹)</td>
                                <td class="text-right">
                                    <input type="number" class="form-control" value="200" id="csamt">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="csPaidITC">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="csPaidCT">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="csPaidST">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="csPaidCESS">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="csToBePaidCash">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="csReverseChargePayable">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="csReverseChargeToBePaidCash">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="csInterestPayable">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="csInterestToBePaidCash">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="csLateFeePayable">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="csLateFeeToBePaidCash">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="csUtilizeCashBalance">
                                </td>
                                <td class="text-right">
                                    <input type="number" class="form-control" id="csAdditionalCashRequired">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="gstr-3B action-btns">
                    <button class="btn btn-primary">Back</button>
                    <button class="btn btn-primary" id="generateJson">Preview Draft GSTR-3B</button>
                    <button class="btn btn-primary" id="saveJson">Save GSTR-3B</button>
                    <button class="btn btn-primary text-xs" disabled>Proceed to File</button>
                </div>
            </div>
        </div>
    </section>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
    $(document).ready(function() {
        $(function() {
            $('#startDate').datepicker({
                format: 'dd/mm/yyyy'
            });
        });
    });
</script>
<?php
require_once("../common/footer.php");
?>

<script>
    // Function to generate JSON data based on input values
    function generateJsonData() {
        // Capture values from the form
        const iamt = parseFloat(document.getElementById('iamt').value) || 0;
        const iPaidITC = parseFloat(document.getElementById('iPaidITC').value) || 0;
        const iPaidCT = parseFloat(document.getElementById('iPaidCT').value) || 0;
        const iPaidST = parseFloat(document.getElementById('iPaidST').value) || 0;
        const iPaidCESS = parseFloat(document.getElementById('iPaidCESS').value) || 0;
        const iToBePaidCash = parseFloat(document.getElementById('iToBePaidCash').value) || 0;
        const iReverseChargePayable = parseFloat(document.getElementById('iReverseChargePayable').value) || 0;
        const iReverseChargeToBePaidCash = parseFloat(document.getElementById('iReverseChargeToBePaidCash').value) || 0;
        const iInterestPayable = parseFloat(document.getElementById('iInterestPayable').value) || 0;
        const iInterestToBePaidCash = parseFloat(document.getElementById('iInterestToBePaidCash').value) || 0;
        const iLateFeePayable = parseFloat(document.getElementById('iLateFeePayable').value) || 0;
        const iLateFeeToBePaidCash = parseFloat(document.getElementById('iLateFeeToBePaidCash').value) || 0;
        const iUtilizeCashBalance = parseFloat(document.getElementById('iUtilizeCashBalance').value) || 0;
        const iAdditionalCashRequired = parseFloat(document.getElementById('iAdditionalCashRequired').value) || 0;

        // Create the pdcash array
        const pdcash = [{
                liab_ldg_id: 1233,
                trans_typ: 30002,
                ipd: iamt,
                cpd: iPaidCT,
                spd: iPaidST,
                cspd: iPaidCESS,
                i_intrpd: iInterestPayable,
                c_intrpd: iInterestToBePaidCash,
                s_intrpd: iLateFeePayable,
                cs_intrpd: iLateFeeToBePaidCash,
                c_lfeepd: iLateFeePayable,
                s_lfeepd: iLateFeeToBePaidCash
            },
            {
                liab_ldg_id: 1234,
                trans_typ: 30003,
                ipd: iamt,
                cpd: iPaidCT,
                spd: iPaidST,
                cspd: iPaidCESS,
                i_intrpd: iInterestPayable,
                c_intrpd: iInterestToBePaidCash,
                s_intrpd: iLateFeePayable,
                cs_intrpd: iLateFeeToBePaidCash,
                c_lfeepd: iLateFeePayable,
                s_lfeepd: iLateFeeToBePaidCash
            }
        ];

        // Create the pditc object
        const pditc = {
            liab_ldg_id: 12321,
            trans_typ: 30002,
            i_pdi: iamt,
            i_pdc: 0,
            i_pds: 0,
            c_pdi: iPaidCT,
            c_pdc: 0,
            s_pdi: iPaidST,
            s_pds: 0,
            cs_pdcs: 0
        };

        // Combine into final JSON structure
        const jsonData = {
            pdcash: pdcash,
            pditc: pditc
        };

        // Convert to JSON string
        return JSON.stringify(jsonData, null, 2); // Pretty print
    }

    // Attach the event listener to the button
    document.getElementById('generateJson').addEventListener('click', function() {
        const jsonString = generateJsonData(); // Call the function to generate JSON
        console.log(jsonString);
        $.ajax({
            type: 'GET',
            url: `ajaxs/api/ajax-gstr3b-save-itc.php`,
            data: {
                action: <?= json_encode($queryParams)?>,
                jsonString:jsonString
            },
            dataType: "json",
            beforeSend: function() {
                console.log("Loading...");
            },
            success: function(response) {
                console.log(response);
                Swal.fire({
                    icon: response.status,
                    title: response.message,
                    timer: 1000,
                    showConfirmButton: false,
                })

            }
        })
    });
</script>