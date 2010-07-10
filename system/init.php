<?php

// Setup our locations
define('BASE_PATH', realpath(dirname('../../')));
define('LIBRARIES', BASE_PATH . '/libraries');
define('APP_PATH', BASE_PATH . '/app');

// Get the Base class
include BASE_PATH . '/system/base.php';

// Load spyc ( yaml to array)
include LIBRARIES . '/spyc.php';

// ActiveRecord
include LIBRARIES . '/activerecord/ActiveRecord.php';

// Setup for configurations
$config = array();
include BASE_PATH . '/config/config.php';

// Load database information
$db_info = spyc_load_file(BASE_PATH . '/config/database.yml');

if( isset($db_info['default']) ) {
	if( isset($db_info[ENVIRONMENT]) )
		$db_info[ENVIRONMENT] = array_merge($db_info['default'], $db_info[ENVIRONMENT]);
				
	unset($db_info['default']);
}

$config[ENVIRONMENT]['DB_HOST'] = $db_info[ENVIRONMENT]['host'];
$config[ENVIRONMENT]['DB_USER'] = $db_info[ENVIRONMENT]['user'];
$config[ENVIRONMENT]['DB_PASS'] = $db_info[ENVIRONMENT]['password'];
$config[ENVIRONMENT]['DB_NAME'] = $db_info[ENVIRONMENT]['database_name'];
unset($db_info);
// Done loading database info and putting it into $config array

// Connect to the database
ActiveRecord\Config::initialize(function($cfg) {
    $cfg->set_model_directory(APP_PATH . '/models');
    $cfg->set_connections(array(ENVIRONMENT => 'mysql://' . Base::config('DB_USER') . ':' . Base::config('DB_PASS') . '@' . Base::config('DB_HOST') . '/' . Base::config('DB_NAME') . ''));
});



?>