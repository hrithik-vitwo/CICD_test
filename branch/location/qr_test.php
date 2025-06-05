<?php
include_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require("../../vendor/autoload.php");
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Writer\PngWriter;


try {
    // Start output buffering
   

    // Create the QR code
    $result = Builder::create()
        ->writer(new PngWriter())
        ->data('hello people')
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
        ->size(300)
        ->margin(10)
        ->build();

    // Clear any previous output and set the Content-Type header
  
    header('Content-Type: image/png');
    
    // Output the QR code image directly
    echo $result->getString();

    // End and flush the output buffer
  

} catch (Exception $e) {
  
    echo 'Error: ' . $e->getMessage();
}


?>