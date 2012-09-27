<?php
/**
 * Controller.php
 * Concrete implementation of Controller_Abstract.
 * 
 * @author zburnham
 * @version 0.0.1
 */
namespace Leaf\Controller;

use Leaf\Config\Config;
use Leaf\ClassFactory\ClassFactory;

class Controller extends ControllerAbstract
{
    /**
     * Class constructor.
     * 
     * @param array $config Configuration array.
     * @param array $parameters In this case, 'request' and 'view'.
     */
    public function __construct(Config $config, array $parameters) 
    {
        $this->setRequest($parameters['Request'])
             ->setView($parameters['View'])
             ->setConfig($config->addOptionalConfigKeys($this->getOptionalConfigKeys()));
        
        $modelParameters = array ('Request' => $this->getRequest());
        
        foreach($this->getConfig()->get('models') as $model) {
            $this->addModel($this->getCf()->get($model, $modelParameters));
        }
        
        foreach($this->getConfig()->get('forms') as $form) {
            $this->addForm($this->getCf()->get($form));
        }
    }
    
    /**
     * DEBUG
     * TODO 
     */
    public function indexAction()
    {
        //echo "indexAction";
    }
    
    public function fooAction()
    {
        
    }
    
}