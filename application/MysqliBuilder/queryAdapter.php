<?php
require_once ("JoinClosureBuilder.php");
require_once ("ValueObject.php");
class queryAdapter
{
	/**
	*
	* @var mysqli connection
	*
	*/
	protected static $connection = null;
	/**
	*
	* @var string
	*
	*/
	protected $point = '`';
	public function __construct(mysqli $connection)
	{
		if($connection)
			self::$connection = $connection;
		else
			throw new Exception("No database connection founded", 1);	
	}
	public function select($statements)
	{
		$groupBy = '';
		$orderBy = '';
		$sqlArray = array();
		$bindings = array();
		if(!array_key_exists('tables',$statements))
			throw new Exception("No Tables Founded", 1);
		if(!array_key_exists('selects', $statements))
			$statements["selects"][] = '*';
		//Getting SELECT,FROM and JOIN
		$selects = $this->arrayToString($statements['selects'],', ');
		$tables = $this->arrayToString($statements['tables'],', ');
		$joins = $this->buildJoin($statements);
		$sqlArray = array_merge($sqlArray,array('SELECT' . (isset($statements['distinct']) ? ' DISTINCT' : ''),$selects,'FROM',$tables,$joins));
		//Getting WHERE, GROUP BY AND HAVING
		$wheres = $this->buildCriteria($statements,'wheres','WHERE',true);
		if(isset($statements['groupBys']) && $groupBy = $this->arrayToString($statements['groupBys'],', '))
			$groupBy = 'GROUP BY '.$groupBy;
		$havings = $this->buildCriteria($statements, 'havings', 'HAVING',true);
		$sqlArray = array_merge($sqlArray,array($wheres['sql'],$groupBy,$havings['sql']));
		//Getting ORDER BY, LIMIT, AND OFFSET
		if(isset($statements["orderBys"]) && is_array($statements["orderBys"]))
		{
			foreach ($statements["orderBys"] as $order) 
			{
				$orderBy .= $order["field"] . ' ' . $order["type"] . ', ';
			}
			if($orderBy = trim($orderBy,', '))
				$orderBy = 'ORDER BY '.$orderBy;
		}
		$limit = isset($statements["limit"]) ? 'LIMIT ' . $statements["limit"] : '';
		$offset = isset($statements['offset']) ? 'OFFSET ' . $statements['offset'] : '';
		$sqlArray = array_merge($sqlArray, array($orderBy,$limit,$offset));
		//Getting sql String from sqlArray
		$bindings = array_merge($wheres['bindings'],$havings["bindings"]);
		$bindings = new ValueObject($bindings);
		$sql = $this->queryArrayToString($sqlArray);	
		return compact('sql','bindings');
	}
	public function insert($statements, array $data, $type)
	{
		$sql = '';
		$sqlArray = $bindings = $keys = $values = array();
		if(!isset($statements['tables']) && sizeof($statements['tables']) > 0)
			throw new Exception("No table Founded", 1);
		if(count($data) < 1)
			throw new Exception("No data given", 1);
		$table = end($statements['tables']);
		$keys = array_keys($data);
		for($i = 0; $i < count($keys); $i++)
		{
			$values[] = '?';
			$bindings[] = $data[$keys[$i]];
		}
		$sqlArray = array_merge($sqlArray,array($type . ' INTO',$table,'(' . $this->arrayToString($keys,', ') . ')','VALUES','(' . $this->arrayToString($values,', ') . ')'));
		if(isset($statements["updateonduplicate"]))
		{
			if(sizeof($statements["updateonduplicate"]) > 0)
			{
				$update = $this->getUpdateSetStatement($statements["updateonduplicate"]);
				$sqlArray[] = 'ON DUPLICATE KEY UPDATE ' . $update["sql"];
				$bindings = array_merge($bindings,$update["bindings"]);
			}
			else
				throw new Exception("No Update Parameter to duplicate key founded", 1);
		}
		$bindings = new ValueObject($bindings);
		$sql = $this->queryArrayToString($sqlArray);
		return compact('sql','bindings');
	}
	public function update($statements, array $data)
	{
		$sql = '';
		$sqlArray = array();
		$bindings = array();
		if(!array_key_exists('tables',$statements))
			throw new \Exception("No Tables Founded", 1);
		if(sizeof($data) < 1)
			throw new \Exception("No Data Founded", 1);
		//Table
		$table = end($statements['tables']);
		//Update 
		$updates = $this->getUpdateSetStatement($data);
		//Where
		$wheres = $this->buildCriteria($statements,'wheres','WHERE',true);
		$sqlArray = array_merge($sqlArray,array('UPDATE',$table,'SET '.$updates["sql"],$wheres['sql']));
		$bindings = array_merge($bindings,$updates["bindings"],$wheres["bindings"]);
		$bindings = new ValueObject($bindings);
		$sql = $this->queryArrayToString($sqlArray);	
		return compact('sql','bindings');
	}
	public function getUpdateSetStatement($statements)
	{
		$sql = '';
		$bindings = $keys = array();
		$keys = array_keys($statements);
		$values = array_values($statements);
		for($i = 0; $i < sizeof($keys); $i++)
		{
			$sql .= $keys[$i] . '= ?, ';
			$bindings[] = $values[$i];
		}
		$sql = trim($sql,', ');
		return compact('sql','bindings');

	}
	public function delete($statements)
	{
		$sql = '';
		$sqlArray = array();
		$bindings = array();
		if(!array_key_exists('tables',$statements))
			throw new \Exception("No Tables Founded", 1);
		//Table
		$table = end($statements['tables']);
		//wheres
		$wheres = $this->buildCriteria($statements,'wheres','WHERE',true);
		//Limit
		$limit = isset($statements["limit"]) ? 'LIMIT ' . $statements["limit"] : '';
		$sqlArray = array_merge($sqlArray,array('DELETE FROM',$table,$wheres['sql'],$limit));
		$bindings = array_merge($bindings,$wheres['bindings']);
		$bindings = new ValueObject($bindings);
		$sql = $this->queryArrayToString($sqlArray);
		return compact('sql', 'bindings');
	}
	public function criteriaonly($statements,$bindValues = true)
	{
		$sql = "";
		$bindings = array();
		if(isset($statements["wheres"]))
		{
			$wheres = $this->buildCriteria($statements,"wheres","WHERE",$bindValues);
			$sql = $wheres['sql'];
			$bindings = $wheres['bindings'];
			$sql = preg_replace('/^(\s?WHERE ?)|\s$/i', '', $sql);
		}
		return compact('sql','bindings');
	}
	public function buildCriteria($statements, $key, $type = "WHERE", $bindValues = true)
	{
		$sql = '';
		$bindings = array();
		if(isset($statements[$key]))
		{	
			foreach ($statements[$key] as $statement) 
			{
				//Closure
				if($statement['key'] instanceof \Closure)
				{
					$temporalObject = new queryBuilder(self::$connection);
					$statement["key"]($temporalObject);
					$queryArray = $temporalObject->createQuery('criteriaonly',true);
					$bindings = array_merge($bindings,$queryArray["bindings"]);
					$sql .= $statement["joiner"] . ' (' . $queryArray["sql"] . ') ';
				}
				//IN , BETWEEN
				else if(is_array($statement['value']))
				{
					$sql .= $statement["joiner"] . ' ' . $statement["key"] . ' ' . $statement["operator"];
					switch ($statement["operator"]) 
					{
						case 'BETWEEN':
							$bindings = array_merge($bindings,$statement["value"]);
							$sql .= ' ? AND ? ';
						break;
						default:
							$valueString = '';
							foreach ($statement["value"] as $statementValue) 
							{
								$valueString .= '?, ';
								$bindings[] = $statementValue;	
							}
							$valueString = trim($valueString,', ');
							$sql .= ' (' . $valueString . ') ';
						break;
					}
				}
				//IS NULL
				else if(is_array($statement['key']))
					$sql .= $statement["jointer"] . ' ' . $statement["key"][0] . ' ' .$statement["key"][1];
				//Where, Or where and negative
				else
				{
					if($bindValues)
					{
						$bindings[] = $statement["value"];
						$sql .= $statement["joiner"] . ' ' . $statement["key"] . ' ' . $statement["operator"] . ' ? ';
					}
					else
						$sql .= $statement["joiner"] . ' ' . $statement["key"] . ' ' . $statement["operator"] . ' ' . $statement["value"] . ' ';
				}
			}
			$sql = preg_replace('/^(\s?AND ?|\s?OR ?)|\s$/i', '', $sql);
			if($sql)
				$sql = $type . ' ' .$sql;
		}
		return compact('sql','bindings');
	}
	public function buildJoin($statements)
	{
		$sql = '';
		if(isset($statements['joins']) && is_array($statements['joins']))
		{
			foreach ($statements['joins'] as $statement) 
			{
				$table = '';
				$sqlCriteria = '';
				if(is_array($statement['table']))
					$table = $statement['table'][0] . ' AS ' .$statement['table'][1];
				else
					$table = $statement['table'];
				if($statement['criteria'] instanceof JoinClosureBuilder)
					$sqlCriteria = $statement["criteria"]->createQuery('criteriaonly',false)['sql'];
				else
					$sqlCriteria = $statement["criteria"][0] . ' ' . $statement["criteria"][1] . ' ' . $statement["criteria"][2];
				$sql = $this->queryArrayToString(array($sql,strtoupper($statement['type']),'JOIN',$table,'ON',$sqlCriteria));
			}
		}
		return $sql;
	}
	public function arrayToString(array $parts, $glue)
	{
		$string = '';
		foreach ($parts as $key => $part) 
		{
			if(!is_int($key))
				$part = $key . ' AS ' . $part;
			$string .= $part . $glue;
		}
		return trim($string,$glue);
	}
	public function queryArrayToString(array $parts)
	{
		$string = '';
		for($i = 0; $i < sizeof($parts); $i++)
		{
			$string = trim($string) . ' ' . trim($parts[$i]);
		}
		return trim($string);
	}
}
?>