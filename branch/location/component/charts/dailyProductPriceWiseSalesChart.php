<?php
// console($chartSearchData);
$searchValue = "dailyProductPriceWiseSales"; // Add this from ajax "?chart=" value
$index = array_search($searchValue, array_column($chartSearchData['data'], "chartName"));
$dashComponents = array();
if ($index !== false) {
    // echo "The index of '{$searchValue}' is '{$index}'.";
    $dashComponents = unserialize($chartSearchData['data'][$index]['components']);
}
?>
<!-- HTML -->
<!-- PRODUCT PRICE WISE DAILY SALES -->
<div class="col-md-6 col-sm-6 d-flex">
    <div class="card flex-fill dailyProductPriceWiseSales">
        <div class="card-header">
            <div class="head-title">
                <button class="btn btn-primary pin-btn border" id="dailyProductPriceWiseSalesChart"><?= chartExistorNot('dailyProductPriceWiseSalesChart.php') ? 'Pinned' : 'Pin'; ?></button>
                <h5 class="card-title chartDivDailyProductPriceWiseSales"></h5>
            </div>

            <div id="containerThreeDot">

                <div id="menu-wrap">
                    <input type="checkbox" class="toggler bg-transparent" />
                    <div class="dots">
                        <div></div>
                    </div>
                    <div class="menu ">
                        <div>
                            <ul>
                                <li>
                                    <select name="quickDrop" id="quickDrop" class="form-control quickDrop">
                                        <option value="6" <?php if (isset($dashComponents['quickDrop']) && $dashComponents['quickDrop'] == 6) {
                                                                echo "selected";
                                                            } ?>>Last 7 Days</option>
                                        <option value="14" <?php if (isset($dashComponents['quickDrop']) && $dashComponents['quickDrop'] == 14) {
                                                                echo "selected";
                                                            } ?>>Last 15 Days</option>
                                        <option value="29" <?php if (isset($dashComponents['quickDrop']) && $dashComponents['quickDrop'] == 29) {
                                                                echo "selected";
                                                            } ?>>Last 30 Days</option>
                                        <option value="59" <?php if (isset($dashComponents['quickDrop']) && $dashComponents['quickDrop'] == 59) {
                                                                echo "selected";
                                                            } ?>>Last 60 Days</option>
                                        <option value="89" <?php if (isset($dashComponents['quickDrop']) && $dashComponents['quickDrop'] == 89) {
                                                                echo "selected";
                                                            } ?>>Last 90 Days</option>
                                        <option value="179" <?php if (isset($dashComponents['quickDrop']) && $dashComponents['quickDrop'] == 179) {
                                                                echo "selected";
                                                            } ?>>Last 180 Days</option>
                                        <option value="364" <?php if (isset($dashComponents['quickDrop']) && $dashComponents['quickDrop'] == 364) {
                                                                echo "selected";
                                                            } ?>>Last 365 Days</option>
                                    </select>
                                </li>

                                <li>
                                    <select name="itemCode" id="itemCode" class="form-control itemCode dailyProdP">

                                        <?php
                                        $item = queryGet("SELECT * FROM `erp_inventory_items` WHERE `company_id` = $company_id", true);
                                        $itemdata = $item['data'];
                                        foreach ($itemdata as $data) {
                                        ?>
                                            <option value="<?= $data['itemCode'] ?>" <?php if (isset($dashComponents['itemCode']) && $dashComponents['itemCode'] == $data['itemCode']) {
                                                                                            echo "selected";
                                                                                        } ?>><?= $data['itemName'] ?></option>

                                        <?php

                                        }

                                        ?>
                                    </select>
                                </li>


                                <li>
                                    <button class="btn btn-primary applyFilter<?= $searchValue; ?>">Apply</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-body">
            <div class="load-wrapp">
                <div class="load-1">
                    <div class="line"></div>
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
            </div>
            <div id="chartDivDailyProductPriceWiseSales" class="chartContainer"></div>
        </div>
    </div>
</div>
<!-- PRODUCT PRICE WISE DAILY SALES -->

