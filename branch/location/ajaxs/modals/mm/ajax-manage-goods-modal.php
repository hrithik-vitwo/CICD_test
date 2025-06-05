<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-po-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../../app/v1/functions/common/templates/template-item-master-controller.php");

$headerData = array('Content-Type: application/json');
$dbObj = new Database();
$BranchPoObj = new BranchPo();
$ItemsObj = new ItemsController();
$tempObj = new TemplateItemController();
$goodsController = new GoodsController();




function getHsnDesc($hsnCode)
{
    $sql = "SELECT hsnDescription as hsnDesc  FROM `erp_hsn_code` WHERE hsnCode=$hsnCode";
    $res = queryGet($sql);

    if ($res['status'] == "success") {
        return $res['data']['hsnDesc'];
    } else {
        return null;
    }
}
function getImagesByItemId($itemId, $company_id)
{
    $sql = queryGet("SELECT * FROM `erp_inventory_item_images` WHERE item_id=$itemId AND `company_id`=$company_id ;", true);
    $imageArray = $sql['data'];
    $imageNamesArray = [];
    foreach ($imageArray as $image) {
        $imageNamesArray[] = $image['image_name'];
    }
    return $imageNamesArray;
}
function getUomName($uomId)
{
    $sql = "SELECT uomName ,uomDesc FROM `erp_inventory_mstr_uom` WHERE uomId=$uomId";
    $res = queryGet($sql);
    $data = $res['data'];
    if ($res['status'] == "success") {
        $result =  $data['uomName'] . " || " . $data['uomDesc'];
        return $result;
    } else {
        return null;
    }
}
function getStorageName($storagId, $company_id)
{
    $sql = "SELECT loc.storage_location_name as storage_location_name FROM `erp_storage_location` as loc WHERE loc.company_id=$company_id AND loc.storage_location_id=$storagId;";
    $res = queryGet($sql);
    $data = $res['data'];
    if ($res['status'] == "success") {
        return $data['storage_location_name'];
    }
}


function getPurchaseGroupName($purhchaseId,$company_id){
  $sql="SELECT * FROM `erp_inventory_mstr_purchase_groups` WHERE companyId=$company_id AND purchaseGroupId=$purhchaseId;";
  $res=queryGet($sql);
  return $res['data']['purchaseGroupName'];
}

function getDiscountNameArray($discountArrayId,$company_id,$branch_id,$location_id){
  $discountGroupName=[];
  $disCountArr=json_decode($discountArrayId);
  
  foreach ($disCountArr as $id){
    $discountSql="SELECT * FROM `erp_item_discount_group` WHERE item_discount_group_id=$id AND company_id=$company_id AND branch_id=$branch_id AND  location_id=$location_id";
    $discountQuery=queryGet($discountSql);
    $discountGroupName[]=$discountQuery['data']['item_discount_group'];
  }
  return $discountGroupName;

}

