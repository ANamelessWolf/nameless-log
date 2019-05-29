<?php
include_once  "lib/urabe/HasamiWrapper.php";
/**
 * User table controller
 */
class  ChatService  extends  HasamiWrapper
{
	const  TABLE_NAME  =  "chat";
	/**
	 * Initialize a new instance for the user table controller
	 */
	public  function  __construct()
	{
		$properties = require dirname(__FILE__) . '/../config/properties.php';
		$connector  =  new  MYSQLKanojoX();
		$conn = $properties->connection;
		$connector->init($conn);
		parent::__construct(self::TABLE_NAME, $connector, "chatId");
		$this->set_service_status("POST", ServiceStatus::LOGGED);
		var_dump(get_access(1));
	}
    /**
     * Validates access for chat services
     * @return boolean True if the validation access succeed
     */
	protected function validate_access()
	{
		session_start();
		$token = $_SESSION["token"];
		return !is_null($token);
	}
}
