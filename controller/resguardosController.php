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
				$this->_data["id"] = $resguardo;
				foreach ($equipList as $equip) {
					$q->table('resguardosInventario')->insert(array('idresguardo'=>$resguardo,'idinventario'=>$equip["id"]));
					$q->table('inventario')->where('id',$equip["id"])->update(array('status'=>1));
				}
			});
			if($transaccion)
			{
				$this->_return["msg"] = "Resguardo guardado correctamente";
				$this->_return["ok"] = true;
				$this->_return["id"] = $this->_data["id"];
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
				$resguardo["file"] = archivos::select(array('archivos.filename'))->join(array('archivosResguardo','ar'),'archivos.id','=','ar.idarchivo','LEFT')
										->where('ar.idresguardo',$id)
										->get()->fetch_assoc();
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
				$resguardo = resguardos::select(array('CONCAT("C4-RES/",idunico,"/",anio)'=>'idunico','nombre','dependencia','departamento','cargo','nota','area'))
                      ->where('id',$folio)
                      ->get()->fetch_assoc();
				if($resguardo)
				{
					//Cargamos Equipos
					$resguardo["equipos"] = inventario::select(array('inventario.id','cantidad','codigo','categoria','tipoEquipo','marca','modelo','noSerie','descripcion'))
								->join(array('resguardosInventario','ri'),'inventario.id','=','ri.idinventario','LEFT')
								->where('ri.idresguardo',$folio)->get()->fetch_all();
					$resguardo["encargadoArea"] = areas::select(array('representante'))->where('clave',$resguardo['area'])->get()->fetch_assoc();
					$resguardo["coordinador"] = areas::select(array('representante'))->where('clave','Coordinacion')->get()->fetch_assoc();
					//Iniciamos creacion del PDF
					$pdfText .='<div style="height:100%; font-family: Arial,Helvetica Neue,Helvetica,sans-serif; font-size:11px;">
									<div style=" float:left;">
										<div style="float:left; width:19%; height:120px; text-align:center;">
											<img src="'.ROOT.'/images/c4.jpg" style="width:120px height:120px;"></img>
										</div>
										<div style="float:left; width:60%; height:120px; text-align:center; border:solid 1px #fff;">
											<h2 style="margin:20px 0px 0px 0px;">Centro de Control, Comando, Comunicaciones y Computo C4 Yucatan</h2>
											<h3>RESGUARDO DE MOBILIARIO Y EQUIPO</h3>
											<h3>'.$resguardo["idunico"].'</h3>
										</div>
										<div style="float:left; width:19%; height:120px; text-align:center;">
											<img src="'.ROOT.'/images/cesp.jpg" style="width:100px height:100px;"></img>
										</div>
									</div>
									<hr>
									<div style="float:left; width:92%; margin-left:7.5%; font-size:12px;">
										<div style="float:left; width:70%;">
											<div style="float:left; width:17%; padding:3px 0px; font-weight:bold;">Dependencia:</div>
											<div style="float:left; width:82%; padding:3px 0px;">'.$resguardo["dependencia"].'</div>
										</div>
										<div style="float:left; width:29%; border: solid 1px white;">
											<div style="float:left; width:30%; padding:3px 0px; font-weight:bold;">Depto.</div>
											<div style="float:left; width:69%; padding:3px 0px;">'.$resguardo["departamento"].'</div>
										</div>
										<div style="float:left; width:100%; padding:3px 0px;">
											<div style="float:left; width:18%; padding:3px 0px; font-weight:bold;">Jefe/Responsable:</div>
											<div style="float:left; width:82%; padding:3px 0px;">'.$resguardo["nombre"].'</div>
										</div>
										<div style="float:left; width:100%; padding:3px 0px;">
											<div style="float:left; width:17%; padding:3px 0px; font-weight:bold;">Grado o Puesto:</div>
											<div style="float:left; width:83%; padding:3px 0px;">'.$resguardo["cargo"].'</div>
										</div>
									</div>
									<hr>';
					if(sizeof($resguardo["equipos"]) > 0)
					{
						$pdfText .= '<div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center; font-weight:bold;">Cantidad</div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center; font-weight:bold;">Tipo de Equipo</div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center; font-weight:bold;">Marca</div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center; font-weight:bold;">Modelo</div>
										<div style="float:left; width:24%; padding:3px 0px; text-align:center; font-weight:bold;">Serie</div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center; font-weight:bold;">Observ.</div>';
						for($i = 0; $i < sizeof($resguardo["equipos"]);$i++)
						{
							$pdfText .='<div style="float:left; width:15%; padding:3px 0px; text-align:center;">'.$resguardo["equipos"][$i]["cantidad"].'</div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center;">'.$resguardo["equipos"][$i]["tipoEquipo"].'</div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center;">'.$resguardo["equipos"][$i]["marca"].'</div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center;">'.($resguardo["equipos"][$i]["modelo"] != "" ? $resguardo["equipos"][$i]["modelo"] : "&nbsp;" ).'</div>
										<div style="float:left; width:24%; padding:3px 0px; text-align:center;">'.$resguardo["equipos"][$i]["noSerie"].'</div>
										<div style="float:left; width:15%; padding:3px 0px; text-align:center;">'.$resguardo["equipos"][$i]["descripcion"].'</div>
								';
						}
						$pdfText .= '</div>';
					}
					$pdfText .='<div style="float:left; width:100%; padding:3px 0px; margin-top:25px;">'.$resguardo["nota"].'</div>
								</div>
								<div style="position:absolute; bottom:1.5cm; width:100%; font-family: Arial,Helvetica Neue,Helvetica,sans-serif; font-size:11px;">
									<div style="float:left; width:33%; padding:3px 0px; text-align:center;">
										<div>Coordinador General del C4 Yucatan</div>
										<div style="margin-top:40px;">___________________________________</div>
										<div>'.$resguardo["coordinador"]["representante"].'</div>
									</div>
									<div style="float:left; width:33%; padding:3px 0px; text-align:center;">
										<div>Coordinador del area de '.$resguardo["area"].'</div>
										</br></br>
										<div style="margin-top:40px;">___________________________________</div>
										<div>'.$resguardo["encargadoArea"]["representante"].'</div>
									</div>
									<div style="float:left; width:33%; padding:3px 0px; text-align:center;">
										<div>Recibi de Conformidad</div>
										</br></br>
										<div style="margin-top:40px;">___________________________________</div>
										<div>'.$resguardo["nombre"].'</div>
									</div>
								</div>';
				}
        		else
      				$pdfText .= "Resguardo no Encontrado";
			}
			else
				$pdfText .= $this->_validar->getWarnings();
		}
		else
			$pdfText .= "Parametro no recibido correctamente";
		//echo $pdfText;
		$pdf -> WriteHTML($pdfText);
		$pdf -> Output("resguardo.pdf","I");
	}
	public function saveResguardoSigned()
	{
		Session::regenerateId();
		Session::securitySession();
		$db = new queryBuilder();
		$this->resguardo($_POST);
		$condi = true;
		$condi = $condi && $this->_validar->Int($this->_data["id"],"Folio");
		$condi = $condi && $this->_validar->MinInt($this->_data["id"],1,"Debe enviar un folio valido");
		if($condi)
		{
			$resguardo = resguardos::select(array('id'))->where('id',$this->_data["id"])->where('area',$_SESSION["userData"]["area"])->get()->fetch_assoc();
			if($resguardo)
			{
				if(isset($_FILES) && sizeof($_FILES) > 0)
                {
                    $fields = array_keys($_FILES);
                    foreach ($fields as $field)
                	{
                        if($_FILES[$field]["tmp_name"] != "")
                        {
                            $this->_data["archivo"] = preg_replace(array('/á/','/é/','/í/','/ó/','/ú/','/Á/','/É/','/Í/','/Ó/','/Ú/'),array('a','e','i','o','u','A','E','I','O','U'),$_FILES[$field]["name"]);
                            $extension = explode(".",$this->_data["archivo"]);
                            $extension = $extension[count($extension)-1];
                            $archivo = archivos::select(array('archivos.name','archivos.size'))->join(array('archivosResguardo','ar'),'archivos.id','=','ar.idarchivo','LEFT')->where('ar.idresguardo',$this->_data["id"])->get()->fetch_assoc();
                            if(!$archivo || ($archivo && ($archivo["name"] !== $_FILES[$field]["name"] || $archivo["size"] != $_FILES[$field]["size"])))
                            {
                                $this->_data["requireUpdate"] = $archivo ? true : false;
	                            if($_FILES[$field]["type"] === "application/pdf")
	                            {
	                                $this->_data["filename"] = tempnam(ROOT . 'private/resguardos/','');
	                                unlink($this->_data["filename"]);
	                                $this->_data["filename"] = explode('/',$this->_data["filename"]);
	                                $this->_data["filename"] = $this->_data["filename"][count($this->_data["filename"])-1].'.'.$extension;
	                                if(move_uploaded_file($_FILES[$field]['tmp_name'], ROOT . 'private/resguardos/'.$this->_data["filename"]))
	                                {
	                                    $this->_data["filetype"] = $_FILES[$field]['type'];
	                                    $this->_data["filesize"] = $_FILES[$field]['size'];
	                                    $transaccion = $db->transaction(function($q)
	                                    {
	                                        $imagen = $q->table('archivos')->insert(array('filename'=>$this->_data["filename"],'name'=>$this->_data["archivo"],'type'=>$this->_data["filetype"],'size'=>$this->_data["filesize"],'usuarioAlta'=>$_SESSION["userData"]["usuario"],'fechaAlta'=>date('Y-m-d H:i:s')));
	                                        if($this->_data["requireUpdate"])
                                            	$q->table('archivosResguardo')->where('idresguardo',$this->_data["id"])->update(array('idarchivo'=>$imagen));
                                        	else
	                                        	$q->table('archivosResguardo')->insert(array('idarchivo'=>$imagen,'idresguardo'=>$this->_data["id"]));
	                                    });
	                                    if($transaccion)
	                                	{
											$this->_return["msg"] = $this->_data["archivo"]." guardado correctamente";
											$this->_return["ok"] = true;
	                                    }
	                                    else
	                                    {
	                                        $this->_return["msg"] = "No fue posible guardar ".$this->_data["archivo"]." Error : ".$db->getError()["string"];
	                                        unlink(ROOT . 'private/resguardos/'.$this->_data["filename"]);
	                                    }
	                                }
	                                else
	                                    $this->_return["msg"] = "No fue posible guardar ".$this->_data["archivo"];
	                            }
	                            else
	                                $this->_return["msg"] = "El archivo ".$this->_data["archivo"]." no viene con un formato correcto.";
	                        }
	                        else
	                        	$this->_return["msg"] = "Este resguardo ya cuenta con su PDF firmado.";
                        }
                        else
                        	$this->_return["msg"] = "No se recibio un archivo para guardar";
                    }
                }
                else
                	$this->_return["msg"] = "No se recibio un archivo para guardar";
			}
			else
				$this->_return["msg"] = "No se encontro un resguardo con el folio enviado";
		}
		else
			$this->_return["msg"] = $this->_validar->getWarnings();
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