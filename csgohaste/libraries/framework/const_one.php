<?php 

    namespace Acko;

    /**
     * Class which simulates constant value, it can be set only once
     *
     * Data (value) could be pulled anytime, but can be set only once.
     * 
     * @package Acko
     * @author Acko
     */
    class ConstOne
    {
        /** @var $data -> data to be stored */
        private $data;

        /**
         * Constructor of the class
         *
         * Sets data to initial value, if nothign sent it is null
         *
         * @param $data -> data to be set (default null)
         */
        public function __construct($data=null)
        {
            $this -> data  = $data;
        }

        /**
         * Setter method for data of class.
         *
         * It only works if data isn't set already
         *
         * @param $data -> data to be sent.
         */
        public function set_value($data)
        {
            if ($this -> data === null)
                $this -> data = $data;
        }

        /**
         * Getter method for data which instance is storing
         *
         * @return data which instance stores (could be null)
         */
        public function value()
        {
            return $this -> data;
        }
    }
?>
