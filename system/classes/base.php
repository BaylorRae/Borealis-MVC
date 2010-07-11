<?php

class Base {
	
	public $params = array();
	public $config = array();
	public $variables = array();
	public static $rendered = false;
	
	function __construct() {
		global $config, $params, $variables;
		$this->config 		= $config;
		$this->params 		= $params;
		$this->variables 	= $variables;
	}
	
	public function __set($name, $value) {
		global $variables;
		$variables[$name] = $value;
	}
	
	public function __get($name) {
		global $variables;
		if( isset($variables[$name]) )
			return $variables[$name];
	}
	
	public static function config($name) {
		global $config;
		
		if( in_array($name, array('DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME')) )
			return $config[ENVIRONMENT][$name];
		else
			return $config[$name];
	}
	
	public static function params($name, $value = null) {
		global $params;
		
		if( empty($value) ) {
			if( isset($params[$name]) )
				return $params[$name];
		}else {
			$params[$name] = $value;
			return $params[$name];
		}
	}
	
	public function renderAction($controller_name, $action) {
			
		if( class_exists($controller_name) ) {
			$controller_name = (is_object($controller_name)) ? get_class($controller_name) : $controller_name;
			$controller = new $controller_name;

			if( method_exists($controller, $action) ) {

				// Run the action in the controller
				$controller->$action();

				// Create the variables for the view
				$this->renderView($controller_name, $action);

			}else {
				die('Could not load action <b>' . $this->params['action'] . '</b> in controller <b>' . $controller_name . '</b>');
			}
		}else {
			die('Controller <b>' . $controller_name . '</b> could not be found in <br /><b>' . $controller_path . '</b>');
		}
			
	}
	
	public function renderView($controller, $action, $format = null) {
				
		if( Base::$rendered )
			return;
		else {
			Base::$rendered = true;
			$controller = (is_object($controller)) ? get_class($controller) : $controller;
			$format = (empty($format)) ? 'html' : $format;
			$view_path = APP_PATH . '/views/' . str_ireplace('controller', '', strtolower($controller)) . '/' . $action . '.' . $format . '.tpl';
			
			if( file_exists($view_path) ) {
				
				$vars = $this->loadVars();
				
				foreach ($vars as $var => $value) {
					$$var = $value;
				}
				
				include $view_path;
				
			}else
				die('View file not found in<br /><b>' . $view_path . '</b>');
		}
		
	}
	
	public function renderText($text) {
		
		if( Base::$rendered )
			return;
		else {
			Base::$rendered = true;
			echo $text;
		}
	}
	
	public static function loadVars() {
		global $variables, $params;
		
		$params = array('params' => $params);
		
		$output = array_merge($params, $variables);
		
		return $output;
	}
}

?>