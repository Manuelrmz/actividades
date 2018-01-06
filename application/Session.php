<?php
class Session
{
	public static function initSession()
	{
		session_start();
	}
	public static function securitySession()
	{
		$codigo_seguridad = sha1($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);
		if(!isset($_SESSION["userData"]))
		{
			session_regenerate_id();
			$_SESSION = array();
			header("Location: /".BASE_DIR.DS."principal/login");
			exit;
		}
		if (!isset($_SESSION["codigo_seguridad"]))
		{
			session_regenerate_id();
			$_SESSION["codigo_seguridad"] = $codigo_seguridad;			
		}
		if (strcmp($_SESSION["codigo_seguridad"],$codigo_seguridad)!== 0) 
		{
			session_regenerate_id();
			$_SESSION = array();
			header("Location: /".BASE_DIR.DS."principal/login");
			exit;
		}
	}
	public static function destroySession()
	{
		session_destroy();
	}
	public static function regenerateId()
	{
		session_regenerate_id();
	}
}
?>