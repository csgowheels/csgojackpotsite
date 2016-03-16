<?php

	/*
	 * 	This module contains RouterContainer class which is used for storing routes which you should
	 * 	define in your application.
	 * 
	 * 	It is part of Framework, it is used just as container for route, rewrite pair, with separator
	 * 
	 * 	Usage: 
	 * 		$obj = new Acko\RoutesContainer('#');
	 * 		$obj -> add_route('/', 'index#welcome');
	 * 
	 * 	You can not add two or more actions for one route (TODO maybe fix that)
	 * 
	 * 	@autor Acko
	 * 	@date_created 06.09.2015.
	 * 	@last_changed 06.09.2015. 04:20
	 */

	namespace Acko;

	/**
	 * This class is used for low level url (uri) parsing and routing.
	 * 
	 * Instance of this class contains all routes (loaded from configuration or added manually) and appropriate rewrites for them.
	 * It handles all low level string (url/uri) manipulations, such as finding escape sequences in routes and rewrites,
	 * connecting them, and eventually switching concrete values in appropriate order when parsing new route.
	 * 
	 * @package Acko
	 * @author Acko
	 */
	class RoutesContainer
	{
		/**
		 * Class constructor, set instance variables to default values (if values are not sent)
		 */	
		public function __construct($separator = '#', $param_escape = '!:!')
		{
			$this -> routes = array();
			$this -> route_params = array();
			$this -> separator = $separator;
			$this -> param_escape = $param_escape;
		}
		
		/**
		 * Method for adding new route-rewrite pair.
		 * 
		 * First it finds all escape sequences in route string and matches them with appropriate escape sequences
		 * in rewrite string (using match_params method).
		 * 
		 * Route string becomes stripped to the first escape sequence and like that added to array of routes,
		 * if same named key already exists method throws an Exception. 
		 * (@Note same named route can exist if you add two same routes with different parameters)
		 * Rewrite string gets changed too, and new changed string is being pushed along side with (route) key into
		 * route array. (More about route and rewrite string changes in {@link match_params} method).
		 * 
		 * @param $route -> (string) which requested uri should match.
		 * @param $rewrite  -> (string) with which that uri should be replaced before parsing for controller/action information.
		 * 
		 * @throws InternalError -> if key already exists (meaning that same base route already exists in array).
		 */
		public function add_route($route, $rewrite)
		{
			$fixed = $this -> match_params($route, $rewrite);
			
			if (array_key_exists($route, $this -> routes))
				throw new InternalError("Key already exists. (Route: '" . $route . "', Route-base: '" . $fixed["route"] . "')"); 
			
			$this -> routes[$fixed["route"]] = $fixed["rewrite"];
			$this -> route_params[$fixed["route"]] = $fixed["params"];
		}
		
		/**
		 * Method for getting route rewrite (from requested uri)
		 * 
		 * It starts from full uri sent to method (removing only right '/' sing(s)) and tries to find it in route table,
		 * if it can't find it, it strips last part of uri (/a/b/*x*) and again last right '/' sign and tries again,
		 * storing stripped part in side array. When match is found in route table it calls method which will place
		 * all stripped parameters in appropriate places in matching rewrite string and returns that.
		 * 
		 * @param $route -> (string) requested uri which should be routed according to routing table.
		 * 
		 * @return rewrite string with all parameters replaced (if any) if route found, null otherwise.
		 */
		public function get_route($route)
		{
			$base = strlen($route) > 1 ? rtrim($route, '/') : $route;
			
			$params = array();
			while (!array_key_exists($base, $this -> routes) && strlen($base) > 0)
			{
				$index = strrpos($base, "/");
				if ($index === FALSE)
					break;
				array_push($params, substr($base, $index + 1));
				$base = substr($base, 0, $index);
			}
			if (array_key_exists($base, $this -> routes))
				return $this -> replace_params($this -> routes[$base], $params, $base);
			
			return null;
		}
		
		/**
		 * This private method is used for escape sequence marked parameters in route-rewrite string pairs
		 * 
		 * In order to make things easier for string manipulation, first of all which happens is each parameter
		 * (marked with escape sequence) from route string is pushed to side array (beginning from left), then
		 * all those parameters were found in rewrite string and replaced with appropriate index from side array
		 * (0 is first parameter when reading from left to right). After that route string is stripped from first
		 * occurrence of escape sequence until the end.
		 * 
		 * Those changed route and rewrite string are returned inside array.
		 * 
		 * @param $route -> (string) which should match requested uri.
		 * @param $rewrite -> (string) which requested uri should be rewritten to.
		 * 
		 * @return array containing new route string (as "route") and new rewrite string (as "rewrite")
		 * 				and array of parameters found in route string (as "params").
		 * 
		 * @throws InternalError -> if route/rewrite parameters (marked with escape sequence) don't match
		 */
		private function match_params($route, $rewrite)
		{
			$params = array();
			// finding all parameters in route string (marked with escape sequence) and storing them into $params
			$first_index = $start_index = strpos($route, $this -> param_escape);
			while ($start_index !== FALSE)
			{
				$end_index = strpos(substr($route, $start_index + strlen($this -> param_escape)), $this -> param_escape);
				array_push($params, substr($route, $start_index + strlen($this -> param_escape), $end_index));
				$start_index = $start_index + $end_index + 2 * strlen($this -> param_escape) < strlen($route) 
													?	$start_index + $end_index + 2 * strlen($this -> param_escape) 
															+ strpos(substr($route, $start_index + $end_index + 2 * strlen($this -> param_escape)),
																					 $this -> param_escape) 
													: FALSE; // checks for end of string
			}
			// finding and replacing all parameters found in route string, inside rewrite string
			$start_index = strpos($rewrite, $this -> param_escape);
			$i = 0;
			while ($start_index !== FALSE)
			{
				$end_index = strpos(substr($rewrite, $start_index + strlen($this -> param_escape)), $this -> param_escape);
				$rewrite = substr($rewrite, 0, $start_index) . array_search(substr($rewrite, $start_index + strlen($this -> param_escape),
																				$end_index), $params)
									. ($start_index + $end_index + 2 * strlen($this -> param_escape) < strlen($rewrite) 
																	? substr($rewrite, $start_index + $end_index + 2 * strlen($this -> param_escape))
																	: "");
				$start_index = strpos($rewrite, $this -> param_escape);
				$i++;
			}
			
			// check if route/rewrite parameters don't match
			if ($i != count($params))
				throw new InternalError("Route/Rewrite parameters don't match. (at Route: '" . $route . "')");
			
			if ($first_index !== FALSE && $first_index != "")
				$route = substr($route, 0, $first_index);
			
			$route = strlen($route) > 1 ? rtrim($route, '/') : $route;
			
			return array("route" => $route, "rewrite" => $rewrite, "params" => $params);
		}

		/**
		 * This private method is used for replacing rewrite's changed parameters (indexes of parameters from left to right)
		 * with concrete parameters passed to method.
		 * 
		 * This method is called when get_route method is called, and when base for routing is found, then, this method
		 * gets rewrite answer along with the parameter array which should be replaced inside rewrite string.
		 * Parameters are sorted in opposite order (because of stripping last part of uri in get_route method).
		 * 
		 * @param $rewrite -> string which is got from routing table for given base.
		 * @param $params -> array of parameters which should be properly replaced in rewrite string.
		 * @param $route -> route for which is rewrite found.
		 * 
		 * @return new rewrite string with all given parameters replaced.
		 */		
		private function replace_params($rewrite, $params, $route)
		{
			for ($i = 0; $i < count($params); $i++)
			{
				$position = strpos($rewrite, "$i");
				if ($position === FALSE)
					break; // more parameters passed in url then found in rewrite, just ignore them.
				$rewrite = substr($rewrite, 0, $position) . $params[count($params) - 1 - $i] . 
									( $position + 1 < strlen($rewrite) ? substr($rewrite, $position + 1) : "" );
			}
			// checks if all parameters which should be matched are matched (this may should be delegated to router for some default values)
			if ($i != count($this -> route_params [$route]))
				for ($j = $i; $j < count($this -> route_params[$route]); $j++)
				{
					$position = strpos($rewrite, "$i");
					$rewrite = substr($rewrite, 0, $position) . "null" . ( $position + 1 < strlen($rewrite) ? substr($rewrite, $position + 1) : "" );
				}
			
			return $rewrite;
		}
	}

?>