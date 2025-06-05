$(document).ready(function () {
    
    $(document).on("click", "#addNewGrnFormSubmitBtn", function(e){

        let validStatus = 0;

        // POSTING DATE VALIDATION
        if ($("#invoicePostingDate").val() == "") {
            $(".grn_invoicePostingDate").remove();
            $("#invoicePostingDate").parent().parent().append('<span class="error grn_invoicePostingDate">Posting Date is required</span>');
            $(".grn_invoicePostingDate").show();
        } else {
            $(".grn_invoicePostingDate").remove();
            validStatus++;
        }

        // DUE DATE VALIDATION
        if ($("#invoiceDueDate").val() == "") {
            $(".grn_invoiceDueDate").remove();
            $("#invoiceDueDate").parent().parent().append('<span class="error grn_invoiceDueDate">Due Date is required</span>');
            $(".grn_invoiceDueDate").show();
        } else {
            $(".grn_invoiceDueDate").remove();
            validStatus++;
        }

        // DUE DAYS VALIDATION
        if ($("#invoiceDueDays").val() == "") {
            $(".grn_invoiceDueDays").remove();
            $("#invoiceDueDays").parent().parent().append('<span class="error grn_invoiceDueDays">Due Days is required</span>');
            $(".grn_invoiceDueDays").show();
        } else {
            $(".grn_invoiceDueDays").remove();
            validStatus++;
        }

        if (validStatus !== 3) {
            e.preventDefault();
        }
    });
});
