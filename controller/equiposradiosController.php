<?php
class equiposradiosController extends Controller
{
	private $_data;
	public function __construct()
	{
		parent::__construct();
	}
	public function add()
	{
		session::regenerateId();
        Session::securitySession();
        //$this->validatePermissions('equiposradios');
        $this->equiporadios();
        $return = array('ok'=>false,'msg'=>'');
        $condi = true;
        $condi = $condi && $this->_validar->Int($this->_data["rfsi"],"RFSI");
        $condi = $condi && $this->_validar->MinMax($this->_data["rfsi"],7,9,"RFSI");
        $condi = $condi && $this->_validar->MinMax($this->_data["nslogico"],8,10,"N/S Logico");
        $condi = $condi && $this->_validar->MinMax($this->_data["serie"],15,30,"Serie");
        $condi = $condi && $this->_validar->MinMax($this->_data["version"],1,10,"Version");
        $condi = $condi && $this->_validar->MinMax($this->_data["tipo"],1,3,"Tipo");
        $condi = $condi && $this->_validar->Int($this->_data["activo"],"Estado");
        if($condi)
        {
        	unset($this->_data["id"]);
        	$this->_data["usuarioAlta"] = $_SESSION["userData"]["usuario"];
        	$this->_data["fechaAlta"] = date("Y-m-d H:i:s");
        	$nuevo = equiposradios::insert($this->_data);
        	if($nuevo)
        	{
        		$return["msg"] = "Equipo Insertado Correctamente";
        		$return["ok"] = true;
        	}
        	else
        		$return["msg"] = "Ocurrio un error insertando el nuevo equipo: ".$nuevo;
        }
        else
        	$return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
	}
	public function updatebyid()
	{
		session::regenerateId();
        Session::securitySession();
        $this->validatePermissions('equiposradios');
        $this->equiporadios();
        $return = array('ok'=>false,'msg'=>'');
        $condi = true;
        $condi = $condi && $this->_validar->Int($this->_data["id"],'Folio'); 
        $condi = $condi && $this->_validar->Int($this->_data["rfsi"],"RFSI");
        $condi = $condi && $this->_validar->MinMax($this->_data["rfsi"],7,9,"RFSI");
        $condi = $condi && $this->_validar->MinMax($this->_data["nslogico"],8,10,"N/S Logico");
        $condi = $condi && $this->_validar->MinMax($this->_data["serie"],15,30,"Serie");
        $condi = $condi && $this->_validar->MinMax($this->_data["version"],1,10,"Version");
        $condi = $condi && $this->_validar->MinMax($this->_data["tipo"],1,3,"Tipo");
        $condi = $condi && $this->_validar->Int($this->_data["activo"],"Estado");
        if($condi)
        {
        	$this->_data["usuarioMod"] = $_SESSION["userData"]["usuario"];
        	$mod = equiposradios::where('id',$this->_data["id"])->update($this->_data);
        	if($mod)
        	{
        		$return["msg"] = "Equipo Modificado Correctamente";
        		$return["ok"] = true;
        	}
        	else
        		$return["msg"] = "Ocurrio un error modificando el equipo: ".$mod;
        }
        else
        	$return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
	}
	public function getAll()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radiosmantto');
        $return = array('ok'=>false,'msg'=>'');
        $equipos = equiposradios::select(array('rfsi'=>'text','id'=>'value'))->get()->fetch_all();
        if($equipos)
        {
        	$return["msg"] = $equipos;
        	$return["ok"] = true;
        }	
        else
        	$return["msg"] = "No se pudieron obtener los equipos de radio";
        echo json_encode($return);
	}
	public function getbyId()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radiosmantto');
        $return = array('ok'=>false,'msg'=>'');
        $condi = true;
        $id = (integer)$_POST["id"];
        $condi = $condi && $this->_validar->Int($id,'RFSI');
        if($condi)
        {
	        $equipo = equiposradios::select(array('equiposradios.*','t.descripcion'))->join(array('tipoequiporadios','t'),'equiposradios.tipo','=','t.clave','LEFT')->where('equiposradios.id',$id)->get()->fetch_assoc();
	        if($equipo)
	        {
	        	$return["msg"] = $equipo;
	        	$return["ok"] = true;
	        }	
	        else
	        	$return["msg"] = "No se pudieron obtener los datos del equipo";
	    }
	    else
	    	$return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
	}
	public function getForTable()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('equiposradios');
        $return = array('ok'=>false,'msg'=>'');
        $equipos = equiposradios::select(array('rfsi','id'=>'folio','nslogico','serie','tipo','activo'))->get()->fetch_all();
        if($equipos)
        {
        	$return["msg"] = $equipos;
        	$return["ok"] = true;
        }	
        else
        	$return["msg"] = "No se pudieron obtener los equipos de radio";
        echo json_encode($return);
	}
	public function equiporadios()
	{
		$this->_data["id"] = isset($_POST["id"]) ? (integer)$_POST["id"] : 0;
		$this->_data["rfsi"] = isset($_POST["rfsi"]) ? (integer)$_POST["rfsi"] : 0;
		$this->_data["nslogico"] = isset($_POST["nslogico"]) ? $_POST["nslogico"] : "";
		$this->_data["serie"] = isset($_POST["serie"]) ? $_POST["serie"] : "";
		$this->_data["version"] = isset($_POST["version"]) ? $_POST["version"] : "";
		$this->_data["tipo"] = isset($_POST["tipo"]) ? $_POST["tipo"] : "";
		$this->_data["comentario1"] = isset($_POST["comentario1"]) ? $_POST["comentario1"] : 0;
		$this->_data["comentario2"] = isset($_POST["comentario2"]) ? $_POST["comentario2"] : 0;
		$this->_data["activo"] = isset($_POST["activo"]) ? (integer)$_POST["activo"] : 2;
	}
}
?>