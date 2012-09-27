<?php
/**
 * Application.class.php
 * Top-level class that runs the whole thing.
 * 
 * @author zburnham
 * @version 0.0.1
 */
namespace Leaf\Application;

use Leaf\Config\Interfaces\Configable;

use Leaf\Config\Config;
use Leaf\Request\Request;
use Leaf\Response\Response;
use Leaf\Router\RouterAbstract;
use Leaf\ClassFactory\ClassFactory;
use Leaf\View\View;
use Leaf\Controller\ControllerAbstract;
use Leaf\Log\LogException;

class Application implements Configable
{
    /**
     * Required config keys.
     * 
     * @var array 
     */
    protected $requiredConfigKeys = array(
                                          'Request',
                                          'Response', 
                                          'View',
                                          'Router',
                                          'defaultAction',
                                          'defaultController',
                                         );
    
    /**
     * Optional config keys.
     * 
     * @var array 
     */
//    protected $optionalConfigKeys = array(
//                                          'classFactoryClass' 
//                                              => 'Leaf\ClassFactory\ClassFactory',
//                                          'Request'
//                                              => 'Leaf\Request\Request',
//                                          'View'
//                                              => 'Leaf\View\View', 
//                                          'Router'
//                                              => 'Leaf\Router\Router', 
//                                          'Response'
//                                              => 'Leaf\Response\Response',
//                                          'Controller'
//                                              => 'Leaf\Controller\Controller',
//                                         );
    
    protected $optionalConfigKeys = array();
    
    /**
     * Configuration object.
     * 
     * @var Config 
     */
    protected $config;
    
    /**
     * Request object.
     * 
     * @var Request
     */
    protected $request;
    
    /**
     * Response object.
     * 
     * @var Response 
     */
    protected $response;

    /**
     * Router instance. 
     * 
     * @var Router 
     */
    protected $router;
    
    /**
     * View object.
     * 
     * @var View 
     */
    protected $view;
    
//    /**
//     * The specific kind of controller to create, for example "Main" or "Form".
//     * Defauls to "Controller".
//     * 
//     * @var string 
//     */
//    protected $controllerClass = 'Controller';
    
    /**
     * Full name of error controller class.
     * 
     * @var string 
     */
    protected $errorControllerClass;
    
    /**
     * Error controller instance.
     * 
     * @var \ErrorController_Abstract 
     */
    protected $errorController; // TODO write this class.
    
    /**
     * Controller array.
     * 
     * @var array 
     */
    protected $controllers;
    
    /**
     * Action method name.
     * 
     * @var string 
     */
    protected $action;
    
    /**
     * Error action method name.
     * 
     * @var string 
     */
    protected $errorAction;
    
    /**
     * Any GET parameters that came in with the request.
     * 
     * @var array 
     */
    protected $parameters;
    
    /**
     * Results of routing.
     * 
     * @var array 
     */
    protected $routing;
    
    /**
     * ClassFactory instance.
     * 
     * @var ClassFactory 
     */
    protected $cf;
    
    /**
     * Class constructor.
     * TODO more to come.
     * 
     * @param array $configarray Main configuration array.  We generate a Config object
     * from this so that we don't have to do it in index.php.
     */
    public function __construct(array $configArray)
    {
        $this->setCf(new $configArray['classFactoryClass']);
        $this->setConfig(new Config($configArray,
                                    $this->getCf()));
        
        $this->getConfig()->addOptionalConfigKeys($this->getOptionalConfigKeys());
        
        $cf = $this->getCf();
        $this->setRequest($cf->get($this->getConfig()->get('Request')))
             ->setResponse($cf->get($this->getConfig()->get('Response')))
             ->setRouter($cf->get($this->getConfig()->get('Router')));
        
        $viewParameters = array(
                                'accepts' => $this->getRequest()->getAccepts(),
                               );
        
        $this->setView($cf->get($this->getConfig()->get('View')));
        $this->getView()->setRawAccepts($this->getRequest()->getAccepts());

        $this->getResponse()->setView($this->getView());
        
        $controllers = $this->getConfig()->get('controllers');
        
        $controllerParameters = array (
                                       'Request' => $this->getRequest(),
                                       'View' => $this->getView(),
                                      );
        /**
         * This may not be the best way to go.  The way it happens below, all
         * controllers are initialized on every request, not just the one(s) we
         * need.  TODO. 
         */
        
        foreach ($controllers as $name => $controller)
        {
            $this->addController($name, $this->getCf()->get(new Config($controller, $this->getCf()), $controllerParameters));
        }
    }
    
    /**
     * And, away we go.  Main high-level process of the application.
     *  
     */
    public function run()
    {
        $config = $this->getConfig();
        
        try {
            $routing = $this->route();
        } catch (LogException $le) {
            // do something
        }

        try {
            $controllers = $this->getControllers();
            $action = $this->getAction();
            $actionMethod = lcfirst($action) . 'Action';
            $this->getView()->setAction($this->getAction())
                            ->setController($this->getControllerClass())
                            ->setTemplate($this->getAction());
            $controllers[$this->getControllerClass()]->$actionMethod();
            
        } catch (LogException $le) {
            
//            $parameterArray['exception'] = $le;
//            
//            $this->setErrorController($this->getCf()->get($this->getErrorControllerClass(),
//                                                          $parameterArray));
//            
//            $errorControllerClass = $config['defaultErrorController'];
//            $errorController = $this->getCf()->get($errorControllerClass,
//                                                   $parameterArray);
//            
//            $errorAction = $this->getErrorAction();
//            
//            $this->getErrorController()->init()->$errorAction;
            
            
        }
        
        try {
            $this->getResponse()->setView($this->getView())->send();
        } catch (LogException $le) {
            $this->getResponse()->getView()->setErrorMessage($le->getMessage())
                                           ->setStatusCode($le->getStatusCode());
            $this->getResponse()->send();
        }
    }
    
