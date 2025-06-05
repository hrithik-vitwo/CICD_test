<?php
require_once("../../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../../common/header.php");
require_once("../../common/navbar.php");
require_once("../../common/sidebar.php");
require_once("../../common/pagination.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/branch/func-bom-controller.php");
require_once("../../../app/v1/functions/branch/func-goods-controller.php");



$goodsBomController = new GoodsBomController();
$goodsController = new GoodsController();

function getRmSfgItems()
{
    global $location_id;
    $sql = 'SELECT
                items.itemId,
                items.itemName,
                items.itemCode,
                items.parentGlId,
                itemTypes.type,
                itemUom.uomName,
                COALESCE(summary.movingWeightedPrice,0.00) AS movingWeightedPrice,
                COALESCE(itemBom.cogm,0.00) AS itemBomPrice
            FROM
                `erp_inventory_stocks_summary` AS summary
            INNER JOIN `erp_inventory_items` AS items
            ON
                summary.`itemId` = items.`itemId`
            INNER JOIN `erp_inventory_mstr_good_types` AS itemTypes
            ON
                items.`goodsType` = itemTypes.`goodTypeId`
            LEFT JOIN `erp_inventory_mstr_uom` AS itemUom
            ON
                items.`baseUnitMeasure` = itemUom.`uomId`
            LEFT JOIN `erp_bom` AS itemBom
            ON
                items.itemId = itemBom.itemId AND summary.`location_id` = itemBom.`locationId`
            WHERE
                summary.`location_id` = ' . $location_id . ' AND(
                    itemTypes.`type` = "RM" OR itemTypes.`type` = "SFG"
                )';

    return queryGet($sql, true);
}

function getGoodAndServiceItems($itemTypes = ["RM", "SFG"])
{
    global $location_id;
    $sql = 'SELECT
                items.itemId,
                items.itemName,
                items.itemCode,
                items.parentGlId,
                itemTypes.type,
                itemUom.uomName,
                COALESCE(summary.movingWeightedPrice,0.00) AS movingWeightedPrice,
                COALESCE(itemBom.cogm,0.00) AS itemBomPrice,
                summary.bomStatus
            FROM
                `erp_inventory_stocks_summary` AS summary
            INNER JOIN `erp_inventory_items` AS items
            ON
                summary.`itemId` = items.`itemId`
            INNER JOIN `erp_inventory_mstr_good_types` AS itemTypes
            ON
                items.`goodsType` = itemTypes.`goodTypeId`
            LEFT JOIN `erp_inventory_mstr_uom` AS itemUom
            ON
                items.`baseUnitMeasure` = itemUom.`uomId`
            LEFT JOIN `erp_bom` AS itemBom
            ON
                items.itemId = itemBom.itemId AND summary.`location_id` = itemBom.`locationId`
            WHERE
                summary.`location_id` = ' . $location_id . ' AND itemTypes.`type` IN ("' . implode('","', $itemTypes) . '")';

    return queryGet($sql, true);
}


function getGoodActivities()
{
    global $location_id;
    global $branch_id;
    global $company_id;
    $sql = 'SELECT
                `CostCenter_id`,
                `CostCenter_code`,
                `CostCenter_desc`,
                `labour_hour_rate`,
                `machine_hour_rate`,
                `gl_code`,
                `parent_id`,
                `type`
            FROM
                `erp_cost_center`
            WHERE
                `CostCenter_status` = "active" AND `company_id` = ' . $company_id . '
            ORDER BY
                `CostCenter_id`
            DESC';

    return queryGet($sql, true);
}

function getWcList()
{


    global $location_id;
    global $branch_id;
    global $company_id;
    $sql = 'SELECT
                `work_center_id`,
                `work_center_code`,
                `work_center_name`
            FROM
                `erp_work_center`
            WHERE
                `status` = "active" AND `company_id` = ' . $company_id . '
            ORDER BY
                `work_center_id`
            DESC';

    return queryGet($sql, true);
}

$coaObj = getAllChartOfAccounts_list_by_p($company_id, 4);

// if (isset($_POST["addCOGSFormSubmitBtn"])) {
//     // console($_POST);
//     $createCogsObj = $goodsBomController->createBomCOGS($_POST);
//     swalToast($createCogsObj["status"], $createCogsObj["message"]);
// }

