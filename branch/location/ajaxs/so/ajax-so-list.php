<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$soListObj = new BranchSo();
if ($_GET['act'] === "itemWiseSearch") {
    $soDetails = $soListObj->fetchBranchSoListing()['data'];
    // console($soListObj->fetchBranchSoListing()['data']);

    // console($soListObj->fetchAllSoDeliverySchedule()['data']);
    $lists = $soListObj->fetchAllSoDeliverySchedule()['data'];
?>
    <table>
        <thead>
            <tr>
                <th>So Number</th>
                <th>Delivery Date</th>
                <th>SO Date</th>
                <th>Credit Period</th>
                <th>Item Code</th>
                <th>Total Qty</th>
                <th>Uom</th>
                <th>Tax</th>
                <th>Total Discount</th>
                <th>Total Price</th>
                <th>Delivery Status</th>
                <th>Delivery Qty</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($lists as $list) { ?>
                <tr>
                    <td><?= $list['so_number'] ?></td>
                    <td><?= $list['delivery_date'] ?></td>
                    <td><?= $list['so_date'] ?></td>
                    <td><?= $list['credit_period'] ?></td>
                    <td><?= $list['itemCode'] ?></td>
                    <td><?= $list['total_quantity'] ?></td>
                    <td><?= $list['uom'] ?></td>
                    <td><?= $list['tax'] ?></td>
                    <td><?= $list['total_discount'] ?></td>
                    <td><?= $list['item_total_price'] ?></td>
                    <td><?= $list['deliveryStatus'] ?></td>
                    <td><?= $list['delivery_qty'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

<?php
}
