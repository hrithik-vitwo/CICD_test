<?php
// console($chartSearchData);
$asOnDate =  date('Y-m-d');

$searchValue = "customerWiseReceivables"; // Add this from ajax "?chart=" value
$index = array_search($searchValue, array_column($chartSearchData['data'], "chartName"));
$dashComponents = array();
if ($index !== false) {
    // echo "The index of '{$searchValue}' is '{$index}'.";
    $dashComponents = unserialize($chartSearchData['data'][$index]['components']);
}
?>
<!-- HTML -->
<!-- CUSTOMER WISE RECEIVABLES -->
<div class="col-md-12 col-sm-12 d-flex">
    <div class="card flex-fill customerWiseReceivables">
        <div class="card-header">
            <div class="head-title">
                <button class="btn btn-primary pin-btn border" id="customerWiseReceivables"><?= chartExistorNot('customerWiseReceivables.php') ? 'Pinned' : 'Pin'; ?></button>
                <h5 class="card-title chartDivCustomerWiseReceivables"></h5>
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
                            <div class="form-inline gap-2">
                                    <label for="">As on date </label>
                                    <input class="form-control" type="date" name="asOnDate" id="asOnDate<?= $searchValue; ?>" value="<?=$asOnDate?>">
                            </div>
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
            <div id="chartDivCustomerWiseReceivables" class="chartContainer"></div>
            <div id="chartDivCustomerWiseReceivablesOnAccount" class="chartContainer"></div>
        </div>
    </div>
</div>
<!-- CUSTOMER WISE RECEIVABLES -->

