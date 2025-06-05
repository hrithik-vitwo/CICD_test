<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-goods-controller.php");

$headerData = array('Content-Type: application/json');
$responseData = [];
$companyID = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
$goodsObj = new GoodsController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //console($_POST);
    $keyword = $_POST['keyword'];
    $selected = $_POST['selected'];
//   exit();
    $getAllGoodGroupObj = $goodsObj->getAllHsnPegination($_POST);
   // console($getAllGoodGroupObj);
    if ($getAllGoodGroupObj["status"] == "success") {
        if (!empty($getAllGoodGroupObj["data"])) {
            foreach ($getAllGoodGroupObj["data"] as $hsn) {
?>
                <tr>
                    <td><input type="radio" id="hsn" name="hsn" value="<?= $hsn['hsnCode']  ?>" <?php if($hsn['hsnCode'] == $selected) { echo  'checked="checked"' ; } ?>></td>
                    <td>
                        <p id="hsnCode_<?= $hsn['hsnCode'] ?>"><?= $hsn['hsnCode'] ?></p>
                    </td>
                    <td>
                        <p id="hsnDescription_<?= $hsn['hsnCode'] ?>"><?= $hsn['hsnDescription'] ?></p>
                    </td>
                    <td>
                        <p id="taxPercentage_<?= $hsn['hsnCode'] ?>"><?= $hsn['taxPercentage'] ?>%</p>
                    </td>
                </tr>
            <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="4">No Result Found</td>
            </tr>
            <tr>
                <td colspan="4">
                    <div class="add-btn-hsn">
                        <a id="searchValue" class="btn btn-primary searchValue" data-toggle="modal" data-target="#hsnAdd">Add HSN</a>
                        <!-- <a class="btn btn-primary" data-toggle="modal" data-target="#hsnAdd">Add HSN</a> -->
                        <!-- modal start -->

                        
                        <!-- <div class="modal fade right add-new-hsn" id="hsnAdd" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content card">
                                    <div class="modal-header card-header p-3">
                                        <h4 class="modal-title" id="exampleModalLabel">Add Goods Group</h4>
                                    </div>
                                    <div class="modal-body card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-input mb-2">
                                                    <label>HSN Code</label>
                                                    <input type="text" name="hsnCode" value="<?= $keyword ?>" id="hsnName" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-input mb-2">
                                                    <label>HSN Description</label>
                                                    <input type="text" name="hsnDesc" id="hsnDesc" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-input mb-2">
                                                    <label>HSN Rate</label>
                                                    <input type="text" class="form-control" id="hsnRate" name="hsnRate">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-input mb-2 d-flex">
                                                    <label>Public</label>
                                                    <input type="radio" name="hsnPublic" id="hsnPublic" value="0">
                                                    <label>Private</label>
                                                    <input type="radio" name="hsnPublic" id="hsnPublic" value="1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="input-group btn-col">
                                            <a id="addNewHSNFormSubmitBtn" class="btn btn-primary btnstyle">Submit</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>

                    <div class="modal add-new-hsn" id="hsnAdd" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content card">
                                    <div class="modal-header card-header p-3">
                                        <h5 class="modal-title text-sm text-white">Add Goods Group</h5>
                                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                                    </div>
                                    <div class="modal-body card-body">
                                        <div class="row hsn-details">
                                            <div class="col-md-6 col">
                                                <div class="form-input mb-2">
                                                    <label>HSN Code</label>
                                                    <input type="text" name="hsnCode" value="<?= $keyword ?>" id="hsnName" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col">
                                                <div class="form-input mb-2">
                                                    <label>HSN Rate</label>
                                                    <input type="text" class="form-control" id="hsnRate" name="hsnRate">
                                                </div>
                                            </div>
                                            <div class="col-md-12 col">
                                                <div class="form-input mb-2">
                                                    <label>HSN Description</label>
                                                    <textarea class="form-control" name="hsnDesc" id="hsnDesc" cols="30" rows="10"></textarea>
                                                    <!-- <input type="text" name="hsnDesc" id="hsnDesc" class="form-control"> -->
                                                </div>
                                            </div>

                                            <div class="col-md-12 col ">
                                                <div class="form-input selct-hsn-type mb-2">
                                                    <label>Public</label>
                                                    <input type="radio" name="hsnPublic" id="hsnPublic" value="0">
                                                    <label>Private</label>
                                                    <input type="radio" name="hsnPublic" id="hsnPublic" value="1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" id="addNewHSNFormSubmitBtn" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                </td>
            </tr>





        <?php
        }
    } else {
        ?>
        <tr>
            <td colspan="4">No Result Found</td>
        </tr>
    <?php
    }
} else {
    ?>
    <tr>
        <td colspan="4">No Result Found</td>
    </tr>
<?php
}
?>