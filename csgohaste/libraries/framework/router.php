<?php

	/*
	 * 	This module contains Router class which is used for url parsing, and dealing with requests.
	 * 
	 * 	It is part of Framework, application uses this class for url manipulation.
	 * 
	 * 	@autor Acko
	 * 	@date_created 06.09.2015.
	 * 	@last_changed 06.09.2015. 04:20
	 */
	 
	 namespace Acko;
	 
	 /**
	  * Router class used as a route controller, and some kind of facade (pattern) for RoutesContainer.
	  * 
	  * This class contains information about name postfixes (controller name postfix, action name postfix ...),
	  * and contains its own RoutesContainer instance which is used for url (uri) parsing. When instantiated
	  * Router object contain all empty fields which should be filled with data using appropriate (setter) methods.
	  * 
	  * Main use of this class is through its process_uri() method which processes given (requested) url (uri) and
	  * returns all information about controller name, path, action name and all necessary information.
	  * 
	  * @package Acko
	  * @author Acko
	  */
	 class Router
	 {
	 	private $server_prefix;
		private $routes;
		private $controller_postfix;
		private $controller_path_postfix;
		private $action_postfix;

		/**
		 * Constructor of class, sets all private fields to empty fields.
		 */
	 	public function __construct()
		{
			$this -> server_prefix = "";
			$this -> controller_postfix = "";
			$this -> controller_path_postfix = "";
			$this -> action_postfix = "";
			$this -> routes = null;
		}
		
		/**
		 * Setter method for server prefix.
		 * 
		 * @Note 
		 * 		server prefix is a string which is removed from each url (uri) passed to router,
		 * 		it is used if your application is running in some subfolder on server.
		 * 		For example:
		 * 			if "www.acko.com/tutorials/mvcpattern/" is home addres of my application
		 * 			$_SERVER['REQUEST_URI'] would be "/tutorials/mvcpattern/...", and because I don't need
		 * 			that prefix for my application routing, I would set server prefix to "/tutorials/mvcpattern"
		 * 			and then my uri after removing server prefix would be "/" for home and "/.." for anything else.
		 * 
		 * @param $server_prefix -> (string) server prefix.
		 */
		public function set_server_prefix($server_prefix)
		{
			$this -> server_prefix = $server_prefix;
		}
		
		/**
		 * Setter method for routes (RouteContainer object).
		 * 
		 * @param $routes_container -> (RouteContainer instance) routes for current Router instance.
		 */
		public function load_routes($routes_container)
		{
			$this -> routes = $routes_container;
		}
		
		/**
		 * Setter method for controller postfix.
		 * 
		 * @param $postfix -> (string) postfix for controller class name.
		 */
		public function set_controller_postfix($postfix)
		{
			$this -> controller_postfix = $postfix;
		}
		
		/**
		 * Setter method for controller path postfix.
		 * 
		 * @param $postfix -> (string) postfix for controller class containing file path.
		 */
		public function set_controller_path_postfix($postfix)
		{
			$this -> controller_path_postfix = $postfix;
		}
		
		/**
		 * Setter method for action postfix.
		 * 
		 * @param $postfix -> (string) postfix for action name.
		 */
		public function set_action_postfix($postfix)
		{
			$this -> action_postfix = $postfix;
		}
		
		/**
		 * Main Router functionality.
		 * 
		 * This method accepts requested uri, and do all additional checking and fixing, and then
		 * using its RoutesContainer instance gets appropriate uri replacement and then parsing that
		 * and using controller name postfix, path postfix, action name postfix it creates full
		 * controller class name, controller class file path, action name, and additional parameters,
		 * which all get returned in the end.
		 * 
		 * @param $uri -> requested uri which should be parsed and routed.
		 * 
		 * @return array containing controller name (as "controller"), controller class file path (as "controller_path"),
		 * 		action name (as "action") and array of parameters (as "params").
		 * 
		 * @throws InternalError -> if routes are not set (not instantiated) and method is called. 
		 * @throws RouteNotFound -> if it can not find concrete route.
		 */
		public function process_uri($uri)
		{
			// remove server prefix (if any exists)
			if (substr($uri, 0, strlen($this -> server_prefix)) === $this -> server_prefix)
				$uri = substr($uri, strlen($this -> server_prefix));

            // fix uri (handle errors)
            $uri = $this -> check_uri($uri);

			if ($this -> routes === null)	
				throw new InternalError("Routes not defined...");
			
			$route = $this -> routes -> get_route($uri);
			if ($route === null)
				throw new RouteNotFound("Route not found");
			
			$arr = explode($this -> routes -> separator, $route);
			
			return array("controller" 		=> isset($arr[0]) ? ucfirst($arr[0]) . $this -> controller_postfix : "",
						 "controller_path" 	=> isset($arr[0]) ? $arr[0] . $this -> controller_path_postfix : "",
						 "action"	 		=> isset($arr[1]) ? $arr[1] . $this -> action_postfix : "",
						 "params" 			=> isset($arr[2]) ? array_slice($arr, 2) : array());
		}

        /**
         * This method fixes uri passed to router, by checking for special characters inside and or extensions (.php)
         *
         * First it calls rawurlencode (php built-in method) for replacing all special characters with appropriate
         * replacements, and then finds all extensions, and delete those words from uri
         *
         * @param $uri -> uri to be checked and fixed
         *
         * @return fixed uri
         */
        private function check_uri($uri)
        {
            $uri = rawurlencode($uri);
            // Replacing back %2F (mark for '/') because of RouteContainer parsing later on
            $uri = preg_replace("/%2F/", "/", $uri);
            if (preg_match_all("(\..*)", $uri, $matches))
                foreach ($matches as $match)
                    $uri = preg_replace("/\/[a-zA-Z0-9\-\_]*" . $match[0] . "/", "", $uri);

            return $uri;
        }

	 }

?>
