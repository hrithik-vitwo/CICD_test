<?php

require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");


?>

<style>
    .qa-specification p:nth-child(2) {
        position: absolute;
        left: 40%;
        font-weight: 600;
    }

    .qa-item-recieve-block {
        position: relative;
    }

    .qa-item-recieve-block p {
        position: relative;
    }

    .qa-item-recieve-block-sub-item p::before {
        content: '';
        display: inline-block;
        position: absolute;
        left: 0;
        top: 8px;
        background-color: #fff;
        width: 20px;
        height: 1px;
    }

    .qa-item-recieve-block-sub-item p {
        padding-left: 2rem;
    }

    .qa-checked-item p {
        position: relative;
        padding-left: 5rem;
    }

    .qa-item-recieve-block-sub-item {
        border-left: 1px solid #fff;
    }

    .qa-checked-item p::before {
        content: '';
        display: inline-block;
        position: absolute;
        left: 50px;
        top: -5px;
        background-color: #fff;
        width: 1px;
        height: 26px;
    }

    .qa-checked-item p::after {
        content: '';
        display: inline-block;
        position: absolute;
        left: 50px;
        top: 9px;
        background-color: #fff;
        width: 20px;
        height: 1px;
    }

    .qa-view-header {
        display: flex;
        align-items: flex-start;
        gap: 100px;
        padding-top: 3em;
    }

    button.submit_frm {
        width: 150px;
        float: right;
    }

    .qa-modal-body-acc-btn {
        font-size: 12px !important;
        font-weight: 600;
    }

    .pdf-view {
        width: 100%;
        height: 200px;
        border: 2px dotted #ccc;
        border-radius: 12px;
        margin: 53px 0;
        position: relative;
        box-shadow: -19px 31px 26px -16px #6f6f6f;
        transition-duration: 0.2s;
        display: grid;
        place-items: center;
    }

    .pdf-view:hover {
        box-shadow: -19px 31px 26px -35px #6f6f6f;
    }


    .pdf-view span.float-label {
        font-size: 13px;
        position: absolute;
        top: -10px;
        left: 15px;
        background: #fff;
        padding: 0px 6px;
        font-weight: 600;
    }

    .img-view {
        width: 100%;
        height: auto;
        border: 2px dotted #ccc;
        border-radius: 12px;
        margin: 53px 0;
        position: relative;
        box-shadow: -19px 31px 26px -16px #6f6f6f;
        transition-duration: 0.2s;
    }

    .img-view:hover {
        box-shadow: -19px 31px 26px -35px #6f6f6f;
    }


    .img-view span.float-label {
        font-size: 13px;
        position: absolute;
        top: -10px;
        left: 15px;
        background: #fff;
        padding: 0px 6px;
        font-weight: 600;
    }

    .dotted-border-area.detailRecievedItem {
        position: relative;
        border-width: 1px;
        border-style: solid;
        border-color: #cfcfcf;
        margin: 2rem 0;
        padding: 1rem 1.5rem;
    }

    .dotted-border-area.detailRecievedItem .display-flex-space-between {
        flex-direction: column;
    }

    .detailRecievedItem label {
        position: absolute;
        top: -9px;
        background: #fff;
        padding: 0rem 0.5rem;
        font-weight: 600 !important;
    }
</style>

<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<?php
if (isset($_GET["value"]) && $_GET["value"] == "fg") {
    require_once("components/qa/received-items-fg.php");
} elseif (isset($_GET["value"]) && $_GET["value"] == "sfg") {
    require_once("components/qa/received-items-sfg.php");
} elseif (isset($_GET["value"]) && $_GET["value"] == "rm") {
    require_once("components/qa/received-items-rm.php");
} elseif (isset($_GET["value"]) && $_GET["value"] == "rejected") {
    require_once("components/qa/received-items-reject.php");
} else {
    require_once("components/qa/received-items-grn.php");
}
?>





<!-- End Pegination from------->
<?php ?>

<?php
require_once("../common/footer.php");
?>

<script>
    $(".imgAdd").click(function() {
        $(this)
            .closest(".row")
            .find(".imgAdd")
            .before(
                '<div class="col-lg-4 col-md-4 col-sm-4 col-12 imgUp"><input type="text" class="form-control my-2 all-link" placeholder="upload image link"><i class="fa fa-times del"></i></div>'
            );
    });
    $(document).on("click", "i.del", function() {
        $(this).parent().remove();
    });
    $(function() {
        $(document).on("change", ".uploadFile", function() {
            var uploadFile = $(this);
            var files = !!this.files ? this.files : [];
            if (!files.length || !window.FileReader) return;

            if (/^image/.test(files[0].type)) {
                // only image file
                var reader = new FileReader();
                reader.readAsDataURL(files[0]);

                reader.onloadend = function() {

                    uploadFile
                        .closest(".imgUp")
                        .find(".imagePreview")
                        .css("background-image", "url(" + this.result + ")");
                };
            }
        });
    });
</script>

<script src="<?= BASE_URL; ?>public/validations/pgiValidation.js"></script>