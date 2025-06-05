<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../common/header.php");
require_once("../../common/navbar.php");
require_once("../../common/sidebar.php");
require_once("../../common/pagination.php");
require_once("../../../app/v1/functions/company/func-branches.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-customers-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/func-brunch-po-controller.php");

?>
<style>
    .wrapper {
        min-height: auto !important;
    }

    @media print {
        .sidebar-mini.sidebar-collapse .content-wrapper {
            margin-left: 0 !important;
        }

        .page-break {
            page-break-after: always;
        }
    }
</style>

<div class="container mt-5">
    <?php
    if (isset($_GET['poId'])) {
        $poId = ($_GET['poId']);
        console($poId);
        $templatePoObj = new TemplatePoController();
        $templatePoObj->printPoItems($poId);
    } 

    ?>
</div>
<?php
require_once("../../common/footer.php");
?>
<script>
    $(document).ready(function() {
        window.print();
    })
</script>