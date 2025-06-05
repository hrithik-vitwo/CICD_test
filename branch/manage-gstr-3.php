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
</style>

<?php

class ComplianceGSTR3bData
{
    private $company_id;
    private $branch_id;
    private $start_date;
    private $end_date;
    function __construct($startDate = "2023-01-01", $endDate = "2023-05-31")
    {
        global $company_id, $branch_id;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->start_date = $startDate;
        $this->end_date = $endDate;
    }
    function getOutwardTaxableSupplies(){
        $allInvoicesObj = queryGet('SELECT `so_invoice_id`, `company_id`, `branch_id`, `location_id`,`invoice_no`, `invoice_date`, `inv_variant_id`, `varient_array`, `totalItems`, `igst`, `sgst`, `cgst`, `totalDiscount`, `total_tax_amt`, `sub_total_amt`, `all_total_amt`, `due_amount`,`compInvoiceType`,`customer_gstin`,`placeOfSupply`, `type`, `compFileDate`, `status` FROM `erp_branch_sales_order_invoices` WHERE `branch_id`='.$this->branch_id.' AND `invoice_date` BETWEEN "'.$this->start_date.'" AND "'.$this->end_date.'" ORDER BY `so_invoice_id` DESC', true);

        $grandIgst = 0.00;
        $grandCgst = 0.00;
        $grandSgst = 0.00;
        $grandCess = 0.00;
        $grandSubTotal = 0.00;
        foreach($allInvoicesObj["data"] as $invoice){
            $grandSubTotal+=$invoice["sub_total_amt"] ?? 0.00;
            $grandIgst += $invoice["igst"];
            $grandCgst += $invoice["cgst"];
            $grandSgst += $invoice["sgst"];
        }

        return [
            "status" => "success",
            "message" => "Successfully fetched data",
            "data" =>[
                "grandIgst" => $grandIgst,
                "grandCgst" => $grandCgst,
                "grandSgst" => $grandSgst,
                "grandCess" => $grandCess,
                "grandSubTotal" => $grandSubTotal
            ]
        ];
    }

    function getInterStateTaxableSupplies(){
        $allInvoicesObj = queryGet('SELECT `placeOfSupply`, SUM(`sub_total_amt`) AS totalTaxableAmount, SUM(`igst`) AS totalIgstAmount FROM `erp_branch_sales_order_invoices` WHERE `branch_id`='.$this->branch_id.' AND `invoice_date` BETWEEN "'.$this->start_date.'" AND "'.$this->end_date.'" GROUP BY `placeOfSupply`', true);
        return $allInvoicesObj;
    }





}

?>

