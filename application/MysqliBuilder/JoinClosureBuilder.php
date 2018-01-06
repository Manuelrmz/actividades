<?php
require_once("queryBuilder.php");
class JoinClosureBuilder extends queryBuilder
{
	public function on($key,$operator,$value)
	{
		$joiner = 'AND';
		$this->statements['wheres'][] = compact('key', 'operator', 'value', 'joiner');
		return $this;
	}
	public function orOn($key,$operator,$value)
	{
		$joiner = 'OR';
		$this->statements['wheres'][] = compact('key', 'operator', 'value', 'joiner');
		return $this;
	}
}
?>