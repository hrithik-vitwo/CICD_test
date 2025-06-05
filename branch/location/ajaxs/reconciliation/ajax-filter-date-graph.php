<?php

require_once("../../../../app/v1/connection-branch-admin.php");

require_once("../../../../app/v1/functions/branch/bankReconciliationStatement.controller.php");

if(isset($_GET['startdate']) && $_GET['startdate'] != "" && isset($_GET['enddate']) && $_GET['enddate'] != ""){

     $start_date = $_GET['startdate'];
     $end_date = $_GET['enddate'];

     $brsObj = new BankReconciliationStatement($bankId, $tnxType);
     $table = "";

     function getMonthsAndDatesInRange($startDate, $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        $monthsAndDates = array();

        while ($start <= $end) {
            $month = $start->format('m');
            $date = $start->format('d');
            $year = $start->format('Y');
            $month_in_words = $start->format('M');

            $proper_start_date = new DateTime($year."-".$month."-".$date);

            $proper_start_date->modify('last day of this month');


            // Determine the target date for each month
            if ($month == $end->format('m') && $year == $end->format('Y')) {
                $last_date = $end->format('d');
            }
            else
            {
                $last_date = $proper_start_date->format('d');
            }

            $monthsAndDates[] = array("month_in_words" => $month_in_words, "first_date" => array('month' => $month,'date' => $date,'year' => $year),"last_date"=>array('month' => $proper_start_date->format('m'),'date' => $last_date,'year' => $proper_start_date->format('Y')));

            $start->modify('first day of next month');
        }

        return $monthsAndDates;
    }

    $startDate = $start_date;
    $endDate = $end_date;

    $monthsAndDatesInRange = getMonthsAndDatesInRange($startDate, $endDate);
    $graph_date_wise = [];

    foreach ($monthsAndDatesInRange as $item) {
        $month = $item["last_date"]['month'];
        $date = $item["last_date"]['date'];
        $year = $item["last_date"]['year'];
        $month_in_words = $item["month_in_words"].",".$year;

        $start_date = $item["first_date"]['year']."-".$item["first_date"]['month']."-".$item["first_date"]['date'];
        $end_date = $item["last_date"]['year']."-".$item["last_date"]['month']."-".$item["last_date"]['date'];

        $bank_amount = $brsObj->getBankAmountgraph($start_date,$end_date)["data"]["amount"] ?? 0;
        $book_amount = $brsObj->getBooksAmountgraph($start_date,$end_date);

        $graph_date_wise[] = array("month" => $month_in_words, "book" => $book_amount, "bank" => $bank_amount == "" ? 0 : $bank_amount);

        
    }
     



     echo json_encode($graph_date_wise);

}
?>
