$(document).ready(function () {
  
$(document).on("keyup change","#expDate",function(e){
  let expdate=$(this).val();
  $(`.delDate`).val(expdate);
  // $("#dateInputvalid").attr("min", expdate); 
})
  $(document).on("keyup", ".itemQty", function (e) {

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
    let expDate = $("#expDate").val();
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
//       //$(modalSelector).modal('hide');
//       $(modalSelector).removeClass('show').addClass('fade').css('display', 'none');
//       $(modalSelector).attr('aria-hidden', 'true').removeAttr('aria-modal');
//       $(modalSelector).removeAttr('role');
//       $(modalSelector).css('padding-right', '');
//       $('.modal-backdrop').remove();
//       $('body').removeClass('modal-open');
//       $('body').css('padding-right', '');
//       $(document).off('focusin.modal');


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


  $(document).on("change", "[name='expDate']", function (e) {
    
    for (elem of $(".delDate")) {
          let element = elem.getAttribute("data-attr");
          $(`.delDate_${element}`).val($("[name='expDate']").val());
    };
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
  });

  $(document).on("click", ".yesType", function (e) {
   
    let payloadData = typeCheck[typeCheck.length - 2];
   
    $("#usetypesDropdown").val(payloadData);

    $.ajax({
      type: "GET",
      url: `ajaxs/pr/ajax-items.php`,
      data: {
        "type" : payloadData,
      },
      beforeSend: function () {
        $("#itemsDropDown").html(`<option value="">Loding...</option>`);
      },
      success: function (response) {
        // console.log(response);
        $("#itemsDropDown").html(response);
      },
    });
  });

  $(document).on("click", ".noType", function (e) {
    $("#itemsTable").html("");
  });




  $(document).on("click", "#prbtn", function (e) {

    let validStatus = 0;

    // USE TYPE VALIDATION
    if ($("#usetypesDropdown").val() == "") {
      $(".pr_usetypesDropdown").remove();
      $("#usetypesDropdown")
        .parent()
        .append(
          '<span class="error pr_usetypesDropdown">Type is required</span>'
        );
      $(".pr_usetypesDropdown").show();

      $(".notesUsetypesDropdown").remove();
      $("#notesModalBody").append(
        '<p class="notesUsetypesDropdown font-monospace text-danger">Type is required</p>'
      );
    } else {
      $(".pr_usetypesDropdown").remove();

      $(".notesUsetypesDropdown").remove();
      validStatus++;
    }

    // REQUIRED DATE VALIDATION
    if ($("#expDate").val() == "") {
      $(".pr_expDate").remove();
      $("#expDate")
        .parent()
        .append('<span class="error pr_expDate">Date is required</span>');
      $(".pr_expDate").show();

      $(".notesExpDate").remove();
      $("#notesModalBody").append(
        '<p class="notesExpDate font-monospace text-danger">Date is required</p>'
      );
    } else {
      $(".pr_expDate").remove();

      $(".notesExpDate").remove();
      validStatus++;
    }

    // PR DATE VALIDATION
    if ($("#prDate").val() == "") {
      $(".pr_prDate").remove();
      $("#prDate")
        .parent()
        .append('<span class="error pr_prDate">PR Date is required</span>');
      $(".pr_prDate").show();

      $(".notesPrDate").remove();
      $("#notesModalBody").append(
        '<p class="notesPrDate font-monospace text-danger">PR Date is required</p>'
      );
    } else {
      $(".pr_prDate").remove();

      $(".notesPrDate").remove();
      validStatus++;
    }

    // VALIDITY PERIOD VALIDATION
 
    if ($("#dateInputvalid").val() == "") {
    
      $(".pr_valDate").remove();
      $("#dateInputvalid")
        .parent()
        .append('<span class="error pr_valDate">Validity Period is required</span>');
      $(".pr_valDate").show();

      $(".notesvalDate").remove();
      $("#notesModalBody").append(
        '<p class="notesvalDate font-monospace text-danger">Validity Period is required</p>'
      );
    } else {
      $(".pr_valDate").remove();

      $(".notesvalDate").remove();
      validStatus++;
    }



    // REF NO VALIDATION
    if ($("#refNo").val() == "") {
      $(".pr_refNo").remove();
      $("#refNo")
        .parent()
        .append('<span class="error pr_refNo">Ref No is required</span>');
      $(".pr_refNo").show();

      $(".notesRefNo").remove();
      $("#notesModalBody").append(
        '<p class="notesRefNo font-monospace text-danger">Ref No is required</p>'
      );
    } else {
      $(".pr_refNo").remove();

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

    if (validStatus !== 6) {
      e.preventDefault();
      $("#exampleModal").modal("show");
    }
  });
});
