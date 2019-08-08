<?php
/**
 * @var string NAMELESS_KEY
 * Shh this is the default crypto key
 */
const NAMELESS_KEY = "N4m3LeSs";
/**
 * @var string NAMELESS_IV
 * Shh this is the default vector
 */
const NAMELESS_IV = "MTYttcmDrDl6IQwn";
/**
 * This class encrypts and decrypts a string using the DES
 * algorithm and ths ECB MODE
 * @version 1.0.0
 * @api Alice
 * @author A nameless wolf <anamelessdeath@gmail.com>
 * @copyright 2015-2020 Nameless Studios
 */
class Caterpillar
{
    /**
     * @var string Encryption Key
     */
    protected $secure_key;
    /**
     * @var string Encryption Vector
     */
    protected $secure_iv;
    /**
     * AES Authenticated Encryption type
     *
     * @var string cypher algorithm
     */
    protected $cypher;
    /**
     * Encryption options
     *
     * @var int cypher options
     */
    protected $options;
    /**
     * Initialize a new instance of the caterpillar class
     * @param string $key The encryption key
     * @param string $iv The encryption vector.
     */
    public function __construct($key = NAMELESS_KEY)
    {
        $this->secure_key = $key;
        $this->secure_iv = NAMELESS_IV;
        $this->cypher = "AES-128-CBC";
        $this->options = OPENSSL_RAW_DATA;
    }
    /**
     * Gets the bytes from a string, using ASCII encoding
     *
     * @param string $string The string to get its bytes.
     * @return void
     */
    function get_bytes($string)
    {
        $byte = array();
        for ($i = 0; $i < mb_strlen($string, 'UTF8'); $i++)
            array_push($byte, ord($string[$i]));
        return $byte;
    }
    /**
     * Encrypts a string
     *
     * @param string $string The string to be encrypted
     * @return string The encrypted string
     */
    public function encrypt($string)
    {
        $value = openssl_encrypt($string, $this->cypher, $this->secure_key, $this->options, $this->secure_iv);
        return utf8_encode($value);
    }
    /**
     * Decrypts a string
     *
     * @param string $string The string to be decrypted
     * @return string The decrypted string 
     */
    public function decrypt($string)
    {
        $string = utf8_decode($string);
        return openssl_decrypt($string, $this->cypher, $this->secure_key, $this->options, $this->secure_iv);
    }

    /**
     * Generates a random password
     * @return string The random password encrypted and not encrypted
     */
    public function random_password()
    {
        $characters = '!#$&+-_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; strlen($randomString) < 8; $i++) {
            $char = $characters[rand(0, $charactersLength - 1)];
            if (strpos($randomString, $char) == false)
                $randomString .= $char;
        }
        return (object)array("encrypted" => $this->encrypt($randomString), "password" => $randomString);
    }
}
