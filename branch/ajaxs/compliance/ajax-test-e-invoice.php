<?php
include_once("../../../app/v1/connection-branch-admin.php");
// include_once("../../../app/v1/functions/branch/func-compliance-controller.php");

class eInvoice
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

  private function eInvoiceAuth()
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
      $userEInvoiceCredObj = queryGet('SELECT `branch_gstin`,`branch_einvoice_username`,`branch_einvoice_password` FROM `erp_branches` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id);

      if ($userEInvoiceCredObj["status"] == "success") {
        $userCred = $userEInvoiceCredObj["data"];
        // $userCred["branch_gstin"] = "29AABCT1332L000";
        // $userCred["branch_einvoice_username"] = "mastergst";
        // $userCred["branch_einvoice_password"] = "Malli#123";

        if ($userCred["branch_einvoice_username"] != "" && $userCred["branch_einvoice_password"] != "") {

          $url = "https://api.mastergst.com/einvoice/authenticate?email=" . $this->API_CLIENT_EMAIL;
          $headers = array(
            "Content-Type: application/json",
            "client_id: " . $this->API_CLIENT_ID,
            "client_secret: " . $this->API_CLIENT_SECRET_ID,
            "ip_address: " . $this->API_USER_IP,
            "gstin: " . $userCred["branch_gstin"],
            "username: " . $userCred["branch_einvoice_username"],
            "password: " . $userCred["branch_einvoice_password"],
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

            $insertObj = queryInsert('INSERT INTO `erp_e_invoice_auth` SET `company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`gstin`="' . $userCred["branch_gstin"] . '",`username`="' . $userCred["branch_einvoice_username"] . '",`auth_token`="' . $authData["AuthToken"] . '",`token_expiry`="' . $authData["TokenExpiry"] . '",`client_id`="' . $authData["ClientId"] . '",`sek`="' . $authData["Sek"] . '",`user_ip`="' . $this->API_USER_IP . '", `created_by`="' . $this->created_by . '"');

            if ($insertObj["status"] == "success") {
              return [
                "status" => "success",
                "message" => "E-Invoice authorization success and the token will expire at " . $authData["TokenExpiry"],
                "data" => [
                  "gstin" => $userCred["branch_gstin"],
                  "username" => $userCred["branch_einvoice_username"],
                  "auth_token" => $authData["AuthToken"],
                  "user_ip" => $this->API_USER_IP
                ]
              ];
            } else {
              return [
                "status" => "error",
                "message" => "E-Invoice authorization failed, try again",
                "data" => []
              ];
            }
          } else {
            return [
              "status" => "warning",
              "message" => "E-Invoice authentication failed",
              "data" => []
            ];
          }
        } else {
          return [
            "status" => "warning",
            "message" => "E-Invoice credentials not found",
            "data" => []
          ];
        }
      } else {
        return [
          "status" => "warning",
          "message" => "E-Invoice credentials not found",
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

    console($invoiceDetails);

    $invoiceItemList = $dbObj->queryGet('SELECT items.*, uomTable.uomName, itemMater.goodsType FROM `erp_branch_sales_order_invoice_items` AS items LEFT JOIN `erp_inventory_mstr_uom` as uomTable ON items.uom=uomTable.uomId LEFT JOIN `erp_inventory_items` itemMater ON items.inventory_item_id=itemMater.itemId WHERE items.so_invoice_id=' . $invoiceDetails["so_invoice_id"], true)["data"];


    $customerDetails = $dbObj->queryGet('SELECT * FROM `erp_customer` WHERE `customer_id`=' . $invoiceDetails["customer_id"] . ' AND `company_branch_id`=' . $this->branch_id)["data"];
    $billingAddressObj = $dbObj->queryGet('SELECT * FROM `erp_customer_address` WHERE `customer_address_id`=' . $invoiceDetails["billing_address_id"]);
    $billingAddress = $billingAddressObj["data"];
    $shippingAddress = $dbObj->queryGet('SELECT * FROM `erp_customer_address` WHERE `customer_address_id`=' . $invoiceDetails["shipping_address_id"])["data"];


    $invoiceDetails["companyDetails"] = unserialize($invoiceDetails["companyDetails"]);
    $invoiceDetails["customerDetails"] = unserialize($invoiceDetails["customerDetails"]);

    $payloadArr = [];
    if ($invoiceDetails["customer_gstin"] == "") {
      //B2C E-Invoice Details
    } else {
      //B2B E-Invoice Details

      // $invoiceDetails["invoice_no"]
      $payloadArr = [
        'Version' => '1.1',
        'TranDtls' => [
          'TaxSch' => 'GST',
          'SupTyp' => 'B2B',
          'RegRev' => $invoiceDetails["reverseCharge"],
          'EcmGstin' => null,
          //"IgstOnIntra"=> "N",
        ],
        'DocDtls' => [
          'Typ' => 'INV',
          'No' => substr("VITWO1".$invoiceDetails["invoice_no"], 0, 16),
          'Dt' => date("d/m/Y", strtotime($invoiceDetails["invoice_date"])),
        ],
        'SellerDtls' => [
          'Gstin' => $branchDetails["branch_gstin"],
          'LglNm' => $branchDetails["branch_legal_name"],
          'Addr1' => substr($locationDetails["othersLocation_building_no"] . ", " . $locationDetails["othersLocation_city"], 0, 100),
          'Loc' => $locationDetails["othersLocation_name"],
          'Pin' => intval($locationDetails["othersLocation_pin_code"]),
          'Stcd' => substr($invoiceDetails["companyDetails"]["branch_gstin"], 0, 2) . "",
          'Ph' => $invoiceDetails["companyDetails"]["companyPhone"] . "",
          'Em' => $invoiceDetails["companyDetails"]["companyEmail"],
        ],
        'BuyerDtls' => [
          'Gstin' => $customerDetails["customer_gstin"],
          'LglNm' => $customerDetails["legal_name"],
          'Pos' => $invoiceDetails["placeOfSupply"] . "",
          'Addr1' => substr($invoiceDetails["customer_billing_address"], 0, 100),
          'Loc' => $billingAddress["customer_address_location"],
          'Pin' => intval($billingAddress["customer_address_pin_code"]),
          'Stcd' => substr($invoiceDetails["customerDetails"]["customer_gstin"], 0, 2) . "",
          'Ph' => $invoiceDetails["customerDetails"]["customer_authorised_person_phone"] . "",
          'Em' => $invoiceDetails["customerDetails"]["customer_authorised_person_email"],
        ],
        'ItemList' => [],
        'ValDtls' => [
          'AssVal' => 0,
          'CgstVal' => 0,
          'SgstVal' => 0,
          'IgstVal' => 0,
          'CesVal' => 0,
          'StCesVal' => 0,
          'Discount' => 0,
          'OthChrg' => 0,
          'RndOffAmt' => 0,
          'TotInvVal' => 0,
        ],
      ];

      $payloadArr["ValDtls"]["AssVal"] = round(floatval($invoiceDetails["sub_total_amt"]),2);
      $payloadArr["ValDtls"]["CgstVal"] = round(floatval($invoiceDetails["cgst"]),2);
      $payloadArr["ValDtls"]["SgstVal"] = round(floatval($invoiceDetails["sgst"]),2);
      $payloadArr["ValDtls"]["IgstVal"] = round(floatval($invoiceDetails["igst"]),2);
      $payloadArr["ValDtls"]["RndOffAmt"] = round(floatval($invoiceDetails["adjusted_amount"]),2);
      $payloadArr["ValDtls"]["TotInvVal"] = round(floatval($invoiceDetails["all_total_amt"]),2);


      foreach ($invoiceItemList as $key => $row) {
        $sgst = 0;
        $cgst = 0;
        $igst = 0;
        $cess = 0;
        if(floatval($payloadArr["ValDtls"]["IgstVal"]) > 0) {
          $igst = round(floatval($row["totalTax"]),2);
        } else {
          $sgst = round(floatval($row["totalTax"] / 2),2);
          $cgst = round(floatval($row["totalTax"] / 2),2);
        }


        $payloadArr["ItemList"][] = [
          'SlNo' => ($key + 1) . "",
          'IsServc' => in_array($row["goodsType"], [5, 7]) ? "Y" : "N",
          'PrdDesc' => $row["itemName"],
          'HsnCd' => $row["hsnCode"] . "",
          'Qty' => floatval($row["qty"]),
          'FreeQty' => 0,
          'Unit' => $row["uomName"] != "" ? $row["uomName"] : "OTH",
          'UnitPrice' => round(floatval($row["unitPrice"]),2),
          'TotAmt' => round(floatval($row["qty"] * $row["unitPrice"]),2),
          'Discount' => round(floatval($row["totalDiscountAmt"]),2),
          'AssAmt' => round(floatval($row["totalPrice"] - $row["totalTax"]),2),
          'GstRt' => round(floatval($row["tax"]),2),
          'SgstAmt' => $sgst,
          'IgstAmt' => $igst,
          'CgstAmt' => $cgst,
          'CesRt' => 0,
          'CesAmt' => $cess,
          'TotItemVal' => round(floatval($row["totalPrice"]),2),
        ];
      }
    }
    return [
      "status" => "success",
      "message" => "Invoice",
      "custType" => $invoiceDetails["customer_gstin"] == "" ? "b2c" : "b2b",
      "data" => $payloadArr,
      "billingAddressObj" => $billingAddressObj
    ];

    
    // "data" => [
    //   "locationDetails" => $locationDetails,
    //   "branchDetails" => $branchDetails,
    //   "invoiceDetails" => $invoiceDetails,
    //   "customerDetails" => $customerDetails,
    //   "billingAddressData" => $billingAddress,
    //   "shippingAddressData" => $shippingAddress,
    //   "invoiceItemList" => $invoiceItemList
    // ],

  }

  function generateB2cEinvoice()
  {
    return [];
  }

  function generateEinvoice($payloadArr = [], $invoiceId = 0)
  {
    $authObj = $this->eInvoiceAuth();
    if ($authObj["status"] != "success") {
      return $authObj;
    }

    $authData = $authObj["data"];
    $checkPrevEinvoiceObj = queryGet('SELECT * FROM `erp_e_invoices` WHERE `invoice_id`=' . $invoiceId . ' AND `location_id`=' . $this->location_id . ' AND `branch_id`=' . $this->branch_id . ' AND `company_id`=' . $this->company_id);
    if ($checkPrevEinvoiceObj["status"] == "success") {
      $prevEinvoiceData = $checkPrevEinvoiceObj["data"];
      return [
        "status" => "success",
        "message" => "E-Invoice already created",
        "data" => [
          "ack_no" => $prevEinvoiceData["ack_no"],
          "ack_date" => $prevEinvoiceData["ack_date"],
          "irn" => $prevEinvoiceData["irn"],
          "signed_invoice" => $prevEinvoiceData["signed_invoice"],
          "signed_qr_code" => $prevEinvoiceData["signed_qr_code"],
          "e_invoice_status" => $prevEinvoiceData["e_invoice_status"],
          "ewb_no" => $prevEinvoiceData["ewb_no"],
          "ewb_date" => $prevEinvoiceData["ewb_date"],
          "ewb_valid_till" => $prevEinvoiceData["ewb_valid_till"],
          "remarks" => $prevEinvoiceData["remarks"]
        ],
        "obj" => $checkPrevEinvoiceObj
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

    $responseData["status_cd"] = 0;

    if ($responseData["status_cd"] == 1) {
      $eInvDetails = $responseData["data"];
      $insertObj = queryInsert('INSERT INTO `erp_e_invoices` SET `company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`invoice_id`=' . $invoiceId . ',`ack_no`="' . $eInvDetails["AckNo"] . '",`ack_date`="' . $eInvDetails["AckDt"] . '",`irn`="' . $eInvDetails["Irn"] . '",`signed_invoice`="' . $eInvDetails["SignedInvoice"] . '",`signed_qr_code`="' . $eInvDetails["SignedQRCode"] . '",`e_invoice_status`="' . $eInvDetails["Status"] . '",`ewb_no`="' . $eInvDetails["EwbNo"] . '",`ewb_date`="' . $eInvDetails["EwbDt"] . '",`ewb_valid_till`="' . $eInvDetails["EwbValidTill"] . '",`remarks`="' . $eInvDetails["Remarks"] . '",`created_by`="' . $this->created_by . '",`updated_by`="' . $this->created_by . '"');

      if ($insertObj["status"] == "success") {
        return [
          "status" => "success",
          "message" => "E-Invoice created successfully",
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
          "message" => "E-Invoice created failed",
          "data" => $insertObj,
          "payload" => $payloadArr
        ];
      }
    } else {

      $messages = ["E-Invoice created failed."];
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
if ($_SERVER["REQUEST_METHOD"] == "GET") {
  $invoiceId = $_REQUEST["invoiceId"] ?? 0;
  if ($invoiceId > 0) {
    $eInvoiceObj = new eInvoice();
    $payloadObj = $eInvoiceObj->generatePayload($invoiceId);
    if ($payloadObj["status"] == "success" && $payloadObj["custType"] == "b2b") {
      $responseData = $eInvoiceObj->generateEinvoice($payloadObj["data"], $invoiceId);
      // $responseData = $payloadObj;

    } else {
      $responseData = [
        "status" => "warning",
        "message" => "B2C e-invoice in progress,  will be added soon.",
      ];
    }
  } else {
    $responseData = [
      "status" => "warning",
      "message" => "Please select valid invoice to generate E-Invoice",
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


console($responseData);
