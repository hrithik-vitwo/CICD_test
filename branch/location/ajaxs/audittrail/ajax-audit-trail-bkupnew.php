<?php
require_once("../../../../app/v1/connection-branch-admin.php");

if (isset($_GET['auditTrailBodyContent'])) {

    if (isset($_POST['ccode']) && !empty($_POST['ccode'])) {
        $ccode=str_replace('-','/',$_POST['ccode']);
        $auditQuery = "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))";
        $resultaudit = queryGet($auditQuery);
        $auditQuery = "SELECT * FROM `" . ERP_AUDIT_TRAIL . "` WHERE `document_number` = '" . $ccode . "' AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id AND `status`='active' GROUP BY `action_code`  ORDER BY `id` DESC";
        $resultaudit = queryGet($auditQuery, true);
        if ($resultaudit['status'] == 'success') {
            $rowresultaudit = $resultaudit["data"];
?>
            <ol class="timeline">

                <?php
                $count = count($rowresultaudit);
                foreach ($rowresultaudit as $trailKey => $trailData) {
                ?>
                    <li class="timeline-item mb-0 bg-transparent auditTrailBodyContentLine" type="button" data-toggle="modal" data-id="<?= $trailData['id']; ?>" data-ccode="<?= $ccode; ?>" data-target="#innerModal">
                        <span class="timeline-item-icon | filled-icon"><img src="<?= BASE_URL ?>public/storage/audittrail/<?= $trailData['trail_type']; ?>.png" width="25" height="25"></span>
                        <span class="step-count"><?= $count; ?></span>
                        <div class="new-comment font-bold">
                            <p><?= getCreatedByUser($trailData['created_by']); ?>
                            <ul class="ml-3 pl-0">
                                <li style="list-style: disc; color: #a7a7a7;"><?= formatDateORDateTime($trailData['created_at']); ?></li>
                            </ul>
                            </p>
                        </div>
                    </li>
                    <p class="mt-0 mb-5 ml-5"><?= $trailData['action_title']; ?></p>
                <?php
                $count--;
            } ?>


            </ol>

        <?php } else { ?>

            <ol class="timeline">
                <li class="timeline-item mb-0 bg-transparent">
                    <div class="new-comment font-bold">
                        <p>History not found </p>
                    </div>
                </li>
            </ol>
        <?php }
    } else { ?>

        <ol class="timeline">
            <li class="timeline-item mb-0 bg-transparent">
                <div class="new-comment font-bold">
                    <p>Somthing went wrong!</p>
                </div>
            </li>
        </ol>
        <?php }
} elseif (isset($_GET['auditTrailBodyContentLine'])) {
    if (isset($_POST['ccode']) && !empty($_POST['ccode']) && isset($_POST['id']) && !empty($_POST['id'])) {
        $ccode=str_replace('-','/',$_POST['ccode']);
        $currentAuditQuery = "SELECT * FROM `" . ERP_AUDIT_TRAIL . "` WHERE `id`=" . $_POST['id'] . " AND `document_number` = '" . $ccode . "' AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id AND `status`='active'";
        $previousAuditQuery = "SELECT * FROM `" . ERP_AUDIT_TRAIL . "` WHERE id < (SELECT `id` FROM `" . ERP_AUDIT_TRAIL . "` WHERE `id`=" . $_POST['id'] . " AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id  AND `status`='active' LIMIT 1) AND `document_number` = '" . $ccode . "' AND (`trail_type`='ADD' OR `trail_type`='EDIT') AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id AND `status`='active' ORDER BY id DESC LIMIT 1";
        $currentResultaudit = queryGet($currentAuditQuery);
        $previousResultaudit = queryGet($previousAuditQuery);

        if ($currentResultaudit['status'] == 'success') {
            // --------------------------Solution 1
            $data2_serialized = $currentResultaudit['data']['action_data'];
            $dataP2 = unserialize($data2_serialized);
            $data2 = convertArraysToStrings($dataP2);

            $changes = [];
            if ($previousResultaudit['status'] == 'success') {
                $data1_serialized = $previousResultaudit['data']['action_data'];
                $dataP1 = unserialize($data1_serialized);
                $data1 = convertArraysToStrings($dataP1);

                $changes = compareArrays($data1, $data2);
            }
            // console($changes);

            $pattern = "/^'|'$/";
            $html = '';
            $html .= '<div class="modal-header">
                        <div class="head-audit">
                        <p>' . $currentResultaudit['data']['action_title'] . '</p>
                        </div>
                        <div class="head-audit">
                        <p>' . getCreatedByUser($currentResultaudit['data']['created_by']) . '</p>
                        <p>' . formatDateORDateTime($currentResultaudit['data']['created_at']) . '</p>
                        </div>
                    </div>';
            $html .= '<div class="modal-body p-0"> 
                        <div class="free-space-bg">
                            <div class="color-define-text">
                                <p class="update"><span></span> Record Updated </p>
                                <p class="all"><span></span> New Added </p>
                            </div>
                            <ul class="nav nav-tabs pb-0" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="concise-tab" data-toggle="tab" href="#consize" role="tab" aria-controls="concise" aria-selected="true"><i class="fa fa-th-large mr-2" aria-hidden="true"></i> Concised View</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" id="detail-tab" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="false"><i class="fa fa-list mr-2" aria-hidden="true"></i>Detailed View</a>
                            </li>
                            </ul>
                        </div>
                        <div class="tab-content pt-0" id="myTabContent">
                            <div class="tab-pane fade show active" id="consize" role="tabpanel" aria-labelledby="consize-tab">';
                                foreach ($changes as $section => $values) {
                                    $notChanges=1;
                                    // console($changes);
                                    $html .= '<div class="dotted-box">
                                    <p class="overlap-title">' . $section . '</p>';
                                    foreach ($values as $key => $value) {
                                        if(isset($dataP2[$section][$key]) && !empty($dataP2[$section][$key])){
                                            $notChanges++;
                                            if (is_array($value)) {
                                                $html .= '<div class="dotted-box">
                                                <p class="overlap-title">' . $key . '</p>';
                                                foreach ($value as $ckey => $cvalue) {
                                                    if (is_array($cvalue)) {                                                        
                                                        $html .= '<div class="dotted-box">
                                                        <p class="overlap-title">' . $ckey . '</p>';
                                                        foreach ($cvalue as $cckey => $ccvalue) {
                                                            if (is_array($ccvalue)) {
                                                                $html .= '<div class="box-content">
                                                                <p>Information</p>
                                                                <p>Under Development, Report This is Bug</p>
                                                                </div>';
                                                            } else {
                                                                $highlit = (!empty(preg_replace($pattern, "", $ccvalue))) ? 'hightlight' : 'hightlight-green';
                                                                $newValue=preg_replace($pattern, "", $dataP2[$section][$key][$ckey][$cckey]);
                                                                $html .= '<div class="box-content ' . $highlit . ' ">
                                                                <p>' . ucfirst(str_replace(array('-', '_'), ' ', $cckey)) . '</p>
                                                                <div class ="existing-cross-data">
                                                                <p class="exist-value">' . preg_replace($pattern, "", $ccvalue) . '</p>
                                                                <p>' .  $newValue .'</p>
                                                                </div>
                                                                </div>';
                                                            }
                                                        }
                                                    } else {
                                                        $highlit = (!empty(preg_replace($pattern, "", $cvalue))) ? 'hightlight' : 'hightlight-green';
                                                        $newValue=preg_replace($pattern, "", $dataP2[$section][$key][$ckey]);
                                                        $html .= '<div class="box-content ' . $highlit . ' ">
                                                        <p>' . ucfirst(str_replace(array('-', '_'), ' ', $ckey)) . '</p>
                                                        <div class ="existing-cross-data">
                                                        <p class="exist-value">' . preg_replace($pattern, "", $cvalue) . '</p>
                                                        <p>' .  $newValue .'</p>
                                                        </div>
                                                        </div>';
                                                    }
                                                }
                                                $html .= '</div>';
                                            } else {
                                                $highlit = (!empty(preg_replace($pattern, "", $value))) ? 'hightlight' : 'hightlight-green';
                                                $newValue=preg_replace($pattern, "", $dataP2[$section][$key]);
                                                $html .= '<div class="box-content ' . $highlit . ' ">
                                                <p>' . ucfirst(str_replace(array('-', '_'), ' ', $key)) . '</p>
                                                <div class ="existing-cross-data">
                                                <p class="exist-value">' . preg_replace($pattern, "", $value) . '</p>
                                                <p>' .  $newValue . '</p>
                                                </div>
                                                </div>';
                                            }
                                        }
                                    }
                                    if($notChanges==1){
                                        $html .= '<div class="box-content">
                                        <p>No Changes Found</p>
                                        <div class ="existing-cross-data">
                                        <p></p>
                                        </div>
                                        </div>';
                                    }

                                    $html .= '</div>';
                                }
                            $html .= '</div>
                        <div class="tab-pane fade" id="detail" role="tabpanel" aria-labelledby="detail-tab">';
                        foreach ($dataP2 as $section => $values) {
                            $html .= '<div class="dotted-box">
                            <p class="overlap-title">' . $section . '</p>';
                            foreach ($values as $key => $value) {
                                if (is_array($value)) {
                                    $html .= '<div class="dotted-box">
                                    <p class="overlap-title">' . $key . '</p>';
                                    foreach ($value as $ckey => $cvalue) {
                                        if (is_array($cvalue)) {
                                            $html .= '<div class="dotted-box">
                                            <p class="overlap-title">' . $ckey . '</p>';
                                            foreach ($cvalue as $cckey => $ccvalue) {
                                                if (is_array($ccvalue)) {
                                                    $html .= '<div class="dotted-box">
                                                    <p class="overlap-title">' . $cckey . '</p>';
                                                    foreach ($ccvalue as $ccckey => $cccvalue) {
                                                        if (is_array($cccvalue)) {
                                                            $html .= '<div class="box-content">
                                                            <p>Information</p>
                                                            <p>Under Development, Report This is Bug</p>
                                                            </div>';
                                                        } else {
                                                            if (isset($changes[$section][$key][$ckey][$cckey][$ccckey])) {

                                                                $highlit = (!empty(preg_replace($pattern, "", $changes[$section][$key][$ckey][$cckey]))) ? 'hightlight' : 'hightlight-green';
                                                                $html .= '<div class="box-content ' . $highlit . ' ">
                                                                <p>' . ucfirst(str_replace(array('-', '_'), ' ', $ccckey)) .'</p>
                                                                <div class ="existing-cross-data">
                                                                <p class="exist-value">' . preg_replace($pattern, "", $changes[$section][$key][$ckey][$cckey][$ccckey]) . '</p>
                                                                <p>' . $cccvalue . '</p>
                                                                </div>
                                                                </div>';
                                                            } else {
                                                                $html .= '<div class="box-content">
                                                                <p>' . ucfirst(str_replace(array('-', '_'), ' ', $ccckey)) . '</p>
                                                                <div class ="existing-cross-data">
                                                                <p>' . $cccvalue . '</p>
                                                                </div>
                                                                </div>';
                                                            }
                                                        }
                                                    }
                                                    $html .= '</div>';
                                                } else {
                                                    if (isset($changes[$section][$key][$ckey][$cckey])) {

                                                        $highlit = (!empty(preg_replace($pattern, "", $changes[$section][$key][$ckey]))) ? 'hightlight' : 'hightlight-green';
                                                        $html .= '<div class="box-content ' . $highlit . ' ">
                                                        <p>' . ucfirst(str_replace(array('-', '_'), ' ', $cckey)) .'</p>
                                                        <div class ="existing-cross-data">
                                                        <p class="exist-value">' . preg_replace($pattern, "", $changes[$section][$key][$ckey][$cckey]) . '</p>
                                                        <p>' . $ccvalue . '</p>
                                                        </div>
                                                        </div>';
                                                    } else {
                                                        $html .= '<div class="box-content">
                                                        <p>' . ucfirst(str_replace(array('-', '_'), ' ', $cckey)) . '</p>
                                                        <div class ="existing-cross-data">
                                                        <p>' . $ccvalue . '</p>
                                                        </div>
                                                        </div>';
                                                    }

                                                }
                                            }
                                            $html .= '</div>';
                                        } else {
                                            if (isset($changes[$section][$key][$ckey])) {

                                                $highlit = (!empty(preg_replace($pattern, "", $changes[$section][$key]))) ? 'hightlight' : 'hightlight-green';
                                                $html .= '<div class="box-content ' . $highlit . ' ">
                                                <p>' . ucfirst(str_replace(array('-', '_'), ' ', $ckey)) .'</p>
                                                <div class ="existing-cross-data">
                                                <p class="exist-value">' . preg_replace($pattern, "", $changes[$section][$key][$ckey]) . '</p>
                                                <p>' . $cvalue . '</p>
                                                </div>
                                                </div>';
                                            } else {
                                                $html .= '<div class="box-content">
                                                <p>' . ucfirst(str_replace(array('-', '_'), ' ', $ckey)) . '</p>
                                                <div class ="existing-cross-data">
                                                <p>' . $cvalue . '</p>
                                                </div>
                                                </div>';
                                            }
                                        }
                                    }
                                    $html .= '</div>';
                                } else {
                                    if (isset($changes[$section][$key])) {
                                        $highlit = (!empty(preg_replace($pattern, "", $changes[$section][$key]))) ? 'hightlight' : 'hightlight-green';
                                        
                                        $html .= '<div class="box-content ' . $highlit . ' ">
                                        <p>' . ucfirst(str_replace(array('-', '_'), ' ', $key)) . '</p>
                                        <div class ="existing-cross-data">
                                        <p class="exist-value">' . preg_replace($pattern, "", $changes[$section][$key]) . '</p>
                                        <p>' . $value . '</p>
                                        </div>
                                        </div>';
                                    } else {
                                        $html .= '<div class="box-content">
                                    <p>' . ucfirst(str_replace(array('-', '_'), ' ', $key)) . '</p>
                                    <div class ="existing-cross-data">
                                    <p>' . $value . '</p>
                                    </div>
                                    </div>';
                                    }
                                }
                            }
                            $html .= '</div>';
                        }
            $html .= '</div></div>';

            echo $html;
        } else { ?>

            <ol class="timeline">
                <li class="timeline-item mb-0 bg-transparent">
                    <div class="new-comment font-bold">
                        <p>History not found</p>
                    </div>
                </li>
            </ol>
        <?php }
    } else { ?>

        <ol class="timeline">
            <li class="timeline-item mb-0 bg-transparent">
                <div class="new-comment font-bold">
                    <p>Somthing went wrong!</p>
                </div>
            </li>
        </ol>
    <?php }
} else {
    ?>

    <ol class="timeline">

        <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
            <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
            <div class="new-comment font-bold">
                <p>Loading...
                <ul class="ml-3 pl-0">
                    <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                </ul>
                </p>
            </div>
        </li>
        <p class="mt-0 mb-5 ml-5">Loading...</p>

        <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
            <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
            <div class="new-comment font-bold">
                <p>Loading...
                <ul class="ml-3 pl-0">
                    <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                </ul>
                </p>
            </div>
        </li>
        <p class="mt-0 mb-5 ml-5">Loading...</p>


    </ol>

<?php } ?>