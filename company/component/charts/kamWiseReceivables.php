<?php
// console($chartSearchData);
$searchValue = "kamWiseReceivables"; // Add this from ajax "?chart=" value
$index = array_search($searchValue, array_column($chartSearchData['data'], "chartName"));
$dashComponents = array();
if ($index !== false) {
    // echo "The index of '{$searchValue}' is '{$index}'.";
    $dashComponents = unserialize($chartSearchData['data'][$index]['components']);
}
?>
<!-- HTML -->
<!-- KAM WISE RECEIVABLES -->
<div class="col-md-12 col-sm-12 d-flex">
    <div class="card flex-fill kamWiseReceivables">
        <div class="card-header">
            <div class="head-title">
                <button class="btn btn-primary pin-btn border" id="kamWiseReceivables"><?= chartExistorNot('kamWiseReceivables.php') ? 'Pinned' : 'Pin'; ?></button>
                <h5 class="card-title chartDivKamWiseReceivables"></h5>
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
            <div id="chartDivKamWiseReceivables" class="chartContainer"></div>
        </div>
    </div>
</div>
<!-- KAM WISE RECEIVABLES -->

<!-- Chart code -->
<script>
    // **************************************************** CHART FUNCTION ********************************************************
    // ============================= KAM WISE RECEIVABLES ===============================
    function kamWiseReceivables(chartData, chartTitle) {

        $(`.${chartTitle}`).text(`KAM Wise Receivables`);

        if (chartData.length == 0) {
            chartData = [{
                kamName: "",
                due_days: 0,
                total_due_amount: 0
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

            let formattedData = chartData.map(obj => {

                let due_days = parseInt(obj.due_days);

                if (due_days >= 0 && due_days <= 30) {
                    obj.type = "0-30 days";
                    return obj;
                } else if (due_days >= 31 && due_days <= 60) {
                    obj.type = "31-60 days";
                    return obj;
                } else if (due_days >= 61 && due_days <= 90) {
                    obj.type = "61-90 days";
                    return obj;
                } else if (due_days >= 91 && due_days <= 180) {
                    obj.type = "91-180 days";
                    return obj;
                } else if (due_days >= 181 && due_days <= 365) {
                    obj.type = "181-365 days";
                    return obj;
                } else {
                    obj.type = "More than 365 days";
                    return obj;
                };
            });

            for (let obj of formattedData) {

                const outerObj = finalData.map(obj => {
                    return obj.category
                })
                outerIndex = outerObj.indexOf(obj.type)

                if (outerIndex !== -1) {

                    const innerObj = finalData[outerIndex].breakdown.map(obj => {
                        return obj.category
                    })
                    innerIndex = innerObj.indexOf(obj.kamName)

                    if (innerIndex !== -1) {
                        finalData[outerIndex].value += Number(obj.total_due_amount);
                        finalData[outerIndex].breakdown[innerIndex].value += Number(obj.total_due_amount);
                    } else {
                        finalData[outerIndex].value += Number(obj.total_due_amount);
                        finalData[outerIndex].breakdown.push({
                            "category": obj.kamName,
                            "value": Number(obj.total_due_amount)
                        });
                    };
                } else {
                    finalData.push({
                        "category": obj.type,
                        "value": Number(obj.total_due_amount),
                        "breakdown": [{
                            "category": obj.kamName,
                            "value": Number(obj.total_due_amount)
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
            var pieChart = chart.createChild(am4charts.PieChart3D);
            pieChart.data = data;
            pieChart.hiddenState.properties.opacity = 0; // this creates initial fade-in

            pieChart.legend = new am4charts.Legend();
            // pieChart.innerRadius = am4core.percent(50);

            // Add and configure Series
            var pieSeries = pieChart.series.push(new am4charts.PieSeries3D());
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
    var quickDrop = $(".kamWiseReceivables").find("select.quickDrop").val();

    function get_kamWiseReceivables(quickDrop, search) {
        // AJAX CALL 
        $.ajax({
            url: `<?= BASE_URL ?>company/ajaxs/reports/ajax-charts.php?chart=kamWiseReceivables&search=${search}&quickDrop=${quickDrop}`,
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
                kamWiseReceivables(res.kamWiseReceivables.data, "chartDivKamWiseReceivables");
                // =======================================================================================================================================
            }
        });
    }

    get_kamWiseReceivables(quickDrop, null);

    $('.applyFilter<?= $searchValue; ?>').click(function() {
        var quickDrop = $(".kamWiseReceivables").find("select.quickDrop").val();

        get_kamWiseReceivables(quickDrop, 'searching');
    });
</script>