// if (isset($_POST["releaseBom"])) {
//     $bomId = base64_decode($_POST["releaseBom"]);
//     $updateCurrentBomItemPriceObj = $goodsBomController->updateCurrentBomItemPrice($bomId);
//     // console($updateCurrentBomItemPriceObj);
//     swalToast($updateCurrentBomItemPriceObj["status"], $updateCurrentBomItemPriceObj["message"]);
// }


?>

<style>
    .bom-modal .modal-dialog {
        max-width: 100%;
        width: 50%;
    }

    .bom-modal .modal-header {
        height: auto;
    }

    .bom-modal .modal-body {
        width: 100%;
        top: -30px;
    }

    .bom-modal .modal-body .card .card-body {
        padding: 15px 0 0px;
    }

    .bom-modal .modal-body .card .card-body table {
        margin-bottom: 20px;
    }

    .card.p-0.boq-form-card.bg-transparent.boq-section .card-body {
        overflow: auto;
        padding-left: 0;
        padding-right: 0;
    }

    .is-bom .card.p-0.boq-form-card.bg-transparent.boq-section .card-body .select2-container,
    .is-bom .card.p-0.boq-form-card.bg-transparent.boq-section .card-body select {
        width: 100% !important;
        max-width: 257px;
    }

    .is-bom .acc-summary {
        max-width: 500px;
        margin-left: auto;
    }

    .new-over-modal .form-input {
        margin: 7px 0;
    }

    .new-over-modal .select2-container {
        width: 100% !important;
    }
</style>
<!-- <link rel="stylesheet" href="../../public/assets/sales-order.css"> -->
<link rel="stylesheet" href="../../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">



<div class="content-wrapper is-bom">
    <section class="content">
        <div class="container-fluid">
            <!-- Create Bom -->
            <?php
            if (isset($_GET["create"]) && $_GET["create"] != "") {
                require_once("create.php");
            } elseif (isset($_GET["editBom"]) && $_GET["editBom"] != "") {
                require_once("editBom.php");
            } elseif (isset($_GET["view"]) && $_GET["view"] != "") {
                require_once("view.php");
            } elseif (isset($_GET["copy"]) && $_GET["copy"] != "") {
                require_once("copy.php");
            } else {
                require_once("list-p.php");
               
            }
            ?>
            <!-- end Create Bom -->
        </div>
    </section>
