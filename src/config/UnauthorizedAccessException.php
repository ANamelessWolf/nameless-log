<?php
/**
 * Define an exception that is thrown when the system denies access 
 * to a service
 */
class UnauthorizedAccessException extends Exception
{
    /**
     * Initialize a new instance of the Unauthorized Access Exception
     *
     * @param string $service The service name
     * @param string $responseName The response name
     * @param integer $code The exception code
     * @param Exception $previous The previous thrown exception
     */
    public function __construct($service, $responseName, $code = 0, Exception $previous = null)
    {
        KanojoX::$http_error_code = 403;
        $response = get_system_response($service, $responseName);
        parent::__construct($response->error, $code, $previous);
    }
}