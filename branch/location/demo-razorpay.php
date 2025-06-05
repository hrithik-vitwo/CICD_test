<html>
<head>
    <title>Automatic Razorpay Checkout</title>
</head>
<body>
    <form id="payment-form" action="charge.php" method="POST">
        <input type="hidden" id="razorpay_payment_id" name="payment_id">
    </form>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        function triggerRazorpay() {
            var options = {
                "key": "rzp_test_SRNNcrFvhl0M3C", // Enter the Key ID generated from the Razorpay Dashboard
                "amount": "100", // Amount in currency subunits. Default currency is INR. Hence, 1000 refers to 1000 paise or ₹10.00.
                "currency": "INR",
                "name": "Your Company Name",
                "description": "A Wild Sheep Chase is the third novel by Haruki Murakami",
                "image": "https://your-logo-url.com",
                "prefill": {
                    "name": "Somdutta Sengupta",
                    "email": "somdutta075@gmail.com"
                },
                "theme": {
                    "color": "#F37254"
                },
                "method": {
                    "upi": true
                },
                "handler": function (response){
                    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                    document.getElementById('payment-form').submit();
                },
                "modal": {
                    "ondismiss": function(){
                        alert("Payment Cancelled");
                    }
                }
            };
            var rzp = new Razorpay(options);
            rzp.open();
        }
        window.onload = triggerRazorpay;
    </script>
</body>
</html>