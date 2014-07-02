<?php

require 'session.php';
require 'database.php';
require 'template.php';
require 'request.php';

/**
* Bootstrap class for the entire application. It does all the init, it<br>
* delegates the request for handling and in the end it destroys everything.
*/
final class Core
{
	/**
	* Loads configuration file and creates constants from it.
	*/
	private static function initConfig()
	{
		$config_str 	= file_get_contents("app/config.json");
		$config 	= json_decode($config_str,true);

		foreach ($config as $key => $value)
		{
			// TODO : recursion
			define($key, $value);
		}
	}

	/**
	* Main entry point of the application.
	*/
	public static function Start()
	{

		// get configuration data
		self::initConfig();

		// init database
		DB::start();

		// init auth
		Session::start();

		


		// do routing
		Request::route();





		// close database
		DB::end();

		// close session
		// Session::end();
	}

}