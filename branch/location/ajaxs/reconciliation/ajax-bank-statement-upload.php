<?php
require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');

function processStatementOcr()
{
    if (isset($_POST["uploadFile"])) {
        // echo "company_id ".$company_id;
        // echo json_encode($_POST+$_FILES);
        $uploadObj = uploadFile($_FILES["file"], COMP_STORAGE_DIR . "/acc-statement/", ["jpg", "png", "jpeg", "pdf"]);
        $uploadedFile = $uploadObj["data"];
        $uploadedUrl = COMP_STORAGE_URL . "/acc-statement/" . $uploadObj["data"];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ocr.vitwo.ai/api/v1/ocr/bank_statement/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120, // Set a timeout of 5 minutes
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'file_url' => $uploadedUrl,
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response, true);

        if ($responseData["success"] == "true") {
            return [
                "status" => "success",
                "message" => "Success",
                "data" => $responseData["payload"],
                "uploadedFile" => $uploadedFile
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Something went wrong in ocr processing!",
                "data" => []
            ];
        }
    } else {
        return [
            "status" => "warning",
            "message" => "Something went wrong, please try again!",
            "data" => []
        ];
    }
}


$processStatementObj = processStatementOcr();

// $processStatementObj = [
//     "status" => "warning",
//     "message" => "Something went wrong",
//     "data" => [],
// ];

if ($processStatementObj["status"] != "success") {
    http_response_code(400);
    echo json_encode($processStatementObj, true);
} else {
    http_response_code(200);
    $ocr_column_names = [
        "tnx_date" => ["Date", "date", "dated"],
        "utr_number" => ["Tnx No", "tran id", "utr_no", "utr", "utr no", "chq/ref. no"],
        "particular" => ["Referance", "referance", "remarks", "remark", "transaction description"],
        "withdrawal_amt" => ["Withdrawals/Debit", "debit", "dr", "debit amount", "withdrawals", "withdrawal", "withdraw"],
        "deposit_amt" => ["Deposits/Credit", "credit", "cr", "credit amount", "deposits", "deposit", ],
        "balance_amt" => ["Balance", "balance", "amt", "amount", "closing balance"]
    ];

   
    $serializedData = json_encode($processStatementObj["data"]);
    ?>
    <form action="" method="post" id="statementForm_<?= $_POST["bank_id"] ?>">
        <input type="hidden" name="statement_data" value='<?=htmlspecialchars($serializedData)?>'>
        <input type="hidden" name="statement_file" value="<?= $processStatementObj["uploadedFile"] ?>">
        <input type="hidden" name="statement_bank_id" value="<?= $_POST["bank_id"] ?>">
        <table class="table">
            <?php
            foreach ($processStatementObj["data"] as $rowKey => $oneRow) {
                if ($rowKey == 0) {
                    echo "<thead>";
                    echo "<tr>";
                    foreach (array_keys($oneRow) as $columnName) {
                        echo "<th>$columnName</th>";
                    }
                    echo "</tr>";
                    echo "</thead>";
                }
                echo "<tbody>";
                if ($rowKey == 0) {
                    echo "<tr>";
                    foreach (array_keys($oneRow) as $sl => $columnName) {
                        ?>
                        <td>
                            <select class="form-control" name="column_names[<?= $sl ?>]">
                                <option value="">Select Correct Column</option>
                                <?php
                                foreach ($ocr_column_names as $ocr_column_name => $ocr_column_values) {
                                ?>
                                    <option value="<?= $ocr_column_name ?>" <?= in_array(strtolower($columnName), $ocr_column_values) ? "selected" : "" ?>><?= ucfirst($ocr_column_values[0]) ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </td>
                        <?php
                    }
                    echo "</tr>";
                }
                echo "<tr>";
                foreach ($oneRow as $columnName => $columnValue) {
                    echo "<td>$columnValue</td>";
                }
                echo "</tr>";
                echo "</tbody>";
            }
            ?>
        </table>
        <button type="submit" name="submitOcrStatementBtn" class="form-control btn btn-primary">Save Statement</button>
    </form>
<?php
}
