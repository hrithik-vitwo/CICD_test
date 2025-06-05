<?php
class OpeningClosingBalance
{
	private $company_id;
	private $branch_id;
	private $location_id;
	private $created_by;
	private $updated_by;
	private $compOpeningDate;

	function __construct()
	{
		global $company_id;
		global $branch_id;
		global $location_id;
		global $created_by;
		global $updated_by;
		global $compOpeningDate;

		$this->company_id = $company_id;
		$this->branch_id = $branch_id;
		$this->location_id = $location_id;
		$this->created_by = $created_by;
		$this->updated_by = $updated_by;
		$this->compOpeningDate = $compOpeningDate;
	}

	function getMonthsForPrevPosting($postingDate = null)
	{
		$postingDate = $postingDate != null ? $postingDate : date("Y-m-d");
		$startDate = new DateTime(date("Y-m-01", strtotime($postingDate)));
		$endDate = new DateTime(date("Y-m-01"));
		$months = [];
		$current = clone $startDate;
		while ($current <= $endDate) {
			$months[] = $current->format('Y-m');
			$current->modify('+1 month');
		}
		return $months;
	}

	function saveOpeningBalance($data = [])
	{
		// INPUT
		// $data = [
		// 	[
		// 		"postinDate" => "23-05-01",
		// 		"gl" => 2,
		// 		"subgl" => "",
		// 		"quantity" => 0,
		// 		"rate" => 0,
		// 		"amount" => 100.00,
		// 		"storageLocation" => 0
		// 	]
		// ];

		$dbObj = new Database(true);
		$dbObj->setActionName("Opening Balance");
		$dbObj->setSuccessMsg("Opening Balance successfully saved");
		$dbObj->setErrorMsg("Something went wrong, please try again!");

		foreach ($data as $key => $row) {
			$row["postinDate"] = "";
			$postinDate = $row["postinDate"] != "" ? $row["postinDate"] : ($this->compOpeningDate != "" ? $this->compOpeningDate : date("Y-m-d"));
			$gl = $row["gl"] > 0 ? $row["gl"] : 0;
			$subgl = $row["subgl"] != "" ? $row["subgl"] : "";
			$rate = $row["rate"] > 0 ? $row["rate"] : 0;
			$amount = $row["amount"] != "" ? $row["amount"] : 0;
			$quantity = $row["quantity"] > 0 ? $row["quantity"] : 0;
			$storageLocation = $row["storageLocation"] > 0 ? $row["storageLocation"] : 0;

			$allMonthList = $this->getMonthsForPrevPosting($postinDate);

			foreach ($allMonthList as $monthKey => $oneMonthYear) {
				$oneDateMonthYear = $oneMonthYear . "-01";

				$prevCheckObj = $dbObj->queryGet('SELECT * FROM `erp_opening_closing_balance` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND DATE_FORMAT(`date`, "%Y-%m") = "' . $oneMonthYear . '" AND `gl`=' . $gl . ' AND `subgl`="' . $subgl . '"');
				$prevCheckDataId = $prevCheckObj["data"]["id"];
				if ($gl > 0) {
					if ($subgl == "") {
						if ($prevCheckObj["status"] == "success") {
							//Update the gl amount
							if ($monthKey == count($allMonthList) - 1) {
								$gl_opening_closing_condition = '`opening_val`=`opening_val`+' . $amount;
							} else {
								$gl_opening_closing_condition = '`opening_val`=`opening_val`+' . $amount . ', `closing_val`=`closing_val`+' . $amount;
							}
							$dbObj->queryUpdate('UPDATE `erp_opening_closing_balance` SET ' . $gl_opening_closing_condition . ', `updated_by`="' . $this->updated_by . '" WHERE `id`=' . $prevCheckDataId);
						} else {
							//Insert the gl amount
							if ($monthKey == count($allMonthList) - 1) {
								$gl_opening_closing_condition = '`opening_val`=' . $amount;
							} else {
								$gl_opening_closing_condition = '`opening_val`=' . $amount . ', `closing_val`=' . $amount;
							}
							$dbObj->queryInsert('INSERT INTO `erp_opening_closing_balance` SET ' . $gl_opening_closing_condition . ', `company_id`=' . $this->company_id . ', `branch_id`=' . $this->branch_id . ', `location_id`=' . $this->location_id . ', `date` = "' . $oneDateMonthYear . '", `gl`=' . $gl . ', `subgl`="' . $subgl . '", `created_by`="' . $this->created_by . '", `updated_by`="' . $this->updated_by . '"');
						}
					} else {
						if ($prevCheckObj["status"] == "success") {
							//Update the sub gl value and subtruct the gl value;
							if ($monthKey == count($allMonthList) - 1) {
								$gl_opening_closing_condition = '`opening_val`= (`opening_val`-' . $amount . ')';
								$subgl_opening_closing_condition = '`opening_val`=`opening_val`+' . $amount;
							} else {
								$gl_opening_closing_condition = '`opening_val`=(`opening_val`-' . $amount . '), `closing_val`=(`closing_val`-' . $amount . ')';
								$subgl_opening_closing_condition = '`opening_val`=`opening_val`+' . $amount . ', `closing_val`=`closing_val`+' . $amount;
							}
							$dbObj->queryUpdate('UPDATE `erp_opening_closing_balance` SET ' . $subgl_opening_closing_condition . ', `updated_by`="' . $this->updated_by . '" WHERE `id`=' . $prevCheckDataId);
						} else {
							//Insert the sub gl value and subtruct the gl value;
							if ($monthKey == count($allMonthList) - 1) {

								$gl_opening_closing_condition = '`opening_val`= (`opening_val`-' . $amount . ')';
								$subgl_opening_closing_condition = '`opening_val`=' . $amount;
							} else {
								$gl_opening_closing_condition = '`opening_val`=(`opening_val`-' . $amount . '), `closing_val`=(`closing_val`-' . $amount . ')';
								$subgl_opening_closing_condition = '`opening_val`=' . $amount . ', `closing_val`=' . $amount;
							}
							$dbObj->queryInsert('INSERT INTO `erp_opening_closing_balance` SET ' . $subgl_opening_closing_condition . ', `company_id`=' . $this->company_id . ', `branch_id`=' . $this->branch_id . ', `location_id`=' . $this->location_id . ', `date` = "' . $oneDateMonthYear . '", `gl`=' . $gl . ', `subgl`="' . $subgl . '", `created_by`="' . $this->created_by . '", `updated_by`="' . $this->updated_by . '"');
						} 

						$prevGlCheckObj = $dbObj->queryGet('SELECT * FROM `erp_opening_closing_balance` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND DATE_FORMAT(`date`, "%Y-%m") = "' . $oneMonthYear . '" AND `gl`=' . $gl . ' AND `subgl`=""');
						$prevGlCheckDataId = $prevGlCheckObj["data"]["id"];
						if ($prevGlCheckObj["status"] == "success") {
							//update the subtructed gl amount
							$dbObj->queryUpdate('UPDATE `erp_opening_closing_balance` SET ' . $gl_opening_closing_condition . ',  `updated_by`="' . $this->updated_by . '" WHERE `id`=' . $prevGlCheckDataId);
						} else {
							//asume the gl amount 0 then insert the subtructed amount
							if ($monthKey == count($allMonthList) - 1) {
								$gl_opening_closing_condition = '`opening_val`= (0-' . $amount . ')';
							} else {
								$gl_opening_closing_condition = '`opening_val`=(0-' . $amount . '), `closing_val`=(0-' . $amount . ')';
							}
							$dbObj->queryInsert('INSERT INTO `erp_opening_closing_balance` SET ' . $gl_opening_closing_condition . ', `company_id`=' . $this->company_id . ', `branch_id`=' . $this->branch_id . ', `location_id`=' . $this->location_id . ', `date` = "' . $oneDateMonthYear . '", `gl`=' . $gl . ', `subgl`="", `created_by`="' . $this->created_by . '", `updated_by`="' . $this->updated_by . '"');
						}
					}
				}
			}
		}
		return $dbObj->queryFinish();
	}
}
