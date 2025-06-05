<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../../app/v1/functions/branch/func-compliance-controller.php");
require_once("./controller/gstr1-json-repositary-controller.php");

//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
$queryParamsAction = json_decode(base64_decode(($_GET['action'])));
$authGstinPortalObj = new AuthGstinPortal();
$authObj = $authGstinPortalObj->checkAuth();
$queryParamsData = $_SESSION['gstr3bSummary'];
$queryParams = $queryParamsData['data']['sec_sum'];
$period = $queryParamsAction->period;
$startDate = date("Y-m-d", strtotime($queryParamsAction->startDate));
$endDate = date("Y-m-d", strtotime($queryParamsAction->endDate));
$gstr1JsonRepoObj = new Gstr1JsonRepository($period, $startDate, $endDate);
$gstr1jsonObj = $gstr1JsonRepoObj->generate();
// console($gstr1jsonObj['b2cs']);

?>

<link rel="stylesheet" href="../../public/assets/stock-report-new.css">


<style>
    .is-gststate-supply .card {
        margin: 40px auto;
        max-width: 95%;
        border: 1px solid #ccc;
        background: #fff;
    }

    .is-gststate-supply .card .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #fff;
        padding: 15px;
    }

    .is-gststate-supply .card .card-header h2 {
        font-size: 0.9rem;
        color: #fff;
        margin-bottom: 0;
    }

    .is-gststate-supply .card .card-header ion-icon {
        font-size: 2rem;
    }

    .is-gststate-supply .card .card-body {
        border-radius: 12px;
        padding: 15px 0 0;
        background: #fff;
    }

    .is-gststate-supply .card .card-body .accordion {
        background-color: #fff;
    }

    .is-gststate-supply .card .card-body .accordion .accordion-button {
        font-size: 0.85rem;
        font-weight: 600;
        color: #003060;
    }

    .is-gststate-supply table {
        border-bottom: 1px solid #ccc;
        border-right: 1px solid #ccc;
    }

    .is-gststate-supply table tr th {
        background: #b9b9b98f;
        color: #000;
        font-size: 0.8rem;
        font-weight: 600;
        border-bottom: 1px solid #ccc;
        text-align: center;
    }

    .is-gststate-supply table tr td {
        border-bottom: 1px solid #ccc;
        border: 1px solid #ccc;
        padding: 20px 15px;
        text-align: center;
    }


    .is-gststate-supply table tr:last-child th,
    .is-gststate-supply table tr:last-child td {
        border-bottom: 0;
    }

    .is-gststate-supply table tr th:last-child,
    .is-gststate-supply table tr td:last-child {
        border-right: 0;
    }

    .is-gststate-supply table tr td,
    .is-gststate-supply table tr:nth-child(2n+1) td {
        background: #fff;
    }
</style>

