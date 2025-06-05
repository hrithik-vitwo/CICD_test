<button id="rzp-button">Pay</button>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var options = {
    key: 'rzp_test_SRNNcrFvhl0M3C', 
    amount: 50000, 
    currency: 'INR',
    name: 'Your Company Name',
    description: 'Product or Service Description',
    image: 'https://your-company-logo.png', 
    handler: function(response) {
        alert('Payment successful. Payment ID: ' + response.razorpay_payment_id);
        
    },
    prefill: {
        name: 'Somdutta Senguota',
        email: 'somdutta075@gmail.com',
        contact: '8910533689'
    },
    notes: {
        
    },
    theme: {
        color: '#003060' 
    },
   
};

document.getElementById('rzp-button').onclick = function(e){
    var rzp = new Razorpay(options);
    rzp.open();
    e.preventDefault();
}

</script>
