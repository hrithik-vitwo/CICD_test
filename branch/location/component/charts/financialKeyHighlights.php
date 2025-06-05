<?php
// console($chartSearchData);
$searchValue = "financialKeyHighlights"; // Add this from ajax "?chart=" value
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
    <div class="card flex-fill financialKeyHighlights">
        <div class="card-header">
            <div class="head-title">
                <button class="btn btn-primary pin-btn border" id="financialKeyHighlights"><?= chartExistorNot('financialKeyHighlights.php') ? 'Pinned' : 'Pin'; ?></button>
                <h5 class="card-title chartDivFinancialKeyHighlights"></h5>
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
            <div id="chartDivFinancialKeyHighlights" class="chartContainer"></div>
        </div>
    </div>
</div>
<!-- KAM WISE RECEIVABLES -->

<!-- Chart code -->
<script>
    // **************************************************** CHART FUNCTION ********************************************************
    // ============================= KAM WISE RECEIVABLES ===============================
    function financialKeyHighlights(chartData, chartTitle) {

        $(`.${chartTitle}`).text(`Financial Key Highlights`);

        // console.log("000", chartData);

        am4core.ready(function() {

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;
            chart.hiddenState.properties.opacity = 0; // this makes initial fade in effect

            let finalData = [];

            if (chartData.total_revenue) {

                let totalRevenue = Number(chartData.total_revenue);

                finalData = [{
                    "category": "Total revenue",
                    "value": totalRevenue,
                    "open": 0,
                    "stepValue": totalRevenue,
                    "color": chart.colors.getIndex(13),
                    "displayValue": totalRevenue
                }];

                let tempValue = totalRevenue;

                const lvl2Objects = chartData.financial_data.filter(obj => obj.lvl === '2');
                const pIdToExpenseMap = {};
                lvl2Objects.forEach(obj => {
                    const pId = obj.p_id;
                    const expence = Number(obj.expence);
                    if (expence !== null && expence !== 0) {
                        if (pIdToExpenseMap[pId]) {
                            pIdToExpenseMap[pId] += Number(expence);
                        } else {
                            pIdToExpenseMap[pId] = Number(expence);
                        }
                    }
                });

                const newArray = chartData.financial_data
                    .filter(obj => obj.lvl === '1')
                    .map(obj => ({
                        ...obj,
                        expence: pIdToExpenseMap[obj.id] || Number(obj.expence)
                    }))
                    .filter(obj => obj.expence !== 0 && obj.expence !== null);

                // console.log("555", newArray);

                for (obj of newArray) {

                    let tempObj = {};

                    // if (obj.gl_code[0] == "4") {
                    tempObj.open = tempValue;
                    tempObj.color = chart.colors.getIndex(9);
                    tempObj.category = obj.gl_label;
                    tempObj.displayValue = Number(obj.expence);

                    tempValue -= Number(obj.expence);
                    tempObj.value = tempValue;
                    tempObj.stepValue = tempObj.value;
                    // } else {
                    //     tempObj.open = tempValue;
                    //     tempObj.color = chart.colors.getIndex(16);
                    //     tempObj.category = obj.gl_label;
                    //     tempObj.displayValue = Number(obj.expence);

                    //     tempValue += Number(obj.expence);
                    //     tempObj.value = tempValue;
                    //     tempObj.stepValue = tempObj.value;
                    // };

                    finalData.push(tempObj);
                };

                finalData.push({
                    "category": "Net revenue",
                    "value": tempValue,
                    "open": 0,
                    "color": chart.colors.getIndex(17),
                    "displayValue": tempValue
                });

            } else {
                finalData = [{
                        "category": "Total revenue",
                        "value": 0,
                        "open": 0,
                        "stepValue": 0,
                        "color": chart.colors.getIndex(13),
                        "displayValue": 0
                    },
                    {
                        "category": "Net revenue",
                        "value": 0,
                        "open": 0,
                        "color": chart.colors.getIndex(17),
                        "displayValue": 0
                    }
                ];
            };

            // console.log(finalData);

            // using math in the data instead of final values just to illustrate the idea of Waterfall chart
            // a separate data field for step series is added because we don't need last step (notice, the last data item doesn't have stepValue)
            chart.data = finalData;

            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "category";
            categoryAxis.renderer.minGridDistance = 40;

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

            var columnSeries = chart.series.push(new am4charts.ColumnSeries());
            columnSeries.dataFields.categoryX = "category";
            columnSeries.dataFields.valueY = "value";
            columnSeries.dataFields.openValueY = "open";
            columnSeries.fillOpacity = 0.8;
            columnSeries.sequencedInterpolation = true;
            columnSeries.interpolationDuration = 1500;

            var columnTemplate = columnSeries.columns.template;
            columnTemplate.strokeOpacity = 0;
            columnTemplate.propertyFields.fill = "color";

            var label = columnTemplate.createChild(am4core.Label);
            label.text = "{displayValue.formatNumber('₹#,## a')}";
            label.align = "center";
            label.valign = "middle";


            var stepSeries = chart.series.push(new am4charts.StepLineSeries());
            stepSeries.dataFields.categoryX = "category";
            stepSeries.dataFields.valueY = "stepValue";
            stepSeries.noRisers = true;
            stepSeries.stroke = new am4core.InterfaceColorSet().getFor("alternativeBackground");
            stepSeries.strokeDasharray = "3,3";
            stepSeries.interpolationDuration = 2000;
            stepSeries.sequencedInterpolation = true;

            // because column width is 80%, we modify start/end locations so that step would start with column and end with next column
            stepSeries.startLocation = 0.1;
            stepSeries.endLocation = 1.1;

            chart.cursor = new am4charts.XYCursor();
            chart.cursor.behavior = "none";

        });
    };
    // ===============================================================================================
    var quickDrop = $(".financialKeyHighlights").find("select.quickDrop").val();

    function get_financialKeyHighlights(quickDrop, search) {
        // AJAX CALL 
        $.ajax({
            url: `<?= BASE_URL ?>branch/location/ajaxs/reports/ajax-charts.php?chart=financialKeyHighlights&search=${search}&quickDrop=${quickDrop}`,
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
                    "total_revenue": res.financialKeyHighlights.total_revenue.data[0].total_income,
                    "financial_data": res.financialKeyHighlights.financial_data.data,
                };

                // =======================================================================================================================================
                financialKeyHighlights(resData, "chartDivFinancialKeyHighlights");
                // =======================================================================================================================================
            }
        });

    }

    $('.applyFilter<?= $searchValue; ?>').click(function() {
        var quickDrop = $(".financialKeyHighlights").find("select.quickDrop").val();

        get_financialKeyHighlights(quickDrop, 'searching');
    });

    get_financialKeyHighlights(quickDrop, null);
</script>