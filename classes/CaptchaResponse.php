<?php

use GuzzleHttp\Client;

class CaptchaResponse 
{
    const ERROR_INVALID = "The text entered doesn't match the image.";

    public $challenge;
    public $response;
    private $verifyUrl;
    private $privateKey;

    public function __construct($challenge, $response)
    {
        $this->challenge = $challenge;
        $this->response = $response;

        $config = $this->getConfig();
        $this->verifyUrl = $config['verify_url'];
        $this->privateKey = $config['privatekey'];
    }

    /**
     * @return bool TRUE if valid, FALSE if invalid.
     */
    public function validate()
    {
        $client = new Client();
        $response = $client->post($this->verifyUrl, [
            'body' => [
                'privatekey' => $this->privateKey,
                'remoteip' => $_SERVER['REMOTE_ADDR'],
                'challenge' => $this->challenge,
                'response' => $this->response,
            ],
        ]);

        return (strpos($response->getBody(), 'true') !== FALSE);
    }

    private function getConfig()
    {
        return require __DIR__ . '/../config/captcha.php';
    }
} 
