<?php
include_once  "lib/urabe/HasamiWrapper.php";
include_once  "utils/Caterpillar.php";
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
		$properties = require dirname(__FILE__) . '/../config/properties.php';
		$connector  =  new  MYSQLKanojoX();
		$conn = $properties->connection;
		$connector->init($conn);
		parent::__construct(self::TABLE_NAME, $connector, "userId");
		$this->set_service_task("PUT", "register_user");
	}
	    /**
     * Defines the default PUT action, by default execute an insertion query with the given data passed
     * in the body properties insert_values
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @return UrabeResponse The server response
     */
	public function register_user($data, $urabe){
		$cat = new Caterpillar();
		$password=$data->body->insert_values->values->password;
		$username =$data->body->insert_values->values->username;
		$data->body->insert_values->values->password = $cat->encrypt($password);
		$result = $urabe->select_one("SELECT * FROM nameless_log.".self::TABLE_NAME." WHERE username = @1", array($username));
		if(is_null($result)){
			$values = $this->format_values($data->body->insert_values->values);
			return $urabe->insert("nameless_log.".self::TABLE_NAME, $values);
		}
		else
		return $result;
	}

	/**
	 * Inicia sesión
	 *
	 * @return void
	 */
	public function u_action_login(){
	
	}
}
?>