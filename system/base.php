<?php

class Base {
	
	public static function config($name) {
		global $config;
		
		if( in_array($name, array('DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME')) )
			return $config[ENVIRONMENT][$name];
		else
			return $config[$name];
	}
	
}

?>