<!-- Chart code -->
<script>
    // **************************************************** CHART FUNCTION ********************************************************
    // ============================= CUSTOMER WISE RECEIVABLES ===============================
    // function customerWiseReceivables(chartData, chartTitle) {

    //     $(`.${chartTitle}`).text(`Customer Wise Receivables`);

    //     if (chartData.length == 0) {
    //         chartData = [{
    //             trade_name: "",
    //             due_days: 0,
    //             total_due_amount: 0
    //         }];
    //     };

    //     am4core.ready(function() {

    //         // Themes
    //         am4core.useTheme(am4themes_animated);

    //         /**
    //          * Source data
    //          */

    //         let finalData = [];
    //         let outerIndex = 0;
    //         let innerIndex = 0;

    //         let formattedData = chartData.map(obj => {

    //             let due_days = parseInt(obj.due_days);

    //             if (due_days >= 0 && due_days <= 30) {
    //                 obj.type = "0-30 days";
    //                 return obj;
    //             } else if (due_days >= 31 && due_days <= 60) {
    //                 obj.type = "31-60 days";
    //                 return obj;
    //             } else if (due_days >= 61 && due_days <= 90) {
    //                 obj.type = "61-90 days";
    //                 return obj;
    //             } else if (due_days >= 91 && due_days <= 180) {
    //                 obj.type = "91-180 days";
    //                 return obj;
    //             } else if (due_days >= 181 && due_days <= 365) {
    //                 obj.type = "181-365 days";
    //                 return obj;
    //             } else {
    //                 obj.type = "More than 365 days";
    //                 return obj;
    //             };
    //         });

    //         for (let obj of formattedData) {

    //             const outerObj = finalData.map(obj => {
    //                 return obj.category
    //             })
    //             outerIndex = outerObj.indexOf(obj.type)

    //             if (outerIndex !== -1) {

    //                 const innerObj = finalData[outerIndex].breakdown.map(obj => {
    //                     return obj.category
    //                 })
    //                 innerIndex = innerObj.indexOf(obj.trade_name)

    //                 if (innerIndex !== -1) {
    //                     finalData[outerIndex].value += Number(obj.total_due_amount);
    //                     finalData[outerIndex].breakdown[innerIndex].value += Number(obj.total_due_amount);
    //                 } else {
    //                     finalData[outerIndex].value += Number(obj.total_due_amount);
    //                     finalData[outerIndex].breakdown.push({
    //                         "category": obj.trade_name,
    //                         "value": Number(obj.total_due_amount)
    //                     });
    //                 };
    //             } else {
    //                 finalData.push({
    //                     "category": obj.type,
    //                     "value": Number(obj.total_due_amount),
    //                     "breakdown": [{
    //                         "category": obj.trade_name,
    //                         "value": Number(obj.total_due_amount)
    //                     }]
    //                 });
    //             };
    //         };

    //         data = finalData;

    //         /**
    //          * Chart container
    //          */

    //         // Create chart instance
    //         var chart = am4core.create(`${chartTitle}`, am4core.Container);
    //         chart.logo.disabled = true;
    //         chart.width = am4core.percent(100);
    //         chart.height = am4core.percent(100);
    //         chart.layout = "horizontal";

    //         /**
    //          * Column chart
    //          */

    //         // Create chart instance
    //         var columnChart = chart.createChild(am4charts.XYChart);

    //         // Create axes
    //         var categoryAxis = columnChart.yAxes.push(new am4charts.CategoryAxis());
    //         categoryAxis.dataFields.category = "category";
    //         categoryAxis.renderer.grid.template.location = 0;
    //         categoryAxis.renderer.inversed = true;

    //         var valueAxis = columnChart.xAxes.push(new am4charts.ValueAxis());

    //         // Create series
    //         var columnSeries = columnChart.series.push(new am4charts.ColumnSeries());
    //         columnSeries.dataFields.valueX = "value";
    //         columnSeries.dataFields.categoryY = "category";
    //         columnSeries.columns.template.strokeWidth = 0;
    //         columnSeries.columns.template.tooltipText = "[bold]{categoryY}: [#fff font-size: 20px]{valueX}"

    //         categoryAxis.renderer.labels.template.truncate = true;
    //         categoryAxis.renderer.labels.template.maxWidth = 120; // Adjust the maximum width as needed
    //         categoryAxis.renderer.labels.template.tooltipText = "{category}"; // Display full category name in tooltip

    //         /**
    //          * Pie chart
    //          */

    //         // Create chart instance
    //         var pieChart = chart.createChild(am4charts.PieChart3D);
    //         pieChart.data = data;
    //         pieChart.hiddenState.properties.opacity = 0; // this creates initial fade-in

    //         pieChart.legend = new am4charts.Legend();
    //         // pieChart.innerRadius = am4core.percent(50);

    //         // Add and configure Series
    //         var pieSeries = pieChart.series.push(new am4charts.PieSeries3D());
    //         pieSeries.dataFields.value = "value";
    //         pieSeries.dataFields.category = "category";
    //         pieSeries.slices.template.propertyFields.fill = "color";
    //         pieSeries.labels.template.disabled = true;

    //         // Set up labels
    //         var label1 = pieChart.seriesContainer.createChild(am4core.Label);
    //         label1.text = "";
    //         label1.horizontalCenter = "middle";
    //         label1.fontSize = 35;
    //         label1.fontWeight = 600;
    //         label1.dy = -30;

    //         var label2 = pieChart.seriesContainer.createChild(am4core.Label);
    //         label2.text = "";
    //         label2.horizontalCenter = "middle";
    //         label2.fontSize = 12;
    //         label2.dy = 20;

    //         // Auto-select first slice on load
    //         pieChart.events.on("ready", function(ev) {
    //             pieSeries.slices.getIndex(0).isActive = true;
    //         });

    //         // Set up toggling events
    //         pieSeries.slices.template.events.on("toggled", function(ev) {
    //             if (ev.target.isActive) {

    //                 // Untoggle other slices
    //                 pieSeries.slices.each(function(slice) {
    //                     if (slice != ev.target) {
    //                         slice.isActive = false;
    //                     }
    //                 });

    //                 // Update column chart
    //                 columnSeries.appeared = false;
    //                 columnChart.data = ev.target.dataItem.dataContext.breakdown;
    //                 columnSeries.fill = ev.target.fill;
    //                 columnSeries.reinit();

    //                 // Update labels
    //                 label1.text = pieChart.numberFormatter.format(ev.target.dataItem.values.value.percent, "#.'%'");
    //                 label1.fill = ev.target.fill;

    //                 label2.text = ev.target.dataItem.category;
    //             }
    //         });

    //     });
    // };
    function customerWiseReceivables(chartData, chartTitle) {

        $(`.${chartTitle}`).text("Customer Wise Receivables");

        if (chartData.length == 0) {
            chartData = [{
                customer_name: "",
                total_due: 0,
                "0-30_days_due": 0,
                "31-60_days_due": 0,
                "61-90_days_due": 0,
                "91-120_days_due": 0,
                "121-150_days_due": 0,
                "151-180_days_due": 0,
                "more_than_180_days_due": 0
            }];
        }

    am4core.ready(function() {

    // Themes
    am4core.useTheme(am4themes_animated);

    let finalData = [];

    chartData.forEach(obj => {
        let dueCategories = [
            {type: "0-30 days due", amount: parseFloat(obj["0-30_days_due"])},
            {type: "31-60 days due", amount: parseFloat(obj["31-60_days_due"])},
            {type: "61-90 days due", amount: parseFloat(obj["61-90_days_due"])},
            {type: "91-120 days due", amount: parseFloat(obj["91-180_days_due"])},
            {type: "121-150 days due", amount: parseFloat(obj["121-150_days_due"])},
            {type: "151-180 days due", amount: parseFloat(obj["151-180_days_due"])},
            {type: "More than 180 days due", amount: parseFloat(obj["more_than_180_days_due"])}
        ];

        dueCategories.forEach(dueCategory => {
            let outerIndex = finalData.findIndex(data => data.category === dueCategory.type);

            if (outerIndex !== -1) {
                let innerIndex = finalData[outerIndex].breakdown.findIndex(data => data.category === obj.customer_name);

                if (innerIndex !== -1) {
                    finalData[outerIndex].value += dueCategory.amount;
                    finalData[outerIndex].breakdown[innerIndex].value += dueCategory.amount;
                } else {
                    finalData[outerIndex].value += dueCategory.amount;
                    finalData[outerIndex].breakdown.push({
                        "category": obj.customer_name,
                        "value": dueCategory.amount
                    });
                }
            } else {
                finalData.push({
                    "category": dueCategory.type,
                    "value": dueCategory.amount,
                    "breakdown": [{
                        "category": obj.customer_name,
                        "value": dueCategory.amount
                    }]
                });
            }
        });
    });

    let data = finalData;

    // Chart container
    var chart = am4core.create(chartTitle, am4core.Container);
    chart.logo.disabled = true;
    chart.width = am4core.percent(100);
    chart.height = am4core.percent(100);
    chart.layout = "horizontal";

    // Column chart
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
    columnSeries.columns.template.tooltipText = "[bold]{categoryY}: [#fff font-size: 20px]{valueX}";

    categoryAxis.renderer.labels.template.truncate = true;
    categoryAxis.renderer.labels.template.maxWidth = 120;
    categoryAxis.renderer.labels.template.tooltipText = "{category}";

    // Pie chart
    var pieChart = chart.createChild(am4charts.PieChart3D);
    pieChart.data = data;
    pieChart.hiddenState.properties.opacity = 0;

    pieChart.legend = new am4charts.Legend();

    var pieSeries = pieChart.series.push(new am4charts.PieSeries3D());
    pieSeries.dataFields.value = "value";
    pieSeries.dataFields.category = "category";
    pieSeries.slices.template.propertyFields.fill = "color";
    pieSeries.labels.template.disabled = true;

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

    pieChart.events.on("ready", function(ev) {
        pieSeries.slices.getIndex(0).isActive = true;
    });

    pieSeries.slices.template.events.on("toggled", function(ev) {
        if (ev.target.isActive) {
            pieSeries.slices.each(function(slice) {
                if (slice != ev.target) {
                    slice.isActive = false;
                }
            });

            columnSeries.appeared = false;
            columnChart.data = ev.target.dataItem.dataContext.breakdown;
            columnSeries.fill = ev.target.fill;
            columnSeries.reinit();

            label1.text = pieChart.numberFormatter.format(ev.target.dataItem.values.value.percent, "#.'%'");
            label1.fill = ev.target.fill;

            label2.text = ev.target.dataItem.category;
        }
    });

});
}

