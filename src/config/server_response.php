<?php

return (object) array(
    "installer" =>
    (object) array(
        "MissingSQLFolder" => "The Installation can not find the SQL folder. Check if the path is correct",
        "MissingSQLScript" => "The Installation can not open the file '%s' check if the file is valid and the system is able to read it.",
        "QueryError" => "Error executing the query '%s'",
        "ErrorCreatingAdmin" => "Error creating the administrator user",
        "InstallationSucceed" => (object) array(
            "succeed" => true,
            "message" => "Installation succeed",
            "admin" => (object) array("username" => "", "pass" => "")
        ),
    ),
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
        ),
        "tokenResponse" => (object) array(
            "succeed" => true,
            "token" => $_SESSION["token"]
        )
    ),
    "contacts" => (object) array(
        "errorMsg" => "Error processing the request. The user id is not valid.",
    )
);
