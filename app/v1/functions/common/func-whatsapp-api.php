<?php
function SendMessageByWhatsappTemplate($data)
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;

    global $isWhatsappActive;

    global $quickcontact;
    $jsonstring = '';

    $mobileno = '+91' . $data['to'];
    $templateName = $data['templatename'];

    $playstorelink = "https://play.google.com/store/apps/details?id=com.claimz.claimz&pcampaignid";
    $appstorelink = "https://apps.apple.com/us/app/claimzhrms/id6450458787";

    if ($data['templatename'] == 'vendor_onboard_msg') {
        $language_name = "en_GB";

        $vendorname = $data['vendorname'];
        $companyname = $data['companyname'];
        $companyCodeNav = $data['companyCodeNav'];
        $vendor_code = $data['vendor_code'];
        $password = $data['password'];
        // $quickcontact=$data['quickcontact']?;
        $current_userName = $data['current_userName'];
        $user_designation = $data['user_designation'];

        $jsonstring = array(
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $mobileno,
            "type" => "template",
            "template" => array(
                "name" => $templateName,
                "language" => array(
                    "code" => $language_name
                ),
                "components" => array(
                    array(
                        "type" => "body",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $vendorname
                            ),
                            array(
                                "type" => "text",
                                "text" => $companyname
                            ),
                            array(
                                "type" => "text",
                                "text" => $companyCodeNav
                            ),
                            array(
                                "type" => "text",
                                "text" => $vendor_code
                            ),
                            array(
                                "type" => "text",
                                "text" => $password
                            ),
                            array(
                                "type" => "text",
                                "text" => $quickcontact
                            ),
                            array(
                                "type" => "text",
                                "text" => $current_userName
                            ),
                            array(
                                "type" => "text",
                                "text" => $user_designation
                            )
                        )
                    )
                )
            )
        );
    } elseif ($data['templatename'] == 'customer_onboard_msg') {

        $language_name = "en_GB";

        $customername = $data['customername'];
        $companyname = $data['companyname'];
        $companyCodeNav = $data['companyCodeNav'];
        $customer_code = $data['customer_code'];
        $password = $data['password'];
        // $quickcontact=$data['quickcontact']?;
        $current_userName = $data['current_userName'];
        $user_designation = $data['user_designation'];

        $jsonstring = array(
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $mobileno,
            "type" => "template",
            "template" => array(
                "name" => $templateName,
                "language" => array(
                    "code" => $language_name
                ),
                "components" => array(
                    array(
                        "type" => "body",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $customername
                            ),
                            array(
                                "type" => "text",
                                "text" => $companyname
                            ),
                            array(
                                "type" => "text",
                                "text" => $companyCodeNav
                            ),
                            array(
                                "type" => "text",
                                "text" => $customer_code
                            ),
                            array(
                                "type" => "text",
                                "text" => $password
                            ),
                            array(
                                "type" => "text",
                                "text" => $quickcontact
                            ),
                            array(
                                "type" => "text",
                                "text" => $current_userName
                            )
                        )
                    )
                )
            )
        );
    } elseif ($data['templatename'] == 'so_created_order_confirmation_msg') {

        $language_name = "en_GB";

        $customername = $data['customername'];
        $companyname = $data['companyname'];
        $so_number = $data['so_number'];
        // $quickcontact=$data['quickcontact']?;
        $current_userName = $data['current_userName'];

        $jsonstring = array(
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $mobileno,
            "type" => "template",
            "template" => array(
                "name" => $templateName,
                "language" => array(
                    "code" => $language_name
                ),
                "components" => array(
                    array(
                        "type" => "body",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $customername
                            ),
                            array(
                                "type" => "text",
                                "text" => $so_number
                            ),
                            array(
                                "type" => "text",
                                "text" => $quickcontact
                            ),
                            array(
                                "type" => "text",
                                "text" => $companyname
                            ),
                            array(
                                "type" => "text",
                                "text" => $current_userName
                            )
                        )
                    )
                )
            )
        );
    } elseif ($data['templatename'] == 'password_change_msg') {

        $language_name = "en_GB";

        $userfullname = $data['userfullname'];
        // $quickcontact=$data['quickcontact']?;
        $current_userName = $data['current_userName'];

        $jsonstring = array(
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $mobileno,
            "type" => "template",
            "template" => array(
                "name" => $templateName,
                "language" => array(
                    "code" => $language_name
                ),
                "components" => array(
                    array(
                        "type" => "body",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $userfullname
                            ),
                            array(
                                "type" => "text",
                                "text" => $quickcontact
                            ),
                            array(
                                "type" => "text",
                                "text" => $current_userName
                            )
                        )
                    )
                )
            )
        );
    } elseif ($data['templatename'] == 'welcome_note_to_new_user_msg') {

        $language_name = "en_GB";
        $buttonlink = BASE_URL;

        $userfullname = $data['userfullname'];
        $username = $data['username'];
        $password = $data['password'];
        // $quickcontact=$data['quickcontact']?;
        $current_userName = $data['current_userName'];

        $jsonstring = array(
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $mobileno,
            "type" => "template",
            "template" => array(
                "name" => $templateName,
                "language" => array(
                    "code" => $language_name
                ),
                "components" => array(
                    array(
                        "type" => "body",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $userfullname
                            ),
                            array(
                                "type" => "text",
                                "text" => $username
                            ),
                            array(
                                "type" => "text",
                                "text" => $password
                            ),
                            array(
                                "type" => "text",
                                "text" => $quickcontact
                            ),
                            array(
                                "type" => "text",
                                "text" => $current_userName
                            )
                        )
                    ),
                    array(
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => "0",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $buttonlink
                            )
                        )
                    )
                )
            )
        );
    } elseif ($data['templatename'] == 'after_payment_to_vendor_against_invoice_msg') {
        $language_name = "en_GB";

        $vendorname = $data['vendorname'];
        $invoiceno = $data['invoiceno'];
        // $quickcontact=$data['quickcontact']?;
        $current_userName = $data['current_userName'];

        $jsonstring = array(
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $mobileno,
            "type" => "template",
            "template" => array(
                "name" => $templateName,
                "language" => array(
                    "code" => $language_name
                ),
                "components" => array(
                    array(
                        "type" => "body",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $vendorname
                            ),
                            array(
                                "type" => "text",
                                "text" => $invoiceno
                            ),
                            array(
                                "type" => "text",
                                "text" => $quickcontact
                            ),
                            array(
                                "type" => "text",
                                "text" => $current_userName
                            )
                        )
                    )
                )
            )
        );
    } elseif ($data['templatename'] == 'after_payment_to_vendor_msg') {
        $language_name = "en_GB";

        $vendorname = $data['vendorname'];
        $amount = $data['amount'];
        // $quickcontact=$data['quickcontact']?;
        $current_userName = $data['current_userName'];

        $jsonstring = array(
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $mobileno,
            "type" => "template",
            "template" => array(
                "name" => $templateName,
                "language" => array(
                    "code" => $language_name
                ),
                "components" => array(
                    array(
                        "type" => "body",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $vendorname
                            ),
                            array(
                                "type" => "text",
                                "text" => $amount
                            ),
                            array(
                                "type" => "text",
                                "text" => $quickcontact
                            ),
                            array(
                                "type" => "text",
                                "text" => $current_userName
                            )
                        )
                    )
                )
            )
        );
    } elseif ($data['templatename'] == 'sending_po_to_vendor_msg') {
        $language_name = "en_GB";

        $vendorname = $data['vendorname'];
        // $quickcontact=$data['quickcontact']?;
        $current_userName = $data['current_userName'];

        $jsonstring = array(
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $mobileno,
            "type" => "template",
            "template" => array(
                "name" => $templateName,
                "language" => array(
                    "code" => $language_name
                ),
                "components" => array(
                    array(
                        "type" => "body",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $vendorname
                            ),
                            array(
                                "type" => "text",
                                "text" => $quickcontact
                            ),
                            array(
                                "type" => "text",
                                "text" => $current_userName
                            )
                        )
                    )
                )
            )
        );
    } elseif ($data['templatename'] == 'getting_selected_for_quotation_msg') {
        $language_name = "en_GB";

        $vendorname = $data['vendorname'];
        // $quickcontact=$data['quickcontact']?;
        $current_userName = $data['current_userName'];

        $jsonstring = array(
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $mobileno,
            "type" => "template",
            "template" => array(
                "name" => $templateName,
                "language" => array(
                    "code" => $language_name
                ),
                "components" => array(
                    array(
                        "type" => "body",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $vendorname
                            ),
                            array(
                                "type" => "text",
                                "text" => $quickcontact
                            ),
                            array(
                                "type" => "text",
                                "text" => $current_userName
                            )
                        )
                    )
                )
            )
        );
    } elseif ($data['templatename'] == 'request_for_quotation_or_quotation_publish_msg') {
        $language_name = "en_GB";

        $vendorname = $data['vendorname'];

        // $quickcontact=$data['quickcontact']?;
        $current_userName = $data['current_userName'];

        $rfqlink = $data['rfqlink'];

        $jsonstring = array(
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $mobileno,
            "type" => "template",
            "template" => array(
                "name" => $templateName,
                "language" => array(
                    "code" => $language_name
                ),
                "components" => array(
                    array(
                        "type" => "body",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $vendorname
                            ),
                            array(
                                "type" => "text",
                                "text" => $quickcontact
                            ),
                            array(
                                "type" => "text",
                                "text" => $current_userName
                            )
                        )
                    ),
                    array(
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => "0",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $rfqlink
                            )
                        )
                    )
                )
            )
        );
    } elseif ($data['templatename'] == 'after_settlement_of_invoices_msg') {
        $language_name = "en_GB";

        $customername = $data['customername'];
        // $quickcontact=$data['quickcontact']?;
        $current_userName = $data['current_userName'];

        $jsonstring = array(
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $mobileno,
            "type" => "template",
            "template" => array(
                "name" => $templateName,
                "language" => array(
                    "code" => $language_name
                ),
                "components" => array(
                    array(
                        "type" => "body",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $customername
                            ),
                            array(
                                "type" => "text",
                                "text" => $quickcontact
                            ),
                            array(
                                "type" => "text",
                                "text" => $current_userName
                            )
                        )
                    )
                )
            )
        );
    } elseif ($data['templatename'] == 'after_collection_from_customer_msg') {
        $language_name = "en_GB";

        $customername = $data['customername'];
        $invoiceno = $data['invoiceno'];
        // $quickcontact=$data['quickcontact']?;
        $current_userName = $data['current_userName'];

        $jsonstring = array(
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $mobileno,
            "type" => "template",
            "template" => array(
                "name" => $templateName,
                "language" => array(
                    "code" => $language_name
                ),
                "components" => array(
                    array(
                        "type" => "body",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $customername
                            ),
                            array(
                                "type" => "text",
                                "text" => $invoiceno
                            ),
                            array(
                                "type" => "text",
                                "text" => $quickcontact
                            ),
                            array(
                                "type" => "text",
                                "text" => $current_userName
                            )
                        )
                    )
                )
            )
        );
    } elseif ($data['templatename'] == 'invoice_sent_msg') {
        $language_name = "en_GB";

        $customername = $data['customername'];
        $invoiceno = $data['invoiceno'];

        // $quickcontact=$data['quickcontact']?;
        $current_userName = $data['current_userName'];

        $invoicelink = $data['invoicelink'];

        $jsonstring = array(
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $mobileno,
            "type" => "template",
            "template" => array(
                "name" => $templateName,
                "language" => array(
                    "code" => $language_name
                ),
                "components" => array(
                    array(
                        "type" => "body",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $customername
                            ),
                            array(
                                "type" => "text",
                                "text" => $invoiceno
                            ),
                            array(
                                "type" => "text",
                                "text" => $quickcontact
                            ),
                            array(
                                "type" => "text",
                                "text" => $current_userName
                            )
                        )
                    ),
                    array(
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => "0",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $invoicelink
                            )
                        )
                    )
                )
            )
        );
    } elseif ($data['templatename'] == 'after_location_is_created') {
        $language_name = "en_US";

        $companyname = $data['companyname'];
        $location_name = $data['location_name'];
        $username = $data['username'];
        $password = $data['password'];
        $current_userName = $data['current_userName'];

        $jsonstring = array(
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $mobileno,
            "type" => "template",
            "template" => array(
                "name" => $templateName,
                "language" => array(
                    "code" => $language_name
                ),
                "components" => array(
                    array(
                        "type" => "body",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $companyname
                            ),
                            array(
                                "type" => "text",
                                "text" => $location_name
                            ),
                            array(
                                "type" => "text",
                                "text" => $username
                            ),
                            array(
                                "type" => "text",
                                "text" => $password
                            ),
                            array(
                                "type" => "text",
                                "text" => $current_userName
                            )
                        )
                    )
                )
            )
        );
    } elseif ($data['templatename'] == 'after_creating_a_branch') {
        $language_name = "en_US";

        $companyname_branch = $data['companyname_branch'];
        $username = $data['username'];
        $password = $data['password'];
        $current_userName = $data['current_userName'];

        $jsonstring = array(
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $mobileno,
            "type" => "template",
            "template" => array(
                "name" => $templateName,
                "language" => array(
                    "code" => $language_name
                ),
                "components" => array(
                    array(
                        "type" => "body",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $companyname_branch
                            ),
                            array(
                                "type" => "text",
                                "text" => $username
                            ),
                            array(
                                "type" => "text",
                                "text" => $password
                            ),
                            array(
                                "type" => "text",
                                "text" => $current_userName
                            )
                        )
                    )
                )
            )
        );
    } elseif ($data['templatename'] == 'company_onboard') {
        $language_name = "en_US";

        $companyname = $data['companyname'];
        $username = $data['username'];
        $password = $data['password'];
        $current_userName = $data['current_userName'];
        $user_designation = $data['user_designation'];

        $jsonstring = array(
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $mobileno,
            "type" => "template",
            "template" => array(
                "name" => $templateName,
                "language" => array(
                    "code" => $language_name
                ),
                "components" => array(
                    array(
                        "type" => "body",
                        "parameters" => array(
                            array(
                                "type" => "text",
                                "text" => $companyname
                            ),
                            array(
                                "type" => "text",
                                "text" => $username
                            ),
                            array(
                                "type" => "text",
                                "text" => $password
                            ),
                            array(
                                "type" => "text",
                                "text" => $current_userName
                            ),
                            array(
                                "type" => "text",
                                "text" => $user_designation
                            )
                        )
                    )
                )
            )
        );
    }




    $jsonstring = json_encode($jsonstring);

    if ($isWhatsappActive == 'no') {
        return true;
    } else {
        $whatsappresponce = sendwhatsappMsg($jsonstring);
        $whatsappresponcejsonstring = json_encode($whatsappresponce, true);

        $partyCode = '';
        $operationSlug = '';
        $documentId = '';
        $documentCode = '';

        $ins = "INSERT INTO `erp_globalwhatsapp` 
                SET
                    `company_id`='$company_id',
                    `branch_id`='$branch_id',
                    `location_id`='$location_id',
                    `partyCode`='$partyCode',
                    `operationSlug`='$operationSlug',
                    `documentId`='$documentId',
                    `documentCode`='$documentCode',
                    `templateName`='$templateName',
                    `msgBody`='$jsonstring',
                    `responce`='$whatsappresponcejsonstring',
                    `sendStatus`='1',
                    `created_by`='$created_by',
                    `updated_by`='$updated_by'";
        $globMailIns = queryInsert($ins);
        $globMailId = $globMailIns['insertedId'];

        return $whatsappresponce;
    }
}
