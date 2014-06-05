<?php
require_once '../protected/CaptchaResponse.php';
require_once '../protected/ContactMessage.php';

try {
    $email = new ContactMessage();
    $email->send();
    echo json_encode(array(
        'type' => 'success',
    ));
}
catch (Exception $e) {
    echo json_encode(array(
        'type' => 'error',
        'message' => $e->getMessage(),
    ));
}