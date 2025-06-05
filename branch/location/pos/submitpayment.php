
<?php
require_once "../../../app/v1/connection-branch-admin.php";
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET,PUT,PATCH,DELETE');
header("Content-Type: application/json");
header("Accept: application/json");
header('Access-Control-Allow-Headers:Access-Control-Allow-Origin,Access-Control-Allow-Methods,Content-Type');
global $company_id;

global $branch_id;
global $location_id;
global $created_by;
global $updated_by;
$BranchSoObj = new BranchSo();
$requestPayload = file_get_contents('php://input');
$requestData = json_decode($requestPayload, true);

if (isset($requestData['action_post']) && $requestData['action_post'] === "collect") {
  $addCollectPayment = $BranchSoObj->insertCollectPayment($requestData, $_FILES);

    if ($addCollectPayment['status'] == "success") {
          echo json_encode(['res' => 'success']);
        exit();
    } else {
        echo json_encode(['res' => 'error']);
        exit();
    }
}

if (isset($_POST['act']) && ($_POST['act'] = 'pos_invoice')) {
    $bankid = $_POST['bankId'];
    $amount = $_POST['paid'];
    $customerId = $_POST['customerId'];

    $bank = queryGet(
        "SELECT `access_token`, `access_key` 
                  FROM `erp_payment_gateway` 
                  WHERE `bank_id` = '" .
            $bankid .
            "'
                  AND `company_id` = '" .
            $company_id .
            "'
                  AND `branch_id` = '" .
            $branch_id .
            "'
                  AND `location_id` = '" .
            $location_id .
            "'
                  AND `getway_type` = 'razorpay'",
        false
    );
    $key = $bank["data"]["access_key"];
    $secret_key = $bank["data"]["access_token"];
    if (isset($POST['walkInCustomerCheckbox'])) {
        $customerType = "walkin";
        $customerId = 0;
        $customer_name = $POST['walkInCustomerName'];
        $customer_email = "";
        $customer_phone = $POST['walkInCustomerMobile'];
    } else {
        $customerType = "customer";
        $customer = queryGet(
            "SELECT `trade_name`, `customer_authorised_person_email`, `customer_authorised_person_phone` 
                      FROM `erp_customer` 
                      WHERE `customer_id` = '" .
                $customerId .
                "'
                      AND `company_id` = '" .
                $company_id .
                "'
                      AND `company_branch_id` = '" .
                $branch_id .
                "'
                      AND `location_id` = '" .
                $location_id .
                "'",
            false
        );
        $customer_name = $customer["data"]["trade_name"];
        $customer_email = $customer["data"]["customer_authorised_person_email"];
        $customer_phone = $customer["data"]["customer_authorised_person_phone"];
    }

    $authAPIkey = "Basic " . base64_encode($key . ":" . $secret_key);
    // Set transaction details
    $order_id = "VITWO_" . uniqid();

    $note = "Payment of amount Rs. " . $amount;

    $postdata = [
        "amount" => $amount * 100,
        "currency" => "INR",
        "receipt" => $note,
        "notes" => [
            "notes_key_1" => $note,
            "notes_key_2" => "",
        ],
    ];
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.razorpay.com/v1/orders',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postdata),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: ' . $authAPIkey],
    ]);

    $response = curl_exec($curl);

    curl_close($curl);
    $orderRes = json_decode($response);

    if (isset($orderRes->id)) {
        $rpay_order_id = $orderRes->id;

        $dataArr = [
            'amount' => $amount,
            'description' => "Pay bill of Rs. " . $amount,
            'rpay_order_id' => $rpay_order_id,
            'name' => $customer_name,
            'email' => $customer_email,
            'mobile' => $customer_phone,
        ];
        
        echo json_encode(['res' => 'success', 'order_number' => $order_id, 'userData' => $dataArr, 'razorpay_key' => $key]);
        exit();
    } else {
        echo json_encode(['res' => 'error', 'order_id' => $order_id, 'info' => 'Error with payment']);
        exit();
    }
}

?>   
