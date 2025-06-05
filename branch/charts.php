<?php

require_once("../app/v1/connection-branch-admin.php");

require_once("common/header.php");

require_once("common/navbar.php");

require_once("common/sidebar.php");

require_once("common/footer.php");

// administratorAuth();

?>

<!-- <link rel="stylesheet" href="../public/assets/ref-style.css"> -->
<link rel="stylesheet" href="../public/assets/listing.css">

<!-- Styles -->
<style>
    .chartContainer {
        width: 100%;
        height: 450px;
    }

    .card.flex-fill h5 {
        color: #fff;
        font-size: 15px;
    }

    .card.flex-fill .card-header {
        padding: 15px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card.flex-fill .card-header input,
    .card.flex-fill .card-header select {
        max-width: 155px;
    }

    .head-title,
    .head-input {
        display: flex;
        gap: 10px;
        align-items: center;
    }


    .card-body::after,
    .card-footer::after,
    .card-header::after {
        display: none;
    }

    .pin-tab {
        cursor: pointer;
        text-decoration: none;
    }
</style>

<!-- Resources -->
<script src="../public/assets/core.js"></script>
<script src="../public/assets/charts.js"></script>
<script src="../public/assets/animated.js"></script>
<script src="../public/assets/forceDirected.js"></script>
<script src="../public/assets/sunburst.js"></script>

<!-- HTML -->
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid my-4">

            <div class="row">
                <!-- PERIODIC WISE DAILY SALES -->
                <div class="col-md-12 col-sm-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <div class="head-title">

                                <i class="fa fa-thumbtack po-list-icon"></i>

                                <h5 class="card-title chartDivDailyPeriodicSales"></h5>
                            </div>
                            <div class="head-input">
                                <input type="text" name="daterange" class="form-control" value="01/01/2018 - 01/15/2018" />
                                <select name="" id="filterDropdown" class="form-control">
                                    <option value="">1</option>
                                    <option value="">1</option>
                                    <option value="">1</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chartDivDailyPeriodicSales" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- PERIODIC WISE DAILY SALES -->
            
                <!-- PRODUCT QUANTITY WISE DAILY SALES -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <div class="head-title">

                                <i class="fa fa-thumbtack text-white"></i>

                                <h5 class="card-title chartDivDailyProductQuantityWiseSales"></h5>
                            </div>
                            <div class="head-input">
                                <input type="text" name="daterange" class="form-control" value="01/01/2018 - 01/15/2018" />
                                <select name="" id="filterDropdown" class="form-control">
                                    <option value="">1</option>
                                    <option value="">1</option>
                                    <option value="">1</option>
                                </select>
                            </div>
                        </div>
                    
                        <div class="card-body">
                            <div id="chartDivDailyProductQuantityWiseSales" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- PRODUCT QUANTITY WISE DAILY SALES -->

                <!-- PRODUCT PRICE WISE DAILY SALES -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivDailyProductPriceWiseSales"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivDailyProductPriceWiseSales" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- PRODUCT PRICE WISE DAILY SALES -->
            
                <!-- PRODUCT QUANTITY WISE MONTHLY SALES -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivMonthlyProductQuantityWiseSales"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivMonthlyProductQuantityWiseSales" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- PRODUCT QUANTITY WISE MONTHLY SALES -->

                <!-- PRODUCT PRICE WISE MONTHLY SALES -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivMonthlyProductPriceWiseSales"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivMonthlyProductPriceWiseSales" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- PRODUCT PRICE WISE MONTHLY SALES -->
            
                <!-- PRODUCT QUANTITY WISE YEARLY SALES -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivYearlyProductQuantityWiseSales"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivYearlyProductQuantityWiseSales" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- PRODUCT QUANTITY WISE YEARLY SALES -->

                <!-- PRODUCT PRICE WISE YEARLY SALES -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivYearlyProductPriceWiseSales"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivYearlyProductPriceWiseSales" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- PRODUCT PRICE WISE YEARLY SALES -->
            
                <!-- PRODUCT QUANTITY WISE SALES ON DATE -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivProductQuantityWiseSalesOnDate"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivProductQuantityWiseSalesOnDate" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- PRODUCT QUANTITY WISE SALES ON DATE -->

                <!-- PRODUCT PRICE WISE SALES ON DATE -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivProductPriceWiseSalesOnDate"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivProductPriceWiseSalesOnDate" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- PRODUCT PRICE WISE SALES ON DATE -->
            
                <!-- PRODUCT QUANTITY WISE SALES ON MONTH -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivProductQuantityWiseSalesOnMonth"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivProductQuantityWiseSalesOnMonth" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- PRODUCT QUANTITY WISE SALES ON MONTH -->

                <!-- PRODUCT PRICE WISE SALES ON MONTH -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivProductPriceWiseSalesOnMonth"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivProductPriceWiseSalesOnMonth" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- PRODUCT PRICE WISE SALES ON MONTH -->
           
                <!-- PROFIT CENTER WISE DAILY SALES -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivDailyProfitCenterWiseSales"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivDailyProfitCenterWiseSales" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- PROFIT CENTER WISE DAILY SALES -->

                <!-- PROFIT CENTER WISE MONTHLY SALES -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivMonthlyProfitCenterWiseSales"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivMonthlyProfitCenterWiseSales" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- PROFIT CENTER WISE MONTHLY SALES -->
           
                <!-- PROFIT CENTER WISE YEARLY SALES -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivYearlyProfitCenterWiseSales"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivYearlyProfitCenterWiseSales" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- PROFIT CENTER WISE YEARLY SALES -->
           
                <!-- PRODUCT QUANTITY WISE PROFIT CENTER SALES -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivProductQuantityWiseProfitCenterSales"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivProductQuantityWiseProfitCenterSales" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- PRODUCT QUANTITY WISE PROFIT CENTER SALES -->

                <!-- PRODUCT PRICE WISE PROFIT CENTER SALES -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivProductPriceWiseProfitCenterSales"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivProductPriceWiseProfitCenterSales" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- PRODUCT PRICE WISE PROFIT CENTER SALES -->
            
                <!-- KAM WISE QUANTITY PRODUCT GROUP SALES -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivKamWiseQuantityProductGroupSales"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivKamWiseQuantityProductGroupSales" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- KAM WISE QUANTITY PRODUCT GROUP SALES -->

                <!-- KAM WISE PRICING PRODUCT GROUP SALES -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivKamWisePricingProductGroupSales"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivKamWisePricingProductGroupSales" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- KAM WISE PRICING PRODUCT GROUP SALES -->
            
                <!-- STATE WISE QUANTITY PRODUCT GROUP SALES -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivStateWiseQuantityProductGroupSales"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivStateWiseQuantityProductGroupSales" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- STATE WISE QUANTITY PRODUCT GROUP SALES -->

                <!-- STATE WISE PRICING PRODUCT GROUP SALES -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivStateWisePricingProductGroupSales"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivStateWisePricingProductGroupSales" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- STATE WISE PRICING PRODUCT GROUP SALES -->
            
                <!-- CUSTOMER WISE RECEIVABLES -->
                <div class="col-md-12 col-sm-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivCustomerWiseReceivables"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivCustomerWiseReceivables" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- CUSTOMER WISE RECEIVABLES -->

                <!-- KAM WISE RECEIVABLES -->
                <div class="col-md-12 col-sm-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivKamWiseReceivables"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivKamWiseReceivables" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- KAM WISE RECEIVABLES -->
            
                <!-- VENDOR WISE PAYABLES -->
                <div class="col-md-12 col-sm-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivVendorWisePayables"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivVendorWisePayables" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- VENDOR WISE PAYABLES -->
            
                <!-- SALES ORDER BOOK -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivSalesOrderBook"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivSalesOrderBook" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- SALES ORDER BOOK -->

                <!-- PURCHASE ORDER BOOK -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivPurchaseOrderBook"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivPurchaseOrderBook" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- PURCHASE ORDER BOOK -->
            
                <!-- SALES VS COLLECTION -->
                <div class="col-md-12 col-sm-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title chartDivSalesVsCollection"></h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivSalesVsCollection" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- SALES VS COLLECTION -->
            </div>

        </div>
    </section>
</div>

<!-- Chart code -->
<script>
    // **************************************************** CHART FUNCTIONS ********************************************************
    // =============================================================================================================================
    function dailyPeriodicSalesChart(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Daily Periodic Sales`);


            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

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


    // =============================================================================================================================
    function dailyProductQuantityWiseSalesChart(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Daily Quantity Wise Sales for ${chartData[0].item_name}`);

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            let data = [];

            chartData.map((dataObject) => {
                let object = {};
                object.color = chart.colors.next();
                object.bullet = "https://www.amcharts.com/lib/images/faces/E01.png";
                object.x_axis = dataObject.date_;
                object.y_axis = Number(dataObject.total_qty);
                data.push(object);
            });

            // Add data
            chart.data = data;

            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
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
            chart.paddingBottom = 0;

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "y_axis";
            series.dataFields.categoryX = "x_axis";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.propertyFields.stroke = "color";
            series.columns.template.column.cornerRadiusTopLeft = 15;
            series.columns.template.column.cornerRadiusTopRight = 15;
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/b]";

            // Add bullets
            var bullet = series.bullets.push(new am4charts.Bullet());
            var image = bullet.createChild(am4core.Image);
            image.horizontalCenter = "middle";
            image.verticalCenter = "bottom";
            image.dy = 20;
            image.y = am4core.percent(100);
            image.propertyFields.href = "bullet";
            image.tooltipText = series.columns.template.tooltipText;
            image.propertyFields.fill = "color";
            image.filters.push(new am4core.DropShadowFilter());

        });
    };

    function dailyProductPriceWiseSalesChart(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Daily Price Wise Sales for ${chartData[0].item_name}`);

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            let data = [];

            chartData.map((dataObject) => {
                let object = {};
                object.color = chart.colors.next();
                object.bullet = "https://www.amcharts.com/lib/images/faces/E01.png";
                object.x_axis = dataObject.date_;
                object.y_axis = Number(dataObject.total_amount);
                data.push(object);
            });

            // Add data
            chart.data = data;

            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
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
            chart.paddingBottom = 0;

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "y_axis";
            series.dataFields.categoryX = "x_axis";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.propertyFields.stroke = "color";
            series.columns.template.column.cornerRadiusTopLeft = 15;
            series.columns.template.column.cornerRadiusTopRight = 15;
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/b]";

            // Add bullets
            var bullet = series.bullets.push(new am4charts.Bullet());
            var image = bullet.createChild(am4core.Image);
            image.horizontalCenter = "middle";
            image.verticalCenter = "bottom";
            image.dy = 20;
            image.y = am4core.percent(100);
            image.propertyFields.href = "bullet";
            image.tooltipText = series.columns.template.tooltipText;
            image.propertyFields.fill = "color";
            image.filters.push(new am4core.DropShadowFilter());

        });
    };

    function monthlyProductQuantityWiseSalesChart(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Monthly Quantity Wise Sales for ${chartData[0].item_name}`);

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            let data = [];

            chartData.map((dataObject) => {
                let object = {};
                object.color = chart.colors.next();
                object.bullet = "https://www.amcharts.com/lib/images/faces/C02.png";
                object.x_axis = dataObject.month;
                object.y_axis = Number(dataObject.total_qty);
                data.push(object);
            });

            // Add data
            chart.data = data;

            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
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
            chart.paddingBottom = 0;

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "y_axis";
            series.dataFields.categoryX = "x_axis";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.propertyFields.stroke = "color";
            series.columns.template.column.cornerRadiusTopLeft = 15;
            series.columns.template.column.cornerRadiusTopRight = 15;
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/b]";

            // Add bullets
            var bullet = series.bullets.push(new am4charts.Bullet());
            var image = bullet.createChild(am4core.Image);
            image.horizontalCenter = "middle";
            image.verticalCenter = "bottom";
            image.dy = 20;
            image.y = am4core.percent(100);
            image.propertyFields.href = "bullet";
            image.tooltipText = series.columns.template.tooltipText;
            image.propertyFields.fill = "color";
            image.filters.push(new am4core.DropShadowFilter());

        });
    };

    function monthlyProductPriceWiseSalesChart(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Monthly Price Wise Sales for ${chartData[0].item_name}`);

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            let data = [];

            chartData.map((dataObject) => {
                let object = {};
                object.color = chart.colors.next();
                object.bullet = "https://www.amcharts.com/lib/images/faces/C02.png";
                object.x_axis = dataObject.month;
                object.y_axis = Number(dataObject.total_amount);
                data.push(object);
            });

            // Add data
            chart.data = data;

            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
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
            chart.paddingBottom = 0;

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "y_axis";
            series.dataFields.categoryX = "x_axis";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.propertyFields.stroke = "color";
            series.columns.template.column.cornerRadiusTopLeft = 15;
            series.columns.template.column.cornerRadiusTopRight = 15;
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/b]";

            // Add bullets
            var bullet = series.bullets.push(new am4charts.Bullet());
            var image = bullet.createChild(am4core.Image);
            image.horizontalCenter = "middle";
            image.verticalCenter = "bottom";
            image.dy = 20;
            image.y = am4core.percent(100);
            image.propertyFields.href = "bullet";
            image.tooltipText = series.columns.template.tooltipText;
            image.propertyFields.fill = "color";
            image.filters.push(new am4core.DropShadowFilter());

        });
    };

    function yearlyProductQuantityWiseSalesChart(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Product Quantity Wise Sales FY 2022-23`);

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            let data = [];

            chartData.map((dataObject) => {
                let object = {};
                object.color = chart.colors.next();
                object.bullet = "https://www.amcharts.com/lib/images/faces/C02.png";
                object.x_axis = dataObject.item_name;
                object.y_axis = Number(dataObject.total_qty);
                data.push(object);
            });

            // Add data
            chart.data = data;

            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "x_axis";
            categoryAxis.renderer.grid.template.disabled = true;
            categoryAxis.renderer.inside = true;
            categoryAxis.renderer.labels.template.fill = am4core.color("#000");

            // **************************************************************************
            categoryAxis.renderer.labels.template.fontSize = 10;
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.minGridDistance = 30;
            categoryAxis.renderer.labels.template.horizontalCenter = "right";
            categoryAxis.renderer.labels.template.verticalCenter = "middle";
            categoryAxis.renderer.labels.template.rotation = -70;
            categoryAxis.tooltip.disabled = true;
            categoryAxis.renderer.minHeight = 210;
            // **************************************************************************

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.renderer.grid.template.strokeDasharray = "4,4";
            valueAxis.renderer.labels.template.disabled = true;
            valueAxis.min = 0;

            // Do not crop bullets
            // chart.maskBullets = false;

            // Remove padding
            chart.paddingBottom = 0;

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "y_axis";
            series.dataFields.categoryX = "x_axis";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.propertyFields.stroke = "color";
            series.columns.template.column.cornerRadiusTopLeft = 15;
            series.columns.template.column.cornerRadiusTopRight = 15;
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/b]";

            // Add bullets
            var bullet = series.bullets.push(new am4charts.Bullet());
            var image = bullet.createChild(am4core.Image);
            image.horizontalCenter = "middle";
            image.verticalCenter = "bottom";
            image.dy = 20;
            image.y = am4core.percent(100);
            image.propertyFields.href = "bullet";
            image.tooltipText = series.columns.template.tooltipText;
            image.propertyFields.fill = "color";
            image.filters.push(new am4core.DropShadowFilter());

        });
    };

    function yearlyProductPriceWiseSalesChart(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Product Price Wise Sales FY 2022-23`);

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            let data = [];

            chartData.map((dataObject) => {
                let object = {};
                object.color = chart.colors.next();
                object.bullet = "https://www.amcharts.com/lib/images/faces/C02.png";
                object.x_axis = dataObject.item_name;
                object.y_axis = Number(dataObject.total_amount);
                data.push(object);
            });

            // Add data
            chart.data = data;

            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            categoryAxis.dataFields.category = "x_axis";
            categoryAxis.renderer.grid.template.disabled = true;
            categoryAxis.renderer.minGridDistance = 30;
            categoryAxis.renderer.inside = true;
            categoryAxis.renderer.labels.template.fill = am4core.color("#000");

            // **************************************************************************
            categoryAxis.renderer.labels.template.fontSize = 10;
            categoryAxis.renderer.grid.template.location = 0;
            categoryAxis.renderer.minGridDistance = 30;
            categoryAxis.renderer.labels.template.horizontalCenter = "right";
            categoryAxis.renderer.labels.template.verticalCenter = "middle";
            categoryAxis.renderer.labels.template.rotation = -70;
            categoryAxis.tooltip.disabled = true;
            categoryAxis.renderer.minHeight = 210;
            // **************************************************************************

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.renderer.grid.template.strokeDasharray = "4,4";
            valueAxis.renderer.labels.template.disabled = true;
            valueAxis.min = 0;

            // Do not crop bullets
            // chart.maskBullets = false;

            // Remove padding
            chart.paddingBottom = 0;

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "y_axis";
            series.dataFields.categoryX = "x_axis";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.propertyFields.stroke = "color";
            series.columns.template.column.cornerRadiusTopLeft = 15;
            series.columns.template.column.cornerRadiusTopRight = 15;
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/b]";

            // Add bullets
            var bullet = series.bullets.push(new am4charts.Bullet());
            var image = bullet.createChild(am4core.Image);
            image.horizontalCenter = "middle";
            image.verticalCenter = "bottom";
            image.dy = 20;
            image.y = am4core.percent(100);
            image.propertyFields.href = "bullet";
            image.tooltipText = series.columns.template.tooltipText;
            image.propertyFields.fill = "color";
            image.filters.push(new am4core.DropShadowFilter());

        });
    };

    function productQuantityWiseSalesOnDate(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Product Quantity Wise Sales On Date`);

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            let data = [];

            chartData.map((dataObject) => {
                let object = {};
                object.color = chart.colors.next();
                object.bullet = "https://www.amcharts.com/lib/images/faces/C02.png";
                object.x_axis = dataObject.item_name;
                object.y_axis = Number(dataObject.total_qty);
                data.push(object);
            });

            // Add data
            chart.data = data;

            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
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
            chart.paddingBottom = 0;

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "y_axis";
            series.dataFields.categoryX = "x_axis";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.propertyFields.stroke = "color";
            series.columns.template.column.cornerRadiusTopLeft = 15;
            series.columns.template.column.cornerRadiusTopRight = 15;
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/b]";

            // Add bullets
            var bullet = series.bullets.push(new am4charts.Bullet());
            var image = bullet.createChild(am4core.Image);
            image.horizontalCenter = "middle";
            image.verticalCenter = "bottom";
            image.dy = 20;
            image.y = am4core.percent(100);
            image.propertyFields.href = "bullet";
            image.tooltipText = series.columns.template.tooltipText;
            image.propertyFields.fill = "color";
            image.filters.push(new am4core.DropShadowFilter());

        });
    };

    function productPriceWiseSalesOnDate(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Product Price Wise Sales On Date`);

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            let data = [];

            chartData.map((dataObject) => {
                let object = {};
                object.color = chart.colors.next();
                object.bullet = "https://www.amcharts.com/lib/images/faces/C02.png";
                object.x_axis = dataObject.item_name;
                object.y_axis = Number(dataObject.total_amount);
                data.push(object);
            });

            // Add data
            chart.data = data;

            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
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
            chart.paddingBottom = 0;

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "y_axis";
            series.dataFields.categoryX = "x_axis";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.propertyFields.stroke = "color";
            series.columns.template.column.cornerRadiusTopLeft = 15;
            series.columns.template.column.cornerRadiusTopRight = 15;
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/b]";

            // Add bullets
            var bullet = series.bullets.push(new am4charts.Bullet());
            var image = bullet.createChild(am4core.Image);
            image.horizontalCenter = "middle";
            image.verticalCenter = "bottom";
            image.dy = 20;
            image.y = am4core.percent(100);
            image.propertyFields.href = "bullet";
            image.tooltipText = series.columns.template.tooltipText;
            image.propertyFields.fill = "color";
            image.filters.push(new am4core.DropShadowFilter());

        });
    };

    function productQuantityWiseSalesOnMonth(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Product Quantity Wise Sales On Month`);

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            let data = [];

            chartData.map((dataObject) => {
                let object = {};
                object.color = chart.colors.next();
                object.bullet = "https://www.amcharts.com/lib/images/faces/C02.png";
                object.x_axis = dataObject.item_name;
                object.y_axis = Number(dataObject.total_qty);
                data.push(object);
            });

            // Add data
            chart.data = data;

            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
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
            chart.paddingBottom = 0;

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "y_axis";
            series.dataFields.categoryX = "x_axis";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.propertyFields.stroke = "color";
            series.columns.template.column.cornerRadiusTopLeft = 15;
            series.columns.template.column.cornerRadiusTopRight = 15;
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/b]";

            // Add bullets
            var bullet = series.bullets.push(new am4charts.Bullet());
            var image = bullet.createChild(am4core.Image);
            image.horizontalCenter = "middle";
            image.verticalCenter = "bottom";
            image.dy = 20;
            image.y = am4core.percent(100);
            image.propertyFields.href = "bullet";
            image.tooltipText = series.columns.template.tooltipText;
            image.propertyFields.fill = "color";
            image.filters.push(new am4core.DropShadowFilter());

        });
    };

    function productPriceWiseSalesOnMonth(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Product Price Wise Sales On Month`);

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            let data = [];

            if (chartData.length) {
                chartData.map((dataObject) => {
                    let object = {};
                    object.color = chart.colors.next();
                    object.bullet = "https://www.amcharts.com/lib/images/faces/C02.png";
                    object.x_axis = dataObject.item_name;
                    object.y_axis = Number(dataObject.total_amount);
                    data.push(object);
                });
            }else{
                data = [
                    {
                        "color" : chart.colors.next(),
                        "x_axis" : "",
                        "y_axis" : 0
                    }
                ]; 
            };
            
            // Add data
            chart.data = data;

            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
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
            chart.paddingBottom = 0;

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "y_axis";
            series.dataFields.categoryX = "x_axis";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.propertyFields.stroke = "color";
            series.columns.template.column.cornerRadiusTopLeft = 15;
            series.columns.template.column.cornerRadiusTopRight = 15;
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/b]";

            // Add bullets
            var bullet = series.bullets.push(new am4charts.Bullet());
            var image = bullet.createChild(am4core.Image);
            image.horizontalCenter = "middle";
            image.verticalCenter = "bottom";
            image.dy = 20;
            image.y = am4core.percent(100);
            image.propertyFields.href = "bullet";
            image.tooltipText = series.columns.template.tooltipText;
            image.propertyFields.fill = "color";
            image.filters.push(new am4core.DropShadowFilter());

        });
    };
    // =============================================================================================================================


    // =======================================================================================================================================
    function dailyProfitCenterWiseSalesChart(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Daily Profit Center Wise Sales`);

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            let data = [];

            chartData.map((dataObject) => {
                let object = {};
                object.color = chart.colors.next();
                object.bullet = "https://www.amcharts.com/lib/images/faces/A04.png";
                object.x_axis = dataObject.date_;
                object.y_axis = Number(dataObject.total_amount);
                data.push(object);
            });

            // Add data
            chart.data = data;

            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
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
            chart.paddingBottom = 0;

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "y_axis";
            series.dataFields.categoryX = "x_axis";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.propertyFields.stroke = "color";
            series.columns.template.column.cornerRadiusTopLeft = 15;
            series.columns.template.column.cornerRadiusTopRight = 15;
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/b]";

            // Add bullets
            var bullet = series.bullets.push(new am4charts.Bullet());
            var image = bullet.createChild(am4core.Image);
            image.horizontalCenter = "middle";
            image.verticalCenter = "bottom";
            image.dy = 20;
            image.y = am4core.percent(100);
            image.propertyFields.href = "bullet";
            image.tooltipText = series.columns.template.tooltipText;
            image.propertyFields.fill = "color";
            image.filters.push(new am4core.DropShadowFilter());

        });
    };

    function monthlyProfitCenterWiseSalesChart(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Monthly Profit Center Wise Sales`);

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            let data = [];

            chartData.map((dataObject) => {
                let object = {};
                object.color = chart.colors.next();
                object.bullet = "https://www.amcharts.com/lib/images/faces/A04.png";
                object.x_axis = dataObject.month;
                object.y_axis = Number(dataObject.total_amount);
                data.push(object);
            });

            // Add data
            chart.data = data;

            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
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
            chart.paddingBottom = 0;

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "y_axis";
            series.dataFields.categoryX = "x_axis";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.propertyFields.stroke = "color";
            series.columns.template.column.cornerRadiusTopLeft = 15;
            series.columns.template.column.cornerRadiusTopRight = 15;
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/b]";

            // Add bullets
            var bullet = series.bullets.push(new am4charts.Bullet());
            var image = bullet.createChild(am4core.Image);
            image.horizontalCenter = "middle";
            image.verticalCenter = "bottom";
            image.dy = 20;
            image.y = am4core.percent(100);
            image.propertyFields.href = "bullet";
            image.tooltipText = series.columns.template.tooltipText;
            image.propertyFields.fill = "color";
            image.filters.push(new am4core.DropShadowFilter());

        });
    };

    function yearlyProfitCenterWiseSalesChart(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Profit Center Wise Sales FY 2022-23`);

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            let data = [];

            chartData.map((dataObject) => {
                let object = {};
                object.color = chart.colors.next();
                object.bullet = "https://www.amcharts.com/lib/images/faces/A04.png";
                object.x_axis = dataObject.profit_center;
                object.y_axis = Number(dataObject.total_amount);
                data.push(object);
            });

            // Add data
            chart.data = data;

            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
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
            chart.paddingBottom = 0;

            // Create series
            var series = chart.series.push(new am4charts.ColumnSeries());
            series.dataFields.valueY = "y_axis";
            series.dataFields.categoryX = "x_axis";
            series.columns.template.propertyFields.fill = "color";
            series.columns.template.propertyFields.stroke = "color";
            series.columns.template.column.cornerRadiusTopLeft = 15;
            series.columns.template.column.cornerRadiusTopRight = 15;
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/b]";

            // Add bullets
            var bullet = series.bullets.push(new am4charts.Bullet());
            var image = bullet.createChild(am4core.Image);
            image.horizontalCenter = "middle";
            image.verticalCenter = "bottom";
            image.dy = 20;
            image.y = am4core.percent(100);
            image.propertyFields.href = "bullet";
            image.tooltipText = series.columns.template.tooltipText;
            image.propertyFields.fill = "color";
            image.filters.push(new am4core.DropShadowFilter());

        });
    };
    // =======================================================================================================================================


    // =======================================================================================================================================
    function productQuantityWiseProfitCenterSales(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Product Quantity Wise Profit Center Sales`);

            // Themes
            am4core.useTheme(am4themes_animated);

            /**
             * Source data
             */
            let finalData = [];
            let tempData = "";
            let counter = 0;

            for (let obj of chartData) {

                if (obj.profit_center === tempData) {
                    finalData[counter].value += Number(obj.total_qty);
                    finalData[counter].breakdown.push({
                        "category": obj.item_name,
                        "value": Number(obj.total_qty)
                    });
                } else {
                    if (finalData.length > 0) {
                        counter++;
                    }

                    finalData.push({
                        "category": obj.profit_center,
                        "value": Number(obj.total_qty),
                        "breakdown": [{
                            "category": obj.item_name,
                            "value": Number(obj.total_qty)
                        }]
                    });

                }
                tempData = obj.profit_center;
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

    function productPriceWiseProfitCenterSales(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Product Price Wise Profit Center Sales`);

            // Themes
            am4core.useTheme(am4themes_animated);

            /**
             * Source data
             */
            let finalData = [];
            let tempData = "";
            let counter = 0;

            for (let obj of chartData) {

                if (obj.profit_center === tempData) {
                    finalData[counter].value += Number(obj.total_price);
                    finalData[counter].breakdown.push({
                        "category": obj.item_name,
                        "value": Number(obj.total_price)
                    });
                } else {
                    if (finalData.length > 0) {
                        counter++;
                    }

                    finalData.push({
                        "category": obj.profit_center,
                        "value": Number(obj.total_price),
                        "breakdown": [{
                            "category": obj.item_name,
                            "value": Number(obj.total_price)
                        }]
                    });

                }
                tempData = obj.profit_center;
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
    // =======================================================================================================================================


    // =======================================================================================================================================
    function kamWiseQuantityProductGroupSales(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`KAM Wise Quantity Product Group Sales`);

            // Themes
            am4core.useTheme(am4themes_animated);

            /**
             * Source data
             */
            let finalData = [];
            let tempData = "";
            let counter = 0;

            for (let obj of chartData) {

                if (obj.goodGroupName === tempData) {
                    finalData[counter].value += Number(obj.total_qty);
                    finalData[counter].breakdown.push({
                        "category": obj.kam_name,
                        "value": Number(obj.total_qty)
                    });
                } else {
                    if (finalData.length > 0) {
                        counter++;
                    }

                    finalData.push({
                        "category": obj.goodGroupName,
                        "value": Number(obj.total_qty),
                        "breakdown": [{
                            "category": obj.kam_name,
                            "value": Number(obj.total_qty)
                        }]
                    });

                }
                tempData = obj.goodGroupName;
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

    function kamWisePricingProductGroupSales(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`KAM Wise Pricing Product Group Sales`);

            // Themes
            am4core.useTheme(am4themes_animated);

            /**
             * Source data
             */
            let finalData = [];
            let tempData = "";
            let counter = 0;

            for (let obj of chartData) {

                if (obj.goodGroupName === tempData) {
                    finalData[counter].value += Number(obj.total_price);
                    finalData[counter].breakdown.push({
                        "category": obj.kam_name,
                        "value": Number(obj.total_price)
                    });
                } else {
                    if (finalData.length > 0) {
                        counter++;
                    }

                    finalData.push({
                        "category": obj.goodGroupName,
                        "value": Number(obj.total_price),
                        "breakdown": [{
                            "category": obj.kam_name,
                            "value": Number(obj.total_price)
                        }]
                    });

                }
                tempData = obj.goodGroupName;
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

    function stateWiseQuantityProductGroupSales(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`State Wise Quantity Product Group Sales`);

            // Themes
            am4core.useTheme(am4themes_animated);

            /**
             * Source data
             */
            let finalData = [];
            let tempData = "";
            let counter = 0;

            for (let obj of chartData) {

                if (obj.goodGroupName === tempData) {
                    finalData[counter].value += Number(obj.total_qty);
                    finalData[counter].breakdown.push({
                        "category": obj.state,
                        "value": Number(obj.total_qty)
                    });
                } else {
                    if (finalData.length > 0) {
                        counter++;
                    }

                    finalData.push({
                        "category": obj.goodGroupName,
                        "value": Number(obj.total_qty),
                        "breakdown": [{
                            "category": obj.state,
                            "value": Number(obj.total_qty)
                        }]
                    });

                }
                tempData = obj.goodGroupName;
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

    function stateWisePricingProductGroupSales(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`State Wise Pricing Product Group Sales`);

            // Themes
            am4core.useTheme(am4themes_animated);

            /**
             * Source data
             */
            let finalData = [];
            let tempData = "";
            let counter = 0;

            for (let obj of chartData) {

                if (obj.goodGroupName === tempData) {
                    finalData[counter].value += Number(obj.total_price);
                    finalData[counter].breakdown.push({
                        "category": obj.state,
                        "value": Number(obj.total_price)
                    });
                } else {
                    if (finalData.length > 0) {
                        counter++;
                    }

                    finalData.push({
                        "category": obj.goodGroupName,
                        "value": Number(obj.total_price),
                        "breakdown": [{
                            "category": obj.state,
                            "value": Number(obj.total_price)
                        }]
                    });

                }
                tempData = obj.goodGroupName;
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
    // =======================================================================================================================================


    // =======================================================================================================================================
    function customerWiseReceivables(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Customer Wise Receivables`);

            // Themes
            am4core.useTheme(am4themes_animated);

            /**
             * Source data
             */

            let formattedData = chartData.map(obj => {
                if (obj.due_days >= 0 && obj.due_days <= 30) {
                    obj.type = "0-30 days";
                    return obj;
                } else if (obj.due_days >= 31 && obj.due_days <= 60) {
                    obj.type = "31-60 days";
                    return obj;
                } else if (obj.due_days >= 61 && obj.due_days <= 90) {
                    obj.type = "61-90 days";
                    return obj;
                } else if (obj.due_days >= 91 && obj.due_days <= 180) {
                    obj.type = "91-180 days";
                    return obj;
                } else if (obj.due_days >= 181 && obj.due_days <= 365) {
                    obj.type = "181-365 days";
                    return obj;
                } else {
                    obj.type = "More than 365 days";
                    return obj;
                };
            });

            let finalData = [];
            let tempData = "";
            let counter = 0;
            let tempCounter = 0;

            for (let obj of formattedData) {

                if (obj.type === tempData) {
                    if (obj.trade_name === finalData[counter].breakdown[tempCounter].category) {
                        finalData[counter].value += Number(obj.total_due_amount);
                        finalData[counter].breakdown[tempCounter].value += Number(obj.total_due_amount);
                    } else {
                        if (finalData[counter].breakdown.length > 0) {
                            tempCounter++;
                        };
                        finalData[counter].value += Number(obj.total_due_amount);
                        finalData[counter].breakdown.push({
                            "category": obj.trade_name,
                            "value": Number(obj.total_due_amount)
                        });
                    }
                } else {
                    if (finalData.length > 0) {
                        counter++;
                    }
                    finalData.push({
                        "category": obj.type,
                        "value": Number(obj.total_due_amount),
                        "breakdown": [{
                            "category": obj.trade_name,
                            "value": Number(obj.total_due_amount)
                        }]
                    });
                };
                tempData = obj.type;
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

    function kamWiseReceivables(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`KAM Wise Receivables`);

            // Themes
            am4core.useTheme(am4themes_animated);

            /**
             * Source data
            */

            console.log("1:",chartData)

            let formattedData = chartData.map(obj => {
                if (obj.due_days >= 0 && obj.due_days <= 30) {
                    obj.type = "0-30 days";
                    return obj;
                } else if (obj.due_days >= 31 && obj.due_days <= 60) {
                    obj.type = "31-60 days";
                    return obj;
                } else if (obj.due_days >= 61 && obj.due_days <= 90) {
                    obj.type = "61-90 days";
                    return obj;
                } else if (obj.due_days >= 91 && obj.due_days <= 180) {
                    obj.type = "91-180 days";
                    return obj;
                } else if (obj.due_days >= 181 && obj.due_days <= 365) {
                    obj.type = "181-365 days";
                    return obj;
                } else {
                    obj.type = "More than 365 days";
                    return obj;
                };
            });

            console.log("2:",formattedData)

            let finalData = [];
            let tempData = "";
            let counter = 0;
            let tempCounter = 0;

            for (let obj of formattedData) {

                if (obj.type === tempData) {
                    if (obj.kamName === finalData[counter].breakdown[tempCounter].category) {
                        finalData[counter].value += Number(obj.total_due_amount);
                        finalData[counter].breakdown[tempCounter].value += Number(obj.total_due_amount);
                    } else {
                        if (finalData[counter].breakdown.length > 0) {
                            tempCounter++;
                        };
                        finalData[counter].value += Number(obj.total_due_amount);
                        finalData[counter].breakdown.push({
                            "category": obj.kamName,
                            "value": Number(obj.total_due_amount)
                        });
                    }
                } else {
                    if (finalData.length > 0) {
                        counter++;
                    }
                    finalData.push({
                        "category": obj.type,
                        "value": Number(obj.total_due_amount),
                        "breakdown": [{
                            "category": obj.kamName,
                            "value": Number(obj.total_due_amount)
                        }]
                    });
                };
                tempData = obj.type;
            };
            console.log("3:",finalData)
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

    function vendorWisePayables(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Vendor Wise Payables`);

            // Themes
            am4core.useTheme(am4themes_animated);

            /**
             * Source data
             */

            let formattedData = chartData.map(obj => {
                if (obj.due_days >= 0 && obj.due_days <= 30) {
                    obj.type = "0-30 days";
                    return obj;
                } else if (obj.due_days >= 31 && obj.due_days <= 60) {
                    obj.type = "31-60 days";
                    return obj;
                } else if (obj.due_days >= 61 && obj.due_days <= 90) {
                    obj.type = "61-90 days";
                    return obj;
                } else if (obj.due_days >= 91 && obj.due_days <= 180) {
                    obj.type = "91-180 days";
                    return obj;
                } else if (obj.due_days >= 181 && obj.due_days <= 365) {
                    obj.type = "181-365 days";
                    return obj;
                } else {
                    obj.type = "More than 365 days";
                    return obj;
                };
            });

            let finalData = [];
            let tempData = "";
            let counter = 0;
            let tempCounter = 0;

            for (let obj of formattedData) {

                if (obj.type === tempData) {
                    if (obj.vendorName === finalData[counter].breakdown[tempCounter].category) {
                        finalData[counter].value += Number(obj.total_due_amount);
                        finalData[counter].breakdown[tempCounter].value += Number(obj.total_due_amount);
                    } else {
                        if (finalData[counter].breakdown.length > 0) {
                            tempCounter++;
                        };
                        finalData[counter].value += Number(obj.total_due_amount);
                        finalData[counter].breakdown.push({
                            "category": obj.vendorName,
                            "value": Number(obj.total_due_amount)
                        });
                    }
                } else {
                    if (finalData.length > 0) {
                        counter++;
                    }
                    finalData.push({
                        "category": obj.type,
                        "value": Number(obj.total_due_amount),
                        "breakdown": [{
                            "category": obj.vendorName,
                            "value": Number(obj.total_due_amount)
                        }]
                    });
                };
                tempData = obj.type;
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


            /**
             * Pie chart
             */

            // Create chart instance
            var pieChart = chart.createChild(am4charts.PieChart3D);
            pieChart.data = data;
            pieChart.hiddenState.properties.opacity = 0; // this creates initial fade-in

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
    // =======================================================================================================================================


    // =======================================================================================================================================
    function salesOrderBook(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Sales Order Book`);

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

    function purchaseOrderBook(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Purchase Order Book`);

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
    // =======================================================================================================================================


    // =======================================================================================================================================
    function salesVsCollection(chartData, chartTitle) {
        am4core.ready(function() {

            $(`.${chartTitle}`).text(`Sales Vs Collection`);

            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            // Add data
            chart.data = [{
                "date": "2013-01-16",
                "market1": 71,
                "market2": 75,
                "sales1": 5,
                "sales2": 8
            }, {
                "date": "2013-01-17",
                "market1": 74,
                "market2": 78,
                "sales1": 4,
                "sales2": 6
            }, {
                "date": "2013-01-18",
                "market1": 78,
                "market2": 88,
                "sales1": 5,
                "sales2": 2
            }, {
                "date": "2013-01-19",
                "market1": 85,
                "market2": 89,
                "sales1": 8,
                "sales2": 9
            }, {
                "date": "2013-01-20",
                "market1": 82,
                "market2": 89,
                "sales1": 9,
                "sales2": 6
            }, {
                "date": "2013-01-21",
                "market1": 83,
                "market2": 85,
                "sales1": 3,
                "sales2": 5
            }, {
                "date": "2013-01-22",
                "market1": 88,
                "market2": 92,
                "sales1": 5,
                "sales2": 7
            }, {
                "date": "2013-01-23",
                "market1": 85,
                "market2": 90,
                "sales1": 7,
                "sales2": 6
            }, {
                "date": "2013-01-24",
                "market1": 85,
                "market2": 91,
                "sales1": 9,
                "sales2": 5
            }, {
                "date": "2013-01-25",
                "market1": 80,
                "market2": 84,
                "sales1": 5,
                "sales2": 8
            }, {
                "date": "2013-01-26",
                "market1": 87,
                "market2": 92,
                "sales1": 4,
                "sales2": 8
            }, {
                "date": "2013-01-27",
                "market1": 84,
                "market2": 87,
                "sales1": 3,
                "sales2": 4
            }, {
                "date": "2013-01-28",
                "market1": 83,
                "market2": 88,
                "sales1": 5,
                "sales2": 7
            }, {
                "date": "2013-01-29",
                "market1": 84,
                "market2": 87,
                "sales1": 5,
                "sales2": 8
            }, {
                "date": "2013-01-30",
                "market1": 81,
                "market2": 85,
                "sales1": 4,
                "sales2": 7
            }];

            // Create axes
            var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
            //dateAxis.renderer.grid.template.location = 0;
            //dateAxis.renderer.minGridDistance = 30;

            var valueAxis1 = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis1.title.text = "Sales";

            var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis2.title.text = "Market Days";
            valueAxis2.renderer.opposite = true;
            valueAxis2.renderer.grid.template.disabled = true;

            // Create series
            var series1 = chart.series.push(new am4charts.ColumnSeries());
            series1.dataFields.valueY = "sales1";
            series1.dataFields.dateX = "date";
            series1.yAxis = valueAxis1;
            series1.name = "Target Sales";
            series1.tooltipText = "{name}\n[bold font-size: 20]${valueY}M[/]";
            series1.fill = chart.colors.getIndex(0);
            series1.strokeWidth = 0;
            series1.clustered = false;
            series1.columns.template.width = am4core.percent(40);

            var series2 = chart.series.push(new am4charts.ColumnSeries());
            series2.dataFields.valueY = "sales2";
            series2.dataFields.dateX = "date";
            series2.yAxis = valueAxis1;
            series2.name = "Actual Sales";
            series2.tooltipText = "{name}\n[bold font-size: 20]${valueY}M[/]";
            series2.fill = chart.colors.getIndex(0).lighten(0.5);
            series2.strokeWidth = 0;
            series2.clustered = false;
            series2.toBack();

            var series3 = chart.series.push(new am4charts.LineSeries());
            series3.dataFields.valueY = "market1";
            series3.dataFields.dateX = "date";
            series3.name = "Market Days";
            series3.strokeWidth = 2;
            series3.tensionX = 0.7;
            series3.yAxis = valueAxis2;
            series3.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";

            var bullet3 = series3.bullets.push(new am4charts.CircleBullet());
            bullet3.circle.radius = 3;
            bullet3.circle.strokeWidth = 2;
            bullet3.circle.fill = am4core.color("#fff");

            var series4 = chart.series.push(new am4charts.LineSeries());
            series4.dataFields.valueY = "market2";
            series4.dataFields.dateX = "date";
            series4.name = "Market Days ALL";
            series4.strokeWidth = 2;
            series4.tensionX = 0.7;
            series4.yAxis = valueAxis2;
            series4.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";
            series4.stroke = chart.colors.getIndex(0).lighten(0.5);
            series4.strokeDasharray = "3,3";

            var bullet4 = series4.bullets.push(new am4charts.CircleBullet());
            bullet4.circle.radius = 3;
            bullet4.circle.strokeWidth = 2;
            bullet4.circle.fill = am4core.color("#fff");

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
    // =======================================================================================================================================


    // AJAX CALL 
    $.ajax({
        url: "<?= BASE_URL ?>branch/location/ajaxs/reports/ajax-charts.php?",
        async: false,
        success: function(result) {

            let res = jQuery.parseJSON(result);

            // =======================================================================================================================================
            dailyPeriodicSalesChart(res.dailyPeriodicSales.data, "chartDivDailyPeriodicSales");
            // =======================================================================================================================================


            // =======================================================================================================================================
            dailyProductQuantityWiseSalesChart(res.dailyProductQuantityWiseSales.data, "chartDivDailyProductQuantityWiseSales");

            dailyProductPriceWiseSalesChart(res.dailyProductPriceWiseSales.data, "chartDivDailyProductPriceWiseSales");

            monthlyProductQuantityWiseSalesChart(res.monthlyProductQuantityWiseSales.data, "chartDivMonthlyProductQuantityWiseSales");

            monthlyProductPriceWiseSalesChart(res.monthlyProductPriceWiseSales.data, "chartDivMonthlyProductPriceWiseSales");

            yearlyProductQuantityWiseSalesChart(res.yearlyProductQuantityWiseSales.data, "chartDivYearlyProductQuantityWiseSales");

            yearlyProductPriceWiseSalesChart(res.yearlyProductPriceWiseSales.data, "chartDivYearlyProductPriceWiseSales");

            productQuantityWiseSalesOnDate(res.productQuantityWiseSalesOnDate.data, "chartDivProductQuantityWiseSalesOnDate");

            productPriceWiseSalesOnDate(res.productPriceWiseSalesOnDate.data, "chartDivProductPriceWiseSalesOnDate");

            productQuantityWiseSalesOnMonth(res.productQuantityWiseSalesOnMonth.data, "chartDivProductQuantityWiseSalesOnMonth");

            productPriceWiseSalesOnMonth(res.productPriceWiseSalesOnMonth.data, "chartDivProductPriceWiseSalesOnMonth");
            // =======================================================================================================================================


            // =======================================================================================================================================
            dailyProfitCenterWiseSalesChart(res.dailyProfitCenterWiseSales.data, "chartDivDailyProfitCenterWiseSales");

            monthlyProfitCenterWiseSalesChart(res.monthlyProfitCenterWiseSales.data, "chartDivMonthlyProfitCenterWiseSales");

            yearlyProfitCenterWiseSalesChart(res.yearlyProfitCenterWiseSales.data, "chartDivYearlyProfitCenterWiseSales");
            // =======================================================================================================================================


            // =======================================================================================================================================
            productQuantityWiseProfitCenterSales(res.productQuantityWiseSalesProfitCenter.data, "chartDivProductQuantityWiseProfitCenterSales");

            productPriceWiseProfitCenterSales(res.productPriceWiseSalesProfitCenter.data, "chartDivProductPriceWiseProfitCenterSales");
            // =======================================================================================================================================


            // =======================================================================================================================================
            kamWiseQuantityProductGroupSales(res.kamWiseQuantityProductGroupSales.data, "chartDivKamWiseQuantityProductGroupSales");

            kamWisePricingProductGroupSales(res.kamWisePriceProductGroupSales.data, "chartDivKamWisePricingProductGroupSales");

            stateWiseQuantityProductGroupSales(res.stateWiseQuantityProductGroupSales.data, "chartDivStateWiseQuantityProductGroupSales");

            stateWisePricingProductGroupSales(res.stateWisePriceProductGroupSales.data, "chartDivStateWisePricingProductGroupSales");
            // =======================================================================================================================================


            // =======================================================================================================================================
            customerWiseReceivables(res.customerWiseReceivables.data, "chartDivCustomerWiseReceivables");

            kamWiseReceivables(res.kamWiseReceivables.data, "chartDivKamWiseReceivables");

            vendorWisePayables(res.vendorWisePayables.data, "chartDivVendorWisePayables");
            // =======================================================================================================================================


            // =======================================================================================================================================
            salesOrderBook(res.salesOrderBook.data, "chartDivSalesOrderBook");

            purchaseOrderBook(res.purchaseOrderBook.data, "chartDivPurchaseOrderBook");
            // =======================================================================================================================================


            // =======================================================================================================================================
            salesVsCollection(res.salesVsCollection.data, "chartDivSalesVsCollection");
            // =======================================================================================================================================

        }
    });
</script>

<script>
    $(function() {
        $('input[name="daterange"]').daterangepicker({
            opens: 'left'
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('DD-MM-YYYY') + ' to ' + end.format('DD-MM-YYYY'));
        });
    });


    $('#filterDropdown')
        .select2()
        .on('select2:open', () => {
            // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
        });

    // $(".fa").click(function() {
    //     $(".fa").toggleClass("fa-thumbtack");
    // });
</script>