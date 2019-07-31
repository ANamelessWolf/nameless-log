<?php
include_once  "model/Chat.php";
include_once  "model/UserAccess.php";
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
 * Gets a system property
 *
 * @param string $propertyName The property name
 * @return array The system response
 */
function get_system_property($propertyName)
{
    $response =  require dirname(__FILE__) . '/../config/properties.php';
    return $response->{$propertyName};
}
/**
 * Gets the access for current logged user
 * Logged user is only allowed to modify elements that had his ids
 *
 * @param int $userId The user id
 * @return UserAccess The current user access
 */
function get_access($userId = null)
{
    session_start();
    $token = $_SESSION["token"];
    if (is_null($token)) {
        KanojoX::$http_error_code = 403;
        $response = get_system_response("users", "Unauthorized");
        throw new Exception($response->error);
    } else {
        $service = new UserService();
        $urabe = $service->get_urabe();
        $sql = $urabe->format_sql_place_holders("SELECT %s FROM " . $service->get_table_name() . " WHERE SHA1(CONCAT(username,pass)) = @1");
        $loggedUsername = $urabe->select_one(sprintf($sql, 'username'), array($token));
        $loggedUserId = $urabe->select_one(sprintf($sql, 'userId'), array($token));
        return  new UserAccess($loggedUsername, $loggedUserId, $userId);
    }
}
/**
 * Check if the logged user has admin privileges
 *
 * @return boolean True if the user currently had admin privileges
 */
function has_admin_privileges()
{
    session_start();
    $token = $_SESSION["token"];
    if (is_null($token)) {
        KanojoX::$http_error_code = 403;
        $response = get_system_response("users", "Unauthorized");
        throw new Exception($response->error);
    } else {
        $service = new UserService();
        $urabe = $service->get_urabe();
        $sql = $urabe->format_sql_place_holders("SELECT %s FROM " . $service->get_table_name() . " WHERE SHA1(CONCAT(username,pass)) = @1");
        $username = $urabe->select_one(sprintf($sql, 'username'), array($token));
        $admin = get_system_property("admin")->username;
        return  $admin == $username;
    }
}
/**
 * Get the chats available to a given user
 * @param int $userId The user id
 * @return array The chat array
 */
function get_chats($userId = null)
{
    $service  =  new  ChatService();
    $urabe = $service->get_urabe();
    $sql = "SELECT chatId, name FROM chat LEFT JOIN users ON users.userId = chat.userId";
    if (!is_null($userId)) {
        $sql .= " WHERE users.userId=@1";
        $sql = $urabe->format_sql_place_holders($sql);
        $result = $urabe->select($sql, array($userId))->result;
    } else 
        $result = $urabe->select($sql)->result;
    return array_map(function ($item) {
        $chat = new Chat();
        $chat->chatId = $item["chatId"];
        $chat->name = $item["name"];
        return $chat;
    }, $result);
}
