<?php
/**
 * RouteDefault.php
 * Default route to be given to a Router instance.
 * 
 * @author zburnham
 * @version 0.0.1
 * 
 */
namespace Leaf\Route;

use Leaf\Config\Config;

class RouteDefault extends RouteAbstract
{
    /**
     * Base path for the project.  We strip this off to find the controller
     * and action.
     * @var string 
     */
    protected $basePath;
    
    protected $requestURI;
    
    /**
     * Class constructor.
     * 
     * @param Config $config
     * @param type $parameters Must include 'requestURI' key.
     */
    public function __construct(Config $config, $parameters)
    {
        $this->setConfig($config);
        if (isset($parameters['requestURI'])) {
            $this->setRequestURI($parameters['requestURI']);
        }
        
        $this->addRequiredConfigKeys(array('basePath'));
        $this->setBasePath($this->getConfig()->get('basePath'));
    }
    
    /**
     * Implementation of RouteAbstract::process().
     * 
     * @return array 
     */
    public function process()
    {
        if (NULL === $this->getRequestURI()) { // No query string means we can't match anything.
            return FALSE;
        }
        $trimmedURI = trim(str_replace($this->getBasePath(), '', $this->getRequestURI()), '/');
        
        $parts = explode('/', $trimmedURI);
        
        $controller = array_shift($parts);
        $action = array_shift($parts);
        
        $parameters = array();
        
        foreach ($parts as $part)
        {
            $parameters[array_shift($part)] = array_shift($part);
        }
        
        return array(
                     'controller' => $controller,
                     'action'     => $action,
                     'parameters' => $parameters,
                    );
    }
    
    /**
     * @return string 
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param string $basePath
     * @return \Leaf\Route\RouteDefault 
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
        return $this;
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
     * @return \Leaf\Route\RouteDefault 
     */
    public function setRequestURI($requestURI)
    {
        $this->requestURI = $requestURI;
        return $this;
    }
}