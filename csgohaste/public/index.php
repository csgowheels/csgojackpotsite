<?php

	error_reporting(E_ALL);
	
	define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/new');
	define('APP_PATH', BASE_PATH . '/app');
	define('LIB_PATH', BASE_PATH . '/libraries');
	
	include LIB_PATH . '/core.php';
	
	Acko\Log::set_success_log_path(APP_PATH . '/logs/success.txt');
	Acko\Log::set_error_log_path(APP_PATH . '/logs/error.txt');
	
	$app = Acko\Application::get_instance();
	$app -> load_config(LIB_PATH . '/app_config.json');
	
	$app -> start(); 
?>
