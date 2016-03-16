<?php

    /*
	 *  This module contains class Application which is base class for each application
	 * 	which uses this framework.
	 * 
	 * 	It is "Singleton" pattern based, so that only one instance exists in one project.
	 * 
	 * 	It should contains all necessary methods and data for one MVC application to work using
	 * 	this framework
	 * 
	 * 	@author Acko
	 * 	@date_created 06.09.2015.
	 * 	@last_changed  06.09.2015. 04:21
	 */

	namespace Acko;
	
	/**
	 * This is main class of framework.
	 * 
	 * It should be instantiated only once and it contain's all information about application,
	 * (such as router info, router_container info, app_path, and all similar stuff).
	 * 
	 * It contains it's own Acko\Router class instance, which it uses for routing and parsing url (uri).
	 * That router is accessed through public variable router (Acko\Application::get_instance() -> router),
	 * and for setting other stuff for router see {@link Acko\Router class},
	 * also it relies on RoutesContainer class, because Router is internally based on RoutesContainer.
	 * 
	 * It can load configuration (json format), but those configuration must follow some blueprint, in order for this to work.
	 * 
	 * Main method for this class is called for each index.php (bootstrap) request and it uses Router to parse url (uri) and
	 * to find appropriate controller class (and its action) which will then do rest of work.
	 * 
	 * @package Acko
	 * @author Acko
	 */
	class Application
	{
        /** @var $instance -> Singleton pattern instance variable */
		private static $instance = null;
        /** @var $default_controller -> Name of default controller which will be loaded if there is an error */
        private $default_controller;
        /** @var $default_path -> path to default controller */
        private $default_path;
        /** @var $default_action -> Name of default action which will be performed if there is an error */
        private $default_action;
        /** @var default_controller_path -> path to default controller class file */
        private $default_controller_path;
        /** @var application_namespace -> namespace for all application controllers and models */
        private $application_namespace;
		
		/**
		 * Private constructor (Base constructor)
		 * 
		 * Does initial setting up and initializing all needed variables of a class
		 */
		private function __construct()
		{
			$this -> router = new Router();
            $this -> path_info = new PathInformation();
            $this -> application_namespace = "";
		}
		
		/**
		 * Public method for getting instance of Application class (Singleton pattern)
		 * 
		 * @return -> Acko\Application instance
		 */
		public static function get_instance()
		{
			if (Application::$instance === null)
				Application::$instance = new Application();
			return Application::$instance;
		}
		
		/**
		 * Method for loading and parsing configuration written for application.
		 * 
		 * It reads configuration file, then tries to parse json read, and then sets all parameters from json
		 * to current instance's properties. @Note json file should follow some pattern (blueprint)
		 * 
		 * @param $path -> (string) path to the configuration file
		 * 
		 * TODO: Do corrupted json handling.
		 */
		public function load_config($path)
		{
			$file = fopen($path, "r");
			$json = fread($file, filesize($path));
			fclose($file);
			
			$decoded = json_decode($json) or die("unable to parse");

            $this -> application_namespace = $decoded -> general_config -> app -> app_namespace;
						
            // seting path variables
            $this -> path_info -> SERVER_PATH -> set_value($decoded -> path_config -> SERVER_PATH);
            $this -> path_info -> APP_PATH -> set_value($this -> path_info -> SERVER_PATH -> value() 
                                                            . $decoded -> path_config -> app_path_postfix);
            $this -> path_info -> CONTROLLER_PATH -> set_value($this -> path_info -> APP_PATH -> value() 
                                                                . $decoded -> path_config -> controller_path_postfix);
            $this -> path_info -> VIEW_PATH -> set_value($this -> path_info -> APP_PATH -> value() 
                                                            . $decoded -> path_config -> view_path_postfix);
            $this -> path_info -> MODEL_PATH -> set_value($this -> path_info -> APP_PATH -> value() 
                                                            . $decoded -> path_config -> model_path_postfix);
            $this -> path_info -> LIB_PATH -> set_value($this -> path_info -> SERVER_PATH -> value() 
                                                            . $decoded -> path_config -> lib_path_postfix);
            $this -> path_info -> DEFAULTS_PATH -> set_value($this -> path_info -> LIB_PATH -> value() 
                                                                 . $decoded -> path_config -> defaults_path_postfix);
            $this -> path_info -> URL -> set_value($decoded -> path_config -> URL);
            $this -> path_info -> SITE_BASE -> set_value($this -> path_info -> URL -> value() 
                                                            . $decoded -> general_config -> router -> server_prefix);
            $this -> path_info -> CSS_PATH -> set_value($this -> path_info -> URL -> value() 
                                                         . $decoded -> general_config -> router -> server_prefix 
                                                         . $decoded -> path_config -> css_path_postfix);
            $this -> path_info -> JS_PATH -> set_value($this -> path_info -> URL -> value() 
                                                            . $decoded -> general_config -> router -> server_prefix
                                                            . $decoded -> path_config -> js_path_postfix);

			// set router options (from json)
			$this -> router -> set_server_prefix($decoded -> general_config -> router -> server_prefix);
			$this -> router -> set_controller_postfix($decoded -> general_config -> router -> controller_postfix);
			$this -> router -> set_controller_path_postfix($decoded -> general_config -> router -> controller_path_postfix);
			$this -> router -> set_action_postfix($decoded -> general_config -> router -> action_postfix);
			
			// setting up default values
			$this -> default_controller = $decoded -> general_config -> defaults -> controller_name;
            $this -> default_controller_path = $decoded -> general_config -> defaults -> controller_path;
			$this -> default_action = $decoded -> general_config -> defaults -> action_name;
			
			// create routes container, add all routes from json, and load them into router
            $routes = new RoutesContainer($decoded -> general_config -> routes -> separator,
                                             $decoded -> general_config -> routes -> param_escape);
			foreach ($decoded -> general_config -> routes as $route => $parsed_route)
			{
				if ($route == "separator" or $route == "param_escape") continue;
				if (strlen($route) != 1) $route = rtrim($route);
				
				try { $routes -> add_route($route, $parsed_route); }
				catch (InternalError $e) { Log::error($e -> getMessage()); }
			}
			$this -> router -> load_routes($routes);
		}

		/**
		 * Main method of Application class.
		 * 
		 * This method does all important work, from given url (uri), using Router instance it gets information about
		 * Controller name and action name, and search for them inside set controller paths (application path), and when
		 * found it instantiate new controller (from given class name) and calls its action (from given action name).
		 * Also all additional parameters which are parsed through url (uri) are handled here.
		 * 
		 * @Note One small bug all parameters are passed to controller method as a Array, not as individual variables
		 * 
		 * @param $uri -> (string) requested uri
		 */
		public function start()
		{
            // get URI from $_SERVER array
            $uri = $_SERVER['REQUEST_URI'];

			try { $parsed_uri = $this -> router -> process_uri($uri); }
			catch (InternalError $e) { Log::error($e -> getMessage()); return; }
			// Here should maybe go some error handler (not only default loading)
			catch (RouteNotFound $e) { $this -> execute_defaults(); return; }

			$controller = isset($parsed_uri["controller"]) ? $parsed_uri["controller"] : null;
			$controller_path = isset($parsed_uri["controller_path"]) ? $this -> path_info -> CONTROLLER_PATH -> value() . "/" .
																		 $parsed_uri["controller_path"] 
																	: null;
			$action = isset($parsed_uri["action"]) ? $parsed_uri["action"] : null;
			$params = isset($parsed_uri["params"]) ? $parsed_uri["params"] : null;
			
			if ($controller_path !== null && $controller !== null && $action !== null)
			{
				if (file_exists($controller_path))
				{
					include_once $controller_path;
					if (class_exists($this -> application_namespace . $controller))
					{
                        $controller = $this -> application_namespace . $controller;
						$controller = new $controller($this -> path_info);
						if (method_exists($controller, $action))
						{
                            try
                            {
							    $controller -> pre_action();
							    if ($params === null) $controller -> $action();
							    else $controller -> $action($params);
							    $controller -> post_action();
                            }
                            catch (Exception $e)
                            {
                                $this -> execute_defaults();
                            }
						}
						else
                        {
							Log::error("Method not found in given class.");
							$this -> execute_defaults();
						}
					}
					else 
                    {
						Log::error("Class not found in given file.");
						$this -> execute_defaults();
					}
				}
				else 
                {
					Log::error("Controller path corrupted, no file found");
					$this -> execute_defaults();
				}
			}
			else
			{
				Log::error("Controller and/or action not propertly set.");
				$this -> execute_defaults();
			}
		}
		
		/**
		 * Private method which run default controller action job
		 */
		private function execute_defaults()
		{
			include_once $this -> path_info -> DEFAULTS_PATH -> value() . $this -> default_controller_path;
			
			$controller = new $this -> default_controller($this -> path_info);
			$action = $this -> default_action;
			
			$controller -> pre_action();
			$controller -> $action();
			$controller -> post_action();
		}
	}

?>
