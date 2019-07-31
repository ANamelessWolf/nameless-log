<?php
/**
 * This class is used to stores user access
 */
class UserAccess
{
    /**
     * Stores the name for the current admin
     *
     * @var string The administrator name
     */
    public $admin;
    /**
     * True if the user has admin privileges
     *
     * @var bool Returns true if has admin privileges
     */
    public $isAdmin;
    /**
     * True if the user has access to the current context
     *
     * @var bool Returns true if access in the current context
     */
    public $hasAccess;
    /**
     * Stores the name for logged user
     *
     * @var string The logged username
     */
    public $username;
    /**
     * Stores the userId for logged user
     *
     * @var string The logged userId
     */
    public $userId;
    /**
     * Initialize a new instance for the class User access
     *
     * @param string $loggedUsername The logged user name
     * @param string $userId The logged user id
     * @param int $userId The user id to check the current user access
     */
    public function __construct($loggedUsername, $loggedUserId, $userId = null)
    {
        $this->username = $loggedUsername;
        $this->userId = $loggedUserId;
        $this->admin = get_system_property("admin")->username;
        $this->isAdmin = $this->admin == $this->username;
        if (is_null($userId))
            $this->hasAccess = $this->isAdmin;
        else
            $this->hasAccess = $this->isAdmin || $loggedUserId == $userId;
    }
}
