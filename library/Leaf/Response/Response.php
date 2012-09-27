<?php
/**
 * Response.class.php
 * Class to handle some tasks regarding what is returned as a result
 * of an HTTP request. 
 * 
 * @author zburham
 * @version 0.0.1
 */
namespace Leaf\Response;

use Leaf\Config\Config;
use Leaf\Config\Interfaces\Configable;
use Leaf\ClassFactory\ClassFactory;
use Leaf\View\View;

class Response implements Configable
{
    /**
     * Configuration object.
     * 
     * @var Config 
     */
    protected $config;
    
    /**
     * Required configuration keys.
     * 
     * @var array 
     */
    protected $requiredConfigKeys = array();
    
    /**
     * Optional configuration keys.
     * 
     * @var array 
     */
    protected $optionalConfigKeys = array();
    
    /**
     * View object to handle building pages.
     *
     * @var View 
     */
    protected $view;
    
    /**
     * ClassFactory instance.
     * 
     * @var ClassFactory 
     */
    protected $cf;
    
    /**
     * Class constructor.
     * 
     * @param Config $config Configuration object.
     */
    public function __construct(Config $config)
    {
        $this->setConfig($config->addOptionalConfigKeys($this->getOptionalConfigKeys()))
             ->setCf($this->getConfig()->getCf());
    }
    
    /**
     * Sends the response.  Gets headers and body from the View object and 
     * sends/echos as appropriate.
     * 
     * @return void
     */
    public function send()
    {   
        try {
            $this->getView()->build();
        } catch (LogException $le) {
            throw $le;
        }
        $page = '';
        $page .= $this->getView()->sendHeaders();
        $page .= $this->getView()->getBody();
        echo $page;
    }
    
    /**
     * For when something.. bad.. happens.
     * 
     * @param LogException $le 
     */
    public function spewErrorAndExit(LogException $le)
    {
        $this->setStatusCode($le->getStatusCode())
             ->setErrorMessage(get_class($le->getPrevious()) .
                                         ': ' . $le->getMessage() .
                                         "<br><pre>" . print_r($le->getTrace(), true) .
                                         "</pre>")
             ->send();
        exit();
    }
//
//    /**
//     * @return string 
//     */
//    public function getBody()
//    {
//        return $this->body;
//    }
//    
//    /**
//     * @param string $body
//     * @return \Response 
//     */
//    public function setBody($body)
//    {
//        $this->body = $body;
//        return $this;
//    }

    /**
     * @return \View 
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param View $view
     * @return \Response 
     */
    public function setView($view)
    {
        $this->view = $view;
        return $this;
    }
    
    /**
     * @return Config 
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param Config $config 
     * @return \Response
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return array 
     */
    public function getRequiredConfigKeys()
    {
        return $this->requiredConfigKeys;
    }

    /**
     * @param array $requiredConfigKeys 
     * @return \Response
     */
    public function setRequiredConfigKeys(array $requiredConfigKeys)
    {
        $this->requiredConfigKeys = $requiredConfigKeys;
        return $this;
    }
    
    /**
     * @param array $keys 
     */
    public function addRequiredConfigKeys(array $keys)
    {
        $this->setRequiredConfigKeys(array_merge($this->getRequiredConfigKeys(), $keys));
    }

    /**
     * @return array 
     */
    public function getOptionalConfigKeys()
    {
        return $this->optionalConfigKeys;
    }

    /**
     * @param array $optionalConfigKeys
     * @return \Response 
     */
    public function setOptionalConfigKeys(array $optionalConfigKeys)
    {
        $this->optionalConfigKeys = $optionalConfigKeys;
        return $this;
    }

    /**
     * @param array $keys 
     */
    public function addOptionalConfigKeys(array $keys)
    {
        $this->setOptionalConfigKeys(array_merge($this->getOptionalConfigKeys(), $keys));
    }
    
    /**
     * @return ClassFactory 
     */
    public function getCf()
    {
        return $this->cf;
    }

    /**
     * @param ClassFactory $cf
     * @return \Leaf\Response\Response 
     */
    public function setCf(ClassFactory $cf)
    {
        $this->cf = $cf;
        return $this;
    }
}
