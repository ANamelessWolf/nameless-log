<?php
include_once  "lib/urabe/HasamiWrapper.php";
/**
 * User table controller
 */
class  ChatEntryService  extends  HasamiWrapper
{
	const  TABLE_NAME  =  "chat_entry";
	/**
	 * Chat identifier
	 *
	 * @var int Chat identifier
	 */
	private $chatId;
	/**
	 * Stores the user access
	 * @var UserAccess The user access
	 */
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
		parent::__construct(self::TABLE_NAME, $connector, "entryId");
		$this->set_service_status("POST", ServiceStatus::LOGGED);
		$this->userAccess = get_access();
		if ($this->request_data->in_GET_variables("chatId"))
			$this->chatId = $this->request_data->get_variables["chatId"];
		else
			$this->chatId = null;
		if ($this->request_data->method == "PUT")
			$this->request_data->body->insert_values->values->creation_time = date("Y-m-d H:i:s");
	}

	/**
	 * Validates access for chat entry
	 * Only logged users are allowed
	 * @return boolean True if the validation access succeed
	 */
	protected function validate_access()
	{
		if (is_null($this->chatId))
			throw new Exception("Invalid request");
		if ($this->userAccess->hasAccess && $this->userAccess) {
			$urabe = $this->urabe->get_clone();
			$sql = "SELECT COUNT(*) as total FROM chat_members WHERE chatId = @1 AND userId = @2";
			$sql = $urabe->format_sql_place_holders($sql);
			$total = intval($urabe->select_one($sql, array($this->chatId, $this->userAccess->userId)));
			return $total > 0;
		} else
			return false;
	}
}
