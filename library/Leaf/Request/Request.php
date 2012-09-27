<?php
/**
 * Request.class.php
 * Utility class to handle some of the chores of parsing the HTTP request.
 * 
 * @author zburnham
 * @version 0.0.1 
 */

namespace Leaf\Request;

use Leaf\Config\Interfaces\Configable;
use Leaf\Config\Config;
use Leaf\Post\Post;
use Leaf\ClassFactory\ClassFactory;

class Request implements Configable
{
    /**
     * Configuration object.
     * @var Config
     */
    protected $config;
    
    /**
     * Required configuration keys.
     *  
     * @var array
     */
    protected $requiredConfigKeys = array('requestMethods', 'accepts');
    
    /**
     * Optional configuration keys.
     * 
     * @var array 
     */
    protected $optionalConfigKeys = array('Post' => NULL);
    /**
     * HTTP methods allowed.
     * @var array 
     */
    protected $requestMethods;
    
    /**
     * Parsed accept header contents.
     * @var array 
     */
    protected $accepts;
    
    /**
     * $_POST superglobal array
     * Only set in constructor; constructor handles validation
     * @var array
     */
    protected $post = array();
    
    /**
     * DB connection.
     * @var DBService_Generic
     */
    protected $db;
    
    /**
     * Token object formed on instantiation.
     * @var Token 
     */
    protected $token;
    
    /**
     * Results of routing from Application class.
     * 
     * @var array 
     */
    protected $routing;
    
    /**
     * URI from http request.
     * 
     * @var string 
     */
    protected $requestURI;

    /**
     * ClassFactory instance.
     *
     * @var ClassFactory 
     */
    protected $cf;
    
