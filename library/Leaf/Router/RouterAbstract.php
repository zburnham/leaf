<?php
/**
 * RouterAbstract.php
 * Base Router class.
 * 
 * @author zburnham
 * @version 0.0.1
 * 
 */
namespace Leaf\Router;

use Leaf\Config\Config;
use Leaf\Config\Interfaces\Configable;
use Leaf\ClassFactory\ClassFactory; // TODO Can we make this configurable?

abstract class RouterAbstract implements Configable
{
    /**
     * Configuration object.
     * 
     * @var Config 
     */
    protected $config;
    
    /**
     * Request URI given to us by the Application object from the Request object.
     * 
     * @var string 
     */
    protected $requestURI;
    
    /**
     * Defined routes.
     * 
     * @var array Array of Route_Abstract objects. 
     */
    protected $routes;
    
    /**
     * Required configuration keys.
     * 
     * @var type 
     */
    protected $requiredConfigKeys;
    
    /**
     * Optional configuration keys.
     * 
     * @var array 
     */
    protected $optionalConfigKeys;
    
    /**
     * ClassFactory instance.
     * 
     * @var ClassFactory 
     */
    protected $cf;
    
    /**
     * Called by Application to determine what controller and what action to initiate. 
     * 
     * @param $parameters Optional parameters to hand to route object.
     */
    abstract public function route($parameters = array());

    /**
     * @return array 
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param array $routes
     * @return \Leaf\Router\Router_Abstract 
     */
    protected function setRoutes(array $routes)
    {
        $this->routes = $routes;
        return $this;
    }
    
    /**
     * Adds to existing routes.
     * 
     * @param array $routes 
     */
    public function addRoutes(array $routes)
    {
        $this->setRoutes(array_merge($this->getRoutes(), $routes));
    }
    
    /**
     * @return string 
     */
    public function getRequestURI()
    {
        return $this->requestURI;
    }

    /**
     * @param string $requestURI
     * @return \Leaf\Router\Router_Abstract 
     */
    public function setRequestURI($requestURI)
    {
        $this->requestURI = $requestURI;
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
     * @return \Leaf\Router\Router_Abstract 
     */
    public function setRequiredConfigKeys(array $requiredConfigKeys)
    {
        $this->requiredConfigKeys = $requiredConfigKeys;
        return $this;
    }
    
    /**
     * @param array $requiredConfigKeys 
     */
    public function addRequiredConfigKeys(array $requiredConfigKeys)
    {
        $this->setRequiredConfigKeys(array_merge($this->getRequiredConfigKeys(), $requiredConfigKeys));
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
     * @return \Leaf\Router\RouterAbstract 
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
     * @return Config 
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param Config $config
     * @return \Leaf\Router\Router_Abstract 
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
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
     * @return \Leaf\Router\Router 
     */
    public function setCf(ClassFactory $cf)
    {
        $this->cf = $cf;
        return $this;
    }
}