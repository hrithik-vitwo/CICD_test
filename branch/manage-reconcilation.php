<?php
require_once("../app/v1/connection-branch-admin.php");
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar.php");
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

    .reconColumn {
        background-color: #606470 !important;
        color: white;
    }

    table tr td {
        background: #ffffff !important;
        padding-left: 0px !important;
        padding-right: 0px !important;
        text-align: center !important;
        cursor: pointer;
    }

    table th {
        padding-left: 0px !important;
        padding-right: 0px !important;
        text-align: center !important;
    }

    .matchedRowColor-100 td {
        background-color: #d1f0cc !important;
        color: #064908;
    }

    .matchedRowColor-75 td {
        background-color: #b3d5f0 !important;
        color: #064908;
    }

    .matchedRowColor-50 td {
        background-color: #f0deb3 !important;
        color: #064908;
    }

    .matchedRowColor-25 td {
        background-color: #fdf0f0 !important;
        color: #064908;
    }

    table.dataTable>thead .sorting:before,
    table.dataTable>thead .sorting:after,
    table.dataTable>thead .sorting_asc:before,
    table.dataTable>thead .sorting_asc:after,
    table.dataTable>thead .sorting_desc:before,
    table.dataTable>thead .sorting_desc:after,
    table.dataTable>thead .sorting_asc_disabled:before,
    table.dataTable>thead .sorting_asc_disabled:after,
    table.dataTable>thead .sorting_desc_disabled:before,
    table.dataTable>thead .sorting_desc_disabled:after {
        display: block !important;
    }


    .dataTables_wrapper .row:nth-child(3) {
        display: flex !important;
    }

    div.dataTables_wrapper div.dataTables_filter {
        display: block !important;
    }

    div.dataTables_wrapper div.dataTables_filter label {
        font-size: 0;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: 0;
        display: inline-block;
        width: auto;
        padding-left: 10px;
        border: 1px solid #E5E5E5;
        color: #1B2559;
        height: 25px;
        border-radius: 8px;
    }

    ul.pagination {
        border: 0;
    }

    /* .header-title .card-body {
        display: flex;
        justify-content: space-between;
    }
    .card-body::after, .card-footer::after, .card-header::after {
        display: none !important;
    } */
</style>

