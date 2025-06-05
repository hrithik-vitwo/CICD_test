<?php
include_once("../../../app/v1/connection-branch-admin.php");
class eWayBill
{
  private $company_id;
  private $branch_id;
  private $location_id;
  private $created_by;
  private $API_USER_IP;
  // private $API_CLIENT_ID = "6eae2ed0-91fb-434e-b639-c089e0772654";
  // private $API_CLIENT_EMAIL = "developer@vitwo.in";
  // private $API_CLIENT_SECRET_ID = "e89d9741-5576-48f5-b917-d5a4ca334e52";
  private $API_CLIENT_ID = "6f25ecb7-2010-4796-94bf-b3438f9cb3e0";
  private $API_CLIENT_EMAIL = "developer@vitwo.in";
  private $API_CLIENT_SECRET_ID = "ef950f81-a68c-4f09-9657-e962da9aeadb";

  function __construct()
  {
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $this->company_id = $company_id;
    $this->branch_id = $branch_id;
    $this->location_id = $location_id;
    $this->created_by = $created_by;
    $this->API_USER_IP = $_SERVER['REMOTE_ADDR'];
  }

  private function eWayBillAuth()
  {
    $prevAuthObj = queryGet('SELECT `gstin`, `username`, `auth_token`, `user_ip` FROM erp_e_invoice_auth WHERE `token_expiry`>=CURRENT_TIMESTAMP AND `branch_id`=' . $this->branch_id . ' AND `company_id`=' . $this->company_id . ' ORDER BY `id` DESC LIMIT 1');

    if ($prevAuthObj["status"] == "success") {
      return [
        "status" => "success",
        "message" => "Already authorized",
        "data" => $prevAuthObj["data"]
      ];
    } else {
      //call auth api for new authorization
      $usereWayBillCredObj = queryGet('SELECT `branch_gstin`,`branch_eWayBill_username`,`branch_eWayBill_password` FROM `erp_branches` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id);

      if ($usereWayBillCredObj["status"] == "success") {
        $userCred = $usereWayBillCredObj["data"];
        // $userCred["branch_gstin"] = "29AABCT1332L000";
        // $userCred["branch_eWayBill_username"] = "mastergst";
        // $userCred["branch_eWayBill_password"] = "Malli#123";

        if ($userCred["branch_eWayBill_username"] != "" && $userCred["branch_eWayBill_password"] != "") {

          $url = "https://api.mastergst.com/eWayBill/authenticate?email=" . $this->API_CLIENT_EMAIL;
          $headers = array(
            "Content-Type: application/json",
            "client_id: " . $this->API_CLIENT_ID,
            "client_secret: " . $this->API_CLIENT_SECRET_ID,
            "ip_address: " . $this->API_USER_IP,
            "gstin: " . $userCred["branch_gstin"],
            "username: " . $userCred["branch_eWayBill_username"],
            "password: " . $userCred["branch_eWayBill_password"],
          );

          $ch = curl_init($url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

          $response = curl_exec($ch);
          $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
          curl_close($ch);

          if ($responseCode == 200) {
            $responseData = json_decode($response, true);

            $authData = $responseData["data"];

            $insertObj = queryInsert('INSERT INTO `erp_e_invoice_auth` SET `company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`gstin`="' . $userCred["branch_gstin"] . '",`username`="' . $userCred["branch_eWayBill_username"] . '",`auth_token`="' . $authData["AuthToken"] . '",`token_expiry`="' . $authData["TokenExpiry"] . '",`client_id`="' . $authData["ClientId"] . '",`sek`="' . $authData["Sek"] . '",`user_ip`="' . $this->API_USER_IP . '", `created_by`="' . $this->created_by . '"');

            if ($insertObj["status"] == "success") {
              return [
                "status" => "success",
                "message" => "E way bill authorization success and the token will expire at " . $authData["TokenExpiry"],
                "data" => [
                  "gstin" => $userCred["branch_gstin"],
                  "username" => $userCred["branch_eWayBill_username"],
                  "auth_token" => $authData["AuthToken"],
                  "user_ip" => $this->API_USER_IP
                ]
              ];
            } else {
              return [
                "status" => "error",
                "message" => "E way bill authorization failed, try again",
                "data" => []
              ];
            }
          } else {
            return [
              "status" => "warning",
              "message" => "E way bill authentication failed",
              "data" => []
            ];
          }
        } else {
          return [
            "status" => "warning",
            "message" => "E way bill credentials not found",
            "data" => []
          ];
        }
      } else {
        return [
          "status" => "warning",
          "message" => "E way bill credentials not found",
          "data" => []
        ];
      }
    }
  }

  function generatePayload($invoiceId = null)
  {

    $dbObj = new Database();

    $locationDetails = $dbObj->queryGet('SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id`=' . $this->location_id)["data"];

    $branchDetails = $dbObj->queryGet('SELECT * FROM `erp_branches` WHERE `branch_id`=' . $this->branch_id)["data"];

    $invoiceDetailsObj = $dbObj->queryGet('SELECT * FROM `erp_branch_sales_order_invoices` WHERE `so_invoice_id`=' . $invoiceId . ' AND `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id);
    $invoiceDetails = $invoiceDetailsObj["data"];

    $invoiceItemList = $dbObj->queryGet('SELECT items.*, uomTable.uomName, itemMater.goodsType FROM `erp_branch_sales_order_invoice_items` AS items LEFT JOIN `erp_inventory_mstr_uom` as uomTable ON items.uom=uomTable.uomId LEFT JOIN `erp_inventory_items` itemMater ON items.inventory_item_id=itemMater.itemId WHERE items.so_invoice_id=' . $invoiceDetails["so_invoice_id"], true)["data"];


    $customerDetails = $dbObj->queryGet('SELECT * FROM `erp_customer` WHERE `customer_id`=' . $invoiceDetails["customer_id"] . ' AND `company_branch_id`=' . $this->branch_id)["data"];
    $billingAddressObj = $dbObj->queryGet('SELECT * FROM `erp_customer_address` WHERE `customer_address_id`=' . $invoiceDetails["billing_address_id"]);
    $billingAddress = $billingAddressObj["data"];
    $shippingAddress = $dbObj->queryGet('SELECT * FROM `erp_customer_address` WHERE `customer_address_id`=' . $invoiceDetails["shipping_address_id"])["data"];


    $invoiceDetails["companyDetails"] = unserialize($invoiceDetails["companyDetails"]);
    $invoiceDetails["customerDetails"] = unserialize($invoiceDetails["customerDetails"]);

    $payloadArr = [];
    if ($invoiceDetails["customer_gstin"] == "") {
      //B2C E way bill Details
    } else {
      $payloadArr = [
        "Irn" => "47d7ba1814b6ca6123c780ad289b0a24e30c1baed59a7417d29a54e7b00a6bdf",
        "Distance" => 100,
        "TransMode" => "1",
        "TransId" => "12AWGPV7107B1Z1",
        "TransName" => "trans name",
        "TransDocDt" => "01/08/2020",
        "TransDocNo" => "TRAN/DOC/11",
        "VehNo" => "KA12ER1234",
        "VehType" => "R",
        "ExpShipDtls" => [
          "Addr1" => "7th block, kuvempu layout",
          "Addr2" => "kuvempu layout",
          "Loc" => "Banagalore",
          "Pin" => 562160,
          "Stcd" => "29"
        ],
        "DispDtls" => [
          "Nm" => "ABC company pvt ltd",
          "Addr1" => "7th block, kuvempu layout",
          "Addr2" => "kuvempu layout",
          "Loc" => "Banagalore",
          "Pin" => 562160,
          "Stcd" => "29"
        ]
      ];
    }
    return [
      "status" => "success",
      "message" => "Invoice",
      "custType" => $invoiceDetails["customer_gstin"] == "" ? "b2c" : "b2b",
      "data" => $payloadArr,
      "billingAddressObj" => $billingAddressObj
    ];
  }

  function generateB2ceWayBill()
  {
    return [];
  }

  function generateEwayBill($payloadArr = [], $invoiceId = 0)
  {
    $authObj = $this->eWayBillAuth();
    if ($authObj["status"] != "success") {
      return $authObj;
    }

    $authData = $authObj["data"];
    $checkPreveWayBillObj = queryGet('SELECT * FROM `erp_e_invoices` WHERE `invoice_id`=' . $invoiceId . ' AND `location_id`=' . $this->location_id . ' AND `branch_id`=' . $this->branch_id . ' AND `company_id`=' . $this->company_id);
    if ($checkPreveWayBillObj["status"] == "success") {
      $preveWayBillData = $checkPreveWayBillObj["data"];
      return [
        "status" => "success",
        "message" => "E way bill already created",
        "data" => [
          "ack_no" => $preveWayBillData["ack_no"],
          "ack_date" => $preveWayBillData["ack_date"],
          "irn" => $preveWayBillData["irn"],
          "signed_invoice" => $preveWayBillData["signed_invoice"],
          "signed_qr_code" => $preveWayBillData["signed_qr_code"],
          "e_invoice_status" => $preveWayBillData["e_invoice_status"],
          "ewb_no" => $preveWayBillData["ewb_no"],
          "ewb_date" => $preveWayBillData["ewb_date"],
          "ewb_valid_till" => $preveWayBillData["ewb_valid_till"],
          "remarks" => $preveWayBillData["remarks"]
        ],
        "obj" => $checkPreveWayBillObj
      ];
      exit;
    }
    $curl_headers = array(
      "Content-Type: application/json",
      "client_id: " . $this->API_CLIENT_ID,
      "client_secret: " . $this->API_CLIENT_SECRET_ID,
      "ip_address: " . $authData["user_ip"],
      "gstin: " . $authData["gstin"],
      "username: " . $authData["username"],
      "auth-token: " . $authData["auth_token"],
    );

    $curl_body = json_encode($payloadArr, true);

    $url = "https://api.mastergst.com/eWayBill/type/GENERATE/version/V1_03?email=" . $this->API_CLIENT_EMAIL;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    if ($responseCode != 200) {
      return [
        "status" => "warning",
        "message" => "Something went wrong, please try again",
        "data" => []
      ];
      exit();
    }
    $responseData = json_decode($response, true);

    if ($responseData["status_cd"] == 1) {
      $eInvDetails = $responseData["data"];
      $insertObj = queryInsert('INSERT INTO `erp_e_invoices` SET `company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`invoice_id`=' . $invoiceId . ',`ack_no`="' . $eInvDetails["AckNo"] . '",`ack_date`="' . $eInvDetails["AckDt"] . '",`irn`="' . $eInvDetails["Irn"] . '",`signed_invoice`="' . $eInvDetails["SignedInvoice"] . '",`signed_qr_code`="' . $eInvDetails["SignedQRCode"] . '",`e_invoice_status`="' . $eInvDetails["Status"] . '",`ewb_no`="' . $eInvDetails["EwbNo"] . '",`ewb_date`="' . $eInvDetails["EwbDt"] . '",`ewb_valid_till`="' . $eInvDetails["EwbValidTill"] . '",`remarks`="' . $eInvDetails["Remarks"] . '",`created_by`="' . $this->created_by . '",`updated_by`="' . $this->created_by . '"');

      if ($insertObj["status"] == "success") {
        return [
          "status" => "success",
          "message" => "E way bill created successfully",
          "data" => [
            "ack_no" => $eInvDetails["AckNo"],
            "ack_date" => $eInvDetails["AckDt"],
            "irn" => $eInvDetails["Irn"],
            "signed_invoice" => $eInvDetails["SignedInvoice"],
            "signed_qr_code" => $eInvDetails["SignedQRCode"],
            "e_invoice_status" => $eInvDetails["Status"],
            "ewb_no" => $eInvDetails["EwbNo"],
            "ewb_date" => $eInvDetails["EwbDt"],
            "ewb_valid_till" => $eInvDetails["EwbValidTill"],
            "remarks" => $eInvDetails["Remarks"]
          ]
        ];
      } else {
        return [
          "status" => "warning",
          "message" => "E way bill created failed",
          "data" => $insertObj,
        ];
      }
    } else {

      $messages = ["E way bill created failed."];
      foreach (json_decode($responseData["status_desc"], true) as $row) {
        $messages[] = $row["ErrorMessage"];
      }

      return [
        "status" => "warning",
        "message" => implode(" ", $messages),
        "data" => $responseData,
        "payload" => $payloadArr,
      ];
    }
  }
}


$responseData = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $invoiceId = $_POST["invoiceId"] ?? 0;
  if ($invoiceId > 0) {
    $eWayBillObj = new eWayBill();
    $payloadObj = $eWayBillObj->generatePayload($invoiceId);
    if ($payloadObj["status"] == "success" && $payloadObj["custType"] == "b2b") {
      $responseData = $eWayBillObj->generateEwayBill($payloadObj["data"], $invoiceId);
      // $responseData = $payloadObj;
       

    } else {
      $responseData = [
        "status" => "warning",
        "message" => "B2C E way bill in progress,  will be added soon.",
      ];
    }
  } else {
    $responseData = [
      "status" => "warning",
      "message" => "Please select valid invoice to generate E way bill",
      "data" => []
    ];
  }
} else {
  $responseData = [
    "status" => "warning",
    "message" => "Method not allowed",
    "data" => []
  ];
}

echo json_encode($responseData, true);
