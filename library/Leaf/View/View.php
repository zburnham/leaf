<?php
/**
 * View.class.php
 * Class to centralize formation of response.
 * 
 * @author zburnham
 * @version 0.0.1
 * 
 */
namespace Leaf\View;

use Leaf\Config\Interfaces\Configable;
use Leaf\Config\Config;
use Leaf\ClassFactory\ClassFactory;
use Leaf\HttpUtils\HttpUtils;

class View implements Configable
{
    /**
     * Keys required to instantiate object.
     * 
     * @var array
     */
    protected $requiredConfigKeys = array(
                                          'templatePath',
                                          'templateExtension',
                                          'errorTemplateSubPath',
                                         );
    
    /**
     * Data to populate template.
     * 
     * @var array 
     */
    protected $data;
    
    /**
     * String containing filename of template to use minus .phtml extension.
     * 
     * @var string 
     */
    protected $template;
    
    /**
     * Relative template path.
     * 
     * @var string 
     */
    protected $templatePath;
    
    /**
     * Absolute path to the selected template.
     * 
     * @var string 
     */
    protected $fullTemplatePath;
    
    /**
     * Extension for templates.
     * 
     * @var string 
     */
    protected $templateExtension;
    
    /**
     * HTTP status code to return.
     *
     * @var int 
     */
    protected $statusCode = 200;
    
    /**
     * Message corresponding to HTTP status code.
     * 
     * @var string 
     */
    protected $statusCodeMessage = 'OK';
    
    /**
     * Human-readable error message (specific error.)
     *
     * @var string 
     */
    protected $errorMessage;
    
    /**
     * Sub-path in main templates directory for error messages.
     * 
     * @var string 
     */
    protected $errorTemplateSubPath;
    
    /**
     * Raw HTTP headers.
     *
     * @var array 
     */
    protected $headers;
    
    /**
     * Raw Accept: header.
     * 
     * @var string
     */
    protected $rawAccepts;
    
    /**
     * Array of processed Accept headers.
     * 
     * @var array 
     */
    protected $processedAccepts;
    /**
     * Body of page to be built.
     * 
     * @var type 
     */
    protected $body;
    
    /**
     * Configuration array.
     * 
     * @var array 
     */
    protected $config;
    
    /**
     * ClassFactory instance.
     * 
     * @var ClassFactory
     */
    protected $cf;
    
    /**
     * Controller selected from routing.
     * 
     * @var string 
     */
    protected $controller;
    
    /**
     * Action selected from routing.
     * 
     * @var string 
     */
    protected $action;
    
    /**
     * Class constructor.
     * 
     * @param Confg $config Configuration object.
     */
    public function __construct(Config $config, $parameters = array())
    {   
        $this->setConfig($config)
             ->setCf($this->getConfig()->getCf())
             ->setTemplatePath($this->getConfig()->get('templatePath'))
             ->setTemplateExtension($this->getConfig()->get('templateExtension'))
             ->setController($this->getConfig()->get('defaultController'))
             ->setAction($this->getConfig()->get('defaultAction'))
             ->setTemplate($this->getConfig()->get('defaultTemplate'))
             ->setErrorTemplateSubPath($this->getConfig()->get('errorTemplateSubPath'));
    }
    
    /**
     * Magic function to return entry in $data for populating the template.
     *
     * @param mixed $property
     * @return mixed 
     * @throws ViewException if attempt is made to use non-existent data
     */
    public function __get($property)
    {
        if (isset($this->data[$property])) {
            if ('statusCode' == $property) {
                return $this->getStatusCode();
            } else if ('statusMessage' == $property) {
                return $this->getStatusCodeMessage();
            }
            return $this->data[$property];
        } else {
            throw new ViewException('Request for non-existent data: ' . $property);
        }
    }
    
    /**
     * @param Config $config 
     */
    public function setConfig(Config $config) 
    {
        $this->config = $config;
        return $this;
    }
    
    /**
     * Returns specific config key.
     * 
     * @return Config 
     */
    public function getConfig() 
    {
        return $this->config;
    }
    
    /**
     * @return array 
     */
    protected function getConfigArray()
    {
        return $this->config;
    }
    
