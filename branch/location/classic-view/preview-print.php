<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../common/header.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("../../../app/v1/functions/common/templates/template-manage-payment.php");
?>


<div class="container mt-5">
    <?php
   if (isset($_GET['so_id'])) {
        $so_id = base64_decode($_GET['so_id']);
        $templateSalesOrderControllerObj = new TemplateSalesOrderController();
        $templateSalesOrderControllerObj->printSalesOrder($so_id);
    }elseif (isset($_GET['pay_id'])) {
        $pay_id = base64_decode($_GET['pay_id']);
        $templatepayment = new TemplatePayment();
        $templatepayment->printManagePayment($pay_id);
    }
    ?>
</div>

<script>
    $(document).ready(function(){
        window.print();
    })
</script>