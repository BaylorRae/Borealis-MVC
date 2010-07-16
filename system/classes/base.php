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
	
	public function renderAction($controller_name, $action, $format = null) {
		$controller_name = (is_object($controller_name)) ? get_class($controller_name) : $controller_name;
		
		if( class_exists($controller_name) ) {
			$controller_name = (is_object($controller_name)) ? get_class($controller_name) : $controller_name;
			$controller = new $controller_name;

			if( method_exists($controller, $action) ) {

				// Run the action in the controller
				$controller->$action();

				// Create the variables for the view
				$this->renderView($controller_name, $action, $format);

			}else {
				die('Could not load action <b>' . $this->params('action') . '</b> in controller <b>' . $controller_name . '</b>');
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
				
				// Create the page variables
				$vars = $this->loadVars();
				foreach ($vars as $var => $value) {
					$$var = $value;
				}
				
				// Get the contents of the view
				$view = file_get_contents($view_path, true);
				$layout = null;
				$content = null;
				
				// ===================
				// = Load the layout =
				// ===================
					// Hierarchy
					// 1. controller-action.format.tpl
					// 2. controller.format.tpl
					// 3. application.format.html
				
				// Look for action specific
				if( file_exists(APP_PATH . '/views/layouts/' . str_ireplace('controller', '', strtolower($controller)) . '-' . $action . '.' . $format . '.tpl') )
					$layout = file_get_contents(APP_PATH . '/views/layouts/' . str_ireplace('controller', '', strtolower($controller)) . '-' . $action . '.' . $format . '.tpl', true);
				
				// Check for controller specific
				elseif( file_exists(APP_PATH . '/views/layouts/' . str_ireplace('controller', '', strtolower($controller)) . '.' . $format . '.tpl') )
					$layout = file_get_contents(APP_PATH . '/views/layouts/' . str_ireplace('controller', '', strtolower($controller)) . '.' . $format . '.tpl', true);
					
				// Check for a default application view
				elseif( file_exists(APP_PATH . '/views/layouts/application.' . $format . '.tpl') )
					$layout = file_get_contents(APP_PATH . '/views/layouts/application.' . $format . '.tpl', true);
									
				if( empty($layout) )
					$content = $view;
				else
					$content = str_replace('{PAGE_CONTENT}', $view, $layout);
				
				// Create the file
				$render_path = BASE_PATH . '/system/tmp/views/' . $controller . '-' . $action . '.' . time() . '.php';
				$file = fopen($render_path, 'w');
				fwrite($file, $content);
				fclose($file);
				
				include_once $render_path;
				
				unlink($render_path);
				
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