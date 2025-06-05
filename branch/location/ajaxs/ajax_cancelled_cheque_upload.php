<?php
/*echo "<pre>";
print_r($_FILES);
exit();*/
require_once("../../../app/v1/connection-branch-admin.php");
$curl = curl_init();
// curl_setopt_array($curl, array(
//   CURLOPT_URL => 'http://ocrserver.centralindia.cloudapp.azure.com:8000/api/v1/ocr/cheque/',
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => '',
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => 'POST',
//   CURLOPT_POSTFIELDS => array('file'=> new CURLFILE($_FILES['file']['tmp_name'])),
// ));
// $dir = BASE_DIR."uploads/1/cancelled-cheque/";
$dir = COMP_STORAGE_DIR."/cancelled-cheque/";
$fileUploadObj = uploadFile($_FILES['file'], $dir, ["pdf","png","jpg","jpeg"]);
if($fileUploadObj["status"]=="success"){
  $cheque_url = COMP_STORAGE_URL."/cancelled-cheque/".$fileUploadObj["data"];
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://ocr.vitwo.ai/api/v1/ocr/cheque/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('cheque_url' => $cheque_url),
  ));
}else{
  echo json_encode([
    "status" => "warning",
    "message" => "Cheque upload failed, please try again",
    "fileUploadObj" => $fileUploadObj,
    "dir"=>$dir
  ], true);
}

echo $response = curl_exec($curl);
/*if($response){
  echo $response;
}else{
  swalToast("warning", "Something went wrong try again!");
}
*/
curl_close($curl);
