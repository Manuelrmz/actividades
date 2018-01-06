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
spl_autoload_register('autoload');
?>