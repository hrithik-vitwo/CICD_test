<?php
$companyCountry = $_SESSION["logedBranchAdminInfo"]["companyCountry"];
?>

<script>
    // new js start
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
            beforeSend: function () { },
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


    // new js end
</script>

<!-- --------------------  Total old scripts of direct create start  ---------------->

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
        var currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set(paramName, paramValue);
        window.history.replaceState({}, '', currentUrl);
    }

    $("#profitCenterDropDown").on("change", function () {
        let functionalArea = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 1;
        $.ajax({
            type: "POST",
            url: `ajaxs/so/ajax-generate-inv-number.php`,
            data: {
                act: "getVerientExamplecopy",
                functionalArea: functionalArea
            },
            beforeSend: function () { },
            success: function (response) {
                let data = JSON.parse(response);
                $("#iv_varient").val(data['id']);
                $(".ivnumberexample").html(data['iv_number_example']);
            }
        });
    });

    $("#iv_varient").on("change", function () {
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
            beforeSend: function () { },
            success: function (response) {
                let data = JSON.parse(response);
                $(".ivnumberexample").html(data['iv_number_example']);
            }
        });
    });


    $(document).on("click", ".dlt-popup", function () {
        $(this).parent().parent().remove();
    });

    function rm() {
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
            .on('select2:open', () => { });
    }

    $(document).on("click", ".add-btn-minus", function () {
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

    $(document).ready(function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('so_to_invoice') || urlParams.has('proforma_to_invoice') || urlParams.has('pgi_to_invoice')) {
            let customerGstinCode = $(".customerGstinCode").val();
            taxGenerate(customerGstinCode);
        }

        $(document).on('click', '.go', function () {
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

        $('#fob').on('change', function () {
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
                beforeSend: function () {
                    $("#itemsDropDown").html(`<option value="">Loading...</option>`);
                },
                data: {
                    act: "goodsType",
                    goodsType: goodsType
                },
                success: function (response) {
                    $("#itemsDropDown").html(response);
                }
            });
        };

        $("#fob").on("click", function () {
            // alert();
            if ($('#fob').is(':checked')) {
                $("#otherCostCard").show();
            } else {
                $("#otherCostCard").hide();
            }
        });

        $("#soDate").on("change", function () {
            let soDate = $(this).val();
            $(".multiDeliveryDate").val(soDate);
        })

        // add customers
        $("#addCustomerBtn").on("click", function (e) {
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
                    beforeSend: function () {
                        $("#addCustomerBtn").prop('disabled', true);
                        $("#addCustomerBtn").text(`Adding...`);
                    },
                    success: function (response) {
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
                beforeSend: function () {
                    $("#customerDropDown").html(`<option value="">Loading... </option>`);
                },
                success: function (response) {
                    $("#customerDropDown").html(response);
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
                beforeSend: function () {
                    $("#shipTo").html(`Loading...`);
                },
                success: function (response) {
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
                beforeSend: function () {
                    $("#customerInfo").html(`<option value="">Loading...</option>`);
                },
                success: function (response) {
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
        $("#customerDropDown").on("change", function () {
            let customerId = $(this).val();

            if (customerId > 0) {
                $(document).on("click", ".billToCheckbox", function () {
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

            $(".deliveryScheduleModal").each(function () {
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
                    if (isNaN(days) || days < 0) {
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
                        beforeSend: function () { },
                        success: function (response) {
                            $(`#originalItemUnitPriceInp_${rowNo}`).val(response);
                            $(`#originalChangeItemUnitPriceInp_${rowNo}`).val(response);
                            calculateOneItemAmounts(rowNo);

                        }
                    });

                } else { }
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
                beforeSend: function () {
                    $("#customerInfo").html(`<option value="">Loading...</option>`);
                },
                success: function (response) {
                    $("#customerInfo").html(response);
                    let creditPeriod = $("#spanCreditPeriod").text();
                    $("#inputCreditPeriod").val(creditPeriod);

                    let stateCodeSpanElement = $(".stateCodeSpan");
                    let stateCodeSpan = stateCodeSpanElement.length > 0 ? stateCodeSpanElement.text().trim() : null;
                    $("#placeOfSupply1").val(stateCodeSpan).trigger("change");
                    taxGenerate(stateCodeSpan);


                    let customerGstinCode = $(".customerGstinCode").val();
                    let branchGstinCode = $(".branchGstin").val();
                    if (customerGstinCode !== "") {
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
                        $(document).on("change", "#placeOfSupply1", function () {
                            let placeOfSupply1 = $(this).val();
                            //alert(placeOfSupply1);
                            let customerGstinCode = $(".customerGstinCode").val();
                            //  alert(customerGstinCode);
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
                            success: function (response2) {
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
                                    $("#profitCenterDropDown").val('').trigger("change");
                                    // $("#compInvoiceType").val('R').trigger("change");
                                    $("#kamDropDown").val('').trigger("change");
                                    $("#bankId").val('').trigger("change");
                                    // $("#placeOfSupply1").val('').trigger("change");
                                    $("#iv_varient").val('').trigger("change");
                                }
                            },
                            error: function (xhr, status, error) { }
                        });
                    }
                }
            });
        }

        $(document).on("click", "#pills-home-tab", function () {
            $("#saveChanges").html('<button type="button" class="btn btn-primary go">Go</button>');
        });
        $(document).on("click", "#pills-profile-tab", function () {
            $("#saveChanges").html('<button type="button" class="btn btn-primary" id="save">Save</button>');
        });


        // subscription
        $("#makeRecurring").on("click", function () {
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
        $(".subscriptionClose").on('click', function () {
            $("#recurringModal").modal("hide");
        });

        // handleConfig function to fetch and update options
        function handleConfig() {
            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-config-invoice.php`,
                beforeSend: function () {
                    $("#config").html(`<option value="">Loading...</option>`);
                },
                data: {
                    act: "getContact"
                },
                success: function (response) {
                    let data = JSON.parse(response);
                    if (data.status == "success") {

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
                success: function (response) {
                    let data = JSON.parse(response);
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
                error: function (xhr, status, error) { }
            });
        }
        // handleConfigSave function
        $(document).on("click", "#handleConfigSave", function () {
            handleConfigSave();
        })

        // submit address form
        $(document).on('click', '#save', function () {
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
                    beforeSend: function () {
                        $(`#save`).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                    },

                    success: function (response) {
                        let data = JSON.parse(response);
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
        function itemAutoAdd(itemIdArry, serviceitemIdArry) {

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
                $.each(itemIdArryTo, function (index, value) {
                    if (value.inventory_item_id > 0) {
                        $.ajax({
                            type: "GET",
                            url: `ajaxs/so/ajax-items-list-direct-new-inv.php`,
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
                            beforeSend: function () {
                                $(`#spanItemsTable`).html(`Loading...`);
                            },
                            success: function (response) {
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

            if (serviceitemIdArry != '') {

                const sanitizedJSONData = serviceitemIdArry.replace(/[\x00-\x1F\x7F-\x9F]/g, '');
                var itemIdArryTo = JSON.parse(sanitizedJSONData);
                var invoicedate = $("#invoiceDate").val();
                var compInvoiceType = $("#compInvoiceType").val();
                // console.log(itemIdArryTo);
                $.each(itemIdArryTo, function (index, subArray) {

                    $.each(subArray, function (subIndex, value) {
                        if (value.inventory_item_id > 0) {

                            $.ajax({
                                type: "GET",
                                url: `ajaxs/so/ajax-items-list-direct-new-inv.php`,
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
                                beforeSend: function () {
                                    $(`#spanItemsTable`).html(`Loading...`);
                                },
                                success: function (response) {
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
                });
            }

        }

        itemAutoAdd('<?= $itemIdJson; ?>', '<?= $serviceitemjson; ?>');
        // get item details by id
        $("#itemsDropDown").on("change", function () {
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
                    url: `ajaxs/so/ajax-items-list-direct-new-inv.php`,
                    data: {
                        act: "listItem",
                        type: param,
                        valueId: searchValue,
                        invoicedate: invoicedate,
                        compInvoiceType: compInvoiceType,
                        customer_id: customer_id,
                        itemId
                    },
                    beforeSend: function () {
                        $(`#spanItemsTable`).html(`Loading...`);
                    },
                    success: function (response) {
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


        $(document).ready(function () {
            // Event delegation to handle modal opening
            $(document).on('show.bs.modal', '.deliveryScheduleModal', function () {
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

                    if (isNaN(days) || days < 0) {
                        alert('Please enter a valid positive integer for credit period');

                    } else {
                        discount_varients(rowNo, customer_id, item_id, days);
                    }

                } else { }
            });





        });

    function getOtherAdress(customerId){
        $.ajax({
        url: 'ajaxs/so/ajax-customers-other-address-list.php',
        type: 'GET',
        data: {
        act: 'otherAddressList',
        id: customerId
        },
        dataType: 'json',
        success: function(response) {
        if (response.status === "success") {
            var addressHtmlArr = [];
            $.each(response.data, function(index, address) {
            var addressDiv = `
                <div class="address-to bill-to">
                <input type="radio" class="address-check checkRadio"
                    data-addid="${address.customer_address_id}"
                    data-statecode="${address.customer_address_state_code}"
                    id="checkRadio_${address.customer_address_id}"
                    name="radioBtn"
                    value="${address.full_address}"
                    ${address.checked}>
                <h5>${address.customer_address_city}</h5>
                <hr class="mt-0 mb-2">
                <p>${address.full_address}</p>
                </div>
            `;
            addressHtmlArr.push(addressDiv);
            });
            $('#savedAddress').html(addressHtmlArr.join(''));
        } else {
            $('#savedAddress').html('<div>No other address found!</div>');
        }
        }
    });
  }
        
    $(document).on('click', '.otheraddressbtn', function() {
        var customerId = $(this).data('id');

        getOtherAdress(customerId);


    });
        
    $(document).on('click', '.changeAddress', function() {
        var customerId = $(this).data('id');
        // alert("went here")

        getOtherAdress(customerId);


    }); 

        $('#inputCreditPeriod').keyup(function () {
            // alert(1);

            $(".deliveryScheduleModal").each(function () {
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
                    if (isNaN(days) || days < 0) {
                        alert('Please enter a valid positive integer for credit period');

                    } else {
                        discount_varients(rowNo, customer_id, item_id, days);
                    }
                } else { }

            });

        });



        function discount_varients(rowNo, customer_id, item_id, days) {
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
                beforeSend: function () { },
                success: function (response) {

                    var obj = JSON.parse(response);


                    $.each(obj, function (index, item) {

                        var rand = Math.ceil(Math.random() * 100000);


                        let selectedDiscountDivNote = item.discount_variant_id == previousDocDiscountVariantId ? `<p class="pre-normal text-warning">In the previous document, this discount was selected!</p>` : ``;

                        if (item.discount_type == 'value') {
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

            $(`.discountView_${rowNo}`).on('click', '.discount_radio', function () {
                var random = $(this).data('attr');

                var percentage_val = $(`#percentage_val_${random}`).val();
                var discount_type = $(`#discount_type_${random}`).val();
                var discount_varient_id = $(`#discount_variant_id_${random}`).val();

                var max_val = $(`#max_val_${random}`).val();
                var base_price = itemVal;

                let itemQty = helperQuantity($(`#itemQty_${rowNo}`).val()) > 0 ? helperQuantity($(`#itemQty_${rowNo}`).val()) : 0;
                let itemCashDiscountAmount = helperAmount($(`#itemTotalCashDiscount_${rowNo}`).text()) || 0;
                let originalChangeItemUnitPriceInp = helperAmount($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) || 0;
                let basicPrice = helperAmount(originalChangeItemUnitPriceInp * itemQty);

                let item_id = $(`#itemId_${rowNo}`).val();
                $(`#itemTradeDiscountPercentageInp_${rowNo}`).val(`${decimalQuantity(percentage_val)}`);
                $(`#itemTradeDiscountPercentageSpan_${rowNo}`).text(`${decimalQuantity(percentage_val)}`);

                if (discount_type == 'percentage') {
                    let itemQty = $(`#itemQty_${rowNo}`).val();

                    var percentage_amount = helperAmount((percentage_val / 100) * base_price);
                    let itemGrossAmount = helperAmount((itemQty * originalChangeItemUnitPriceInp) - percentage_amount);
                    $(`#itemGrossAmountSpan_${rowNo}`).text(`${decimalAmount(itemGrossAmount)}`);

                    $(`#itemGrossAmountSpan_${rowNo}`).text(decimalAmount(basicPrice - percentage_amount));
                    $(`#itemTaxableAmountSpan_${rowNo}`).text(decimalAmount(basicPrice - percentage_amount - itemCashDiscountAmount));
                    if (max_val > 0 && max_val !== null) {
                        if (percentage_amount > max_val) {

                            var new_base_price = helperAmount(base_price - max_val);
                            $(`#itemTotalDiscount1_${rowNo}`).val(decimalAmount(max_val));
                            $(`#itemDiscount_${rowNo}`).val(decimalAmount(percentage_val));
                            $(`#itemDiscountVarientId_${rowNo}`).val(discount_varient_id);
                            $(`#itemTotalDiscountHidden_${rowNo}`).val(decimalAmount(max_val));
                            $(`#itemTotalDiscount_${rowNo}`).html(decimalAmount(max_val));
                            var dis = $(`#itemTotalDiscount1_${rowNo}`).val();
                            $(`#itemTradeDiscountAmountInp_${rowNo}`).val(decimalAmount(max_val));
                            $(`#itemTradeDiscountAmountSpan_${rowNo}`).text(decimalAmount(max_val));
                            var percentage = helperAmount($(`#itemDiscount_${rowNo}`).val())
                            $(`#itemTradeDiscountPercentageSpan_${rowNo}`).text(`${percentage}`)
                            console.log("percentage_val =>" + percentage_val)
                            console.log("max_val =>" + max_val)
                            console.log("percentage =>" + percentage)
                            calculateOneItemAmounts(rowNo);
                        }

                    } else {
                        var new_base_price = helperAmount(base_price - percentage_amount);
                        $(`#itemTotalDiscount1_${rowNo}`).val(decimalAmount(percentage_amount));
                        $(`#itemDiscount_${rowNo}`).val(decimalAmount(percentage_val));
                        $(`#itemDiscountVarientId_${rowNo}`).val(discount_varient_id);
                        $(`#itemTotalDiscountHidden_${rowNo}`).val(decimalAmount(percentage_amount));
                        $(`#itemTotalDiscount_${rowNo}`).html(decimalAmount(percentage_amount));

                        $(`#itemTradeDiscountAmountInp_${rowNo}`).val(decimalAmount(percentage_amount));
                        $(`#itemTradeDiscountAmountSpan_${rowNo}`).text(decimalAmount(percentage_amount));
                        var percentage = helperAmount($(`#itemDiscount_${rowNo}`).val())
                        $(`#itemTradeDiscountPercentageSpan_${rowNo}`).text(`${decimalQuantity(percentage)}`)
                        console.log("percentage_val => " + percentage_val)
                        console.log("percentage_amount =>" + percentage_amount)
                        console.log("percentage =>" + percentage)
                        calculateOneItemAmounts(rowNo);
                    }

                } else {
                    var percent = helperQuantity((percentage_val * 100) / base_price)
                    $(`#itemTotalDiscount1_${rowNo}`).val(decimalAmount(percentage_val));
                    $(`#itemDiscount_${rowNo}`).val(percent);
                    $(`#itemDiscountVarientId_${rowNo}`).val(discount_varient_id);
                    $(`#itemTotalDiscountHidden_${rowNo}`).val(decimalQuantity(percentage_val));
                    $(`#itemTotalDiscount_${rowNo}`).html(decimalQuantity(percentage_val));
                    $(`#itemTradeDiscountAmountInp_${rowNo}`).val(decimalQuantity(percentage_val));
                    // $(`#itemTradeDiscountAmountInp_${rowNo}`).val(percentage_amount);
                    $(`#itemTradeDiscountAmountSpan_${rowNo}`).text(decimalAmount(percentage_amount));
                    var percentage = parseFloat($(`#itemDiscount_${rowNo}`).val());
                    $(`#itemTradeDiscountPercentageSpan_${rowNo}`).text(`${decimalQuantity(percentage)}`);
                    $(`#itemTradeDiscountName_${rowNo}`).val("cash");
                    calculateOneItemAmounts(rowNo);
                }

                calculateQuantity(rowNo, item_id, percentage_val);
                calculateOneItemAmounts(rowNo);
                calculateGrandTotalAmount();
            });
        }

        $("#round_off_hide").hide();
        $(document).on('change', '.checkbox', function () {
            if (this.checked) {
                $("#round_off_hide").show();
            } else {
                $("#round_off_hide").hide();
            }
        });

        function roundofftotal(total_value, sign, roudoff) {
            let final_value = 0;
            let tcsAmount = (helperAmount($('#tcs_amount').val()) > 0) ? helperAmount($('#tcs_amount').val()) : 0;

            if (sign === "+") {
                final_value = helperAmount(total_value + roudoff + tcsAmount);
            } else {
                final_value = helperAmount(total_value + tcsAmount - roudoff);
            }
            $(".adjustedDueAmt").html(decimalAmount(final_value));
            $(".adjustedCollectAmountInp").val(decimalAmount(final_value));
            $("#grandTotalAmtInp").html(decimalAmount(final_value));


        }

        $(document).on("change", "#round_sign", function () {
            let roundValue = (helperAmount($("#round_value").val()) > 0) ? helperAmount($("#round_value").val()) : 0;
            let total_value = (helperAmount($("#grandTotalAmtInp").val()) > 0) ? helperAmount($("#grandTotalAmtInp").val()) : 0;
            var sign = $('#round_sign').val();
            roundofftotal(total_value, sign, roundValue);
            $(".roundOffValueHidden").val(decimalAmount(sign + roundValue));
        });

        $(document).on("keyup", "#round_value", function () {
            let roundValue = (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
            let total_value = (helperAmount($("#grandTotalAmtInp").val()) > 0) ? helperAmount($("#grandTotalAmtInp").val()) : 0;
            var sign = $('#round_sign').val();
            roundofftotal(total_value, sign, roundValue);
            $(".roundOffValueHidden").val(decimalAmount(sign + roundValue));
            calculateGrandTotalAmount();
        });

        // tcs function section start

        function tcsTotal(total_value, tcsAmount) {
            let final_value = 0;
            final_value = total_value + tcsAmount;
            $("#grandTotalAmt").html(decimalAmount(final_value));
            $('.tcsValueInp').val(decimalAmount(tcsAmount))
            $(".adjustedCollectAmountInp").val(decimalAmount(final_value));
            $("#grandTotalAmtInp").html(decimalAmount(final_value));


        }

        $(document).on("keyup", "#tcs_amount", function () {
            let tcsAmount = (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
            let total_value = (helperAmount($("#grandTotalAmtInp").val()) > 0) ? helperAmount($("#grandTotalAmtInp").val()) : 0;
            tcsTotal(total_value, tcsAmount);
            $(".roundOffValueHidden").val(decimalAmount(sign + roundValue));
            calculateGrandTotalAmount();
        });
        // tcs function section end

        $(document).on("click", ".delItemBtn", function () {
            $(this).parent().parent().remove();
            calculateGrandTotalAmount();
        });

        $(document).on('submit', '#addNewItemForm', function (event) {
            event.preventDefault();
            let formData = $("#addNewItemsForm").serialize();
            $.ajax({
                type: "POST",
                url: `ajaxs/so/ajax-items.php`,
                data: formData,
                beforeSend: function () {
                    $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
                    $("#addNewItemsFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');
                },
                success: function (response) {
                    $("#goodTypeDropDown").html(response);
                    $('#addNewItemsForm').trigger("reset");
                    $("#addNewItemsFormModal").modal('toggle');
                    $("#addNewItemsFormSubmitBtn").html("Submit");
                    $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
                }
            });
        });

        $(document).on("keyup change", ".qty", function () {
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
                beforeSend: function () {
                    $(".totalPrice").html(`<option value="">Loading...</option>`);
                },
                success: function (response) {
                    $(".totalPrice").html(response);
                }
            });
        });

        $(document).on("click", ".toggleServiceRemarksPen", function () {
            let rowNo = ($(this).attr("id")).split("_")[1];
            $(`#itemRemarks_${rowNo}`).toggle();
        });


        // one item calculation 
        function calculateOneItemAmounts(rowNo) {
            let param = window.location.search.split("=")[0];
            let baseAmountIsCheck = $(`#baseAmountRadio_${rowNo}`).is(":checked");

            let itemQty = helperQuantity($(`#itemQty_${rowNo}`).val()) || 0;
            let originalItemUnitPrice = helperAmount($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) || 0;
            let convertedItemUnitPrice = helperAmount($(`#convertedItemUnitPriceSpan_${rowNo}`).text()) || 0;
            let itemDiscount = helperAmount($(`#itemDiscount_${rowNo}`).val()) || 0;
            let itemDiscountVarientId = helperAmount($(`#itemDiscountVarientId_${rowNo}`).val()) || 0;
            let itemCashDiscount = helperAmount($(`#itemCashDiscount_${rowNo}`).val()) || 0;
            let convertedCurrencyValue = helperAmount($(`#curr_rate`).val()) || 0;

            let basicPrice = helperAmount(itemQty * originalItemUnitPrice);

            let itemTradeDiscountAmount = helperAmount($(`#itemTradeDiscountAmountSpan_${rowNo}`).text()) || 0;
            let itemCashDiscountAmount = helperAmount($(`#itemCashDiscountAmountSpan_${rowNo}`).text()) || 0;
            let itemGrossAmount = helperAmount($(`#itemGrossAmountSpan_${rowNo}`).text()) || 0;
            let itemTradeDiscountPercentageSpan = helperAmount($(`#itemTradeDiscountPercentageSpan_${rowNo}`).text()) || 0;
            let itemCashDiscountPercentageSpan = helperAmount($(`#itemCashDiscountPercentageSpan_${rowNo}`).text()) || 0;

            let itemTradeDiscountAmountInp = helperAmount($(`#itemTradeDiscountAmountInp_${rowNo}`).val()) || 0;
            let itemCashDiscountAmountInp = helperAmount($(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val()) || 0;

            let grossAmount = helperAmount(basicPrice - ((basicPrice) * itemTradeDiscountPercentageSpan / 100));


            let itemTradeDiscountName = $(`#itemTradeDiscountName_${rowNo}`).val();
            if (itemTradeDiscountName == 'cash') {
                grossAmount = helperAmount(basicPrice - itemTradeDiscountAmount);

            } else {
                grossAmount = helperAmount(basicPrice - ((basicPrice) * itemTradeDiscountPercentageSpan / 100));
            }

            let itemTaxableAmount = helperAmount((grossAmount - itemCashDiscountAmount)) || 0;

            let convertedTradeDiscountAmount = helperAmount(itemTradeDiscountAmountInp * convertedCurrencyValue);
            let convertedGrossAmount = helperAmount(itemGrossAmount * convertedCurrencyValue);
            let convertedCashDiscountAmount = helperAmount(itemCashDiscountAmountInp * convertedCurrencyValue);
            let convertedTaxableAmount = helperAmount(itemTaxableAmount * convertedCurrencyValue);

            $(`#convertedItemTradeDiscountAmountSpan_${rowNo}`).text(decimalAmount(convertedTradeDiscountAmount));
            $(`#convertedGrossAmountSpan_${rowNo}`).text(decimalAmount(convertedGrossAmount));
            $(`#convertedCashDiscountAmountSpan_${rowNo}`).text(decimalAmount(convertedCashDiscountAmount));
            $(`#convertedTaxableAmountSpan_${rowNo}`).text(decimalAmount(convertedTaxableAmount));

            let itemTax = parseFloat($(`#itemTax_${rowNo}`).val()) || 0;

            $(`#multiQuantity_${rowNo}`).val(itemQty);

            let convertedBasicPrice = helperAmount(convertedItemUnitPrice * itemQty);

            let totalDiscount = helperAmount($(`#itemTotalDiscount1_${rowNo}`).val()) || 0;
            let convertedTotalDiscount = helperAmount(convertedBasicPrice * itemDiscount / 100);
            let convertedTotalCashDiscount = helperAmount(convertedBasicPrice * itemCashDiscount / 100);

            // let cashDiscountAmount = (basicPrice * itemCashDiscountPercentageSpan) / 100;
            let cashDiscountAmount = itemCashDiscountAmount;


            let priceWithDiscount = itemTaxableAmount;

            let convertedPriceWithDiscount = helperAmount(convertedBasicPrice - convertedTotalDiscount);
            let taxableAmount = helperAmount(grossAmount - cashDiscountAmount);
            let totalTax = helperAmount(taxableAmount * itemTax / 100);
            //alert(totalTax);
            let convertedTotalTax = helperAmount(convertedPriceWithDiscount * itemTax / 100);

            let totalItemPrice = helperAmount(taxableAmount + totalTax);
            let convertedTotalItemPrice = helperAmount(convertedPriceWithDiscount + convertedTotalTax);

            $(`#itemGrossAmountSpan_${rowNo}`).text(decimalAmount(basicPrice - totalDiscount));
            $(`#itemGrossAmountInCashDiscount_${rowNo}`).text(decimalAmount(basicPrice - totalDiscount));
            $(`#grossAmountRadio_${rowNo}`).val(decimalAmount(basicPrice - itemTradeDiscountAmount));
            $(`#itemTaxableAmountSpan_${rowNo}`).text(decimalAmount(grossAmount - cashDiscountAmount));

            $(`#itemBaseAmountInp_${rowNo}`).val(decimalAmount(basicPrice));
            $(`#itemBaseAmountInCashDiscount_${rowNo}`).html(decimalAmount(basicPrice));
            $(`#baseAmountRadio_${rowNo}`).val(decimalAmount(basicPrice));
            $(`#itemBaseAmountSpan_${rowNo}`).text(decimalAmount(basicPrice));
            $(`#convertedItemBaseAmountSpan_${rowNo}`).text(decimalAmount(convertedBasicPrice));

            $(`#itemTotalDiscountHidden_${rowNo}`).val(decimalAmount(totalDiscount));
            $(`#itemTotalDiscount1_${rowNo}`).val(decimalAmount(totalDiscount));

            if (param === "?so_to_invoice") {
                $(`#itemTradeDiscountAmountSpan_${rowNo}`).text(decimalAmount((basicPrice * itemTradeDiscountPercentageSpan) / 100));
            } else {
                $(`#itemTradeDiscountAmountSpan_${rowNo}`).text(decimalAmount(totalDiscount));
            }

            if (baseAmountIsCheck) {
                $(`#itemCashDiscountAmountSpan_${rowNo}`).text(decimalAmount(cashDiscountAmount));
                $(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val(decimalAmount(cashDiscountAmount));
            } else {
                $(`#itemCashDiscountAmountSpan_${rowNo}`).text(decimalAmount((grossAmount * itemCashDiscountPercentageSpan) / 100));
                $(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val(decimalAmount((grossAmount * itemCashDiscountPercentageSpan) / 100));
            }

            // $(`#itemTradeDiscountAmountInp_${rowNo}`).val((basicPrice * itemTradeDiscountPercentageSpan) / 100);

            $(`#itemTotalDiscount_${rowNo}`).html(decimalAmount(totalDiscount));
            $(`#convertedItemDiscountAmountSpan_${rowNo}`).html(decimalAmount(convertedTotalDiscount));
            $(`#convertedItemCashDiscountAmountSpan_${rowNo}`).html(decimalAmount(convertedTotalCashDiscount));

            $(`#itemTotalTax1_${rowNo}`).val(decimalAmount(totalTax));
            $(`#itemTotalTax_${rowNo}`).html(decimalAmount(totalTax));
            $(`#convertedItemTaxAmountSpan_${rowNo}`).html(decimalAmount(convertedTotalTax));

            $(`#itemTotalPrice_${rowNo}`).val(decimalAmount(totalItemPrice));
            $(`#itemTotalPrice1_${rowNo}`).html(decimalAmount(totalItemPrice));
            $(`#convertedItemTotalPriceSpan_${rowNo}`).html(decimalAmount(convertedTotalItemPrice));

            $(`#totalItemAmountModal_${rowNo}`).html(decimalAmount(totalItemPrice));

            calculateGrandTotalAmount();
            roundOffCal();
        }

        window.calculateGrandTotalAmount1 = function () {
            // Function code here
            calculateGrandTotalAmount();
        };

        function calculateGrandTotalAmount() {
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
            let curr_rate = helperAmount($(`#curr_rate`).val()) || 0;
            let adjustedDueAmt = helperAmount($(`.adjustedDueAmt`).text()) || 0;
            $(`.convertedAdjustedDueAmt`).text(decimalAmount(adjustedDueAmt * curr_rate));

            // item total price
            $(".itemTotalPrice1").each(function () {
                totalAmount += helperAmount($(this).text().replace(/,/g, "")) || 0;
            });
            // alert(totalAmount);
            $(".itemTotalPrice").each(function () {
                totalAmountOriginal += helperAmount($(this).val().replace(/,/g, "")) || 0;
            });
            // alert(totalAmountOriginal);
            $(".convertedItemTotalPriceSpan").each(function () {
                convertedItemTotalPrice += helperAmount($(this).text().replace(/,/g, "")) || 0;
            });
            // alert(convertedItemTotalPrice);

            // item total tax
            $(".itemTotalTax1").each(function () {
                totalTaxAmountOriginal += helperAmount($(this).val().replace(/,/g, "")) || 0;
            });
            //alert(totalTaxAmountOriginal);
            $(".itemTotalTax").each(function () {
                totalTaxAmount += helperAmount($(this).html().replace(/,/g, "")) || 0;

            });
            //  alert(totalTaxAmount);
            $(".convertedItemTaxAmountSpan").each(function () {
                convertedItemTaxAmountSpan += helperAmount($(this).html().replace(/,/g, "")) || 0;
            });

            // item total discount
            $(".itemTotalDiscountHidden").each(function () {
                totalDiscountAmountOriginal += helperAmount($(this).val().replace(/,/g, "")) || 0;
            });
            //  alert(totalDiscountAmountOriginal);
            // alert(totalDiscountAmountOriginal);
            // $(".itemTotalDiscount").each(function() {
            //   totalDiscountAmount += helperAmount($(this).html().replace(/,/g, "")) || 0;
            // });

            $(".itemTradeDiscountAmountInp").each(function () {
                totalDiscountAmount += helperAmount($(this).val().replace(/,/g, "")) || 0;
            });

            $(".itemCashDiscountAmountHiddenInp").each(function () {
                totalCashDiscountAmount += helperAmount($(this).val().replace(/,/g, "")) || 0;
            });

            //alert(totalDiscountAmount);
            //alert(totalDiscountAmount);
            $(".convertedItemDiscountAmountSpan").each(function () {
                convertedItemDiscountAmountSpan += helperAmount($(this).html().replace(/,/g, "")) || 0;
            });
            // alert(convertedItemDiscountAmountSpan)

            // item base amount
            $(".itemBaseAmountInp").each(function () {
                itemBaseAmountInpOriginal += helperAmount($(this).val().replace(/,/g, "")) || 0;
            });
            // alert(itemBaseAmountInpOriginal);
            $(".itemBaseAmountSpan").each(function () {
                itemBaseAmountSpan += helperAmount($(this).html().replace(/,/g, "")) || 0;
            });
            //  alert(itemBaseAmountSpan);
            $(".convertedItemBaseAmountSpan").each(function () {
                convertedItemBaseAmountSpan += helperAmount($(this).html().replace(/,/g, "")) || 0;
            });
            // alert(convertedItemBaseAmountSpan);

            let compInvoiceType = $("#compInvoiceType").val();
            // alert(compInvoiceType);
            let grandTotalAmountAfterOriginal = helperAmount(totalAmountOriginal - totalTaxAmount);
            let grandTotalAmountAfter = helperAmount(totalAmount - totalTaxAmount);
            //  alert(grandTotalAmountAfter);
            let convertedGrandTotalAmountWithoutTax = helperAmount(convertedItemTotalPrice - convertedItemTaxAmountSpan);

            let grandTotalCashDiscount = helperAmount($("#grandTotalCashDiscount").text());
            let convertedGrandTotalCashDiscountAmount = helperAmount(grandTotalCashDiscount * curr_rate);


            if (compInvoiceType === "CBW" || compInvoiceType === "LUT" || compInvoiceType === "SEWOP" || compInvoiceType === "E") {
                //alert(111111111111);
                $(".grandSgstCgstAmt").html(0);
                $(".convertedGrandSgstCgstAmt").html(0);

                $("#grandTaxAmt").html(0);
                $("#convertedGrandTaxAmount").html(0);

                $("#grandTaxAmtInp").val(0);

                // $(".itemTaxPercentage").text(0);
                // $(".itemTotalTax").text(0);
                // $(".itemTotalTax1").val(0);
                // $(".convertedItemTaxAmountSpan").text(0);
                // alert('ok');
                $("#grandSubTotalAmt").html(decimalAmount(itemBaseAmountSpan));
                $("#grandSubTotalAmtInp").val(decimalAmount(itemBaseAmountInpOriginal));
                $("#convertedGrandSubTotalAmt").text(decimalAmount(convertedItemBaseAmountSpan));

                //$("#grandTotalDiscount").html(totalDiscountAmount);
                $("#grandTotalDiscount").html(decimalAmount(convertedItemDiscountAmountSpan));
                $("#grandTotalDiscountAmtInp").val(decimalAmount(totalDiscountAmountOriginal));

                $("#grandTotalCashDiscount").html(decimalAmount(totalCashDiscountAmount));
                $("#grandTotalCashDiscountAmtInp").val(decimalAmount(totalCashDiscountAmount));

                $("#convertedGrandTotalDiscountAmount").text(decimalAmount(convertedItemDiscountAmountSpan));

                $("#grandTotalAmt").html(decimalAmount(grandTotalAmountAfter));
                $("#grandTotalAmtInp").val(decimalAmount(grandTotalAmountAfter));
                $("#convertedGrandTotalAmt").text(decimalAmount(convertedGrandTotalAmountWithoutTax));


            } else {
                // start new rule book js
                var gstDetailsArray = []; // Array to hold all GST details

                $("tr.gst").each(function () {
                    var gstType = $(this).find(".totalCal").text().trim(); // Gets the text "CGST" or "SGST"
                    var taxPercentage = $(this).find("input[type='hidden']").val(); // Gets the tax percentage value

                    var grandTaxAmtId = "#grandTaxAmt_" + gstType;
                    var grandTaxAmtval = "#grandTaxAmtval_" + gstType;

                    // Calculate the tax amount
                    var taxAmount = helperAmount((totalTaxAmount * taxPercentage / 100));

                    $(grandTaxAmtId).html(decimalAmount(taxAmount));
                    $(grandTaxAmtval).val(decimalAmount(taxAmount));

                    // Add the GST details to the array
                    gstDetailsArray.push({
                        gstType: gstType,
                        taxPercentage: taxPercentage,
                        taxAmount: taxAmount
                    });
                });

                var gstDetailsJson = JSON.stringify(gstDetailsArray);
                $("input[name='gstdetails']").val(gstDetailsJson);
                console.log(gstDetailsJson);


                // new rule end 

                $("#grandTaxAmt").html(decimalAmount(totalTaxAmount));
                // alert(3);
                $("#convertedGrandTaxAmount").html(decimalAmount(convertedItemTaxAmountSpan));

                $("#convertedGrandTotalCashDiscountAmount").html(decimalAmount(convertedGrandTotalCashDiscountAmount));
                // alert(4);
                $("#grandTaxAmtInp").val(decimalAmount(totalTaxAmountOriginal));
                // alert(5);


                $("#grandSubTotalAmt").html(decimalAmount(itemBaseAmountSpan));
                // alert(6);
                $("#grandSubTotalAmtInp").val(decimalAmount(itemBaseAmountInpOriginal));
                // alert(7);
                $("#convertedGrandSubTotalAmt").text(decimalAmount(convertedItemBaseAmountSpan));
                // alert(8);

                //  $("#grandTotalDiscount").html(totalDiscountAmount);

                $("#grandTotalDiscount").html(decimalAmount(convertedItemDiscountAmountSpan));

                $("#grandTotalDiscountAmtInp").val(decimalAmount(totalDiscountAmountOriginal));

                $("#grandTotalCashDiscount").html(decimalAmount(totalCashDiscountAmount));
                $("#grandTotalCashDiscountAmtInp").val(decimalAmount(totalCashDiscountAmount));

                // alert(10);
                $("#convertedGrandTotalDiscountAmount").text(decimalAmount(convertedItemDiscountAmountSpan));
                // alert(11);

                $("#grandTotalAmt").html(decimalAmount(totalAmount));
                $("#grandTotalAmtInp").val(decimalAmount(totalAmountOriginal));
                $("#convertedGrandTotalAmt").text(decimalAmount(convertedItemTotalPrice));
            }

        }

        // currency conversion
        function currency_conversion() {
            for (elem of $(".convertedItemUnitPriceSpan")) {
                let rowNo = ($(elem).attr("id")).split("_")[1];
                let newVal = $("#curr_rate").val() * $(`#originalChangeItemUnitPriceInp_${rowNo}`).val();

                // alert($("#curr_rate").val());
                // alert($(`#originalChangeItemUnitPriceInp_${rowNo}`).val());
                newVal = newVal > 0 ? newVal : $(elem).val();

                $(elem).text(newVal);
                calculateOneItemAmounts(rowNo);
            };

            let currencyIcon = ($('.currencyDropdown').val()).split("≊")[2];
            $(".currency-symbol-dynamic").text(currencyIcon);
            calculateGrandTotalAmount();
        }

        // change dynamic currency 
        $(".currencyDropdown").on("change", function () {
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
                    beforeSend: function () {
                        $("#curr_rate").val(`Loading...`);
                        $("#curr_rate").prop('disabled', true);
                    },
                    success: function (result) {
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

        $(document).on("keyup keydown", "#curr_rate", function () {
            currency_conversion();
        });

        currency_conversion();

        // #######################################################
        function calculateQuantity(rowNo, itemId, thisVal) {
            let itemQty = (helperQuantity($(`#itemQty_${itemId}`).val()) > 0) ? helperQuantity($(`#itemQty_${itemId}`).val()) : 0;
            let totalQty = 0;
            $(".multiQuantity").each(function () {
                if ($(this).data("itemid") == itemId) {
                    totalQty += (helperQuantity($(this).val()) > 0) ? helperQuantity($(this).val()) : 0;
                }
            });
            let avlQty = helperQuantity(itemQty - totalQty);

            if (avlQty < 0) {
                let totalQty = 0;
                $(`#multiQuantity_${rowNo}`).val('');
                $(".multiQuantity").each(function () {
                    if ($(this).data("itemid") == itemId) {
                        totalQty += (helperQuantity($(this).val()) > 0) ? helperQuantity($(this).val()) : 0;
                    }
                });
                let avlQty = helperQuantity(itemQty - totalQty);

                $(`#mainQtymsg_${itemId}`).show();
                $(`#mainQtymsg_${itemId}`).html("[Error! Delivery QTY should equal to order QTY.]");
                $(`#mainQty_${itemId}`).html(avlQty);
            } else {
                let totalQty = 0;
                $(".multiQuantity").each(function () {
                    if ($(this).data("itemid") == itemId) {
                        totalQty += (helperQuantity($(this).val()) > 0) ? helperQuantity($(this).val()) : 0;
                    }
                });

                let avlQty = helperQuantity(itemQty - totalQty);

                $(`#mainQtymsg_${itemId}`).hide();
                $(`#mainQty_${itemId}`).html(decimalQuantity(avlQty));
            }
            if (avlQty == 0) {
                $(`#saveClose_${itemId}`).show();
                $(`#saveCloseLoading_${itemId}`).hide();
            } else {
                $(`#saveClose_${itemId}`).hide();
                $(`#saveCloseLoading_${itemId}`).show();
                $(`#setAvlQty_${itemId}`).html(decimalQuantity(avlQty));
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

        $(document).on("input keyup paste blur", ".inputQuantityClass", function () {
            let val = $(this).val();
            let base = <?= $decimalQuantity ?>;
            // Allow only numbers and one decimal point
            if (val.includes(".")) {
                let parts = val.split(".");
                if (parts[1].length > base) {
                    $(this).val(parts[0] + "." + parts[1].substring(0, base)); // Restrict extra decimals
                }
            }
        });

        $(document).on("input keyup paste blur", ".inputAmountClass", function () {
            let val = $(this).val();
            let base = <?= $decimalValue ?>;
            // Allow only numbers and one decimal point
            if (val.includes(".")) {
                let parts = val.split(".");
                if (parts[1].length > base) {
                    $(this).val(parts[0] + "." + parts[1].substring(0, base)); // Restrict extra decimals
                }
            }
        });

        // item qty check
        $(document).on("keyup", ".itemQty", function () {



            let rowNo = ($(this).attr("id")).split("_")[1];

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


            let itemVal = helperAmount($(`#itemQty_${rowNo}`).val()) > 0 ? helperAmount($(`#itemQty_${rowNo}`).val()) : 0;
            itemVal = Number(itemVal);
            let itemTradeDiscountAmount = helperAmount($(`#itemTradeDiscountAmountSpan_${rowNo}`).text()) || 0;
            let itemCashDiscountAmount = helperAmount($(`#itemCashDiscountAmountSpan_${rowNo}`).text()) || 0;
            let originalItemUnitPrice = helperAmount($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) || 0;
            let basicPrice = originalItemUnitPrice * itemVal;

            // Check if the corresponding "checkQty_" element exists
            if ($(`#checkQty_${rowNo}`).length > 0) {
                let checkQty = helperAmount($(`#checkQty_${rowNo}`).val()) > 0 ? helperAmount($(`#checkQty_${rowNo}`).val()) : 0;
                let splitQty = helperAmount($(`#checkQty_${rowNo}`).val()) > 0 ? helperAmount($(`#checkQty_${rowNo}`).val()) : 0;
                let soqty = $(`#itemInvQty_${rowNo}`).val();
                soqty = Number(soqty);
                splitQty = Number(splitQty);
                if (itemVal > soqty) {
                    $(`#qtyMsg1_${rowNo}`).removeAttr("style").css("display", "block").html("invoice quantity should be less or equal than so quantity");
                    $(`#itemQty_${rowNo}`).val("");
                } else {
                    $(`#qtyMsg1_${rowNo}`).css("display", "none");
                }

                if (itemVal <= splitQty) {
                    $(`#itemSpecialDiscount_${rowNo}`).text(`Special Discount`);
                    $(`#qtyMsg_${rowNo}`).hide();
                } else {
                    $(`#itemQty_${rowNo}`).val("");
                    $(`#qtyMsg_${rowNo}`).show();
                }
            } else { }
            calculateOneItemAmounts(rowNo);
        });

        function validDate(soDate, soValidDate, invoicedate) {
            if (invoicedate < soDate) {
                $("#invdatelabel").html(`<p class="text-danger text-xs" id="invdatelabel">Invoice creation Date should not be less than sales order date</p>`);
                document.getElementById("directInvoiceCreationBtn").disabled = true;
            } else if (invoicedate > soValidDate) {
                $("#invdatelabel").html(`<p class="text-danger text-xs" id="invdatelabel">Invoice creation Date should not be greater than so validity date</p>`);
                document.getElementById("directInvoiceCreationBtn").disabled = true;
            } else {
                $("#invdatelabel").html(`<p class="text-danger text-xs" id="invdatelabel"></p>`);
                document.getElementById("directInvoiceCreationBtn").disabled = false;
            }
        }

        $("#invoiceDate").on("change", function (e) {

            let url = window.location.search;
            let param = url.split("=")[0];

            var invoicedate = $(this).val();
            let soDate = $("#SoDate").val();
            let soValidDate = $("#SoValidDate").val();
            if (soDate && soValidDate) {
                validDate(soDate, soValidDate, invoicedate);
            }
            var rowData = {};
            let flag = 0;

            $(".itemRow").each(function () {

                let itemType = $(this).attr("goodsType");
                let goodsType = $(this).data('id');
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
                        type: param,
                        invoiceDate: invoicedate,
                        itemId: itemId,
                        randCode: rowId
                    },
                    beforeSend: function () { },
                    success: function (response) {
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
                    });


                    $.ajax({
                        type: "POST",
                        url: `ajaxs/so/ajax-items-stock-check.php`,
                        data: {
                            act: "itemStockCheck",
                            type: param,
                            invoicedate: invoicedate,
                            rowData: StringRowData
                        },
                        beforeSend: function () {
                            $(".tableDataBody").html(`<option value="">Loading...</option>`);
                        },
                        success: function (response) {
                            let data = JSON.parse(response);
                            let itemData = data.data;
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

        $(document).on("keyup blur", ".originalChangeItemUnitPriceInp", function () {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
            currency_conversion();
        });



        $("#compInvoiceType").on("change", function () {
            let compInvoiceType = $(this).val();

            for (elem of $(".itemTotalTax")) {
                let rowNo = ($(elem).attr("id")).split("_")[1];

                let itemTaxBkup = $(`#itemTaxBkup_${rowNo}`).val();

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

        $("#directInvoiceCreationBtn").on("click", function (event) {
            var isChecked = $('#round_off_checkbox').prop('checked');
            let isValidQuantity = true;
            let isValidunitPrice = true;

            $(".itemQty").each(function () {
                let qty = $(this).val();
                if (qty <= 0 || qty === "") {
                    isValidQuantity = false;
                    alert("Please enter valid quantity");
                    return false;
                }
            });

            $(".originalChangeItemUnitPriceInp").each(function () {
                let price = $(this).val();
                if (price <= 0 || price === "") {
                    isValidunitPrice = false;
                    alert("Please enter unit price");
                    return false;
                }
            });

            if (!isValidQuantity) {
                event.preventDefault();
                return;
            }

            if (!isValidunitPrice) {
                event.preventDefault();
                return;
            }

            if (isChecked) {
                $("#round_off_checkbox").val(1);
            } else {
                $("#round_off_checkbox").val(0);
            }
        });

        $("#round_off_checkbox").on("change", function () {
            roundOffCal();
        });

        function roundOffCal() {
            let grandTotalAmt = helperAmount($("#grandTotalAmt").text());
            $(".adjustedDueAmt").text(decimalAmount(grandTotalAmt));
            $(".adjustedCollectAmountInp").val(decimalAmount(grandTotalAmt));
        }


        $("#goodsType").on("change", function () {
            let goodsType = $(this).val();

            if (goodsType === "service") {
                $(".recurringDiv").show();


                $("#orderForService").prop("checked", true);

                let orderForRadio = '';

            } else if (goodsType === "project") {
                $(".recurringDiv").hide();
            } else {
                $("#orderForService").prop("checked", false);
                $(".orderFor").hide();
                $(".recurringDiv").hide();
                $(".fob-section").show();
            }

            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-items-goods-type.php`,
                data: {
                    act: "goodsType",
                    goodsType
                },
                beforeSend: function () {
                    $("#itemsDropDown").html(`<option>Loading...</option>`);
                },
                success: function (response) {
                    $("#itemsDropDown").html(response);
                }
            });
            // alert("444");
        });

        $("#neverExpire").on('click', function () {
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

            $(".itemDiscount").each(function () {
                let rowNum = ($(this).attr("id")).split("_")[1];
                let discountPercentage = helperAmount($(this).val());
                discountPercentage = discountPercentage > 0 ? discountPercentage : 0;
                let maxDiscountPercentage = helperAmount($(`#itemMaxDiscount_${rowNum}`).html());
                maxDiscountPercentage = maxDiscountPercentage > 0 ? maxDiscountPercentage : 0;
                if (discountPercentage > maxDiscountPercentage) {
                    isSpecialDiscountApplied = true;
                }
            });

            if (isSpecialDiscountApplied) {
                $(`#approvalStatus`).val(`12`);
            } else {
                $(`#approvalStatus`).val(`14`);
            }
        }

        function cashDiscountFunc(rowNo, keyValue) {
            let baseAmountIsCheck = $(`#baseAmountRadio_${rowNo}`).is(":checked");
            let itemBaseAmount = helperAmount($(`#itemBaseAmountInp_${rowNo}`).val());
            let itemQty = helperQuantity($(`#itemQty_${rowNo}`).val());
            let unitPrice = helperAmount($(`#originalChangeItemUnitPriceInp_${rowNo}`).val());
            let itemGrossAmount = helperAmount($(`#itemGrossAmountSpan_${rowNo}`).text()) || 0;
            let discountAmt = helperAmount(itemGrossAmount) * helperAmount(keyValue) / 100;

            if (baseAmountIsCheck) {

                discountAmt = helperAmount(itemBaseAmount) * helperAmount(keyValue) / 100;
            }

            $(`#itemTotalCashDiscount1_${rowNo}`).val(decimalAmount(discountAmt));

            $(`#itemCashDiscountPercentageInp_${rowNo}`).val(`${keyValue}`);
            $(`#itemCashDiscountPercentageSpan_${rowNo}`).text(`${keyValue}`);
            $(`#itemCashDiscountAmountSpan_${rowNo}`).text(decimalAmount(discountAmt));
            $(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val(decimalAmount(discountAmt));
            $(`#itemTaxableAmountSpan_${rowNo}`).text(decimalAmount(itemGrossAmount - discountAmt));
        }

        $(document).on("keyup", ".itemCashDiscount", function () {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let keyValue = $(this).val();

            cashDiscountFunc(rowNo, keyValue);
            calculateOneItemAmounts(rowNo);
            checkSpecialDiscount();
        });



        function cashDiscountTotalFunc(rowNo, keyValue) {
            let baseAmountIsCheck = $(`#baseAmountRadio_${rowNo}`).is(":checked");
            let itemBaseAmount = helperAmount($(`#itemBaseAmountInp_${rowNo}`).val());
            let itemQty = helperQuantity($(`#itemQty_${rowNo}`).val());
            let unitPrice = helperAmount($(`#originalChangeItemUnitPriceInp_${rowNo}`).val());
            let itemGrossAmount = helperAmount($(`#itemGrossAmountSpan_${rowNo}`).text()) || 0;

            let discPercentage = (100 * helperAmount(keyValue)) / helperAmount(itemGrossAmount);
            if (baseAmountIsCheck) {
                discPercentage = (100 * helperAmount(keyValue)) / helperAmount(itemBaseAmount);
            }

            $(`#itemCashDiscount_${rowNo}`).val(decimalQuantity(discPercentage));
            $(`#itemCashDiscountPercentageInp_${rowNo}`).val(`${decimalQuantity(discPercentage)}`);
            $(`#itemCashDiscountPercentageSpan_${rowNo}`).text(`${decimalQuantity(discPercentage)}`);
            $(`#itemCashDiscountAmountSpan_${rowNo}`).text(decimalAmount(keyValue));
            $(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val(decimalAmount(keyValue));
            $(`#itemTaxableAmountSpan_${rowNo}`).text(decimalAmount(itemGrossAmount - keyValue));
        }

        $(document).on("keyup", ".itemTotalCashDiscount1", function () {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let keyValue = $(this).val();
            cashDiscountTotalFunc(rowNo, keyValue);
            calculateOneItemAmounts(rowNo);
            checkSpecialDiscount();
        });

        $(document).on("change", ".grossAmountRadio", function () {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let keyValue = helperAmount($(`#itemCashDiscount_${rowNo}`).val())
            cashDiscountFunc(rowNo, keyValue);
            calculateOneItemAmounts(rowNo);
            checkSpecialDiscount();
        });

        $(document).on("change", ".baseAmountRadio", function () {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let keyValue = helperAmount($(`#itemCashDiscount_${rowNo}`).val());
            cashDiscountFunc(rowNo, keyValue);
            calculateOneItemAmounts(rowNo);
            checkSpecialDiscount();
        });

        $(document).on("change", ".cashDiscountTypeRadioBtn", function () {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let discountType = $(this).data("cash-discount-type");
            $(`#selectedCashDiscountType_${rowNo}`).val(discountType);
        });

        $(document).on("keyup", ".itemDiscount", function () {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let keyValue = $(this).val();

            if (keyValue < 0) {
                $(this).val(0);
            }

            if (keyValue > 100) {
                $(this).val(100);
            }

            $(`#itemTradeDiscountPercentageInp_${rowNo}`).val(decimalQuantity(keyValue));
            $(`#itemTradeDiscountPercentageSpan_${rowNo}`).text(decimalQuantity(keyValue));

            calculateOneItemAmounts(rowNo);
            itemMaxDiscount(rowNo, keyValue);
            checkSpecialDiscount();
        });

        $(document).on("keyup blur click change", ".multiQuantity", function () {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let itemid = ($(this).data("itemid"));
            let thisVal = ($(this).val());
            calculateQuantity(rowNo, itemid, thisVal);
        });

        $(document).on("blur", ".itemTotalDiscount1", function () {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let itemDiscountAmt = helperAmount($(this).val());

            let itemQty = (helperQuantity($(`#itemQty_${rowNo}`).val()) > 0) ? helperQuantity($(`#itemQty_${rowNo}`).val()) : 0;
            let originalItemUnitPrice = (helperAmount($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) > 0) ? helperAmount($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) : 0;

            let totalAmt = helperAmount(itemQty * originalItemUnitPrice);
            let discountPercentage = helperAmount(itemDiscountAmt * 100 / totalAmt);

            $(`#itemDiscount_${rowNo}`).val(decimalQuantity(discountPercentage));

            calculateOneItemAmounts(rowNo);
        });

        $("#allItemsBtn").on('click', function () {
            window.location.href = "";
        })

        $("#itemWiseSearch").on('click', function () {
            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-so-list.php`,
                data: {
                    act: "itemWiseSearch"
                },
                beforeSend: function () {
                    $(".tableDataBody").html(`<option value="">Loading...</option>`);
                },
                success: function (response) {
                    $(".tableDataBody").html(response);
                }
            });
        })

        $(function () {
            $("#datepicker").datepicker({
                autoclose: true,
                todayHighlight: true
            }).datepicker('update', new Date());
        });


        $(document).on("click", ".itemreleasetypeclass", function () {
            let itemreleasetype = $(this).val();
            var rdcode = $(this).data("rdcode");
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

        $(document).on("keyup paste keydown", ".enterQty", function () {
            let enterQty = $(this).val();
            var rdcodeSt = $(this).data("rdcode");
            var maxqty = $(this).data("maxval");
            let rdatrr = [];
            rdatrr = rdcodeSt.split("|");
            let rdcode = rdatrr[0];
            let rdBatch = rdatrr[1];

            if (enterQty <= maxqty) {
                if (enterQty > 0) {
                    totalquentity(rdcodeSt);
                    $('.batchCheckbox' + rdBatch).prop('checked', true);
                } else {
                    $(this).val('');
                    totalquentity(rdcodeSt);
                    $('.batchCheckbox' + rdBatch).prop('checked', false);
                }
            } else {
                $(this).val('');
                totalquentity(rdcodeSt);
            }
        });

        function totalquentitydiscut(rdcode) {

            $(".qty" + rdcode).each(function () {
                $(this).val('');
            });
            $("#itemSelectTotalQty_" + rdcode).html(0);
            $("#itemQty_" + rdcode).val(0);
            $('.batchCbox').prop('checked', false);
        }

        function totalquentity(rdcodeSt) {
            let rdatrr = [];
            rdatrr = rdcodeSt.split("|");
            let rdcode = rdatrr[0];
            let rdBatch = rdatrr[1];
            var sum = 0;

            $(".qty" + rdcode).each(function () {
                var value = helperQuantity($(this).val()) || 0;
                sum += value;
            });
            $("#itemSelectTotalQty_" + rdcode).html(sum);
            $("#itemQty_" + rdcode).val(sum);
            calculateOneItemAmounts(rdcode);
        }

        $(document).on("click", "#btnSearchCollpase", function () {
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

    });

    $('.hamburger').click(function () {
        $('.hamburger').toggleClass('show');
        $('#overlay').toggleClass('show');
        $('.nav-action').toggleClass('show');
    });

    $(`#terms-and-condition`)
        .select2()
        .on('select2:open', () => { });

    $('#itemsDropDown')
        .select2()
        .on('select2:open', () => { });
    $('.currencyDropdown')
        .select2()
        .on('select2:open', () => { });


    $('#customerDropDown').select2({
        placeholder: 'Select Customer',
        ajax: {
            url: 'ajaxs/so/ajax-customerslst-select2.php',
            dataType: 'json',
            delay: 50,
            data: function (params) {
                return {
                    searchTerm: params.term // search term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    }).on('select2:open', function (e) {
        var $results = $(e.target).data('select2').$dropdown.find('.select2-results');

        if (!$results.find('a').length) {
            $results.append(`<div class="btn-row"><a type="button" class="btn btn-primary addCust add-btn" data-toggle="modal" data-target="#addNewCustomerModal">Add New</a></div>`);
        }
    });

      $('#configContact').select2({
    placeholder: 'Select Contact',
    ajax: {
      url: 'ajaxs/so/ajax-contacts-select.php',
      dataType: 'json',
      delay: 50,
      data: function (params) {
        return {
          searchTerm: params.term // search term
        };
      },
      processResults: function (data) {
        return {
          results: data
        };
      },
      cache: true
    }
  }).on('select2:open', function (e) {
    var $results = $(e.target).data('select2').$dropdown.find('.select2-results');
    
  });

    $('#profitCenterDropDown')
        .select2()
        .on('select2:open', () => { });
    $('#servicesDropDown')
        .select2()
        .on('select2:open', () => { });
    $('#kamDropDown')
        .select2()
        .on('select2:open', () => { });
    $('#placeOfSupply1')
        .select2()
        .on('select2:open', () => { });



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

    const removeClasses = (elemSet, className) => {

        elemSet.forEach(elem => {

            elem.classList.remove(className);

        });

    };

    const findParent = (elem, parentClass) => {

        let currentNode = elem;

        while (!currentNode.classList.contains(parentClass)) {
            currentNode = currentNode.parentNode;
        }

        return currentNode;

    };

    const getActiveStep = elem => {
        return Array.from(DOMstrings.stepsBtns).indexOf(elem);
    };

    const setActiveStep = activeStepNum => {

        removeClasses(DOMstrings.stepsBtns, 'js-active');

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

    $(document).on("click", ".add_data", function () {
        var data = this.value;
        $("#createdatamultiform").val(data);
        // confirm('Are you sure to Submit?')
        $("#add_frm").submit();
    });

    // tcs hide show function
    $("#tcsAmtshowhidediv").hide();
    $(document).on('change', '.tcscheckbox', function () {
        if (this.checked) {
            $("#tcsAmtshowhidediv").show();
        } else {
            $("#tcsAmtshowhidediv").hide();
        }
    });

    $(document).ready(function () {
        $('.previewBtn').click(function () {
            var selectedValue = $('#terms-and-condition').val();
            $.ajax({
                url: 'ajaxs/so/ajax-tc.php', // Replace with your API endpoint or server URL
                type: 'GET',
                data: {
                    value: selectedValue, // Send the selected value to the server
                    act: "tc"
                },

                success: function (response) {
                    let obj = JSON.parse(response);
                    $('.tc-modal-title').html(obj['termHead']);
                    $('.tc-modal-body').html(obj['termscond']);
                },
                error: function (error) {
                    $('#modalBody').html('An error occurred while fetching the data.');
                }
            });
        });
    });
</script>

<!-- -------------------   Total old scripts of direct create end   ------------------>

<script src="<?= BASE_URL; ?>public/validations/soValidation.js?v=1"></script>

<script src="https://code.getmdl.io/1.2.0/material.min.js"></script>