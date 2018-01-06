<?php
class asignacionController extends Controller
{
	private $_data;
	public function __construct()
	{
		parent::__construct();
	}
	public function getAsignaciones()
	{
		Session::regenerateId();
                Session::securitySession(); 
                $this->validatePermissions('radioscat');
                $asignaciones = asignacion::select(array('id','nombre','activo'))->get()->fetch_all();
                if($asignaciones)
                	echo json_encode(array('ok'=>true,'msg'=>$asignaciones));
                else
                	echo json_encode(array('ok'=>false,'msg'=>"No se lograron obtener las asignaciones"));
	}
        public function getAsignacionesActivas()
        {
                Session::regenerateId();
                Session::securitySession(); 
                $this->validatePermissions('radiosmantto');
                $asignaciones = asignacion::select(array('id','nombre','activo'))->where('activo',1)->get()->fetch_all();
                if($asignaciones)
                        echo json_encode(array('ok'=>true,'msg'=>$asignaciones));
                else
                        echo json_encode(array('ok'=>false,'msg'=>"No se lograron obtener las asignaciones"));
        }
	public function add()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radioscat');
        $this->asignacion();
        $return = array("ok"=>false,"msg"=>"");
        $condi = true;
        $condi = $condi && $this->_validar->NoEmpty($this->_data["nombre"],"Nombre");
        $condi = $condi && $this->_validar->Int($this->_data["activo"],"Activo");
        if($condi)
        {
        	$asignacion = asignacion::insert(array('nombre'=>$this->_data["nombre"],'activo'=>$this->_data["activo"]));
        	if($asignacion)
        	{
        		$return["ok"] = true;
        		$return["msg"] = "Asignacion guardada correctamente";
        	}
        	else
        		$return["msg"] = "No fue posible guardar la asignacion.";
        }
        else
        	$return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
	}
	public function updatebyid()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radioscat');
        $this->asignacion();
        $return = array("ok"=>false,"msg"=>"");
        $condi = true;
        $condi = $condi && $this->_validar->Int($this->_data["id"],"Folio");
        $condi = $condi && $this->_validar->NoEmpty($this->_data["nombre"],"Nombre");
        $condi = $condi && $this->_validar->Int($this->_data["activo"],"Activo");
        if($condi)
        {
        	$asignacion = asignacion::where('id',$this->_data["id"])->update(array('nombre'=>$this->_data["nombre"],'activo'=>$this->_data["activo"]));
        	if($asignacion)
        	{
        		$return["ok"] = true;
        		$return["msg"] = "Asignacion modificada correctamente";
        	}
        	else
        		$return["msg"] = "No fue posible modificar la asignacion.";
        }
        else
        	$return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
	}
	public function getbyid()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radioscat');
        $return = array('ok'=>false,'msg'=>'');
        $this->asignacion();
        $asignacion = asignacion::select(array('nombre','activo'))->where('id',$this->_data["id"])->get()->fetch_assoc();
        if($asignacion)
        {
        	$return["msg"] = $asignacion;
        	$return["ok"] = true;
        }
        else
        	$return["msg"] = "Error obteniendo la asignacion";
        echo json_encode($return);
	}
	public function asignacion()
	{
		$this->_data["id"] = isset($_POST["id"]) ? (integer)$_POST["id"] : 0;
		$this->_data["nombre"] = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
		$this->_data["activo"] = isset($_POST["activo"]) ? (integer)$_POST["activo"] : 2;
	}
}
?>