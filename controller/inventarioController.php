<?php
class inventarioController extends Controller
{
	private $_data = array();
	private $_return = array('ok'=>false,'msg'=>'');
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
	public function getForTable()
	{
		Session::regenerateId();
    	Session::securitySession();
		$this->validatePermissions('inventario');
		$activos = inventario::select(array('inventario.id'=>'idInventario','cantidad','codigo','ic.nombre'=>'categoria','ite.nombre'=>'tipoEquipo','im.nombre'=>'marca','modelo','noSerie','ium.nombre'=>'um','inventario.descripcion'))
			->join(array('inventarioCategoria','ic'),'inventario.categoria','=','ic.id','LEFT')
			->join(array('inventarioMarca','im'),'inventario.marca','=','im.id','LEFT')
			->join(array('inventarioTipoEquipo','ite'),'inventario.tipoEquipo','=','ite.id','LEFT')
			->join(array('inventarioUM','ium'),'inventario.um','=','ium.id','LEFT')
			->get()->fetch_all();
		if($activos)
		{
			$this->_return["msg"] = $activos;
			$this->_return["ok"] = true;
		}
		else
			$this->_return["msg"] = "No se encontraron activos";
		echo json_encode($this->_return);
	}
}
?>