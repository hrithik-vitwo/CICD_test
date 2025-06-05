$(document).ready(function () {
  $(document).on("click", "#deliveryCreationBtn", function (e) {
    let validStatus = 0;
    let counter = 0;
    let validCounter = 0;

    // DELIVERY POSTING VALIDATION
    if ($("#postingDeliveryDate").val() == "") {
      $(".delivery_postingDeliveryDate").remove();
      $("#postingDeliveryDate")
        .parent()
        .append(
          '<span class="error delivery_postingDeliveryDate">Delivery Posting Date is required</span>'
        );
      $(".delivery_postingDeliveryDate").show();

      $(".notespostingDeliveryDate").remove();
      $("#notesModalBody").append(
        '<p class="notespostingDeliveryDate font-monospace text-danger">Delivery Posting Date is required</p>'
      );
    } else {
      $(".delivery_postingDeliveryDate").remove();
      $(".notespostingDeliveryDate").remove();
      validStatus++;
    }

    for (elem of $(".deliveryScheduleQty")) {
      // SCHEDULE DATE VALIDATION
      if ($(elem).val() == "" || typeof($(elem).val())  === "undefined") {
        $(`.delivery_scheduleDate_${counter}`).remove();
        $(`#deliveryScheduleQty_${counter}`)
          .parent()
          .append(
            `<span class="error delivery_scheduleDate_${counter}">Schedule Date is required</span>`
          );
        $(`.delivery_scheduleDate_${counter}`).show();

        $(`.notesscheduleDate_${counter}`).remove();
        $("#notesModalBody").append(
          `<p class="notesscheduleDate_${counter} font-monospace text-danger">Schedule Date is required for Line No. ${counter+1}</p>`
        );
      } else {
        $(`.delivery_scheduleDate_${counter}`).remove();

        $(`.notesscheduleDate_${counter}`).remove();
        validStatus++;
        validCounter++;
        $('#finalSubmitModal').modal('show');
      }
      counter++;
    }

    if (validStatus !== 1+validCounter) {
      e.preventDefault();
      $("#exampleModal").modal("show");
    }else{
      $('#finalSubmitModal').modal('show');
    }
  });
});
