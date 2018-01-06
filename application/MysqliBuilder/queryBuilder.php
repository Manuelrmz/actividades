<?php
require_once "Connection.php";
require_once "queryAdapter.php";
require_once "ValueObject.php";
class queryBuilder
{
	/**
	*
	* @var Connection
	*
	*/
	protected $connectionAdapter = null;
	/**
	*
	* @var mysqli connection
	*
	*/
	protected static $connection = null;
	/**
	*
	* @var queryAdapter 
	*
	*/
	protected $adapter = null;
	/**
	*
	* @var array
	*
	*/
	protected $statements = array();
	/**
	*
	* @var mysqli statement
	*
	*/
	protected $mysqliStatement = null;
	/**
	*
	* @var mysqli statement result
	*
	*/
	protected $mysqliResult = null;
	/**
	*
	* @var result type
	*
	*/
	protected $fetchParam = MYSQLI_NUM;
	/**
	*
	* @var array
	*
	*/
	protected $allowedFetchType = array(MYSQLI_ASSOC,MYSQLI_NUM,MYSQLI_BOTH);
	/**
	*
	* @var array
	*
	*/
	protected $allowedQueryTypes = array('select','insert','delete','update','criteriaonly');
	/**
	*
	* @var array
	*
	*/
	protected $error = array('ok'=>false,'string'=>'','value'=>0);
	/**
	*
	* @var string
	*
	*/
	protected $sql = "";
	/**
	*
	* @var array
	*
	*/
	protected $bindings = array();
	/**
	*
	* Construct Funcion Class
	*
	*/
	public function __construct(mysqli $connection = null)
	{
		if(!$connection)
		{
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
			try
			{
				Connection::connect();
				self::$connection = Connection::getConnection();
				$this->queryAdapter = new queryAdapter(self::$connection);
			}
			catch(Exception $e)
			{
				throw $e;
			}
		}
		else
		{
			self::$connection = $connection;
			$this->queryAdapter = new queryAdapter(self::$connection);
		}
	}
	/**
	*
	* @param array | string $fields
	* 
	* @return $this
	*/
	public function select($fields = array("*"))
	{
		if(!is_array($fields))
			$fields = func_get_args();
		$this->addStatements('selects',$fields);
		return $this;
	}
	/**
	*
	* @param array | string $tables
	*
	* @return $this
	*
	*/
	public function table($tables)
	{
		if(!is_array($tables))
			$tables = func_get_args();
		$this->addStatements('tables',$tables);
		return $this;
	}
	/**
	*
	* @param array | string $tables
	*
	* @return $this
	*
	*/
	public function from($tables)
	{
		if(!is_array($tables))
			$tables = func_get_args();
		$this->addStatements('tables',$tables);
		return $this;
	}
	/**
	*
	* @param Closure | string $key
	* @param $operator
	* @param $value
	*
	* @return $this
	*
	*/
	public function where($key, $operator = null, $value = null)
	{
		if(func_num_args() == 2)
		{
			$value = $operator;
			$operator = "=";
		}
		return $this->whereHandler($key, $operator, $value);
	}
	/**
	*
	* @param Closure | string $key
	* @param $operator
	* @param $value
	*
	* @return $this
	*
	*/
	public function orWhere($key, $operator = null, $value = null)
    {
        if(func_num_args() == 2)
		{
			$value = $operator;
			$operator = "=";
		}
		return $this->whereHandler($key, $operator, $value , 'OR');
    }
    /**
	*
	* @param Closure | string $key
	* @param $operator
	* @param $value
	*
	* @return $this
	*
	*/
    public function whereNot($key, $operator = null, $value = null)
    {
        if(func_num_args() == 2)
		{
			$value = $operator;
			$operator = "=";
		}
		return $this->whereHandler($key, $operator, $value, 'AND NOT');
    }
    /**
	*
	* @param Closure | string $key
	* @param $operator
	* @param $value
	*
	* @return $this
	*
	*/
    public function orWhereNot($key, $operator = null, $value = null)
    {
        if(func_num_args() == 2)
		{
			$value = $operator;
			$operator = "=";
		}
		return $this->whereHandler($key, $operator, $value, 'OR NOT');
    }
    /**
	*
	* @param string $key
	* @param $value
	*
	* @return $this->whereHandler
	*
	*/
    public function whereIn($key, $values)
    {
    	if(!is_array($values))
    		$values = array($values);
        return $this->whereHandler($key, 'IN', $values, 'AND');
    }
    /**
	*
	* @param string $key
	* @param $value
	*
	* @return $this->whereHandler
	*
	*/
    public function whereNotIn($key, $values)
    {
    	if(!is_array($values))
    		$values = array($values);
        return $this->whereHandler($key, 'NOT IN', $values, 'AND');
    }
    /**
	*
	* @param string $key
	* @param $value
	*
	* @return $this->whereHandler
	*
	*/
    public function orWhereIn($key, $values)
    {
    	if(!is_array($values))
    		$values = array($values);
        return $this->whereHandler($key, 'IN', $values, 'OR');
    }
    /**
	*
	* @param string $key
	* @param $value
	*
	* @return $this->whereHandler
	*
	*/
	public function orWhereNotIn($key, $values)
    {
    	if(!is_array($values))
    		$values = array($values);
        return $this->whereHandler($key, 'NOT IN', $values, 'OR');
    }
    /**
	*
	* @param string $key
	* @param array $value
	*
	* @return $this->whereHandler
	*
	*/
    public function WhereBetween($key, array $values)
    {
        return $this->whereHandler($key, 'BETWEEN', $values,'AND');
    }
    /**
	*
	* @param string $key
	* @param array $value
	*
	* @return $this->whereHandler
	*
	*/
    public function orWhereBetween($key, array $values)
    {
        return $this->whereHandler($key, 'BETWEEN', $values,'OR');
    }
    /**
	*
	* @param string $key
	*
	* @return $this->whereHandler
	*/
	public function whereNull($key)
    {
        return $this->whereNullHandler($key);
    }
    /**
	*
	* @param string $key
	*
	* @return $this->whereHandler
	*/
	public function whereNotNull($key)
    {
        return $this->whereNullHandler($key, 'NOT');
    }
    /**
	*
	* @param string $key
	*
	* @return $this->whereHandler
	*/
	public function orWhereNull($key)
    {
        return $this->whereNullHandler($key, '', 'or');
    }
    /**
	*
	* @param string $key
	* @param array $value
	*
	* @return $this->whereHandler
	*
	*/
	public function orWhereNotNull($key)
    {
        return $this->whereNullHandler($key, 'NOT', 'or');
    }
    /**
	*
	* @param string $key
	* @param string $prefix
	* @param string $operator
	*
	* @return $this->whereHandler
	*
	*/
    protected function whereNullHandler($key, $prefix = '', $operator = '')
    {
        return $this->{$operator . 'Where'}(array($key,'IS '.$prefix.' NULL'));
    }
    /**
	*
	* @param string | array | Closure $key
	* @param string $operator
	* @param string | array $value
	* @param string $joiner
	*
	* @return $this->whereHandler
	*
	*/
	protected function whereHandler($key, $operator, $value, $joiner = 'AND')
	{
		$this->statements['wheres'][] = compact('key','operator','value','joiner');
		return $this;
	}
	/**
	*
	* @param string || array $field
	*
	* @return $this
	*/
	public function groupBy($field)
    {
        $this->addStatements('groupBys', $field);
        return $this;
    }
    /**
    *
    * @param string $key 
    * @param strint $operator
    * @param string $value
    *
    * @return $this->havingHandler()
    */
  	public function having($key, $operator, $value)
    {
    	if(func_num_args() == 2)
		{
			$value = $operator;
			$operator = '=';
		}
		return $this->havingHandler($key, $operator, $value);
    }
    /**
    *
    * @param string $key 
    * @param strint $operator
    * @param string $value
    *
    * @return $this->havingHandler()
    */
	public function orHaving($key,$operator,$value)
    {
    	if(func_num_args() == 2)
		{
			$value = $operator;
			$operator = '=';
		}
		return $this->havingHandler($key, $operator, $value, 'OR');
    }
    /**
    *
    * @param string $key 
    * @param strint $operator
    * @param string $value
    * @param string $joiner
    *
    * @return $this
    */
    protected function havingHandler($key, $operator, $value, $joiner = 'AND')
    {
		$this->statements['havings'][] = compact('key', 'operator', 'value', 'joiner');
        return $this;
    }
    /**
	*
	* @param string | array $fields
	* @param string $direction
	*
	* @return $this
	*/
	public function orderBy($fields,$direction = 'ASC')
	{
		if(!is_array($fields))
			$fields = array($fields);
		foreach ($fields as $key => $value) 
		{
			$field = $key;
			$type = $value;
			if(is_int($key))
			{
				$field = $value;
				$type = $direction;
			}
			$this->statements["orderBys"][] = compact('field','type');
		}
		return $this;
	}
	/**
	*
	* @param string $limit
	*
	* @return $this
	*/
	public function limit($limit)
	{
		$this->statements["limit"] = $limit;
		return $this;
	}
	/**
	*
	* @param string $offset
	*
	* @return $this
	*/
	public function offset($offset)
    {
        $this->statements['offset'] = $offset;
        return $this;
    }
    /**
	*
	* @param string $sql
	* @param array $bindings
	*
	* @return $this
	*/
    public function query($sql,$bindings)
    {
    	$bindings = new ValueObject($bindings);
    	$this->mysqliStatement = $this->statement($sql,$bindings);
    	$this->mysqliResult = $this->mysqliStatement->get_result();
    	$this->mysqliStatement->close();
		$this->mysqliStatement = null;
    	return $this;
    }
    /**
	*
	* @return $this
	*
	*/
    public function get()
    {
    	$queryObject = $this->createQuery('select');
    	$this->mysqliStatement = $this->statement($queryObject["sql"],$queryObject["bindings"]);
    	$this->mysqliResult = $this->mysqliStatement->get_result();
    	$this->mysqliStatement->close();
		$this->mysqliStatement = null;
    	return $this;
    }
    /**
	*
	* @param string $type
	* @param array $data
	* @param string $queryType
	*
	* @return array
	*
	*/
    public function createQuery($type = 'select',$data = array(),$queryType = '')
    {
    	if(!in_array(strtolower($type),$this->allowedQueryTypes))
    		throw new Exception($type . ' Query type not accepted', 1);
    	$adapter = $this->queryAdapter->$type($this->statements,$data,$queryType);
    	return array('sql'=>$adapter['sql'],'bindings'=>$adapter["bindings"]);	
    }
    /**
	*
	* @param string $sql
	* @param ValueObject $bindings
	*
	* @return mysqli_stmt
	*
	*/
    protected function statement($sql, ValueObject $bindings)
    {
    	$bindingsValues = array();
    	$paramType = '';
    	$this->setSql($sql);
    	$this->setBindings($bindings);
    	//echo 'Imprimimos Query: </br>'.$sql.'</br>';
    	$mysqliStatement = self::$connection->prepare($sql);
    	$bindingsObject = $bindings->getValues();
		$paramType = $bindings->getTypes();
		for($i = 0; $i < sizeof($bindingsObject); $i++)
		{
			$bindingsValues[] = & $bindingsObject[$i];
		}
		array_unshift($bindingsValues,$paramType);
		// echo 'Mostramos los valores de la query: </br>';
		// var_dump($bindingsValues);
		// echo '<br/> Y los tipos de valores: </br>'.$paramType.'</br>';
		if(sizeof($bindingsObject) > 0)
			call_user_func_array(array($mysqliStatement,'bind_param'), $bindingsValues);
		$mysqliStatement->execute();
		$this->statements = array();
		return $mysqliStatement;
    }
    /**
    *
    * @return null || array
    * 
    */
    public function fetch_array()
    {
    	return $this->mysqliResult === null ? null : $this->mysqliResult->fetch_array();
    }
    /**
    *
    * @return null || array
    * 
    */
    public function fetch_assoc()
    {
    	return $this->mysqliResult === null ? null : $this->mysqliResult->fetch_assoc();
    }
    /**
    *
    * @return null || array
    * 
    */
    public function fetch_row()
    {
    	return $this->mysqliResult === null ? null : $this->mysqliResult->fetch_row();
    }
    /**
    *
    * @param integer $mode
    *
    * @return null || array
    */
    public function fetch_all($mode = MYSQLI_ASSOC)
    {
    	$return = array();
    	if(method_exists('mysqli_result','fetch_all'))
    		$return = $this->mysqliResult->fetch_all($mode);
    	else
    	{
    		while($array = $this->mysqliResult->fetch_array($mode))
    		{
    			$return[] = $array;
    		}
    	}
    	return $return;
    }
    /**
    *
    * Get First Row From a statement
    *
    * @return null || array
    */
    public function first()
	{
		$this->limit(1);
        $result = $this->get();
        return $result ? $this->fetch_row() : null;
	}
	/**
    *
    * Get all rows equals to a value
    *
    * @return null | array
    */
	public function findAll($fieldName,$value)
	{
		$this->where($fieldName, '=', $value);
		$result = $this->get();
        return $result ? $this->fetch_all() : null;
	}
	/**
    *
    * Get row equal to a value
    *
    * @return array
    */
	public function find($value,$fieldName = 'id')
	{
		$this->where($fieldName, '=', $value);
        return $this->first();
	}
	/**
    *
    * Get the numbers of rows from a select statement
    *
    * @return integer
    */
	public function count()
	{
		return $this->mysqliResult instanceof mysqli_result ? $this->mysqliResult->num_rows : 0;
	}
	/**
    *
    * @param array $data
    *
    * @return $this->insertHandler()
    */
	public function insert($data)
	{
		return $this->insertHandler($data,'INSERT');
	}
	/**
    *
    * @param array $data
    *
    * @return $this->insertHandler()
    */
	public function insertIgnore($data)
	{
		return $this->insertHandler($data,'INSERT IGNORE');
	}
	/**
    *
    * @param array $data
    *
    * @return $this->insertHandler()
    */
	public function replace($data)
	{
		return $this->insertHandler($data,'REPLACE');
	}
	/**
    *
    * @param array $data
    * @param string $type
    *
    * @return array || null
    */
	protected function insertHandler($data,$type)
	{
		$return = null;
		if(is_array($data))
		{
			if(!is_array(current($data)))
			{
				$query = $this->createQuery('insert',$data,$type);
				$this->mysqliStatement = $this->statement($query['sql'],$query["bindings"]);
				$return = $this->mysqliStatement->affected_rows > 0 ? $this->mysqliStatement->insert_id: null;						
			}
			else
			{
				$return = array();
				for($i = 0; $i < sizeof($data); $i++)
				{
					$query = $this->createQuery('insert',$data[$i],$type);
					$this->mysqliStatement = $this->statement($query['sql'],$query["bindings"]);
					$return[] = $this->mysqliStatement->affected_rows > 0 ? $this->mysqliStatement->insert_id: null;
				}
			}
		}
		return $return;
	}
	/**
	*
	* @param array $data
	*
	* @return integer
	*/
	public function update($data)
	{
		$queryObject = $this->createQuery('update',$data);
		$this->mysqliStatement = $this->statement($queryObject['sql'],$queryObject["bindings"]);
		return $this->mysqliStatement->affected_rows;
	}
	/**
	*
	* @param array $data
	*
	* @return null | integer
	*/
	public function delete()
	{
		$queryObject = $this->createQuery('delete');
		$this->mysqliStatement = $this->statement($queryObject['sql'],$queryObject["bindings"]);
		return $this->mysqliStatement->affected_rows;
	}
	/**
    *
    * @param array $data
    *
    * @return $this
    */
	public function onDuplicateKeyUpdate($data)
	{
		$this->addStatements('updateonduplicate',$data);
		return $this;
	}
	/**
	*
	* @param string || array $table
	* @param string $key
	* @param string $operator
	* @param string $value
	* @param string $type
	*
	* @return $this
	*/
	public function join($table, $key, $operator = null ,$value = null ,$type = 'inner')
	{
		$criteria = null;
		if($key instanceof \Closure)
		{
			$builder = new JoinClosureBuilder();
			$builder = & $builder;
			$key($builder);
			$criteria = $builder;
		}
		else
			$criteria = array($key, $operator, $value);
		$this->statements['joins'][] = compact('type','table','criteria');
		return $this;
	}
	/**
	*
	* @param Closure
	*
	*
	* @return boolean
	*/
	public function transaction(Closure $transaction)
	{
		$return = false;
		try
		{
			self::$connection->autocommit(false);
			$builderObject = new queryBuilder(self::$connection);
			$transaction($builderObject);
			self::$connection->commit();
			$return = true;
		}
		catch(Exception $e)
		{
			self::$connection->rollback();
			$this->setError(true,$e->getMessage(),$e->getCode());
		}
		self::$connection->autocommit(true);
		return $return;
	}
	/**
	* 
	* @param string $key
	* @param string || array $value
	*
	*/
	public function addStatements($key,$value)
	{
		if(!is_array($value))
			$value = array($value);
		if(!array_key_exists($key,$this->statements))
			$this->statements[$key] = $value;
		else
			$this->statements[$key] = array_merge($this->statements[$key],$value);
	}
	/**
	*
	* @return $this->statements
	*
	*/
	public function getStatements()
	{
		return $this->statements;
	}
	/**
	*
	* @param boolean $band
	* @param string $string
	* @param integer $value
	*
	*/
	public function setError($band, $string, $value = 0)
	{
		$this->error['ok'] = (boolean)$band;
		$this->error['string'] = (string)$string;
		$this->error['value'] = (integer)$value;
	}
	/**
	*
	* @return $this->error
	*
	*/
	public function getError()
	{
		return $this->error;
	}
	/**
	*
	* @param mysqli $connection
	*
	* @return $this;
	*/
	public function setConnection(mysqli $connection)
	{
		self::$connection = $connection;
	}
	/**
	*	@return mysqli 
	*/
	public function getConnection()
	{
		return self::$connection;
	}
	/**
	*
	*	@param string
	*
	*/
	public function setSql($sql)
	{
		$this->$sql = $sql;
	}
	/**
	*
	*	@param ValueObject
	*
	*/
	public function setBindings(ValueObject $bindings)
	{
		$this->bindings = $bindings;
	}
	/**
	*	@return string
	*/
	public function getSql()
	{
		return $this->sql;
	}
	/**
	*	@return ValueObject
	*/
	public function getBindings()
	{
		return $this->bindings;
	}
	public function getStatement()
	{
		return $this->mysqliStatement;
	}
}
?>