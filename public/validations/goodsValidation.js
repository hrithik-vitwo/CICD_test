
$(document).ready(function () {

  // CREATE VALIDATION
  $(document).on("submit", "#goodsSubmitForm", function (e) {
//alert(1);
    let dataAttrVal = $("#goodTypeDropDown").find(":selected").data("goodtype");
    let validStatus = 0;
    let specStatus = 0;

    // VALIDATION ON CONDITION FOR GOODS TYPE
    if (dataAttrVal == "RM") {
      // GOODS GROUP VALIDATION
      if ($("#goodGroupDropDown").val() == "") {
        $(".gds_group").remove();
        $("#goodGroupDropDown")
          .parent()
          .append(
            '<span class="error gds_group">Goods Group is required</span>'
          );
        $(".gds_group").show();

        $(".notesGoodGroupDropDown").remove();
        $("#notesModalBody").append(
          '<p class="notesGoodGroupDropDown font-monospace text-danger">Goods Group is required</p>'
        );
      } else {
        $(".gds_group").remove();

        $(".notesGoodGroupDropDown").remove();
        validStatus++;
      }

      // PURCHASE GROUP VALIDATION
      if ($("#purchaseGroupDropDown").val() == "") {
        $(".gds_purchase_group").remove();
        $("#purchaseGroupDropDown")
          .parent()
          .append(
            '<span class="error gds_purchase_group">Purchase group is required</span>'
          );
        $(".gds_purchase_group").show();

        $(".notesPurchaseGroupDropDown").remove();
        $("#notesModalBody").append(
          '<p class="notesPurchaseGroupDropDown font-monospace text-danger">Purchase group is required</p>'
        );
      } else {
        $(".gds_purchase_group").remove();

        $(".notesPurchaseGroupDropDown").remove();
        validStatus++;
      }

      // AVAILABILITY CHECK VALIDATION
      if ($("#avl_check").val() == "") {
        $(".gds_avl_check").remove();
        $("#avl_check")
          .parent()
          .append(
            '<span class="error gds_avl_check">Availability check is required</span>'
          );
        $(".gds_avl_check").show();

        $(".notesAvlCheck").remove();
        $("#notesModalBody").append(
          '<p class="notesAvlCheck font-monospace text-danger">Availability check is required</p>'
        );
      } else {
        $(".gds_avl_check").remove();

        $(".notesAvlCheck").remove();
        validStatus++;
      }

        // MWP VALIDATION
        if ($(".rate").val() == "") {
          $(".gds_rate").remove();
          $(".rate")
            .parent()
            .append(
              '<span class="error gds_rate">MWP is required</span>'
            );
          $(".gds_rate").show();
  
          $(".notesItemRate").remove();
          $("#notesModalBody").append(
            '<p class="notesItemRate font-monospace text-danger">MWP is required</p>'
          );
        } else {
          $(".gds_rate").remove();
  
          $(".notesItemRate").remove();
          validStatus++;
        }

      // ITEM NAME VALIDATION
      if ($(".item_name").val() == "") {
        $(".gds_item_name").remove();
        $(".item_name")
          .parent()
          .append(
            '<span class="error gds_item_name">Item name is required</span>'
          );
        $(".gds_item_name").show();

        $(".notesItemName").remove();
        $("#notesModalBody").append(
          '<p class="notesItemName font-monospace text-danger">Item name is required</p>'
        );
      } else {
        $(".gds_item_name").remove();

        $(".notesItemName").remove();
        validStatus++;
      }

      // BASE UNIT OF MEASURE VALIDATION
      if ($("#buomDrop").val() == "") {
        $(".gds_buom").remove();
        $("#buomDrop")
          .parent()
          .append(
            '<span class="error gds_buom" style="position: relative;"">Base Unit of Measure is required</span>'
          );
        $(".gds_buom").show();

        $(".notesBoumDrop").remove();
        $("#notesModalBody").append(
          '<p class="notesBoumDrop font-monospace text-danger">Base Unit of Measure is required</p>'
        );
      } else {
        $(".gds_buom").remove();

        $(".notesBoumDrop").remove();
        validStatus++;
      }

      // ISSUE UNIT OF MEASURE VALIDATION
      if ($("#iuomDrop").val() == "") {
        $(".gds_iuom").remove();
        $("#iuomDrop")
          .parent()
          .append(
            '<span class="error gds_iuom" style="position: relative;">Issue Unit of Measure is required</span>'
          );
        $(".gds_iuom").show();

        $(".notesIuomDrop").remove();
        $("#notesModalBody").append(
          '<p class="notesIuomDrop font-monospace text-danger">Conversion rate is required</p>'
        );
      } else {
        $(".gds_iuom").remove();

        $(".notesIuomDrop").remove();
        validStatus++;
      }

      // ITEM REL VALIDATION
      if ($(".item_rel").val() == "") {
        $(".gds_item_rel").remove();
        $(".item_rel")
          .parent()
          .append(
            '<span class="error gds_item_rel" style="position: relative;">Conversion Rate between two UOM is required.</span>'
          );
        $(".gds_item_rel").show();

        $(".notesItemRel").remove();
        $("#notesModalBody").append(
          '<p class="notesItemRel font-monospace text-danger">Conversion Rate between two UOM is required.</p>'
        );
      } else {
        $(".gds_item_rel").remove();

        $(".notesItemRel").remove();
        validStatus++;
      }

      // HSN VALIDATION
      if ($("#hsnDropDown")[0][0].innerText == "HSN") {
        $(".gds_item_hsn").remove();
        $("#hsnDropDown")
          .parent()
          .append('<span class="error gds_item_hsn">HSN is required</span>');
        $(".gds_item_hsn").show();

        $(".notesHSN").remove();
        $("#notesModalBody").append(
          '<p class="notesHSN font-monospace text-danger">HSN is required</p>'
        );
      } else {
        $(".gds_item_hsn").remove();

        $(".notesHSN").remove();
        validStatus++;
      }

      // ITEM DESCRIPTION VALIDATION
      if ($(".item_desc").val() == "") {
        $(".gds_item_desc").remove();
        $(".item_desc")
          .parent()
          .append(
            '<span class="error gds_item_desc">Item description is required</span>'
          );
        $(".gds_item_desc").show();

        $(".notesItemDesc").remove();
        $("#notesModalBody").append(
          '<p class="notesItemDesc font-monospace text-danger">Item description is required</p>'
        );
      } else {
        $(".gds_item_desc").remove();

        $(".notesItemDesc").remove();
        validStatus++;
      }

      for (elem of $(".spec_vldtn")) {
        let element = elem.getAttribute("data-attr");

        // SPECIFICATION VALIDATION
        if (
          $(`.specification_${element}`).val() == "" &&
          $(`.specificationDetails_${element}`).val() != ""
        ) {
          $(`.gds_item_specification_${element}`).remove();
          $(`.specification_${element}`)
            .parent()
            .append(
              `<span class="error gds_item_specification_${element}">Specification is required</span>`
            );
          $(`.gds_item_specification_${element}`).show();

          $(`.notesSpecification_${element}`).remove();
          $("#notesModalBody").append(
            `<p class="notesSpecification_${element} font-monospace text-danger">Specification is required</p>`
          );
        } else {
          $(`.gds_item_specification_${element}`).remove();

          $(`.notesSpecification_${element}`).remove();
          specStatus++;
        }

        // SPECIFICATION DETAILS VALIDATION
        // if (
        //   $(`.specificationDetails_${element}`).val() == "" &&
        //   $(`.specification_${element}`).val() != ""
        // ) {
        //   $(`.gds_item_specificationDetails_${element}`).remove();
        //   $(`.specificationDetails_${element}`)
        //     .parent()
        //     .append(
        //       `<span class="error gds_item_specificationDetails_${element}">Specification Details is required</span>`
        //     );
        //   $(`.gds_item_specificationDetails_${element}`).show();

        //   $(`.notesSpecificationDetails_${element}`).remove();
        //   $("#notesModalBody").append(
        //     `<p class="notesSpecificationDetails_${element} font-monospace text-danger">Specification Details is required</p>`
        //   );
        // } else {
        //   $(`.gds_item_specificationDetails_${element}`).remove();

        //   $(`.notesSpecificationDetails_${element}`).remove();
        //   specStatus++;
        // }
      }

      if (validStatus !== 10) {
        e.preventDefault();
        $("#exampleModal").modal("show");
      }

      // if (specStatus !== $(".spec_vldtn").length * 2) {
      //   e.preventDefault();
      //   $("#exampleModal").modal("show");
      // }
    } else if (dataAttrVal == "SFG") {
      // GOODS GROUP VALIDATION
      if ($("#goodGroupDropDown").val() == "") {
        $(".gds_group").remove();
        $("#goodGroupDropDown")
          .parent()
          .append(
            '<span class="error gds_group">Goods Group is required</span>'
          );
        $(".gds_group").show();

        $(".notesGoodGroupDropDown").remove();
        $("#notesModalBody").append(
          '<p class="notesGoodGroupDropDown font-monospace text-danger">Goods Group is required</p>'
        );
      } else {
        $(".gds_group").remove();

        $(".notesGoodGroupDropDown").remove();
        validStatus++;
      }

      // PURCHASE GROUP VALIDATION
      if ($("#purchaseGroupDropDown").val() == "") {
        $(".gds_purchase_group").remove();
        $("#purchaseGroupDropDown")
          .parent()
          .append(
            '<span class="error gds_purchase_group">Purchase group is required</span>'
          );
        $(".gds_purchase_group").show();

        $(".notesPurchaseGroupDropDown").remove();
        $("#notesModalBody").append(
          '<p class="notesPurchaseGroupDropDown font-monospace text-danger">Purchase group is required</p>'
        );
      } else {
        $(".gds_purchase_group").remove();

        $(".notesPurchaseGroupDropDown").remove();
        validStatus++;
      }

      // AVAILABILITY CHECK VALIDATION
      if ($("#avl_check").val() == "") {
        $(".gds_avl_check").remove();
        $("#avl_check")
          .parent()
          .append(
            '<span class="error gds_avl_check">Availability check is required</span>'
          );
        $(".gds_avl_check").show();

        $(".notesAvlCheck").remove();
        $("#notesModalBody").append(
          '<p class="notesAvlCheck font-monospace text-danger">Availability check is required</p>'
        );
      } else {
        $(".gds_avl_check").remove();

        $(".notesAvlCheck").remove();
        validStatus++;
      }

      // ITEM NAME VALIDATION
      if ($(".item_name").val() == "") {
        $(".gds_item_name").remove();
        $(".item_name")
          .parent()
          .append(
            '<span class="error gds_item_name">Item name is required</span>'
          );
        $(".gds_item_name").show();

        $(".notesItemName").remove();
        $("#notesModalBody").append(
          '<p class="notesItemName font-monospace text-danger">Item name is required</p>'
        );
      } else {
        $(".gds_item_name").remove();

        $(".notesItemName").remove();
        validStatus++;
      }

      // BASE UNIT OF MEASURE VALIDATION
      if ($("#buomDrop").val() == "") {
        $(".gds_buom").remove();
        $("#buomDrop")
          .parent()
          .append(
            '<span class="error gds_buom" style="position: relative;"">Base Unit of Measure is required</span>'
          );
        $(".gds_buom").show();

        $(".notesBoumDrop").remove();
        $("#notesModalBody").append(
          '<p class="notesBoumDrop font-monospace text-danger">Base Unit of Measure is required</p>'
        );
      } else {
        $(".gds_buom").remove();

        $(".notesBoumDrop").remove();
        validStatus++;
      }

      // ISSUE UNIT OF MEASURE VALIDATION
      if ($("#iuomDrop").val() == "") {
        $(".gds_iuom").remove();
        $("#iuomDrop")
          .parent()
          .append(
            '<span class="error gds_iuom" style="position: relative;">Issue Unit of Measure is required</span>'
          );
        $(".gds_iuom").show();

        $(".notesIuomDrop").remove();
        $("#notesModalBody").append(
          '<p class="notesIuomDrop font-monospace text-danger">Issue Unit of Measure is required</p>'
        );
      } else {
        $(".gds_iuom").remove();

        $(".notesIuomDrop").remove();
        validStatus++;
      }

      // ITEM REL VALIDATION
      if ($(".item_rel").val() == "") {
        $(".gds_item_rel").remove();
        $(".item_rel")
          .parent()
          .append(
            '<span class="error gds_item_rel" style="position: relative;">Conversion Rate between two UOM is required.</span>'
          );
        $(".gds_item_rel").show();

        $(".notesItemRel").remove();
        $("#notesModalBody").append(
          '<p class="notesItemRel font-monospace text-danger">Conversion Rate between two UOM is required.</p>'
        );
      } else {
        $(".gds_item_rel").remove();

        $(".notesItemRel").remove();
        validStatus++;
      }

      // HSN VALIDATION
      if ($("#hsnDropDown")[0][0].innerText == "HSN") {
        $(".gds_item_hsn").remove();
        $("#hsnDropDown")
          .parent()
          .append('<span class="error gds_item_hsn">HSN is required</span>');
        $(".gds_item_hsn").show();

        $(".notesHSN").remove();
        $("#notesModalBody").append(
          '<p class="notesHSN font-monospace text-danger">HSN is required</p>'
        );
      } else {
        $(".gds_item_hsn").remove();

        $(".notesHSN").remove();
        validStatus++;
      }

      // ITEM DESCRIPTION VALIDATION
      if ($(".item_desc").val() == "") {
        $(".gds_item_desc").remove();
        $(".item_desc")
          .parent()
          .append(
            '<span class="error gds_item_desc">Item description is required</span>'
          );
        $(".gds_item_desc").show();

        $(".notesItemDesc").remove();
        $("#notesModalBody").append(
          '<p class="notesItemDesc font-monospace text-danger">Item description is required</p>'
        );
      } else {
        $(".gds_item_desc").remove();

        $(".notesItemDesc").remove();
        validStatus++;
      }

      for (elem of $(".spec_vldtn")) {
        let element = elem.getAttribute("data-attr");

        // SPECIFICATION VALIDATION
        if (
          $(`.specification_${element}`).val() == "" &&
          $(`.specificationDetails_${element}`).val() != ""
        ) {
          $(`.gds_item_specification_${element}`).remove();
          $(`.specification_${element}`)
            .parent()
            .append(
              `<span class="error gds_item_specification_${element}">Specification is required</span>`
            );
          $(`.gds_item_specification_${element}`).show();

          $(`.notesSpecification_${element}`).remove();
          $("#notesModalBody").append(
            `<p class="notesSpecification_${element} font-monospace text-danger">Specification is required</p>`
          );
        } else {
          $(`.gds_item_specification_${element}`).remove();

          $(`.notesSpecification_${element}`).remove();
          specStatus++;
        }

        // SPECIFICATION DETAILS VALIDATION
        // if (
        //   $(`.specificationDetails_${element}`).val() == "" &&
        //   $(`.specification_${element}`).val() != ""
        // ) {
        //   $(`.gds_item_specificationDetails_${element}`).remove();
        //   $(`.specificationDetails_${element}`)
        //     .parent()
        //     .append(
        //       `<span class="error gds_item_specificationDetails_${element}">Specification Details is required</span>`
        //     );
        //   $(`.gds_item_specificationDetails_${element}`).show();

        //   $(`.notesSpecificationDetails_${element}`).remove();
        //   $("#notesModalBody").append(
        //     `<p class="notesSpecificationDetails_${element} font-monospace text-danger">Specification Details is required</p>`
        //   );
        // } else {
        //   $(`.gds_item_specificationDetails_${element}`).remove();

        //   $(`.notesSpecificationDetails_${element}`).remove();
        //   specStatus++;
        // }
      }

      if (validStatus !== 9) {
        e.preventDefault();
        $("#exampleModal").modal("show");
      }

      // if (specStatus !== $(".spec_vldtn").length * 2) {
      //   e.preventDefault();
      //   $("#exampleModal").modal("show");
      // }
    } else if (dataAttrVal == "FG") {
    

      // BOM TYPE VALIDATION
      if ($("[name='bomRequired_radio']:checked").length === 0) {
        $(".gds_bomRequired_radio").remove();
        $("[name='bomRequired_radio']")
          .parent()
          .parent()
          .parent()
          .append(
            '<span class="error gds_bomRequired_radio">BOM Type is required</span>'
          );
        $(".gds_bomRequired_radio").show();

        $(".notesbomRequired_radio").remove();
        $("#notesModalBody").append(
          '<p class="notesbomRequired_radio font-monospace text-danger">BOM Type is required</p>'
        );
      } else {
        $(".gds_bomRequired_radio").remove();

        $(".notesbomRequired_radio").remove();
        validStatus++;
      }

      // ITEM NAME VALIDATION
      if ($(".item_name").val() == "") {
        $(".gds_item_name").remove();
        $(".item_name")
          .parent()
          .append(
            '<span class="error gds_item_name">Item name is required</span>'
          );
        $(".gds_item_name").show();

        $(".notesItemName").remove();
        $("#notesModalBody").append(
          '<p class="notesItemName font-monospace text-danger">Item name is required</p>'
        );
      } else {
        $(".gds_item_name").remove();

        $(".notesItemName").remove();
        validStatus++;
      }

      // BASE UNIT OF MEASURE VALIDATION
      if ($("#buomDrop").val() == "") {
        $(".gds_buom").remove();
        $("#buomDrop")
          .parent()
          .append(
            '<span class="error gds_buom" style="position: relative;"">Base Unit of Measure is required</span>'
          );
        $(".gds_buom").show();

        $(".notesBoumDrop").remove();
        $("#notesModalBody").append(
          '<p class="notesBoumDrop font-monospace text-danger">Base Unit of Measure is required</p>'
        );
      } else {
        $(".gds_buom").remove();

        $(".notesBoumDrop").remove();
        validStatus++;
      }

      // ISSUE UNIT OF MEASURE VALIDATION
      if ($("#iuomDrop").val() == "") {
        $(".gds_iuom").remove();
        $("#iuomDrop")
          .parent()
          .append(
            '<span class="error gds_iuom" style="position: relative;">Issue Unit of Measure is required</span>'
          );
        $(".gds_iuom").show();

        $(".notesIuomDrop").remove();
        $("#notesModalBody").append(
          '<p class="notesIuomDrop font-monospace text-danger">Issue Unit of Measure is required</p>'
        );
      } else {
        $(".gds_iuom").remove();

        $(".notesIuomDrop").remove();
        validStatus++;
      }

      // ITEM REL VALIDATION
      if ($(".item_rel").val() == "") {
        $(".gds_item_rel").remove();
        $(".item_rel")
          .parent()
          .append(
            '<span class="error gds_item_rel" style="position: relative;">Conversion Rate between two UOM is required.</span>'
          );
        $(".gds_item_rel").show();

        $(".notesItemRel").remove();
        $("#notesModalBody").append(
          '<p class="notesItemRel font-monospace text-danger">Conversion Rate between two UOM is required.</p>'
        );
      } else {
        $(".gds_item_rel").remove();

        $(".notesItemRel").remove();
        validStatus++;
      }

      // HSN VALIDATION
      if ($("#hsnDropDown")[0][0].innerText == "HSN") {
        $(".gds_item_hsn").remove();
        $("#hsnDropDown")
          .parent()
          .append('<span class="error gds_item_hsn">HSN is required</span>');
        $(".gds_item_hsn").show();

        $(".notesHSN").remove();
        $("#notesModalBody").append(
          '<p class="notesHSN font-monospace text-danger">HSN is required</p>'
        );
      } else {
        $(".gds_item_hsn").remove();

        $(".notesHSN").remove();
        validStatus++;
      }

      // ITEM DESCRIPTION VALIDATION
      if ($(".item_desc").val() == "") {
        $(".gds_item_desc").remove();
        $(".item_desc")
          .parent()
          .append(
            '<span class="error gds_item_desc">Item description is required</span>'
          );
        $(".gds_item_desc").show();

        $(".notesItemDesc").remove();
        $("#notesModalBody").append(
          '<p class="notesItemDesc font-monospace text-danger">Item description is required</p>'
        );
      } else {
        $(".gds_item_desc").remove();

        $(".notesItemDesc").remove();
        validStatus++;
      }

      // PRICE VALIDATION
      if ($(".price").val() == "") {
        $(".gds_price").remove();
        $(".price")
          .parent()
          .append('<span class="error gds_price">Price is required</span>');
        $(".gds_price").show();

        $(".notesPrice").remove();
        $("#notesModalBody").append(
          '<p class="notesPrice font-monospace text-danger">Price is required</p>'
        );
      } else {
        $(".gds_price").remove();

        $(".notesPrice").remove();
        validStatus++;
      }

      // DISCOUNT VALIDATION
      if ($(".discount").val() == "") {
        $(".gds_discount").remove();
        $(".discount")
          .parent()
          .append(
            '<span class="error gds_discount">Discount is required</span>'
          );
        $(".gds_discount").show();

        $(".notesDiscount").remove();
        $("#notesModalBody").append(
          '<p class="notesDiscount font-monospace text-danger">Discount is required</p>'
        );
      } else {
        $(".gds_discount").remove();

        $(".notesDiscount").remove();
        validStatus++;
      }

      if (validStatus !== 9) {
        e.preventDefault();
        $("#exampleModal").modal("show");
      }

    
    } 
   else if (dataAttrVal == "SERVICES") {
      // GOODS GROUP VALIDATION
      if ($("#goodGroupDropDown").val() == "") {
        $(".gds_group").remove();
        $("#goodGroupDropDown")
          .parent()
          .append(
            '<span class="error gds_group">Goods Group is required</span>'
          );
        $(".gds_group").show();

        $(".notesGoodGroupDropDown").remove();
        $("#notesModalBody").append(
          '<p class="notesGoodGroupDropDown font-monospace text-danger">Goods Group is required</p>'
        );
      } else {
        $(".gds_group").remove();

        $(".notesGoodGroupDropDown").remove();
        validStatus++;
      }

      // PRICE VALIDATION
      if ($(".price").val() == "") {
        $(".gds_price").remove();
        $(".price")
          .parent()
          .append('<span class="error gds_price">Price is required</span>');
        $(".gds_price").show();

        $(".notesPrice").remove();
        $("#notesModalBody").append(
          '<p class="notesPrice font-monospace text-danger">Price is required</p>'
        );
      } else {
        $(".gds_price").remove();

        $(".notesPrice").remove();
        validStatus++;
      }

      // DISCOUNT VALIDATION
      if ($(".discount").val() == "") {
        $(".gds_discount").remove();
        $(".discount")
          .parent()
          .append(
            '<span class="error gds_discount">Discount is required</span>'
          );
        $(".gds_discount").show();

        $(".notesDiscount").remove();
        $("#notesModalBody").append(
          '<p class="notesDiscount font-monospace text-danger">Discount is required</p>'
        );
      } else {
        $(".gds_discount").remove();

        $(".notesDiscount").remove();
        validStatus++;
      }

      // ITEM NAME VALIDATION
      if ($(".service_name").val() == "") {
        $(".gds_service_name").remove();
        $(".service_name")
          .parent()
          .append(
            '<span class="error gds_service_name">Service name is required</span>'
          );
        $(".gds_service_name").show();

        $(".notesServiceName").remove();
        $("#notesModalBody").append(
          '<p class="notesServiceName font-monospace text-danger">Service name is required</p>'
        );
      } else {
        $(".gds_service_name").remove();

        $(".notesServiceName").remove();
        validStatus++;
      }

    //TARGET PRICE VALIDATION
      if ($("#service_target_price").val() == "") {
       
       $(".gds_service_target_price").html('target price required');
        $(".gds_service_target_price").show();

  $(".notesServiceTargetPrice").remove();
        $("#notesModalBody").append(
          '<p class="notesServiceTargetPrice font-monospace text-danger">Target Price is required</p>'
        );



      
      } else {
        $(".gds_service_target_price").remove();

        $(".notesServiceTargetPrice").remove();
        validStatus++;
      }

      // ITEM DESCRIPTION VALIDATION
      if ($(".service_desc").val() == "") {
        $(".gds_service_desc").remove();
        $(".service_desc")
          .parent()
          .append(
            '<span class="error gds_service_desc">Service description is required</span>'
          );
        $(".gds_service_desc").show();

        $(".notesServiceDesc").remove();
        $("#notesModalBody").append(
          '<p class="notesServiceDesc font-monospace text-danger">Service description is required</p>'
        );
      } else {
        $(".gds_service_desc").remove();

        $(".notesServiceDesc").remove();
        validStatus++;
      }

      // HSN VALIDATION
      if ($(".servicehsnDropDown")[0][0].innerText == "HSN") {
        $(".gds_service_hsn").remove();
        $(".servicehsnDropDown")
          .parent()
          .append('<span class="error gds_service_hsn">HSN is required</span>');
        $(".gds_service_hsn").show();

        $(".notesServiceHSN").remove();
        $("#notesModalBody").append(
          '<p class="notesServiceHSN font-monospace text-danger">HSN is required</p>'
        );
      } else {
        $(".gds_service_hsn").remove();

        $(".notesServiceHSN").remove();
        validStatus++;
      }

      // GL CODE VALIDATION
      if ($("#glCode").val() == "") {
        $(".gds_item_glcode").remove();
        $("#glCode")
          .parent()
          .append(
            '<span class="error gds_item_glcode">GL Code is required</span>'
          );
        $(".gds_item_glcode").show();

        $(".notesGlCode").remove();
        $("#notesModalBody").append(
          '<p class="notesGlCode font-monospace text-danger">GL Code is required</p>'
        );
      } else {
        $(".gds_item_glcode").remove();

        $(".notesGlCode").remove();
        validStatus++;
      }

      // SERVICE UNIT VALIDATION
      if ($("#serviceUnitDrop").val() == "" || $("#serviceUnitDrop").val() == "Service Unit of Measurement") {
      //  alert(1);
        $(".gds_service_unit").remove();
        $("#serviceUnitDrop")
          .parent()
          .append(
            '<span class="error gds_service_unit">Service Unit is required</span>'
          );
        $(".gds_service_unit").show();

        $(".notesservice_unit").remove();
        $(
          "#notesModalBody").append(
          '<p class="notesservice_unit font-monospace text-danger">Service Unit is required</p>'
        );
      } else {
       // alert($("#serviceUnitDrop").val());
        $(".gds_service_unit").remove();

        $(".notesservice_unit").remove();
        validStatus++;
      }


      if (validStatus !== 9) {
        e.preventDefault();
        $("#exampleModal").modal("show");
      }
    } else if (dataAttrVal == "SERVICEP") {
      // GOODS GROUP VALIDATION
      if ($("#goodGroupDropDown").val() == "") {
        $(".gds_group").remove();
        $("#goodGroupDropDown")
          .parent()
          .append(
            '<span class="error gds_group">Goods Group is required</span>'
          );
        $(".gds_group").show();

        $(".notesGoodGroupDropDown").remove();
        $("#notesModalBody").append(
          '<p class="notesGoodGroupDropDown font-monospace text-danger">Goods Group is required</p>'
        );
      } else {
        $(".gds_group").remove();

        $(".notesGoodGroupDropDown").remove();
        validStatus++;
      }

      // ITEM NAME VALIDATION
      if ($(".service_name").val() == "") {
        $(".gds_service_name").remove();
        $(".service_name")
          .parent()
          .append(
            '<span class="error gds_service_name">Service name is required</span>'
          );
        $(".gds_service_name").show();

        $(".notesServiceName").remove();
        $("#notesModalBody").append(
          '<p class="notesServiceName font-monospace text-danger">Service name is required</p>'
        );
      } else {
        $(".gds_service_name").remove();

        $(".notesServiceName").remove();
        validStatus++;
      }

      // ITEM DESCRIPTION VALIDATION
      if ($(".service_desc").val() == "") {
        $(".gds_service_desc").remove();
        $(".service_desc")
          .parent()
          .append(
            '<span class="error gds_service_desc">Service description is required</span>'
          );
        $(".gds_service_desc").show();

        $(".notesServiceDesc").remove();
        $("#notesModalBody").append(
          '<p class="notesServiceDesc font-monospace text-danger">Service description is required</p>'
        );
      } else {
        $(".gds_service_desc").remove();

        $(".notesServiceDesc").remove();
        validStatus++;
      }

      // HSN VALIDATION
      if ($(".servicehsnDropDown")[0][0].innerText == "HSN") {
        $(".gds_service_hsn").remove();
        $(".servicehsnDropDown")
          .parent()
          .append('<span class="error gds_service_hsn">HSN is required</span>');
        $(".gds_service_hsn").show();

        $(".notesServiceHSN").remove();
        $("#notesModalBody").append(
          '<p class="notesServiceHSN font-monospace text-danger">HSN is required</p>'
        );
      } else {
        $(".gds_service_hsn").remove();

        $(".notesServiceHSN").remove();
        validStatus++;
      }

      // TDS VALIDATION
      if ($("#tdsDropDown")[0][0].innerText == "SELECT TDS") {
        $(".gds_item_tds").remove();
        $("#tdsDropDown")
          .parent()
          .append('<span class="error gds_item_tds">TDS is required</span>');
        $(".gds_item_tds").show();

        $(".notesTDS").remove();
        $("#notesModalBody").append(
          '<p class="notesTDS font-monospace text-danger">TDS is required</p>'
        );
      } else {
        $(".gds_item_tds").remove();

        $(".notesTDS").remove();
        validStatus++;
      }

      // GL CODE VALIDATION
      if ($("#glCode").val() == "") {
        $(".gds_item_glcode").remove();
        $("#glCode")
          .parent()
          .append(
            '<span class="error gds_item_glcode">GL Code is required</span>'
          );
        $(".gds_item_glcode").show();

        $(".notesGlCode").remove();
        $("#notesModalBody").append(
          '<p class="notesGlCode font-monospace text-danger">GL Code is required</p>'
        );
      } else {
        $(".gds_item_glcode").remove();

        $(".notesGlCode").remove();
        validStatus++;
      }

      // SERVICE UNIT VALIDATION
      if ($("#serviceUnitDrop").val() == "" || $("#serviceUnitDrop").val() == "Service Unit of Measurement") {
        $(".gds_service_unit").remove();
        $("#serviceUnitDrop")
          .parent()
          .append(
            '<span class="error gds_service_unit">Service unit is required</span>'
          );
        $(".gds_service_unit").show();

        $(".notesServiceUnit").remove();
        $("#notesModalBody").append(
          '<p class="notesServiceUnit font-monospace text-danger">Service unit is required</p>'
        );
      } else {
        $(".gds_service_unit").remove();

        $(".notesServiceUnit").remove();
        validStatus++;
      }

      if (validStatus !== 7) {
        e.preventDefault();
        $("#exampleModal").modal("show");
      }
    } else if (dataAttrVal == "ASSET") {
 

      // GL CODE VALIDATION
      if ($("#glCodeAsset").val() == "") {
        $(".gds_glCodeAsset").remove();
        $("#glCodeAsset")
          .parent()
          .append(
            '<span class="error gds_glCodeAsset">GL Code is required</span>'
          );
        $(".gds_glCodeAsset").show();

        $(".notesglCodeAsset").remove();
        $("#notesModalBody").append(
          '<p class="notesglCodeAsset font-monospace text-danger">GL Code is required</p>'
        );
      } else {
        $(".gds_glCodeAsset").remove();

        $(".notesglCodeAsset").remove();
        validStatus++;
      }

      // ASSET CLASSIFICATION VALIDATION
      if ($("#asset_classification_select").val() == "") {
        $(".gds_asset_classification_select").remove();
        $("#asset_classification_select")
          .parent()
          .append(
            '<span class="error gds_asset_classification_select">Asset Classification is required</span>'
          );
        $(".gds_asset_classification_select").show();

        $(".notesasset_classification_select").remove();
        $("#notesModalBody").append(
          '<p class="notesasset_classification_select font-monospace text-danger">Asset Classification is required</p>'
        );
      } else {
        $(".gds_asset_classification_select").remove();

        $(".notesasset_classification_select").remove();
        validStatus++;
      }

      // ITEM NAME VALIDATION
      if ($(".item_name").val() == "") {
        $(".gds_item_name").remove();
        $(".item_name")
          .parent()
          .append(
            '<span class="error gds_item_name">Item name is required</span>'
          );
        $(".gds_item_name").show();

        $(".notesItemName").remove();
        $("#notesModalBody").append(
          '<p class="notesItemName font-monospace text-danger">Item name is required</p>'
        );
      } else {
        $(".gds_item_name").remove();

        $(".notesItemName").remove();
        validStatus++;
      }

      // COST CENTER VALIDATION
      if ($("#cost_center").val() == "") {
        $(".gds_cost_center").remove();
        $("#cost_center")
          .parent()
          .append(
            '<span class="error gds_cost_center">Cost Center is required</span>'
          );
        $(".gds_cost_center").show();

        $(".notescost_center").remove();
        $("#notesModalBody").append(
          '<p class="notescost_center font-monospace text-danger">Cost Center is required</p>'
        );
      } else {
        $(".gds_cost_center").remove();

        $(".notescost_center").remove();
        validStatus++;
      }

      // BASE UNIT OF MEASURE VALIDATION
      if ($("#buomDrop").val() == "") {
        $(".gds_buom").remove();
        $("#buomDrop")
          .parent()
          .append(
            '<span class="error gds_buom" style="position: relative;"">Base Unit of Measure is required</span>'
          );
        $(".gds_buom").show();

        $(".notesBoumDrop").remove();
        $("#notesModalBody").append(
          '<p class="notesBoumDrop font-monospace text-danger">Base Unit of Measure is required</p>'
        );
      } else {
        $(".gds_buom").remove();

        $(".notesBoumDrop").remove();
        validStatus++;
      }

      // ISSUE UNIT OF MEASURE VALIDATION
      if ($("#iuomDrop").val() == "") {
        $(".gds_iuom").remove();
        $("#iuomDrop")
          .parent()
          .append(
            '<span class="error gds_iuom" style="position: relative;">Issue Unit of Measure is required</span>'
          );
        $(".gds_iuom").show();

        $(".notesIuomDrop").remove();
        $("#notesModalBody").append(
          '<p class="notesIuomDrop font-monospace text-danger">Issue Unit of Measure is required</p>'
        );
      } else {
        $(".gds_iuom").remove();

        $(".notesIuomDrop").remove();
        validStatus++;
      }

      // ITEM REL VALIDATION
      if ($(".item_rel").val() == "") {
        $(".gds_item_rel").remove();
        $(".item_rel")
          .parent()
          .append(
            '<span class="error gds_item_rel" style="position: relative;">Conversion Rate between two UOM is required.</span>'
          );
        $(".gds_item_rel").show();

        $(".notesItemRel").remove();
        $("#notesModalBody").append(
          '<p class="notesItemRel font-monospace text-danger">Conversion Rate between two UOM is required.</p>'
        );
      } else {
        $(".gds_item_rel").remove();

        $(".notesItemRel").remove();
        validStatus++;
      }

      // HSN VALIDATION
      if ($("#hsnDropDown")[0][0].innerText == "HSN") {
        $(".gds_item_hsn").remove();
        $("#hsnDropDown")
          .parent()
          .append('<span class="error gds_item_hsn">HSN is required</span>');
        $(".gds_item_hsn").show();

        $(".notesHSN").remove();
        $("#notesModalBody").append(
          '<p class="notesHSN font-monospace text-danger">HSN is required</p>'
        );
      } else {
        $(".gds_item_hsn").remove();

        $(".notesHSN").remove();
        validStatus++;
      }

      // ITEM DESCRIPTION VALIDATION
      if ($(".item_desc").val() == "") {
        $(".gds_item_desc").remove();
        $(".item_desc")
          .parent()
          .append(
            '<span class="error gds_item_desc">Item description is required</span>'
          );
        $(".gds_item_desc").show();

        $(".notesItemDesc").remove();
        $("#notesModalBody").append(
          '<p class="notesItemDesc font-monospace text-danger">Item description is required</p>'
        );
      } else {
        $(".gds_item_desc").remove();

        $(".notesItemDesc").remove();
        validStatus++;
      }

      for (elem of $(".spec_vldtn")) {
        let element = elem.getAttribute("data-attr");

        // SPECIFICATION VALIDATION
        if (
          $(`.specification_${element}`).val() == "" &&
          $(`.specificationDetails_${element}`).val() != ""
        ) {
          $(`.gds_item_specification_${element}`).remove();
          $(`.specification_${element}`)
            .parent()
            .append(
              `<span class="error gds_item_specification_${element}">Specification is required</span>`
            );
          $(`.gds_item_specification_${element}`).show();

          $(`.notesSpecification_${element}`).remove();
          $("#notesModalBody").append(
            `<p class="notesSpecification_${element} font-monospace text-danger">Specification is required</p>`
          );
        } else {
          $(`.gds_item_specification_${element}`).remove();

          $(`.notesSpecification_${element}`).remove();
          specStatus++;
        }

     
      }

    
      if (validStatus !== 9) {
        e.preventDefault();
        $("#exampleModal").modal("show");
      } 
    

    }
  });

  // UPDATE VALIDATION
  $(document).on("submit", "#goodsEditForm", function (e) {

    //  let dataAttrVal = $("#goodTypeDropDown_edit").find(":selected").data("goodstype");
     let dataAttrVal = $("#goodsTypeName").val();
    //alert(dataAttrVal);
    let validStatus = 0;
    let specFlag = false;
    let specDetailsFlag = false;

    // VALIDATION ON CONDITION FOR GOODS TYPE
    if (dataAttrVal == "Raw Material") {


      // AVAILABILITY CHECK VALIDATION
      if ($("#avl_check").val() == "") {
        $(".gds_avl_check").remove();
        $("#avl_check")
          .parent()
          .append(
            '<span class="error gds_avl_check">Availability check is required</span>'
          );
        $(".gds_avl_check").show();

        $(".notesAvlCheck").remove();
        $("#notesModalBody").append(
          '<p class="notesAvlCheck font-monospace text-danger">Availability check is required</p>'
        );
      } else {
        $(".gds_avl_check").remove();

        $(".notesAvlCheck").remove();
        validStatus++;
      }

         // MWP VALIDATION
         if ($(".rate").val() == "") {
          $(".gds_rate").remove();
          $(".rate")
            .parent()
            .append(
              '<span class="error gds_rate">MWP is required</span>'
            );
          $(".gds_rate").show();
  
          $(".notesItemRate").remove();
          $("#notesModalBody").append(
            '<p class="notesItemRate font-monospace text-danger">MWP is required</p>'
          );
        } else {
          $(".gds_rate").remove();
  
          $(".notesItemRate").remove();
          validStatus++;
        }
      

      // ITEM NAME VALIDATION
      if ($(".item_name").val() == "") {
        $(".gds_item_name").remove();
        $(".item_name")
          .parent()
          .append(
            '<span class="error gds_item_name">Item name is required</span>'
          );
        $(".gds_item_name").show();

        $(".notesItemName").remove();
        $("#notesModalBody").append(
          '<p class="notesItemName font-monospace text-danger">Item name is required</p>'
        );
      } else {
        $(".gds_item_name").remove();

        $(".notesItemName").remove();
        validStatus++;
      }

      // BASE UNIT OF MEASURE VALIDATION
      if ($("#buomDrop").val() == "") {
        $(".gds_buom").remove();
        $("#buomDrop")
          .parent()
          .append(
            '<span class="error gds_buom" style="position: relative;"">Base Unit of Measure is required</span>'
          );
        $(".gds_buom").show();

        $(".notesBoumDrop").remove();
        $("#notesModalBody").append(
          '<p class="notesBoumDrop font-monospace text-danger">Base Unit of Measure is required</p>'
        );
      } else {
        $(".gds_buom").remove();

        $(".notesBoumDrop").remove();
        validStatus++;
      }

      // ISSUE UNIT OF MEASURE VALIDATION
      if ($("#iuomDrop").val() == "") {
        $(".gds_iuom").remove();
        $("#iuomDrop")
          .parent()
          .append(
            '<span class="error gds_iuom" style="position: relative;">Issue Unit of Measure is required</span>'
          );
        $(".gds_iuom").show();

        $(".notesIuomDrop").remove();
        $("#notesModalBody").append(
          '<p class="notesIuomDrop font-monospace text-danger">Issue Unit of Measure is required</p>'
        );
      } else {
        $(".gds_iuom").remove();

        $(".notesIuomDrop").remove();
        validStatus++;
      }

      // ITEM REL VALIDATION
      if ($(".item_rel").val() == "") {
        $(".gds_item_rel").remove();
        $(".item_rel")
          .parent()
          .append(
            '<span class="error gds_item_rel" style="position: relative;">Conversion Rate between two UOM is required.</span>'
          );
        $(".gds_item_rel").show();

        $(".notesItemRel").remove();
        $("#notesModalBody").append(
          '<p class="notesItemRel font-monospace text-danger">Conversion Rate between two UOM is required.</p>'
        );
      } else {
        $(".gds_item_rel").remove();

        $(".notesItemRel").remove();
        validStatus++;
      }

      // HSN VALIDATION
      if ($("#hsnDropDown")[0][0].innerText == "HSN") {
        $(".gds_item_hsn").remove();
        $("#hsnDropDown")
          .parent()
          .append('<span class="error gds_item_hsn">HSN is required</span>');
        $(".gds_item_hsn").show();

        $(".notesHSN").remove();
        $("#notesModalBody").append(
          '<p class="notesHSN font-monospace text-danger">HSN is required</p>'
        );
      } else {
        $(".gds_item_hsn").remove();

        $(".notesHSN").remove();
        validStatus++;
      }

      // ITEM DESCRIPTION VALIDATION
      if ($(".item_desc").val() == "") {
        $(".gds_item_desc").remove();
        $(".item_desc")
          .parent()
          .append(
            '<span class="error gds_item_desc">Item description is required</span>'
          );
        $(".gds_item_desc").show();

        $(".notesItemDesc").remove();
        $("#notesModalBody").append(
          '<p class="notesItemDesc font-monospace text-danger">Item description is required</p>'
        );
      } else {
        $(".gds_item_desc").remove();

        $(".notesItemDesc").remove();
        validStatus++;
      }



      if (validStatus !== 8) {
        e.preventDefault();
        $("#exampleModal").modal("show");
      }

     
     
    } 
    else if (dataAttrVal == "Semi Finished Good") {
      

      // ITEM NAME VALIDATION
      if ($(".item_name").val() == "") {
        $(".gds_item_name").remove();
        $(".item_name")
          .parent()
          .append(
            '<span class="error gds_item_name">Item name is required</span>'
          );
        $(".gds_item_name").show();

        $(".notesItemName").remove();
        $("#notesModalBody").append(
          '<p class="notesItemName font-monospace text-danger">Item name is required</p>'
        );
      } else {
        $(".gds_item_name").remove();

        $(".notesItemName").remove();
        validStatus++;
      }

      // BASE UNIT OF MEASURE VALIDATION
      if ($("#buomDrop").val() == "") {
        $(".gds_buom").remove();
        $("#buomDrop")
          .parent()
          .append(
            '<span class="error gds_buom" style="position: relative;"">Base Unit of Measure is required</span>'
          );
        $(".gds_buom").show();

        $(".notesBoumDrop").remove();
        $("#notesModalBody").append(
          '<p class="notesBoumDrop font-monospace text-danger">Base Unit of Measure is required</p>'
        );
      } else {
        $(".gds_buom").remove();

        $(".notesBoumDrop").remove();
        validStatus++;
      }

      // ISSUE UNIT OF MEASURE VALIDATION
      if ($("#iuomDrop").val() == "") {
        $(".gds_iuom").remove();
        $("#iuomDrop")
          .parent()
          .append(
            '<span class="error gds_iuom" style="position: relative;">Issue Unit of Measure is required</span>'
          );
        $(".gds_iuom").show();

        $(".notesIuomDrop").remove();
        $("#notesModalBody").append(
          '<p class="notesIuomDrop font-monospace text-danger">Issue Unit of Measure is required</p>'
        );
      } else {
        $(".gds_iuom").remove();

        $(".notesIuomDrop").remove();
        validStatus++;
      }

      // ITEM REL VALIDATION
      if ($(".item_rel").val() == "") {
        $(".gds_item_rel").remove();
        $(".item_rel")
          .parent()
          .append(
            '<span class="error gds_item_rel" style="position: relative;">Conversion Rate between two UOM is required.</span>'
          );
        $(".gds_item_rel").show();

        $(".notesItemRel").remove();
        $("#notesModalBody").append(
          '<p class="notesItemRel font-monospace text-danger">Conversion Rate between two UOM is required.</p>'
        );
      } else {
        $(".gds_item_rel").remove();

        $(".notesItemRel").remove();
        validStatus++;
      }

      // HSN VALIDATION
      if ($("#hsnDropDown")[0][0].innerText == "HSN") {
        $(".gds_item_hsn").remove();
        $("#hsnDropDown")
          .parent()
          .append('<span class="error gds_item_hsn">HSN is required</span>');
        $(".gds_item_hsn").show();

        $(".notesHSN").remove();
        $("#notesModalBody").append(
          '<p class="notesHSN font-monospace text-danger">HSN is required</p>'
        );
      } else {
        $(".gds_item_hsn").remove();

        $(".notesHSN").remove();
        validStatus++;
      }

      // ITEM DESCRIPTION VALIDATION
      if ($(".item_desc").val() == "") {
        $(".gds_item_desc").remove();
        $(".item_desc")
          .parent()
          .append(
            '<span class="error gds_item_desc">Item description is required</span>'
          );
        $(".gds_item_desc").show();

        $(".notesItemDesc").remove();
        $("#notesModalBody").append(
          '<p class="notesItemDesc font-monospace text-danger">Item description is required</p>'
        );
      } else {
        $(".gds_item_desc").remove();

        $(".notesItemDesc").remove();
        validStatus++;
      }

    

       
       

      if (validStatus !== 6) {
        e.preventDefault();
        $("#exampleModal").modal("show");
      }

   
    } 
    else if (dataAttrVal == "FG Trading" || dataAttrVal == "Finished Good") {


         // AVAILABILITY CHECK VALIDATION
         if ($("#avl_check").val() == "") {
          $(".gds_avl_check").remove();
          $("#avl_check")
            .parent()
            .append(
              '<span class="error gds_avl_check">Availability check is required</span>'
            );
          $(".gds_avl_check").show();
  
          $(".notesAvlCheck").remove();
          $("#notesModalBody").append(
            '<p class="notesAvlCheck font-monospace text-danger">Availability check is required</p>'
          );
        } else {
          $(".gds_avl_check").remove();
  
          $(".notesAvlCheck").remove();
          validStatus++;
        }
  
    
      // ITEM NAME VALIDATION
      if ($(".item_name").val() == "") {
        $(".gds_item_name").remove();
        $(".item_name")
          .parent()
          .append(
            '<span class="error gds_item_name">Item name is required</span>'
          );
        $(".gds_item_name").show();

        $(".notesItemName").remove();
        $("#notesModalBody").append(
          '<p class="notesItemName font-monospace text-danger">Item name is required</p>'
        );
      } else {
        $(".gds_item_name").remove();

        $(".notesItemName").remove();
        validStatus++;
      }

      // BASE UNIT OF MEASURE VALIDATION
      if ($("#buomDrop").val() == "") {
        $(".gds_buom").remove();
        $("#buomDrop")
          .parent()
          .append(
            '<span class="error gds_buom" style="position: relative;"">Base Unit of Measure is required</span>'
          );
        $(".gds_buom").show();

        $(".notesBoumDrop").remove();
        $("#notesModalBody").append(
          '<p class="notesBoumDrop font-monospace text-danger">Base Unit of Measure is required</p>'
        );
      } else {
        $(".gds_buom").remove();

        $(".notesBoumDrop").remove();
        validStatus++;
      }

      // ISSUE UNIT OF MEASURE VALIDATION
      if ($("#iuomDrop").val() == "") {
        $(".gds_iuom").remove();
        $("#iuomDrop")
          .parent()
          .append(
            '<span class="error gds_iuom" style="position: relative;">Issue Unit of Measure is required</span>'
          );
        $(".gds_iuom").show();

        $(".notesIuomDrop").remove();
        $("#notesModalBody").append(
          '<p class="notesIuomDrop font-monospace text-danger">Issue Unit of Measure is required</p>'
        );
      } else {
        $(".gds_iuom").remove();

        $(".notesIuomDrop").remove();
        validStatus++;
      }

      // ITEM REL VALIDATION
      if ($(".item_rel").val() == "") {
        $(".gds_item_rel").remove();
        $(".item_rel")
          .parent()
          .append(
            '<span class="error gds_item_rel" style="position: relative;">Conversion Rate between two UOM is required.</span>'
          );
        $(".gds_item_rel").show();

        $(".notesItemRel").remove();
        $("#notesModalBody").append(
          '<p class="notesItemRel font-monospace text-danger">Conversion Rate between two UOM is required.</p>'
        );
      } else {
        $(".gds_item_rel").remove();

        $(".notesItemRel").remove();
        validStatus++;
      }

      // HSN VALIDATION
      if ($("#hsnDropDown")[0][0].innerText == "HSN") {
        $(".gds_item_hsn").remove();
        $("#hsnDropDown")
          .parent()
          .append('<span class="error gds_item_hsn">HSN is required</span>');
        $(".gds_item_hsn").show();

        $(".notesHSN").remove();
        $("#notesModalBody").append(
          '<p class="notesHSN font-monospace text-danger">HSN is required</p>'
        );
      } else {
        $(".gds_item_hsn").remove();

        $(".notesHSN").remove();
        validStatus++;
      }

      // ITEM DESCRIPTION VALIDATION
      if ($(".item_desc").val() == "") {
        $(".gds_item_desc").remove();
        $(".item_desc")
          .parent()
          .append(
            '<span class="error gds_item_desc">Item description is required</span>'
          );
        $(".gds_item_desc").show();

        $(".notesItemDesc").remove();
        $("#notesModalBody").append(
          '<p class="notesItemDesc font-monospace text-danger">Item description is required</p>'
        );
      } else {
        $(".gds_item_desc").remove();

        $(".notesItemDesc").remove();
        validStatus++;
      }

      // PRICE VALIDATION
      if ($(".price").val() == "") {
        $(".gds_price").remove();
        $(".price")
          .parent()
          .append('<span class="error gds_price">Price is required</span>');
        $(".gds_price").show();

        $(".notesPrice").remove();
        $("#notesModalBody").append(
          '<p class="notesPrice font-monospace text-danger">Price is required</p>'
        );
      } else {
        $(".gds_price").remove();

        $(".notesPrice").remove();
        validStatus++;
      }

      // DISCOUNT VALIDATION
      if ($(".discount").val() == "") {
        $(".gds_discount").remove();
        $(".discount")
          .parent()
          .append(
            '<span class="error gds_discount">Discount is required</span>'
          );
        $(".gds_discount").show();

        $(".notesDiscount").remove();
        $("#notesModalBody").append(
          '<p class="notesDiscount font-monospace text-danger">Discount is required</p>'
        );
      } else {
        $(".gds_discount").remove();

        $(".notesDiscount").remove();
        validStatus++;
      }

          if (validStatus !== 9) {
        e.preventDefault();
        $("#exampleModal").modal("show");
     
      }

    } 
    else if (dataAttrVal == "Service Sales") {
    

      // PRICE VALIDATION

      if ($(".service_target_price").val() == "") {
        $(".gds_price").remove();
        $(".service_target_price") 
          .parent()
          .append('<span class="error gds_price">Price is required</span>');
        $(".gds_price").show();

        $(".notesPrice").remove();
        $("#notesModalBody").append(
          '<p class="notesPrice font-monospace text-danger">Price is required</p>'
        );
      } else {
        $(".gds_price").remove();

        $(".notesPrice").remove();
        validStatus++;
      }

    
      // ITEM NAME VALIDATION
      if ($(".service_name").val() == "") {
        $(".gds_service_name").remove();
        $(".service_name")
          .parent()
          .append(
            '<span class="error gds_service_name">Service name is required</span>'
          );
        $(".gds_service_name").show();

        $(".notesServiceName").remove();
        $("#notesModalBody").append(
          '<p class="notesServiceName font-monospace text-danger">Service name is required</p>'
        );
      } else {
        $(".gds_service_name").remove();

        $(".notesServiceName").remove();
        validStatus++;
      }

      // ITEM DESCRIPTION VALIDATION
      if ($(".service_desc").val() == "") {
        $(".gds_service_desc").remove();
        $(".service_desc")
          .parent()
          .append(
            '<span class="error gds_service_desc">Service description is required</span>'
          );
        $(".gds_service_desc").show();

        $(".notesServiceDesc").remove();
        $("#notesModalBody").append(
          '<p class="notesServiceDesc font-monospace text-danger">Service description is required</p>'
        );
      } else {
        $(".gds_service_desc").remove();

        $(".notesServiceDesc").remove();
        validStatus++;
      }

      // SERVICE UNIT VALIDATION
      if ($("#serviceUnitDrop").val() == ""  || $("#serviceUnitDrop").val() == "Service Unit of Measurement") {
       // alert(1);
        $(".gds_service_unit").remove();
        $("#serviceUnitDrop")
          .parent()
          .append(
            '<span class="error gds_service_unit">Service unit is required</span>'
          );
        $(".gds_service_unit").show();

        $(".notesServiceUnit").remove();
        $("#notesModalBody").append(
          '<p class="notesServiceUnit font-monospace text-danger">Service unit is required</p>'
        );
      } else {
        $(".gds_service_unit").remove();

        $(".notesServiceUnit").remove();
        validStatus++;
      }


      
      if (validStatus !== 4) {
        e.preventDefault();
        $("#exampleModal").modal("show");
      }
    } 
    else if (dataAttrVal == "Service Purchase") {
 
    
      // ITEM NAME VALIDATION
      if ($(".service_name").val() == "") {
        $(".gds_service_name").remove();
        $(".service_name")
          .parent()
          .append(
            '<span class="error gds_service_name">Service name is required</span>'
          );
        $(".gds_service_name").show();

        $(".notesServiceName").remove();
        $("#notesModalBody").append(
          '<p class="notesServiceName font-monospace text-danger">Service name is required</p>'
        );
      } else {
        $(".gds_service_name").remove();

        $(".notesServiceName").remove();
        validStatus++;
      }

      // ITEM DESCRIPTION VALIDATION
      if ($(".service_desc").val() == "") {
        $(".gds_service_desc").remove();
        $(".service_desc")
          .parent()
          .append(
            '<span class="error gds_service_desc">Service description is required</span>'
          );
        $(".gds_service_desc").show();

        $(".notesServiceDesc").remove();
        $("#notesModalBody").append(
          '<p class="notesServiceDesc font-monospace text-danger">Service description is required</p>'
        );
      } else {
        $(".gds_service_desc").remove();

        $(".notesServiceDesc").remove();
        validStatus++;
      }

   

      // SERVICE UNIT VALIDATION
    
      if ($("#serviceUnitDrop").val() == ""  || $("#serviceUnitDrop").val() == "Service Unit of Measurement") {
        $(".gds_serviceUnitDrop").remove();
        $("#serviceUnitDrop")
          .parent()
          .append(
            '<span class="error gds_serviceUnitDrop">Service Unit is required</span>'
          );
        $(".gds_serviceUnitDrop").show();

        $(".notesserviceUnitDrop").remove();
        $("#notesModalBody").append(
          '<p class="notesserviceUnitDrop font-monospace text-danger">Service Unit is required</p>'
        );
      } else {
        $(".gds_serviceUnitDrop").remove();

        $(".notesserviceUnitDrop").remove();
        validStatus++;
      }



      if (validStatus !== 3) {
        e.preventDefault();
        $("#exampleModal").modal("show");
      }
    }
     else if (dataAttrVal == "Asset") {
     



      // ITEM NAME VALIDATION
      if ($(".item_name").val() == "") {
        $(".gds_item_name").remove();
        $(".item_name")
          .parent()
          .append(
            '<span class="error gds_item_name">Item name is required</span>'
          );
        $(".gds_item_name").show();

        $(".notesItemName").remove();
        $("#notesModalBody").append(
          '<p class="notesItemName font-monospace text-danger">Item name is required</p>'
        );
      } else {
        $(".gds_item_name").remove();

        $(".notesItemName").remove();
        validStatus++;
      }

    

      // BASE UNIT OF MEASURE VALIDATION
      if ($("#buomDrop").val() == "") {
        $(".gds_buom").remove();
        $("#buomDrop")
          .parent()
          .append(
            '<span class="error gds_buom" style="position: relative;"">Base Unit of Measure is required</span>'
          );
        $(".gds_buom").show();

        $(".notesBoumDrop").remove();
        $("#notesModalBody").append(
          '<p class="notesBoumDrop font-monospace text-danger">Base Unit of Measure is required</p>'
        );
      } else {
        $(".gds_buom").remove();

        $(".notesBoumDrop").remove();
        validStatus++;
      }

      // ISSUE UNIT OF MEASURE VALIDATION
      if ($("#iuomDrop").val() == "") {
        $(".gds_iuom").remove();
        $("#iuomDrop")
          .parent()
          .append(
            '<span class="error gds_iuom" style="position: relative;">Issue Unit of Measure is required</span>'
          );
        $(".gds_iuom").show();

        $(".notesIuomDrop").remove();
        $("#notesModalBody").append(
          '<p class="notesIuomDrop font-monospace text-danger">Issue Unit of Measure is required</p>'
        );
      } else {
        $(".gds_iuom").remove();

        $(".notesIuomDrop").remove();
        validStatus++;
      }

      // ITEM REL VALIDATION
      if ($(".item_rel").val() == "") {
        $(".gds_item_rel").remove();
        $(".item_rel")
          .parent()
          .append(
            '<span class="error gds_item_rel" style="position: relative;">Conversion Rate between two UOM is required.</span>'
          );
        $(".gds_item_rel").show();

        $(".notesItemRel").remove();
        $("#notesModalBody").append(
          '<p class="notesItemRel font-monospace text-danger">Conversion Rate between two UOM is required.</p>'
        );
      } else {
        $(".gds_item_rel").remove();

        $(".notesItemRel").remove();
        validStatus++;
      }

     

      // ITEM DESCRIPTION VALIDATION
      if ($(".item_desc").val() == "") {
        $(".gds_item_desc").remove();
        $(".item_desc")
          .parent()
          .append(
            '<span class="error gds_item_desc">Item description is required</span>'
          );
        $(".gds_item_desc").show();

        $(".notesItemDesc").remove();
        $("#notesModalBody").append(
          '<p class="notesItemDesc font-monospace text-danger">Item description is required</p>'
        );
      } else {
        $(".gds_item_desc").remove();

        $(".notesItemDesc").remove();
        validStatus++;
      }


      if (validStatus !== 5) {
        e.preventDefault();
        $("#exampleModal").modal("show");
      }

     
    }
  });
});
