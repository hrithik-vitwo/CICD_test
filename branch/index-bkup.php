<?php
require_once("../app/v1/connection-branch-admin.php");
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar.php");
administratorAuth();

?>
<?php

$invoiceC = "SELECT * FROM `erp_branch_sales_order_invoices` ";
$get = queryGet($invoiceC, true);
$data = $get['data'];
$Pcount = count($data);

$grnC = "SELECT * FROM `erp_grn` WHERE `branchId`=$branch_id AND `grnApprovedStatus`='approved' ";
$gets = queryGet($grnC, true);
$datas = $gets['data'];
$Rcount = count($datas);

$customer = "SELECT * FROM `erp_customer` WHERE `company_branch_id`=$branch_id";
$getC = queryGet($customer, true);
$dataC = $getC['data'];
$Ccount = count($dataC);


$vendor = "SELECT * FROM `erp_vendor_details` WHERE `company_branch_id`=$branch_id";
$getV = queryGet($vendor, true);
$dataV = $getV['data'];
$Vcount = count($dataV);



?>
<style>
    #profileImage {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #512DA8;
        font-size: 15px;
        font-weight: 600;
        color: #fff;
        text-align: center;
        margin: 10px 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .flex-display {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-direction: row-reverse;
        gap: 8px;
    }

    #pieChartdiv {
        width: 100%;
        height: 98vh;
    }
    #chartdiv {
        width: 100%;
        height: 98vh;
    }
    path.amcharts-axis-grid {
    stroke: #003060;
    stroke-opacity: 1;
    }
</style>

