<?php
require_once PROJECT_ROOT . DIRECTORY_SEPARATOR . 'autoloader.php';
use Medoo\Medoo;
class DbHelper
{
	private static $instance;	
	public static function shared()
	{
		if (null === self::$instance) {
			self::$instance = new Medoo(array('database_type' => 'mysql', 'database_name' => 'appsfor9_fineform', 'server' => 'localhost', 'username' => 'appsfor9_fnf', 'password' => 'F1neform', 'charset' => 'utf8'));
		}		
		return self::$instance;
	}	
	protected function __construct()
	{
	}	
	private function __clone()
	{
	}	
	private function __wakeup()
	{
	}
}
?>