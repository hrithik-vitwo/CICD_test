<?php
require_once("../../../../app/v1/connection-branch-admin.php");

if (isset($_POST['ccode']) && !empty($_POST['ccode'])) {
    $ccode = str_replace('-', '/', $_POST['ccode']);
    $mailQuery = "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))";
    $resultmail = queryGet($mailQuery);
    $mailQuery = "SELECT * FROM `erp_globalmail` WHERE `partyCode` = '" . $ccode . "' AND `status`='active' ORDER BY `email_id` DESC LIMIT 50";
    $resultmail = queryGet($mailQuery, true);
    if ($resultmail['status'] == 'success') {
        $rowresultmail = $resultmail["data"];

        foreach ($rowresultmail as $trailKey => $trailData) {
?>

            <div class="card mb-2">
                <div class="card-body">
                    <div class="left-details">
                        <div class="icon">
                            <i class="fa fa-user icon-font"></i>
                        </div>
                        <div class="text">
                            <p class="font-bold">To: <?= $trailData['toaddress']; ?></p>
                            <p ><?= $trailData['mailTitle']; ?></p>
                            <!-- <p><?= $trailData['msgBody']; ?></p> -->
                        </div>
                    </div>
                    <div class="right-details">
                        <div class="date-time-details">
                            <p><?= formatDateORDateTime($trailData['created_at'],1); ?></p>
                        </div>
                    </div>
                </div>
            </div>

        <?php }
    } else { ?>
        <div class="card mb-2">
            <div class="card-body">
                <div class="left-details">

                    <div class="text">
                        <p class="font-bold">History not found</p>
                    </div>
                </div>
            </div>
        </div>
    <?php }
} else { ?>
    <div class="card mb-2">
        <div class="card-body">
            <div class="left-details">

                <div class="text">
                    <p class="font-bold">Somthing went wrong!<< /p>
                </div>
            </div>
        </div>
    </div>

<?php } ?>