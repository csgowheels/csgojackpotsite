<?php

	/*
	 * 	This module contains all important imports which are required in order for framework to work
	 * 
	 * 	In your application bootstrap (index.php) you should include this module, so that your
	 * 	framework works as planned
	 * 
	 * 	Important this module is not part of framework, it is just module used for site
	 * 
	 * 	@author Acko
	 * 	@date_created 06.09.2015.
	 * 	@last_changed 06.09.2015. 04:20 
	 */
	 
	 // includes for framework
	 include_once LIB_PATH . '/framework/application.php';
	 include_once LIB_PATH . '/framework/controller.php';
	 include_once LIB_PATH . '/framework/model.php';
	 include_once LIB_PATH . '/framework/view.php';
	 include_once LIB_PATH . '/framework/router.php';
	 include_once LIB_PATH . '/framework/routes_container.php';
	 include_once LIB_PATH . '/framework/exceptions.php';
	 include_once LIB_PATH . '/framework/log.php';
     include_once LIB_PATH . '/framework/path_info.php';
     include_once LIB_PATH . '/framework/const_one.php';

     include_once APP_PATH . '/controllers/csgowheels_controller.php';
     include_once APP_PATH . '/models/csgowheels_model.php';
	 
?>
