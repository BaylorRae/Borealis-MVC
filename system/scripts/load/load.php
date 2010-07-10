<?php

class Loader {
	
	
	public static function init() {
		
		$arguments = $GLOBALS['argv'];
		
		if( isset($arguments[1]) == FALSE ) {
			echo "\nUse this to load a Javascript framework";
			echo "\nLoad a javascript framework.\n./script/load javascript " . self::javascript('list') . "\n\n";
			return;
		}
		
		if( $arguments[1] == 'javascript' && isset($arguments[2]) )
			self::javascript($arguments[2]);
		else
			echo "\nLoad a javascript framework.\n./script/load javascript " . self::javascript('list') . "\n\n";
		
	}
	
	public static function javascript($framework) {
		
		// List of frameworks
			// 'framework_name' => array('url', 'name')
		$frameworks = array(
				'jquery' 				=> array('http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', 'jQuery'),
				'jqueryui' 				=> array('http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js', 'jQuery UI'),
				'mootools' 				=> array('http://ajax.googleapis.com/ajax/libs/mootools/1/mootools.js', 'MooTools'),
				'googleWebFonts' 		=> array('http://ajax.googleapis.com/ajax/libs/webfont/1.0.4/webfont.js', 'Google Web Fonts')
			);
		
		// Download the JS framework
		if( isset($frameworks[$framework]) ) {
			
			$file = fopen(self::url('javascripts') . '/' . $framework . '.js', 'w');
			echo "\nDownloading " . $frameworks[$framework][1];
			fwrite($file, file_get_contents($frameworks[$framework][0]));
			fclose($file);
			echo "\n" . $frameworks[$framework][1] . " has been added!\n\n";
		
		// List the JS frameworks
		}elseif( $framework == 'list' ) {
			
			echo "\n";
			
			$output = '';
			foreach( $frameworks as $name => $data ) {
				$output .= $name . '|';
			}
			$output = rtrim($output, '|');
			return '[' . $output . ']';
		
		// 	Framework was not found
		}else {
			echo "\nInvalid javascript framework.\nPlease use one from below.\n./script/load javascript " . self::javascript('list') . "\n\n";
		}
		
	}
	
	private static function url($location) {
		
		switch ($location) {
			
			case 'javascripts' :
				return BASE_PATH . '/../public/javascripts';
			break;
			
			default :
				return false;
			break;
		}
	}
}

Loader::init();

?>