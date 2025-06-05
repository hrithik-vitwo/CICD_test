$(document).ready(function () {

    $(document).on("click", "#serviceInvoiceCreationBtn", function(e){

        let validStatus = 0;
        
        // CUSTOMER VALIDATION
        if ($("[name='customerId']").val() == "") {
            $(".inv_customerId").remove();
            $("[name='customerId']").parent().append('<span class="error inv_customerId">Customer is required</span>');
            $(".inv_customerId").show();

            $(".notescustomerId").remove();
            $("#notesModalBody").append('<p class="notescustomerId font-monospace text-danger">Customer is required</p>');
        } else {
            $(".inv_customerId").remove();

            $(".notescustomerId").remove();
            validStatus++;
        }

        // INVOICE DATE VALIDATION
        if ($("[name='invoiceDate']").val() == "") {
            $(".inv_invoiceDate").remove();
            $("[name='invoiceDate']").parent().append('<span class="error inv_invoiceDate">Invoice Date is required</span>');
            $(".inv_invoiceDate").show();

            $(".notesinvoiceDate").remove();
            $("#notesModalBody").append('<p class="notesinvoiceDate font-monospace text-danger">Invoice Date is required</p>');
        } else {
            $(".inv_invoiceDate").remove();

            $(".notesinvoiceDate").remove();
            validStatus++;
        }

        // CREDIT PERIOD VALIDATION
        if ($("[name='creditPeriod']").val() == "") {
            $(".inv_creditPeriod").remove();
            $("[name='creditPeriod']").parent().append('<span class="error inv_creditPeriod">Credit Period is required</span>');
            $(".inv_creditPeriod").show();

            $(".notescreditPeriod").remove();
            $("#notesModalBody").append('<p class="notescreditPeriod font-monospace text-danger">Credit Period is required</p>');
        } else {
            $(".inv_creditPeriod").remove();

            $(".notescreditPeriod").remove();
            validStatus++;
        }

        // BANK VALIDATION
        if ($("[name='bankId']").val() == "") {
            $(".inv_bankId").remove();
            $("[name='bankId']").parent().append('<span class="error inv_bankId">Bank is required</span>');
            $(".inv_bankId").show();

            $(".notesbankId").remove();
            $("#notesModalBody").append('<p class="notesbankId font-monospace text-danger">Bank is required</p>');
        } else {
            $(".inv_bankId").remove();

            $(".notesbankId").remove();
            validStatus++;
        }

        // ITEM VALIDATION
        if ($("#itemsTable").children().length == 0) {
            $(".inv_tableitems").remove();
            $("#itemsTable").parent().append('<span class="error inv_tableitems">Atleast One Service is required</span>');
            $(".inv_tableitems").show();

            $(".notesTableItems").remove();
            $("#notesModalBody").append('<p class="notesTableItems font-monospace text-danger">Atleast One Service is required</p>');
        } else {
            $(".inv_tableitems").remove();

            $(".notesTableItems").remove();
            validStatus++;
        }
        
        if (validStatus !== 5) {
            e.preventDefault();
            $("#exampleModal").modal("show");
        }
    });

});