<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h2>Charts</h2>
    <div id="chartdiv" style="width: 100%; height: 400px;"></div>
    <div id="chartDivReceivableAnalysis" class="chartContainer" style="width: 100%; height: 400px;"></div>


    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>

    <!-- <script>
        const data = [{
                "item_code": "22000001",
                "item_name": "Carvaan Mini Legends kannada - Sunset Red",
                "0-30_days_total_qty": "144",
                "0-30_days_total_value": "272818.08",
                "31-60_days_total_qty": "144",
                "31-60_days_total_value": "272818.08",
                "61-90_days_total_qty": "3024",
                "61-90_days_total_value": "5729179.68",
                "91-180_days_total_qty": "1296",
                "91-180_days_total_value": "2455362.72",
                "more_than_180_days_total_qty": "1296",
                "more_than_180_days_total_value": "2455362.72"

            },
            {
                "item_code": "22000001",
                "item_name": "Carvaan Mini Legends kannada - Sunset Green",
                "0-30_days_total_qty": "144",
                "0-30_days_total_value": "272818.08",
                "31-60_days_total_qty": "144",
                "31-60_days_total_value": "272818.08",
                "61-90_days_total_qty": "3024",
                "61-90_days_total_value": "5729179.68",
                "91-180_days_total_qty": "1296",
                "91-180_days_total_value": "2455362.72",
                "more_than_180_days_total_qty": "1296",
                "more_than_180_days_total_value": "2455362.72"

            },
            {
                "item_code": "22000001",
                "item_name": "Carvaan Mini Legends kannada - Sunset Blue",
                "0-30_days_total_qty": "144",
                "0-30_days_total_value": "272818.08",
                "31-60_days_total_qty": "144",
                "31-60_days_total_value": "272818.08",
                "61-90_days_total_qty": "3024",
                "61-90_days_total_value": "5729179.68",
                "91-180_days_total_qty": "1296",
                "91-180_days_total_value": "2455362.72",
                "more_than_180_days_total_qty": "1296",
                "more_than_180_days_total_value": "2455362.72"

            }
        ]
    </script> -->

    <script>
        // Create chart instance
        var chart = am4core.create("chartdiv", am4charts.XYChart);

        // Add data
        chart.data = [{
                "category": "Sunset Red",
                "0-30_days_total_qty": 144,
                "31-60_days_total_qty": 144,
                "61-90_days_total_qty": 3024,
                "91-180_days_total_qty": 1296,
                "more_than_180_days_total_qty": 1296
            },
            {
                "category": "Sunset Green",
                "0-30_days_total_qty": 144,
                "31-60_days_total_qty": 144,
                "61-90_days_total_qty": 3024,
                "91-180_days_total_qty": 1296,
                "more_than_180_days_total_qty": 1296
            },
            {
                "category": "Sunset Blue",
                "0-30_days_total_qty": 144,
                "31-60_days_total_qty": 144,
                "61-90_days_total_qty": 3024,
                "91-180_days_total_qty": 1296,
                "more_than_180_days_total_qty": 1296
            },
            {
                "category": "Sunset Red1",
                "0-30_days_total_qty": 144,
                "31-60_days_total_qty": 144,
                "61-90_days_total_qty": 3024,
                "91-180_days_total_qty": 1296,
                "more_than_180_days_total_qty": 1296
            },
            {
                "category": "Sunset Green1",
                "0-30_days_total_qty": 144,
                "31-60_days_total_qty": 144,
                "61-90_days_total_qty": 3024,
                "91-180_days_total_qty": 1296,
                "more_than_180_days_total_qty": 1296
            },
            {
                "category": "Sunset Blue1",
                "0-30_days_total_qty": 144,
                "31-60_days_total_qty": 144,
                "61-90_days_total_qty": 3024,
                "91-180_days_total_qty": 1296,
                "more_than_180_days_total_qty": 1296
            },
            {
                "category": "Sunset Red2",
                "0-30_days_total_qty": 144,
                "31-60_days_total_qty": 144,
                "61-90_days_total_qty": 3024,
                "91-180_days_total_qty": 1296,
                "more_than_180_days_total_qty": 1296
            },
            {
                "category": "Sunset Green2",
                "0-30_days_total_qty": 144,
                "31-60_days_total_qty": 144,
                "61-90_days_total_qty": 3024,
                "91-180_days_total_qty": 1296,
                "more_than_180_days_total_qty": 1296
            },
            {
                "category": "Sunset Blue2",
                "0-30_days_total_qty": 144,
                "31-60_days_total_qty": 144,
                "61-90_days_total_qty": 3024,
                "91-180_days_total_qty": 1296,
                "more_than_180_days_total_qty": 1296
            }
        ];

        // Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "category";
        categoryAxis.renderer.grid.template.location = 0;

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

        // Define colors for each time period
        var colors = {
            "0-30_days_total_qty": am4core.color("#FF5733"),
            "31-60_days_total_qty": am4core.color("#33FF57"),
            "61-90_days_total_qty": am4core.color("#3366FF"),
            "91-180_days_total_qty": am4core.color("#FF3366"),
            "more_than_180_days_total_qty": am4core.color("#FFFF33")
        };

        // Create series for each time period
        for (var i = 0; i < Object.keys(chart.data[0]).length - 1; i++) {
            var series = chart.series.push(new am4charts.ColumnSeries());
            var field = Object.keys(chart.data[0])[i + 1];
            series.dataFields.valueY = field;
            series.dataFields.categoryX = "category";
            series.name = field.split('_').join(' ').replace('total qty', 'Qty').replace('total value', 'Value');
            series.stacked = true;
            series.fill = colors[field];

            // Add tooltip
            series.columns.template.tooltipText = "{name}: {valueY}";
        }

        // Add chart cursor
        chart.cursor = new am4charts.XYCursor();

        // Add legend
        chart.legend = new am4charts.Legend();

        // Add title
        chart.title.text = "Item Quantities by Time Period and Color";

        // Add labels
        chart.seriesContainer.zIndex = -1;
    </script>

    <script>
        var chartData = [{
                "item_code": "22000001",
                "item_name": "Carvaan Mini Legends kannada - Sunset Red",
                "0-30_days": {
                    "total_qty": 144,
                    "total_value": 272818.08
                },
                "31-60_days": {
                    "total_qty": 144,
                    "total_value": 272818.08
                },
                "61-90_days": {
                    "total_qty": 3024,
                    "total_value": 5729179.68
                },
                "91-180_days": {
                    "total_qty": 1296,
                    "total_value": 2455362.72
                },
                "more_than_180_days": {
                    "total_qty": 1296,
                    "total_value": 2455362.72
                }
            },
            {
                "item_code": "22000002",
                "item_name": "Carvaan Mini Legends kannada - Sunset Green",
                "0-30_days": {
                    "total_qty": 144,
                    "total_value": 272818.08
                },
                "31-60_days": {
                    "total_qty": 144,
                    "total_value": 272818.08
                },
                "61-90_days": {
                    "total_qty": 3024,
                    "total_value": 5729179.68
                },
                "91-180_days": {
                    "total_qty": 1296,
                    "total_value": 2455362.72
                },
                "more_than_180_days": {
                    "total_qty": 1296,
                    "total_value": 2455362.72
                }
            },
            {
                "item_code": "22000003",
                "item_name": "Carvaan Mini Legends kannada - Sunset Blue",
                "0-30_days": {
                    "total_qty": 144,
                    "total_value": 272818.08
                },
                "31-60_days": {
                    "total_qty": 144,
                    "total_value": 272818.08
                },
                "61-90_days": {
                    "total_qty": 3024,
                    "total_value": 5729179.68
                },
                "91-180_days": {
                    "total_qty": 1296,
                    "total_value": 2455362.72
                },
                "more_than_180_days": {
                    "total_qty": 1296,
                    "total_value": 2455362.72
                }
            }
        ];

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
                let due_days = parseInt(obj["61-90_days"].total_qty); // Change this to the appropriate field for due days
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
                } else {
                    obj.type = "More than 180 days";
                    return obj;
                }
            });

            for (let obj of formattedData) {
                const outerObj = finalData.map(obj => {
                    return obj.category;
                });
                outerIndex = outerObj.indexOf(obj.type);

                if (outerIndex !== -1) {
                    const innerObj = finalData[outerIndex].breakdown.map(obj => {
                        return obj.category;
                    });
                    innerIndex = innerObj.indexOf(obj.item_name);

                    if (innerIndex !== -1) {
                        finalData[outerIndex].value += Number(obj["61-90_days"].total_value); // Change this to the appropriate field for total value
                        finalData[outerIndex].breakdown[innerIndex].value += Number(obj["61-90_days"].total_value); // Change this to the appropriate field for total value
                    } else {
                        finalData[outerIndex].value += Number(obj["61-90_days"].total_value); // Change this to the appropriate field for total value
                        finalData[outerIndex].breakdown.push({
                            "category": obj.item_name,
                            "value": Number(obj["61-90_days"].total_value) // Change this to the appropriate field for total value
                        });
                    }
                } else {
                    finalData.push({
                        "category": obj.type,
                        "value": Number(obj["61-90_days"].total_value), // Change this to the appropriate field for total value
                        "breakdown": [{
                            "category": obj.item_name,
                            "value": Number(obj["61-90_days"].total_value) // Change this to the appropriate field for total value
                        }]
                    });
                }
            }

            // Include "Other" category in the pie chart
            let pieTotal = 0;
            finalData.forEach(entry => pieTotal += entry.value);

            let otherValue = 0;
            finalData.forEach(entry => {
                if (entry.value / pieTotal < 0.05) { // Set a threshold for "Other" slice
                    otherValue += entry.value;
                    entry.value = 0;
                }
            });

            if (otherValue > 0) {
                finalData.push({
                    "category": "Other",
                    "value": otherValue,
                    "breakdown": [] // You can adjust this based on your data structure
                });
            }

            data = finalData;

            /**
             * Chart container
             */

            // Create chart instance
            var chart = am4core.create("chartDivReceivableAnalysis", am4core.Container);
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
            columnSeries.columns.template.tooltipText = "[bold]{categoryY}: [#fff font-size: 20px]{valueX}";

            categoryAxis.renderer.labels.template.truncate = true;
            categoryAxis.renderer.labels.template.maxWidth = 120; // Adjust the maximum width as needed
            categoryAxis.renderer.labels.template.tooltipText = "{category}"; // Display the full category name in the tooltip

            /**
             * Pie chart
             */

            // Create chart instance
            var pieChart = chart.createChild(am4charts.PieChart3D);
            pieChart.data = data;
            pieChart.hiddenState.properties.opacity = 0; // This creates initial fade-in

            pieChart.legend = new am4charts.Legend();
            pieChart.innerRadius = am4core.percent(50);

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

            // Auto-select the first slice on load
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

                    // Update the column chart
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
    </script>


</body>



</html>