    /**
     * Class constructor.  
     * Defines class parameters based on an array
     * representing the $_POST superglobal. 
     * 
     * @param Config $config Key->value pairs from the main configuration.
     * @param array $parameters Non-configurable configuration (dynamic values).
     * @throws RequestException
     * @return void 
     */
    public function __construct(Config $config, array $parameters)
    {
        //$config->addOptionalConfigKeys($this->getOptionalConfigKeys())
        $this->setConfig($config->addOptionalConfigKeys($this->getOptionalConfigKeys()))
             ->setRequestMethods($this->getConfig()->get('requestMethods'))
             ->setCf($this->getConfig()->getCf())
             ->setAccepts($this->getConfig()->get('accepts'))
             ->setRequestURI($_SERVER['REQUEST_URI']);
        
        if (in_array($_SERVER['REQUEST_METHOD'], $this->getRequestMethods())) {
            throw new RequestException('Unexpected method.', 400);
        }
        
        $cf = $this->getCf();
        
        $this->setPost($cf->get($this->getConfig()->get('Post')));
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
     * Magic setter function.  Will not allow existing post variable to be overwritten.
     * 
     * @param string $name
     * @param mixed $value
     * @throws LogException 
     */
    public function __set($name, $value)
    {
        if (FALSE !== $this->getPost()->getPostValue($name)) {
            $method = 'get' . $name;
            throw new RequestException('Cannot override existing post variable named ' .
                                       $name .', current value: ' . $this->$method() .
                                       ' proposed new value: ' . $value,
                                       500);
        } else {
            $this->$name = $value;
        }
    }

    /**
     * Magic getter function.  Typically post parameters are accessed this way.
     * 
     * @param string $name
     * @return mixed
     * @throws RequestException 
     */
    public function __get($name) 
    {
        if(FALSE !== $this->getPost()->getPostValue($name)) {
            return $this->getPost()->getPostValue($name);
        } else if (FALSE !== $this->getPost()->getPostValue('id') 
                && FALSE !== $this->getPost()->getPostValue($name . '_' . $this->getPost()->getPostValue('id'))) {
            return $this->getPost()->getPostValue($name . '_' . $this->getPost()->getPostValue('id'));
        } else {
            throw new RequestException('Invalid $_POST variable called: ' . $name,
                                       500);
        }
    }
    
    /**
     * Magic call function.  For convenience.
     * 
     * @param string $name
     * @param array $arguments Unused.
     * @return mixed
     * @throws RequestException 
     */
    public function __call($name, $arguments) 
    {
//        $post = $this->getPost();
//        $name = lcfirst(preg_replace('|get|', '', $name));
//        if(array_key_exists($name, $post)) {
//            return $post[$name];
//        } else if (array_key_exists('id', $post) && !empty($post['id']) && array_key_exists($name . '_' . $post['id'], $post)) {
//            return $post[$name . '_' . $post['id']];
//        } else {
//            throw new RequestException('Invalid $_POST variable called: ' . $name,
//                                       500);
//        }
        $name = lcfirst(preg_replace('|get|', '', $name));
        return $this->$name;
    }
    
    /**
     * Checks the Post object for the existence of a key $name.
     * 
     * @param string $name
     * @return boolean 
     */
    public function exists($name) 
    {
        if (FALSE === $this->getPost()->getPostValue($name)) {
            return FALSE;
        }
        return TRUE;
    }
    
    /**
     * Archives the $_POST for future examination.
     * 
     * @param string $json
     * @throws RequestException 
     */
    public function storeJsonPost($json)
    {
        $query = "INSERT INTO `duke_requests` VALUES ('', ?, NOW())";
        try {
            $numRows = $this->getDb()->query($query, array($json));
        } catch (DBServiceException $dbse) {
            throw new RequestException('Error storing json-encoded $_POST array: ', 
                                   500,
                                   NULL,
                                   $dbse);
        }
    }
    
    /**
     * @return array
     */
    public function getRequestMethods()
    {
        return $this->requestMethods;
    }

    /**
     * @param array $requestMethods
     * @return \Request 
     */
    public function setRequestMethods(array $requestMethods)
    {
        $this->requestMethods = $requestMethods;
        return $this;
    }

    /**
     * @return array 
     */
    public function getAccepts()
    {
        return $this->accepts;
    }

    /**
     * @param array $accepts
     * @return \Request 
     */
    public function setAccepts($accepts)
    {
        $this->accepts = $accepts;
        return $this;
    }
    
    /**
     * @return Post 
     */
    protected function getPost()
    {
        return $this->post;
    }
    
    /**
     * @param array $post
     * @return \Request 
     */
    protected function setPost(Post $post)
    {
        $this->post = $post;
        return $this;
    }
    
    /**
     *
     * @param DBService_Generic $db
     * @return \Request 
     */
    public function setDb(DBService_Generic $db)
    {
        $this->db = $db;
        return $this;
    }
    
    /**
     * @return DBService_Generic 
     */
    protected function getDb()
    {
        return $this->db;
    }
    
    /**
     * @return array 
     */
    public function getRequiredConfigKeys()
    {
        return $this->requiredConfigKeys;
    }

    /**
     * @param type $requiredConfigKeys
     * @return \Request 
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
     * @param Config $config
     * @return \Request 
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }
    
//    /**
//     * @param string $key Key of configuration array to return.
//     * @return array 
//     */
//    public function getConfig($key)
//    {
//        $config = $this->getConfigArray();
//        if (isset($config[$key])) {
//            return $config[$key];
//        }
//        return NULL;
//    }
    
    /**
     * @return Config 
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * @return Token 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param Token $token
     * @return \Request 
     */
    public function setToken(Token $token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return array 
     */
    public function getRouting()
    {
        return $this->routing;
    }

    /**
     * @param array $routing
     * @return \Request 
     */
    public function setRouting(array $routing)
    {
        $this->routing = $routing;
        return $this;
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
     * @return \Request 
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
     * @return \Leaf\Request\Request 
     */
    public function setCf(ClassFactory $cf)
    {
        $this->cf = $cf;
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
     * @return \Leaf\Request\Request 
     */
    public function setRequestURI($requestURI)
    {
        $this->requestURI = $requestURI;
        return $this;
    }
}