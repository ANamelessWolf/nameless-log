<?php
include_once  "lib/urabe/HasamiWrapper.php";

/**
 * User table controller
 */
class  ChatMembersService  extends  HasamiWrapper
{
	const  TABLE_NAME  =  "chat_members";

	/**
	 * Stores the available chats for the current user
	 *
	 * @var array The available chats
	 */
	public $chats;
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
		parent::__construct(self::TABLE_NAME, $connector, "memberId");
		$this->set_service_status("GET", ServiceStatus::LOGGED);
		$this->set_service_task("GET", "test");
		$this->userAccess = get_access();
		if ($this->userAccess->isAdmin)
			$this->chats = get_chats();
		else
			$this->chats = get_chats($this->userAccess->userId);
	}
	/**
	 * Validates access for chat member services
	 * Only logged users are allowed
	 * @return boolean True if the validation access succeed
	 */
	protected function validate_access()
	{
		$chatId = $this->request_data->body->chatId;
		$ids = array_map(function ($item) {
			return $item->chatId;
		}, $this->chats);
		$val = in_array($chatId, $ids);
		return $val;
	}

	/**
	 * List all available members for the current chat
	 *
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The urabe response
	 */
	public function test($data, $urabe)
	{
		$fields = "cm.memberId, cm.userId, u.username, cm.chatId";
		$sql = "SELECT $fields FROM chat_members cm LEFT JOIN users u ON cm.userId = u.userId WHERE chatId = @1";
		$sql = $urabe->format_sql_place_holders($sql);
		$chatId = $data->body->chatId;
		$urabe->set_parser(new MysteriousParser());
		return $urabe->select($sql, array($chatId));
	}
}
