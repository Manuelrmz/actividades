<?php
class errorController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		$this->_view->renderizar('index','C4 - Archivo no encontrado');
	}
	public function error520()
	{
		$this->_view->renderizar('error520','C4 - Ruta no encontrada');
	}
	public function error403()
	{
		$this->_view->renderizar('error403','C4 - Acceso Restringido');
	}
}
?>