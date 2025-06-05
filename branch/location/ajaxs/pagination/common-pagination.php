<?php
function pagiNation($pageNo, $totalPage)
{
    $output="";
    $page_no = $pageNo;
    $total_page = $totalPage;

    $output .= '<div class="pagination-btn" id="pagination">';
    $output .= '<ol>';

    if ($page_no > 1) {
        $output .= "<li class='pagination-blocks'><a id='" . ($page_no - 1) . "' href='?page=" . ($page_no - 1) . "'>Previous</a><li>";
    }

    for ($i = 1; $i <= $total_page; $i++) {
        $activeClass = ($i == $page_no) ? "active" : "";
        if ($i <= 5 || $i >= $total_page - 1 || ($i >= $page_no - 2 && $i <= $page_no + 2)) {
            $output .= "<li class='pagination-blocks {$activeClass}'><a id='{$i}' href='?page={$i}'>{$i}</a></li>";
        }
    }

    if ($page_no < $total_page) {
        $output .= "<li class='pagination-blocks'><a id='" . ($page_no + 1) . "' href='?page=" . ($page_no + 1) . "'>Next</a></li>";
        $output .= "<li class='pagination-blocks'><a id='" . $total_page . "' href='?page=" . ($page_no + 1) . "'>Last</a></li>";
    }

    $output .= '</ol>';
    $output .= '</div>';

    return $output;
    
}

function pagiNationinnerTable($pageNo, $totalPage)
{
    $output="";
    $page_no = $pageNo;
    $total_page = $totalPage;

    $output .= '<div class="pagination-btn" id="paginationinner">';
    $output .= '<ol>';

    if ($page_no > 1) {
        $output .= "<li class='pagination-blocks'><a id='" . ($page_no - 1) . "' href='?page=" . ($page_no - 1) . "'>Previous</a><li>";
    }

    for ($i = 1; $i <= $total_page; $i++) {
        $activeClass = ($i == $page_no) ? "active" : "";
        if ($i <= 5 || $i >= $total_page - 1 || ($i >= $page_no - 2 && $i <= $page_no + 2)) {
            $output .= "<li class='pagination-blocks {$activeClass}'><a id='{$i}' href='?page={$i}'>{$i}</a></li>";
        }
    }

    if ($page_no < $total_page) {
        $output .= "<li class='pagination-blocks'><a id='" . ($page_no + 1) . "' href='?page=" . ($page_no + 1) . "'>Next</a></li>";
        $output .= "<li class='pagination-blocks'><a id='" . $total_page . "' href='?page=" . ($page_no + 1) . "'>Last</a></li>";
    }

    $output .= '</ol>';
    $output .= '</div>';

    return $output;
    
}

function pagiNationinnerTable2($pageNo, $totalPage)
{
    $output="";
    $page_no = $pageNo;
    $total_page = $totalPage;

    $output .= '<div class="pagination-btn" id="paginationinner2">';
    $output .= '<ol>';

    if ($page_no > 1) {
        $output .= "<li class='pagination-blocks'><a id='" . ($page_no - 1) . "' href='?page=" . ($page_no - 1) . "'>Previous</a><li>";
    }

    for ($i = 1; $i <= $total_page; $i++) {
        $activeClass = ($i == $page_no) ? "active" : "";
        if ($i <= 5 || $i >= $total_page - 1 || ($i >= $page_no - 2 && $i <= $page_no + 2)) {
            $output .= "<li class='pagination-blocks {$activeClass}'><a id='{$i}' href='?page={$i}'>{$i}</a></li>";
        }
    }

    if ($page_no < $total_page) {
        $output .= "<li class='pagination-blocks'><a id='" . ($page_no + 1) . "' href='?page=" . ($page_no + 1) . "'>Next</a></li>";
        $output .= "<li class='pagination-blocks'><a id='" . $total_page . "' href='?page=" . ($page_no + 1) . "'>Last</a></li>";
    }

    $output .= '</ol>';
    $output .= '</div>';

    return $output;
    
}

function pagiNationinnerTable3($pageNo, $totalPage)
{
    $output="";
    $page_no = $pageNo;
    $total_page = $totalPage;

    $output .= '<div class="pagination-btn" id="paginationinner3">';
    $output .= '<ol>';

    if ($page_no > 1) {
        $output .= "<li class='pagination-blocks'><a id='" . ($page_no - 1) . "' href='?page=" . ($page_no - 1) . "'>Previous</a><li>";
    }

    for ($i = 1; $i <= $total_page; $i++) {
        $activeClass = ($i == $page_no) ? "active" : "";
        if ($i <= 5 || $i >= $total_page - 1 || ($i >= $page_no - 2 && $i <= $page_no + 2)) {
            $output .= "<li class='pagination-blocks {$activeClass}'><a id='{$i}' href='?page={$i}'>{$i}</a></li>";
        }
    }

    if ($page_no < $total_page) {
        $output .= "<li class='pagination-blocks'><a id='" . ($page_no + 1) . "' href='?page=" . ($page_no + 1) . "'>Next</a></li>";
        $output .= "<li class='pagination-blocks'><a id='" . $total_page . "' href='?page=" . ($page_no + 1) . "'>Last</a></li>";
    }

    $output .= '</ol>';
    $output .= '</div>';

    return $output;
    
}

function pagiNationinnerTable4($pageNo, $totalPage)
{
    $output="";
    $page_no = $pageNo;
    $total_page = $totalPage;

    $output .= '<div class="pagination-btn" id="paginationinner4">';
    $output .= '<ol>';

    if ($page_no > 1) {
        $output .= "<li class='pagination-blocks'><a id='" . ($page_no - 1) . "' href='?page=" . ($page_no - 1) . "'>Previous</a><li>";
    }

    for ($i = 1; $i <= $total_page; $i++) {
        $activeClass = ($i == $page_no) ? "active" : "";
        if ($i <= 5 || $i >= $total_page - 1 || ($i >= $page_no - 2 && $i <= $page_no + 2)) {
            $output .= "<li class='pagination-blocks {$activeClass}'><a id='{$i}' href='?page={$i}'>{$i}</a></li>";
        }
    }

    if ($page_no < $total_page) {
        $output .= "<li class='pagination-blocks'><a id='" . ($page_no + 1) . "' href='?page=" . ($page_no + 1) . "'>Next</a></li>";
        $output .= "<li class='pagination-blocks'><a id='" . $total_page . "' href='?page=" . ($page_no + 1) . "'>Last</a></li>";
    }

    $output .= '</ol>';
    $output .= '</div>';

    return $output;
    
}





