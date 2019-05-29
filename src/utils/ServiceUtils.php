<?php
/**
 * Gets a system web service response
 *
 * @param string $service The service name
 * @param string $responseName The response name
 * @return array The system response
 */
function get_system_response($service, $responseName)
{
    $response =  require dirname(__FILE__) . '/../config/server_response.php';
    return $response->{$service}->{$responseName};
}
/**
 * Gets a system property
 *
 * @param string $propertyName The property name
 * @return array The system response
 */
function get_system_property($propertyName)
{
    $response =  require dirname(__FILE__) . '/../config/properties.php';
    return $response->{$propertyName};
}
