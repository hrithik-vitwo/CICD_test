var regEmail =
  /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;

$(document).ready(function () {
  $(document).on("click", "#addNewOtherVendorId", function (e) {
    let validStatus = 0;
    let specStatus = 0;

    // CLOSING DATE VALIDATION
    if ($("#closingDate").val() == "") {
      $(".rfq_closingDate").remove();
      $("#closingDate")
        .parent()
        .append(
          '<span class="error rfq_closingDate">Closing Date is required</span>'
        );
      $(".rfq_closingDate").show();

      $(".notesclosingDate").remove();
      $("#notesModalBody").append(
        '<p class="notesclosingDate font-monospace text-danger">Closing Date is required</p>'
      );
    } else {
      $(".rfq_closingDate").remove();

      $(".notesclosingDate").remove();
      validStatus++;
    }

    // VENDOR VALIDATION
    if (
      $(".modal-add-row_538").children().length == 0 &&
      $("#newLogic").children().length == 0
    ) {
      $(".vndr-rqrd_538").remove();
      $(".vndr-rqrd").append(
        '<span class="error vndr-rqrd_538">Atleast One Vendor is required</span>'
      );
      $(".vndr-rqrd_538").show();

      $(".notesmodal-add-row_538").remove();
      $("#notesModalBody").append(
        '<p class="notesmodal-add-row_538 font-monospace text-danger">Atleast One Vendor is required</p>'
      );
    } else if (
      $(".modal-add-row_538").children().length != 0 &&
      $("#newLogic").children().length == 0
    ) {
      let elemId = $(".each_name")[0].getAttribute("id").split("_")[1];

      if (
        $(`#eachName_${elemId}`).val() == "" ||
        $(`#eachEmail_${elemId}`).val() == ""
      ) {
        $(".vndr-rqrd_538").remove();
        $(".vndr-rqrd").append(
          '<span class="error vndr-rqrd_538">Vendor Name and Email both is required</span>'
        );
        $(".vndr-rqrd_538").show();

        $(".notesmodal-add-row_538").remove();
        $("#notesModalBody").append(
          '<p class="notesmodal-add-row_538 font-monospace text-danger">Vendor Name and Email both is required</p>'
        );
      } else {
        $(".vndr-rqrd_538").remove();

        $(".notesmodal-add-row_538").remove();
        validStatus++;
      }
    } else {
      $(".vndr-rqrd_538").remove();

      $(".notesmodal-add-row_538").remove();
      validStatus++;
    }

    for (elem of $(".each_name").get()) {
      let element = elem.getAttribute("id").split("_")[1];

      // NAME VALIDATION
      if (
        $(`#eachName_${element}`).val() == "" &&
        $(`#eachEmail_${element}`).val() != ""
      ) {
        $(`.rfq_eachName_${element}`).remove();
        $(`#eachName_${element}`)
          .parent()
          .parent()
          .parent()
          .append(
            `<span class="error rfq_eachName_${element}">Vendor Name is required</span>`
          );
        $(`.rfq_eachName_${element}`).show();

        $(`.noteseachName_${element}`).remove();
        $("#notesModalBody").append(
          `<p class="noteseachName_${element} font-monospace text-danger">Vendor Name is required</p>`
        );
      } else {
        $(`.rfq_eachName_${element}`).remove();

        $(`.noteseachName_${element}`).remove();
        specStatus++;
      }

      // EMAIL VALIDATION
      if (
        $(`#eachEmail_${element}`).val() == "" &&
        $(`#eachName_${element}`).val() != ""
      ) {
        $(`.rfq_eachEmail_${element}`).remove();
        $(`#eachEmail_${element}`)
          .parent()
          .parent()
          .parent()
          .append(
            `<span class="error rfq-reachemail-error rfq_eachEmail_${element}">Vendor Email is required</span>`
          );
        $(`.rfq_eachEmail_${element}`).show();

        $(`.noteseachEmail_${element}`).remove();
        $("#notesModalBody").append(
          `<p class="noteseachEmail_${element} font-monospace text-danger">Vendor Email is required</p>`
        );
      } else {
        $(`.rfq_eachEmail_${element}`).remove();

        $(`.noteseachEmail_${element}`).remove();
        specStatus++;
      }

      // EMAIL REGEX VALIDATION
      if ($(`#eachEmail_${element}`).val() != "") {
        if (regEmail.test($(`#eachEmail_${element}`).val())) {
          $(`.rfq_eachEmail_${element}`).remove();

          $(`.noteseachEmail_${element}`).remove();
          specStatus++;
        } else {
          $(`.rfq_eachEmail_${element}`).remove();
          $(`#eachEmail_${element}`)
            .parent()
            .parent()
            .parent()
            .append(
              `<span class="error rfq_eachEmail_${element}">Check your email</span>`
            );
          $(`.rfq_eachEmail_${element}`).show();

          $(`.noteseachEmail_${element}`).remove();
          $("#notesModalBody").append(
            `<p class="noteseachEmail_${element} font-monospace text-danger">Check your email</p>`
          );
        }
      }
    }

    if (validStatus !== 2) {
      e.preventDefault();
      $("#exampleRfqModal").modal("show");
    } else if (specStatus !== $(".each_name").length * 3) {
      e.preventDefault();
      $("#exampleRfqModal").modal("show");
    } else {
      var newArray = new Array();
      var newArray1 = new Array();
      var arr3 = new Array();
      $.each($(".each_name"), function (i, value) {
        newArray.push($(this).val());
      });
      $.each($(".each_email"), function (j, values) {
        newArray1.push($(this).val());
      });

      console.log(newArray);
      console.log(newArray1);
      let i = 0,
        j = 0,
        k = 0;

      while (i < newArray.length && j < newArray1.length) {
        if (newArray[i] == "" && newArray1[j] == "") {
          i++;
          j++;
          continue;
        } else {
          arr3[k++] =
            null +
            "|" +
            null +
            "|" +
            newArray[i++] +
            "|" +
            newArray1[j++] +
            "|others";
        }
      }

      $.ajax({
        type: "POST",
        url: `ajaxs/pr/ajax-rfq-submit.php`,
        data: {
          data: arr3.concat(test),
          rfq_code: $("#rfqNum").val(),
          rfq_item_list_id: $("#rfqId").val(),
          closing_date: $("#closingDate").val(),
        },
        beforeSend: function () {
          $("#addNewOtherVendorId").html(`Submitting...`);
        },
        success: function (response) {
          console.log(JSON.parse(response));
          $("#addNewOtherVendorId").html(`Submitted`);
          window.location.href =
            BASE_URL+"branch/location/manage-rfq.php";
        },
      });
    }
  });
});
