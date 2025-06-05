<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("controller/gstr1-view-data.controller.php");
require_once("controller/gstr1-json-data.controller.php");
// administratorAuth();
?>
<style>
    .filter-list a {
        background: #fff;
        box-shadow: 1px 2px 5px -1px #8e8e8e;
    }

    .filter-list {
        margin-bottom: 2em;
    }

    li.nav-item.complince a {
        background: #fff;
        color: #003060;
        z-index: 9;
        margin-bottom: 1em;
    }
</style>
<link rel="stylesheet" href="../../public/assets/listing.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid my-4">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <table class="table defaultDataTable table-hover">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Month</th>
                                <th>Year</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Jan</td>
                                <td>2022</td>
                                <td>Filed</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->
<?php
require_once("../common/footer.php");
?>
<script src="../../public/assets/apexchart/apexcharts.min.js"></script>
<script src="../../public/assets/apexchart/chart-data.js"></script>
<script src="../../public/assets/piechart/piecore.js"></script>
<script src="https://amcharts.com/lib/4/charts.js"></script>
<script src="https://amcharts.com/lib/4/themes/animated.js"></script>
<script src="../../public/assets/apexchart/apexcharts.min.js"></script>
<script src="../../public/assets/apexchart/chart-data.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://amcharts.com/lib/3/amcharts.js?x"></script>
<script src="https://amcharts.com/lib/3/serial.js?x"></script>
<script src="https://amcharts.com/lib/3/themes/dark.js"></script>
<script>
    $(document).ready(function() {
        console.log("Document loaded");
    });
</script>