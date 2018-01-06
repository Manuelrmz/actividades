<?php
class Router
{
	protected $_routes;
	protected $_method;
	protected $_path;
	protected $_controller;
	protected $_metodo;
	protected $_args;
	public function __construct()
	{
		if(!include APP_PATH."Route.php"){
           throw new Exception("Error Processing Routes", 1);  
        }
        $this->_method = $_SERVER['REQUEST_METHOD'];
        if(isset($_GET['url']))
			$this->_path = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);
        else
        	$this->_path = DEFAULT_CONTROLLER;
	}
	public function get($ruta,$action = ""){$this->_routes['GET'][$ruta] = $action;}
	public function post($ruta,$action = ""){$this->_routes['POST'][$ruta] = $action;}
	public function put($ruta,$action = ""){$this->_routes['PUT'][$ruta] = $action;}
	public function delete($ruta,$action = ""){$this->_routes['DELETE'][$ruta] = $action;}
	public function match()
	{
	    $_tmp = $this->_routes[$this->_method];
	    if(count($_tmp)>0)
    	{
    		$pathTmp = explode('/',$this->_path);
            $pathTmp = array_filter($pathTmp);
            $this->_controller = strtolower(array_shift($pathTmp));
            $this->_metodo = strtolower(array_shift($pathTmp));
            $this->_args = $pathTmp;
    		foreach ($_tmp as $ruta => $action) 
    		{
				$rutaTmp = explode('/',$ruta);
				if($rutaTmp[0]==$this->_controller)
				{
					if(isset($rutaTmp[1])&&$rutaTmp[1]!="")
					{
						if($this->_metodo!="")
						{
							if($this->_metodo == $rutaTmp[1])
							{
								if($action!="")
								{
									$this->_metodo = $action;
								}
								return;
							}
						}
						else
						{
							if($rutaTmp[1]=='index')
							{
								$this->_metodo = 'index';
								if($action!="")
								{
									$this->_metodo = $action;
								}
								return;
							}
						}
					}
					else
					{
						if($this->_metodo=="" || $this->_metodo =="index")
						{
							$this->_metodo = 'index';
							return;
						}
					}
				}
    		}
    	}
    	$this->_controller = DEFAULT_ERROR;
    	$this->_metodo = 'error520';
	}
	public function getAddressArray()
	{
		return array('controlador'=>$this->_controller,'metodo'=>$this->_metodo,'argumentos'=>$this->_args);
	}
}
?>