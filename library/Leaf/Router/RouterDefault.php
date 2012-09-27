<?php
/**
 * RouterDefault.php
 * Default router class.
 * 
 * @author zburnham
 * @version 0.0.1 
 */
namespace Leaf\Router;

use Leaf\Config\Config;

class RouterDefault extends RouterAbstract
{
    /**
     * Class constructor.
     * 
     * @param Config $config 
     */
    public function __construct(Config $config)
    {
        $this->setConfig($config);
        $this->setRoutes($this->getConfig()->get('routes'));
    }
    
    /**
     * Implementation of RouterAbstract::route().
     * 
     * In the default case, it simply iterates over the routes it's been configured
     * to have.  Routes return FALSE if they cannot match the query string.
     * The first match wins; when configuring the 'routes' key in the configuration,
     * the array has as its keys a numeric 'priority' that this function sorts
     * on to determine what routes get tried first.  If a route has as its priority
     * 'default', that's pulled out and tacked onto the end of the array after
     * numeric sorting.
     * 
     * @param array $parameters Information to give to individual route.
     * @return array Keys are 'controller', 'action', and 'parameters'.
     * @throws RouterException when no routes matched.
     */
    public function route($parameters = array())
    {
        $routes = $this->getRoutes();
        
        if (isset($routes['default'])) {
            $default = $routes['default'];
            unset($routes['default']);
        }
        ksort($routes, SORT_NUMERIC);
        if (NULL !== $default) {
            array_push($routes, $default);
        }
        
        foreach ($routes as $priority => $route) {
            $c = new Config($route, $this->getCf());
            $r = $this->getCf()->get($c, $parameters);
            $routingInfo = $r->process($parameters);
            if (FALSE !== $routingInfo) {
                return $routingInfo;
            }
        }
        // no routes matched
        throw new RouterException('No routes matched. Query string: ' . $queryString,
                                  500);
    }
}