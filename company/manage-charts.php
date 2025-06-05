<?php
require_once("../app/v1/connection-company-admin.php");

administratorAuth();

require_once("common/header.php");

require_once("common/navbar.php");

require_once("common/sidebar.php");

require_once("common/footer.php");


function chartExistorNot($page_name)
{
    global $company_id;
    $lavel = strtolower($_SESSION["logedCompanyAdminInfo"]["adminType"]);
    $responce = [];
    $user_id = $_SESSION["logedCompanyAdminInfo"]["adminId"];
    $dasSql = "SELECT * FROM `" . ERP_USER_DASHBOARD . "` WHERE lavel = '".$lavel."' AND company_id = $company_id AND `user_id`=" . $user_id . " AND status='active'";
    $dasSqlresult = queryGet($dasSql);
    if ($dasSqlresult['numRows'] != 0) {
        $sql = "SELECT
                    dashComponent.*,
                    userDash.`company_id`,
                    userDash.`user_id`,
                    dashCompMaster.`page_name`,
                    dashCompMaster.`component_title`,
                    dashCompMaster.`divArea`,
                    dashCompMaster.`component_desc`
                FROM
                    `" . ERP_DASH_COMPONENT . "` AS dashComponent,
                    `" . ERP_USER_DASHBOARD . "` AS userDash,
                    `" . ERP_DASH_COMPONENT_MASTER . "` AS dashCompMaster
                WHERE
                    dashComponent.dashboard_id = userDash.dashboard_id 
                    AND dashComponent.component_id = dashCompMaster.component_id 
                    AND dashCompMaster.`page_name` = '" . $page_name . "' 
                    AND dashCompMaster.`lavel` = '" . $lavel . "' 
                    AND userDash.`lavel` = '" . $lavel . "' 
                    AND userDash.`company_id` = " . $company_id . " 
                    AND userDash.`user_id` = " . $user_id . "";

        $res = queryGet($sql);
        if ($res['numRows'] == 0) {
            $responce = false;
        } else {
            $responce = true;
        }
    } else {
        $responce = false;
    }
    return $responce;
};

//echo chartExistorNot('kamWiseReceivables.php')?'Pinned':'pin';

// console($_SESSION);

$chartSearchData=getDashTableSettings(); //Add Dash Table Settings
?>

<!-- <link rel="stylesheet" href="../public/assets/ref-style.css"> -->
<link rel="stylesheet" href="../public/assets/listing.css">

<!-- Styles -->
<style>
    .chartContainer {
        width: 100%;
        height: 450px;
    }

    .card.flex-fill h5 {
        color: #fff;
        font-size: 15px;
    }

    .card.flex-fill .card-header {
        padding: 15px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card.flex-fill .card-header input,
    .card.flex-fill .card-header select {
        max-width: 155px;
    }

    .head-title,
    .head-input {
        display: flex;
        gap: 10px;
        align-items: center;
    }


    .card-body::after,
    .card-footer::after,
    .card-header::after {
        display: none;
    }

    .pin-tab {
        cursor: pointer;
        text-decoration: none;
    }

    .robo-element {
        height: 30vh; /* 50vh */
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 25px;
    }

    .robo-element img {
        width: 200px;
        height: 200px;
        object-fit: contain;
    }
</style>

<!-- Resources -->
<script src="../public/assets/core.js"></script>
<script src="../public/assets/charts.js"></script>
<script src="../public/assets/animated.js"></script>
<script src="../public/assets/forceDirected.js"></script>
<script src="../public/assets/sunburst.js"></script>

<!-- HTML -->
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid my-4">

            <div class="row">

                <?php
                include("component/charts/dailyPeriodicSalesChart.php");
                ?>

                <?php
                include("component/charts/dailyProductQuantityWiseSalesChart.php");
                ?>

                <?php
                include("component/charts/dailyProductPriceWiseSalesChart.php");
                ?>

                <?php
                include("component/charts/monthlyProductQuantityWiseSalesChart.php");
                ?>

                <?php
                include("component/charts/monthlyProductPriceWiseSalesChart.php");
                ?>

                <?php
                include("component/charts/yearlyProductQuantityWiseSalesChart.php");
                ?>

                <?php
                include("component/charts/yearlyProductPriceWiseSalesChart.php");
                ?>

                <?php
                include("component/charts/productQuantityWiseSalesOnDate.php");
                ?>

                <?php
                include("component/charts/productPriceWiseSalesOnDate.php");
                ?>

                <?php
                include("component/charts/productQuantityWiseSalesOnMonth.php");
                ?>

                <?php
                include("component/charts/productPriceWiseSalesOnMonth.php");
                ?>

                <?php
                include("component/charts/dailyProfitCenterWiseSalesChart.php");
                ?>

                <?php
                include("component/charts/monthlyProfitCenterWiseSalesChart.php");
                ?>

                <?php
                include("component/charts/yearlyProfitCenterWiseSalesChart.php");
                ?>

                <?php
                include("component/charts/productQuantityWiseProfitCenterSales.php");
                ?>

                <?php
                include("component/charts/productPriceWiseProfitCenterSales.php");
                ?>

                <?php
                include("component/charts/stateWiseProfitCenterSales.php");
                ?>

                <?php
                include("component/charts/kamWiseQuantityProductGroupSales.php");
                ?>

                <?php
                include("component/charts/kamWisePricingProductGroupSales.php");
                ?>

                <?php
                include("component/charts/stateWiseQuantityProductGroupSales.php");
                ?>

                <?php
                include("component/charts/stateWisePricingProductGroupSales.php");
                ?>

                <?php
                include("component/charts/customerWiseReceivables.php");
                ?>

                <?php
                include("component/charts/kamWiseReceivables.php");
                ?>

                <?php
                include("component/charts/vendorWisePayables.php");
                ?>

                <?php
                include("component/charts/salesOrderBook.php");
                ?>

                <?php
                include("component/charts/purchaseOrderBook.php");
                ?>

                <?php
                include("component/charts/salesVsCollection.php");
                ?>

                <?php
                include("component/charts/financialKeyHighlights.php");
                ?>

            </div>

        </div>
    </section>
</div>

<script>
    $(function() {
        $('input[name="daterange"]').daterangepicker({
            opens: 'left'
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('DD-MM-YYYY') + ' to ' + end.format('DD-MM-YYYY'));
        });
    });


    $('#filterDropdown')
        .select2()
        .on('select2:open', () => {});
</script>

<script>
    $(document).on("click", ".pin-btn", function() {

        const chartID = $(this).attr("id");
        const pageName = $(this).attr("id") + ".php";

        // AJAX CALL 
        $.ajax({
            url: `<?= BASE_URL ?>company/ajaxs/reports/ajax-addToDashboard.php?page=${pageName}`,
            async: false,
            success: function(result) {

                let res = jQuery.parseJSON(result);

                if (res['status'] == 'success') {
                    // console.log(chartID);
                    // console.log(pageName);
                    $(`#${chartID}`).text(res['txt']);
                    // $(`#${chartID}`).attr("disabled", "disabled");
                    
                    let Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    Toast.fire({
                        icon: res['status'],
                        title: res['message']
                    });

                }else{
                    let Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    Toast.fire({
                        icon: res['status'],
                        title: res['message']
                    });
                }

            }
        });
    });
</script>