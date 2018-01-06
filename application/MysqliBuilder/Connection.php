<?php
class Connection
{
	protected static $connection;
	public static function connect($server = DB_HOST, $user = DB_USER, $pass = DB_PASS, $bd = DB_NAME)
	{
		static::$connection = new mysqli($server,$user,$pass,$bd);
		static::$connection->set_charset("utf8");
	}
	public static function getConnection()
	{
		return static::$connection;
	}
}
?>