</div>
<?php
require_once("../../common/footer.php");
?>
<script>
    $(document).ready(function() {
        var indexValues = [];
        var dataTable;

        function initializeDataTable() {
            dataTable = $("#dataTable_detailed_view").DataTable({
                dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
                "lengthMenu": [10, 25, 50, 100, 200, 250],
                "ordering": false,
                info: false,
                "initComplete": function(settings, json) {
                    $('#dataTable_detailed_view_filter input[type="search"]').attr('placeholder', 'Search....');
                },

                buttons: [{
                    extend: 'collection',
                    text: '<ion-icon name="download-outline"></ion-icon> Export',
                    buttons: [
                        {
                            extend: 'csv',
                            text: '<ion-icon name="document-outline" class="ion-csv"></ion-icon> CSV'
                        }
                    ]
                }],
                // select: true,
                "bPaginate": false,
            });

        }
        $('#dataTable_detailed_view thead tr').append('<th>Action</th>');

        initializeDataTable();

        function fill_datatable(formDatas = '', pageNo = '', limit = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;
            var columnMapping = <?php echo json_encode($columnMapping); ?>;
            var checkboxSettings = Cookies.get('cookiesBom');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "../ajaxs/ajax-bom.php",
                dataType: 'json',
                data: {
                    act: 'bom',
                    comid: comid,
                    locId: locId,
                    bId: bId,
                    formDatas: formDatas,
                    pageNo: pageNo,
                    limit: limit
                },
                beforeSend: function() {
                    $("#detailed_tbody").html(`<td colspan=17 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>`);
              },
                success: function(response) {
                    // console.log(response);
                    // alert(response)

                    if (response.status) {
                        var responseObj = response.data;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(length - 1).visible(true);
                        dataTable.column(length - 2).visible(true);

                         $.each(responseObj, function(index, value) {
                             $('#item_id').val(value.itemId);

                             dataTable.row.add([
                                    value.sl_no,
                                    `<a href="#" class="soModal"  data-id="${value.itemCode}" data-toggle="modal" data-target="#viewGlobalModal">${ value.itemCode}</a>`,
                                    `<p class='pre-normal'>${value.itemName}</p>`,
                                    formatDate(value.preparedDate),
                                    value.cogm_m,
                                    value.cogm_a,
                                    value.cogm,
                                    value.cogs,
                                    value.msp,
                                    value.bomStatus,


                                 ` <div class="dropout">
                                     <button class="more">
                                          <span></span>
                                          <span></span>
                                          <span></span>
                                     </button>
                                     <ul>
                                     <li>
                                         <button data-toggle="modal" data-target="#editModal"><ion-icon name="create-outline" class="ion-edit"></ion-icon>Edit</button>
                                     </li>
                                     <li>
                                         <button data-toggle="modal" data-target="#viewModal"><ion-icon name="trash-outline" class="ion-delete"></ion-icon>Delete</button>
                                     </li>
                                     <li>
                                         <button data-toggle="modal" data-target="#viewModal"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                     </li>
                                    
                                     </ul>
                                   
                                 </div>`,
                             ]).draw(false);
                         });

                        $('#yourDataTable_paginate').html(response.pagination);
                        $('#limitText').html(response.limitTxt);

                        if (checkboxSettings) {
                            var checkedColumns = JSON.parse(checkboxSettings);

                            $(".settingsCheckbox_detailed").each(function(index) {
                                var columnVal = $(this).val();
                                if (checkedColumns.includes(columnVal)) {
                                    $(this).prop("checked", true);
                                    dataTable.column(index).visible(true);

                                } else {
                                    notVisibleColArr.push(index);
                                }
                            });
                            // console.log("notVisibleColArr index:", notVisibleColArr);
                            if (notVisibleColArr.length > 0) {
                                notVisibleColArr.forEach(function(index) {
                                    dataTable.column(index).visible(false);
                                });
                            }

                            console.log('Cookie value:', checkboxSettings);

                        } else {
                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            $(".settingsCheckbox_detailed").each(function(index) {
                                if ($(this).prop("checked")) {
                                    dataTable.column(index).visible(true);

                                }
                            });
                            console.log('Cookie value:', checkboxSettings);
                        }
                    } else {

                        $("#detailed_tbody").html(`<td colspan=17 class='else-td not-found-td text-center'><img src="../../../public/assets/gif/no-transaction.gif" width="150" alt=""><p>No Data Found</p></td>`);
                        $('#yourDataTable_paginate').remove();
                        $('#limitText').remove();
                    }
                }
            });
        }

        fill_datatable();


        //    ----- page length limit-----
        let formInputs = {};
        $(document).on("change", ".custom-select", function(e) {
            var maxlimit = $(this).val();
            fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit);

        });

        //    ------------ pagination-------------

        $(document).on("click", "#pagination a ", function(e) {
            e.preventDefault();
            var page_id = $(this).attr('id');
            var limitDisplay = $(".custom-select").val();

            fill_datatable(formDatas = formInputs, pageNo = page_id, limit = limitDisplay);

        });

        //<--------------advance search------------------------------->
        $(document).ready(function() {
            $(document).on("click", "#serach_submit", function(event) {
                event.preventDefault();
                let values;
                $(".selectOperator").each(function() {
                    let columnIndex = ($(this).attr("id")).split("_")[1];
                    let columnSlag = $(`#columnSlag_${columnIndex}`).val();
                    let operatorName = $(`#selectOperator_${columnIndex}`).val();
                    let value = $(`#value_${columnIndex}`).val() ?? "";
                    let value2 = $(`#value2_${columnIndex}`).val() ?? "";


                    if ((columnSlag === 'so.posting_date') && operatorName == "BETWEEN") {
                        formInputs[columnSlag] = {
                            operatorName,
                            value: {
                                fromDate: value,
                                toDate: value2
                            }
                        };
                    } else {
                        formInputs[columnSlag] = {
                            operatorName,
                            value
                        };
                    }
                });

                $('#btnSearchCollpase_modal').modal('hide');
                console.log("FormInputs:", formInputs);

                fill_datatable(formDatas = formInputs);

            });
        });

        // -------------checkbox----------------------

        $(document).ready(function() {
            var columnMapping = <?php echo json_encode($columnMapping); ?>;

            var indexValues = [];

            function toggleColumnVisibility(columnIndex, checkbox) {
                var column = dataTable.column(columnIndex);
                column.visible(checkbox.checked);

            }

            $("input[name='settingsCheckbox[]']").change(function() {
                var columnVal = $(this).val();
                console.log(columnVal);

                var index = columnMapping.findIndex(function(column) {
                    return column.slag === columnVal;
                });
                console.log(index);
                toggleColumnVisibility(index, this);
            });

            $(".grand-checkbox").on("click", function() {
                $(".colomnTable tr td input[type='checkbox']").prop("checked", this.checked);
                $("input[name='settingsCheckbox[]']").each(function() {
                    var columnVal = $(this).val();
                    // console.log(columnVal);
                    var index = columnMapping.findIndex(function(column) {
                        return column.slag === columnVal;
                    });
                    if ($(this).is(':checked')) {
                        indexValues.push(index);
                    } else {
                        var removeIndex = indexValues.indexOf(index);
                        if (removeIndex !== -1) {
                            indexValues.splice(removeIndex, 1);
                        }
                    }
                    toggleColumnVisibility(index, this);
                });
            });

        });

    });

    //    -------------- save cookies--------------------

    $(document).ready(function() {
        $(document).on("click", "#check-box-submt", function(event) {
            // console.log("Hiiiii");
            event.preventDefault();
            // $("#myModal1").modal().hide();
            $('#btnSearchCollpase_modal').modal('hide');
            var tablename = $("#tablename").val();
            var pageTableName = $("#pageTableName").val();
            var settingsCheckbox = [];
            var formData = {};
            $(".settingsCheckbox_detailed").each(function() {
                if ($(this).prop('checked')) {
                    var chkBox = $(this).val();
                    settingsCheckbox.push(chkBox);
                    formData = {
                        tablename,
                        pageTableName,
                        settingsCheckbox
                    };
                }
            });

            console.log(formData);
            if (settingsCheckbox.length < 5) {
                alert("Please select at least 5");
            } else {
                $.ajax({
                    type: "POST",
                    url: "../ajaxs/ajax-save-cookies.php",
                    dataType:'json',
                    data: {
                        act: 'goodsItem',
                        formData: formData
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: response.status,
                            title: response.message,
                            timer: 1000,
                            showConfirmButton: false,
                        })
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });

            }
        });
    });
