<?php
/**
 * defineEnvironment.php
 * Defines the working environment (development vs. testing vs. production, etc.)
 * 
 * @author zburnham
 * @version 0.0.1
 */
define('ENVIRONMENT', getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production');