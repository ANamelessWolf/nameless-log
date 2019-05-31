<?php
//SELECT users.* FROM `contacts` LEFT JOIN users ON contacts.userId = users.userId 
include_once  "lib/urabe/HasamiWrapper.php";
/**
 * User table controller
 */
class  ContactService  extends  HasamiWrapper
{
	const  TABLE_NAME  =  "contacts";

	private $userAccess;
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
        $this->set_service_status("GET", ServiceStatus::AVAILABLE);
		$this->set_service_status("POST", ServiceStatus::BLOCKED);
		$this->userAccess = get_access();
    }
}
?>