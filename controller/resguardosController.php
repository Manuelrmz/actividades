<?php
class resguardosController extends Controller
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
		$this->validatePermissions('resguardos');
		$this->_view->renderizar('index','C4 - Resguardos');
	}
	public function resguardo($data)
	{
		$this->_data["id"] = isset($data["id"]) ? (integer)$data["id"] : null;
		$this->_data["nombre"] = isset($data["nombre"]) ? $data["nombre"] : "";
		$this->_data["cargo"] = isset($data["cargo"]) ? $data["cargo"] : "";
		$this->_data["dependencia"] = isset($data["dependencia"]) ? $data["dependencia"] : "";
		$this->_data["areaSolicitante"] = isset($data["areaSolicitante"]) ? $data["areaSolicitante"] : "";
		$this->_data["telefono"] = isset($data["telefono"]) ? $data["telefono"] : "";
		$this->_data["extension"] = isset($data["extension"]) ? $data["extension"] : "";
		$this->_data["direccion"] = isset($data["direccion"]) ? $data["direccion"] : "";
		$this->_data["area"] = isset($data["area"]) ? $data["area"] : "";
		$this->_data["usuarioAlta"] = isset($data["usuarioAlta"]) ? $data["usuarioAlta"] : "";
	}
}
?>