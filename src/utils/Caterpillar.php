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
const NAMELESS_IV = "tYzX78SZ";
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
     * Initialize a new instance of the caterpillar class
     * @param string $key The encryption key
     * @param string $iv The encryption vector.
     */
    public function __construct($key = NAMELESS_KEY, $iv = NAMELESS_IV)
    {
        $this->secure_key = $key;
        $this->secure_iv = $iv;
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
        for ($i = 0; $i < mb_strlen($string, 'ASCII'); $i++)
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
        $first_key = base64_decode($this->secure_key);
        $second_key = base64_decode($this->secure_iv);

        $method = "aes-256-cbc";
        $iv_length = openssl_cipher_iv_length($method);
        $iv = $iv_length;

        $first_encrypted = openssl_encrypt($string, $method, $first_key, OPENSSL_RAW_DATA, $iv);
        $second_encrypted = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);

        $output = base64_encode($iv . $second_encrypted . $first_encrypted);
        return $output;
    }
    /**
     * Decrypts a string
     *
     * @param string $string The string to be decrypted
     * @return string The decrypted string 
     */
    public function decrypt($string)
    {
        $first_key = base64_decode($this->secure_key);
        $second_key = base64_decode($this->secure_iv);
        $mix = base64_decode($string);

        $method = "aes-256-cbc";
        $iv_length = openssl_cipher_iv_length($method);

        $iv = substr($mix, 0, $iv_length);
        $second_encrypted = substr($mix, $iv_length, 64);
        $first_encrypted = substr($mix, $iv_length + 64);

        $data = openssl_decrypt($first_encrypted, $method, $first_key, OPENSSL_RAW_DATA, $iv);
        $second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);

        if (hash_equals($second_encrypted, $second_encrypted_new))
            return $data;
        else
            return false;
    }
}
