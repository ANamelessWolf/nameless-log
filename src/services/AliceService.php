<?php

/**
 * Encryption table test controller
 */
class  AliceService  extends  HasamiWrapper
{
    const  TABLE_NAME  =  "alice";
    /**
     * Initialize a new instance for the user table controller
     */
    public  function  __construct()
    {
        $connector  =  new  MYSQLKanojoX();
        $conn = get_system_property("connection");
        $connector->init($conn);
        parent::__construct(self::TABLE_NAME, $connector, "id");
        $this->set_service_task("PUT", "addEntry");
        $this->set_service_status("GET", ServiceStatus::AVAILABLE);
        $this->set_service_task("GET", "listEntries");
    }

    /**
     * Adds an entry to the table
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @return UrabeResponse The server response
     */
    public function listEntries($data, $urabe)
    {
        $cat = new Caterpillar();
        $values = $urabe->select_all(self::TABLE_NAME);
        for ($i = 0; $i < sizeof($values->result); $i++) 
            $values->result[$i]["content"] = $cat->decrypt($values->result[$i]["content"]);
        return $values;
    }

    /**
     * Adds an entry to the table
     * @param WebServiceContent $data The web service content
     * @param Urabe $urabe The database manager
     * @return UrabeResponse The server response
     */
    public function addEntry($data, $urabe)
    {
        $cat = new Caterpillar();
        $val = $data->body->insert_values->values->val;
        $data->body->insert_values->values->content = $cat->encrypt($val);
        $values = $this->format_values($data->body->insert_values->values);
        return $urabe->insert(self::TABLE_NAME, $values);
    }
}
