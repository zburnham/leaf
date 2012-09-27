<?php
/**
 * ControllerAbstract.php
 * Main controller class.
 * 
 * @author zburnham
 * @version 0.0.1 
 */
namespace Leaf\Controller;

use Leaf\Config\Interfaces\Configable;
use Leaf\ClassFactory\ClassFactory;
use Leaf\Config\Config;
use Leaf\Request\Request;
use Leaf\View\View;

class ControllerAbstract implements Configable
{
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
    protected $optionalConfigKeys = array(
                                          'models' => array(),
                                          'forms' => array(),
                                         );
    
    /**
     * Request object.
     * 
     * @var Request
     */
    protected $request;
    
    /**
     * Models used by the controller.
     * 
     * @var array 
     */
    protected $models;
    
    /**
     * Forms used by the controller. 
     * 
     * @var array 
     */
    protected $forms;
    
    /**
     * View model.
     *
     * @var View 
     */
    protected $view;
    
    /**
     * Currently selected action.
     * 
     * @var string 
     */
    protected $action;
    
    /**
     * Response object.
     * 
     * @var Response 
     */
    protected $response;
    
    /**
     * Configuration object.
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
     * Executes the current action.
     * 
     * @return void 
     */
    public function dispatch() 
    {
        $method = strtolower($this->getAction()) . 'Action';
        $this->$method();
        return $this->getView();
    }
    
    /**
     * Adds a model to the controller.
     * 
     * @param Model_Abstract $model 
     */
    public function addModel(Model_Abstract $model)
    {
        $models = $this->getModels();
        $models[] = $model;
        $this->setModels($models);
    }
    
    /**
     * Adds a form to the controller.
     * 
     * @param FormElementCollection $form 
     */
    public function addForm(FormElementCollection $form)
    {
        $forms = $this->getForms();
        $forms[] = $form;
        $this->setForms($forms);
    }
    
    /**
     * @return array 
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * @param array $models
     * @return \Controller_Abstract 
     */
    public function setModels($models)
    {
        $this->models = $models;
        return $this;
    }

    /**
     * @return array 
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * @param array $forms
     * @return \Controller_Abstract 
     */
    public function setForms($forms)
    {
        $this->forms = $forms;
        return $this;
    }

    /**
     * @return View 
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param View $view
     * @return \Controller_Abstract 
     */
    public function setView(View $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return \Controller_Abstract 
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return type 
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return \Controller_Abstract 
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }
    
    /**
     * @return Response 
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param type $response 
     */
    public function setResponse($response)
    {
        $this->response = $response;
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
     * @return \Controller_Abstract 
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }
    
    /**
     * @param array $keys 
     */
    public function setRequiredConfigKeys(array $keys)
    {
        $this->requiredConfigKeys = $keys;
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
     * @param array $keys 
     * @return \Controller_Abstract
     */
    public function addRequiredConfigKeys(array $keys)
    {
        return $this->setRequiredConfigKeys(array_merge($this->getRequiredConfigKeys(), $keys));
    }
    
    /**
     * @param array $keys
     * @return \Controller_Abstract 
     */
    public function setOptionalConfigKeys(array $keys)
    {
        $this->optionalConfigKeys = $keys;
        return $this;;
    }
    
    /**
     * @return array 
     */
    public function getOptionalConfigKeys()
    {
        return $this->optionalConfigKeys;
    }
    
    /**
     * @param array $keys 
     * @return \Controller_Abstract
     */
    public function addOptionalConfigKeys(array $keys)
    {
        return $this->setOptionalConfigKeys(array_merge($this->getOptionalConfigKeys(), $keys));
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
     * @return \Controller_Abstract 
     */
    public function setCf(ClassFactory $cf)
    {
        $this->cf = $cf;
        return $this;
    }
}