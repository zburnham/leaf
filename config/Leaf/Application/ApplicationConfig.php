<?php
/**
 * ApplicationConfig.php 
 */

//namespace Leaf;

return array(
    'className' => 'Leaf\Application\Application',
    'classFactoryClass' => 'Leaf\ClassFactory\ClassFactory',
    'Request' => array(
        'className' => 'Leaf\Request\Request',
        'Post' => array(
            'className' => 'Leaf\Post\Post',
            //'accepts' => getAcceptHeader(),
            'post' => array('foo' => 'bar'),
        ),
        'requestMethods' => array(
            'POST',
        ),
        'accepts' => getAcceptHeader(),
    ),
    'Response' => array(
        'className' => 'Leaf\Response\Response',
        // TODO
    ),
    'Controller' => array(
        // TODO
        'className' => 'Leaf\Controller\Controller',
    ),
    'Router' => include($basePath . '/Router/RouterConfig.php'),
    'View' => include($basePath . '/View/ViewConfig.php'),
    'defaultController' => 'index',
    'defaultAction' => 'index',
    'controllers' => array(
        'index' => array(
            'className' => 'Leaf\Controller\Controller',
        ),
    ),
);

function getAcceptHeader()
{
    $headers = apache_request_headers();
    return $headers['Accept'];
}