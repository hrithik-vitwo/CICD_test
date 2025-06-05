<?php
class ComplianceBranchSettings
{
    private $company_id = null;
    private $branch_id = null;
    function __construct()
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
    }

    function getGstr1FillingMonths()
    {
        $currentDate = new DateTime();
        $monthPeriods = [];
        for ($i = 0; $i < 12; $i++) {
            // Get the first day of the month
            $firstDay = $currentDate->format('Y-m-01');
            // Get the last day of the month
            $lastDay = $currentDate->format('Y-m-t');
            // Add to the array
            $monthPeriods[$currentDate->format('m-Y')] = $currentDate->format('Y, F');
            // Move back one month
            $currentDate->modify('-1 month');
        }
        return $monthPeriods;
    }
}
