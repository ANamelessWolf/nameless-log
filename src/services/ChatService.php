<?php
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
		$this->userAccess = get_access();
		if ($this->request_data->method == "PUT")
			$this->request_data->body->insert_values->values->{"userId"} = $this->userAccess->userId;
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
		return $urabe->select($sql, array($this->userAccess->userId));
	}
}
