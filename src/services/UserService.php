<?php

/**
 * User service controller. This service manage user login to the application
 * and user management.
 */
class  UserService  extends  HasamiWrapper
{
	const  TABLE_NAME  =  "users";
	/**
	 * Initialize a new instance for the user service controller
	 */
	public  function  __construct()
	{
		$connector  =  new  MYSQLKanojoX();
		$conn = get_system_property("connection");
		$connector->init($conn);
		parent::__construct(self::TABLE_NAME, $connector, "userId");
		$this->set_service_task("PUT", "register_user");
		$this->set_service_task("POST", "update_user");
	}
	/**
	 * Validates access to this web service only a logged user
	 * declared admin can called the PUT and DELETE verbose
	 *
	 * @return bool True if the user has permission
	 */
	protected function validate_access()
	{
		return has_admin_privileges();
	}
	/**
	 * List all available users, this method is restricted to be used only
	 * with POST verbose and with admin credentials
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The urabe response
	 */
	public function u_action_list($data, $urabe)
	{
		$data->restrict_by_content("POST");
		if (has_admin_privileges()) {
			$sql = "SELECT * FROM " . self::TABLE_NAME;
			$result = $urabe->select($sql);
			$cat = new Caterpillar();
			for ($i = 0; $i < $result->size; $i++)
				$result->result[$i]["pass"] = $cat->decrypt($result->result[$i]["pass"]);
			return $result;
		} else
			throw new UnauthorizedAccessException("users", "Unauthorized");
	}
	/**
	 * Gets a user data via its user id. This method is restricted
	 * to the Admin
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The urabe response
	 */
	public function u_action_get_user($data, $urabe)
	{
		$data->restrict_by_content("POST");
		$access = get_access($data->body->condition);
		if ($access->isAdmin) {
			$userId = $data->body->userId;
			$sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE userId = $userId";
			$result = $urabe->select($sql);
			$cat = new Caterpillar();
			if ($result->size > 0)
				$result->result[0]["pass"] = $cat->decrypt($result->result[0]["pass"]);
			return $result;
		} else
			throw new UnauthorizedAccessException("users", "Unauthorized");
	}
	/**
	 * Updates user data. The Admin can update any user and
	 * the logged user can only update his data.
	 * Currently only password is allowed to be updated.
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The server response
	 */
	public function update_user($data, $urabe)
	{
		$access = get_access($data->body->condition);
		if ($access->hasAccess) {
			$cat = new Caterpillar();
			$values = $data->body->values;
			$values->pass = $cat->encrypt($values->pass);
			$userId = $data->body->condition;
			return $urabe->update(self::TABLE_NAME, $values, "userId=$userId");
		} else
			throw new UnauthorizedAccessException("users", "Unauthorized");
	}
	/**
	 * Registers a new users in to the database via PUT verbose. Only Admin
	 * can access this request.
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The server response
	 */
	public function register_user($data, $urabe)
	{
		$cat = new Caterpillar();
		$pass = $data->body->insert_values->values->pass;
		$username = $data->body->insert_values->values->username;
		$data->body->insert_values->values->pass = $cat->encrypt($pass);
		$sql = $urabe->format_sql_place_holders("SELECT * FROM " . self::TABLE_NAME . " WHERE username = @1");
		$result = $urabe->select_one($sql, array($username));
		if (is_null($result)) {
			$values = $this->format_values($data->body->insert_values->values);
			return $urabe->insert(self::TABLE_NAME, $values);
		} else
			return get_system_response("users", "usernameExists");
	}
	/**
	 * Login in to the system
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The urabe response
	 */
	public function u_action_login($data, $urabe)
	{
		$data->restrict_by_content("POST");
		$sql = "SELECT CONCAT(username,pass) as token FROM " . self::TABLE_NAME . " WHERE username = @1 AND pass = @2";
		$sql = $urabe->format_sql_place_holders($sql);
		$cat = new Caterpillar();
		$urabe->set_parser(new MysteriousParser());
		$password = $cat->encrypt($data->body->pass);
		$token = $urabe->select_one($sql, array($data->body->username, $password));
		if (!is_null($token)) {
			session_start();
			$_SESSION["token"] = sha1($token);
			$response = get_system_response("users", "tokenResponse");
			$response->token = $_SESSION["token"];
		} else
			$response = get_system_response("users", "loginFail");
		return $response;
	}
	/**
	 * Logout from the system
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The urabe response
	 */
	public function u_action_logout($data, $urabe)
	{
		session_start();
		$_SESSION["token"] = null;
		return get_system_response("users", "loginOut");
	}
	/**
	 * Checks the current session token
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The urabe response
	 */
	public function u_action_check_session($data, $urabe)
	{
		$data->restrict_by_content("POST");
		session_start();
		return	$_SESSION["token"];
	}
}