<!-- Chart code -->
<script>
    // **************************************************** CHART FUNCTION ********************************************************
    // ============================= PRODUCT PRICE WISE DAILY SALES =================================
    function dailyProductPriceWiseSalesChart(chartData, chartTitle) {

        $(`.${chartTitle}`).text(`Daily Product Price Wise Sales for ${$(".dailyProdP").find(":selected").text()}`);

        if (chartData.length == 0) {
            const currentDate = new Date();
            const year = currentDate.getFullYear();
            const month = String(currentDate.getMonth() + 1).padStart(2, '0');
            const day = String(currentDate.getDate()).padStart(2, '0');

            const formattedDate = `${year}-${month}-${day}`;

            chartData = [{
                date_: formattedDate,
                total_amount: 0
            }];
        };
        am4core.ready(function() {

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            let data = [];

            chartData.map((dataObject) => {
                let object = {};
                object.color = chart.colors.next();
                object.x_axis = dataObject.date_;
                object.y_axis = Number(dataObject.total_amount);
                data.push(object);
            });

            // Add data
            chart.data = data;

            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

            categoryAxis.dataFields.category = "x_axis";
            categoryAxis.renderer.grid.template.disabled = true;
            categoryAxis.renderer.minGridDistance = 30;
            categoryAxis.renderer.inside = true;
            categoryAxis.renderer.labels.template.fill = am4core.color("#000");
            categoryAxis.renderer.labels.template.fontSize = 20;

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.renderer.grid.template.strokeDasharray = "4,4";
            valueAxis.renderer.labels.template.disabled = true;
            valueAxis.min = 0;

            // Do not crop bullets
            chart.maskBullets = false;

            // Remove padding
            // chart.paddingBottom = 0;

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "y_axis";
            series.dataFields.categoryX = "x_axis";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.propertyFields.stroke = "color";
            series.columns.template.column.cornerRadiusTopLeft = 5;
            series.columns.template.column.cornerRadiusTopRight = 5;
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/b]";

            categoryAxis.renderer.labels.template.horizontalCenter = "right";
            categoryAxis.renderer.labels.template.verticalCenter = "middle";
            categoryAxis.renderer.labels.template.rotation = -50;
            categoryAxis.tooltip.disabled = true;
            categoryAxis.renderer.minHeight = 10;
            categoryAxis.renderer.labels.template.fontSize = 10;
            categoryAxis.renderer.grid.template.location = 0;

            chart.paddingBottom = 50;
        });
    };
    // ===============================================================================================
    var quickDrop = $(".dailyProductPriceWiseSales").find("select.quickDrop").val();

    var itemCode = $(".dailyProductPriceWiseSales").find("select.itemCode").val();


    function get_dailyProductPriceWiseSalesChart(quickDrop, search, itemCode) {

        // AJAX CALL 
        $.ajax({
            url: `<?= BASE_URL ?>branch/location/ajaxs/reports/ajax-charts.php?chart=dailyProductPriceWiseSales&search=${search}&quickDrop=${quickDrop}&itemCode=${itemCode}`,
            async: false,
            beforeSend: function() {
                $(".load-wrapp").show();
                $(".load-wrapp").css('opacity', 1);
            },
            success: function(result) {
                $(".load-wrapp").hide();
                $(".load-wrapp").css('opacity', 0);

                let res = jQuery.parseJSON(result);

                // =======================================================================================================================================
                dailyProductPriceWiseSalesChart(res.dailyProductPriceWiseSales.data, "chartDivDailyProductPriceWiseSales");
                // =======================================================================================================================================
            }
        });
    }

    get_dailyProductPriceWiseSalesChart(quickDrop, null, itemCode);

    $('.applyFilter<?= $searchValue; ?>').click(function() {
        var quickDrop = $(".dailyProductPriceWiseSales").find("select.quickDrop").val();
        var itemCode = $(".dailyProductPriceWiseSales").find("select.itemCode").val();

        get_dailyProductPriceWiseSalesChart(quickDrop, 'searching', itemCode);
    });
</script>