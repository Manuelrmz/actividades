<?php
class Model
{
	/**
	*
	* queryBuilder Object
	*
	*/
	protected static $builderInstance;
	/**
	*
	* @param $method
	* @param $args
	*
	*
	* @return mixed
	*/
	public static function __callStatic($method,$args)
	{
		if(!static::$builderInstance)
			static::$builderInstance = new queryBuilder();
		$callerClass = get_called_class();
		static::$builderInstance->addStatements('tables',$callerClass);
		if(method_exists(static::$builderInstance,$method))
			return call_user_func_array(array(static::$builderInstance,$method),$args);
		else 
			return null;
	}
}
?>