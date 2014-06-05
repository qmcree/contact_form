<?php

require_once '../../vendor/autoload.php';

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