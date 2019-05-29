<?php
/**
 * User table controller
 */
class  UserService  extends  HasamiWrapper
{
	const  TABLE_NAME  =  "users";
	/**
	 * Initialize a new instance for the user table controller
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
	 * Validates access to this web service only logged as an
	 * Admin the user can use PUT and DELETE services
	 *
	 * @return bool True if the user has permission
	 */
	protected function validate_access()
	{
		session_start();
		$token = $_SESSION["token"];
		if (is_null($token))
			return false;
		else {
			$sql =	$this->urabe->format_sql_place_holders("SELECT username FROM " . self::TABLE_NAME . " WHERE SHA1(CONCAT(username,pass)) = @1");
			$username = $this->urabe->select_one($sql, array($token));
			$admin = get_system_property("admin")->username;
			return $admin == $username;
		}
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
		$this->set_service_status("POST", ServiceStatus::LOGGED);
		$data->restrict_by_content("POST");
		$sql = "SELECT * FROM " . self::TABLE_NAME;
		$result = $urabe->select($sql);
		$cat = new Caterpillar();
		for ($i = 0; $i < $result->size; $i++)
			$result->result[$i]["pass"] = $cat->decrypt($result->result[$i]["pass"]);
		return $result;
	}
	/**
	 * Gets a user from the database by selecting its id
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The urabe response
	 */
	public function u_action_get_user($data, $urabe)
	{
		session_start();
		$token = $_SESSION["token"];
		$data->restrict_by_content("POST");
		if (is_null($token))
			return get_system_response("users", "NotLogged");
		else {
			$sql =	$this->urabe->format_sql_place_holders("SELECT * FROM " . self::TABLE_NAME . " WHERE SHA1(CONCAT(username,pass)) = @1");
			$queryResult = $this->urabe->select($sql, array($token));
			$username = $queryResult->result[0]["username"];
			$userId = $queryResult->result[0]["userId"];
			$admin = get_system_property("admin")->username;
			if ($admin == $username || $userId == $data->body->userId) {
				$userId = $data->body->userId;
				$sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE userId = $userId";
				$result = $urabe->select($sql);
				$cat = new Caterpillar();
				if ($result->size > 0)
					$result->result[0]["pass"] = $cat->decrypt($result->result[0]["pass"]);
			}
			return $result;
		}
	}
	/**
	 * Registers an users in to the database, only admin can update
	 * any user
	 * @param WebServiceContent $data The web service content
	 * @param Urabe $urabe The database manager
	 * @return UrabeResponse The server response
	 */
	public function update_user($data, $urabe)
	{
		session_start();
		$token = $_SESSION["token"];
		if (is_null($token))
			return get_system_response("users", "NotLogged");
		else {
			$access = get_access($data->body->condition);
			
			$sql =	$this->urabe->format_sql_place_holders("SELECT * FROM " . self::TABLE_NAME . " WHERE SHA1(CONCAT(username,pass)) = @1");
			$queryResult = $this->urabe->select($sql, array($token));
			$username = $queryResult->result[0]["username"];
			$userId = $queryResult->result[0]["userId"];
			$admin = get_system_property("admin")->username;
			if ($admin == $username || $userId == $data->body->condition) {
				$cat = new Caterpillar();
				$values = $data->body->values;
				$values->pass = $cat->encrypt($values->pass);
				$userId = $data->body->condition;
				return $urabe->update(self::TABLE_NAME, $values, "userId=$userId");
			} else {
				KanojoX::$http_error_code = 403;
				$response = get_system_response("users", "Unauthorized");
				throw new Exception($response->error);
			}
		}
	}
	/**
	 * Updates user data
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
		$sql = "SELECT CONCAT(username,pass) as token FROM " . self::TABLE_NAME . " WHERE username = @1 and pass = @2";
		$sql = $urabe->format_sql_place_holders($sql);
		$cat = new Caterpillar();
		$urabe->set_parser(new MysteriousParser());
		$token = $urabe->select_one($sql, array($data->body->username, $cat->encrypt($data->body->pass)));
		if (!is_null($token)) {
			session_start();
			$_SESSION["token"] = sha1($token);
			return (object)array("succeed" => true);
		} else
			return get_system_response("users", "loginFail");
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
