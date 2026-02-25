<?php
$client = new \GuzzleHttp\Client();
try {
    $res = $client->post('http://127.0.0.1:8000/api/v1/social-login', [
        'json' => [
            'email' => 'test2@example.com',
            'name' => 'User',
            'provider' => 'firebase_email',
            'provider_id' => 'fake_uid_123',
            'id_token' => 'fake_token',
            'device_name' => 'flutter_app',
        ]
    ]);
    echo $res->getBody();
} catch (\Exception $e) {
    if ($e->hasResponse()) {
        echo $e->getResponse()->getBody();
    } else {
        echo $e->getMessage();
    }
}
