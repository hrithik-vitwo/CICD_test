$(document).ready(function () {
  $(document).on("click", "[name='consumptionPosting']", function (e) {
    let validStatus = 0;
    let specStatus = 0;
    let ID = $(this).attr("id").split("_")[1];

    // POSTING DATE VALIDATION
    if (Number($(`#productionQuantity_${ID}`).val()) <= Number($(`#remainingQty_${ID}`)[0].innerText)) {
        $(".productionQty").remove();

        validStatus++;
    } else {
        $(".productionQty").remove();
        $("[name='productionQuantity']")
            .parent()
            .append(
            '<span class="error productionQty">Declare Quantity is greater than Remaining Quantity</span>'
            );
        $(".productionQty").show();
    }
    
    let sl = 0;
    for (elem of $(`.availableQuantity_${ID}`).get()) {
      let element = elem.getAttribute("id").split("_")[1];

      // STORAGE LOCATION VALIDATION
      if (Number($(`#availableQuantity_${element}`).val()) >= Number($(`.requireConsumptionValidation_${element}`).val())) {
        $(`.availableqty_${element}`).remove();

        specStatus++;
      } else {
        sl++;
        $(`.availableqty_${element}`).remove();
        $(`[name='productionQuantity']`)
          .parent()
          .append(
            `<span class="error availableqty_${element}">Required Quantity is greater than Available Quantity for Item No. ${sl}</span>`
          );
        $(`.availableqty_${element}`).show();

      }
    }

    if (validStatus !== 1) {
      e.preventDefault();
    }

    if (specStatus !== $(".availableQuantity").length) {
        e.preventDefault();
    }

  });
});
