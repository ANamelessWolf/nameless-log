<?php
include_once  "Entry.php";
/**
 * This class is used to stores an Entry
 */
class EntryParser extends MysteriousParser
{
    /**
     * The caterpillar encryption utility
     *
     * @var Caterpillar Encryption tool
     */
    private $cat;
    /**
     * Defines a basic parser for an entry
     *
     * @param string $chatKey The chat parser id
     */
    function __construct($chatKey)
    {
        $this->cat = new Caterpillar($chatKey);
        $table_fields = array(
            "username" => new StringFieldDefinition(0, "username", PARSE_AS_STRING, 25),
            "entryId" => new NumericFieldDefinition(1, "entryId", PARSE_AS_INT, 0, 0),
            "entry" => new StringFieldDefinition(2, "entry", PARSE_AS_STRING, 0),
            "creation_time" => new DateFieldDefinition(3, "creation_time", PARSE_AS_DATE, "m-d-y_h:i:s"),
            "memberId" => new NumericFieldDefinition(4, "memberId", PARSE_AS_INT, 0, 0)
        );
        parent::__construct($table_fields);
        $this->parse_method = "entry_parser";
    }
    /**
     * Parse the data using the field definition, if a column map is set the result keys are mapped
     * to the given value
     *
     * @param MysteriousParser $mys_parser The mysterious parser that are extracting the data
     * @param array $result The collection of rows where the parsed rows are stored
     * @param array $row The selected row picked from the fetch assoc process
     * @return void
     */
    protected function entry_parser($mys_parser, &$result, $row)
    {
        $obj = new Entry();
        $properties = array_keys(get_object_vars($obj));
        foreach ($row as $column_name => $column_value) {
            $key = $mys_parser->get_column_name($column_name);
            if ($key == "entry")
                $value = $this->cat->decrypt($mys_parser->table_definition[$column_name]->get_value($column_value));
            else
                $value = $mys_parser->table_definition[$column_name]->get_value($column_value);
            if (in_array($key, $properties))
                $obj->{$key} = $value;
        }
        array_push($result, $obj);
    }
}