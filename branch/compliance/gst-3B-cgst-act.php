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
                3.1 Details of Outward Supplies and inward supplies liable to reverse charge (other than those covered by Table 3.1.1)
            </h2>
            <div class="help-block">
                <ion-icon name="help-circle-outline"></ion-icon>
            </div>
        </div>
        <div class="card-body">
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
                                <input type="number" class="form-control" name="" id="" value="<?= ($queryParams->osup_det->txval)  ?>">
                            </div>
                        </td>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="" value="<?= ($queryParams->osup_det->iamt)  ?>">
                            </div>
                        </td>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="" value="<?= ($queryParams->osup_det->camt)  ?>">
                            </div>
                        </td>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id=""value="<?= ($queryParams->osup_det->samt)  ?>">
                            </div>
                        </td>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="" value="<?= ($queryParams->osup_det->csamt)  ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <p class="pre-normal">(b) Outward taxable supplies (zero rated).</p>
                        </th>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="" value="<?= ($queryParams->osup_zero->txval)  ?>">
                            </div>
                        </td>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="" value="<?= ($queryParams->osup_zero->iamt)  ?>">
                            </div>
                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="" value="<?= ($queryParams->osup_zero->csamt)  ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <p class="pre-normal">(c) Other outward supplies (Nil rated, exempted).</p>
                        </th>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="" value="<?= ($queryParams->osup_nil_exmp->txval) ?>">
                            </div>
                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                    </tr>
                    <tr>
                        <th>
                            <p class="pre-normal">(d) Inward supplies (liable to reverse charge).</p>
                        </th>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="" value="<?= ($queryParams->isup_rev->txval)  ?>">
                            </div>
                        </td>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="" value="<?= ($queryParams->isup_rev->iamt)  ?>">
                            </div>
                        </td>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="" value="<?= ($queryParams->isup_rev->camt)  ?>">
                            </div>
                        </td>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="" value="<?= ($queryParams->isup_rev->samt)  ?>">
                            </div>
                        </td>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="" value="<?= ($queryParams->isup_rev->csamt)  ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <p class="pre-normal">(e) Non-GST outward supplies.</p>
                        </th>
                        <td>
                            <div class="form-input">
                                <input type="number" class="form-control" name="" id="" value="<?= ($queryParams->osup_nongst->txval) ?>">
                            </div>
                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-end gap-3 my-3 mr-2">
                <button type="button" class="btn btn-primary gap-2">Confirm</button>
                <button type="button" class="btn btn-danger gap-2" id="cancelBtn">Cancel</button>
            </div>
        </div>
    </div>
</div>

<?php
require_once("../common/footer.php");
?>
<script>
    $(document).on("click","#cancelBtn",function(){
        window.location.href="gst-3b-summary.php"
    })
</script>