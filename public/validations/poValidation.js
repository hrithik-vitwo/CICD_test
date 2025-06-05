$(document).ready(function () {
  $('#selectBtn').prop('disabled', true);

  $(function(){
    // 1) Delegate click so radios can be toggled on/off
    $('#pr_form').on('click', 'input[name="pr-po-creation"]', function(){
      const $r = $(this);
      if ($r.data('waschecked')) {
        $r.prop('checked', false).data('waschecked', false);
      } else {
        $('input[name="pr-po-creation"]')
          .prop('checked', false)
          .data('waschecked', false);
        $r.prop('checked', true).data('waschecked', true);
      }
      $r.trigger('change');
    });
  
    // 2) Delegate change to toggle the Select button
    $('#pr_form').on('change', 'input[name="pr-po-creation"]', function(){
      const any = $('#pr_form input[name="pr-po-creation"]:checked').length > 0;
      $('#selectBtn').prop('disabled', !any);
    });
  
    // 3) Reset everything each time the modal opens
    $('#select-pr').on('show.bs.modal', function(){
      const $m = $(this);
      // uncheck all, clear flags
      $m.find('input[name="pr-po-creation"]')
        .prop('checked', false)
        .data('waschecked', false);
      // disable the button
      $('#selectBtn').prop('disabled', true);
    });
  });
  
  
  var typeCheck = [];
  $(document).on("click", "#usetypesDropdown", function (e) {
    typeCheck.push($("#usetypesDropdown").val());
  });

  $(document).on("change", "#usetypesDropdown", function (e) {
    if (
      $("#usetypesDropdown").val() == "material" &&
      $("#itemsTable").children().length > 0
    ) {
      $(".notesTypeCheck").remove();
      $("#itemModalBody").append(
        '<p class="notesTypeCheck font-monospace text-danger">Do you want to keep existing Items?</p>'
      );
      $(".modal-footer").remove();
      $(".itemModalContent").append(
        '<div class="modal-footer pt-0"><button type="button" class="yesType btn btn-secondary" data-bs-dismiss="modal">Yes</button><button type="button" class="noType btn btn-primary" data-bs-dismiss="modal">No</button></div>'
      );
      $("#itemModal").modal("show");
    } else if (
      $("#usetypesDropdown").val() == "servicep" &&
      $("#itemsTable").children().length > 0
    ) {
      $(".notesTypeCheck").remove();
      $("#itemModalBody").append(
        '<p class="notesTypeCheck font-monospace text-danger">Do you want to keep existing Items?</p>'
      );
      $(".modal-footer").remove();
      $(".itemModalContent").append(
        '<div class="modal-footer pt-0"><button type="button" class="yesType btn btn-secondary" data-bs-dismiss="modal">Yes</button><button type="button" class="noType btn btn-primary" data-bs-dismiss="modal">No</button></div>'
      );
      $("#itemModal").modal("show");
    } else if (
      $("#usetypesDropdown").val() == "asset" &&
      $("#itemsTable").children().length > 0
    ) {
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

    $(document).on("click", ".yesType", function (e) {
      let payloadData = typeCheck[typeCheck.length - 2];
      $("#usetypesDropdown").val(payloadData);
      $.ajax({
        type: "GET",
        url: `ajaxs/po/ajax-items.php`,
        data: {
            "type" : payloadData,
        },
        beforeSend: function () {
          $("#itemsDropDown").html(`<option value="">Loding...</option>`);
        },
        success: function (response) {
        //   console.log(response);
          $("#itemsDropDown").html(response);
        },
      });
    });

    $(document).on("click", ".noType", function (e) {
      $("#itemsTable").html("");
    });
  });

  $(document).on("change", "[name='deliveryDate']", function (e) {
    
    for (elem of $(".delDate")) {
          let element = elem.getAttribute("data-attr");
          $(`.delDate_${element}`).val($("[name='deliveryDate']").val());
    };
  });

  $(document).on("click", ".qty_minus", function (e) {
    
    let element = $(this)[0].getAttribute("data-attr");

    setTimeout(function () {

      let sumQty = 0;
  
      for (elem of $(`.multiQty_${element}`)) {
        sumQty += Number($(elem).val());
      };
  
      let actualQty = Number($(`#itemQty_${element}`).val());
  
      if (actualQty === sumQty) {
        $(`#finalBtn_${element}`)[0].disabled = false;
        $(".po_qtyValidation").remove();
      } else {
        $(`#finalBtn_${element}`)[0].disabled = true;
        $(`.modal-body`).append('<span class="error po_qtyValidation">Quantity is not matching.</span>');
        $(".po_qtyValidation").show();
      };
    }, 100);
    
  });

  $(document).on("change", ".itemQty", function (e) {

    let element = $(this)[0].getAttribute("id").split("_")[1];

    $(`#multiQuantity_${element}`).val($(this).val());
    
  });

  var QtyValidation = 0;
  var DateValidation = 0;

$(document).on("keyup", ".multiQuantity", function (e) {
  $(".po_qtyValidation").remove();
  QtyValidation = 0;

  let element = $(this).data("attr");
  let sumQty = 0;

  $(`.multiQty_${element}`).each(function () {
    sumQty += Number($(this).val());
  });

  let actualQty = Number($(`#itemQty_${element}`).val());

  if (actualQty === sumQty) {
    QtyValidation = 0;
  } else {
    QtyValidation++;
    if (!$(".po_qtyValidation").length) {
      $(".modal-body").append(
        '<span class="error po_qtyValidation" style="color:red;">Quantity is not matching.</span>'
      );
    }
  }
  if (QtyValidation === 0 && DateValidation === 0) {
    $(".po_delDateValidation").remove();
    $("#finalBtn, #prbtn").prop("disabled", false);
  } else {
    $("#finalBtn, #prbtn").prop("disabled", true);
  }
});

 

  $(document).on("keyup change", ".delDate", function () {
    $(".po_delDateValidation").remove();
    DateValidation = 0;
    let expDate = $("#deliveryDate").val();
  
    if (!expDate) {
      $("#finalBtn, #prbtn").prop("disabled", true);
      return;
     }
   
    $(".delDate").each(function () {
      let element = $(this).data("attr");   
      let date    = $(this).val();
      let itemid = $(this).data("itemid");
      if (!date) return;
      if (date < expDate) {
        DateValidation++;
      
          $(".po_delDateValidation").remove();
          $(`#Date_error${itemid}`).append(
            `<span class="error po_delDateValidation ">Delivery Date must be ≥ ${expDate}</span>`
          );
        
      }
    });
    if (typeof QtyValidation === "undefined") QtyValidation = 0;
    if (QtyValidation === 0 && DateValidation === 0) {
      $("#finalBtn, #prbtn").prop("disabled", false);
    } else {
      $("#finalBtn, #prbtn").prop("disabled", true);
    }
  });
  function dateQuantity(attr) {
    let isValid = true;
    let totalQty = 0;
    let actualQty = Number($(`#itemQty_${attr}`).val());
    $('.delDate:visible').each(function () {
      if ($(this).val().trim() === '') {
        isValid = false;
        $(this).addClass('is-invalid');
      } else {
        $(this).removeClass('is-invalid');
      }
    });
  
    $('.multiQuantity:visible').each(function () {
      let qty = $(this).val().trim();
      if (qty === '') {
        isValid = false;
        $(this).addClass('is-invalid');
      } else {
        $(this).removeClass('is-invalid');
        totalQty += parseFloat(qty);
      }
    });
  
    if (totalQty !== actualQty) {
      isValid = false;
    }
  
    return isValid;
  }
  

//   $(document).on("click", ".save-close-btn", function (e) {
//     e.preventDefault();
//     let attr = $(this).data('attr'); // This is randCode
//     let isValid = dateQuantity(attr);
//     let errorMessage = "All delivery date and quantity fields must be filled.";
   
//     let modalSelector = '#deliveryScheduleModal_' + attr;
//     // Clear any previous error message
//     $('.Date_error').text('');
//     if (!isValid) {
//         e.preventDefault(); 
//         let attr = $(this).data('itemid'); 
//         $('#Date_error' + attr).append(
//           `<span class="error po_delDateValidation "> ${errorMessage}</span>`
//         );
//         $("#finalBtn, #prbtn").prop("disabled", true);
//     }else {
//       $("#finalBtn, #prbtn").prop("disabled", false);
//       //$(modalSelector).modal('hide');
//       // $(modalSelector).css('display', 'none');
//       // $(modalSelector).attr('aria-hidden', 'true').removeAttr('aria-modal');
//       // $(modalSelector).removeAttr('role');
//       // $(modalSelector).css('padding-right', '');
//       // $('.modal-backdrop').remove();
//       // $('body').removeClass('modal-open');
//       // $('body').css('padding-right', '');
//       // $(document).off('focusin.modal');


//   }
// });

$(document).on("click", ".add-btn-minus", function(e) {
  $(this).parent().parent().remove();
  let errorMessage = "All delivery date and quantity fields must be filled.";
  let attr = $(this).data('attr');
  let isValid = dateQuantity(attr);
 
  if (!isValid) {
    e.preventDefault(); 
    let attr = $(this).data('itemid'); 
    $('#Date_error' + attr).append(
      `<span class="error po_delDateValidation "> ${errorMessage}</span>`
    );
    $("#finalBtn, #prbtn").prop("disabled", true);
}else {
  let attr = $(this).data('itemid');
  $('#Date_error' + attr).text('');
  $("#finalBtn, #prbtn").prop("disabled", false);
}

});

});

function validationfunction() {
  let validStatus = 0;
  let specStatus = 0;

  // VENDOR VALIDATION
  if ($("[name='vendorId']").val() == "") {
    $(".po_vendorId").remove();
    $("[name='vendorId']")
      .parent()
      .append('<span class="error po_vendorId">Vendor is required</span>');
    $(".po_vendorId").show();

    $(".notesvendorId").remove();
    $("#notesModalBody").append(
      '<p class="notesvendorId font-monospace text-danger">Vendor is required</p>'
    );
  } else {
    $(".po_vendorId").remove();

    $(".notesvendorId").remove();
    validStatus++;
  }

  // DELIVERY DATE VALIDATION
  if ($("[name='deliveryDate']").val() == "") {
    $(".po_deliveryDate").remove();
    $("[name='deliveryDate']")
      .parent()
      .append(
        '<span class="error po_deliveryDate">Delivery Date is required</span>'
      );
    $(".po_deliveryDate").show();

    $(".notesDeliveryDate").remove();
    $("#notesModalBody").append(
      '<p class="notesDeliveryDate font-monospace text-danger">Delivery Date is required</p>'
    );
  } else {
    $(".po_deliveryDate").remove();

    $(".notesDeliveryDate").remove();
    validStatus++;
  }

  // PO CREATION DATE VALIDATION
  if ($("[name='podatecreation']").val() == "") {
    $(".po_podatecreation").remove();
    $("[name='podatecreation']")
      .parent()
      .append(
        '<span class="error po_podatecreation">PO Creation Date is required</span>'
      );
    $(".po_podatecreation").show();

    $(".notespodatecreation").remove();
    $("#notesModalBody").append(
      '<p class="notespodatecreation font-monospace text-danger">PO Creation Date is required</p>'
    );
  } else {
    $(".po_podatecreation").remove();

    $(".notespodatecreation").remove();
    validStatus++;
  }

  // USE TYPE VALIDATION
  if ($("[name='usetypesDropdown']").val() == "") {
    $(".po_usetypesDropdown").remove();
    $("[name='usetypesDropdown']")
      .parent()
      .append(
        '<span class="error po_usetypesDropdown">Type is required</span>'
      );
    $(".po_usetypesDropdown").show();

    $(".notesUsetypesDropdown").remove();
    $("#notesModalBody").append(
      '<p class="notesUsetypesDropdown font-monospace text-danger">Type is required</p>'
    );
  } else {
    $(".po_usetypesDropdown").remove();

    $(".notesUsetypesDropdown").remove();
    validStatus++;
  }

  // PO TYPE VALIDATION
  if ($("[name='potypes']").val() == "") {
    $(".po_potypes").remove();
    $("[name='potypes']")
      .parent()
      .append('<span class="error po_potypes">PO Type is required</span>');
    $(".po_potypes").show();

    $(".notespotypes").remove();
    $("#notesModalBody").append(
      '<p class="notespotypes font-monospace text-danger">PO Type is required</p>'
    );
  } else {
    $(".po_potypes").remove();

    $(".notespotypes").remove();
    validStatus++;
  }

  // REF NO VALIDATION
  if ($("[name='refNo']").val() == "") {
    $(".po_refNo").remove();
    $("[name='refNo']")
      .parent()
      .append('<span class="error po_refNo">Ref No is required</span>');
    $(".po_refNo").show();

    $(".notesRefNo").remove();
    $("#notesModalBody").append(
      '<p class="notesRefNo font-monospace text-danger">Ref No is required</p>'
    );
  } else {
    $(".po_refNo").remove();

    $(".notesRefNo").remove();
    validStatus++;
  }
 
  // ITEM VALIDATION
  if ($("#itemsTable").children().length == 0) {
    $(".pr_tableitems").remove();
    $("#itemsTable")
      .parent()
      .append(
        '<span class="error pr_tableitems">Atleast One Item is required</span>'
      );
    $(".pr_tableitems").show();

    $(".notesTableItems").remove();
    $("#notesModalBody").append(
      '<p class="notesTableItems font-monospace text-danger">Atleast One Item is required</p>'
    );
  } else {
    $(".pr_tableitems").remove();

    $(".notesTableItems").remove();
    validStatus++;
  }

 

  let sl = 0;
  for (elem of $(".itemQty").get()) {
      let element = elem.getAttribute("id").split("_")[1];
      sl++;
      
      // MOQ VALIDATION
      if ($(`#itemQty_${element}`).val() == "" || $(`#itemQty_${element}`).val() == "0") {
          $(`.po_itemQty_${element}`).remove();
          $(`#itemQty_${element}`)
          .parent()
          .append(
              `<span class="error po_itemQty_${element}">Quantity is required</span>`
          );
          $(`.po_itemQty_${element}`).show();

          $(`.notesitemQty_${element}`).remove();
          $("#notesModalBody").append(
          `<p class="notesitemQty_${element} font-monospace text-danger">Quantity is required for Item No. ${sl}</p>`
          );
      } else {
          $(`.po_itemQty_${element}`).remove();

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
  }

  if (validStatus !== 7) {
    $("#exampleModal").modal("show");
    return false;
  } else if (specStatus !== $(".itemQty").length*2) {
    $("#exampleModal").modal("show");
    // e.preventDefault();
    return false;
  } else {
    return true;
  }
}


