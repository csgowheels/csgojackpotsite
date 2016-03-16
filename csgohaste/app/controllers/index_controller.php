<?php

    namespace CSGOwheels;

	class IndexController extends CSGOwheelsController
	{
		private $logged = false; // steamId if someone is logged
		
		// model objects
		private $index_model = null;
		private $feed = null;
		private $chat = null;
		private $wheel = null;
		private $inventory = null;
		
		/**
		 * Test function
		 */
		public function welcome_action()
		{
            $params = array("title"          => "Initial Test",
                            "content"        => "Test...",
                            "stylesheet"     => $this -> path_info -> CSS_PATH -> value() . "/index/welcome/index.css");
            $html = $this -> render_view($this -> get_view_path("/index/welcome.html"),  $params);
            echo $html;
		}
		
		/**
		 * Function designed to be called when user opens main page.
		 */
		public function main_action()
		{
			$this -> start();
			$this -> check_logged();
		}
		
		/**
		 * This function should handle all initial settings for instance when page requested.
		 * 
		 * It starts session (for logging checks and similar stuff). Also creates global models.
		 */
		private function start()
		{
			// start session to have it through whole program
			@session_start();
			
			// handle index_model creation (mainly for database)
			$this -> include_model("index");
			$this -> index_model = new IndexModel();
			// $this -> db = $this -> index_model -> db();
			
			// create feed, chat and wheel objects (model objects)
			$this -> include_model("feed");
			$this -> feed = new FeedModel();
			
			$this -> include_model("chat");
			$this -> chat = new ChatModel();
			
			$this -> include_model("wheel");
			$this -> wheel = new WheelModel();
		}
		
		private function check_logged()
		{
			if (isset($_SESSION["userSteamId"]))
				$this -> logged = true;
			else
				$this -> logged = false;
		}
	}

?>
