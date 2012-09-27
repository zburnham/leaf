<?php
/**
 * ViewConfig.php
 * Default View configuration.
 * 
 * @author zburnham
 * @version 0.0.1
 */
namespace Leaf;

return array(
    'className' => 'Leaf\View\View',
    'templatePath' => 'templates',
    'templateExtension' => '.phtml',
    'defaultAction' => 'index',
    'defaultController' => 'index',
    'defaultTemplate' => 'index',
    'errorTemplateSubPath' => 'error',
);