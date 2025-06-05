<?php

    require_once("../app/v1/connection-branch-admin.php");

    require_once("common/header.php");

    require_once("common/navbar.php");

    require_once("common/sidebar.php");

    require_once("common/footer.php");

    administratorAuth();

?>

<link rel="stylesheet" href="../public/assets/ref-style.css">

<!-- Styles -->
<style>
    .chartContainer {
        width: 100%;
        height: 500px;
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
                <!-- +++++++++++++++++++++++++++++ CHART - 1 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">3D Cylinder Chart [chart-1]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivCylinder" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 1 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 2 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Pareto Diagram [chart-2]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivPareto" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 2 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 3 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Waterfall Chart [chart-3]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivWaterfall" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 3 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 4 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Clustered Column Chart [chart-4]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivClusteredColumn" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 4 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 5 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Column and Line Mix [chart-5]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivColumnLineMix" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 5 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 6 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Layered Column Chart [chart-6]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivLayeredColumn" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 6 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 7 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Column Chart with Images on top [chart-7]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivColumnImageTop" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 7 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 8 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Real Time Data Sorting [chart-8]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivRealTimeDataSort" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 8 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 9 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Duration on Value Axis [chart-9]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivDurationValueAxis" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 9 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 10 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Date Based Data [chart-10]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivDateBasedData" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 10 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 11 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Range Chart [chart-11]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivRangeChart" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 11 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 12 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Area With Time Based Data [chart-12]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivAreaTimeBasedData" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 12 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 13 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Two-level Pie Chart [chart-13]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivTwoLevelPieChart" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 13 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 14 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">3D Donut Chart [chart-14]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivDonutChart" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 14 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 15 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Variable-height 3D Pie Chart [chart-15]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivVariableHeightPieChart" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 15 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 16 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Variable-radius nested donut chart [chart-16]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivVariableRadiusDonutChart" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 16 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 17 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Zoomable Bubble Chart [chart-17]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivZoomableBubbleChart" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 17 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 18 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Animated gauge [chart-18]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivAnimatedGauge" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 18 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 19 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Gauge with bands [chart-19]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivBandGauge" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 19 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 20 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Gauge with gradient fill [chart-20]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivGradientGauge" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 20 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 21 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Radar Heat Map [chart-21]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivRadarHeatMap" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 21 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 22 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Sankey Diagram [chart-22]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivSankeyDiagram" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 22 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 23 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Funnel with Gradient Fill [chart-23]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivGradientFunnel" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 23 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 24 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Multi-series Funnel/Pyramid [chart-24]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivMultiSeriesFunnel" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 24 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 25 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Vertical Sankey Diagram [chart-25]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivVerticalSankeyDiagram" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 25 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 26 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Vertically stacked axes chart [chart-26]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivVerticallyStackedAxesChart" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 26 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 27 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Multiple Value Axes [chart-27]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivMultipleValueAxes" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 27 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 28 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Collapsible force-directed tree [chart-28]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivCollapsibleForceDirectedTree" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 28 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 29 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Variance indicators [chart-29]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivVarianceIndicators" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 29 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 30 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Hybrid drill-down Pie/Bar chart [chart-30]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivHybridDrillDownPieBarChart" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 30 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 31 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Using SVG Filters [chart-31]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivSVGFilters" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 31 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 32 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Cylinder gauge [chart-32]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivCylinderGauge" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 32 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 33 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Pie Charts as Bullets [chart-33]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivPieChartAsBullet" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 33 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 34 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Stacked bar chart with negative values [chart-34]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivStackedBarChartWithNegativeValues" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 34 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 35 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">3D Stacked Column Chart [chart-35]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDiv3DStackedColumnChart" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 35 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 36 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">100% Stacked Column Chart [chart-36]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDiv100PercentStackedColumnChart" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 36 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 37 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Stacked waterfall chart [chart-37]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivStackedWaterfallChart" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 37 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 38 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Divergent stacked bars [chart-38]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivDivergentStackedBars" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 38 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 39 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Partitioned bar chart [chart-39]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivPartitionedBarChart" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 39 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 40 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Combined bullet/column and line graphs with multiple value axes [chart-40]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivCombinedColumnAndLineChart" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 40 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 41 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">3D Pie Chart [chart-41]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDiv3DPieChart" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 41 ============================= -->
                
                <!-- +++++++++++++++++++++++++++++ CHART - 42 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Angular Gauge With Two Axes [chart-42]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivAngularGaugeTwoAxes" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 42 ============================= -->
            </div>

            <div class="row">
                <!-- +++++++++++++++++++++++++++++ CHART - 43 +++++++++++++++++++++++++++++ -->
                <div class="col-md-6 col-sm-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <h5 class="card-title">Sunburst chart [chart-43]</h5>
                        </div>
                        <div class="card-body">
                            <div id="chartDivSunburstChart" class="chartContainer"></div>
                        </div>
                    </div>
                </div>
                <!-- ============================= CHART - 43 ============================= -->
            </div>
        </div>
    </section>
</div>

<!-- Chart code -->
<script>

    // ====================================== 3D Cylinder Chart ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivCylinder", am4charts.XYChart3D);
        chart.paddingBottom = 30;
        chart.angle = 35;
        chart.logo.disabled = true;

        // Add data
        chart.data = [
            {
                "country": "USA",
                "visits": 4025
            }, 
            {
                "country": "China",
                "visits": 1882
            },
            {
                "country": "Japan",
                "visits": 1809
            }, 
            {
                "country": "Germany",
                "visits": 1322
            }, 
            {
                "country": "UK",
                "visits": 1122
            }, 
            {
                "country": "France",
                "visits": 1114
            }, 
            {
                "country": "India",
                "visits": 984
            }, 
            {
                "country": "Spain",
                "visits": 711
            }, 
            {
                "country": "Netherlands",
                "visits": 665
            }, 
            {
                "country": "Russia",
                "visits": 580
            }, 
            {
                "country": "South Korea",
                "visits": 443
            }, 
            {
                "country": "Canada",
                "visits": 441
            }, 
            {
                "country": "Brazil",
                "visits": 395
            }, 
            {
                "country": "Italy",
                "visits": 386
            }, 
            {
                "country": "Taiwan",
                "visits": 338
            }
        ];

        // Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "country";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.minGridDistance = 20;
        categoryAxis.renderer.inside = true;
        categoryAxis.renderer.grid.template.disabled = true;

        let labelTemplate = categoryAxis.renderer.labels.template;
        labelTemplate.rotation = -90;
        labelTemplate.horizontalCenter = "left";
        labelTemplate.verticalCenter = "middle";
        labelTemplate.dy = 10; // moves it a bit down;
        labelTemplate.inside = false; // this is done to avoid settings which are not suitable when label is rotated

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.renderer.grid.template.disabled = true;

        // Create series
        var series = chart.series.push(new am4charts.ConeSeries());
        series.dataFields.valueY = "visits";
        series.dataFields.categoryX = "country";

        var columnTemplate = series.columns.template;
        columnTemplate.adapter.add("fill", function(fill, target) {
        return chart.colors.getIndex(target.dataItem.index);
        })

        columnTemplate.adapter.add("stroke", function(stroke, target) {
        return chart.colors.getIndex(target.dataItem.index);
        })

    });
    // ++++++++++++++++++++++++++++++++++++++ 3D Cylinder Chart ++++++++++++++++++++++++++++++++++++++

    // ====================================== Pareto Diagram ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivPareto", am4charts.XYChart);
        chart.scrollbarX = new am4core.Scrollbar();
        chart.logo.disabled = true;

        // Add data
        chart.data = [
            {
                "country": "USA",
                "visits": 3025
            }, 
            {
                "country": "China",
                "visits": 1882
            }, 
            {
                "country": "Japan",
                "visits": 1809
            }, 
            {
                "country": "Germany",
                "visits": 1322
            }, 
            {
                "country": "UK",
                "visits": 1122
            }, 
            {
                "country": "France",
                "visits": 1114
            }, 
            {
                "country": "India",
                "visits": 984
            }, 
            {
                "country": "Spain",
                "visits": 711
            }, 
            {
                "country": "Netherlands",
                "visits": 665
            }
        ];

        prepareParetoData();

        function prepareParetoData(){
            var total = 0;

            for(var i = 0; i < chart.data.length; i++){
                var value = chart.data[i].visits;
                total += value;
            }

            var sum = 0;
            for(var i = 0; i < chart.data.length; i++){
                var value = chart.data[i].visits;
                sum += value;   
                chart.data[i].pareto = sum / total * 100;
            }    
        }

        // Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "country";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.minGridDistance = 60;
        categoryAxis.tooltip.disabled = true;

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.renderer.minWidth = 50;
        valueAxis.min = 0;
        valueAxis.cursorTooltipEnabled = false;

        // Create series
        var series = chart.series.push(new am4charts.ColumnSeries());
        series.sequencedInterpolation = true;
        series.dataFields.valueY = "visits";
        series.dataFields.categoryX = "country";
        series.tooltipText = "[{categoryX}: bold]{valueY}[/]";
        series.columns.template.strokeWidth = 0;

        series.tooltip.pointerOrientation = "vertical";

        series.columns.template.column.cornerRadiusTopLeft = 10;
        series.columns.template.column.cornerRadiusTopRight = 10;
        series.columns.template.column.fillOpacity = 0.8;

        // on hover, make corner radiuses bigger
        var hoverState = series.columns.template.column.states.create("hover");
        hoverState.properties.cornerRadiusTopLeft = 0;
        hoverState.properties.cornerRadiusTopRight = 0;
        hoverState.properties.fillOpacity = 1;

        series.columns.template.adapter.add("fill", function(fill, target) {
            return chart.colors.getIndex(target.dataItem.index);
        })


        var paretoValueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        paretoValueAxis.renderer.opposite = true;
        paretoValueAxis.min = 0;
        paretoValueAxis.max = 100;
        paretoValueAxis.strictMinMax = true;
        paretoValueAxis.renderer.grid.template.disabled = true;
        paretoValueAxis.numberFormatter = new am4core.NumberFormatter();
        paretoValueAxis.numberFormatter.numberFormat = "#'%'"
        paretoValueAxis.cursorTooltipEnabled = false;

        var paretoSeries = chart.series.push(new am4charts.LineSeries())
        paretoSeries.dataFields.valueY = "pareto";
        paretoSeries.dataFields.categoryX = "country";
        paretoSeries.yAxis = paretoValueAxis;
        paretoSeries.tooltipText = "pareto: {valueY.formatNumber('#.0')}%[/]";
        paretoSeries.bullets.push(new am4charts.CircleBullet());
        paretoSeries.strokeWidth = 2;
        paretoSeries.stroke = new am4core.InterfaceColorSet().getFor("alternativeBackground");
        paretoSeries.strokeOpacity = 0.5;

        // Cursor
        chart.cursor = new am4charts.XYCursor();
        chart.cursor.behavior = "panX";

    });
    // ++++++++++++++++++++++++++++++++++++++ Pareto Diagram ++++++++++++++++++++++++++++++++++++++

    // ====================================== Waterfall Chart ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivWaterfall", am4charts.XYChart);
        chart.logo.disabled = true;
        chart.hiddenState.properties.opacity = 0; // this makes initial fade in effect

        // using math in the data instead of final values just to illustrate the idea of Waterfall chart
        // a separate data field for step series is added because we don't need last step (notice, the last data item doesn't have stepValue)
        chart.data = [ 
            {
                category: "Net revenue",
                value: 8786,
                open: 0,
                stepValue: 8786,
                color: chart.colors.getIndex( 13 ),
                displayValue: 8786
            },
            {
                category: "Cost of sales",
                value: 8786 - 2786,
                open: 8786,
                stepValue: 8786 - 2786,
                color: chart.colors.getIndex( 8 ),
                displayValue: 2786
            },
            {
                category: "Operating expenses",
                value: 8786 - 2786 - 1786,
                open: 8786 - 2786,
                stepValue: 8786 - 2786 - 1786,
                color: chart.colors.getIndex( 9 ),
                displayValue: 1786
            },
            {
                category: "Amortisation",
                value: 8786 - 2786 - 1786 - 453,
                open: 8786 - 2786 - 1786,
                stepValue: 8786 - 2786 - 1786 - 453,
                color: chart.colors.getIndex( 10 ),
                displayValue: 453
            },
            {
                category: "Income from equity",
                value: 8786 - 2786 - 1786 - 453 + 1465,
                open: 8786 - 2786 - 1786 - 453,
                stepValue: 8786 - 2786 - 1786 - 453 + 1465,
                color: chart.colors.getIndex( 16 ),
                displayValue: 1465
            }, 
            {
                category: "Operating income",
                value: 8786 - 2786 - 1786 - 453 + 1465,
                open: 0,
                color: chart.colors.getIndex( 17 ),
                displayValue: 8786 - 2786 - 1786 - 453 + 1465
            } 
        ];

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
        label.text = "{displayValue.formatNumber('$#,## a')}";
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
    // ++++++++++++++++++++++++++++++++++++++ Waterfall Chart ++++++++++++++++++++++++++++++++++++++

    // ====================================== Clustered Column Chart ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivClusteredColumn", am4charts.XYChart);
        chart.logo.disabled = true;
        chart.colors.step = 2;

        chart.legend = new am4charts.Legend()
        chart.legend.position = 'top'
        chart.legend.paddingBottom = 20
        chart.legend.labels.template.maxWidth = 95

        var xAxis = chart.xAxes.push(new am4charts.CategoryAxis())
        xAxis.dataFields.category = 'category'
        xAxis.renderer.cellStartLocation = 0.1
        xAxis.renderer.cellEndLocation = 0.9
        xAxis.renderer.grid.template.location = 0;

        var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
        yAxis.min = 0;

        function createSeries(value, name) {
            var series = chart.series.push(new am4charts.ColumnSeries())
            series.dataFields.valueY = value
            series.dataFields.categoryX = 'category'
            series.name = name

            series.events.on("hidden", arrangeColumns);
            series.events.on("shown", arrangeColumns);

            var bullet = series.bullets.push(new am4charts.LabelBullet())
            bullet.interactionsEnabled = false
            bullet.dy = 30;
            bullet.label.text = '{valueY}'
            bullet.label.fill = am4core.color('#ffffff')

            return series;
        }

        // Add data
        chart.data = [
            {
                category: 'Place #1',
                first: 40,
                second: 55,
                third: 60
            },
            {
                category: 'Place #2',
                first: 30,
                second: 78,
                third: 69
            },
            {
                category: 'Place #3',
                first: 27,
                second: 40,
                third: 45
            },
            {
                category: 'Place #4',
                first: 50,
                second: 33,
                third: 22
            }
        ]

        createSeries('first', 'The First');
        createSeries('second', 'The Second');
        createSeries('third', 'The Third');

        function arrangeColumns() {

            var series = chart.series.getIndex(0);

            var w = 1 - xAxis.renderer.cellStartLocation - (1 - xAxis.renderer.cellEndLocation);
            if (series.dataItems.length > 1) {
                var x0 = xAxis.getX(series.dataItems.getIndex(0), "categoryX");
                var x1 = xAxis.getX(series.dataItems.getIndex(1), "categoryX");
                var delta = ((x1 - x0) / chart.series.length) * w;
                if (am4core.isNumber(delta)) {
                    var middle = chart.series.length / 2;

                    var newIndex = 0;
                    chart.series.each(function(series) {
                        if (!series.isHidden && !series.isHiding) {
                            series.dummyData = newIndex;
                            newIndex++;
                        }
                        else {
                            series.dummyData = chart.series.indexOf(series);
                        }
                    })
                    var visibleCount = newIndex;
                    var newMiddle = visibleCount / 2;

                    chart.series.each(function(series) {
                        var trueIndex = chart.series.indexOf(series);
                        var newIndex = series.dummyData;

                        var dx = (newIndex - trueIndex + middle - newMiddle) * delta

                        series.animate({ property: "dx", to: dx }, series.interpolationDuration, series.interpolationEasing);
                        series.bulletsContainer.animate({ property: "dx", to: dx }, series.interpolationDuration, series.interpolationEasing);
                    })
                }
            }
        }

    });
    // ++++++++++++++++++++++++++++++++++++++ Clustered Column Chart ++++++++++++++++++++++++++++++++++++++

    // ====================================== Column and Line Mix ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivColumnLineMix", am4charts.XYChart);
        chart.logo.disabled = true;

        // Export
        chart.exporting.menu = new am4core.ExportMenu();

        // Data for both series
        var data = [ 
            {
                "year": "2009",
                "income": 23.5,
                "expenses": 21.1
            },
            {
                "year": "2010",
                "income": 26.2,
                "expenses": 30.5
            },
            {
                "year": "2011",
                "income": 30.1,
                "expenses": 34.9
            },
            {
                "year": "2012",
                "income": 29.5,
                "expenses": 31.1
            },
            {
                "year": "2013",
                "income": 30.6,
                "expenses": 28.2,
                "lineDash": "5,5",
            },
            {
                "year": "2014",
                "income": 34.1,
                "expenses": 32.9,
                "strokeWidth": 1,
                "columnDash": "5,5",
                "fillOpacity": 0.2,
                "additional": "(projection)"
            } 
        ];

        /* Create axes */
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "year";
        categoryAxis.renderer.minGridDistance = 30;

        /* Create value axis */
        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

        /* Create series */
        var columnSeries = chart.series.push(new am4charts.ColumnSeries());
        columnSeries.name = "Income";
        columnSeries.dataFields.valueY = "income";
        columnSeries.dataFields.categoryX = "year";

        columnSeries.columns.template.tooltipText = "[#fff font-size: 15px]{name} in {categoryX}:\n[/][#fff font-size: 20px]{valueY}[/] [#fff]{additional}[/]"
        columnSeries.columns.template.propertyFields.fillOpacity = "fillOpacity";
        columnSeries.columns.template.propertyFields.stroke = "stroke";
        columnSeries.columns.template.propertyFields.strokeWidth = "strokeWidth";
        columnSeries.columns.template.propertyFields.strokeDasharray = "columnDash";
        columnSeries.tooltip.label.textAlign = "middle";

        var lineSeries = chart.series.push(new am4charts.LineSeries());
        lineSeries.name = "Expenses";
        lineSeries.dataFields.valueY = "expenses";
        lineSeries.dataFields.categoryX = "year";

        lineSeries.stroke = am4core.color("#fdd400");
        lineSeries.strokeWidth = 3;
        lineSeries.propertyFields.strokeDasharray = "lineDash";
        lineSeries.tooltip.label.textAlign = "middle";

        var bullet = lineSeries.bullets.push(new am4charts.Bullet());
        bullet.fill = am4core.color("#fdd400"); // tooltips grab fill from parent by default
        bullet.tooltipText = "[#fff font-size: 15px]{name} in {categoryX}:\n[/][#fff font-size: 20px]{valueY}[/] [#fff]{additional}[/]"
        var circle = bullet.createChild(am4core.Circle);
        circle.radius = 4;
        circle.fill = am4core.color("#fff");
        circle.strokeWidth = 3;

        chart.data = data;

    });
    // ++++++++++++++++++++++++++++++++++++++ Column and Line Mix ++++++++++++++++++++++++++++++++++++++
    
    // ====================================== Layered Column Chart ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivLayeredColumn", am4charts.XYChart);
        chart.logo.disabled = true;

        // Add percent sign to all numbers
        chart.numberFormatter.numberFormat = "#.#'%'";

        // Add data
        chart.data = [
            {
                "country": "USA",
                "year2004": 3.5,
                "year2005": 4.2
            }, 
            {
                "country": "UK",
                "year2004": 1.7,
                "year2005": 3.1
            }, 
            {
                "country": "Canada",
                "year2004": 2.8,
                "year2005": 2.9
            }, 
            {
                "country": "Japan",
                "year2004": 2.6,
                "year2005": 2.3
            }, 
            {
                "country": "France",
                "year2004": 1.4,
                "year2005": 2.1
            }, 
            {
                "country": "Brazil",
                "year2004": 2.6,
                "year2005": 4.9
            }
        ];

        // Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "country";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.minGridDistance = 30;

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.title.text = "GDP growth rate";
        valueAxis.title.fontWeight = 800;

        // Create series
        var series = chart.series.push(new am4charts.ColumnSeries());
        series.dataFields.valueY = "year2004";
        series.dataFields.categoryX = "country";
        series.clustered = false;
        series.tooltipText = "GDP grow in {categoryX} (2004): [bold]{valueY}[/]";

        var series2 = chart.series.push(new am4charts.ColumnSeries());
        series2.dataFields.valueY = "year2005";
        series2.dataFields.categoryX = "country";
        series2.clustered = false;
        series2.columns.template.width = am4core.percent(50);
        series2.tooltipText = "GDP grow in {categoryX} (2005): [bold]{valueY}[/]";

        chart.cursor = new am4charts.XYCursor();
        chart.cursor.lineX.disabled = true;
        chart.cursor.lineY.disabled = true;

    });
    // ++++++++++++++++++++++++++++++++++++++ Layered Column Chart ++++++++++++++++++++++++++++++++++++++
    
    // ====================================== Column Chart with Images on top ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivColumnImageTop", am4charts.XYChart);
        chart.logo.disabled = true;

        // Add data
        chart.data = [
            {
                "name": "John",
                "points": 35654,
                "color": chart.colors.next(),
                "bullet": "https://www.amcharts.com/lib/images/faces/A04.png"
            }, 
            {
                "name": "Damon",
                "points": 65456,
                "color": chart.colors.next(),
                "bullet": "https://www.amcharts.com/lib/images/faces/C02.png"
            }, 
            {
                "name": "Patrick",
                "points": 45724,
                "color": chart.colors.next(),
                "bullet": "https://www.amcharts.com/lib/images/faces/D02.png"
            }, 
            {
                "name": "Mark",
                "points": 13654,
                "color": chart.colors.next(),
                "bullet": "https://www.amcharts.com/lib/images/faces/E01.png"
            }
        ];

        // Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "name";
        categoryAxis.renderer.grid.template.disabled = true;
        categoryAxis.renderer.minGridDistance = 30;
        categoryAxis.renderer.inside = true;
        categoryAxis.renderer.labels.template.fill = am4core.color("#fff");
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
        series.dataFields.valueY = "points";
        series.dataFields.categoryX = "name";
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
    // ++++++++++++++++++++++++++++++++++++++ Column Chart with Images on top ++++++++++++++++++++++++++++++++++++++

    // ====================================== Real Time Data Sorting ======================================
    am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        var chart = am4core.create("chartDivRealTimeDataSort", am4charts.XYChart);
        chart.logo.disabled = true;

        chart.data = [
            {
            "country": "USA",
            "visits": 2025
            }, 
            {
            "country": "China",
            "visits": 1882
            }, 
            {
            "country": "Japan",
            "visits": 1809
            }, 
            {
            "country": "Germany",
            "visits": 1322
            }, 
            {
            "country": "UK",
            "visits": 1122
            }, 
            {
            "country": "France",
            "visits": 1114
            }, 
            {
            "country": "India",
            "visits": 984
            }, 
            {
            "country": "Spain",
            "visits": 711
            }, 
            {
            "country": "Netherlands",
            "visits": 665
            }, 
            {
            "country": "Russia",
            "visits": 580
            }, 
            {
            "country": "South Korea",
            "visits": 443
            }, 
            {
            "country": "Canada",
            "visits": 441
            }
        ];

        chart.padding(40, 40, 40, 40);

        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.dataFields.category = "country";
        categoryAxis.renderer.minGridDistance = 60;
        categoryAxis.renderer.inversed = true;
        categoryAxis.renderer.grid.template.disabled = true;

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.min = 0;
        valueAxis.extraMax = 0.1;
        //valueAxis.rangeChangeEasing = am4core.ease.linear;
        //valueAxis.rangeChangeDuration = 1500;

        var series = chart.series.push(new am4charts.ColumnSeries());
        series.dataFields.categoryX = "country";
        series.dataFields.valueY = "visits";
        series.tooltipText = "{valueY.value}"
        series.columns.template.strokeOpacity = 0;
        series.columns.template.column.cornerRadiusTopRight = 10;
        series.columns.template.column.cornerRadiusTopLeft = 10;
        //series.interpolationDuration = 1500;
        //series.interpolationEasing = am4core.ease.linear;
        var labelBullet = series.bullets.push(new am4charts.LabelBullet());
        labelBullet.label.verticalCenter = "bottom";
        labelBullet.label.dy = -10;
        labelBullet.label.text = "{values.valueY.workingValue.formatNumber('#.')}";

        chart.zoomOutButton.disabled = true;

        // as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
        series.columns.template.adapter.add("fill", function (fill, target) {
        return chart.colors.getIndex(target.dataItem.index);
        });

        setInterval(function () {
            am4core.array.each(chart.data, function (item) {
            item.visits += Math.round(Math.random() * 200 - 100);
            item.visits = Math.abs(item.visits);
            })
            chart.invalidateRawData();
        }, 2000)

        categoryAxis.sortBySeries = series;

    });
    // ++++++++++++++++++++++++++++++++++++++ Real Time Data Sorting ++++++++++++++++++++++++++++++++++++++
    
    // ====================================== Duration on Value Axis ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivDurationValueAxis", am4charts.XYChart);
        chart.logo.disabled = true;

        chart.colors.step = 2;
        chart.maskBullets = false;

        // Add data
        chart.data = [
            {
                "date": "2012-01-01",
                "distance": 227,
                "townName": "New York",
                "townName2": "New York",
                "townSize": 12,
                "latitude": 40.71,
                "duration": 408
            }, {
                "date": "2012-01-02",
                "distance": 371,
                "townName": "Washington",
                "townSize": 7,
                "latitude": 38.89,
                "duration": 482
            }, {
                "date": "2012-01-03",
                "distance": 433,
                "townName": "Wilmington",
                "townSize": 3,
                "latitude": 34.22,
                "duration": 562
            }, {
                "date": "2012-01-04",
                "distance": 345,
                "townName": "Jacksonville",
                "townSize": 3.5,
                "latitude": 30.35,
                "duration": 379
            }, {
                "date": "2012-01-05",
                "distance": 480,
                "townName": "Miami",
                "townName2": "Miami",
                "townSize": 5,
                "latitude": 25.83,
                "duration": 501
            }, {
                "date": "2012-01-06",
                "distance": 386,
                "townName": "Tallahassee",
                "townSize": 3.5,
                "latitude": 30.46,
                "duration": 443
            }, {
                "date": "2012-01-07",
                "distance": 348,
                "townName": "New Orleans",
                "townSize": 5,
                "latitude": 29.94,
                "duration": 405
            }, {
                "date": "2012-01-08",
                "distance": 238,
                "townName": "Houston",
                "townName2": "Houston",
                "townSize": 8,
                "latitude": 29.76,
                "duration": 309
            }, {
                "date": "2012-01-09",
                "distance": 218,
                "townName": "Dalas",
                "townSize": 8,
                "latitude": 32.8,
                "duration": 287
            }, {
                "date": "2012-01-10",
                "distance": 349,
                "townName": "Oklahoma City",
                "townSize": 5,
                "latitude": 35.49,
                "duration": 485
            }, {
                "date": "2012-01-11",
                "distance": 603,
                "townName": "Kansas City",
                "townSize": 5,
                "latitude": 39.1,
                "duration": 890
            }, {
                "date": "2012-01-12",
                "distance": 534,
                "townName": "Denver",
                "townName2": "Denver",
                "townSize": 9,
                "latitude": 39.74,
                "duration": 810
            }, {
                "date": "2012-01-13",
                "townName": "Salt Lake City",
                "townSize": 6,
                "distance": 425,
                "duration": 670,
                "latitude": 40.75,
                "dashLength": 8,
                "alpha": 0.4
            }, {
                "date": "2012-01-14",
                "latitude": 36.1,
                "duration": 470,
                "townName": "Las Vegas",
                "townName2": "Las Vegas"
            }, {
                "date": "2012-01-15"
            }, {
                "date": "2012-01-16"
            }, {
                "date": "2012-01-17"
            }
        ];

        // Create axes
        var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        dateAxis.renderer.grid.template.location = 0;
        dateAxis.renderer.minGridDistance = 50;
        dateAxis.renderer.grid.template.disabled = true;
        dateAxis.renderer.fullWidthTooltip = true;

        var distanceAxis = chart.yAxes.push(new am4charts.ValueAxis());
        distanceAxis.title.text = "Distance";
        //distanceAxis.renderer.grid.template.disabled = true;

        var durationAxis = chart.yAxes.push(new am4charts.DurationAxis());
        durationAxis.title.text = "Duration";
        durationAxis.baseUnit = "minute";
        //durationAxis.renderer.grid.template.disabled = true;
        durationAxis.renderer.opposite = true;
        durationAxis.syncWithAxis = distanceAxis;

        durationAxis.durationFormatter.durationFormat = "hh'h' mm'min'";

        var latitudeAxis = chart.yAxes.push(new am4charts.ValueAxis());
        latitudeAxis.renderer.grid.template.disabled = true;
        latitudeAxis.renderer.labels.template.disabled = true;
        latitudeAxis.syncWithAxis = distanceAxis;

        // Create series
        var distanceSeries = chart.series.push(new am4charts.ColumnSeries());
        distanceSeries.dataFields.valueY = "distance";
        distanceSeries.dataFields.dateX = "date";
        distanceSeries.yAxis = distanceAxis;
        distanceSeries.tooltipText = "{valueY} miles";
        distanceSeries.name = "Distance";
        distanceSeries.columns.template.fillOpacity = 0.7;
        distanceSeries.columns.template.propertyFields.strokeDasharray = "dashLength";
        distanceSeries.columns.template.propertyFields.fillOpacity = "alpha";
        distanceSeries.showOnInit = true;

        var distanceState = distanceSeries.columns.template.states.create("hover");
        distanceState.properties.fillOpacity = 0.9;

        var durationSeries = chart.series.push(new am4charts.LineSeries());
        durationSeries.dataFields.valueY = "duration";
        durationSeries.dataFields.dateX = "date";
        durationSeries.yAxis = durationAxis;
        durationSeries.name = "Duration";
        durationSeries.strokeWidth = 2;
        durationSeries.propertyFields.strokeDasharray = "dashLength";
        durationSeries.tooltipText = "{valueY.formatDuration()}";
        durationSeries.showOnInit = true;

        var durationBullet = durationSeries.bullets.push(new am4charts.Bullet());
        var durationRectangle = durationBullet.createChild(am4core.Rectangle);
        durationBullet.horizontalCenter = "middle";
        durationBullet.verticalCenter = "middle";
        durationBullet.width = 7;
        durationBullet.height = 7;
        durationRectangle.width = 7;
        durationRectangle.height = 7;

        var durationState = durationBullet.states.create("hover");
        durationState.properties.scale = 1.2;

        var latitudeSeries = chart.series.push(new am4charts.LineSeries());
        latitudeSeries.dataFields.valueY = "latitude";
        latitudeSeries.dataFields.dateX = "date";
        latitudeSeries.yAxis = latitudeAxis;
        latitudeSeries.name = "Duration";
        latitudeSeries.strokeWidth = 2;
        latitudeSeries.propertyFields.strokeDasharray = "dashLength";
        latitudeSeries.tooltipText = "Latitude: {valueY} ({townName})";
        latitudeSeries.showOnInit = true;

        var latitudeBullet = latitudeSeries.bullets.push(new am4charts.CircleBullet());
        latitudeBullet.circle.fill = am4core.color("#fff");
        latitudeBullet.circle.strokeWidth = 2;
        latitudeBullet.circle.propertyFields.radius = "townSize";

        var latitudeState = latitudeBullet.states.create("hover");
        latitudeState.properties.scale = 1.2;

        var latitudeLabel = latitudeSeries.bullets.push(new am4charts.LabelBullet());
        latitudeLabel.label.text = "{townName2}";
        latitudeLabel.label.horizontalCenter = "left";
        latitudeLabel.label.dx = 14;

        // Add legend
        chart.legend = new am4charts.Legend();

        // Add cursor
        chart.cursor = new am4charts.XYCursor();
        chart.cursor.fullWidthLineX = true;
        chart.cursor.xAxis = dateAxis;
        chart.cursor.lineX.strokeOpacity = 0;
        chart.cursor.lineX.fill = am4core.color("#000");
        chart.cursor.lineX.fillOpacity = 0.1;

    });
    // ++++++++++++++++++++++++++++++++++++++ Duration on Value Axis ++++++++++++++++++++++++++++++++++++++
    
    // ====================================== Date Based Data ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivDateBasedData", am4charts.XYChart);
        chart.logo.disabled = true;

        // Add data
        chart.data = [
            {
            "date": "2012-07-27",
            "value": 13
            }, {
            "date": "2012-07-28",
            "value": 11
            }, {
            "date": "2012-07-29",
            "value": 15
            }, {
            "date": "2012-07-30",
            "value": 16
            }, {
            "date": "2012-07-31",
            "value": 18
            }, {
            "date": "2012-08-01",
            "value": 13
            }, {
            "date": "2012-08-02",
            "value": 22
            }, {
            "date": "2012-08-03",
            "value": 23
            }, {
            "date": "2012-08-04",
            "value": 20
            }, {
            "date": "2012-08-05",
            "value": 17
            }, {
            "date": "2012-08-06",
            "value": 16
            }, {
            "date": "2012-08-07",
            "value": 18
            }, {
            "date": "2012-08-08",
            "value": 21
            }, {
            "date": "2012-08-09",
            "value": 26
            }, {
            "date": "2012-08-10",
            "value": 24
            }, {
            "date": "2012-08-11",
            "value": 29
            }, {
            "date": "2012-08-12",
            "value": 32
            }, {
            "date": "2012-08-13",
            "value": 18
            }, {
            "date": "2012-08-14",
            "value": 24
            }, {
            "date": "2012-08-15",
            "value": 22
            }, {
            "date": "2012-08-16",
            "value": 18
            }, {
            "date": "2012-08-17",
            "value": 19
            }, {
            "date": "2012-08-18",
            "value": 14
            }, {
            "date": "2012-08-19",
            "value": 15
            }, {
            "date": "2012-08-20",
            "value": 12
            }, {
            "date": "2012-08-21",
            "value": 8
            }, {
            "date": "2012-08-22",
            "value": 9
            }, {
            "date": "2012-08-23",
            "value": 8
            }, {
            "date": "2012-08-24",
            "value": 7
            }, {
            "date": "2012-08-25",
            "value": 5
            }, {
            "date": "2012-08-26",
            "value": 11
            }, {
            "date": "2012-08-27",
            "value": 13
            }, {
            "date": "2012-08-28",
            "value": 18
            }, {
            "date": "2012-08-29",
            "value": 20
            }, {
            "date": "2012-08-30",
            "value": 29
            }, {
            "date": "2012-08-31",
            "value": 33
            }, {
            "date": "2012-09-01",
            "value": 42
            }, {
            "date": "2012-09-02",
            "value": 35
            }, {
            "date": "2012-09-03",
            "value": 31
            }, {
            "date": "2012-09-04",
            "value": 47
            }, {
            "date": "2012-09-05",
            "value": 52
            }, {
            "date": "2012-09-06",
            "value": 46
            }, {
            "date": "2012-09-07",
            "value": 41
            }, {
            "date": "2012-09-08",
            "value": 43
            }, {
            "date": "2012-09-09",
            "value": 40
            }, {
            "date": "2012-09-10",
            "value": 39
            }, {
            "date": "2012-09-11",
            "value": 34
            }, {
            "date": "2012-09-12",
            "value": 29
            }, {
            "date": "2012-09-13",
            "value": 34
            }, {
            "date": "2012-09-14",
            "value": 37
            }, {
            "date": "2012-09-15",
            "value": 42
            }, {
            "date": "2012-09-16",
            "value": 49
            }, {
            "date": "2012-09-17",
            "value": 46
            }, {
            "date": "2012-09-18",
            "value": 47
            }, {
            "date": "2012-09-19",
            "value": 55
            }, {
            "date": "2012-09-20",
            "value": 59
            }, {
            "date": "2012-09-21",
            "value": 58
            }, {
            "date": "2012-09-22",
            "value": 57
            }, {
            "date": "2012-09-23",
            "value": 61
            }, {
            "date": "2012-09-24",
            "value": 59
            }, {
            "date": "2012-09-25",
            "value": 67
            }, {
            "date": "2012-09-26",
            "value": 65
            }, {
            "date": "2012-09-27",
            "value": 61
            }, {
            "date": "2012-09-28",
            "value": 66
            }, {
            "date": "2012-09-29",
            "value": 69
            }, {
            "date": "2012-09-30",
            "value": 71
            }, {
            "date": "2012-10-01",
            "value": 67
            }, {
            "date": "2012-10-02",
            "value": 63
            }, {
            "date": "2012-10-03",
            "value": 46
            }, {
            "date": "2012-10-04",
            "value": 32
            }, {
            "date": "2012-10-05",
            "value": 21
            }, {
            "date": "2012-10-06",
            "value": 18
            }, {
            "date": "2012-10-07",
            "value": 21
            }, {
            "date": "2012-10-08",
            "value": 28
            }, {
            "date": "2012-10-09",
            "value": 27
            }, {
            "date": "2012-10-10",
            "value": 36
            }, {
            "date": "2012-10-11",
            "value": 33
            }, {
            "date": "2012-10-12",
            "value": 31
            }, {
            "date": "2012-10-13",
            "value": 30
            }, {
            "date": "2012-10-14",
            "value": 34
            }, {
            "date": "2012-10-15",
            "value": 38
            }, {
            "date": "2012-10-16",
            "value": 37
            }, {
            "date": "2012-10-17",
            "value": 44
            }, {
            "date": "2012-10-18",
            "value": 49
            }, {
            "date": "2012-10-19",
            "value": 53
            }, {
            "date": "2012-10-20",
            "value": 57
            }, {
            "date": "2012-10-21",
            "value": 60
            }, {
            "date": "2012-10-22",
            "value": 61
            }, {
            "date": "2012-10-23",
            "value": 69
            }, {
            "date": "2012-10-24",
            "value": 67
            }, {
            "date": "2012-10-25",
            "value": 72
            }, {
            "date": "2012-10-26",
            "value": 77
            }, {
            "date": "2012-10-27",
            "value": 75
            }, {
            "date": "2012-10-28",
            "value": 70
            }, {
            "date": "2012-10-29",
            "value": 72
            }, {
            "date": "2012-10-30",
            "value": 70
            }, {
            "date": "2012-10-31",
            "value": 72
            }, {
            "date": "2012-11-01",
            "value": 73
            }, {
            "date": "2012-11-02",
            "value": 67
            }, {
            "date": "2012-11-03",
            "value": 68
            }, {
            "date": "2012-11-04",
            "value": 65
            }, {
            "date": "2012-11-05",
            "value": 71
            }, {
            "date": "2012-11-06",
            "value": 75
            }, {
            "date": "2012-11-07",
            "value": 74
            }, {
            "date": "2012-11-08",
            "value": 71
            }, {
            "date": "2012-11-09",
            "value": 76
            }, {
            "date": "2012-11-10",
            "value": 77
            }, {
            "date": "2012-11-11",
            "value": 81
            }, {
            "date": "2012-11-12",
            "value": 83
            }, {
            "date": "2012-11-13",
            "value": 80
            }, {
            "date": "2012-11-14",
            "value": 81
            }, {
            "date": "2012-11-15",
            "value": 87
            }, {
            "date": "2012-11-16",
            "value": 82
            }, {
            "date": "2012-11-17",
            "value": 86
            }, {
            "date": "2012-11-18",
            "value": 80
            }, {
            "date": "2012-11-19",
            "value": 87
            }, {
            "date": "2012-11-20",
            "value": 83
            }, {
            "date": "2012-11-21",
            "value": 85
            }, {
            "date": "2012-11-22",
            "value": 84
            }, {
            "date": "2012-11-23",
            "value": 82
            }, {
            "date": "2012-11-24",
            "value": 73
            }, {
            "date": "2012-11-25",
            "value": 71
            }, {
            "date": "2012-11-26",
            "value": 75
            }, {
            "date": "2012-11-27",
            "value": 79
            }, {
            "date": "2012-11-28",
            "value": 70
            }, {
            "date": "2012-11-29",
            "value": 73
            }, {
            "date": "2012-11-30",
            "value": 61
            }, {
            "date": "2012-12-01",
            "value": 62
            }, {
            "date": "2012-12-02",
            "value": 66
            }, {
            "date": "2012-12-03",
            "value": 65
            }, {
            "date": "2012-12-04",
            "value": 73
            }, {
            "date": "2012-12-05",
            "value": 79
            }, {
            "date": "2012-12-06",
            "value": 78
            }, {
            "date": "2012-12-07",
            "value": 78
            }, {
            "date": "2012-12-08",
            "value": 78
            }, {
            "date": "2012-12-09",
            "value": 74
            }, {
            "date": "2012-12-10",
            "value": 73
            }, {
            "date": "2012-12-11",
            "value": 75
            }, {
            "date": "2012-12-12",
            "value": 70
            }, {
            "date": "2012-12-13",
            "value": 77
            }, {
            "date": "2012-12-14",
            "value": 67
            }, {
            "date": "2012-12-15",
            "value": 62
            }, {
            "date": "2012-12-16",
            "value": 64
            }, {
            "date": "2012-12-17",
            "value": 61
            }, {
            "date": "2012-12-18",
            "value": 59
            }, {
            "date": "2012-12-19",
            "value": 53
            }, {
            "date": "2012-12-20",
            "value": 54
            }, {
            "date": "2012-12-21",
            "value": 56
            }, {
            "date": "2012-12-22",
            "value": 59
            }, {
            "date": "2012-12-23",
            "value": 58
            }, {
            "date": "2012-12-24",
            "value": 55
            }, {
            "date": "2012-12-25",
            "value": 52
            }, {
            "date": "2012-12-26",
            "value": 54
            }, {
            "date": "2012-12-27",
            "value": 50
            }, {
            "date": "2012-12-28",
            "value": 50
            }, {
            "date": "2012-12-29",
            "value": 51
            }, {
            "date": "2012-12-30",
            "value": 52
            }, {
            "date": "2012-12-31",
            "value": 58
            }, {
            "date": "2013-01-01",
            "value": 60
            }, {
            "date": "2013-01-02",
            "value": 67
            }, {
            "date": "2013-01-03",
            "value": 64
            }, {
            "date": "2013-01-04",
            "value": 66
            }, {
            "date": "2013-01-05",
            "value": 60
            }, {
            "date": "2013-01-06",
            "value": 63
            }, {
            "date": "2013-01-07",
            "value": 61
            }, {
            "date": "2013-01-08",
            "value": 60
            }, {
            "date": "2013-01-09",
            "value": 65
            }, {
            "date": "2013-01-10",
            "value": 75
            }, {
            "date": "2013-01-11",
            "value": 77
            }, {
            "date": "2013-01-12",
            "value": 78
            }, {
            "date": "2013-01-13",
            "value": 70
            }, {
            "date": "2013-01-14",
            "value": 70
            }, {
            "date": "2013-01-15",
            "value": 73
            }, {
            "date": "2013-01-16",
            "value": 71
            }, {
            "date": "2013-01-17",
            "value": 74
            }, {
            "date": "2013-01-18",
            "value": 78
            }, {
            "date": "2013-01-19",
            "value": 85
            }, {
            "date": "2013-01-20",
            "value": 82
            }, {
            "date": "2013-01-21",
            "value": 83
            }, {
            "date": "2013-01-22",
            "value": 88
            }, {
            "date": "2013-01-23",
            "value": 85
            }, {
            "date": "2013-01-24",
            "value": 85
            }, {
            "date": "2013-01-25",
            "value": 80
            }, {
            "date": "2013-01-26",
            "value": 87
            }, {
            "date": "2013-01-27",
            "value": 84
            }, {
            "date": "2013-01-28",
            "value": 83
            }, {
            "date": "2013-01-29",
            "value": 84
            }, {
            "date": "2013-01-30",
            "value": 81
            }
        ];

        // Set input format for the dates
        chart.dateFormatter.inputDateFormat = "yyyy-MM-dd";

        // Create axes
        var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

        // Create series
        var series = chart.series.push(new am4charts.LineSeries());
        series.dataFields.valueY = "value";
        series.dataFields.dateX = "date";
        series.tooltipText = "{value}"
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

        dateAxis.start = 0.79;
        dateAxis.keepSelection = true;

    });
    // ++++++++++++++++++++++++++++++++++++++ Date Based Data ++++++++++++++++++++++++++++++++++++++
        
    // ====================================== Range Chart ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        var chart = am4core.create("chartDivRangeChart", am4charts.XYChart);
        chart.logo.disabled = true;

        chart.data = [
            { date: 1577743200000, open: 122, close: 104 },
            { date: 1577829600000, open: 121, close: 70 },
            { date: 1577916000000, open: 101, close: 55 },
            { date: 1578002400000, open: 103, close: 45 },
            { date: 1578088800000, open: 153, close: 85 },
            { date: 1578175200000, open: 150, close: 116 },
            { date: 1578261600000, open: 135, close: 153 },
            { date: 1578348000000, open: 98, close: 152 },
            { date: 1578434400000, open: 101, close: 192 },
            { date: 1578520800000, open: 110, close: 225 },
            { date: 1578607200000, open: 157, close: 233 },
            { date: 1578693600000, open: 128, close: 232 },
            { date: 1578780000000, open: 101, close: 235 },
            { date: 1578866400000, open: 109, close: 200 },
            { date: 1578952800000, open: 142, close: 214 },
            { date: 1579039200000, open: 123, close: 224 },
            { date: 1579125600000, open: 99, close: 176 },
            { date: 1579212000000, open: 100, close: 172 },
            { date: 1579298400000, open: 67, close: 138 },
            { date: 1579384800000, open: 81, close: 127 },
            { date: 1579471200000, open: 39, close: 137 },
            { date: 1579557600000, open: 73, close: 127 },
            { date: 1579644000000, open: 78, close: 154 },
            { date: 1579730400000, open: 116, close: 127 },
            { date: 1579816800000, open: 136, close: 78 },
            { date: 1579903200000, open: 139, close: 61 },
            { date: 1579989600000, open: 162, close: 13 },
            { date: 1580076000000, open: 201, close: 41 },
            { date: 1580162400000, open: 221, close: 72 },
            { date: 1580248800000, open: 257, close: 87 },
            { date: 1580335200000, open: 211, close: 114 },
            { date: 1580421600000, open: 233, close: 138 },
            { date: 1580508000000, open: 261, close: 141 },
            { date: 1580594400000, open: 279, close: 130 }
        ]

        var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        dateAxis.renderer.grid.template.location = 0;
        dateAxis.renderer.minGridDistance = 60;

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.tooltip.disabled = true;

        // only for the legend
        var iconSeries = chart.series.push(new am4charts.ColumnSeries())
        iconSeries.fill = am4core.color("#ec0800");
        iconSeries.strokeOpacity = 0;
        iconSeries.stroke = am4core.color("#ec0800");
        iconSeries.name = "Events";
        iconSeries.dataFields.dateX = "date";
        iconSeries.dataFields.valueY = "v";

        var series = chart.series.push(new am4charts.LineSeries());
        series.dataFields.dateX = "date";
        series.dataFields.openValueY = "open";
        series.dataFields.valueY = "close";
        series.tooltipText = "open: {openValueY.value} close: {valueY.value}";
        series.sequencedInterpolation = true;
        series.stroke = am4core.color("#1b7cb3");
        series.strokeWidth = 2;
        series.name = "District Metered Usage";
        series.stroke = chart.colors.getIndex(0);
        series.fill = series.stroke;
        series.fillOpacity = 0.8;

        var bullet = series.bullets.push(new am4charts.CircleBullet())
        bullet.fill = new am4core.InterfaceColorSet().getFor("background");
        bullet.fillOpacity = 1;
        bullet.strokeWidth = 2;
        bullet.circle.radius = 4;

        var series2 = chart.series.push(new am4charts.LineSeries());
        series2.dataFields.dateX = "date";
        series2.dataFields.valueY = "open";
        series2.sequencedInterpolation = true;
        series2.strokeWidth = 2;
        series2.tooltip.getFillFromObject = false;
        series2.tooltip.getStrokeFromObject = true;
        series2.tooltip.label.fill = am4core.color("#000");
        series2.name = "SP Aggregate usage";
        series2.stroke = chart.colors.getIndex(7);
        series2.fill = series2.stroke;

        var bullet2 = series2.bullets.push(new am4charts.CircleBullet())
        bullet2.fill = bullet.fill;
        bullet2.fillOpacity = 1;
        bullet2.strokeWidth = 2;
        bullet2.circle.radius = 4;

        chart.cursor = new am4charts.XYCursor();
        chart.cursor.xAxis = dateAxis;
        chart.scrollbarX = new am4core.Scrollbar();

        var negativeRange;

        // create ranges
        var negativeRange;

        // create ranges
        chart.events.on("datavalidated", function() {
            series.dataItems.each(function(s1DataItem) {
                var s1PreviousDataItem;
                var s2PreviousDataItem;

                var s2DataItem = series2.dataItems.getIndex(s1DataItem.index);

                if (s1DataItem.index > 0) {
                    s1PreviousDataItem = series.dataItems.getIndex(s1DataItem.index - 1);
                    s2PreviousDataItem = series2.dataItems.getIndex(s1DataItem.index - 1);
                }

                var startTime = am4core.time.round(new Date(s1DataItem.dateX.getTime()), dateAxis.baseInterval.timeUnit, dateAxis.baseInterval.count).getTime();

                // intersections
                if (s1PreviousDataItem && s2PreviousDataItem) {
                    var x0 = am4core.time.round(new Date(s1PreviousDataItem.dateX.getTime()), dateAxis.baseInterval.timeUnit, dateAxis.baseInterval.count).getTime() + dateAxis.baseDuration / 2;
                    var y01 = s1PreviousDataItem.valueY;
                    var y02 = s2PreviousDataItem.valueY;

                    var x1 = startTime + dateAxis.baseDuration / 2;
                    var y11 = s1DataItem.valueY;
                    var y12 = s2DataItem.valueY;

                    var intersection = am4core.math.getLineIntersection({ x: x0, y: y01 }, { x: x1, y: y11 }, { x: x0, y: y02 }, { x: x1, y: y12 });

                    startTime = Math.round(intersection.x);
                }

                // start range here
                if (s2DataItem.valueY > s1DataItem.valueY) {
                    if (!negativeRange) {
                        negativeRange = dateAxis.createSeriesRange(series);
                        negativeRange.date = new Date(startTime);
                        negativeRange.contents.fill = series2.fill;
                        negativeRange.contents.fillOpacity = 0.8;
                    }
                }
                else {
                    // if negative range started
                    if (negativeRange) {
                        negativeRange.endDate = new Date(startTime);
                    }
                    negativeRange = undefined;
                }
                // end if last
                if (s1DataItem.index == series.dataItems.length - 1) {
                    if (negativeRange) {
                        negativeRange.endDate = new Date(s1DataItem.dateX.getTime() + dateAxis.baseDuration / 2);
                        negativeRange = undefined;
                    }
                }
            })
        })

    });
    // ++++++++++++++++++++++++++++++++++++++ Range Chart ++++++++++++++++++++++++++++++++++++++
    
    // ====================================== Area With Time Based Data ======================================
    am4core.ready(function() {

        // Themes 
        am4core.useTheme(am4themes_animated);

        // Create chart
        var chart = am4core.create("chartDivAreaTimeBasedData", am4charts.XYChart);
        chart.logo.disabled = true;
        chart.paddingRight = 20;

        chart.data = generateChartData();

        var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        dateAxis.baseInterval = {
            "timeUnit": "minute",
            "count": 1
        };
        dateAxis.tooltipDateFormat = "HH:mm, d MMMM";

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.tooltip.disabled = true;
        valueAxis.title.text = "Unique visitors";

        var series = chart.series.push(new am4charts.LineSeries());
        series.dataFields.dateX = "date";
        series.dataFields.valueY = "visits";
        series.tooltipText = "Visits: [bold]{valueY}[/]";
        series.fillOpacity = 0.3;


        chart.cursor = new am4charts.XYCursor();
        chart.cursor.lineY.opacity = 0;
        chart.scrollbarX = new am4charts.XYChartScrollbar();
        chart.scrollbarX.series.push(series);


        dateAxis.start = 0.8;
        dateAxis.keepSelection = true;

        function generateChartData() {
            var chartData = [];
            // current date
            var firstDate = new Date();
            // now set 500 minutes back
            firstDate.setMinutes(firstDate.getDate() - 500);

            // and generate 500 data items
            var visits = 500;
            for (var i = 0; i < 500; i++) {
                var newDate = new Date(firstDate);
                // each time we add one minute
                newDate.setMinutes(newDate.getMinutes() + i);
                // some random number
                visits += Math.round((Math.random()<0.5?1:-1)*Math.random()*10);
                // add data item to the array
                chartData.push({
                    date: newDate,
                    visits: visits
                });
            }
            return chartData;
        }

    });
    // ++++++++++++++++++++++++++++++++++++++ Area With Time Based Data ++++++++++++++++++++++++++++++++++++++
    
    // ====================================== Two-level Pie Chart ======================================
    am4core.ready(function() {

        // Themes 
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivTwoLevelPieChart", am4charts.PieChart);
        chart.logo.disabled = true;

        // Let's cut a hole in our Pie chart the size of 40% the radius
        chart.innerRadius = am4core.percent(40);

        // Add and configure Series
        var pieSeries = chart.series.push(new am4charts.PieSeries());
        pieSeries.dataFields.value = "value";
        pieSeries.dataFields.category = "category";
        pieSeries.slices.template.stroke = am4core.color("#fff");
        pieSeries.innerRadius = 10;
        pieSeries.slices.template.fillOpacity = 0.5;

        pieSeries.slices.template.propertyFields.disabled = "labelDisabled";
        pieSeries.labels.template.propertyFields.disabled = "labelDisabled";
        pieSeries.ticks.template.propertyFields.disabled = "labelDisabled";


        // Add data
        pieSeries.data = [
            {
            "category": "First + Second",
            "value": 60
            }, {
            "category": "Unused",
            "value": 30,
            "labelDisabled":true
            }
        ];

        // Disable sliding out of slices
        pieSeries.slices.template.states.getKey("hover").properties.shiftRadius = 0;
        pieSeries.slices.template.states.getKey("hover").properties.scale = 1;

        // Add second series
        var pieSeries2 = chart.series.push(new am4charts.PieSeries());
        pieSeries2.dataFields.value = "value";
        pieSeries2.dataFields.category = "category";
        pieSeries2.slices.template.states.getKey("hover").properties.shiftRadius = 0;
        pieSeries2.slices.template.states.getKey("hover").properties.scale = 1;
        pieSeries2.slices.template.propertyFields.fill = "fill";

        // Add data
        pieSeries2.data = [
            {
            "category": "First",
            "value": 30
            }, {
            "category": "Second",
            "value": 30
            }, {
            "category": "Remaining",
            "value": 30,
            "fill":"#dedede"
            }
        ];


        pieSeries.adapter.add("innerRadius", function(innerRadius, target){
            return am4core.percent(40);
        })

        pieSeries2.adapter.add("innerRadius", function(innerRadius, target){
            return am4core.percent(60);
        })

        pieSeries.adapter.add("radius", function(innerRadius, target){
            return am4core.percent(100);
        })

        pieSeries2.adapter.add("radius", function(innerRadius, target){
            return am4core.percent(80);
        })

    });
    // ++++++++++++++++++++++++++++++++++++++ Two-level Pie Chart ++++++++++++++++++++++++++++++++++++++
    
    // ====================================== 3D Donut Chart ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        var chart = am4core.create("chartDivDonutChart", am4charts.PieChart3D);
        chart.logo.disabled = true;
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

        chart.legend = new am4charts.Legend();

        chart.data = [
            {
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

        chart.innerRadius = 100;

        var series = chart.series.push(new am4charts.PieSeries3D());
        series.dataFields.value = "litres";
        series.dataFields.category = "country";

    });
    // ++++++++++++++++++++++++++++++++++++++ 3D Donut Chart ++++++++++++++++++++++++++++++++++++++

    // ====================================== Variable-height 3D Pie Chart ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        var chart = am4core.create("chartDivVariableHeightPieChart", am4charts.PieChart3D);
        chart.logo.disabled = true;
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

        chart.data = [
            {
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
            }
        ];

        chart.innerRadius = am4core.percent(40);
        chart.depth = 120;

        chart.legend = new am4charts.Legend();

        var series = chart.series.push(new am4charts.PieSeries3D());
        series.dataFields.value = "litres";
        series.dataFields.depthValue = "litres";
        series.dataFields.category = "country";
        series.slices.template.cornerRadius = 5;
        series.colors.step = 3;

    });
    // ++++++++++++++++++++++++++++++++++++++ Variable-height 3D Pie Chart ++++++++++++++++++++++++++++++++++++++

    // ====================================== Variable-radius nested donut chart ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivVariableRadiusDonutChart", am4charts.PieChart);
        chart.logo.disabled = true;
        chart.startAngle = 160;
        chart.endAngle = 380;

        // Let's cut a hole in our Pie chart the size of 40% the radius
        chart.innerRadius = am4core.percent(40);

        // Add data
        chart.data = [
            {
                "country": "Lithuania",
                "litres": 501.9,
                "bottles": 1500
            }, {
                "country": "Czech Republic",
                "litres": 301.9,
                "bottles": 990
            }, {
                "country": "Ireland",
                "litres": 201.1,
                "bottles": 785
            }, {
                "country": "Germany",
                "litres": 165.8,
                "bottles": 255
            }, {
                "country": "Australia",
                "litres": 139.9,
                "bottles": 452
            }, {
                "country": "Austria",
                "litres": 128.3,
                "bottles": 332
            }, {
                "country": "UK",
                "litres": 99,
                "bottles": 150
            }, {
                "country": "Belgium",
                "litres": 60,
                "bottles": 178
            }, {
                "country": "The Netherlands",
                "litres": 50,
                "bottles": 50
            }
        ];

        // Add and configure Series
        var pieSeries = chart.series.push(new am4charts.PieSeries());
        pieSeries.dataFields.value = "litres";
        pieSeries.dataFields.category = "country";
        pieSeries.slices.template.stroke = new am4core.InterfaceColorSet().getFor("background");
        pieSeries.slices.template.strokeWidth = 1;
        pieSeries.slices.template.strokeOpacity = 1;

        // Disabling labels and ticks on inner circle
        pieSeries.labels.template.disabled = true;
        pieSeries.ticks.template.disabled = true;

        // Disable sliding out of slices
        pieSeries.slices.template.states.getKey("hover").properties.shiftRadius = 0;
        pieSeries.slices.template.states.getKey("hover").properties.scale = 1;
        pieSeries.radius = am4core.percent(40);
        pieSeries.innerRadius = am4core.percent(30);

        var cs = pieSeries.colors;
        cs.list = [am4core.color(new am4core.ColorSet().getIndex(0))];

        cs.stepOptions = {
            lightness: -0.05,
            hue: 0
        };
        cs.wrap = false;


        // Add second series
        var pieSeries2 = chart.series.push(new am4charts.PieSeries());
        pieSeries2.dataFields.value = "bottles";
        pieSeries2.dataFields.category = "country";
        pieSeries2.slices.template.stroke = new am4core.InterfaceColorSet().getFor("background");
        pieSeries2.slices.template.strokeWidth = 1;
        pieSeries2.slices.template.strokeOpacity = 1;
        pieSeries2.slices.template.states.getKey("hover").properties.shiftRadius = 0.05;
        pieSeries2.slices.template.states.getKey("hover").properties.scale = 1;

        pieSeries2.labels.template.disabled = true;
        pieSeries2.ticks.template.disabled = true;


        var label = chart.seriesContainer.createChild(am4core.Label);
        label.textAlign = "middle";
        label.horizontalCenter = "middle";
        label.verticalCenter = "middle";
        label.adapter.add("text", function(text, target){
            return "[font-size:18px]total[/]:\n[bold font-size:30px]" + pieSeries.dataItem.values.value.sum + "[/]";
        })

    });
    // ++++++++++++++++++++++++++++++++++++++ Variable-radius nested donut chart ++++++++++++++++++++++++++++++++++++++

    // ====================================== Zoomable Bubble Chart ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        var chart = am4core.create("chartDivZoomableBubbleChart", am4charts.XYChart);
        chart.logo.disabled = true;

        var valueAxisX = chart.xAxes.push(new am4charts.ValueAxis());
        valueAxisX.renderer.ticks.template.disabled = true;
        valueAxisX.renderer.axisFills.template.disabled = true;

        var valueAxisY = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxisY.renderer.ticks.template.disabled = true;
        valueAxisY.renderer.axisFills.template.disabled = true;

        var series = chart.series.push(new am4charts.LineSeries());
        series.dataFields.valueX = "x";
        series.dataFields.valueY = "y";
        series.dataFields.value = "value";
        series.strokeOpacity = 0;
        series.sequencedInterpolation = true;
        series.tooltip.pointerOrientation = "vertical";

        var bullet = series.bullets.push(new am4core.Circle());
        bullet.fill = am4core.color("#ff0000");
        bullet.propertyFields.fill = "color";
        bullet.strokeOpacity = 0;
        bullet.strokeWidth = 2;
        bullet.fillOpacity = 0.5;
        bullet.stroke = am4core.color("#ffffff");
        bullet.hiddenState.properties.opacity = 0;
        bullet.tooltipText = "[bold]{title}:[/]\nPopulation: {value.value}\nIncome: {valueX.value}\nLife expectancy:{valueY.value}";

        var outline = chart.plotContainer.createChild(am4core.Circle);
        outline.fillOpacity = 0;
        outline.strokeOpacity = 0.8;
        outline.stroke = am4core.color("#ff0000");
        outline.strokeWidth = 2;
        outline.hide(0);

        var blurFilter = new am4core.BlurFilter();
        outline.filters.push(blurFilter);

        bullet.events.on("over", function(event) {
            var target = event.target;
            outline.radius = target.pixelRadius + 2;
            outline.x = target.pixelX;
            outline.y = target.pixelY;
            outline.show();
        })

        bullet.events.on("out", function(event) {
            outline.hide();
        })

        var hoverState = bullet.states.create("hover");
        hoverState.properties.fillOpacity = 1;
        hoverState.properties.strokeOpacity = 1;

        series.heatRules.push({ target: bullet, min: 2, max: 60, property: "radius" });

        bullet.adapter.add("tooltipY", function (tooltipY, target) {
            return -target.radius;
        })

        chart.cursor = new am4charts.XYCursor();
        chart.cursor.behavior = "zoomXY";
        chart.cursor.snapToSeries = series;

        chart.scrollbarX = new am4core.Scrollbar();
        chart.scrollbarY = new am4core.Scrollbar();

        chart.data = [
            {
                "title": "Afghanistan",
                "id": "AF",
                "color": "#eea638",
                "continent": "asia",
                "x": 1349.69694102398,
                "y": 60.524,
                "value": 33397058
            },
            {
                "title": "Albania",
                "id": "AL",
                "color": "#d8854f",
                "continent": "europe",
                "x": 6969.30628256456,
                "y": 77.185,
                "value": 3227373
            },
            {
                "title": "Algeria",
                "id": "DZ",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 6419.12782939372,
                "y": 70.874,
                "value": 36485828
            },
            {
                "title": "Angola",
                "id": "AO",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 5838.15537582502,
                "y": 51.498,
                "value": 20162517
            },
            {
                "title": "Argentina",
                "id": "AR",
                "color": "#86a965",
                "continent": "south_america",
                "x": 15714.1031814398,
                "y": 76.128,
                "value": 41118986
            },
            {
                "title": "Armenia",
                "id": "AM",
                "color": "#d8854f",
                "continent": "europe",
                "x": 5059.0879636443,
                "y": 74.469,
                "value": 3108972
            },
            {
                "title": "Australia",
                "id": "AU",
                "color": "#8aabb0",
                "continent": "australia",
                "x": 36064.7372768548,
                "y": 82.364,
                "value": 22918688
            },
            {
                "title": "Austria",
                "id": "AT",
                "color": "#d8854f",
                "continent": "europe",
                "x": 36731.6287741081,
                "y": 80.965,
                "value": 8428915
            },
            {
                "title": "Azerbaijan",
                "id": "AZ",
                "color": "#d8854f",
                "continent": "europe",
                "x": 9291.02626998762,
                "y": 70.686,
                "value": 9421233
            },
            {
                "title": "Bahrain",
                "id": "BH",
                "color": "#eea638",
                "continent": "asia",
                "x": 24472.896235865,
                "y": 76.474,
                "value": 1359485
            },
            {
                "title": "Bangladesh",
                "id": "BD",
                "color": "#eea638",
                "continent": "asia",
                "x": 1792.55023464123,
                "y": 70.258,
                "value": 152408774
            },
            {
                "title": "Belarus",
                "id": "BY",
                "color": "#d8854f",
                "continent": "europe",
                "x": 13515.1610255056,
                "y": 69.829,
                "value": 9527498
            },
            {
                "title": "Belgium",
                "id": "BE",
                "color": "#d8854f",
                "continent": "europe",
                "x": 32585.0119650436,
                "y": 80.373,
                "value": 10787788
            },
            {
                "title": "Benin",
                "id": "BJ",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 1464.13825459126,
                "y": 59.165,
                "value": 9351838
            },
            {
                "title": "Bhutan",
                "id": "BT",
                "color": "#eea638",
                "continent": "asia",
                "x": 6130.86235464324,
                "y": 67.888,
                "value": 750443
            },
            {
                "title": "Bolivia",
                "id": "BO",
                "color": "#86a965",
                "continent": "south_america",
                "x": 4363.43264453337,
                "y": 66.969,
                "value": 10248042
            },
            {
                "title": "Bosnia and Herzegovina",
                "id": "BA",
                "color": "#d8854f",
                "continent": "europe",
                "x": 7664.15281166303,
                "y": 76.211,
                "value": 3744235
            },
            {
                "title": "Botswana",
                "id": "BW",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 14045.9403255843,
                "y": 47.152,
                "value": 2053237
            },
            {
                "title": "Brazil",
                "id": "BR",
                "color": "#86a965",
                "continent": "south_america",
                "x": 10383.5405937283,
                "y": 73.667,
                "value": 198360943
            },
            {
                "title": "Brunei",
                "id": "BN",
                "color": "#eea638",
                "continent": "asia",
                "x": 45658.2532642054,
                "y": 78.35,
                "value": 412892
            },
            {
                "title": "Bulgaria",
                "id": "BG",
                "color": "#d8854f",
                "continent": "europe",
                "x": 11669.7223127119,
                "y": 73.448,
                "value": 7397873
            },
            {
                "title": "Burkina Faso",
                "id": "BF",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 1363.77981282077,
                "y": 55.932,
                "value": 17481984
            },
            {
                "title": "Burundi",
                "id": "BI",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 484.090924612833,
                "y": 53.637,
                "value": 8749387
            },
            {
                "title": "Cambodia",
                "id": "KH",
                "color": "#eea638",
                "continent": "asia",
                "x": 2076.68958647462,
                "y": 71.577,
                "value": 14478320
            },
            {
                "title": "Romania",
                "id": "RO",
                "color": "#d8854f",
                "continent": "europe",
                "x": 11058.1809744544,
                "y": 73.718,
                "value": 21387517
            },
            {
                "title": "Russia",
                "id": "RU",
                "color": "#d8854f",
                "continent": "europe",
                "x": 15427.6167470064,
                "y": 67.874,
                "value": 142703181
            },
            {
                "title": "Rwanda",
                "id": "RW",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 1223.52570881561,
                "y": 63.563,
                "value": 11271786
            },
            {
                "title": "Saudi Arabia",
                "id": "SA",
                "color": "#eea638",
                "continent": "asia",
                "x": 26259.6213479005,
                "y": 75.264,
                "value": 28705133
            },
            {
                "title": "Senegal",
                "id": "SN",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 1753.48800936096,
                "y": 63.3,
                "value": 13107945
            },
            {
                "title": "Serbia",
                "id": "RS",
                "color": "#d8854f",
                "continent": "europe",
                "x": 9335.95911484282,
                "y": 73.934,
                "value": 9846582
            },
            {
                "title": "Sierra Leone",
                "id": "SL",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 1072.95787930719,
                "y": 45.338,
                "value": 6126450
            },
            {
                "title": "Singapore",
                "id": "SG",
                "color": "#eea638",
                "continent": "asia",
                "x": 49381.9560054179,
                "y": 82.155,
                "value": 5256278
            },
            {
                "title": "Slovak Republic",
                "id": "SK",
                "color": "#d8854f",
                "continent": "europe",
                "x": 20780.9857840812,
                "y": 75.272,
                "value": 5480332
            },
            {
                "title": "Slovenia",
                "id": "SI",
                "color": "#d8854f",
                "continent": "europe",
                "x": 23986.8506836646,
                "y": 79.444,
                "value": 2040057
            },
            {
                "title": "Solomon Islands",
                "id": "SB",
                "color": "#8aabb0",
                "continent": "australia",
                "x": 2024.23067334134,
                "y": 67.465,
                "value": 566481
            },
            {
                "title": "Somalia",
                "id": "SO",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 953.275713662563,
                "y": 54,
                "value": 9797445
            },
            {
                "title": "South Africa",
                "id": "ZA",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 9657.25275417241,
                "y": 56.271,
                "value": 50738255
            },
            {
                "title": "South Sudan",
                "id": "SS",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 1433.03720057714,
                "y": 54.666,
                "value": 10386101
            },
            {
                "title": "Spain",
                "id": "ES",
                "color": "#d8854f",
                "continent": "europe",
                "x": 26457.7572559653,
                "y": 81.958,
                "value": 46771596
            },
            {
                "title": "Sri Lanka",
                "id": "LK",
                "color": "#eea638",
                "continent": "asia",
                "x": 5182.66658831813,
                "y": 74.116,
                "value": 21223550
            },
            {
                "title": "Sudan",
                "id": "SD",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 2917.61641581811,
                "y": 61.875,
                "value": 35335982
            },
            {
                "title": "Suriname",
                "id": "SR",
                "color": "#86a965",
                "continent": "south_america",
                "x": 8979.80549248675,
                "y": 70.794,
                "value": 534175
            },
            {
                "title": "Swaziland",
                "id": "SZ",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 4979.704126513,
                "y": 48.91,
                "value": 1220408
            },
            {
                "title": "Sweden",
                "id": "SE",
                "color": "#d8854f",
                "continent": "europe",
                "x": 34530.2628238397,
                "y": 81.69,
                "value": 9495392
            },
            {
                "title": "Switzerland",
                "id": "CH",
                "color": "#d8854f",
                "continent": "europe",
                "x": 37678.3928108684,
                "y": 82.471,
                "value": 7733709
            },
            {
                "title": "Syria",
                "id": "SY",
                "color": "#eea638",
                "continent": "asia",
                "x": 4432.01553897559,
                "y": 71,
                "value": 21117690
            },
            {
                "title": "Taiwan",
                "id": "TW",
                "color": "#eea638",
                "continent": "asia",
                "x": 32840.8623523232,
                "y": 79.45,
                "value": 23114000
            },
            {
                "title": "Tajikistan",
                "id": "TJ",
                "color": "#eea638",
                "continent": "asia",
                "x": 1952.10042735043,
                "y": 67.118,
                "value": 7078755
            },
            {
                "title": "Tanzania",
                "id": "TZ",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 1330.05614548839,
                "y": 60.885,
                "value": 47656367
            },
            {
                "title": "Thailand",
                "id": "TH",
                "color": "#eea638",
                "continent": "asia",
                "x": 8451.15964058768,
                "y": 74.225,
                "value": 69892142
            },
            {
                "title": "Timor-Leste",
                "id": "TL",
                "color": "#eea638",
                "continent": "asia",
                "x": 3466.08281224683,
                "y": 67.033,
                "value": 1187194
            },
            {
                "title": "Togo",
                "id": "TG",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 975.396852535221,
                "y": 56.198,
                "value": 6283092
            },
            {
                "title": "Trinidad and Tobago",
                "id": "TT",
                "color": "#a7a737",
                "continent": "north_america",
                "x": 17182.0954558471,
                "y": 69.761,
                "value": 1350999
            },
            {
                "title": "Tunisia",
                "id": "TN",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 7620.47056462131,
                "y": 75.632,
                "value": 10704948
            },
            {
                "title": "Turkey",
                "id": "TR",
                "color": "#d8854f",
                "continent": "europe",
                "x": 9287.29312549815,
                "y": 74.938,
                "value": 74508771
            },
            {
                "title": "Turkmenistan",
                "id": "TM",
                "color": "#eea638",
                "continent": "asia",
                "x": 7921.2740619558,
                "y": 65.299,
                "value": 5169660
            },
            {
                "title": "Uganda",
                "id": "UG",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 1251.09807015907,
                "y": 58.668,
                "value": 35620977
            },
            {
                "title": "Ukraine",
                "id": "UA",
                "color": "#d8854f",
                "continent": "europe",
                "x": 6389.58597273257,
                "y": 68.414,
                "value": 44940268
            },
            {
                "title": "United Arab Emirates",
                "id": "AE",
                "color": "#eea638",
                "continent": "asia",
                "x": 31980.24143802,
                "y": 76.671,
                "value": 8105873
            },
            {
                "title": "United Kingdom",
                "id": "GB",
                "color": "#d8854f",
                "continent": "europe",
                "x": 31295.1431522074,
                "y": 80.396,
                "value": 62798099
            },
            {
                "title": "United States",
                "id": "US",
                "color": "#a7a737",
                "continent": "north_america",
                "x": 42296.2316492477,
                "y": 78.797,
                "value": 315791284
            },
            {
                "title": "Uruguay",
                "id": "UY",
                "color": "#86a965",
                "continent": "south_america",
                "x": 13179.2310803465,
                "y": 77.084,
                "value": 3391428
            },
            {
                "title": "Uzbekistan",
                "id": "UZ",
                "color": "#eea638",
                "continent": "asia",
                "x": 3117.27386553102,
                "y": 68.117,
                "value": 28077486
            },
            {
                "title": "Venezuela",
                "id": "VE",
                "color": "#86a965",
                "continent": "south_america",
                "x": 11685.1771941737,
                "y": 74.477,
                "value": 29890694
            },
            {
                "title": "West Bank and Gaza",
                "id": "PS",
                "color": "#eea638",
                "continent": "asia",
                "x": 4328.39115760087,
                "y": 73.018,
                "value": 4270791
            },
            {
                "title": "Vietnam",
                "id": "VN",
                "color": "#eea638",
                "continent": "asia",
                "x": 3073.64961158389,
                "y": 75.793,
                "value": 89730274
            },
            {
                "title": "Yemen, Rep.",
                "id": "YE",
                "color": "#eea638",
                "continent": "asia",
                "x": 2043.7877761328,
                "y": 62.923,
                "value": 25569263
            },
            {
                "title": "Zambia",
                "id": "ZM",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 1550.92385858124,
                "y": 57.037,
                "value": 13883577
            },
            {
                "title": "Zimbabwe",
                "id": "ZW",
                "color": "#de4c4f",
                "continent": "africa",
                "x": 545.344601005788,
                "y": 58.142,
                "value": 13013678
            }
        ]

    });
    // ++++++++++++++++++++++++++++++++++++++ Zoomable Bubble Chart ++++++++++++++++++++++++++++++++++++++

    // ====================================== Animated gauge ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        // create chart
        var chart = am4core.create("chartDivAnimatedGauge", am4charts.GaugeChart);
        chart.logo.disabled = true;
        chart.innerRadius = am4core.percent(82);

        /**
         * Normal axis
         */

        var axis = chart.xAxes.push(new am4charts.ValueAxis());
        axis.min = 0;
        axis.max = 100;
        axis.strictMinMax = true;
        axis.renderer.radius = am4core.percent(80);
        axis.renderer.inside = true;
        axis.renderer.line.strokeOpacity = 1;
        axis.renderer.ticks.template.disabled = false
        axis.renderer.ticks.template.strokeOpacity = 1;
        axis.renderer.ticks.template.length = 10;
        axis.renderer.grid.template.disabled = true;
        axis.renderer.labels.template.radius = 40;
        axis.renderer.labels.template.adapter.add("text", function(text) {
            return text + "%";
        })

        /**
         * Axis for ranges
         */

        var colorSet = new am4core.ColorSet();

        var axis2 = chart.xAxes.push(new am4charts.ValueAxis());
        axis2.min = 0;
        axis2.max = 100;
        axis2.strictMinMax = true;
        axis2.renderer.labels.template.disabled = true;
        axis2.renderer.ticks.template.disabled = true;
        axis2.renderer.grid.template.disabled = true;

        var range0 = axis2.axisRanges.create();
        range0.value = 0;
        range0.endValue = 50;
        range0.axisFill.fillOpacity = 1;
        range0.axisFill.fill = colorSet.getIndex(0);

        var range1 = axis2.axisRanges.create();
        range1.value = 50;
        range1.endValue = 100;
        range1.axisFill.fillOpacity = 1;
        range1.axisFill.fill = colorSet.getIndex(2);

        /**
         * Label
         */

        var label = chart.radarContainer.createChild(am4core.Label);
        label.isMeasured = false;
        label.fontSize = 45;
        label.x = am4core.percent(50);
        label.y = am4core.percent(100);
        label.horizontalCenter = "middle";
        label.verticalCenter = "bottom";
        label.text = "50%";


        /**
         * Hand
         */

        var hand = chart.hands.push(new am4charts.ClockHand());
        hand.axis = axis2;
        hand.innerRadius = am4core.percent(20);
        hand.startWidth = 10;
        hand.pin.disabled = true;
        hand.value = 50;

        hand.events.on("propertychanged", function(ev) {
        range0.endValue = ev.target.value;
        range1.value = ev.target.value;
        label.text = axis2.positionToValue(hand.currentPosition).toFixed(1);
            axis2.invalidate();
        });

        setInterval(function() {
            var value = Math.round(Math.random() * 100);
            var animation = new am4core.Animation(hand, {
                property: "value",
                to: value
            }, 1000, am4core.ease.cubicOut).start();
        }, 2000);

    });
    // ++++++++++++++++++++++++++++++++++++++ Animated gauge ++++++++++++++++++++++++++++++++++++++

    // ====================================== Gauge with bands ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        var chartMin = -50;
        var chartMax = 100;

        var data = {
            score: 52.7,
            gradingData: [
                {
                title: "Unsustainable",
                color: "#ee1f25",
                lowScore: -100,
                highScore: -20
                },
                {
                title: "Volatile",
                color: "#f04922",
                lowScore: -20,
                highScore: 0
                },
                {
                title: "Foundational",
                color: "#fdae19",
                lowScore: 0,
                highScore: 20
                },
                {
                title: "Developing",
                color: "#f3eb0c",
                lowScore: 20,
                highScore: 40
                },
                {
                title: "Maturing",
                color: "#b0d136",
                lowScore: 40,
                highScore: 60
                },
                {
                title: "Sustainable",
                color: "#54b947",
                lowScore: 60,
                highScore: 80
                },
                {
                title: "High Performing",
                color: "#0f9747",
                lowScore: 80,
                highScore: 100
                }
            ]
        };

        /**
        Grading Lookup
        */
        function lookUpGrade(lookupScore, grades) {
            // Only change code below this line
            for (var i = 0; i < grades.length; i++) {
                if (
                    grades[i].lowScore < lookupScore &&
                    grades[i].highScore >= lookupScore
                ) {
                    return grades[i];
                }
            }
            return null;
        }

        // create chart
        var chart = am4core.create("chartDivBandGauge", am4charts.GaugeChart);
        chart.logo.disabled = true;
        chart.hiddenState.properties.opacity = 0;
        chart.fontSize = 11;
        chart.innerRadius = am4core.percent(80);
        chart.resizable = true;

        /**
         * Normal axis
         */

        var axis = chart.xAxes.push(new am4charts.ValueAxis());
        axis.min = chartMin;
        axis.max = chartMax;
        axis.strictMinMax = true;
        axis.renderer.radius = am4core.percent(80);
        axis.renderer.inside = true;
        axis.renderer.line.strokeOpacity = 0.1;
        axis.renderer.ticks.template.disabled = false;
        axis.renderer.ticks.template.strokeOpacity = 1;
        axis.renderer.ticks.template.strokeWidth = 0.5;
        axis.renderer.ticks.template.length = 5;
        axis.renderer.grid.template.disabled = true;
        axis.renderer.labels.template.radius = am4core.percent(15);
        axis.renderer.labels.template.fontSize = "0.9em";

        /**
         * Axis for ranges
         */

        var axis2 = chart.xAxes.push(new am4charts.ValueAxis());
        axis2.min = chartMin;
        axis2.max = chartMax;
        axis2.strictMinMax = true;
        axis2.renderer.labels.template.disabled = true;
        axis2.renderer.ticks.template.disabled = true;
        axis2.renderer.grid.template.disabled = false;
        axis2.renderer.grid.template.opacity = 0.5;
        axis2.renderer.labels.template.bent = true;
        axis2.renderer.labels.template.fill = am4core.color("#000");
        axis2.renderer.labels.template.fontWeight = "bold";
        axis2.renderer.labels.template.fillOpacity = 0.3;



        /**
        Ranges
        */

        for (let grading of data.gradingData) {
            var range = axis2.axisRanges.create();
            range.axisFill.fill = am4core.color(grading.color);
            range.axisFill.fillOpacity = 0.8;
            range.axisFill.zIndex = -1;
            range.value = grading.lowScore > chartMin ? grading.lowScore : chartMin;
            range.endValue = grading.highScore < chartMax ? grading.highScore : chartMax;
            range.grid.strokeOpacity = 0;
            range.stroke = am4core.color(grading.color).lighten(-0.1);
            range.label.inside = true;
            range.label.text = grading.title.toUpperCase();
            range.label.inside = true;
            range.label.location = 0.5;
            range.label.inside = true;
            range.label.radius = am4core.percent(10);
            range.label.paddingBottom = -5; // ~half font size
            range.label.fontSize = "0.9em";
        }

        var matchingGrade = lookUpGrade(data.score, data.gradingData);

        /**
         * Label 1
         */

        var label = chart.radarContainer.createChild(am4core.Label);
        label.isMeasured = false;
        label.fontSize = "6em";
        label.x = am4core.percent(50);
        label.paddingBottom = 15;
        label.horizontalCenter = "middle";
        label.verticalCenter = "bottom";
        //label.dataItem = data;
        label.text = data.score.toFixed(1);
        //label.text = "{score}";
        label.fill = am4core.color(matchingGrade.color);

        /**
         * Label 2
         */

        var label2 = chart.radarContainer.createChild(am4core.Label);
        label2.isMeasured = false;
        label2.fontSize = "2em";
        label2.horizontalCenter = "middle";
        label2.verticalCenter = "bottom";
        label2.text = matchingGrade.title.toUpperCase();
        label2.fill = am4core.color(matchingGrade.color);


        /**
         * Hand
         */

        var hand = chart.hands.push(new am4charts.ClockHand());
        hand.axis = axis2;
        hand.innerRadius = am4core.percent(55);
        hand.startWidth = 8;
        hand.pin.disabled = true;
        hand.value = data.score;
        hand.fill = am4core.color("#444");
        hand.stroke = am4core.color("#000");

        hand.events.on("positionchanged", function(){
            label.text = axis2.positionToValue(hand.currentPosition).toFixed(1);
            var value2 = axis.positionToValue(hand.currentPosition);
            var matchingGrade = lookUpGrade(axis.positionToValue(hand.currentPosition), data.gradingData);
            label2.text = matchingGrade.title.toUpperCase();
            label2.fill = am4core.color(matchingGrade.color);
            label2.stroke = am4core.color(matchingGrade.color);  
            label.fill = am4core.color(matchingGrade.color);
        })

        setInterval(function() {
            var value = chartMin + Math.random() * (chartMax - chartMin);
            hand.showValue(value, 1000, am4core.ease.cubicOut);
        }, 2000);

    });
    // ++++++++++++++++++++++++++++++++++++++ Gauge with bands ++++++++++++++++++++++++++++++++++++++

    // ====================================== Gauge with gradient fill ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        // create chart
        var chart = am4core.create("chartDivGradientGauge", am4charts.GaugeChart);
        chart.logo.disabled = true;
        chart.innerRadius = -15;

        var axis = chart.xAxes.push(new am4charts.ValueAxis());
        axis.min = 0;
        axis.max = 100;
        axis.strictMinMax = true;

        var colorSet = new am4core.ColorSet();

        var gradient = new am4core.LinearGradient();
        gradient.stops.push({color:am4core.color("red")})
        gradient.stops.push({color:am4core.color("yellow")})
        gradient.stops.push({color:am4core.color("green")})

        axis.renderer.line.stroke = gradient;
        axis.renderer.line.strokeWidth = 15;
        axis.renderer.line.strokeOpacity = 1;

        axis.renderer.grid.template.disabled = true;

        var hand = chart.hands.push(new am4charts.ClockHand());
        hand.radius = am4core.percent(97);

        setInterval(function() {
            hand.showValue(Math.random() * 100, 1000, am4core.ease.cubicOut);
        }, 2000);

    });
    // ++++++++++++++++++++++++++++++++++++++ Gauge with gradient fill ++++++++++++++++++++++++++++++++++++++

    // ====================================== Radar Heat Map ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        var chart = am4core.create("chartDivRadarHeatMap", am4charts.RadarChart);
        chart.logo.disabled = true;
        chart.innerRadius = am4core.percent(30);
        chart.fontSize = 11;

        var xAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        var yAxis = chart.yAxes.push(new am4charts.CategoryAxis());
        yAxis.renderer.minGridDistance = 5;

        xAxis.renderer.labels.template.location = 0.5;
        xAxis.renderer.labels.template.bent = true;
        xAxis.renderer.labels.template.radius = 5;

        xAxis.dataFields.category = "hour";
        yAxis.dataFields.category = "weekday";

        xAxis.renderer.grid.template.disabled = true;
        xAxis.renderer.minGridDistance = 10;

        yAxis.renderer.grid.template.disabled = true;
        yAxis.renderer.inversed = true;

        // this makes the y axis labels to be bent. By default y Axis labels are regular AxisLabels, so we replace them with AxisLabelCircular
        // and call fixPosition for them to be bent
        var yAxisLabel = new am4charts.AxisLabelCircular();
        yAxisLabel.bent = true;
        yAxisLabel.events.on("validated", function(event){  
            event.target.fixPosition(-90, am4core.math.getDistance({x:event.target.pixelX, y:event.target.pixelY}) - 5);
            event.target.dx = -event.target.pixelX;
            event.target.dy = -event.target.pixelY;
        })
        yAxis.renderer.labels.template = yAxisLabel;

        var series = chart.series.push(new am4charts.RadarColumnSeries());
        series.dataFields.categoryX = "hour";
        series.dataFields.categoryY = "weekday";
        series.dataFields.value = "value";
        series.sequencedInterpolation = true;

        var columnTemplate = series.columns.template;
        columnTemplate.strokeWidth = 2;
        columnTemplate.strokeOpacity = 1;
        columnTemplate.stroke = am4core.color("#ffffff");
        columnTemplate.tooltipText = "{weekday}, {hour}: {value.workingValue.formatNumber('#.')}";
        columnTemplate.width = am4core.percent(100);
        columnTemplate.height = am4core.percent(100);

        chart.seriesContainer.zIndex = -5;

        columnTemplate.hiddenState.properties.opacity = 0;

        // heat rule, this makes columns to change color depending on value
        series.heatRules.push({ target: columnTemplate, property: "fill", min: am4core.color("#fffb77"), max: am4core.color("#fe131a") });

        // heat legend

        var heatLegend = chart.bottomAxesContainer.createChild(am4charts.HeatLegend);
        heatLegend.width = am4core.percent(100);
        heatLegend.series = series;
        heatLegend.valueAxis.renderer.labels.template.fontSize = 9;
        heatLegend.valueAxis.renderer.minGridDistance = 30;

        // heat legend behavior
        series.columns.template.events.on("over", function (event) {
            handleHover(event.target);
        })

        series.columns.template.events.on("hit", function (event) {
            handleHover(event.target);
        })

        function handleHover(column) {
            if (!isNaN(column.dataItem.value)) {
                heatLegend.valueAxis.showTooltipAt(column.dataItem.value)
            }
            else {
                heatLegend.valueAxis.hideTooltip();
            }
        }

        series.columns.template.events.on("out", function (event) {
            heatLegend.valueAxis.hideTooltip();
        })

        chart.data = [
            {
                "hour": "12pm",
                "weekday": "Sunday",
                "value": 2990
            },
            {
                "hour": "1am",
                "weekday": "Sunday",
                "value": 2520
            },
            {
                "hour": "2am",
                "weekday": "Sunday",
                "value": 2334
            },
            {
                "hour": "3am",
                "weekday": "Sunday",
                "value": 2230
            },
            {
                "hour": "4am",
                "weekday": "Sunday",
                "value": 2325
            },
            {
                "hour": "5am",
                "weekday": "Sunday",
                "value": 2019
            },
            {
                "hour": "6am",
                "weekday": "Sunday",
                "value": 2128
            },
            {
                "hour": "7am",
                "weekday": "Sunday",
                "value": 2246
            },
            {
                "hour": "8am",
                "weekday": "Sunday",
                "value": 2421
            },
            {
                "hour": "9am",
                "weekday": "Sunday",
                "value": 2788
            },
            {
                "hour": "10am",
                "weekday": "Sunday",
                "value": 2959
            },
            {
                "hour": "11am",
                "weekday": "Sunday",
                "value": 3018
            },
            {
                "hour": "12am",
                "weekday": "Sunday",
                "value": 3154
            },
            {
                "hour": "1pm",
                "weekday": "Sunday",
                "value": 3172
            },
            {
                "hour": "2pm",
                "weekday": "Sunday",
                "value": 3368
            },
            {
                "hour": "3pm",
                "weekday": "Sunday",
                "value": 3464
            },
            {
                "hour": "4pm",
                "weekday": "Sunday",
                "value": 3746
            },
            {
                "hour": "5pm",
                "weekday": "Sunday",
                "value": 3656
            },
            {
                "hour": "6pm",
                "weekday": "Sunday",
                "value": 3336
            },
            {
                "hour": "7pm",
                "weekday": "Sunday",
                "value": 3292
            },
            {
                "hour": "8pm",
                "weekday": "Sunday",
                "value": 3269
            },
            {
                "hour": "9pm",
                "weekday": "Sunday",
                "value": 3300
            },
            {
                "hour": "10pm",
                "weekday": "Sunday",
                "value": 3403
            },
            {
                "hour": "11pm",
                "weekday": "Sunday",
                "value": 3323
            },
            {
                "hour": "12pm",
                "weekday": "Monday",
                "value": 3346
            },
            {
                "hour": "1am",
                "weekday": "Monday",
                "value": 2725
            },
            {
                "hour": "2am",
                "weekday": "Monday",
                "value": 3052
            },
            {
                "hour": "3am",
                "weekday": "Monday",
                "value": 3876
            },
            {
                "hour": "4am",
                "weekday": "Monday",
                "value": 4453
            },
            {
                "hour": "5am",
                "weekday": "Monday",
                "value": 3972
            },
            {
                "hour": "6am",
                "weekday": "Monday",
                "value": 4644
            },
            {
                "hour": "7am",
                "weekday": "Monday",
                "value": 5715
            },
            {
                "hour": "8am",
                "weekday": "Monday",
                "value": 7080
            },
            {
                "hour": "9am",
                "weekday": "Monday",
                "value": 8022
            },
            {
                "hour": "10am",
                "weekday": "Monday",
                "value": 8446
            },
            {
                "hour": "11am",
                "weekday": "Monday",
                "value": 9313
            },
            {
                "hour": "12am",
                "weekday": "Monday",
                "value": 9011
            },
            {
                "hour": "1pm",
                "weekday": "Monday",
                "value": 8508
            },
            {
                "hour": "2pm",
                "weekday": "Monday",
                "value": 8515
            },
            {
                "hour": "3pm",
                "weekday": "Monday",
                "value": 8399
            },
            {
                "hour": "4pm",
                "weekday": "Monday",
                "value": 8649
            },
            {
                "hour": "5pm",
                "weekday": "Monday",
                "value": 7869
            },
            {
                "hour": "6pm",
                "weekday": "Monday",
                "value": 6933
            },
            {
                "hour": "7pm",
                "weekday": "Monday",
                "value": 5969
            },
            {
                "hour": "8pm",
                "weekday": "Monday",
                "value": 5552
            },
            {
                "hour": "9pm",
                "weekday": "Monday",
                "value": 5434
            },
            {
                "hour": "10pm",
                "weekday": "Monday",
                "value": 5070
            },
            {
                "hour": "11pm",
                "weekday": "Monday",
                "value": 4851
            },
            {
                "hour": "12pm",
                "weekday": "Tuesday",
                "value": 4468
            },
            {
                "hour": "1am",
                "weekday": "Tuesday",
                "value": 3306
            },
            {
                "hour": "2am",
                "weekday": "Tuesday",
                "value": 3906
            },
            {
                "hour": "3am",
                "weekday": "Tuesday",
                "value": 4413
            },
            {
                "hour": "4am",
                "weekday": "Tuesday",
                "value": 4726
            },
            {
                "hour": "5am",
                "weekday": "Tuesday",
                "value": 4584
            },
            {
                "hour": "6am",
                "weekday": "Tuesday",
                "value": 5717
            },
            {
                "hour": "7am",
                "weekday": "Tuesday",
                "value": 6504
            },
            {
                "hour": "8am",
                "weekday": "Tuesday",
                "value": 8104
            },
            {
                "hour": "9am",
                "weekday": "Tuesday",
                "value": 8813
            },
            {
                "hour": "10am",
                "weekday": "Tuesday",
                "value": 9278
            },
            {
                "hour": "11am",
                "weekday": "Tuesday",
                "value": 10425
            },
            {
                "hour": "12am",
                "weekday": "Tuesday",
                "value": 10137
            },
            {
                "hour": "1pm",
                "weekday": "Tuesday",
                "value": 9290
            },
            {
                "hour": "2pm",
                "weekday": "Tuesday",
                "value": 9255
            },
            {
                "hour": "3pm",
                "weekday": "Tuesday",
                "value": 9614
            },
            {
                "hour": "4pm",
                "weekday": "Tuesday",
                "value": 9713
            },
            {
                "hour": "5pm",
                "weekday": "Tuesday",
                "value": 9667
            },
            {
                "hour": "6pm",
                "weekday": "Tuesday",
                "value": 8774
            },
            {
                "hour": "7pm",
                "weekday": "Tuesday",
                "value": 8649
            },
            {
                "hour": "8pm",
                "weekday": "Tuesday",
                "value": 9937
            },
            {
                "hour": "9pm",
                "weekday": "Tuesday",
                "value": 10286
            },
            {
                "hour": "10pm",
                "weekday": "Tuesday",
                "value": 9175
            },
            {
                "hour": "11pm",
                "weekday": "Tuesday",
                "value": 8581
            },
            {
                "hour": "12pm",
                "weekday": "Wednesday",
                "value": 8145
            },
            {
                "hour": "1am",
                "weekday": "Wednesday",
                "value": 7177
            },
            {
                "hour": "2am",
                "weekday": "Wednesday",
                "value": 5657
            },
            {
                "hour": "3am",
                "weekday": "Wednesday",
                "value": 6802
            },
            {
                "hour": "4am",
                "weekday": "Wednesday",
                "value": 8159
            },
            {
                "hour": "5am",
                "weekday": "Wednesday",
                "value": 8449
            },
            {
                "hour": "6am",
                "weekday": "Wednesday",
                "value": 9453
            },
            {
                "hour": "7am",
                "weekday": "Wednesday",
                "value": 9947
            },
            {
                "hour": "8am",
                "weekday": "Wednesday",
                "value": 11471
            },
            {
                "hour": "9am",
                "weekday": "Wednesday",
                "value": 12492
            },
            {
                "hour": "10am",
                "weekday": "Wednesday",
                "value": 9388
            },
            {
                "hour": "11am",
                "weekday": "Wednesday",
                "value": 9928
            },
            {
                "hour": "12am",
                "weekday": "Wednesday",
                "value": 9644
            },
            {
                "hour": "1pm",
                "weekday": "Wednesday",
                "value": 9034
            },
            {
                "hour": "2pm",
                "weekday": "Wednesday",
                "value": 8964
            },
            {
                "hour": "3pm",
                "weekday": "Wednesday",
                "value": 9069
            },
            {
                "hour": "4pm",
                "weekday": "Wednesday",
                "value": 8898
            },
            {
                "hour": "5pm",
                "weekday": "Wednesday",
                "value": 8322
            },
            {
                "hour": "6pm",
                "weekday": "Wednesday",
                "value": 6909
            },
            {
                "hour": "7pm",
                "weekday": "Wednesday",
                "value": 5810
            },
            {
                "hour": "8pm",
                "weekday": "Wednesday",
                "value": 5151
            },
            {
                "hour": "9pm",
                "weekday": "Wednesday",
                "value": 4911
            },
            {
                "hour": "10pm",
                "weekday": "Wednesday",
                "value": 4487
            },
            {
                "hour": "11pm",
                "weekday": "Wednesday",
                "value": 4118
            },
            {
                "hour": "12pm",
                "weekday": "Thursday",
                "value": 3689
            },
            {
                "hour": "1am",
                "weekday": "Thursday",
                "value": 3081
            },
            {
                "hour": "2am",
                "weekday": "Thursday",
                "value": 6525
            },
            {
                "hour": "3am",
                "weekday": "Thursday",
                "value": 6228
            },
            {
                "hour": "4am",
                "weekday": "Thursday",
                "value": 6917
            },
            {
                "hour": "5am",
                "weekday": "Thursday",
                "value": 6568
            },
            {
                "hour": "6am",
                "weekday": "Thursday",
                "value": 6405
            },
            {
                "hour": "7am",
                "weekday": "Thursday",
                "value": 8106
            },
            {
                "hour": "8am",
                "weekday": "Thursday",
                "value": 8542
            },
            {
                "hour": "9am",
                "weekday": "Thursday",
                "value": 8501
            },
            {
                "hour": "10am",
                "weekday": "Thursday",
                "value": 8802
            },
            {
                "hour": "11am",
                "weekday": "Thursday",
                "value": 9420
            },
            {
                "hour": "12am",
                "weekday": "Thursday",
                "value": 8966
            },
            {
                "hour": "1pm",
                "weekday": "Thursday",
                "value": 8135
            },
            {
                "hour": "2pm",
                "weekday": "Thursday",
                "value": 8224
            },
            {
                "hour": "3pm",
                "weekday": "Thursday",
                "value": 8387
            },
            {
                "hour": "4pm",
                "weekday": "Thursday",
                "value": 8218
            },
            {
                "hour": "5pm",
                "weekday": "Thursday",
                "value": 7641
            },
            {
                "hour": "6pm",
                "weekday": "Thursday",
                "value": 6469
            },
            {
                "hour": "7pm",
                "weekday": "Thursday",
                "value": 5441
            },
            {
                "hour": "8pm",
                "weekday": "Thursday",
                "value": 4952
            },
            {
                "hour": "9pm",
                "weekday": "Thursday",
                "value": 4643
            },
            {
                "hour": "10pm",
                "weekday": "Thursday",
                "value": 4393
            },
            {
                "hour": "11pm",
                "weekday": "Thursday",
                "value": 4017
            },
            {
                "hour": "12pm",
                "weekday": "Friday",
                "value": 4022
            },
            {
                "hour": "1am",
                "weekday": "Friday",
                "value": 3063
            },
            {
                "hour": "2am",
                "weekday": "Friday",
                "value": 3638
            },
            {
                "hour": "3am",
                "weekday": "Friday",
                "value": 3968
            },
            {
                "hour": "4am",
                "weekday": "Friday",
                "value": 4070
            },
            {
                "hour": "5am",
                "weekday": "Friday",
                "value": 4019
            },
            {
                "hour": "6am",
                "weekday": "Friday",
                "value": 4548
            },
            {
                "hour": "7am",
                "weekday": "Friday",
                "value": 5465
            },
            {
                "hour": "8am",
                "weekday": "Friday",
                "value": 6909
            },
            {
                "hour": "9am",
                "weekday": "Friday",
                "value": 7706
            },
            {
                "hour": "10am",
                "weekday": "Friday",
                "value": 7867
            },
            {
                "hour": "11am",
                "weekday": "Friday",
                "value": 8615
            },
            {
                "hour": "12am",
                "weekday": "Friday",
                "value": 8218
            },
            {
                "hour": "1pm",
                "weekday": "Friday",
                "value": 7604
            },
            {
                "hour": "2pm",
                "weekday": "Friday",
                "value": 7429
            },
            {
                "hour": "3pm",
                "weekday": "Friday",
                "value": 7488
            },
            {
                "hour": "4pm",
                "weekday": "Friday",
                "value": 7493
            },
            {
                "hour": "5pm",
                "weekday": "Friday",
                "value": 6998
            },
            {
                "hour": "6pm",
                "weekday": "Friday",
                "value": 5941
            },
            {
                "hour": "7pm",
                "weekday": "Friday",
                "value": 5068
            },
            {
                "hour": "8pm",
                "weekday": "Friday",
                "value": 4636
            },
            {
                "hour": "9pm",
                "weekday": "Friday",
                "value": 4241
            },
            {
                "hour": "10pm",
                "weekday": "Friday",
                "value": 3858
            },
            {
                "hour": "11pm",
                "weekday": "Friday",
                "value": 3833
            },
            {
                "hour": "12pm",
                "weekday": "Saturday",
                "value": 3503
            },
            {
                "hour": "1am",
                "weekday": "Saturday",
                "value": 2842
            },
            {
                "hour": "2am",
                "weekday": "Saturday",
                "value": 2808
            },
            {
                "hour": "3am",
                "weekday": "Saturday",
                "value": 2399
            },
            {
                "hour": "4am",
                "weekday": "Saturday",
                "value": 2280
            },
            {
                "hour": "5am",
                "weekday": "Saturday",
                "value": 2139
            },
            {
                "hour": "6am",
                "weekday": "Saturday",
                "value": 2527
            },
            {
                "hour": "7am",
                "weekday": "Saturday",
                "value": 2940
            },
            {
                "hour": "8am",
                "weekday": "Saturday",
                "value": 3066
            },
            {
                "hour": "9am",
                "weekday": "Saturday",
                "value": 3494
            },
            {
                "hour": "10am",
                "weekday": "Saturday",
                "value": 3287
            },
            {
                "hour": "11am",
                "weekday": "Saturday",
                "value": 3416
            },
            {
                "hour": "12am",
                "weekday": "Saturday",
                "value": 3432
            },
            {
                "hour": "1pm",
                "weekday": "Saturday",
                "value": 3523
            },
            {
                "hour": "2pm",
                "weekday": "Saturday",
                "value": 3542
            },
            {
                "hour": "3pm",
                "weekday": "Saturday",
                "value": 3347
            },
            {
                "hour": "4pm",
                "weekday": "Saturday",
                "value": 3292
            },
            {
                "hour": "5pm",
                "weekday": "Saturday",
                "value": 3416
            },
            {
                "hour": "6pm",
                "weekday": "Saturday",
                "value": 3131
            },
            {
                "hour": "7pm",
                "weekday": "Saturday",
                "value": 3057
            },
            {
                "hour": "8pm",
                "weekday": "Saturday",
                "value": 3227
            },
            {
                "hour": "9pm",
                "weekday": "Saturday",
                "value": 3060
            },
            {
                "hour": "10pm",
                "weekday": "Saturday",
                "value": 2855
            },
            {
                "hour": "11pm",
                "weekday": "Saturday",
                "value": 2625
            }

        ];

    });
    // ++++++++++++++++++++++++++++++++++++++ Radar Heat Map ++++++++++++++++++++++++++++++++++++++

    // ====================================== Sankey Diagram ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        var chart = am4core.create("chartDivSankeyDiagram", am4charts.SankeyDiagram);
        chart.logo.disabled = true;
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

        chart.data = [
            { from: "A", to: "D", value: 10 },
            { from: "B", to: "D", value: 8 },
            { from: "B", to: "E", value: 4 },
            { from: "C", to: "E", value: 3 },
            { from: "D", to: "G", value: 5 },
            { from: "D", to: "I", value: 2 },
            { from: "D", to: "H", value: 3 },
            { from: "E", to: "H", value: 6 },    
            { from: "G", to: "J", value: 5 },
            { from: "I", to: "J", value: 1 },
            { from: "H", to: "J", value: 9 }    
        ];

        let hoverState = chart.links.template.states.create("hover");
        hoverState.properties.fillOpacity = 0.6;

        chart.dataFields.fromName = "from";
        chart.dataFields.toName = "to";
        chart.dataFields.value = "value";

        // for right-most label to fit
        chart.paddingRight = 30;

        // make nodes draggable
        var nodeTemplate = chart.nodes.template;
        nodeTemplate.inert = true;
        nodeTemplate.readerTitle = "Drag me!";
        nodeTemplate.showSystemTooltip = true;
        nodeTemplate.width = 20;

        // make nodes draggable
        var nodeTemplate = chart.nodes.template;
        nodeTemplate.readerTitle = "Click to show/hide or drag to rearrange";
        nodeTemplate.showSystemTooltip = true;
        nodeTemplate.cursorOverStyle = am4core.MouseCursorStyle.pointer

    });
    // ++++++++++++++++++++++++++++++++++++++ Sankey Diagram ++++++++++++++++++++++++++++++++++++++

    // ====================================== Funnel with Gradient Fill ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        let chart = am4core.create("chartDivGradientFunnel", am4charts.SlicedChart);
        chart.logo.disabled = true;

        chart.data = [
            {
                "name": "Stage #1",
                "value": 600
            }, {
                "name": "Stage #2",
                "value": 300
            }, {
                "name": "Stage #3",
                "value": 200
            }, {
                "name": "Stage #4",
                "value": 180
            }, {
                "name": "Stage #5",
                "value": 50
            }, {
                "name": "Stage #6",
                "value": 20
            }, {
                "name": "Stage #7",
                "value": 10
            }
        ];

        let series = chart.series.push(new am4charts.FunnelSeries());
        series.dataFields.value = "value";
        series.dataFields.category = "name";

        var fillModifier = new am4core.LinearGradientModifier();
        fillModifier.brightnesses = [-0.5, 1, -0.5];
        fillModifier.offsets = [0, 0.5, 1];
        series.slices.template.fillModifier = fillModifier;
        series.alignLabels = true;

        series.labels.template.text = "{category}: [bold]{value}[/]";

    });
    // ++++++++++++++++++++++++++++++++++++++ Funnel with Gradient Fill ++++++++++++++++++++++++++++++++++++++

    // ====================================== Multi-series Funnel/Pyramid ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        var chart = am4core.create("chartDivMultiSeriesFunnel", am4charts.SlicedChart);
        chart.logo.disabled = true;
        
        chart.data = [
            {
                "name": "Stage #1",
                "value1": 600,
                "value2": 450
            }, {
                "name": "Stage #2",
                "value1": 300,
                "value2": 400
            }, {
                "name": "Stage #3",
                "value1": 200,
                "value2": 290
            }, {
                "name": "Stage #4",
                "value1": 180,
                "value2": 100
            }, {
                "name": "Stage #5",
                "value1": 50,
                "value2": 50
            }, {
                "name": "Stage #6",
                "value1": 20,
                "value2": 20
            }, {
                "name": "Stage #7",
                "value1": 10,
                "value2": 10
            }
        ];

        var series1 = chart.series.push(new am4charts.FunnelSeries());
        series1.dataFields.value = "value2";
        series1.dataFields.category = "name";
        series1.labels.template.disabled = true;

        var series2 = chart.series.push(new am4charts.PyramidSeries());
        series2.dataFields.value = "value2";
        series2.dataFields.category = "name";
        series2.labels.template.disabled = true;

    });
    // ++++++++++++++++++++++++++++++++++++++ Multi-series Funnel/Pyramid ++++++++++++++++++++++++++++++++++++++

    // ====================================== Vertical Sankey Diagram ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        var chart = am4core.create("chartDivVerticalSankeyDiagram", am4charts.SankeyDiagram);
        chart.logo.disabled = true;

        chart.data = [
            { from: "Cash in the U.S.", color: "#00aea0"},
            { from: "Cash Overseas", color: "#000000"},

            { from: "Source", to: "Total non financial companies", value: 1768, color: "#5ea9e1", labelText: "[font-size:1.5em]2016 BREAKDOWN OF\nTHE U.S.CORPORATE CASH PILE\n\n[/]NON-FINANCIAL COMPANIES \n [bold]$1,768 Trillion[/b]", zIndex: 100 },

            { from: "Total non financial companies", to: "Non-tech companies", value: 907, color: "#5ea9e1", labelText: "NON-TECH COMPANIES\n [bold]$907 Billion[/]" },
            { from: "Total non financial companies", to: "Tech companies", value: 861, color: "#5ea9e1", labelText: "TECH COMPANIES\n [bold]861 Billion[/]" },

            { from: "Non-tech companies", to: "Cash in the U.S.", value: 324, color: "#5ea9e1", zIndex: 101 },
            { from: "Non-tech companies", to: "Cash Overseas", value: 584, color: "#5ea9e1" },

            { from: "Tech companies", to: "Rest of tech", value: 274, color: "#5ea9e1", labelText: "REST OF TECH\n[bold]$274 Billion[/]" },
            { from: "Tech companies", to: "Top 5 tech companies", value: 587, color: "#5ea9e1", labelText: "TOP 5 TECH COMPANIES\n[bold]$587 Billion[/]" },

            { from: "Rest of tech", to: "Cash in the U.S.", value: 74, color: "#5ea9e1", zIndex: 100 },
            { from: "Rest of tech", to: "Cash Overseas", value: 200, color: "#5ea9e1" },

            { from: "Top 5 tech companies", to: "Joytechs", value: 67, color: "#5ea9e1" },
            { from: "Joytechs", to: "Cash in the U.S.", value: 10, color: "#5ea9e1" },
            { from: "Joytechs", to: "Cash Overseas", value: 57, color: "#5ea9e1", center: "right", dy: -50, labelText: "JOYTECHS [bold]$67[/]B", labelLocation: 0, labelRotation: 0 },

            { from: "Top 5 tech companies", to: "Fireex", value: 68, color: "#5ea9e1" },
            { from: "Fireex", to: "Cash in the U.S.", value: 8, color: "#5ea9e1" },
            { from: "Fireex", to: "Cash Overseas", value: 60, color: "#5ea9e1", center: "right", dy: -50, labelText: "FIREEX [bold]$68[/]B", labelLocation: 0, labelRotation: 0 },

            { from: "Top 5 tech companies", to: "Globalworld", value: 85, color: "#5ea9e1" },
            { from: "Globalworld", to: "Cash in the U.S.", value: 10, color: "#5ea9e1" },
            { from: "Globalworld", to: "Cash Overseas", value: 75, color: "#5ea9e1", center: "right", dy: -50, labelText: "GLOBALWORLD [bold]$85[/]B", labelLocation: 0, labelRotation: 0 },

            { from: "Top 5 tech companies", to: "Betagate", value: 115, color: "#5ea9e1" },
            { from: "Betagate", to: "Cash in the U.S.", value: 10, color: "#5ea9e1" },
            { from: "Betagate", to: "Cash Overseas", value: 105, color: "#5ea9e1", center: "right", dy: -50, labelText: "BETAGATE [bold]$115[/]B", labelLocation: 0, labelRotation: 0 },

            { from: "Top 5 tech companies", to: "Apexi", value: 253, color: "#5ea9e1" },
            { from: "Apexi", to: "Cash in the U.S.", value: 23, color: "#5ea9e1" },
            { from: "Apexi", to: "Cash Overseas", value: 230, color: "#5ea9e1", center: "right", dy: -50, labelText: "APEXI [bold]$253[/]B", labelLocation: 0, labelRotation: 0 },

            { from: "Cash in the U.S.", color: "#00aea0", labelText: "CASH IN THE U.S.\n[bold]$460 BILLION", labelLocation: 0, value: 460, zIndex: 102, dy: -30 },
            { from: "Cash Overseas", color: "#000000", labelText: "[#5ea9e1 font-size:1.5em]CASH OVERSEAS\n[bold #5ea9e1 font-size:1.5em]$1,31 TRILLION", labelLocation: 0, value: 1310, dy: -30 }
        ];

        chart.minNodeSize = 0.001;
        chart.nodeAlign = "bottom";
        chart.paddingLeft = 80;
        chart.paddingRight = 80;
        chart.dataFields.fromName = "from";
        chart.dataFields.toName = "to";
        chart.dataFields.value = "value";
        chart.dataFields.color = "color";

        chart.orientation = "vertical";
        chart.sortBy = "none";

        chart.nodes.template.togglable = false;

        var linkTemplate = chart.links.template;
        linkTemplate.colorMode = "gradient";
        linkTemplate.fillOpacity = 0.95;

        linkTemplate.cursorOverStyle = am4core.MouseCursorStyle.pointer;
        linkTemplate.readerTitle = "drag me!";
        linkTemplate.showSystemTooltip = true;
        linkTemplate.tooltipText = "";
        linkTemplate.propertyFields.zIndex = "zIndex";
        linkTemplate.tension = 0.6;

        //dragging
        chart.links.template.events.on("down", function (event) {
        var fromNode = event.target.dataItem.fromNode;
        var toNode = event.target.dataItem.toNode;

        var distanceToFromNode = am4core.math.getDistance(event.pointer.point, { x: fromNode.pixelX, y: fromNode.pixelY });
        var distanceToToNode = Infinity;
        if (toNode) {
            distanceToToNode = am4core.math.getDistance(event.pointer.point, { x: toNode.pixelX, y: toNode.pixelY });
        }

        if (distanceToFromNode < distanceToToNode) {
            fromNode.dragStart(event.pointer);
        }
        else {
            toNode.dragStart(event.pointer);
        }
        })

        chart.nodes.template.draggable = true;
        chart.nodes.template.inert = true;
        chart.nodes.template.width = 0;
        chart.nodes.template.height = 0;
        chart.nodes.template.nameLabel.disabled = true;
        chart.nodes.template.clickable = false;

        var labelBullet = chart.links.template.bullets.push(new am4charts.LabelBullet());
        labelBullet.label.propertyFields.text = "labelText";
        labelBullet.propertyFields.locationX = "labelLocation";
        labelBullet.propertyFields.rotation = "labelRotation";
        labelBullet.label.rotation = -90;
        labelBullet.propertyFields.dy = "dy";
        labelBullet.label.propertyFields.horizontalCenter = "center";
        labelBullet.label.textAlign = "middle";

    });
    // ++++++++++++++++++++++++++++++++++++++ Vertical Sankey Diagram ++++++++++++++++++++++++++++++++++++++

    // ====================================== Vertically stacked axes chart ======================================
    am4core.ready(function() {

        // Themes 
        am4core.useTheme(am4themes_animated);

        var chart = am4core.create("chartDivVerticallyStackedAxesChart", am4charts.XYChart);
        chart.logo.disabled = true;

        var data = [];
        var price = 100;
        var quantity = 1000;
        for (var i = 0; i < 300; i++) {
            price += Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 100);
            quantity += Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 1000);
            data.push({ date: new Date(2000, 1, i), price: price, quantity: quantity });
        }

        var interfaceColors = new am4core.InterfaceColorSet();

        chart.data = data;
        // the following line makes value axes to be arranged vertically.
        chart.leftAxesContainer.layout = "vertical";

        // uncomment this line if you want to change order of axes
        //chart.bottomAxesContainer.reverseOrder = true;

        var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        dateAxis.renderer.grid.template.location = 0;
        dateAxis.renderer.ticks.template.length = 8;
        dateAxis.renderer.ticks.template.strokeOpacity = 0.1;
        dateAxis.renderer.grid.template.disabled = true;
        dateAxis.renderer.ticks.template.disabled = false;
        dateAxis.renderer.ticks.template.strokeOpacity = 0.2;

        // these two lines makes the axis to be initially zoomed-in
        //dateAxis.start = 0.7;
        //dateAxis.keepSelection = true;

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.tooltip.disabled = true;
        valueAxis.zIndex = 1;
        valueAxis.renderer.baseGrid.disabled = true;

        // Set up axis
        valueAxis.renderer.inside = true;
        valueAxis.height = am4core.percent(60);
        valueAxis.renderer.labels.template.verticalCenter = "bottom";
        valueAxis.renderer.labels.template.padding(2,2,2,2);
        //valueAxis.renderer.maxLabelPosition = 0.95;
        valueAxis.renderer.fontSize = "0.8em"

        // uncomment these lines to fill plot area of this axis with some color
        valueAxis.renderer.gridContainer.background.fill = interfaceColors.getFor("alternativeBackground");
        valueAxis.renderer.gridContainer.background.fillOpacity = 0.05;


        var series = chart.series.push(new am4charts.LineSeries());
        series.dataFields.dateX = "date";
        series.dataFields.valueY = "price";
        series.tooltipText = "{valueY.value}";
        series.name = "Series 1";

        var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis2.tooltip.disabled = true;

        // this makes gap between panels
        valueAxis2.marginTop = 30;
        valueAxis2.renderer.baseGrid.disabled = true;
        valueAxis2.renderer.inside = true;
        valueAxis2.height = am4core.percent(40);
        valueAxis2.zIndex = 3
        valueAxis2.renderer.labels.template.verticalCenter = "bottom";
        valueAxis2.renderer.labels.template.padding(2,2,2,2);
        //valueAxis2.renderer.maxLabelPosition = 0.95;
        valueAxis2.renderer.fontSize = "0.8em"

        // uncomment these lines to fill plot area of this axis with some color
        valueAxis2.renderer.gridContainer.background.fill = interfaceColors.getFor("alternativeBackground");
        valueAxis2.renderer.gridContainer.background.fillOpacity = 0.05;

        var series2 = chart.series.push(new am4charts.ColumnSeries());
        series2.columns.template.width = am4core.percent(50);
        series2.dataFields.dateX = "date";
        series2.dataFields.valueY = "quantity";
        series2.yAxis = valueAxis2;
        series2.tooltipText = "{valueY.value}";
        series2.name = "Series 2";

        chart.cursor = new am4charts.XYCursor();
        chart.cursor.xAxis = dateAxis;

        var scrollbarX = new am4charts.XYChartScrollbar();
        scrollbarX.series.push(series);
        scrollbarX.marginBottom = 20;
        chart.scrollbarX = scrollbarX;

    });
    // ++++++++++++++++++++++++++++++++++++++ Vertically stacked axes chart ++++++++++++++++++++++++++++++++++++++

    // ====================================== Multiple Value Axes ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivMultipleValueAxes", am4charts.XYChart);
        chart.logo.disabled = true;

        // Increase contrast by taking evey second color
        chart.colors.step = 2;

        // Add data
        chart.data = generateChartData();

        // Create axes
        var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        dateAxis.renderer.minGridDistance = 50;

        // Create series
        function createAxisAndSeries(field, name, opposite, bullet) {
            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            if(chart.yAxes.indexOf(valueAxis) != 0){
                valueAxis.syncWithAxis = chart.yAxes.getIndex(0);
            }
            
            var series = chart.series.push(new am4charts.LineSeries());
            series.dataFields.valueY = field;
            series.dataFields.dateX = "date";
            series.strokeWidth = 2;
            series.yAxis = valueAxis;
            series.name = name;
            series.tooltipText = "{name}: [bold]{valueY}[/]";
            series.tensionX = 0.8;
            series.showOnInit = true;
            
            var interfaceColors = new am4core.InterfaceColorSet();
            
            switch(bullet) {
                case "triangle":
                    var bullet = series.bullets.push(new am4charts.Bullet());
                    bullet.width = 12;
                    bullet.height = 12;
                    bullet.horizontalCenter = "middle";
                    bullet.verticalCenter = "middle";
                    
                    var triangle = bullet.createChild(am4core.Triangle);
                    triangle.stroke = interfaceColors.getFor("background");
                    triangle.strokeWidth = 2;
                    triangle.direction = "top";
                    triangle.width = 12;
                    triangle.height = 12;
                    break;
                case "rectangle":
                    var bullet = series.bullets.push(new am4charts.Bullet());
                    bullet.width = 10;
                    bullet.height = 10;
                    bullet.horizontalCenter = "middle";
                    bullet.verticalCenter = "middle";
                    
                    var rectangle = bullet.createChild(am4core.Rectangle);
                    rectangle.stroke = interfaceColors.getFor("background");
                    rectangle.strokeWidth = 2;
                    rectangle.width = 10;
                    rectangle.height = 10;
                    break;
                default:
                    var bullet = series.bullets.push(new am4charts.CircleBullet());
                    bullet.circle.stroke = interfaceColors.getFor("background");
                    bullet.circle.strokeWidth = 2;
                    break;
            }
            
            valueAxis.renderer.line.strokeOpacity = 1;
            valueAxis.renderer.line.strokeWidth = 2;
            valueAxis.renderer.line.stroke = series.stroke;
            valueAxis.renderer.labels.template.fill = series.stroke;
            valueAxis.renderer.opposite = opposite;
        }

        createAxisAndSeries("visits", "Visits", false, "circle");
        createAxisAndSeries("views", "Views", true, "triangle");
        createAxisAndSeries("hits", "Hits", true, "rectangle");

        // Add legend
        chart.legend = new am4charts.Legend();

        // Add cursor
        chart.cursor = new am4charts.XYCursor();

        // generate some random data, quite different range
        function generateChartData() {
            var chartData = [];
            var firstDate = new Date();
            firstDate.setDate(firstDate.getDate() - 100);
            firstDate.setHours(0, 0, 0, 0);

            var visits = 1600;
            var hits = 2900;
            var views = 8700;

            for (var i = 0; i < 15; i++) {
                // we create date objects here. In your data, you can have date strings
                // and then set format of your dates using chart.dataDateFormat property,
                // however when possible, use date objects, as this will speed up chart rendering.
                var newDate = new Date(firstDate);
                newDate.setDate(newDate.getDate() + i);

                visits += Math.round((Math.random()<0.5?1:-1)*Math.random()*10);
                hits += Math.round((Math.random()<0.5?1:-1)*Math.random()*10);
                views += Math.round((Math.random()<0.5?1:-1)*Math.random()*10);

                chartData.push({
                date: newDate,
                visits: visits,
                hits: hits,
                views: views
                });
            }
            return chartData;
        }

    });
    // ++++++++++++++++++++++++++++++++++++++ Multiple Value Axes ++++++++++++++++++++++++++++++++++++++

    // ====================================== Collapsible force-directed tree ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        var chart = am4core.create("chartDivCollapsibleForceDirectedTree", am4plugins_forceDirected.ForceDirectedTree);
        chart.logo.disabled = true;
        chart.legend = new am4charts.Legend();

        var networkSeries = chart.series.push(new am4plugins_forceDirected.ForceDirectedSeries())

        networkSeries.data = [{
        name: 'Flora',
        children: [{
            name: 'Black Tea', value: 1
        }, {
            name: 'Floral',
            children: [{
            name: 'Chamomile', value: 1
            }, {
            name: 'Rose', value: 1
            }, {
            name: 'Jasmine', value: 1
            }]
        }]
        }, {
        name: 'Fruity',
        children: [{
            name: 'Berry',
            children: [{
            name: 'Blackberry', value: 1
            }, {
            name: 'Raspberry', value: 1
            }, {
            name: 'Blueberry', value: 1
            }, {
            name: 'Strawberry', value: 1
            }]
        }, {
            name: 'Dried Fruit',
            children: [{
            name: 'Raisin', value: 1
            }, {
            name: 'Prune', value: 1
            }]
        }, {
            name: 'Other Fruit',
            children: [{
            name: 'Coconut', value: 1
            }, {
            name: 'Cherry', value: 1
            }, {
            name: 'Pomegranate', value: 1
            }, {
            name: 'Pineapple', value: 1
            }, {
            name: 'Grape', value: 1
            }, {
            name: 'Apple', value: 1
            }, {
            name: 'Peach', value: 1
            }, {
            name: 'Pear', value: 1
            }]
        }, {
            name: 'Citrus Fruit',
            children: [{
            name: 'Grapefruit', value: 1
            }, {
            name: 'Orange', value: 1
            }, {
            name: 'Lemon', value: 1
            }, {
            name: 'Lime', value: 1
            }]
        }]
        }, {
        name: 'Sour/Fermented',
        children: [{
            name: 'Sour',
            children: [{
            name: 'Sour Aromatics', value: 1
            }, {
            name: 'Acetic Acid', value: 1
            }, {
            name: 'Butyric Acid', value: 1
            }, {
            name: 'Isovaleric Acid', value: 1
            }, {
            name: 'Citric Acid', value: 1
            }, {
            name: 'Malic Acid', value: 1
            }]
        }, {
            name: 'Alcohol/Fremented',
            children: [{
            name: 'Winey', value: 1
            }, {
            name: 'Whiskey', value: 1
            }, {
            name: 'Fremented', value: 1
            }, {
            name: 'Overripe', value: 1
            }]
        }]
        }, {
        name: 'Green/Vegetative',
        children: [{
            name: 'Olive Oil', value: 1
        }, {
            name: 'Raw', value: 1
        }, {
            name: 'Green/Vegetative',
            children: [{
            name: 'Under-ripe', value: 1
            }, {
            name: 'Peapod', value: 1
            }, {
            name: 'Fresh', value: 1
            }, {
            name: 'Dark Green', value: 1
            }, {
            name: 'Vegetative', value: 1
            }, {
            name: 'Hay-like', value: 1
            }, {
            name: 'Herb-like', value: 1
            }]
        }, {
            name: 'Beany', value: 1
        }]
        }, {
        name: 'Other',
        children: [{
            name: 'Papery/Musty',
            children: [{
            name: 'Stale', value: 1
            }, {
            name: 'Cardboard', value: 1
            }, {
            name: 'Papery', value: 1
            }, {
            name: 'Woody', value: 1
            }, {
            name: 'Moldy/Damp', value: 1
            }, {
            name: 'Musty/Dusty', value: 1
            }, {
            name: 'Musty/Earthy', value: 1
            }, {
            name: 'Animalic', value: 1
            }, {
            name: 'Meaty Brothy', value: 1
            }, {
            name: 'Phenolic', value: 1
            }]
        }, {
            name: 'Chemical',
            children: [{
            name: 'Bitter', value: 1
            }, {
            name: 'Salty', value: 1
            }, {
            name: 'Medicinal', value: 1
            }, {
            name: 'Petroleum', value: 1
            }, {
            name: 'Skunky', value: 1
            }, {
            name: 'Rubber', value: 1
            }]
        }]
        }, {
        name: 'Roasted',
        children: [{
            name: 'Pipe Tobacco', value: 1
        }, {
            name: 'Tobacco', value: 1
        }, {
            name: 'Burnt',
            children: [{
            name: 'Acrid', value: 1
            }, {
            name: 'Ashy', value: 1
            }, {
            name: 'Smoky', value: 1
            }, {
            name: 'Brown, Roast', value: 1
            }]
        }, {
            name: 'Cereal',
            children: [{
            name: 'Grain', value: 1
            }, {
            name: 'Malt', value: 1
            }]
        }]
        }, {
        name: 'Spices',
        children: [{
            name: 'Pungent', value: 1
        }, {
            name: 'Pepper', value: 1
        }, {
            name: 'Brown Spice',
            children: [{
            name: 'Anise', value: 1
            }, {
            name: 'Nutmeg', value: 1
            }, {
            name: 'Cinnamon', value: 1
            }, {
            name: 'Clove', value: 1
            }]
        }]
        }, {
        name: 'Nutty/Cocoa',
        children: [{
            name: 'Nutty',
            children: [{
            name: 'Peanuts', value: 1
            }, {
            name: 'Hazelnut', value: 1
            }, {
            name: 'Almond', value: 1
            }]
        }, {
            name: 'Cocoa',
            children: [{
            name: 'Chocolate', value: 1
            }, {
            name: 'Dark Chocolate', value: 1
            }]
        }]
        }, {
        name: 'Sweet',
        children: [{
            name: 'Brown Sugar',
            children: [{
            name: 'Molasses', value: 1
            }, {
            name: 'Maple Syrup', value: 1
            }, {
            name: 'Caramelized', value: 1
            }, {
            name: 'Honey', value: 1
            }]
        }, {
            name: 'Vanilla', value: 1
        }, {
            name: 'Vanillin', value: 1
        }, {
            name: 'Overall Sweet', value: 1
        }, {
            name: 'Sweet Aromatics', value: 1
        }]
        }];

        networkSeries.dataFields.linkWith = "linkWith";
        networkSeries.dataFields.name = "name";
        networkSeries.dataFields.id = "name";
        networkSeries.dataFields.value = "value";
        networkSeries.dataFields.children = "children";

        networkSeries.nodes.template.tooltipText = "{name}";
        networkSeries.nodes.template.fillOpacity = 1;

        networkSeries.nodes.template.label.text = "{name}"
        networkSeries.fontSize = 8;
        networkSeries.maxLevels = 2;
        networkSeries.maxRadius = am4core.percent(6);
        networkSeries.manyBodyStrength = -16;
        networkSeries.nodes.template.label.hideOversized = true;
        networkSeries.nodes.template.label.truncate = true;

    });
    // ++++++++++++++++++++++++++++++++++++++ Collapsible force-directed tree ++++++++++++++++++++++++++++++++++++++

    // ====================================== Variance indicators ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivVarianceIndicators", am4charts.XYChart);
        chart.logo.disabled = true;

        // Add data
        chart.data = [{
            "year": "2011",
            "value": 600000
        }, {
            "year": "2012",
            "value": 900000
        }, {
            "year": "2013",
            "value": 180000
        }, {
            "year": "2014",
            "value": 600000
        }, {
            "year": "2015",
            "value": 350000
        }, {
            "year": "2016",
            "value": 600000
        }, {
            "year": "2017",
            "value": 670000
        }];

        // Populate data
        for (var i = 0; i < (chart.data.length - 1); i++) {
            chart.data[i].valueNext = chart.data[i + 1].value;
        }

        // Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "year";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.minGridDistance = 30;

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.min = 0;

        // Create series
        var series = chart.series.push(new am4charts.ColumnSeries());
        series.dataFields.valueY = "value";
        series.dataFields.categoryX = "year";

        // Add series for showing variance arrows
        var series2 = chart.series.push(new am4charts.ColumnSeries());
        series2.dataFields.valueY = "valueNext";
        series2.dataFields.openValueY = "value";
        series2.dataFields.categoryX = "year";
        series2.columns.template.width = 1;
        series2.fill = am4core.color("#555");
        series2.stroke = am4core.color("#555");

        // Add a triangle for arrow tip
        var arrow = series2.bullets.push(new am4core.Triangle);
        arrow.width = 10;
        arrow.height = 10;
        arrow.horizontalCenter = "middle";
        arrow.verticalCenter = "top";
        arrow.dy = -1;

        // Set up a rotation adapter which would rotate the triangle if its a negative change
        arrow.adapter.add("rotation", function(rotation, target) {
            return getVariancePercent(target.dataItem) < 0 ? 180 : rotation;
        });

        // Set up a rotation adapter which adjusts Y position
        arrow.adapter.add("dy", function(dy, target) {
            return getVariancePercent(target.dataItem) < 0 ? 1 : dy;
        });

        // Add a label
        var label = series2.bullets.push(new am4core.Label);
        label.padding(10, 10, 10, 10);
        label.text = "";
        label.fill = am4core.color("#0c0");
        label.strokeWidth = 0;
        label.horizontalCenter = "middle";
        label.verticalCenter = "bottom";
        label.fontWeight = "bolder";

        // Adapter for label text which calculates change in percent
        label.adapter.add("textOutput", function(text, target) {
            var percent = getVariancePercent(target.dataItem);
            return percent ? percent + "%" : text;
        });

        // Adapter which shifts the label if it's below the variance column
        label.adapter.add("verticalCenter", function(center, target) {
            return getVariancePercent(target.dataItem) < 0 ? "top" : center;
        });

        // Adapter which changes color of label to red
        label.adapter.add("fill", function(fill, target) {
            return getVariancePercent(target.dataItem) < 0 ? am4core.color("#c00") : fill;
        });

        function getVariancePercent(dataItem) {
            if (dataItem) {
                var value = dataItem.valueY;
                var openValue = dataItem.openValueY;
                var change = value - openValue;
                return Math.round(change / openValue * 100);
            }
            return 0;
        }

    });
    // ++++++++++++++++++++++++++++++++++++++ Variance indicators ++++++++++++++++++++++++++++++++++++++

    // ====================================== Hybrid drill-down Pie/Bar chart ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        /**
         * Source data
         */
        var data = [{
        "category": "Critical",
        "value": 89,
        "color": am4core.color("#dc4534"),
        "breakdown": [{
            "category": "Sales inquiries",
            "value": 29
        }, {
            "category": "Support requests",
            "value": 40
        }, {
            "category": "Bug reports",
            "value": 11
        }, {
            "category": "Other",
            "value": 9
        }]
        }, {
        "category": "Acceptable",
        "value": 71,
        "color": am4core.color("#d7a700"),
        "breakdown": [{
            "category": "Sales inquiries",
            "value": 22
        }, {
            "category": "Support requests",
            "value": 30
        }, {
            "category": "Bug reports",
            "value": 11
        }, {
            "category": "Other",
            "value": 10
        }]
        }, {
        "category": "Good",
        "value": 120,
        "color": am4core.color("#68ad5c"),
        "breakdown": [{
            "category": "Sales inquiries",
            "value": 60
        }, {
            "category": "Support requests",
            "value": 35
        }, {
            "category": "Bug reports",
            "value": 15
        }, {
            "category": "Other",
            "value": 10
        }]
        }]

        /**
         * Chart container
         */

        // Create chart instance
        var chart = am4core.create("chartDivHybridDrillDownPieBarChart", am4core.Container);
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
    // ++++++++++++++++++++++++++++++++++++++ Hybrid drill-down Pie/Bar chart ++++++++++++++++++++++++++++++++++++++

    // ====================================== Using SVG Filters ======================================
    am4core.ready(function() {

        // Themes 
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivSVGFilters", am4charts.XYChart);
        chart.logo.disabled = true;

        // Add data
        chart.data = [{
        "year": "2007",
        "value1": 1691,
        "value2": 737
        }, {
        "year": "2008",
        "value1": 1098,
        "value2": 680,
        "value3": 910
        }, {
        "year": "2009",
        "value1": 975,
        "value2": 664,
        "value3": 670
        }, {
        "year": "2010",
        "value1": 1246,
        "value2": 648,
        "value3": 930
        }, {
        "year": "2011",
        "value1": 1218,
        "value2": 637,
        "value3": 1010
        }, {
        "year": "2012",
        "value1": 1913,
        "value2": 133,
        "value3": 1770
        }, {
        "year": "2013",
        "value1": 1299,
        "value2": 621,
        "value3": 820
        }, {
        "year": "2014",
        "value1": 1110,
        "value2": 10,
        "value3": 1050
        }, {
        "year": "2015",
        "value1": 765,
        "value2": 232,
        "value3": 650
        }, {
        "year": "2016",
        "value1": 1145,
        "value2": 219,
        "value3": 780
        }, {
        "year": "2017",
        "value1": 1163,
        "value2": 201,
        "value3": 700
        }, {
        "year": "2018",
        "value1": 1780,
        "value2": 85,
        "value3": 1470
        }, {
        "year": "2019",
        "value1": 1580,
        "value2": 285
        }];

        // Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "year";
        categoryAxis.renderer.grid.template.disabled = true;
        categoryAxis.renderer.minGridDistance = 30;
        categoryAxis.startLocation = 0.5;
        categoryAxis.endLocation = 0.5;
        categoryAxis.renderer.minLabelPosition = 0.05;
        categoryAxis.renderer.maxLabelPosition = 0.95;


        var categoryAxisTooltip = categoryAxis.tooltip.background;
        categoryAxisTooltip.pointerLength = 0;
        categoryAxisTooltip.fillOpacity = 0.3;
        categoryAxisTooltip.filters.push(new am4core.BlurFilter).blur = 5;
        categoryAxis.tooltip.dy = 5;


        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.renderer.inside = true;
        valueAxis.renderer.grid.template.disabled = true;
        valueAxis.renderer.minLabelPosition = 0.05;
        valueAxis.renderer.maxLabelPosition = 0.95;

        var valueAxisTooltip = valueAxis.tooltip.background;
        valueAxisTooltip.pointerLength = 0;
        valueAxisTooltip.fillOpacity = 0.3;
        valueAxisTooltip.filters.push(new am4core.BlurFilter).blur = 5;


        // Create series
        var series1 = chart.series.push(new am4charts.LineSeries());
        series1.dataFields.categoryX = "year";
        series1.dataFields.valueY = "value1";
        series1.fillOpacity = 1;
        series1.stacked = true;

        var blur1 = new am4core.BlurFilter();
        blur1.blur = 20;
        series1.filters.push(blur1);

        var series2 = chart.series.push(new am4charts.LineSeries());
        series2.dataFields.categoryX = "year";
        series2.dataFields.valueY = "value2";
        series2.fillOpacity = 1;
        series2.stacked = true;

        var blur2 = new am4core.BlurFilter();
        blur2.blur = 20;
        series2.filters.push(blur2);

        var series3 = chart.series.push(new am4charts.LineSeries());
        series3.dataFields.categoryX = "year";
        series3.dataFields.valueY = "value3";
        series3.stroke = am4core.color("#fff");
        series3.strokeWidth = 2;
        series3.strokeDasharray = "3,3";
        series3.tooltipText = "{categoryX}\n---\n[bold font-size: 20]{valueY}[/]";
        series3.tooltip.pointerOrientation = "vertical";
        series3.tooltip.label.textAlign = "middle";

        var bullet3 = series3.bullets.push(new am4charts.CircleBullet())
        bullet3.circle.radius = 8;
        bullet3.fill = chart.colors.getIndex(3);
        bullet3.stroke = am4core.color("#fff");
        bullet3.strokeWidth = 3;

        var bullet3hover = bullet3.states.create("hover");
        bullet3hover.properties.scale = 1.2;

        var shadow3 = new am4core.DropShadowFilter();
        series3.filters.push(shadow3);

        chart.cursor = new am4charts.XYCursor();
        chart.cursor.lineX.disabled = true;
        chart.cursor.lineY.disabled = true;

    });
    // ++++++++++++++++++++++++++++++++++++++ Using SVG Filters ++++++++++++++++++++++++++++++++++++++

    // ====================================== Cylinder gauge ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivCylinderGauge", am4charts.XYChart3D);
        chart.logo.disabled = true;

        chart.titles.create().text = "Crude oil reserves";

        // Add data
        chart.data = [{
        "category": "2018 Q1",
        "value1": 30,
        "value2": 70
        }, {
        "category": "2018 Q2",
        "value1": 15,
        "value2": 85
        }, {
        "category": "2018 Q3",
        "value1": 40,
        "value2": 60
        }, {
        "category": "2018 Q4",
        "value1": 55,
        "value2": 45
        }];

        // Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "category";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.grid.template.strokeOpacity = 0;

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.renderer.grid.template.strokeOpacity = 0;
        valueAxis.min = -10;
        valueAxis.max = 110;
        valueAxis.strictMinMax = true;
        valueAxis.renderer.baseGrid.disabled = true;
        valueAxis.renderer.labels.template.adapter.add("text", function(text) {
        if ((text > 100) || (text < 0)) {
            return "";
        }
        else {
            return text + "%";
        }
        })

        // Create series
        var series1 = chart.series.push(new am4charts.ConeSeries());
        series1.dataFields.valueY = "value1";
        series1.dataFields.categoryX = "category";
        series1.columns.template.width = am4core.percent(80);
        series1.columns.template.fillOpacity = 0.9;
        series1.columns.template.strokeOpacity = 1;
        series1.columns.template.strokeWidth = 2;

        var series2 = chart.series.push(new am4charts.ConeSeries());
        series2.dataFields.valueY = "value2";
        series2.dataFields.categoryX = "category";
        series2.stacked = true;
        series2.columns.template.width = am4core.percent(80);
        series2.columns.template.fill = am4core.color("#000");
        series2.columns.template.fillOpacity = 0.1;
        series2.columns.template.stroke = am4core.color("#000");
        series2.columns.template.strokeOpacity = 0.2;
        series2.columns.template.strokeWidth = 2;

    });
    // ++++++++++++++++++++++++++++++++++++++ Cylinder gauge ++++++++++++++++++++++++++++++++++++++

    // ====================================== Pie Charts as Bullets ======================================
    am4core.ready(function() {

        // Themes 
        am4core.useTheme(am4themes_animated);

        var data = [{
        "country": "Lithuania",
        "units": 500,
        "pie": [{
            "value": 250,
            "title": "Cat #1"
        }, {
            "value": 150,
            "title": "Cat #2"
        }, {
            "value": 100,
            "title": "Cat #3"
        }]
        }, {
        "country": "Czechia",
        "units": 300,
        "pie": [{
            "value": 80,
            "title": "Cat #1"
        }, {
            "value": 130,
            "title": "Cat #2"
        }, {
            "value": 90,
            "title": "Cat #3"
        }]
        }, {
        "country": "Ireland",
        "units": 200,
        "pie": [{
            "value": 75,
            "title": "Cat #1"
        }, {
            "value": 55,
            "title": "Cat #2"
        }, {
            "value": 70,
            "title": "Cat #3"
        }]
        }];


        // Create chart instance
        var chart = am4core.create("chartDivPieChartAsBullet", am4charts.XYChart);
        chart.logo.disabled = true;
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

        // Add data
        chart.data = data;

        // Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "country";
        categoryAxis.renderer.grid.template.disabled = true;

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.title.text = "Units sold (M)";
        valueAxis.min = 0;
        valueAxis.renderer.baseGrid.disabled = true;
        valueAxis.renderer.grid.template.strokeOpacity = 0.07;

        // Create series
        var series = chart.series.push(new am4charts.ColumnSeries());
        series.dataFields.valueY = "units";
        series.dataFields.categoryX = "country";
        series.tooltip.pointerOrientation = "vertical";


        var columnTemplate = series.columns.template;
        // add tooltip on column, not template, so that slices could also have tooltip
        columnTemplate.column.tooltipText = "Series: {name}\nCategory: {categoryX}\nValue: {valueY}";
        columnTemplate.column.tooltipY = 0;
        columnTemplate.column.cornerRadiusTopLeft = 20;
        columnTemplate.column.cornerRadiusTopRight = 20;
        columnTemplate.strokeOpacity = 0;


        // as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
        columnTemplate.adapter.add("fill", function(fill, target) {
        var color = chart.colors.getIndex(target.dataItem.index * 3);
        return color;
        });

        // create pie chart as a column child
        var pieChart = series.columns.template.createChild(am4charts.PieChart);
        pieChart.width = am4core.percent(80);
        pieChart.height = am4core.percent(80);
        pieChart.align = "center";
        pieChart.valign = "middle";
        pieChart.dataFields.data = "pie";

        var pieSeries = pieChart.series.push(new am4charts.PieSeries());
        pieSeries.dataFields.value = "value";
        pieSeries.dataFields.category = "title";
        pieSeries.labels.template.disabled = true;
        pieSeries.ticks.template.disabled = true;
        pieSeries.slices.template.stroke = am4core.color("#ffffff");
        pieSeries.slices.template.strokeWidth = 1;
        pieSeries.slices.template.strokeOpacity = 0;

        pieSeries.slices.template.adapter.add("fill", function(fill, target) {
        return am4core.color("#ffffff")
        });

        pieSeries.slices.template.adapter.add("fillOpacity", function(fillOpacity, target) {
        return (target.dataItem.index + 1) * 0.2;
        });

        pieSeries.hiddenState.properties.startAngle = -90;
        pieSeries.hiddenState.properties.endAngle = 270;

        var data = [{
        "country": "Lithuania",
        "units": 500,
        "pie": [{
            "value": 250,
            "title": "Cat #1"
        }, {
            "value": 150,
            "title": "Cat #2"
        }, {
            "value": 100,
            "title": "Cat #3"
        }]
        }, {
        "country": "Czechia",
        "units": 300,
        "pie": [{
            "value": 80,
            "title": "Cat #1"
        }, {
            "value": 130,
            "title": "Cat #2"
        }, {
            "value": 90,
            "title": "Cat #3"
        }]
        }, {
        "country": "Ireland",
        "units": 30,
        "pie": [{
            "value": 75,
            "title": "Cat #1"
        }, {
            "value": 55,
            "title": "Cat #2"
        }, {
            "value": 70,
            "title": "Cat #3"
        }]
        }];


        // Create chart instance
        var chart = am4core.create("chartDivPieChartAsBullet", am4charts.XYChart);
        chart.logo.disabled = true;
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

        // Add data
        chart.data = data;

        // Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "country";
        categoryAxis.renderer.grid.template.disabled = true;

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.title.text = "Units sold (M)";
        valueAxis.min = 0;
        valueAxis.renderer.baseGrid.disabled = true;
        valueAxis.renderer.grid.template.strokeOpacity = 0.07;

        // Create series
        var series = chart.series.push(new am4charts.ColumnSeries());
        series.dataFields.valueY = "units";
        series.dataFields.categoryX = "country";
        series.tooltip.pointerOrientation = "vertical";


        var columnTemplate = series.columns.template;
        // add tooltip on column, not template, so that slices could also have tooltip
        columnTemplate.column.tooltipText = "Series: {name}\nCategory: {categoryX}\nValue: {valueY}";
        columnTemplate.column.tooltipY = 0;
        columnTemplate.column.cornerRadiusTopLeft = 20;
        columnTemplate.column.cornerRadiusTopRight = 20;
        columnTemplate.strokeOpacity = 0;


        // as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
        columnTemplate.adapter.add("fill", function(fill, target) {
        var color = chart.colors.getIndex(target.dataItem.index * 3);
        return color;
        });

        // create pie chart as a column child
        var pieChart = series.columns.template.createChild(am4charts.PieChart);
        pieChart.width = am4core.percent(80);
        pieChart.height = am4core.percent(80);
        pieChart.align = "center";
        pieChart.valign = "middle";
        pieChart.dataFields.data = "pie";

        var pieSeries = pieChart.series.push(new am4charts.PieSeries());
        pieSeries.dataFields.value = "value";
        pieSeries.dataFields.category = "title";
        pieSeries.labels.template.disabled = true;
        pieSeries.ticks.template.disabled = true;
        pieSeries.slices.template.strokeWidth = 1;

        pieSeries.slices.template.adapter.add("stroke", function(stroke, target) {
        return chart.colors.getIndex(target.parent.parent.dataItem.index * 3);
        });

        pieSeries.slices.template.adapter.add("fill", function(fill, target) {
        return am4core.color("#ffffff")
        });

        pieSeries.slices.template.adapter.add("fillOpacity", function(fillOpacity, target) {
        return (target.dataItem.index + 1) * 0.2;
        });

        pieSeries.hiddenState.properties.startAngle = -90;
        pieSeries.hiddenState.properties.endAngle = 270;

        // this moves the pie out of the column if column is too small
        pieChart.adapter.add("verticalCenter", function(verticalCenter, target) {
        var point = am4core.utils.spritePointToSprite({ x: 0, y: 0 }, target.seriesContainer, chart.plotContainer);
        point.y -= target.dy;

        if (point.y > chart.plotContainer.measuredHeight - 15) {
            target.dy = -target.seriesContainer.measuredHeight - 15;
        }
        else {
            target.dy = 0;
        }
        return verticalCenter
        })

    });
    // ++++++++++++++++++++++++++++++++++++++ Pie Charts as Bullets ++++++++++++++++++++++++++++++++++++++

    // ====================================== Stacked bar chart with negative values ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivStackedBarChartWithNegativeValues", am4charts.XYChart);
        chart.logo.disabled = true;

        // Add data
        chart.data = [{
        "age": "85+",
        "male": -0.1,
        "female": 0.3
        }, {
        "age": "80-54",
        "male": -0.2,
        "female": 0.3
        }, {
        "age": "75-79",
        "male": -0.3,
        "female": 0.6
        }, {
        "age": "70-74",
        "male": -0.5,
        "female": 0.8
        }, {
        "age": "65-69",
        "male": -0.8,
        "female": 1.0
        }, {
        "age": "60-64",
        "male": -1.1,
        "female": 1.3
        }, {
        "age": "55-59",
        "male": -1.7,
        "female": 1.9
        }, {
        "age": "50-54",
        "male": -2.2,
        "female": 2.5
        }, {
        "age": "45-49",
        "male": -2.8,
        "female": 3.0
        }, {
        "age": "40-44",
        "male": -3.4,
        "female": 3.6
        }, {
        "age": "35-39",
        "male": -4.2,
        "female": 4.1
        }, {
        "age": "30-34",
        "male": -5.2,
        "female": 4.8
        }, {
        "age": "25-29",
        "male": -5.6,
        "female": 5.1
        }, {
        "age": "20-24",
        "male": -5.1,
        "female": 5.1
        }, {
        "age": "15-19",
        "male": -3.8,
        "female": 3.8
        }, {
        "age": "10-14",
        "male": -3.2,
        "female": 3.4
        }, {
        "age": "5-9",
        "male": -4.4,
        "female": 4.1
        }, {
        "age": "0-4",
        "male": -5.0,
        "female": 4.8
        }];

        // Use only absolute numbers
        chart.numberFormatter.numberFormat = "#.#s";

        // Create axes
        var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "age";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.inversed = true;

        var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
        valueAxis.extraMin = 0.1;
        valueAxis.extraMax = 0.1;
        valueAxis.renderer.minGridDistance = 40;
        valueAxis.renderer.ticks.template.length = 5;
        valueAxis.renderer.ticks.template.disabled = false;
        valueAxis.renderer.ticks.template.strokeOpacity = 0.4;
        valueAxis.renderer.labels.template.adapter.add("text", function(text) {
        return text == "Male" || text == "Female" ? text : text + "%";
        })

        // Create series
        var male = chart.series.push(new am4charts.ColumnSeries());
        male.dataFields.valueX = "male";
        male.dataFields.categoryY = "age";
        male.clustered = false;

        var maleLabel = male.bullets.push(new am4charts.LabelBullet());
        maleLabel.label.text = "{valueX}%";
        maleLabel.label.hideOversized = false;
        maleLabel.label.truncate = false;
        maleLabel.label.horizontalCenter = "right";
        maleLabel.label.dx = -10;

        var female = chart.series.push(new am4charts.ColumnSeries());
        female.dataFields.valueX = "female";
        female.dataFields.categoryY = "age";
        female.clustered = false;

        var femaleLabel = female.bullets.push(new am4charts.LabelBullet());
        femaleLabel.label.text = "{valueX}%";
        femaleLabel.label.hideOversized = false;
        femaleLabel.label.truncate = false;
        femaleLabel.label.horizontalCenter = "left";
        femaleLabel.label.dx = 10;

        var maleRange = valueAxis.axisRanges.create();
        maleRange.value = -10;
        maleRange.endValue = 0;
        maleRange.label.text = "Male";
        maleRange.label.fill = chart.colors.list[0];
        maleRange.label.dy = 20;
        maleRange.label.fontWeight = '600';
        maleRange.grid.strokeOpacity = 1;
        maleRange.grid.stroke = male.stroke;

        var femaleRange = valueAxis.axisRanges.create();
        femaleRange.value = 0;
        femaleRange.endValue = 10;
        femaleRange.label.text = "Female";
        femaleRange.label.fill = chart.colors.list[1];
        femaleRange.label.dy = 20;
        femaleRange.label.fontWeight = '600';
        femaleRange.grid.strokeOpacity = 1;
        femaleRange.grid.stroke = female.stroke;

    });
    // ++++++++++++++++++++++++++++++++++++++ Stacked bar chart with negative values ++++++++++++++++++++++++++++++++++++++

    // ====================================== 3D Stacked Column Chart ======================================
    am4core.ready(function() {

        // Themes 
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDiv3DStackedColumnChart", am4charts.XYChart3D);
        chart.logo.disabled = true;

        // Add data
        chart.data = [{
            "country": "USA",
            "year2017": 3.5,
            "year2018": 4.2
        }, {
            "country": "UK",
            "year2017": 1.7,
            "year2018": 3.1
        }, {
            "country": "Canada",
            "year2017": 2.8,
            "year2018": 2.9
        }, {
            "country": "Japan",
            "year2017": 2.6,
            "year2018": 2.3
        }, {
            "country": "France",
            "year2017": 1.4,
            "year2018": 2.1
        }, {
            "country": "Brazil",
            "year2017": 2.6,
            "year2018": 4.9
        }, {
            "country": "Russia",
            "year2017": 6.4,
            "year2018": 7.2
        }, {
            "country": "India",
            "year2017": 8,
            "year2018": 7.1
        }, {
            "country": "China",
            "year2017": 9.9,
            "year2018": 10.1
        }];

        // Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "country";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.minGridDistance = 30;

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.title.text = "GDP growth rate";
        valueAxis.renderer.labels.template.adapter.add("text", function(text) {
        return text + "%";
        });

        // Create series
        var series = chart.series.push(new am4charts.ColumnSeries3D());
        series.dataFields.valueY = "year2017";
        series.dataFields.categoryX = "country";
        series.name = "Year 2017";
        series.clustered = false;
        series.columns.template.tooltipText = "GDP grow in {category} (2017): [bold]{valueY}[/]";
        series.columns.template.fillOpacity = 0.9;

        var series2 = chart.series.push(new am4charts.ColumnSeries3D());
        series2.dataFields.valueY = "year2018";
        series2.dataFields.categoryX = "country";
        series2.name = "Year 2018";
        series2.clustered = false;
        series2.columns.template.tooltipText = "GDP grow in {category} (2017): [bold]{valueY}[/]";

    });
    // ++++++++++++++++++++++++++++++++++++++ 3D Stacked Column Chart ++++++++++++++++++++++++++++++++++++++

    // ====================================== 100% Stacked Column Chart ======================================
    am4core.ready(function() {

        // Themes 
        am4core.useTheme(am4themes_animated);

        var chart = am4core.create("chartDiv100PercentStackedColumnChart", am4charts.XYChart);
        chart.logo.disabled = true;
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

        chart.data = [
        {
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

    // ====================================== Stacked waterfall chart ======================================
    am4core.ready(function() {

        // Themes 
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivStackedWaterfallChart", am4charts.XYChart);
        chart.logo.disabled = true;

        // Add data
        chart.data = [{
        "category": "Stage #1",
        "open1": 0,
        "close1": 83,
        "open2": 83,
        "close2": 128
        }, {
        "category": "Stage #2",
        "open1": 121,
        "close1": 128,
        "open2": 128,
        "close2": 128
        }, {
        "category": "Stage #3",
        "open1": 111,
        "close1": 114,
        "open2": 114,
        "close2": 121
        }, {
        "category": "Stage #4",
        "open1": 98,
        "close1": 108,
        "open2": 108,
        "close2": 111
        }, {
        "category": "Stage #5",
        "open1": 85,
        "close1": 96,
        "open2": 96,
        "close2": 98
        }, {
        "category": "Stage #6",
        "open1": 55,
        "close1": 70,
        "open2": 70,
        "close2": 85
        }, {
        "category": "Stage #7",
        "open1": 3,
        "close1": 36,
        "open2": 36,
        "close2": 55
        }, {
        "category": "Stage #8",
        "open1": 0,
        "close1": 2,
        "open2": 2,
        "close2": 3
        }];

        // Create axes
        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "category";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.minGridDistance = 30;
        categoryAxis.renderer.ticks.template.disabled = false;
        categoryAxis.renderer.ticks.template.strokeOpacity = 0.5;
        // categoryAxis.renderer.labels.template.rotation = -25;
        // categoryAxis.renderer.labels.template.horizontalCenter = "right";

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.calculateTotals = true;

        // Create series
        function createSeries(open, close, names, showSum) {
        var series = chart.series.push(new am4charts.ColumnSeries());
        series.dataFields.valueY = close;
        series.dataFields.openValueY = open;
        series.name = name;
        series.dataFields.categoryX = "category";
        series.clustered = false;
        series.strokeWidth = 0;
        series.columns.template.width = am4core.percent(90);
        
        var labelBullet = series.bullets.push(new am4charts.LabelBullet());
        labelBullet.label.hideOversized = true;
        labelBullet.label.fill = am4core.color("#fff");
        labelBullet.label.text = "{valueY}";
        labelBullet.label.adapter.add("text", function(text, target) {
            var val = Math.abs(target.dataItem.valueY - target.dataItem.openValueY);
            return val;
        });
        labelBullet.locationY = 0.5;
        
        if (showSum) {
            var sumBullet = series.bullets.push(new am4charts.LabelBullet());
            sumBullet.label.text = "{valueY.close}";
            sumBullet.verticalCenter = "bottom";
            sumBullet.dy = -8;
            sumBullet.label.adapter.add("text", function(text, target) {
            var val = Math.abs(target.dataItem.dataContext.close2 - target.dataItem.dataContext.open1);
            return val;
            });
        }
        }

        createSeries("open1", "close1", "High", false);
        createSeries("open2", "close2", "Medium", true);

    });
    // ++++++++++++++++++++++++++++++++++++++ Stacked waterfall chart ++++++++++++++++++++++++++++++++++++++

    // ====================================== Divergent stacked bars ======================================
    am4core.ready(function() {

        // Themes 
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivDivergentStackedBars", am4charts.XYChart);
        chart.logo.disabled = true;

        // Title
        var title = chart.titles.push(new am4core.Label());
        title.text = "Research tools used by students";
        title.fontSize = 25;
        title.marginBottom = 15;

        // Add data
        chart.data = [{
        "category": "Search engines",
        "negative1": -0.1,
        "negative2": -0.9,
        "positive1": 5,
        "positive2": 94
        }, {
        "category": "Online encyclopedias",
        "negative1": -2,
        "negative2": -4,
        "positive1": 19,
        "positive2": 75
        }, {
        "category": "Peers",
        "negative1": -2,
        "negative2": -10,
        "positive1": 46,
        "positive2": 42
        }, {
        "category": "Social media",
        "negative1": -2,
        "negative2": -13,
        "positive1": 33,
        "positive2": 52
        }, {
        "category": "Study guides",
        "negative1": -6,
        "negative2": -19,
        "positive1": 34,
        "positive2": 41
        }, {
        "category": "News websites",
        "negative1": -3,
        "negative2": -23,
        "positive1": 49,
        "positive2": 25
        }, {
        "category": "Textbooks",
        "negative1": -5,
        "negative2": -28,
        "positive1": 49,
        "positive2": 18
        }, {
        "category": "Librarian",
        "negative1": -14,
        "negative2": -34,
        "positive1": 37,
        "positive2": 16
        }, {
        "category": "Printed books",
        "negative1": -9,
        "negative2": -41,
        "positive1": 38,
        "positive2": 12
        }, {
        "category": "Databases",
        "negative1": -18,
        "negative2": -36,
        "positive1": 29,
        "positive2": 17
        }, {
        "category": "Student search engines",
        "negative1": -17,
        "negative2": -39,
        "positive1": 34,
        "positive2": 10
        }];


        // Create axes
        var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "category";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.inversed = true;
        categoryAxis.renderer.minGridDistance = 20;
        categoryAxis.renderer.axisFills.template.disabled = false;
        categoryAxis.renderer.axisFills.template.fillOpacity = 0.05;


        var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
        valueAxis.min = -100;
        valueAxis.max = 100;
        valueAxis.renderer.minGridDistance = 50;
        valueAxis.renderer.ticks.template.length = 5;
        valueAxis.renderer.ticks.template.disabled = false;
        valueAxis.renderer.ticks.template.strokeOpacity = 0.4;
        valueAxis.renderer.labels.template.adapter.add("text", function(text) {
        return text + "%";
        })

        // Legend
        chart.legend = new am4charts.Legend();
        chart.legend.position = "right";

        // Use only absolute numbers
        chart.numberFormatter.numberFormat = "#.#s";

        // Create series
        function createSeries(field, name, color) {
        var series = chart.series.push(new am4charts.ColumnSeries());
        series.dataFields.valueX = field;
        series.dataFields.categoryY = "category";
        series.stacked = true;
        series.name = name;
        series.stroke = color;
        series.fill = color;
        
        var label = series.bullets.push(new am4charts.LabelBullet);
        label.label.text = "{valueX}%";
        label.label.fill = am4core.color("#fff");
        label.label.strokeWidth = 0;
        label.label.truncate = false;
        label.label.hideOversized = true;
        label.locationX = 0.5;
        return series;
        }

        var interfaceColors = new am4core.InterfaceColorSet();
        var positiveColor = interfaceColors.getFor("positive");
        var negativeColor = interfaceColors.getFor("negative");

        createSeries("negative2", "Unlikely", negativeColor.lighten(0.5));
        createSeries("negative1", "Never", negativeColor);
        createSeries("positive1", "Sometimes", positiveColor.lighten(0.5));
        createSeries("positive2", "Very often", positiveColor);

        chart.legend.events.on("layoutvalidated", function(event){
        chart.legend.itemContainers.each((container)=>{
            if(container.dataItem.dataContext.name == "Never"){
            container.toBack();
            }
        })
        })

    });
    // ++++++++++++++++++++++++++++++++++++++ Divergent stacked bars ++++++++++++++++++++++++++++++++++++++

    // ====================================== Partitioned Bar chart ======================================
    am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // Create chart instance
        var chart = am4core.create("chartDivPartitionedBarChart", am4charts.XYChart);
        chart.logo.disabled = true;

        // Add data
        chart.data = [
        {
            "region": "Central",
            "state": "North Dakota",
            "sales": 920
        },
        {
            "region": "Central",
            "state": "South Dakota",
            "sales": 1317
        },
        {
            "region": "Central",
            "state": "Kansas",
            "sales": 2916
        },
        {
            "region": "Central",
            "state": "Iowa",
            "sales": 4577
        },
        {
            "region": "Central",
            "state": "Nebraska",
            "sales": 7464
        },
        {
            "region": "Central",
            "state": "Oklahoma",
            "sales": 19686
        },
        {
            "region": "Central",
            "state": "Missouri",
            "sales": 22207
        },
        {
            "region": "Central",
            "state": "Minnesota",
            "sales": 29865
        },
        {
            "region": "Central",
            "state": "Wisconsin",
            "sales": 32125
        },
        {
            "region": "Central",
            "state": "Indiana",
            "sales": 53549
        },
        {
            "region": "Central",
            "state": "Michigan",
            "sales": 76281
        },
        {
            "region": "Central",
            "state": "Illinois",
            "sales": 80162
        },
        {
            "region": "Central",
            "state": "Texas",
            "sales": 170187
        },
        {
            "region": "East",
            "state": "West Virginia",
            "sales": 1209
        },
        {
            "region": "East",
            "state": "Maine",
            "sales": 1270
        },
        {
            "region": "East",
            "state": "District of Columbia",
            "sales": 2866
        },
        {
            "region": "East",
            "state": "New Hampshire",
            "sales": 7294
        },
        {
            "region": "East",
            "state": "Vermont",
            "sales": 8929
        },
        {
            "region": "East",
            "state": "Connecticut",
            "sales": 13386
        },
        {
            "region": "East",
            "state": "Rhode Island",
            "sales": 22629
        },
        {
            "region": "East",
            "state": "Maryland",
            "sales": 23707
        },
        {
            "region": "East",
            "state": "Delaware",
            "sales": 27453
        },
        {
            "region": "East",
            "state": "Massachusetts",
            "sales": 28639
        },
        {
            "region": "East",
            "state": "New Jersey",
            "sales": 35763
        },
        {
            "region": "East",
            "state": "Ohio",
            "sales": 78253
        },
        {
            "region": "East",
            "state": "Pennsylvania",
            "sales": 116522
        },
        {
            "region": "East",
            "state": "New York",
            "sales": 310914
        },
        {
            "region": "South",
            "state": "South Carolina",
            "sales": 8483
        },
        {
            "region": "South",
            "state": "Louisiana",
            "sales": 9219
        },
        {
            "region": "South",
            "state": "Mississippi",
            "sales": 10772
        },
        {
            "region": "South",
            "state": "Arkansas",
            "sales": 11678
        },
        {
            "region": "South",
            "state": "Alabama",
            "sales": 19511
        },
        {
            "region": "South",
            "state": "Tennessee",
            "sales": 30662
        },
        {
            "region": "South",
            "state": "Kentucky",
            "sales": 36598
        },
        {
            "region": "South",
            "state": "Georgia",
            "sales": 49103
        },
        {
            "region": "South",
            "state": "North Carolina",
            "sales": 55604
        },
        {
            "region": "South",
            "state": "Virginia",
            "sales": 70641
        },
        {
            "region": "South",
            "state": "Florida",
            "sales": 89479
        },
        {
            "region": "West",
            "state": "Wyoming",
            "sales": 1603
        },
        {
            "region": "West",
            "state": "Idaho",
            "sales": 4380
        },
        {
            "region": "West",
            "state": "New Mexico",
            "sales": 4779
        },
        {
            "region": "West",
            "state": "Montana",
            "sales": 5589
        },
        {
            "region": "West",
            "state": "Utah",
            "sales": 11223
        },
        {
            "region": "West",
            "state": "Nevada",
            "sales": 16729
        },
        {
            "region": "West",
            "state": "Oregon",
            "sales": 17431
        },
        {
            "region": "West",
            "state": "Colorado",
            "sales": 32110
        },
        {
            "region": "West",
            "state": "Arizona",
            "sales": 35283
        },
        {
            "region": "West",
            "state": "Washington",
            "sales": 138656
        },
        {
            "region": "West",
            "state": "California",
            "sales": 457731
        }
        ];

        // Create axes
        var yAxis = chart.yAxes.push(new am4charts.CategoryAxis());
        yAxis.dataFields.category = "state";
        yAxis.renderer.grid.template.location = 0;
        yAxis.renderer.labels.template.fontSize = 10;
        yAxis.renderer.minGridDistance = 10;

        var xAxis = chart.xAxes.push(new am4charts.ValueAxis());

        // Create series
        var series = chart.series.push(new am4charts.ColumnSeries());
        series.dataFields.valueX = "sales";
        series.dataFields.categoryY = "state";
        series.columns.template.tooltipText = "{categoryY}: [bold]{valueX}[/]";
        series.columns.template.strokeWidth = 0;
        series.columns.template.adapter.add("fill", function(fill, target) {
        if (target.dataItem) {
            switch(target.dataItem.dataContext.region) {
            case "Central":
                return chart.colors.getIndex(0);
                break;
            case "East":
                return chart.colors.getIndex(1);
                break;
            case "South":
                return chart.colors.getIndex(2);
                break;
            case "West":
                return chart.colors.getIndex(3);
                break;
            }
        }
        return fill;
        });

        var axisBreaks = {};
        var legendData = [];

        // Add ranges
        function addRange(label, start, end, color) {
        var range = yAxis.axisRanges.create();
        range.category = start;
        range.endCategory = end;
        range.label.text = label;
        range.label.disabled = false;
        range.label.fill = color;
        range.label.location = 0;
        range.label.dx = -130;
        range.label.dy = 12;
        range.label.fontWeight = "bold";
        range.label.fontSize = 12;
        range.label.horizontalCenter = "left"
        range.label.inside = true;
        
        range.grid.stroke = am4core.color("#396478");
        range.grid.strokeOpacity = 1;
        range.tick.length = 200;
        range.tick.disabled = false;
        range.tick.strokeOpacity = 0.6;
        range.tick.stroke = am4core.color("#396478");
        range.tick.location = 0;
        
        range.locations.category = 1;
        var axisBreak = yAxis.axisBreaks.create();
        axisBreak.startCategory = start;
        axisBreak.endCategory = end;
        axisBreak.breakSize = 1;
        axisBreak.fillShape.disabled = true;
        axisBreak.startLine.disabled = true;
        axisBreak.endLine.disabled = true;
        axisBreaks[label] = axisBreak;  

        legendData.push({name:label, fill:color});
        }

        addRange("Central", "Texas", "North Dakota", chart.colors.getIndex(0));
        addRange("East", "New York", "West Virginia", chart.colors.getIndex(1));
        addRange("South", "Florida", "South Carolina", chart.colors.getIndex(2));
        addRange("West", "California", "Wyoming", chart.colors.getIndex(3));

        chart.cursor = new am4charts.XYCursor();


        var legend = new am4charts.Legend();
        legend.position = "right";
        legend.scrollable = true;
        legend.valign = "top";
        legend.reverseOrder = true;

        chart.legend = legend;
        legend.data = legendData;

        legend.itemContainers.template.events.on("toggled", function(event){
        var name = event.target.dataItem.dataContext.name;
        var axisBreak = axisBreaks[name];
        if(event.target.isActive){
            axisBreak.animate({property:"breakSize", to:0}, 1000, am4core.ease.cubicOut);
            yAxis.dataItems.each(function(dataItem){
            if(dataItem.dataContext.region == name){
                dataItem.hide(1000, 500);
            }
            })
            series.dataItems.each(function(dataItem){
            if(dataItem.dataContext.region == name){
                dataItem.hide(1000, 0, 0, ["valueX"]);
            }
            })    
        }
        else{
            axisBreak.animate({property:"breakSize", to:1}, 1000, am4core.ease.cubicOut);
            yAxis.dataItems.each(function(dataItem){
            if(dataItem.dataContext.region == name){
                dataItem.show(1000);
            }
            })  

            series.dataItems.each(function(dataItem){
            if(dataItem.dataContext.region == name){
                dataItem.show(1000, 0, ["valueX"]);
            }
            })        
        }
        })

    });
    // ++++++++++++++++++++++++++++++++++++++ Partitioned Bar chart ++++++++++++++++++++++++++++++++++++++

    // ====================================== Combined bullet/column and line graphs with multiple value axes ======================================
    am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // Create chart instance
        var chart = am4core.create("chartDivCombinedColumnAndLineChart", am4charts.XYChart);
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
    // ++++++++++++++++++++++++++++++++++++++ Combined bullet/column and line graphs with multiple value axes ++++++++++++++++++++++++++++++++++++++

    // ====================================== 3D Pie chart ======================================
    am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        var chart = am4core.create("chartDiv3DPieChart", am4charts.PieChart3D);
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
        chart.logo.disabled = true;

        chart.legend = new am4charts.Legend();

        chart.data = [
        {
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

    // ====================================== Angular Gauge With Two Axes ======================================
    am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // create chart
        var chart = am4core.create("chartDivAngularGaugeTwoAxes", am4charts.GaugeChart);
        chart.hiddenState.properties.opacity = 0;
        chart.logo.disabled = true;

        var axis = chart.xAxes.push(new am4charts.ValueAxis());
        axis.min = 0;
        axis.max = 160;
        axis.strictMinMax = true;
        axis.renderer.inside = true;
        //axis.renderer.ticks.template.inside = true;
        //axis.stroke = chart.colors.getIndex(3);
        axis.renderer.radius = am4core.percent(97);
        //axis.renderer.radius = 80;
        axis.renderer.line.strokeOpacity = 1;
        axis.renderer.line.strokeWidth = 5;
        axis.renderer.line.stroke = chart.colors.getIndex(0);
        axis.renderer.ticks.template.disabled = false
        axis.renderer.ticks.template.stroke = chart.colors.getIndex(0);
        axis.renderer.labels.template.radius = 35;
        axis.renderer.ticks.template.strokeOpacity = 1;
        axis.renderer.grid.template.disabled = true;
        axis.renderer.ticks.template.length = 10;
        axis.hiddenState.properties.opacity = 1;
        axis.hiddenState.properties.visible = true;
        axis.setStateOnChildren = true;
        axis.renderer.hiddenState.properties.endAngle = 180;

        var axis2 = chart.xAxes.push(new am4charts.ValueAxis());
        axis2.min = 0;
        axis2.max = 240;
        axis2.strictMinMax = true;

        axis2.renderer.line.strokeOpacity = 1;
        axis2.renderer.line.strokeWidth = 5;
        axis2.renderer.line.stroke = chart.colors.getIndex(3);
        axis2.renderer.ticks.template.stroke = chart.colors.getIndex(3);

        axis2.renderer.ticks.template.disabled = false
        axis2.renderer.ticks.template.strokeOpacity = 1;
        axis2.renderer.grid.template.disabled = true;
        axis2.renderer.ticks.template.length = 10;
        axis2.hiddenState.properties.opacity = 1;
        axis2.hiddenState.properties.visible = true;
        axis2.setStateOnChildren = true;
        axis2.renderer.hiddenState.properties.endAngle = 180;

        var hand = chart.hands.push(new am4charts.ClockHand());
        hand.fill = axis.renderer.line.stroke;
        hand.stroke = axis.renderer.line.stroke;
        hand.axis = axis;
        hand.pin.radius = 14;
        hand.startWidth = 10;

        var hand2 = chart.hands.push(new am4charts.ClockHand());
        hand2.fill = axis2.renderer.line.stroke;
        hand2.stroke = axis2.renderer.line.stroke;
        hand2.axis = axis2;
        hand2.pin.radius = 10;
        hand2.startWidth = 10;

        setInterval(function() {
        hand.showValue(Math.random() * 160, 1000, am4core.ease.cubicOut);
        label.text = Math.round(hand.value).toString();
        hand2.showValue(Math.random() * 160, 1000, am4core.ease.cubicOut);
        label2.text = Math.round(hand2.value).toString();
        }, 2000);

        var legend = new am4charts.Legend();
        legend.isMeasured = false;
        legend.y = am4core.percent(100);
        legend.verticalCenter = "bottom";
        legend.parent = chart.chartContainer;
        legend.data = [{
        "name": "Measurement #1",
        "fill": chart.colors.getIndex(0)
        }, {
        "name": "Measurement #2",
        "fill": chart.colors.getIndex(3)
        }];

        legend.itemContainers.template.events.on("hit", function(ev) {
        var index = ev.target.dataItem.index;

        if (!ev.target.isActive) {
            chart.hands.getIndex(index).hide();
            chart.xAxes.getIndex(index).hide();
            labelList.getIndex(index).hide();
        }
        else {
            chart.hands.getIndex(index).show();
            chart.xAxes.getIndex(index).show();
            labelList.getIndex(index).show();
        }
        });

        var labelList = new am4core.ListTemplate(new am4core.Label());
        labelList.template.isMeasured = false;
        labelList.template.background.strokeWidth = 2;
        labelList.template.fontSize = 25;
        labelList.template.padding(10, 20, 10, 20);
        labelList.template.y = am4core.percent(50);
        labelList.template.horizontalCenter = "middle";

        var label = labelList.create();
        label.parent = chart.chartContainer;
        label.x = am4core.percent(40);
        label.background.stroke = chart.colors.getIndex(0);
        label.fill = chart.colors.getIndex(0);
        label.text = "0";

        var label2 = labelList.create();
        label2.parent = chart.chartContainer;
        label2.x = am4core.percent(60);
        label2.background.stroke = chart.colors.getIndex(3);
        label2.fill = chart.colors.getIndex(3);
        label2.text = "0";

    });
    // ++++++++++++++++++++++++++++++++++++++ Angular Gauge With Two Axes ++++++++++++++++++++++++++++++++++++++

    // ====================================== Sunburst chart ======================================
    am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // create chart
        var chart = am4core.create("chartDivSunburstChart", am4plugins_sunburst.Sunburst);
        chart.padding(0,0,0,0);
        chart.radius = am4core.percent(98);
        chart.logo.disabled = true;

        chart.data = [{
        name: "First",
        children: [
            { name: "A1", value: 100 },
            { name: "A2", value: 60 }
        ]
        },
        {
        name: "Second",
        children: [
            { name: "B1", value: 135 },
            { name: "B2", value: 98 }
        ]
        },
        {
        name: "Third",
        children: [
            {
            name: "C1",
            children: [
                { name: "EE1", value: 130 },
                { name: "EE2", value: 87 },
                { name: "EE3", value: 55 }
            ]
            },
            { name: "C2", value: 148 },
            {
            name: "C3", children: [
                { name: "CC1", value: 53 },
                { name: "CC2", value: 30 }
            ]
            },
            { name: "C4", value: 26 }
        ]
        },
        {
        name: "Fourth",
        children: [
            { name: "D1", value: 415 },
            { name: "D2", value: 148 },
            { name: "D3", value: 89 }
        ]
        },
        {
        name: "Fifth",
        children: [
            {
            name: "E1",
            children: [
                { name: "EE1", value: 33 },
                { name: "EE2", value: 40 },
                { name: "EE3", value: 89 }
            ]
            },
            {
            name: "E2",
            value: 148
            }
        ]
        }];

        chart.colors.step = 2;
        chart.fontSize = 11;
        chart.innerRadius = am4core.percent(10);

        // define data fields
        chart.dataFields.value = "value";
        chart.dataFields.name = "name";
        chart.dataFields.children = "children";


        var level0SeriesTemplate = new am4plugins_sunburst.SunburstSeries();
        level0SeriesTemplate.hiddenInLegend = false;
        chart.seriesTemplates.setKey("0", level0SeriesTemplate)

        // this makes labels to be hidden if they don't fit
        level0SeriesTemplate.labels.template.truncate = true;
        level0SeriesTemplate.labels.template.hideOversized = true;

        level0SeriesTemplate.labels.template.adapter.add("rotation", function(rotation, target) {
        target.maxWidth = target.dataItem.slice.radius - target.dataItem.slice.innerRadius - 10;
        target.maxHeight = Math.abs(target.dataItem.slice.arc * (target.dataItem.slice.innerRadius + target.dataItem.slice.radius) / 2 * am4core.math.RADIANS);

        return rotation;
        })


        var level1SeriesTemplate = level0SeriesTemplate.clone();
        chart.seriesTemplates.setKey("1", level1SeriesTemplate)
        level1SeriesTemplate.fillOpacity = 0.75;
        level1SeriesTemplate.hiddenInLegend = true;

        var level2SeriesTemplate = level0SeriesTemplate.clone();
        chart.seriesTemplates.setKey("2", level2SeriesTemplate)
        level2SeriesTemplate.fillOpacity = 0.5;
        level2SeriesTemplate.hiddenInLegend = true;

        chart.legend = new am4charts.Legend();

    });
    // ++++++++++++++++++++++++++++++++++++++ Sunburst chart ++++++++++++++++++++++++++++++++++++++



</script>