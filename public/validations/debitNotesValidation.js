$(document).ready(function () {
	$('#addNewCreditNoteFormSubmitBtn').click(function (e) {
		e.preventDefault();
		clearValidationMessages();
		let errorMessages = [];

		function addToErrorMessages(message) {
			errorMessages.push(message);
		}

		function displayValidationMessage(message, element) {
			let validationMessage = `<p class="validation-message" style="color: red;">${message}</p>`;
			element.parent().append(validationMessage); // Append below the input box
			element.addClass('error-field');
			$('#errorMessage').append(validationMessage);
		}

		function displayModalValidationMessage(message) {
			let modalValidationMessage = `<p class="modal-validation-message" style="color: red;">${message}</p>`;
			$('#modalErrors').append(modalValidationMessage);
		}

		function validateForm() {
			let isValid = true;

			function checkField(value, errorMessage, element) {
				if (!value) {
					addToErrorMessages(errorMessage);
					isValid = false;
					displayValidationMessage(errorMessage, element);
					displayModalValidationMessage(errorMessage);
				}
			}

			let customerClass = $('.customerClass');
			let vendorClass = $('.vendorClass');
			let vendorCustomer = $('#vendor_customer').val();
			let supplyAddress = $('#supplyAddress').val();
			let destinationAddress = $('#destinationAddress').val();
			let crCode = $('#iv_varient').val();
			let partyDebitDate = $('#partyDebitDate').val();
			let reasons = $('#reasons').val();

			let customerChecked = customerClass.is(':checked');
			let vendorChecked = vendorClass.is(':checked');

			addToErrorMessages('Please fix the following errors:');

			checkField(
				customerChecked || vendorChecked,
				'Please select either customer or vendor.',
				customerClass
			);
			checkField(
				vendorCustomer,
				'Please select party name.',
				$('#vendor_customer')
			);
			// checkField(
			// 	supplyAddress,
			// 	'Please select source of supply.',
			// 	$('#supplyAddress')
			// );
			// checkField(
			// 	destinationAddress,
			// 	'Please select destination of supply.',
			// 	$('#destinationAddress')
			// );
			checkField(crCode, 'Please enter Debit no.', $('#cr_code'));
			checkField(
				partyDebitDate,
				'Please enter Party Debit date.',
				$('#partyDebitDate')
			);
			checkField(reasons, 'Please select reasons.', $('#reasons'));

			// ... (existing validation code)

			return isValid;
		}

		// $('.item_select').each(function () {
		// 	let value = $(this).val();
		// 	if (value === '0') {
		// 		addToErrorMessages('Please select an item.');
		// 		displayValidationMessage('Please select an item.', $(this));
		// 		displayModalValidationMessage('Please select an item.');
		// 	}
		// });

		$('.gl_select').each(function () {
			let value = $(this).val();
			if (value === 'Select Account') {
				addToErrorMessages('Please select an account.');
				displayValidationMessage('Please select an account.', $(this));
				displayModalValidationMessage('Please select an account.');
			}
		});

		$('.itemQty').each(function () {
			let value = $(this).val();
			if (!value) {
				addToErrorMessages('Quantity is required.');
				displayValidationMessage('Quantity is required.', $(this));
				displayModalValidationMessage('Quantity is required.');
			}
		});

		$('.price').each(function () {
			let value = $(this).val();
			if (!value) {
				addToErrorMessages('Price is required.');
				displayValidationMessage('Price is required.', $(this));
				displayModalValidationMessage('Price is required.');
			}
		});
		// $('.tax').each(function () {
		// 	let value = $(this).val();
		// 	if (!value) {
		// 		addToErrorMessages('tax is required.');
		// 		displayValidationMessage('tax is required.', $(this));
		// 		displayModalValidationMessage('tax is required.');
		// 	}
		// });

		function clearValidationMessages() {
			$('.validation-message').remove();
			$('.formItemErrors').remove();
			$('.modal-validation-message').remove();
		}

		console.log(errorMessages, 'errorMessages');

		if (validateForm() && errorMessages.length === 1) {
			// Submit the form or perform additional actions here
			// $('#yourFormId').submit();
			showLoader();
			disableButton();
		} else {
			// Display the modal only if there are error messages
			$('#validateModal').modal('show');
		}
		function showLoader() {
			// Add your code to display a loader
			// For example, you might show a spinner or change the button text
			$('#addNewCreditNoteFormSubmitBtn').html(
				'<i class="fa fa-spinner fa-spin"></i> Loading...'
			);
			// You can customize the loader appearance based on your needs
		}
		function disableButton() {
			// Disable the button
			$('#addNewCreditNoteFormSubmitBtn').prop('disabled', true);
			$('#addNewCreditNoteFormSubmitBtn').css('font-size', '0.75rem');
			$('#drnote').submit();
		}
	});
});