    /**
     * Defines routing. 
     * 
     * @return array
     */
    protected function route()
    {
        $routing = $this->getRouter()->route(array('requestURI' => $this->getRequest()->getRequestURI()));
        $this->setControllerClass($routing['controller'])
             ->setAction($routing['action'])
             ->setParameters($routing['parameters']);
        return $routing;
    }
    
    /**
     * @return Request 
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return \Application 
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
     * @param Response $response
     * @return \Application 
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

//    /**
//     * @return array 
//     */
//    public function getForms()
//    {
//        return $this->forms;
//    }
//
//    /**
//     * @param array $forms
//     * @return \Application 
//     */
//    public function setForms(array $forms)
//    {
//        $this->forms = $forms;
//        return $this;
//    }

    /**
     * @return View 
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param View $view
     * @return \Application 
     */
    public function setView(View $view)
    {
        $this->view = $view;
        return $this;
    }
    
    /**
     * @return RouterAbstract 
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param RouterAbstract $router
     * @return \Application 
     */
    public function setRouter(RouterAbstract $router)
    {
        $this->router = $router;
        return $this;
    }
    
    /**
     * @return string 
     */
    public function getControllerClass()
    {
        $config = $this->getConfig();
        
        if ('' != $this->controllerClass) {
            return $this->controllerClass;
        } else {
            return $config->get('defaultController');
        }
    }

    /**
     * @param string $controllerClass
     * @return \Application 
     */
    public function setControllerClass($controllerClass = '')
    {
        $this->controllerClass = $controllerClass;
        return $this;
    }
    
    /**
     * @return string 
     */
    public function getErrorControllerClass()
    {
        $config = $this->getConfig();
        if (NULL != $this->errorControllerClass) {
            return $this->errorControllerClass;
        } else if (isset($config['defaultErrorController'])) {
            return $this->setErrorControllerClass($config['defaultErrorController'])
                        ->getErrorControllerClass();
        }
        return $this->setErrorControllerClass('ErrorController')->getErrorControllerClass();
    }

    /**
     * @param string $errorControllerClass
     * @return \Application 
     */
    public function setErrorControllerClass($errorControllerClass)
    {
        $this->errorControllerClass = $errorControllerClass;
        return $this;
    }

    /**
     * @return ErrorController_Abstract 
     */
    public function getErrorController()
    {
        return $this->errorController;
    }

    /**
     * @param ErrorController_Abstract $errorController
     * @return \Application 
     */
    public function setErrorController(ErrorController_Abstract $errorController)
    {
        $this->errorController = $errorController;
        return $this;
    }
    
    /**
     * @return string 
     */
    public function getErrorAction()
    {
        return $this->errorAction;
    }

    /**
     * @param string $errorAction
     * @return \Application 
     */
    public function setErrorAction($errorAction)
    {
        $this->errorAction = $errorAction;
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
     * @return \Application 
     */
    public function setRouting(array $routing)
    {
        $this->routing = $routing;
        return $this;
    }
    
    /**
     * @return array 
     */
    public function getControllers()
    {
        return $this->controllers;
    }

    /**
     * @param array $controllers
     * @return \Application 
     */
    protected function setControllers(array $controllers)
    {
        $this->controllers = $controllers;
        return $this;
    }
    
    /**
     * Adds a controller to the application.
     * 
     * @param $name Alias for controller.
     * @param ControllerAbstract $controller
     * @return \Leaf\Application\Application 
     */
    public function addController($name, ControllerAbstract $controller)
    {
        $controllers = $this->getControllers();
        $controllers[$name] = $controller;
        $this->setControllers($controllers);
        return $this;
    }

    /**
     * @return string 
     */
    public function getAction()
    {
        $config = $this->getConfig();
        if ('' != $this->action) {
            return $this->action;
        } else {
            return $config->get('defaultAction');
        }
    }

    /**
     * @param string $action
     * @return \Application 
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return array 
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return \Application 
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
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
     * @return \Application 
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }
    
    /**
     * @param array $requiredConfigKeys
     * @return \Application 
     */
    public function setRequiredConfigKeys(array $requiredConfigKeys)
    {
        $this->requiredConfigKeys = $requiredConfigKeys;
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
     * Adds to the required config key array.
     * 
     * @param array $keys 
     * @return \Application
     */
    public function addRequiredConfigKeys(array $keys)
    {
        return $this->setRequiredConfigKeys(array_merge($this->getRequiredConfigKeys(), $keys));
    }
    
    /**
     * @param array $keys 
     */
    public function setOptionalConfigKeys(array $keys)
    {
        $this->optionalConfigKeys = $keys;
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
     * @param array $keys 
     * @return \Application
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
     * @return \Leaf\Application\Application 
     */
    public function setCf(ClassFactory $cf)
    {
        $this->cf = $cf;
        return $this;
    }
}