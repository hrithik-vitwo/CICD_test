$(document).ready(function () {

  $(document).on("click", ".cstcntr_btn", function (e) {
    let element = $(this)[0].getAttribute("id").split("_")[2];
    let actualAmount = Number($(`#grnItemInvoiceTotalPriceTdSpan_${element}`).text().replace(/,/g, ''));

    $(`#modalAmount_${element}`).text(`Total Amount: ${actualAmount}`);

    let sumAmt = 0;

    for (elem of $(`.cstcntr_rate`)) {
      let element_id = $(elem)[0].getAttribute("name");
      var values = element_id.match(/\w+/g);
      let curRowID = values[1];

      if (element == curRowID) {
        sumAmt += Number($(elem).val());
      };
    };

    if (actualAmount === sumAmt) {
      $(`#modalButton_${element}`)[0].disabled = false;
      $(".srn_cst_cntr").remove();
    } else {
      $(".srn_cst_cntr").remove();
      $(`#modalButton_${element}`)[0].disabled = true;
      $(`.modal-footer`).append(
        '<span class="error srn_cst_cntr">Amount is not matching.</span>'
      );
      $(".srn_cst_cntr").show();
    };
  });

  $(document).on("change", ".itemTds", function (e) {
    let element = $(this)[0].getAttribute("id").split("_")[1];
    let actualAmount = Number($(`#grnItemInvoiceTotalPriceTdSpan_${element}`).text());

    $(`#modalAmount_${element}`).text(`Total Amount: ${actualAmount}`);
    $(`#myModal_${element}`).modal("show");

    $(".srn_cst_cntr").remove();
    $(`#modalButton_${element}`)[0].disabled = true;
    $(`.modal-footer`).append(
      '<span class="error srn_cst_cntr">Amount is not matching.</span>'
    );
    $(".srn_cst_cntr").show();
  });

  $(document).on("change", ".itemUnitPrice", function (e) {
    let element = $(this)[0].getAttribute("id").split("_")[1];
    let actualAmount = Number($(`#grnItemInvoiceTotalPriceTdSpan_${element}`).text());

    $(`#modalAmount_${element}`).text(`Total Amount: ${actualAmount}`);
    $(`#myModal_${element}`).modal("show");

    $(".srn_cst_cntr").remove();
    $(`#modalButton_${element}`)[0].disabled = true;
    $(`.modal-footer`).append(
      '<span class="error srn_cst_cntr">Amount is not matching.</span>'
    );
    $(".srn_cst_cntr").show();
  });

  $(document).on("change", ".received_quantity", function (e) {
    let element = $(this)[0].getAttribute("id").split("_")[1];
    let actualAmount = Number($(`#grnItemInvoiceTotalPriceTdSpan_${element}`).text());

    $(`#modalAmount_${element}`).text(`Total Amount: ${actualAmount}`);
    $(`#myModal_${element}`).modal("show");

    $(".srn_cst_cntr").remove();
    $(`#modalButton_${element}`)[0].disabled = true;
    $(`.modal-footer`).append(
      '<span class="error srn_cst_cntr">Amount is not matching.</span>'
    );
    $(".srn_cst_cntr").show();
  });

  $(document).on("keyup", ".cstcntr_rate", function (e) {
    let element = $(this)[0].getAttribute("name");
    var values = element.match(/\w+/g);
    let rowID = values[1];

    let actualAmount = Number($(`#grnItemInvoiceTotalPriceTdSpan_${rowID}`).text().replace(/,/g, ''));
    $(`#modalAmount_${rowID}`).text(`Total Amount: ${actualAmount}`);

    let sumAmt = 0;

    for (elem of $(`.cstcntr_rate`)) {
      let element = $(elem)[0].getAttribute("name");
      var values = element.match(/\w+/g);
      let curRowID = values[1];

      if (rowID == curRowID) {
        sumAmt += Number($(elem).val());
      };
    };

    if (actualAmount === sumAmt) {
      $(`#modalButton_${rowID}`)[0].disabled = false;
      $(".srn_cst_cntr").remove();
    } else {
      $(".srn_cst_cntr").remove();
      $(`#modalButton_${rowID}`)[0].disabled = true;
      $(`.modal-footer`).append(
        '<span class="error srn_cst_cntr">Amount is not matching.</span>'
      );
      $(".srn_cst_cntr").show();
    };
  });

  $(document).on("click", "#addNewGrnFormSubmitBtn", function (e) {
    let validStatus = 0;
    let specStatus = 0;

    // POSTING DATE VALIDATION
    if ($("[name='invoicePostingDate']").val() == "") {
      $(".pending_srn_postingDate").remove();
      $("[name='invoicePostingDate']")
        .parent()
        .append(
          '<span class="error pending_srn_postingDate">Posting Date is required</span>'
        );
      $(".pending_srn_postingDate").show();

      $(".notespostingDate").remove();
      $("#notesModalBody").append(
        '<p class="notespostingDate font-monospace text-danger">Posting Date is required</p>'
      );
    } else {
      $(".pending_srn_postingDate").remove();

      $(".notespostingDate").remove();
      validStatus++;
    }

    // DUE DATE VALIDATION
    if ($("[name='invoiceDueDate']").val() == "") {
      $(".pending_srn_invoiceDueDate").remove();
      $("[name='invoiceDueDate']")
        .parent()
        .append(
          '<span class="error pending_srn_invoiceDueDate">Due Date is required</span>'
        );
      $(".pending_srn_invoiceDueDate").show();

      $(".notesiv_due_date").remove();
      $("#notesModalBody").append(
        '<p class="notesiv_due_date font-monospace text-danger">Due Date is required</p>'
      );
    } else {
      $(".pending_srn_invoiceDueDate").remove();

      $(".notesiv_due_date").remove();
      validStatus++;
    }

    // PO NO VALIDATION
    // if ($("#invoicePoNumber").val() == "") {
    //     $(".pending_srn_invoicePoNumber").remove();
    //     $("#invoicePoNumber").parent().append('<span class="error pending_srn_invoicePoNumber">PO Number is Empty</span>');
    //     $(".pending_srn_invoicePoNumber").show();

    //     $(".notesinvoicePoNumber").remove();
    //     $("#notesModalBody").append('<p class="notesinvoicePoNumber font-monospace text-danger">PO Number is Empty</p>');
    // } else {
    //     $(".pending_srn_invoicePoNumber").remove();

    //     $(".notesinvoicePoNumber").remove();
    //     validStatus++;
    // }

    for (elem of $(".received_quantity").get()) {
      let element = elem.getAttribute("id").split("_")[1];

      // STORAGE LOCATION VALIDATION
      if ($(`.itemCostCenterId_${element}`).val() == "") {
        $(`.srn_itemCostCenterId_${element}`).remove();
        $(`.itemCostCenterId_${element}`)
          .parent()
          .append(
            `<span class="error srn_itemCostCenterId_${element}">Cost Center is required</span>`
          );
        $(`.srn_itemCostCenterId_${element}`).show();

        $(`.notesitemCostCenterId_${element}`).remove();
        $("#notesModalBody").append(
          `<p class="notesitemCostCenterId_${element} font-monospace text-danger">Cost Center is required for Line No. ${element}</p>`
        );
      } else {
        $(`.srn_itemCostCenterId_${element}`).remove();

        $(`.notesitemCostCenterId_${element}`).remove();
        specStatus++;
      }

      // RECEIVED QUANTITY VALIDATION
      if (
        $(`#itemReceivedQtyTdInput_${element}`).val() == "" ||
        $(`#itemReceivedQtyTdInput_${element}`).val() == "0"
      ) {
        $(`.itemReceivedQty_${element}`).remove();
        $(`#itemReceivedQtyTdInput_${element}`)
          .parent()
          .append(
            `<span class="error itemReceivedQty_${element}">Received Quantity is required</span>`
          );
        $(`.itemReceivedQty_${element}`).show();

        $(`.notesitemReceivedQty_${element}`).remove();
        $("#notesModalBody").append(
          `<p class="notesitemReceivedQty_${element} font-monospace text-danger">Received Quantity is required for Line No. ${element}</p>`
        );
      } else {
        $(`.itemReceivedQty_${element}`).remove();

        $(`.notesitemReceivedQty_${element}`).remove();
        specStatus++;
      }

      // UNIT PRICE VALIDATION
      if (
        $(`#itemUnitPriceTdInput_${element}`).val() == "" ||
        $(`#itemUnitPriceTdInput_${element}`).val() == "0"
      ) {
        $(`.itemUnitPrice_${element}`).remove();
        $(`#itemUnitPriceTdInput_${element}`)
          .parent()
          .append(
            `<span class="error itemUnitPrice_${element}">Unit Price is required</span>`
          );
        $(`.itemUnitPrice_${element}`).show();

        $(`.notesitemUnitPrice_${element}`).remove();
        $("#notesModalBody").append(
          `<p class="notesitemUnitPrice_${element} font-monospace text-danger">Unit Price is required for Line No. ${element}</p>`
        );
      } else {
        $(`.itemUnitPrice_${element}`).remove();

        $(`.notesitemUnitPrice_${element}`).remove();
        specStatus++;
      }

      // TDS VALIDATION
      if ($(`#grnItemTdsTdInput_${element}`).val() == "") {
        $(`.itemTds_${element}`).remove();
        $(`#grnItemTdsTdInput_${element}`)
          .parent()
          .append(
            `<span class="error itemTds_${element}">TDS is required</span>`
          );
        $(`.itemTds_${element}`).show();

        $(`.notesitemTds_${element}`).remove();
        $("#notesModalBody").append(
          `<p class="notesitemTds_${element} font-monospace text-danger">TDS is required for Line No. ${element}</p>`
        );
      } else {
        $(`.itemTds_${element}`).remove();

        $(`.notesitemTds_${element}`).remove();
        specStatus++;
      }
    }

    if (validStatus !== 2) {
      e.preventDefault();
      $("#examplePendingGrnModal").modal("show");
    }

    if (specStatus !== $(".received_quantity").length * 4) {
      e.preventDefault();
      $("#examplePendingGrnModal").modal("show");
    }
  });
});