</script>
<!-- -----fromDate todate input add--- -->
<script>
    $(document).ready(function() {
        $(document).on("change", ".selectOperator", function() {
            let columnIndex = parseInt(($(this).attr("id")).split("_")[1]);
            let operatorName = $(this).val();
            let columnName = $(`#columnName_${columnIndex}`).html();
            let inputContainer = $(`#td_${columnIndex}`);
            let inputId;
            if (columnName === 'Posting Date') {
                inputId = "value2_" + columnIndex;
            }

            if ((columnName === 'Posting Date') && operatorName === 'BETWEEN') {
                inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
            } else {
                $(`#${inputId}`).remove();
            }
            console.log(`Change operator => ${operatorName}, columnName => ${columnName}`);
        });

    });
</script>

<script>
    function openFullscreen() {
        var elem = document.getElementById("listTabPan")

        if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                /* Safari */
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                /* IE11 */
                elem.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                /* Safari */
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                /* IE11 */
                document.msExitFullscreen();
            }
        }
    }

    document.addEventListener('fullscreenchange', exitHandler);
    document.addEventListener('webkitfullscreenchange', exitHandler);
    document.addEventListener('MSFullscreenChange', exitHandler);

    function exitHandler() {
        if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
            $(".content-wrapper").removeClass("fullscreen-mode");
        } else {
            $(".content-wrapper").addClass("fullscreen-mode");
        }
    }
</script>

<script>
    document.querySelector('table.stock-new-table').onclick = ({
        target
    }) => {
        if (!target.classList.contains('more')) return
        document.querySelectorAll('.dropout.active').forEach(
            (d) => d !== target.parentElement && d.classList.remove('active')
        )
        target.parentElement.classList.toggle('active')
    }
</script>