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
     * @return The string encrypted
     */
    public function encrypt($string)
    {
        $block = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_ECB);
        $pad = $block - (strlen($string) % $block);
        $string .= str_repeat(chr($pad), $pad);
        $string = mcrypt_encrypt(MCRYPT_DES, $this->secure_key, $string, MCRYPT_MODE_ECB, $this->secure_iv);
        $string = base64_encode($string);
        return $string;
    }
    /**
     * Decrypts a string
     *
     * @param string $string The string to be decrypted
     * @return The string decrypted
     */
    public function decrypt($string)
    {
        $string = base64_decode($string);
        $str = mcrypt_decrypt(MCRYPT_DES, $this->secure_key, $string, MCRYPT_MODE_ECB, $this->secure_iv);
        $block = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_ECB);
        $pad = ord($str[ ($len = strlen($str)) - 1]);
        return substr($str, 0, strlen($str) - $pad);
    }
}
?>