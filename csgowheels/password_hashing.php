<?php
	
	class PasswordHasher
	{
		private static $base64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';		
		
		private static function join_pass_salt($password, $salt)
		{
			return substr($salt, 0, 7) . substr($password, 0, 4) . substr($salt, 7, 7) 
					. substr($password, 4) . substr($salt, 14);
		}
		
		private static function generate_salt()
		{
			$output = '';
			
			for ($i = 0; $i < 22; $i++)
				if (function_exists('random_int'))
					$output .= PasswordHasher::$base64[random_int(0, strlen(PasswordHasher::$base64) - 1)];
				else
					$output .= PasswordHasher::$base64[mt_rand(0, strlen(PasswordHasher::$base64) - 1)];
			
			return $output;
		}
		
		private static function join_hash_salt($hash, $salt)
		{
			return substr($salt, 0, 7) . substr($hash, 0, 21) . substr($salt, 7, 7) 
					. substr($hash, 21) . substr($salt, 14);
		}
		
		private static function get_salt($hybrid_hash)
		{
			return substr($hybrid_hash, 0, 7) . substr($hybrid_hash, 28, 7) 
					. substr($hybrid_hash, strlen($hybrid_hash) - 8);
		}
		
		public static function new_password($password)
		{
			$salt = PasswordHasher::generate_salt();
			$password = PasswordHasher::join_pass_salt($password, $salt);
			
			$hash = hash('sha512', $password);
			$hash = PasswordHasher::join_hash_salt($hash, $salt);
			
			return $hash;
		}
		
		public static function check_password($password, $hash)
		{
			$salt = PasswordHasher::get_salt($hash);
			$password = PasswordHasher::join_pass_salt($password, $salt);
			
			$new_hash = hash('sha512', $password);
			$new_hash = PasswordHasher::join_hash_salt($new_hash, $salt);
			
			return $new_hash == $hash;
		}
	}

?>