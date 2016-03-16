<?php

	namespace CSGOwheels;
	
	class FeedModel extends CSGOwheelsModel
	{
		private $data = null;
		
		/**
		 * Function to update feed data... (pulling from database and refreashing data variable)
		 */
		public function update_data()
		{
			// update data (pull from feed database information)
			$this -> db();
		}
		
		public function data()
		{
			return $this -> data;
		}
		
	}

?>