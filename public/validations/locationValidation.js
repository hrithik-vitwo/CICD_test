var regEmail= /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;


$(document).ready(function () {
    
    // CREATE FORM
    $(document).on("click", ".add_branch_location", function(e){

        let validStatus = 0;

        // LOCATION NAME VALIDATION
        if ($("#locationName").val() == "") {
            $(".lctn_locationName").remove();
            $("#locationName").parent().append('<span class="error lctn_locationName">Location name is required</span>');
            $(".lctn_locationName").show();
        } else {
            $(".lctn_locationName").remove();
            validStatus++;
        }

        // PINCODE VALIDATION
        if ($("#pincode").val() == "") {
            $(".lctn_pincode").remove();
            $("#pincode").parent().append('<span class="error lctn_pincode">Pincode is required</span>');
            $(".lctn_pincode").show();
        } else {
            $(".lctn_pincode").remove();
            validStatus++;
        }

        // LOCATION VALIDATION
        if ($("#location").val() == "") {
            $(".lctn_location").remove();
            $("#location").parent().append('<span class="error lctn_location">Location is required</span>');
            $(".lctn_location").show();
        } else {
            $(".lctn_location").remove();
            validStatus++;
        }

        // CITY VALIDATION
        if ($("#city").val() == "") {
            $(".lctn_city").remove();
            $("#city").parent().append('<span class="error lctn_city">City is required</span>');
            $(".lctn_city").show();
        } else {
            $(".lctn_city").remove();
            validStatus++;
        }
        
        // DISTRICT VALIDATION
        if ($("#district").val() == "") {
            $(".lctn_district").remove();
            $("#district").parent().append('<span class="error lctn_district">District is required</span>');
            $(".lctn_district").show();
        } else {
            $(".lctn_district").remove();
            validStatus++;
        }
       
        // STATE VALIDATION
        if ($("#state").val() == "") {
            $(".lctn_state").remove();
            $("#state").parent().append('<span class="error lctn_state">State is required</span>');
            $(".lctn_state").show();
        } else {
            $(".lctn_state").remove();
            validStatus++;
        }

        // FUNCTIONALITIES CHECK VALIDATION
        if (!$(".func_check").is(":checked")) {
            $(".lctn_func_check").remove();
            $(".target_div").append('<span class="error lctn_func_check">You have to select atleast one</span>');
            $(".lctn_func_check").show();
        } else {
            $(".lctn_func_check").remove();
            validStatus++;
        }

        // POC NAME VALIDATION
        if ($("#adminName").val() == "") {
            $(".lctn_adminName").remove();
            $("#adminName").parent().append('<span class="error lctn_adminName">Name is required</span>');
            $(".lctn_adminName").show();
        } else {
            $(".lctn_adminName").remove();
            validStatus++;
        }

        // POC EMAIL VALIDATION
        if ($("#adminEmail").val() == "") {
            $(".lctn_adminEmail").remove();
            $("#adminEmail").parent().append('<span class="error lctn_adminEmail">Email is required</span>');
            $(".lctn_adminEmail").show();
        } else {
            if (regEmail.test($("#adminEmail").val())) {
                $(".lctn_adminEmail").remove();
                validStatus++;
            } else {
                $(".lctn_adminEmail").remove();
                $("#adminEmail").parent().append('<span class="error lctn_adminEmail">Check your email</span>');
                $(".lctn_adminEmail").show();
            };
        }

        // POC PHONE NO VALIDATION
        if ($("#adminPhone").val() == "") {
            $(".lctn_adminPhone").remove();
            $("#adminPhone").parent().append('<span class="error lctn_adminPhone">Phone no is required</span>');
            $(".lctn_adminPhone").show();
        } else {
            if ($("#adminPhone").val().length != 10) {
                $(".lctn_adminPhone").remove();
                $("#adminPhone").parent().append('<span class="error lctn_adminPhone">Check your phone no</span>');
                $(".lctn_adminPhone").show();
            } else {
                $(".lctn_adminPhone").remove();
                validStatus++;
            };
        }

        // POC PASSWORD VALIDATION
        if ($("#adminPassword").val() == "") {
            $(".lctn_adminPassword").remove();
            $("#adminPassword").parent().append('<span class="error lctn_adminPassword">Password is required</span>');
            $(".lctn_adminPassword").show();
        } else {
            $(".lctn_adminPassword").remove();
            validStatus++;
        }

        if (!$(".func_check").is(":checked")) {
            $(".lctn_func_check").remove();
            $(".func_check").parent().append('<span class="error lctn_func_check">Functional Area is required</span>');
            $(".lctn_func_check").show();
        } else {
            $(".lctn_func_check").remove();
            validStatus++;
        }

        // e.preventDefault();
        // alert(validStatus);
        if (validStatus == 12) {
            // console.log("first")
            // e.preventDefault();
            $("#add_frm").submit();
        }
    });

    // EDIT FORM
    $(document).on("click", ".edit_branch_location", function(e){

        let validStatus = 0;

        // LOCATION NAME VALIDATION
        if ($("#locationName").val() == "") {
            $(".lctn_locationName").remove();
            $("#locationName").parent().append('<span class="error lctn_locationName">Location name is required</span>');
            $(".lctn_locationName").show();
        } else {
            $(".lctn_locationName").remove();
            validStatus++;
        }

        // PINCODE VALIDATION
        if ($("#pincode").val() == "") {
            $(".lctn_pincode").remove();
            $("#pincode").parent().append('<span class="error lctn_pincode">Pincode is required</span>');
            $(".lctn_pincode").show();
        } else {
            $(".lctn_pincode").remove();
            validStatus++;
        }

        // LOCATION VALIDATION
        if ($("#location").val() == "") {
            $(".lctn_location").remove();
            $("#location").parent().append('<span class="error lctn_location">Location is required</span>');
            $(".lctn_location").show();
        } else {
            $(".lctn_location").remove();
            validStatus++;
        }

        // CITY VALIDATION
        if ($("#city").val() == "") {
            $(".lctn_city").remove();
            $("#city").parent().append('<span class="error lctn_city">City is required</span>');
            $(".lctn_city").show();
        } else {
            $(".lctn_city").remove();
            validStatus++;
        }
        
        // DISTRICT VALIDATION
        if ($("#district").val() == "") {
            $(".lctn_district").remove();
            $("#district").parent().append('<span class="error lctn_district">District is required</span>');
            $(".lctn_district").show();
        } else {
            $(".lctn_district").remove();
            validStatus++;
        }
       
        // STATE VALIDATION
        if ($("#state").val() == "") {
            $(".lctn_state").remove();
            $("#state").parent().append('<span class="error lctn_state">State is required</span>');
            $(".lctn_state").show();
        } else {
            $(".lctn_state").remove();
            validStatus++;
        }

        // FUNCTIONALITIES CHECK VALIDATION
        if (!$(".func_check").is(":checked")) {
            $(".lctn_func_check").remove();
            $(".target_div").append('<span class="error lctn_func_check">You have to select atleast one</span>');
            $(".lctn_func_check").show();
        } else {
            $(".lctn_func_check").remove();
            validStatus++;
        }

        // POC NAME VALIDATION
        if ($("#adminName").val() == "") {
            $(".lctn_adminName").remove();
            $("#adminName").parent().append('<span class="error lctn_adminName">Name is required</span>');
            $(".lctn_adminName").show();
        } else {
            $(".lctn_adminName").remove();
            validStatus++;
        }

        // POC EMAIL VALIDATION
        if ($("#adminEmail").val() == "") {
            $(".lctn_adminEmail").remove();
            $("#adminEmail").parent().append('<span class="error lctn_adminEmail">Email is required</span>');
            $(".lctn_adminEmail").show();
        } else {
            if (regEmail.test($("#adminEmail").val())) {
                $(".lctn_adminEmail").remove();
                validStatus++;
            } else {
                $(".lctn_adminEmail").remove();
                $("#adminEmail").parent().append('<span class="error lctn_adminEmail">Check your email</span>');
                $(".lctn_adminEmail").show();
            };
        }

        // POC PHONE NO VALIDATION
        if ($("#adminPhone").val() == "") {
            $(".lctn_adminPhone").remove();
            $("#adminPhone").parent().append('<span class="error lctn_adminPhone">Phone no is required</span>');
            $(".lctn_adminPhone").show();
        } else {
            if ($("#adminPhone").val().length != 10) {
                $(".lctn_adminPhone").remove();
                $("#adminPhone").parent().append('<span class="error lctn_adminPhone">Check your phone no</span>');
                $(".lctn_adminPhone").show();
            } else {
                $(".lctn_adminPhone").remove();
                validStatus++;
            };
        }

        // POC PASSWORD VALIDATION
        if ($("#adminPassword").val() == "") {
            $(".lctn_adminPassword").remove();
            $("#adminPassword").parent().append('<span class="error lctn_adminPassword">Password is required</span>');
            $(".lctn_adminPassword").show();
        } else {
            $(".lctn_adminPassword").remove();
            validStatus++;
        }

        if (!$(".func_check").is(":checked")) {
            $(".lctn_func_check").remove();
            $(".func_check").parent().append('<span class="error lctn_func_check">Functional Area is required</span>');
            $(".lctn_func_check").show();
        } else {
            $(".lctn_func_check").remove();
            validStatus++;
        }
        
        if (validStatus !== 12) {
            e.preventDefault();
        }
    });

});