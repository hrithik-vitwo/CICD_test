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


  function generateDebitNotePayload($documentId = null)
  {

    $invoiceId = $documentId;
    $dbObj = new Database();

    $locationDetails = $dbObj->queryGet('SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id`=' . $this->location_id)["data"];

    $branchDetails = $dbObj->queryGet('SELECT * FROM `erp_branches` WHERE `branch_id`=' . $this->branch_id)["data"];

    $invoiceDetailsObj = $dbObj->queryGet('SELECT * FROM `erp_debit_note` WHERE `dr_note_id`=' . $invoiceId . ' AND `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id);
    $invoiceDetails = $invoiceDetailsObj["data"];

    $invoiceItemListObj = $dbObj->queryGet('SELECT items.*, uomTable.uomName, itemMater.* FROM `debit_note_item` AS items LEFT JOIN `erp_inventory_items` as itemMater ON items.item_id=itemMater.itemId LEFT JOIN `erp_inventory_mstr_uom` as uomTable ON itemMater.baseUnitMeasure=uomTable.uomId WHERE items.debit_note_id=' . $invoiceId, true);

    $invoiceItemList = $invoiceItemListObj["data"];

    // console($locationDetails);
    // console($branchDetails);
    // console($invoiceItemListObj);
    //console($invoiceDetails);
    $partyId = $invoiceDetails["party_id"];

    if ($invoiceDetails["debitor_type"] == "customer") {
      $partyObj = $dbObj->queryGet("SELECT * FROM `erp_customer` WHERE `customer_id`=$partyId AND `location_id`=$this->location_id");
      $partyAddObj = $dbObj->queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_id`=$partyId AND `customer_address_primary_flag`=1");
      // console($partyObj);
      // console($partyAddObj);
      $partyData = [
        "Gstin" => $partyObj["data"]["customer_gstin"],
        "LglNm" => $partyObj["data"]["legal_name"],
        "Pos" => $partyAddObj["data"]["customer_address_state_code"],
        "Stcd" => $partyAddObj["data"]["customer_address_state_code"],
        "Loc" => $partyAddObj["data"]["customer_address_location"],
        "Addr1" => substr($partyAddObj["data"]["customer_address_location"], 0, 100),
        "Pin" => $partyAddObj["data"]["customer_address_pin_code"]
      ];
    } elseif ($invoiceDetails["debitor_type"] == "vendor") {

      $partyObj = $dbObj->queryGet("SELECT * FROM `erp_vendor_details` WHERE `vendor_id`=$partyId AND `location_id`=$this->location_id");
      $partyAddObj = $dbObj->queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=$partyId AND `vendor_business_primary_flag`=1");
      // console($partyObj);
      // console($partyAddObj);
      $partyData = [
        "Gstin" => $partyObj["data"]["vendor_gstin"],
        "LglNm" => $partyObj["data"]["legal_name"],
        "Pos" => $partyAddObj["data"]["state_code"],
        "Stcd" => $partyAddObj["data"]["state_code"],
        "Loc" => $partyAddObj["data"]["vendor_business_location"],
        "Addr1" => substr($partyAddObj["data"]["vendor_business_location"], 0, 100),
        "Pin" => $partyAddObj["data"]["vendor_business_pin_code"]
      ];
    } else {
      $partyData = [];
    }

    $sellerData = [
      'Gstin' => $branchDetails["branch_gstin"],
      'LglNm' => $branchDetails["branch_legal_name"],
      'Addr1' => substr($locationDetails["othersLocation_building_no"] . ", " . $locationDetails["othersLocation_city"], 0, 100),
      'Loc' => $locationDetails["othersLocation_name"],
      'Pin' => intval($locationDetails["othersLocation_pin_code"]),
      'Stcd' => $locationDetails["state_code"]
    ];

    $buyerData = $partyData;

    $payloadArr = [];
    if ($invoiceDetails["customer_gstin"] == "" && $invoiceDetails["compInvoiceType"] == "R") {
      //B2C E-Invoice Details
    } else {
      //B2B E-Invoice Details
      $SupTypArr = [
        "LUT" => "EXPWOP",
        "CBW" => "",
        "SEWP" => "SEZWP",
        "SEWOP" => "SEZWOP",
      ];

      $SupTyp = $SupTypArr[$invoiceDetails["compInvoiceType"]] ?? "B2B";

      if ($partyData["Gstin"] == "") {
        $partyData["Gstin"] = "URP";
        $partyData["Pos"] = "96";
        $partyData["Stcd"] = "96";
        $partyData["Pin"] = 999999;
      }

      //$invoiceDetails["invoice_no"]
      $payloadArr = [
        'Version' => '1.1',
        'TranDtls' => [
          'TaxSch' => 'GST',
          'SupTyp' => $SupTyp
        ],
        'DocDtls' => [
          'Typ' => 'DBN',
          'No' => substr($invoiceDetails["debit_note_no"], 0, 16),
          'Dt' => date("d/m/Y", strtotime($invoiceDetails["postingDate"])),
        ],
        'SellerDtls' => $sellerData,
        'BuyerDtls' => $buyerData,
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

      // $payloadArr["ValDtls"]["AssVal"] = round(floatval($invoiceDetails["sub_total_amt"]), 2);
      $payloadArr["ValDtls"]["AssVal"] = 0;
      $payloadArr["ValDtls"]["CgstVal"] = round(floatval($invoiceDetails["cgst"]), 2);
      $payloadArr["ValDtls"]["SgstVal"] = round(floatval($invoiceDetails["sgst"]), 2);
      $payloadArr["ValDtls"]["IgstVal"] = round(floatval($invoiceDetails["igst"]), 2);
      $payloadArr["ValDtls"]["RndOffAmt"] = round(floatval($invoiceDetails["adjustment"]), 2);
      $payloadArr["ValDtls"]["TotInvVal"] = round(floatval($invoiceDetails["total"]), 2);

      $documentSubTotal = 0;

      foreach ($invoiceItemList as $key => $row) {
        $sgst = 0;
        $cgst = 0;
        $igst = 0;
        $cess = 0;

        $total = floatval($row["item_qty"] * $row["item_rate"]);
        $discount = 0;
        $subTotal = $total - $discount;
        $documentSubTotal += $subTotal;

        if ($payloadArr["ValDtls"]["IgstVal"] > 0) {
          // $igst = round(floatval($row["totalTax"]), 2);
          $igst = round(floatval($row["igst"]), 2);
        } else {
          // $sgst = round(floatval($row["totalTax"] / 2), 2);
          $sgst = round(floatval($row["sgst"]), 2);
          // $cgst = round(floatval($row["totalTax"] / 2), 2);
          $cgst = round(floatval($row["cgst"]), 2);
        }
        $payloadArr["ItemList"][] = [
          'SlNo' => ($key + 1) . "",
          'IsServc' => in_array($row["goodsType"], [5, 7]) ? "Y" : "N",
          'PrdDesc' => $row["itemName"],
          'HsnCd' => $row["hsnCode"] . "",
          'Qty' => floatval($row["item_qty"]),
          'FreeQty' => 0,
          'Unit' => $row["uomName"] != "" ? $row["uomName"] : "OTH",
          'UnitPrice' => round(floatval($row["item_rate"]), 2),
          'TotAmt' => round($total, 2),
          'Discount' => round($discount, 2),
          'AssAmt' => round($subTotal, 2),
          'GstRt' => round(floatval($row["item_tax"]), 2),
          'SgstAmt' => $sgst,
          'IgstAmt' => $igst,
          'CgstAmt' => $cgst,
          'CesRt' => 0,
          'CesAmt' => $cess,
          'TotItemVal' => round(floatval($row["item_amount"]), 2),
        ];
      }

      $payloadArr["ValDtls"]["AssVal"] = round($documentSubTotal, 2);
    }

    if (count($payloadArr) > 0) {
      return [
        "status" => "success",
        "message" => "E-Invoice payload generated success.",
        "data" => $payloadArr,
      ];
    } else {
      return [
        "status" => "warning",
        "message" => "Please select valid invoice for E-invoice.",
        "data" => $payloadArr
      ];
    }
  }

  function generateCreditNotePayload($documentId = null)
  {

    $invoiceId = $documentId;
    $dbObj = new Database();

    $locationDetails = $dbObj->queryGet('SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id`=' . $this->location_id)["data"];

    $branchDetails = $dbObj->queryGet('SELECT * FROM `erp_branches` WHERE `branch_id`=' . $this->branch_id)["data"];

    $invoiceDetailsObj = $dbObj->queryGet('SELECT * FROM `erp_credit_note` WHERE `cr_note_id`=' . $invoiceId . ' AND `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id);
    $invoiceDetails = $invoiceDetailsObj["data"];

    $invoiceItemListObj = $dbObj->queryGet('SELECT items.*, uomTable.uomName, itemMater.* FROM `credit_note_item` AS items LEFT JOIN `erp_inventory_items` as itemMater ON items.item_id=itemMater.itemId LEFT JOIN `erp_inventory_mstr_uom` as uomTable ON itemMater.baseUnitMeasure=uomTable.uomId WHERE items.credit_note_id=' . $invoiceId, true);

    $invoiceItemList = $invoiceItemListObj["data"];

    // console($locationDetails);
    // console($branchDetails);
    // console($invoiceItemListObj);
    // console($invoiceDetails);
    $partyId = $invoiceDetails["party_id"];

    if ($invoiceDetails["creditors_type"] == "customer") {
      $partyObj = $dbObj->queryGet("SELECT * FROM `erp_customer` WHERE `customer_id`=$partyId AND `location_id`=$this->location_id");
      $partyAddObj = $dbObj->queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_id`=$partyId AND `customer_address_primary_flag`=1");
      // console($partyObj);
      // console($partyAddObj);
      $partyData = [
        "Gstin" => $partyObj["data"]["customer_gstin"],
        "LglNm" => $partyObj["data"]["legal_name"],
        "Pos" => $partyAddObj["data"]["customer_address_state_code"],
        "Stcd" => $partyAddObj["data"]["customer_address_state_code"],
        "Loc" => $partyAddObj["data"]["customer_address_location"],
        "Addr1" => substr($partyAddObj["data"]["customer_address_location"], 0, 100),
        "Pin" => $partyAddObj["data"]["customer_address_pin_code"]
      ];
    } elseif ($invoiceDetails["creditors_type"] == "vendor") {

      $partyObj = $dbObj->queryGet("SELECT * FROM `erp_vendor_details` WHERE `vendor_id`=$partyId AND `location_id`=$this->location_id");
      $partyAddObj = $dbObj->queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=$partyId AND `vendor_business_primary_flag`=1");
      // console($partyObj);
      // console($partyAddObj);
      $partyData = [
        "Gstin" => $partyObj["data"]["vendor_gstin"],
        "LglNm" => $partyObj["data"]["legal_name"],
        "Pos" => $partyAddObj["data"]["state_code"],
        "Stcd" => $partyAddObj["data"]["state_code"],
        "Loc" => $partyAddObj["data"]["vendor_business_location"],
        "Addr1" => substr($partyAddObj["data"]["vendor_business_location"], 0, 100),
        "Pin" => $partyAddObj["data"]["vendor_business_pin_code"]
      ];
    } else {
      $partyData = [];
    }

    $sellerData = [
      'Gstin' => $branchDetails["branch_gstin"],
      'LglNm' => $branchDetails["branch_legal_name"],
      'Addr1' => substr($locationDetails["othersLocation_building_no"] . ", " . $locationDetails["othersLocation_city"], 0, 100),
      'Loc' => $locationDetails["othersLocation_name"],
      'Pin' => intval($locationDetails["othersLocation_pin_code"]),
      'Stcd' => $locationDetails["state_code"]
    ];

    $buyerData = $partyData;

    $payloadArr = [];
    if ($invoiceDetails["customer_gstin"] == "" && $invoiceDetails["compInvoiceType"] == "R") {
      //B2C E-Invoice Details
    } else {
      //B2B E-Invoice Details
      $SupTypArr = [
        "LUT" => "EXPWOP",
        "CBW" => "",
        "SEWP" => "SEZWP",
        "SEWOP" => "SEZWOP",
      ];

      $SupTyp = $SupTypArr[$invoiceDetails["compInvoiceType"]] ?? "B2B";

      if ($partyData["Gstin"] == "") {
        $partyData["Gstin"] = "URP";
        $partyData["Pos"] = "96";
        $partyData["Stcd"] = "96";
        $partyData["Pin"] = 999999;
      }

      //$invoiceDetails["invoice_no"]
      $payloadArr = [
        'Version' => '1.1',
        'TranDtls' => [
          'TaxSch' => 'GST',
          'SupTyp' => $SupTyp
        ],
        'DocDtls' => [
          'Typ' => 'CRN',
          'No' => substr($invoiceDetails["credit_note_no"], 0, 16),
          'Dt' => date("d/m/Y", strtotime($invoiceDetails["postingDate"])),
        ],
        'SellerDtls' => $sellerData,
        'BuyerDtls' => $buyerData,
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

      // $payloadArr["ValDtls"]["AssVal"] = round(floatval($invoiceDetails["sub_total_amt"]), 2);
      $payloadArr["ValDtls"]["AssVal"] = 0;
      $payloadArr["ValDtls"]["CgstVal"] = round(floatval($invoiceDetails["cgst"]), 2);
      $payloadArr["ValDtls"]["SgstVal"] = round(floatval($invoiceDetails["sgst"]), 2);
      $payloadArr["ValDtls"]["IgstVal"] = round(floatval($invoiceDetails["igst"]), 2);
      $payloadArr["ValDtls"]["RndOffAmt"] = round(floatval($invoiceDetails["adjustment"]), 2);
      $payloadArr["ValDtls"]["TotInvVal"] = round(floatval($invoiceDetails["total"]), 2);

      $documentSubTotal = 0;

      foreach ($invoiceItemList as $key => $row) {
        $sgst = 0;
        $cgst = 0;
        $igst = 0;
        $cess = 0;

        $total = floatval($row["item_qty"] * $row["item_rate"]);
        $discount = 0;
        $subTotal = $total - $discount;
        $documentSubTotal += $subTotal;

        if ($payloadArr["ValDtls"]["IgstVal"] > 0) {
          // $igst = round(floatval($row["totalTax"]), 2);
          $igst = round(floatval($row["igst"]), 2);
        } else {
          // $sgst = round(floatval($row["totalTax"] / 2), 2);
          $sgst = round(floatval($row["sgst"]), 2);
          // $cgst = round(floatval($row["totalTax"] / 2), 2);
          $cgst = round(floatval($row["cgst"]), 2);
        }
        $payloadArr["ItemList"][] = [
          'SlNo' => ($key + 1) . "",
          'IsServc' => in_array($row["goodsType"], [5, 7]) ? "Y" : "N",
          'PrdDesc' => $row["itemName"],
          'HsnCd' => $row["hsnCode"] . "",
          'Qty' => floatval($row["item_qty"]),
          'FreeQty' => 0,
          'Unit' => $row["uomName"] != "" ? $row["uomName"] : "OTH",
          'UnitPrice' => round(floatval($row["item_rate"]), 2),
          'TotAmt' => round($total, 2),
          'Discount' => round($discount, 2),
          'AssAmt' => round($subTotal, 2),
          'GstRt' => round(floatval($row["item_tax"]), 2),
          'SgstAmt' => $sgst,
          'IgstAmt' => $igst,
          'CgstAmt' => $cgst,
          'CesRt' => 0,
          'CesAmt' => $cess,
          'TotItemVal' => round(floatval($row["item_amount"]), 2),
        ];
      }

      $payloadArr["ValDtls"]["AssVal"] = round($documentSubTotal, 2);
    }

    if (count($payloadArr) > 0) {
      return [
        "status" => "success",
        "message" => "E-Invoice payload generated success.",
        "data" => $payloadArr,
      ];
    } else {
      return [
        "status" => "warning",
        "message" => "Please select valid invoice for E-invoice.",
        "data" => $payloadArr
      ];
    }
  }


  function generateEinvoice($payloadArr = [], $documentId = 0, $documentType = "DBN")
  {
    $invoiceId = $documentId;
    $authObj = $this->eInvoiceAuth();
    if ($authObj["status"] != "success") {
      return $authObj;
    }

    $authData = $authObj["data"];
    $checkPrevEinvoiceObj = queryGet("SELECT * FROM `erp_e_invoices` WHERE `invoice_id`=$invoiceId AND `document_type` = '$documentType' AND `location_id`=$this->location_id AND `branch_id`=$this->branch_id AND `company_id`=$this->company_id");

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

    $url = "https://api.mastergst.com/einvoice/type/GENERATE/version/V1_03?email=" . $this->API_CLIENT_EMAIL;
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

    // $responseData["status_cd"] = 0;

    if ($responseData["status_cd"] == 1) {
      $eInvDetails = $responseData["data"];
      $insertObj = queryInsert("INSERT INTO `erp_e_invoices` SET `document_type`='$documentType', `company_id`=$this->company_id,`branch_id`=$this->branch_id,`location_id`=$this->location_id, `invoice_id`=$invoiceId,`ack_no`='" . $eInvDetails["AckNo"] . "',`ack_date`='" . $eInvDetails["AckDt"] . "',`irn`='" . $eInvDetails["Irn"] . "',`signed_invoice`='" . $eInvDetails["SignedInvoice"] . "',`signed_qr_code`='" . $eInvDetails["SignedQRCode"] . "',`e_invoice_status`='" . $eInvDetails["Status"] . "',`ewb_no`='" . $eInvDetails["EwbNo"] . "',`ewb_date`='" . $eInvDetails["EwbDt"] . "',`ewb_valid_till`='" . $eInvDetails["EwbValidTill"] . "',`remarks`='" . $eInvDetails["Remarks"] . "',`created_by`='$this->created_by',`updated_by`='$this->created_by'");

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

// entry points
$responseData = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $documentId = $_POST["documentId"] ?? 0;
  $documentType = strtoupper($_POST["documentType"]); //INV,CRN,DBN

  if ($documentId > 0) {
    $eInvoiceObj = new eInvoice();
    if ($documentType == "DBN") {
      $payloadObj = $eInvoiceObj->generateDebitNotePayload($documentId);
    } else if ($documentType == "CRN") {
      $payloadObj = $eInvoiceObj->generateCreditNotePayload($documentId);
    } else {
      $payloadObj = [
        "status" => "warning",
        "message" => "Invalid document type",
        "data" => []
      ];
    }
    $responseData = $payloadObj;
    if ($payloadObj["status"] == "success") {
      $responseData = $eInvoiceObj->generateEinvoice($payloadObj["data"], $documentId, $documentType);
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
  // $eInvoiceObj = new eInvoice();
  // $drPayloadObj = $eInvoiceObj->generateDebitNotePayload(111);
  // $crPayloadObj = $eInvoiceObj->generateCreditNotePayload(150);
  // $responseData = [
  //   "debitNote" => $drPayloadObj,
  //   "creditNote" => $crPayloadObj
  // ];

  $responseData = [
    "status" => "warning",
    "message" => "Method not allowed",
    "data" => []
  ];
}

header("Content-Type: application/json");
echo json_encode($responseData, true);