    /**
     * Pre-digests 'Accept:' header for use in response.
     * Populates 'accepts' property with an array of arrays.
     * 
     * Array keys: 
     * 'type': First part of the MIME type (application, text, etc)
     * 'subtype': Second part of the MIME type (html, json, xml, etc)
     * 'quality': Priority to return various types with.  The Response object
     * iterates over these arrays and prioritizes by this value.  Normally the 
     * default value (if nothing is specified) is 1, but we assign values here 
     * based on the order in which the MIME types are listed.  For example, 
     * 'application/json,text/html,text/javascript;q=0.8' would see 
     * 'application/json' get assigned a quality of 2, 'text/html' a quality
     *  of 1, and text/javascript a quality of 0.8. 
     * 
     * @return void
     * 
     */
    protected function processAcceptHeader()
    {
        $accept = $this->getRawAccepts();
        $acceptEntries = split(',', $accept);
        
        $size = sizeof($acceptEntries);
        $acceptArray = array();
        foreach ($acceptEntries as $acceptEntry) {
            $entry = array();
            $bits = split ('/', $acceptEntry);
            if ('*' == $bits[0]) {
                $entry['type'] = 'default';
            } else {
                $entry['type'] = $bits[0];
            }
            $priorityBits = split(';q=', $bits[1]);
            if ('*' == $priorityBits[0]) {
                $entry['subtype'] = 'default';
            } else {
                $entry['subtype'] = $priorityBits[0];
            }
            if (!empty($priorityBits[1]))
            {
                $entry['quality'] = (float)$priorityBits[1];
            } else {
                $entry['quality'] = (float)$size;
                $size--;
            }
            $acceptArray[] = $entry;
        }
        usort($acceptArray, array($this, 'sortAcceptHeaders'));
        
        $this->setProcessedAccepts($acceptArray);
    }
    
    /**
     * Custom sorting function for processAcceptHeader().
     * 
     * @param array $a
     * @param array $b
     * @return int 
     */
    protected function sortAcceptHeaders($a, $b)
    {
        $aQuality = $a['quality'];
        $bQuality = $b['quality'];
        
        if ($aQuality == $bQuality) {
            return 0;
        }
        return ($aQuality > $bQuality) ? -1 : 1;
    }
    
    /**
     * Builds content by interpolating relevant entries from $this->data into
     * selected templates.
     * 
     * @throws ViewException
     * 
     */
    public function build()
    {
        $validTemplate = FALSE;
        $errorTemplate = FALSE;
        $error = FALSE;
        $type = '';
        $subtype = '';
        $this->processAcceptHeader();
        foreach ($this->getProcessedAccepts() as $entry) {
            $subtype = $entry['subtype'];
            if (200 != $this->getStatusCode())
            {
                $error = TRUE;
                $path = $this->buildErrorTemplatePath($subtype);
                if (file_exists($this->buildErrorTemplatePath($subtype))) {
                                    $this->setFullTemplatePath($this->buildErrorTemplatePath($subtype));
                                    $errorTemplate = TRUE;
                }
            } else if (file_exists($this->buildFullTemplatePath($subtype))) {
                                        $this->setFullTemplatePath($this->buildFullTemplatePath($subtype));
                                        $validTemplate = TRUE;
            }
            if ($validTemplate || $errorTemplate) {
                $type = $entry['type'];
                break;
            }
        }
        
        if ($error) {
            if (!$errorTemplate) {
                throw new ViewException($this->buildErrorTemplateNotFoundExceptionMessage());
            }
        } else if (!$validTemplate) {
            throw new ViewException($this->buildNotFoundExceptionMessage(), 500);
        }

        $this->setStatusCodeMessage(HttpUtils::getStatusCodeMessage($this->getStatusCode()));
        
        $this->prependHeader('Content-Type: '. $type . '/' . $subtype . '; charset=UTF-8');
        $this->prependHeader('HTTP/1.1 ' . $this->getStatusCode() . ' ' . $this->getStatusCodeMessage());
    
        ob_start();
        try {
            include ($this->getFullTemplatePath());
        } catch (ViewException $ve) {
            throw $ve;
        }
        $this->setBody(ob_get_clean());
    }
    
    protected function buildErrorTemplateNotFoundExceptionMessage()
    {
        return $this->getStatusCode() . 
               '.error' . 
               $this->getTemplateExtension() . 
               ' not found.';
    }
    
    /**
     * Convenience function to build "not found" exception message.
     * 
     * @return string 
     */
    protected function buildNotFoundExceptionMessage()
    {
        return $this->getTemplate() . 
               $this->getTemplateExtension() . 
               ' not found in any context. Controller = ' . 
               $this->getController() . 
               ' Action = ' . 
               $this->getAction();
    }
    
    /**
     * Convenience function to build full template path.
     * 
     * @param string $subtype
     * @return string 
     */
    protected function buildFullTemplatePath($subtype)
    {
        return $this->getTemplatePath() . '/' .
               $this->getController() . '/' .
               $this->getAction() . '/' . 
               $subtype . '/' .
               $this->getTemplate() . 
               $this->getTemplateExtension();
    }
    
    /**
     * Convenience function to build full error template path.
     * 
     * @param string $subtype
     * @return string 
     */
    protected function buildErrorTemplatePath($subtype)
    {
        return $this->getTemplatePath() . '/' .
               $this->getErrorTemplateSubPath() . '/' .
               $subtype . '/' .
               $this->getStatusCode() .  
               '.error' . 
               $this->getTemplateExtension();
    }
    
