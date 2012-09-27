<?php
/**
 * AutoloadFactory.php
 * Class to initialize and load all autoloaders needed for the application.
 * 
 * @author zburnham
 * @version 0.0.1
 */
namespace Leaf\Autoloader;

class AutoloaderFactory
{
    protected $config;
    
    protected $loaders;
    
    public function __construct(array $config)
    {
        $this->setConfig($config);
        $myConfig = $this->getConfig(); // Doing this here in case someone wants
                                        // to do something to the config in 
                                        // the setConfig method in a child class.
        $this->includePath($myConfig['Autoloader']['baseClass']['path']);
        $this->setLoaders($config['Autoloader']['autoloaders']);
        $this->registerAutoloaders();
    }
    
    public function registerAutoloaders()
    {
        foreach($this->getLoaders() as $autoloader) {
            if (!class_exists($autoloader['className'])) {
                $this->includePath($autoloader['path']);
            }
            $a = new $autoloader['className']($autoloader);
            try {
                spl_autoload_register(array($a, 'autoload'), TRUE);
            } catch (Exception $e) {
                echo 'Autoloader registration error: ' . $e->getMessage();
                die();
            }
        }
    }
    
    protected function includePath($path)
    {
        return require($path);
    }
    
    /**
     * @return array 
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     * @return \Leaf\Autoload\AutoloadFactory 
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }
        
    /**
     * @return array 
     */
    public function getLoaders()
    {
        return $this->loaders;
    }

    /**
     * @param array $loaders
     * @return \Leaf\Autoload\AutoloadFactory 
     */
    public function setLoaders($loaders)
    {
        $this->loaders = $loaders;
        return $this;
    }
}