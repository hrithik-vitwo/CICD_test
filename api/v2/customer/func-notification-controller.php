<?php
require_once("api-common-func.php");
class CustomerNotificationController
{
    function sendNotification($device_tokens, $message,$link)
    {
        try {
            $SERVER_API_KEY = 'AAAAdwPuc94:APA91bG5p3LzKKZwdBpNFHU9ZZe1yR9hs4qN72TWe2ULkclyJMQ-E_PAo-fKoh-svjC17xhZf61Q9kewP9ISYxdq-sPzQJhIkZExdrG2SFItyt-A_41Z3wEZ0ZZiEp7Alg-E2F7vCFx5';

            // payload data, it will vary according to requirement
            $notification = [
                "title" => $message,
            	"link" => $link
                ];
            $data = [
                "to" => $device_tokens, // for multiple device ids
                "notification" => $notification,
            ];
            $dataString = json_encode($data);

            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            $response = curl_exec($ch);
            curl_close($ch);
            $data_r['status'] = '1';
            $data_r['msg'] = 'success';
            $data_r['data'] = json_decode($response);
            $response = json_decode($response);
            sendApiResponse($data_r, 200);
            // return response()->json($data_r, 200);
            //return $response;
        } catch (\Throwable $th) {
            //throw $th;
            $datae['status'] = "0";
            $datae["msg"] = "failed";
            $datae["data"] = "something went wrong";
            sendApiResponse($datae, 501);
            // return response()->json($datae, 501);
        }
    }
}


?>