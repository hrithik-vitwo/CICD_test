
$(document).ready(function () {
  let compliance_invoice = $("#compliance_invoice").val();
  var typeCheck = [];
  $(document).on("click", "#goodsType", function (e) {
    typeCheck.push($("#goodsType").val());
  });

  var iv_varient = $('#iv_varient').val();
  console.log(iv_varient + 'ivvv');
  var invoiceNumberType = $('#invoiceNumberType').val();
  console.log(invoiceNumberType + 'ttpe');


  $(document).on("change", "#goodsType", function (e) {
    let goodsType = $("#goodsType").val();

    if (goodsType === "material" && $("#itemsTable").children().length > 0) {
      $(".notesTypeCheck").remove();
      $("#itemModalBody").append(
        '<p class="notesTypeCheck font-monospace text-danger">Do you want to keep existing Items?</p>'
      );
      $(".modal-footer").remove();
      $(".itemModalContent").append(
        '<div class="modal-footer pt-0"><button type="button" class="yesType btn btn-secondary" data-bs-dismiss="modal">Yes</button><button type="button" class="noType btn btn-primary" data-bs-dismiss="modal">No</button></div>'
      );
      $("#itemModal").modal("show");
    } else if (goodsType === "service" && $("#itemsTable").children().length > 0) {
      $(".notesTypeCheck").remove();
      $("#itemModalBody").append(
        '<p class="notesTypeCheck font-monospace text-danger">Do you want to keep existing Items?</p>'
      );
      $(".modal-footer").remove();
      $(".itemModalContent").append(
        '<div class="modal-footer pt-0"><button type="button" class="yesType btn btn-secondary" data-bs-dismiss="modal">Yes</button><button type="button" class="noType btn btn-primary" data-bs-dismiss="modal">No</button></div>'
      );
      $("#itemModal").modal("show");
    } else if (goodsType === "project" && $("#itemsTable").children().length > 0) {
      $(".notesTypeCheck").remove();
      $("#itemModalBody").append(
        '<p class="notesTypeCheck font-monospace text-danger">Do you want to keep existing Items?</p>'
      );
      $(".modal-footer").remove();
      $(".itemModalContent").append(
        '<div class="modal-footer pt-0"><button type="button" class="yesType btn btn-secondary" data-bs-dismiss="modal">Yes</button><button type="button" class="noType btn btn-primary" data-bs-dismiss="modal">No</button></div>'
      );
      $("#itemModal").modal("show");
    } else {
      $(".notesTypeCheck").remove();
    }
  });

  $(document).on("click", ".yesType", function (e) {
    let goodsType = typeCheck[typeCheck.length - 2];

    $("#goodsType").val(goodsType);

    $.ajax({
      type: "GET",
      url: `ajaxs/so/ajax-items-goods-type.php`,
      data: {
        act: "goodsType",
        goodsType,
      },
      beforeSend: function () {
        $("#itemsDropDown").html(`Loding...`);
      },
      success: function (response) {
        console.log(response);
        $("#itemsDropDown").html(response);
      },
    });
  });

  $(document).on("click", ".noType", function (e) {
    $("#itemsTable").html("");
  });

  $("#invoiceNumberType").change(function () {

    $(".so_manualInvoiceNo").remove();

    let selectedItem = $(this).val();
    if (selectedItem === 'manual') {
      $('#ivnumberManual').show();
      $('#liveInvoice').hide();
    } else {
      $('#liveInvoice').show();
      $('#ivnumberManual').hide();
      $("[name='ivnumberManual']").val('');
      $(".inv_manual_no").remove();
      $(".so_manualInvoiceNo").remove();


    }
  })
  $("#iv_varient").change(function () {
    $("#ivnumberManual").val('');
    $(".so_manualInvoiceNo").remove();
    $(".inv_manual_no").remove();

  })
  let checkInvoiceNumberIsValid = false;

  $('#ivnumberManual').keyup(function () {
    let number = $(this).val();
    let iv_varient = $("#iv_varient").val();
    $.ajax({
      type: "POST",
      url: `ajaxs/so/ajax-check-inv-number.php`,
      dataType: 'json',
      data: {
        act: "checkInvNumber",
        iv_id: iv_varient,
        number: number
      },
      beforeSend: function () {
        $(".inv_manual_no").remove();

        $("#ivnumberManual")
          .parent()
          .append(
            `<span class="error inv_manual_no">checking.....</span>`
          );
        $(".inv_manual_no").show();
      },
      success: function (response) {
        $(".inv_manual_no").remove();

        console.log(response);

        if (response.status == 'error') {
          checkInvoiceNumberIsValid = false;
          let propertyNames = Object.keys(response);
          // Access the value of the first property
          let msg = response[propertyNames[0]];
          $(".inv_manual_no").remove();
          $("#ivnumberManual")
            .parent()
            .append(
              `<span class="error inv_manual_no">${msg}</span>`
            );
          $(".inv_manual_no").show();
        } else if (response.status == 'warning') {
          console.log("warning");
          $(".inv_manual_no").remove();
          $("#ivnumberManual")
            .parent()
            .append(
              `<span class="error inv_manual_no">Duplicate Entry</span>`
            );
          $(".inv_manual_no").show();
        } else {
          checkInvoiceNumberIsValid = true;
          $(".inv_manual_no").remove();
        }

      },
      complete: function (xhr, textStatus, error) {
        // console.log("Request done!");
      }
    });
  });









  $(document).on("click", "#directInvoiceCreationBtn", function (e) {
    let validStatus = 0;

    // CUSTOMER VALIDATION
    if ($("[name='customerId']").val() == "") {
      $(".so_customerId").remove();
      $("[name='customerId']")
        .parent()
        .append(
          '<span class="error so_customerId">Customer is required</span>'
        );
      $(".so_customerId").show();

      $(".notescustomerId").remove();
      $("#notesModalBody").append(
        '<p class="notescustomerId font-monospace text-danger">Customer is required</p>'
      );
    } else {
      $(".so_customerId").remove();

      $(".notescustomerId").remove();
      validStatus++;
    }

    // POSTING DATE VALIDATION
    if ($("[name='soDate']").val() == "") {
      $(".so_soDate").remove();
      $("[name='soDate']")
        .parent()
        .append(
          '<span class="error so_soDate">Posting Date is required</span>'
        );
      $(".so_soDate").show();

      $(".notessoDate").remove();
      $("#notesModalBody").append(
        '<p class="notessoDate font-monospace text-danger">Posting Date is required</p>'
      );
    } else {
      $(".so_soDate").remove();

      $(".notessoDate").remove();
      validStatus++;
    }

    // DELIVERY DATE VALIDATION
    if ($("[name='deliveryDate']").val() == "") {
      $(".so_deliveryDate").remove();
      $("[name='deliveryDate']")
        .parent()
        .append(
          '<span class="error so_deliveryDate">Delivery Date is required</span>'
        );
      $(".so_deliveryDate").show();

      $(".notesDeliveryDate").remove();
      $("#notesModalBody").append(
        '<p class="notesDeliveryDate font-monospace text-danger">Delivery Date is required</p>'
      );
    } else {
      $(".so_deliveryDate").remove();

      $(".notesDeliveryDate").remove();
      validStatus++;
    }

    // PROFIT CENTER VALIDATION
    if ($("[name='profitCenter']").val() == "") {
      $(".so_profitCenter").remove();
      $("[name='profitCenter']")
        .parent()
        .append(
          '<span class="error so_profitCenter">Functional area is required</span>'
        );
      $(".so_profitCenter").show();

      $(".notesprofitCenter").remove();
      $("#notesModalBody").append(
        '<p class="notesprofitCenter font-monospace text-danger">Functional area is required</p>'
      );
    } else {
      $(".so_profitCenter").remove();

      $(".notesprofitCenter").remove();
      validStatus++;
    }

    // CUSTOMER ORDER NO VALIDATION
    if ($("[name='customerPO']").val() == "") {
      $(".so_customerPO").remove();
      $("[name='customerPO']")
        .parent()
        .append(
          '<span class="error so_customerPO">Customer Order No is required</span>'
        );
      $(".so_customerPO").show();

      $(".notescustomerPO").remove();
      $("#notesModalBody").append(
        '<p class="notescustomerPO font-monospace text-danger">Customer Order No is required</p>'
      );
    } else {
      $(".so_customerPO").remove();

      $(".notescustomerPO").remove();
      validStatus++;
    }

    // CREDIT PERIOD VALIDATION
    if ($("[name='creditPeriod']").val() == "") {
      $(".so_creditPeriod").remove();
      $("[name='creditPeriod']")
        .parent()
        .append(
          '<span class="error so_creditPeriod">Credit Period is required</span>'
        );
      $(".so_creditPeriod").show();

      $(".notescreditPeriod").remove();
      $("#notesModalBody").append(
        '<p class="notescreditPeriod font-monospace text-danger">Credit Period is required</p>'
      );
    } else {
      $(".so_creditPeriod").remove();

      $(".notescreditPeriod").remove();
      validStatus++;
    }

    // SALES PERSON VALIDATION
    if ($("[name='kamId']").val() == "") {
      $(".so_kamId").remove();
      $("[name='kamId']")
        .parent()
        .append('<span class="error so_kamId">Sales Person is required</span>');
      $(".so_kamId").show();

      $(".noteskamId").remove();
      $("#notesModalBody").append(
        '<p class="noteskamId font-monospace text-danger">Sales Person is required</p>'
      );
    } else {
      $(".so_kamId").remove();

      $(".noteskamId").remove();
      validStatus++;
    }

    // ORDER TYPE VALIDATION
    if ($("[name='goodsType']").val() == "") {
      $(".so_goodsType").remove();
      $("[name='goodsType']")
        .parent()
        .append(
          '<span class="error so_goodsType">Order Type is required</span>'
        );
      $(".so_goodsType").show();

      $(".notesgoodsType").remove();
      $("#notesModalBody").append(
        '<p class="notesgoodsType font-monospace text-danger">Order Type is required</p>'
      );
    } else {
      $(".so_goodsType").remove();

      $(".notesgoodsType").remove();
      validStatus++;
    }

    // ITEM VALIDATION
    if ($("#itemsTable").children().length == 0) {
      $(".so_tableitems").remove();
      $("#itemsTable")
        .parent()
        .append(
          '<span class="error so_tableitems">Atleast One Item is required</span>'
        );
      $(".so_tableitems").show();

      $(".notesTableItems").remove();
      $("#notesModalBody").append(
        '<p class="notesTableItems font-monospace text-danger">Atleast One Item is required</p>'
      );
    } else {
      $(".so_tableitems").remove();

      $(".notesTableItems").remove();
      validStatus++;
    }

    console.log(invoiceNumberType);
    console.log(checkInvoiceNumberIsValid);
    // Manual Invoice Number VALIDATION
    if ($('#invoiceNumberType').val() === 'manual') {
      if ($("#ivnumberManual").val() == "") {
        $(".so_manualInvoiceNo").remove();
        $("#ivnumberManual")
          .parent()
          .append(
            '<span class="error so_manualInvoiceNo">Manual Invoice Number is required</span>'
          );
        $(".so_manualInvoiceNo").show();

        $(".notesmanualInvoiceNo").remove();
        $("#notesModalBody").append(
          '<p class="notesmanualInvoiceNo font-monospace text-danger">Manual Invoice Number is required</p>'
        );
      } else {
        $(".so_manualInvoiceNo").remove();

        $(".notesmanualInvoiceNo").remove();
        validStatus++;
      }
      if (validStatus !== 10 && checkInvoiceNumberIsValid == true) {
        e.preventDefault();
        $("#exampleModal").modal("show");
      }
    } else {
      if (validStatus !== 9) {
        e.preventDefault();
        $("#exampleModal").modal("show");
      }
    }

    if (compliance_invoice == 1) {
      var selectElement = document.getElementById("compInvoiceType");
      if (selectElement.value === "") {
      $(".customerCompliance").remove();
      $("[name='compInvoiceType']")
        .parent()
        .append(
          '<span class="error customerCompliance">Compliance Invoice Type is required</span>'
        );
      $(".customerCompliance").show();

      $(".notescustomerCompliance").remove();
      $("#notesModalBody").append(
        '<p class="notescustomerCompliance font-monospace text-danger">Compliance Invoice Type is required</p>'
      );
    } else {
      $(".customerCompliance").remove();

      $(".notescustomerCompliance").remove();
      validStatus++;
    }  
  }

  });
});