if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'modalData') {

    $itemId = $_GET['itemId'];
    $cond = "goods.company_id=$company_id  AND goods.itemId=$itemId AND goods.status!='deleted'";
    $sql_list = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` as goods WHERE " . $cond . "   ORDER BY itemId desc";
    $sqlMainQryObj = $dbObj->queryGet($sql_list);
    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    $dynamic_data = [];

    if ($num_list > 0) {
        // Good group tree  start
        $goodObj=$goodsController->findGoodGrpDetailById($data['goodsGroup']);

        $tree[] = [
            "goodGroupName" => $goodObj['goodGroupName'],
            "goodGroupId" => $data['goodsGroup'],
        ];

        if ($goodObj['groupParentId']!=0) {
            $parentId = $goodObj['groupParentId'];
            $resData = $goodsController->findGoodGrpDetailById($parentId);
            while ($resData) {
                $tree[] = [
                    "goodGroupName" => $resData['goodGroupName'],
                    "goodGroupId" => $resData['goodGroupId']
                ];

                if ($resData['groupParentId']==0) {
                    break;
                }

                $resData = $goodsController->findGoodGrpDetailById($resData['groupParentId']);
                $parentId = $resData['groupParentId'];
            }
        }
        // good group tree end
        $type = "service";
        $gldetails = getChartOfAccountsDataDetails($data['parentGlId'])['data'];

        // classification
        $goodTypeId = $data['goodsType'];
        $type_sql = $dbObj->queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
        $goodGroupId = $data['goodsGroup'];
        $group_sql = $dbObj->queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE `goodGroupId`=$goodGroupId ");
        $group_name = $group_sql['data']['goodGroupName'];
        $type_name = $type_sql['data']['goodTypeName'] ? $type_sql['data']['goodTypeName'] : '-';

        $classification = [
            "glName" => $gldetails['gl_label'],
            "glCode" => $gldetails['gl_code'],
            "typeName" => $type_name,
            "groupName" => $group_name
        ];

        if ($data['goodsType'] == 5 || $data['goodsType'] == 7 || $data['goodsType'] == 10) {

            $serviceDetails = [
                "itemName" => $data['itemName'],
                "itemDesc" => $data['itemDesc'],
                "hsnCode" => $data['hsnCode'],
                "glName" => $gldetails['gl_label'],
                "glCode" => $gldetails['gl_code'],
            ];
            // moving weighted price
            $summary_sql = $dbObj->queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE itemId=$itemId AND `company_id`=$company_id ");
            $summary_data = $summary_sql['data'];

            $dynamic_data = [
                "type" => $type,
                "dataObj" => $data,
                "companyCurrency" => getSingleCurrencyType($company_currency),
                "created_by" => getCreatedByUser($data['createdBy']),
                "created_at" => formatDateORDateTime($data['createdAt']),
                "updated_by" => getCreatedByUser($data['updatedBy']),
                "updated_at" => formatDateORDateTime($data['updatedAt']),
                "serviceDetails" => $serviceDetails,
                "hsnDesc" => getHsnDesc($data['hsnCode']),
                "serviceUnit" => getUomName($data['baseUnitMeasure']),
                "summaryData" => $summary_data,
                "classification" => $classification,
                "tree" => array_reverse($tree),
                "discountGroupName"=>getDiscountNameArray($data['discountGroup'],$company_id,$branch_id,$location_id)

            ];
        } else if ($data['goodsType'] != 5 || $data['goodsType'] != 7 || $data['goodsType'] != 10) {
            $type = "other";

            $item_id = $data['itemId'];
            $storage_sql = $dbObj->queryGet("SELECT * FROM `" . ERP_INVENTORY_STORAGE . "` WHERE `item_id`=$item_id AND `location_id`=$location_id");
            $storage_data = $storage_sql['data'];
            // console($storage_sql);

            $storageDetails = [
                "storageControl" => $storage_data['storage_control'] ?? "",
                "maxStoragePeriod" => $storage_data['maxStoragePeriod'],
                "maxStoragePeriodTimeUnit" => $storage_data['maxStoragePeriodTimeUnit'],
                "minRemainSelfLife" => $storage_data['minRemainSelfLife'],
                "minRemainSelfLifeTimeUnit" => $storage_data['minRemainSelfLifeTimeUnit'],
            ];

            // moving weighted price
            $summary_sql = $dbObj->queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE itemId=$itemId AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id ");
            $summary_data = $summary_sql['data'];

            // images response using  item id 
            $imageRes = getImagesByItemId($itemId, $company_id);

            // item specification
            $specification_sql = $dbObj->queryGet("SELECT specification,specification_detail FROM `erp_item_specification` WHERE item_id=$itemId AND branch_id=$branch_id AND company_id=$company_id AND location_id=$location_id;", true);
            $specification_data = $specification_sql['data'];
          
            // $discountArrayId=$data['discountGroup'];
            
            // console($discountGroupName);
            //console($discountArrayId);

            $dynamic_data = [
                "type" => $type,
                "dataObj" => $data,
                "companyCurrency" => getSingleCurrencyType($company_currency),
                "created_by" => getCreatedByUser($data['createdBy']),
                "created_at" => formatDateORDateTime($data['createdAt']),
                "updated_by" => getCreatedByUser($data['updatedBy']),
                "updated_at" => formatDateORDateTime($data['updatedAt']),
                "classification" => $classification,
                "storageDetails" => $storageDetails,
                "hsnDesc" => getHsnDesc($data['hsnCode']),
                "movWeightPrice" => $summary_data['movingWeightedPrice'],
                "techSpecification" => $specification_data,
                "baseUnitMeasure" => getUomName($data['baseUnitMeasure']),
                "issueUnitMeasure" => getUomName($data['issueUnitMeasure']),
                "itemPrice" => $summary_data['itemPrice'],
                "itemMaxDiscount" => $summary_data['itemMaxDiscount'],
                "summaryData" => $summary_data,
                "images" => $imageRes,
                "defaultStorageLocationName" =>($summary_data['default_storage_location'] > 0) ?  getStorageName($summary_data['default_storage_location'], $company_id):'',
                "qaStorageLocationName" => ($summary_data['qa_storage_location'] > 0) ? getStorageName($summary_data['qa_storage_location'], $company_id) : '',
                "tree" => array_reverse($tree),
                "purchaseGroupName"=>getPurchaseGroupName($data['purchaseGroup'],$company_id),
                "discountGroupName"=>getDiscountNameArray($data['discountGroup'],$company_id,$branch_id,$location_id),
                // "check"=>findGoodGrpDetailById($data['goodsGroup'],$company_id)
            ];
        }


        $res = [
            "status" => true,
            "msg" => "Success",
            "sql_list" => $sql_list,
            "data" => $dynamic_data
        ];
    } else {
        $res = [
            "status" => false,
            "msg" => "Error!",
            "sql" => $sqlMainQryObj
        ];
    }

    echo json_encode($res);
} else if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'classicView') {
    $itemId = $_GET['itemId'];
    $tempObj->printItemPreview($itemId);
}
else if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'stChange') {
    $itemId = $_GET['itemId'];
    $sql="UPDATE `erp_inventory_items` SET status='inactive' WHERE itemId=$itemId";
    $res =$dbObj->queryUpdate($sql);
    echo json_encode($res);
}
else if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'goodDel') {
    $id = $_GET['id'];
    $sql="UPDATE `erp_inventory_items` SET status='deleted' WHERE itemId=$id";
    $res =$dbObj->queryUpdate($sql);
    echo json_encode($res);
}
else if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'addItemLocModal') {
    $id = $_GET['id'];
    $sql="UPDATE `erp_inventory_items` SET status='deleted' WHERE itemId=$id";
    $res =$dbObj->queryUpdate($sql);
    echo json_encode($res);


?>
    
    <!-----add form modal start --->
    <div class="modal fade hsn-dropdown-modal" id="addToLocation_<?= $row['itemId'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
      <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <form method="POST" action="">
              <input type="hidden" name="createLocationItem" id="createLocationItem" value="">
              <input type="hidden" name="item_id" value="<?= $id ?>">


              <div class="row">


                <div class="col-lg-12 col-md-12 col-sm-12">

                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                    <div class="card-header">

                      <h4>Storage Details</h4>

                    </div>

                    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row goods-info-form-view customer-info-form-view">









                            <div class="col-lg-4 col-md-4 col-sm-4">

                              <div class="form-input">

                                <label for="">Storage Control</label>

                                <input type="text" name="storageControl" class="form-control">

                              </div>

                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4">

                              <div class="form-input">

                                <label for="">Max Storage Period</label>

                                <input type="text" name="maxStoragePeriod" class="form-control">

                              </div>

                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4">
                              <div class="form-input">
                                <label class="label-hidden" for="">Min Time Unit</label>
                                <select id="minTime" name="minTime" class="select2 form-control">
                                  <option value="">Min Time Unit</option>
                                  <option value="Day">Day</option>
                                  <option value="Month">Month</option>
                                  <option value="Hours">Hours</option>

                                </select>
                              </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Minimum Remain Self life</label>

                                <input type="text" name="minRemainSelfLife" class="form-control">

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <div class="form-input">
                                <label class="label-hidden" for="">Max Time Unit</label>
                                <select id="maxTime" name="maxTime" class="select2 form-control">
                                  <option value="">Max Time Unit</option>
                                  <option value="Day">Day</option>
                                  <option value="Month">Month</option>
                                  <option value="Hours">Hours</option>

                                </select>
                              </div>
                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>




                <div class="col-lg-12 col-md-12 col-sm-12">
                  <?php
                  //  }
                  if ($type_name == "Finished Good" || $type_name == "Service Sales" || $type_name == "FG Trading") {
                  ?>

                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                      <div class="card-header">

                        <h4>Pricing and Discount

                          <span class="text-danger">*</span>

                        </h4>

                      </div>

                      <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                        <div class="row">

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="row goods-info-form-view customer-info-form-view">

                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label for="">Default MRP</label>

                                  <input step="0.01" type="number" name="price" class="form-control price" id="exampleInputBorderWidth2" placeholder="price">

                                </div>

                              </div>

                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label for="">Default Discount</label>

                                  <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">

                                </div>




                              </div>

                            </div>

                          </div>

                        </div>

                      </div>
                    <?php }
                    ?>

                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <button class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Submit</button>
                    </div>


                    </div>






                </div>

            </form>

          </div>
          <div class="modal-body" style="height: 500px; overflow: auto;">
            <div class="card">

            </div>
          </div>
        </div>
      </div>
    </div>
    <!---end modal --->
    <?php
}