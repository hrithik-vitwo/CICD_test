<?php
// console($chartSearchData);
$searchValue = "productQuantityWiseSalesOnMonth"; // Add this from ajax "?chart=" value
$index = array_search($searchValue, array_column($chartSearchData['data'], "chartName"));
$dashComponents = array();
if ($index !== false) {
    // echo "The index of '{$searchValue}' is '{$index}'.";
    $dashComponents = unserialize($chartSearchData['data'][$index]['components']);
}
?>
<!-- HTML -->
<!-- PRODUCT QUANTITY WISE SALES ON MONTH -->
<div class="col-md-6 col-sm-6 d-flex">
    <div class="card flex-fill productQuantityWiseSalesOnMonth">
        <div class="card-header">
            <div class="head-title">
                <button class="btn btn-primary pin-btn border" id="productQuantityWiseSalesOnMonth"><?= chartExistorNot('productQuantityWiseSalesOnMonth.php') ? 'Pinned' : 'Pin'; ?></button>
                <h5 class="card-title chartDivProductQuantityWiseSalesOnMonth"></h5>
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
                                    <input type="month" name="monthRange" id="monthRange" class="form-control monthRange" style="max-width: 100%;" value="<?= date('Y-m'); ?>" />
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
            <div id="chartDivProductQuantityWiseSalesOnMonth" class="chartContainer"></div>
        </div>
    </div>
</div>
<!-- PRODUCT QUANTITY WISE SALES ON MONTH -->

<!-- Chart code -->
<script>
    // **************************************************** CHART FUNCTION ********************************************************
    // ============================= PRODUCT QUANTITY WISE SALES ON MONTH ===============================
    function productQuantityWiseSalesOnMonth(chartData, chartTitle) {

        // Convert the input to a Date object
        var date = new Date($("#monthRange").val());

        // Format the month and year
        var formattedDate = date.toLocaleDateString('en-US', {
            month: 'long',
            year: 'numeric'
        });

        $(`.${chartTitle}`).text(`Product Quantity Wise Sales On ${formattedDate}`);

        if (chartData.length == 0) {
            chartData = [{
                item_name: "",
                total_qty: 0
            }];
        };

        am4core.ready(function() {

            // Themes
            am4core.useTheme(am4themes_animated);

            // Create chart instance
            var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
            chart.logo.disabled = true;

            let data = [];

            chartData.map((dataObject) => {
                let object = {};
                object.color = chart.colors.next();
                object.x_axis = dataObject.item_name;
                object.y_axis = Number(dataObject.total_qty);
                data.push(object);
            });

            // Add data
            chart.data = data;

            // Create axes
            var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

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
            series.columns.template.column.cornerRadiusTopLeft = 5;
            series.columns.template.column.cornerRadiusTopRight = 5;
            series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/b]";

            categoryAxis.renderer.labels.template.horizontalCenter = "right";
            categoryAxis.renderer.labels.template.verticalCenter = "middle";
            categoryAxis.renderer.labels.template.rotation = -20;
            categoryAxis.tooltip.disabled = true;
            categoryAxis.renderer.minHeight = 10;
            categoryAxis.renderer.labels.template.fontSize = 10;
            categoryAxis.renderer.grid.template.location = 0;

            chart.paddingBottom = 50;

            categoryAxis.renderer.labels.template.truncate = true;
            categoryAxis.renderer.labels.template.maxWidth = 120; // Adjust the maximum width as needed
            categoryAxis.renderer.labels.template.tooltipText = "{category}"; // Display full category name in tooltip

        });
    };
    // ===============================================================================================
    var monthRange = $("#monthRange").val();

    function get_productQuantityWiseSalesOnMonth(monthRange, search) {
        // AJAX CALL 
        $.ajax({
            url: `<?= BASE_URL ?>branch/location/ajaxs/reports/ajax-charts.php?chart=productQuantityWiseSalesOnMonth&search=${search}&monthRange=${monthRange}`,
            async: false,
            beforeSend: function() {
                $(".load-wrapp").show();
                $(".load-wrapp").css('opacity', 1);
            },
            success: function(result) {

                $(".load-wrapp").hide();
                $(".load-wrapp").css('opacity', 0);
                let res = jQuery.parseJSON(result);

                // =======================================================================================================================================
                productQuantityWiseSalesOnMonth(res.productQuantityWiseSalesOnMonth.data, "chartDivProductQuantityWiseSalesOnMonth");
                // =======================================================================================================================================
            }
        });
    }

    get_productQuantityWiseSalesOnMonth(monthRange, null);

    $('.applyFilter<?= $searchValue; ?>').click(function() {
        var monthRange = $("#monthRange").val();

        get_productQuantityWiseSalesOnMonth(monthRange, 'searching');
    });
</script>