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