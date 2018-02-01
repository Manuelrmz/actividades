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
	public function new()
	{	
		//Tipo 1 = normal, tipo 0 = temporal
		Session::regenerateId();
		Session::securitySession();
		$this->validatePermissions('recibos');
		$this->resguardo($_POST);
		$db = new queryBuilder();
		$bandGuardar = true;
		$condi = true;
		$equipos = json_decode($this->_data["equipos"],true);
		$condi = $condi && $this->_validar->MinInt($this->_data["idresguardo"],1,"Debe seleccionar al menos un equipo para asignar al resguardo");
		$condi = $condi && $this->_validar->MinInt($this->_data["personal"],1,"Debe seleccionar un personal de la lista");
		$condi = $condi && $this->_validar->MinMaxInt($this->_data["tipo"],0,1,"El recibo debe ser temporal o normal");
		$condi = $condi && $this->_validar->Date($this->_data["fechaEntrega"],"Debe enviar una fecha de entrega valida");
		$condi = $condi && $this->_validar->MinMax($this->_data["nombre"],1,200,"Nombre Solicitante");
		$condi = $condi && $this->_validar->MinMax($this->_data["dependencia"],1,150,"Dependencia Solicitante");
		$condi = $condi && $this->_validar->MinInt(sizeof($equipos),1,"Debe seleccionar al menos un equipo para asignar al resguardo");
		if($condi)
		{
			
			if($this->_data["tipo"] == 1)
			{
				$haveTemporal = recibos::select(array('id'))->where('idresguardo',$this->_data["idresguardo"])->where('tipo',0)->where('status',1)->get()->fetch_assoc();
				if($haveTemporal)
				{
					$bandGuardar = false;
					$this->_return["msg"] = "El resguardo normal que intenta guardar cuenta con un resguardo temporal abierto, finalice ese y vuelva a intentarlo";
				}
			}
			if($bandGuardar)
			{
				$this->_data["equipos"] = $equipos;
				$this->_data["anio"] = date('Y');
				$idunico = recibos::select(array('idunico'))->where('area',$_SESSION["userData"]["area"])->where('anio',$this->_data["anio"])->orderBy('idunico','DESC')->limit(1)->get()->fetch_assoc();
				$this->_data["idunico"] = ($idunico ? ((integer)$idunico["idunico"] + 1) : 1);
				$transaccion = $db->transaction(function($q)
				{
					$equipList = $this->_data["equipos"];
					unset($this->_data["equipos"]);
					$this->_data["area"] = $_SESSION["userData"]["area"];
					$this->_data["usuarioAlta"] = $_SESSION["userData"]["usuario"];
					$this->_data["status"] = $this->_data["tipo"] == 1 ? 0 : 1 ;
					$recibo = $q->table('recibos')->insert($this->_data);
					$this->_data["id"] = $recibo;
					if($this->_data["tipo"] == 1)
						$q->table('resguardos')->where('id',$this->_data["idresguardo"])->update(array('status'=>0));
					foreach ($equipList as $equip) {
						if($this->_data["tipo"] == 0)
							$q->table('recibosInventario')->insert(array('idrecibo'=>$recibo,'idinventario'=>$equip["id"]));
						$q->table('inventario')->where('id',$equip["id"])->update(array('status'=>($this->_data["tipo"] == 1 ? 2 : 3)));
					}
				});
				if($transaccion)
				{
					$this->_return["msg"] = "Recibo creado correctamente";
					$this->_return["ok"] = true;
					$this->_return["id"] = $this->_data["id"];
				}
				else
					$this->_return["msg"] = "Ocurrio un error insertando el resguardo: ".$db->getError()["string"];
			}
		}
		else
			$this->_return["msg"] = $this->_validar->getWarnings();
		echo json_encode($this->_return);
	}
	public function getForTableByUserArea()
	{
		Session::regenerateId();
		Session::securitySession();
		$recibos = recibos::select(array('recibos.id'=>'idrecibo','CONCAT("C4-REC/",recibos.idunico,"/",recibos.anio)'=>'idunico','recibos.nombre','recibos.dependencia','recibos.status','recibos.tipo','u.nombres'=>'personal','recibos.fechaAlta'))
					->join(array('usuarios','u'),'recibos.personal','=','u.id','LEFT')
					->where('recibos.area',$_SESSION["userData"]["area"])->get()->fetch_all();
		if($recibos)
		{
			$this->_return["msg"] = $recibos;
			$this->_return["ok"] = true;
		}
		else
			$this->_return["msg"] = "No se encontraron recibos";
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
			$recibo = recibos::select(array('recibos.id','r.id'=>'idresguardo','CONCAT("C4-RES/",r.idunico,"/",r.anio)'=>'folioResguardo','CONCAT("C4-REC/",recibos.idunico,"/",recibos.anio)'=>'idunico','recibos.nombre','recibos.dependencia','recibos.departamento','recibos.cargo','recibos.tipo','recibos.fechaEntrega','recibos.nota','recibos.area','recibos.personal','recibos.status'))
					->join(array('resguardos','r'),'recibos.idresguardo','=','r.id','LEFT')
					->where('recibos.id',$id)->get()->fetch_assoc();
			if($recibo)
			{
				if($recibo["tipo"] == 1)
				{
					$recibo["equipos"] = inventario::select(array('inventario.id','codigo','categoria','tipoEquipo','marca','modelo','noSerie','descripcion'))
										->join(array('resguardosInventario','ri'),'inventario.id','=','ri.idinventario','LEFT')
										->where('ri.idresguardo',$recibo["idresguardo"])->get()->fetch_all();
				}
				else if($recibo["tipo"] == 0)
				{
					$recibo["equipos"] = inventario::select(array('inventario.id','codigo','categoria','tipoEquipo','marca','modelo','noSerie','descripcion'))
										->join(array('recibosInventario','ri'),'inventario.id','=','ri.idinventario','LEFT')
										->where('ri.idrecibo',$id)->get()->fetch_all();
				}
				$this->_return["msg"] = $recibo;
				$this->_return["ok"] = true;
			}
			else
				$this->_return["msg"] = "No se encontro un recibo con el folio enviado";
		}
		else
			$this->_return["msg"] = $this->_validar->getWarnings();
		echo json_encode($this->_return);
	}
	public function closeReciboTemporalById($id)
	{
		Session::regenerateId();
		Session::securitySession();
		$db = new queryBuilder();
		$condi = true;
		$condi = $condi && $this->_validar->Int($id,"Folio");
		$condi = $condi && $this->_validar->MinInt($id,1,"Debe enviar un folio valido");
		if($condi)
		{
			$this->_data["id"] = $id;
			$recibo = recibos::select(array('id'))
						->where('id',$id)
						->where('area',$_SESSION["userData"]["area"])
						->where('tipo','0')
						->where('status','1')
						->get()->fetch_assoc();
			if($recibo)
			{
				$transaccion = $db->transaction(function($q)
				{
					$q->table('recibos')->where('id',$this->_data["id"])->where('area',$_SESSION["userData"]["area"])->where('tipo','0')->where('status','1')->update(array('status'=>0));
					$equipList = $q->table('inventario')->select(array('inventario.id'))
									->join(array('recibosInventario','ri'),'inventario.id','=','ri.idinventario','LEFT')
									->where('ri.idrecibo',$this->_data["id"])->get()->fetch_all();
					if($equipList)
					{
						foreach ($equipList as $equip) 
						{
							$q->table('inventario')->where('id',$equip["id"])->update(array('status'=>1));
						}
					}
				});
				if($transaccion)
				{
					$this->_return["msg"] = "Recibo finalizado correctamente";
					$this->_return["ok"] = true;
				}
				else
					$this->_return["msg"] = "Ocurrio un error finalizando el resguardo temporal: ".$db->getError()["string"];
			}
			else
				$this->_return["msg"] = "No se encontro un recibo con el folio enviado";
		}
		else
			$this->_return["msg"] = $this->_validar->getWarnings();
		echo json_encode($this->_return);
	}
	public function getPdf($id)
	{
		Session::regenerateId();
		Session::securitySession();
		include_once(CORE_PATH . 'mpdf/mpdf.php');
		$pdfText = '';
		$pdf = new mpdf('utf8','A4', '', '',1,1,12,13);
		if(isset($id))
		{
			$folio = (integer)$id;
			$condi = true;
			$condi = $condi && $this->_validar->Int($folio,'Folio');
			if($condi)
			{
				$recibo = recibos::select(array('CONCAT("C4-REC/",idunico,"/",anio)'=>'idunico','nombre','dependencia','departamento','cargo','nota','area','tipo','idresguardo'))
                      ->where('id',$folio)
                      ->get()->fetch_assoc();
				if($recibo)
				{
					//Cargamos Equipos
					if($recibo["tipo"] == 1)
					{
						$recibo["equipos"] = inventario::select(array('inventario.id','codigo','categoria','tipoEquipo','marca','modelo','noSerie','descripcion','cantidad'))
											->join(array('resguardosInventario','ri'),'inventario.id','=','ri.idinventario','LEFT')
											->where('ri.idresguardo',$recibo["idresguardo"])->get()->fetch_all();
					}
					else if($recibo["tipo"] == 0)
					{
						$recibo["equipos"] = inventario::select(array('inventario.id','codigo','categoria','tipoEquipo','marca','modelo','noSerie','descripcion','cantidad'))
											->join(array('recibosInventario','ri'),'inventario.id','=','ri.idinventario','LEFT')
											->where('ri.idrecibo',$folio)->get()->fetch_all();
					}
					$recibo["encargadoArea"] = areas::select(array('representante'))->where('clave',$recibo['area'])->get()->fetch_assoc();
					$recibo["coordinador"] = areas::select(array('representante'))->where('clave','Coordinacion')->get()->fetch_assoc();
					//Iniciamos creacion del PDF
					$pdfText .='<div style="height:100%; font-family: Arial,Helvetica Neue,Helvetica,sans-serif; font-size:11px;">
									<div style=" float:left;">
										<div style="float:left; width:19%; height:120px; text-align:center;">
											<img src="'.ROOT.'/images/c4.jpg" style="width:120px height:120px;"></img>
										</div>
										<div style="float:left; width:60%; height:120px; text-align:center; border:solid 1px #fff;">
											<h2 style="margin:20px 0px 0px 0px;">Centro de Control, Comando, Comunicaciones y Computo C4 Yucatan</h2>
											<h3>RECIBO DE MOBILIARIO Y EQUIPO</h3>
											<h3>'.$recibo["idunico"].'</h3>
										</div>
										<div style="float:left; width:19%; height:120px; text-align:center;">
											<img src="'.ROOT.'/images/cesp.jpg" style="width:100px height:100px;"></img>
										</div>
									</div>
									<hr>
									<div style="float:left; width:92%; margin-left:7.5%; font-size:12px;">
										<div style="float:left; width:70%;">
											<div style="float:left; width:17%; padding:3px 0px; font-weight:bold;">Dependencia:</div>
											<div style="float:left; width:82%; padding:3px 0px;">'.$recibo["dependencia"].'</div>
										</div>
										<div style="float:left; width:29%; border: solid 1px white;">
											<div style="float:left; width:30%; padding:3px 0px; font-weight:bold;">Depto.</div>
											<div style="float:left; width:69%; padding:3px 0px;">'.$recibo["departamento"].'</div>
										</div>
										<div style="float:left; width:100%; padding:3px 0px;">
											<div style="float:left; width:18%; padding:3px 0px; font-weight:bold;">Jefe/Responsable:</div>
											<div style="float:left; width:82%; padding:3px 0px;">'.$recibo["nombre"].'</div>
										</div>
										<div style="float:left; width:100%; padding:3px 0px;">
											<div style="float:left; width:17%; padding:3px 0px; font-weight:bold;">Grado o Puesto:</div>
											<div style="float:left; width:83%; padding:3px 0px;">'.$recibo["cargo"].'</div>
										</div>
									</div>
									<hr>';
					if(sizeof($recibo["equipos"]) > 0)
					{
						$pdfText .= '<div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center; font-weight:bold;">Cantidad</div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center; font-weight:bold;">Tipo de Equipo</div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center; font-weight:bold;">Marca</div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center; font-weight:bold;">Modelo</div>
										<div style="float:left; width:24%; padding:3px 0px; text-align:center; font-weight:bold;">Serie</div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center; font-weight:bold;">Observ.</div>';
						for($i = 0; $i < sizeof($recibo["equipos"]);$i++)
						{
							$pdfText .='<div style="float:left; width:15%; padding:3px 0px; text-align:center;">'.$recibo["equipos"][$i]["cantidad"].'</div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center;">'.$recibo["equipos"][$i]["tipoEquipo"].'</div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center;">'.$recibo["equipos"][$i]["marca"].'</div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center;">'.($recibo["equipos"][$i]["modelo"] != "" ? $recibo["equipos"][$i]["modelo"] : "&nbsp;" ).'</div>
										<div style="float:left; width:24%; padding:3px 0px; text-align:center;">'.$recibo["equipos"][$i]["noSerie"].'</div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center;">'.$recibo["equipos"][$i]["descripcion"].'</div>
								';
						}
						$pdfText .= '</div>';
					}
					$pdfText .='<div style="float:left; width:100%; padding:3px 0px; margin-top:25px;">'.$recibo["nota"].'</div></div>
								<div style="position:absolute; bottom:1.5cm; width:100%; font-family: Arial,Helvetica Neue,Helvetica,sans-serif; font-size:11px;">
									<div style="float:left; width:50%; padding:3px 0px; text-align:center;">
										<div>Coordinador del area de '.$recibo["area"].'</div>
										</br></br>
										<div style="margin-top:40px;">___________________________________</div>
										<div>'.$recibo["encargadoArea"]["representante"].'</div>
									</div>
									<div style="float:left; width:50%; padding:3px 0px; text-align:center;">
										<div>Entregue de Conformidad</div>
										</br></br>
										<div style="margin-top:40px;">___________________________________</div>
										<div>'.$recibo["nombre"].'</div>
									</div>
								</div>';
				}
        		else
      				$pdfText .= "Recibo no Encontrado";
			}
			else
				$pdfText .= $this->_validar->getWarnings();
		}
		else
			$pdfText .= "Parametro no recibido correctamente";
		//echo $pdfText;
		$pdf -> WriteHTML($pdfText);
		$pdf -> Output("recibo.pdf","I");
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
		$this->_data["equipos"] = isset($data["equipos"]) ? $data["equipos"] : "[]";
	}
}
?>