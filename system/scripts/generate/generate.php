<?php

class Generator {
	
	
	public static function init() {
		$arguments = $GLOBALS['argv'];
		
		if( isset($arguments[1]) == FALSE ) {
			echo "\nUse this to generate a controller, or model.\n\n";
			return;
		}
		
		if( $arguments[1] == 'controller' && isset($arguments[2]) )
			self::controller($arguments[2]);
			
		elseif( $arguments[1] == 'model' && isset($arguments[2]) )
			self::model($arguments[2]);
			
		else
			echo "\nPlease include a controller name.\n./script/generate controller [name]\n\n";
		
	}
	
	// Generate a controller
	public static function controller($name) {
				
		if( file_exists(BASE_PATH . '/../app/controllers/' . $name . '_controller.php' ) ) {
			echo "\nThe controller " . $name . " already exists in\n" . realpath(dirname(BASE_PATH . '/../app/controllers/')) . '/controllers/' . $name . '_controller.php' . "\n\n";
		}else {
			
			$class_name = ucfirst($name);
			
			$template = file_get_contents('controller.tpl', true);
			$template = str_replace('{$class_name}', $class_name, $template);
			
			$file = fopen(BASE_PATH . '/../app/controllers/' . $name . '_controller.php', 'w');
			fwrite($file, $template);
			fclose($file);
			
			echo "\nCreated the controller " . $class_name . " in\n";
			echo realpath(dirname(BASE_PATH . '/../app/controllers/')) . '/controllers/' . $name . '_controller.php' . "\n\n";
			
		}
		
	}
	
	// Generate a model
	public static function model($name) {
				
		if( file_exists(BASE_PATH . '/../app/models/' . $name . '.php' ) ) {
			echo "\nThe model " . $name . " already exists in\n" . realpath(dirname(BASE_PATH . '/../app/models/')) . '/models/' . $name . '.php' . "\n\n";
		}else {
			
			$class_name = ucfirst($name);
			
			$template = file_get_contents('model.tpl', true);
			$template = str_replace('{$class_name}', $class_name, $template);
			
			$file = fopen(BASE_PATH . '/../app/models/' . $name . '.php', 'w');
			fwrite($file, $template);
			fclose($file);
			
			echo "\nCreated the model " . $class_name . " in\n";
			echo realpath(dirname(BASE_PATH . '/../app/models/')) . '/models/' . $name . '.php' . "\n\n";
			
		}
		
	}
}

Generator::init();

?>