<?php
class recibosController extends Controller
{
	private $_data = array();
	private $_return = array('ok'=>false,'msg'=>'');
	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		Session::regenerateId();
		Session::securitySession();
		$this->validatePermissions('recibos');
		$this->_view->renderizar('index','C4 - Recibos');
	}
	public function resguardo($data)
	{
		$this->_data["id"] = isset($data["id"]) ? (integer)$data["id"] : null;
		$this->_data["idunico"] = isset($data["idunico"]) ? (integer)$data["idunico"] : null;
		$this->_data["anio"] = isset($data["anio"]) ? (integer)$data["anio"] : null;
		$this->_data["idresguardo"] = isset($data["idresguardo"]) ? (integer)$data["idresguardo"] : 0;
		$this->_data["nombre"] = isset($data["nombre"]) ? $data["nombre"] : "";
		$this->_data["dependencia"] = isset($data["dependencia"]) ? $data["dependencia"] : "";
		$this->_data["departamento"] = isset($data["departamento"]) ? $data["departamento"] : "";
		$this->_data["cargo"] = isset($data["cargo"]) ? $data["cargo"] : "";
		$this->_data["nota"] = isset($data["nota"]) ? $data["nota"] : "";
		$this->_data["fechaEntrega"] = isset($data["fechaEntrega"]) ? $data["fechaEntrega"] : "";
		$this->_data["personal"] = isset($data["personal"]) ? (integer)$data["personal"] : 0;
		$this->_data["tipo"] = isset($data["tipo"]) ? (integer)$data["tipo"] : 0;
		$this->_data["status"] = isset($data["status"]) ? (integer)$data["status"] : 0;
		$this->_data["area"] = isset($data["area"]) ? $data["area"] : "";
	}
}
?>