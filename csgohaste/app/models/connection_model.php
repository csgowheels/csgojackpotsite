<?php

    namespace CSGOwheels;

    class ConnectionModel
    {
        private static $instance = null;
        private $db;

        private function __construct()
        {
            // here should be replaced all parameters (hostname, dbname, username, password), and it should be uncommented
           $this -> db = new \PDO("mysql:host=localhost;dbname=csgo_new", "root", "");
        }

        public static function get_instance()
        {
            if (self::$instance === null)
                self::$instance = new ConnectionModel();
            return self::$instance;
        }

        public function get_db()
        {
            return $this -> db;
        }
    }
?>
