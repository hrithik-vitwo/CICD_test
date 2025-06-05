<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");

function chartExistorNot()
{
    global $company_id;
    $responce = [];
    $user_id = $_SESSION["logedBranchAdminInfo"]["adminId"];
    $lavel = strtolower($_SESSION["logedBranchAdminInfo"]["adminType"]);
    $dasSql = "SELECT * FROM `" . ERP_USER_DASHBOARD . "` WHERE  `lavel` = '" . $lavel . "' AND company_id = $company_id AND `user_id`=" . $user_id . " AND status='active'";
    $dasSqlresult = queryGet($dasSql);
    if ($dasSqlresult['numRows'] != 0) {
        $sql = "SELECT
                    dashComponent.*,
                    userDash.`company_id`,
                    userDash.`user_id`,
                    dashCompMaster.`page_name`,
                    dashCompMaster.`component_title`,
                    dashCompMaster.`divArea`,
                    dashCompMaster.`component_desc`
                FROM
                    `" . ERP_DASH_COMPONENT . "` AS dashComponent,
                    `" . ERP_USER_DASHBOARD . "` AS userDash,
                    `" . ERP_DASH_COMPONENT_MASTER . "` AS dashCompMaster
                WHERE
                    dashComponent.dashboard_id = userDash.dashboard_id 
                    AND dashComponent.component_id = dashCompMaster.component_id 
                    AND dashCompMaster.`lavel` = '" . $lavel . "' 
                    AND userDash.`lavel` = '" . $lavel . "' 
                    AND userDash.`company_id` = " . $company_id . " 
                    AND userDash.`user_id` = " . $user_id . "";

        $res = queryGet($sql);
        if ($res['numRows'] == 0) {
            $responce = false;
        } else {
            $responce = true;
        }
    } else {
        $responce = false;
    }
    return $responce;
}

function getDashboardAllComponent()
{
    global $company_id;
    $lavel = strtolower($_SESSION["logedBranchAdminInfo"]["adminType"]);
    $responce = [];
    $user_id = $_SESSION["logedBranchAdminInfo"]["adminId"];
    $sql = "SELECT
        dashComponent.*,
        userDash.`company_id`,
        userDash.`user_id`,
        dashCompMaster.`page_name`,
        dashCompMaster.`component_title`,
        dashCompMaster.`divArea`,
        dashCompMaster.`component_desc`
    FROM
        `" . ERP_DASH_COMPONENT . "` AS dashComponent,
        `" . ERP_USER_DASHBOARD . "` AS userDash,
        `" . ERP_DASH_COMPONENT_MASTER . "` AS dashCompMaster
    WHERE
        dashComponent.dashboard_id = userDash.dashboard_id 
        AND dashComponent.component_id = dashCompMaster.component_id  
        AND dashCompMaster.`lavel` = '" . $lavel . "' 
        AND userDash.`lavel` = '" . $lavel . "' 
        AND userDash.`company_id` = " . $company_id . " 
        AND userDash.`user_id` = " . $user_id . "";

    $res = queryGet($sql, true);
    return $res;
}


$chartSearchData = getDashTableSettings(); //Add Dash Table Settings

?>


<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/index.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/listing.css">


