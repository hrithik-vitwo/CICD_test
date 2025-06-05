<?php
include_once("../../../../../app/v1/connection-branch-admin.php");
class TemplateJournal
{
    private $company_id, $branch_id, $location_id, $created_by, $updated_by;
    function __construct()
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->location_id = $location_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
    public function getGlLableDetailById($id)
    {
        $sql = "SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE id=$id";
        $res = queryGet($sql);
        if ($res['numRows'] > 0) {
            return $res['data'];
        }
        return null;
    }
    public function printManageJouranl($id = 0)
    {
        $dbObj = new Database();
        $sql_list = "SELECT temp_table1.id, temp_table1.jv_no, temp_table1.party_code, temp_table1.party_name, temp_table1.parent_id, temp_table1.parent_slug, temp_table1.refarenceCode, temp_table1.documentNo, temp_table1.documentDate, temp_table1.postingDate, temp_table1.remark, temp_table1.total_debit, temp_table1.reverse_jid, temp_table1.journalEntryReference,SUM(credit.credit_amount) AS total_credit,temp_table1.created_by, temp_table1.created_at,temp_table1.updated_at, temp_table1.updated_by FROM ( SELECT journal.id, journal.jv_no, journal.party_code, journal.party_name, journal.parent_id, journal.parent_slug, journal.refarenceCode, journal.documentNo, journal.documentDate, journal.postingDate, journal.remark, journal.reverse_jid, journal.journalEntryReference,journal.journal_created_at as created_at, journal.journal_created_by as created_by,journal.journal_updated_at AS updated_at, journal.journal_updated_by AS updated_by,SUM(debit.debit_amount) AS total_debit FROM `" . ERP_ACC_JOURNAL . "` AS journal LEFT JOIN `" . ERP_ACC_DEBIT . "` AS debit ON journal.id = debit.journal_id WHERE journal.parent_slug = 'journal'  AND journal.journal_status = 'active' AND journal.company_id=$this->company_id AND journal.branch_id=$this->branch_id AND journal.location_id=$this->location_id AND journal.id=$id GROUP BY journal.id, journal.jv_no, journal.party_code, journal.party_name, journal.parent_id, journal.parent_slug, journal.refarenceCode, journal.documentNo, journal.documentDate, journal.postingDate, journal.remark, journal.reverse_jid, journal.journalEntryReference ) AS temp_table1 LEFT JOIN `" . ERP_ACC_CREDIT . "` AS credit ON temp_table1.id = credit.journal_id GROUP BY temp_table1.id, temp_table1.jv_no, temp_table1.party_code, temp_table1.party_name, temp_table1.parent_id, temp_table1.parent_slug, temp_table1.refarenceCode, temp_table1.documentNo, temp_table1.documentDate, temp_table1.postingDate, temp_table1.remark, temp_table1.reverse_jid, temp_table1.journalEntryReference, temp_table1.total_debit";
        $sqlMainQryObj = $dbObj->queryGet($sql_list);

        $data = $sqlMainQryObj['data'];
        $num_list = $sqlMainQryObj['numRows'];
        // console($data);
        $creditDetail = $dbObj->queryGet("SELECT * FROM `erp_acc_credit` WHERE journal_id =$id", true);
        $debitDetail = $dbObj->queryGet("SELECT * FROM `erp_acc_debit` WHERE journal_id=$id", true);

        // fetch company details
        $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$this->company_id'")['data'];
        $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$this->company_id' AND `fldAdminBranchId`='$this->branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
        $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$this->branch_id' AND `company_id`='$this->company_id'")['data'];
        $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$this->company_id' AND flag='1'")['data'];
        $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$this->branch_id' AND `company_id`='$this->company_id' AND othersLocation_id='$this->location_id'")['data'];
        $companyData = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);

?>


        <style>
            .journal-print-view tr th,
            .journal-print-view tr td {
                background-color: #fff !important;
                color: #000 !important;
                font-size: 0.7rem !important;
            }

            .journal-print-view tr th {
                font-weight: 600 !important;
            }

            .journal-print-view tr th p,
            .journal-print-view tr td p {
                color: #000;
                margin-bottom: 5px;
                font-size: 0.7rem !important;
                font-weight: 500;
            }

            .journal-print-view tr th,
            .journal-print-view tr td {
                border: 0;
            }
        </style>
        <div class="tab-pane" id="classic-view" role="tabpanel" aria-labelledby="profile-tab">
            <div class="card classic-view bg-transparent">
                <div class="card-body classic-view-so-table" style="overflow: auto;">
                    <!-- <button type="button" class="btn btn-primary classic-view-btn float-right" id="printButton">Print Table</button> -->

                    <div class="printable-view">
                        <table class="journal-print-view" style="width: 100%;">
                            <tbody>
                                <tr>

