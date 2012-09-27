<?php
/**
 * Config.class.php
 * Class to store configuration information.
 * 
 * @author zburnham
 * @version 0.0.1
 */
namespace Leaf\Config;

use Leaf\Config\ConfigException;

use Leaf\ClassFactory\ClassFactory;

class Config
{
    /**
     * Array containing configuration information.
     * 
     * @var array
     */
    protected $configArray;
    
    /**
     * Class factory object to pass down.
     * 
     * @var type 
     */
    protected $cf;
    
    /**
     * Class constructor.
     * 
     * @param array $configArray 
     */
    public function __construct(array $configArray, ClassFactory $cf, $parameters = array())
    {
        if (!array_key_exists('className', $configArray)) {
            throw new ConfigException('Required className not present:' . var_dump($configArray), 500);
        }
        $this->setConfigArray($configArray);
        $this->setCf($cf);
    }
    
    /**
     * Creates a new Config object 
     * @param string $key
     * @param array $parameters Parameters to pass along to the new Config class
     * if necessary
     * @return Config 
     * @throws ConfigException when an undefined configuration key is requested.
     */
    public function get($key, $parameters = array()) 
    {
        $configArray = $this->getConfigArray();

        if (isset($configArray[$key])) {
            if(is_array($configArray[$key]) && isset($configArray[$key]['className'])) {
                return new Config($configArray[$key], $this->getCf(), $parameters);
            }
            return $configArray[$key];
        } else {
            throw new ConfigException('Invalid key requested: ' . $key,
                                      500);
        }
        
//        if (NULL !== $configArray[$key]) {
//            return $configArray[$key];
//        } else {
//            throw new ConfigException('Invalid key requested: ' . $key,
//                                      500);
//        }
////        if (!array_key_exists($key, $configArray)) {
////            throw new ConfigException('Configuration key ' . $key . ' not found',
////                                      500);
////        }
////        if (is_array($configArray[$key])) {
////            return new Config($configArray[$key]);
////        } else {
////            return $configArray[$key];
////        }
////        return new Config($configArray[$key]);
//        
//        if (is_array($configArray[$key])) {
//            return new Config($configArray[$key], $parameters);
//        } 
    }
    
    /**
     * Creates new Config object for new class being created (see ClassFactory)
     * 
     * @param array $configArray
     * @return \Config 
     */
    public function getChildConfigObject()
    {
        return new Config($this->getConfigArray());
    }
    
    /**
     * @return array 
     */
    public function getConfigArray()
    {
        return $this->configArray;
    }

    /**
     * @param array $configArray
     * @return \Config 
     */
    public function setConfigArray(array $configArray)
    {
        $this->configArray = $configArray;
        return $this;
    }
    
    /**
     * Adds default values for optional config keys.  Defaults do not overwrite
     * specified values in the configuration array.
     * 
     * @param array $optionalConfigKeys 
     */
    public function addOptionalConfigKeys(array $optionalConfigKeys)
    {
        $this->setConfigArray(array_merge($optionalConfigKeys, $this->getConfigArray()));
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
     * @return \Leaf\Config\Config 
     */
    public function setCf(ClassFactory $cf)
    {
        $this->cf = $cf;
        return $this;
    }
}