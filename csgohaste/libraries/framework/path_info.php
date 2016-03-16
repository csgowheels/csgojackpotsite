<?php

    namespace Acko;

    /**
     * This class is more struct then a class
     * 
     * It contains all information about paths (physical and logical paths)
     *
     * @package Acko
     * @author Acko
     */
    class PathInformation
    {
        // FOLLOWING VARIABLES ARE PHYSICAL (on machine) paths
        /** @var SERVER_PATH -> path to project on server machine */
        public $SERVER_PATH;
        /** @var APP_PATH -> path to application folder on server machine */
        public $APP_PATH;
        /** @var CONTROLLER_PATH -> path to controllers folder on server machine */
        public $CONTROLLER_PATH;
        /** @var VIEW_PATH -> path to views folder on server machine */
        public $VIEW_PATH;
        /** @var MODEL_PATH -> path to models folder on server machine */
        public $MODEL_PATH;
        /** @var LIB_PATH -> path to Library folder on server machine */
        public $LIB_PATH;
        /** @var DEFAULTS_PATH -> path to default framework classes on server machine */
        public $DEFAULTS_PATH;

        // FOLLOWING VARIABLES ARE VIRTUAL (url) paths
        /** @var URL -> path to project on browser (url) */
        public $URL;
        /** @var SITE_BASE -> url path to site base */
        public $SITE_BASE;
        /** @var PUBLIC_PATH -> path to publicly accessible files on browser (url) */
        public $PUBLIC_PATH;
        /** @var CSS_PATH -> path to css folder on browser (url) */
        public $CSS_PATH;
        /** @var JS_PATH -> path to js folder on browser (url) */
        public $JS_PATH;

        /**
         * Constructor of class, sets all parameters to emtpy ConstOne instances
         */
        public function __construct()
        {
            $this -> SERVER_PATH = new ConstOne(null);
            $this -> APP_PATH = new ConstOne(null);
            $this -> CONTROLLER_PATH = new ConstOne(null);
            $this -> VIEW_PATH = new ConstOne(null);
            $this -> MODEL_PATH = new ConstOne(null);
            $this -> LIB_PATH = new ConstOne(null);
            $this -> DEFAULTS_PATH = new ConstOne(null);
            $this -> DEFAULTS_PATH = new ConstOne(null);
            $this -> URL = new ConstOne(null);
            $this -> SITE_BASE = new ConstOne(null);
            $this -> PUBLIC_PATH = new ConstOne(null);
            $this -> CSS_PATH = new ConstOne(null);
            $this -> JS_PATH = new ConstOne(null);
        }
    }

?>
