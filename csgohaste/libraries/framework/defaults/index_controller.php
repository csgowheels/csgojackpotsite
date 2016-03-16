<?php

	namespace Acko\Defaults;

	class IndexController extends \Acko\Controller
	{
		public function index_action()
		{
            echo self::render_view($this -> path_info -> DEFAULTS_PATH -> value() . "/index.html");
		}
	}

?>
