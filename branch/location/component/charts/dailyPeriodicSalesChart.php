<?php
// console($chartSearchData);
$searchValue = "dailyPeriodicSales"; // Add this from ajax "?chart=" value
$index = array_search($searchValue, array_column($chartSearchData['data'], "chartName"));
$dashComponents = array();
if ($index !== false) {
    // echo "The index of '{$searchValue}' is '{$index}'.";
    $dashComponents = unserialize($chartSearchData['data'][$index]['components']);
}
?>
<!-- PERIODIC WISE DAILY SALES -->
<div class="col-md-12 col-sm-12 d-flex">
    <div class="card flex-fill dailyPeriodicSales"> <!-- Add Class -->
        <div class="card-header p-3">
            <div class="head-title">
                <button class="btn btn-primary pin-btn border" id="dailyPeriodicSalesChart"><?= chartExistorNot('dailyPeriodicSalesChart.php') ? 'Pinned' : 'Pin'; ?></button>

                <h5 class="card-title chartDivDailyPeriodicSales"></h5>
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
            <div id="chartDivDailyPeriodicSales" class="chartContainer"></div>
        </div>
    </div>
</div>
<!-- PERIODIC WISE DAILY SALES -->


<!-- Chart code -->
<script>
    // **************************************************** CHART FUNCTIONS ********************************************************
    // =============================================================================================================================
    function dailyPeriodicSalesChart(chartData, chartTitle) {

        $(`.${chartTitle}`).text(`Daily Periodic Sales`);

        if (chartData.length == 0) {
            const currentDate = new Date();
            const year = currentDate.getFullYear();
            const month = String(currentDate.getMonth() + 1).padStart(2, '0');
            const day = String(currentDate.getDate()).padStart(2, '0');

            const formattedDate = `${year}-${month}-${day}`;

            chartData = [{
                invoice_date: formattedDate,
                total_revenue: '0.00'
            }];
        };

        am4core.ready(function() {

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            // Function to check if a date is present in the array of invoices
            function isInvoiceDatePresent(date) {
                return chartData.some(invoice => invoice.invoice_date === date);
            }

            // Find the minimum and maximum dates in the array of chartData
            const minDate = new Date(chartData.reduce((min, invoice) => min.invoice_date < invoice.invoice_date ? min : invoice).invoice_date);
            const maxDate = new Date(chartData.reduce((max, invoice) => max.invoice_date > invoice.invoice_date ? max : invoice).invoice_date);

            // Iterate through the dates from the minimum to the maximum date
            const currentDate = new Date(minDate);
            while (currentDate <= maxDate) {
                const formattedDate = currentDate.toISOString().split('T')[0];

                // Check if the current date is missing in the array
                if (!isInvoiceDatePresent(formattedDate)) {
                    // Create a new key-value pair with a total revenue of 0 for the missing date
                    chartData.push({
                        invoice_date: formattedDate,
                        total_revenue: '0.00'
                    });
                }

                // Move to the next date
                currentDate.setDate(currentDate.getDate() + 1);
            }

            // Sort the chartData array by invoice_date
            chartData.sort((a, b) => a.invoice_date.localeCompare(b.invoice_date));
            // Output the updated array of chartData
            // console.log(chartData);

            // Add data
            chart.data = chartData;

            // Set input format for the dates
            chart.dateFormatter.inputDateFormat = "yyyy-MM-dd";

            // Create axes
            var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

            // Create series
            var series = chart.series.push(new am4charts.LineSeries());
            series.dataFields.valueY = "total_revenue";
            series.dataFields.dateX = "invoice_date";
            series.tooltipText = "{total_revenue}"
            series.strokeWidth = 2;
            series.minBulletDistance = 15;

            // Drop-shaped tooltips
            series.tooltip.background.cornerRadius = 20;
            series.tooltip.background.strokeOpacity = 0;
            series.tooltip.pointerOrientation = "vertical";
            series.tooltip.label.minWidth = 40;
            series.tooltip.label.minHeight = 40;
            series.tooltip.label.textAlign = "middle";
            series.tooltip.label.textValign = "middle";

            // Make bullets grow on hover
            var bullet = series.bullets.push(new am4charts.CircleBullet());
            bullet.circle.strokeWidth = 2;
            bullet.circle.radius = 4;
            bullet.circle.fill = am4core.color("#fff");

            var bullethover = bullet.states.create("hover");
            bullethover.properties.scale = 1.3;

            // Make a panning cursor
            chart.cursor = new am4charts.XYCursor();
            chart.cursor.behavior = "panXY";
            chart.cursor.xAxis = dateAxis;
            chart.cursor.snapToSeries = series;

            // Create vertical scrollbar and place it before the value axis
            chart.scrollbarY = new am4core.Scrollbar();
            chart.scrollbarY.parent = chart.leftAxesContainer;
            chart.scrollbarY.toBack();

            // Create a horizontal scrollbar with previe and place it underneath the date axis
            chart.scrollbarX = new am4charts.XYChartScrollbar();
            chart.scrollbarX.series.push(series);
            chart.scrollbarX.parent = chart.bottomAxesContainer;

            // dateAxis.start = 0;
            // dateAxis.start = 0.79;
            dateAxis.keepSelection = true;

            dateAxis.renderer.grid.template.disabled = true;
            valueAxis.renderer.grid.template.disabled = true;

        });
    };
    // =============================================================================================================================
    var quickDrop = $(".dailyPeriodicSales").find("select.quickDrop").val();

    function get_dailyPeriodicSales(quickDrop, search) {

        // AJAX CALL 
        $.ajax({
            url: `<?= BASE_URL ?>branch/location/ajaxs/reports/ajax-charts.php?chart=dailyPeriodicSales&search=${search}&quickDrop=${quickDrop}`,
            async: false,
            beforeSend: function() {
                $(".load-wrapp").show();
                $(".load-wrapp").css('opacity', 1);
            },
            success: function(result) {

                $(".load-wrapp").hide();
                $(".load-wrapp").css('opacity', 0);
                // $(".menu").hide();
                let res = jQuery.parseJSON(result);

                // =======================================================================================================================================
                dailyPeriodicSalesChart(res.dailyPeriodicSales.data, "chartDivDailyPeriodicSales");
                // =======================================================================================================================================
            }
        });
    }

    get_dailyPeriodicSales(quickDrop, null);

    $('.applyFilter<?= $searchValue; ?>').click(function() {
        // alert();
        var quickDrop = $(".dailyPeriodicSales").find("select.quickDrop").val();
        // var quickDrop = $(this).find("select.quickDrop").val();
        console.log("Selected value:", quickDrop);

        get_dailyPeriodicSales(quickDrop, 'searching');
    });
</script>