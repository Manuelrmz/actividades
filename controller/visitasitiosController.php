<?php
class visitasitiosController extends Controller
{
	private $_data;
	public function __construct() 
    {
        parent::__construct();
    }
    public function add()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('rptvisitasitio');
        $this->visitasitio();
        $db = new queryBuilder();
        $return = array('ok'=>false,'msg'=>'');
        $condi = true;
        $condi = $condi && $this->_validar->MinMax($this->_data["reporta"],1,150,"Reporta");
        $condi = $condi && $this->_validar->NoEmpty($this->_data["motivo"],"Motivo");
        $condi = $condi && $this->_validar->Date($this->_data["fechaVisita"],"Fecha de Visita");
        if($this->_data["odometro"] != "")
        	$condi = $condi && $this->_validar->Int($this->_data["odometro"],"Odometro");
        $condi = $condi && $this->_validar->validarExpresion($this->_data["sitios"],"/^([0-9],*)/","Sitios");
        $condi = $condi && $this->_validar->validarExpresion($this->_data["personal"],"/^([0-9],*)/","Personal");
        $condi = $condi && $this->_validar->NoEmpty($this->_data["comentarios"],"Comentarios");
        if($condi)
        {
        	unset($this->_data["id"]);
        	$this->_data["folio"] = $this->getNextFolio();
        	$this->_data["usuarioAlta"] = $_SESSION["userData"]["usuario"];
        	$this->_data["fechaAlta"] = date('Y-m-d H:i:s');
			$visita = $db->table('visitasitios')->insert($this->_data);
			if($visita)
			{
				$this->_data["id"] = $visita;
				$return["msg"] = "Visita Insertada Correctamente";
				$return["ok"] = true;
				$return["id"] = $visita;
				if(isset($_FILES["files"]))
				{
					$permitidas = array('jpg','JPG','jpeg','jpge','png','gif');
					for($i = 0; $i < sizeof($_FILES["files"]["name"]); $i++)
					{
						if($_FILES["files"]["tmp_name"][$i] != "")
						{
							$this->_data["archivo"] = preg_replace(array('/á/','/é/','/í/','/ó/','/ú/','/Á/','/É/','/Í/','/Ó/','/Ú/'),array('a','e','i','o','u','A','E','I','O','U'),$_FILES["files"]["name"][$i]);
							$extension = explode(".",$this->_data["archivo"]);
							$extension = $extension[count($extension)-1];
							if(in_array($extension,$permitidas))
							{
								$this->_data["filename"] = tempnam(ROOT . 'private/visitassitios/','');
								unlink($this->_data["filename"]);
								$this->_data["filename"] = explode('/',$this->_data["filename"]);
								$this->_data["filename"] = $this->_data["filename"][count($this->_data["filename"])-1].'.'.$extension;
						        if(move_uploaded_file($_FILES["files"]['tmp_name'][$i], ROOT . 'private/visitassitios/'.$this->_data["filename"]))
						        {
						        	$this->_data["filetype"] = $_FILES["files"]['type'][$i];
						        	$this->_data["filesize"] = $_FILES["files"]['size'][$i];
									$transaccion = $db->transaction(function($q)
									{
										$imagen = $q->table('archivos')->insert(array('filename'=>$this->_data["filename"],'name'=>$this->_data["archivo"],'type'=>$this->_data["filetype"],'size'=>$this->_data["filesize"],'usuarioAlta'=>$_SESSION["userData"]["usuario"],'fechaAlta'=>date('Y-m-d H:i:s')));
										$q->table('archivosVisita')->insert(array('idarchivo'=>$imagen,'idvisita'=>$this->_data["id"],'descripcion'=>''));
									});
									if($transaccion)
										$return["msg"] .= "</br>".$this->_data["archivo"]." guardada correctamente";
									else
									{
										$return["msg"] .= "</br>No fue posible guardar ".$this->_data["archivo"];
										unlink(ROOT . 'private/visitassitios/'.$this->_data["filename"]);
									}
						        }
						        else
						        	$return["msg"] .= "</br>No fue posible guardar ".$this->_data["archivo"];
							}
							else
								$return["msg"] .= "</br>La imagen ".$this->_data["archivo"]." no viene con un formato correcto.";
						}
					}
				}
			}
			else
				$return["msg"] = "Ocurrio un error insertando su visita: ".$visita;
        }
        else
        	$return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
	}
    public function update()
    {
    	Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('rptvisitasitio');
        $this->visitasitio();
        $db = new queryBuilder();
        $return = array('ok'=>false,'msg'=>'');
        $condi = true;
        $condi = $condi && $this->_validar->Int($this->_data["id"],"Folio");
        $condi = $condi && $this->_validar->MinMax($this->_data["reporta"],1,150,"Reporta");
        $condi = $condi && $this->_validar->NoEmpty($this->_data["motivo"],"Motivo");
        $condi = $condi && $this->_validar->Date($this->_data["fechaVisita"],"Fecha de Visita");
        if($this->_data["odometro"] != "")
        	$condi = $condi && $this->_validar->Int($this->_data["odometro"],"Odometro");
        $condi = $condi && $this->_validar->validarExpresion($this->_data["sitios"],"/^([0-9],*)/","Sitios");
        $condi = $condi && $this->_validar->validarExpresion($this->_data["personal"],"/^([0-9],*)/","Personal");
        $condi = $condi && $this->_validar->NoEmpty($this->_data["comentarios"],"Comentarios");
        if($condi)
        {
        	$this->_data["usuarioMod"] = $_SESSION["userData"]["usuario"];
			$visita = $db->table('visitasitios')->where('id',$this->_data["id"])->update($this->_data);
			$return["msg"] = "Visita Modificada Correctamente";
			$return["ok"] = true;
			if(isset($_FILES["files"]))
			{
				$permitidas = array('jpg','JPG','jpeg','jpge','png','gif');
				for($i = 0; $i < sizeof($_FILES["files"]["name"]); $i++)
				{
					if($_FILES["files"]["tmp_name"][$i] != "")
					{
						$this->_data["archivo"] = preg_replace(array('/á/','/é/','/í/','/ó/','/ú/','/Á/','/É/','/Í/','/Ó/','/Ú/'),array('a','e','i','o','u','A','E','I','O','U'),$_FILES["files"]["name"][$i]);
						$extension = explode(".",$this->_data["archivo"]);
						$extension = $extension[count($extension)-1];
						if(in_array($extension,$permitidas))
						{
							$this->_data["filename"] = tempnam(ROOT . 'private/visitassitios/','');
							unlink($this->_data["filename"]);
							$this->_data["filename"] = explode('/',$this->_data["filename"]);
							$this->_data["filename"] = $this->_data["filename"][count($this->_data["filename"])-1].'.'.$extension;
					        if(move_uploaded_file($_FILES["files"]['tmp_name'][$i], ROOT . 'private/visitassitios/'.$this->_data["filename"]))
					        {
					        	$this->_data["filetype"] = $_FILES["files"]['type'][$i];
					        	$this->_data["filesize"] = $_FILES["files"]['size'][$i];
								$transaccion = $db->transaction(function($q)
								{
									$imagen = $q->table('archivos')->insert(array('filename'=>$this->_data["filename"],'name'=>$this->_data["archivo"],'type'=>$this->_data["filetype"],'size'=>$this->_data["filesize"],'usuarioAlta'=>$_SESSION["userData"]["usuario"],'fechaAlta'=>date('Y-m-d H:i:s')));
									$q->table('archivosVisita')->insert(array('idarchivo'=>$imagen,'idvisita'=>$this->_data["id"],'descripcion'=>''));
								});
								if($transaccion)
									$return["msg"] .= "</br>".$this->_data["archivo"]." guardada correctamente";
								else
								{
									$return["msg"] .= "</br>No fue posible guardar ".$this->_data["archivo"];
									unlink(ROOT . 'private/visitassitios/'.$this->_data["filename"]);
								}
					        }
					        else
					        	$return["msg"] .= "</br>No fue posible guardar ".$this->_data["archivo"];
						}
						else
							$return["msg"] .= "</br>La imagen ".$this->_data["archivo"]." no viene con un formato correcto.";
					}
				}
			}
		}
		else
        	$return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
    }
    public function createfile($id)
	{
		Session::regenerateId();
		Session::securitySession();
		$this->validatePermissions('rptvisitasitio');
		include_once(CORE_PATH . 'mpdf/mpdf.php');
		$pdfText = "<div style='height:100%; font-family: Arial,Helvetica Neue,Helvetica,sans-serif; font-size:11px;'>";
		$pdf = new mpdf('utf8','A4', '', '',1,1,1,1);
		if(isset($id))
		{
			$folio = (integer)$id;
			$condi = true;
			$condi = $condi && $this->_validar->Int($folio,'Folio');
			if($condi)
			{
				$visita = visitasitios::select(array('CONCAT("RVS-C4-RADIO-",visitasitios.folio,"-",visitasitios.anio)'=>'folio','visitasitios.reporta','visitasitios.motivo','visitasitios.vehiculo','visitasitios.placas','visitasitios.odometro','visitasitios.sitios','visitasitios.personal','visitasitios.comentarios','visitasitios.fechaVisita','CONCAT(u.nombres," ",u.apellidos)'=>'nombres'))
													->join(array('usuarios','u'),'visitasitios.usuarioAlta','=','u.usuario','LEFT')
													->where('visitasitios.id',$id)->get()->fetch_assoc();
				if($visita)
				{
					//Cargamos Sitios
					$visita["sitios"] = explode(',',$visita["sitios"]);
					$sitios = sitios::select(array('nombre'))->where('id',$visita["sitios"][0]);
					for($i = 1; $i < sizeof($visita["sitios"]); $i++)
					{
						$sitios = $sitios->orWhere('id',$visita["sitios"][$i]);
					}
					$visita["sitios"] = $sitios->get()->fetch_all();
					//Cargamos Personal
					$visita["personal"] = explode(',',$visita["personal"]);
					$personal = usuarios::select(array('CONCAT(nombres," ",apellidos)'=>'nombres'))->where('id',$visita["personal"][0]);
					for($i = 1; $i < sizeof($visita["personal"]); $i++)
					{
						$personal = $personal->orWhere('id',$visita["personal"][$i]);
					}
					$visita["personal"] = $personal->get()->fetch_all();
					//Cargamos sus imagenes
					$visita["imagenes"] = archivos::select(array('archivos.filename'))->join(array('archivosVisita','av'),'archivos.id','=','av.idarchivo','LEFT')->where('av.idvisita',$folio)->get()->fetch_all();
					//Iniciamos creacion del PDF
					// echo '<pre>';
					// var_dump($visita);
					// echo '</pre>';
					if($visita)
					{
						$pdfText .='<div style=" float:left;">
										<div style="float:left; width:19%; height:70px; text-align:center;">
											<img src="'.ROOT.'/images/c4.jpg" style="width:70px height:70px;"></img>
										</div>
										<div style="float:left; width:60%; height:70px; text-align:center; border:solid 1px #fff;">
											<h3 style="margin:20px 0px 0px 0px;">REPORTE DE VISITA A SITIOS C4 RADIO</h3>
										</div>
										<div style="float:left; width:19%; height:70px; text-align:center;">&nbsp;</div>
									</div>
									<div style="float:left; width:82%; font-weight:bold; text-align:right;">Folio:&nbsp;</div>
									<div style="float:left; width:17%;">'.$visita["folio"].'</div>
									<div style="float:left; margin-top:10px;">
										<div style="float:left; width:11.666666667%; font-weight:bold;">FECHA:</div>
										<div style="float:left; width:16.666666667%;">'.date_format(date_create($visita["fechaVisita"]),'d-m-Y').'</div>
										<div style="float:left; width:11.666666667%; font-weight:bold;">REPORTA:</div>
										<div style="float:left; width:16.666666667%; text-decoration:underline;">'.$visita["reporta"].'</div>
										<div style="float:left; width:11.666666667%; font-weight:bold;">ELABORO:</div>
										<div style="float:left; width:30.666666667%; text-decoration:underline;">'.$visita["nombres"].'</div>
									</div>
									<div style="float:left; width:100%; font-weight:bold; margin-top:10px;">MOTIVO DE LA VISITA:</div>
									<div style="float:left; width:100%; margin-top:3px; text-decoration:underline;">'.$visita["motivo"].'</div>
									<div style="float:left; margin-top:10px;">
										<div style="float:left; width:11.666666667%; font-weight:bold;">VEHICULO:</div>
										<div style="float:left; width:16.666666667%; text-decoration:underline;">'.$visita["vehiculo"].'</div>
										<div style="float:left; width:11.666666667%; font-weight:bold;">PLACAS:</div>
										<div style="float:left; width:16.666666667%; text-decoration:underline;">'.$visita["placas"].'</div>
										<div style="float:left; width:11.666666667%; font-weight:bold;">ODOMETRO:</div>
										<div style="float:left; width:30.666666667%; text-decoration:underline;">'.$visita["odometro"].' km</div>
									</div>
									<div style="float:left; margin-top:10px; height:20px; background-color:#ccc; font-weight:bold;">SITIO</div>
									<div style="float:left; margin-top:10px;">
										<div style="float:left; width:15%; font-weight:bold; ">SITIOS VISITADOS:</div>
										<div style="float:left; width:83%; text-decoration:underline;">';
									$pdfText.= $visita["sitios"][0]["nombre"];
									for($i = 1; $i < sizeof($visita["sitios"]);$i++)
									{
										$pdfText.= ', '.$visita["sitios"][$i]["nombre"];
									}
						$pdfText .='	</div>
									</div>
									<div style="float:left; margin-top:10px; height:20px; background-color:#ccc; font-weight:bold;">PERSONAL QUE PARTICIPA EN LA VISITA</div>
									<div style="float:left; margin-top:10px;">
										<div style="float:left; width:15%; font-weight:bold; ">PERSONAL ENVIADO:</div>
										<div style="float:left; width:83%; text-decoration:underline;">';
									$pdfText.= $visita["personal"][0]["nombres"];
									for($i = 1; $i < sizeof($visita["personal"]);$i++)
									{
										$pdfText.= ', '.$visita["personal"][$i]["nombres"];
									}
						$pdfText .='</div>
									<div style="float:left; width:100%; margin-top:10px; font-weight:bold;">GALERIA FOTOGRAFICA:</div>
									<div style="float:left; height:500px; max-height:500px; margin-top:3px; border:solid 1px #000;">';
									if(sizeof($visita["imagenes"]) > 6)
										$tamañociclo = 6;
									else
										$tamañociclo = sizeof($visita["imagenes"]);
									for ($i=0; $i < $tamañociclo; $i++) 
									{
										if(file_exists(ROOT . 'private/visitassitios/'.$visita["imagenes"][$i]["filename"]))
										{
											$pdfText .= '<div style="float:left; width:250px; height:250px;"><img src="'.ROOT.'/private/visitassitios/'.$visita["imagenes"][$i]["filename"].'" style="margin:auto; height:auto; max-width:100%; width:auto;max-height:100%;"></div>';
										}
									}

						$pdfText .='</div>
									<div style="float:left; width:100%; font-weight:bold; margin-top:10px;">COMENTARIOS:</div>
									<div style="float:left; width:100%; margin-top:3px; height:100px; text-decoration:underline;">'.$visita["comentarios"].'</div>
									<div style="float:left; width:100%; text-align:center; font-weight:bold;">
										<br><br><br><br>ELABORÓ:<br><br><br><br>_________________________________<br>'.$visita["personal"][0]["nombres"].'<br>RADIOCOMUNICACIÓN C4
									</div>
									';
					}
					else
						$pdfText = "Oficio no Encontrado";
				}
			}
		}
		else
			$pdfText = "Parametro no recibido correctamente";
		$pdfText .= '</div>';
		//echo $pdfText;
		$pdf -> WriteHTML($pdfText);
  		$pdf -> Output("boletaFiscalia.pdf","I");
	}
    public function getbyid()
    {
    	Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('rptvisitasitio');
        $this->visitasitio();
        $return = array('ok'=>false,'msg'=>'');
        $condi = true;
        $condi = $condi && $this->_validar->Int($this->_data["id"],"Folio");
        if($condi)
        {
        	$visita = visitasitios::select(array('id'=>'folio','reporta','motivo','vehiculo','placas','odometro','sitios','personal','comentarios','fechaVisita'))->where('id',$this->_data["id"])->get()->fetch_assoc();
        	if($visita)
        	{
        		$return["msg"] = $visita;
        		$return["ok"] = true;
        	}
        	else
        		$return["msg"] = "Error obteniendo la visita: ".$visita;
        }
        else
        	$return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
    }
    public function getNextFolio()
    {
    	Session::regenerateId();
		Session::securitySession();
		$currentId = visitasitios::select('folio')->where('anio',date('Y'))->orderBy('folio','DESC')->first();
		return $currentId ? $currentId[0] + 1 : 1;
    }
    public function getVisitasForTable()
    {
    	Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('rptvisitasitio');
        $fechaIni = trim(isset($_POST["fechaIni"]) ? $_POST["fechaIni"] : "");
        $fechaFin = trim(isset($_POST["fechaFin"]) ? $_POST["fechaFin"] : "");
        $condi = true;
        if($fechaIni != "")
        	$condi = $condi && $this->_validar->Date($fechaIni,"Fecha de Inicio");
        if($fechaFin != "")
        	$condi = $condi && $this->_validar->Date($fechaFin,"Fecha Final");
        if($condi)
        {
        	$visitas = visitasitios::select(array("id"=>"folio",'CONCAT("RVS-C4-RADIO-",folio,"-",anio)'=>'folioFull','reporta','motivo','usuarioAlta'=>'captura','fechaVisita','fechaAlta'=>'fecha'));
        	if($fechaIni != "" || $fechaFin != "")
        	{
	        	if($fechaIni != "")
	        		$visitas = $visitas->where('fechaVisita','>=',$fechaIni." 00:00:00");
	        	if($fechaFin != "")
	        		$visitas = $visitas->where('fechaVisita','<=',$fechaFin." 23:59:59");
	        }
	        else
	        	$visitas = $visitas->where('fechaVisita','>=',date("Y-m-d H:i:s", strtotime ("-72 hours")));
        	$visitas = $visitas->get()->fetch_all();
        	if($visitas)
        	{
        		$return["msg"] = $visitas;
        		$return["ok"] = true;
        	}
        	else
        		$return["msg"] = "No se encontraron Visitas";
        }
        else
        	$return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
    }
    public function visitasitio()
    {
    	$this->_data["id"] = isset($_POST["id"]) ? (integer)$_POST["id"] : 0;
    	$this->_data["anio"] = isset($_POST["anio"]) ? (integer)$_POST["anio"] : date("Y");
    	$this->_data["reporta"] = isset($_POST["reporta"]) ? $_POST["reporta"] : "";
    	$this->_data["motivo"] = isset($_POST["motivo"]) ? $_POST["motivo"] : "";
    	$this->_data["fechaVisita"] = isset($_POST["fechaVisita"]) ? $_POST["fechaVisita"] : date("Y-m-d");
    	$this->_data["vehiculo"] = isset($_POST["vehiculo"]) ? $_POST["vehiculo"] : "";
    	$this->_data["placas"] = isset($_POST["placas"]) ? $_POST["placas"] : "";
    	$this->_data["odometro"] = isset($_POST["odometro"]) ? (integer)$_POST["odometro"] : 0;
    	$this->_data["sitios"] = isset($_POST["sitios"]) ? $_POST["sitios"] : "";
    	$this->_data["personal"] = isset($_POST["personal"]) ? $_POST["personal"] : "";
    	$this->_data["comentarios"] = isset($_POST["comentarios"]) ? $_POST["comentarios"] : "";
    }
}
?>