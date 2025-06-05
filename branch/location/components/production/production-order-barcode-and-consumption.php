<style>
    .bar-code-title.d-flex {
        font-family: cursive;
        justify-content: space-between;
        font-size: 12px;
        font-weight: 600;
    }

    .bar-code-title.d-flex p {
        font-family: cursive;
        color: #fff;
    }

    .card.bar-code-multiple-card {
        border-radius: 5px;
        padding: 5px;
        max-width: 200px;
        width: 100%;
        box-shadow: 6px 7px 12px -3px #00000052;
    }

    .card.bar-code-multiple-card .card-footer {
        background-color: #003060;
    }

    svg.bar-code-img {
        max-width: 300px;
        width: 100%;
        height: auto;
        display: block;
    }

    .row.bar-code-cards {
        gap: 20px;
    }

    .bar-code-btns {
        gap: 7px;
    }
</style>
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <div class="p-0 pt-1 my-2">
                        <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                            <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                <h3 class="card-title">Manage Production Order</h3>
                                <span>
                                    <span id="multipleMrpRunSpan"></span>
                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>" class="btn btn-sm btn-primary"><i class="fa fa-plus" style="margin-right: 0;"></i></a>
                                </span>
                            </li>
                        </ul>
                    </div>
                    <?php
                    if (isset($_POST["confirmDeclareProd"])) {
                      $data = ($_POST['consumptionPostingData']);
                    //   console($data);
                    //   exit();
                      
                        $consumptionPostingData = json_decode(base64_decode($_POST["consumptionPostingData"]), true);
                        $consumptionControllerObj = new ConsumptionController();
                        
                        $confirmObj = $consumptionControllerObj->confirmConsumption($consumptionPostingData);
                        // console($confirmObj);
                        // exit();
                        if($_POST['confirmDeclareProd'] == 'Print & Confirm Declare'){
                        swalAlert($confirmObj["status"], ucfirst($confirmObj["status"]), $confirmObj["message"], $_SERVER["PHP_SELF"]);
                        }
                        else{
                            $href = BASE_URL.'branch/location/components/production/production-barcode-print.php?data='.$data;
                            swalAlert($confirmObj["status"], ucfirst($confirmObj["status"]), $confirmObj["message"], $href); 
                        }

                    }else if (isset($_POST["consumptionPosting"])) {
                        $consumptionControllerObj = new ConsumptionController();
                        $previewObj = $consumptionControllerObj->previewConsumption($_POST);
                        if($previewObj["status"]!="success"){
                            swalAlert2($previewObj["status"], ucfirst($previewObj["status"]), $previewObj["message"], $_SERVER["PHP_SELF"]);
                        }
                        // console($previewObj);
                        if ($previewObj["status"] == "success") {
                        ?>
                            <form action="?consumption-preview" method="post">
                                <input type="hidden" name="consumptionPostingData" value="<?= base64_encode(json_encode($_POST)) ?>">
                                <div class="card" style="border-radius: 20px;">
                                    <div class="row bar-code-cards p-0 m-0">
                                        <?php
                                        foreach ($previewObj["data"] as $oneItem) {
                                        ?>
                                            <div class="card bar-code-multiple-card m-2">
                                                <div class="card-body p-0">
                                                    <svg class="bar-code-img" id="barcode<?= $oneItem["declearQtySl"] ?>"></svg>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="bar-code-title d-flex">
                                                        <p>Mfg</p>
                                                        <p><?= date("d-m-Y") ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                $(document).ready(function() {
                                                    JsBarcode("#barcode<?= $oneItem["declearQtySl"] ?>", "<?= $oneItem["barcode"] ?>", {
                                                        fontSize: 14,
                                                        fontOptions: "bold",
                                                        margin: 5,
                                                        height: 75,
                                                        width: 1
                                                    });
                                                });
                                            </script>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="bar-code-btns d-flex mt-2 float-right">
                                        <a href="<?= $_SERVER["PHP_SELF"] ?>" class="btn btn-danger mb-2">Cancel</a>
                                        <button type="submit" name="confirmDeclareProd" value="Print & Confirm Declare" class="btn btn-primary mb-2">Confirm Declaration</button>
                                        <button type="submit" name="confirmDeclareProd" value="Print & Confirm Declare with print" class="btn btn-primary mb-2">Confirm Declaration With Print</button>

                                    </div>
                                </div>
                            </form>
                    <?php
                        }
                    } else {
                        redirect($_SERVER["PHP_SELF"]);
                    }
                    ?>
                </div>
    </section>
</div>
<script>
    $(document).ready(function() {

    });
</script>

<?php
    require_once("../common/footer2.php");
?>