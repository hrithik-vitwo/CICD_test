<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");

if(isset($_GET["po"]) && isset($_GET["grn"]))

{

  function getItemCodeAndHsn($vendorCode, $vendorItemTitle)
        {
            global $company_id;
            global $branch_id;
            global $location_id;
            global $created_by;
            global $updated_by;

            $vendorGoodsCodeObj = queryGet("SELECT `itemId`,`itemCode`,`itemType` FROM `" . ERP_VENDOR_ITEM_MAP . "` WHERE `companyId`='" . $company_id . "' AND `vendorCode`='" . $vendorCode . "' AND `itemTitle`='" . strip_tags($vendorItemTitle) . "' ORDER BY `vendorItemMapId` DESC LIMIT 1");
            if ($vendorGoodsCodeObj["status"] == "success") {
                $itemCode = $vendorGoodsCodeObj["data"]["itemCode"];
                $itemType = $vendorGoodsCodeObj["data"]["itemType"];
                $item_id = $vendorGoodsCodeObj["data"]["itemId"];

                // console($item_id);

                // return $vendorItemTitle;


                $goodsHsnObj = queryGet("SELECT `itemId`, `itemName`, `hsnCode`,`baseUnitMeasure` FROM `" . ERP_INVENTORY_ITEMS . "` WHERE `company_id`='" . $company_id . "' AND `itemId`='" . $item_id . "'");
                if ($goodsHsnObj["status"] == "success") {

                    // return $goodsHsnObj["data"]["itemName"];

                    $baseunitmeasure = $goodsHsnObj["data"]["baseUnitMeasure"];

                    $getUOM = queryGet("SELECT `uomName` FROM `erp_inventory_mstr_uom` WHERE `uomId`='" . $baseunitmeasure . "'");

                    if ($getUOM["status"] == "success") {
                        return [
                            "itemCode" => $itemCode,
                            "itemHsn" => $goodsHsnObj["data"]["hsnCode"],
                            "itemId" => $goodsHsnObj["data"]["itemId"],
                            "itemName" => $goodsHsnObj["data"]["itemName"],
                            "uom" => $getUOM["data"]["uomName"],
                            "type" => $itemType
                        ];
                    } else {
                        return [
                            "itemCode" => $itemCode,
                            "itemHsn" => $goodsHsnObj["data"]["hsnCode"],
                            "itemId" => $goodsHsnObj["data"]["itemId"],
                            "itemName" => $goodsHsnObj["data"]["itemName"],
                            "uom" => "",
                            "type" => $itemType
                        ];
                    }
                } else {
                    return [
                        "itemCode" => $vendorGoodsCodeObj["data"]["itemCode"],
                        "itemHsn" => "",
                        "itemId" => "",
                        "itemName" => "",
                        "type" => $itemType
                    ];
                }
            } else {
                return [
                    "itemCode" => "",
                    "itemHsn" => "",
                    "itemId" => "",
                    "itemName" => "",
                    "type" => ""
                ];
            }
        }




    $po_id = $_GET["po"];
    $grn_id = $_GET["grn"];
    $vendorCode = $_GET["vendor_code"];

    $sql = "UPDATE `erp_grn_multiple` SET `po_no`='" . $po_id . "' WHERE `grn_mul_id`='" . $grn_id."'";

    $update = queryUpdate($sql);

    $grn = queryGet("SELECT * FROM `erp_grn_multiple` WHERE `grn_mul_id` = '" . $id . "'", false);

    $invoiceDataGet = $grn["data"];

    $invoice_data_json = unserialize($invoiceDataGet["grn_read_json"]);
    $invoiceData = $invoice_data_json["data"];

    $customerPurchaseOrder = $po_id;

    $poDetailsObj = queryGet('SELECT * FROM `erp_branch_purchase_order` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `po_status`="9" AND `po_number`="' . $customerPurchaseOrder . '"');
    $poDetails = $poDetailsObj["data"] ?? [];
    $poId = $poDetails["po_id"] ?? 0;
    $poItemsListObj = queryGet('SELECT * FROM `erp_branch_purchase_order_items` WHERE `po_id`=' . $poId, true);
    $poItemsList = $poItemsListObj["data"] ?? [];

    foreach ($invoiceData["Items"] as $oneItemObj) {

      $itemName = $oneItemObj["Description"];

      if ($vendorCode != "") {
          $itemCodeAndHsnObj = getItemCodeAndHsn($vendorCode, $itemName);
          //  console($oneItemData["Description"]);
          $internalItemId = $itemCodeAndHsnObj["itemId"];
          $internalItemCode = $itemCodeAndHsnObj["itemCode"];
          $internalItemUom = $itemCodeAndHsnObj["uom"];
          $itemType = $itemCodeAndHsnObj["type"];
          $itemHSN = $itemCodeAndHsnObj["itemHsn"];
          $itemName = $itemCodeAndHsnObj["itemName"];
      }

      $match = "Mismatched";
      $po_date = "";
      foreach($poItemsList as $poItem)
      {
          if($poItem["itemName"] == $itemName)
          {
              if($oneItemObj["UnitPrice"] == $poItem["unitPrice"] && $oneItemObj["Quantity"] == $poItem["qty"])
              {
                  $match = "Matched";
                  $po_date = $poItem["po_date"];
                  break;
              }
              else
              {
                  continue;
              }
          }
          else
          {
              continue;
          }
      }

    }


    $returnData = [
        "status" => "success",
        "message" => "PO updated successfully",
        "po_date" => $po_date
      ];

    echo json_encode($returnData);

}

?>