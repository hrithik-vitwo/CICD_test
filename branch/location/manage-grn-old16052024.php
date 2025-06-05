<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/common/func-common.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/branch/func-grn-controller.php");

?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
    .content-wrapper {
        height: auto !important;
    }

    .row.grn-create .card ul {
        padding-left: 0;
    }

    .quick-registration-vendor {
        overflow: auto;
    }

    div.grn-table {
        margin: 40px 0 10px;
    }

    table.grn-table tr td {
        padding: 5px 15px !important;
    }

    table.grn-table tr td input,
    table.grn-table tr td select {
        height: 30px;
        width: auto !important;
    }

    table.grn-table tr td select {
        width: auto;
    }

    .derived-qty-info {
        display: inline-block;
    }

    .derived-qty-info p {

        white-space: pre-line;
        top: -53px !important;

    }

    .derived-qty-info::before {
        content: '!' !important;
    }

    span.error {
        position: relative;
        display: block;
        text-align: center;
        top: 0;
        left: 0;
        margin: 7px 0;
        display: flex !important;
        align-items: center;
        gap: 7px;
        justify-content: flex-end;
    }

    span.calculate-error {
        left: 0;
    }

    table.grn-table tr.span-error-tr td {
        background-color: transparent !important;
        height: 0;
        padding: 0 !important;
    }

    .invoice-iframe .modal-dialog {
        max-width: 705px;
    }

    .modal-open {
        overflow: auto !important;
    }

    .row.grn-vendor-details .display-flex select {
        font-size: 9px !important;
        background: none;
        border: 0;
        max-width: 120px;
        cursor: pointer;
        height: auto;
        background-color: #0000001a;
        margin: 6px 0px;
    }

    .total-amount-grn-table .card {
        max-width: 500px;
        margin-left: auto;
        border-radius: 7px;
        margin-bottom: 0;
    }

    .grn-table.pending-grn-view table tr.span-error-tr td.bg-transparent {
        background: #fff !important;
    }

    .total-amount-grn-table .card td {
        padding: 10px 15px;
    }

    .select-type {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .select-type div {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .select-type div label {
        margin-bottom: 0;
    }

    form#mapInvoiceItemCodeForm .select2-container {
        width: 100% !important;
    }

    form#changeInvoiceItemCodeForm .select2-container {
        width: 100% !important;
    }

    .filter-list a.active {
        background: #003060;
        color: #fff;
    }




    @media (max-width: 575px) {
        #grnInvoicePreviewIfram {
            display: block;
        }

        div.grn-table {
            margin: 50px 0;
        }

        span.error {
            left: 440px;
        }
    }
</style>

<?php

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}


if (isset($_GET["post-grn"])) {
    /*
    $accountingObj = new Accounting();


    $data = [
        [
            "acountName" => "Rm 1",
            "accountSubGlCode" => 1100001,
            "accountSubGlParentId" => 9,
            "amount" => 2000
        ],
        [
            "acountName" => "Rm 2",
            "accountSubGlCode" => 1100002,
            "accountSubGlParentId" => 9,
            "amount" => 500
        ],
        [
            "acountName" => "Rm 3",
            "accountSubGlCode" => 1100003,
            "accountSubGlParentId" => 9,
            "amount" => 800
        ],
        [
            "acountName" => "CGST",
            "accountSubGlCode" => 300001,
            "accountSubGlParentId" => 10,
            "amount" => 60
        ],
        [
            "acountName" => "SGST",
            "accountSubGlCode" => 300002,
            "accountSubGlParentId" => 10,
            "amount" => 60
        ],
        [
            "acountName" => "ITC Limited",
            "accountSubGlCode" => 610002,
            "accountSubGlParentId" => 12,
            "amount" => 456789
        ]
    ];

    $mapp=$accountingObj->grnAccountingPosting($data,'grn');
    console($mapp);
 
    $filterBy = '9'; // or Finance etc.

    $new = array_filter($data, function ($var) use ($filterBy) {
       return ($var['accountSubGlParentId'] == $filterBy);
    });
    console($new);
*/

    //require_once("components/grn/grn-create.php");
    //require_once("components/grn/grn-create-new-demo.php");
    require_once("components/grn/grn-create-new.php");
} else if (isset($_GET["view"]) && $_GET["view"] != "" && isset($_GET["type"]) && $_GET["type"] == "grn") {
    require_once("components/grn/grn-view.php");
} else if (isset($_GET["view"]) && $_GET["view"] != "" && isset($_GET["type"]) && $_GET["type"] == "srn") {
    require_once("components/grn/srn-view.php");
} else if (isset($_GET["posting"])) {
    require_once("components/grn/grnIVPosted-list.php");
} else {
    require_once("components/grn/grn-list.php");
}

require_once("../common/footer.php");
?>