<link rel="stylesheet" href="../public/assets/listing.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid my-4">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    GSTR-3
                </div>
            </div>
            <div class="row">
                <?php
                    $complianceGSTR3bDataObj = new ComplianceGSTR3bData();
                    $outwardTaxableSuppliesObj = $complianceGSTR3bDataObj->getOutwardTaxableSupplies();
                    $interStateTaxableSuppliesObj = $complianceGSTR3bDataObj->getInterStateTaxableSupplies();
                    // console($outwardTaxableSuppliesObj);
                ?>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-body px-0 pb-0">
                            <ul class="nav nav-tabs" role="tablist" style="background-color: #001621;padding: 5px;">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#gstr3bSummaryTabDiv" role="tab" aria-selected="true">Summary</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#gstr3bFileTabDiv" role="tab" aria-selected="true">FILE 3B</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="gstr3bSummaryTabDiv" role="tabpanel" aria-labelledby="listTab">
                                    <table class="table defaultDataTable table-hover">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Partculars</th>
                                                <th>Voucher Count</th>
                                                <th>Taxable Amount</th>
                                                <th>CGST</th>
                                                <th>SGST</th>
                                                <th>IGST</th>
                                                <th>CESS</th>
                                                <th>Total Tax</th>
                                                <th>Invoice Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="gstr3bFileTabDiv" role="tabpanel" aria-labelledby="listTab">
                                    File GSTR3B
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="card p-0">
                    <div class="card-header text-light">
                        <p>3.1 Tax on outward and reverse charge inward supplies</p>
                        <small>(IGST: 456789.89, CGST: 8765.00, SGST: 5678.00, CESS: 0.00)</small>
                    </div>
                    <div class="card-body" style="overflow-x: auto;">
                        <table class="table p-0">
                            <thead>
                                <tr>
                                    <th>Nature of Supplies</th>
                                    <th>Total Taxable Value</th>
                                    <th>IGST</th>
                                    <th>CGST</th>
                                    <th>SGST</th>
                                    <th>CESS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>a. Outward taxable supplies (Other then zero rated, nill rated and exampted)</td>
                                    <td><input type="number" step="any" name="all_outwards_taxable_value" value="<?= $outwardTaxableSuppliesObj["data"]["grandSubTotal"] ?>"></td>
                                    <td><input type="number" step="any" name="all_outwards_igst" value="<?= $outwardTaxableSuppliesObj["data"]["grandIgst"] ?>"></td>
                                    <td><input type="number" step="any" name="all_outwards_cgst" value="<?= $outwardTaxableSuppliesObj["data"]["grandCgst"] ?>"></td>
                                    <td><input type="number" step="any" name="all_outwards_sgst" value="<?= $outwardTaxableSuppliesObj["data"]["grandSgst"] ?>"></td>
                                    <td><input type="number" step="any" name="all_outwards_cess" value="<?= $outwardTaxableSuppliesObj["data"]["grandCess"] ?>"></td>
                                </tr>
                                <tr>
                                    <td>b. Outward taxable supplies (Zero rated)</td>
                                    <td><input type="number" step="any" name="zero_rated_outwards_taxable_value" value="0.00"></td>
                                    <td><input type="number" step="any" name="zero_rated_outwards_igst" value="0.00"></td>
                                    <td></td>
                                    <td></td>
                                    <td><input type="number" step="any" name="zero_rated__outwards_cess" value="0.00"></td>
                                </tr>
                                <tr>
                                    <td>c. Other outward supplies (Nil rated, Exampted)</td>
                                    <td><input type="number" step="any" name="nil_exampted_outwards_taxable_value" value="0.00"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>d. Inward taxable supplies (Liable to reverse charge)</td>
                                    <td><input type="number" step="any" name="reverse_charge_inwards_taxable_value" value="0.00"></td>
                                    <td><input type="number" step="any" name="reverse_charge_inwards_igst" value="0.00"></td>
                                    <td><input type="number" step="any" name="reverse_charge_inwards_cgst" value="0.00"></td>
                                    <td><input type="number" step="any" name="reverse_charge_inwards_sgst" value="0.00"></td>
                                    <td><input type="number" step="any" name="reverse_charge_inwards_cess" value="0.00"></td>
                                </tr>
                                <tr>
                                    <td>e. Non-GST outward supplies</td>
                                    <td><input type="number" step="any" name="non_gst_outwards_taxable_value" value="0.00"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <hr/>
                        <center><button class="btn btn-primary">Save</button></center>
                    </div>
                </div>
                <div class="card p-0">
                    <div class="card-header text-light">
                        <p>3.2 Inter-state supplies</p>
                    </div>
                    <div class="card-body" style="overflow-x: auto;">
                        <table class="table p-0">
                            <thead>
                                <tr>
                                    <th>Place of Supply(State/UT)</th>
                                    <th>Total Taxable Value</th>
                                    <th>IGST</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach($interStateTaxableSuppliesObj["data"] as $row){
                                        ?>
                                        <tr>
                                            <td><input type="number" step="any" name="inter_state_place_supply[]" value="<?= $row["placeOfSupply"] ?>"></td>
                                            <td><input type="number" step="any" name="inter_state_taxable_value[]" value="<?= $row["totalTaxableAmount"] ?>"></td>
                                            <td><input type="number" step="any" name="inter_state_igst[]" value="<?= $row["totalIgstAmount"] ?>"></td>
                                        </tr>
                                        <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                        <hr/>
                        <center><button class="btn btn-primary">Save Intra-State Data</button></center>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="card p-0">
                    <div class="card-header text-light">
                        <p>4. Eligible ITC</p>
                        <small>(IGST: 456789.89, CGST: 8765.00, SGST: 5678.00, CESS: 0.00)</small>
                    </div>
                    <div class="card-body" style="overflow-x: auto;">
                        <table class="table p-0">
                            <thead>
                                <tr>
                                    <th>Details</th>
                                    <th>IGST</th>
                                    <th>CGST</th>
                                    <th>SGST</th>
                                    <th>CESS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>(A) ITC. Available (whether in full or part)</td>
                                    <td><input type="number" step="any" name="all_outwards_igst" value=""></td>
                                    <td><input type="number" step="any" name="all_outwards_cgst" value=""></td>
                                    <td><input type="number" step="any" name="all_outwards_sgst" value=""></td>
                                    <td><input type="number" step="any" name="all_outwards_cess" value="<?= $outwardTaxableSuppliesObj["data"]["grandCess"] ?>"></td>
                                </tr>
                                <tr>
                                    <td>(a) Import of goods</td>
                                    <td><input type="number" step="any" name="zero_rated_outwards_taxable_value" value="0.00"></td>
                                    <td><input type="number" step="any" name="zero_rated_outwards_igst" value="0.00"></td>
                                    <td></td>
                                    <td></td>
                                    <td><input type="number" step="any" name="zero_rated__outwards_cess" value="0.00"></td>
                                </tr>
                                <tr>
                                    <td>c. Other outward supplies (Nil rated, Exampted)</td>
                                    <td><input type="number" step="any" name="nil_exampted_outwards_taxable_value" value="0.00"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>d. Inward taxable supplies (Liable to reverse charge)</td>
                                    <td><input type="number" step="any" name="reverse_charge_inwards_taxable_value" value="0.00"></td>
                                    <td><input type="number" step="any" name="reverse_charge_inwards_igst" value="0.00"></td>
                                    <td><input type="number" step="any" name="reverse_charge_inwards_cgst" value="0.00"></td>
                                    <td><input type="number" step="any" name="reverse_charge_inwards_sgst" value="0.00"></td>
                                    <td><input type="number" step="any" name="reverse_charge_inwards_cess" value="0.00"></td>
                                </tr>
                                <tr>
                                    <td>e. Non-GST outward supplies</td>
                                    <td><input type="number" step="any" name="non_gst_outwards_taxable_value" value="0.00"></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <hr/>
                        <center><button class="btn btn-primary">Save</button></center>
                        <!-- 0 Tables 4(A)(1), (3), (4), (5) and 4(B)(2) are auto-drafted b
                        (A) ITC. Available (whether in full or part)
                        (a) Import of goods
                        (2) Import of services
                        (3) Inward supplies liable to reverse charge (other than 1 I
                        above)
                        (4) Inward supplies from 1SD
                        (5) All other ITC
                        SEE Reversed
                        (1) As per rules 38,42 & 43 of CGST Rules and section 17(5)
                        (C) Net ITC Available (A) - (B)
                        (D) Other Details
                        (1) ITC reclaimed which was reversed under Table 4(B)(2) in
                        carlicr tax period
                        57 / 8:52 -->
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
    $(function() {
        $("#gstr1b2bTable_wrapper").DataTable({
            "responsive": true,
            "lengthChange": false,
            paging: false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#defaultDataTable_wrapper .col-md-6:eq(0)');

    });
</script>