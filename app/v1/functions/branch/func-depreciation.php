<?php
class AssetDepreciation
{
    private $method;
    private $assetValue;
    private $salvageValue;
    private $usefulLife;
    private $depreciationRate;
    private $putToUseDate;

    public function __construct($method, $assetValue, $salvageValue = 0, $usefulLife = 0, $depreciationRate = 0, $putToUseDate = null)
    {
        $this->method = strtolower($method); // slm or wdv
        $this->assetValue = $assetValue;
        $this->salvageValue = $salvageValue;
        $this->usefulLife = $usefulLife;
        $this->depreciationRate = $depreciationRate;
        $this->putToUseDate = $putToUseDate ? new DateTime($putToUseDate) : new DateTime();
    }
    function calculateMonths($putToUseDate, $month, $year)
    {
        // Convert the putToUse date into a DateTime object
        $putToUse = ($putToUseDate);
        $year2 = $putToUse->format('Y') + 1;
        $putToUsem = $putToUse->format('m');
        if ($putToUsem >= 1 && $putToUsem <= 3) {
            $year = $year;
            $year2 = $year;
        } else {
            $year = $year;
            $year2 = $year2;
        }

        // Define the start date as the first day of the provided month and year

        $startDate = new DateTime("$year-$month-01");

        // Define the end date as March 31st of the next year
        $endDate = new DateTime(($year2) . '-03-31');


        // If the putToUse date is after the end date, return 0
        if ($putToUse > $endDate) {
            return 0;
        }

        // Count the months between the start date and the end date, inclusive
        $months = 0;
        $currentDate = clone $startDate;

        // Loop through each month from the start date until the end date
        while ($currentDate <= $endDate) {
            $months++;

            // Move to the next month
            $currentDate->modify('first day of next month');
        }

        return $months - 1;
    }

    public function calculateYearlyDepreciation()
    {
        if ($this->method === 'slm') {
            return $this->calculateSLMYearly();
        } elseif ($this->method === 'wdv') {
            return $this->calculateWDVYearly();
        } else {
            throw new Exception("Invalid depreciation method. Use 'slm' or 'wdv'.");
        }
    }

    public function calculateMonthlyDepreciation()
    {
        $yearlyDepreciation = $this->calculateYearlyDepreciation();
        return $yearlyDepreciation / 12;
    }

    private function calculateSLMYearly()
    {
        if ($this->usefulLife <= 0) {
            throw new Exception("Useful life must be greater than 0 for SLM method.");
        }

        return ($this->assetValue - $this->salvageValue) / $this->usefulLife;
    }

    private function calculateWDVYearly()
    {
        if ($this->depreciationRate <= 0 || $this->depreciationRate > 100) {
            throw new Exception("Depreciation rate must be between 0 and 100 for WDV method.");
        }

        return $this->assetValue * ($this->depreciationRate / 100);
    }

