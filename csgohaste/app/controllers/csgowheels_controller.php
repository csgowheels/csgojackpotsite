<?php

    namespace CSGOwheels;

    abstract class CSGOwheelsController extends \Acko\Controller
    {
        protected function get_view_path($view_postfix)
        {
            return $this -> path_info -> VIEW_PATH -> value() . $view_postfix;
        }

        protected function include_model($model)
        {
            include_once ($this -> path_info -> MODEL_PATH -> value() . "/" . $model . "_model.php");
        }
    }

?>
