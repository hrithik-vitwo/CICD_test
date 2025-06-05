<?php
include("../app/v1/connection-customer-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header mb-2 p-0  border-bottom">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= CUSTOMER_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Dashboard</a></li>
            </ol>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">


            <div class="row">
                <div class="card card-outline card-primary w-100">
                    <div class="card-header text-start">Financials – Key Highlights</div>
                    <div class="card-body">
                        <canvas id="financialsKeyHighlightsChartCanvasId" style="min-height: 350px;"></canvas>
                    </div>
                    <!-- /.card-body -->
                </div>
                <div class="card card-outline card-primary w-100">
                    <div class="card-header text-start">Vertical wise Revenue Break-up</div>
                    <div class="card-body">
                        <canvas id="verticalWiseRevenueBreakUpChartCanvasId" style="min-height: 350px;"></canvas>
                    </div>
                    <!-- /.card-body -->
                </div>

                <div class="col-md-6">
                    <div class="card card-outline card-primary w-100">
                        <div class="card-header text-start">Sales -Past 6 Month</div>
                        <div class="card-body">
                            <canvas id="salesPastChartCanvasId" style="min-height: 350px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-outline card-primary w-100">
                        <div class="card-header text-start">Admin Expenses- YTD Feb 22</div>
                        <div class="card-body">
                            <canvas id="adminExpensesYTDChartCanvasId" style="min-height: 350px;"></canvas>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->

<?php
include("common/footer.php");
?>


<script>
    $(document).ready(function() {

        function renderFinancialsKeyHighlightsChart() {
            let ctx = document.getElementById("financialsKeyHighlightsChartCanvasId").getContext('2d');
            let myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ["<  1", "1 - 2", "3 - 4", "5 - 9", "10 - 14", "15 - 19", "20 - 24", "25 - 29", "> - 29"],
                    datasets: [{
                        label: 'Expense',
                        backgroundColor: "#0071c1",
                        data: [12, 59, 5, 56, 58, 12, 59, 87, 45],
                    }, {
                        label: 'Revenue',
                        backgroundColor: "#92d14f",
                        data: [12, 59, 5, 56, 58, 12, 59, 85, 23],
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
                    labels: ["2020/02/17", "", "2020/02/23", "", "2020/02/29", ""],
                    datasets: [{
                            type: "bar",
                            backgroundColor: "rgba(54, 162, 235, 0.2)",
                            borderColor: "rgba(54, 162, 235, 1)",
                            borderWidth: 1,
                            label: "Bar",
                            data: [60, 49, 72, 90, 100, 60]
                        },
                        {
                            type: "line",
                            label: "line",
                            borderColor: "rgba(54, 162, 235, 1)",
                            borderWidth: 2,
                            data: [25, 13, 30, 35, 25, 40],
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
                    labels: ["2020/02/17", "2020/02/20", "2020/02/23", "2020/02/25", "2020/02/29", "2020/02/35"],
                    datasets: [{
                        type: "line",
                        label: "line",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 2,
                        data: [25, 13, 30, 35, 25, 40],
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
                    labels: ["202", "203", "204"],
                    datasets: [{
                        label: 'My First Dataset',
                        data: [300, 50, 100],
                        backgroundColor: [
                            'rgb(255, 99, 132)',
                            'rgb(54, 162, 235)',
                            'rgb(255, 205, 86)'
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