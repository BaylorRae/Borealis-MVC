<?php

class UrlHelper extends HelperBase {
	
	private static $loader;
	// private static $_params;
	
	function __construct() {
		UrlHelper::$loader = new AssetsLoader($this->config('ROOT'), 'stylesheets', 'javascripts');
		// UrlHelper::$_params = Base::$params;
	}
	
	/**
	 * Include stylesheets.
	 *
	 * @param string $names Can be an array e.g. array('reset', 'application') or a string 'reset, application'
	 * @return Void
	 * @author Baylor Rae'
	 */
	public static function get_stylesheets($files = null) {
		
		// Make sure the list is not an array
		if( is_array($files) == FALSE ) {
			$list = explode(',', $files);
			
			$files = array();
			foreach( $list as $name ) {
				$name = trim($name);
				$files[] = $name;
			}
		}
		
		foreach( $files as $file ) {
			self::$loader->get_stylesheet_link($file);
		}
		
	}
	
	/**
	 * Include javascripts.
	 *
	 * @param string $names Can be an array e.g. array('jquery', 'jqueryui') or a string 'jquery, jqueryui'
	 * @return Void
	 * @author Baylor Rae'
	 */
	public static function get_javascripts($files = null) {
		
		// Make sure the list is not an array
		if( is_array($files) == FALSE ) {
			$list = explode(',', $files);
			
			$files = array();
			foreach( $list as $name ) {
				$name = trim($name);
				$files[] = $name;
			}
		}
		
		foreach( $files as $file ) {
			self::$loader->get_script_tag($file);
		}
		
	}

	public static function link_to($text, $controller_name, $action, $params = null) {
		global $config;
		
		if( isset($config['_path']) )
			$path = $config['_path'];
		else
			$path = null;
								
		// Make sure the path includes a controller and an action
		if( !preg_match('/:controller/', $path) || !preg_match('/:action/', $path) )
			
			// If it doesn't, then use the default path
			$path = $config['_default_path'];
						
		// Get each variable in the path
		$segments = explode('/', $path);
		
		// Check for a controller
		$controller_name = (is_object($controller_name)) ? get_class($controller_name) : $controller_name;
		
		$params = (!is_array($params)) ? array() : $params;
		
		$params['controller'] = str_replace('controller', '', strtolower($controller_name));
		$params['action'] = $action;
		
		$special = array('href');
		$special = array_merge($special, $segments);
				
		// Check for paramaters
		foreach( $params as $key => $value ) {
			foreach( $segments as $segment ) {
				if( ':' . $key == $segment ) {
					if( !empty($value) )
						$path = str_replace($segment, $value, $path);
				}
			}
		}
		
		$attributes = null;
		if( is_array($params['attr']) ) {
			foreach( $params['attr'] as $prop => $value ) {
				$attributes .= ' ' . $prop . '="' . $value . '"';
			}
		}
								
		$path = preg_replace('/\/:(\w+)/', '', $path);
							
		$url = rtrim($config['ROOT'], '/') . $path;
				
		if( !empty($params['format']) )
			$url .= '.' . $params['format'];
		
		echo '<a' . $attributes . ' href="' . $url . '">' . $text . '</a>';
	}
}

if( !function_exists('get_stylesheets') ) {
	function get_stylesheets($files = null) {
		UrlHelper::get_stylesheets($files);
	}
}

if( !function_exists('get_javascripts') ) {
	function get_javascripts($files = null) {
		UrlHelper::get_javascripts($files);
	}
}

if( !function_exists('link_to') ) {
	function link_to($text, $controller_name, $action, $params = null) {
		UrlHelper::link_to($text, $controller_name, $action, $params);
	}
}


/**
 * This Class loads CSS and JS files
 *
 * @package default
 * @author Baylor Rae'
 **/
class AssetsLoader {
	var $main_directory;
	var $css_directory;
	var $js_directory;
	
	private $link_format = "<link rel=\"stylesheet\" href=\"%href%.css\" type=\"text/css\" media=\"%media%\" charset=\"utf-8\" />\n";
	private $link_format_media = 'screen';
	private $link_format_changed = false;
	
	private $js_format = "<script src=\"%src%.js\" type=\"text/javascript\" charset=\"utf-8\"></script>\n";
	private $js_format_changed = false;
	
	private $options;
	private $loop = 0;
	
	/**
	 * Initialized the loader
	 *
	 * @param string $dir - Root Location of the server
	 * @param string $css - Name of the CSS directory
	 * @param string $js  - Name of the JS directory
	 * @return void
	 * @author Baylor Rae'
	 */
	function __construct($dir = '', $css = '', $js = '') {
		$dir = (empty($dir)) ? './' 		 : $dir;
		$css = (empty($css)) ? 'stylesheets' : $css;
		$js  = (empty($js))	 ? 'javascripts' : $js;
			
		$this->set('main', $dir);
		$this->set('css', $css);
		$this->set('js', $js);
		return;
	}
	
	/**
	 * Sets the value of the (Main, CSS, or JS) directories
	 *
	 * @param string $option - Which directory to change
	 * @param string $value - Name of the directory e.g (css)
	 * @return void
	 * @author Baylor Rae'
	 */
	function set($option, $value) {
		switch(strtolower($option)) {
			case 'main' :
				$this->main_directory = $this->set_extension($value);
			break;
			case 'css' :
				$this->css_directory = $this->main_directory . $this->set_extension($value);
			break;
			case 'js' :
				$this->js_directory = $this->main_directory . $this->set_extension($value);
			break;
			default :
				echo "loader::set option but be main, css, or js";
			break;
		}
	}
	
