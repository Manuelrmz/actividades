<?php
class catalogoController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		Session::regenerateId();
    Session::securitySession();
    //$this->validatePermissions('usuariosadmon');
    $this->_view->renderizar('index','C4 - Catalogo General');
	}
	public function radios()
	{
		Session::regenerateId();
    Session::securitySession();
    $this->validatePermissions('radioscat');
    $this->_view->renderizar('radios','C4 - Catalogos');
	}
	public function facturas()
	{
		Session::regenerateId();
    Session::securitySession();
    $this->_view->renderizar('facturas','C4 - Catalogos');
	}
}
?>
