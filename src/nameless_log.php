<?php
/**
 * Nameless log entry point
 */

include_once  "lib/urabe/HasamiWrapper.php";
include_once  "utils/Caterpillar.php";
include_once  "utils/ServiceUtils.php";
include_once  "services/UserService.php";
$content = new WebServiceContent();
$service_name = $content->url_params[0];
switch ($service_name) {
    case 'users':
        $service  =  new  UserService();
        break;
    case 'chat':
        include_once  "services/ChatService.php";
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
}
if (!is_null($service))
    $result = $service->get_response();
else
    $result = get_system_response("common", "UnknownAction");
echo json_encode($result, JSON_PRETTY_PRINT);