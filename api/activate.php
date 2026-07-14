<?php

header('Content-Type: application/json');
$licenseKey = trim($_POST['license_key'] ?? '');
$serial     = trim($_POST['serial_number'] ?? '');

if (empty($licenseKey)) {
    echo json_encode([
        'status' => 'ERROR',
        'message' => 'License key required'
    ]);
    exit;
}

$file = __DIR__ . '/jkmisa_serverlicense.json';

if (!file_exists($file)) {
    echo json_encode([
        'status' => 'ERROR',
        'message' => 'License database not found'
    ]);
    exit;
}

$licenses = json_decode(file_get_contents($file), true);

if (!is_array($licenses)) {
    echo json_encode([
        'status' => 'ERROR',
        'message' => 'Invalid license database'
    ]);
    exit;
}

foreach ($licenses as &$license) {

    if (($license['license_key'] ?? '') !== $licenseKey) {
        continue;
    }

    // Disabled license
    if (isset($license['licensed']) && !$license['licensed']) {
        echo json_encode([
            'status' => 'ERROR',
            'message' => 'License disabled'
        ]);
        exit;
    }

    // First activation
    if (empty($license['serial_number'])) {
        $license['serial_number'] = $serial;
        $license['activated'] = date('Y-m-d H:i:s');

        file_put_contents(
            $file,
            json_encode($licenses, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    // Already activated on another device
    if (
        !empty($license['serial_number']) &&
        !empty($serial) &&
        $license['serial_number'] !== $serial
    ) {
        echo json_encode([
            'status' => 'ERROR',
            'message' => 'License already activated on another device'
        ]);
        exit;
    }

    echo json_encode([
        'status' => 'OK',
        'license_key' => $license['license_key'],
        'license_type' => $license['license_type'] ?? 'LIFETIME-LICENSED',
        'charging' => $license['charging'] ?? true,
        'licensed' => $license['licensed'] ?? true,
        'trial' => $license['trial'] ?? false,
        'registered_vendos' => $license['registered_vendos'] ?? 999,
        'registered_pcs' => $license['registered_pcs'] ?? 999,
        'expiration_date' => $license['expiration_date'] ?? '2099-12-31 23:59:59',
        'remaining_days' => $license['remaining_days'] ?? 99999,
        'serial_number' => $license['serial_number'],
        'activated' => $license['activated'] ?? null
    ]);
    exit;
}

echo json_encode([
    'status' => 'ERROR',
    'message' => 'Invalid License'
]);
