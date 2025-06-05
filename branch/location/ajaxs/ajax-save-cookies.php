<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
$headerData = array('Content-Type: application/json');

function setCookietoDb($formData, $cookieName)
{
    setcookie($cookieName, (json_encode($formData['settingsCheckbox'])), time() + 86400 * 30, '/');
    $editDataObj = updateInsertTableSettingsCheckbox(($formData), $_SESSION["logedBranchAdminInfo"]["adminId"]);
    return $editDataObj;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['act'] === 'manageSo') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieManageSo";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo  json_encode($editDataObj);
    } elseif ($_POST['act'] === 'managePendingSo') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiePendingSo";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'manageApproveSo') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieApproveSo";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'manageExcepSo') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieExceptionalSo";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'itemOrder') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieItemOrder";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'pendingJob') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiePendingJob";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'doneJob') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieDonejob";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'soDelivery') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesoDelivery";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'soPgi') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesoPgi";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'proformaIv') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesProformaInvoice";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'soquotation') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesquotation";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo  json_encode($editDataObj);
    } elseif ($_POST['act'] === 'partyOrder') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiesPartyorder";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'fgStock') {
        $formData = $_POST['formData'];
        $cookieName = "cookiefgStock";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'manageCustomer') {
        $formData = $_POST['formData'];
        $cookieName = "cookieManageCustomer";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    elseif ($_POST['act'] === 'manageCustomerList') {
        $formData = $_POST['formData'];
        $cookieName = "cookieManageCustomerList";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    elseif ($_POST['act'] === 'manageCustomerDownload') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieManageCustomerDownload";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
     elseif ($_POST['act'] === 'items') {
        $formData = $_POST['formData'];
        $cookieName = "cookieslocationItem";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'rfq') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesrfq";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'vmquotation') {
        $formData = $_POST['formData'];
        $cookieName = "cookiemanagevmquotation";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'vendors') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiemanagevendors";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } 
    elseif ($_POST['act'] === 'vendorsDownload') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiemanagevendorsDownload";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    elseif ($_POST['act'] === 'inventory') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesinventory";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'goods') {
        $formData = $_POST['formData'];
        $cookieName = "cookiegooditem";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'goodsItem') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesgoodstypeItm";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'warehouse') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieTableWarehouse";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'strageloc') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieStorageloc";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'rmStock') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesrmStock";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'rmStock') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesSfgStock";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'assets') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesassets";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'assetsUnder') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesassetsUnderCons";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'managePr') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesManagePr";
        $editDataObj = setCookietoDb($_POST['formData'], $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'prodDeclare') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesprodDeclare";
        $editDataObj = setCookietoDb($_POST['formData'], $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'prodDeclareSfg') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesprodDeclareSfg";
        $editDataObj = setCookietoDb($_POST['formData'], $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'prodDeclareFg') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesprodDeclareFg";
        $editDataObj = setCookietoDb($_POST['formData'], $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'boq') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiesBoq";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'mangePoall') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesPoall";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'poService') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesPoService";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'poClosed') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesPoClosed";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'poOpen') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesPoOpen";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'poPending') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesPoPending";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'poItems') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesPoItems";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'poRecievedItems') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesPoItems";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'poRecievedItems') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesPoItems";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'poRecievedItemsFg') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesPoItemsFg";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'poRecievedItemsSfg') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesPoItemsSfg";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'poRecievedItemsRm') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesPoItemsRm";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'poRecievedItemsRejected') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesPoItemsRejected";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'payroll') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesPayroll";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'salary') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesSalary";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'tds') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesTds";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'esi') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesEsi";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'pf') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesPf";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'ptax') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesPtax";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'managePrOpen') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesPrOpen";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'managePrClosed') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesPrClosed";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'grnPending') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesGrnPending";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'grnPosted') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesGrnPosted";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'mrpConcised') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesMrpConcised";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'mrpDetailed') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesMrpDetailed";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'goodsSfg') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesGoodsSfg";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'goodsFg') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesGoodsFg";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'debitnotes') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiedebitNotes";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'creditnote') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiecreditNotes";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'goodsService') {
        $formData = $_POST['formData'];
        $cookieName = "cookiesGoodsService";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'faliledAccInv') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiefailedaccInv";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'managecollectpayment') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiescollectpayment";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'manageadvancepayment') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieManagePayments";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'grninvpending') {
        $formData = $_POST['formData'];
        $cookieName = "cookiegrninvpending";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'grninvposted') {
        $formData = $_POST['formData'];
        $cookieName = "cookiegrninvposted";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'goodsStock') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiesgoodsStock";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'quotationvm') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiesquotationvm";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'manageNetSalesReport') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieManageNetSales";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'manageInvoiceList') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieInvoiceList";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'managePendingGrn') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiePendingGrn";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'managePostedGrn') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiePostedGrn";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } elseif ($_POST['act'] === 'tdata') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiePostedGrn";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    elseif ($_POST['act'] === 'manageProdDeclaire') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiesManageProdDeclair";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    elseif ($_POST['act'] === 'manageProductionOrder') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieProductionOrder";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    elseif ($_POST['act'] === 'manageJournal') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieManageJournal";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'manageNetSalesReportCustomerWise') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieManageNetSalesCustomerWise";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'manageNetSalesReportProductWise') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieManageNetSalesProductWise";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    elseif ($_POST['act'] === 'raclog') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieRackLOg";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } 
    else if ($_POST['act'] === 'manageNetSalesReportProductGroupWise') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieManageNetSalesProductGroupWise";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'manageVendorInvoice') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieVendorInvoice";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'manageBoq') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieManageBoq";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'RADetailed') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieReceivableAnalysisDetailed";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'RAConcised') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieReceivableAnalysisConcised";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'tdsVendorBranch') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieTdsVendorBranch";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'tdsSectionBranch') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieTdsSectionBranch";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'cookiereconInv') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiereconInv";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'stockReport') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieStockReport";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'closingStockReport') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieClosingStockReport";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo  json_encode($editDataObj);
    } 
    else if ($_POST['act'] === 'cookiereverseInv') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiereverseInv";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo  json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'failedpgi') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiefailedpgi";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo  json_encode($editDataObj);
    } 
    else if ($_POST['act'] === 'failedpaymentDebitNote') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiefailedpaymentDebitNote";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'failedpaymentCreditNote') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiefailedpaymentCreditNote";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'failedproductiondeclaration') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiefailedproductiondeclaration";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'failedpayroll') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiefailedpayroll";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'failedgrnsrn') {
        $formData = $_POST['formData'];
        $cookieName = "cookiefailedgrnsrn";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'failedgrnsrnIv') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiefailedgrnsrnIv";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'failedcollectpayment') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiefailedcollectpayment";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'failedpaymentAcc') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiefailedpaymentAcc";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'mrpGroup') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiemrpGroup";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'mrpVariant') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiemrpVariant";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'mrpCustomerDiscountGroup') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieCusDiscountGroup";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'mrpitemDiscountGroup') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieitemDiscountGroup";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    
    else if ($_POST['act'] === 'discountvariant') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieDiscountVariant";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'vendorPayment') {
        $formData = $_POST['fromData'];
        $cookieName = "cookieVendorPayment";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if ($_POST['act'] === 'grnInvoice') {
        $formData = $_POST['fromData'];
        $cookieName = "cookiegrnInvoice";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }else if($_POST['act']=='far')
    {
        $formData = $_POST['fromData'];
        $cookieName = "far";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if($_POST['act']=='manageCookieBomReport')
    {
        $formData = $_POST['fromData'];
        $cookieName = "cookieManageBomReport";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    }
    else if($_POST['act']=='assetsInuse')
    {
        $formData = $_POST['fromData'];
        $cookieName = "cookieAssetsInuse";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);
    } else if($_POST['act']=='vendorPaymentPro')
    {
        $formData = $_POST['fromData'];
        $cookieName = "cookieVendorPaymentPro";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);

    }
    else if($_POST['act']=='layerlog')
    {
        $formData = $_POST['fromData'];
        $cookieName = "cookieLayer";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);

    }
    else if($_POST['act']=='binlog')
    {
        $formData = $_POST['fromData'];
        $cookieName = "cookieBinLOg";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);

    }
    else if($_POST['act']=='QaRejected')
    {
        $formData = $_POST['fromData'];
        $cookieName = "cookieQaRejected";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);

    }
    else if($_POST['act']=='QaGoods_List')
    {
        $formData = $_POST['fromData'];
        $cookieName = "cookieQaGoodsList";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);

    }
    else if($_POST['act']=='stocktransferlist')
    {
        $formData = $_POST['formData'];
        $cookieName = "cookieStockTransferList";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);

    }
    else if($_POST['act']=='transactionalDaybook')
    {
        $formData = $_POST['fromData'];
        $cookieName = "cookieDaybookTransac";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);

    }
    else if($_POST['act']=='transactionalDaybookConcised')
    {
        $formData = $_POST['fromData'];
        $cookieName = "cookieDaybookTransacConcised";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);

    }
    else if($_POST['act']=='bank_List')
    {
        $formData = $_POST['fromData'];
        $cookieName = "cookieBankList";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);

    }
    else if($_POST['act']=='trialBalance')
    {
        $formData = $_POST['fromData'];
        $cookieName = "cookieTrialBalance";
        $editDataObj = setCookietoDb($formData, $cookieName);
        echo json_encode($editDataObj);

    }

    
    // else{
    //     setcookie("cookieTableStockReport", json_encode($_POST['settingsCheckbox']), time() + 86400 * 30, '/');
    //      $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    //     //  echo json_encode($editDataObj["message"]);
    // }
}
