<?php
/*echo "<pre>";
print_r($_FILES);
exit();*/
require_once("../../../app/v1/connection-branch-admin.php");
$curl = curl_init();
// curl_setopt_array($curl, array(
//   CURLOPT_URL => 'http://ocrserver.centralindia.cloudapp.azure.com:8000/api/v1/ocr/visiting_card/',
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => '',
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => 'POST',
//   CURLOPT_POSTFIELDS => array('file'=> new CURLFILE($_FILES['file']['tmp_name'])),
// ));

$fileUploadObj = uploadFile($_FILES['file'], COMP_STORAGE_DIR."/visiting-card/", ["pdf","png","jpg","jpeg"]);
if($fileUploadObj["status"]=="success"){
  $card_url = COMP_STORAGE_URL."/visiting-card/".$fileUploadObj["data"];

  curl_setopt_array($curl, array(
   CURLOPT_URL => 'https://ocr.vitwo.ai/api/v1/ocr/visiting_card/',
   CURLOPT_RETURNTRANSFER => true,
   CURLOPT_ENCODING => '',
   CURLOPT_MAXREDIRS => 10,
   CURLOPT_TIMEOUT => 0,
   CURLOPT_FOLLOWLOCATION => true,
   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
   CURLOPT_CUSTOMREQUEST => 'POST',
   CURLOPT_POSTFIELDS => array('card_url' => $card_url),
  ));
  
}else{
  echo json_encode([
    "status" => "warning",
    "message" => "Visiting card upload failed, please try again",
    "fileUploadObj" => $fileUploadObj
  ], true);
}

echo $response = curl_exec($curl);

curl_close($curl);
