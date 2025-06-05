// loadItems
export function loadItems() {
    let value = $('#goodsType').val();
    let searchUrl = window.location.search;

    goodsType = (value != null && value != undefined) ? value : (searchUrl === "?create_service_invoice" ? 'service' : 'material');

    $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-items-goods-type.php`,
        beforeSend: function () {
            $("#itemsDropDown").html(`<option value="">Loding...</option>`);
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

// loadCustomers
export function loadCustomers() {
    $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-customers.php`,
        data: {
            customerId: '<?= $customerId ?>'
        },
        beforeSend: function () {
            $("#customerDropDown").html(`<option value="">Loding...</option>`);
        },
        success: function (response) {
            $("#customerDropDown").html(response);
        }
    });
}