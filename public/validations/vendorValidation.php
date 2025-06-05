<?php
require_once("./../../app/v1/connection-branch-admin.php");
$companyCountry = $_SESSION["logedBranchAdminInfo"]["companyCountry"];
?>

<script>
	var regPan = /[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
	var regEmail =
		/^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;


	function checkCompany(comp_1, comp_2) {
		for (obj of comp_1.split(' ')) {
			comp_1 = comp_1
				.replace('.', '')
				.replace('private', 'pvt')
				.replace('limited', 'ltd')
				.replace('organization', 'org')
				.replace('organisation', 'org');
		}
		for (obj of comp_2.split(' ')) {
			comp_2 = comp_2
				.replace('.', '')
				.replace('private', 'pvt')
				.replace('limited', 'ltd')
				.replace('organization', 'org')
				.replace('organisation', 'org');
		}

		if (comp_1 === comp_2) {
			return true;
		} else {
			return false;
		}
	}

	$(document).ready(function() {
		$(document).on('submit', '#add_frm', function(e) {
			let country_id = '<?php echo $companyCountry ?>';
			let validStatus = 0;
			let altPh = 1;
			let altEmail = 1;

			//console.log(companyCountry);
			if ($('#account_holder').val() != $('#trade_name').val()) {

				$('.notesBankAccountHolder_mis').remove();
				$('#notesModalBody').append(
					'<p class="notesBankAccountHolder_mis font-monospace text-danger">Bank acc holder is mismatched Testing</p>'
				);
			} else {
				//alert(2);
				$('.notesBankAccountHolder_mis').remove();
				validStatus++;
			}
			// PAN VALIDATION
			if ($('#vendor_pan').val() == '') {
				$('.vndr_pan').remove();
				$('#vendor_pan')
					.parent()
					.append('<span class="error vndr_pan">Pan/TFN is required</span>');
				$('.vndr_pan').show();

				$('.notesPan').remove();
				$('#notesModalBody').append(
					'<p class="notesPan font-monospace text-danger">Pan/TFN is required</p>'
				);
			} else {
				if (country_id == 103) {
					if (regPan.test($('#vendor_pan').val().toUpperCase())) {
						$('.vndr_pan').remove();

						$('.notesPan').remove();
						validStatus++;
					} else {
						$('.vndr_pan').remove();
						$('#vendor_pan')
							.parent()
							.append(
								'<span class="error vndr_pan">Check your pan</span>'
							);
						$('.vndr_pan').show();

						$('.notesPan').remove();
						$('#notesModalBody').append(
							'<p class="notesPan font-monospace text-danger">Check your pan</p>'
						);
					}
				}
			}


			// TRADE NAME VALIDATION
			if ($('#trade_name').val() == '') {
				$('.vndr_trade_name').remove();
				$('#trade_name')
					.parent()
					.append(
						'<span class="error vndr_trade_name">vendor name is required</span>'
					);
				$('.vndr_trade_name').show();

				$('.notesTradeName').remove();
				$('#notesModalBody').append(
					'<p class="notesTradeName font-monospace text-danger"> Vendor name is required</p>'
				);
			} else {
				$('.vndr_trade_name').remove();

				$('.notesTradeName').remove();
				validStatus++;
			}

			// LEGAL NAME VALIDATION
			if ($('#legal_name').val() == '') {
				$('.vndr_legal_name').remove();
				$('#legal_name')
					.parent()
					.append(
						'<span class="error vndr_legal_name">Legal Name name is required</span>'
					);
				$('.vndr_legal_name').show();

				$('.noteslegal_name').remove();
				$('#notesModalBody').append(
					'<p class="noteslegal_name font-monospace text-danger"> Legal Name is required</p>'
				);
			} else {
				$('.vndr_legal_name').remove();

				$('.noteslegal_name').remove();
				validStatus++;
			}

			// CONST OF BUSINESS VALIDATION
			if ($('#con_business').val() == '') {
				$('.vndr_con_business').remove();
				$('#con_business')
					.parent()
					.append(
						'<span class="error vndr_con_business">Constitution of business is required</span>'
					);
				$('.vndr_con_business').show();

				$('.notesConstBusiness').remove();
				$('#notesModalBody').append(
					'<p class="notesConstBusiness font-monospace text-danger">Constitution of business is required</p>'
				);
			} else {
				$('.vndr_con_business').remove();

				$('.notesConstBusiness').remove();
				validStatus++;
			}

			// STATE VALIDATION
			if ($('#state').val() == '') {
				$('.vndr_state').remove();
				$('#state')
					.parent()
					.append(
						'<span class="error vndr_state">State is required</span>'
					);
				$('.vndr_state').show();

				$('.notesState').remove();
				$('#notesModalBody').append(
					'<p class="notesState font-monospace text-danger">State is required</p>'
				);
			} else {
				$('.vndr_state').remove();

				$('.notesState').remove();
				validStatus++;
			}

			// CITY VALIDATION
			if ($('#city').val() == '') {
				$('.vndr_city').remove();
				$('#city')
					.parent()
					.append(
						'<span class="error vndr_city">City is required</span>'
					);
				$('.vndr_city').show();

				$('.notesCity').remove();
				$('#notesModalBody').append(
					'<p class="notesCity font-monospace text-danger">City is required</p>'
				);
			} else {
				$('.vndr_city').remove();

				$('.notesCity').remove();
				validStatus++;
			}

			// DISTRICT VALIDATION
			if ($('#district').val() == '') {
				$('.vndr_district').remove();
				$('#district')
					.parent()
					.append(
						'<span class="error vndr_district">District is required</span>'
					);
				$('.vndr_district').show();

				$('.notesDistrict').remove();
				$('#notesModalBody').append(
					'<p class="notesDistrict font-monospace text-danger">District is required</p>'
				);
			} else {
				$('.vndr_district').remove();

				$('.notesDistrict').remove();
				validStatus++;
			}

			// LOCATION VALIDATION
			if ($('#location').val() == '') {
				$('.vndr_location').remove();
				$('#location')
					.parent()
					.append(
						'<span class="error vndr_location">Location is required</span>'
					);
				$('.vndr_location').show();

				$('.notesLocation').remove();
				$('#notesModalBody').append(
					'<p class="notesLocation font-monospace text-danger">Location is required</p>'
				);
			} else {
				$('.vndr_location').remove();

				$('.notesLocation').remove();
				validStatus++;
			}

			// BUILIDING NO VALIDATION
			if ($('#build_no').val() == '') {
				$('.cstmr_build_no').remove();
				$('#build_no')
					.parent()
					.append(
						'<span class="error cstmr_build_no">Building No. is required</span>'
					);
				$('.cstmr_build_no').show();

				$('.notesbuild_no').remove();
				$('#notesModalBody').append(
					'<p class="notesbuild_no font-monospace text-danger">Building No. is required</p>'
				);
			} else {
				$('.cstmr_build_no').remove();

				$('.notesbuild_no').remove();
				validStatus++;
			}

			// STREET NAME VALIDATION
			if ($('#street_name').val() == '') {
				$('.cstmr_street_name').remove();
				$('#street_name')
					.parent()
					.append(
						'<span class="error cstmr_street_name">Street Name is required</span>'
					);
				$('.cstmr_street_name').show();

				$('.notesstreet_name').remove();
				$('#notesModalBody').append(
					'<p class="notesstreet_name font-monospace text-danger">Street Name is required</p>'
				);
			} else {
				$('.cstmr_street_name').remove();

				$('.notesstreet_name').remove();
				validStatus++;
			}

			// PINCODE VALIDATION
			if ($('#pincode').val() == '') {
				$('.vndr_pincode').remove();
				$('#pincode')
					.parent()
					.append(
						'<span class="error vndr_pincode">Pincode is required</span>'
					);
				$('.vndr_pincode').show();

				$('.notesPincode').remove();
				$('#notesModalBody').append(
					'<p class="notesPincode font-monospace text-danger">Pincode is required</p>'
				);
			} else {
				$('.vndr_pincode').remove();

				$('.notesPincode').remove();
				validStatus++;
			}

			// OPENING BALANCE VALIDATION
			if ($('#vendor_opening_balance').val() == '') {
				$('.vndr_opening_balance').remove();
				$('#vendor_opening_balance')
					.parent()
					.append(
						'<span class="error vndr_opening_balance">Opening balance is required</span>'
					);
				$('.vndr_opening_balance').show();

				$('.notesOpeningBalance').remove();
				$('#notesModalBody').append(
					'<p class="notesOpeningBalance font-monospace text-danger">Opening balance is required</p>'
				);
			} else {
				$('.vndr_opening_balance').remove();

				$('.notesOpeningBalance').remove();
				validStatus++;
			}

			if ($('#createtype').val() == 'withgst') {
				// CREDIT PERIOD VALIDATION
				if ($('#vendor_credit_period').val() == '') {
					$('.vndr_credit_period').remove();
					$('#vendor_credit_period')
						.parent()
						.append(
							'<span class="error vndr_credit_period">Credit period is required</span>'
						);
					$('.vndr_credit_period').show();

					$('.notesCreditPeriod').remove();
					$('#notesModalBody').append(
						'<p class="notesCreditPeriod font-monospace text-danger">Credit period is required</p>'
					);
				} else {
					$('.vndr_credit_period').remove();
					validStatus++;

					$('.notesCreditPeriod').remove();
				}
				if (country_id == 103) {

					// IFSC VALIDATION
					if ($('#vendor_bank_ifsc').val() == '') {
						$('.vndr_bank_ifsc').remove();
						$('#vendor_bank_ifsc')
							.parent()
							.append(
								'<span class="error vndr_bank_ifsc">Ifsc is required</span>'
							);
						$('.vndr_bank_ifsc').show();

						$('.notesBankIFSC').remove();
						$('#notesModalBody').append(
							'<p class="notesBankIFSC font-monospace text-danger">Ifsc is required</p>'
						);
					} else {
						$('.vndr_bank_ifsc').remove();

						$('.notesBankIFSC').remove();
						validStatus++;
					}
				}

				// BANK NAME VALIDATION
				if ($('#vendor_bank_name').val() == '') {
					$('.vndr_bank_name').remove();
					$('#vendor_bank_name')
						.parent()
						.append(
							'<span class="error vndr_bank_name">Bank name is required</span>'
						);
					$('.vndr_bank_name').show();

					$('.notesBankName').remove();
					$('#notesModalBody').append(
						'<p class="notesBankName font-monospace text-danger">Bank name is required</p>'
					);
				} else {
					$('.vndr_bank_name').remove();

					$('.notesBankName').remove();
					validStatus++;
				}

				// BANK BRANCH VALIDATION
				if ($('#vendor_bank_branch').val() == '') {
					$('.vndr_bank_branch').remove();
					$('#vendor_bank_branch')
						.parent()
						.append(
							'<span class="error vndr_bank_branch">Bank branch is required</span>'
						);
					$('.vndr_bank_branch').show();

					$('.notesBankBranch').remove();
					$('#notesModalBody').append(
						'<p class="notesBankBranch font-monospace text-danger">Bank branch is required</p>'
					);
				} else {
					$('.vndr_bank_branch').remove();

					$('.notesBankBranch').remove();
					validStatus++;
				}

				// BANK ADDRESS VALIDATION
				if ($('#vendor_bank_address').val() == '') {
					$('.vndr_bank_address').remove();
					$('#vendor_bank_address')
						.parent()
						.append(
							'<span class="error vndr_bank_address">Bank address is required</span>'
						);
					$('.vndr_bank_address').show();

					$('.notesBankAddress').remove();
					$('#notesModalBody').append(
						'<p class="notesBankAddress font-monospace text-danger">Bank address is required</p>'
					);
				} else {
					$('.vndr_bank_address').remove();

					$('.notesBankAddress').remove();
					validStatus++;
				}

				// ACCOUNT NO VALIDATION
				if ($('#account_number').val() == '') {
					$('.vndr_bank_account_number').remove();
					$('#account_number')
						.parent()
						.append(
							'<span class="error vndr_bank_account_number">Bank acc no is required</span>'
						);
					$('.vndr_bank_account_number').show();

					$('.notesBankAccountNumber').remove();
					$('#notesModalBody').append(
						'<p class="notesBankAccountNumber font-monospace text-danger">Bank acc no is required</p>'
					);
				} else {
					$('.vndr_bank_account_number').remove();

					$('.notesBankAccountNumber').remove();
					validStatus++;
				}

				// ACCOUNT HOLDER VALIDATION
				if ($('#account_holder').val() == '') {
					$('.vndr_bank_account_holder').remove();
					$('#account_holder')
						.parent()
						.append(
							'<span class="error vndr_bank_account_holder">Bank acc holder is required</span>'
						);
					$('.vndr_bank_account_holder').show();

					$('.notesBankAccountHolder').remove();
					$('#notesModalBody').append(
						'<p class="notesBankAccountHolder font-monospace text-danger">Bank acc holder is required</p>'
					);
				} else {
					$('.vndr_bank_account_holder').remove();

					$('.notesBankAccountHolder').remove();
					validStatus++;
				}
			}

			// POC NAME VALIDATION
			if ($('#adminName').val() == '') {
				$('.vndr_adminName').remove();
				$('#adminName')
					.parent()
					.append(
						'<span class="error vndr_adminName">Name is required</span>'
					);
				$('.vndr_adminName').show();

				$('.notesAdminName').remove();
				$('#notesModalBody').append(
					'<p class="notesAdminName font-monospace text-danger">Name is required</p>'
				);
			} else {
				$('.vndr_adminName').remove();

				$('.notesAdminName').remove();
				validStatus++;
			}

			// POC DESIGNATION VALIDATION
			if ($('#vendor_authorised_person_designation').val() == '') {
				$('.vndr_authorised_person_designation').remove();
				$('#vendor_authorised_person_designation')
					.parent()
					.append(
						'<span class="error vndr_authorised_person_designation">Designation is required</span>'
					);
				$('.vndr_authorised_person_designation').show();

				$('.notesAuthPersDesignation').remove();
				$('#notesModalBody').append(
					'<p class="notesAuthPersDesignation font-monospace text-danger">Designation is required</p>'
				);
			} else {
				$('.vndr_authorised_person_designation').remove();

				$('.notesAuthPersDesignation').remove();
				validStatus++;
			}

			// POC PHONE NO VALIDATION
			if ($('#adminPhone').val() == '') {
				$('.vndr_adminPhone').remove();
				$('#adminPhone')
					.parent()
					.append(
						'<span class="error vndr_adminPhone">Phone no is required</span>'
					);
				$('.vndr_adminPhone').show();

				$('.notesAdminPhone').remove();
				$('#notesModalBody').append(
					'<p class="notesAdminPhone font-monospace text-danger">Phone no is required</p>'
				);
			} else {
				if ($('#adminPhone').val().length != 10) {
					$('.vndr_adminPhone').remove();
					$('#adminPhone')
						.parent()
						.append(
							'<span class="error vndr_adminPhone">Check your phone no</span>'
						);
					$('.vndr_adminPhone').show();

					$('.notesAdminPhone').remove();
					$('#notesModalBody').append(
						'<p class="notesAdminPhone font-monospace text-danger">Check your phone no</p>'
					);
				} else {
					$('.vndr_adminPhone').remove();

					$('.notesAdminPhone').remove();
					validStatus++;
				}
			}

			// POC ALTERNATIVE PHONE NO VALIDATION
			if ($('#vendor_authorised_person_phone').val() != '') {
				if ($('#vendor_authorised_person_phone').val().length != 10) {
					$('.vndr_authorised_person_phone').remove();
					$('#vendor_authorised_person_phone')
						.parent()
						.append(
							'<span class="error vndr_authorised_person_phone">Check your phone no</span>'
						);
					$('.vndr_authorised_person_phone').show();

					$('.notesAuthPersPhone').remove();
					$('#notesModalBody').append(
						'<p class="notesAuthPersPhone font-monospace text-danger">Check your phone no</p>'
					);
					altPh--;
				} else {
					$('.vndr_authorised_person_phone').remove();

					$('.notesAuthPersPhone').remove();
					altPh++;
				}
			} else {
				$('.vndr_authorised_person_phone').remove();

				$('.notesAuthPersPhone').remove();
			}

			// POC EMAIL VALIDATION
			if ($('#adminEmail').val() == '') {
				$('.vndr_adminEmail').remove();
				$('#adminEmail')
					.parent()
					.append(
						'<span class="error vndr_adminEmail">Email is required</span>'
					);
				$('.vndr_adminEmail').show();

				$('.notesAdminEmail').remove();
				$('#notesModalBody').append(
					'<p class="notesAdminEmail font-monospace text-danger">Email is required</p>'
				);
			} else {
				if (regEmail.test($('#adminEmail').val())) {
					$('.vndr_adminEmail').remove();

					$('.notesAdminEmail').remove();
					validStatus++;
				} else {
					$('.vndr_adminEmail').remove();
					$('#adminEmail')
						.parent()
						.append(
							'<span class="error vndr_adminEmail">Check your email</span>'
						);
					$('.vndr_adminEmail').show();

					$('.notesAdminEmail').remove();
					$('#notesModalBody').append(
						'<p class="notesAdminEmail font-monospace text-danger">Check your Email</p>'
					);
				}
			}

			// POC ALTERNATIVE PHONE NO VALIDATION
			if ($('#vendor_authorised_person_email').val() != '') {
				if (regEmail.test($('#vendor_authorised_person_email').val())) {
					$('.vndr_authorised_person_email').remove();

					$('.notesAuthPersEmail').remove();
					altEmail++;
				} else {
					$('.vndr_authorised_person_email').remove();
					$('#vendor_authorised_person_email')
						.parent()
						.append(
							'<span class="error vndr_authorised_person_email">Check your email</span>'
						);
					$('.vndr_authorised_person_email').show();

					$('.notesAuthPersEmail').remove();
					$('#notesModalBody').append(
						'<p class="notesAuthPersEmail font-monospace text-danger">Check your email</p>'
					);
					altEmail--;
				}
			} else {
				$('.vndr_authorised_person_email').remove();

				$('.notesAuthPersEmail').remove();
			}

			if (
				$('#con_business').val().toLowerCase() ===
				'public limited company' ||
				$('#con_business').val().toLowerCase() ===
				'private limited company' ||
				$('#con_business').val().toLowerCase() ===
				'limited liability partnership'
			) {
				if (
					checkCompany(
						$('#trade_name').val().toLowerCase(),
						$('#account_holder').val().toLowerCase()
					)
				) {
					$('.notesCheckName').remove();
					validStatus++;
				} else {
					//$('#exampleModal').modal('show');
					$('.notesCheckName').remove();
					$('#notesModalBody').append(
						'<p class="notesCheckName font-monospace text-danger">Account Holder Name Mismatch</p>'
					);
				}
			} else {
				validStatus++;
			}

			if ($('#adminPassword').val() != '') {
				if ($('#adminPassword').val().length >= 4) {
					$('.vndr_adminPassword').remove();

					$('.notesAdminPassword').remove();
					validStatus++;
				} else {
					$('.vndr_adminPassword').remove();
					$('#adminPassword')
						.parent()
						.append(
							'<span class="error vndr_adminPassword">Password must be at least 4 characters</span>'
						);
					$('.vndr_adminPassword').show();

					$('.notesAdminPassword').remove();
					$('#notesModalBody').append(
						'<p class="notesAdminPassword font-monospace text-danger">Password must be at least 4 characters</p>'
					);
					validStatus--;
				}
			} else {
				$('.vndr_adminPassword').remove();
				$('#adminPassword')
					.parent()
					.append('<span class="error vndr_adminPassword">Password is required</span>');
				$('.vndr_adminPassword').show();

				$('.notesAdminPassword').remove();
				$('#notesModalBody').append(
					'<p class="notesAdminPassword font-monospace text-danger">Password is required</p>'
				);
				validStatus--;
			}




			if ($('#createtype').val() == 'withoutgst') {
				if (country_id == 103) {
					if (validStatus !== 19) {
						e.preventDefault();
						$('#exampleModal').modal('show');
					} else if (altPh == 0) {
						e.preventDefault();
						$('#exampleModal').modal('show');
					} else if (altEmail == 0) {
						e.preventDefault();
						$('#exampleModal').modal('show');
					} else {
						$('#vendorCreateBtn').prop('disabled', true);
					}
				} else {
					if (validStatus !== 18) {
						e.preventDefault();
						$('#exampleModal').modal('show');
					} else if (altPh == 0) {
						e.preventDefault();
						$('#exampleModal').modal('show');
					} else if (altEmail == 0) {
						e.preventDefault();
						$('#exampleModal').modal('show');
					} else {
						$('#vendorCreateBtn').prop('disabled', true);
					}
				}
			} else {
				if (validStatus !== 26) {
					e.preventDefault();

					$('#exampleModal').modal('show');
				} else if (altPh == 0) {
					e.preventDefault();
					$('#exampleModal').modal('show');
				} else if (altEmail == 0) {
					e.preventDefault();
					$('#exampleModal').modal('show');
				} else {
					// alert("final else box")
					$('#vendorCreateBtn').prop('disabled', true);
				}
			}



		});

		$(document).on('submit', '#edit_frm', function(e) {
			let country_id = '<?php echo $companyCountry ?>';
			let validStatus = 0;
			let altPh = 1;
			let altEmail = 1;

			// PAN VALIDATION

			// alert(companyCountry);

			if ($('#vendor_pan').val() == '') {
				$('.vndr_pan').remove();
				$('#vendor_pan')
					.parent()
					.append('<span class="error vndr_pan">Pan/TFN is required</span>');
				$('.vndr_pan').show();

				$('.notesPan').remove();
				$('#notesModalBody').append(
					'<p class="notesPan font-monospace text-danger">Pan/TFN is required</p>'
				);
			} else {
				if (country_id = 103) {
					if (regPan.test($('#vendor_pan').val().toUpperCase())) {
						$('.vndr_pan').remove();

						$('.notesPan').remove();
						validStatus++;
					} else {
						$('.vndr_pan').remove();
						$('#vendor_pan')
							.parent()
							.append(
								'<span class="error vndr_pan">Check your pan</span>'
							);
						$('.vndr_pan').show();

						$('.notesPan').remove();
						$('#notesModalBodyUpdate').append(
							'<p class="notesPan font-monospace text-danger">Check your pan</p>'
						);
					}
				}
			}

			// TRADE NAME VALIDATION
			if ($('#trade_name').val() == '') {
				$('.vndr_trade_name').remove();
				$('#trade_name')
					.parent()
					.append(
						'<span class="error vndr_trade_name">vendor name is required</span>'
					);
				$('.vndr_trade_name').show();
				$('.notesTradeName').remove();
				//$('#notesModalBodyUpdate').html('hello');
				$('#notesModalBodyUpdate').append(
					'<p class="notesTradeName font-monospace text-danger">Vendor name is required</p>'
				);
			} else {
				$('.vndr_trade_name').remove();

				$('.notesTradeName').remove();
				validStatus++;
			}

			// LEGAL NAME VALIDATION
			if ($('#legal_name').val() == '') {
				$('.vndr_legal_name').remove();
				$('#legal_name')
					.parent()
					.append(
						'<span class="error vndr_legal_name">vendor legal name is required</span>'
					);
				$('.vndr_legal_name').show();

				$('.notesLegalName').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesLegalName font-monospace text-danger"> Vendor legal name is required</p>'
				);
			} else {
				$('.vndr_legal_name').remove();

				$('.notesLegalName').remove();
				validStatus++;
			}

			// CONST OF BUSINESS VALIDATION
			if ($('#con_business').val() == '') {
				$('.vndr_con_business').remove();
				$('#con_business')
					.parent()
					.append(
						'<span class="error vndr_con_business">Constitution of business is required</span>'
					);
				$('.vndr_con_business').show();

				$('.notesConstBusiness').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesConstBusiness font-monospace text-danger">Constitution of business is required</p>'
				);
			} else {
				$('.vndr_con_business').remove();

				$('.notesConstBusiness').remove();
				validStatus++;
			}

			// STATE VALIDATION
			if ($('#state').val() == '') {
				$('.vndr_state').remove();
				$('#state')
					.parent()
					.append(
						'<span class="error vndr_state">State is required</span>'
					);
				$('.vndr_state').show();

				$('.notesState').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesState font-monospace text-danger">State is required</p>'
				);
			} else {
				$('.vndr_state').remove();

				$('.notesState').remove();
				validStatus++;
			}

			// CITY VALIDATION
			if ($('#city').val() == '') {
				$('.vndr_city').remove();
				$('#city')
					.parent()
					.append(
						'<span class="error vndr_city">City is required</span>'
					);
				$('.vndr_city').show();

				$('.notesCity').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesCity font-monospace text-danger">City is required</p>'
				);
			} else {
				$('.vndr_city').remove();

				$('.notesCity').remove();
				validStatus++;
			}

			// DISTRICT VALIDATION
			if ($('#district').val() == '') {
				$('.vndr_district').remove();
				$('#district')
					.parent()
					.append(
						'<span class="error vndr_district">District is required</span>'
					);
				$('.vndr_district').show();

				$('.notesDistrict').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesDistrict font-monospace text-danger">District is required</p>'
				);
			} else {
				$('.vndr_district').remove();

				$('.notesDistrict').remove();
				validStatus++;
			}

			// LOCATION VALIDATION
			if ($('#location').val() == '') {
				$('.vndr_location').remove();
				$('#location')
					.parent()
					.append(
						'<span class="error vndr_location">Location is required</span>'
					);
				$('.vndr_location').show();

				$('.notesLocation').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesLocation font-monospace text-danger">Location is required</p>'
				);
			} else {
				$('.vndr_location').remove();

				$('.notesLocation').remove();
				validStatus++;
			}

			// BUILIDING NO VALIDATION
			if ($('#build_no').val() == '') {
				$('.cstmr_build_no').remove();
				$('#build_no')
					.parent()
					.append(
						'<span class="error cstmr_build_no">Building No. is required</span>'
					);
				$('.cstmr_build_no').show();

				$('.notesbuild_no').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesbuild_no font-monospace text-danger">Building No. is required</p>'
				);
			} else {
				$('.cstmr_build_no').remove();

				$('.notesbuild_no').remove();
				validStatus++;
			}

			// STREET NAME VALIDATION
			if ($('#street_name').val() == '') {
				$('.cstmr_street_name').remove();
				$('#street_name')
					.parent()
					.append(
						'<span class="error cstmr_street_name">Street Name is required</span>'
					);
				$('.cstmr_street_name').show();

				$('.notesstreet_name').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesstreet_name font-monospace text-danger">Street Name is required</p>'
				);
			} else {
				$('.cstmr_street_name').remove();

				$('.notesstreet_name').remove();
				validStatus++;
			}

			// Flat Number VALIDATION
			if ($('#flat_no').val() == '') {
				$('.flat_no').remove();
				$('#flat_no')
					.parent()
					.append(
						'<span class="error flat_no">Flat Number is required</span>'
					);
				$('.flat_no').show();

				$('.flatNo').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="flatNo font-monospace text-danger">Flat Number is required</p>'
				);
			} else {
				$('.flat_no').remove();

				$('.flatNo').remove();
				validStatus++;
			}

			// CREDIT PERIOD VALIDATION
			if ($('#vendor_credit_period').val() == '') {
				$('.vndr_credit_period').remove();
				$('#vendor_credit_period')
					.parent()
					.append(
						'<span class="error vndr_credit_period">Credit period is required</span>'
					);
				$('.vndr_credit_period').show();

				$('.notesCreditPeriod').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesCreditPeriod font-monospace text-danger">Credit period is required</p>'
				);
			} else {
				$('.vndr_credit_period').remove();
				validStatus++;

				$('.notesCreditPeriod').remove();
			}
			if (country_id = 103) {
				// IFSC VALIDATION
				if ($('#vendor_bank_ifsc').val() == '') {
					$('.vndr_bank_ifsc').remove();
					$('#vendor_bank_ifsc')
						.parent()
						.append(
							'<span class="error vndr_bank_ifsc">Ifsc is required</span>'
						);
					$('.vndr_bank_ifsc').show();

					$('.notesBankIFSC').remove();
					$('#notesModalBodyUpdate').append(
						'<p class="notesBankIFSC font-monospace text-danger">Ifsc is required</p>'
					);
				} else {
					$('.vndr_bank_ifsc').remove();

					$('.notesBankIFSC').remove();
					validStatus++;
				}
			}

			// BANK NAME VALIDATION
			if ($('#vendor_bank_name').val() == '') {
				$('.vndr_bank_name').remove();
				$('#vendor_bank_name')
					.parent()
					.append(
						'<span class="error vndr_bank_name">Bank name is required</span>'
					);
				$('.vndr_bank_name').show();

				$('.notesBankName').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesBankName font-monospace text-danger">Bank name is required</p>'
				);
			} else {
				$('.vndr_bank_name').remove();

				$('.notesBankName').remove();
				validStatus++;
			}

			// BANK BRANCH VALIDATION
			if ($('#vendor_bank_branch').val() == '') {
				$('.vndr_bank_branch').remove();
				$('#vendor_bank_branch')
					.parent()
					.append(
						'<span class="error vndr_bank_branch">Bank branch is required</span>'
					);
				$('.vndr_bank_branch').show();

				$('.notesBankBranch').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesBankBranch font-monospace text-danger">Bank branch is required</p>'
				);
			} else {
				$('.vndr_bank_branch').remove();

				$('.notesBankBranch').remove();
				validStatus++;
			}

			// BANK ADDRESS VALIDATION
			if ($('#vendor_bank_address').val() == '') {
				$('.vndr_bank_address').remove();
				$('#vendor_bank_address')
					.parent()
					.append(
						'<span class="error vndr_bank_address">Bank address is required</span>'
					);
				$('.vndr_bank_address').show();

				$('.notesBankAddress').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesBankAddress font-monospace text-danger">Bank address is required</p>'
				);
			} else {
				$('.vndr_bank_address').remove();

				$('.notesBankAddress').remove();
				validStatus++;
			}

			// ACCOUNT NO VALIDATION
			if ($('#account_number').val() == '') {
				$('.vndr_bank_account_number').remove();
				$('#account_number')
					.parent()
					.append(
						'<span class="error vndr_bank_account_number">Bank acc no is required</span>'
					);
				$('.vndr_bank_account_number').show();

				$('.notesBankAccountNumber').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesBankAccountNumber font-monospace text-danger">Bank acc no is required</p>'
				);
			} else {
				$('.vndr_bank_account_number').remove();

				$('.notesBankAccountNumber').remove();
				validStatus++;
			}

			// ACCOUNT HOLDER VALIDATION
			if ($('#account_holder').val() === '') {
				$('.vndr_bank_account_holder').remove();
				$('#account_holder')
					.parent()
					.append(
						'<span class="error vndr_bank_account_holder">Bank acc holder is required</span>'
					);
				$('.vndr_bank_account_holder').show();

				$('.notesBankAccountHolder').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesBankAccountHolder font-monospace text-danger">Bank acc holder is required</p>'
				);
			} else if ($('#account_holder').val() !== $('#trade_name').val()) {
				$('.vndr_bank_account_holder').remove();
				$('#account_holder')
					.parent()
					.append(
						'<span class="error vndr_bank_account_holder">Bank acc holder is mismatched</span>'
					);
				$('.vndr_bank_account_holder').show();

				$('.notesBankAccountHolder').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesBankAccountHolder font-monospace text-danger">Bank acc holder is mismatched</p>'
				);
			} else {
				$('.vndr_bank_account_holder').remove();
				$('.notesBankAccountHolder').remove();
				validStatus++;
			}

			// POC NAME VALIDATION
			if ($('#adminName').val() == '') {
				$('.vndr_adminName').remove();
				$('#adminName')
					.parent()
					.append(
						'<span class="error vndr_adminName">Name is required</span>'
					);
				$('.vndr_adminName').show();

				$('.notesAdminName').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesAdminName font-monospace text-danger">Name is required</p>'
				);
			} else {
				$('.vndr_adminName').remove();

				$('.notesAdminName').remove();
				validStatus++;
			}

			// POC DESIGNATION VALIDATION
			if ($('#vendor_authorised_person_designation').val() == '') {
				$('.vndr_authorised_person_designation').remove();
				$('#vendor_authorised_person_designation')
					.parent()
					.append(
						'<span class="error vndr_authorised_person_designation">Designation is required</span>'
					);
				$('.vndr_authorised_person_designation').show();

				$('.notesAuthPersDesignation').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesAuthPersDesignation font-monospace text-danger">Designation is required</p>'
				);
			} else {
				$('.vndr_authorised_person_designation').remove();

				$('.notesAuthPersDesignation').remove();
				validStatus++;
			}

			// POC PHONE NO VALIDATION
			if ($('#adminPhone').val() == '') {
				$('.vndr_adminPhone').remove();
				$('#adminPhone')
					.parent()
					.append(
						'<span class="error vndr_adminPhone">Phone no is required</span>'
					);
				$('.vndr_adminPhone').show();

				$('.notesAdminPhone').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesAdminPhone font-monospace text-danger">Phone no is required</p>'
				);
			} else {
				if ($('#adminPhone').val().length != 10) {
					$('.vndr_adminPhone').remove();
					$('#adminPhone')
						.parent()
						.append(
							'<span class="error vndr_adminPhone">Check your phone no</span>'
						);
					$('.vndr_adminPhone').show();

					$('.notesAdminPhone').remove();
					$('#notesModalBody').append(
						'<p class="notesAdminPhone font-monospace text-danger">Check your phone no</p>'
					);
				} else {
					$('.vndr_adminPhone').remove();

					$('.notesAdminPhone').remove();
					validStatus++;
				}
			}

			// POC ALTERNATIVE PHONE NO VALIDATION
			if ($('#vendor_authorised_person_phone').val() != '') {
				if ($('#vendor_authorised_person_phone').val().length != 10) {
					$('.vndr_authorised_person_phone').remove();
					$('#vendor_authorised_person_phone')
						.parent()
						.append(
							'<span class="error vndr_authorised_person_phone">Check your phone no</span>'
						);
					$('.vndr_authorised_person_phone').show();

					$('.notesAuthPersPhone').remove();
					$('#notesModalBodyUpdate').append(
						'<p class="notesAuthPersPhone font-monospace text-danger">Check your phone no</p>'
					);
					altPh--;
				} else {
					$('.vndr_authorised_person_phone').remove();

					$('.notesAuthPersPhone').remove();
					altPh++;
				}
			} else {
				$('.vndr_authorised_person_phone').remove();

				$('.notesAuthPersPhone').remove();
			}

			// POC EMAIL VALIDATION
			if ($('#adminEmail').val() == '') {
				$('.vndr_adminEmail').remove();
				$('#adminEmail')
					.parent()
					.append(
						'<span class="error vndr_adminEmail">Email is required</span>'
					);
				$('.vndr_adminEmail').show();

				$('.notesAdminEmail').remove();
				$('#notesModalBodyUpdate').append(
					'<p class="notesAdminEmail font-monospace text-danger">Email is required</p>'
				);
			} else {
				if (regEmail.test($('#adminEmail').val())) {
					$('.vndr_adminEmail').remove();

					$('.notesAdminEmail').remove();
					validStatus++;
				} else {
					$('.vndr_adminEmail').remove();
					$('#adminEmail')
						.parent()
						.append(
							'<span class="error vndr_adminEmail">Check your email</span>'
						);
					$('.vndr_adminEmail').show();

					$('.notesAdminEmail').remove();
					$('#notesModalBody').append(
						'<p class="notesAdminEmail font-monospace text-danger">Check your Email</p>'
					);
				}
			}

			// POC ALTERNATIVE PHONE NO VALIDATION
			if ($('#vendor_authorised_person_email').val() != '') {
				if (regEmail.test($('#vendor_authorised_person_email').val())) {
					$('.vndr_authorised_person_email').remove();

					$('.notesAuthPersEmail').remove();
					altEmail++;
				} else {
					$('.vndr_authorised_person_email').remove();
					$('#vendor_authorised_person_email')
						.parent()
						.append(
							'<span class="error vndr_authorised_person_email">Check your email</span>'
						);
					$('.vndr_authorised_person_email').show();

					$('.notesAuthPersEmail').remove();
					$('#notesModalBodyUpdate').append(
						'<p class="notesAuthPersEmail font-monospace text-danger">Check your email</p>'
					);
					altEmail--;
				}
			} else {
				$('.vndr_authorised_person_email').remove();

				$('.notesAuthPersEmail').remove();
			}

			if (validStatus !== 22) {
				//alert('A');
				e.preventDefault();
				$('#validateMessage').modal('show');
			} else if (altPh == 0) {
				//alert('B');
				e.preventDefault();
				$('#validateMessage').modal('show');
			} else if (altEmail == 0) {
				e.preventDefault();
				$('#validateMessage').modal('show');
			} else {
				$('#vendorEditBtn').prop('disabled', true);

			}
		});
	});
</script>