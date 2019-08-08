<?php

include_once "../lib/urabe/MYSQLKanojoX.php";
include_once "../lib/urabe/Urabe.php";
include_once "../lib/urabe/UrabeResponse.php";
include_once "../utils/Caterpillar.php";
include_once "../config/InstallationException.php";

/**
 * Gets a system web service response
 *
 * @param string $service The service name
 * @param string $responseName The response name
 * @return array The system response
 */
function get_system_response($service, $responseName)
{
    $response =  require dirname(__FILE__) . '/../config/server_response.php';
    return $response->{$service}->{$responseName};
}
/**
 * Installation process
 */
$properties =  require dirname(__FILE__) . '/../config/properties.php';
//Add here the name of the script to run on installation, all scripts should be stored at
//the SQL folder
$scripts = array('drop_tables.sql', 'user.sql', 'chat.sql', 'chat_members.sql', 'chat_entry.sql', 'contacts.sql');
//If SQL folder is moved redirect this path
$scripts_path = ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "sql" . DIRECTORY_SEPARATOR;
//1: Validates the SQL folder exists
if (!file_exists($scripts_path))
    throw new InstallationException("MissingSQLFolder");
//2: Validates that all SQL scripts exists
foreach ($scripts as &$script) {
    $pth = $scripts_path . $script;
    if (!file_exists($pth))
        throw new InstallationException("MissingSQLScript", realpath($scripts_path) . DIRECTORY_SEPARATOR . $script);
}
//3: Parse SQL files before start executing the SQL queries
$lines = array();
$sqlComments = '@(([\'"]).*?[^\\\]\2)|((?:\#|--).*?$|/\*(?:[^/*]|/(?!\*)|\*(?!/)|(?R))*\*\/)\s*|(?<=;)\s+@ms';
foreach ($scripts as &$script) {
    $pth = $scripts_path . $script;
    $sql = file_get_contents($pth);
    $SQLines = explode(";", $sql);
    foreach ($SQLines as &$line) {
        $line = trim(preg_replace('/\s\s+/', ' ', $line));
        $line = trim(preg_replace($sqlComments, ' ', $line));
        if (strlen($line) > 0)
            array_push($lines, $line);
    }
}
//4: Create an SQL transaction
$connector  =  new  MYSQLKanojoX();
$conn = $properties->connection;
$connector->init($conn);
$urabe = new Urabe($connector);
try {
    //5: Create tables
    $connector->connection->begin_transaction();
    foreach ($lines as &$sql) {
        $result = $urabe->query($sql);
        if (!$result->succeed)
            throw new InstallationException("QueryError", $sql);
    }
    //6: Create user admin
    $admin = $properties->admin->username;
    $cat = new Caterpillar();
    $pass = $cat->random_password();
    $userPass = $pass->password;
    $user = (object) array("username" => $admin, "pass" => $pass->encrypted);
    $result = $urabe->insert("users", $user);
    if ($result->succeed) {
        $result = get_system_response("installer", "InstallationSucceed");
        $result->admin->username = $admin;
        $result->admin->pass = $userPass;
        $connector->connection->commit();
        $connector->connection->close();
        echo json_encode($result, JSON_PRETTY_PRINT);
    } else
        throw new InstallationException("ErrorCreatingAdmin", $sql);
} catch (Exception $e) {
    $connector->connection->rollback();
    $connector->connection->close();
    throw $e;
}