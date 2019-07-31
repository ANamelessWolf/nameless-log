<?php
include_once "../lib/urabe/MYSQLKanojoX.php";
include_once "../lib/urabe/Urabe.php";
include_once "../lib/urabe/UrabeResponse.php";

$response =  require dirname(__FILE__) . '/../config/properties.php';
$conn = $response->connection;
$connector  =  new  MYSQLKanojoX();
$connector->init($conn);
$urabe = new Urabe($connector);
$tables = array("chat", "users");
for ($i = 0; $i < sizeof($tables); $i++) 
    $response = $urabe->query("DELETE FROM " . $tables[$i]);
$connector->close();
$response = new UrabeResponse();
echo json_encode($response->get_response("Database erased",array()));