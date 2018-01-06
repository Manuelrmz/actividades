<?php
function autoload($class)
{
	if(class_exists($class,false))
		return '';
	else
	{
		if(is_readable(MODEL_PATH . $class . '.php'))
    		require_once(MODEL_PATH . $class . '.php');
	}
}
function loadController($class)
{
	if(class_exists($class,false))
		return '';
	else
	{
		if(is_readable(CONTROLLER_PATH . $class . '.php'))
    		require_once(CONTROLLER_PATH . $class . '.php');
	}
}
spl_autoload_register('autoload');
spl_autoload_register('loadController');
?>