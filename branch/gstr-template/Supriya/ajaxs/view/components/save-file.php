<?php

require_once("../../../../../app/v1/connection-branch-admin.php");


if ($_SERVER['REQUEST_METHOD'] == "POST") {
?>

<h4 class="text-sm font-bold m-4">Save File</h4>
    <div class="section-box" id="sectionBox"> 
        <input type="hidden" name="" id="testInput" value="23">
        <div class="box mb-4">
            <p class="text-sm mb-2"><b>Total Of GST</b> - 2,00,000</p>
            <p class="text-sm mb-2"><b>CGST</b> - 2,00,000</p>
            <p class="text-sm mb-2"><b>SGST</b> - 2,00,000</p>
            <p class="text-sm mb-2"><b>IGST</b> - 2,00,000</p>
            <p class="text-sm mb-2"><b>Total Items</b> - 25,000</p>
            <img src="<?= BASE_URL?>public/assets/img/VitNew 1.png" alt="">
        </div>
        <div class="text">
            <p class="text-sm font-bold">All the data will be saved when you clicking the save button. </p>
            <p class="text-sm font-bold">Would you like to save data in GST Server?</p>
            <div class="btns-group my-3">
                <button class="btn btn-primary border mr-2" data-toggle="modal" data-target="#successPopup">Yes</button>
                <button class="btn btn-primary reset-btn">No</button>
            </div>
            <div class="modal success-popup fade" id="successPopup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <img src="../../public/assets/img/correct 2.jpg" alt="">
                            <p class="text-center">Your EVC is successful created. It has generated the below acknowledgment as a confirmation of your return submission</p>
                            <button class="btn btn-primary" data-dismiss="modal">Ok</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php


} else {
    console("METHOD_ERROR");
}

?>