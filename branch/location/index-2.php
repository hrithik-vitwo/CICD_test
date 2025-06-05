<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");

?>

<?php

$invoiceC = "SELECT * FROM `erp_branch_sales_order_invoices` ";
$get = queryGet($invoiceC, true);
$data = $get['data'];
$Pcount = count($data);

$grnC = "SELECT * FROM `erp_grn` WHERE `locationId`=$location_id AND `grnApprovedStatus`='approved' ";
$gets = queryGet($grnC, true);
$datas = $gets['data'];
$Rcount = count($datas);

$customer = "SELECT * FROM `erp_customer` WHERE `location_id`=$location_id";
$getC = queryGet($customer, true);
$dataC = $getC['data'];
$Ccount = count($dataC);


$vendor = "SELECT * FROM `erp_vendor_details` WHERE `company_branch_id`=$branch_id";
$getV = queryGet($vendor, true);
$dataV = $getV['data'];
$Vcount = count($dataV);



?>

<style>
    .align-center-bottom {
        min-height: 500px;
        height: auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .gap-btn {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .align-center-bottom button {
        max-width: 173px;
    }

    .sidenav {
        position: relative;
        right: -1px;
        display: grid;
        top: -27em;
        background: #000;
        padding: 15px;
        border-radius: 7px;
        transition: width 2s;
        transition-timing-function: cubic-bezier(0.1, 0.7, 1.0, 0.1);
        max-width: 170px;
        float: right;
    }

    button#rightFloatBtn {
        position: relative;
        right: 0;
        display: grid;
        top: -30em;
        background: #000;
        padding: 15px;
        border-radius: 7px 0 0 7px;
        transform: translate(-1px, 0px);
    }
</style>


<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://www.amcharts.com/lib/3/amcharts.js?x"></script>
<script src="https://www.amcharts.com/lib/3/serial.js?x"></script>
<script src="https://www.amcharts.com/lib/3/themes/dark.js"></script>
<link rel="stylesheet" href="../../public/assets/index.css">

<style>
    .text-truncate.invoiced i:nth-child(1) {
        color: 007bff !important;
    }
</style>

<link rel="stylesheet" href="../../public/assets/ref-style.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid my-4">
            <div class="align-center-bottom">
                <a type="button" href="../charts.php" class="btn btn-primary gap-btn" target="_blank"><i class="fa fa-plus"></i>Configure Dashboard</a>
            </div>
            <div class="note-text">
                <h6 class="text-xs font-bold">Note</h6>
                <hr>
                <ul class="pl-3">
                    <li style="list-style-type: disc;">
                        <p class="text-xs">You can create your own dashboard as your requirement.</p>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <div id="mySidenav" class="sidenav slide-right" style="display: none;">
        <a href="#">ITEM 1</a>
        <a href="#">ITEM 2</a>
        <a href="#">ITEM 3</a>
        <a href="#">ITEM 4</a>
    </div>

    <button class="btn btn-primary float-right" id="rightFloatBtn"><i class="fa fa-cog fa-2x fa-spin"></i></span>





        <!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->

<?php
require_once("../common/footer.php");
?>

<script src="../../public/assets/apexchart/apexcharts.min.js"></script>
<script src="../../public/assets/apexchart/chart-data.js"></script>


<script>
    $(document).ready(function() {

        function renderFinancialsKeyHighlightsChart() {
            let ctx = document.getElementById("financialsKeyHighlightsChartCanvasId").getContext('2d');
            let myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ["Revenue", "Direct Expenses", "S&D Cost", "Emp Cost", "Admin Cost", "Fin Cost", "Depreciation", "PBT"],
                    datasets: [{
                        label: 'Expense',
                        backgroundColor: "#0071c1",
                        data: [0, 45.45, 9.09, 385.07, 192.51, 2.94, 9.17, 0],
                    }, {
                        label: 'Revenue',
                        backgroundColor: "#92d14f",
                        data: [790.73, 745.28, 736.19, 351.12, 158.61, 155.67, 146.50, 146.50],
                    }],
                },
                options: {
                    tooltips: {
                        displayColors: true,
                        callbacks: {
                            mode: 'x',
                        },
                    },
                    scales: {
                        xAxes: [{
                            stacked: true,
                            gridLines: {
                                display: false,
                            }
                        }],
                        yAxes: [{
                            stacked: true,
                            ticks: {
                                beginAtZero: true,
                            },
                            type: 'linear',
                        }]
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        position: 'bottom'
                    },
                }
            });
        }

        function renderVerticalWiseRevenueBreakUpChart() {
            var ctx = document.getElementById("verticalWiseRevenueBreakUpChartCanvasId");
            var chart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: ["SaaS/PaaS_D", "Cust_D", "Enrollment", "PEC and Kiosk Maint", "HTS AMC", "HTS Hardware", "Consultaion", "SaaS/PaaS_E", "Cust_E"],
                    datasets: [{
                            type: "bar",
                            backgroundColor: "rgba(54, 162, 235, 0.2)",
                            borderColor: "rgba(54, 162, 235, 1)",
                            borderWidth: 1,
                            label: "Revenue",
                            data: [323.18, 131.16, 1.76, 40.05, 14.30, 6.81, 6.00, 241.36, 0]
                        },
                        {
                            type: "line",
                            label: "Revenue",
                            borderColor: "#6610f2",
                            borderWidth: 2,
                            data: [323.18, 131.16, 1.76, 40.05, 14.30, 6.81, 6.00, 241.36, 0],
                            lineTension: 0,
                            fill: false
                        }
                    ]
                }
            });
        }

        function renderSalesPastChart() {
            var ctx = document.getElementById("salesPastChartCanvasId");
            var chart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: ["April", "May", "June", "July", "August", "September"],
                    datasets: [{
                        type: "line",
                        label: "Sales",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 2,
                        data: [36, 127, 37, 70, 36, 37],
                        lineTension: 0,
                        fill: false
                    }]
                }
            });
        }

        function renderAdminExpensesYTDChart() {
            var ctx = document.getElementById("adminExpensesYTDChartCanvasId");
            var chart = new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: ["Prof & Consul", "Office and Estbl", "Travel & Conv", "Repair & Main", "Other Admin"],
                    datasets: [{
                        label: 'My First Dataset',
                        data: [111.82, 20.95, 4.21, 25.05, 30.48],
                        backgroundColor: [
                            `rgb(${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)})`,
                            `rgb(${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)})`,
                            `rgb(${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)})`,
                            `rgb(${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)})`,
                            `rgb(${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)}, ${Math.floor(Math.random() * 256)})`
                        ],
                        hoverOffset: 4
                    }]
                }
            });
        }

        renderFinancialsKeyHighlightsChart();
        renderVerticalWiseRevenueBreakUpChart();
        renderSalesPastChart();
        renderAdminExpensesYTDChart();
    });
