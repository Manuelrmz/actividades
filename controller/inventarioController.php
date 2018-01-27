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
		$condi = $condi && $this->_validar->MinInt($this->_data["cantidad"],"1","Debes seleccionar un valor valido del campo Cantidad");
		$condi = $condi && $this->_validar->MinMax($this->_data["categoria"],1,100,"Categoria");
		$condi = $condi && $this->_validar->MinMax($this->_data["tipoEquipo"],1,100,"Tipo de Equipo");
		$condi = $condi && $this->_validar->MinMax($this->_data["marca"],1,100,"Marca");
		$condi = $condi && $this->_validar->MinMax($this->_data["um"],1,100,"Unidad de Medida");
		if($condi)
		{
			unset($this->_data["id"]);
			$noSerieTemporal = inventario::select(array('noSerie'))->where('noSerie','LIKE','S/N-%')->orderBy('noSerie','DESC')->get()->fetch_assoc();
            if($noSerieTemporal)
            {
                $noSerieTemporal = explode('-',$noSerieTemporal["noSerie"]);
                $noSerieTemporal = (integer)$noSerieTemporal[1] + 1;
            }
            else
                $noSerieTemporal = 1;
            if($this->_data["noSerie"] === "" && $this->_data["categoria"] == "EQUIPO")
            	$this->_data["noSerie"] = 'S/N-'.$noSerieTemporal;
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
		$condi = $condi && $this->_validar->MinInt($this->_data["cantidad"],"1","Debes seleccionar un valor valido del campo Cantidad");
		$condi = $condi && $this->_validar->MinMax($this->_data["categoria"],1,100,"Categoria");
		$condi = $condi && $this->_validar->MinMax($this->_data["tipoEquipo"],1,100,"Tipo de Equipo");
		$condi = $condi && $this->_validar->MinMax($this->_data["marca"],1,100,"Marca");
		$condi = $condi && $this->_validar->MinMax($this->_data["um"],1,100,"Unidad de Medida");
		if($condi)
		{
			$haveResguardo = resguardos::select(array('resguardos.id','i.status'))
							->join(array('resguardosInventario','ri'),'resguardos.id','=','ri.idresguardo','LEFT')
							->join(array('inventario','i'),'i.id','=','ri.idinventario','LEFT')
							->where('resguardos.status',1)
							->where('ri.idinventario',$this->_data["id"])
							->get()->fetch_assoc();
			$noSerieTemporal = inventario::select(array('noSerie'))->where('noSerie','LIKE','S/N-%')->orderBy('noSerie','DESC')->get()->fetch_assoc();
            if($noSerieTemporal)
            {
                $noSerieTemporal = explode('-',$noSerieTemporal["noSerie"]);
                $noSerieTemporal = (integer)$noSerieTemporal[1] + 1;
            }
            else
                $noSerieTemporal = 1;
            if($this->_data["noSerie"] === "" && $this->_data["categoria"] == "EQUIPO")
            	$this->_data["noSerie"] = 'S/N-'.$noSerieTemporal;
            if($haveResguardo && ($haveResguardo["status"] != $this->_data["status"]))
            {
            	$this->_return["msg"] = "Equipo modificado correctamente, el status no fue modificado por que el equipo se encuentra asignado a un resguardo";
            	$this->_data["status"] = $haveResguardo["status"];
            }
            else
            	$this->_return["msg"] = "Equipo modificado correctamente";
			inventario::where('id',$this->_data["id"])->update($this->_data);
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
		$activos = inventario::select(array('id'=>'idInventario','cantidad','codigo','categoria','tipoEquipo','marca','modelo','noSerie','um','descripcion'))
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
	public function getAllowedForResguardoCombo()
	{
		Session::regenerateId();
    	Session::securitySession();
		$activos = inventario::select(array('inventario.id'=>'value','inventario.noSerie'=>'text'))
			->join(array('facturas','f'),'inventario.idfactura','=','f.id','LEFT')
			->join(array('areas','a'),'f.area','=','a.id','LEFT')
			->where('inventario.status','2')
			->where('a.clave',$_SESSION["userData"]["area"])
			->where('inventario.cantidad',1)
			->where('inventario.categoria',"EQUIPO")
			->where('inventario.noSerie','!=','')
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
		$this->_data["categoria"] = isset($data["categoria"]) ? $data["categoria"] : "";
		$this->_data["tipoEquipo"] = isset($data["tipoEquipo"]) ? $data["tipoEquipo"] : "";
		$this->_data["marca"] = isset($data["marca"]) ? $data["marca"] : "";
		$this->_data["modelo"] = isset($data["modelo"]) ? $data["modelo"] : "";
		$this->_data["noSerie"] = isset($data["noSerie"]) ? $data["noSerie"] : "";
		$this->_data["um"] = isset($data["um"]) ? $data["um"] : "";
		$this->_data["descripcion"] = isset($data["descripcion"]) ? $data["descripcion"] : "";
		$this->_data["status"] = isset($data["status"]) ? ((integer)$data["status"] > 0 ? (integer)$data["status"] : 2) : 2;
	}
}
?>