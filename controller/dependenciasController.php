<?php
class dependenciasController extends Controller
{
	private $_data;
	public function __construct()
	{
		parent::__construct();
	}
	public function getDependencias()
	{
		Session::regenerateId();
                Session::securitySession(); 
                $this->validatePermissions('radioscat');
                $dependencias = dependencias::select(array('id','nombre','activo'))->get()->fetch_all();
                if($dependencias)
                {
                	echo json_encode(array('ok'=>true,'msg'=>$dependencias));
                }
                else
                	echo json_encode(array('ok'=>false,'msg'=>"No se lograron obtener las dependencias"));
	}
        public function getDependenciasActivas()
        {
                Session::regenerateId();
                Session::securitySession(); 
                $this->validatePermissions('radiosmantto');
                $dependencias = dependencias::select(array('id','nombre','activo'))->where('activo',1)->get()->fetch_all();
                if($dependencias)
                {
                        echo json_encode(array('ok'=>true,'msg'=>$dependencias));
                }
                else
                        echo json_encode(array('ok'=>false,'msg'=>"No se lograron obtener las dependencias"));
        }
	public function add()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radioscat');
        $this->dependencia();
        $return = array("ok"=>false,"msg"=>"");
        $condi = true;
        $condi = $condi && $this->_validar->NoEmpty($this->_data["nombre"],"Nombre");
        $condi = $condi && $this->_validar->Int($this->_data["activo"],"Activo");
        if($condi)
        {
        	$dependencia = dependencias::insert(array('nombre'=>$this->_data["nombre"],'activo'=>$this->_data["activo"]));
        	if($dependencia)
        	{
        		$return["ok"] = true;
        		$return["msg"] = "Dependencia guardada correctamente";
        	}
        	else
        		$return["msg"] = "No fue posible guardar la dependencia.";
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
        $this->dependencia();
        $return = array("ok"=>false,"msg"=>"");
        $condi = true;
        $condi = $condi && $this->_validar->Int($this->_data["id"],"Folio");
        $condi = $condi && $this->_validar->NoEmpty($this->_data["nombre"],"Nombre");
        $condi = $condi && $this->_validar->Int($this->_data["activo"],"Activo");
        if($condi)
        {
        	$dependencia = dependencias::where('id',$this->_data["id"])->update(array('nombre'=>$this->_data["nombre"],'activo'=>$this->_data["activo"]));
        	if($dependencia)
        	{
        		$return["ok"] = true;
        		$return["msg"] = "Dependencia modificada correctamente";
        	}
        	else
        		$return["msg"] = "No fue posible modificar la dependencia.";
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
        $this->dependencia();
        $dependencia = dependencias::select(array('nombre','activo'))->where('id',$this->_data["id"])->get()->fetch_assoc();
        if($dependencia)
        {
        	$return["msg"] = $dependencia;
        	$return["ok"] = true;
        }
        else
        	$return["msg"] = "Error obteniendo la dependencia";
        echo json_encode($return);
	}
	public function dependencia()
	{
		$this->_data["id"] = isset($_POST["id"]) ? (integer)$_POST["id"] : 0;
		$this->_data["nombre"] = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
		$this->_data["activo"] = isset($_POST["activo"]) ? (integer)$_POST["activo"] : 0;
	}
}
?>