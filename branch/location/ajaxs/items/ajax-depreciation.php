<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/branch/func-depreciation.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$companyID = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
$goodsObj = new GoodsController();

function calculateRemainingAssetLife($startDate, $assetLifeYears)
{
    // Convert the start date to a DateTime object
    $startDateObj = DateTime::createFromFormat('Y-m-d', $startDate);

    if (!$startDateObj) {
        return "Invalid start date format. Please use 'YYYY-MM-DD'.";
    }

    // Get the current date
    $currentDate = new DateTime();

    // Calculate the end-of-life date for the asset
    $endOfLifeDate = clone $startDateObj;
    $endOfLifeDate->add(new DateInterval("P{$assetLifeYears}Y"));

    // If the current date is after the end-of-life date, return 0 years and 0 months
    if ($currentDate > $endOfLifeDate) {
        return "0 years 0 months";
    }

    // Calculate the remaining life
    $remainingLife = $currentDate->diff($endOfLifeDate);
    $remainingLife->m += 1;
    // Return the remaining life in "X years Y months" format
    return "{$remainingLife->y} Years {$remainingLife->m} Months";
}

function checkDepreciationPosting($putToUseDate, $postingDate, $frequency, $previousPostingDate)
{
    $allow = 0; // Default to not allowed

    $putToUse = new DateTime($putToUseDate);
    $posting = new DateTime($postingDate);
    $prevPosting = $previousPostingDate ? new DateTime($previousPostingDate) : null;

    if ($posting < $putToUse) {
        return ["allow" => $allow, "message" => "Posting date cannot be before put-to-use date."];
    }

    if ($prevPosting && $posting <= $prevPosting) {
        return ["allow" => $allow, "message" => "Depreciation already posted for this period."];
    }

    // Financial Year Calculation (April 1st - March 31st next year)
    $financialYearStartYear = $putToUse->format('Y');
    if ((int)$putToUse->format('m') >= 1 && (int)$putToUse->format('m') <= 3) {
        $financialYearStartYear -= 1;
    }
    $financialYearEndYear = $financialYearStartYear + 1;
    $financialYearStart = new DateTime("$financialYearStartYear-04-01");
    $financialYearEnd = new DateTime("$financialYearEndYear-03-31");

    $financialYear = $financialYearStart->format('Y') . "-" . $financialYearEnd->format('Y');

    if ($frequency === 'monthly') {
        if (!$prevPosting) {
            // First depreciation must be in the same month as put-to-use date
            if ($posting->format('Y-m') === $putToUse->format('Y-m')) {
                $allow = 1;
            } else {
                return ["allow" => $allow, "message" => "First monthly depreciation must be in the same month as the put-to-use date."];
            }
        } else {
            // Ensure previous month's depreciation was posted
            // $lastMonth = (clone $posting)->modify("-1 month");
            $lastMonth = (clone $posting)->modify("last day of previous month");
            if ($prevPosting->format('Y-m') !== $lastMonth->format('Y-m')) {
                return ["allow" => $allow, "message" => "Previous month's depreciation must be posted before allowing this month's."];
            }
            $allow = 1;
        }
    }

    if ($frequency === 'yearly') {
        if (!$prevPosting) {
            // First yearly depreciation must be on or before March 31st of the first financial year
            if ($posting <= $financialYearEnd) {
                $allow = 1;
            } else {
                return ["allow" => $allow, "message" => "First yearly depreciation must be posted on or before March 31st of the first financial year."];
            }
        } else {
            // Ensure previous year's depreciation was done on March 31st
            if ($prevPosting->format('m-d') === "03-31") {
                $allowedStart = new DateTime($prevPosting->format('Y') . "-04-01");
                $allowedEnd = new DateTime(($prevPosting->format('Y') + 1) . "-03-31");
                if ($posting >= $allowedStart && $posting <= $allowedEnd) {
                    $allow = 1;
                } else {
                    return ["allow" => $allow, "message" => "Yearly depreciation must be posted between April 1st and March 31st of the next financial year."];
                }
            } else {
                return ["allow" => $allow, "message" => "Previous year's depreciation must be posted on March 31st before allowing next year."];
            }
        }
    }

    return ["allow" => $allow, "message" => "Depreciation posting allowed for financial year $financialYear."];
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {


    // $keys = $_GET['dep_keys'];


    $keys = array_unique(is_array($_GET['dep_keys']) ? $_GET['dep_keys'] : explode(',', $_GET['dep_keys'] ?? ''));
    $company_detaisl = queryGet("SELECT `depreciation_schedule` FROM `erp_companies` WHERE `company_id`='$company_id'");
    $dep_schedule = $company_detaisl['data']['depreciation_schedule'];


    if (!empty($dep_schedule) && $dep_schedule !== null) {
        // Do something if depreciation schedule is not empty and not null

?>
        <form id="depreciationForm" method="POST">
            <input type="hidden" value="add_dep" name="add_dep">

            <?php
            $sl = 0;
            $btn = 0;
            foreach ($keys as $key) {


                $depre = queryGet("SELECT posting_date FROM erp_asset_depreciation 
                   WHERE asset_use_id = '$key' 
                   AND branch_id = '$branch_id' 
                   AND location_id = '$location_id' 
                   AND company_id = '$company_id'
                   AND status!='reverse' 
                   ORDER BY created_at DESC 
                   LIMIT 1");

                $prev_posting_date = (!empty($depre['data'])) ? $depre['data']['posting_date'] : null;

              
                $asset_sql1 = queryGet("SELECT `use_date`,`asset_id` FROM `erp_asset_use` WHERE `use_asset_id` = $key AND `company_id` =$company_id AND `location_id` = $location_id AND `branch_id`=$branch_id");
                $puttouse = $asset_sql1['data']['use_date'];
                $asset_id= $asset_sql1['data']['asset_id'];
                $gldetails = queryGet("SELECT `parentGlId` FROM `erp_inventory_items` WHERE `itemId` = $asset_id AND `company_id` =$company_id AND `location_id` = $location_id AND `branch`=$branch_id");
                $glid=$gldetails['data']['parentGlId'];
                $previous_year = $depre['data']['depreciation_value'];
                
                $posting_date = $_GET['postingdate'];
                $date = new DateTime($posting_date);

                $month = $date->format('m'); // Extracts month (e.g., 03)
                $year = $date->format('Y');  // Extracts year (e.g., 2025)



                if($glid==9)
                {
                    echo '<p style="
                        text-align: center;
                        font-size: xx-large;
                        color: green;
                        font-weight: bold;
                    "> Land is not depreciated as it does not lose value over time </p>';
                    exit();
                }
                $result = checkDepreciationPosting($puttouse, $posting_date, $dep_schedule, $prev_posting_date);
             

                $allow=$result['allow'];
                $msg=$result['message'];
                // console($result);
                // exit();
                if($allow)
                {
                    $msg = '<p style="
                        text-align: center;
                        font-size: xx-large;
                        color: green;
                        font-weight: bold;
                    ">' . $msg . '</p>';
                }
                else{
                    $msg = '<p style="
                        text-align: center;
                        font-size: xx-large;
                        color: red;
                        font-weight: bold;
                    ">' . $msg . '</p>';
                }
                if ($allow) {
                    $btn = 1;
                    $rand = rand(10, 1000);
                    $asset_sql = queryGet("SELECT * FROM `erp_asset_use` WHERE `use_asset_id` = $key AND `company_id` =$company_id AND `location_id` = $location_id AND `branch_id`=$branch_id");
                    $asset_id = $asset_sql['data']['asset_id'];
                    $dep_key_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE `itemId` = $asset_id");
                    $dep_key = $dep_key_sql['data']['dep_key'];
                    $asset_code = $dep_key_sql['data']['itemCode'];
                    $asset_id = $dep_key_sql['data']['itemId'];
                    $asset_name = $dep_key_sql['data']['itemName'];
                    $put_to_use_date = $asset_sql['data']['use_date'];
                    $scrap_value = $asset_sql['data']['scrap_value'];
                    $company_sql = queryGet("SELECT `depreciation_type` FROM `erp_companies` WHERE `company_id`='$company_id'");
                    $rule_type = $company_sql['data']['depreciation_type'];

                    $dep_percentage_sql = queryGet("SELECT * FROM `erp_depreciation_table` WHERE `desp_key` = '$dep_key' AND `company_id` = '$company_id'");
                    $dep_percentage = $dep_percentage_sql['data']['wdv'];
                    $asset_life = $dep_percentage_sql['data']['asset_life'] ? $dep_percentage_sql['data']['asset_life'] : 10;
                    $asset_price_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`='$asset_id' AND `company_id`='$company_id'");
                    $uom = $asset_sql['data']['uom'];
                    $use_qty = $asset_sql['data']['qty'];
                    $per_qty_price = $asset_sql['data']['rate'];
                    $cost_id = $asset_sql['data']['cost_center_id'];
                    $cost_center_details = queryGet("SELECT `CostCenter_code`,`CostCenter_desc` FROM `erp_cost_center` WHERE `company_id` = '$company_id' AND `CostCenter_id`= '$cost_id' AND `branch_id`= '$branch_id'");
                    $CostCenter_code = $cost_center_details['data']['CostCenter_code'];
                    $CostCenter_desc = $cost_center_details['data']['CostCenter_desc'];
                    $cost_center = $CostCenter_code . '(' . $CostCenter_desc . ')';
                    $uom_details = queryGet("SELECT `uomName` FROM `erp_inventory_mstr_uom` WHERE `uomId` = '$uom'");
                    $uom_name = $uom_details['data']['uomName'];
                    $method = $rule_type; // 'SLM' or 'WDV'
                    $cost = $asset_sql['data']['total_value']; // Asset cost
                    $rate = $asset_sql['data']['dep_percentage']; // Depreciation rate in %
                    $putToUseDate = $put_to_use_date; // Date asset was put to use
                    $givenMonth = $month;
                    $givenYear = $year;
                    $compOpeningDate = $_GET['postingdate']; // Example: '2024-12-31'

                    

                    $dep_ca = new AssetDepreciation($method, $cost, $scrap_value, $asset_life, $rate, $put_to_use_date);

                    if ($dep_schedule == "monthly") {

                        
                     
                        $res = $dep_ca->calculateDepreciationSchedule($asset_life, $givenMonth, $givenYear);

                        $depreciation_on = $res['lst_wdv'];
                        $depreciated_val = $res['depreciation'];
                        $depreciation_percentage = $rate;
                        $after_dep_val = $res['bookValue'];

                    }
                    if ($dep_schedule == "yearly") {
                        $res = $dep_ca->calculateDepreciationSchedule($asset_life);
                       
                        foreach ($res as $r) {

                            if($month>= 1 && $month<=3)
                            {
                                $nextYear = $year -1;
                                $ye = $nextYear . "-" . $year;
                            }else{
                            $nextYear = $year + 1;
                            $ye = $year . "-" . $nextYear;
                            }
                            
                            if ($r['financialYear'] == $ye) {
                                $depreciation_on = $r['lst_wdv'];
                                $depreciated_val = $r['depreciation'];
                                $depreciation_percentage = $rate;
                                $after_dep_val = $r['bookValue'];
                                break;
                            }
                        }
                    }



                    if (is_array($keys) && count($keys) > 1) {
                        $sl++;
            ?>
                        <input type="hidden" name="asset[<?= $rand ?>][asset_use_id]" value="<?= $key ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][asset_id]" value="<?= $asset_id ?>">

                        <input type="hidden" name="asset[<?= $rand ?>][month]" value="<?= $month ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][year]" value="<?= $year ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][dep_schedule]" value="<?= $dep_schedule ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][posting_date]" value="<?= $posting_date ?>">

                        <input type="hidden" name="asset[<?= $rand ?>][method]" value="<?= $method ?>">

                        <input type="hidden" name="asset[<?= $rand ?>][asset_code]" value="<?= $asset_code ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][asset_name]" value="<?= $asset_name ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][cost_center]" value="<?= $cost_center ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][put_to_use_date]" value="<?= DateTime::createFromFormat('Y-m-d', $putToUseDate)->format('d-m-Y') ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][asset_life]" value="<?= $asset_life . " Years" ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][asset_remaining_life]" value="<?= calculateRemainingAssetLife($put_to_use_date, $asset_life); ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][uom_name]" value="<?= $uom_name ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][use_qty]" value="<?= number_format($use_qty, 2) ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][per_qty_price]" value="<?= $per_qty_price ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][asset_total]" value="<?= number_format($per_qty_price * $use_qty, 2, '.', '') ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][depreciation_on]" value="<?= $depreciation_on ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][depreciation_percentage]" value="<?= $depreciation_percentage ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][depreciated_val]" value="<?= $depreciated_val ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][after_dep_val]" value="<?= $after_dep_val ?>">
                        <input type="hidden" name="asset[<?= $rand ?>][scrap_value]" value="<?= $asset_sql['data']['scrap_value'] ?>">
                        <?php if ($sl == 1) { ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Sl-No</th>
                                        <th>Asset Code</th>
                                        <th>Asset Name</th>
                                        <th>Cost Center</th>
                                        <th>Put To Use</th>
                                        <th>Asset Life</th>
                                        <th>Remaining Life</th>
                                        <th>UOM</th>
                                        <th>Qty</th>
                                        <th>Historical Price</th>
                                        <th>Historical Total Price</th>
                                        <th>Last <?= $method ?>></th>
                                        <th>Rate Of Depreciation</th>
                                        <th>Depreciation Amount</th>
                                        <th><?= $method ?></th>
                                        <th>Residual Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php } ?>
                                <tr>
                                    <td><?= $sl ?></td>
                                    <td><?= $asset_code ?></td>
                                    <td><?= $asset_name ?></td>
                                    <td><?= $cost_center ?></td>
                                    <td><?= DateTime::createFromFormat('Y-m-d', $putToUseDate)->format('d-m-Y') ?></td>
                                    <td><?= $asset_life . " Years" ?></td>
                                    <td><?= calculateRemainingAssetLife($put_to_use_date, $asset_life); ?></td>
                                    <td><?= $uom_name ?></td>
                                    <td><?= number_format($use_qty, 2) ?></td>
                                    <td><?= $per_qty_price ?></td>
                                    <td><?= number_format($per_qty_price * $use_qty, 2, '.', '') ?></td>
                                    <td><?= $depreciation_on ?></td>
                                    <td><?= $depreciation_percentage ?></td>
                                    <td><?= $depreciated_val ?></td>
                                    <td><?= $after_dep_val ?></td>
                                    <td><?= $asset_sql['data']['scrap_value'] ?></td>
                                </tr>
                                <?php if ($sl == 1) { ?>
                                <?php }
                            } else {
                                ?>


                                <div class="row p-3">

                                    <input type="hidden" name="asset[<?= $rand ?>][asset_use_id]" value="<?= $key ?>">
                                    <input type="hidden" name="asset[<?= $rand ?>][asset_id]" value="<?= $asset_id ?>">
                                    <input type="hidden" name="asset[<?= $rand ?>][month]" value="<?= $month ?>">
                                    <input type="hidden" name="asset[<?= $rand ?>][year]" value="<?= $year ?>">
                                    <input type="hidden" name="asset[<?= $rand ?>][dep_schedule]" value="<?= $dep_schedule ?>">
                                    <input type="hidden" name="asset[<?= $rand ?>][posting_date]" value="<?= $posting_date ?>">

                                    <input type="hidden" name="asset[<?= $rand ?>][method]" value="<?= $method ?>">


                                    <div class="col-lg-6 col-md-6 col-sm-12">

                                        <div class="form-input">

                                            <label for=""> Asset Code </label>

                                            <input type="text" name="asset[<?= $rand ?>][asset_code]" id="asset_code" class="form-control asset_code" value="<?= $asset_code ?>" readonly>

                                        </div>

                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">

                                        <div class="form-input">

                                            <label for=""> Asset Name </label>

                                            <input type="text" name="asset[<?= $rand ?>][asset_name]" id="asset_name" class="form-control asset_name" value="<?= $asset_name ?>" readonly>

                                        </div>

                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">

                                        <div class="form-input">

                                            <label for=""> Cost Center </label>

                                            <input type="text" name="asset[<?= $rand ?>][cost_center]" id="cost_center" class="form-control cost_center" value="<?= $cost_center ?>" readonly>

                                        </div>

                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">

                                        <div class="form-input">

                                            <label for=""> Put To Use </label>

                                            <input type="text" name="asset[<?= $rand ?>][put_to_use_date]" id="put_to_use_date" class="form-control put_to_use_date" value="<?= DateTime::createFromFormat('Y-m-d', $putToUseDate)->format('d-m-Y') ?>" readonly>

                                        </div>

                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6">

                                        <div class="form-input">

                                            <label for=""> Asset Life </label>

                                            <input type="text" name="asset[<?= $rand ?>][asset_life]" id="asset_life" class="form-control asset_life" value="<?= $asset_life . " Years" ?>" readonly>

                                        </div>

                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6">

                                        <div class="form-input">

                                            <label for=""> Remaining Life </label>

                                            <input type="text" name="asset[<?= $rand ?>][asset_name]" id="asset_name" class="form-control asset_name" value="<?= calculateRemainingAssetLife($put_to_use_date, $asset_life); ?>" readonly>

                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6">

                                        <div class="form-input">

                                            <label for=""> UOM </label>

                                            <input type="text" name="asset[<?= $rand ?>][uom_name]" id="uom_name" class="form-control uom_name" value="<?= $uom_name ?>" readonly>

                                        </div>

                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6">

                                        <div class="form-input">

                                            <label for=""> Qty </label>

                                            <input type="text" name="asset[<?= $rand ?>][use_qty]" id="use_qty" class="form-control use_qty" value="<?= number_format($use_qty, 2) ?>" readonly>

                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-6">

                                        <div class="form-input">

                                            <label for="">Historical Price </label>

                                            <input type="text" name="asset[<?= $rand ?>][per_qty_price]" id="per_qty_price" class="form-control per_qty_price" value="<?= $per_qty_price ?>" readonly>

                                        </div>

                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-6">

                                        <div class="form-input">

                                            <label for="">Historical Total Price </label>

                                            <input type="text" name="asset[<?= $rand ?>][asset_total]" id="" class="form-control asset_total" value="<?= number_format($per_qty_price * $use_qty, 2, '.', '') ?>" readonly>

                                        </div>

                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6">

                                        <div class="form-input">

                                            <label for="">Last <?= $method ?></label>

                                            <input type="text" name="asset[<?= $rand ?>][depreciation_on]" id="" class="form-control depreciation_on" value="<?= $depreciation_on ?>" readonly>

                                        </div>

                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-6">

                                        <div class="form-input">

                                            <label for="">Rate Of Depreciation</label>

                                            <input type="text" name="asset[<?= $rand ?>][depreciation_percentage]" id="" class="form-control depreciation_percentage" value="<?= $depreciation_percentage ?>" readonly>

                                        </div>

                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-6">

                                        <div class="form-input">

                                            <label for="">Depreciation Amount</label>

                                            <input type="text" name="asset[<?= $rand ?>][depreciated_val]" id="depreciated_val" class="form-control depreciated_val" value="<?= $depreciated_val ?>" readonly>

                                        </div>

                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-12">

                                        <div class="form-input">

                                            <label for=""><?= $method ?></label>

                                            <input type="text" name="asset[<?= $rand ?>][after_dep_val]" id="after_dep_val" class="form-control after_dep_val" value="<?= $after_dep_val ?>" readonly>

                                        </div>

                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-12">

                                        <div class="form-input">

                                            <label for="">Residual Value</label>

                                            <input type="text" name="asset[<?= $rand ?>][scrap_value]" id="scrap_value" class="form-control scrap_value" value="<?= $asset_sql['data']['scrap_value'] ?>" readonly>

                                        </div>

                                    </div>

                                </div>

                    <?php
                            }
                        }
                    }
                    if ($btn == 0) {
                        echo $msg;
                    }
                    ?>
                    <?php if (is_array($keys) && count($keys) > 1) { ?>
                                </tbody>
                            </table>
                        <?php } ?>
                        <?php if ($btn) { ?>
                            <button class="btn btn-primary save-close-btn btn-xs float-right add_dep">Run</button>

        </form>
    <?php } ?>


<?php
    } else {
        echo '<p style="
                text-align: center;
                font-size:xx-large;
                color: red;
                font-weight: bold;
            ">Depreciation schedule not set !</p>';
    }
}


?>