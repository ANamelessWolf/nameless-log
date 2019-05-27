<?php
include_once  "services/UserService.php";
$service  =  new  UserService();
$result  =  $service->get_response();
echo json_encode($result, JSON_PRETTY_PRINT);
?>