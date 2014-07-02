<?php

final class DB {

	private static $connection = null;

	// connect to a database
	public static function start() {
		
		if(self::$connection==null) {
			self::$connection = @mysqli_connect('localhost','root','','test');

			if (mysqli_connect_errno()) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
				die();
			}
		}

	}

	// close a connection
	public static function end() {

		if(self::$connection!=null)
			mysqli_close(self::$connection);

	}

	public static function query($q) {

		return (self::$connection!=null)
					? mysqli_query( self::$connection, $q )
					: array();
	}

	public static function getNext($res) {
		return ($res && count($res)>0)
										? mysqli_fetch_array($res)
										: array();
	}

}