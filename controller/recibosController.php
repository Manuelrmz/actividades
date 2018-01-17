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
		$this->_data["idresguardo"] = isset($data["idresguardo"]) ? (integer)$data["idresguardo"] : 0;
		$this->_data["fechaEntrega"] = isset($data["fechaEntrega"]) ? $data["fechaEntrega"] : "";
		$this->_data["personal"] = isset($data["personal"]) ? $data["personal"] : "";
		$this->_data["tipo"] = isset($data["tipo"]) ? (integer)$data["tipo"] : 0;
		$this->_data["status"] = isset($data["status"]) ? (integer)$data["status"] : 0;
		$this->_data["area"] = isset($data["area"]) ? $data["area"] : "";
	}
}
?>