<?php

namespace App\Http\Services;

class NotificationServices
{
    /**
     * send
     *
     * @param  int $user_id
     * @param  string $message
     * @return bool
     */
    public function send($user_id, $message)
    {
        $url = 'https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04';

        $fields = [
            'user_id '      => $user_id,
            'message' => $message
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
