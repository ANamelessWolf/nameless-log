<?php

/**
 * Nameless log entry point
 */

include_once  "lib/urabe/HasamiWrapper.php";
include_once  "services/UserService.php";
include_once  "services/AliceService.php";
include_once  "services/ChatService.php";
include_once  "utils/ICostumeErrorMessage.php";
include_once  "utils/Caterpillar.php";
include_once  "utils/ServiceUtils.php";
include_once  "config/UnauthorizedAccessException.php";

$content = new WebServiceContent();
$service_name = $content->url_params[0];
switch ($service_name) {
    case 'alice':
        $service  =  new  AliceService();
        break;
    case 'users':
        $service  =  new  UserService();
        break;
    case 'chat':
        include_once  "services/ChatMembersService.php";
        $service  =  new  ChatService();
        break;
    case 'chat_member':
        include_once  "services/ChatMembersService.php";
        $service  =  new  ChatMembersService();
        break;
    case 'chat_entry':

        include_once  "services/ChatEntryService.php";
        $service  =  new  ChatEntryService();
        break;
    case 'contacts':
        include_once  "services/ContactService.php";
        $service  =  new  ContactService();
        break;
}

try {

    if (!is_null($service))
        $result = $service->get_response();
    else
        $result = get_system_response("common", "UnknownAction");
} catch (Exception $e) {
    if (array_key_exists("ICostumeErrorMessage", class_implements($service)))
        throw new Exception($service->get_error_msg($e), 1);
    else
        throw $e;
}
echo json_encode($result, JSON_PRETTY_PRINT);