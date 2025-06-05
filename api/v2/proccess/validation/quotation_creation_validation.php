<?php
  $companyCountry = $_SESSION["logedBranchAdminInfo"]["companyCountry"]; 
?>
<script>
function taxGenerate(customerGstinCode) {
	//alert(1);
	let country_id = '<?php echo $companyCountry ?>';

	let branchGstinCode = $('#branchGstin').val();
	//  alert(branchGstinCode);

	$.ajax({
		type: 'GET',
		url: `ajaxs/so/ajax-generate-tax.php`,
		data: {
			act: 'getTaxComponent',
			country_id: country_id,
			branchGstinCode: branchGstinCode,
			customerGstinCode: customerGstinCode,
		},
		beforeSend: function () {},
		success: function (response) {
			$('.gst').remove();
			$(".totalCal:contains('Cash Discount')")
				.closest('tr')
				.after(response);
        

			setTimeout(() => {
				console.log('Tax generated');
				// Ensure tax calculation starts after 3 seconds
				window.calculateGrandTotalAmount1();
			}, 1000);
		},
	});
}
</script>
<script>
  // js validator for email,name and phone 

  function validateInputs(name, email, phone) {
    const nameRegex = /^[A-Za-z\s]+$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    // const phoneRegex = /^(\+?\d{1,2}\s?)?(\(?\d{3}\)?[\s-]?)?[\d\s-]{7,10}$/;
    const phoneRegex = /^[1-9]\d{9}$/
    let validationMessage = "";
    if (!nameRegex.test(name)) {
      validationMessage += "Invalid name. name should contain only alphabets.<br>";
    }
    if (!emailRegex.test(email)) {
      validationMessage += "Invalid email. enter a valid email address.<br>";
    }
    if (!phoneRegex.test(phone)) {
      validationMessage += "Invalid number. enter a valid phone number.<br>";
    }
    return validationMessage;
  }



  // Function to update a query parameter in the URL
  function updateQueryParam(paramName, paramValue) {
    // Get the current URL
    var currentUrl = new URL(window.location.href);

    // Set the new parameter value
    currentUrl.searchParams.set(paramName, paramValue);

    // Update the URL in the browser
    window.history.replaceState({}, '', currentUrl);
  }

  $("#profitCenterDropDown").on("change", function() {
    let functionalArea = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 1;
    console.log(functionalArea, 'functionalArea');
    $.ajax({
      type: "POST",
      url: `ajaxs/so/ajax-generate-inv-number.php`,
      data: {
        act: "getVerientExamplecopy",
        functionalArea: functionalArea
      },
      beforeSend: function() {
        // $("#itemsDropDown").html(`Loading...`);
      },
      success: function(response) {
        let data = JSON.parse(response);
        console.log('data');
        console.log(data);
        $("#iv_varient").val(data['id']);
        $(".ivnumberexample").html(data['iv_number_example']);
      }
    });
  });

  $("#iv_varient").on("change", function() {
    let vid = $(this).val();
    let functionalArea = $("#profitCenterDropDown").val();

    $.ajax({
      type: "POST",
      url: `ajaxs/so/ajax-generate-inv-number.php`,
      data: {
        act: "getVerientExamplecopy",
        functionalArea: functionalArea,
        vid: vid
      },
      beforeSend: function() {
        // $("#itemsDropDown").html(`Loading...`);
      },
      success: function(response) {
        let data = JSON.parse(response);
        $(".ivnumberexample").html(data['iv_number_example']);
      }
    });
  });


  $(document).on("click", ".dlt-popup", function() {
    $(this).parent().parent().remove();
  });

  function rm() {
    // $(event.target).closest("tr").remove();
    $(this).parent().parent().parent().remove();
  }

  function addOtherCost(id) {
    let addressRandNo = Math.ceil(Math.random() * 100000);
    $(`.modal-add-row_${id}`).append(`<div class="row othe-cost-infor">
          <div class="col-lg-5 col-md-12 col-sm-12">
              <div class="form-input">
                  <label for="">Services</label>
                  <select name="otherCostDetails[${addressRandNo}][services]" class="selct-vendor-dropdown" id="servicesDropDown_${addressRandNo}">
                    <option value="">Select One</option>
                      <?php foreach ($serviceList as $service) { ?>
                        <option value="<?= $service["itemId"] ?>_<?= $service["itemCode"] ?>_<?= $service["itemName"] ?>_<?= $service["service_unit"] ?>"><?= $service['itemName'] ?><small>(<?= $service['itemCode'] ?>)[<?= $service['goodsType'] ?>]</small></option>
                      <?php } ?>
                  </select>
              </div>
          </div>
          <div class="col-lg-5 col-md-12 col-sm-12">
              <div class="form-input">
                  <label for="">Qty</label>
                  <input step="any" type="number" class="form-control" placeholder="Qty" name="otherCostDetails[${addressRandNo}][qty]">
              </div>
          </div>
          <div class="col-lg-2 col-md-6 col-sm-6">
              <div class="add-btn-minus">
                  <a style="cursor: pointer" class="btn btn-danger">
                      <i class="fa fa-minus"></i>
                  </a>
              </div>
          </div>
      </div>`);

    $(`#servicesDropDown_${addressRandNo}`)
      .select2()
      .on('select2:open', () => {
        // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
      });


  }






  $(document).on("click", ".add-btn-minus", function() {
    $(this).parent().parent().remove();
  });

  function addMultiQty(id) {
    let addressRandNo = Math.ceil(Math.random() * 100000);
    //$(`.modal-add-row_${id}`).append(`<tr><td><span class='has-float-label'><input type='date' name='listItem[${id}][deliverySchedule][${addressRandNo}][multiDeliveryDate]' class='form-control' placeholder='delivery date' required><label>Delivery date</label></span></td><td><span class='has-float-label'><input type='text' name='listItem[${id}][deliverySchedule][${addressRandNo}][quantity]' class='form-control multiQuantity' data-itemid="${id}" id='multiQuantity_${addressRandNo}' placeholder='quantity' required><label>quantity</label></span></td><td><a class='btn btn-danger' onclick='rm()'><i class='fa fa-minus'></i></a></td></tr>`);
    $(`.modal-add-row_${id}`).append(`
      <div class="modal-add-row">
        <div class="row modal-cog-right">
          <div class="col-lg-5 col-md-5 col-sm-5">
              <div class="form-input">
                  <label>Delivery Date</label>
                  <input type="date" name="listItem[${id}][deliverySchedule][${addressRandNo}][multiDeliveryDate]" class="form-control multiDeliveryDate" id="multiDeliveryDate_${id}" placeholder="delivery date" value="<?= $_GET['deliveryDate'] ?>">
              </div>
          </div>
          <div class="col-lg-5 col-md-5 col-sm-5">
              <div class="form-input">
                  <label>Quantity</label>
                  <input type="text" name="listItem[${id}][deliverySchedule][${addressRandNo}][quantity]" class="form-control multiQuantity" data-itemid="${id}" id="multiQuantity_${id}" placeholder="quantity" value="0">
              </div>
          </div>
          <div class="col-lg-2 col-md-2 col-sm-2 dlt-popup">
              <a style="cursor: pointer" class="btn btn-danger">
                  <i class="fa fa-minus"></i>
              </a>
          </div>
        </div>
      </div>`);
  }
</script>