<link rel="stylesheet" href="../public/assets/listing.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid my-4">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    GSTR-2B Reconciliation
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="div w-100 p-3 d-flex justify-content-between">
                            <div>
                                <p>Total Available Credit</p>
                                <p style="font-size: 20px;"><i class="fas fa-rupee-sign"></i> 9,55,000.00</p>
                            </div>
                            <div>
                                <p>Remaining Credit</p>
                                <p style="font-size: 20px;"><i class="fas fa-rupee-sign"></i> 5,00,000.00</p>
                            </div>
                            <div class="p-1">
                                <button class="btn btn-sm btn-primary mr-3" id="matchTheTableRowBtn">
                                    <i class="fa fa-match text-light"></i> Match </button>
                                <button class="btn btn-sm btn-primary mr-3" id="addMatchedRowToBusketBtn">
                                    <i class="fa fa-check text-light"></i> Add to List </button>
                                <span style="font-size: 30px;">
                                    <i class="fas fa-rupee-sign"></i> 4,55,000.00
                                </span>
                                <i class="fas fa-file" style="font-size:65px;"></i>
                                <span class="badge badge-pill badge-info p-1" style="font-size: 10px!important;" id="mathedRowInBusketCounterSpan">19</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="row p-0 m-0">
                    <div class="col-4 ml-auto mb-2">
                        <input type="search" name="" id="" class="form-control common-search text-xs" placeholder="Search...">
                    </div>
                </div> -->
                <div class="row p-0 m-0">
                    <div class="col-6 pr-0">
                        <table class="table gstr2aTable">
                            <thead>
                                <th>GSTIN</th>
                                <th>VENDOR NAME</th>
                                <th>INVOICE NO</th>
                                <th>INV AMOUNT</th>
                                <th>TAX AMOUNT</th>
                                <th>ITC</th>
                                <th style="background-color: #011a3c!important; color:white">RECON</th>
                                <th style="background-color: #011a3c!important; color:white">MATCH</th>
                            </thead>

                            <tbody id="portalGstr2bTableBody">
                                <tr id="leftRow-1">
                                    <td class="portalVendorGstin">24AACCJ3219P1ZF</td>
                                    <td class="portalVendorName">JRS Global Networks Pvt. Ltd.</td>
                                    <td class="portalInvoiceNo">JRS/22-23IA/0011-98765</td>
                                    <td class="portalInvoiceAmt text-right">132160</td>
                                    <td class="portalInvoiceTaxAmt text-right">20160</td>
                                    <td class="reconColumn">
                                        <input type="checkbox" name="" id="" class="reconColumnCheckBox">
                                    </td>
                                    <td class="reconPercentageColumn reconColumn">0%</td>
                                </tr>
                                <tr id="leftRow-2">
                                    <td class="portalVendorGstin">24AACCJ3219P1ZF</td>
                                    <td class="portalVendorName">JRS Global Networks Pvt. Ltd.</td>
                                    <td class="portalInvoiceNo">INV /112215</td>
                                    <td class="portalInvoiceAmt text-right">132160</td>
                                    <td class="portalInvoiceTaxAmt text-right">20160</td>
                                    <td class="reconColumn">
                                        <input type="checkbox" name="" id="">
                                    </td>
                                    <td class="reconPercentageColumn reconColumn">0%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-6 pl-0">
                        <table class="table gstr2aTable">
                            <thead>
                                <th>GSTIN</th>
                                <th>VENDOR NAME</th>
                                <th>INVOICE NO</th>
                                <th>INV AMOUNT</th>
                                <th>TAX AMOUNT</th>
                                <th><i class="fas fa-bars"></i></th>
                            </thead>
                            <tbody id="localGstr2bTableBody">
                                <?php
                                $localInvoiceObj = queryGet('SELECT `grnIvId`, `grnId`, `companyId`, `branchId`, `vendorId`, `vendorCode`, `vendorGstin`, `vendorName`, `vendorDocumentNo`, `vendorDocumentDate`, `postingDate`, `grnTotalCgst`, `grnTotalSgst`, `grnTotalIgst`, `grnTotalAmount`, `paymentStatus` FROM `erp_grninvoice` WHERE `companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' ORDER BY `grnIvId` DESC', true);

                                if ($localInvoiceObj["status"] == "success") {
                                    $rowNo = 0;
                                    foreach ($localInvoiceObj["data"] as $oneLocInv) {
                                ?>
                                        <tr id="rightRow-<?= $rowNo += 1; ?>">
                                            <td class="localVendorGstin"><?= $oneLocInv["vendorGstin"] ?></td>
                                            <td class="localVendorName"><?= $oneLocInv["vendorName"] ?></td>
                                            <td class="localInvoiceNo"><?= $oneLocInv["vendorDocumentNo"] ?></td>
                                            <td class="localInvoiceAmt text-right"><?= $oneLocInv["grnTotalAmount"] ?></td>
                                            <td class="localInvoiceTaxAmt text-right"><?= $oneLocInv["grnTotalCgst"] + $oneLocInv["grnTotalSgst"] + $oneLocInv["grnTotalIgst"] ?></td>
                                            <td><i class="fa fa-sort"></i></td>
                                        </tr>
                                <?php
                                    }
                                }

                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="row">

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(document).ready(function() {
        // jQuery statements


        $('.gstr2aTable').DataTable({
            "searching": true,
            "paging": false,
            "info": false,
            "lengthChange": false,
        });

        $('#localGstr2bTableBody').sortable({
            start: function(event, ui) {
                // matchedRowColor-100
                console.log("HTML:", $(this).html());
                // $( this ).removeClass("matchedRowColor-100");
                console.log(ui);
                console.log(event);
            },
            stop: function(event, ui) {
                console.log(ui);
            }
        });



        $(document).on('click', "#addMatchedRowToBusketBtn", function() {
            console.log("hello there");
            $('#leftRow-1,#rightRow-1').fadeOut(300, function() {
                $(this).remove();
            });
            let counter = parseInt($("#mathedRowInBusketCounterSpan").html()) + 1;
            console.log(counter);
            $("#mathedRowInBusketCounterSpan").html(counter);
            console.log(counter);

            addTempReconciliation();
        });

        $(document).on('click', "#matchTheTableRowBtn", function() {
            console.log("Matching the table row...");

            // console.log("portalGstr2bTableBody");
            // $('#localGstr2bTableBody > tr').each(function(index, tr) {
            //     console.log("========ROW===========",index);
            //     $(this).find("td").each(function (i, td) {
            //         let tdVal = $(this).text();
            //         if(i==0){
            //             console.log("GSTIN:", tdVal);
            //         }else if(i==1){
            //             console.log("VENDOR NAME:", tdVal);
            //         }else if(i==2){
            //             console.log("INV NO:", tdVal);
            //         }else if(i==3){
            //             console.log("INV AMT:", tdVal);
            //         }else if(i==4){
            //             console.log("INV TAX:", tdVal);
            //         }
            //     });
            // });

            console.log("localGstr2bTableBody");
            $('#portalGstr2bTableBody > tr').each(function(leftTrIndex, leftTr) {
                let leftThis = this;
                let portalVendorGstin = $(this).find('.portalVendorGstin').text();
                let portalVendorName = $(this).find('.portalVendorName').text();
                let portalInvoiceNo = $(this).find('.portalInvoiceNo').text();
                let portalInvoiceAmt = $(this).find('.portalInvoiceAmt').text();
                let portalInvoiceTaxAmt = $(this).find('.portalInvoiceTaxAmt').text();
                console.log("===== LEFT ROW =====", leftTrIndex);
                console.log("portalVendorGstin:", portalVendorGstin);
                console.log("portalVendorName:", portalVendorName);
                console.log("portalInvoiceNo:", portalInvoiceNo);
                console.log("portalInvoiceAmt:", portalInvoiceAmt);
                console.log("portalInvoiceTaxAmt:", portalInvoiceTaxAmt);



                let prevMatchedConditions = 0;
                let prevMatchedIndex = 0;
                $('#localGstr2bTableBody > tr').each(function(rightTrIndex, rightTr) {
                    let rightThis = this;
                    let localVendorGstin = $(this).find('.localVendorGstin').text();
                    let localVendorName = $(this).find('.localVendorName').text();
                    let localInvoiceNo = $(this).find('.localInvoiceNo').text();
                    let localInvoiceAmt = $(this).find('.localInvoiceAmt').text();
                    let localInvoiceTaxAmt = $(this).find('.localInvoiceTaxAmt').text();

                    let matchedConditions = 0;
                    if (portalVendorGstin == localVendorGstin) {
                        matchedConditions += 25;
                    }
                    if (portalInvoiceAmt == localInvoiceAmt) {
                        matchedConditions += 25;
                    }
                    if (localInvoiceTaxAmt == localInvoiceTaxAmt) {
                        matchedConditions += 25;
                    }
                    if (portalInvoiceNo == localInvoiceNo) {
                        matchedConditions += 25;
                    }

                    if (matchedConditions > prevMatchedConditions) {

                        // leftTrIndex
                        $(leftThis).find('.reconPercentageColumn').html(`${matchedConditions}%`);
                        $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).removeClass(`matchedRowColor-100 matchedRowColor-75 matchedRowColor-50 matchedRowColor-25`);
                        $(`#localGstr2bTableBody tr:eq(${leftTrIndex})`).before(rightTr);
                        $(rightTr).addClass(`matchedRowColor-${matchedConditions}`);
                        $(leftTr).removeClass("matchedRowColor-100 matchedRowColor-75 matchedRowColor-50 matchedRowColor-25");
                        $(leftTr).addClass(`matchedRowColor-${matchedConditions}`);

                        prevMatchedConditions = matchedConditions;
                        prevMatchedIndex = leftTrIndex

                        console.log("========RIGHT ROW===========", rightTrIndex);
                        console.log("localVendorGstin:", localVendorGstin);
                        console.log("localVendorName:", localVendorName);
                        console.log("localInvoiceNo:", localInvoiceNo);
                        console.log("localInvoiceAmt:", localInvoiceAmt);
                        console.log("localInvoiceTaxAmt:", localInvoiceTaxAmt);
                        console.log("MATCHED PERCENTAGE::::", matchedConditions);
                    }
                });


            });

            // $('#localGstr2bTableBody > tr').each(function(index, tr) {
            //     console.log("========ROW===========",index);
            //     // if(index==0){
            //     //     $(this).html(`<td class="portalVendorGstin">Test Test</td>
            //     //                     <td class="localVendorName">AMAZON</td>
            //     //                     <td class="localInvoiceNo">IV98765456</td>
            //     //                     <td class="localInvoiceAmt text-right">65,000.00</td>
            //     //                     <td class="localInvoiceTaxAmt text-right">12,900.00</td>
            //     //                 <td><i class="fa fa-sort"></i></td>`);
            //     // }

            //     let localVendorGstin = $(this).find('.localVendorGstin').text();
            //     let localVendorName = $(this).find('.localVendorName').text();
            //     let localInvoiceNo = $(this).find('.localInvoiceNo').text();
            //     let localInvoiceAmt = $(this).find('.localInvoiceAmt').text();
            //     let localInvoiceTaxAmt = $(this).find('.localInvoiceTaxAmt').text();
            //     console.log("localVendorGstin:", localVendorGstin);
            //     console.log("localVendorName:", localVendorName);
            //     console.log("localInvoiceNo:", localInvoiceNo);
            //     console.log("localInvoiceAmt:", localInvoiceAmt);
            //     console.log("localInvoiceTaxAmt:", localInvoiceTaxAmt);
            // });






        });


        function addTempReconciliation() {
            $.ajax({
                method: "post",
                url: "ajaxs/ajax-gstr2b-temp-reconciliation.php",
                data: {
                    reconMonth: "",
                    reconYear: "",
                    portalVendorGstin: "",
                    portalVendorName: "",
                    portalVendorInvNo: "",
                    portalVendorInvAmt: "",
                    portalVendorTaxAmt: "",
                    localVendorGstin: "",
                    localVendorName: "",
                    localVendorInvNo: "",
                    localVendorInvAmt: 6789,
                    localVendorTaxAmt: 98765
                },
                beforeSend: function() {
                    console.log("beforeSend");
                },
                success: function(data) {
                    console.log("response from ajax:");
                    console.log(data);
                }
            });
        }



    });
</script>