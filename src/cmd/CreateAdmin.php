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
$cat = new Caterpillar();
$pass = $cat->random_password();
$values = (object) array(
    "username" => $admin,
    "pass" => $pass->encrypted
);
$response = new UrabeResponse();
try {
    $qResult = $urabe->insert("users", $values);
    echo json_encode($response->get_response("Admin created", array("username" => $admin, "pass" => $pass->password)));
} catch (Exception $e) {
    $queryResult = $urabe->select("SELECT * FROM users WHERE username = '$admin'");
    $password = $queryResult->result[0]['pass'];
    $response = $response->get_response("Admin already exists", array("username" => $admin, "pass" => $cat->decrypt($password)));
    $response->error = $e->getMessage();
    echo json_encode($response);
}