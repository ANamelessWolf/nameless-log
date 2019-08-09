<?php
include_once  "lib/urabe/HasamiWrapper.php";
/**
 * Chat entry controller. Manage the message entries from a
 * chat
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
	 * Member identifier
	 *
	 * @var int The member identifier
	 */
	private $memberId;
	/**
	 * Chat encryption key
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
		$this->set_service_task("DELETE", "delete_entry");
		$this->getChatKey();
		$this->getMemberId();
	}
	/**
	 * Validates access for chat entry
	 * Only logged users are allowed
	 * @return boolean True if the validation access succeed
	 */
	protected function validate_access()
	{
		if (!$this->userAccess->isAdmin) {
			if (is_null($this->chatKey) || is_null($this->memberId))
				throw new Exception(get_system_response("entries", "notAMemberMsg"));
			if (is_null($this->chatId))
				throw new Exception("Invalid request");
			$urabe = $this->urabe->get_clone();
			$sql = "SELECT COUNT(*) as total FROM chat_members WHERE chatId = @1 AND userId = @2";
			$sql = $urabe->format_sql_place_holders($sql);
			$total = intval($urabe->select_one($sql, array($this->chatId, $this->userAccess->userId)));
			return $total > 0;
		} else
			return true;
	}
	/**
	 * Adds a new entry to the log
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The urabe response
	 */
	public function delete_entry($data, $urabe)
	{
		$lftJoin = "`chat_members` cm ON e.memberId = cm.memberId";
		$condition = "e.entryId = @1 AND cm.memberId = @2";
		$sql = "SELECT COUNT(*) AS Total FROM `chat_entry` e LEFT JOIN $lftJoin WHERE $condition";
		$sql = $urabe->format_sql_place_holders($sql);
		$entryId = $data->body->condition;
		$total = intval($urabe->select_one($sql, array($entryId, $this->memberId)));
		if ($total > 0 || $this->userAccess->isAdmin)
			return	$urabe->delete_by_field(self::TABLE_NAME, "entryId", $entryId);
		else
			throw new Exception(get_system_response("entries", "protectedEntry"));
	}
	/**
	 * Adds a new entry to the log
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The urabe response
	 */
	public function add_entry($data, $urabe)
	{
		if (is_null($this->chatKey) || is_null($this->memberId))
			throw new Exception(get_system_response("entries", "notAMemberMsg"));
		$cat = new Caterpillar($this->chatKey);
		$data->body->insert_values->values->memberId = $this->memberId;
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
		$sql = $this->get_selection_query($urabe);
		return $this->get_query_result($urabe, $sql);
	}
	/**
	 * Select the last ten entries for the current chat
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The urabe response
	 */
	public function u_action_last_ten($data, $urabe)
	{
		$sql = $this->get_selection_query($urabe, 10);
		return $this->get_query_result($urabe, $sql);
	}
	/**
	 * Gets the query for selecting the current chat entries.
	 * When entries are limited via the $limit variable it returns the
	 * last $limit entries
	 *
	 * @param Urabe $urabe The database manager
	 * @param int $limit The number of rows to be retrieved
	 * @return string The selection query
	 */
	private function get_selection_query($urabe, $limit = NULL)
	{
		$fields = "u.username, e.entryId, e.entry, e.creation_time, cm.memberId";
		$lftJoinMembers = "`chat_members` cm ON e.memberId = cm.memberId";
		$lftJoinUsers = "`users` u ON u.userId = cm.userId";
		$orderBy = "e.creation_time, e.entryId";
		if (is_null($limit))
			$sql = "SELECT $fields FROM `chat_entry` e LEFT JOIN $lftJoinMembers LEFT JOIN $lftJoinUsers WHERE chatId = @1 ORDER BY $orderBy";
		else {
			$sql = "SELECT $fields FROM `chat_entry` e LEFT JOIN $lftJoinMembers LEFT JOIN $lftJoinUsers WHERE chatId = @1 ORDER BY $orderBy DESC LIMIT $limit";
			$orderBy = str_replace("e.", "tab.", $orderBy);
			$sql = "SELECT * FROM ($sql) tab ORDER BY $orderBy";
		}
		$sql = $urabe->format_sql_place_holders($sql);
		return $sql;
	}
	/**
	 * Gets the query result for the selection query
	 *
	 * @param Urabe $urabe The database manager
	 * @param string $sql The selection query
	 * @return UrabeResponse The urabe response
	 */
	private function get_query_result($urabe, $sql)
	{
		$cat = new Caterpillar($this->chatKey);
		$query_result = $urabe->select($sql, array($this->chatId));
		$result = $query_result->result;
		for ($i = 0; $i < count($result); $i++)
			$result[$i]["entry"] = $cat->decrypt($result[$i]["entry"]);
		$query_result->result = $result;
		return $query_result;
	}
	/**
	 * Gets the member id and store in the
	 * memberId property
	 */
	private function getMemberId()
	{
		$urabe = $this->urabe->get_clone();
		$sql = "SELECT memberId FROM chat_members cm WHERE cm.chatId = @1 AND cm.userId = @2";
		$sql = $urabe->format_sql_place_holders($sql);
		$this->memberId = $urabe->select_one($sql, array($this->chatId, $this->userAccess->userId));
	}
	/**
	 * Gets the chat key
	 */
	private function getChatKey()
	{
		$urabe = $this->urabe->get_clone();
		$sql = "SELECT %s chatKey FROM %s cm LEFT JOIN %s c ON %s WHERE %s";
		$fields = "SHA1(CONCAT(c.chatId, c.userId))";
		$joinCondition = "c.chatId = cm.chatId";
		if ($this->userAccess->isAdmin)
			$condition = "c.chatId = @1";
		else
			$condition = "c.chatId = @1 AND cm.userId = @2";
		$sql = sprintf($sql, $fields, "chat_members", "chat", $joinCondition, $condition);
		$sql = $urabe->format_sql_place_holders($sql);
		if ($this->userAccess->isAdmin)
			$this->chatKey = $urabe->select_one($sql, array($this->chatId));
		else
			$this->chatKey = $urabe->select_one($sql, array($this->chatId, $this->userAccess->userId));
	}
}