$(document).ready(function () {
  $(document).on("click", "#subBtn", function (e) {
    let validStatus = 0;
    let specStatus = 0;
    let counter = 1;

    // e.preventDefault();
    // MOVEMENT TYPE VALIDATION
    if ($("[name='movemenrtypesDropdown']").val() == "") {
      $(".stock_movemenrtypesDropdown").remove();
      $("[name='movemenrtypesDropdown']")
        .parent()
        .append(
          '<span class="error stock_movemenrtypesDropdown">Movement Type is required</span>'
        );
      $(".stock_movemenrtypesDropdown").show();

      $(".notesmovemenrtypesDropdown").remove();
      $("#notesModalBody").append(
        '<p class="notesmovemenrtypesDropdown font-monospace text-danger">Movement Type is required</p>'
      );
    } else {
      $(".stock_movemenrtypesDropdown").remove();

      $(".notesmovemenrtypesDropdown").remove();
      validStatus++;
    }

    // CREATION DATE VALIDATION
    if ($("[name='creationDate']").val() == "") {
      $(".stock_creationDate").remove();
      $("[name='creationDate']")
        .parent()
        .append(
          '<span class="error stock_creationDate">Creation Date is required</span>'
        );
      $(".stock_creationDate").show();

      $(".notescreationDate").remove();
      $("#notesModalBody").append(
        '<p class="notescreationDate font-monospace text-danger">Creation Date is required</p>'
      );
    } else {
      $(".stock_creationDate").remove();

      $(".notescreationDate").remove();
      validStatus++;
    }

    if ($("[name='movemenrtypesDropdown']").val() == "storage_location") {
      // STORAGE LOCATION VALIDATION
      if ($("[name='sl']").val() == "") {
        $(".stock_sl").remove();
        $("[name='sl']")
          .parent()
          .append(
            '<span class="error stock_sl">Storage Location is required</span>'
          );
        $(".stock_sl").show();

        $(".notessl").remove();
        $("#notesModalBody").append(
          '<p class="notessl font-monospace text-danger">Storage Location is required</p>'
        );
      } else {
        $(".stock_sl").remove();

        $(".notessl").remove();
        validStatus++;
        validStatus++;
      }
    } else if ($("[name='movemenrtypesDropdown']").val() == "item") {
      // STORAGE LOCATION VALIDATION
      if ($("[name='item_sl']").val() == "") {
        $(".stock_item_sl").remove();
        $("[name='item_sl']")
          .parent()
          .append(
            '<span class="error stock_item_sl">Storage Location is required</span>'
          );
        $(".stock_item_sl").show();

        $(".notesitem_sl").remove();
        $("#notesModalBody").append(
          '<p class="notesitem_sl font-monospace text-danger">Storage Location is required</p>'
        );
      } else {
        $(".stock_item_sl").remove();

        $(".notesitem_sl").remove();
        validStatus++;
      }

      // ITEM VALIDATION
      if ($("[name='item_name']").val() == "") {
        $(".stock_item_name").remove();
        $("[name='item_name']")
          .parent()
          .append(
            '<span class="error stock_item_name">Item is required</span>'
          );
        $(".stock_item_name").show();

        $(".notesitem_name").remove();
        $("#notesModalBody").append(
          '<p class="notesitem_name font-monospace text-danger">Item is required</p>'
        );
      } else {
        $(".stock_item_name").remove();

        $(".notesitem_name").remove();
        validStatus++;
      }
    }

    // ITEM VALIDATION
    if ($("#item_add").children().length == 0) {
      $(".pr_tableitems").remove();
      $("#item_add")
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

    if ($("#item_add").children().length > 0) {
      
      for (elem of $(".itemsDropDown").get()) {
        let element = elem.getAttribute("data-val");

        // ITEM VALIDATION
        if ($(`#itemsDropDown_${element}`).val() == "") {
          $(`.stock_itemsDropDown_${element}`).remove();
          $(`#itemsDropDown_${element}`)
            .parent()
            .append(
              `<span class="error stock_itemsDropDown_${element}">Item is required</span>`
            );
          $(`.stock_itemsDropDown_${element}`).show();

          $(`.notesitemsDropDown_${element}`).remove();
          $("#notesModalBody").append(
            `<p class="notesitemsDropDown_${element} font-monospace text-danger">Item is required for Line No. ${counter}</p>`
          );
        } else {
          $(`.stock_itemsDropDown_${element}`).remove();

          $(`.notesitemsDropDown_${element}`).remove();
          specStatus++;
        }

        // UOM VALIDATION
        if ($(`#uom_${element}`).val() == "") {
          $(`.stock_uom_${element}`).remove();
          $(`#uom_${element}`)
            .parent()
            .append(
              `<span class="error stock_uom_${element}">UOM is required</span>`
            );
          $(`.stock_uom_${element}`).show();

          $(`.notesuom_${element}`).remove();
          $("#notesModalBody").append(
            `<p class="notesuom_${element} font-monospace text-danger">UOM is required for Line No. ${counter}</p>`
          );
        } else {
          $(`.stock_uom_${element}`).remove();

          $(`.notesuom_${element}`).remove();
          specStatus++;
        }

        // STORAGE LOCATION VALIDATION
        if ($(`#storagelocation_${element}`).val() == "") {
          $(`.stock_storagelocation_${element}`).remove();
          $(`#storagelocation_${element}`)
            .parent()
            .append(
              `<span class="error stock_storagelocation_${element}">Storage Location is required</span>`
            );
          $(`.stock_storagelocation_${element}`).show();

          $(`.notesstoragelocation_${element}`).remove();
          $("#notesModalBody").append(
            `<p class="notesstoragelocation_${element} font-monospace text-danger">Storage Location is required for Line No. ${counter}</p>`
          );
        } else {
          $(`.stock_storagelocation_${element}`).remove();

          $(`.notesstoragelocation_${element}`).remove();
          specStatus++;
        }

        // QUANTITY VALIDATION
        if (
          $(`#quantity_${element}`).val() == "" ||
          $(`#quantity_${element}`).val() == "0"
        ) {
          $(`.stock_quantity_${element}`).remove();
          $(`#quantity_${element}`)
            .parent()
            .append(
              `<span class="error stock_quantity_${element}">Quantity is required</span>`
            );
          $(`.stock_quantity_${element}`).show();

          $(`.notesquantity_${element}`).remove();
          $("#notesModalBody").append(
            `<p class="notesquantity_${element} font-monospace text-danger">Quantity is required for Line No. ${counter}</p>`
          );
        } else {
          $(`.stock_quantity_${element}`).remove();

          $(`.notesquantity_${element}`).remove();
          specStatus++;
        }
        counter++;
      }
    }

    if (validStatus !== 5) {
      e.preventDefault();
      $("#exampleModal").modal("show");
    }
    
    if (specStatus !== $(".itemsDropDown").length*4) {
      e.preventDefault();
      $("#exampleModal").modal("show");
    }
    
  });
});
