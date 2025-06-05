<?php
require_once("../../../app/v1/connection-branch-admin.php");
if(isset($_POST["migrateData"]) && $_POST["tableData"]!=""){
    try{

        $tableData = json_decode(base64_decode($_POST["tableData"]), true);
        console($tableData);


    }catch(Exception $e){
        console(["status" => "error", "message" => "Error"]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once("common/header.php") ?>

<body class="sidebar-mini layout-fixed sidebar-collapse">

    <?php include_once("common/navbar.php") ?>
    <?php include_once("common/sidebar.php") ?>

    <?php if (isset($_FILES["uploadFile"])) : ?>
        <section class="content">
            <div class="row p-0 m-0">
                <?php
                $excelObj  = readTheXlsxAndCsvFile($_FILES["uploadFile"]);
                // console($excelObj);
                $sheetArrData = $excelObj["data"];
                ?>
                <?php if ($excelObj["status"] != "success") : ?>
                    <script>
                        alert(`<?= ucfirst($excelObj["status"]) ?>! <?= ucfirst($excelObj["message"]) ?>`);
                        window.location.href = `<?= BASE_URL ?>branch/location/datamigration/`;
                    </script>
                <?php endif; ?>
            </div>
            <div class="top-fixed-content border-bottom">
                <div class="left-text">
                    <p class="text-sm font-bold">Total Count 100</p>
                    <p class="text-sm font-bold">Total Value 100</p>
                </div>
                <hr>
                <div class="right-text">
                    <p class="text-sm font-bold text-success">GL Code : 100011</p>
                    <p class="text-sm font-bold text-danger">GL Code not mapping</p>
                </div>
            </div>
            <div class="col-12">
                <div class="row p-0 m-0">
                    <table class="excel-table-view">
                        <thead>
                            <tr>
                                <?php foreach ($sheetArrData[0] as $oneCellKey => $oneCell) : ?>
                                    <th><?= $oneCell ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sheetArrData as $rowKey => $oneRow) : ?>
                                <?php if ($rowKey < 1) {
                                    continue;
                                } ?>
                                <tr>
                                    <?php foreach ($oneRow as $oneCellKey => $oneCell) : ?>
                                        <td contenteditable="true" style="max-width:50px;" id="<?= $rowKey . "_" . $oneCellKey ?>" class="tableCellClass"><?= $oneCell ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="row p-0 m-0">
                    <div class="button-preview mt-1 float-right">
                        <form action="" method="post">
                            <input type="hidden" name="tableData" id="tableData" value="">
                            <button class="btn btn-primary" type="submit" name="previewData"><i class="fa fa-eye"></i> Preview</button>
                        </form>
                    </div>
                </div>
            </div>

        </section>
    <?php elseif (isset($_POST["previewData"])) : ?>
        <section class="content">
            <div class="row p-0 m-0">
                <?php
                $sheetArrData = json_decode(base64_decode($_POST["tableData"]), true);
                ?>
            </div>
            <div class="top-fixed-content border-bottom">
                <div class="left-text">
                    <p class="text-sm font-bold">Total Count 100</p>
                    <p class="text-sm font-bold">Total Value 100</p>
                </div>
                <hr>
                <div class="right-text">
                    <p class="text-sm font-bold text-success">GL Code : 100011</p>
                    <p class="text-sm font-bold text-danger">GL Code not mapping</p>
                </div>
            </div>
            <div class="col-12">
                <div class="row p-0 m-0">
                    <table class="excel-table-view">
                        <thead>
                            <tr>
                                <?php foreach ($sheetArrData[0] as $oneCellKey => $oneCell) : ?>
                                    <th><?= $oneCell ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sheetArrData as $rowKey => $oneRow) : ?>
                                <?php if ($rowKey < 1) {
                                    continue;
                                } ?>
                                <tr>
                                    <?php foreach ($oneRow as $oneCellKey => $oneCell) : ?>
                                        <td style="max-width:50px;" id="<?= $rowKey . "_" . $oneCellKey ?>" class="tableCellClass"><?= $oneCell ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="row p-0 m-0">
                    <div class="button-preview mt-1 float-right">
                        <form action="" method="post" onsubmit="return confirm('Are you sure to Migrate?')">
                            <input type="hidden" name="tableData" id="tableData" value="">
                            <button class="btn btn-primary" type="submit" name="migrateData"><i class="fa fa-right"></i> Migrate Data</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    <?php else : ?>
        <div class="top-fixed-content border-bottom">
            <div class="left-text">
                <p class="text-sm font-bold">Total Count 100</p>
                <p class="text-sm font-bold">Total Value 100</p>
            </div>
            <hr>
            <div class="right-text">
                <p class="text-sm font-bold text-success">GL Code : 100011</p>
                <p class="text-sm font-bold text-danger">GL Code not mapping</p>
            </div>
        </div>
        <div class="row p-0 m-0">
            <div class="col-6 my-auto">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="file" id="uploadFileInput" name="uploadFile" class="form-control">
                    <button type="submit" name="submitFile" class="form-control btn btn-primary">Upload File</button>
                </form>
            </div>
        </div>
    <?php endif; ?>




    <!-- <div class="card data-card">
            <div class="card-body">
                <div class="border-area">
                    <form action="" method="post" enctype="multipart/form-data" id="uploadFileForm">
                        <input type="file" class="dat-migration-input" id="uploadFileInput" name="uploadFile">
                        <input type="submit" value="Submit" name="submit">
                    </form>
                    <div class="icon">
                        <i class="fa fa-cloud-upload fa-2x"></i>
                    </div>
                    <div class="suggestion">
                        <p class="text-xs font-bold">Upload Data Sheet <span class="text-italic">( Allowed format : CSV, XLS)</span></p>
                    </div>
                </div>
                <div class="template-download">
                    <div id="uploadBtnDivArea" class="text-center">
                        <button class="btn btn-sm btn-primary" id="submitUploadFileForm">Upload Now</button>
                    </div>
                    <a href="#">Download Template</a>
                </div>
                <hr>
                <div class="note">
                    <p class="border-bottom pb-2 text-sm">Note : </p>
                    <ul>
                        <li class="text-xs">Gl and Sub GL mapping</li>
                        <li class="text-xs">Customer Code (Old/New)</li>
                        <li class="text-xs">Mandate Field</li>
                    </ul>
                </div>
            </div>
        </div> -->


    <!-- <div class="card editable-table-card">
            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td contenteditable="true" style="max-width:50px;">6 Livo technologies pvt ltd</td>
                            <td contenteditable="true" style="max-width:50px;">6 Livo technologies pvt ltd</td>
                            <td contenteditable="true" style="max-width:50px;">6 Livo technologies pvt ltd</td>
                            <td contenteditable="true" style="max-width:50px;">6 Livo technologies pvt ltd</td>
                            <td contenteditable="true" style="max-width:50px;">6 Livo technologies pvt ltd</td>
                            <td contenteditable="true" style="max-width:50px;">6 Livo technologies pvt ltd</td>
                            <td contenteditable="true" style="max-width:50px;">6 Livo technologies pvt ltd</td>
                            <td contenteditable="true" style="max-width:50px;">6 Livo technologies pvt ltd</td>
                            <td contenteditable="true" style="max-width:50px;">6 Livo technologies pvt ltd</td>
                            <td contenteditable="true" style="max-width:50px;">6 Livo technologies pvt ltd</td>
                            <td contenteditable="true" style="max-width:50px;">6 Livo technologies pvt ltd</td>
                            <td contenteditable="true" style="max-width:50px;">6 Livo technologies pvt ltd</td>
                            <td contenteditable="true" style="max-width:50px;">6 Livo technologies pvt ltd</td>
                            <td contenteditable="true" style="max-width:50px;">6 Livo technologies pvt ltd</td>
                            <td contenteditable="true" style="max-width:50px;">6 Livo technologies pvt ltd</td>
                        </tr>
                    </tbody>
                </table>

                <div class="button-preview">
                    <button class="btn btn-primary float-right">
                        <i class="fa fa-eye"></i> Preview</button>
                </div>
            </div>
        </div> -->

    <!-- <div class="card view-table-card">
            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                            <th>test</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                            <td>
                                <p>test</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p contenteditable="true">test</p>
                            </td>
                            <td>
                                <p contenteditable="true">test</p>
                            </td>
                            <td>
                                <p contenteditable="true">test</p>
                            </td>
                            <td>
                                <p contenteditable="true">test</p>
                            </td>
                            <td>
                                <p contenteditable="true">test</p>
                            </td>
                            <td>
                                <p contenteditable="true">test</p>
                            </td>
                            <td>
                                <p contenteditable="true">test</p>
                            </td>
                            <td>
                                <p contenteditable="true">test</p>
                            </td>
                            <td>
                                <p contenteditable="true">test</p>
                            </td>
                            <td>
                                <p contenteditable="true">test</p>
                            </td>
                            <td>
                                <p contenteditable="true">test</p>
                            </td>
                            <td>
                                <p contenteditable="true">test</p>
                            </td>
                            <td>
                                <p contenteditable="true">test</p>
                            </td>
                            <td>
                                <p contenteditable="true">test</p>
                            </td>
                            <td>
                                <p contenteditable="true">test</p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="button-preview">
                    <button class="btn btn-primary float-right">Submit</button>
                    <button class="btn btn-danger">Cancel</button>
                </div>
            </div>
        </div> -->

    </section>

    <?php include_once("common/footer.php") ?>

    <script>
        $(document).ready(function() {
            var localStorageTableData = <?= json_encode($sheetArrData) ?>;
            $("#tableData").val(btoa(JSON.stringify(localStorageTableData)));
            $(document).on('keyup', ".tableCellClass", function() {
                let cellIds = ($(this).attr('id')).split("_");
                let changedData = $(this).text();
                localStorageTableData[cellIds[0]][cellIds[1]] = changedData.trim().trimStart().trimEnd();
                $("#tableData").val(btoa(JSON.stringify(localStorageTableData)));
            });









            $(document).on('change', '#uploadFileInput', function() {
                $(`#uploadBtnDivArea`).html(`<button class="btn btn-sm btn-primary" id="submitUploadFileForm">Upload Now</button>`);
                console.log("File uploading...");
            });
            $(document).on('click', '#submitUploadFileForm', function() {
                $(`#uploadFileForm`).submit();
                console.log("File uploading...");
            });



        });
    </script>

</body>

</html>