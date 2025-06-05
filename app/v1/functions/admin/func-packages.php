<?php
//*************************************/INSERT/******************************************//
function insertPackage($POST = [])
{
    global $dbCon;
    global $created_by;
    global $updated_by;
    $returnData = [];

    $packageName = $POST['packageDetails']['packageName'];
    $duration = $POST['packageDetails']['duration'];
    $basePrice = $POST['packageDetails']['basePrice'];
    $description = $POST['packageDetails']['description'];

    $variantDetails = $POST['variantDetails'];

    $insPackage = "INSERT INTO `" . ERP_PACKAGE_MANAGEMENT . "`
                 SET
                     `packageTitle`='$packageName',
                     `packageDuration`='$duration',
                     `packageBasePrice`='$basePrice',
                     `packageDescription`='$description',
                     `created_by`='$created_by',
                     `updated_by`='$updated_by' ";

    if (mysqli_query($dbCon, $insPackage)) {
        $lastPackageId = mysqli_insert_id($dbCon);
        foreach ($variantDetails as $oneVariant) {
            $variantName = $oneVariant['variantName'];
            $isPrimary = $oneVariant['isPrimary'];
            $price = $oneVariant['price'];
            $transaction = $oneVariant['transaction'];
            $OCR = $oneVariant['OCR'];

            $insVariant = "INSERT INTO `".ERP_PACKAGE_VARIANT."`
                             SET
                                 `variantTitle`='$variantName',
                                 `packageId`='$lastPackageId',
                                 `isPrimary`='$isPrimary',
                                 `variantPrice`='$price',
                                 `transaction`='$transaction',
                                 `OCR`='$OCR',
                                 `created_by`='$created_by',
                                 `updated_by`='$updated_by' ";
            if (mysqli_query($dbCon, $insVariant)) {
                $returnData['status'] = "success";
                $returnData['message'] = "Package Addedd Successfull";
            }
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Package Addedd Failed";
    }
    return $returnData;
}

function fetchVariantDetails($packageId)
{
    global $dbCon;
    $returnData = [];

    $selectVariant = "SELECT * FROM `" .ERP_PACKAGE_VARIANT. "` WHERE packageId='$packageId'";
    if ($res = mysqli_query($dbCon, $selectVariant)) {
        $returnData['status'] = "success";
        $returnData['message'] = "Data found";
        $returnData['data'] = $res->fetch_all(MYSQLI_ASSOC);
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Data not found";
    }
    return $returnData;
}
//*************************************/END/******************************************//