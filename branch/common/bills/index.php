<script>
    // var url = "http://ocrserver.centralindia.cloudapp.azure.com:8000/api/v1/ocr/azure/";

    // var xhr = new XMLHttpRequest();
    // xhr.open("GET", url);
    
    // xhr.onreadystatechange = function () {
    //   if (xhr.readyState === 4) {
    //       console.log(xhr.status);
    //       console.log(xhr.responseText);
    //   }};
    
    // xhr.send();
    var requestOptions = {
      method: 'GET',
      redirect: 'follow'
    };
    
    fetch("https://ocrserver.centralindia.cloudapp.azure.com:8000/api/v1/ocr/azure/", requestOptions)
      .then(response => response.text())
      .then(result => console.log(result))
      .catch(error => console.log('error', error));

</script>

<?php

$curl = curl_init();

$opt = array(
  CURLOPT_URL => 'http://ocrserver.centralindia.cloudapp.azure.com:8000/api/v1/ocr/azure/',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => 'GET'
);

curl_setopt_array($curl, $opt);

$response = curl_exec($curl);

echo "Checking...";
if ($response === FALSE) {
  die("Curl Failed: " . curl_error($curl));
} else {
    //print_r($opt);
    echo $response;
}

curl_close($curl);


// $curl = curl_init();

// curl_setopt_array($curl, array(
//   CURLOPT_URL => 'http://api.open-meteo.com/v1/forecast?latitude=52.52&longitude=13.41',
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => '',
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => 'GET',
// ));

// $response = curl_exec($curl);

// curl_close($curl);
// echo $response;




?>