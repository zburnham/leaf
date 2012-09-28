<?php
/**
 * HttpUtils.class.php
 * Class to provide static methods for use by View objects.
 * 
 * @author zburnham
 * @version 0.0.1
 * 
 */
namespace Leaf\HttpUtils;

class HttpUtils
{
    const DEFAULT_400_MESSAGE = "You have sent a malformed or invalid request.";
    const DEFAULT_401_MESSAGE = "You are not authorized for this service.";
    const DEFAULT_404_MESSAGE = "The requested URL was not found.";
    const DEFAULT_500_MESSAGE = "The server encountered an error processing your request.";
    const DEFAULT_501_MESSAGE = "The requested method is not implemented.";
    const DEFAULT_UNK_MESSAGE = "An unknown error ocurred.";
    
    /**
     * Library of HTTP status codes.
     * 
     * @param string $statusCode
     * @return string 
     */
    public static function getStatusCodeMessage($statusCode){

    $codes = Array(  
            100 => 'Continue',  
            101 => 'Switching Protocols',  
            200 => 'OK',  
            201 => 'Created',  
            202 => 'Accepted',  
            203 => 'Non-Authoritative Information',  
            204 => 'No Content',  
            205 => 'Reset Content',  
            206 => 'Partial Content',  
            300 => 'Multiple Choices',  
            301 => 'Moved Permanently',  
            302 => 'Found',  
            303 => 'See Other',  
            304 => 'Not Modified',  
            305 => 'Use Proxy',  
            306 => '(Unused)',  
            307 => 'Temporary Redirect',  
            400 => 'Bad Request',  
            401 => 'Unauthorized',  
            402 => 'Payment Required',  
            403 => 'Forbidden',  
            404 => 'Not Found',  
            405 => 'Method Not Allowed',  
            406 => 'Not Acceptable',  
            407 => 'Proxy Authentication Required',  
            408 => 'Request Timeout',  
            409 => 'Conflict',  
            410 => 'Gone',  
            411 => 'Length Required',  
            412 => 'Precondition Failed',  
            413 => 'Request Entity Too Large',  
            414 => 'Request-URI Too Long',  
            415 => 'Unsupported Media Type',  
            416 => 'Requested Range Not Satisfiable',  
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot.',
            420 => 'Enhance your calm.',
            500 => 'Internal Server Error',  
            501 => 'Not Implemented',  
            502 => 'Bad Gateway',  
            503 => 'Service Unavailable',  
            504 => 'Gateway Timeout',  
            505 => 'HTTP Version Not Supported'  
        );  
    return (isset($codes[$statusCode])) ? $codes[$statusCode] : '';
    }
}