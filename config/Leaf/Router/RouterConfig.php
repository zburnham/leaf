<?php
/**
 * RouterConfig.php
 * EFI-specific router configuration.
 * 
 * @author zburnham
 * @version 0.0.1
 */
return array(
    'className' => 'Leaf\Router\RouterDefault',
    'routes' => array(
        'default' => array(
            'className' => 'Leaf\Route\RouteDefault',
            'basePath' => '',
        )
    )
);