</script>

<script>
    Highcharts.chart("pie", {
        chart: {
            type: "pie",
            options3d: {
                enabled: true,
                alpha: 45
            }
        },
        plotOptions: {
            pie: {
                innerSize: 70,
                depth: 65,
                allowPointSelect: true,
                cursor: "pointer",
                dataLabels: {
                    enabled: true,
                    format: "{point.name} ({point.percentage:.1f}%)",
                    connectorWidth: 2
                }
            }
        },
        colors: ["#3b5998", "#22cc62", "#FF0000"],
        series: [{
            name: "Million user",
            data: [
                ["Invoice", 1500],
                ["Recieved", 375],
                {
                    name: "Pending",
                    y: 450,
                    sliced: true,
                    selected: true
                }
            ]
        }],
        tooltip: {
            useHTML: true,
            headerFormat: "<h1>{point.key}</h1>",
            pointFormat: "<h4>{point.percentage:.1f} {series.name} </h4>"
        }
    });
</script>
<!-------bar chart script----------->
<script>
    var chartData = [{
            date: "2012-01-01",
            distance: 227,
            townName: "New York",
            townName2: "New York",
            townSize: 25,
            latitude: 40.71,
            duration: 408
        },
        {
            date: "2012-01-02",
            distance: 371,
            townName: "Washington",
            townSize: 14,
            latitude: 38.89,
            duration: 482
        },
        {
            date: "2012-01-03",
            distance: 433,
            townName: "Wilmington",
            townSize: 6,
            latitude: 34.22,
            duration: 562
        },
        {
            date: "2012-01-04",
            distance: 345,
            townName: "Jacksonville",
            townSize: 7,
            latitude: 30.35,
            duration: 379
        },
        {
            date: "2012-01-05",
            distance: 480,
            townName: "Miami",
            townName2: "Miami",
            townSize: 10,
            latitude: 25.83,
            duration: 501
        },
        {
            date: "2012-01-06",
            distance: 386,
            townName: "Tallahassee",
            townSize: 7,
            latitude: 30.46,
            duration: 443
        },
        {
            date: "2012-01-07",
            distance: 348,
            townName: "New Orleans",
            townSize: 10,
            latitude: 29.94,
            duration: 405
        },
        {
            date: "2012-01-08",
            distance: 238,
            townName: "Houston",
            townName2: "Houston",
            townSize: 16,
            latitude: 29.76,
            duration: 309
        },
        {
            date: "2012-01-09",
            distance: 218,
            townName: "Dalas",
            townSize: 17,
            latitude: 32.8,
            duration: 287
        },
        {
            date: "2012-01-10",
            distance: 349,
            townName: "Oklahoma City",
            townSize: 11,
            latitude: 35.49,
            duration: 485
        },
        {
            date: "2012-01-11",
            distance: 603,
            townName: "Kansas City",
            townSize: 10,
            latitude: 39.1,
            duration: 890
        },
        {
            date: "2012-01-12",
            distance: 534,
            townName: "Denver",
            townName2: "Denver",
            townSize: 18,
            latitude: 39.74,
            duration: 810
        },
        {
            date: "2012-01-13",
            townName: "Salt Lake City",
            townSize: 12,
            distance: 425,
            duration: 670,
            latitude: 40.75,
            alpha: 0.4
        },
        {
            date: "2012-01-14",
            latitude: 36.1,
            duration: 470,
            townName: "Las Vegas",
            townName2: "Las Vegas",
            bulletClass: "lastBullet"
        },
        {
            date: "2012-01-15"
        },
        {
            date: "2012-01-16"
        },
        {
            date: "2012-01-17"
        },
        {
            date: "2012-01-18"
        },
        {
            date: "2012-01-19"
        }
    ];
    var chart = AmCharts.makeChart("chartdiv", {
        type: "serial",
        theme: "dark",
        dataDateFormat: "YYYY-MM-DD",
        dataProvider: chartData,

        addClassNames: true,
        startDuration: 1,
        color: "#000",
        marginLeft: 0,

        categoryField: "date",
        categoryAxis: {
            parseDates: true,
            minPeriod: "DD",
            autoGridCount: false,
            gridCount: 50,
            gridAlpha: 0.1,
            gridColor: "#FFFFFF",
            axisColor: "#555555",
            dateFormats: [{
                    period: "DD",
                    format: "DD"
                },
                {
                    period: "WW",
                    format: "MMM DD"
                },
                {
                    period: "MM",
                    format: "MMM"
                },
                {
                    period: "YYYY",
                    format: "YYYY"
                }
            ]
        },

        valueAxes: [{
                id: "a1",
                title: "distance",
                gridAlpha: 0,
                axisAlpha: 0
            },
            {
                id: "a2",
                position: "right",
                gridAlpha: 0,
                axisAlpha: 0,
                labelsEnabled: false
            },
            {
                id: "a3",
                title: "duration",
                position: "right",
                gridAlpha: 0,
                axisAlpha: 0,
                inside: true,
                duration: "mm",
                durationUnits: {
                    DD: "d. ",
                    hh: "h ",
                    mm: "min",
                    ss: ""
                }
            }
        ],
        graphs: [{
                id: "g1",
                valueField: "distance",
                title: "distance",
                type: "column",
                fillAlphas: 0.9,
                valueAxis: "a1",
                balloonText: "[[value]] miles",
                legendValueText: "[[value]] mi",
                legendPeriodValueText: "total: [[value.sum]] mi",
                lineColor: "#263138",
                alphaField: "alpha"
            },
            {
                id: "g2",
                valueField: "latitude",
                classNameField: "bulletClass",
                title: "latitude/city",
                type: "line",
                valueAxis: "a2",
                lineColor: "#786c56",
                lineThickness: 1,
                legendValueText: "[[description]]/[[value]]",
                descriptionField: "townName",
                bullet: "round",
                bulletSizeField: "townSize",
                bulletBorderColor: "#786c56",
                bulletBorderAlpha: 1,
                bulletBorderThickness: 2,
                bulletColor: "#000000",
                labelText: "[[townName2]]",
                labelPosition: "right",
                balloonText: "latitude:[[value]]",
                showBalloon: true,
                animationPlayed: true
            },
            {
                id: "g3",
                title: "duration",
                valueField: "duration",
                type: "line",
                valueAxis: "a3",
                lineColor: "#ff5755",
                balloonText: "[[value]]",
                lineThickness: 1,
                legendValueText: "[[value]]",
                bullet: "square",
                bulletBorderColor: "#ff5755",
                bulletBorderThickness: 1,
                bulletBorderAlpha: 1,
                dashLengthField: "dashLength",
                animationPlayed: true
            }
        ],

        chartCursor: {
            zoomable: false,
            categoryBalloonDateFormat: "DD",
            cursorAlpha: 0,
            valueBalloonsEnabled: false
        },
        legend: {
            bulletType: "round",
            equalWidths: false,
            valueWidth: 120,
            useGraphSettings: true,
            color: "#000"
        }
    });
</script>
<script>
    $(document).ready(function() {
        $("#rightFloatBtn").click(function() {
            $("#mySidenav").toggle(200);
        });
    });
</script>