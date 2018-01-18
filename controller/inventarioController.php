<?php
class inventarioController extends Controller
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
		$this->validatePermissions('inventario');
    	$this->_view->renderizar('index','C4 - Inventario');
	}
	public function add()
	{
		Session::regenerateId();
    	Session::securitySession();
		$this->validatePermissions('inventario');
		$this->inventario($_POST);
		$condi = true;
		$condi = $condi && $this->_validar->Int($this->_data["cantidad"],"Cantidad");
		$condi = $condi && $this->_validar->Int($this->_data["categoria"],"Categoria");
		$condi = $condi && $this->_validar->Int($this->_data["tipoEquipo"],"Tipo de Equipo");
		$condi = $condi && $this->_validar->Int($this->_data["marca"],"Marca");
		$condi = $condi && $this->_validar->Int($this->_data["um"],"Unidad de Medida");
		$condi = $condi && $this->_validar->MinInt($this->_data["cantidad"],"1","Debes seleccionar un valor valido del campo Cantidad");
		$condi = $condi && $this->_validar->MinInt($this->_data["categoria"],"1","Debes seleccionar un valor valido del campo Categoria");
		$condi = $condi && $this->_validar->MinInt($this->_data["tipoEquipo"],"1","Debes seleccionar un valor valido del campo Tipo de Equipo");
		$condi = $condi && $this->_validar->MinInt($this->_data["marca"],"1","Debes seleccionar un valor valido del campo Marca");
		$condi = $condi && $this->_validar->MinInt($this->_data["um"],"1","Debes seleccionar un valor valido del campo Unidad de Medida");
		if($condi)
		{
			unset($this->_data["id"]);
			$equipo = inventario::insert($this->_data);
			if($equipo)
			{
				$this->_return["msg"] = "Equipo agregado correctamente";
				$this->_return["id"] = $equipo;
				$this->_return["ok"] = true;
			}
			else
				$this->_return["msg"] = "Ocurrio un error agregando el equipo al inventario";
		}
		else
			$this->_return["msg"] = $this->_validar->getWarnings();
		echo json_encode($this->_return);
	}
	public function update()
	{
		Session::regenerateId();
    	Session::securitySession();
		$this->validatePermissions('inventario');
		$this->inventario($_POST);
		$condi = true;
		$condi = $condi && $this->_validar->Int($this->_data["id"],"Folio");
		$condi = $condi && $this->_validar->MinInt($this->_data["id"],"1","Debes enviar un equipo con Folio Valido");
		$condi = $condi && $this->_validar->Int($this->_data["cantidad"],"Cantidad");
		$condi = $condi && $this->_validar->Int($this->_data["categoria"],"Categoria");
		$condi = $condi && $this->_validar->Int($this->_data["tipoEquipo"],"Tipo de Equipo");
		$condi = $condi && $this->_validar->Int($this->_data["marca"],"Marca");
		$condi = $condi && $this->_validar->Int($this->_data["um"],"Unidad de Medida");
		$condi = $condi && $this->_validar->MinInt($this->_data["cantidad"],"1","Debes seleccionar un valor valido del campo Cantidad");
		$condi = $condi && $this->_validar->MinInt($this->_data["categoria"],"1","Debes seleccionar un valor valido del campo Categoria");
		$condi = $condi && $this->_validar->MinInt($this->_data["tipoEquipo"],"1","Debes seleccionar un valor valido del campo Tipo de Equipo");
		$condi = $condi && $this->_validar->MinInt($this->_data["marca"],"1","Debes seleccionar un valor valido del campo Marca");
		$condi = $condi && $this->_validar->MinInt($this->_data["um"],"1","Debes seleccionar un valor valido del campo Unidad de Medida");
		if($condi)
		{
			inventario::where('id',$this->_data["id"])->update($this->_data);
			$this->_return["msg"] = "Equipo modificado correctamente";
			$this->_return["ok"] = true;
		}
		else
			$this->_return["msg"] = $this->_validar->getWarnings();
		echo json_encode($this->_return);
	}
	public function getbyid($id)
	{
		Session::regenerateId();
    	Session::securitySession();
		$this->validatePermissions('inventario');
		$this->inventario($_POST);
		$condi = true;
		$condi = $condi && $this->_validar->Int($id,"Folio");
		$condi = $condi && $this->_validar->MinInt($id,"1","Debes enviar un equipo con Folio Valido");
		if($condi)
		{
			$equipo = inventario::where('id',$id)->get()->fetch_assoc();
			if($equipo)
			{
				$this->_return["msg"] = $equipo;
				$this->_return["ok"] = true;
			}
			else
				$this->_return["msg"] = "No se encontro el equipo con el id enviado";
		}
		else
			$this->_return["msg"] = $this->_validar->getWarnings();
		echo json_encode($this->_return);
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
	public function getAvailableByUserArea()
	{
		Session::regenerateId();
    	Session::securitySession();
		$activos = inventario::select(array('inventario.id'=>'value','CONCAT(noSerie," ",ite.nombre," ",ic.nombre," ",im.nombre," ",modelo)'=>'text'))
			->join(array('inventarioCategoria','ic'),'inventario.categoria','=','ic.id','LEFT')
			->join(array('inventarioMarca','im'),'inventario.marca','=','im.id','LEFT')
			->join(array('inventarioTipoEquipo','ite'),'inventario.tipoEquipo','=','ite.id','LEFT')
			->join(array('inventarioUM','ium'),'inventario.um','=','ium.id','LEFT')
			->join(array('facturas','f'),'inventario.idfactura','=','f.id','LEFT')
			->join(array('areas','a'),'f.area','=','a.id','LEFT')
			->where('inventario.status','2')
			->where('a.clave',$_SESSION["userData"]["area"])
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
	public function inventario($data)
	{
		$this->_data["id"] = isset($data["id"]) ? (integer)$data["id"] : 0;
		$this->_data["idfactura"] = isset($data["idfactura"]) ? ((integer)$data["idfactura"] > 0 ? (integer)$data["idfactura"] : null ) : null;
		$this->_data["cantidad"] = isset($data["cantidad"]) ? (integer)$data["cantidad"] : 0;
		$this->_data["codigo"] = isset($data["codigo"]) ? $data["codigo"] : "";
		$this->_data["categoria"] = isset($data["categoria"]) ? (integer)$data["categoria"] : 0;
		$this->_data["tipoEquipo"] = isset($data["tipoEquipo"]) ? (integer)$data["tipoEquipo"] : 0;
		$this->_data["marca"] = isset($data["marca"]) ? (integer)$data["marca"] : 0;
		$this->_data["modelo"] = isset($data["modelo"]) ? $data["modelo"] : "";
		$this->_data["noSerie"] = isset($data["noSerie"]) ? $data["noSerie"] : "";
		$this->_data["um"] = isset($data["um"]) ? (integer)$data["um"] : 0;
		$this->_data["descripcion"] = isset($data["descripcion"]) ? $data["descripcion"] : "";
		$this->_data["status"] = isset($data["status"]) ? ((integer)$data["status"] > 0 ? (integer)$data["status"] : 2) : 2;
	}
}
?>