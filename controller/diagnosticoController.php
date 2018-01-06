<?php
class diagnosticoController extends Controller
{
	private $_data;
	public function __construct()
	{
		parent::__construct();
	}
	public function getDiagnosticos()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radioscat');
        $diagnosticos = diagnostico::select(array('id','nombre','activo'))->where('area',$_SESSION["userData"]["area"])->get()->fetch_all();
        if($diagnosticos)
        	echo json_encode(array('ok'=>true,'msg'=>$diagnosticos));
        else
        	echo json_encode(array('ok'=>false,'msg'=>"No se lograron obtener los diagnosticos"));
	}
        public function getDiagnosticosActivos()
        {
                Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radiosmantto');
        $diagnosticos = diagnostico::select(array('id','nombre','activo'))->where('activo',1)->where('area',$_SESSION["userData"]["area"])->get()->fetch_all();
        if($diagnosticos)
                echo json_encode(array('ok'=>true,'msg'=>$diagnosticos));
        else
                echo json_encode(array('ok'=>false,'msg'=>"No se lograron obtener los diagnosticos"));
        }
	public function add()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radioscat');
        $this->diagnostico();
        $return = array("ok"=>false,"msg"=>"");
        $condi = true;
        $condi = $condi && $this->_validar->NoEmpty($this->_data["nombre"],"Nombre");
        $condi = $condi && $this->_validar->Int($this->_data["activo"],"Activo");
        if($condi)
        {
        	$diagnostico = diagnostico::insert(array('nombre'=>$this->_data["nombre"],'activo'=>$this->_data["activo"],'area'=>$_SESSION["userData"]["area"]));
        	if($diagnostico)
        	{
        		$return["ok"] = true;
        		$return["msg"] = "Diagnostico guardado correctamente";
        	}
        	else
        		$return["msg"] = "No fue posible guardar la diagnostico.";
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
        $this->diagnostico();
        $return = array("ok"=>false,"msg"=>"");
        $condi = true;
        $condi = $condi && $this->_validar->Int($this->_data["id"],"Folio");
        $condi = $condi && $this->_validar->NoEmpty($this->_data["nombre"],"Nombre");
        $condi = $condi && $this->_validar->Int($this->_data["activo"],"Activo");
        if($condi)
        {
        	$diagnostico = diagnostico::where('id',$this->_data["id"])->where('area',$_SESSION["userData"]["area"])->update(array('nombre'=>$this->_data["nombre"],'activo'=>$this->_data["activo"]));
        	if($diagnostico)
        	{
        		$return["ok"] = true;
        		$return["msg"] = "Diagnostico modificado correctamente.";
        	}
        	else
        		$return["msg"] = "No fue posible modificar el diagnostico.";
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
        $this->diagnostico();
        $diagnostico = diagnostico::select(array('nombre','activo'))->where('id',$this->_data["id"])->get()->fetch_assoc();
        if($diagnostico)
        {
        	$return["msg"] = $diagnostico;
        	$return["ok"] = true;
        }
        else
        	$return["msg"] = "Error obteniendo el diagnostico";
        echo json_encode($return);
	}
	public function diagnostico()
	{
		$this->_data["id"] = isset($_POST["id"]) ? (integer)$_POST["id"] : 0;
		$this->_data["nombre"] = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
		$this->_data["activo"] = isset($_POST["activo"]) ? (integer)$_POST["activo"] : 0;
	}
}
?>