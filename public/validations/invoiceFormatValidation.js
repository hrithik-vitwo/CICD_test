$(document).ready(function () {
    var resetTimeFlag = false;

    $(document).on("submit", "#add_frm", function (e) {

        let validStatus = 0;
        let specStatus = 0;
        
        // VARIANT VALIDATION
        if ($("#variant_name").val() == "") {
            $(".variant_name").remove();
            $("#variant_name").parent().append('<span class="error variant_name">Variant name is required</span>');
            $(".variant_name").show();

            $(".notesvariant_name").remove();
            $("#notesModalBody").append('<p class="notesvariant_name font-monospace text-danger">Variant name is required</p>');
        } else {
            $(".variant_name").remove();

            $(".notesvariant_name").remove();
            validStatus++;
        }

        // PREFIX VALIDATION
        if ($(".inputDivSec")[0].innerHTML == "") {
            $(".inv_inputDivSec").remove();
            $(".previewDiv").append('<span class="error inv_inputDivSec">Atleast one prefix is required</span>');
            $(".inv_inputDivSec").show();

            $(".notesinputDivSec").remove();
            $("#notesModalBody").append('<p class="notesinputDivSec font-monospace text-danger">Atleast one prefix is required</p>');
        } else {
            $(".inv_inputDivSec").remove();

            $(".notesinputDivSec").remove();
            validStatus++;
        }

        // RESET TIME VALIDATION
        if (resetTimeFlag) {
            if ($(".reset_radio").is(':checked')) {
                $(".inv_reset_radio").remove();
    
                $(".notesreset_radio").remove();
                validStatus++;
            } else {                
                $(".inv_reset_radio").remove();
                $(".previewDiv").append('<span class="error inv_reset_radio">Atleast one option is required</span>');
                $(".inv_reset_radio").show();
    
                $(".notesreset_radio").remove();
                $("#notesModalBody").append('<p class="notesreset_radio font-monospace text-danger">Atleast one option is required</p>');
            }
        } else {
            $(".inv_reset_radio").remove();
    
            $(".notesreset_radio").remove();
            validStatus++;
        }

        for (elem of $(".prefixInput").get()) {
            // DIVIDER VALIDATION
            if ($(elem).val() == "") {
                $(".elemVal").remove();
                $(elem).parent().append('<span class="error elemVal">Value is required</span>');
                $(".elemVal").show();

                $(".noteselemVal").remove();
                $("#notesModalBody").append('<p class="noteselemVal font-monospace text-danger">Value is required</p>');
            } else {
                $(".elemVal").remove();

                $(".noteselemVal").remove();
                specStatus++;
            }
        };

        for (elem of $(".prefixDropDown").get()) {
            // DIVIDER VALIDATION
            if ($(elem).val() == "") {
                $(".elemNameVal").remove();
                $(elem).parent().append('<span class="error elemNameVal">Type is required</span>');
                $(".elemNameVal").show();

                $(".noteselemNameVal").remove();
                $("#notesModalBody").append('<p class="noteselemNameVal font-monospace text-danger">Type is required</p>');
            } else {
                $(".elemNameVal").remove();

                $(".noteselemNameVal").remove();
                specStatus++;
            }
        };

        prefixValidation(e);
        
        if (validStatus !== 3) {
            e.preventDefault();
            $("#exampleModal").modal("show");
        }     
        
        if (specStatus !== $(".prefixInput").length*2) {
            e.preventDefault();
            $("#exampleModal").modal("show");
        }
    });
    
    function prefixValidation(e) {
        
        let otherValidStatus = 0;
        let prefixList = [];

        for (elem of $(".prefixDropDown")) {
            prefixList.push($(elem).val());
        };

        if (prefixList.includes("serial")) {
            $(".inv_serial").remove();

            $(".notesserial").remove();
            otherValidStatus++;
        } else {
            $(".inv_serial").remove();
            $(".previewDiv").append('<span class="error inv_serial">Atleast serial option is required</span>');
            $(".inv_serial").show();

            $(".notesserial").remove();
            $("#notesModalBody").append('<p class="notesserial font-monospace text-danger">Atleast serial option is required</p>');
        }

        if (prefixList.includes("yyyy") || prefixList.includes("fy")) {
            $(".reset_time").show();
            resetTimeFlag = true;
        } else {
            resetTimeFlag = false;
            $(".reset_time").hide();
        }

        if (otherValidStatus !== 1) {
            e.preventDefault();
            $("#prefixModal").modal("show");
        }
        prefixList = [];
    };

    $(document).on("change", ".prefixDropDown", function (e) {
        prefixValidation(e);
    });

    $(document).on("click", ".removeInvNoPrefixDivBtn", function (e) {
        prefixValidation(e);
    });

});