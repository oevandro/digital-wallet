<?php

namespace App\Http\Services;

class AuthorizationTransationServices
{
    /**
     * send
     *
     * @param  string $type
     * @param  int $user_id
     * @param  float $amount
     * @return bool
     */
    public function send($type, $user_id, $amount)
    {
        $url = 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';

        $fields = [
            'user_id'      => $user_id,
            'amount' => $amount,
            'type' => $type
        ];

        $fields_string = http_build_query($fields);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($curl, CURLOPT_HEADER, true);
        $result = curl_exec($curl);
        curl_close($curl);

        if (!empty($result)) {
            return true;
        }

        return false;
    }
}