<script>
  $(document).ready(function() {
    // 👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀
    $(document).on('click', '.go', function() {
      let the_value = $('input[name=radioBtn]:radio:checked').val();
      let address_id = $('input[name=radioBtn]:radio:checked').data('addid');
      let stateCode = $('input[name=radioBtn]:radio:checked').data('statecode');
      // alert(stateCode);

      $(".address-change-modal").toggle();
      $("html").css({
        "overflow": "auto"
      });
      $("#shipTo").html(the_value);
      $("#placeOfSupply1").val(stateCode).trigger("change");
      $("#shippingAddressInp").val(the_value);
      $("#shipping_address_id").val(address_id);
      $('input.billToCheckbox').prop('checked', false);
    });

    $('#fob').on('change', function() {
      if ($('#fob').is(':checked')) {
        $('#fobCheckbox').val('checked');
      } else {
        $('#fobCheckbox').val('unchecked');
      }
    });

    loadItems();
    // loadCustomers();
    var customer__ID = '<?= $customerId ?>';
    // **************************************
    function loadItems() {
      // alert();
      let value = $('#goodsType').val();
      let searchUrl = window.location.search;

      goodsType = (value != null && value != undefined) ? value : (searchUrl === "?create_service_invoice" ? 'service' : 'material');

      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-items-goods-type.php`,
        beforeSend: function() {
          $("#itemsDropDown").html(`<option value="">Loading...</option>`);
        },
        data: {
          act: "goodsType",
          goodsType: goodsType
        },
        success: function(response) {
          $("#itemsDropDown").html(response);
        }
      });
    };

    $("#fob").on("click", function() {
      // alert();
      if ($('#fob').is(':checked')) {
        $("#otherCostCard").show();
      } else {
        $("#otherCostCard").hide();
      }
    });

    $("#soDate").on("change", function() {
      let soDate = $(this).val();
      $(".multiDeliveryDate").val(soDate);
    })

    // add customers
    $("#addCustomerBtn").on("click", function(e) {
      e.preventDefault();
      let customerName = $("#customerName").val();
      let customerEmail = $("#customerEmail").val();
      let customerPhone = $("#customerPhone").val();
      if (customerPhone != "") {
        $.ajax({
          type: "POST",
          url: `ajaxs/so/ajax-customers.php`,
          data: {
            act: "addCustomer",
            customerName,
            customerEmail,
            customerPhone
          },
          beforeSend: function() {
            $("#addCustomerBtn").prop('disabled', true);
            $("#addCustomerBtn").text(`Adding...`);
          },
          success: function(response) {
            let data = JSON.parse(response);

            $("#customerDropDown").html(response);
            if (data.status === "success") {
              $("#customerName").val("");
              $("#customerEmail").val("");
              $("#customerPhone").val("");
              $("#addCustomerBtn").text(`Add`);
              $("#addCustomerBtn").prop('disabled', false);
              $("#addCustomerBtn").text(`Add`);
              $("#addCustomerCloseBtn").trigger("click");
              // loadCustomers();
            }
          }
        });
      } else {
        $("#customerPhoneMsg").html(`<span class="text-xs text-danger">Phone number is required</span>`);
      }
    });

    // customers ********************************
    function loadCustomers() {
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-customers.php`,
        data: {
          customerId: '<?= $customerId ?>'
        },
        beforeSend: function() {
          $("#customerDropDown").html(`<option value="">Loading... </option>`);
        },
        success: function(response) {
          $("#customerDropDown").html(response);
          console.log('response');
          console.log(response);
        }
      });
    }

    function addCustomerFunc(customerId) {
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-customers-address.php`,
        data: {
          act: "customerAddress",
          customerId
        },
        beforeSend: function() {
          $("#shipTo").html(`Loading...`);
        },
        success: function(response) {
          let data = JSON.parse(response);
          $("#shipTo").html(data.data);
        }
      });

      $(".customerIdInp").val(customerId);
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-customers-list.php`,
        data: {
          act: "listItem",
          customerId
        },
        beforeSend: function() {
          $("#customerInfo").html(`<option value="">Loading...</option>`);
        },
        success: function(response) {
          $("#customerInfo").html(response);
          let creditPeriod = $("#spanCreditPeriod").text();
          $("#inputCreditPeriod").val(creditPeriod);

          let customerGstinCode = $(".customerGstinCode").val();
          let branchGstinCode = $(".branchGstin").val();
          if (customerGstinCode === branchGstinCode) {
            $(".igstTr").hide();
            $(".cgstTr").show();
            $(".sgstTr").show();
          } else {
            $(".igstTr").show();
            $(".cgstTr").hide();
            $(".sgstTr").hide();
          }
        }
      });
    }

    addCustomerFunc('<?= $customerId; ?>');

    // get customer details by id
    $("#customerDropDown").on("change", function() {
      let customerId = $(this).val();

      if (customerId > 0) {
        $(document).on("click", ".billToCheckbox", function() {
          if ($('input.billToCheckbox').is(':checked')) {
            // $(".shipTo").html(`checked ${customerId}`);
            addCustomerFunc(customerId);
          } else {
            $(".changeAddress").click();
            // $("#shipTo").html(`unchecked ${customerId}`);
          }
        });
        $(".customerIdInp").val(customerId);
        customerDetailsInfo(customerId);
      }

      $(".deliveryScheduleModal").each(function() {
        //  alert(1);
        var target = $(this).attr('id');

        //  alert(target);
        if (target) {

          var rowNo = target.substring(target.lastIndexOf('_') + 1); // Extract randCode
          //alert(rowNo);
          let item_id = $(`#itemId_${rowNo}`).val();
          // alert(item_id);
          let customer_id = $('#customerDropDown').val();
          let days = $('#inputCreditPeriod').val();
          //  alert(days);
          $(`.discountView_${rowNo}`).empty();
          $(`#itemDiscount_${rowNo}`).val(0);
          $(`#itemTotalDiscount_${rowNo}`).val(0);
          $(`#itemTotalDiscount1_${rowNo}`).val(0);
          calculateOneItemAmounts(rowNo);
          if (isNaN(days) || days <= 0) {
            alert('Please enter a valid positive integer for credit period');

          } else {
            discount_varients(rowNo, customer_id, item_id, days);
          }


          $.ajax({
            type: "GET",
            url: `ajaxs/mrp/ajax-mrp.php`,
            data: {

              customer_id: customer_id,
              item_id: item_id,

            },
            beforeSend: function() {
              //   alert(1);
            },
            success: function(response) {
              // alert(response);
              console.log(response);
              $(`#originalItemUnitPriceInp_${rowNo}`).val(response);
              $(`#originalChangeItemUnitPriceInp_${rowNo}`).val(response);
              calculateOneItemAmounts(rowNo);

            }
          });

        } else {
          console.log('not ok');
        }
      });
    });

    customerDetailsInfo(customer__ID);



 function customerDetailsInfo(customerId) {
      let searchUrl = window.location.search;
      let param = searchUrl.split("=")[0];
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-customers-list.php`,
        data: {
          act: "listItem",
          customerId
        },
        beforeSend: function() {
          $("#customerInfo").html(`<option value="">Loading...</option>`);
        },
        success: function(response) {
          $("#customerInfo").html(response);
          let creditPeriod = $("#spanCreditPeriod").text();
          $("#inputCreditPeriod").val(creditPeriod);

          let stateCodeSpanElement = $(".stateCodeSpan");
          let stateCodeSpan = stateCodeSpanElement.length > 0 ? stateCodeSpanElement.text().trim() : null;
          $("#placeOfSupply1").val(stateCodeSpan).trigger("change");

          let customerGstinCode = $(".customerGstinCode").val();
          let branchGstinCode = $(".branchGstin").val();
          <?php if ($invoiceType == "edit_so" || $invoiceType2=="quotation_to_so") { ?>
             taxGenerate(stateCodeSpan);
            <?php } ?>
          if (customerGstinCode !== "") {
            console.log('customerGstinCode is not empty 🏁🏁🏁', customerGstinCode)
            if (customerGstinCode === branchGstinCode) {
              $(".igstTr").hide();
              $(".cgstTr").show();
              $(".sgstTr").show();
            } else {
              $(".igstTr").show();
              $(".cgstTr").hide();
              $(".sgstTr").hide();
            }
          } else {
            $(document).on("change", "#placeOfSupply1", function() {
              let placeOfSupply1 = $(this).val();
          
              let customerGstinCode = $(".customerGstinCode").val();
     
              taxGenerate(placeOfSupply1);
              
            });
            
          }
          if (param != "?repost_invoice") {
            // Second AJAX request
            $.ajax({
              url: "ajaxs/so/ajax-customers-invoice-log.php",
              type: "GET",
              data: {
                act: "customersInvoiceLog",
                customerId
              },
              success: function(response2) {
                // console.log(response2);
                let data2 = JSON.parse(response2);
                if (data2.status == "success") {
                  let profit_center = data2.data.profit_center;
                  let kamId = data2.data.kamId;
                  let complianceInvoiceType = data2.data.complianceInvoiceType;
                  let placeOfSupply = data2.data.placeOfSupply;
                  let invoiceNoFormate = data2.data.invoiceNoFormate;
                  let bank = data2.data.bank;

                  $("#profitCenterDropDown").val(profit_center).trigger("change");
                  $("#compInvoiceType").val(complianceInvoiceType).trigger("change");
                  $("#kamDropDown").val(kamId).trigger("change");
                  $("#bankId").val(bank).trigger("change");
                  // $("#placeOfSupply1").val(placeOfSupply).trigger("change");
                  $("#iv_varient").val(invoiceNoFormate).trigger("change");
                } else {
                  console.log('somthing went wrong');
                  $("#profitCenterDropDown").val('').trigger("change");
                  // $("#compInvoiceType").val('R').trigger("change");
                  $("#kamDropDown").val('').trigger("change");
                  $("#bankId").val('').trigger("change");
                  // $("#placeOfSupply1").val('').trigger("change");
                  $("#iv_varient").val('').trigger("change");
                }
              },
              error: function(xhr, status, error) {
                console.log("Error 2:", error);
              }
            });
          }
        }
      });
    }

    $(document).on("click", "#pills-home-tab", function() {
      $("#saveChanges").html('<button type="button" class="btn btn-primary go">Go</button>');
    });
    $(document).on("click", "#pills-profile-tab", function() {
      $("#saveChanges").html('<button type="button" class="btn btn-primary" id="save">Save</button>');
    });

    // // AddressEdit
    // $(document).on("click", ".billAddressEdit", function() {
    //   let addressId = ($(this).attr("id")).split("_")[1];
    //   $(`.changeAddress`).click();
    //   $(`.newaddress`).click();
    //   alert(addressId)
    // });

    // $(document).on("click", ".shipAddressEdit", function() {
    //   let addressId = ($(this).attr("id")).split("_")[1];
    //   $(`.changeAddress`).click();
    //   $(`.newaddress`).click();
    //   alert(addressId)
    // });

    // subscription
    $("#makeRecurring").on("click", function() {
      if ($(this).is(":checked")) {
        $("#recurringModal").modal("show");
      } else {
        $("#recurringModal").modal("hide");

        $("#repeatEveryDropDown").val('');
        $("#startOn").val('');
        $("#endOn").val('');
        $("#neverExpire").val('');
      }
    });
    $(".subscriptionClose").on('click', function() {
      $("#recurringModal").modal("hide");
    });

    // handleConfig function to fetch and update options
    function handleConfig() {
      // console.log('handleConfig');
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-config-invoice.php`,
        beforeSend: function() {
          $("#config").html(`<option value="">Loading...</option>`);
        },
        data: {
          act: "getContact"
        },
        success: function(response) {
          let data = JSON.parse(response);
          // console.log(data);
          if (data.status == "success") {
            // console.log(data.data);

            // Get the select element
            let selectElement = document.getElementById('config');

            // Clear existing options
            selectElement.innerHTML = '';

            // First not selected 
            let optEl = document.createElement('option');
            optEl.value = "";
            optEl.textContent = "Select One";
            selectElement.appendChild(optEl);

            // Create and add new options based on data
            data.data.forEach(option => {
              let optionElement = document.createElement('option');
              optionElement.value = option.config_id;
              optionElement.textContent = option.email;
              selectElement.appendChild(optionElement);
            });
          }
        }
      });
    }

    // $('#configDropdown').on('change', function() {
    //     handleConfig();
    // });


    // handleConfigSave
    function handleConfigSave() {
      let configName = $("#configName").val();
      let configEmail = $("#configEmail").val();
      let configPhone = $("#configPhone").val();
      console.log(configName, configEmail, configPhone);

      if (configEmail == "" || configPhone == "" || configName == "") {
        swal.fire({
          icon: `error`,
          title: `Note`,
          text: `Please fill all the fields`
        })
        return;
      }

      const validationMessage = validateInputs(configName, configEmail, configPhone);
      if (validationMessage) {
        swal.fire({
          icon: `warning`,
          title: `Validation Failed!`,
          html: `${validationMessage}`
        });
        return;
      }

      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-config-invoice.php`,
        data: {
          act: "handleConfigSave",
          configName,
          configEmail,
          configPhone
        },
        success: function(response) {
          let data = JSON.parse(response);
          console.log(data);
          if (data.status == "success") {
            swal.fire({
              icon: `success`,
              title: `Note`,
              text: `${data.message}`
            });

            $("#configEmail").val("");
            $("#configName").val("");
            $("#configPhone").val("");
            $('#handleConfigClose').click();
            handleConfig();
          } else {
            swal.fire({
              icon: `error`,
              title: `Note`,
              text: `${data.message}`
            })
          }
        },
        error: function(xhr, status, error) {
          console.log("Error:", error);
        }
      });
    }
    // handleConfigSave function
    $(document).on("click", "#handleConfigSave", function() {
      handleConfigSave();
    })

    // submit address form
    $(document).on('click', '#save', function() {
      let customerId = $('.customerIdInp').val();
      let recipientName = $("#recipientName").val();
      let billingNo = $("#billingNo").val();
      let flatNo = $("#flatNo").val();
      let streetName = $("#streetName").val();
      let location = $("#location").val();
      let city = $("#city").val();
      let pinCode = $("#pinCode").val();
      let district = $("#district").val();
      let state = $("#state").val();
      let stateCode = $("#stateCode").val();

      if (billingNo != '') {
        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-customers-address.php`,
          data: {
            act: "shipAddressSave",
            customerId,
            recipientName,
            billingNo,
            flatNo,
            streetName,
            location,
            city,
            pinCode,
            district,
            state,
            stateCode
          },
          beforeSend: function() {
            $(`#save`).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          },

          success: function(response) {
            let data = JSON.parse(response);
            console.log(data);
            $(".address-change-modal").hide();
            $(".modal-backdrop").hide();
            $("#shipTo").html(data.data);
            $("#shipToLastInsertedId").val(data.lastInsertedId);
            $('input.billToCheckbox').prop('checked', false);
            $(`#save`).html('Save');

            // input value null
            $("#recipientName").val('');
            $("#billingNo").val('');
            $("#flatNo").val('');
            $("#streetName").val('');
            $("#location").val('');
            $("#city").val('');
            $("#pinCode").val('');
            $("#district").val('');
            $("#state").val('');
            $("#stateCode").val('');
            $(`#pills-home-tab`).click();
            $(`.closeButton`).click();
          }
        });
      } else {
        alert(`All fields are required`);
      }
    });

    // get item details by id
    function itemAutoAdd(itemIdArry) {

      let customer_id = $('#customerDropDown').val();
      let url = window.location.search;
      let param = url.split("=")[0];

      let othersdata = '<?= $pgiCode ?>';


      let companyCurrencyName = '<?= $currencyName ?>';
      let currencyName = ($('.currencyDropdown').val()).split("≊")[2];

      // to toggle FOB
      if (param === "?sales_order_creation" || param === "?quotation_to_so" || param === "?party_order_to_so" || param === "?party_order_to_quotation" || param === "?proforma_to_so") {
        $(".fob-section").show();


      } else {
        $(".fob-section").hide();
        $(".tc-section").show();


      }

      if (itemIdArry != '') {
        const sanitizedJSONData = itemIdArry.replace(/[\x00-\x1F\x7F-\x9F]/g, '');
        var itemIdArryTo = JSON.parse(sanitizedJSONData);
        var invoicedate = $("#invoiceDate").val();
        var compInvoiceType = $("#compInvoiceType").val();
        $.each(itemIdArryTo, function(index, value) {
          if (value.inventory_item_id > 0) {
            $.ajax({
              type: "GET",
              url: `ajaxs/so/ajax-items-list-direct-new-rachhel.php`,
              data: {
                act: "listItem",
                itemId: value.inventory_item_id,
                type: param,
                othersdata: othersdata,
                invoicedate: invoicedate,
                customer_id: customer_id,
                compInvoiceType: compInvoiceType,
                items: value
              },
              beforeSend: function() {
                $(`#spanItemsTable`).html(`Loading...`);
              },
              success: function(response) {
                //  alert(response);
                // console.log(response)

                $("#itemsTable").append(response);
                calculateGrandTotalAmount();
                $(`#spanItemsTable`).html(``);

                if (companyCurrencyName !== currencyName) {
                  $(`.convertedDiv`).show();
                } else {
                  $(`.convertedDiv`).hide();
                }
              }
            });
          }
        });
      }
    }

    itemAutoAdd('<?= $itemIdJson; ?>');
    // get item details by id
    $("#itemsDropDown").on("change", function() {
      let itemId = $(this).val();
      let url = window.location.search;
      let param = url.split("=")[0];
      // dynamic value
      let customer_id = $('#customerDropDown').val();
      const currentURL = window.location.href;
      const ccurl = new URL(currentURL);
      const searchParams = new URLSearchParams(ccurl.search);
      const searchValue = searchParams.get(param.substring(1));

      let companyCurrencyName = '<?= $currencyName ?>';
      let currencyName = ($('.currencyDropdown').val()).split("≊")[2];

      var invoicedate = $("#invoiceDate").val();
      var compInvoiceType = $("#compInvoiceType").val();
      if (itemId > 0) {
        let deliveryDate = $('#deliveryDate').val();
        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-items-list-direct-new-rachhel.php`,
          data: {
            act: "listItem",
            type: param,
            valueId: searchValue,
            invoicedate: invoicedate,
            compInvoiceType: compInvoiceType,
            customer_id: customer_id,
            itemId
          },
          beforeSend: function() {
            $(`#spanItemsTable`).html(`Loading...`);
          },
          success: function(response) {

            //alert(response);


            console.log(response);

            $("#itemsTable").append(response);
            calculateGrandTotalAmount();
            $(`#spanItemsTable`).html(``);
            currency_conversion();

            let deliveryDate = $("#deliveryDate").val();
            $(".multiDeliveryDate").val(deliveryDate);

            if (companyCurrencyName !== currencyName) {
              $(`.convertedDiv`).show();
            } else {
              $(`.convertedDiv`).hide();
            }

            // gstin validation
            let branchGstin = $("#branchGstin").val("");
            if (branchGstin === "") {
              for (elem of $(".itemTotalTax")) {
                let rowNo = ($(elem).attr("id")).split("_")[1];

                $(`#itemTax_${rowNo}`).val(0);
                $(`#itemTaxPercentage_${rowNo}`).html(0);

                calculateOneItemAmounts(rowNo);
              };
              calculateGrandTotalAmount();
            }

            $('#itemsDropDown option:first').prop('selected', true);
          }

        });
      }
    });


    $(document).ready(function() {
      // Event delegation to handle modal opening
      $(document).on('show.bs.modal', '.deliveryScheduleModal', function() {
        // Display alert when modal is opened
        var modal = $(this); // Reference to the current modal
        var target = $(this).attr('id'); // Get the data-target attribute value
        // alert(target);


        if (target) {

          var rowNo = target.substring(target.lastIndexOf('_') + 1); // Extract randCode
          // alert(rowNo);
          let item_id = $(`#itemId_${rowNo}`).val();
          // alert(item_id);
          let customer_id = $('#customerDropDown').val();
          let days = $('#inputCreditPeriod').val();
          //  alert(days);
          $(`.discountView_${rowNo}`).empty();

          if (isNaN(days) || days <= 0) {
            alert('Please enter a valid positive integer for credit period');

          } else {
            discount_varients(rowNo, customer_id, item_id, days);
          }

        } else {
          console.log('not ok');
        }
      });





    });

    $('#inputCreditPeriod').keyup(function() {
      // alert(1);

      $(".deliveryScheduleModal").each(function() {
        var target = $(this).attr('id');
        if (target) {

          var rowNo = target.substring(target.lastIndexOf('_') + 1); // Extract randCode
          // alert(rowNo);
          let item_id = $(`#itemId_${rowNo}`).val();
          // alert(item_id);
          let customer_id = $('#customerDropDown').val();
          let days = $('#inputCreditPeriod').val();

          $(`.discountView_${rowNo}`).empty();
          $(`#itemDiscount_${rowNo}`).val(0);
          $(`#itemTotalDiscount_${rowNo}`).val(0);
          $(`#itemTotalDiscount1_${rowNo}`).val(0);
          calculateOneItemAmounts(rowNo);
          if (isNaN(days) || days <= 0) {
            alert('Please enter a valid positive integer for credit period');

          } else {
            discount_varients(rowNo, customer_id, item_id, days);
          }
        } else {
          console.log('not ok');
        }

      });

    });



    function discount_varients(rowNo, customer_id, item_id, days) {
      //console.log(days);
      let itemVal = $(`#itemBaseAmountInp_${rowNo}`).val();
      // alert(itemVal);

      let previousDocDiscountVariantId = $(`#previousDiscountVarientId_${rowNo}`).val();


      let qty = $(`#itemQty_${rowNo}`).val();

      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-item-discount-rachhel.php`,
        data: {
          customer_id: customer_id,
          item_id: item_id,
          days: days,
          qty: qty,
          value: itemVal
        },
        beforeSend: function() {
          //$(`#spanItemsTable`).html(`Loading...`);
        },
        success: function(response) {

          console.log("Item discount data:", response);
          //  alert(rowNo);
          //   console.log(Array.isArray(JSON.parse(response)));
          var obj = JSON.parse(response);


          $.each(obj, function(index, item) {

            var rand = Math.ceil(Math.random() * 100000);

            console.log({
              previousDocDiscountVariantId
            })

            let selectedDiscountDivNote = item.discount_variant_id == previousDocDiscountVariantId ? `<p class="pre-normal text-warning">In the previous document, this discount was selected!</p>` : ``;

            if (item.discount_type == 'value') {
              // console.log(item.discount_type);
              var div = `<div class="discount-card">
                            <input type="radio" class="discount_radio" name="discount" id="discount_radio` + rand + `" data-attr="` + rand + `" value="` + item.discount_value + `">
                            <p><b>Rs.` + item.discount_value + ` off </b> </p>
                            <p>`
              if (item.minimum_value != 0 && item.minimum_value != null) {
                div += ` on minimum purchase value of<b>Rs.` + item.minimum_value + `</b> `
              }
              if (item.minimum_value != null && item.minimum_qty != null) {

                div += `` + item.condition;
              }
              if (item.minimum_qty != 0 && item.minimum_qty != null) {
                div += ` minimum quantity of <b>` + item.minimum_qty + `</b> `
              }
              div += `</p><div class='d-flex justify-content-between validity-days'>
                              <div class='form-inline'>
                                <label>Valid from</label>
                                <p>` + item.valid_from + `</p>
                              </div>
                              <div class='form-inline'>
                                <label>Valid upto</label>
                                <p>` + item.valid_upto + `</p>
                              </div>
                            </div>`

              if (item.term_of_payment != null && item.term_of_payment != 0) {
                div += `<div class='form-input'>
                             <label>Terms of Payment</label>
                             <p>` + item.term_of_payment + ` Days</p>
                          </div>`;
              }
              div += `</div>
                          <input type="hidden" id="discount_variant_id_${rand}" name="discount_variant_id" value="` + item.discount_variant_id + `">
                          <input type="hidden" id="percentage_val_${rand}" name="percentage_val" value="` + item.discount_value + `">
                          <input type="hidden" id="discount_type_${rand}" name="discount_type" value="` + item.discount_type + `">
                          <input type="hidden" id="max_val_${rand}" name="max_val" value="` + item.discount_max_value + `">
                          <input type="hidden" id="min_val_${rand}" name="min_val" value="` + item.minimum_value + `">
                          <input type="hidden" id="min_qty_${rand}" name="min_qty" value="` + item.minimum_qty + `">
                          <input type="hidden" id=base_price_${rand}" name="base_price" value="` + itemVal + `">
                        `;
            } else {
              var div = `
                          <div class="discount-card">
                            <input type="radio" class="discount_radio" name="discount" id="discount_radio" data-attr = "` + rand + `" value="` + item.discount_percentage + `">
                            <p><b>` + item.discount_percentage + ` % off </b>`;

              if (item.discount_max_value != 0 && item.discount_max_value != null) {
                div += `upto Rs.` + item.discount_max_value;
              }

              if (item.minimum_value != 0 && item.minimum_value != null) {
                div += ` on minimum purchase value <b>Rs.` + item.minimum_value + `</b>`;
              }
              if (item.minimum_value != null && item.minimum_qty != null) {
                div += `` + item.condition;
              }
              if (item.minimum_qty != 0 && item.minimum_qty != null) {
                div += ` minimum quantity of <b>` + item.minimum_qty + `</b>`;
              }

              div += `<div class='d-flex justify-content-between validity-days'>
                              <div class='form-inline'>
                                <label>Valid from</label>
                                <p>` + item.valid_from + `</p>
                              </div>
                              <div class='form-inline'>
                                <label>Valid upto</label>
                                <p>` + item.valid_upto + `</p>
                              </div>
                            </div>`;

              if (item.term_of_payment != null && item.term_of_payment != 0) {
                div += `<div class='form-input'>
                             <label>Terms of Payment</label>
                             <p>` + item.term_of_payment + ` Days</p>
                          </div>`;
              }


              div += `${selectedDiscountDivNote}
                        </div>
                          <input type="hidden" id="discount_variant_id_${rand}" name="discount_variant_id" value="` + item.discount_variant_id + `">
                          <input type="hidden" id="percentage_val_${rand}" name="percentage_val" value="` + item.discount_percentage + `">
                          <input type="hidden" id="discount_type_${rand}" name="discount_type" value="` + item.discount_type + `">
                          <input type="hidden" id="max_val_${rand}" name="max_val" value="` + item.discount_max_value + `">
                          <input type="hidden" id="min_val_${rand}" name="min_val" value="` + item.minimum_value + `">
                          <input type="hidden" id="min_qty_${rand}" name="min_qty" value="` + item.minimum_qty + `">
                          <input type="hidden" id="base_price_${rand}" name="base_price" value="` + itemVal + `">
                          `;
            }


            $(`.discountView_${rowNo}`).append(div);

            // Here you can perform any action you want with the current element
          });
        }
      });

      $(`.discountView_${rowNo}`).on('click', '.discount_radio', function() {
        // alert(this.value); // Show value of the clicked radio button
        var random = $(this).data('attr');
        //  alert(random);
        console.log({
          random,
          rowNo
        })
        var percentage_val = $(`#percentage_val_${random}`).val();
        //alert(percentage_val);
        var discount_type = $(`#discount_type_${random}`).val();
        var discount_varient_id = $(`#discount_variant_id_${random}`).val();
        console.log({
          discount_varient_id
        });
        //alert(discount_type);
        var max_val = $(`#max_val_${random}`).val();
        // alert(max_val);
        var base_price = itemVal;
        // alert(base_price);

        let itemQty = parseFloat($(`#itemQty_${rowNo}`).val()) > 0 ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
        let itemCashDiscountAmount = parseFloat($(`#itemTotalCashDiscount_${rowNo}`).text()) || 0;
        let originalChangeItemUnitPriceInp = parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) || 0;
        let basicPrice = originalChangeItemUnitPriceInp * itemQty;

        let item_id = $(`#itemId_${rowNo}`).val();
        $(`#itemTradeDiscountPercentageInp_${rowNo}`).val(`${percentage_val}`);
        $(`#itemTradeDiscountPercentageSpan_${rowNo}`).text(`${percentage_val}`);

        if (discount_type == 'percentage') {
          let itemQty = $(`#itemQty_${rowNo}`).val();

          var percentage_amount = (percentage_val / 100) * base_price;
          let itemGrossAmount = (itemQty * originalChangeItemUnitPriceInp) - percentage_amount;
          $(`#itemGrossAmountSpan_${rowNo}`).text(`${itemGrossAmount.toFixed(2)}`);

          $(`#itemGrossAmountSpan_${rowNo}`).text(parseFloat(basicPrice) - parseFloat(percentage_amount));
          $(`#itemTaxableAmountSpan_${rowNo}`).text(parseFloat(basicPrice) - parseFloat(percentage_amount) - parseFloat(itemCashDiscountAmount));
          if (max_val > 0 && max_val !== null) {
            if (percentage_amount > max_val) {

              var new_base_price = base_price - max_val;
              $(`#itemTotalDiscount1_${rowNo}`).val(max_val);
              $(`#itemDiscount_${rowNo}`).val(percentage_val);
              $(`#itemDiscountVarientId_${rowNo}`).val(discount_varient_id);
              $(`#itemTotalDiscountHidden_${rowNo}`).val(max_val);
              $(`#itemTotalDiscount_${rowNo}`).html(max_val);
              var dis = $(`#itemTotalDiscount1_${rowNo}`).val();
              $(`#itemTradeDiscountAmountInp_${rowNo}`).val(max_val);
              $(`#itemTradeDiscountAmountSpan_${rowNo}`).text(max_val);
              var percentage = parseFloat($(`#itemDiscount_${rowNo}`).val()).toFixed(2);
              $(`#itemTradeDiscountPercentageSpan_${rowNo}`).text(`${percentage}`)

              calculateOneItemAmounts(rowNo);
            }

          } else {
            // alert(1);
            var new_base_price = base_price - percentage_amount;
            $(`#itemTotalDiscount1_${rowNo}`).val(percentage_amount);
            $(`#itemDiscount_${rowNo}`).val(percentage_val);
            $(`#itemDiscountVarientId_${rowNo}`).val(discount_varient_id);
            $(`#itemTotalDiscountHidden_${rowNo}`).val(percentage_amount);
            $(`#itemTotalDiscount_${rowNo}`).html(percentage_amount);

            $(`#itemTradeDiscountAmountInp_${rowNo}`).val(percentage_amount);
            $(`#itemTradeDiscountAmountSpan_${rowNo}`).text(percentage_amount);
            var percentage = parseFloat($(`#itemDiscount_${rowNo}`).val()).toFixed(2);
            $(`#itemTradeDiscountPercentageSpan_${rowNo}`).text(`${percentage}`)

            calculateOneItemAmounts(rowNo);
          }

        } else {
          var percent = (percentage_val * 100) / base_price
          $(`#itemTotalDiscount1_${rowNo}`).val(percentage_val);
          $(`#itemDiscount_${rowNo}`).val(percent);
          $(`#itemDiscountVarientId_${rowNo}`).val(discount_varient_id);
          $(`#itemTotalDiscountHidden_${rowNo}`).val(percentage_val);
          $(`#itemTotalDiscount_${rowNo}`).html(percentage_val);
          $(`#itemTradeDiscountAmountInp_${rowNo}`).val(percentage_val);
          // $(`#itemTradeDiscountAmountInp_${rowNo}`).val(percentage_amount);
          $(`#itemTradeDiscountAmountSpan_${rowNo}`).text(percentage_amount);
          var percentage = parseFloat($(`#itemDiscount_${rowNo}`).val()).toFixed(2);
          $(`#itemTradeDiscountPercentageSpan_${rowNo}`).text(`${percentage}`);
          $(`#itemTradeDiscountName_${rowNo}`).val("cash");
          calculateOneItemAmounts(rowNo);
          // calculateQuantity(rowNo, item_id, percentage_val);
          // calculateGrandTotalAmount();
        }

        calculateQuantity(rowNo, item_id, percentage_val);
        calculateOneItemAmounts(rowNo);
        calculateGrandTotalAmount();
      });
    }




    // roundoff function start 👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾 
    $("#round_off_hide").hide();
    $(document).on('change', '.checkbox', function() {
      if (this.checked) {
        $("#round_off_hide").show();
      } else {
        $("#round_off_hide").hide();
      }
    });

    function roundofftotal(total_value, sign, roudoff) {
      let final_value = 0;
      let tcsAmount = (parseFloat($('#tcs_amount').val()) > 0) ? parseFloat($('#tcs_amount').val()) : 0;

      if (sign === "+") {
        final_value = total_value + roudoff + tcsAmount;
      } else {
        final_value = total_value + tcsAmount - roudoff;
      }
      $(".adjustedDueAmt").html(final_value.toFixed(2));
      $(".adjustedCollectAmountInp").val(final_value.toFixed(2));
      $("#grandTotalAmtInp").html(final_value.toFixed(2));


    }

    $(document).on("change", "#round_sign", function() {
      let roundValue = (parseFloat($("#round_value").val()) > 0) ? parseFloat($("#round_value").val()) : 0;
      let total_value = (parseFloat($("#grandTotalAmtInp").val()) > 0) ? parseFloat($("#grandTotalAmtInp").val()) : 0;
      var sign = $('#round_sign').val();
      roundofftotal(total_value, sign, roundValue);
      $(".roundOffValueHidden").val(sign + roundValue);
    });

    $(document).on("keyup", "#round_value", function() {
      let roundValue = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      let total_value = (parseFloat($("#grandTotalAmtInp").val()) > 0) ? parseFloat($("#grandTotalAmtInp").val()) : 0;
      var sign = $('#round_sign').val();
      roundofftotal(total_value, sign, roundValue);
      $(".roundOffValueHidden").val(sign + roundValue);
      calculateGrandTotalAmount();
    });
    // roundoff function end 👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾

    // tcs function section start

    function tcsTotal(total_value, tcsAmount) {
      let final_value = 0;
      final_value = total_value + tcsAmount;
      console.log("final_value" + final_value)
      $("#grandTotalAmt").html(final_value.toFixed(2));
      $('.tcsValueInp').val(tcsAmount.toFixed(2))
      // $(".grandTotalAmounttInp").html(final_value.toFixed(2));
      $(".adjustedCollectAmountInp").val(final_value.toFixed(2));
      $("#grandTotalAmtInp").html(final_value.toFixed(2));


    }

    $(document).on("keyup", "#tcs_amount", function() {
      let tcsAmount = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      let total_value = (parseFloat($("#grandTotalAmtInp").val()) > 0) ? parseFloat($("#grandTotalAmtInp").val()) : 0;
      tcsTotal(total_value, tcsAmount);
      $(".roundOffValueHidden").val(sign + roundValue);
      calculateGrandTotalAmount();
    });
    // tcs function section end

    $(document).on("click", ".delItemBtn", function() {
      // let id = ($(this).attr("id")).split("_")[1];
      // $(`#delItemRowBtn_${id}`).remove();
      $(this).parent().parent().remove();
      calculateGrandTotalAmount();
    });

    $(document).on('submit', '#addNewItemForm', function(event) {
      event.preventDefault();
      let formData = $("#addNewItemsForm").serialize();
      $.ajax({
        type: "POST",
        url: `ajaxs/so/ajax-items.php`,
        data: formData,
        beforeSend: function() {
          $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
          $("#addNewItemsFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');
        },
        success: function(response) {
          $("#goodTypeDropDown").html(response);
          $('#addNewItemsForm').trigger("reset");
          $("#addNewItemsFormModal").modal('toggle');
          $("#addNewItemsFormSubmitBtn").html("Submit");
          $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
        }
      });
    });

    $(document).on("keyup change", ".qty", function() {
      let id = $(this).val();
      var sls = $(this).attr("sls");
      alert(sls);
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-items-list.php`,
        data: {
          act: "totalPrice",
          itemId: "ss",
          id
        },
        beforeSend: function() {
          $(".totalPrice").html(`<option value="">Loading...</option>`);
        },
        success: function(response) {
          console.log(response);
          $(".totalPrice").html(response);
        }
      });
    });

    // #######################################################
    $(document).on("click", ".toggleServiceRemarksPen", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      $(`#itemRemarks_${rowNo}`).toggle();
    });

    // -- generated by chatGPT 🤖🤖🤖 || imranali59059 || original code is above 👆🏾
    //// one item calculation 
    // function calculateOneItemAmounts(rowNo) {
    //   let param = window.location.search.split("=")[0];
    //   let baseAmountIsCheck = $(`#baseAmountRadio_${rowNo}`).is(":checked");
    //   console.log('window.location.search.split("=")[0]', param);

    //   //alert(rowNo);totalItemAmountModal
    //   let itemQty = parseFloat($(`#itemQty_${rowNo}`).val()) || 0;
    //   let originalItemUnitPrice = parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) || 0;
    //   let convertedItemUnitPrice = parseFloat($(`#convertedItemUnitPriceSpan_${rowNo}`).text()) || 0;
    //   let itemDiscount = parseFloat($(`#itemDiscount_${rowNo}`).val()) || 0;
    //   let itemCashDiscount = parseFloat($(`#itemCashDiscount_${rowNo}`).val()) || 0;
    //   let convertedCurrencyValue = parseFloat($(`#curr_rate`).val()) || 0;

    //   let itemTradeDiscountAmount = parseFloat($(`#itemTradeDiscountAmountSpan_${rowNo}`).text()) || 0;
    //   let itemCashDiscountAmount = parseFloat($(`#itemCashDiscountAmountSpan_${rowNo}`).text()) || 0;
    //   let itemGrossAmount = parseFloat($(`#itemGrossAmountSpan_${rowNo}`).text()) || 0;
    //   let itemTaxableAmount = parseFloat($(`#itemTaxableAmountSpan_${rowNo}`).text()) || 0;
    //   let itemTradeDiscountPercentageSpan = parseFloat($(`#itemTradeDiscountPercentageSpan_${rowNo}`).text()) || 0;
    //   let itemCashDiscountPercentageSpan = parseFloat($(`#itemCashDiscountPercentageSpan_${rowNo}`).text()) || 0;

    //   let itemTradeDiscountAmountInp = parseFloat($(`#itemTradeDiscountAmountInp_${rowNo}`).val()) || 0;
    //   let itemCashDiscountAmountInp = parseFloat($(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val()) || 0;

    //   let convertedTradeDiscountAmount = itemTradeDiscountAmountInp * convertedCurrencyValue;
    //   let convertedGrossAmount = itemGrossAmount * convertedCurrencyValue;
    //   let convertedCashDiscountAmount = itemCashDiscountAmountInp * convertedCurrencyValue;
    //   let convertedTaxableAmount = itemTaxableAmount * convertedCurrencyValue;

    //   $(`#convertedItemTradeDiscountAmountSpan_${rowNo}`).text(convertedTradeDiscountAmount.toFixed(2));
    //   $(`#convertedGrossAmountSpan_${rowNo}`).text(convertedGrossAmount.toFixed(2));
    //   $(`#convertedCashDiscountAmountSpan_${rowNo}`).text(convertedCashDiscountAmount.toFixed(2));
    //   $(`#convertedTaxableAmountSpan_${rowNo}`).text(convertedTaxableAmount.toFixed(2));

    //   //alert(itemDiscount);
    //   let itemTax = parseFloat($(`#itemTax_${rowNo}`).val()) || 0;
    //   //alert(itemTax);

    //   $(`#multiQuantity_${rowNo}`).val(itemQty);

    //   let basicPrice = itemQty * originalItemUnitPrice;
    //   let convertedBasicPrice = convertedItemUnitPrice * itemQty;

    //   let totalDiscount = parseFloat($(`#itemTotalDiscount1_${rowNo}`).val()) || 0;
    //   let convertedTotalDiscount = convertedBasicPrice * itemDiscount / 100;
    //   let convertedTotalCashDiscount = convertedBasicPrice * itemCashDiscount / 100;
    //   let grossAmount = basicPrice - (parseFloat(basicPrice) * itemTradeDiscountPercentageSpan / 100);
    //   let cashDiscountAmount = (basicPrice * itemCashDiscountPercentageSpan) / 100;

    //   // let priceWithDiscount = basicPrice - totalDiscount;
    //   // let priceWithDiscount = (basicPrice - itemGrossAmount) - itemCashDiscountAmount;

    //   let priceWithDiscount = itemTaxableAmount;

    //   let convertedPriceWithDiscount = convertedBasicPrice - convertedTotalDiscount;
    //   let taxableAmount = grossAmount.toFixed(2) - cashDiscountAmount.toFixed(2);
    //   let totalTax = taxableAmount * itemTax / 100;
    //   //alert(totalTax);
    //   let convertedTotalTax = convertedPriceWithDiscount * itemTax / 100;

    //   let totalItemPrice = taxableAmount + totalTax;
    //   //alert(totalItemPrice);
    //   let convertedTotalItemPrice = convertedPriceWithDiscount + convertedTotalTax;

    //   console.log(`itemGrossAmountSpan =>>`, basicPrice, `-`, itemTradeDiscountAmount)

    //   console.log({
    //     itemQty,
    //     originalItemUnitPrice,
    //     convertedItemUnitPrice,
    //     itemDiscount,
    //     itemCashDiscount,
    //     convertedCurrencyValue,
    //     itemTradeDiscountAmount,
    //     itemCashDiscountAmount,
    //     itemGrossAmount,
    //     itemTaxableAmount,
    //     itemTradeDiscountPercentageSpan,
    //     itemCashDiscountPercentageSpan,
    //     basicPrice,
    //     totalDiscount,
    //     convertedTotalDiscount,
    //     grossAmount,
    //     cashDiscountAmount,
    //     priceWithDiscount,
    //     convertedPriceWithDiscount,
    //     totalTax,
    //   });




    //   // $(`#itemGrossAmountSpan_${rowNo}`).text(grossAmount.toFixed(2));
    //   $(`#itemGrossAmountSpan_${rowNo}`).text(basicPrice.toFixed(2) - totalDiscount.toFixed(2));
    //   $(`#itemGrossAmountInCashDiscount_${rowNo}`).text(basicPrice.toFixed(2) - totalDiscount.toFixed(2));
    //   // $(`#itemGrossAmountInCashDiscount_${rowNo}`).html(parseFloat(basicPrice) - (parseFloat(basicPrice) * itemTradeDiscountPercentageSpan / 100));
    //   $(`#grossAmountRadio_${rowNo}`).val(parseFloat(basicPrice) - parseFloat(itemTradeDiscountAmount));
    //   // $(`#itemTaxableAmountSpan_${rowNo}`).text(parseFloat(basicPrice) - parseFloat(itemTradeDiscountAmount) - parseFloat(itemCashDiscountAmount));
    //   $(`#itemTaxableAmountSpan_${rowNo}`).text(grossAmount.toFixed(2) - cashDiscountAmount.toFixed(2));    

    //   $(`#itemBaseAmountInp_${rowNo}`).val(basicPrice.toFixed(2));
    //   $(`#itemBaseAmountInCashDiscount_${rowNo}`).html(basicPrice.toFixed(2));
    //   $(`#baseAmountRadio_${rowNo}`).val(basicPrice.toFixed(2));
    //   $(`#itemBaseAmountSpan_${rowNo}`).text(basicPrice.toFixed(2));
    //   $(`#convertedItemBaseAmountSpan_${rowNo}`).text(convertedBasicPrice.toFixed(2));

    //   $(`#itemTotalDiscountHidden_${rowNo}`).val(totalDiscount.toFixed(2));
    //   $(`#itemTotalDiscount1_${rowNo}`).val(totalDiscount.toFixed(2));

    //   if(param === "?so_to_invoice"){
    //     $(`#itemTradeDiscountAmountSpan_${rowNo}`).text((basicPrice * itemTradeDiscountPercentageSpan) / 100);
    //   }else{
    //     $(`#itemTradeDiscountAmountSpan_${rowNo}`).text(totalDiscount.toFixed(2));
    //   }        

    //   if (baseAmountIsCheck) {
    //     $(`#itemCashDiscountAmountSpan_${rowNo}`).text(cashDiscountAmount);
    //     $(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val(cashDiscountAmount);
    //   }else{
    //     $(`#itemCashDiscountAmountSpan_${rowNo}`).text((grossAmount * itemCashDiscountPercentageSpan) / 100);
    //     $(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val((grossAmount * itemCashDiscountPercentageSpan) / 100);
    //   }

    //   $(`#itemTradeDiscountAmountInp_${rowNo}`).val((basicPrice * itemTradeDiscountPercentageSpan) / 100);

    //   $(`#itemTotalDiscount_${rowNo}`).html(totalDiscount.toFixed(2));
    //   $(`#convertedItemDiscountAmountSpan_${rowNo}`).html(convertedTotalDiscount.toFixed(2));
    //   $(`#convertedItemCashDiscountAmountSpan_${rowNo}`).html(convertedTotalCashDiscount.toFixed(2));

    //   $(`#itemTotalTax1_${rowNo}`).val(totalTax.toFixed(2));
    //   $(`#itemTotalTax_${rowNo}`).html(totalTax.toFixed(2));
    //   $(`#convertedItemTaxAmountSpan_${rowNo}`).html(convertedTotalTax.toFixed(2));

    //   $(`#itemTotalPrice_${rowNo}`).val(totalItemPrice.toFixed(2));
    //   $(`#itemTotalPrice1_${rowNo}`).html(totalItemPrice.toFixed(2));
    //   $(`#convertedItemTotalPriceSpan_${rowNo}`).html(convertedTotalItemPrice.toFixed(2));

    //   $(`#totalItemAmountModal_${rowNo}`).html(totalItemPrice.toFixed(2));

    //   calculateGrandTotalAmount();
    //   roundOffCal();
    // }

    // one item calculation 
    function calculateOneItemAmounts(rowNo) {
      let param = window.location.search.split("=")[0];
      let baseAmountIsCheck = $(`#baseAmountRadio_${rowNo}`).is(":checked");
      console.log('window.location.search.split("=")[0]', param);

      //alert(rowNo);totalItemAmountModal
      let itemQty = parseFloat($(`#itemQty_${rowNo}`).val()) || 0;
      let originalItemUnitPrice = parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) || 0;
      let convertedItemUnitPrice = parseFloat($(`#convertedItemUnitPriceSpan_${rowNo}`).text()) || 0;
      let itemDiscount = parseFloat($(`#itemDiscount_${rowNo}`).val()) || 0;
      let itemDiscountVarientId = parseFloat($(`#itemDiscountVarientId_${rowNo}`).val()) || 0;
      let itemCashDiscount = parseFloat($(`#itemCashDiscount_${rowNo}`).val()) || 0;
      let convertedCurrencyValue = parseFloat($(`#curr_rate`).val()) || 0;

      let basicPrice = itemQty * originalItemUnitPrice;

      let itemTradeDiscountAmount = parseFloat($(`#itemTradeDiscountAmountSpan_${rowNo}`).text()) || 0;
      let itemCashDiscountAmount = parseFloat($(`#itemCashDiscountAmountSpan_${rowNo}`).text()) || 0;
      let itemGrossAmount = parseFloat($(`#itemGrossAmountSpan_${rowNo}`).text()) || 0;
      let itemTradeDiscountPercentageSpan = parseFloat($(`#itemTradeDiscountPercentageSpan_${rowNo}`).text()) || 0;
      let itemCashDiscountPercentageSpan = parseFloat($(`#itemCashDiscountPercentageSpan_${rowNo}`).text()) || 0;

      let itemTradeDiscountAmountInp = parseFloat($(`#itemTradeDiscountAmountInp_${rowNo}`).val()) || 0;
      let itemCashDiscountAmountInp = parseFloat($(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val()) || 0;

      let grossAmount = basicPrice - (parseFloat(basicPrice) * itemTradeDiscountPercentageSpan / 100);

    // console(grossAmount);
      // let grossAmount=itemGrossAmount;  

      let itemTradeDiscountName=$(`#itemTradeDiscountName_${rowNo}`).val();
      if(itemTradeDiscountName=='cash'){
        // alert(1);
        grossAmount = basicPrice - itemTradeDiscountAmount;
    
      }
      else{
      //  alert(0);
        grossAmount = basicPrice - (parseFloat(basicPrice) * itemTradeDiscountPercentageSpan / 100);
      }

      let itemTaxableAmount = parseFloat((grossAmount - itemCashDiscountAmount).toFixed(2)) || 0;

      let convertedTradeDiscountAmount = itemTradeDiscountAmountInp * convertedCurrencyValue;
      let convertedGrossAmount = itemGrossAmount * convertedCurrencyValue;
      let convertedCashDiscountAmount = itemCashDiscountAmountInp * convertedCurrencyValue;
      let convertedTaxableAmount = itemTaxableAmount * convertedCurrencyValue;

      $(`#convertedItemTradeDiscountAmountSpan_${rowNo}`).text(convertedTradeDiscountAmount.toFixed(2));
      $(`#convertedGrossAmountSpan_${rowNo}`).text(convertedGrossAmount.toFixed(2));
      $(`#convertedCashDiscountAmountSpan_${rowNo}`).text(convertedCashDiscountAmount.toFixed(2));
      $(`#convertedTaxableAmountSpan_${rowNo}`).text(convertedTaxableAmount.toFixed(2));

      //alert(itemDiscount);
      let itemTax = parseFloat($(`#itemTax_${rowNo}`).val()) || 0;
      //alert(itemTax);

      $(`#multiQuantity_${rowNo}`).val(itemQty);

      let convertedBasicPrice = convertedItemUnitPrice * itemQty;

      let totalDiscount = parseFloat($(`#itemTotalDiscount1_${rowNo}`).val()) || 0;
      let convertedTotalDiscount = convertedBasicPrice * itemDiscount / 100;
      let convertedTotalCashDiscount = convertedBasicPrice * itemCashDiscount / 100;

      // let cashDiscountAmount = (basicPrice * itemCashDiscountPercentageSpan) / 100;
      let cashDiscountAmount = itemCashDiscountAmount;

      // let priceWithDiscount = basicPrice - totalDiscount;
      // let priceWithDiscount = (basicPrice - itemGrossAmount) - itemCashDiscountAmount;

      let priceWithDiscount = itemTaxableAmount;

      let convertedPriceWithDiscount = convertedBasicPrice - convertedTotalDiscount;
      let taxableAmount = grossAmount.toFixed(2) - cashDiscountAmount.toFixed(2);
      let totalTax = taxableAmount * itemTax / 100;
      //alert(totalTax);
      let convertedTotalTax = convertedPriceWithDiscount * itemTax / 100;

      let totalItemPrice = taxableAmount + totalTax;
      //alert(totalItemPrice);
      let convertedTotalItemPrice = convertedPriceWithDiscount + convertedTotalTax;

      console.log(`itemGrossAmountSpan =>>`, basicPrice, `-`, itemTradeDiscountAmount)

      console.log({
        itemQty,
        originalItemUnitPrice,
        convertedItemUnitPrice,
        itemDiscount,
        itemCashDiscount,
        convertedCurrencyValue,
        itemTradeDiscountAmount,
        itemCashDiscountAmount,
        itemGrossAmount,
        itemTaxableAmount,
        itemTradeDiscountPercentageSpan,
        itemCashDiscountPercentageSpan,
        basicPrice,
        totalDiscount,
        convertedTotalDiscount,
        grossAmount,
        cashDiscountAmount,
        priceWithDiscount,
        convertedPriceWithDiscount,
        totalTax,
      });




      // $(`#itemGrossAmountSpan_${rowNo}`).text(grossAmount.toFixed(2));
      $(`#itemGrossAmountSpan_${rowNo}`).text(basicPrice.toFixed(2) - totalDiscount.toFixed(2));
      $(`#itemGrossAmountInCashDiscount_${rowNo}`).text(basicPrice.toFixed(2) - totalDiscount.toFixed(2));
      // $(`#itemGrossAmountInCashDiscount_${rowNo}`).html(parseFloat(basicPrice) - (parseFloat(basicPrice) * itemTradeDiscountPercentageSpan / 100));
      $(`#grossAmountRadio_${rowNo}`).val(parseFloat(basicPrice) - parseFloat(itemTradeDiscountAmount));
      // $(`#itemTaxableAmountSpan_${rowNo}`).text(parseFloat(basicPrice) - parseFloat(itemTradeDiscountAmount) - parseFloat(itemCashDiscountAmount));
      $(`#itemTaxableAmountSpan_${rowNo}`).text(grossAmount.toFixed(2) - cashDiscountAmount.toFixed(2));

      $(`#itemBaseAmountInp_${rowNo}`).val(basicPrice.toFixed(2));
      $(`#itemBaseAmountInCashDiscount_${rowNo}`).html(basicPrice.toFixed(2));
      $(`#baseAmountRadio_${rowNo}`).val(basicPrice.toFixed(2));
      $(`#itemBaseAmountSpan_${rowNo}`).text(basicPrice.toFixed(2));
      $(`#convertedItemBaseAmountSpan_${rowNo}`).text(convertedBasicPrice.toFixed(2));

      $(`#itemTotalDiscountHidden_${rowNo}`).val(totalDiscount.toFixed(2));
      $(`#itemTotalDiscount1_${rowNo}`).val(totalDiscount.toFixed(2));

      if (param === "?so_to_invoice") {
        $(`#itemTradeDiscountAmountSpan_${rowNo}`).text((basicPrice * itemTradeDiscountPercentageSpan) / 100);
      } else {
        $(`#itemTradeDiscountAmountSpan_${rowNo}`).text(totalDiscount.toFixed(2));
      }

      if (baseAmountIsCheck) {
        $(`#itemCashDiscountAmountSpan_${rowNo}`).text(cashDiscountAmount);
        $(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val(cashDiscountAmount);
      } else {
        $(`#itemCashDiscountAmountSpan_${rowNo}`).text((grossAmount * itemCashDiscountPercentageSpan) / 100);
        $(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val((grossAmount * itemCashDiscountPercentageSpan) / 100);
      }

      // $(`#itemTradeDiscountAmountInp_${rowNo}`).val((basicPrice * itemTradeDiscountPercentageSpan) / 100);

      $(`#itemTotalDiscount_${rowNo}`).html(totalDiscount.toFixed(2));
      $(`#convertedItemDiscountAmountSpan_${rowNo}`).html(convertedTotalDiscount.toFixed(2));
      $(`#convertedItemCashDiscountAmountSpan_${rowNo}`).html(convertedTotalCashDiscount.toFixed(2));

      $(`#itemTotalTax1_${rowNo}`).val(totalTax.toFixed(2));
      $(`#itemTotalTax_${rowNo}`).html(totalTax.toFixed(2));
      $(`#convertedItemTaxAmountSpan_${rowNo}`).html(convertedTotalTax.toFixed(2));

      $(`#itemTotalPrice_${rowNo}`).val(totalItemPrice.toFixed(2));
      $(`#itemTotalPrice1_${rowNo}`).html(totalItemPrice.toFixed(2));
      $(`#convertedItemTotalPriceSpan_${rowNo}`).html(convertedTotalItemPrice.toFixed(2));

      $(`#totalItemAmountModal_${rowNo}`).html(totalItemPrice.toFixed(2));

      calculateGrandTotalAmount();
      roundOffCal();
    }

    window.calculateGrandTotalAmount1 = function() {
    // Function code here
    calculateGrandTotalAmount();
    };
    // -- generated by chatGPT 🤖🤖🤖 || imranali59059 || original code is above 👆🏾
   function calculateGrandTotalAmount() {

      console.log('first')
      let totalAmount = 0;
      let totalAmountOriginal = 0;

      let totalTaxAmount = 0;
      let totalTaxAmountOriginal = 0;
      let convertedItemTaxAmountSpan = 0;

      let totalDiscountAmount = 0;
      let totalDiscountAmountOriginal = 0;
      let convertedItemDiscountAmountSpan = 0;

      let itemBaseAmountSpan = 0;
      let itemBaseAmountInpOriginal = 0;
      let convertedItemBaseAmountSpan = 0;
      let convertedItemTotalPrice = 0;
      let totalCashDiscountAmount = 0;
      let curr_rate = parseFloat($(`#curr_rate`).val()) || 0;
      let adjustedDueAmt = parseFloat($(`.adjustedDueAmt`).text()) || 0;
      $(`.convertedAdjustedDueAmt`).text(adjustedDueAmt * curr_rate);

      // item total price
      $(".itemTotalPrice1").each(function() {
        totalAmount += parseFloat($(this).text().replace(/,/g, "")) || 0;
      });
      // alert(totalAmount);
      $(".itemTotalPrice").each(function() {
        totalAmountOriginal += parseFloat($(this).val().replace(/,/g, "")) || 0;
      });
      // alert(totalAmountOriginal);
      $(".convertedItemTotalPriceSpan").each(function() {
        convertedItemTotalPrice += parseFloat($(this).text().replace(/,/g, "")) || 0;
      });
      // alert(convertedItemTotalPrice);

      // item total tax
      $(".itemTotalTax1").each(function() {
        totalTaxAmountOriginal += parseFloat($(this).val().replace(/,/g, "")) || 0;
      });
      //alert(totalTaxAmountOriginal);
      $(".itemTotalTax").each(function() {
        totalTaxAmount += parseFloat($(this).html().replace(/,/g, "")) || 0;

      });
      //  alert(totalTaxAmount);
      $(".convertedItemTaxAmountSpan").each(function() {
        convertedItemTaxAmountSpan += parseFloat($(this).html().replace(/,/g, "")) || 0;
      });

      // item total discount
      $(".itemTotalDiscountHidden").each(function() {
        totalDiscountAmountOriginal += parseFloat($(this).val().replace(/,/g, "")) || 0;
      });
      //  alert(totalDiscountAmountOriginal);
      // alert(totalDiscountAmountOriginal);
      // $(".itemTotalDiscount").each(function() {
      //   totalDiscountAmount += parseFloat($(this).html().replace(/,/g, "")) || 0;
      // });

      $(".itemTradeDiscountAmountInp").each(function() {
        totalDiscountAmount += parseFloat($(this).val().replace(/,/g, "")) || 0;
      });

      $(".itemCashDiscountAmountHiddenInp").each(function() {
        totalCashDiscountAmount += parseFloat($(this).val().replace(/,/g, "")) || 0;
      });

      //alert(totalDiscountAmount);
      //alert(totalDiscountAmount);
      $(".convertedItemDiscountAmountSpan").each(function() {
        convertedItemDiscountAmountSpan += parseFloat($(this).html().replace(/,/g, "")) || 0;
      });
      // alert(convertedItemDiscountAmountSpan)

      // item base amount
      $(".itemBaseAmountInp").each(function() {
        itemBaseAmountInpOriginal += parseFloat($(this).val().replace(/,/g, "")) || 0;
      });
      // alert(itemBaseAmountInpOriginal);
      $(".itemBaseAmountSpan").each(function() {
        itemBaseAmountSpan += parseFloat($(this).html().replace(/,/g, "")) || 0;
      });
      //  alert(itemBaseAmountSpan);
      $(".convertedItemBaseAmountSpan").each(function() {
        convertedItemBaseAmountSpan += parseFloat($(this).html().replace(/,/g, "")) || 0;
      });
      // alert(convertedItemBaseAmountSpan);

      let compInvoiceType = $("#compInvoiceType").val();
      // alert(compInvoiceType);
      let grandTotalAmountAfterOriginal = totalAmountOriginal - totalTaxAmount;
      let grandTotalAmountAfter = totalAmount - totalTaxAmount;
      //  alert(grandTotalAmountAfter);
      let convertedGrandTotalAmountWithoutTax = convertedItemTotalPrice - convertedItemTaxAmountSpan;

      let grandTotalCashDiscount = $("#grandTotalCashDiscount").text();
      let convertedGrandTotalCashDiscountAmount = grandTotalCashDiscount * curr_rate;


      if (compInvoiceType === "CBW" || compInvoiceType === "LUT" || compInvoiceType === "SEWOP" || compInvoiceType === "E") {
        //alert(111111111111);
        <?php if($companyCountry==103){ ?>
        $(".grandSgstCgstAmt").html(0);
        $(".convertedGrandSgstCgstAmt").html(0);

        $("#grandTaxAmt").html(0);
        $("#convertedGrandTaxAmount").html(0);

        $("#grandTaxAmtInp").val(0.00);
        
        var gstDetailsArray = []; // Array to hold all GST details

          $("tr.gst").each(function() {
              var gstType = $(this).find(".totalCal").text().trim(); // Gets the text "CGST" or "SGST"
              var taxPercentage = $(this).find("input[type='hidden']").val(); // Gets the tax percentage value
              
              var grandTaxAmtId = "#grandTaxAmt_" + gstType;
              var grandTaxAmtval = "#grandTaxAmtval_" + gstType;

              // Calculate the tax amount
              var taxAmount = (totalTaxAmount * taxPercentage / 100).toFixed(2);
              
              // Update the HTML and input fields
              $(grandTaxAmtId).html(taxAmount);
              $(grandTaxAmtval).val(taxAmount);
              // Add the GST details to the array
              gstDetailsArray.push({
                  gstType: gstType,
                  taxPercentage: taxPercentage,
                  taxAmount: taxAmount
              });
          });

          // Create a single JSON object with all GST details
          var gstDetailsJson = JSON.stringify(gstDetailsArray);

          // Pass the JSON to the input field with name 'gstdetails'
          $("input[name='gstdetails']").val(gstDetailsJson);
   
        $("#grandSubTotalAmt").html(itemBaseAmountSpan.toFixed(2));
        $("#grandSubTotalAmtInp").val(itemBaseAmountInpOriginal.toFixed(2));
        $("#convertedGrandSubTotalAmt").text(convertedItemBaseAmountSpan.toFixed(2));

        //$("#grandTotalDiscount").html(totalDiscountAmount.toFixed(2));
        $("#grandTotalDiscount").html(convertedItemDiscountAmountSpan.toFixed(2));
        $("#grandTotalDiscountAmtInp").val(totalDiscountAmountOriginal.toFixed(2));

        $("#grandTotalCashDiscount").html(totalCashDiscountAmount.toFixed(2));
        $("#grandTotalCashDiscountAmtInp").val(totalCashDiscountAmount.toFixed(2));

        $("#convertedGrandTotalDiscountAmount").text(convertedItemDiscountAmountSpan.toFixed(2));

        $("#grandTotalAmt").html(grandTotalAmountAfter.toFixed(2));
        $("#grandTotalAmtInp").val(grandTotalAmountAfter.toFixed(2));
        $("#convertedGrandTotalAmt").text(convertedGrandTotalAmountWithoutTax.toFixed(2));

          <?php } ?>
      } else {

          var gstDetailsArray = []; // Array to hold all GST details

          $("tr.gst").each(function() {
              var gstType = $(this).find(".totalCal").text().trim(); // Gets the text "CGST" or "SGST"
              var taxPercentage = $(this).find("input[type='hidden']").val(); // Gets the tax percentage value
              
              var grandTaxAmtId = "#grandTaxAmt_" + gstType;
              var grandTaxAmtval = "#grandTaxAmtval_" + gstType;

              // Calculate the tax amount
              var taxAmount = (totalTaxAmount * taxPercentage / 100).toFixed(2);
              
              // Update the HTML and input fields
              $(grandTaxAmtId).html(taxAmount);
              $(grandTaxAmtval).val(taxAmount);
              // Add the GST details to the array
              gstDetailsArray.push({
                  gstType: gstType,
                  taxPercentage: taxPercentage,
                  taxAmount: taxAmount
              });
          });

          // Create a single JSON object with all GST details
          var gstDetailsJson = JSON.stringify(gstDetailsArray);

          // Pass the JSON to the input field with name 'gstdetails'
          $("input[name='gstdetails']").val(gstDetailsJson);


        // alert(1);
        $(".convertedGrandSgstCgstAmt").html((convertedItemTaxAmountSpan / 2));
        // alert(2);

        // $("#grandTaxAmt").html(totalTaxAmount.toFixed(2));
        // alert(3);
        $("#convertedGrandTaxAmount").html(convertedItemTaxAmountSpan.toFixed(2));

        $("#convertedGrandTotalCashDiscountAmount").html(convertedGrandTotalCashDiscountAmount.toFixed(2));
        // alert(4);
        $("#grandTaxAmtInp").val(totalTaxAmountOriginal.toFixed(2));
        // alert(5);

        // $(".itemTaxPercentage").text(0);
        // $(".itemTotalTax").text(0);
        // $(".itemTotalTax1").val(0);
        // $(".convertedItemTaxAmountSpan").text(0);

        $("#grandSubTotalAmt").html(itemBaseAmountSpan.toFixed(2));
        // alert(6);
        $("#grandSubTotalAmtInp").val(itemBaseAmountInpOriginal.toFixed(2));
        // alert(7);
        $("#convertedGrandSubTotalAmt").text(convertedItemBaseAmountSpan.toFixed(2));
        // alert(8);

      //  $("#grandTotalDiscount").html(totalDiscountAmount.toFixed(2));

      $("#grandTotalDiscount").html(convertedItemDiscountAmountSpan.toFixed(2));

        $("#grandTotalDiscountAmtInp").val(totalDiscountAmountOriginal.toFixed(2));

        $("#grandTotalCashDiscount").html(totalCashDiscountAmount.toFixed(2));
        $("#grandTotalCashDiscountAmtInp").val(totalCashDiscountAmount.toFixed(2));

        // alert(10);
        $("#convertedGrandTotalDiscountAmount").text(convertedItemDiscountAmountSpan.toFixed(2));
        // alert(11);

        $("#grandTotalAmt").html(totalAmount.toFixed(2));
        $("#grandTotalAmtInp").val(totalAmountOriginal.toFixed(2));
        $("#convertedGrandTotalAmt").text(convertedItemTotalPrice.toFixed(2));
      }

    }

    // currency conversion
    function currency_conversion() {
  $(".convertedItemUnitPriceSpan").each(function () {
    let elem = $(this);
    let rowNo = elem.attr("id").split("_")[1];
    
    // Retrieve and convert inputs to numbers
    let currRate = parseFloat($("#curr_rate").val());
    let originalPrice = parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val());

    // Validate the inputs
    if (isNaN(currRate) || isNaN(originalPrice)) {
      console.error(`Invalid input for row ${rowNo}: currRate=${currRate}, originalPrice=${originalPrice}`);
      return; // Skip processing this element
    }

    // Calculate new value
    let newVal = currRate * originalPrice;

    // Ensure newVal is positive
    newVal = newVal > 0 ? newVal : parseFloat(elem.text()) || 0;

    // Update element text with formatted value
    elem.text(newVal.toFixed(2));

    // Recalculate dependent amounts
    calculateOneItemAmounts(rowNo);
  });

  // Update the currency symbol
  let currencyIcon = ($('.currencyDropdown').val() || "").split("≊")[2] || "";
  $(".currency-symbol-dynamic").text(currencyIcon);

  // Recalculate the grand total
  calculateGrandTotalAmount();
}


    // change dynamic currency 
    $(".currencyDropdown").on("change", function() {
      let currencyIcon = ($(this).val()).split("≊")[1];
      let currencyName = ($(this).val()).split("≊")[2];
      let companyCurrencyName = $('.companyCurrencyName').text();

      if (companyCurrencyName !== currencyName) {
        currency_conversion();
        $.ajax({
          url: `ajaxs/so/ajax-currency.php`,
          type: 'GET',
          data: {
            act: 'currencyPage',
            currency: companyCurrencyName,
            currencyName
          },
          beforeSend: function() {
            $("#curr_rate").val(`Loading...`);
            $("#curr_rate").prop('disabled', true);
          },
          success: function(result) {
            let data = JSON.parse(result);
            let rate = data.data.rate;
            $(".currency-symbol-dynamic").text(currencyName);
            $("#curr_rate").val(rate);
            $("#curr_rate").prop('disabled', false);
            currency_conversion();
          },
        });
        $(`.convertedDiv`).show();
      } else {
        $("#curr_rate").val(1);
        currency_conversion();
        $(`.convertedDiv`).hide();
      }
    });

    $(document).on("keyup keydown", "#curr_rate", function() {
      currency_conversion();
    });

    currency_conversion();

    // #######################################################
    function calculateQuantity(rowNo, itemId, thisVal) {
      let itemQty = (parseFloat($(`#itemQty_${itemId}`).val()) > 0) ? parseFloat($(`#itemQty_${itemId}`).val()) : 0;
      let totalQty = 0;
      $(".multiQuantity").each(function() {
        if ($(this).data("itemid") == itemId) {
          totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
        }
      });
      let avlQty = itemQty - totalQty;

      if (avlQty < 0) {
        let totalQty = 0;
        $(`#multiQuantity_${rowNo}`).val('');
        $(".multiQuantity").each(function() {
          if ($(this).data("itemid") == itemId) {
            totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
          }
        });
        let avlQty = itemQty - totalQty;

        $(`#mainQtymsg_${itemId}`).show();
        $(`#mainQtymsg_${itemId}`).html("[Error! Delivery QTY should equal to order QTY.]");
        $(`#mainQty_${itemId}`).html(avlQty);
      } else {
        let totalQty = 0;
        $(".multiQuantity").each(function() {
          if ($(this).data("itemid") == itemId) {
            totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
          }
        });

        let avlQty = itemQty - totalQty;

        $(`#mainQtymsg_${itemId}`).hide();
        $(`#mainQty_${itemId}`).html(avlQty);
      }
      if (avlQty == 0) {
        $(`#saveClose_${itemId}`).show();
        $(`#saveCloseLoading_${itemId}`).hide();
      } else {
        $(`#saveClose_${itemId}`).hide();
        $(`#saveCloseLoading_${itemId}`).show();
        $(`#setAvlQty_${itemId}`).html(avlQty);
      }
    }

    function itemMaxDiscount(rowNo, keyValue = 0) {

      let itemMaxDis = $(`#itemMaxDiscount_${rowNo}`).html();
      if (parseFloat(keyValue) > parseFloat(itemMaxDis)) {
        $(`#itemSpecialDiscount_${rowNo}`).text(`Special Discount`);
        $(`#itemSpecialDiscount_${rowNo}`).show();
        $(`#specialDiscount`).show();
      } else {
        $(`#itemSpecialDiscount_${rowNo}`).hide();
        $(`#specialDiscount`).hide();
      }
    }

    // item qty check
    $(document).on("keyup", ".itemQty", function() {



      let rowNo = ($(this).attr("id")).split("_")[1];
      console.log('rowNo', rowNo);

      $(`#itemDiscount_${rowNo}`).val(0);

      $(`#discount_variant_id_${rowNo}`).val(0);
      $(`#itemDiscountVarientId_${rowNo}`).val(0);

      $(`#itemTotalDiscount1_${rowNo}`).val(0);
      $(`#itemTotalDiscountHidden_${rowNo}`).val(0);

      $(`#itemTradeDiscountPercentageSpan_${rowNo}`).html(0);
      $(`#itemCashDiscountPercentageSpan_${rowNo}`).html(0);

      $(`#itemTradeDiscountAmountSpan_${rowNo}`).html(0);
      $(`#itemCashDiscountAmountSpan_${rowNo}`).html(0);

      $(`#itemTotalCashDiscountHidden_${rowNo}`).val(0);
      $(`#itemCashDiscount_${rowNo}`).val(0);
      $(`#itemTotalCashDiscount1_${rowNo}`).val(0);


      let itemVal = parseFloat($(`#itemQty_${rowNo}`).val()) > 0 ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
      let itemTradeDiscountAmount = parseFloat($(`#itemTradeDiscountAmountSpan_${rowNo}`).text()) || 0;
      let itemCashDiscountAmount = parseFloat($(`#itemCashDiscountAmountSpan_${rowNo}`).text()) || 0;
      let originalItemUnitPrice = parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) || 0;
      let basicPrice = originalItemUnitPrice * itemVal;



      // Check if the corresponding "checkQty_" element exists
      if ($(`#checkQty_${rowNo}`).length > 0) {
        let checkQty = parseFloat($(`#checkQty_${rowNo}`).val()) > 0 ? parseFloat($(`#checkQty_${rowNo}`).val()) : 0;


        // $(`#itemGrossAmountSpan_${rowNo}`).text(parseFloat(basicPrice) - parseFloat(itemTradeDiscountAmount));
        // $(`#itemTaxableAmountSpan_${rowNo}`).text(parseFloat(basicPrice) - parseFloat(itemTradeDiscountAmount) - parseFloat(itemCashDiscountAmount));

        // if (checkQty) {
        let splitQty = parseFloat($(`#checkQty_${rowNo}`).val()) > 0 ? parseFloat($(`#checkQty_${rowNo}`).val()) : 0;
        console.log('itemVal, checkQty', itemVal, checkQty, splitQty);
        // }
      } else {
        // $(`#itemGrossAmountSpan_${rowNo}`).text(parseFloat(basicPrice) - parseFloat(itemTradeDiscountAmount));
        // $(`#itemTaxableAmountSpan_${rowNo}`).text(parseFloat(basicPrice) - parseFloat(itemTradeDiscountAmount) - parseFloat(itemCashDiscountAmount));
      }
      calculateOneItemAmounts(rowNo);
    });



    // invoice date *****************************************
    $("#invoiceDate").on("change", function(e) {
      // dynamic value
      let url = window.location.search;
      let param = url.split("=")[0];

      var invoicedate = $(this).val();
      var rowData = {};
      let flag = 0;
      $(".itemRow").each(function() {

        let itemType = $(this).attr("goodsType");
        let goodsType = $(this).data('id');
        // alert(goodsType);
        if (goodsType != 5) {
          flag++;
        }
        let rowId = $(this).attr("id").split("_")[2];
        let itemId = $(this).attr("id").split("_")[1];
        rowData[rowId] = itemId;

        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-items-stock-list.php`,
          data: {
            act: "itemStock",
            invoiceDate: invoicedate,
            itemId: itemId,
            randCode: rowId
          },
          beforeSend: function() {
            // $(".tableDataBody").html(`<option value="">Loading...</option>`);
          },
          success: function(response) {
            $(`.customitemreleaseDiv${rowId}`).hide();
            $(`.customitemreleaseDiv${rowId}`).html(response);
          }
        });
      });

      StringRowData = JSON.stringify(rowData);
      if (param !== "?create_service_invoice") {
        if (flag > 0) {
          Swal.fire({
            icon: `warning`,
            title: `Note`,
            text: `Available stock has been recalculated`,
            // showCancelButton: true,
            // confirmButtonColor: '#3085d6',
            // cancelButtonColor: '#d33',
            // confirmButtonText: 'Confirm'
          });


          $.ajax({
            type: "POST",
            url: `ajaxs/so/ajax-items-stock-check.php`,
            data: {
              act: "itemStockCheck",
              invoicedate: invoicedate,
              rowData: StringRowData
            },
            beforeSend: function() {
              $(".tableDataBody").html(`<option value="">Loading...</option>`);
            },
            success: function(response) {
              let data = JSON.parse(response);
              let itemData = data.data;
              console.log(data);
              if (data.status === "success") {
                for (let key in itemData) {
                  if (itemData.hasOwnProperty(key)) {

                    $(`#itemQty_${key}`).val(0);
                    $(`#checkQty_${key}`).val(itemData[key]);
                    $(`#checkQtySpan_${key}`).html(itemData[key]);
                    $(`#fifo_${key}`).prop('checked', true);
                    $(`#itemSellType_${key}`).html('FIFO');
                    $(`.enterQty`).val('');
                  }
                }
              }
            }
          });
        }
      }
    });

    $(document).on("keyup blur", ".originalChangeItemUnitPriceInp", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      calculateOneItemAmounts(rowNo);
      currency_conversion();
    });

    <?php if($companyCountry==103){?>
    $("#compInvoiceType").on("change", function() {
      let compInvoiceType = $(this).val();
      // console.log(compInvoiceType);

      // For GST calculation----
      for (elem of $(".itemTotalTax")) {
        let rowNo = ($(elem).attr("id")).split("_")[1];

        let itemTaxBkup = $(`#itemTaxBkup_${rowNo}`).val();
        // alert(itemTaxBkup);

        if (compInvoiceType === "CBW" || compInvoiceType === "LUT" || compInvoiceType === "SEWOP" || compInvoiceType === "E") {
          $(`#itemTax_${rowNo}`).val(0);
          $(`#itemTaxPercentage_${rowNo}`).html(0);
        } else {

          $(`#itemTax_${rowNo}`).val(itemTaxBkup);
          $(`#itemTaxPercentage_${rowNo}`).html(itemTaxBkup);
        }

        calculateOneItemAmounts(rowNo);
      };

      calculateGrandTotalAmount();
    });
    <?php } ?>
    $("#directInvoiceCreationBtn").on("click", function(event) {
      // console.log(event);
      var isChecked = $('#round_off_checkbox').prop('checked');
      let isValidQuantity = true;
      let isValidunitPrice = true;

      $(".itemQty").each(function() {
        let qty = $(this).val();
        if (qty <= 0 || qty === "") {
          isValidQuantity = false;
          alert("Please enter valid quantity");
          return false; // exits the each loop
        }
      });

      $(".originalChangeItemUnitPriceInp").each(function() {
        let price = $(this).val();
        if (price <= 0 || price === "") {
          isValidunitPrice = false;
          alert("Please enter unit price");
          return false; // exits the each loop
        }
      });

      if (!isValidQuantity) {
        event.preventDefault(); // prevent the default action (e.g., form submission)
        return;
      }

      if (!isValidunitPrice) {
        event.preventDefault(); // prevent the default action (e.g., form submission)
        return;
      }

      if (isChecked) {
        console.warn('Checkbox is checked.');
        $("#round_off_checkbox").val(1);
      } else {
        console.warn('Checkbox is not checked.');
        $("#round_off_checkbox").val(0);
      }
    });

    $("#round_off_checkbox").on("change", function() {
      roundOffCal();
    });

    function roundOffCal() {
      let grandTotalAmt = $("#grandTotalAmt").text();
      $(".adjustedDueAmt").text(grandTotalAmt);
      $(".adjustedCollectAmountInp").val(grandTotalAmt);
    }
 

    $("#goodsType").on("change", function() {
      let goodsType = $(this).val();
      // alert("111");

      if (goodsType === "service") {
        // alert("777");
        $(".recurringDiv").show();
        // $(".fob-section").hide();
        // $(".orderFor").show();
        // alert("555");

        $("#orderForService").prop("checked", true);
        // alert("222");

        let orderForRadio = '';

        // $(".orderForRadio").on("click", function() {
        //   if ($(this).is(":checked")) {
        //     orderForRadio = $(this).val();
        //     if (orderForRadio === "service") {
        //       goodsType = "service";
        //       $("#goodsType").val("service");
        //     } else {
        //       goodsType = "project";
        //       $("#goodsType").val("project");
        //     }
        //   }
        //   $.ajax({
        //     type: "GET",
        //     url: `ajaxs/so/ajax-items-goods-type.php`,
        //     data: {
        //       act: "goodsType",
        //       goodsType
        //     },
        //     beforeSend: function() {
        //       $("#itemsDropDown").html(`<option>Loading...</option>`);
        //     },
        //     success: function(response) {
        //       $("#itemsDropDown").html(response);
        //     }
        //   });
        // });
        // $(".quickAdd").show();
      } else if (goodsType === "project") {
        $(".recurringDiv").hide();
      } else {
        $("#orderForService").prop("checked", false);
        $(".orderFor").hide();
        $(".recurringDiv").hide();
        $(".fob-section").show();
        // $(".quickAdd").show();
      }
      // alert("333");

      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-items-goods-type.php`,
        data: {
          act: "goodsType",
          goodsType
        },
        beforeSend: function() {
          $("#itemsDropDown").html(`<option>Loading...</option>`);
        },
        success: function(response) {
          $("#itemsDropDown").html(response);
        }
      });
      // alert("444");
    });

    $("#neverExpire").on('click', function() {
      let rec = $(this);
      if (rec.is(':checked')) {
        $("#endOn").val("");
        $("#endOn").prop('disabled', true);
      } else {
        $("#endOn").prop('disabled', false);
      }
    });

    function checkSpecialDiscount() {
      let isSpecialDiscountApplied = false;

      $(".itemDiscount").each(function() {
        let rowNum = ($(this).attr("id")).split("_")[1];
        let discountPercentage = parseFloat($(this).val());
        discountPercentage = discountPercentage > 0 ? discountPercentage : 0;
        let maxDiscountPercentage = parseFloat($(`#itemMaxDiscount_${rowNum}`).html());
        maxDiscountPercentage = maxDiscountPercentage > 0 ? maxDiscountPercentage : 0;
        if (discountPercentage > maxDiscountPercentage) {
          isSpecialDiscountApplied = true;
        }
      });

      if (isSpecialDiscountApplied) {
        $(`#approvalStatus`).val(`12`);
        console.log('max');
      } else {
        $(`#approvalStatus`).val(`14`);
        console.log('ok');
      }
    }

    function cashDiscountFunc(rowNo, keyValue) {
      let baseAmountIsCheck = $(`#baseAmountRadio_${rowNo}`).is(":checked");
      let itemBaseAmount = $(`#itemBaseAmountInp_${rowNo}`).val();
      console.log('baseAmountIsCheck', baseAmountIsCheck, keyValue);
      let itemQty = $(`#itemQty_${rowNo}`).val();
      let unitPrice = $(`#originalChangeItemUnitPriceInp_${rowNo}`).val();
      let itemGrossAmount = parseFloat($(`#itemGrossAmountSpan_${rowNo}`).text()) || 0;
      let discountAmt = parseFloat(itemGrossAmount) * parseFloat(keyValue) / 100;

      if (baseAmountIsCheck) {
        console.log('baseAmountIsCheck');

        discountAmt = parseFloat(itemBaseAmount) * parseFloat(keyValue) / 100;
      }

      $(`#itemTotalCashDiscount1_${rowNo}`).val(discountAmt);

      $(`#itemCashDiscountPercentageInp_${rowNo}`).val(`${keyValue}`);
      $(`#itemCashDiscountPercentageSpan_${rowNo}`).text(`${keyValue}`);
      $(`#itemCashDiscountAmountSpan_${rowNo}`).text(discountAmt);
      // console.log(discountAmt);
      $(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val(discountAmt);
      $(`#itemTaxableAmountSpan_${rowNo}`).text(parseFloat(itemGrossAmount) - discountAmt);
    }

    $(document).on("keyup", ".itemCashDiscount", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let keyValue = $(this).val();

      cashDiscountFunc(rowNo, keyValue);
      calculateOneItemAmounts(rowNo);
      checkSpecialDiscount();
    });



    function cashDiscountTotalFunc(rowNo, keyValue) {
      let baseAmountIsCheck = $(`#baseAmountRadio_${rowNo}`).is(":checked");
      //alert(baseAmountIsCheck);
      let itemBaseAmount = $(`#itemBaseAmountInp_${rowNo}`).val();
      console.log('baseAmountIsCheck', baseAmountIsCheck);
      let itemQty = $(`#itemQty_${rowNo}`).val();
      let unitPrice = $(`#originalChangeItemUnitPriceInp_${rowNo}`).val();
      let itemGrossAmount = parseFloat($(`#itemGrossAmountSpan_${rowNo}`).text()) || 0;

      // let discountAmt = parseFloat(itemGrossAmount) * parseFloat(keyValue) / 100;
      let discPercentage = (100 * parseFloat(keyValue)) / parseFloat(itemGrossAmount);
      if (baseAmountIsCheck) {
        // alert(1);
        //  discountAmt = parseFloat(itemBaseAmount) * parseFloat(keyValue) / 100;
        discPercentage = (100 * parseFloat(keyValue)) / parseFloat(itemBaseAmount);
      }

      $(`#itemCashDiscount_${rowNo}`).val(discPercentage);
      //alert(keyValue);
      $(`#itemCashDiscountPercentageInp_${rowNo}`).val(`${discPercentage}`);
      $(`#itemCashDiscountPercentageSpan_${rowNo}`).text(`${discPercentage}`);
      //  alert(keyValue);
      $(`#itemCashDiscountAmountSpan_${rowNo}`).text(keyValue);
      //  alert(keyValue);
      $(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val(keyValue);
      $(`#itemTaxableAmountSpan_${rowNo}`).text(parseFloat(itemGrossAmount) - keyValue);
    }




    $(document).on("keyup", ".itemTotalCashDiscount1", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let keyValue = $(this).val();

      cashDiscountTotalFunc(rowNo, keyValue);
      calculateOneItemAmounts(rowNo);
      checkSpecialDiscount();
    });

    $(document).on("change", ".grossAmountRadio", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let keyValue = $(`#itemCashDiscount_${rowNo}`).val();
      cashDiscountFunc(rowNo, keyValue);
      calculateOneItemAmounts(rowNo);
      checkSpecialDiscount();
    });

    $(document).on("change", ".baseAmountRadio", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let keyValue = $(`#itemCashDiscount_${rowNo}`).val();
      cashDiscountFunc(rowNo, keyValue);
      calculateOneItemAmounts(rowNo);
      checkSpecialDiscount();
    });

    $(document).on("change", ".cashDiscountTypeRadioBtn", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let discountType = $(this).data("cash-discount-type");
      $(`#selectedCashDiscountType_${rowNo}`).val(discountType);
    });

    $(document).on("keyup", ".itemDiscount", function() {
      console.log('this is item discount')
      let rowNo = ($(this).attr("id")).split("_")[1];
      let keyValue = $(this).val();

      if (keyValue < 0) {
        $(this).val(0);
      }

      if (keyValue > 100) {
        $(this).val(100);
      }

      $(`#itemTradeDiscountPercentageInp_${rowNo}`).val(keyValue);
      $(`#itemTradeDiscountPercentageSpan_${rowNo}`).text(keyValue);

      calculateOneItemAmounts(rowNo);
      itemMaxDiscount(rowNo, keyValue);
      checkSpecialDiscount();
      // $(`#itemTotalDiscount1_${rowNo}`).attr('disabled', 'disabled');
    });

    // #######################################################
    $(document).on("keyup blur click change", ".multiQuantity", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let itemid = ($(this).data("itemid"));
      let thisVal = ($(this).val());
      calculateQuantity(rowNo, itemid, thisVal);
    });

    // #######################################################
    $(document).on("blur", ".itemTotalDiscount1", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let itemDiscountAmt = ($(this).val());

      let itemQty = (parseFloat($(`#itemQty_${rowNo}`).val()) > 0) ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
      let originalItemUnitPrice = (parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) > 0) ? parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) : 0;

      let totalAmt = itemQty * originalItemUnitPrice;
      let discountPercentage = itemDiscountAmt * 100 / totalAmt;

      $(`#itemDiscount_${rowNo}`).val(discountPercentage);

      calculateOneItemAmounts(rowNo);
    });

    // allItemsBtn
    $("#allItemsBtn").on('click', function() {
      window.location.href = "";
    })

    // itemWiseSearch
    $("#itemWiseSearch").on('click', function() {
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-so-list.php`,
        data: {
          act: "itemWiseSearch"
        },
        beforeSend: function() {
          $(".tableDataBody").html(`<option value="">Loading...</option>`);
        },
        success: function(response) {
          $(".tableDataBody").html(response);
        }
      });
    })

    $(function() {
      $("#datepicker").datepicker({
        autoclose: true,
        todayHighlight: true
      }).datepicker('update', new Date());
    });


    // ***********************************************
    // ***********************************************
    $(document).on("click", ".itemreleasetypeclass", function() {
      let itemreleasetype = $(this).val();
      var rdcode = $(this).data("rdcode");
      console.log(rdcode);
      totalquentitydiscut(rdcode);
      $("#itemSellType_" + rdcode).html(itemreleasetype);
      if (itemreleasetype == 'CUSTOM') {
        $(".customitemreleaseDiv" + rdcode).show();
        $("#itemQty_" + rdcode).prop("readonly", true);
      } else {
        $(".customitemreleaseDiv" + rdcode).hide();
        $("#itemQty_" + rdcode).prop("readonly", false);
      }
    });

    $(document).on("keyup paste keydown", ".enterQty", function() {
      let enterQty = $(this).val();
      var rdcodeSt = $(this).data("rdcode");
      var maxqty = $(this).data("maxval");
      let rdatrr = [];
      rdatrr = rdcodeSt.split("|");
      let rdcode = rdatrr[0]; // Change the variable name to rdcode
      let rdBatch = rdatrr[1];

      console.log(enterQty);
      if (enterQty <= maxqty) {
        if (enterQty > 0) {
          console.log("01");
          totalquentity(rdcodeSt);
          $('.batchCheckbox' + rdBatch).prop('checked', true);
        } else {
          $(this).val('');
          console.log("02");
          totalquentity(rdcodeSt);
          $('.batchCheckbox' + rdBatch).prop('checked', false);
        }
      } else {
        $(this).val('');
        console.log("03");
        totalquentity(rdcodeSt);
      }
    });

    function totalquentitydiscut(rdcode) {

      $(".qty" + rdcode).each(function() {
        $(this).val('');
      });
      $("#itemSelectTotalQty_" + rdcode).html(0);
      $("#itemQty_" + rdcode).val(0);
      $('.batchCbox').prop('checked', false);
    }

    function totalquentity(rdcodeSt) {
      let rdatrr = [];
      rdatrr = rdcodeSt.split("|");
      let rdcode = rdatrr[0]; // Change the variable name to rdcode
      let rdBatch = rdatrr[1];
      var sum = 0;

      $(".qty" + rdcode).each(function() {
        // Parse the value as a number and add it to the sum
        var value = parseFloat($(this).val()) || 0;
        sum += value;
      });

      // console.log("Sum: " + sum);

      $("#itemSelectTotalQty_" + rdcode).html(sum);
      $("#itemQty_" + rdcode).val(sum);
      console.log('first => ' + rdcode);
      calculateOneItemAmounts(rdcode);
    }
    // ***********************************************
    // ***********************************************
    $(document).on("click", "#btnSearchCollpase", function() {
      sec = document.getElementById("btnSearchCollpase").parentElement;
      coll = sec.getElementsByClassName("collapsible-content")[0];

      if (sec.style.width != '100%') {
        sec.style.width = '100%';
      } else {
        sec.style.width = 'auto';
      }

      if (coll.style.height != 'auto') {
        coll.style.height = 'auto';
      } else {
        coll.style.height = '0px';
      }

      $(this).children().toggleClass("fa-search fa-times");
    });

  }); // document ready end here

  $('.hamburger').click(function() {
    $('.hamburger').toggleClass('show');
    $('#overlay').toggleClass('show');
    $('.nav-action').toggleClass('show');
  });

  $(`#terms-and-condition`)
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });

  $('#itemsDropDown')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
  $('.currencyDropdown')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
  // $('#customerDropDown')
  //   .select2()
  //   .on('select2:open', () => {
  //     $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewCustomerModal">Add New</a></div>`);
  //   });

  $('#customerDropDown').select2({
    placeholder: 'Select Customer',
    ajax: {
      url: 'ajaxs/so/ajax-customerslst-select2.php',
      dataType: 'json',
      delay: 50,
      data: function(params) {
        return {
          searchTerm: params.term // search term
        };
      },
      processResults: function(data) {
        return {
          results: data
        };
      },
      cache: true
    }
  }).on('select2:open', function(e) {
    var $results = $(e.target).data('select2').$dropdown.find('.select2-results');

    // Conditionally add the 'Add New' button based on the element
    if (!$results.find('a').length) {
      $results.append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewCustomerModal">Add New</a></div>`);
    }
  });

  $('#profitCenterDropDown')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
  $('#servicesDropDown')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
  $('#kamDropDown')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
  $('#placeOfSupply1')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
