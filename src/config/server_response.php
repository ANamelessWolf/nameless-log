<?php
return (object) array(
    "common" =>
    (object) array(
        "UnknownAction" => (object) array(
            "succeed" => false,
            "error" => "Service not found"
        )
    ),
    "entries" =>
    (object) array(
        "notAMemberMsg" => "It seems you are not a member of the current chat, or the chat does not exists.",
        "protectedEntry" => "The entry can not be deleted with the current user"
    ),
    "users" =>
    (object) array(
        "usernameExists" => (object) array(
            "succeed" => false,
            "error" => "The username is already registered",
            "affected_rows" => 0,
            "result" => []
        ),
        "NotLogged" => (object) array(
            "succeed" => false,
            "error" => "This service can not be accessed without logging into the system",
        ),
        "loginFail" => (object) array(
            "succeed" => false,
            "error" => "Invalid login, check your credentials",
        ),
        "loginOut" => (object) array(
            "succeed" => true,
            "message" => "Logged out from the system",
        ),
        "Unauthorized" => (object) array(
            "succeed" => false,
            "error" => "You don't have permission for accessing this service",
        )
    ),
    "contacts" => (object) array(
        "errorMsg" => "Error processing the request. The user id is not valid.",
    )
);
