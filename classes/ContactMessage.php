<?php

use GuzzleHttp\Client;

class ContactMessage
{
    const MAIL_SEND_URL = 'http://www.mailgun.com/qmcree.com/messages';
    const ADDRESS_TO = 'qmcree@gmail.com';
    const SUBJECT = 'Message from %s';
    const MSG_ERROR_EMPTY_FIELDS = 'Please complete all fields.';
    const MSG_ERROR_INVALID_EMAIL = 'Please enter a valid email.';
    const MSG_ERROR_EMAIL_FAILED = "Something's wrong. Please email or call me directly.";

    protected $name, $email, $message, $captchaChallenge, $captchaResponse;

    /**
     * Constructor
     */
    function __construct()
    {
        $this->name = (isset($_POST['name'])) ? $_POST['name'] : '';
        $this->email = (isset($_POST['email'])) ? $_POST['email'] : '';
        $this->message = (isset($_POST['message'])) ? $_POST['message'] : '';
        $this->captchaChallenge = (isset($_POST['recaptcha_challenge_field'])) ? $_POST['recaptcha_challenge_field'] : '';
        $this->captchaResponse = (isset($_POST['recaptcha_response_field'])) ? $_POST['recaptcha_response_field'] : '';

        $this->sanitize();
        $this->validate();
    }

    /**
     * Sanitizes parameters.
     */
    protected function sanitize()
    {
        $this->name = preg_replace('/[^a-z ]|[ ]{2,}/i', '', $this->name);
        $this->message = addslashes(strip_tags($this->message));
    }

    /**
     * Validates parameters and CAPTCHA.
     * @throws Exception
     */
    protected function validate()
    {
        if (!$this->name || !$this->email || !$this->message)
            throw new Exception(self::MSG_ERROR_EMPTY_FIELDS);

        // @see http://regexlib.com/REDetails.aspx?regexp_id=3122
        if (!preg_match('/^[0-9a-zA-Z]+([0-9a-zA-Z]*[-._+])*[0-9a-zA-Z]+@[0-9a-zA-Z]+([-.][0-9a-zA-Z]+)*([0-9a-zA-Z]*[.])[a-zA-Z]{2,20}$/', $this->email))
            throw new Exception(self::MSG_ERROR_INVALID_EMAIL);

        $response = new CaptchaResponse($this->captchaChallenge, $this->captchaResponse);
        if (!$response->validate())
            throw new Exception(CaptchaResponse::ERROR_INVALID);
    }

    /**
     * Sends email.
     * @throws Exception
     */
    public function email()
    {
        $config = $this->getMailConfig();

        $client = new Client();

        try {
            $client->post(self::MAIL_SEND_URL, [
                'auth' => [$config['user'], $config['password']],
                'body' => [
                    'from' => $this->email,
                    'to' => self::ADDRESS_TO,
                    'subject' => sprintf(self::SUBJECT, $this->name),
                    'text' => sprintf("Form submission from %s <%s>\n\n%s", $this->name, $this->email, $this->message),
                ],
            ]);
        }
        catch (GuzzleHttp\Exception\TransferException $e) {
            throw new Exception(self::MSG_ERROR_EMAIL_FAILED);
        }
    }

    private function getMailConfig()
    {
        return require __DIR__ . '/../config/mail.php';
    }
} 