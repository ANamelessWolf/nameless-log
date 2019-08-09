<?php
/**
 * This interface adds the functionality to a HasamiWrapper web service
 * class to implement a costume message when a bad request is generated
 */
interface ICostumeErrorMessage
{
    /**
	 * Gets the error message when a bad request is generated
	 * @param Exception $e The bad request exception, it can be concatenated for a full error description
	 * @return string The error message
	 */
	public function get_error_msg($e);
}