<link rel="stylesheet" href="../public/assets/ref-style.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid my-4">
            <div class="row">
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="dash-widget-header">
                                <span class="dash-widget-icon bg-1">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                                <div class="dash-count">
                                    <div class="dash-title">Accounts Receivables</div>
                                    <div class="dash-counts">
                                        <p><?= $Rcount  ?> </p>
                                    </div>
                                </div>
                            </div>
                            <div class="progress progress-sm mt-3">
                                <div class="progress-bar bg-5" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p class="text-muted mt-3 mb-0"><span class="text-danger me-1"><i class="fas fa-arrow-down me-1"></i>1.15%</span> since last week</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="dash-widget-header">
                                <span class="dash-widget-icon bg-2">
                                    <i class="fas fa-users"></i>
                                </span>
                                <div class="dash-count">
                                    <div class="dash-title">Customers</div>
                                    <div class="dash-counts">
                                        <p></p><?= $Ccount ?>
                                    </div>
                                </div>
                            </div>
                            <div class="progress progress-sm mt-3">
                                <div class="progress-bar bg-6" role="progressbar" style="width: 65%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p class="text-muted mt-3 mb-0"><span class="text-success me-1"><i class="fas fa-arrow-up me-1"></i>2.37%</span> since last week</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="dash-widget-header">
                                <span class="dash-widget-icon bg-3">
                                    <i class="fas fa-file-alt"></i>
                                </span>
                                <div class="dash-count">
                                    <div class="dash-title">Accounts Payable</div>
                                    <div class="dash-counts">
                                        <p><?= $Pcount ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="progress progress-sm mt-3">
                                <div class="progress-bar bg-7" role="progressbar" style="width: 85%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p class="text-muted mt-3 mb-0"><span class="text-success me-1"><i class="fas fa-arrow-up me-1"></i>3.77%</span> since last week</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="dash-widget-header">
                                <span class="dash-widget-icon bg-4">
                                    <i class="far fa-file"></i>
                                </span>
                                <div class="dash-count">
                                    <div class="dash-title">Vendors</div>
                                    <div class="dash-counts">
                                        <p><?= $Vcount ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="progress progress-sm mt-3">
                                <div class="progress-bar bg-8" role="progressbar" style="width: 45%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p class="text-muted mt-3 mb-0"><span class="text-danger me-1"><i class="fas fa-arrow-down me-1"></i>8.68%</span> since last week</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-7 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title">Sales Analytics</h5>
                                <div class="dropdown">
                                    <button class="btn btn-white btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        Monthly
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Weekly</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Monthly</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Yearly</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap">
                                <div class="w-md-100 d-flex align-items-center mb-3 flex-wrap flex-md-nowrap">
                                    <div>
                                        <span>Total Sales</span>
                                        <p class="h3 text-primary me-5">₹1000</p>
                                    </div>
                                    <div>
                                        <span>Receipts</span>
                                        <p class="h3 text-success me-5">₹1000</p>
                                    </div>
                                    <div>
                                        <span>Expenses</span>
                                        <p class="h3 text-danger me-5">₹300</p>
                                    </div>
                                    <div>
                                        <span>Earnings</span>
                                        <p class="h3 text-dark me-5">₹700</p>
                                    </div>
                                </div>
                            </div>
                            <!-- <div id="sales_chart"></div> -->
                            <div id="chartdiv"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title">Invoice Analytics</h5>
                                <div class="dropdown">
                                    <button class="btn btn-white btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        Monthly
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Weekly</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Monthly</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Yearly</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- <div id="invoice_chart"></div> -->

                            <div id="pieChartdiv"></div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title">Accounts Receivable</h5>
                                </div>
                                <div class="col-auto">
                                    <a href="invoices.html" class="btn-right btn btn-sm btn-outline-primary">
                                        View All
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="overflow-x: auto;">
                            <div class="mb-3">
                                <div class="progress progress-md rounded-pill mb-3">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 47%" aria-valuenow="47" aria-valuemin="0" aria-valuemax="100"></div>
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 28%" aria-valuenow="28" aria-valuemin="0" aria-valuemax="100"></div>
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="row">
                                    <div class="col-auto">
                                        <i class="fas fa-circle text-success me-1"></i> Paid
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-circle text-warning me-1"></i> Unpaid
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-circle text-danger me-1"></i> Overdue
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-circle text-info me-1"></i> Draft
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-stripped table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Customer</th>
                                            <th>Amount</th>
                                            <th>PO Date</th>
                                            <th>Status</th>
                                            <!-- <th class="text-right">Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $invoice = "SELECT * FROM `erp_branch_sales_order_invoices`  ORDER BY `so_invoice_id` DESC LIMIT 5 ";
                                        $get = queryGet($invoice, true);
                                        $datas = $get['data'];
                                        $rand = rand(00, 99);
                                        foreach ($datas as $data) {

                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="flex-display">
                                                        <span id="firstName"><?= $data['customer_name'] ?></span>
                                                        <div id="profileImage"> <?php echo ucfirst(substr($data['customer_name'], 0, 1)) ?></div>
                                                    </div>
                                                </td>
                                                <td><?= $data['all_total_amt'] ?></td>
                                                <td><?= $data['po_date'] ?></td>
                                                <td><span class="badge bg-success-light"><?= $data['status'] ?></span></td>
                                                <!-- <td class="text-right">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="edit-invoice.html"><i class="far fa-edit me-2"></i>Edit</a>
                                                    <a class="dropdown-item" href="view-invoice.html"><i class="far fa-eye me-2"></i>View</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-trash-alt me-2"></i>Delete</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-check-circle me-2"></i>Mark as
                                                        sent</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-paper-plane me-2"></i>Send Invoice</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-copy me-2"></i>Clone Invoice</a>
                                                </div>
                                            </div> -->
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title">Accounts Payable</h5>
                                </div>
                                <div class="col-auto">
                                    <a href="estimates.html" class="btn-right btn btn-sm btn-outline-primary">
                                        View All
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="progress progress-md rounded-pill mb-3">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 39%" aria-valuenow="39" aria-valuemin="0" aria-valuemax="100"></div>
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 35%" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 26%" aria-valuenow="26" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <!-- <div class="row">
                                <div class="col-auto">
                                    <i class="fas fa-circle text-success me-1"></i> Sent
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-circle text-warning me-1"></i> Draft
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-circle text-danger me-1"></i> Expired
                                </div>
                            </div> -->
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Vendor</th>
                                            <th>Due Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <!-- <th class="text-right">Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $grn = "SELECT * FROM `erp_grn` WHERE `branchId`=$branch_id AND `grnApprovedStatus`='approved' ORDER BY `grnId` DESC LIMIT 5 ";
                                        $get = queryGet($grn, true);
                                        $datas = $get['data'];
                                        //  console($datas);
                                        foreach ($datas as $data) {
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="flex-display">
                                                        <span id="firstName"><?= $data['vendorName'] ?></span>
                                                        <div id="profileImage"> <?php echo ucfirst(substr($data['vendorName'], 0, 1)) ?></div>
                                                    </div>

                                                    <!-- <h2 class="table-avatar">
                                                        <a href="profile.html"><img class="avatar avatar-sm me-2 avatar-img rounded-circle" src="../public/assets/img/profiles/avatar-05.jpg" alt="User Image"> <?= $data['vendorName'] ?> </a>
                                                    </h2> -->

                                                </td>
                                                <td><?= $data['dueDate'] ?></td>
                                                <td><?= $data['grnTotalAmount'] ?></td>
                                                <td><span class="badge bg-info-light"><?= $data['grnApprovedStatus'] ?> </span></td>
                                                <!-- <td class="text-right">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="edit-invoice.html"><i class="far fa-edit me-2"></i>Edit</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-trash-alt me-2"></i>Delete</a>
                                                    <a class="dropdown-item" href="view-estimate.html"><i class="far fa-eye me-2"></i>View</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-file-alt me-2"></i>Convert to
                                                        Invoice</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-check-circle me-2"></i>Mark as
                                                        sent</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-paper-plane me-2"></i>Send
                                                        Estimate</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-check-circle me-2"></i>Mark as
                                                        Accepted</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-times-circle me-2"></i>Mark as
                                                        Rejected</a>
                                                </div>
                                            </div>
                                        </td> -->
                                            </tr>

                                        <?php
                                        }
                                        ?>


                                        <!-- <tr>
                                        <td>
                                            <h2 class="table-avatar">
                                                <a href="profile.html"><img class="avatar avatar-sm me-2 avatar-img rounded-circle" src="../public/assets/img/profiles/avatar-09.jpg" alt="User Image"> Leatha Bailey</a>
                                            </h2>
                                        </td>
                                        <td>30 Sep 2020</td>
                                        <td>$785</td>
                                        <td><span class="badge bg-success-light">Accepted</span></td>
                                        <td class="text-right">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="edit-invoice.html"><i class="far fa-edit me-2"></i>Edit</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-trash-alt me-2"></i>Delete</a>
                                                    <a class="dropdown-item" href="view-estimate.html"><i class="far fa-eye me-2"></i>View</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-file-alt me-2"></i>Convert to
                                                        Invoice</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-check-circle me-2"></i>Mark as
                                                        sent</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-paper-plane me-2"></i>Send
                                                        Estimate</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-check-circle me-2"></i>Mark as
                                                        Accepted</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i class="far fa-times-circle me-2"></i>Mark as
                                                        Rejected</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr> -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->

<?php
require_once("common/footer.php");
?>

<script src="../public/assets/apexchart/apexcharts.min.js"></script>
<script src="../public/assets/apexchart/chart-data.js"></script>


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
<script src="../public/assets/piechart/piecore.js"></script>
<script src="//www.amcharts.com/lib/4/charts.js"></script>
<script src="//www.amcharts.com/lib/4/themes/animated.js"></script>
<script src="../public/assets/apexchart/apexcharts.min.js"></script>
<script src="../public/assets/apexchart/chart-data.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://www.amcharts.com/lib/3/amcharts.js?x"></script>
<script src="https://www.amcharts.com/lib/3/serial.js?x"></script>
<script src="https://www.amcharts.com/lib/3/themes/dark.js"></script>


<script>
    am4core.useTheme(am4themes_animated);

    var chart = am4core.create("pieChartdiv", am4charts.PieChart3D);
    chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

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

    chart.innerRadius = am4core.percent(40);

    var series = chart.series.push(new am4charts.PieSeries3D());
    series.dataFields.value = "litres";
    series.dataFields.category = "country";
    
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
        hideCredits: true,

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