    public function calculateDepreciationSchedule($durationInYears, $month = null, $year = null)
    {
        $schedule = [];
        $currentValue = $this->assetValue;
        $currentDate = clone $this->putToUseDate;

        for ($yearIndex = 1; $yearIndex <= $durationInYears; $yearIndex++) {
            // Adjust start and end dates for Indian financial year
            $startYear = $currentDate->format('Y');
            $putToUseYear = $currentDate->format('Y');
            $putToUseMonth = $currentDate->format('m');
            if ($putToUseMonth >= 1 && $putToUseMonth <= 3) {
                $startYear = $currentDate->format('Y') - 1;
            } else {
                $startYear = $currentDate->format('Y');
            }
            $financialYearStart = new DateTime("$startYear-04-01");
            $financialYearEnd = new DateTime(($startYear + 1) . "-03-31");

            if ($yearIndex === 1 && $currentDate > $financialYearStart) {
                $daysUsedInYear = (int)$financialYearEnd->diff($currentDate)->format('%a') + 1;
                $daysInYear = (int)$financialYearEnd->diff($financialYearStart)->format('%a') + 1;
            } else {
                $daysUsedInYear = (int)$financialYearEnd->diff($financialYearStart)->format('%a') + 1;
                $daysInYear = $daysUsedInYear;
            }

            $yearlyDepreciation = $this->method === 'slm'
                ? (($this->assetValue - $this->salvageValue) / $this->usefulLife) * ($daysUsedInYear / $daysInYear)
                : $currentValue * ($this->depreciationRate / 100) * ($daysUsedInYear / $daysInYear);
            $lst = $currentValue;
            $currentValue -= $yearlyDepreciation;
            if ($currentValue < $this->salvageValue) {
                $currentValue = $this->salvageValue;
            }

            $financialYear = $financialYearStart->format('Y') . '-' . $financialYearEnd->format('Y');

            $schedule[] = [
                'financialYear' => $financialYear,
                'depreciation' => round($yearlyDepreciation, 2),
                'bookValue' => round($currentValue, 2),
                'lst_wdv' => round($lst, 2),
            ];

            $currentDate = $financialYearEnd->modify('+1 day');
        }

        if ($month !== null && $year !== null) {
            $requestedMonthStart = new DateTime("$year-$month-01");
            $requestedMonthEnd = clone $requestedMonthStart;
            $requestedMonthEnd->modify('last day of this month');

            foreach ($schedule as $entry) {
                $entryStartYear = explode('-', $entry['financialYear'])[0];
               
                $entryStartDate = new DateTime("$entryStartYear-04-01");
                $entryEndDate = new DateTime(($entryStartYear + 1) . "-03-31");


                if ($requestedMonthStart >= $entryStartDate && $requestedMonthEnd <= $entryEndDate) {
                    $year1 = $this->putToUseDate->format('Y');
                    $month1 = $this->putToUseDate->format('m');
                    if ($month1 >= 1 && $month <= 3) {
                        $year2 = $year1;
                    } else {
                        $year2 = $year1 + 1;
                    }


                    if ($year == $year1 && $month1 == $month) {
                        $dep = $entry['depreciation'] - $this->calculateMonths($this->putToUseDate, $month, $year) * $this->calculateMonthlyDepreciation();
                        $tot = $this->assetValue - $dep;
                        $lst = $tot + $dep;
                    } else if ($year2 == $year && $this->calculateMonths($this->putToUseDate, $month, $year) > 0 && $month <= 3) {
                        $dep = $this->calculateMonthlyDepreciation();
                        $tot = $this->assetValue + ($this->calculateMonths($this->putToUseDate, $month, $year) * $this->calculateMonthlyDepreciation()) - $entry['depreciation'];
                        $lst = $tot + $dep;
                    } else if ($year == $year1 && $this->calculateMonths($this->putToUseDate, $month, $year) > 0) {
                        $dep = $this->calculateMonthlyDepreciation();
                        $tot = $this->assetValue + ($this->calculateMonths($this->putToUseDate, $month, $year) * $this->calculateMonthlyDepreciation()) - $entry['depreciation'];
                        $lst = $tot + $dep;
                    } else if ($year2 == $year && 3 == $month) {
                        $dep = $this->calculateMonthlyDepreciation();
                        $tot = $this->assetValue - $entry['depreciation'];
                        $lst = $tot + $dep;
                    } else if ($year != $year2) {
                        $dep = $entry['depreciation'] / 12;
                        $monthArray = [
                            1 => 10,   // January
                            2 => 11,   // February
                            3 => 12,   // March
                            4 => 1,   // April (key=4, value=1)
                            5 => 2,   // May
                            6 => 3,   // June
                            7 => 4,   // July
                            8 => 5,   // August
                            9 => 6,   // September
                            10 => 7,  // October
                            11 => 8,  // November
                            12 => 9   // December
                        ];
                        $tot = $entry['bookValue'] + $entry['depreciation'] - $dep * $monthArray[intval($month)];
                        $lst = $tot + $dep;
                    }
                    return [
                        'month' => $requestedMonthStart->format('F Y'),
                        'depreciation' => round($dep, 2),
                        'bookValue' => round($tot, 2),
                        'lst_wdv'   => round($lst, 2),
                        // 'bookValue' => round($entry['bookValue'], 2),
                    ];
                }
            }
        }

        return $schedule;
    }
}

// Example usage:
// try {
//     $depreciation = new AssetDepreciation(
//         method: 'wdv', // 'slm' or 'wdv'
//         assetValue: 525,
//         salvageValue: 26.25,
//         usefulLife: 8, // For SLM
//         depreciationRate: 31.23, // For WDV (optional if using SLM)
//         putToUseDate: '2024-10-30' // Optional: Date asset was put to use
//     );

//     // Yearly Depreciation
//     echo "Yearly Depreciation: " . $depreciation->calculateYearlyDepreciation() . "\n";

//     // Monthly Depreciation
//     echo "Monthly Depreciation: " . $depreciation->calculateMonthlyDepreciation() . "\n";

//     // Depreciation Schedule for 10 years
//     $schedule = $depreciation->calculateDepreciationSchedule(10);
//     echo "\nDepreciation Schedule:\n";
//     foreach ($schedule as $entry) {
//         echo "Financial Year: {$entry['financialYear']}, Depreciation: {$entry['depreciation']}, Book Value: {$entry['bookValue']}, Book Value: {$entry['lst_wdv']}\n";
//     }

//     // Depreciation for a specific month
//     $monthData = $depreciation->calculateDepreciationSchedule(8, 4, 2025); // May 2025
//     print_r($monthData);
//     echo "\nDepreciation for May 2025:\n";
//     echo "Month: {$monthData['month']}, Depreciation: {$monthData['depreciation']}, Book Value: {$monthData['bookValue']}\n";

// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }

?>