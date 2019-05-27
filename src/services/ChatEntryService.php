<?php
include_once  "lib/urabe/HasamiWrapper.php";
/**
 * User table controller
 */
class  ChatEntryService  extends  HasamiWrapper
{
	const  TABLE_NAME  =  "chat_entry";
	/**
	 * Initialize a new instance for the user table controller
	 */
	public  function  __construct()
	{
		$properties = require dirname(__FILE__) . '/../config/properties.php';
		$connector  =  new  MYSQLKanojoX();
		$conn = $properties->connection;
		$connector->init($conn);
		parent::__construct(self::TABLE_NAME, $connector, "entryId");
	}
}
?>