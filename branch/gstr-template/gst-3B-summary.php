<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />


<style>
    section.gstr-3B {
        padding: 0px 20px;
    }

    .head-btn-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .gstr-3B-filter {
        left: 0;
        top: 0;
    }

    .gstr-3B-filter a.active {
        background-color: #003060;
        color: #fff;
    }


    .daybook-filter-list.filter-list {
        display: flex;
        gap: 7px;
        justify-content: flex-end;
        position: relative;
        top: 0px;
        left: 18px;
        margin: 15px 0;
        float: right;
    }

    .daybook-filter-list.filter-list a.active {
        background-color: #003060;
        color: #fff;
    }

    .date-range-input {
        gap: 7px;
    }

    .date-range-input .form-input {
        width: 100%;
    }

    .daybook-tabs {
        flex-direction: row-reverse;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    ul.nav-preview {
        position: absolute;
        top: 15px;
        left: 100px;
        z-index: 9;
    }

    ul.nav-preview li .nav-link,
    ul.nav-preview li .nav-link:hover {
        display: flex;
        align-items: center;
        color: #000 !important;
    }

    ul.nav-preview li .nav-link.active {
        background-color: #fff;
        color: #000;
    }

    .nav-preview-content nav.details .nav-tabs {
        background: transparent;
        padding: 5px 3px 0px;
    }

    .nav-preview-content nav.details .nav-tabs button {
        font-weight: 500;
        color: #000;
        font-size: 13px;
    }

    .nav-preview-content nav.details .nav-tabs button.active {
        background: #a7a7a7;
        border: 0;
        color: #fff;
    }


    .date-picker {
        width: 260px;
        height: auto;
        max-height: 50px;
        background: white;
        position: relative;
        overflow: hidden;
        transition: all 0.3s 0s ease-in-out;
    }

    .date-picker .input {
        width: 100%;
        height: 50px;
        font-size: 0;
        cursor: pointer;
    }

    .date-picker .input .result,
    .date-picker .input button {
        display: inline-block;
        vertical-align: top;
    }

    .date-picker .input .result {
        width: calc(100% - 50px);
        height: 50px;
        line-height: 50px;
        font-size: 16px;
        padding: 0 10px;
        color: grey;
        box-sizing: border-box;
    }

    .date-picker .input button {
        width: 50px;
        height: 50px;
        background-color: #8392A7;
        color: white;
        line-height: 50px;
        border: 0;
        font-size: 18px;
        padding: 0;
    }

    .date-picker .input button:hover {
        background-color: #68768A;
    }

    .date-picker .input button:focus {
        outline: 0;
    }

    .date-picker .calendar {
        position: relative;
        width: 100%;
        background: #fff;
        border-radius: 0px;
        overflow: hidden;
    }

    .date-picker .ui-datepicker-inline {
        position: relative;
        width: 100%;
    }

    .date-picker .ui-datepicker-header {
        height: 100%;
        line-height: 50px;
        background: #8392A7;
        color: #fff;
        margin-bottom: 10px;
    }

    .date-picker .ui-datepicker-prev,
    .date-picker .ui-datepicker-next {
        width: 20px;
        height: 20px;
        text-indent: 9999px;
        border: 2px solid #fff;
        border-radius: 100%;
        cursor: pointer;
        overflow: hidden;
        margin-top: 12px;
    }

    .date-picker .ui-datepicker-prev {
        float: left;
        margin-left: 12px;
    }

    .date-picker .ui-datepicker-prev:after {
        transform: rotate(45deg);
        margin: -43px 0px 0px 8px;
    }

    .date-picker .ui-datepicker-next {
        float: right;
        margin-right: 12px;
    }

    .date-picker .ui-datepicker-next:after {
        transform: rotate(-135deg);
        margin: -43px 0px 0px 6px;
    }

    .date-picker .ui-datepicker-prev:after,
    .date-picker .ui-datepicker-next:after {
        content: "";
        position: absolute;
        display: block;
        width: 4px;
        height: 4px;
        border-left: 2px solid #fff;
        border-bottom: 2px solid #fff;
    }

    .date-picker .ui-datepicker-prev:hover,
    .date-picker .ui-datepicker-next:hover,
    .date-picker .ui-datepicker-prev:hover:after,
    .date-picker .ui-datepicker-next:hover:after {
        border-color: #68768A;
    }

    .date-picker .ui-datepicker-title {
        text-align: center;
    }

    .date-picker .ui-datepicker-calendar {
        width: 100%;
        text-align: center;
    }

    .date-picker .ui-datepicker-calendar thead tr th span {
        display: block;
        width: 100%;
        color: #8392A7;
        margin-bottom: 5px;
        font-size: 13px;
    }

    .date-picker .ui-state-default {
        display: block;
        text-decoration: none;
        color: #b5b5b5;
        line-height: 40px;
        font-size: 12px;
    }

    .date-picker .ui-state-default:hover {
        background: rgba(0, 0, 0, 0.02);
    }

    .date-picker .ui-state-highlight {
        color: #68768A;
    }

    .date-picker .ui-state-active {
        color: #68768A;
        background-color: rgba(131, 146, 167, 0.12);
        font-weight: 600;
    }

    .date-picker .ui-datepicker-unselectable .ui-state-default {
        color: #eee;
        border: 2px solid transparent;
    }

    .date-picker.open {
        max-height: 400px;
    }

    .date-picker.open .input button {
        background: #68768A;
    }



    #startDate {
        max-width: 200px;
    }

    .datepicker-bg {
        background-color: #003060;
        color: #fff;
    }

    table.summary-details-table {
        width: 100%;
    }

    table.summary-details-table tr:nth-child(even) td {
        background: #fff;
    }

    table.summary-details-table tr:nth-child(odd) td {
        background: #eee;
    }

    table.summary-details-table td {
        white-space: pre-wrap !important;
    }

    .summary-block .card {
        box-shadow: rgba(0, 0, 0, 0.12) 0px 1px 3px, rgba(0, 0, 0, 0.24) 0px 1px 2px;
        transition-duration: 0.2s;
        min-height: 260px;
    }

    .summary-block .card:hover {
        box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;
    }

    .summary-details {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .summary-details>div {
        flex-basis: calc(50% - 40px);
        margin: 0 0;
        padding: 10px 0;
    }
</style>


<div class="content-wrapper">
    <section class="gstr-3B">
        <h4 class="text-lg font-bold mt-4 mb-4">GSTR-3B</h4>
        <div class="head-btn-section mb-3">
            <div class="filter-list gstr-3B-filter">
                <a href="./gst-3B-summary.php" class="btn active"><i class="fas fa-chart-bar mr-2"></i>Preview</a>
                <a href="./gstr-3B-file-connect-portal.php" class="btn"><i class="fa fa-list mr-2"></i>file</a>
            </div>
            <div class="input-group date" id="startDate">
                <span class="input-group-addon input-group-text datepicker-bg"><ion-icon name="calendar"></ion-icon>
                </span>
                <input type="text" class="form-control border px-5" name="startDate" placeholder="dd/mm/yyyy" />
            </div>
        </div>

        <div class="card bg-light">
            <div class="card-body p-0">
                <a type="button" class="btn add-col setting-menu mt-3" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                <ul class="nav nav-pills nav-preview mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active text-xs" id="pills-summary-tab" data-bs-toggle="pill" data-bs-target="#pills-summary" type="button" role="tab" aria-controls="pills-summary" aria-selected="true"><ion-icon name="document-text-outline" class="mr-2"></ion-icon>Summary</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-xs" id="pills-details-tab" data-bs-toggle="pill" data-bs-target="#pills-details" type="button" role="tab" aria-controls="pills-details" aria-selected="false"><ion-icon name="list-outline" class="mr-2"></ion-icon>Details</button>
                    </li>
                </ul>
                <div class="tab-content nav-preview-content pt-0" id="pills-tabContent">
                    <div class="tab-pane summary fade show active" id="pills-summary" role="tabpanel" aria-labelledby="pills-summary-tab">
                        <div class="card bg-light">
                            <div class="card-body border-top mt-3">
                                <p class="text-xs my-2"><span class="text-danger pr-1">*</span>Table 3.1(a), (b), (c) and (e) are auto-drafted based on values provided in gstr-3B. Whereas Table 3.1(d) is auto-drafted based on GSTR-2B</p>
                                <div class="row summary-block">

                                    <div class="col-lg-4 col-md-4 col-sm-6">
                                        <div class="card my-3 bg-white rounded-3">
                                            <div class="card-body">
                                                <label for="" class="py-1">3.1 Tax on outward and reverse charge inward supplies</label>
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">CGST</p>
                                                        <p class="text-xs my-2">17,516.89</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">CGST</p>
                                                        <p class="text-xs my-2">17,516.89</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">CGST</p>
                                                        <p class="text-xs my-2">17,516.89</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">CGST</p>
                                                        <p class="text-xs my-2">17,516.89</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">Total</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">17,516.89</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6">
                                        <div class="card my-3 bg-white rounded-3">
                                            <div class="card-body">
                                                <label for="" class="py-1">3.1.1 Supplies notified under section 9(5) of the CGST Act, 2017</label>
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">CGST</p>
                                                        <p class="text-xs my-2">17,516.89</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">CGST</p>
                                                        <p class="text-xs my-2">17,516.89</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">CGST</p>
                                                        <p class="text-xs my-2">17,516.89</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">CGST</p>
                                                        <p class="text-xs my-2">17,516.89</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">Total</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">17,516.89</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6">
                                        <div class="card my-3 bg-white rounded-3">
                                            <div class="card-body">
                                                <label for="" class="py-1">3.2 Inter-state supplies</label>
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">Taxable Value</p>
                                                        <p class="text-xs my-2">0.00</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">IGST</p>
                                                        <p class="text-xs my-2">0.00</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">Total</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">0.00</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6">
                                        <div class="card my-3 bg-white rounded-3">
                                            <div class="card-body">
                                                <label for="" class="py-1">3.1 Tax on outward and reverse charge inward supplies</label>
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">CGST</p>
                                                        <p class="text-xs my-2">17,516.89</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">CGST</p>
                                                        <p class="text-xs my-2">17,516.89</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">CGST</p>
                                                        <p class="text-xs my-2">17,516.89</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">CGST</p>
                                                        <p class="text-xs my-2">17,516.89</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">Total</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">17,516.89</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6">
                                        <div class="card my-3 bg-white rounded-3">
                                            <div class="card-body">
                                                <label for="" class="py-1">3.1.1 Supplies notified under section 9(5) of the CGST Act, 2017</label>
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">CGST</p>
                                                        <p class="text-xs my-2">17,516.89</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">CGST</p>
                                                        <p class="text-xs my-2">17,516.89</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">CGST</p>
                                                        <p class="text-xs my-2">17,516.89</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">CGST</p>
                                                        <p class="text-xs my-2">17,516.89</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">Total</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">17,516.89</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6">
                                        <div class="card my-3 bg-white rounded-3">
                                            <div class="card-body">
                                                <label for="" class="py-1">3.2 Inter-state supplies</label>
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">Taxable Value</p>
                                                        <p class="text-xs my-2">0.00</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">IGST</p>
                                                        <p class="text-xs my-2">0.00</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="summary-details">
                                                    <div>
                                                        <p class="font-bold text-sm">Total</p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-sm">0.00</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="tab-pane details fade" id="pills-details" role="tabpanel" aria-labelledby="pills-details-tab">
                        <div class="card bg-light">
                            <div class="card-body px-0">
                                <nav class="details">
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#taxOn" type="button" role="tab" aria-controls="nav-b2b" aria-selected="true">3.1 Tax on</button>
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#supplyNotify" type="button" role="tab" aria-controls="nav-b2brepeat" aria-selected="false">3.11 Supplies notified</button>
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#interState" type="button" role="tab" aria-controls="nav-b2cs" aria-selected="false">3.2 Inter-state</button>
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#eligibleItc" type="button" role="tab" aria-controls="nav-cdnr" aria-selected="false">4. Eligible ITC</button>
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#exeMpt" type="button" role="tab" aria-controls="nav-cdhur" aria-selected="false">5. Exempt</button>
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#interestLateFee" type="button" role="tab" aria-controls="nav-exp" aria-selected="false">5.1 Interest and Late Fee</button>

                                    </div>
                                </nav>
                                <p class="text-xs py-3 px-2 d-flex gap-3"><span class="text-danger pr-1">*</span> Table 3.1(a), (b), (c) and (e) are auto-drafted based on values provided in gstr-3B. Whereas Table 3.1(d) is auto-drafted based on GSTR-2B <span class="btn-close ml-auto">x</span></p>
                                <div class="tab-content pt-0" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="taxOn" role="tabpanel" aria-labelledby="nav-taxOn-tab">
                                        <table class="table table-hover summary-details-table">
                                            <thead>
                                                <tr>
                                                    <th>Nature of Supplies</th>
                                                    <th>Total Taxable Value (₹)</th>
                                                    <th>IGST (₹)</th>
                                                    <th>CGST (₹)</th>
                                                    <th>SGST (₹)</th>
                                                    <th>CESS (₹)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>(a) Outward taxable supplies (other than zero rated, nil rated and exempted)</td>
                                                    <td>
                                                        <input type="text" class="form-control" value="7,00,675,00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>(b) Outward taxable supplies (zero rated)</td>
                                                    <td>
                                                        <input type="text" class="form-control" value="7,00,675,00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <!-- <input type="text" class="form-control label-hidden" value="0.00"> -->
                                                    </td>
                                                    <td>
                                                        <!-- <input type="text" class="form-control" value="0.00"> -->
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>(C) Other outward supplies (Nil rated, exempted)</td>
                                                    <td>
                                                        <input type="text" class="form-control" value="7,00,675,00">
                                                    </td>
                                                    <td>
                                                    </td>
                                                    <td>
                                                    </td>
                                                    <td>
                                                    </td>
                                                    <td>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>(d) Inward supplies (liable to reverse charge)</td>
                                                    <td>
                                                        <input type="text" class="form-control" value="7,00,675,00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>(e) Non-GST outward supplies</td>
                                                    <td>
                                                        <input type="text" class="form-control" value="7,00,675,00">
                                                    </td>
                                                    <td>
                                                    </td>
                                                    <td>
                                                    </td>
                                                    <td>
                                                    </td>
                                                    <td>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="supplyNotify" role="tabpanel" aria-labelledby="nav-supplyNotify-tab">
                                        test
                                    </div>
                                    <div class="tab-pane fade" id="interState" role="tabpanel" aria-labelledby="nav-interState-tab">
                                        <table class="table table-hover summary-details-table">
                                            <thead>
                                                <tr>
                                                    <th>Place of Supply (State/UT)</th>
                                                    <th>Total Taxable Value (₹)</th>
                                                    <th>Amount of Integrated Tax (₹)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <select name="" id="" class="form-control">
                                                            <option value="1">01-Delhi</option>
                                                            <option value="2">02-Delhi</option>
                                                            <option value="3">03-Delhi</option>
                                                            <option value="4">04-Delhi</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <select name="" id="" class="form-control">
                                                            <option value="1">01-Delhi</option>
                                                            <option value="2">02-Delhi</option>
                                                            <option value="3">03-Delhi</option>
                                                            <option value="4">04-Delhi</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <select name="" id="" class="form-control">
                                                            <option value="1">01-Delhi</option>
                                                            <option value="2">02-Delhi</option>
                                                            <option value="3">03-Delhi</option>
                                                            <option value="4">04-Delhi</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="eligibleItc" role="tabpanel" aria-labelledby="nav-eligibleItc-tab">
                                        <table class="table table-hover summary-details-table">
                                            <thead>
                                                <tr>
                                                    <th>Details</th>
                                                    <th>IGST (₹)</th>
                                                    <th>CGST (₹)</th>
                                                    <th>SGST (₹)/th>
                                                    <th>CESS (₹)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="5" class="font-bold">(A) ITC Available (whether in full of part)</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        (1) Import of goods
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        (2) Import of services
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        (3) Inward supplies liable to reverse charge (other than 1 & 2 above)
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        (4) Inward supplies from ISD
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        (5) All other ITC
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5" class="font-bold">ITC Reserved</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        (1) As per rules 38,42 & 43 of CGST Rules and section 17(5)
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        (2) Others
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="font-bold">
                                                        (C) Net ITC Available (A) - (B)
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00" readonly>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5" class="font-bold">ITC Reserved</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        (1) ITC reclaimed which was reversed under Table 4(B) (2) in earlier tax period
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        (2) Ineligible ITC under section 16(4) & ITC restricted due to poS rules
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="0.00">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="exeMpt" role="tabpanel" aria-labelledby="nav-exeMpt-tab">
                                       test
                                    </div>
                                    <div class="tab-pane fade" id="interestLateFee" role="tabpanel" aria-labelledby="nav-interestLateFee-tab">
                                        test-2
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="modal" id="myModal2">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Table Column Settings</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                        <div class="modal-body" style="max-height: 450px;">
                            <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                            <input type="hidden" name="pageTableName" value="ERP_ACC_JOURNAL" />
                            <div class="modal-body">
                                <div id="dropdownframe"></div>
                                <div id="main2">
                                    <div class="checkAlltd d-flex gap-2 mb-2">
                                        <input type="checkbox" class="grand-checkbox" value="" />
                                        <p class="text-xs font-bold">Check All</p>
                                    </div>
                                    <?php $p = 1; ?>
                                    <table class="colomnTable">
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                Sl</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                Period</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                Voucher Court</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                Taxable Amount</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="<?php echo $p; ?>" />
                                                CGST</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?php echo $p; ?>" />
                                                SGST</td>
                                        </tr>

                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="<?php echo $p; ?>" />
                                                IGST</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                                CESS</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="<?php echo $p; ?>" />
                                                Total Tax</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="<?php echo $p; ?>" />
                                                Invoice Amount</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" name="add-table-settings" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </section>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
    $(document).ready(function() {

        $(function() {
            $('#startDate').datepicker({
                format: 'dd/mm/yyyy'
            });
        });


    });
</script>


<?php
require_once("../common/footer.php");
?>