<div class="content-wrapper is-gststate-supply">
    <div class="card">
        <div class="card-header">
            <h2>
                3.2 Of the supplies shown in 3.1(a) and 3.1.1(i), details of inter-state supplies made to unregistered persons, composition taxable persons, and UIN holders
            </h2>
            <div class="help-block">
                <ion-icon name="help-circle-outline"></ion-icon>
            </div>
        </div>
        <div class="card-body">
            <div class="accordion" id="accordionExample">
                <!-- Accordion Item 1: Unregistered Persons -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Supplies made to Unregistered Persons
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <table id="unregTable">
                                <thead>
                                    <?php foreach ($gstr1jsonObj["b2cs"] as $invoiceItems) {  ?>
                                        <tr>
                                            <th><input type="checkbox"></th>
                                            <th width="45%">Place of Supply (State/UT)</th>
                                            <th>Total Taxable Value (₹)</th>
                                            <th>Amount of Integrated Tax (₹)</th>
                                        </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="checkbox"></td>
                                        <td>
                                            <select class="form-control">
                                                <option value="" disabled selected>Select State</option>
                                                <?php
                                                $stateNameList = fetchStateName()['data'] ?? [];
                                                foreach ($stateNameList as $state) {
                                                    $stateCode = ltrim($state['gstStateCode'], '0'); // Remove leading zeros from state code
                                                    $isSelected = ($invoiceItems['pos'] ?? '') == $stateCode ? 'selected' : ''; // Check if the option should be selected
                                                    echo "<option value='$stateCode' $isSelected>{$state['gstStateCode']} - {$state['gstStateName']}</option>";
                                                }
                                                ?>
                                            </select>

                                        </td>
                                        <td><input type="text" class="form-control text-right" value="<?= $invoiceItems["txval"] ?>"></td>
                                        <td><input type="text" class="form-control text-right" value="<?= $invoiceItems["iamt"] ?>"></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                            <div class="state-supply-action-btns d-flex justify-content-end gap-2 p-3">
                                <button class="btn btn-primary" id="addNewRowUnreg">Add</button>
                                <button class="btn btn-danger" id="removeRowUnreg">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Accordion Item 2: Composition Taxable Persons -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Supplies made to Composition Taxable Persons
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <table id="compTable">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox"></th>
                                        <th width="45%">Place of Supply (State/UT)</th>
                                        <th>Total Taxable Value (₹)</th>
                                        <th>Amount of Integrated Tax (₹)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="checkbox"></td>
                                        <td>
                                            <select class="form-control">
                                                <option value="">Select State</option>
                                                <?php
                                                foreach ($stateNameList as $one) {
                                                    echo "<option value='" . ltrim($one['gstStateCode'], '0') . "'>" . $one['gstStateCode'] . " - " . $one['gstStateName'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control text-right"></td>
                                        <td><input type="text" class="form-control text-right"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="state-supply-action-btns d-flex justify-content-end gap-2 p-3">
                                <button class="btn btn-primary" id="addNewRowComp">Add</button>
                                <button class="btn btn-danger" id="removeRowComp">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Accordion Item 3: UIN Holders -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Supplies made to UIN Holders
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <table id="uinTable">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox"></th>
                                        <th width="45%">Place of Supply (State/UT)</th>
                                        <th>Total Taxable Value (₹)</th>
                                        <th>Amount of Integrated Tax (₹)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="checkbox"></td>
                                        <td>
                                            <select class="form-control">
                                                <option value="">Select State</option>
                                                <?php
                                                foreach ($stateNameList as $one) {
                                                    echo "<option value='" . ltrim($one['gstStateCode'], '0') . "'>" . $one['gstStateCode'] . " - " . $one['gstStateName'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control text-right"></td>
                                        <td><input type="text" class="form-control text-right"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="state-supply-action-btns d-flex justify-content-end gap-2 p-3">
                                <button class="btn btn-primary" id="addNewRowUIN">Add</button>
                                <button class="btn btn-danger" id="removeRowUIN">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 my-3 mr-2">
            <button type="button" class="btn btn-primary gap-2" id="confirmBtn">Confirm</button>
            <button type="button" class="btn btn-danger gap-2" id="cancelBtn">Cancel</button>
        </div>
    </div>
</div>


<?php
require_once("../common/footer.php");
?>
<script>
    $(document).on("click", "#cancelBtn", function() {
        let action = '<?= json_encode($queryParamsAction) ?>';
        window.location.href = `gst-3b-summary.php?action=${btoa(action)}`;
    })
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Attach event listeners to 'Add' and 'Remove' buttons for each section
        document.querySelectorAll(".state-supply-action-btns").forEach(actionBtns => {
            const addButton = actionBtns.querySelector(".btn-primary");
            const removeButton = actionBtns.querySelector(".btn-danger");

            // Add new row specific to this section
            addButton.addEventListener("click", function() {
                const tableBody = actionBtns.closest(".accordion-body").querySelector("tbody");
                const newRow = document.createElement("tr");
                newRow.innerHTML = `
                <td><input type="checkbox" /></td>
                <td>
                    <select class="form-control">
                        <option value="">Select State</option>
                        ${generateStateOptions()}
                    </select>
                </td>
                <td><input type="text" class="form-control text-right" value="" /></td>
                <td><input type="text" class="form-control text-right" value="" /></td>
            `;
                tableBody.appendChild(newRow);
            });

            // Remove selected rows specific to this section
            removeButton.addEventListener("click", function() {
                const tableBody = actionBtns.closest(".accordion-body").querySelector("tbody");
                const selectedRows = tableBody.querySelectorAll("input[type='checkbox']:checked");

                if (selectedRows.length === 0) {
                    console.log("No rows selected to remove.");
                    return;
                }

                selectedRows.forEach(row => row.closest("tr").remove());
                console.log("Selected rows removed successfully.");
            });
        });
    });

    // Function to generate state options based on server-side data
    function generateStateOptions() {
        const stateNameList = <?php echo json_encode(fetchStateName()['data']); ?>;
        return stateNameList.map(one => {
            const stateCode = one['gstStateCode'].replace(/^0+/, '');
            return `<option value="${stateCode}">${stateCode} - ${one['gstStateName']}</option>`;
        }).join("");
    }

    //Function to capture data from a table

    document.getElementById("confirmBtn").addEventListener("click", function(event) {
        event.preventDefault(); // Prevent any default action like form submission

        // Initialize the structure
        let queryParams = '<?= json_encode($queryParamsAction) ?>';

        let interSup = {
            "unreg_details": [],
            "comp_details": [],
            "uin_details": []
        };

        // Function to capture data from a table
        function getTableData(tableId) {
            const rows = document.querySelectorAll(`#${tableId} tbody tr`);
            const data = [];
            rows.forEach(row => {
                const pos = row.querySelector("td select").value;
                const txval = row.querySelector("td:nth-child(3) input").value;
                const iamt = row.querySelector("td:nth-child(4) input").value;

                // If all values are present, add them to the data array
                if (pos && txval && iamt) {
                    data.push({
                        "pos": pos,
                        "txval": parseFloat(txval),
                        "iamt": parseFloat(iamt)
                    });
                }
            });
            return data.length > 0 ? data : [];
        }

        // Get data from each table and assign it to the correct arrays inside interSup object
        interSup.unreg_details = getTableData("unregTable");
        interSup.comp_details = getTableData("compTable");
        interSup.uin_details = getTableData("uinTable");

        // Ensure the structure is always present, even if no data is found
        const encodedData = {
            "inter_sup": {
                "unreg_details": interSup.unreg_details.length ? interSup.unreg_details : [],
                "comp_details": interSup.comp_details.length ? interSup.comp_details : [],
                "uin_details": interSup.uin_details.length ? interSup.uin_details : []
            }
        };

        // Logging the encoded data before sending
        console.log('Sending the following data:', encodedData);

        // Send the data as a plain object (stringified before sending)
        $.ajax({
            type: 'POST',
            url: `ajaxs/ajax-save-gstr3b-filedata.php`,
            data: {
                act: 'inter_sup',
                encodedData: JSON.stringify(encodedData), // Stringify the object
                queryParams: queryParams, // Pass other data
            },
            dataType: "json", // Expecting JSON response
            beforeSend: function() {
                console.log("Loading...");
            },
            success: function(response) {
                console.log("Response received:", response);

                // Handle the response here (optional)
                if (response.success) {
                    // Do something with the successful response
                    console.log('Data saved successfully.');
                    window.location.href = `gst-3B-summary.php?action=${btoa('<?= json_encode($queryParamsAction) ?>')}`;

                } else {
                    // Handle error or failure in response
                    console.error('Error saving data:', response.message);
                    window.location.href = `gst-3B-summary.php?action=${btoa('<?= json_encode($queryParamsAction) ?>')}`;

                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error: ", status, error); // Error handling
            },
            complete: function() {
                console.log("Response completed");
                // Optionally remove loading indicator here
            }
        });
    });
</script>