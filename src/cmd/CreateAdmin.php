<?php

include_once "../lib/urabe/MYSQLKanojoX.php";
include_once "../lib/urabe/Urabe.php";
include_once "../lib/urabe/UrabeResponse.php";
include_once "../utils/Caterpillar.php";

$properties =  require dirname(__FILE__) . '/../config/properties.php';
$conn = $properties->connection;
$admin = $properties->admin->username;
$connector  =  new  MYSQLKanojoX();
$connector->init($conn);
$urabe = new Urabe($connector);
$caterpillar = new Caterpillar();
$password = $caterpillar->random_password();
$values = (object)array(
    "username" => $admin,
    "pass" => $password->encrypted
);
$response = new UrabeResponse();
try {
    $qResult = $urabe->insert("users", $values);
    if ($qResult->succeed)
        echo json_encode($response->get_response("Admin created", array("username" => $admin, "pass" => $password->password)));
    else
        echo json_encode($response->get_response("Admin already exists", array()));
} catch (Exception $e) {
    $response = $response->get_response("Admin already exists", array());
    $response->error = $e->getMessage();
    echo json_encode($response);
}