    /**
     * Returns headers.
     */
    public function sendHeaders()
    {
        foreach ($this->getHeaders() as $header) {
            header($header);
        }
    }

    /**
     * Adds an HTTP header.
     * 
     * @param string $header 
     */
    public function addHeader($header)
    {
        $headers = $this->getHeaders();
        $headers[] = $header;
        $this->setHeaders($headers);
    }
    
    /**
     * Prepends a header onto the headers array.  Used to ensure that a 
     * header will be sent before the others.
     * 
     * @param string $header 
     */
    public function prependHeader($header)
    {
        $headers = $this->getHeaders();
        if (empty($headers)) {
            $headers = array();
        }
        array_unshift($headers, $header);
        $this->setHeaders($headers);
    }
    
    /**
     * @return array 
     */
    protected function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return \View 
     */
    protected function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string 
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template Template file without the .phtml extension
     * @return \View 
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }
    
    /**
     * @return string 
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * @param string $templatePath
     * @return \Leaf\View\View 
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
        return $this;
    }
        
    /**
     * @return string 
     */
    public function getFullTemplatePath()
    {
        return $this->fullTemplatePath;
    }

    /**
     * @param string $fullTemplatePath
     * @return \View 
     */
    public function setFullTemplatePath($fullTemplatePath)
    {
        $this->fullTemplatePath = $fullTemplatePath;
        return $this;
    }
    
    /**
     * @return string 
     */
    public function getTemplateExtension()
    {
        return $this->templateExtension;
    }

    /**
     * @param string $templateExtension
     * @return \Leaf\View\View 
     */
    public function setTemplateExtension($templateExtension)
    {
        $this->templateExtension = $templateExtension;
        return $this;
    }

    /**
     * @return int 
     */
    protected function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     * @return \View 
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @return string 
     */
    protected function getStatusCodeMessage()
    {
        return $this->statusCodeMessage;
    }

    /**
     * @param string $statusCodeMessage
     * @return \View 
     */
    protected function setStatusCodeMessage($statusCodeMessage)
    {
        $this->statusCodeMessage = $statusCodeMessage;
        return $this;
    }
    
    /**
     * @return string 
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     * @return \View 
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }
    
    /**
     * @return string 
     */
    public function getErrorTemplateSubPath()
    {
        return $this->errorTemplateSubPath;
    }

    /**
     * @param string $errorTemplateSubPath
     * @return \Leaf\View\View 
     */
    public function setErrorTemplateSubPath($errorTemplateSubPath)
    {
        $this->errorTemplateSubPath = $errorTemplateSubPath;
        return $this;
    }

    /**
     * @return array 
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return \View 
     */
    protected function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }
    
    /**
     * @return string 
     */
    public function getRawAccepts()
    {
        return $this->rawAccepts;
    }

    /**
     * @param string $rawAccepts 
     */
    public function setRawAccepts($rawAccepts)
    {
        $this->rawAccepts = $rawAccepts;
    }

    /**
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return \View 
     */
    protected function setBody($body)
    {
        $this->body = $body;
        return $this;
    }
    
    /**
     * @return array 
     */
    public function getProcessedAccepts()
    {
        return $this->processedAccepts;
    }

    /**
     * @param array $processedAccepts
     * @return \View 
     */
    public function setProcessedAccepts($processedAccepts)
    {
        $this->processedAccepts = $processedAccepts;
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
     * @return \View 
     */
    public function setCf(ClassFactory $cf)
    {
        $this->cf = $cf;
        return $this;
    }

    /**
     * Returns configuration keys necessary to instantiate object.
     * 
     * @return array 
     */
    public function getRequiredConfigKeys()
    {
        return $this->requiredConfigKeys;
    }
    
    /**
     * @param array $keys
     * @return \Leaf\View\View 
     */
    public function setRequiredConfigKeys(array $keys)
    {
        $this->requiredConfigKeys = $keys;
        return $this;
    }
    
    /**
     * @return string 
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     * @return \Leaf\View\View 
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return \Leaf\View\View 
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }
    
    /**
     * @param array $keys 
     */
    public function addRequiredConfigKeys(array $keys) 
    {
        $this->setRequiredConfigKeys(array_merge($this->getRequiredConfigKeys(), $keys));
    }
    
    //protected function addRequiredConfigKeys()
    
    public function getOptionalConfigKeys() {}
    
    public function setOptionalConfigKeys(array $keys) {}
    
    public function addOptionalConfigKeys(array $keys) 
    {
        $this->setOptionalConfigKeys(array_merge($this->getOptionalConfigKeys(), $keys));
    }
}