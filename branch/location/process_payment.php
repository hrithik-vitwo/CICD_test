<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $bank = $_POST['bank'];
 

    $apiKey = "rzp_test_SRNNcrFvhl0M3C";
    $apiSecret = "qPl5RyOuMtPnLrLL3AwvpJ6v";
    $amount = 1000; // Amount in paise (i.e., ₹10.00)

    // Generate order ID using Razorpay API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'amount' => $amount,
        'currency' => 'INR',
        'receipt' => 'order_rcptid_11'
    ]));
    curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':' . $apiSecret);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    curl_close($ch);

    $orderData = json_decode($response, true);
    $orderId = $orderData['id'];
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Razorpay Checkout</title>
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    </head>
    <body>
        <script>
            var options = {
                "key": "<?php echo $apiKey; ?>", // Enter the Key ID generated from the Dashboard
                "amount": "<?php echo $amount; ?>", // Amount is in currency subunits. Default currency is INR. Hence, 1000 = INR 10.
                "currency": "INR",
                "name": "<?php echo $name; ?>",
                "description": "Test Transaction",
                "image": "https://example.com/your_logo",
                "order_id": "<?php echo $orderId; ?>", //This is a sample Order ID. Pass the `id` obtained in the previous step
                "handler": function (response){
                    alert("Payment successful!");
                    console.log(response);
                    // Implement further processing here
                },
                "prefill": {
                    "name": "<?php echo $name; ?>",
                    "email": "<?php echo $email; ?>",
                    "contact": "<?php echo $phone; ?>"
                },
                "notes": {
                    "bank_name": "<?php echo $bank; ?>"
                },
                "theme": {
                    "color": "#003060"
                },
                "netbanking": "1",
                "bank": 'HDFC'
            };
            var rzp1 = new Razorpay(options);
            rzp1.on('payment.failed', function (response){
                alert("Payment failed!");
                console.log(response);
            });
            rzp1.open();
        </script>
    </body>
    </html>
    <?php
}
?>
