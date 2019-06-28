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
$connector->connect();

$scripts = array('drop_tables.sql', 'user.sql', 'chat.sql', 'chat_members.sql', 'chat_entry.sql', 'contacts.sql');
//If sql folder is moved redirect this path
$scripts_path = ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "sql" . DIRECTORY_SEPARATOR;
if (!file_exists($scripts_path))
    throw new Exception("Installation can not find, sql folder.");
// Validate all scripts
foreach ($scripts as &$script) {
    $pth = $scripts_path . $script;
    if (!file_exists($pth))
        throw new Exception("Installation can not start, file '" . realpath($scripts_path) . DIRECTORY_SEPARATOR . $script . "' is missing.");
}
$status = array(
    (object) array("task" => 'Drop tables', "result" => "", "error" => ""),
    (object) array("task" => 'Create User Table', "result" => "", "error" => ""),
    (object) array("task" => 'Create Chat Table', "result" => "", "error" => ""),
    (object) array("task" => 'Create Members Table', "result" => "", "error" => ""),
    (object) array("task" => 'Create Entry Table', "result" => "", "error" => ""),
    (object) array("task" => 'Create Contacts Table', "result" => "", "error" => "")
);
$index = 0;
foreach ($scripts as &$script) {
    $pth = $scripts_path . $script;
    $sql = file_get_contents($pth);
    $status[$index]->result = $connector->connection->multi_query($sql);
    
    if ($status[$index]->result) {
        do {
            /* store first result set */
            if ($result = $connector->connection->store_result()) {
                while ($row = $connector->connection->fetch_row()) {
                    printf("%s\n", $row[0]);
                }
                $result->free();
            }
            /* print divider */
            if ($connector->connection->more_results()) {
                printf("-----------------\n");
            }
        } while ($connector->connection->next_result());
    }
    $status[$index]->error = $connector->error($sql);

}
var_dump($status);
