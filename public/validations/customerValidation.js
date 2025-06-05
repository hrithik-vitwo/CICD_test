var regPan = /[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
var regEmail= /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;


$(document).ready(function () {
    
    $(document).on("submit", "#add_frm", function(e){

        let validStatus = 0;
        let altPh = 1;
        let altEmail = 1;

      

        // TRADE NAME VALIDATION
        if ($("#trade_name").val() == "") {

            $(".cstmr_trade_name").remove();
            $("#trade_name").parent().append('<span class="error cstmr_trade_name">Customer name is required</span>');
            $(".cstmr_trade_name").show();

            $(".notesTradeName").remove();
            $("#notesModalBody").append('<p class="notesTradeName font-monospace text-danger">Customer name is required</p>');
        } else {
            $(".cstmr_trade_name").remove();

            $(".notesTradeName").remove();
            validStatus++;
        }

    //LEGAL NAME VALIDATION
         if ($("#legal_name").val() == "") {

            $(".cstmr_legal_name").remove();
            $("#legal_name").parent().append('<span class="error cstmr_legal_name">Legal name is required</span>');
            $(".cstmr_legal_name").show();

            $(".noteslegal_name").remove();
            $("#notesModalBody").append('<p class="noteslegal_name font-monospace text-danger">Legal name is required</p>');
        } else {
            $(".cstmr_legal_name").remove();

            $(".noteslegal_name").remove();
            validStatus++;
        }

      

        // STATE VALIDATION
        if ($("#state").val() == "") {
            $(".cstmr_state").remove();
            $("#state").parent().append('<span class="error cstmr_state">State is required</span>');
            $(".cstmr_state").show();

            $(".notesState").remove();
            $("#notesModalBody").append('<p class="notesState font-monospace text-danger">State is required</p>');
        } else {
            $(".cstmr_state").remove();

            $(".notesState").remove();
            validStatus++;
        }

        // CITY VALIDATION
        if ($("#city").val() == "") {
            $(".cstmr_city").remove();
            $("#city").parent().append('<span class="error cstmr_city">City is required</span>');
            $(".cstmr_city").show();

            $(".notesCity").remove();
            $("#notesModalBody").append('<p class="notesCity font-monospace text-danger">City is required</p>');
        } else {
            $(".cstmr_city").remove();

            $(".notesCity").remove();
            validStatus++;
        }
        
        // DISTRICT VALIDATION
        if ($("#district").val() == "") {
            $(".cstmr_district").remove();
            $("#district").parent().append('<span class="error cstmr_district">District is required</span>');
            $(".cstmr_district").show();

            $(".notesDistrict").remove();
            $("#notesModalBody").append('<p class="notesDistrict font-monospace text-danger">District is required</p>');
        } else {
            $(".cstmr_district").remove();

            $(".notesDistrict").remove();
            validStatus++;
        }

        // LOCATION VALIDATION
        if ($("#location").val() == "") {
            $(".cstmr_location").remove();
            $("#location").parent().append('<span class="error cstmr_location">Location is required</span>');
            $(".cstmr_location").show();

            $(".notesLocation").remove();
            $("#notesModalBody").append('<p class="notesLocation font-monospace text-danger">Location is required</p>');
        } else {
            $(".cstmr_location").remove();

            $(".notesLocation").remove();
            validStatus++;
        }

        // BUILIDING NO VALIDATION
        if ($("#build_no").val() == "") {
            $(".cstmr_build_no").remove();
            $("#build_no").parent().append('<span class="error cstmr_build_no">Building No. is required</span>');
            $(".cstmr_build_no").show();

            $(".notesbuild_no").remove();
            $("#notesModalBody").append('<p class="notesbuild_no font-monospace text-danger">Building No. is required</p>');
        } else {
            $(".cstmr_build_no").remove();

            $(".notesbuild_no").remove();
            validStatus++;
        }

        // STREET NAME VALIDATION
        if ($("#street_name").val() == "") {
            $(".cstmr_street_name").remove();
            $("#street_name").parent().append('<span class="error cstmr_street_name">Street Name is required</span>');
            $(".cstmr_street_name").show();

            $(".notesstreet_name").remove();
            $("#notesModalBody").append('<p class="notesstreet_name font-monospace text-danger">Street Name is required</p>');
        } else {
            $(".cstmr_street_name").remove();

            $(".notesstreet_name").remove();
            validStatus++;
        }

        // PINCODE VALIDATION
        if ($("#pincode").val() == "") {
            $(".cstmr_pincode").remove();
            $("#pincode").parent().append('<span class="error cstmr_pincode">Pincode is required</span>');
            $(".cstmr_pincode").show();

            $(".notesPincode").remove();
            $("#notesModalBody").append('<p class="notesPincode font-monospace text-danger">Pincode is required</p>');
        } else {
            $(".cstmr_pincode").remove();

            $(".notesPincode").remove();
            validStatus++;
        }

       

        // CREDIT PERIOD VALIDATION
        if ($("#customer_credit_period").val() == "") {
            $(".cstmr_credit_period").remove();
            $("#customer_credit_period").parent().append('<span class="error cstmr_credit_period">Credit period is required</span>');
            $(".cstmr_credit_period").show();

            $(".notesCreditPeriod").remove();
            $("#notesModalBody").append('<p class="notesCreditPeriod font-monospace text-danger">Credit period is required</p>');
        } else {
            $(".cstmr_credit_period").remove();

            $(".notesCreditPeriod").remove();
            validStatus++;
        }

        // POC NAME VALIDATION
        if ($("#adminName").val() == "") {
            $(".cstmr_adminName").remove();
            $("#adminName").parent().append('<span class="error cstmr_adminName">Name is required</span>');
            $(".cstmr_adminName").show();

            $(".notesAdminName").remove();
            $("#notesModalBody").append('<p class="notesAdminName font-monospace text-danger">Name is required</p>');
        } else {
            $(".cstmr_adminName").remove();
            
            $(".notesAdminName").remove();
            validStatus++;
        }

        // POC DESIGNATION VALIDATION
        if ($("#customer_authorised_person_designation").val() == "") {
            $(".cstmr_authorised_person_designation").remove();
            $("#customer_authorised_person_designation").parent().append('<span class="error cstmr_authorised_person_designation">Designation is required</span>');
            $(".cstmr_authorised_person_designation").show();

            $(".notesAuthPersDesignation").remove();
            $("#notesModalBody").append('<p class="notesAuthPersDesignation font-monospace text-danger">Designation is required</p>');
        } else {
            $(".cstmr_authorised_person_designation").remove();

            $(".notesAuthPersDesignation").remove();
            validStatus++;
        }

        // POC PHONE NO VALIDATION
        if ($("#adminPhone").val() == "") {
            $(".cstmr_adminPhone").remove();
            $("#adminPhone").parent().append('<span class="error cstmr_adminPhone">Phone no is required</span>');
            $(".cstmr_adminPhone").show();

            $(".notesAdminPhone").remove();
            $("#notesModalBody").append('<p class="notesAdminPhone font-monospace text-danger">Phone no is required</p>');
        } else {
            if ($("#adminPhone").val().length != 10) {
                $(".cstmr_adminPhone").remove();
                $("#adminPhone").parent().append('<span class="error cstmr_adminPhone">Check your phone no</span>');
                $(".cstmr_adminPhone").show();

                $(".notesAdminPhone").remove();
                $("#notesModalBody").append('<p class="notesAdminPhone font-monospace text-danger">Check your phone no</p>');
            } else {
                $(".cstmr_adminPhone").remove();

                $(".notesAdminPhone").remove();
                validStatus++;
            };
        }

        // POC ALTERNATIVE PHONE NO VALIDATION
        if ($("#customer_authorised_person_phone").val() != "") {
            if ($("#customer_authorised_person_phone").val().length != 10) {
                $(".cstmr_authorised_person_phone").remove();
                $("#customer_authorised_person_phone").parent().append('<span class="error cstmr_authorised_person_phone">Check your phone no</span>');
                $(".cstmr_authorised_person_phone").show();

                $(".notesAuthPersPhone").remove();
                $("#notesModalBody").append('<p class="notesAuthPersPhone font-monospace text-danger">Check your phone no</p>');
                altPh--;
            } else {
                $(".cstmr_authorised_person_phone").remove();

                $(".notesAuthPersPhone").remove();
                altPh++;
            };
        } else {
            $(".cstmr_authorised_person_phone").remove();

            $(".notesAuthPersPhone").remove();
        }

        // POC EMAIL VALIDATION
        if ($("#adminEmail").val() == "") {
            $(".cstmr_adminEmail").remove();
            $("#adminEmail").parent().append('<span class="error cstmr_adminEmail">Email is required</span>');
            $(".cstmr_adminEmail").show();

            $(".notesAdminEmail").remove();
            $("#notesModalBody").append('<p class="notesAdminEmail font-monospace text-danger">Email is required</p>');
        } else {
            if (regEmail.test($("#adminEmail").val())) {
                $(".cstmr_adminEmail").remove();

                $(".notesAdminEmail").remove();
                validStatus++;
            } else {
                $(".cstmr_adminEmail").remove();
                $("#adminEmail").parent().append('<span class="error cstmr_adminEmail">Check your email</span>');
                $(".cstmr_adminEmail").show();

                $(".notesAdminEmail").remove();
                $("#notesModalBody").append('<p class="notesAdminEmail font-monospace text-danger">Check your Email</p>');
            };
        }

        // POC ALTERNATIVE PHONE NO VALIDATION
        if ($("#customer_authorised_person_email").val() != "") {
            if (regEmail.test($("#customer_authorised_person_email").val())) {
                $(".cstmr_authorised_person_email").remove();

                $(".notesAuthPersEmail").remove();
                altEmail++;
            } else {
                $(".cstmr_authorised_person_email").remove();
                $("#customer_authorised_person_email").parent().append('<span class="error cstmr_authorised_person_email">Check your email</span>');
                $(".cstmr_authorised_person_email").show();

                $(".notesAuthPersEmail").remove();
                $("#notesModalBody").append('<p class="notesAuthPersEmail font-monospace text-danger">Check your email</p>');
                altEmail--;
            };
        } else {
            $(".cstmr_authorised_person_email").remove();

            $(".notesAuthPersEmail").remove();
        }

        // POC LOGIN PASSWORD LENGTH VALIDATION

        if ($("#adminPassword").val() == "") {
            $(".cstmr_adminPassword").remove();
            $("#adminPassword").parent().append('<span class="error cstmr_adminPassword">Password is required</span>');
            $(".cstmr_adminPassword").show();

            $(".notesAdminPassword").remove();
            $("#notesModalBody").append('<p class="notesAdminPassword font-monospace text-danger">Password is required</p>');
        } else {
            if ($("#adminPassword").val().length < 4) {
                $(".cstmr_adminPassword").remove();
                $("#adminPassword").parent().append('<span class="error cstmr_adminPassword">Password must be at least 4 characters</span>');
                $(".cstmr_adminPassword").show();

                $(".notesAdminPassword").remove();
                $("#notesModalBody").append('<p class="notesAdminPassword font-monospace text-danger">Password must be at least 4 characters</p>');
            } else {
                $(".cstmr_adminPassword").remove();
                $(".notesAdminPassword").remove();
                validStatus++;
            }
        }


        
        if (validStatus !== 15) {
            e.preventDefault();
            $("#exampleModal").modal("show");
        } else if (altPh == 0) {
            e.preventDefault();
            $("#exampleModal").modal("show");
        } else if (altEmail == 0) {
            e.preventDefault();
            $("#exampleModal").modal("show");
        } else {
            $("#customerCreateBtn").prop('disabled', true);
        }
    });


    $(document).on("submit", "#edit_frm", function(e){
       // alert(1);
        let validStatus = 0;
        let altPh = 1;
        let altEmail = 1;

      

        // TRADE NAME VALIDATION
        if ($("#trade_name").val() == "") {
            $(".cstmr_trade_name").remove();
            $("#trade_name").parent().append('<span class="error cstmr_trade_name">Customer name is required</span>');
            $(".cstmr_trade_name").show();

            $(".notesTradeName").remove();
            $("#notesModalBody").append('<p class="notesTradeName font-monospace text-danger">Customer name is required</p>');
        } else {
            $(".cstmr_trade_name").remove();

            $(".notesTradeName").remove();
            validStatus++;
        }

     

      

        // CITY VALIDATION
        if ($("#city").val() == "") {
            $(".cstmr_city").remove();
            $("#city").parent().append('<span class="error cstmr_city">City is required</span>');
            $(".cstmr_city").show();

            $(".notesCity").remove();
            $("#notesModalBody").append('<p class="notesCity font-monospace text-danger">City is required</p>');
        } else {
            $(".cstmr_city").remove();

            $(".notesCity").remove();
            validStatus++;
        }
        
        // DISTRICT VALIDATION
        if ($("#district").val() == "") {
            $(".cstmr_district").remove();
            $("#district").parent().append('<span class="error cstmr_district">District is required</span>');
            $(".cstmr_district").show();

            $(".notesDistrict").remove();
            $("#notesModalBody").append('<p class="notesDistrict font-monospace text-danger">District is required</p>');
        } else {
            $(".cstmr_district").remove();

            $(".notesDistrict").remove();
            validStatus++;
        }

        // LOCATION VALIDATION
        if ($("#location").val() == "") {
            $(".cstmr_location").remove();
            $("#location").parent().append('<span class="error cstmr_location">Location is required</span>');
            $(".cstmr_location").show();

            $(".notesLocation").remove();
            $("#notesModalBody").append('<p class="notesLocation font-monospace text-danger">Location is required</p>');
        } else {
            $(".cstmr_location").remove();

            $(".notesLocation").remove();
            validStatus++;
        }

        
        // BUILIDING NO VALIDATION
        if ($("#build_no").val() == "") {
            $(".cstmr_build_no").remove();
            $("#build_no").parent().append('<span class="error cstmr_build_no">Building No. is required</span>');
            $(".cstmr_build_no").show();

            $(".notesbuild_no").remove();
            $("#notesModalBody").append('<p class="notesbuild_no font-monospace text-danger">Building No. is required</p>');
        } else {
            $(".cstmr_build_no").remove();

            $(".notesbuild_no").remove();
            validStatus++;
        }

        // STREET NAME VALIDATION
        if ($("#street_name").val() == "") {
            $(".cstmr_street_name").remove();
            $("#street_name").parent().append('<span class="error cstmr_street_name">Street Name is required</span>');
            $(".cstmr_street_name").show();

            $(".notesstreet_name").remove();
            $("#notesModalBody").append('<p class="notesstreet_name font-monospace text-danger">Street Name is required</p>');
        } else {
            $(".cstmr_street_name").remove();

            $(".notesstreet_name").remove();
            validStatus++;
        }

        // PINCODE VALIDATION
        if ($("#pincode").val() == "") {
            $(".cstmr_pincode").remove();
            $("#pincode").parent().append('<span class="error cstmr_pincode">Pincode is required</span>');
            $(".cstmr_pincode").show();

            $(".notesPincode").remove();
            $("#notesModalBody").append('<p class="notesPincode font-monospace text-danger">Pincode is required</p>');
        } else {
            $(".cstmr_pincode").remove();

            $(".notesPincode").remove();
            validStatus++;
        }

       

        // CREDIT PERIOD VALIDATION
        if ($("#customer_credit_period").val() == "") {
            $(".cstmr_credit_period").remove();
            $("#customer_credit_period").parent().append('<span class="error cstmr_credit_period">Credit period is required</span>');
            $(".cstmr_credit_period").show();

            $(".notesCreditPeriod").remove();
            $("#notesModalBody").append('<p class="notesCreditPeriod font-monospace text-danger">Credit period is required</p>');
        } else {
            $(".cstmr_credit_period").remove();

            $(".notesCreditPeriod").remove();
            validStatus++;
        }

        // POC NAME VALIDATION
        if ($("#adminName").val() == "") {
            $(".cstmr_adminName").remove();
            $("#adminName").parent().append('<span class="error cstmr_adminName">Name is required</span>');
            $(".cstmr_adminName").show();

            $(".notesAdminName").remove();
            $("#notesModalBody").append('<p class="notesAdminName font-monospace text-danger">Name is required</p>');
        } else {
            $(".cstmr_adminName").remove();
            
            $(".notesAdminName").remove();
            validStatus++;
        }

        // POC DESIGNATION VALIDATION
        if ($("#customer_authorised_person_designation").val() == "") {
            $(".cstmr_authorised_person_designation").remove();
            $("#customer_authorised_person_designation").parent().append('<span class="error cstmr_authorised_person_designation">Designation is required</span>');
            $(".cstmr_authorised_person_designation").show();

            $(".notesAuthPersDesignation").remove();
            $("#notesModalBody").append('<p class="notesAuthPersDesignation font-monospace text-danger">Designation is required</p>');
        } else {
            $(".cstmr_authorised_person_designation").remove();

            $(".notesAuthPersDesignation").remove();
            validStatus++;
        }

        // POC PHONE NO VALIDATION
        if ($("#adminPhone").val() == "") {
            $(".cstmr_adminPhone").remove();
            $("#adminPhone").parent().append('<span class="error cstmr_adminPhone">Phone no is required</span>');
            $(".cstmr_adminPhone").show();

            $(".notesAdminPhone").remove();
            $("#notesModalBody").append('<p class="notesAdminPhone font-monospace text-danger">Phone no is required</p>');
        } else {
            if ($("#adminPhone").val().length != 10) {
                $(".cstmr_adminPhone").remove();
                $("#adminPhone").parent().append('<span class="error cstmr_adminPhone">Check your phone no</span>');
                $(".cstmr_adminPhone").show();

                $(".notesAdminPhone").remove();
                $("#notesModalBody").append('<p class="notesAdminPhone font-monospace text-danger">Check your phone no</p>');
            } else {
                $(".cstmr_adminPhone").remove();

                $(".notesAdminPhone").remove();
                validStatus++;
            };
        }

        // POC ALTERNATIVE PHONE NO VALIDATION
        if ($("#customer_authorised_person_phone").val() != "") {
            if ($("#customer_authorised_person_phone").val().length != 10) {
                $(".cstmr_authorised_person_phone").remove();
                $("#customer_authorised_person_phone").parent().append('<span class="error cstmr_authorised_person_phone">Check your phone no</span>');
                $(".cstmr_authorised_person_phone").show();

                $(".notesAuthPersPhone").remove();
                $("#notesModalBody").append('<p class="notesAuthPersPhone font-monospace text-danger">Check your phone no</p>');
                altPh--;
            } else {
                $(".cstmr_authorised_person_phone").remove();

                $(".notesAuthPersPhone").remove();
                altPh++;
            };
        } else {
            $(".cstmr_authorised_person_phone").remove();

            $(".notesAuthPersPhone").remove();
        }

        // POC EMAIL VALIDATION
        if ($("#adminEmail").val() == "") {
            $(".cstmr_adminEmail").remove();
            $("#adminEmail").parent().append('<span class="error cstmr_adminEmail">Email is required</span>');
            $(".cstmr_adminEmail").show();

            $(".notesAdminEmail").remove();
            $("#notesModalBody").append('<p class="notesAdminEmail font-monospace text-danger">Email is required</p>');
        } else {
            if (regEmail.test($("#adminEmail").val())) {
                $(".cstmr_adminEmail").remove();

                $(".notesAdminEmail").remove();
                validStatus++;
            } else {
                $(".cstmr_adminEmail").remove();
                $("#adminEmail").parent().append('<span class="error cstmr_adminEmail">Check your email</span>');
                $(".cstmr_adminEmail").show();

                $(".notesAdminEmail").remove();
                $("#notesModalBody").append('<p class="notesAdminEmail font-monospace text-danger">Check your Email</p>');
            };
        }

        // POC ALTERNATIVE PHONE NO VALIDATION
        if ($("#customer_authorised_person_email").val() != "") {
            if (regEmail.test($("#customer_authorised_person_email").val())) {
                $(".cstmr_authorised_person_email").remove();

                $(".notesAuthPersEmail").remove();
                altEmail++;
            } else {
                $(".cstmr_authorised_person_email").remove();
                $("#customer_authorised_person_email").parent().append('<span class="error cstmr_authorised_person_email">Check your email</span>');
                $(".cstmr_authorised_person_email").show();

                $(".notesAuthPersEmail").remove();
                $("#notesModalBody").append('<p class="notesAuthPersEmail font-monospace text-danger">Check your email</p>');
                altEmail--;
            };
        } else {
            $(".cstmr_authorised_person_email").remove();

            $(".notesAuthPersEmail").remove();
        }

        // POC LOGIN PASSWORD LENGTH VALIDATION

        if ($("#adminPassword").val() == "") {
            $(".cstmr_adminPassword").remove();
            $("#adminPassword").parent().append('<span class="error cstmr_adminPassword">Password is required</span>');
            $(".cstmr_adminPassword").show();

            $(".notesAdminPassword").remove();
            $("#notesModalBody").append('<p class="notesAdminPassword font-monospace text-danger">Password is required</p>');
        } else {
            if ($("#adminPassword").val().length < 4) {
                $(".cstmr_adminPassword").remove();
                $("#adminPassword").parent().append('<span class="error cstmr_adminPassword">Password must be at least 4 characters</span>');
                $(".cstmr_adminPassword").show();

                $(".notesAdminPassword").remove();
                $("#notesModalBody").append('<p class="notesAdminPassword font-monospace text-danger">Password must be at least 4 characters</p>');
            } else {
                $(".cstmr_adminPassword").remove();
                $(".notesAdminPassword").remove();
                validStatus++;
            }
        }

        
      //  alert(validStatus);
        if (validStatus !== 13) {
            e.preventDefault();
            $("#exampleModal").modal("show");
        } else if (altPh == 0) {
            e.preventDefault();
            $("#exampleModal").modal("show");
        } else if (altEmail == 0) {
            e.preventDefault();
            $("#exampleModal").modal("show");
        } else {
            $("#customerCreateBtn").prop('disabled', true);
        }

    });
    
});

