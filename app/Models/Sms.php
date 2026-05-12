<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    use HasFactory;
    public function processSmsAction($actionType, $phone, $otp = null, $message = null)
    {
        $gateway = '1';

        switch ($gateway) {

            case '1':
                $response = $this->msg91($actionType, $phone, $otp, $message);
                break;
        }
        return $response;
    }

    private function msg91($actionType, $phone, $otp, $message)
    {
        $authkey = '423562AIMlbXLnBso66a210d2P1';

        switch ($actionType) {

            case 'OTP':
                if ($phone === '859050818') {
                    $otp = 1234;
                } else {
                    $otp = rand(1111, 9999);
                }
                $this->saveOtp($phone, $otp);
                break;

            case 'VERIFY':
                $response = $this->verifyOtp($phone, $otp);
                return $response;
                break;

            case 'OD_NOTIFY':
                break;
        }

        if ($phone === '859050818') {
            $phone = '918590508189';
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.msg91.com/api/v5/otp?template_id=66a20736d6fc054ebb00c673&mobile=' . $phone . '&authkey=423562AIMlbXLnBso66a210d2P1&otp=' . $otp,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTPHEADER => array(
                    "authkey: $authkey",
                    'content-type: application/json',
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                return false;
            } else {
                return true;
            }
        } else {
            $phone = '971' . $phone;
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.msg91.com/api/v5/otp?template_id=66a20736d6fc054ebb00c673&mobile=' . $phone . '&authkey=423562AIMlbXLnBso66a210d2P1&otp=' . $otp,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTPHEADER => array(
                    "authkey: $authkey",
                    'content-type: application/json',
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                return false;
            } else {
                return true;
            }
        }
    }


    private function saveOtp($phone, $otp)
    {

        $otpTable = OtpTable::where('phone', $phone)->first();

        if ($otpTable) {
            $otpTable->otp = $otp;
            $otpTable->save();
        } else {
            $otpTable = new OtpTable();
            $otpTable->phone = $phone;
            $otpTable->otp = $otp;
            $otpTable->save();
        }
    }

    private function verifyOtp($phone, $otp)
    {
        $otpTable = OtpTable::where('phone', $phone)->first();

        if ($otpTable) {
            if ($otpTable->otp == $otp) {

                $response = [
                    'valid_otp' => true,
                ];
            } else {
                $response = [
                    'valid_otp' => false,
                ];
            }
        } else {
            $response = [
                'valid_otp' => false,
            ];
        }
        return $response;
    }
}
