<?php
class catmarcainventarioController extends Controller
{
	private $_data = array();
	private $_return = array('ok'=>false,'msg'=>'');
	public function __contruct()
	{
		parent::__contruct();
	}
	public function new()
	{
		Session::regenerateId();
	    Session::securitySession();
	    $this->marca($_POST);
	    $condi = true;
	    $condi = $condi && $this->_validar->MinMax($this->_data["nombre"],1,100,"Nombre");
	    if($condi)
	    {
			$marca = inventarioMarca::insert(array('nombre'=>$this->_data["nombre"],'estado'=>($this->_data["estado"] ? 1 : 0)));
			if($marca)
			{
				$this->_return["msg"] = "Marca guardada correctamente";
				$this->_return["ok"] = true;
				$this->_return["id"] = $marca;
			}
			else
				$this->_return["msg"] = "Ocurrio un error insertando el nuevo tipo de equipo";
	    }
	    else
			$this->_return["msg"] = $this->_validar->getWarnings();
	    echo json_encode($this->_return);
	}
	public function update()
	{
		Session::regenerateId();
	    Session::securitySession();
	    $this->marca($_POST);
	    $condi = true;
	    $condi = $condi && $this->_validar->MinMax($this->_data["nombre"],1,100,"Nombre");
	    if($condi)
	    {
			$marca = inventarioMarca::where('id',$this->_data["id"])->update(array('nombre'=>$this->_data["nombre"],'estado'=>($this->_data["estado"] ? 1 : 0)));
			if($marca)
			{
				$this->_return["ok"] = true;
				$this->_return["msg"] = "Marca modificada correctamente";
			}
			else
	        	$this->_return["msg"] = "Ocurrio un error actualizando la marca";
	    }
	    else
			$this->_return["msg"] = $this->_validar->getWarnings();
	    echo json_encode($this->_return);
	}
	public function getForTable()
	{
		Session::regenerateId();
	    Session::securitySession();
	    $marcas = inventarioMarca::select(array('id','nombre','IF(estado = 1,true,false)'=>'estado'))->get()->fetch_all();
	    if($marcas)
	    {
			$this->_return["msg"] = $marcas;
			$this->_return["ok"] = true;
	    }
	    else
			$this->_return["msg"] = "Marcas no encontradas";
	    echo json_encode($this->_return);
	}
	public function getForComboBox()
	{
		Session::regenerateId();
		Session::securitySession();
		$marcas = inventarioMarca::select(array('id'=>'value','nombre'=>'text'))->where('estado','1')->orderBy('nombre','ASC')->get()->fetch_all();
		if($marcas)
		{
			$this->_return["msg"] = $marcas;
			$this->_return["ok"] = true;
		}
		else
			$this->_return["msg"] = "Marcas no encontradas";
		echo json_encode($this->_return);
	}
	public function marca($data)
	{
		$this->_data["id"] = isset($data["id"]) ? (integer)$data["id"] : 0; 
		$this->_data["nombre"] = isset($data["nombre"]) ? $data["nombre"] : "";
		$this->_data["estado"] = isset($data["estado"]) ? ($data["estado"] == "true" ? 1 : (integer)$data["estado"]) : 0;
	}
}
?>