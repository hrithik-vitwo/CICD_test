<?php
class ReportHelper
{
    function generateDateRangeByCompareWith($fromDate, $toDate, $typeDate = "custom", $compareWith = "previousPeriod", $numberOfPeriod = 1)
    {
        $fromDate = new DateTime($fromDate);
        $toDate = new DateTime($toDate);
        return [
            [
                "fromDate" => $fromDate->format("Y-m-d"),
                "toDate" => $toDate->format("Y-m-d")
            ]
        ];
    }
}
