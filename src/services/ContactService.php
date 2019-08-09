<?php
include_once "lib/urabe/HasamiWrapper.php";
/**
 * Contact service controller. This service is used to list public
 * contacts to be added to a chat.
 */
class ContactService extends HasamiWrapper implements ICostumeErrorMessage
{
	const TABLE_NAME = "contacts";
	/**
	 * Stores the user access
	 * @var UserAccess The user access
	 */
	private $userAccess;
	/**
	 * Initialize a new instance for the contact service controller
	 */
	public function __construct()
	{
		$properties = require dirname(__FILE__) . '/../config/properties.php';
		$connector = new MYSQLKanojoX();
		$conn = $properties->connection;
		$connector->init($conn);
		parent::__construct(self::TABLE_NAME, $connector, "userId");
		$this->set_service_status("GET", ServiceStatus::AVAILABLE);
		$this->set_service_status("POST", ServiceStatus::BLOCKED);
		$this->set_service_task("GET", "get_public_contacts");
		$this->userAccess = get_access();
	}
	/**
	 * Creates a costume error message generated when a bad request is generated
	 * 
	 * @param Exception $e The bad request exception, it can be concatenated for a full error description
	 * @return string The error message
	 */
	public function get_error_msg($e)
	{
		//Uncomment the next line, to see the full message error
		//return $e->getMessage();
		return get_system_response("contacts", "errorMsg");
	}
	/**
	 * Delete and PUT services requires
	 * administration access
	 */
	public function validate_access()
	{
		return $this->userAccess->isAdmin;
	}
	/**
	 * Gets the public contacts from the chat
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The urabe response
	 */
	public function get_public_contacts($data, $urabe)
	{
		$sql = "SELECT users.userId, users.username FROM " . self::TABLE_NAME . " LEFT JOIN users ON contacts.userId = users.userId";
		$service = new UserService();
		$urabe = $service->get_urabe();
		return $urabe->select($sql);
	}
}