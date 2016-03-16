<?php

	namespace Acko;

	class Log
	{
		private static $success_log_path = null;
		private static $error_log_path = null;
		
		public static function success($string)
		{
			if (Log::$success_log_path !== null)
			{
				$file = fopen(Log::$success_log_path, "a");
				fwrite($file, "SUCCESS: (" . date("d.m.Y H:i:s") . ") \"" . $string . "\"\n");
				fclose($file);
			}
		}
		
		public static function error($string)
		{
			if (Log::$error_log_path !== null)
			{
				$file = fopen(Log::$error_log_path, "a");
				fwrite($file, "ERROR: (" . date("d.m.Y H:i:s") . ") \"" . $string . "\"\n");
				fclose($file);
			}
		}
		
		public static function set_success_log_path($path)
		{
			if (Log::$success_log_path === null)
				Log::$success_log_path = $path;
		}
		
		public static function set_error_log_path($path)
		{
			if (Log::$error_log_path === null)
				Log::$error_log_path = $path;
		}
	}

?>