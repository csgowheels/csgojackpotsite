<?php

    namespace CSGOwheels;

    abstract class CSGOwheelsModel extends \Acko\Model
    {
        public function db()
        {
            // this maybe should be handled different (more generically)
            include_once (APP_PATH . '/models/connection_model.php');
            return ConnectionModel :: get_instance() -> get_db();
        }
    }

?>
