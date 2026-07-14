<?php

$serial = $_GET['serial_number'] ?? '';

$licenses = json_decode(
    file_get_contents('jkmisa_serverlicense.json'),
    true
);

foreach ($licenses as $license) {

    if ($license['serial_number'] === $serial) {

        $license['status'] = 'OK';

        header('Content-Type: application/json');
        echo json_encode($license);
        exit;
    }
}

echo json_encode([
    'status' => 'NG',
    'message' => 'No license found'
]);