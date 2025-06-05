$(document).ready(function () {
    
    $(document).on("click", "#confirmAndReleaseOrderBtn", function(e){

        let validStatus = 0;

        // POSTING DATE VALIDATION
        if ($("#rmRequiredDate").val() == "") {
            $(".prodOrderRMDate").remove();
            $("#rmRequiredDate").parent().append('<span class="error prodOrderRMDate">RM Date is required</span>');
            $(".prodOrderRMDate").show();

            $(".notesRMDate").remove();
            $("#notesModalBody").append('<p class="notesRMDate font-monospace text-danger">RM Date is required</p>');
        } else {
            $(".prodOrderRMDate").remove();

            $(".notesRMDate").remove();
            validStatus++;
        }
        
        if (validStatus !== 1) {
            e.preventDefault();
            $("#exampleModal").modal("show");
        }
    });
});
