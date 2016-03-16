<?php

	namespace CSGOwheels;
	
	class ChatModel extends CSGOwheelsModel
	{
		private $data = null;
		
		/**
		 * Function to update chat data
		 */
		public function update_data()
		{
			// update chat data (and put it into $data variable)
		}
		
		public function data()
		{
			return $this -> data;
		}
	}
	

?>