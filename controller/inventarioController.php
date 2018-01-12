<?php
class inventarioController extends Controller
{
	public function __contruct()
	{
		parent::__contruct();
	}
	public function index()
	{
    	Session::regenerateId();
    	Session::securitySession();
		$this->validatePermissions('inventario');
    	$this->_view->renderizar('index','C4 - Inventario');
	}
}
?>