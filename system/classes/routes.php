<?php

class Routes extends Base {
	
	private 	$path = null;
	
	function __construct() {
		
		if( isset($_GET['borealis_url']) )
			$this->path = $_GET['borealis_url'];
			
		foreach( $_GET as $name => $value ) {
			if( $name != 'borealis_url' )
				$this->params($name, $value);
		}
		
		foreach( $_POST as $name => $value ) {
			$this->params($name, $value);
		}
		
	}
			
	public function connect($path, $to) {
		
		if( is_array($to) == FALSE )
			die('$Map->connect($path, $to) $to must be an array');
			
		if( empty($this->path) ) {
			
			$this->params('controller', $to['controller']);
			$this->params('action', $to['action']);
			
		}else {
			// $this->connections[] = array($path, $to);
			$this->connections($path, $to);
		}
		
	}
	
	private function run_connect() {
		
		$connections = array_reverse($this->connections());
			
		foreach( $connections as $key => $value ) {
			$path = $value[0];
			$to = $value[1];
			
			if( isset($to['default']) )
				$this->params('_default_path', $path);
			
			// Create the segments
			$segments = explode('/', $path);
			
			// Separate the current url
			$_path = explode('/', $this->path);
			
			foreach( $segments as $position => $segment ) {
				$position = $position - 1;
												
				if( preg_match('/^:(\w+)/', $segment) ) {
					
					// Check for a RegExpr
					if( preg_match('/\[(.+)\]/', $segment, $match) && isset($_path[$position]) ) {
						
						// Store the connection
						$this->config('_path', $path);
						
						// Get the expression
						$pattern = $match[0];
						
						$text = $_path[$position];
												
						if( preg_match("/^$pattern$/", $text, $match) ) {
							
							$new_segment = str_replace(array(':', $pattern), '', $segment);
							
							$this->params($new_segment, $match[0]);
							
						}else
							return;
						
					// No RegExpr
					}else {
						
						// Store the connection
						$this->config('_path', $path);

						$segment = str_replace(':', '', $segment);

						// If the url has this section /1/2/3
						if( isset($_path[$position]) ) {

							// Save the url section
							$this->params($segment, $_path[$position]);

							// Check for a format index.html
							if( $segment == 'action' ) {
								if( preg_match('/(\w+)\.(\w+)/', $_path[$position], $matches) ) {
									$this->params($segment, $matches[1]);
									$this->params('format', $matches[2]);
								}
							}

						}elseif( isset($to[$segment]) )
							$this->params($segment, $to[$segment]);
						
					}
					
				}else {
					
					if( isset($_path[$position]) ) {
						$value = $_path[$position];
						
						if( $value == $segment ) {
							
							// Store the connection
							$this->config('_path', $path);
							
							// Get the controller
							if( isset($to['controller']) )
								$this->params('controller', $to['controller']);
							else
								die('Need default controller');
								
							// Get the action
							if( isset($to['action']) )
								$this->params('action', $to['action']);
							else
								die('Need default action');
								
							// Get the format
							if( isset($to['format']) )
								$this->params('format', $to['format']);
							
						}else
							return;
					}
					
				}
			
			}
		}
		
	}
	
	public function load() {
		
		$this->run_connect();
		
		// Get the controller name
		$controller_name = strtolower($this->params('controller'));
		$controller_path = APP_PATH . '/controllers/' . $controller_name . '_controller.php';
		
		// Include the controller
		if( file_exists($controller_path) )
			include_once $controller_path;
		else
			die('Could not find controller file <br /><b>' . $controller_path . '</b>');
		
		$controller_name = ucfirst($controller_name) . 'Controller';
		
		// Initialize the controller
		$this->renderAction($controller_name, $this->params('action'), $this->params('format'));
		
	}
	
}

$Map = new Routes;
?>