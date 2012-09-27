<?php
/**
 * LogException.class.php
 * Extends Exception and adds logging. 
 * 
 * @author zburnham
 * @version 0.0.1
 */
namespace Leaf\Log;

use \Exception;

class LogException extends Exception
{
    /**
     * HTTP status code.
     * @var string 
     */
    protected $statusCode;
    
    /**
     * Class constructor.
     * 
     * @param string $message Message to be logged.
     * @param string statusCode HTTP status code.
     * @param string $code
     * @param Exception $previous 
     */
    public function __construct($message, $statusCode, $code = 0, Exception $previous = NULL)
    {
        if (NULL !== $previous) {
            $message .= ' ' . get_class($previous) . ': ' . $previous->getMessage();
        }
        parent::__construct($message, $code, $previous);
        
        $this->setStatusCode($statusCode);
        
        $backtrace = debug_backtrace();
        
        error_log($message . ' File: ' . $backtrace[0]['file'] . ' Line: ' . $backtrace[0]['line']);
        
        if (NULL !== $previous) {
            error_log('Previous exception: ' . $previous->getMessage());
        }
    }
    
    /**
     * @param string $message 
     * @return \LogException
     */
    protected function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }
    
    /**
     * @return string 
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param string $statusCode
     * @return \LogException 
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }
}
