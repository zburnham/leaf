<?php
/**
 * config.php
 * Reads top-level configfiles and merges with defaults.
 * Returns an array representing all the configuration for the application.
 * 
 * @author zburnham
 * @version 0.0.1 
 */

require('settings/configSettings.php');

// TODO. This is pretty much a train wreck.  There's probably a far more
// elegant way to do it.

function recursiveConfig($folder, $basePath, array $config)
{
    $handleName = str_replace('/', '_', $folder) . 'Handle';
    $handle = opendir($folder);
    while (FALSE !== ($entry = readdir($handle))) {
        if (!preg_match('|^\.|', $entry)) {
            //TODO maybe a closure to give the config files a little easier time
            // when calling sub-configurations?
            if (is_dir($folder . '/' . $entry)) {
                $config[$entry] = recursiveConfig($folder . '/' . $entry, $basePath, $config);
            } else if (is_link($folder . '/' . $entry)) {
                $config[$entry] = recursiveConfig($folder . '/' . readlink($entry), $basePath, $config);
            } else if (is_file($folder . '/' . $entry)) {
                $newArray = include($folder . '/' . $entry);
                return array_merge($config, $newArray);
            } else {
                throw new \Exception("What the heck is that?  Unknown directory entry: " .
                                        $folder . '/' . $entry);
            }
        }
        
    }
    closedir($handle);
    return $config;
    
}

function compileConfig($configDir, $defaultArray, $additionalNamespaces)
{
    $compiledConfig = array();
    $handle = opendir($configDir);
    while (FALSE !== ($entry = readdir($handle))) {
        $basePath = $configDir . '/' . $entry;
        if (!preg_match('|^\.|', $entry) && is_dir($basePath)) {
            $compiledConfig[$entry] = recursiveConfig($basePath, $basePath, array());
        }
    }
    
//    $defaultsArray = $compiledConfig[$defaultArray];
//    if (!is_array($defaultsArray)) {
//        throw new \Exception('Default configuration array ' . $defaultArray . ' not present.');
//    }
    if (!isset($compiledConfig[$defaultArray])) {
        throw new \Exception('Default configuration array namespace ' . $defaultArray . ' not present.');
    }
    
    $completedConfig = $compiledConfig[$defaultArray]; // enforces defaults.
    
    foreach ($additionalNamespaces as $namespace) { // Enforces loading order.
        $completedConfig = array_merge($completedConfig, $compiledConfig[$namespace]);
    }
    return $completedConfig;
}

/**
 * Array. 
 */
return compileConfig(DEFAULTS_CONFIG_DIRECTORY, DEFAULTS_NAMESPACE, $additionalNamespaces);