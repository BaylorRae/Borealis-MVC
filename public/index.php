<?php

if( isset($_GET['borealis_url']) ) {
	// Load the framework
	include '../system/init.php';	
}elseif( isset($_GET['stylesheet_url']) )  {
	$_GET['file'] = $_GET['stylesheet_url'];
	
	include '../config/config.php';
	
	$config = $config['Scaffold'];
	
	include '../libraries/scaffold/index.php';
}
?>