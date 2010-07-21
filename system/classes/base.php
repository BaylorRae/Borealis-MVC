<?php

class Base {
	
	public static $params = array();
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
	
	public static function config($name, $value = null) {
		global $config;
		
		if( in_array($name, array('DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME')) ) {
			if( empty($value) )
				return $config[ENVIRONMENT][$name];
			else {
				$config[ENVIRONMENT][$name] = $value;
				return $config[ENVIRONMENT][$name];
			}
		}else {
			if( empty($value) )
				return $config[$name];
			else {
				$config[$name] = $value;
				return $config[$name];
			}
		}
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
	
	public static function helpers($name, $value = null) {
		global $helpers;
		
		if( empty($value) ) {
			if( isset($helpers[$name]) )
				return $helpers[$name];
		}else {
			$helpers[$name] = $value;
			return $helpers[$name];
		}
	}

	public static function flash($message) {
		global $flash;
		
		$_SESSION['borealis_flash'][] = $message;
		$flash[] = $message;
				
	}
		
	public function connections($path = null, $to = null) {
		global $connections;
		if( empty($path) || empty($to) )
			return $connections;
		else
			$connections[] = array($path, $to);
	}
	
	public function renderAction($controller_name, $action, $format = null) {
		$controller_name = (is_object($controller_name)) ? get_class($controller_name) : $controller_name;
		
		if( class_exists($controller_name) ) {
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
		global $variables, $params, $flash;
						
		$helpers = Base::loadHelpers();
		
		$output = array_merge(array('params' => $params), $variables, $helpers, array('flash' => $flash));
				
		return $output;
	}

	private function loadHelpers() {
		global $helpers;
		
		// Check Borealis's helpers "/helpers"
		if( $handle = opendir(BASE_PATH . '/helpers') ) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && $file[0] != '.' && substr($file, -4) == '.php') {
					
					include_once BASE_PATH . '/helpers/' . $file;
					
					$classname = ucfirst(substr(strtolower($file), 0, -4)) . 'Helper';
					
					if( class_exists($classname) ) {
						
						Base::helpers(substr(strtolower($file), 0, -4) . '_helper', new $classname);
						
					}
					
				}
			}
			closedir($handle);
			
		
		}
		
		// Check user helpers
		if( $handle = opendir(APP_PATH . '/helpers') ) {
			
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && $file[0] != '.' && substr($file, -4) == '.php') {
					
					include_once APP_PATH . '/helpers/' . $file;
					
					$classname = ucfirst(substr(strtolower($file), 0, -4)) . 'Helper';
					
					if( class_exists($classname) ) {
						
						Base::helpers(substr(strtolower($file), 0, -4) . '_helper', new $classname);
						
					}
					
				}
			}
			closedir($handle);
			
		}
		
		return $helpers;
		
	}
	
	
	/**
	 * Redirect to a url
	 *
	 * @param string $controller_name 
	 * @param string $action 
	 * @param string $format 
	 * @param array $params (optional) 
	 * @return void
	 * @author Baylor Rae'
	 */	
	public function redirectTo($controller_name, $action, $format = null, $params = null) {
		
		Base::$rendered = true;
		
		$path = $this->config('_path');		
		
		// Make sure the path includes a controller and an action
		if( !preg_match('/:controller/', $path) || !preg_match('/:action/', $path) )
			
			// If it doesn't, then use the default path
			$path = $this->config('_default_path');
			
		// Get each variable in the path
		$segments = explode('/', $path);
		
		// Check for a controller
		$controller_name = (is_object($controller_name)) ? get_class($controller_name) : $controller_name;
		
		$params = (!is_array($params)) ? array() : $params;
		
		$params['controller'] = str_replace('controller', '', strtolower($controller_name));
		$params['action'] = $action;
		
		foreach( $params as $key => $value ) {
			foreach( $segments as $segment ) {
				if( ':' . $key == $segment ) {
					$path = str_replace($segment, $value, $path);
				}
			}
		}
		
		$path = preg_replace('/\/:(.+)/', '', $path);
							
		$url = rtrim($this->config('ROOT'), '/') . $path;
				
		if( !empty($format) )
			$url .= '.' . $format;
						
		echo '<meta http-equiv="refresh" content="0;url=' . $url . '" />';
		
	}
}

?>