</script>

<script src="<?= BASE_URL; ?>public/validations/soValidation.js?v=1"></script>
<!-- <script src="<?= BASE_URL; ?>public/validations/goodsInvoiceValidation.js"></script> -->

<script src="https://code.getmdl.io/1.2.0/material.min.js"></script>
<script>
  // *** multi step form *** //

  // DOM elements
  const DOMstrings = {
    stepsBtnClass: 'multisteps-form__progress-btn',
    stepsBtns: document.querySelectorAll(`.multisteps-form__progress-btn`),
    stepsBar: document.querySelector('.multisteps-form__progress'),
    stepsForm: document.querySelector('.multisteps-form__form'),
    stepsFormTextareas: document.querySelectorAll('.multisteps-form__textarea'),
    stepFormPanelClass: 'multisteps-form__panel',
    stepFormPanels: document.querySelectorAll('.multisteps-form__panel'),
    stepPrevBtnClass: 'js-btn-prev',
    stepNextBtnClass: 'js-btn-next'
  };

  //remove class from a set of items
  const removeClasses = (elemSet, className) => {

    elemSet.forEach(elem => {

      elem.classList.remove(className);

    });

  };

  //return exect parent node of the element
  const findParent = (elem, parentClass) => {

    let currentNode = elem;

    while (!currentNode.classList.contains(parentClass)) {
      currentNode = currentNode.parentNode;
    }

    return currentNode;

  };

  //get active button step number
  const getActiveStep = elem => {
    return Array.from(DOMstrings.stepsBtns).indexOf(elem);
  };

  //set all steps before clicked (and clicked too) to active
  const setActiveStep = activeStepNum => {

    //remove active state from all the state
    removeClasses(DOMstrings.stepsBtns, 'js-active');

    //set picked items to active
    DOMstrings.stepsBtns.forEach((elem, index) => {

      if (index <= activeStepNum) {
        elem.classList.add('js-active');
      }

    });
  };

  //get active panel
  const getActivePanel = () => {

    let activePanel;

    DOMstrings.stepFormPanels.forEach(elem => {

      if (elem.classList.contains('js-active')) {

        activePanel = elem;

      }

    });

    return activePanel;

  };

  //open active panel (and close unactive panels)
  const setActivePanel = activePanelNum => {

    //remove active class from all the panels
    removeClasses(DOMstrings.stepFormPanels, 'js-active');

    //show active panel
    DOMstrings.stepFormPanels.forEach((elem, index) => {
      if (index === activePanelNum) {
        elem.classList.add('js-active');
        setFormHeight(elem);
      }
    });
  };

  //set form height equal to current panel height
  const formHeight = activePanel => {
    const activePanelHeight = activePanel.offsetHeight;
    DOMstrings.stepsForm.style.height = `${activePanelHeight}px`;
  };

  const setFormHeight = () => {
    const activePanel = getActivePanel();
    formHeight(activePanel);
  };

  //STEPS BAR CLICK FUNCTION
  DOMstrings.stepsBar.addEventListener('click', e => {

    //check if click target is a step button
    const eventTarget = e.target;

    if (!eventTarget.classList.contains(`${DOMstrings.stepsBtnClass}`)) {
      return;
    }

    //get active button step number
    const activeStep = getActiveStep(eventTarget);

    //set all steps before clicked (and clicked too) to active
    setActiveStep(activeStep);

    //open active panel
    setActivePanel(activeStep);
  });

  //PREV/NEXT BTNS CLICK
  DOMstrings.stepsForm.addEventListener('click', e => {

    const eventTarget = e.target;

    //check if we clicked on `PREV` or NEXT` buttons
    if (!(eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`) || eventTarget.classList.contains(`${DOMstrings.stepNextBtnClass}`))) {
      return;
    }

    //find active panel
    const activePanel = findParent(eventTarget, `${DOMstrings.stepFormPanelClass}`);

    let activePanelNum = Array.from(DOMstrings.stepFormPanels).indexOf(activePanel);

    //set active step and active panel onclick
    if (eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`)) {
      activePanelNum--;

    } else {

      activePanelNum++;

    }

    setActiveStep(activePanelNum);
    setActivePanel(activePanelNum);

  });

  //SETTING PROPER FORM HEIGHT ONLOAD
  window.addEventListener('load', setFormHeight, false);

  //SETTING PROPER FORM HEIGHT ONRESIZE
  window.addEventListener('resize', setFormHeight, false);

  //changing animation via animation select !!!YOU DON'T NEED THIS CODE (if you want to change animation type, just change form panels data-attr)

  const setAnimationType = newType => {
    DOMstrings.stepFormPanels.forEach(elem => {
      elem.dataset.animation = newType;
    });
  };

  // selector onchange - changing animation
  const animationSelect = document.querySelector('.pick-animation__select');

  animationSelect.addEventListener('change', () => {
    const newAnimationType = animationSelect.value;

    setAnimationType(newAnimationType);
  });
