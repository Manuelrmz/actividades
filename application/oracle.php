<?php
class oracle
{
	private $_oracle;
	private $_conn;
	private $_query;
	private $_fetcharray;
	public function conectar($server,$user,$pass,$database)
	{
		@$this->_conn = oci_connect($user,$pass,$server.'/'.$database);
		if($this->_conn)
			return true;
		else
			return false;
	}
	public function query($query,$trans=false)
	{
		$this->_query = oci_parse($this->_conn,$query);
		if($trans)
			return @oci_execute($this->_query,OCI_NO_AUTO_COMMIT);
		else 
			return @oci_execute($this->_query);
	}
	public function fetch_all()
	{
		$this->_fetcharray = array();
		$contador = 1;
		while($array = oci_fetch_row($this->_query))
		{
			$this->_fetcharray[$contador] = $array;
			$contador++;
		}
		return $this->_fetcharray;
	}
	public function fetch_array()
	{
		return oci_fetch_array($this->_query);
	}
	public function fetch_obj()
	{
		return oci_fetch_object($this->_query);
	}
	public function fetch_assoc()
	{
		return oci_fetch_assoc($this->_query);
	}
	public function num_rows()
	{
		return oci_num_rows($this->_query);
	}
	public function rollback()
	{
		oci_rollback($this->_conn);
	}
	public function commit()
	{
		oci_commit($this->_conn);
	}
	public function get_query_error()
	{
		return oci_error($this->_query);
	}
	public function get_connection_error()
	{
		return oci_error();
	}
	public function free_query()
	{
		oci_free_statement($this->_query);
	}
	public function close_connection()
	{
		oci_close($this->_conn);
	}	

}
?>