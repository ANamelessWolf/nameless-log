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
	 * Chat encrypter key
	 */
	private $chatKey;
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
		$this->set_service_task("PUT", "add_entry");
		$this->getChatKey();
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
	/**
	 * Adds a new entry to the log
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The urabe response
	 */
	public function add_entry($data, $urabe)
	{
		$cat = new Caterpillar();
		$data->body->insert_values->values->creation_time = date("Y-m-d H:i:s");
		$data->body->insert_values->values->entry = $cat->encrypt($data->body->insert_values->values->entry);
		return $urabe->insert(self::TABLE_NAME, $data->body->insert_values->values);
	}
	/**
	 * Select all messages from the chat
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The urabe response
	 */
	public function u_action_list($data, $urabe)
	{
		$cat = new Caterpillar();
		$query_result =  $urabe->select_all(self::TABLE_NAME);
		$result = $query_result->result;
		for ($i = 0; $i < count($result); $i++)
			//$result[$i]["entry"] = 
			var_dump($cat->decrypt($result[$i]["entry"]));
		$query_result->result = $result;
		return $query_result;
	}

	/**
	 * Gets the chat key
	 */
	private function getChatKey()
	{
		$urabe = $this->urabe->get_clone();
		$fields = "SHA1( CONCAT(c.chatId, c.userId)) chatKey";
		$leftJoin = "JOIN chat c ON c.chatId = cm.chatId";
		$sql = "SELECT $fields FROM chat_members cm LEFT $leftJoin WHERE c.chatId = @1 AND cm.userId = @2";
		$sql = $urabe->format_sql_place_holders($sql);
		$this->chatKey = $urabe->select_one($sql, array($this->chatId, $this->userAccess->userId));
	}
}
