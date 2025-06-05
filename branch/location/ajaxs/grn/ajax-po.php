<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-po-controller.php");
$BranchPoObj = new BranchPo();
if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    global $company_id;
    global $branch_id;
    global $location_id;
    $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
    $companyCurrencyData = $companyCurrencyObj["data"];
    $currency_name=$companyCurrencyData["currency_name"];

    $cond = '';

    if (isset($_POST['form_date_s']) && $_POST['form_date_s'] != '') {
        $cond .= " AND po_date between '" . $_POST['form_date_s'] . "' AND '" . $_POST['to_date_s'] . "'";
      }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (erp_branch_purchase_order.`po_number` like '%" . $_POST['keyword'] . "%' OR erp_vendor_details.`vendor_code` like '%" . $_POST['keyword'] . "%' OR erp_vendor_details.`trade_name` like '%" . $_POST['keyword'] . "%')";
    }

    $poDetailsObj = queryGet('SELECT * FROM `erp_branch_purchase_order` LEFT JOIN `erp_vendor_details` ON erp_vendor_details.vendor_id = erp_branch_purchase_order.vendor_id WHERE erp_branch_purchase_order.`company_id`=' . $company_id . ' AND erp_branch_purchase_order.`branch_id`=' . $branch_id . ' AND erp_branch_purchase_order.`location_id`=' . $location_id . ' AND erp_branch_purchase_order.`po_status`="9" '.$cond.' ORDER BY erp_branch_purchase_order.po_id DESC', true);
    $poDetails = $poDetailsObj["data"] ?? [];

    $poItemSl = 1;
    foreach ($poDetails as $onePoList) {

        ?>
        
        <tr>
        <td><?= $onePoList['po_number'] ?></td>
        <td><?= formatDateORDateTime($onePoList['po_date']) ?></td>
        <td><?= $onePoList['ref_no'] ?></td>
        <td><?= $onePoList['trade_name'] ?></td>
        <td><?= $onePoList['vendor_code'] ?></td>
        <td><?= $onePoList['use_type'] ?></td>
        <td><?=$currency_name ?> <?= decimalValuePreview($onePoList['totalAmount']) ?></td>
        <td>
        <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $onePoList['po_number'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
        
        <div class="modal fade right customer-modal pending-po-open-modal" id="fluidModalRightSuccessDemo_<?= $onePoList['po_number'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                <!--Content-->
                <div class="modal-content">
                    <!--Header-->
                    <div class="modal-header">

                        <div class="customer-head-info">
                            <div class="customer-name-code">
                                <h2><?= $currency_name?> <?= $onePoList['totalAmount'] ?></h2>
                                <p class="heading lead"><?= $onePoList['po_number'] ?></p>
                                <p>REF :&nbsp;<?= $onePoList['ref_no'] ?></p>
                            </div>
                            <?php
                            $vendorDetails = $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0];
                            ?>
                            <div class="customer-image">
                                <div class="name-item-count">
                                    <h5><?= $vendorDetails['trade_name'] ?></h5>
                                    <span>
                                        <div class="round-item-count"><?= $onePoList['totalItems'] ?></div> Items
                                    </span>
                                </div>
                                <i class="fa fa-user"></i>
                            </div>
                        </div>

                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= $onePoList['po_number'] ?>" role="tab" aria-controls="home" aria-selected="true">Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile<?= $onePoList['po_number'] ?>" role="tab" aria-controls="profile" aria-selected="false">Vendor Details</a>
                            </li>
                            <?php
                            // if ($onePoList['use_type'] == "service" || $onePoList['use_type'] == "servicep") {
                            ?>
                                <!-- <li class="nav-item">
                                    <a class="nav-link" id="" data-toggle="" href="manage-manual-grn.php?view=<?= $onePoList['po_number'] ?>&type=srn"> SRN</a>
                                </li> -->
                            <?php
                            // } else {
                            ?>
                                <li class="nav-item">
                                    <a class="nav-link" id="" data-toggle="" href="manage-manual-grn.php?view=<?= $onePoList['po_number'] ?>&type=grn"> GRN</a>
                                </li>
                            <?php
                            // }
                            ?>
                            <!-- -------------------Audit History Button Start------------------------- -->
                            <li class="nav-item">
                                <a class="nav-link auditTrail" id="history-tab<?= $onePoList['po_number'] ?>" data-toggle="tab" data-ccode="<?= $onePoList['po_number'] ?>" href="#history<?= $onePoList['po_number'] ?>" role="tab" aria-controls="history<?= $onePoList['po_number'] ?>" aria-selected="false"><i class="fa fa-history mr-2"></i>Trail</a>
                            </li>
                            <!-- -------------------Audit History Button End------------------------- -->
                        </ul>
                    </div>
                    <div class="modal-body">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="home-tab">
                                <?php
                                $itemDetails = $BranchPoObj->fetchBranchPoItems($onePoList['po_id'])['data'];
                                foreach ($itemDetails as $oneItem) {
                                ?>
                                    <form action="" method="POST">

                                        <div class="hamburger">
                                            <div class="wrapper-action">
                                                <i class="fa fa-cog fa-2x"></i>
                                            </div>
                                        </div>

                                        <div class="nav-action" id="thumb">
                                            <a title="Notify Me" href="" name="vendorEditBtn">
                                                <i class="fa fa-bell"></i>
                                            </a>
                                        </div>
                                        <div class="nav-action" id="create">
                                            <a title="Edit" href="" name="vendorEditBtn">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </div>
                                        <div class="nav-action trash" id="share">
                                            <a title="Delete" href="" name="vendorEditBtn">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>

                                    </form>


                                    <div class="item-detail-section">
                                        <h6>Items Details</h6>

                                        <div class="card">
                                            <div class="card-body">

                                                <div class="row">

                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                        <div class="left-section">
                                                            <div class="icon-img">
                                                                <i class="fa fa-box"></i>
                                                            </div>
                                                            <div class="code-des">
                                                                <h4><?= $oneItem['itemCode'] ?></h4>
                                                                <p><?= $oneItem['itemName'] ?></p>
                                                                <p><?= decimalValuePreview($oneItem['unitPrice']) ?></p>
                                                                <p>
                                                                    <h10>Quantity- <?= decimalQuantityPreview($oneItem['qty']) . "  " . $oneItem['uom'] ?></h10>
                                                                </p>
                                                                <p>
                                                                    <h10>Remaining Quantity- <?php if ($oneItem['remainingQty'] != "") {
                                                                                                    echo decimalQuantityPreview($oneItem['remainingQty']) . "  " . $oneItem['uom'];
                                                                                                } else {
                                                                                                    echo 0 . "  " . $oneItem['uom'];
                                                                                                }
                                                                                                ?></h10>
                                                                </p>
                                                                <p>
                                                                    <h10>Total Price- <?= $currency_name?> <?= decimalValuePreview($oneItem['total_price']) ?></h10>
                                                                </p>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <?php
                                                $deliverySchedule = $BranchPoObj->fetchBranchPoItemsDeliverySchedule($oneItem['po_item_id'])['data'];
                                                foreach ($deliverySchedule as $dSchedule) {
                                                ?>
                                                    <div class="row">
                                                        <div class="col-lg-8 col-md-8 col-sm-8">
                                                            <div class="left-section">
                                                                <div class="icon-img">
                                                                    <i class="fa fa-clock"></i>
                                                                </div>
                                                                <div class="date-time-parent">
                                                                    <div class="date-time">
                                                                        <div class="code-des">
                                                                            <h4>
                                                                                <?php
                                                                                // $timestamp = $dSchedule['delivery_date'];
                                                                                // $dt1 = date_format($timestamp, "d");
                                                                                echo formatDateWeb($dSchedule['delivery_date']);
                                                                                // $date=date_create($dSchedule['delivery_date']);
                                                                                // echo date_format($date,"Y/F/d");
                                                                                ?>
                                                                            </h4>
                                                                        </div>
                                                                        <p>
                                                                            <?php
                                                                            // echo $timestamp = $dSchedule['delivery_date'];
                                                                            // $dt2 = date("Y", strtotime($timestamp));
                                                                            // echo $dt2;
                                                                            ?>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                                            <div class="right-section unit">
                                                                <div class="dropdown">
                                                                    <button class="btn btn-secondary dropdown-toggle date-time-item" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <?= $dSchedule['qty'] ?> <?= $oneItem['uom'] ?>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>

                                    </div>
                                <?php } ?>
                                <!-- <a href="pending-po.php?approve=<?= $onePoList['po_id'] ?>" class="btn btn-primary">Approve PO</a> -->
                            </div>



                            <div class="tab-pane fade" id="profile<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="accordion accordion-flush customer-details-sells-order" id="accordionFlushCustDetails">
                                            <div class="accordion-item customer-details">
                                                <h2 class="accordion-header" id="flush-headingOne">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOnePo" aria-expanded="false" aria-controls="flush-collapseOne">
                                                        <span>Vendor Details</span>
                                                    </button>
                                                </h2>
                                                <div id="flush-collapseOnePo" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body cust-detsils-body">

                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-lg-2 col-md-2 col-sm-2">
                                                                        <?php
                                                                        $vendorDetails = $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0];
                                                                        ?>
                                                                        <div class="icon">
                                                                            <i class="fa fa-hashtag"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                                        <span>Vendor Code</span>
                                                                    </div>
                                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                                        <p>
                                                                            <?= $vendorDetails['vendor_code'] ?>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-lg-2 col-md-2 col-sm-2">
                                                                        <div class="icon">
                                                                            <i class="fa fa-user"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                                        <span>Vendor Name</span>
                                                                    </div>
                                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                                        <p>
                                                                            <?= $vendorDetails['trade_name'] ?>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-lg-2 col-md-2 col-sm-2">
                                                                        <div class="icon">
                                                                            <i class="fa fa-file"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                                        <span>GST</span>
                                                                    </div>
                                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                                        <p>
                                                                            <?= $vendorDetails['vendor_gstin'] ?>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- -------------------Audit History Tab Body Start------------------------- -->
                            <div class="tab-pane fade" id="history<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                <div class="audit-head-section mb-3 mt-3 ">
                                    <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($onePoList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePoList['created_at']) ?></p>
                                    <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($onePoList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePoList['updated_at']) ?></p>
                                </div>
                                <hr>
                                <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $onePoList['po_number'] ?>">

                                    <ol class="timeline">

                                        <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                            <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                            <div class="new-comment font-bold">
                                                <p>Loading...
                                                <ul class="ml-3 pl-0">
                                                    <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                </ul>
                                                </p>
                                            </div>
                                        </li>
                                        <p class="mt-0 mb-5 ml-5">Loading...</p>

                                        <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                            <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                            <div class="new-comment font-bold">
                                                <p>Loading...
                                                <ul class="ml-3 pl-0">
                                                    <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                </ul>
                                                </p>
                                            </div>
                                        </li>
                                        <p class="mt-0 mb-5 ml-5">Loading...</p>


                                    </ol>
                                </div>
                            </div>
                            <!-- -------------------Audit History Tab Body End------------------------- -->

                        </div>

                    </div>
                </div>
            </div>
            <!--/.Content-->
        </div>
        </td>
        </tr>


        <?php
        $poItemSl++;
        
    }
}

?>