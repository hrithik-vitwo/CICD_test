<style>
    .content-area {
        display: flex;
        align-items: center;
        flex-direction: column;
        gap: 7px;
    }
</style>

<div class="container">
    <h4 class="text-sm font-bold my-4">Save File</h4>
    <div class="col-12 pl-auto">
        <div class="content-area" id="content-area">
            <div class="box mb-4">
                <?php

                $gstr1JsonRepoObj = new Gstr1JsonRepository($queryParams->period, date("Y-m-d", strtotime($queryParams->startDate)), date("Y-m-d", strtotime($queryParams->endDate)));
                $jsonObj = $gstr1JsonRepoObj->generate();
                // console($jsonObj["hsn"]["data"]);
                $totalCgst = 0;
                $totalSgst = 0;
                $totalIgst = 0;
                $totalCess = 0;
                $totalTaxable = 0;
                foreach ($jsonObj["hsn"]["data"] as $hsnData) {
                    $totalTaxable += $hsnData["txval"];
                    $totalCess += $hsnData["csamt"];
                    $totalIgst += $hsnData["iamt"];
                    $totalCgst += $hsnData["camt"];
                    $totalSgst += $hsnData["samt"];
                }
                ?>
                <p class="text-sm mb-2"><b>Taxable</b> : <?= number_format($totalTaxable, 2) ?></p>
                <p class="text-sm mb-2"><b>CGST</b> : <?= number_format($totalCgst, 2) ?></p>
                <p class="text-sm mb-2"><b>SGST</b> : <?= number_format($totalSgst, 2) ?></p>
                <p class="text-sm mb-2"><b>IGST</b> : <?= number_format($totalIgst, 2) ?></p>
                <p class="text-sm mb-2"><b>CESS</b> : <?= number_format($totalCess, 2) ?></p>
                <p class="text-sm mb-2"><b>Total Tax</b> : <?= number_format(($totalCess + $totalCgst + $totalIgst + $totalSgst), 2) ?></p>
                <img src="<?= BASE_URL ?>public/assets/img/VitNew 1.png" alt="">
            </div>
            <div class="text">
                <p class="text-sm font-bold">All the data will be saved when you clicking the save button. </p>
                <p class="text-sm font-bold">Would you like to save data in GST Server?</p>
                <div class="btns-group my-3">
                    <button class="btn btn-primary border mr-2" id="gstr1SaveJsonDataButton">Yes</button>
                    <!-- <button class="btn btn-primary reset-btn">No</button> -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        $("#fileGstr1StageConnect").addClass("progress-success");
        // $("#fileGstr1StageSaveFile").addClass("progress-success");
        // $("#fileGstr1StageProceedFile").addClass("progress-success");
        // $("#fileGstr1StageGenerateEvc").addClass("progress-success");
        // $("#fileGstr1StageFile").addClass("progress-success");

        $(document).on("click", "#gstr1SaveJsonDataButton", function() {
            $.ajax({
                type: "POST",
                url: "<?= BASE_URL ?>branch/compliance/ajaxs/api/ajax-gstr1-save-data.php?action=<?= base64_encode(json_encode($queryParams)) ?>",
                beforeSend: function() {
                    console.log("Saving Data");
                },
                success: function(response) {
                    console.log(response);
                    Swal.fire({
                        icon: `${response.status}`,
                        title: `${response.status[0].toUpperCase()+response.status.substr(1)}!`,
                        text: `${response.message.split(" or ")[0]}!`,
                    });
                    if(response.status=="success"){
                        getGstr1CurrentStage();
                    }
                },
                complete: function() {
                    console.log("Data saved successfully")
                }
            })
        });
    });
</script>