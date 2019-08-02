<?php

use ___PHPSTORM_HELPERS\object;

include_once  "lib/urabe/HasamiWrapper.php";
/**
 * User table controller
 */
class  ChatService  extends  HasamiWrapper
{
	const  TABLE_NAME  =  "chat";
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
		parent::__construct(self::TABLE_NAME, $connector, "chatId");
		$this->set_service_status("POST", ServiceStatus::LOGGED);
		$this->set_service_task("PUT", "create_chat");
		$this->userAccess = get_access();
		if ($this->request_data->method == "PUT")
			$this->request_data->body->insert_values->values->{"userId"} = $this->userAccess->userId;
	}
	/**
	 * Creates a chat and register the current user as a member
	 * to the new chat
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The server response
	 */
	public function create_chat($data, $urabe)
	{
		$response = $this->get_service("PUT")->default_action($data, $urabe);
		$sql = "SELECT MAX(chatId) userId FROM " . self::TABLE_NAME;
		$chat_id = $urabe->select_one($sql);
		$userId = $this->userAccess->userId;
		$body = (object) array(
			"chatId" => $chat_id,
			"insert_values" => (object) array("columns" => array("chatId", "userId")),
			"values" => (object) array("values" => (object) array("chatId" => $chat_id, "userId" => $userId))
		);
		$service = new ChatMembersService();
		var_dump($service->request_data->body);
		$service->request_data->body = $body;
		var_dump($service->request_data->body);
		die;
		//  
		//  $memberService->get_response();
		return $response;
	}

	/**
	 * Validates access for chat services
	 * Only logged users are allowed
	 * @return boolean True if the validation access succeed
	 */
	protected function validate_access()
	{
		session_start();
		$token = $_SESSION["token"];
		return !is_null($token);
	}
	/**
	 * List all available chats for the current user
	 *
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The urabe response
	 */
	public function u_action_list($data, $urabe)
	{
		if ($this->userAccess->isAdmin)
			$sql = "SELECT * FROM " . self::TABLE_NAME;
		else
			$sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE userId=@1";
		$sql = $urabe->format_sql_place_holders($sql);
		return $urabe->select($sql, array($this->userAccess->userId));
	}
}
