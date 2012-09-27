<?php
/**
 * Configable.php
 * Specifies that objects must have certain properties to use the Config
 * class.
 * 
 * @author zburnham
 * @version 0.0.1
 */
namespace Leaf\Config\Interfaces;

use Leaf\Config\Config;
use Leaf\ClassFactory\ClassFactory;

//TODO LEFTOFF Have to see if we can figure out a way to get the ClassFactory
// class to be configurable.

interface Configable
{
    /**
     * Set configuration object.
     * 
     * @param Config $config 
     */
    public function setConfig(Config $config);
    
    /**
     * return Config 
     */
    public function getConfig();
    
    /**
     * Check for required keys. 
     */
    public function getRequiredConfigKeys();
    
    /**
     * Set array of required keys. 
     * 
     * @param array $keys
     */
    public function setRequiredConfigKeys(array $keys);
    
    /**
     * Add config keys to the required array. 
     */
    public function addRequiredConfigKeys(array $keys);
    
    /**
     * Get possible optional config keys. 
     */
    public function getOptionalConfigKeys();
    
    /**
     * Set optional config keys.
     * 
     * @param array $keys
     */
    public function setOptionalConfigKeys(array $keys);
    
    /**
     * Add optional config keys to existing array. 
     */
    public function addOptionalConfigKeys(array $keys);
    
    /**
     * Set the ClassFactory instance.
     * 
     * @param ClassFactory $cf 
     */
    public function setCf(ClassFactory $cf);
    
    /**
     * Get the ClassFactory instance.
     */
    public function getCf();
}