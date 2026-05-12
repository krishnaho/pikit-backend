<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotify extends Model
{
    use HasFactory;

    public function sendMessage($user_id, $message, $heading, $type)
    {
        switch ($type) {
            case 'customer':
                $app_id = 'a59bbb89-9316-4ab3-a6fd-02bd53103dc8';
                $api_key = 'YTcwOTQzZDQtMWRiMi00ZTkwLThjMDItMTNkMmQwZjdlMDI2';

                $user_id = strval($user_id);
                $fields = array(
                    'app_id' => $app_id,
                    'include_external_user_ids' => array($user_id),
                    'channel_for_external_user_ids' => 'push',
                    'contents' => array("en" => $message),
                    'headings' => array("en"=> $heading),
                    );
        
                    $fields = json_encode($fields);
        
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Authorization: Basic '.$api_key));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                
                $response = curl_exec($ch);
                curl_close($ch); 
                $SERVER_API_KEY = 'a59bbb89-9316-4ab3-a6fd-02bd53103dc8:YTcwOTQzZDQtMWRiMi00ZTkwLThjMDItMTNkMmQwZjdlMDI2';
                break;
            case 'vendor':

                //

                $app_id = '6121247a-eb5c-42f0-9505-d31720df404c';
                $api_key = 'MmU3MWViYzYtMWZlNy00MzBjLWIwMzktMWY2MGQ5Y2FlMGQw';

                $user_id = strval($user_id);
                $fields = array(
                    'app_id' => $app_id,
                    'include_external_user_ids' => array($user_id),
                    'channel_for_external_user_ids' => 'push',
                    'contents' => array("en" => $message),
                    'headings' => array("en"=> $heading),
                    );
        
                    $fields = json_encode($fields);
        
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Authorization: Basic '.$api_key));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                
                $response = curl_exec($ch);
                curl_close($ch); 
                $SERVER_API_KEY = '6121247a-eb5c-42f0-9505-d31720df404c:MmU3MWViYzYtMWZlNy00MzBjLWIwMzktMWY2MGQ5Y2FlMGQw-83Z2d_pnpEkEM1j06C0lAwu2fO5UQiVeq';
                break;
            case 'delivery':
                $SERVER_API_KEY = 'AAAA0dXrlbQ:APA91bE6euXt4r8m7kq9YifX4wJyuhmM0PhH0F8zNZCulOUL1BsHFK9CgZVGpl0k9DPn7x_SQ69q3Rih4-hQLkKLSyGhyfAyxTr9-lTegCGD5ihWl5SfCG9WcfGONv7buZ4duzRur5Sf';
                break;
        }

        $tokens = DeviceToken::where('user_id', $user_id)->pluck('token')->toArray();

        if ($tokens) {
            $data = [
                "registration_ids" => $tokens,
                "notification" => [
                    "title" => $heading,
                    "body" => $message,
                ],
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

            if ($response === false) {
                echo 'Error sending FCM message: ' . curl_error($ch);
            } else {
                $responseData = json_decode($response, true);
                if (isset($responseData['failure'])) {
                    $failedTokens = [];
                    $results = $responseData['results'];
                    for ($i = 0; $i < count($results); $i++) {
                        if (isset($results[$i]['error']) && ($results[$i]['error'] == 'InvalidRegistration' || $results[$i]['error'] == 'NotRegistered')) {
                            $failedTokens[] = $tokens[$i];
                        }
                    }
                    DeviceToken::where('user_id', $user_id)->whereIn('token', $failedTokens)->delete();
                }
            }
            curl_close($ch);
        }
    }
}
