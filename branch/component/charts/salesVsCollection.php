<?php
// console($chartSearchData);
$searchValue = "salesVsCollection"; // Add this from ajax "?chart=" value
$index = array_search($searchValue, array_column($chartSearchData['data'], "chartName"));
$dashComponents = array();
if ($index !== false) {
    // echo "The index of '{$searchValue}' is '{$index}'.";
    $dashComponents = unserialize($chartSearchData['data'][$index]['components']);
}
?>
<!-- HTML -->
<!-- SALES VS COLLECTION -->
<div class="col-md-12 col-sm-12 d-flex">
    <div class="card flex-fill salesVsCollection">
        <div class="card-header">
            <div class="head-title">
                <button class="btn btn-primary pin-btn border" id="salesVsCollection"><?= chartExistorNot('salesVsCollection.php') ? 'Pinned' : 'Pin'; ?></button>
                <h5 class="card-title chartDivSalesVsCollection"></h5>
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
            <div id="chartDivSalesVsCollection" class="chartContainer"></div>
        </div>
    </div>
</div>
<!-- SALES VS COLLECTION -->

<!-- Chart code -->
<script>
    // **************************************************** CHART FUNCTION ********************************************************
    // ============================= SALES VS COLLECTION ===============================
    function salesVsCollection(chartData, chartTitle) {

        $(`.${chartTitle}`).text(`Sales Vs Collection`);

        if (chartData.collection.length == 0 || chartData.revenue.length == 0) {
            const currentDate = new Date();
            const year = currentDate.getFullYear();
            const month = String(currentDate.getMonth() + 1).padStart(2, '0');
            const day = String(currentDate.getDate()).padStart(2, '0');

            const formattedDate = `${year}-${month}-${day}`;

            chartData = {
                "revenue": [{
                    date_: formattedDate,
                    collection: 0,
                    receivable: 0,
                    total_revenue: 0
                }],
                "collection": [{
                    date_: formattedDate,
                    collection: 0
                }]
            };
        };

        am4core.ready(function() {

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            let finalData = [];
            let outerIndex = 0;

            for (obj of chartData.revenue) {
                obj.total_revenue = Number(obj.total_revenue);
                obj.receivable = Number(obj.receivable);
                obj.collection = 0;
                finalData.push(obj);
            };

            for (obj of chartData.collection) {

                const outerObj = finalData.map(obj => {
                    return obj.date_
                })
                outerIndex = outerObj.indexOf(obj.date_)

                if (outerIndex !== -1) {
                    finalData[outerIndex].collection = Number(obj.collection);
                } else {
                    obj.collection = Number(obj.collection);
                    obj.total_revenue = 0;
                    obj.receivable = 0;
                    finalData.push(obj);
                }
            }

            finalData.sort((a, b) => (a.date_ > b.date_) ? 1 : ((b.date_ > a.date_) ? -1 : 0))

            // Add data
            chart.data = finalData;

            // Create axes
            var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
            //dateAxis.renderer.grid.template.location = 0;
            //dateAxis.renderer.minGridDistance = 30;

            var valueAxis1 = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis1.title.text = "Sales";

            var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis2.title.text = "Collections";
            valueAxis2.renderer.opposite = true;
            valueAxis2.renderer.grid.template.disabled = true;

            // Create series
            var series1 = chart.series.push(new am4charts.ColumnSeries());
            series1.dataFields.valueY = "receivable";
            series1.dataFields.dateX = "date_";
            series1.yAxis = valueAxis1;
            series1.name = "Receivable";
            series1.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";
            series1.fill = chart.colors.getIndex(0);
            series1.strokeWidth = 0;
            series1.clustered = false;
            series1.columns.template.width = am4core.percent(40);

            var series2 = chart.series.push(new am4charts.ColumnSeries());
            series2.dataFields.valueY = "total_revenue";
            series2.dataFields.dateX = "date_";
            series2.yAxis = valueAxis1;
            series2.name = "Revenue";
            series2.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";
            series2.fill = chart.colors.getIndex(0).lighten(0.5);
            series2.strokeWidth = 0;
            series2.clustered = false;
            series2.toBack();

            var series3 = chart.series.push(new am4charts.LineSeries());
            series3.dataFields.valueY = "collection";
            series3.dataFields.dateX = "date_";
            series3.name = "Collection";
            series3.strokeWidth = 2;
            series3.tensionX = 0.7;
            series3.yAxis = valueAxis2;
            series3.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";

            var bullet3 = series3.bullets.push(new am4charts.CircleBullet());
            bullet3.circle.radius = 3;
            bullet3.circle.strokeWidth = 2;
            bullet3.circle.fill = am4core.color("#fff");

            // Add cursor
            chart.cursor = new am4charts.XYCursor();

            // Add legend
            chart.legend = new am4charts.Legend();
            chart.legend.position = "top";

            // Add scrollbar
            chart.scrollbarX = new am4charts.XYChartScrollbar();
            chart.scrollbarX.series.push(series1);
            chart.scrollbarX.series.push(series3);
            chart.scrollbarX.parent = chart.bottomAxesContainer;
        });
    };
    // ===============================================================================================
    var quickDrop = $(".salesVsCollection").find("select.quickDrop").val();

    function get_salesVsCollection(quickDrop, search) {

        // AJAX CALL 
        $.ajax({
            url: `<?= BASE_URL ?>branch/ajaxs/reports/ajax-charts.php?chart=salesVsCollection&search=${search}&quickDrop=${quickDrop}`,
            async: false,
            beforeSend: function() {
                $(".load-wrapp").show();
                $(".load-wrapp").css('opacity', 1);
            },
            success: function(result) {

                $(".load-wrapp").hide();
                $(".load-wrapp").css('opacity', 0);
                let res = jQuery.parseJSON(result);

                let resData = {
                    "revenue": res.salesVsCollection.revenue.data,
                    "collection": res.salesVsCollection.collection.data,
                };

                // =======================================================================================================================================
                salesVsCollection(resData, "chartDivSalesVsCollection");
                // =======================================================================================================================================
            }
        });
    }

    get_salesVsCollection(quickDrop, null);

    $('.applyFilter<?= $searchValue; ?>').click(function() {
        var quickDrop = $(".salesVsCollection").find("select.quickDrop").val();

        get_salesVsCollection(quickDrop, 'searching');
    });
</script>