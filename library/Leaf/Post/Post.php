<?php
/**
 * Post.class.php
 * Wrapper for the $_POST array.
 * 
 * @author zburnham
 * @version 0.0.1 
 */
namespace Leaf\Post;

use Leaf\Config\Interfaces\Configable;
use Leaf\Config\Config;
use Leaf\ClassFactory\ClassFactory;

class Post implements Configable
{
    /**
     * Required configuration keys.
     * 
     * @var array 
     */
    protected $requiredConfigKeys = array('post');
    
    /**
     * Optional configuration keys.
     * 
     * @var array
     */
    protected $optionalConfigKeys = array();
    
    /**
     * $_POST array.
     * 
     * @var array 
     */
    protected $post;
    
    /**
     * Configuration array.
     * 
     * @var Config 
     */
    protected $config;
    
    /**
     * ClassFactory instance.
     * 
     * @var ClassFactory 
     */
    protected $cf;
    
    /**
     * Class constructor.
     * 
     * @param array $config 
     */
    public function __construct(Config $config)
    {
        $this->setConfig($config->addOptionalConfigKeys($this->getOptionalConfigKeys()))
             ->setCf($this->getConfig()->getCf())
             ->setPost($this->preProcess($this->getConfig()->get('post')));
    }
    
    //TODO refactor this into an abstract class with a required preProcess, getPost, etc.
    
    /**
     * Default implementation of preProcess.  Just returns the same array
     * it's given.
     * 
     * @param array $post Usually the $_POST array.
     * @return array 
     */
    protected function preProcess(array $post)
    {
        return $post;
    }

    /**
     * Returns given post value.
     * 
     * @param string $key
     * @return mixed 
     */
    public function getPostValue($key)
    {
        $post = $this->getPost();
        if (array_key_exists($key, $post)) {
            return $post[$key];
        } else {
            return FALSE;
        }
    }
    
    /**
     * Convenience method for obtaining configuration info.
     * 
     * @param string $key 
     */
    protected function getChild($key)
    {
        $this->getCf()->get($this->getConfig($key));
    }
    
    /**
     * @return array 
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param array $post
     * @return \Post 
     */
    public function setPost($post)
    {
        $this->post = $post;
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
     * @return \Post 
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }
    
    /**
     * Returns required configuration keys.
     * 
     * @return array 
     */
    public function getRequiredConfigKeys()
    {
        return $this->requiredConfigKeys;
    }
    
    /**
     * @param array $keys
     * @return \Post 
     */
    public function setRequiredConfigKeys(array $keys)
    {
        $this->requiredConfigKeys = $keys;
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
     * @return \Post 
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
     * @return \Leaf\Post\Post 
     */
    public function setCf(ClassFactory $cf)
    {
        $this->cf = $cf;
        return $this;
    }
}