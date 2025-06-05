$(document).ready(function () {
	$(document).on('click', '#addNewGrnFormSubmitBtn', function (e) {
		let validStatus = 0;
		let specStatus = 0;

		// POSTING DATE VALIDATION
		if ($("[name='invoicePostingDate']").val() == '') {
			$('.pending_grn_postingDate').remove();
			$("[name='invoicePostingDate']")
				.parent()
				.append(
					'<span class="error pending_grn_postingDate">Posting Date is required</span>'
				);
			$('.pending_grn_postingDate').show();

			$('.notespostingDate').remove();
			$('#notesModalBody').append(
				'<p class="notespostingDate font-monospace text-danger">Posting Date is required</p>'
			);
		} else {
			$('.pending_grn_postingDate').remove();

			$('.notespostingDate').remove();
			validStatus++;
		}

		// DUE DATE VALIDATION
		if ($('#iv_due_date').val() == '') {
			$('.pending_grn_iv_due_date').remove();
			$('#iv_due_date')
				.parent()
				.append(
					'<span class="error pending_grn_iv_due_date">Due Date is required</span>'
				);
			$('.pending_grn_iv_due_date').show();

			$('.notesiv_due_date').remove();
			$('#notesModalBody').append(
				'<p class="notesiv_due_date font-monospace text-danger">Due Date is required</p>'
			);
		} else {
			$('.pending_grn_iv_due_date').remove();

			$('.notesiv_due_date').remove();
			validStatus++;
		}

		// PO NO VALIDATION
		// if ($("#invoicePoNumber").val() == "") {
		//   $(".pending_grn_invoicePoNumber").remove();
		//   $("#invoicePoNumber")
		//     .parent()
		//     .append(
		//       '<span class="error pending_grn_invoicePoNumber">PO Number is Empty</span>'
		//     );
		//   $(".pending_grn_invoicePoNumber").show();

		//   $(".notesinvoicePoNumber").remove();
		//   $("#notesModalBody").append(
		//     '<p class="notesinvoicePoNumber font-monospace text-danger">PO Number is Empty</p>'
		//   );
		// } else {
		//   $(".pending_grn_invoicePoNumber").remove();

		//   $(".notesinvoicePoNumber").remove();
		//   validStatus++;
		// }

		for (elem of $('.received_quantity').get()) {
			let element = elem.getAttribute('id').split('_')[1];

			// STORAGE LOCATION VALIDATION
			if ($(`#itemStorageLocationId_${element}`).val() == '') {
				$(`.itemStorageLocationId_${element}`).remove();
				$(`#itemStorageLocationId_${element}`)
					.parent()
					.append(
						`<span class="error itemStorageLocationId_${element}">St.Loc / Cost Center is required</span>`
					);
				$(`.itemStorageLocationId_${element}`).show();

				$(`.notesitemStorageLocationId_${element}`).remove();
				$('#notesModalBody').append(
					`<p class="notesitemStorageLocationId_${element} font-monospace text-danger">Storage Location is required for Line No. ${element}</p>`
				);
			} else {
				$(`.itemStorageLocationId_${element}`).remove();

				$(`.notesitemStorageLocationId_${element}`).remove();
				specStatus++;
			}

			// DERIVED QUANTITY VALIDATION
			if (
				$(`#itemStockQty_${element}`).val() == '' ||
				$(`#itemStockQty_${element}`).val() == '0'
			) {
				$(`.itemStockQty_${element}`).remove();
				$(`#itemStockQty_${element}`)
					.parent()
					.append(
						`<span class="error itemStockQty_${element}">Derived Quantity is required</span>`
					);
				$(`.itemStockQty_${element}`).show();

				$(`.notesitemStockQty_${element}`).remove();
				$('#notesModalBody').append(
					`<p class="notesitemStockQty_${element} font-monospace text-danger">Derived Quantity is required for Line No. ${element}</p>`
				);
			} else {
				$(`.itemStockQty_${element}`).remove();

				$(`.notesitemStockQty_${element}`).remove();
				specStatus++;
			}

			// RECEIVED QUANTITY VALIDATION
			if (
				$(`#grnItemReceivedQtyTdInput_${element}`).val() == '' ||
				$(`#grnItemReceivedQtyTdInput_${element}`).val() == '0'
			) {
				$(`.grnItemReceivedQtyTdInput_${element}`).remove();
				$(`#grnItemReceivedQtyTdInput_${element}`)
					.parent()
					.append(
						`<span class="error grnItemReceivedQtyTdInput_${element}">Received Quantity is required</span>`
					);
				$(`.grnItemReceivedQtyTdInput_${element}`).show();

				$(`.notesgrnItemReceivedQtyTdInput_${element}`).remove();
				$('#notesModalBody').append(
					`<p class="notesgrnItemReceivedQtyTdInput_${element} font-monospace text-danger">Received Quantity is required for Line No. ${element}</p>`
				);
			} else {
				$(`.grnItemReceivedQtyTdInput_${element}`).remove();

				$(`.notesgrnItemReceivedQtyTdInput_${element}`).remove();
				specStatus++;
			}

			// UNIT PRICE VALIDATION
			if (
				$(`#grnItemUnitPriceTdInput_${element}`).val() == '' ||
				$(`#grnItemUnitPriceTdInput_${element}`).val() == '0'
			) {
				$(`.grnItemUnitPriceTdInput_${element}`).remove();
				$(`#grnItemUnitPriceTdInput_${element}`)
					.parent()
					.append(
						`<span class="error grnItemUnitPriceTdInput_${element}">Unit Price is required</span>`
					);
				$(`.grnItemUnitPriceTdInput_${element}`).show();

				$(`.notesgrnItemUnitPriceTdInput_${element}`).remove();
				$('#notesModalBody').append(
					`<p class="notesgrnItemUnitPriceTdInput_${element} font-monospace text-danger">Unit Price is required for Line No. ${element}</p>`
				);
			} else {
				$(`.grnItemUnitPriceTdInput_${element}`).remove();

				$(`.notesgrnItemUnitPriceTdInput_${element}`).remove();
				specStatus++;
			}
		}

		if (validStatus !== 2) {
			e.preventDefault();
			$('#examplePendingGrnModal').modal('show');
		}

		if (specStatus !== $('.received_quantity').length * 4) {
			e.preventDefault();
			$('#examplePendingGrnModal').modal('show');
		}
	});
});
