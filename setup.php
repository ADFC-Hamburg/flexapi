<?php

include_once __DIR__ . '/FlexAPI.php';

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
// header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    echo "";
    die;
}

try {
    if ($method !== 'POST') {
        throw(new Exception("Wrong method for setup. Must be POST", 400));
    }

    $request = (array) json_decode(file_get_contents("php://input"));

    if (!array_key_exists('resetSecret', $request)) {
        throw(new Exception('Setup secret missing.', 401));
    }
    if ($request['resetSecret'] !== FlexAPI::get('setupSecret')) {
        throw(new Exception('Setup secret invalid.', 401));
    }

    FlexAPI::setup($request);

    http_response_code(200);
    $response['code'] = 200;
} catch (Exception $exc) {
    http_response_code($exc->getCode());
    echo jsenc([
        'severity' => 3,
        'message' => 'Could not setup API: '.$exc->getMessage(),
        "code" => $exc->getCode()
    ]);
    die;
}

echo jsenc($response);
