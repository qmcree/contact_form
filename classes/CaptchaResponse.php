<?php

use GuzzleHttp\Client;

class CaptchaResponse 
{
    const VERIFY_URL = 'https://www.google.com/recaptcha/api/verify';
    const PRIVATE_KEY = '6LfC6vASAAAAAJyLMKTH01oyaAhw4VnIGzS8E-Br';
    const ERROR_INVALID = "The text entered doesn't match the image.";

    /**
     * @param $challenge string
     * @param $response string
     * @return bool TRUE if valid, FALSE if invalid.
     */
    public static function validate($challenge, $response)
    {
        $client = new Client();
        $response = $client->post(self::VERIFY_URL, [
            'body' => [
                'privatekey' => self::PRIVATE_KEY,
                'remoteip' => $_SERVER['REMOTE_ADDR'],
                'challenge' => $challenge,
                'response' => $response,
            ],
        ]);

        return (strpos($response->getBody(), 'true') !== FALSE);
    }
} 
