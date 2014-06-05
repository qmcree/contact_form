<?php

class CaptchaResponse 
{
    const VERIFY_URL = 'https://www.google.com/recaptcha/api/verify';
    const PRIVATE_KEY = null; // removed for privacy.
    const ERROR_INVALID = "The text entered doesn't match the image.";

    /**
     * @param $challenge string
     * @param $response string
     * @return bool TRUE if valid, FALSE if invalid.
     */
    public static function validate($challenge, $response)
    {
        $request = new HttpRequest(self::VERIFY_URL, HTTP_METH_POST);
        $request->setPostFields(array(
            'privatekey' => self::PRIVATE_KEY,
            'remoteip' => $_SERVER['REMOTE_ADDR'],
            'challenge' => $challenge,
            'response' => $response,
        ));
        $request->send();
        $response = $request->getResponseBody();

        return (strpos($response, 'true') !== FALSE);
    }
} 