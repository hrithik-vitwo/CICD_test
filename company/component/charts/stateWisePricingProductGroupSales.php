<?php
// console($chartSearchData);
$searchValue = "stateWisePriceProductGroupSales"; // Add this from ajax "?chart=" value
$index = array_search($searchValue, array_column($chartSearchData['data'], "chartName"));
$dashComponents = array();
if ($index !== false) {
    // echo "The index of '{$searchValue}' is '{$index}'.";
    $dashComponents = unserialize($chartSearchData['data'][$index]['components']);
}
?>
<!-- HTML -->
<!-- STATE WISE PRICING PRODUCT GROUP SALES -->
<div class="col-md-6 col-sm-6 d-flex">
    <div class="card flex-fill stateWisePriceProductGroupSales">
        <div class="card-header">
            <div class="head-title">
                <button class="btn btn-primary pin-btn border" id="stateWisePricingProductGroupSales"><?= chartExistorNot('stateWisePricingProductGroupSales.php') ? 'Pinned' : 'Pin'; ?></button>
                <h5 class="card-title chartDivStateWisePricingProductGroupSales"></h5>
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
            <div id="chartDivStateWisePricingProductGroupSales" class="chartContainer"></div>
        </div>
    </div>
</div>
<!-- STATE WISE PRICING PRODUCT GROUP SALES -->

<!-- Chart code -->
<script>
    // **************************************************** CHART FUNCTION ********************************************************
    // ============================= STATE WISE PRICING PRODUCT GROUP SALES ===============================
    function stateWisePricingProductGroupSales(chartData, chartTitle) {

        $(`.${chartTitle}`).text(`State Wise Pricing Product Group Sales`);

        if (chartData.length == 0) {
            chartData = [{
                goodGroupName: "",
                state: "",
                total_price: 0
            }];
        };

        am4core.ready(function() {

            // Themes
            am4core.useTheme(am4themes_animated);

            /**
             * Source data
             */

            let finalData = [];
            let outerIndex = 0;
            let innerIndex = 0;

            for (let obj of chartData) {

                const outerObj = finalData.map(obj => {
                    return obj.category
                })
                outerIndex = outerObj.indexOf(obj.goodGroupName)

                if (outerIndex !== -1) {
                    finalData[outerIndex].value += Number(obj.total_price);
                    finalData[outerIndex].breakdown.push({
                        "category": obj.state,
                        "value": Number(obj.total_price)
                    });
                } else {
                    finalData.push({
                        "category": obj.goodGroupName,
                        "value": Number(obj.total_price),
                        "breakdown": [{
                            "category": obj.state,
                            "value": Number(obj.total_price)
                        }]
                    });
                };
            };

            data = finalData

            /**
             * Chart container
             */

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4core.Container);
            chart.logo.disabled = true;
            chart.width = am4core.percent(100);
            chart.height = am4core.percent(100);
            chart.layout = "horizontal";


            /**
             * Column chart
             */

            // Create chart instance
            var columnChart = chart.createChild(am4charts.XYChart);

            // Create axes
            var categoryAxis = columnChart.yAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "category";
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.inversed = true;

            var valueAxis = columnChart.xAxes.push(new am4charts.ValueAxis());

            // Create series
            var columnSeries = columnChart.series.push(new am4charts.ColumnSeries());
            columnSeries.dataFields.valueX = "value";
            columnSeries.dataFields.categoryY = "category";
            columnSeries.columns.template.strokeWidth = 0;
            columnSeries.columns.template.tooltipText = "[bold]{categoryY}: [#fff font-size: 20px]{valueX}"

            categoryAxis.renderer.labels.template.truncate = true;
            categoryAxis.renderer.labels.template.maxWidth = 120; // Adjust the maximum width as needed
            categoryAxis.renderer.labels.template.tooltipText = "{category}"; // Display full category name in tooltip

            /**
             * Pie chart
             */

            // Create chart instance
            var pieChart = chart.createChild(am4charts.PieChart);
            pieChart.data = data;
            pieChart.innerRadius = am4core.percent(50);

            // Add and configure Series
            var pieSeries = pieChart.series.push(new am4charts.PieSeries());
            pieSeries.dataFields.value = "value";
            pieSeries.dataFields.category = "category";
            pieSeries.slices.template.propertyFields.fill = "color";
            pieSeries.labels.template.disabled = true;

            // Set up labels
            var label1 = pieChart.seriesContainer.createChild(am4core.Label);
            label1.text = "";
            label1.horizontalCenter = "middle";
            label1.fontSize = 35;
            label1.fontWeight = 600;
            label1.dy = -30;

            var label2 = pieChart.seriesContainer.createChild(am4core.Label);
            label2.text = "";
            label2.horizontalCenter = "middle";
            label2.fontSize = 12;
            label2.dy = 20;

            // Auto-select first slice on load
            pieChart.events.on("ready", function(ev) {
                pieSeries.slices.getIndex(0).isActive = true;
            });

            // Set up toggling events
            pieSeries.slices.template.events.on("toggled", function(ev) {
                if (ev.target.isActive) {

                    // Untoggle other slices
                    pieSeries.slices.each(function(slice) {
                        if (slice != ev.target) {
                            slice.isActive = false;
                        }
                    });

                    // Update column chart
                    columnSeries.appeared = false;
                    columnChart.data = ev.target.dataItem.dataContext.breakdown;
                    columnSeries.fill = ev.target.fill;
                    columnSeries.reinit();

                    // Update labels
                    label1.text = pieChart.numberFormatter.format(ev.target.dataItem.values.value.percent, "#.'%'");
                    label1.fill = ev.target.fill;

                    label2.text = ev.target.dataItem.category;
                }
            });

        });
    };
    // ===============================================================================================
    var quickDrop = $(".stateWisePriceProductGroupSales").find("select.quickDrop").val();

    function get_stateWisePricingProductGroupSales(quickDrop, search) {
        // AJAX CALL 
        $.ajax({
            url: `<?= BASE_URL ?>company/ajaxs/reports/ajax-charts.php?chart=stateWisePriceProductGroupSales&search=${search}&quickDrop=${quickDrop}`,
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
                stateWisePricingProductGroupSales(res.stateWisePriceProductGroupSales.data, "chartDivStateWisePricingProductGroupSales");
                // =======================================================================================================================================
            }
        });
    }

    get_stateWisePricingProductGroupSales(quickDrop, null);

    $('.applyFilter<?= $searchValue; ?>').click(function() {
        var quickDrop = $(".stateWisePriceProductGroupSales").find("select.quickDrop").val();

        get_stateWisePricingProductGroupSales(quickDrop, 'searching');
    });
</script>