                                    <td colspan="3">
                                        <img style="max-width: 200px; margin-bottom: 7px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $this->company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                        <p class="font-bold"><?= $companyData['company_name'] ?></p>
                                        <p><?= $companyData['location_building_no'] ?>, <?= $companyData['location_flat_no'] ?></p>
                                        <p><?= $companyData['location'] ?>, <?= $companyData['location_street_name'] ?>, <?= $companyData['location_pin_code'] ?></p>
                                        <p><?= $companyData['location_city'] ?>, <?= $companyData['location_district'] ?></p>
                                        <p><?= $companyData['location_state'] ?></p>
                                        <!-- <p>GSTIN/UIN: <?= $companyData['branch_gstin'] ?></p> -->
                                        <!-- <p>Company’s PAN: <?= $companyData['company_pan'] ?></p> -->
                                        <p><?= $companyData['companyEmail'] ?></p>
                                    </td>
                                    <th colspan="2" style="vertical-align: baseline;">
                                        <p class="font-bold">Journal Voucher</p>
                                        <p style="position: relative;">Document Date <span style="position: absolute; left: 120px;"><?= formatDateORDateTime($data['documentDate']) ?></span></p>
                                        <p style="position: relative;">Document Number <span style="position: absolute; left: 120px;"><?= $data['documentNo'] ?></span></p>
                                    </th>
                                </tr>
                                <!-- 
                                <tr>
                                    <td colspan="7" style="padding-top: 10px">
                                        <p>We have debited the account "".vide document no "" and credited the account "40001" for the below mentioned transactions enunciated below</p>
                                    </td>
                                </tr> -->
                            </tbody>
                        </table>
                        <br><br>
                        <table class="journal-print-view" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <th>Document Ref</th>
                                    <th>General Ledger Code</th>
                                    <th>GL Name</th>
                                    <th>Subledger Code</th>
                                    <th>SL Name</th>
                                    <th class="text-right">Debit Amount</th>
                                    <th class="text-right">Credit Amount</th>
                                </tr>
                                <?php $temp = 0; ?>
                                <?php if ($creditDetail['numRows'] > 0) { ?>
                                    <!--  credit loop start -->

                                    <?php foreach ($creditDetail['data'] as $oneCreditData) {
                                        $temp++;
                                    ?>
                                        <tr>
                                            <td><?php echo ($temp == 1) ? $data['documentNo'] : ""; ?></td>
                                            <td><?php echo $this->getGlLableDetailById($oneCreditData['glId'])['gl_code'] ?></td>
                                            <td><?php echo $this->getGlLableDetailById($oneCreditData['glId'])['gl_label'] ?></td>
                                            <td><?php echo ($oneCreditData['subGlCode'] != 0) ? $oneCreditData['subGlCode'] : "" ?></td>
                                            <td><?= $oneCreditData['subGlName'] ?></td>
                                            <td>
                                                <p class="text-right"></p>
                                            </td>
                                            <td>
                                                <p class="text-right"><span><?= decimalValuePreview($oneCreditData['credit_amount']) ?></span></p>
                                            </td>
                                        </tr>

                                    <?php } ?>
                                    <!-- credit loop end -->
                                <?php } ?>

                                <?php if ($debitDetail['numRows'] > 0) { ?>
                                    <!-- debit loop start -->
                                    <?php foreach ($debitDetail['data'] as $oneDebitData) {
                                        $temp++;
                                    ?>
                                        <tr>
                                            <td><?php echo ($temp == 1) ? $data['documentNo'] : ""; ?></td>
                                            <td><?php echo $this->getGlLableDetailById($oneDebitData['glId'])['gl_code'] ?></td>
                                            <td><?php echo $this->getGlLableDetailById($oneDebitData['glId'])['gl_label'] ?></td>
                                            <td><?php echo ($oneDebitData['subGlCode'] != 0) ? $oneDebitData['subGlCode'] : "" ?></td>
                                            <td><?= $oneDebitData['subGlName'] ?></td>
                                            <td>
                                                <p class="text-right"><?= decimalValuePreview($oneDebitData['debit_amount']) ?></span></p>
                                            </td>
                                            <td>
                                                <p class="text-right"><span></p>
                                            </td>
                                        </tr>

                                    <?php } ?>
                                    <!-- debit loop end -->
                                <?php } ?>

                                <tr>
                                    <td colspan="5" class="text-left font-bold">Total</td>
                                    <td class="text-right" style="font-weight: 600;">
                                        <span><?= decimalValuePreview($data['total_credit']) ?></span>
                                    </td>
                                    <td class="text-right" style="font-weight: 600;">
                                        <span><?= decimalValuePreview($data['total_debit']) ?></span>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="8">
                                        <?= number_to_words_indian_rupees($data['total_credit'])  ?> Rupees Only
                                        <p class="font-bold mt-4">Remarks:</p><?= $data['remark'] ?>
                                        <!-- <p>Created By: <b><?= getCreatedByUser($data['created_by']) ?></b></p> -->

                                        <p><b>For Company : <?= $companyData['company_name'] ?></b></p>
                                        <div class="d-flex classic-view-footer gap-3">
                                            <p><b style="margin-right: 10px;">Prepared by : </b> <?= getCreatedByUser($data['created_by']) ?></p>
                                            <p><b style="margin-right: 70px;">Checked by :</b></p>
                                            <p><b style="margin-right: 70px;">Authorised Signatory : </b></p>
                                        </div>

                                    </td>
                                    <!-- <td colspan="5" class="text-right border">
                                        <p class="text-center font-bold"> for <?= $companyData['company_name'] ?></p>
                                        <p class="text-center sign-img">
                                            <img width="160" src="<?= COMP_STORAGE_URL ?>/profile/<?= $companyData['signature'] ?>" alt="">

                                        </p>
                                    </td> -->
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
<?php

    }
}