</script>
<script>
  $(document).on("click", ".add_data", function() {
    var data = this.value;
    $("#createdatamultiform").val(data);
    // confirm('Are you sure to Submit?')
    $("#add_frm").submit();
  });
</script>

<!-- script for adding customer with GSTIN  -->
<script>
  $('#customer_gstin').focusout(function() {
    let customerGstNo = $('#customer_gstin').val();

    $.ajax({
      type: "GET",
      dataType: 'json',
      url: `<?= COMPANY_URL ?>ajaxs/ajax-gst-details.php?gstin=${customerGstNo}`,
      success: function(response) {
        let data = response.data;
        let city;
        // console.log(response)
        if (response.status == "success") {
          $('#customer_pan').prop('readonly', true);

          if (data.pradr.addr.city) {
            city = data.pradr.addr.city;
          } else {
            city = data.pradr.addr.loc;
          }
          $('#customer_pan').val((data.gstin).substring(2, 12))

          $('#trade_name').val(data.lgnm)
          $('#con_business').val(data.ctb)
          $(`.selDiv  option:eq(${(data.gstin).slice(0,2)-1})`).prop('selected', true);
          $('#city').val(city)
          $('#district').val(data.pradr.addr.dst)
          $('#location').val(data.pradr.addr.loc)
          $('#build_no').val(data.pradr.addr.bno)
          $('#flat_no').val(data.pradr.addr.flno)
          $('#street_name').val(data.pradr.addr.st)
          $('#pincode').val(data.pradr.addr.pncd)

        } else {
          $('#customer_pan').prop('readonly', false);
        }
      }
    });

  })

  // tcs hide show function
  $("#tcsAmtshowhidediv").hide();
  $(document).on('change', '.tcscheckbox', function() {
    if (this.checked) {
      $("#tcsAmtshowhidediv").show();
    } else {
      $("#tcsAmtshowhidediv").hide();
    }
  });
</script>

<script>
  $(document).ready(function() {
    // On click of the Preview button
    $('.previewBtn').click(function() {
      // Get the selected value from the <select> element
      var selectedValue = $('#terms-and-condition').val();

      // Perform AJAX request based on the selected value
      $.ajax({
        url: 'ajaxs/so/ajax-tc.php', // Replace with your API endpoint or server URL
        type: 'GET',
        data: {
          value: selectedValue, // Send the selected value to the server
          act: "tc"
        },

        success: function(response) {
          // console.log(response);
          let obj = JSON.parse(response);

          $('.tc-modal-title').html(obj['termHead']);
          $('.tc-modal-body').html(obj['termscond']);
          // Assuming the response contains the content you want to show in the modal
          // You can adjust this depending on your response structure
          // $('#modalBody').html(response.data); // Populate the modal with the response data
        },
        error: function(error) {
          // Handle any error that occurs during the AJAX request
          console.log('Error:', error);
          $('#modalBody').html('An error occurred while fetching the data.');
        }
      });
    });
  });
</script>