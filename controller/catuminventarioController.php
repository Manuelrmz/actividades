<?php
class catuminventarioController extends Controller
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
	    $this->unidadMedida($_POST);
	    $condi = true;
	    $condi = $condi && $this->_validar->MinMax($this->_data["nombre"],1,100,"Nombre");
	    if($condi)
	    {
			$um = inventarioUM::insert(array('nombre'=>$this->_data["nombre"],'estado'=>($this->_data["estado"] ? 1 : 0)));
			if($um)
			{
				$this->_return["msg"] = "Unidad de Medida guardada correctamente";
				$this->_return["ok"] = true;
				$this->_return["id"] = $um;
			}
			else
				$this->_return["msg"] = "Ocurrio un error insertando la nueva unidad de medida";
	    }
	    else
			$this->_return["msg"] = $this->_validar->getWarnings();
	    echo json_encode($this->_return);
	}
	public function update()
	{
		Session::regenerateId();
	    Session::securitySession();
	    $this->unidadMedida($_POST);
	    $condi = true;
	    $condi = $condi && $this->_validar->MinMax($this->_data["nombre"],1,100,"Nombre");
	    if($condi)
	    {
			$um = inventarioUM::where('id',$this->_data["id"])->update(array('nombre'=>$this->_data["nombre"],'estado'=>($this->_data["estado"] ? 1 : 0)));
			if($um)
			{
				$this->_return["ok"] = true;
				$this->_return["msg"] = "Unidad de medida modificada correctamente";
			}
			else
				$this->_return["msg"] = "Ocurrio un error actualizando la unidad de medida";
	    }
	    else
			$this->_return["msg"] = $this->_validar->getWarnings();
	    echo json_encode($this->_return);
	}
	public function getForTable()
	{
		Session::regenerateId();
	    Session::securitySession();
	    $um = inventarioUM::select(array('id','nombre','IF(estado = 1,true,false)'=>'estado'))->orderBy('nombre','ASC')->get()->fetch_all();
	    if($um)
	    {
			$this->_return["msg"] = $um;
			$this->_return["ok"] = true;
	    }
	    else
			$this->_return["msg"] = "Unidades de Medida no encontradas";
	    echo json_encode($this->_return);
	}
	public function getForComboBox()
	{
		Session::regenerateId();
		Session::securitySession();
		$um = inventarioUM::select(array('id'=>'value','nombre'=>'text'))->where('estado','1')->get()->orderBy('nombre','ASC')->fetch_all();
		if($um)
		{
			$this->_return["msg"] = $um;
			$this->_return["ok"] = true;
		}
		else
	  		$this->_return["msg"] = "Unidades de Medida no encontradas";
		echo json_encode($this->_return);
	}
	public function unidadMedida($data)
	{
		$this->_data["id"] = isset($data["id"]) ? (integer)$data["id"] : 0; 
		$this->_data["nombre"] = isset($data["nombre"]) ? $data["nombre"] : "";
		$this->_data["estado"] = isset($data["estado"]) ? ($data["estado"] == "true" ? 1 : (integer)$data["estado"]) : 0;
	}
}
?>