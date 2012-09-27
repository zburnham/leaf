<?php
/**
 * RouteAbstract.php
 * Base class for routes.
 * 
 * @author zburnham
 * @version 0.0.1
 */
namespace Leaf\Route;

use Leaf\Config\Config;
use Leaf\Config\Interfaces\Configable;
use Leaf\ClassFactory\ClassFactory;

abstract class RouteAbstract implements Configable
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
    protected $optionalConfigKeys;
    
    /**
     * ClassFactory instance.
     * 
     * @var ClassFactory 
     */
    protected $cf;
    
    /**
     * Main function of Route objects.  Returns an array with keys 'controller',
     * 'action', and 'parameters'.
     * 
     * @return array 
     */
    abstract protected function process();
    
    /**
     * @return Config 
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param Config $config
     * @return \Leaf\Route\RouteAbstract 
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
     * @return \Leaf\Route\RouteAbstract 
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
     * @return \Leaf\Route\RouteAbstract 
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
     * @return \Leaf\Route\RouteAbstract 
     */
    public function setCf(ClassFactory $cf)
    {
        $this->cf = $cf;
        return $this;
    }
}