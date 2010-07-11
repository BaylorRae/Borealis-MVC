<?php

class Routes extends Base {
	
	private 	$path = null;
	public 		$connections = array();
	
	function __construct() {
		
		if( isset($_GET['url']) )
			$this->path = $_GET['url'];
		
	}
		
	public function connect($path, $to) {
		
		if( is_array($to) == FALSE )
			die('$Map->connect($path, $to) $to must be an array');
			
		if( empty($this->path) ) {
			
			// $this->params['controller'] = $to['controller'];
			// $this->params['action'] 	= $to['action'];
			// $this->params['id'] 		= null;
			
			$this->params('controller', $to['controller']);
			$this->params('action', $to['action']);
			$this->params('id', null);
			
		}else {
			
			
			
		}
		
	}
	
	public function load() {
		
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
		$this->renderAction($controller_name, $this->params('action'));
		
	}
	
}

$Map = new Routes;
?>