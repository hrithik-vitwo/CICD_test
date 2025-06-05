$(document).ready(function () {

    $("#submitCollectPaymentBtn").on("click", function(e){

        let validStatus = 0;
        
        // CUSTOMER VALIDATION
        if ($("#customerSelect").val() == "") {
            $(".collection_customerSelect").remove();
            $("#customerSelect").parent().append('<span class="error collection_customerSelect">Customer name is required</span>');
            $(".collection_customerSelect").show();

            $(".notescustomerSelect").remove();
            $("#notesModalBody").append('<p class="notescustomerSelect font-monospace text-danger">Customer name is required</p>');
        } else {
            $(".collection_customerSelect").remove();

            $(".notescustomerSelect").remove();
            validStatus++;
        }

        // BANK VALIDATION
        if ($("[name='paymentDetails[bankId]']").val() == "") {
            $(".collection_bank").remove();
            $("[name='paymentDetails[bankId]']").parent().append('<span class="error collection_bank">Bank account is required</span>');
            $(".collection_bank").show();

            $(".notesBank").remove();
            $("#notesModalBody").append('<p class="notesBank font-monospace text-danger">Bank account is required</p>');
        } else {
            $(".collection_bank").remove();

            $(".notesBank").remove();
            validStatus++;
        }

        // AMOUNT VALIDATION
        if ($("[name='paymentDetails[collectPayment]']").val() == "" || parseInt($("[name='paymentDetails[collectPayment]']").val()) <= 0) {
            $(".collection_amount").remove();
            $("[name='paymentDetails[collectPayment]']").parent().append('<span class="error collection_amount">Amount is required</span>');
            $(".collection_amount").show();

            $(".notesAmount").remove();
            $("#notesModalBody").append('<p class="notesAmount font-monospace text-danger">Amount is required</p>');
        } else {
            $(".collection_amount").remove();

            $(".notesAmount").remove();
            validStatus++;
        }
       
        // DOCUMENT DATE VALIDATION
        if ($("[name='paymentDetails[documentDate]']").val() == "") {
            $(".collection_doc_date").remove();
            $("[name='paymentDetails[documentDate]']").parent().append('<span class="error collection_doc_date">Document Date is required</span>');
            $(".collection_doc_date").show();

            $(".notesDocDate").remove();
            $("#notesModalBody").append('<p class="notesDocDate font-monospace text-danger">Document Date is required</p>');
        } else {
            $(".collection_doc_date").remove();

            $(".notesDocDate").remove();
            validStatus++;
        }
        
        // POSTING DATE VALIDATION
        if ($("[name='paymentDetails[postingDate]']").val() == "") {
            $(".collection_posting_date").remove();
            $("[name='paymentDetails[postingDate]']").parent().append('<span class="error collection_posting_date">Posting Date is required</span>');
            $(".collection_posting_date").show();

            $(".notesPostingDate").remove();
            $("#notesModalBody").append('<p class="notesPostingDate font-monospace text-danger">Posting Date is required</p>');
        } else {
            $(".collection_posting_date").remove();

            $(".notesPostingDate").remove();
            validStatus++;
        }        
        
        // TRANSACTION NUMBER VALIDATION
        if ($("[name='paymentDetails[tnxDocNo]']").val() == "") {
            $(".collection_txndocno").remove();
            $("[name='paymentDetails[tnxDocNo]']").parent().append('<span class="error collection_txndocno">Transaction No is required</span>');
            $(".collection_txndocno").show();

            $(".notestnxDocNo").remove();
            $("#notesModalBody").append('<p class="notestnxDocNo font-monospace text-danger">Transaction No is required</p>');
        } else {
            $(".collection_txndocno").remove();

            $(".notestnxDocNo").remove();
            validStatus++;
        }        

        if (validStatus !== 6) {
            e.preventDefault();
            $("#exampleCollectionModal").modal("show");
        } else if (validStatus === 6) {
            $("#exampleModal").modal("show");
        }
        
    });

});