	/**
	 * Get the value of a directory name
	 *
	 * @param string $option - Directory name (main, css, js)
	 * @param boolean $echo - Echo or return the value (true, false)
	 * @return void
	 * @author Baylor Rae'
	 */
	function get($option, $echo = true) {
		switch(strtolower($option)) {
			case 'main' :
				if( $echo == true )
					echo $this->main_directory;
				else
					return $this->main_directory;
			break;
			case 'css' :
				if( $echo == true )
					echo $this->css_directory;
				else
					return $this->css_directory;
			break;
			case 'js' :
				if( $echo == true )
					echo $this->js_directory;
				else
					return $this->js_directory;
			break;
		}
	}
	
	/**
	 * Check if the string ends with correct extension e.g (.css)
	 *
	 * @param string $var - Variable to check
	 * @param string $extension - Extension to match : default('/')
	 * @return void
	 * @author Baylor Rae'
	 */
	function set_extension($var, $extension = '/') {
		if( substr($var, -strlen($extension)) != $extension )
			return $var .= $extension;
		else
			return $var;
	}
	
	// =========================
	// = Creating Include Tags =
	// =========================
	
	
	/**
	 * Set a custom stylesheet link tag (Requires href="%href%" and media="%media%")
	 *
	 * @param string $format - e.g (<link rel="stylesheet" href="%href%" media="%media%" />)
	 * @return void
	 * @author Baylor Rae'
	 */
	function stylesheet_link($format = '') {
		if( $format != '' ) {
			if( !preg_match('/.css/', $format) ) 
				$format = str_replace('%href%', '%href%.css', $format);
			$this->link_format = $format . "\n";
			$this->link_format_changed = true;
		}
	}
	
	/**
	 * Set a custom javascript include tag (Requires src="%src%")
	 *
	 * @param string $format e.g (<script src="%src%" language="javascript"></script>)
	 * @return void
	 * @author Baylor Rae'
	 */
	function javascript_include_tag($format = '') {
		if( $format != '' ) {
			if( !preg_match('/.js/', $format) ) 
				$format = str_replace('%src%', '%src%.js', $format);
			$this->js_format = $format . "\n";
			$this->js_format_changed = true;
		}
	}
	
	/**
	 * Echos a script tag
	 *
	 * @param string or array $src_url - 'site' or array('framework', 'site')
	 * @param string $dir - Custom directory name e.g (js-plugins)
	 * @return void
	 * @author Baylor Rae'
	 */
	function get_script_tag($src_url, $dir = '') {
		
		$directory = $this->get('js', false);
		if( $dir != '' )
			$directory = $this->get('main', false) . $this->set_extension($dir); 
		
		if( is_array($src_url) ) {
			foreach( $src_url as $src ) :
				$src = str_replace('.js', '', $src);
				$format = $this->js_format;
				echo str_replace('%src%', $directory . $src, $format);
			endforeach;
		}else {
			$src_url = str_replace('.js', '', $src_url);
			$format = $this->js_format;
			echo str_replace('%src%', $directory . $src_url, $format);
		}
	}
	
	/**
	 * Echos a stylesheet link
	 *
	 * @param string or array $href - 'style' or array('reset', 'style')
	 * @param string $dir - Custom directory name e.g (css-plugins)
	 * @param string $media - Set media type for link : default('print')
	 * @return void
	 * @author Baylor Rae'
	 */
	function get_stylesheet_link($href, $dir = '', $media = '') {
		
		$directory = $this->get('css', false);
		if( $dir != '' )
			$directory = $this->get('main', false) . $this->set_extension($dir);
			
		if( $media == '' )
			$media = $this->link_format_media;
		
		if( is_array($href) ) {
			foreach( $href as $location ) :
				$location = str_replace('.css', '', $location);
				$format = $this->link_format;
				echo str_replace(array('%href%', '%media%'), array($directory . $location, $media), $format);
			endforeach;
		}else {
			$href = str_replace('.css', '', $href);
			$format = $this->link_format;
			echo str_replace(array('%href%', '%media%'), array($directory . $href, $media), $format);
		}
	}
	
	/**
	 * Include a IE specific stylesheet
	 *
	 * @param string $version - IE version e.g ('6>=')
	 * @param string or array $file_name - 'ie-style' or array('ie-reset', 'ie-style')
	 * @param string $dir - Custom directory to include files from. e.g (ie-stuff)
	 * @return void
	 * @author Baylor Rae'
	 */
	function ie_stylesheet($version, $file_name, $dir = '') {
		// <!--[if lte IE 6]>
		$range = '';
		
		// Get Version Number
		if( preg_match('/(5\.5|6|7|8)/', $version, $match) ) {
			$version_number = $match[0];
		}
				
		// Get lte
		if( preg_match('/[><=]+/', $version) ) {
			
			// Less Than
			if( preg_match('/>/', $version) ) {
				$range .= ' lt';
			}

			// Greater Than
			if( preg_match('/</', $version) ) {
				$range .= ' gt';
			}
			
			// (option) Equal
			if( preg_match('/=/', $version) ) {
				$range .= 'e';
			}
		}
		
		// echo $range;
		echo "<!-- [if{$range} IE {$version_number}]>" . "\n";
		$this->get_stylesheet_link($file_name, $dir);
		echo '<![endif]-->' . "\n";
	}
	
}
?>