<?php

/**
 * Scaffold_Environment
 *
 * Handles autoloading, exceptions, errors and fatals etc. General,
 * usually optional, environment settings.
 * 
 * @package 		Environment
 * @author 			Anthony Short <anthonyshort@me.com>
 * @author			Kohana Team
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Environment
{
	/**
	 * @var string
	 */
	private static $_view;

	/**
	 * Human-readable error descriptions
	 * @var array
	 */
	protected static $_php_errors = array
	(
	 	E_ERROR              => 'Fatal Error',
	 	E_USER_ERROR         => 'User Error',
	 	E_PARSE              => 'Parse Error',
	 	E_WARNING            => 'Warning',
	 	E_USER_WARNING       => 'User Warning',
	 	E_STRICT             => 'Strict',
	 	E_NOTICE             => 'Notice',
	 	E_RECOVERABLE_ERROR  => 'Recoverable Error',
	);
	
	/**
	 * Automatically loads Scaffold's classes.
	 * Assumes the scaffold folder with all the classes
	 * is located next to this file.
	 * @access public
	 * @param $class string
	 * @return boolean
	 */
	public static function auto_load($class)
	{
		# Check if class is already loaded
		if (class_exists($class,false)) 
			return true;

		# Transform the class name into a path
		$file = str_replace('_', '/', ucfirst($class)) . '.php';
		
		# The path to the lib folder with the file
		$path = dirname(__FILE__) . '/' . $file;
		
		if(file_exists($path))
		{
			require_once $path;
			return true;
		}

		return false;
	}

	/**
	 * PHP error handler, converts all errors into ErrorExceptions. This handler
	 * respects error_reporting settings.
	 * @author 	Kohana
	 * @throws  ErrorException
	 * @return  TRUE
	 */
	public static function error_handler($code, $error, $file = NULL, $line = NULL)
	{
		if (error_reporting() & $code)
		{
			throw new ErrorException($error, $code, 0, $file, $line);
		}

		# Do not execute the PHP error handler
		return TRUE;
	}

	/**
	 * Inline exception handler, displays the error message, source of the
	 * exception, and the stack trace of the error.
	 * @param   object   exception object
	 * @return  boolean
	 */
	public static function exception_handler(Exception $e)
	{
		try
		{
			# Exception text information
			$title 		= "Error";
			$type    	= get_class($e);
			$code 	 	= $e->getCode();
			$message 	= $e->getMessage();
			$file    	= $e->getFile();
			$line    	= $e->getLine();
			
			# Server error status
			if(!headers_sent())
			{
				header('Content-Type: text/html;', TRUE, 500);
			}
	
			// Use the human-readable error name
			if($e instanceof ErrorException)
			{
				if(isset(self::$_php_errors[$code]))
				{
					$title = self::$_php_errors[$code];
				}
			}
			elseif(isset($e->title))
			{
				$title = $e->title;
			}
			
			if(self::$_view !== null)
			{
				ob_start();
				include self::$_view;
				echo ob_get_clean();
			}
			return true;
		}
		catch (Exception $e)
		{
			# Clean the output buffer if one exists
			ob_get_level() and ob_clean();

			# Display the exception text
			echo self::exception_text($e);

			exit(1);
		}
	}
	
	/**
	 * Set a view file to use for errors and exceptions
	 * @author Anthony Short
	 * @param $path Path to the view file
	 * @return void
	 */
	public static function set_view($path)
	{
		if(is_file($path))
		{
			self::$_view = $path;
		}
		else
		{
			throw new Exception('[Environment] Path to view file is not valid');
		}
	}
	
	/**
	 * Get a single line of text representing the exception:
	 *
	 * Error [ Code ]: Message ~ File [ Line ]
	 *
	 * @param   object  Exception
	 * @return  string
	 */
	public static function exception_text(Exception $e)
	{
		return sprintf('%s [ %s ]: %s ~ %s [ %d ]',
			get_class($e), $e->getCode(), strip_tags($e->getMessage()), $e->getFile(), $e->getLine());
	}
	
	/**
	 * Catches errors that are not caught by the error handler, such as E_PARSE.
	 *
	 * @uses    Scaffold::exception_handler
	 * @return  void
	 */
	public static function shutdown_handler()
	{
		if ($error = error_get_last() AND (error_reporting() & $error['type']))
		{
			# If an output buffer exists, clear it
			ob_get_level() and ob_clean();

			# Fake an exception for nice debugging
			self::exception_handler(new ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']));

			exit(1);
		}
	}

}