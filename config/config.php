<?php

 // Current environment. Options are: development and production
defined('ENVIRONMENT') || define('ENVIRONMENT', 'development');

/**
 * Config for Scaffold
 * Documentation for config can be found here:
 * http://wiki.github.com/anthonyshort/Scaffold/configuration
 *
 * @author Anthony Short
 */
$config['Scaffold'] = array(
	
		// Autoload extensions
			// Extensions can be found in /libraries/scaffold/extensions
		'extensions' => array(
				'Sass',
				'Minify',
				'ServerImport'
			),
		
		// ServerImport extension config
			// Normally written as $config['ServerImport']['preppend_@server'] = false
		'ServerImport' => array('prepend_@server' => false)
	);

?>