<style>
    :root {
        --primary-color: #02044A;
        --secondery-color: #25CC88;
        --shadow-color: #9d9fb3;
        --color0: hsl(229, 57%, 11%);
        --color0Trans: hsl(229, 57%, 11%, 0.85);
        --color1: hsl(228, 56%, 26%);
        --color2: hsl(243, 100%, 93%);
        --color3: hsl(229, 7%, 55%);
        --color4: hsl(230, 55%, 18%);
        --color5: hsl(0, 0%, 100%);

        /* gradient */
        --gradient: linear-gradient(90deg,
                hsl(6, 100%, 80%) 0%,
                hsl(335, 100%, 65%) 100%);

        /* font size */
        --fontSize: 14px;

        /* font weight */
        --regularFont: 400;
        --boldFont: 700;
    }




    /*******progress****/

    .section2__progressBarContainer {
        margin-top: 15px;
        position: relative;
        width: 100%;
    }

    .section2__progressBar {
        background-color: hsl(0deg 0% 100%);
        width: 100%;
        height: 15px;
        padding: 2px;
        border-radius: 50px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        margin-bottom: 1em;
        border: 1px solid #d8d8d8;
    }

    .section2__progressBarRect {
        background: #16579778;
        height: 100%;
        padding: 2px;
        border-radius: inherit;
        animation: progressLineTransmission 2.5s 0.3s ease-in-out both;

        /* flex */
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .section2__progressBarCircle {
        background-color: hsl(210deg 100% 19%);
        height: calc(14px - 4px);
        width: calc(14px - 4px);
        border-radius: 50%;
        box-shadow: rgb(0 0 0 / 16%) 0px 1px 4px;
        border: 1px solid #fff;
    }

    .section2__progressBarPoint {
        color: var(--color2);
        margin-top: 8px;
        font-size: 12px;
        font-weight: var(--boldFont);
        position: absolute;
    }

    .section2__progressBarPoint--start {
        left: 0;
    }

    .section2__progressBarPoint--end {
        right: 0;
    }





    .content-wrapper {
        height: auto !important;
    }

    .align-center-bottom {
        height: auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .gap-btn {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .align-center-bottom button {
        max-width: 173px;
    }

    .sidenav {
        position: relative;
        right: -1px;
        display: grid;
        top: -27em;
        background: #000;
        padding: 15px;
        border-radius: 7px;
        transition: width 2s;
        transition-timing-function: cubic-bezier(0.1, 0.7, 1.0, 0.1);
        max-width: 170px;
        float: right;
    }

    button#rightFloatBtn {
        position: relative;
        right: 0;
        display: grid;
        top: -30em;
        background: #000;
        padding: 15px;
        border-radius: 7px 0 0 7px;
        transform: translate(-1px, 0px);
    }

    .form__container {
        margin: 2rem;
        background-color: #fff;
        border-radius: 2rem;
        padding: 1rem 0;
    }

    .title__container {
        width: 100%;
        padding: 1rem 1.8rem;
        display: flex;
        align-items: center;
        background: #003060;
        border-radius: 2rem 2rem 0 0;
    }



    .title__container h1 {
        color: #fff;
        font-size: 18px;
        margin-bottom: 0;
        width: 23%;
    }

    .title__container p {
        color: var(--shadow-color);
        font-size: 0.75rem;
    }

    .body__container {
        display: flex;

    }

    .left__container {
        width: 40%;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 1.25rem 0;
        margin-right: 0;
        padding-right: 1.8rem;
        background: #ebebeb;
    }

    .side__titles {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: end;
        margin-right: 0;
        gap: 14px;
    }

    button.tablinks.active h3 {
        font-weight: 600;
        color: #003060;
    }

    .title__name {
        padding: 0.6rem 0.1rem;
        margin-bottom: 0.25rem;
    }

    .title__name h3 {
        margin-bottom: 0.20rem;
        text-align: right;
        color: #3d3d3d;
        font-size: 0.8rem;
    }

    .title__name p {
        text-align: right;
        color: var(--shadow-color);
        font-size: 0.75rem;
    }

    .progress__bar__container {
        padding-top: 0.6rem;
        /* height: 100%; */
    }

    .progress__bar__container ul .active {
        background-color: #003060;
    }

    li.tablinksli.completed {
        background: #05841b;
    }

    li.tablinksli.completed::before {
        content: '✓' !important;
        position: absolute;
        top: 0;
        left: 0;
        color: #3d3d3d;
    }

    .progress__bar__container ul li {
        display: flex;
        align-items: center;
        justify-content: center;
        list-style: none;
        background: var(--shadow-color);
        padding: 0.5rem 0.6rem;
        margin-bottom: 1.2rem;
        border-radius: 50%;
        font-size: 1.4rem;
        color: #ffffff;
        margin-left: 0;
    }

    .progress__bar__container ul {
        padding-left: 0;
    }

    .progress__bar__container ul li ion-icon {
        z-index: 9;
    }

    .progress__bar__container ul li::after {
        content: '';
        width: 1px;
        height: 11vh;
        position: absolute;
        background-color: var(--shadow-color);
    }

    .progress__bar__container ul .active::after {
        content: '';
        width: 1px;
        height: 11vh;
        position: absolute;
        background-color: var(--secondery-color);
    }

    .right__container {
        width: 100%;
        display: flex;
        padding: 1.5rem 1.5rem;
    }

    .right__container fieldset {
        border: none;
    }

    .sub__title__container {
        padding: 1rem 0 1.2rem 0;
        border-bottom: 1px solid #42434e;
    }

    .sub__title__container h2 {
        color: #000;
        margin: 0.4rem 0;
    }

    .sub__title__container p {
        font-size: 0.75rem;
        color: var(--shadow-color);
    }

    .active__form {
        display: none;
    }

    .input__container {
        width: 100%;
        display: flex;
        flex-direction: column;
        margin-top: 1.25rem;
    }

    .input__container label {
        color: #ffffff;
        font-size: 0.75rem;
        margin-bottom: 0.4rem;
    }

    .input__container input {

        padding: 0.5rem;
        font-size: 1.4rem;
        border-radius: 0.75rem;
        background: none;
        border: 1px solid var(--secondery-color);
        margin-bottom: 1.2rem;
        outline: none;
        color: #ffffff;
    }

    .nxt__btn {
        width: 25%;
        display: flex;
        align-items: center;
        justify-content: center;
        /* float: right; */
        /* width: 30%; */
        padding: 0.75rem 0;
        font-size: 1.1rem;
        font-weight: bold;
        border-radius: 2rem;
        background: var(--secondery-color);
        color: #ffffff;
        /* border: none; */
        /* outline: none; */
        /* margin-left: 20em; */
        /* margin-top: 0.55em;     */
    }

    .nxt__btn:hover {
        transform: scale(1.03);
        background: #1cd68c;
        cursor: pointer;
    }

    .buttons {
        display: flex;
        align-items: center;
        justify-content: space-between;
        /* float: right; */
        margin: 0;
        padding: 0;
        /* justify-content:space-evenly; */
    }

    .prev__btn {
        margin: 0;
        /* padding: 0.5rem 1.5rem 0.7rem 1.5rem  ; */
        /* background-color: #857373; */
        display: flex;
        align-items: center;
        justify-content: center;
        background: none;
        border: none;
        color: #ffffff;
        font-size: 18px;
        /* margin-right: 20px; */
        /* margin-left: 15rem; */
        cursor: pointer;
    }

    /*------------------------------- form-2 design --------------------*/

    .selection {
        display: flex;
        align-items: center;
        border: 1px solid var(--shadow-color);
        padding: 0.5rem 0.5rem;
        margin-bottom: 1rem;
        border-radius: 0.5rem;
        width: 100%;
    }

    .selection:hover {
        border: 1px solid var(--secondery-color);
        background-color: var(--primary-color);
        cursor: pointer;
    }

    .imoji {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.4rem 0.4rem;
        margin: 0 0.2rem;
        margin-right: 0.4rem;
        font-size: 2rem;
        font-weight: 900;
        color: yellow;
        border-radius: 50%;
        background: var(--shadow-color);
    }


    .descriptionTitle h3 {
        color: #ffffff;
        margin-bottom: 4px;
    }

    .descriptionTitle p {
        font-size: 0.75rem;
        color: var(--shadow-color);
    }


    /*-------------------------------------- form-4 design----------------------------------------- */
    .slider {
        display: flex;
        align-items: center;
        /* justify-content: center; */
        -webkit-appearance: none;
        appearance: none;
        width: 100%;
        height: 0.75rem;
        background: #d3d3d3;
        outline: none;
        opacity: 0.7;
        -webkit-transition: .2s;
        transition: opacity .2s;
        position: relative;
        margin-top: 3rem;
        /* margin-right:5rem ; */
    }



    .slider:hover {
        opacity: 1;
    }

    .slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        background: var(--secondery-color);
        cursor: pointer;
        position: relative;
    }

    .slider::-webkit-range-thumb {
        width: 50px;
        height: 50px;
        background: var(--secondery-color);
        cursor: pointer;
        position: relative;
    }

    .output__value {

        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        border-radius: 2em;
        padding: 0.8rem 0.8rem;
        position: absolute;
        background-color: var(--secondery-color);
    }

    .output__value::after {
        content: '';
        width: 1.5rem;
        height: 1.5rem;
        background-color: black;
        transform: rotate(45deg);
        position: absolute;
        margin-top: 40px;
        background-color: var(--secondery-color);
    }

    .progress .progress-bar {
        position: relative;
        left: 0;
        bottom: 0;
        height: auto;
        border: 1px solid #fff;
        border-radius: 7px;
        background: linear-gradient(45deg, #9b9b9b, #003060);
    }

    .container__setup__btn {
        display: flex;
        align-items: flex-end;
        justify-content: flex-end;
        min-height: 200px;
        width: 100%;
    }




    @media only screen and (max-width: 600px) {

        .form__container {
            margin: 0;
            padding: 0;
        }

        .body__container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
        }

        .right__container {
            width: 90%;
            margin: 0;
        }

        .title__container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;

        }

        .left__container {
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
            border: none;
        }

        .buttons {
            justify-content: space-between;
        }

        .descriptionTitle h3 {
            font-size: 1rem;
        }

        .descriptionTitle p {
            font-size: 0.6rem;

        }


        .side__titles {
            display: none;
            flex-direction: row;
            /* align-items: center;  */
            justify-content: space-evenly;
        }

        .title__name h3 {
            font-size: 0.75rem;

        }

        .title__name p {
            font-size: 0.5rem;

        }

        .progress__bar__container {
            margin-bottom: 0;

        }

        .progress__bar__container ul {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            margin-bottom: 0;
            /* width: 50%; */
            padding: 0 2rem;

        }

        .progress__bar__container ul::before {
            height: 5vh;
        }

        .progress__bar__container ul li {
            margin: 10px;
            padding: 10px;
            /* transform: rotate(90deg); */
        }

        .progress__bar__container ul .active::before {
            transform: rotate(90deg);
        }

    }

    .text-truncate.invoiced i:nth-child(1) {
        color: 007bff !important;
    }

    .chartContainer {
        width: 100%;
        height: 450px;
    }

    .card.flex-fill h5 {
        color: #fff;
        font-size: 15px;
    }

    .card.flex-fill .card-header {
        padding: 15px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card.flex-fill .card-header input,
    .card.flex-fill .card-header select {
        max-width: 155px;
    }

    .head-title,
    .head-input {
        display: flex;
        gap: 10px;
        align-items: center;
    }


    .card-body::after,
    .card-footer::after,
    .card-header::after {
        display: none;
    }

    .pin-tab {
        cursor: pointer;
        text-decoration: none;
    }


    .robo-element {
        height: 50vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 25px;
    }

    .robo-element img {
        width: 200px;
        height: 200px;
        object-fit: contain;
    }

    .row.dashboard-charts .card .card-header .card-title {
        white-space: nowrap;
        font-size: 12px;
    }
</style>

<!-- Resources -->
<script src="<?= BASE_URL ?>public/assets/core.js"></script>
<script src="<?= BASE_URL ?>public/assets/charts.js"></script>
<script src="<?= BASE_URL ?>public/assets/animated.js"></script>
<script src="<?= BASE_URL ?>public/assets/forceDirected.js"></script>
<script src="<?= BASE_URL ?>public/assets/sunburst.js"></script>

<!-- dfgvdf -->

<div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid my-4 ">
            <div class="row dashboard-charts">
                <?php if (chartExistorNot()) {
                    $allcomponent = getDashboardAllComponent();
                    // console($allcomponent);
                    if ($allcomponent['status'] == 'success') {
                        foreach ($allcomponent['data'] as $component) {

                            include("component/charts/" . $component['page_name']);
                        }
                ?>

                        <div class="note-text">
                            <h6 class="text-xs font-bold">Note</h6>
                            <hr>
                            <ul class="pl-3 new-add-dashboard">
                                <li style="list-style-type: disc;">
                                    <p class="text-xs"> You can create your own dashboard as your requirement.</p>
                                </li>
                                <li>
                                    <div class="align-center-bottom">
                                        <a type="button" href="manage-charts.php" class="btn btn-primary gap-btn" target="_blank"><i class="fa fa-plus"></i>Add More</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    <?php
                    } else { ?>
                        <div class="align-center-bottom" style=" min-height: 500px;">
                            <a type="button" href="manage-charts.php" class="btn btn-primary gap-btn" target="_blank"><i class="fa fa-plus"></i>Configure Dashboard</a>
                        </div>
                        <div class="note-text">
                            <h6 class="text-xs font-bold">Note</h6>
                            <hr>
                            <ul class="pl-3">
                                <li style="list-style-type: disc;">
                                    <p class="text-xs">You can create your own dashboard as your requirement.</p>
                                </li>
                            </ul>
                        </div>
                    <?php
                    }
                } else { ?>
                    <div class="align-center-bottom" style=" min-height: 500px;">
                        <a type="button" href="manage-charts.php" class="btn btn-primary gap-btn" target="_blank"><i class="fa fa-plus"></i>Configure Dashboard</a>
                    </div>
                    <div class="note-text">
                        <h6 class="text-xs font-bold">Note</h6>
                        <hr>
                        <ul class="pl-3">
                            <li style="list-style-type: disc;">
                                <p class="text-xs">You can create your own dashboard as your requirement.</p>
                            </li>
                        </ul>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </section>
</div>
<?php require_once("../common/footer.php"); ?>





<script>
    $(function() {
        $('input[name="daterange"]').daterangepicker({
            opens: 'left'
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('DD-MM-YYYY') + ' to ' + end.format('DD-MM-YYYY'));
        });
    });


    $('#filterDropdown')
        .select2()
        .on('select2:open', () => {});
</script>

<script>
    $(".pin-btn").click(function() {

        const chartID = $(this).attr("id");
        const pageName = $(this).attr("id") + ".php";

        // AJAX CALL 
        $.ajax({
            url: `<?= BASE_URL ?>branch/location/ajaxs/reports/ajax-addToDashboard.php?page=${pageName}`,
            async: false,
            success: function(result) {

                let res = jQuery.parseJSON(result);

                if (res['status'] == 'success') {
                    // console.log(chartID);
                    // console.log(pageName);
                    $(`#${chartID}`).text(res['txt']);
                    // $(`#${chartID}`).attr("disabled", "disabled");

                    let Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    Toast.fire({
                        icon: res['status'],
                        title: res['message']
                    });

                } else {
                    let Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    Toast.fire({
                        icon: res['status'],
                        title: res['message']
                    });
                }

            }
        });
    });
</script>