<?php

if( isset($_GET['stylesheet_url']) )  {
	
	$_GET['file'] = $_GET['stylesheet_url'];
	
	include '../config/config.php';
		
	$config = $config['Scaffold'];
		
	include '../libraries/scaffold/index.php';
	
}else {
	
	// Load the framework
	include '../system/init.php';
	
}

?>