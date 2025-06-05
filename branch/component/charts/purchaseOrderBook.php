<?php
// console($chartSearchData);
$searchValue = "purchaseOrderBook"; // Add this from ajax "?chart=" value
$index = array_search($searchValue, array_column($chartSearchData['data'], "chartName"));
$dashComponents = array();
if ($index !== false) {
    // echo "The index of '{$searchValue}' is '{$index}'.";
    $dashComponents = unserialize($chartSearchData['data'][$index]['components']);
}
?>
<!-- HTML -->
<!-- PURCHASE ORDER BOOK -->
<div class="col-md-6 col-sm-6 d-flex">
    <div class="card flex-fill purchaseOrderBook">
        <div class="card-header">
            <div class="head-title">
                <button class="btn btn-primary pin-btn border" id="purchaseOrderBook"><?= chartExistorNot('purchaseOrderBook.php') ? 'Pinned' : 'Pin'; ?></button>
                <h5 class="card-title chartDivPurchaseOrderBook"></h5>
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
            <div id="chartDivPurchaseOrderBook" class="chartContainer"></div>
        </div>
    </div>
</div>
<!-- PURCHASE ORDER BOOK -->

<!-- Chart code -->
<script>
    // **************************************************** CHART FUNCTION ********************************************************
    // ============================= PURCHASE ORDER BOOK ===============================
    function purchaseOrderBook(chartData, chartTitle) {

        $(`.${chartTitle}`).text(`Purchase Order Book`);

        if (chartData.length == 0) {
            chartData = [{
                label: "",
                count: 0,
                total_amount: 0
            }];
        };

        am4core.ready(function() {

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            // Export
            // chart.exporting.menu = new am4core.ExportMenu();

            // Data for both series
            chart.data = chartData;

            /* Create axes */
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "label";
            categoryAxis.renderer.minGridDistance = 30;

            /* Create value axis */
            var valueAxis1 = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis1.title.text = "Amount";

            var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis2.title.text = "Order Count";
            valueAxis2.renderer.opposite = true;
            valueAxis2.renderer.grid.template.disabled = true;

            /* Create series */
            var columnSeries = chart.series.push(new am4charts.ColumnSeries());
            columnSeries.yAxis = valueAxis1;
            columnSeries.name = "Amount";
            columnSeries.dataFields.valueY = "total_amount";
            columnSeries.dataFields.categoryX = "label";

            columnSeries.columns.template.tooltipText = "[#fff font-size: 15px]{name} in {categoryX}:\n[/][#fff font-size: 20px]{valueY}"
            columnSeries.columns.template.propertyFields.fillOpacity = "fillOpacity";
            columnSeries.columns.template.propertyFields.stroke = "stroke";
            columnSeries.columns.template.propertyFields.strokeWidth = "strokeWidth";
            columnSeries.columns.template.propertyFields.strokeDasharray = "columnDash";
            columnSeries.tooltip.label.textAlign = "middle";

            var lineSeries = chart.series.push(new am4charts.LineSeries());
            lineSeries.yAxis = valueAxis2;
            lineSeries.name = "Orders";
            lineSeries.dataFields.valueY = "count";
            lineSeries.dataFields.categoryX = "label";

            lineSeries.stroke = am4core.color("#fdd400");
            lineSeries.strokeWidth = 3;
            lineSeries.propertyFields.strokeDasharray = "lineDash";
            lineSeries.tooltip.label.textAlign = "middle";

            var bullet = lineSeries.bullets.push(new am4charts.Bullet());
            bullet.fill = am4core.color("#fdd400"); // tooltips grab fill from parent by default
            bullet.tooltipText = "[#fff font-size: 15px]{name} in {categoryX}:\n[/][#fff font-size: 20px]{valueY}"
            var circle = bullet.createChild(am4core.Circle);
            circle.radius = 4;
            circle.fill = am4core.color("#fff");
            circle.strokeWidth = 3;
        });
    };
    // ===============================================================================================
    var quickDrop = $(".purchaseOrderBook").find("select.quickDrop").val();

    function get_purchaseOrderBook(quickDrop, search) {
        // AJAX CALL 
        $.ajax({
            url: `<?= BASE_URL ?>branch/ajaxs/reports/ajax-charts.php?chart=purchaseOrderBook&search=${search}&quickDrop=${quickDrop}`,
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
                purchaseOrderBook(res.purchaseOrderBook.data, "chartDivPurchaseOrderBook");
                // =======================================================================================================================================
            }
        });
    }

    get_purchaseOrderBook(quickDrop, null);

    $('.applyFilter<?= $searchValue; ?>').click(function() {
        var quickDrop = $(".purchaseOrderBook").find("select.quickDrop").val();

        get_purchaseOrderBook(quickDrop, 'searching');
    });
</script>