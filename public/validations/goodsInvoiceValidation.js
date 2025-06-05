$(document).ready(function () {
  $(document).on("click", "#directInvoiceCreationBtn", function (e) {
    let validStatus = 0;
    let specStatus = 0;

    // CUSTOMER VALIDATION
    if ($("[name='customerId']").val() == "") {
      $(".inv_customerId").remove();
      $("[name='customerId']")
        .parent()
        .append(
          '<span class="error inv_customerId">Customer is required</span>'
        );
      $(".inv_customerId").show();

      $(".notescustomerId").remove();
      $("#notesModalBody").append(
        '<p class="notescustomerId font-monospace text-danger">Customer is required</p>'
      );
    } else {
      $(".inv_customerId").remove();

      $(".notescustomerId").remove();
      validStatus++;
    }

    // INVOICE DATE VALIDATION
    if ($("[name='invoiceDate']").val() == "") {
      $(".inv_invoiceDate").remove();
      $("[name='invoiceDate']")
        .parent()
        .append(
          '<span class="error inv_invoiceDate">Invoice Date is required</span>'
        );
      $(".inv_invoiceDate").show();

      $(".notesinvoiceDate").remove();
      $("#notesModalBody").append(
        '<p class="notesinvoiceDate font-monospace text-danger">Invoice Date is required</p>'
      );
    } else {
      $(".inv_invoiceDate").remove();

      $(".notesinvoiceDate").remove();
      validStatus++;
    }

    // PROFIT CENTER VALIDATION
    if ($("[name='profitCenter']").val() == "") {
      $(".inv_profitCenter").remove();
      $("[name='profitCenter']")
        .parent()
        .append(
          '<span class="error inv_profitCenter">Functional area is required</span>'
        );
      $(".inv_profitCenter").show();

      $(".notesprofitCenter").remove();
      $("#notesModalBody").append(
        '<p class="notesprofitCenter font-monospace text-danger">Functional area is required</p>'
      );
    } else {
      $(".inv_profitCenter").remove();

      $(".notesprofitCenter").remove();
      validStatus++;
    }

    // CREDIT PERIOD VALIDATION
    if ($("[name='creditPeriod']").val() == "") {
      $(".inv_creditPeriod").remove();
      $("[name='creditPeriod']")
        .parent()
        .append(
          '<span class="error inv_creditPeriod">Credit Period is required</span>'
        );
      $(".inv_creditPeriod").show();

      $(".notescreditPeriod").remove();
      $("#notesModalBody").append(
        '<p class="notescreditPeriod font-monospace text-danger">Credit Period is required</p>'
      );
    } else {
      $(".inv_creditPeriod").remove();

      $(".notescreditPeriod").remove();
      validStatus++;
    }

    // SALES PERSON VALIDATION
    if ($("[name='kamId']").val() == "") {
      $(".inv_kamId").remove();
      $("[name='kamId']")
        .parent()
        .append(
          '<span class="error inv_kamId">Sales Person is required</span>'
        );
      $(".inv_kamId").show();

      $(".noteskamId").remove();
      $("#notesModalBody").append(
        '<p class="noteskamId font-monospace text-danger">Sales Person is required</p>'
      );
    } else {
      $(".inv_kamId").remove();

      $(".noteskamId").remove();
      validStatus++;
    }

    // BANK VALIDATION
    if ($("[name='bankId']").val() == "") {
      $(".inv_bankId").remove();
      $("[name='bankId']")
        .parent()
        .append('<span class="error inv_bankId">Bank is required</span>');
      $(".inv_bankId").show();

      $(".notesbankId").remove();
      $("#notesModalBody").append(
        '<p class="notesbankId font-monospace text-danger">Bank is required</p>'
      );
    } else {
      $(".inv_bankId").remove();

      $(".notesbankId").remove();
      validStatus++;
    }

    // ITEM VALIDATION
    if ($("#itemsTable").children().length == 0) {
      $(".inv_tableitems").remove();
      $("#itemsTable")
        .parent()
        .append(
          '<span class="error inv_tableitems">Atleast One Item is required</span>'
        );
      $(".inv_tableitems").show();

      $(".notesTableItems").remove();
      $("#notesModalBody").append(
        '<p class="notesTableItems font-monospace text-danger">Atleast One Item is required</p>'
      );
    } else {
      $(".inv_tableitems").remove();

      $(".notesTableItems").remove();
      validStatus++;
    }

    for (elem of $(".itemQty")) {
      let element = elem.getAttribute("id").split("_")[1];

      if (
        $(`#itemQty_${element}`).val() == "" ||
        $(`#itemQty_${element}`).val() == "0"
      ) {
        $(`.item_qty_${element}`).remove();
        $(`#itemQty_${element}`)
          .parent()
          .parent()
          .append(
            `<span class="error item_qty_${element}">Item Quantity is required</span>`
          );
        $(`.item_qty_${element}`).show();

        $(`.notes_item_qty_${element}`).remove();
        $("#notesModalBody").append(
          `<p class="notes_item_qty_${element} font-monospace text-danger">Item Quantity is required</p>`
        );
      } else {
        $(`.item_qty_${element}`).remove();

        $(`.notes_item_qty_${element}`).remove();
        specStatus++;
      }
    }

    if (validStatus !== 7) {
      e.preventDefault();
      $("#exampleModal").modal("show");
    }

    if (specStatus !== $(".itemQty").length) {
      e.preventDefault();
      $("#exampleModal").modal("show");
    }

  });
});
