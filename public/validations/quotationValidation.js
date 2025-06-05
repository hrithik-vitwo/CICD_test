var regPan = /[A-Z]{5}[0-9]{4}[A-Z]{1}$/;

$(document).ready(function () {

  $(document).on("click", "#finalsubmit", function (e) {
    let validStatus = 0;
    let specStatus = 0;

    var location_url = $("#location_url").val();

    // PHONE NO VALIDATION
    if ($("#v_phone").val() == "") {
      $(".quotationPhone").remove();
      $("#v_phone")
        .parent()
        .append(
          '<span class="error quotationPhone">Phone no is required</span>'
        );
      $(".quotationPhone").show();

      $(".notesPhone").remove();
      $("#notesModalBody").append(
        '<p class="notesPhone font-monospace text-danger">Phone no is required</p>'
      );
    } else {
      if ($("#v_phone").val().length != 10) {
        $(".quotationPhone").remove();
        $("#v_phone")
          .parent()
          .append(
            '<span class="error quotationPhone">Check your phone no</span>'
          );
        $(".quotationPhone").show();

        $(".notesPhone").remove();
        $("#notesModalBody").append(
          '<p class="notesPhone font-monospace text-danger">Check your phone no</p>'
        );
      } else {
        $(".quotationPhone").remove();

        $(".notesPhone").remove();
        validStatus++;
      }
    }

    // T&C VALIDATION
    if (!$("#TandCckbox").is(':checked')) {
        $(".quotationtnc").remove();
        $("#TandCckbox").parent().parent().append('<span class="error quotationtnc">You need to check T&C</span>');
        $(".quotationtnc").show();

        $(".notestnc").remove();
        $("#notesModalBody").append('<p class="notestnc font-monospace text-danger">You need to check T&C</p>');
    } else {
        $(".quotationtnc").remove();

        $(".notestnc").remove();
        validStatus++;
    }

    // GST VALIDATION
    if ($("#gst").is(':checked')) {

      if ($("#v_gst").val() == "") {
        $(".quotationGST").remove();
        $("#gst").parent().append('<span class="error quotationGST">GSTIN is required.</span>');
        $(".quotationGST").show();

        $(".notesGST").remove();
        $("#notesModalBody").append('<p class="notesGST font-monospace text-danger">GSTIN is required.</p>');
      } else {
        $(".quotationGST").remove();

        $(".notesGST").remove();

        // PAN VALIDATION
      if ($("#v_pan").val() == "") {
        $(".quotationpan").remove();
        $("#v_pan").parent().append('<span class="error quotationpan">Pan is required</span>');
        $(".quotationpan").show();

        $(".notesPan").remove();
        $("#notesModalBody").append('<p class="notesPan font-monospace text-danger">Pan is required</p>');
      } else {
          if (regPan.test($("#v_pan").val().toUpperCase())) {
              $(".quotationpan").remove();

              $(".notesPan").remove();
              validStatus++;
          } else {
              $(".quotationpan").remove();
              $("#v_pan").parent().append('<span class="error quotationpan">Check your pan</span>');
              $(".quotationpan").show();

              $(".notesPan").remove();
              $("#notesModalBody").append('<p class="notesPan font-monospace text-danger">Check your pan</p>');
          };
      }

      // TRADE NAME
      if ($("#v_trade_name").val() == "") {
        $(".quotationTradeName").remove();
        $("#v_trade_name").parent().append('<span class="error quotationTradeName">Trade Name is required.</span>');
        $(".quotationTradeName").show();

        $(".notesTradeName").remove();
        $("#notesModalBody").append('<p class="notesTradeName font-monospace text-danger">Trade Name is required.</p>');
      } else {
          $(".quotationTradeName").remove();

          $(".notesTradeName").remove();
          validStatus++;
      }

      // CONST BUSINESS
      if ($("#v_co_busi").val() == "") {
        $(".quotationConstBusiness").remove();
        $("#v_co_busi").parent().append('<span class="error quotationConstBusiness">Const of Business is required.</span>');
        $(".quotationConstBusiness").show();

        $(".notesConstBusiness").remove();
        $("#notesModalBody").append('<p class="notesConstBusiness font-monospace text-danger">Const of Business is required.</p>');
      } else {
          $(".quotationConstBusiness").remove();

          $(".notesConstBusiness").remove();
          validStatus++;
      }

      // FLAT NO
      if ($("#v_flat_no").val() == "") {
        $(".quotationFlatNo").remove();
        $("#v_flat_no").parent().append('<span class="error quotationFlatNo">Flat No is required.</span>');
        $(".quotationFlatNo").show();

        $(".notesFlatNo").remove();
        $("#notesModalBody").append('<p class="notesFlatNo font-monospace text-danger">Flat No is required.</p>');
      } else {
          $(".quotationFlatNo").remove();

          $(".notesFlatNo").remove();
          validStatus++;
      }

      // BUILDING NO
      if ($("#v_build_num").val() == "") {
        $(".quotationBuildingNo").remove();
        $("#v_build_num").parent().append('<span class="error quotationBuildingNo">Building No is required.</span>');
        $(".quotationBuildingNo").show();

        $(".notesBuildingNo").remove();
        $("#notesModalBody").append('<p class="notesBuildingNo font-monospace text-danger">Building No is required.</p>');
      } else {
          $(".quotationBuildingNo").remove();

          $(".notesBuildingNo").remove();
          validStatus++;
      }

      // STREET
      if ($("#v_street_no").val() == "") {
        $(".quotationStreet").remove();
        $("#v_street_no").parent().append('<span class="error quotationStreet">Street is required.</span>');
        $(".quotationStreet").show();

        $(".notesStreet").remove();
        $("#notesModalBody").append('<p class="notesStreet font-monospace text-danger">Street is required.</p>');
      } else {
          $(".quotationStreet").remove();

          $(".notesStreet").remove();
          validStatus++;
      }

      // LOCATION
      if ($("#v_location").val() == "") {
        $(".quotationLocation").remove();
        $("#v_location").parent().append('<span class="error quotationLocation">Location is required.</span>');
        $(".quotationLocation").show();

        $(".notesLocation").remove();
        $("#notesModalBody").append('<p class="notesLocation font-monospace text-danger">Location is required.</p>');
      } else {
          $(".quotationLocation").remove();

          $(".notesLocation").remove();
          validStatus++;
      }

      // CITY
      if ($("#v_city").val() == "") {
        $(".quotationCity").remove();
        $("#v_city").parent().append('<span class="error quotationCity">City is required.</span>');
        $(".quotationCity").show();

        $(".notesCity").remove();
        $("#notesModalBody").append('<p class="notesCity font-monospace text-danger">City is required.</p>');
      } else {
          $(".quotationCity").remove();

          $(".notesCity").remove();
          validStatus++;
      }

      // PIN
      if ($("#v_pin").val() == "") {
        $(".quotationPIN").remove();
        $("#v_pin").parent().append('<span class="error quotationPIN">PIN is required.</span>');
        $(".quotationPIN").show();

        $(".notesPIN").remove();
        $("#notesModalBody").append('<p class="notesPIN font-monospace text-danger">PIN is required.</p>');
      } else {
          $(".quotationPIN").remove();

          $(".notesPIN").remove();
          validStatus++;
      }

      // STATE
      if ($("#v_state").val() == "") {
        $(".quotationState").remove();
        $("#v_state").parent().append('<span class="error quotationState">State is required.</span>');
        $(".quotationState").show();

        $(".notesState").remove();
        $("#notesModalBody").append('<p class="notesState font-monospace text-danger">State is required.</p>');
      } else {
          $(".quotationState").remove();

          $(".notesState").remove();
          validStatus++;
      }

      // DISTRICT
      if ($("#v_district").val() == "") {
        $(".quotationDistrict").remove();
        $("#v_district").parent().append('<span class="error quotationDistrict">District is required.</span>');
        $(".quotationDistrict").show();

        $(".notesDistrict").remove();
        $("#notesModalBody").append('<p class="notesDistrict font-monospace text-danger">District is required.</p>');
      } else {
          $(".quotationDistrict").remove();

          $(".notesDistrict").remove();
          validStatus++;
      }

      }
    } else {
      $(".quotationGST").remove();

        $(".notesGST").remove();
        validStatus+=11;
    }

    // NO GST VALIDATION
    if ($("#no_gst").is(':checked')) {

      // PAN VALIDATION
      if ($("#v_pan").val() == "") {
        $(".quotationpan").remove();
        $("#v_pan").parent().append('<span class="error quotationpan">Pan is required</span>');
        $(".quotationpan").show();

        $(".notesPan").remove();
        $("#notesModalBody").append('<p class="notesPan font-monospace text-danger">Pan is required</p>');
      } else {
          if (regPan.test($("#v_pan").val().toUpperCase())) {
              $(".quotationpan").remove();

              $(".notesPan").remove();
              validStatus++;
          } else {
              $(".quotationpan").remove();
              $("#v_pan").parent().append('<span class="error quotationpan">Check your pan</span>');
              $(".quotationpan").show();

              $(".notesPan").remove();
              $("#notesModalBody").append('<p class="notesPan font-monospace text-danger">Check your pan</p>');
          };
      }

      // TRADE NAME
      if ($("#v_trade_name").val() == "") {
        $(".quotationTradeName").remove();
        $("#v_trade_name").parent().append('<span class="error quotationTradeName">Trade Name is required.</span>');
        $(".quotationTradeName").show();

        $(".notesTradeName").remove();
        $("#notesModalBody").append('<p class="notesTradeName font-monospace text-danger">Trade Name is required.</p>');
      } else {
          $(".quotationTradeName").remove();

          $(".notesTradeName").remove();
          validStatus++;
      }

      // CONST BUSINESS
      if ($("#v_co_busi").val() == "") {
        $(".quotationConstBusiness").remove();
        $("#v_co_busi").parent().append('<span class="error quotationConstBusiness">Const of Business is required.</span>');
        $(".quotationConstBusiness").show();

        $(".notesConstBusiness").remove();
        $("#notesModalBody").append('<p class="notesConstBusiness font-monospace text-danger">Const of Business is required.</p>');
      } else {
          $(".quotationConstBusiness").remove();

          $(".notesConstBusiness").remove();
          validStatus++;
      }

      // FLAT NO
      if ($("#v_flat_no").val() == "") {
        $(".quotationFlatNo").remove();
        $("#v_flat_no").parent().append('<span class="error quotationFlatNo">Flat No is required.</span>');
        $(".quotationFlatNo").show();

        $(".notesFlatNo").remove();
        $("#notesModalBody").append('<p class="notesFlatNo font-monospace text-danger">Flat No is required.</p>');
      } else {
          $(".quotationFlatNo").remove();

          $(".notesFlatNo").remove();
          validStatus++;
      }

      // BUILDING NO
      if ($("#v_build_num").val() == "") {
        $(".quotationBuildingNo").remove();
        $("#v_build_num").parent().append('<span class="error quotationBuildingNo">Building No is required.</span>');
        $(".quotationBuildingNo").show();

        $(".notesBuildingNo").remove();
        $("#notesModalBody").append('<p class="notesBuildingNo font-monospace text-danger">Building No is required.</p>');
      } else {
          $(".quotationBuildingNo").remove();

          $(".notesBuildingNo").remove();
          validStatus++;
      }

      // STREET
      if ($("#v_street_no").val() == "") {
        $(".quotationStreet").remove();
        $("#v_street_no").parent().append('<span class="error quotationStreet">Street is required.</span>');
        $(".quotationStreet").show();

        $(".notesStreet").remove();
        $("#notesModalBody").append('<p class="notesStreet font-monospace text-danger">Street is required.</p>');
      } else {
          $(".quotationStreet").remove();

          $(".notesStreet").remove();
          validStatus++;
      }

      // LOCATION
      if ($("#v_location").val() == "") {
        $(".quotationLocation").remove();
        $("#v_location").parent().append('<span class="error quotationLocation">Location is required.</span>');
        $(".quotationLocation").show();

        $(".notesLocation").remove();
        $("#notesModalBody").append('<p class="notesLocation font-monospace text-danger">Location is required.</p>');
      } else {
          $(".quotationLocation").remove();

          $(".notesLocation").remove();
          validStatus++;
      }

      // CITY
      if ($("#v_city").val() == "") {
        $(".quotationCity").remove();
        $("#v_city").parent().append('<span class="error quotationCity">City is required.</span>');
        $(".quotationCity").show();

        $(".notesCity").remove();
        $("#notesModalBody").append('<p class="notesCity font-monospace text-danger">City is required.</p>');
      } else {
          $(".quotationCity").remove();

          $(".notesCity").remove();
          validStatus++;
      }

      // PIN
      if ($("#v_pin").val() == "") {
        $(".quotationPIN").remove();
        $("#v_pin").parent().append('<span class="error quotationPIN">PIN is required.</span>');
        $(".quotationPIN").show();

        $(".notesPIN").remove();
        $("#notesModalBody").append('<p class="notesPIN font-monospace text-danger">PIN is required.</p>');
      } else {
          $(".quotationPIN").remove();

          $(".notesPIN").remove();
          validStatus++;
      }

      // STATE
      if ($("#v_state").val() == "") {
        $(".quotationState").remove();
        $("#v_state").parent().append('<span class="error quotationState">State is required.</span>');
        $(".quotationState").show();

        $(".notesState").remove();
        $("#notesModalBody").append('<p class="notesState font-monospace text-danger">State is required.</p>');
      } else {
          $(".quotationState").remove();

          $(".notesState").remove();
          validStatus++;
      }

      // DISTRICT
      if ($("#v_district").val() == "") {
        $(".quotationDistrict").remove();
        $("#v_district").parent().append('<span class="error quotationDistrict">District is required.</span>');
        $(".quotationDistrict").show();

        $(".notesDistrict").remove();
        $("#notesModalBody").append('<p class="notesDistrict font-monospace text-danger">District is required.</p>');
      } else {
          $(".quotationDistrict").remove();

          $(".notesDistrict").remove();
          validStatus++;
      }

    } else {
        validStatus+=11;
    }

    let sl = 0;
    for (elem of $(".each_quantity").get()) {
        let element = elem.getAttribute("id").split("_")[1];
        sl++;
        
        // MOQ VALIDATION
        if ($(`#itemQty_${element}`).val() == "" || $(`#itemQty_${element}`).val() == "0") {
            $(`.quotation_itemQty_${element}`).remove();
            $(`#itemQty_${element}`)
            .parent()
            .append(
                `<span class="error quotation_itemQty_${element}">Quantity is required</span>`
            );
            $(`.quotation_itemQty_${element}`).show();

            $(`.notesitemQty_${element}`).remove();
            $("#notesModalBody").append(
            `<p class="notesitemQty_${element} font-monospace text-danger">Quantity is required for Item No. ${sl}</p>`
            );
        } else {
            $(`.quotation_itemQty_${element}`).remove();

            $(`.notesitemQty_${element}`).remove();
            specStatus++;
        }

        // PRICE VALIDATION
        if ($(`#itemUnitPrice_${element}`).val() == "" || $(`#itemUnitPrice_${element}`).val() == "0") {
            $(`.price_${element}`).remove();
            $(`#itemUnitPrice_${element}`)
            .parent()
            .append(
                `<span class="error price_${element}">Price is required</span>`
            );
            $(`.price_${element}`).show();

            $(`.notesprice_${element}`).remove();
            $("#notesModalBody").append(
            `<p class="notesprice_${element} font-monospace text-danger">Price is required for Item No. ${sl}</p>`
            );
        } else {
            $(`.price_${element}`).remove();

            $(`.notesprice_${element}`).remove();
            specStatus++;
        }

        // LEAD TIME VALIDATION
        if ($(`#itemLead_${element}`).val() == "" || $(`#itemLead_${element}`).val() == "0") {
            $(`.leadtime_${element}`).remove();
            $(`#itemLead_${element}`)
            .parent()
            .append(
                `<span class="error leadtime_${element}">Lead Time is required</span>`
            );
            $(`.leadtime_${element}`).show();

            $(`.notesleadtime_${element}`).remove();
            $("#notesModalBody").append(
            `<p class="notesleadtime_${element} font-monospace text-danger">Lead Time is required for Item No. ${sl}</p>`
            );
        } else {
            $(`.leadtime_${element}`).remove();

            $(`.notesleadtime_${element}`).remove();
            specStatus++;
        }

    }

    if (validStatus !== 24) {
      e.preventDefault();
      $("#exampleQuotationModal").modal("show");
    } else if (specStatus !== $(".each_quantity").length*3) {
      e.preventDefault();
      $("#exampleQuotationModal").modal("show");
    } else {
      var quantity_array = new Array();
      var price_array = new Array();
      var discount_array = new Array();
      var gst_array = new Array();
      var delivery_array = new Array();
      var lead_array = new Array();
      var total_array = new Array();
      var detail_array = new Array();

      var arr3 = new Array();
      $.each($(".each_quantity"), function (i, value) {
        quantity_array.push($(this).val());
      });
      $.each($(".each_price"), function (j, values) {
        price_array.push($(this).val());
      });
      $.each($(".each_discount"), function (j, values) {
        discount_array.push($(this).val());
      });
      $.each($(".each_gst"), function (j, values) {
        gst_array.push($(this).val());
      });
      $.each($(".each_total"), function (j, values) {
        total_array.push($(this).val());
      });
      $.each($(".each_incoterms"), function (j, values) {
        delivery_array.push($(this).val());
      });
      $.each($(".each_lead_time"), function (j, values) {
        lead_array.push($(this).val());
      });
      $.each($(".each_detail"), function (j, values) {
        detail_array.push($(this).val());
      });

      // console.log(detail_array);
      // console.log(quantity_array);
      let i = 0,
        j = 0,
        k = 0,
        l = 0,
        m = 0,
        n = 0,
        o = 0,
        p = 0,
        q = 0;

      while (
        i < quantity_array.length &&
        j < price_array.length &&
        k < discount_array.length &&
        l < total_array.length &&
        m < detail_array.length &&
        n < delivery_array.length &&
        o < lead_array.length &&
        p < gst_array.length
      ) {
        if (quantity_array[i] == "") {
          i++;
          j++;
          k++;
          l++;
          m++;
          n++;
          o++;
          p++;
          continue;
        } else {
          arr3[q++] =
            detail_array[m++] +
            "|" +
            quantity_array[i++] +
            "|" +
            price_array[j++] +
            "|" +
            discount_array[k++] +
            "|" +
            total_array[l++] +
            "|" +
            delivery_array[n++] +
            "|" +
            lead_array[o++] +
            "|" +
            gst_array[p++];
        }
      }

      console.log(arr3);

      $.ajax({
        type: "POST",
        url: location_url+'ajaxs/pr/ajax-vendor-submit.php',
        data: {
          v_id: $("#v_id").val(),
          vendor_primary_id: $("#vendor_primary_id").val(),
          v_code: $("#v_code").val(),
          rfq_code: $("#rfq_code").val(),
          rfqId: $("#rfqId").val(),
          vendor_gst: $("#v_gst").val(),
          vendor_pan: $("#v_pan").val(),
          vendor_tradename: $("#v_trade_name").val(),
          vendor_constofbusiness: $("#v_co_busi").val(),
          vendor_flatno: $("#v_flat_no").val(),
          vendor_buildno: $("#v_build_num").val(),
          vendor_streetname: $("#v_street_no").val(),
          vendor_location: $("#v_location").val(),
          v_name: $("#v_name").val(),
          v_email: $("#v_email").val(),
          v_phone: $("#v_phone").val(),
          v_city: $("#v_city").val(),
          v_district: $("#v_district").val(),
          v_state: $("#v_state").val(),
          v_pin: $("#v_pin").val(),
          v_description: $("#v_description").val(),
          v_detail: arr3,
        },
        beforeSend: function () {
          $("#finalsubmit").prop("disabled", true);
          $("#finalsubmit").html(
            `<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...`
          );
        },
        success: function (response) {
          // console.log(response);
          var proper_response = JSON.parse(response);
          var status = proper_response["status"];
          var message = proper_response["message"];
          //alert(proper_response);

          if (status == "success") {
            //alert(message);
            $(document).ready(function () {
              Swal.fire({
                icon: status,
                title: `Thank You`,
                text: message,
              }).then(function () {
                window.location.href = `success-page.php`;
              });
            });
            $("#finalsubmit").html(`Submitted`);
          } else {
            $(document).ready(function () {
              Swal.fire({
                icon: status,
                title: `Opps...!`,
                text: message,
              }).then(function () {
                window.location.href = ``;
              });
            });
            $("#finalsubmit").html(`Submit`);
          }
        },
      });
    }
    
  });
});