function customerWiseReceivablesOnAccount(chartData, chartTitle) {

$(`.${chartTitle}`).text("Customer Wise Receivables On Account");

if (chartData.length == 0) {
    chartData = [{
        customer_name: "",
        total_onaccount: 0,
        "0-30_days_onaccount": 0,
        "31-60_days_onaccount": 0,
        "61-90_days_onaccount": 0,
        "91-120_days_onaccount": 0,
        "121-150_days_onaccount": 0,
        "151-180_days_onaccount": 0,
        "more_than_180_days_onaccount": 0
    }];
}

am4core.ready(function() {

// Themes
am4core.useTheme(am4themes_animated);

let finalData = [];

chartData.forEach(obj => {
let categories = [
    {type: "0-30 days on account", amount: parseFloat(obj["0-30_days_onaccount"])},
    {type: "31-60 days on account", amount: parseFloat(obj["31-60_days_onaccount"])},
    {type: "61-90 days on account", amount: parseFloat(obj["61-90_days_onaccount"])},
    {type: "91-120 days on account", amount: parseFloat(obj["91-180_days_onaccount"])},
    {type: "121-150 days on account", amount: parseFloat(obj["121-150_days_onaccount"])},
    {type: "151-180 days on account", amount: parseFloat(obj["151-180_days_onaccount"])},
    {type: "More than 180 days on account", amount: parseFloat(obj["more_than_180_days_onaccount"])}
];

categories.forEach(category => {
    let outerIndex = finalData.findIndex(data => data.category === category.type);

    if (outerIndex !== -1) {
        let innerIndex = finalData[outerIndex].breakdown.findIndex(data => data.category === obj.customer_name);

        if (innerIndex !== -1) {
            finalData[outerIndex].value += category.amount;
            finalData[outerIndex].breakdown[innerIndex].value += category.amount;
        } else {
            finalData[outerIndex].value += category.amount;
            finalData[outerIndex].breakdown.push({
                "category": obj.customer_name,
                "value": category.amount
            });
        }
    } else {
        finalData.push({
            "category": category.type,
            "value": category.amount,
            "breakdown": [{
                "category": obj.customer_name,
                "value": category.amount
            }]
        });
    }
});
});

let data = finalData;

// Chart container
var chart = am4core.create(chartTitle, am4core.Container);
chart.logo.disabled = true;
chart.width = am4core.percent(100);
chart.height = am4core.percent(100);
chart.layout = "horizontal";

// Column chart
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
columnSeries.columns.template.tooltipText = "[bold]{categoryY}: [#fff font-size: 20px]{valueX}";

categoryAxis.renderer.labels.template.truncate = true;
categoryAxis.renderer.labels.template.maxWidth = 120;
categoryAxis.renderer.labels.template.tooltipText = "{category}";

// Pie chart
var pieChart = chart.createChild(am4charts.PieChart3D);
pieChart.data = data;
pieChart.hiddenState.properties.opacity = 0;

pieChart.legend = new am4charts.Legend();

var pieSeries = pieChart.series.push(new am4charts.PieSeries3D());
pieSeries.dataFields.value = "value";
pieSeries.dataFields.category = "category";
pieSeries.slices.template.propertyFields.fill = "color";
pieSeries.labels.template.disabled = true;

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

pieChart.events.on("ready", function(ev) {
pieSeries.slices.getIndex(0).isActive = true;
});

pieSeries.slices.template.events.on("toggled", function(ev) {
if (ev.target.isActive) {
    pieSeries.slices.each(function(slice) {
        if (slice != ev.target) {
            slice.isActive = false;
        }
    });

    columnSeries.appeared = false;
    columnChart.data = ev.target.dataItem.dataContext.breakdown;
    columnSeries.fill = ev.target.fill;
    columnSeries.reinit();

    label1.text = pieChart.numberFormatter.format(ev.target.dataItem.values.value.percent, "#.'%'");
    label1.fill = ev.target.fill;

    label2.text = ev.target.dataItem.category;
}
});

});
}
    
    // ===============================================================================================
    // var quickDrop = $(".customerWiseReceivables").find("select.quickDrop").val();
    var quickDrop = $("#asOnDate<?= $searchValue; ?>").val();


    function get_customerWiseReceivables(quickDrop, search) {
        // AJAX CALL 
        $.ajax({
            url: `<?= BASE_URL ?>branch/location/ajaxs/reports/ajax-charts.php?chart=customerWiseReceivables&search=${search}&quickDrop=${quickDrop}`,
            async: false,
            beforeSend: function() {
                $(".load-wrapp").show();
                $(".load-wrapp").css('opacity', 1);
            },
            success: function(result) {
                // console.log("ok");
                // console.log(result);

                $(".load-wrapp").hide();
                $(".load-wrapp").css('opacity', 0);
                let res = jQuery.parseJSON(result);
                // console.log("cust due", res);
                // =======================================================================================================================================
                customerWiseReceivables(res.customerWiseReceivables.data, "chartDivCustomerWiseReceivables");
                // =======================================================================================================================================
            }
        });
    }

    get_customerWiseReceivables(quickDrop, null);

    function get_customerWiseReceivablesOnAccount(quickDrop, search) {
        // AJAX CALL 
        $.ajax({
            url: `<?= BASE_URL ?>branch/location/ajaxs/reports/ajax-charts.php?chart=customerWiseReceivablesOnAccount&search=${search}&quickDrop=${quickDrop}`,
            async: false,
            beforeSend: function() {
                $(".load-wrapp").show();
                $(".load-wrapp").css('opacity', 1);
            },
            success: function(result) {
                // console.log("ok");
                // console.log(result);

                $(".load-wrapp").hide();
                $(".load-wrapp").css('opacity', 0);
                let res = jQuery.parseJSON(result);
                // console.log("cust on acct", res);
                // =======================================================================================================================================
                customerWiseReceivablesOnAccount(res.customerWiseReceivablesOnAccount.data, "chartDivCustomerWiseReceivablesOnAccount");
                // =======================================================================================================================================
            }
        });
    }

    get_customerWiseReceivablesOnAccount(quickDrop, null);



    $('.applyFilter<?= $searchValue; ?>').click(function() {
        // var quickDrop = $("#asOnDate<?= $searchValue; ?>").val();
        let asOnDate = $("#asOnDate<?= $searchValue; ?>").val();
        get_customerWiseReceivables(asOnDate, 'searching');
        get_customerWiseReceivablesOnAccount(asOnDate, 'searching');
    });
</script>