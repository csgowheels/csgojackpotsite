<?php

	/*
	 * 	This module contains Controller class, it is abstract class which should be inherited in
	 * 	your own controllers.
	 * 
	 * 	It should contain all methods which are in common for every controller
	 * 
	 * 	@autor Acko
	 * 	@date_created 06.09.2015.
	 * 	@last_changed 06.09.2015. 02:30
	 */

	namespace Acko;
	
    /**
     * Base class for building MVC pattern based application using this framework
     *
     * All controller classes in application should inherit (directly or indirectly) this class,
     * in order for framework to function as planned.
     *
     * It contains one static method for proccessing html code (view part), replacing all placeholders
     * with appropriate values passed to it. And two methods which are incomen for each controller.
     *
     * @package Acko
     * @author Acko
     */
	abstract class Controller
	{
        /** @var $path_info -> Acko\PathInformation instance wich stores path information for application */
        protected $path_info = null;

        /**
         * Constructor of class, sets path_info variable
         */
        public function __construct($path_info)
        {
            $this -> path_info = $path_info;
        }

        /**
         * This method is main usage of Controller class.
         *
         * It takes two parameters first one should be path of view (html) file on server
         * and second one are paremeters which you use to change all placeholders inside html file.
         *
         * Main idea of this method is separating view and Controller/model part (html and php),
         * so all html files contains only html code, and nothing else. And this part of code will create dynamic
         * which is necessary in modern applications.
         *
         * @param $view_path -> (string) path to the html file which should be runned thorough parser and returned 
         *                          with all parameters replaced
         * @param $parameters -> (array) should contain all placeholders (lowercased) as keys and string values for them
         *                          it could contain html code (if few views are combined in one) default it is empty array
         *                          If any key is missing it will just be skipped and on placeholder place will be inserted
         *                          empty string.
         * @param $multiply -> (int) default value 1, describes how many times given html should be worked through
         *                          (etc. if you have html code for one comment, and want 10 comments to put in page
         *                           you will not call 10 times same method which reads file's, because it is slower,
         *                           you will call this one with multiply = 10, and with ajusted parameters param).
         *
         * @throws \Exception -> if file doesn't exist on given path.
         *
         * @return (string) with all html code (all placeholders changed with sent values),
         *          or (array of strings) if multiply is set to more then one.
         */
		public static function render_view($view_path, $parameters=array(), $multiply=1)
		{
            if (!file_exists($view_path))
                throw new \Exception("View you requested does not exist on system");

			$html = file_get_contents($view_path);

			preg_match_all("/(?:[{]{2}\s+)(\w+)(?:\s+[}]{2})/", $html, $matches);
			$matches = $matches[1]; // get only content match (not whole match {{ text }} => text)

            if ($multiply > 1)
            {
                $htmls = array();
                for ($i = 0; $i < $multiply; $i++)
                {
                    $htmls[$i] = $html;
                    foreach ($matches as $match)
                        $htmls[$i] = preg_replace("/([{]{2}\s+)(". $match . ")(\s+[}]{2})/", isset($parameters[$i][strtolower($match)]) 
                                                                                                    ?    $parameters[$i][strtolower($match)]
                                                                                                    :    "", 
                                                                                    $htmls[$i]);
                }
                return $htmls;
            }
            else
            {
                foreach ($matches as $match)
                    $html = preg_replace("/([{]{2}\s+)(". $match . ")(\s+[}]{2})/", isset($parameters[strtolower($match)]) 
                                                                                                ?    $parameters[strtolower($match)]
                                                                                                :    "", 
                                                                                $html);
                return $html;
            }
		}
		
        /**
         * This method should be rewriten if you need to do something before executing any action in your controller.
         *
         * By default this is empty method (not abstract because sometimes you don't need this feature so why would you
         * allways need to rewrite it?). It should be used for some database connection, session checking or something alike.
         */
		public function pre_action() {}
		
        /**
         * This method should be rewriten if you need to do something after executing any action in your controller.
         *
         * By default this is empty method (not abstract same as {@link pre_action()}). It should be used for some connection
         * closing, or something alike.
         */
		public function post_action() {}
	}

?>
