<?php
/**
 * ClassFactory.class.php
 * Factory class to return objects once the configuration has been validated.
 * Classes must implement the Configable interface.
 * 
 * @author zburnham
 * @version 0.0.1
 */
namespace Leaf\ClassFactory;

use Leaf\Config\Config;

class ClassFactory
{
    /**
     * Builds and configures an instance of the requested class.
     * 
     * @param Config $classDefinition Describes class to be instantiated.
     * @param array $parameters Extra parameters to hand to the class constructor
     * that aren't configurable.
     * @return mixed New instance of described class.
     */
    public function get(Config $classDefinition, array $parameters = array())
    {
        if (NULL === $classDefinition->get('className')) {
            throw new ClassFactoryException('Required className not present.', 500);
        }
        
        $class = $classDefinition->get('className');
        $class = new $class($classDefinition, $parameters);
        $requiredConfigKeys = $class->getRequiredConfigKeys();
        $classDefinitionError = FALSE;
        $missingKeys = array();
        if (0 < sizeof($requiredConfigKeys)) {
            foreach ($requiredConfigKeys as $key) {
                if (NULL === $classDefinition->get($key)) {
                    $classDefinitionError = TRUE;
                    $missingKeys[] = $key;
                }
                if ($classDefinitionError) {
                    $invalidKeysString = implode(', ', $missingKeys);
                    throw new ClassFactoryException('Invalid class definition, ' . 
                                                    $invalidKeysString . ' are missing.',
                                                    500);
                }
            }
        }
        // TODO LEFTOFF Have to figure out a way to get sane defaults configured
        // for a given class.  Maybe a separate config that's merged with the
        // app-specific config?
        //$childClassConfig = new Config($classDefinition);
        $class->setConfig($classDefinition);
        $class->setCf($this);
        return $class;
    }
}