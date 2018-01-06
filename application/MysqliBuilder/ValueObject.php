<?php
class ValueObject
{
	/**
	*
	* @var array
	*
	*/
	protected $allowedParameterType = array("s","i","d","b");
	/**
	*
	* @var array
	*
	*/
	protected $values = array();
	/**
	*
	* @var string
	*
	*/
	protected $types = "";
	/**
	*
	* Construct Class
	*
	*/
	public function __construct(array $values)
	{
		foreach ($values as $key => $value)
		{
			$type = 's';
			if((is_int($value) || ctype_digit($value)) || is_bool($value))
				$type = 'i';
			else if(is_float($value) || is_numeric($value))
				$type = 'd';
			$this->values[] = $value;
			$this->types .= $type;
		}
	}
	/**
	*
	*
	* @return array;
	*
	*/
	public function getValues()
	{
		return $this->values;
	}
	/**
	*
	*
	* @return string;
	*
	*/
	public function getTypes()
	{
		return $this->types;
	}
}
?>