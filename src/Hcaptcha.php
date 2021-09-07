<?php

namespace neverbehave;

class Hcaptcha
{
    private $secret_key = "0x0000000000000000000000000000000000000000";
    private $api = "https://hcaptcha.com/siteverify";

    public function __construct($secret_key = false, $api = false)
    {
        if ($api) {
            $this->api = $api;
        }

        if ($secret_key) {
            $this->secret_key = $secret_key;
        } else {
            trigger_error("Init without secret key, use dummy key instead. Don't use in production stage!", E_USER_WARNING);
        }
    }

    public function challenge($token, $remote_ip = false)
    {
        $data = [
            'secret' => $this->secret_key,
            'response' => $token
        ];

        if ($remote_ip) {
            $data['remoteip'] = $remote_ip;
        }

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\n",
                'content' => http_build_query($data),
                'ssl' => [
                    'verify_peer' => true,
                ],
                'ignore_errors' => true
            ]];

        $context = stream_context_create($options);
        $result = json_decode(file_get_contents($this->api, false, $context), true);

        return new  HcaptchaResponse($result);
    }
}