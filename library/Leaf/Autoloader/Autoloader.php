<?php
/**
 * configAutoload.php
 * New configurable autoloader.
 * 
 * @author zburnham
 * @version 0.0.1
 */
namespace Leaf\Autoloader;

class Autoloader
{
    /**
     * Paths to search for class files.
     * 
     * @var array 
     */
    protected $masterPaths =  array(); 
    
    /**
     * Requested class.
     * 
     * @var type 
     */
    protected $classToAutoload;
    
    /**
     * Class constructor.  Note that we're not using the include_path.
     * 
     * @param array $config 
     */
    public function __construct(array $config)
    {
        $this->setMasterPaths($config['masterPaths']);
    }
    
    /**
     * Autoloading function.  Iterates through master paths until it finds what 
     * it wants.  Should be extended to match particular app's needs.
     * 
     * @param string $classToAutoload
     * @return boolean 
     */
    public function autoload($classToAutoload)
    {
        $this->setClassToAutoload($classToAutoload);
        
        $parts = explode('\\', $classToAutoload);
        $className = array_pop($parts);
        $classNamePath = str_replace('_', DIRECTORY_SEPARATOR, $className);
        
        $nameSpace = implode(DIRECTORY_SEPARATOR, $parts);
        
        $path = $nameSpace . '/' . $classNamePath . '.php';
        
        return $this->iterateOverMasterPaths($path);
    }
    
    /**
     * Iterates over established master paths to find the class definition.
     *
     * @param string $path
     * @return boolean 
     */
    protected function iterateOverMasterPaths($path)
    {
        $attemptToAutoload = '';
        foreach ($this->getMasterPaths() as $masterPath => $pathInfo) {
            foreach ($pathInfo['subPaths'] as $subPath) {
                $fullPath = $masterPath . $subPath . $path;
                $attemptToAutoload = $this->getClassToAutoload();
                if (file_exists($fullPath)) {
                    require($fullPath);
                    return TRUE;
                }
            }
        }
        throw new AutoloaderException("Couldn't find the file for class " . $attemptToAutoload,
                                      500);
        //return FALSE;
    }
    
    /**
     * @return array 
     */
    public function getMasterPaths()
    {
        return $this->masterPaths;
    }

    /**
     * @param array $masterPaths
     * @return \configAutoload 
     */
    public function setMasterPaths($masterPaths)
    {
        $this->masterPaths = $masterPaths;
        return $this;
    }
    
    /**
     * @return string 
     */
    public function getClassToAutoload()
    {
        return $this->classToAutoload;
    }

    /**
     * @param string $classToAutoload
     * @return \Leaf\Autoloader\Autoloader 
     */
    public function setClassToAutoload($classToAutoload)
    {
        $this->classToAutoload = $classToAutoload;
        return $this;
    }
}