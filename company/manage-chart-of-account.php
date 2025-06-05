<?php
include("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
include("../app/v1/functions/company/func-ChartOfAccounts.php");
include("../app/v1/functions/admin/func-company.php");



// console($_SESSION);
// exit();
$company_data = getCompanyDataDetails($company_id);
$queryGetNumRows = queryGet("SELECT id,gl_label FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id", true);
// console($queryGetNumRows);
if (isset($_POST["changeStatus"])) {
    $newStatusObj = ChangeStatusChartOfAccounts($_POST, "customer_id", "customer_status");
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
}


if (isset($_POST["createdata"])) {
    // console($_POST);
    $addNewObj = createDataChartOfAccounts($_POST);
    // console($addNewObj);
    swalToast($addNewObj["status"], $addNewObj["message"]);
}

if (isset($_GET["import"]) && $_GET["import"] == "importDefault") {
    if ($queryGetNumRows['numRows'] == 4) {
        $importNewObj = importDefaltChartOfAccounts();
        // console($importNewObj);
         swalToast($addNewObj["status"], $addNewObj["message"],$_SERVER['PHP_SELF']);
        //  exit;
    }
}

if (isset($_POST["editdata"])) {
    $editDataObj = updateDataChartOfAccounts($_POST);
    // console($editDataObj);

    swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}
?>

<link href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?=BASE_URL?>public/assets-2/tree/css/style.css">
<link rel="stylesheet" href="<?=BASE_URL?>public/assets-2/tree/css/treeSortable.css">

<style>
    .content-wrapper {
        padding: 30px 5px 10px 5px !important;
        height: auto !important;
    }

    .form {
        display: flex;
    }

    .form>div {
        display: inline-block;
        vertical-align: top;
    }

    .btn-transparent {
        position: absolute;
        top: 23px;
        left: 9px;
        height: 35px;
        z-index: 9;
        width: 92%;
        background: transparent !important;
    }

    body.sidebar-mini.layout-fixed.sidebar-collapse p.gl-code {
        position: absolute;
        right: 32em;
        top: 5px;
        z-index: 999;
        width: 100px;
        transition-delay: 0.2s;
    }

    body.sidebar-mini.layout-fixed p.gl-code {
        position: absolute;
        right: 22em;
        top: 5px;
        z-index: 999;
        width: 100px;
        transition-delay: 0.2s;
    }

    .gl-filter-list.filter-list {
        display: flex;
        gap: 7px;
        justify-content: flex-end;
        position: relative;
        top: 53px;
        left: -20px;
    }

    .gl-filter-list.filter-list a.active {
        background-color: #003060;
        color: #fff;
    }

    .preview-default-coa-modal .modal-dialog {
        max-width: 85%;
        min-height: 80%;
    }

    .preview-default-coa-modal .modal-content {
        min-height: 80vh;
    }

    .preview-default-coa-modal .modal-content .modal-body {
        height: 400px;
    }

    .accordion-button::after,
    .accordion-button:not(.collapsed)::after {
        display: none;
    }

    section.tree-table-2 {
        background: #f1f2f4 !important;
        height: 100%;
    }
    

    .wrapper-tree .tree-branch>.contents {
        top: -18px;
    }

    .wrapper-tree h2 button {
        border-radius: 10px 10px 0 0;
        color: #fff;
    }

    .wrapper-tree h2 button.collapsed {
        border-radius: 10px;
    }

    .wrapper-tree h2 button ion-icon {
        transform: rotate(180deg);
        margin-left: auto;
    }

    .wrapper-tree h2 button.collapsed ion-icon {
        transform: rotate(0deg);
        margin-left: auto;
    }

    select.form-control.view-dropdown {
        position: relative;
        top: 0;
        width: 145px;
        height: 30px;
        padding: 0 15px;
        font-size: 12px;
        right: 14px;
        background: #cfd8e1;
        border: 1px solid #00306030;
    }
</style>

<link rel="stylesheet" href="<?=BASE_URL?>public/assets/listing.css">
<link rel="stylesheet" href="<?=BASE_URL?>public/assets/sales-order.css">
<link rel="stylesheet" href="<?=BASE_URL?>public/assets/accordion.css">
<link rel="stylesheet" href="<?=BASE_URL?>public/assets/tree-table-2/tree-table-2.css">

<?php

$coatype = 'p_id';
if (isset($_GET['coatype']) && !empty($_GET['coatype'])) {
    $coatype = $_GET['coatype'];
}


if (isset($_GET['create']) && $_GET['create'] == 'account') {
?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <?php if (isset($msg)) { ?>
                <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
                    <?= $msg ?>
                </div>
            <?php } ?>
            <div class="container-fluid">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

                    <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Chart of Account</a></li>

                    <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Add Chart of Account</a></li>

                    <li class="back-button">

                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">

                            <i class="fa fa-reply po-list-icon"></i>

                        </a>

                    </li>

                </ol>
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-6 col-md-12 col-sm-12">

                        <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                            <div class="card-header">

                                <h4>Add Group</h4>
                            </div>

                            <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">
                                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_group_frm" name="add_group_frm">
                                    <input type="hidden" name="createdata" id="createdata" value="group">
                                    <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id; ?>">
                                    <div class="row">

                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                            <div class="row goods-info-form-view customer-info-form-view">

                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                    <div class="form-input">

                                                        <label for="">Select Parent*</label>

                                                        <!-- <button class="btn btn-primary btn-transparent" type="button" data-toggle="modal" data-target="#accountDropdown1"></button> -->

                                                        <select id="gp_id" name="parent" class="form-control form-control-border borderColor" required>
                                                            <option value="">Select Parent*</option>
                                                            <?php
                                                            $listResult = getAllChartOfAccounts_listGroup($company_id);
                                                            if ($listResult["status"] == "success") {
                                                                foreach ($listResult["data"] as $listRow) {
                                                            ?>

                                                                    <option value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label']; ?>
                                                                    </option>
                                                            <?php }
                                                            } ?>
                                                        </select>
                                                        <input type="hidden" name="personal_glcode_lvl" id="gpersonal_glcode_lvl" value="">
                                                        <input type="hidden" name="typeAcc" id="gpersonal_typeAcc" value="">
                                                    </div>

                                                    <span class="error " id="gp_id_error"></span>



                                                </div>


                                                <div class="col-lg-6 col-md-6 col-sm-6">

                                                    <div class="form-input">

                                                        <label for="">Group Name</label>

                                                        <input type="text" class="form-control" id="gl_label" name="gl_label" required>
                                                        <span class="error"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-12">

                                                    <div class="form-input">

                                                        <label for="">Group Description</label>

                                                        <input type="text" class="form-control" id="remark" name="remark">

                                                        <span class="error"></span>

                                                    </div>

                                                </div>
                                                <div class="btn-section mt-2 mb-2 ml-auto">
                                                    <button class="btn btn-primary save-close-btn float-right add_data waves-effect waves-light" value="add_post">Create Group</button>
                                                </div>



                                            </div>

                                        </div>

                                    </div>
                                </form>

                            </div>

                        </div>

                    </div>

                    <div class="col-lg-6 col-md-12 col-sm-12">

                        <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                            <div class="card-header">

                                <h4>Add Account</h4>

                            </div>

                            <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_account_frm" name="add_account_frm">
                                    <input type="hidden" name="createdata" id="createdata" value="account">
                                    <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id; ?>">
                                    <div class="row">

                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                            <div class="row goods-info-form-view customer-info-form-view">

                                                <div class="col-lg-6 col-md-6 col-sm-12">

                                                    <div class="form-input">

                                                        <label for="">Select Parent*</label>

                                                        <!-- <button class="btn btn-primary btn-transparent" type="button" data-toggle="modal" data-target="#accountDropdown"></button> -->

                                                        <select id="ap_id" name="parent" class="form-control form-control-border borderColor mt-0" required>
                                                            <option value="">Select Parent*</option>
                                                            <?php
                                                            $coaSql = "SELECT coaTable.* FROM
                                                                        " . ERP_ACC_CHART_OF_ACCOUNTS . " coaTable
                                                                            WHERE
                                                                                coaTable.company_id = $company_id 
                                                                                AND coaTable.glStType = 'group' AND coaTable.`status` != 'deleted'
                                                                            ORDER BY coaTable.id ASC";
                                                            $listResult = queryGet($coaSql, true);

                                                            if ($listResult["status"] == "success") {
                                                                foreach ($listResult["data"] as $listRow) {
                                                            ?>
                                                                    <option value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label']; ?>
                                                                    </option>
                                                            <?php }
                                                            } ?>
                                                        </select>

                                                        <input type="hidden" name="personal_glcode_lvl" id="apersonal_glcode_lvl" value="">
                                                        <input type="hidden" name="typeAcc" id="apersonal_typeAcc" value="">
                                                    </div>

                                                    <span class="error " id="ap_id_error"></span>

                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-12">

                                                    <div class="form-input">

                                                        <label for="">A/C Code</label>

                                                        <input type="text" class="form-control" id="gl_code_preview" name="gl_code_preview" readonly required>

                                                        <span class="error"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-12">

                                                    <div class="form-input">

                                                        <label for="">Account Name</label>

                                                        <input type="text" class="form-control" id="gl_label" name="gl_label" required>
                                                        <span class="error"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-12">

                                                    <div class="form-input">

                                                        <label for="">Account Description</label>

                                                        <input type="text" class="form-control" id="remark" name="remark">

                                                        <span class="error"></span>

                                                    </div>

                                                </div>

                                                <div class="btn-section mt-2 mb-2 ml-auto">
                                                    <button type="submit" class="btn btn-primary save-close-btn float-right add_data waves-effect waves-light" value="add_post">Create Account</button>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                            </div>
                            </form>

                        </div>

                    </div>

                </div>

            </div>
    </div>
    </section>
    <!-- /.content -->
    </div>

<?php
} else {

?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="gl-filter-list filter-list">
            <div class="btns-group d-flex justify-content-end">
                <?php
                if ($queryGetNumRows['numRows'] == 4) { ?>
                    <a class="btn btn-primary text-white" data-toggle="modal" data-target="#importAcc" id="import-acc">Import Default <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
                    <div class="modal fade zoom-in preview-default-coa-modal" id="importAcc" tabindex="-1" aria-labelledby="importAccLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Preview Default COA</h5>
                                </div>
                                <div class="modal-body">
                                    <?php echo previewGlTreeDefult(); ?>
                                </div>

                                <div class="modal-footer">
                                    <button onclick="window.location.href='<?php echo basename($_SERVER['PHP_SELF']) ?>?import=importDefault'" type="button" class="btn btn-success">Import Default</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php  } else {

                    $colmnsql = "SELECT COLUMN_NAME, COLUMN_COMMENT
                            FROM INFORMATION_SCHEMA.COLUMNS
                            WHERE TABLE_NAME = '" . ERP_ACC_CHART_OF_ACCOUNTS . "'
                            AND ORDINAL_POSITION > (
                                SELECT ORDINAL_POSITION 
                                FROM INFORMATION_SCHEMA.COLUMNS
                                WHERE TABLE_NAME = '" . ERP_ACC_CHART_OF_ACCOUNTS . "'
                                AND COLUMN_NAME = 'typeAcc'
                            )
                            AND ORDINAL_POSITION < (
                                SELECT ORDINAL_POSITION 
                                FROM INFORMATION_SCHEMA.COLUMNS
                                WHERE TABLE_NAME = '" . ERP_ACC_CHART_OF_ACCOUNTS . "'
                                AND COLUMN_NAME = 'lvl'
                            )";
                    $colmnResponce = queryGet($colmnsql, true)['data'];
                    // console($colmnResponce);
                    foreach ($colmnResponce as $colmn) {
                        $active = ' mr-2';
                        if ($coatype == $colmn['COLUMN_NAME']) {
                            $active = 'active';
                        }
                    ?>

                        <a href="manage-chart-of-account.php?coatype=<?= $colmn['COLUMN_NAME']; ?>" class="btn <?= $active; ?> waves-effect waves-light">
                            <i class="fa fa-list <?= $active; ?> mr-2"></i><?= $colmn['COLUMN_COMMENT']; ?>
                        </a>
                        <?php if ($coatype == $colmn['COLUMN_NAME']) { ?>
                            <select class="form-control view-dropdown" name="coaDropdown" id="coaDropdown">
                                <option value="manage-chart-of-account.php?coatype=<?= $colmn['COLUMN_NAME']; ?>" selected>Tree View</option>
                                <option value="sub-gl-view.php?coatype=<?= $colmn['COLUMN_NAME']; ?>">Sub GL View</option>
                                <option value="gl-view.php?coatype=<?= $colmn['COLUMN_NAME']; ?>">GL View</option>
                            </select>
                    <?php }
                    } ?>

                    <a class="btn btn-primary text-white" href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create=account" type="button" id="add-acc">Add Account <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
                <?php } ?>
            </div>
        </div>

        <section class="tree-table-2">
            <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                <li class="pt-2 px-3 my-2 d-flex justify-content-between align-items-center" style="width:100%">
                    <h3 class="card-title">Chart of Account </h3>

                </li>
            </ul>



            <?php //echo createGlTree(); 
            ?>


            <div class="container">
                <div class="wrapper-tree">
                    <?php
                    foreach ($queryGetNumRows['data'] as $key => $value) {
                        if ($key <= 3) {
                    ?>

                            <!-- Supriya Work -->
                            <h2>
                                <button class="accordion-button btn btn-primary p-3" type="button" data-bs-toggle="collapse" data-bs-target="#basicDetails<?= $key;?>" aria-expanded="true" aria-controls="flush-collapseOne">
                                    <?= $value['gl_label']; ?>
                                    <ion-icon name="chevron-down-outline" class="collapse-icon"></ion-icon>
                                </button>
                            </h2>
                            <div id="basicDetails<?= $key;?>" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample" style="">
                                <div class="accordion-body p-0">
                                    <div class="card bg-transparent h-100">
                                        <div class="card-body p-0" style="height: 20px !important;">
                                            <ul class="p-0" id="<?= $value['id']; ?>-tree"></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    <?php
                        }
                    }


                    $queryGet11 = queryGet("SELECT id,$coatype as parent_id, gl_label AS title, (lvl+1) as level1, ordering, lock_status, 1 AS txn_status, glStType as glsttype, gl_code   FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND typeAcc=1 AND `status`='active'  ORDER BY id,$coatype,lvl ASC", true)['data'];
                    $queryGet1 = orderArray($queryGet11);

                    $queryGet22 = queryGet("SELECT id,$coatype as parent_id, gl_label AS title, (lvl+1) as level1, ordering, lock_status, 1 AS txn_status, glStType as glsttype, gl_code   FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND typeAcc=2 AND `status`='active'  ORDER BY id,$coatype,lvl ASC", true)['data'];
                    $queryGet2 = orderArray($queryGet22);

                    $queryGet33 = queryGet("SELECT id,$coatype as parent_id, gl_label AS title, (lvl+1) as level1, ordering, lock_status, 1 AS txn_status, glStType as glsttype, gl_code   FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND typeAcc=3 AND `status`='active'  ORDER BY id,$coatype,lvl ASC", true)['data'];
                    $queryGet3 = orderArray($queryGet33);

                    $queryGet44 = queryGet("SELECT id,$coatype as parent_id, gl_label AS title, (lvl+1) as level1, ordering, lock_status, 1 AS txn_status, glStType as glsttype, gl_code   FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND typeAcc=4 AND `status`='active'  ORDER BY id,$coatype,lvl ASC", true)['data'];
                    $queryGet4 = orderArray($queryGet44);

                    // console($queryGet1);
                    ?>

                </div>
            </div>
            <!-- 
        <div class="container">
            <div class="wrapper-tree">
                <ul id="left-tree"></ul>
                <ul id="right-tree"></ul>
            </div>
        </div> -->


        </section>
        <!-- --------------Edit Modal------------------- -->
        <div class="modal fade right" id="GLedit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog w-50">
                <div class="modal-content">
                    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_group_frm" name="add_group_frm">
                        <div class="modal-header">
                            <h5 class="modal-title text-white" id="staticBackdropLabel">Edit Form</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row ReturnsDataDiv" style="row-gap: 7px;">
                                <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Loding...
                            </div>
                            <div class="row ReturnsDataDivoriginal" style="row-gap: 7px;">

                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- -------------------------Add a new child--------------------------- -->
        <div class="modal fade right" id="AddGLChild" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog w-50">
                <div class="modal-content">
                    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_child" name="add_child">
                        <div class="modal-header">
                            <h5 class="modal-title text-white" id="staticBackdropLabel">Add a new child</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row AddGLChildReturnsDataDiv" style="row-gap: 7px;">
                                <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Loding...
                            </div>
                            <div class="row AddGLChildReturnsDataDivoriginal" style="row-gap: 7px;">

                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- ------------------Add a new sibling-------------------- -->
        <div class="modal fade right" id="AddGLSibling" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog w-50">
                <div class="modal-content">
                    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_sibling" name="add_sibling">
                        <div class="modal-header">
                            <h5 class="modal-title text-white" id="staticBackdropLabel">Add a new sibling</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row AddGLSiblingReturnsDataDiv" style="row-gap: 7px;">
                                <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Loding...
                            </div>
                            <div class="row AddGLSiblingReturnsDataDivoriginal" style="row-gap: 7px;">

                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- ---------------------------------------------------------- -->

    </div>
<?php
}
include("common/footer.php");
?>

<script>
    $(document).ready(function() {
        $(document).on('click', '.edit-gst', function() {
            let type = $(this).data('type');
            let glsttype = $(this).data('glsttype');
            let glid = $(this).data('glid');
            let pid = $(this).data('pid');
            let coatype = '<?= $coatype; ?>';
            console.log("Getting value is - ", glid);
            $.ajax({
                url: `ajaxs/ajax-get-gl-detail-new.php?glid=${glid}&type=${type}&coatype=${coatype}&pid=${pid}&glsttype=${glsttype}`,
                type: 'get',
                beforeSend: function() {
                    $(".ReturnsDataDiv").show();
                    $(".ReturnsDataDivoriginal").hide();
                    $(".ReturnsDataDivoriginal").html('');
                    $(".modal-footer").hide();
                },
                success: function(response) {

                    $(".ReturnsDataDiv").hide();
                    $(".ReturnsDataDivoriginal").show();
                    $(".modal-footer").show();
                    $(".ReturnsDataDivoriginal").html(response);

                }
            });
        });
        // ------------------------------------------------------------
        $(document).on('click', '.add-glchild', function() {
            let type = $(this).data('type');
            let glsttype = $(this).data('glsttype');
            let glid = $(this).data('glid');
            let pid = $(this).data('pid');
            let coatype = '<?= $coatype; ?>';
            console.log("Getting value is - ", glid);
            $.ajax({
                url: `ajaxs/ajax-get-gl-detail-new.php?glid=${glid}&type=${type}&coatype=${coatype}&pid=${pid}&glsttype=${glsttype}`,
                type: 'get',
                beforeSend: function() {
                    $(".AddGLChildReturnsDataDiv").show();
                    $(".AddGLChildReturnsDataDivoriginal").hide();
                    $(".AddGLChildReturnsDataDivoriginal").html('');
                    $(".modal-footer").hide();
                },
                success: function(response) {

                    $(".AddGLChildReturnsDataDiv").hide();
                    $(".AddGLChildReturnsDataDivoriginal").show();
                    $(".modal-footer").show();
                    $(".AddGLChildReturnsDataDivoriginal").html(response);

                }
            });
        });
        // -----------------------------------------------------------

        $(document).on('click', '.add-glsibling', function() {
            let type = $(this).data('type');
            let glsttype = $(this).data('glsttype');
            let glid = $(this).data('glid');
            let pid = $(this).data('pid');
            let coatype = '<?= $coatype; ?>';
            console.log("Getting value is - ", glid);
            $.ajax({
                url: `ajaxs/ajax-get-gl-detail-new.php?glid=${glid}&type=${type}&coatype=${coatype}&pid=${pid}&glsttype=${glsttype}`,
                type: 'get',
                beforeSend: function() {
                    $(".AddGLSiblingReturnsDataDiv").show();
                    $(".AddGLSiblingReturnsDataDivoriginal").hide();
                    $(".AddGLSiblingReturnsDataDivoriginal").html('');
                    $(".modal-footer").hide();
                },
                success: function(response) {

                    $(".AddGLSiblingReturnsDataDiv").hide();
                    $(".AddGLSiblingReturnsDataDivoriginal").show();
                    $(".modal-footer").show();
                    $(".AddGLSiblingReturnsDataDivoriginal").html(response);

                }
            });
        });

        // -----------------------------------------------------------

        $(document).on('click', '.delete', function() {
            let type = $(this).data('type');
            let glsttype = $(this).data('glsttype');
            let glid = $(this).data('glid');
            let pid = $(this).data('pid');
            let coatype = '<?= $coatype; ?>';
            console.log("Getting value is - ", glid);
            Swal.fire({
                icon: `warning`,
                title: `Are you sure!`,
                text: `You want to delete this?`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: `ajaxs/ajax-get-gl-detail-new.php?glid=${glid}&type=${type}&coatype=${coatype}&pid=${pid}&glsttype=${glsttype}`,
                        type: 'get',
                        beforeSend: function() {
                            // $(".AddGLSiblingReturnsDataDiv").show();
                            // $(".AddGLSiblingReturnsDataDivoriginal").hide();
                            // $(".AddGLSiblingReturnsDataDivoriginal").html('');
                            // $(".modal-footer").hide();
                        },
                        success: function(responseData) {
                            responseObj = JSON.parse(responseData);
                            console.log(responseObj);
                            if (responseObj['status'] == 'success') {
                                let Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                                Toast.fire({
                                    icon: `${responseObj['status']}`,
                                    title: `&nbsp;${responseObj['message']}`
                                }).then(function() {
                                    // location.reload();
                                });

                            } else {
                                let Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                                Toast.fire({
                                    icon: `${responseObj['status']}`,
                                    title: `&nbsp;${responseObj['message']}`
                                }).then(function() {
                                    // location.reload();
                                });
                            }

                        }
                    });
                }
            });
        });

    });


    $('#coaDropdown').on('change', function() {
        // Get the selected option value
        const selectedOption = $(this).val();

        // Redirect to the selected URL
        if (selectedOption) {
            window.location.href = selectedOption;
        }
    });


    $(document).on("change", ".createdata", function() {
        let valll = $(this).val();
        if (valll == 'account') {
            $(".ac_code").show();
        } else {
            $(".ac_code").hide();
        }
    });



    /* $(".add_data").click(function() {
      var data = this.value;
      $("#createdata").val(data);
      //confirm('Are you sure to Submit?')
      $("#add_frm").submit();
    });*/
    $(".edit_data").click(function() {
        var data = this.value;
        $("#editdata").val(data);
        alert(data);
        //$( "#edit_frm" ).submit();
    });


    function srch_frm() {
        if ($('#form_date_s').val().trim() != '' && $('#to_date_s').val().trim() === '') { //$("#phone_r_err").css('display','block');
            //$("#phone_r_err").html("Your Phone Number");
            alert("Enter To Date");
            $('#to_date_s').focus();
            return false;
        }
        if ($('#to_date_s').val().trim() != '' && $('#form_date_s').val().trim() === '') { //$("#phone_r_err").css('display','block');
            //$("#phone_r_err").html("Your Phone Number");
            alert("Enter From Date");
            $('#form_date_s').focus();
            return false;
        }

    }

    function table_settings() {
        var favorite = [];
        $.each($("input[name='settingsCheckbox[]']:checked"), function() {
            favorite.push($(this).val());
        });
        var check = favorite.length;
        if (check < 5) {
            alert("Please Check Atlast 5");
            return false;
        }

    }


    $(document).ready(function() {


        $(document).on("change", "#gp_id", function() {
            let p_id = $(this).val();
            $("#gp_id_error").hide();
            $("#gp_id_error").html('');
            //alert(p_id);
            $.ajax({
                url: 'ajaxs/ajax_gl_code.php',
                data: {
                    p_id
                },
                type: 'POST',
                beforeSend: function() {
                    // $('.vendor_bank_cancelled_cheque').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                    //$(".vendor_bank_cancelled_cheque").toggleClass("disabled");
                },
                success: function(responseData) {
                    responseObj = JSON.parse(responseData);
                    console.log(responseObj);
                    if (responseObj['status'] == 'success') {
                        $("#gl_code_preview").val(responseObj['gl_code_preview']);
                        $("#gpersonal_glcode_lvl").val(responseObj['personal_glcode_lvl']);
                        $("#gpersonal_typeAcc").val(responseObj['personal_typeAcc']);
                    } else {
                        $("#gp_id_error").show();
                        $("#gp_id_error").html(responseObj['message']);
                    }
                }
            });

        });


        $(document).on("change", "#ap_id", function() {
            let p_id = $(this).val();
            $("#ap_id_error").hide();
            $("#ap_id_error").html('');
            //alert(p_id);
            $.ajax({
                url: 'ajaxs/ajax_gl_code.php',
                data: {
                    p_id
                },
                type: 'POST',
                beforeSend: function() {
                    // $('.vendor_bank_cancelled_cheque').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                    //$(".vendor_bank_cancelled_cheque").toggleClass("disabled");
                },
                success: function(responseData) {
                    responseObj = JSON.parse(responseData);
                    console.log(responseObj);
                    if (responseObj['status'] == 'success') {
                        $("#gl_code_preview").val(responseObj['gl_code_preview']);
                        $("#apersonal_glcode_lvl").val(responseObj['personal_glcode_lvl']);
                        $("#apersonal_typeAcc").val(responseObj['personal_typeAcc']);
                    } else {
                        $("#ap_id_error").show();
                        $("#ap_id_error").html(responseObj['message']);
                    }
                }
            });

        });

        $(document).on("imput keyup", ".group_imput", function() {
            let label_val = $(this).val();
            let p_id = 0;
            $("#p_id_error").hide();
            $("#p_id_error").html('');
            //alert(p_id);
            $.ajax({
                url: 'ajaxs/ajax_gl_code.php',
                data: {
                    p_id
                },
                type: 'POST',
                beforeSend: function() {
                    // $('.vendor_bank_cancelled_cheque').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                    //$(".vendor_bank_cancelled_cheque").toggleClass("disabled");
                },
                success: function(responseData) {
                    responseObj = JSON.parse(responseData);
                    console.log(responseObj);
                    if (responseObj['status'] == 'success') {
                        $("#personal_glcode_lvl").val(responseObj['personal_glcode_lvl']);
                        $("#personal_typeAcc").val(responseObj['personal_typeAcc']);
                    } else {
                        $("#p_id_error").show();
                        $("#p_id_error").html(responseObj['message']);
                    }
                }
            });

        });

        $('.select2')
            .select2()
            .on('select2:open', () => {
                $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal3">
    Add New
  </a></div>`);
            });
        //**************************************************************
        $('.select4')
            .select4()
            .on('select4:open', () => {
                $(".select4-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal4">
    Add New
  </a></div>`);
            });
    });
