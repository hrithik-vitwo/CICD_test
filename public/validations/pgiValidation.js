$(document).ready(function () {

    $(document).on("click", "#pgibtn", function(e) {
        let validStatus = 0;
        let isValidQty = true;
    
        // PGI DATE VALIDATION
        if ($("#pgiDate").val() == "") {
            $(".pgi_pgiDate").remove();
            $("#pgiDate").parent().append('<span class="error pgi_pgiDate">PGI Posting Date is required</span>');
            $(".pgi_pgiDate").show();
    
            $(".notespgiDate").remove();
            $("#notesModalBody").append('<p class="notespgiDate font-monospace text-danger">PGI Posting Date is required</p>');
        } else if ($("#itemsTable").children("tr").length === 0) {
            $(".pgi_itemsTable").remove();
            $("#itemsTable").parent().append('<span class="error pgi_itemsTable">Please add at least one item</span>');
    
            $(".notesItemsTable").remove();
            $("#notesModalBody").append('<p class="notesItemsTable font-monospace text-danger">At least one item is required</p>');
        } else {
            $(".pgi_pgiDate, .notespgiDate, .notesItemsTable, .pgi_itemsTable").remove();
            validStatus++;
        }
    
        // ITEM QUANTITY VALIDATION
        $(".itemQty").each(function() {
            let row = ($(this).attr("id")).split("_")[1];
    
            let qty = Number($(`#itemQty_${row}`).val());
            let stock = Number($(`.sumOfBatches_${row}`).val());
    
            qty = Number(qty.toFixed(6));
            stock = Number(stock.toFixed(6));
    
            console.log(qty + '<=' + stock);
    
            if (stock < qty) {
                isValidQty = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Stock not enough for this item!!'
                });
                return false; // break loop
            }
        });
    
        // FINAL CHECK
        if (validStatus !== 1 || !isValidQty) {
            e.preventDefault();
            if (validStatus !== 1) {
                $("#exampleModal").modal("show");
            }
        }
    });
    

});