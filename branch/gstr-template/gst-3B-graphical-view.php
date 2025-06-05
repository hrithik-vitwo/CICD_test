<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<style>
    section.gstr-3B {
        padding: 0px 20px;
    }

    .head-btn-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .gstr-3B-filter {
        left: 0;
        top: 0;
    }

    .gstr-3B-filter a.active {
        background-color: #003060;
        color: #fff;
    }

    .chartContainer {
        width: 100%;
        height: 500px;
    }
</style>


<div class="content-wrapper">
    <section class="gstr-3B">
        <h4 class="text-lg font-bold mt-4 mb-4">GSTR-3B</h4>

        <div class="head-btn-section mb-3">
            <div class="filter-list gstr-3B-filter">
                <a href="./gst-3B-graphical-view.php" class="btn active"><i class="fas fa-chart-bar mr-2"></i>Graphical View</a>
                <a href="./gst-3B-consised-view.php" class="btn"><i class="fa fa-list mr-2"></i>Concised View</a>
            </div>
            <a class="btn btn-primary" href="./gst-3B-summary.php" target="_blank"><i class="fa fa-file mr-2"></i>Action/ File</a>
        </div>

        <div class="card">
            <div class="card-header rounded p-3">
                <h4 class="text-xs">Graphical view</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div id="chartDiv100PercentStackedColumnChart" class="chartContainer"></div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div id="chartDiv3DPieChart" class="chartContainer"></div>
                    </div>
                </div>

            </div>
        </div>

    </section>
</div>

<!-- Resources -->
<script src="../../public/assets/core.js"></script>
<script src="../../public/assets/charts.js"></script>
<script src="../../public/assets/animated.js"></script>
<script src="../../public/assets/forceDirected.js"></script>
<script src="../../public/assets/sunburst.js"></script>
<script>
    // ====================================== 100% Stacked Column Chart ======================================
    am4core.ready(function() {

        // Themes 
        am4core.useTheme(am4themes_animated);

        var chart = am4core.create("chartDiv100PercentStackedColumnChart", am4charts.XYChart);
        chart.logo.disabled = true;
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

        chart.data = [{
                category: "One",
                value1: 1,
                value2: 5,
                value3: 3
            },
            {
                category: "Two",
                value1: 2,
                value2: 5,
                value3: 3
            },
            {
                category: "Three",
                value1: 3,
                value2: 5,
                value3: 4
            },
            {
                category: "Four",
                value1: 4,
                value2: 5,
                value3: 6
            },
            {
                category: "Five",
                value1: 3,
                value2: 5,
                value3: 4
            },
            {
                category: "Six",
                value1: 2,
                value2: 13,
                value3: 1
            }
        ];

        chart.colors.step = 2;
        chart.padding(30, 30, 10, 30);
        chart.legend = new am4charts.Legend();

        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "category";
        categoryAxis.renderer.grid.template.location = 0;

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.min = 0;
        valueAxis.max = 100;
        valueAxis.strictMinMax = true;
        valueAxis.calculateTotals = true;
        valueAxis.renderer.minWidth = 50;


        var series1 = chart.series.push(new am4charts.ColumnSeries());
        series1.columns.template.width = am4core.percent(80);
        series1.columns.template.tooltipText =
            "{name}: {valueY.totalPercent.formatNumber('#.00')}%";
        series1.name = "Series 1";
        series1.dataFields.categoryX = "category";
        series1.dataFields.valueY = "value1";
        series1.dataFields.valueYShow = "totalPercent";
        series1.dataItems.template.locations.categoryX = 0.5;
        series1.stacked = true;
        series1.tooltip.pointerOrientation = "vertical";

        var bullet1 = series1.bullets.push(new am4charts.LabelBullet());
        bullet1.interactionsEnabled = false;
        bullet1.label.text = "{valueY.totalPercent.formatNumber('#.00')}%";
        bullet1.label.fill = am4core.color("#ffffff");
        bullet1.locationY = 0.5;

        var series2 = chart.series.push(new am4charts.ColumnSeries());
        series2.columns.template.width = am4core.percent(80);
        series2.columns.template.tooltipText =
            "{name}: {valueY.totalPercent.formatNumber('#.00')}%";
        series2.name = "Series 2";
        series2.dataFields.categoryX = "category";
        series2.dataFields.valueY = "value2";
        series2.dataFields.valueYShow = "totalPercent";
        series2.dataItems.template.locations.categoryX = 0.5;
        series2.stacked = true;
        series2.tooltip.pointerOrientation = "vertical";

        var bullet2 = series2.bullets.push(new am4charts.LabelBullet());
        bullet2.interactionsEnabled = false;
        bullet2.label.text = "{valueY.totalPercent.formatNumber('#.00')}%";
        bullet2.locationY = 0.5;
        bullet2.label.fill = am4core.color("#ffffff");

        var series3 = chart.series.push(new am4charts.ColumnSeries());
        series3.columns.template.width = am4core.percent(80);
        series3.columns.template.tooltipText =
            "{name}: {valueY.totalPercent.formatNumber('#.00')}%";
        series3.name = "Series 3";
        series3.dataFields.categoryX = "category";
        series3.dataFields.valueY = "value3";
        series3.dataFields.valueYShow = "totalPercent";
        series3.dataItems.template.locations.categoryX = 0.5;
        series3.stacked = true;
        series3.tooltip.pointerOrientation = "vertical";

        var bullet3 = series3.bullets.push(new am4charts.LabelBullet());
        bullet3.interactionsEnabled = false;
        bullet3.label.text = "{valueY.totalPercent.formatNumber('#.00')}%";
        bullet3.locationY = 0.5;
        bullet3.label.fill = am4core.color("#ffffff");

        chart.scrollbarX = new am4core.Scrollbar();

    });
    // ++++++++++++++++++++++++++++++++++++++ 100% Stacked Column Chart ++++++++++++++++++++++++++++++++++++++
    // ====================================== 3D Pie chart ======================================
    am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        var chart = am4core.create("chartDiv3DPieChart", am4charts.PieChart3D);
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
        chart.logo.disabled = true;

        chart.legend = new am4charts.Legend();

        chart.data = [{
                country: "Lithuania",
                litres: 501.9
            },
            {
                country: "Czech Republic",
                litres: 301.9
            },
            {
                country: "Ireland",
                litres: 201.1
            },
            {
                country: "Germany",
                litres: 165.8
            },
            {
                country: "Australia",
                litres: 139.9
            },
            {
                country: "Austria",
                litres: 128.3
            },
            {
                country: "UK",
                litres: 99
            },
            {
                country: "Belgium",
                litres: 60
            },
            {
                country: "The Netherlands",
                litres: 50
            }
        ];

        var series = chart.series.push(new am4charts.PieSeries3D());
        series.dataFields.value = "litres";
        series.dataFields.category = "country";

    });
    // ++++++++++++++++++++++++++++++++++++++ 3D Pie chart ++++++++++++++++++++++++++++++++++++++
</script>




<?php
require_once("../common/footer.php");
?>