</script>

<script>
    $(document).on('click', '.accordion-button', function() {
        $(this).find('ion-icon').toggleClass('rotate-up');
        $(this).attr('aria-expanded', function(_, attr) {
            return attr === 'true' ? 'false' : 'true';
        });
    });


    ////////////////External Jquery////////////////////////////////

    $(document).ready(function() {


        const data1 = <?= json_encode($queryGet1); ?>;
        const data2 = <?= json_encode($queryGet2); ?>;
        const data3 = <?= json_encode($queryGet3); ?>;
        const data4 = <?= json_encode($queryGet4); ?>;

        ///////////////////////////////////////////
        const delay = () => {
            return new Promise(resolve => {
                setTimeout(() => {
                    resolve();
                }, 1000);
            });
        };

        
        const data1TreeId = '#1-tree';
        const data1Sortable = new TreeSortable({
            treeSelector: data1TreeId,
        });
        const $data1Tree = $(data1TreeId);
        const $data1content = data1.map(data1Sortable.createBranch);
        $data1Tree.html($data1content);
        data1Sortable.run();


        data1Sortable.onSortCompleted(async (event, ui) => {
            await delay();
            console.log('data1 tree', ui.item);
            console.log('data1 tree-ui', ui);
            console.log('data1 tree-event', event);

            var id = ui.item.data('id');
            var parent = ui.item.data('parent');
            var level = ui.item.data('level');
            var zIndex = ui.item.css('z-index');
            let coatype = '<?= $coatype; ?>';
            let glsttype = ui.item.data('glsttype');

            console.log('z-index:', zIndex);
            console.log('Id:', id);
            console.log('Parent:', parent);
            console.log('Level:', level);
            Swal.fire({
                icon: `warning`,
                title: `Are you sure!`,
                text: `You want to update this Ordering?`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: `ajaxs/ajax-get-gl-detail-new.php?glid=${id}&type=ordering&coatype=${coatype}&pid=${parent}&glsttype=${glsttype}&level=${level}`,
                        type: 'get',
                        beforeSend: function() {
                            // $(".AddGLSiblingReturnsDataDiv").show();
                            // $(".AddGLSiblingReturnsDataDivoriginal").hide();
                            // $(".AddGLSiblingReturnsDataDivoriginal").html('');
                            // $(".modal-footer").hide();
                        },
                        success: function(responseData) {
                            responseObj = JSON.parse(responseData);
                            console.log(responseObj);
                            if (responseObj['status'] == 'success') {
                                let Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                                Toast.fire({
                                    icon: `${responseObj['status']}`,
                                    title: `&nbsp;${responseObj['message']}`
                                }).then(function() {
                                    // location.reload();
                                });

                            } else {
                                let Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                                Toast.fire({
                                    icon: `${responseObj['status']}`,
                                    title: `&nbsp;${responseObj['message']}`
                                }).then(function() {
                                    location.reload();
                                });
                            }

                        }
                    });
                } else {
                    location.reload();
                }
            });
        });

        data1Sortable.addListener('click', '.add-child', function(event, instance) {
            event.preventDefault();
            instance.addChildBranch($(event.target));
        });

        data1Sortable.addListener('click', '.add-sibling', function(event, instance) {
            event.preventDefault();
            instance.addSiblingBranch($(event.target));
        });

        data1Sortable.addListener('click', '.remove-branch', function(event, instance) {
            event.preventDefault();

            const confirm = window.confirm('Are you sure you want to delete this branch?');
            if (!confirm) {
                return;
            }
            instance.removeBranch($(event.target));
        });


        ///////////////////////////////////////////

        const data2TreeId = '#2-tree';
        const data2Sortable = new TreeSortable({
            treeSelector: data2TreeId,
        });
        const $data2Tree = $(data2TreeId);
        const $data2content = data2.map(data2Sortable.createBranch);
        $data2Tree.html($data2content);
        data2Sortable.run();
        data2Sortable.onSortCompleted(async (event, ui) => {
            await delay();
            console.log('data2 tree', ui.item);
            console.log('data2 tree-ui', ui);
            console.log('data2 tree-event', event);

            var id = ui.item.data('id');
            var parent = ui.item.data('parent');
            var level = ui.item.data('level');
            var zIndex = ui.item.css('z-index');
            let coatype = '<?= $coatype; ?>';
            let glsttype = ui.item.data('glsttype');

            console.log('z-index:', zIndex);
            console.log('Id:', id);
            console.log('Parent:', parent);
            console.log('Level:', level);
            Swal.fire({
                icon: `warning`,
                title: `Are you sure!`,
                text: `You want to update this Ordering1?`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    $.ajax({
                        url: `ajaxs/ajax-get-gl-detail-new.php?glid=${id}&type=ordering&coatype=${coatype}&pid=${parent}&glsttype=${glsttype}&level=${level}`,
                        type: 'get',
                        beforeSend: function() {
                            // $(".AddGLSiblingReturnsDataDiv").show();
                            // $(".AddGLSiblingReturnsDataDivoriginal").hide();
                            // $(".AddGLSiblingReturnsDataDivoriginal").html('');
                            // $(".modal-footer").hide();
                        },
                        success: function(responseData) {
                            responseObj = JSON.parse(responseData);
                            console.log(responseObj);
                            if (responseObj['status'] == 'success') {
                                let Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                                Toast.fire({
                                    icon: `${responseObj['status']}`,
                                    title: `&nbsp;${responseObj['message']}`
                                }).then(function() {
                                    // location.reload();
                                });

                            } else {
                                let Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                                Toast.fire({
                                    icon: `${responseObj['status']}`,
                                    title: `&nbsp;${responseObj['message']}`
                                }).then(function() {
                                    location.reload();
                                });
                            }

                        }
                    });
                } else {
                    location.reload();
                }
            });
        });

        data2Sortable.addListener('click', '.add-child', function(event, instance) {
            event.preventDefault();
            instance.addChildBranch($(event.target));
        });

        data2Sortable.addListener('click', '.add-sibling', function(event, instance) {
            event.preventDefault();
            instance.addSiblingBranch($(event.target));
        });

        data2Sortable.addListener('click', '.remove-branch', function(event, instance) {
            event.preventDefault();

            const confirm = window.confirm('Are you sure you want to delete this branch?');
            if (!confirm) {
                return;
            }
            instance.removeBranch($(event.target));
        });




        ///////////////////////////////////////////

        const data3TreeId = '#3-tree';
        const data3Sortable = new TreeSortable({
            treeSelector: data3TreeId,
        });
        const $data3Tree = $(data3TreeId);
        const $data3content = data3.map(data3Sortable.createBranch);
        $data3Tree.html($data3content);
        data3Sortable.run();

        data3Sortable.onSortCompleted(async (event, ui) => {
            await delay();
            console.log('data3 tree', ui.item);
            console.log('data3 tree-ui', ui);
            console.log('data3 tree-event', event);

            var id = ui.item.data('id');
            var parent = ui.item.data('parent');
            var level = ui.item.data('level');
            var zIndex = ui.item.css('z-index');
            let coatype = '<?= $coatype; ?>';
            let glsttype = ui.item.data('glsttype');

            console.log('z-index:', zIndex);
            console.log('Id:', id);
            console.log('Parent:', parent);
            console.log('Level:', level);
            Swal.fire({
                icon: `warning`,
                title: `Are you sure!`,
                text: `You want to update this Ordering?`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: `ajaxs/ajax-get-gl-detail-new.php?glid=${id}&type=ordering&coatype=${coatype}&pid=${parent}&glsttype=${glsttype}&level=${level}`,
                        type: 'get',
                        beforeSend: function() {
                            // $(".AddGLSiblingReturnsDataDiv").show();
                            // $(".AddGLSiblingReturnsDataDivoriginal").hide();
                            // $(".AddGLSiblingReturnsDataDivoriginal").html('');
                            // $(".modal-footer").hide();
                        },
                        success: function(responseData) {
                            responseObj = JSON.parse(responseData);
                            console.log(responseObj);
                            if (responseObj['status'] == 'success') {
                                let Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                                Toast.fire({
                                    icon: `${responseObj['status']}`,
                                    title: `&nbsp;${responseObj['message']}`
                                }).then(function() {
                                    // location.reload();
                                });

                            } else {
                                let Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                                Toast.fire({
                                    icon: `${responseObj['status']}`,
                                    title: `&nbsp;${responseObj['message']}`
                                }).then(function() {
                                    location.reload();
                                });
                            }

                        }
                    });
                } else {
                    location.reload();
                }
            });
        });

        data3Sortable.addListener('click', '.add-child', function(event, instance) {
            event.preventDefault();
            instance.addChildBranch($(event.target));
        });

        data3Sortable.addListener('click', '.add-sibling', function(event, instance) {
            event.preventDefault();
            instance.addSiblingBranch($(event.target));
        });

        data3Sortable.addListener('click', '.remove-branch', function(event, instance) {
            event.preventDefault();

            const confirm = window.confirm('Are you sure you want to delete this branch?');
            if (!confirm) {
                return;
            }
            instance.removeBranch($(event.target));
        });



        ///////////////////////////////////////////

        const data4TreeId = '#4-tree';
        const data4Sortable = new TreeSortable({
            treeSelector: data4TreeId,
        });
        const $data4Tree = $(data4TreeId);
        const $data4content = data4.map(data4Sortable.createBranch);
        $data4Tree.html($data4content);
        data4Sortable.run();

        data4Sortable.onSortCompleted(async (event, ui) => {
            await delay();
            console.log('data4 tree', ui.item);
            console.log('data4 tree-ui', ui);
            console.log('data4 tree-event', event);

            var id = ui.item.data('id');
            var parent = ui.item.data('parent');
            var level = ui.item.data('level');
            var zIndex = ui.item.css('z-index');
            let coatype = '<?= $coatype; ?>';
            let glsttype = ui.item.data('glsttype');

            console.log('z-index:', zIndex);
            console.log('Id:', id);
            console.log('Parent:', parent);
            console.log('Level:', level);
            Swal.fire({
                icon: `warning`,
                title: `Are you sure!`,
                text: `You want to update this Ordering?`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: `ajaxs/ajax-get-gl-detail-new.php?glid=${id}&type=ordering&coatype=${coatype}&pid=${parent}&glsttype=${glsttype}&level=${level}`,
                        type: 'get',
                        beforeSend: function() {
                            // $(".AddGLSiblingReturnsDataDiv").show();
                            // $(".AddGLSiblingReturnsDataDivoriginal").hide();
                            // $(".AddGLSiblingReturnsDataDivoriginal").html('');
                            // $(".modal-footer").hide();
                        },
                        success: function(responseData) {
                            responseObj = JSON.parse(responseData);
                            console.log(responseObj);
                            if (responseObj['status'] == 'success') {
                                let Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                                Toast.fire({
                                    icon: `${responseObj['status']}`,
                                    title: `&nbsp;${responseObj['message']}`
                                }).then(function() {
                                    // location.reload();
                                });

                            } else {
                                let Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                                Toast.fire({
                                    icon: `${responseObj['status']}`,
                                    title: `&nbsp;${responseObj['message']}`
                                }).then(function() {
                                    location.reload();
                                });
                            }

                        }
                    });
                } else {
                    location.reload();
                }
            });
        });

        data4Sortable.addListener('click', '.add-child', function(event, instance) {
            event.preventDefault();
            instance.addChildBranch($(event.target));
        });

        data4Sortable.addListener('click', '.add-sibling', function(event, instance) {
            event.preventDefault();
            instance.addSiblingBranch($(event.target));
        });

        data4Sortable.addListener('click', '.remove-branch', function(event, instance) {
            event.preventDefault();

            const confirm = window.confirm('Are you sure you want to delete this branch?');
            if (!confirm) {
                return;
            }
            instance.removeBranch($(event.target));
        });


        //////////////////////////////////////////////////////////////////////////


        tippy('[data-tippy-content]');
    });
</script>


<!-- <script src="../../public/assets-2/tree/js/treeSortable.js"></script> -->
<script src="<?=BASE_URL?>/public/assets-2/tree/js/treeSortable.js"></script>

<!------Nodetree end------->