<?php
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
 * @return boolean True if the user currently had access
 */
function get_access($userId)
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
        $currentUserId = $urabe->select_one(sprintf($sql, 'userId'), array($token));
        $admin = get_system_property("admin")->username;
        return (object)array(
            "hasAccess" => $admin == $username || $userId == $currentUserId,
            "isAdmin" => $admin == $username,
            "userId" => $currentUserId,
            "username" => $username
        );
    }
}
