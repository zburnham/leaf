<?php
/**
 * AutoloaderConfig.php
 * Contains default configuration information for the Autoloader class.
 * 
 * @author zburnham
 * @version 0.0.1
 */

return array(
    'autoloaderFactoryPath' => realpath(__DIR__) . '/../../../library/Leaf/Autoloader/AutoloaderFactory.php',
    'autoloaderFactoryClassName' => 'Leaf\Autoloader\AutoloaderFactory',
    'baseClass' => array(
        'path' => realpath(__DIR__) . '/../../../library/Leaf/Autoloader/Autoloader.php',
    ),
    'autoloaders' => array(
        'default' => array(
            'className' => 'Leaf\Autoloader\Autoloader',
            'path' => realpath(__DIR__) . '/../../../library/Leaf/Autoloader/Autoloader.php',
            'masterPaths' => array(
                realpath(__DIR__) . '/../../../library' => array(
                    'subPaths' => array(
                        '/',
                    ),
                ),
                realpath(__DIR__) . '/../../../vendor' => array(
                    'subPaths' => array(
                        '/',
                    ),
                ),
            ),
        ),
    ),
);