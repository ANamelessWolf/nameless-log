<?php

/**
 * Define an exception that is thrown when an installation when an error ocurred
 */
class InstallationException extends Exception
{
    /**
     * Initialize a new instance of the Installation Exception
     * 
     * @param string $responseName The response name
     * @param string $responseData Extra information used to format the response
     * @param integer $code The exception code
     * @param Exception $previous The previous thrown exception
     */
    public function __construct($responseName, $responseData = null, $code = 0, Exception $previous = null)
    {
        KanojoX::$http_error_code = 400;
        $msg = get_system_response("installer", $responseName);
        if (!is_null($responseData))
            $msg = sprintf($responseName, $responseData);
        parent::__construct($msg, $code, $previous);
    }
}
