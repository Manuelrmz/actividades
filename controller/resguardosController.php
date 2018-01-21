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
	public function new()
	{
		Session::regenerateId();
		Session::securitySession();
		$this->validatePermissions('resguardos');
		$this->resguardo($_POST);
		$db = new queryBuilder();
		$condi = true;
		$equipos = json_decode($this->_data["equipos"],true);
		$condi = $condi && $this->_validar->MinMax($this->_data["nombre"],1,200,"Nombre Solicitante");
		$condi = $condi && $this->_validar->MinMax($this->_data["dependencia"],1,150,"Dependencia Solicitante");
		$condi = $condi && $this->_validar->MinInt($this->_data["personal"],1,"Debe seleccionar un personal de la lista");
		$condi = $condi && $this->_validar->MinInt(sizeof($equipos),1,"Debe seleccionar al menos un equipo para asignar al resguardo");
		if($condi)
		{
			$this->_data["equipos"] = $equipos;
			$this->_data["anio"] = date('Y');
			$idunico = resguardos::select(array('idunico'))->where('area',$_SESSION["userData"]["area"])->where('anio',$this->_data["anio"])->orderBy('idunico','DESC')->limit(1)->get()->fetch_assoc();
			$this->_data["idunico"] = ($idunico ? ((integer)$idunico["idunico"] + 1) : 1);
			$transaccion = $db->transaction(function($q)
			{
				$equipList = $this->_data["equipos"];
				unset($this->_data["equipos"]);
				$this->_data["area"] = $_SESSION["userData"]["area"];
				$this->_data["usuarioAlta"] = $_SESSION["userData"]["usuario"];
				$resguardo = $q->table('resguardos')->insert($this->_data);
				foreach ($equipList as $equip) {
					$q->table('resguardosInventario')->insert(array('idresguardo'=>$resguardo,'idinventario'=>$equip["id"]));
					$q->table('inventario')->where('id',$equip["id"])->update(array('status'=>1));
				}
			});
			if($transaccion)
			{
				$this->_return["msg"] = "Resguardo guardado correctamente";
				$this->_return["ok"] = true;
			}
			else
				$this->_return["msg"] = "Ocurrio un error insertando el resguardo: ".$db->getError()["string"];
		}
		else
			$this->_return["msg"] = $this->_validar->getWarnings();
		echo json_encode($this->_return);
	}
	public function getById($id)
	{
		Session::regenerateId();
		Session::securitySession();
		$condi = true;
		$condi = $condi && $this->_validar->Int($id,"Folio");
		$condi = $condi && $this->_validar->MinInt($id,1,"Debe enviar un folio valido");
		if($condi)
		{
			$resguardo = resguardos::select(array('id','CONCAT("C4-RES/",resguardos.idunico,"/",resguardos.anio)'=>'idunico','nombre','dependencia','departamento','cargo','nota','area','personal'))->where('id',$id)->get()->fetch_assoc();
			if($resguardo)
			{
				$resguardo["equipos"] = inventario::select(array('inventario.id','codigo','categoria','tipoEquipo','marca','modelo','noSerie','descripcion'))
										->join(array('resguardosInventario','ri'),'inventario.id','=','ri.idinventario','LEFT')
										->where('ri.idresguardo',$id)->get()->fetch_all();
				$this->_return["msg"] = $resguardo;
				$this->_return["ok"] = true;
			}
			else
				$this->_return["msg"] = "No se encontro un resguardo con el folio enviado";
		}
		else
			$this->_return["msg"] = $this->_validar->getWarnings();
		echo json_encode($this->_return);
	}
	public function getForTable()
	{
		Session::regenerateId();
		Session::securitySession();
		$resguardos = resguardos::select(array('resguardos.id'=>'idresguardo','CONCAT("C4-RES/",resguardos.idunico,"/",resguardos.anio)'=>'idunico','resguardos.nombre','resguardos.dependencia','resguardos.departamento','resguardos.cargo','u.nombres'=>'personal','resguardos.fechaAlta'))
					->join(array('usuarios','u'),'resguardos.personal','=','u.id','LEFT')->get()->fetch_all();
		if($resguardos)
		{
			$this->_return["msg"] = $resguardos;
			$this->_return["ok"] = true;
		}
		else
			$this->_return["msg"] = "No se encontraron resguardos";
		echo json_encode($this->_return);
	}
	public function getForTableByUserArea()
	{
		Session::regenerateId();
		Session::securitySession();
		$resguardos = resguardos::select(array('resguardos.id'=>'idresguardo','CONCAT("C4-RES/",resguardos.idunico,"/",resguardos.anio)'=>'idunico','resguardos.nombre','resguardos.dependencia','resguardos.departamento','resguardos.cargo','u.nombres'=>'personal','resguardos.fechaAlta'))
					->join(array('usuarios','u'),'resguardos.personal','=','u.id','LEFT')
					->where('resguardos.area',$_SESSION["userData"]["area"])->get()->fetch_all();
		if($resguardos)
		{
			$this->_return["msg"] = $resguardos;
			$this->_return["ok"] = true;
		}
		else
			$this->_return["msg"] = "No se encontraron resguardos";
		echo json_encode($this->_return);
	}
	public function getOpenForComboBox()
	{
		Session::regenerateId();
		Session::securitySession();
		$resguardos = resguardos::select(array('id'=>'value','CONCAT("C4-RES/",idunico,"/",anio)'=>'text'))->where('status',1)->get()->fetch_all();
		if($resguardos)
		{
			$this->_return["msg"] = $resguardos;
			$this->_return["ok"] = true;
		}
		else
			$this->_return["msg"] = "No se encontraron resguardos";
		echo json_encode($this->_return);
	}
	public function getEquipForComboActiveById($id)
	{
		Session::regenerateId();
		Session::securitySession();
		$equipos = inventario::select(array('inventario.id'=>'value','noSerie'=>'text'))
										->join(array('resguardosInventario','ri'),'inventario.id','=','ri.idinventario','LEFT')
										->where('ri.idresguardo',$id)->where('status',1)->get()->fetch_all();
		if($equipos)
		{
			$this->_return["msg"] = $equipos;
			$this->_return["ok"] = true;
		}
		else
			$this->_return["msg"] = "El resguardo no cuenta con equipos asignados";
		echo json_encode($this->_return);
	}
	public function getEquipForTableById($id)
	{
		Session::regenerateId();
		Session::securitySession();
		$equipos = inventario::select(array('inventario.id','codigo','categoria','tipoEquipo','marca','modelo','noSerie','descripcion'))
										->join(array('resguardosInventario','ri'),'inventario.id','=','ri.idinventario','LEFT')
										->where('ri.idresguardo',$id)->get()->fetch_all();
		if($equipos)
		{
			$this->_return["msg"] = $equipos;
			$this->_return["ok"] = true;
		}
		else
			$this->_return["msg"] = "El resguardo no cuenta con equipos asignados";
		echo json_encode($this->_return);
	}
	public function resguardo($data)
	{
		$this->_data["id"] = isset($data["id"]) ? (integer)$data["id"] : null;
		$this->_data["idunico"] = isset($data["idunico"]) ? (integer)$data["idunico"] : null;
		$this->_data["anio"] = isset($data["anio"]) ? (integer)$data["anio"] : null;
		$this->_data["nombre"] = isset($data["nombre"]) ? $data["nombre"] : "";
		$this->_data["dependencia"] = isset($data["dependencia"]) ? $data["dependencia"] : "";
		$this->_data["departamento"] = isset($data["departamento"]) ? $data["departamento"] : "";
		$this->_data["cargo"] = isset($data["cargo"]) ? $data["cargo"] : "";
		$this->_data["nota"] = isset($data["nota"]) ? $data["nota"] : "";
		$this->_data["area"] = isset($data["area"]) ? $data["area"] : "";
		$this->_data["personal"] = isset($data["personal"]) ? (integer)$data["personal"] : 0;
		$this->_data["usuarioAlta"] = isset($data["usuarioAlta"]) ? $data["usuarioAlta"] : "";
		$this->_data["equipos"] = isset($data["equipos"]) ? $data["equipos"] : "[]";
	}
}
?>