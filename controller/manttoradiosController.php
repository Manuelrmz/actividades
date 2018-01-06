<?php
class manttoradiosController extends Controller
{
	private $_data;
	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radiosmantto');
        $this->_view->renderizar('index','C4 - Mantenimiento Radios');
	}
	public function add()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radiosmantto');
        $return = array('ok'=>false,'msg'=>'');
        $condi = true;
        $this->manttoradios();
        $condi = $condi && $this->_validar->MinMax($this->_data["reporta"],1,200,"Reporta");
        $condi = $condi && $this->_validar->NoEmpty($this->_data["motivo"],"Motivo");
        $condi = $condi && $this->_validar->Int($this->_data["dependencia"],"Dependencia");
        $condi = $condi && $this->_validar->Int($this->_data["asignacion"],"Asignacion");
        $condi = $condi && $this->_validar->Int($this->_data["rfsi"],"RFSI");
        $condi = $condi && $this->_validar->Int($this->_data["mantenimiento"],"Mantenimiento");
        $condi = $condi && $this->_validar->Int($this->_data["diagnostico"],"Diagnostico");
        $condi = $condi && $this->_validar->NoEmpty($this->_data["comentarios"],"Comentarios De la intervencion");
        if($condi)
        {
        	unset($this->_data["id"]);
        	$this->_data["folio"] = $this->getNextFolio();
        	$this->_data["anio"] = date('Y');
        	$this->_data["usuarioAlta"] = $_SESSION["userData"]["usuario"];
        	$this->_data["fechaAlta"] = date('Y-m-d H:i:s');
        	$mantenimiento = manttoradios::insert($this->_data);
        	if($mantenimiento)
        	{
        		$return["id"] = $mantenimiento;
        		$return["msg"] = "Mantenimiento ingresado correctamente: id: ".$this->_data["folio"];
        		$return["ok"] = true;
        	}
        	else
        		$return["msg"] = "Ocurrio un error insertando el nuevo mantenimiento";
        }
        else
        	$return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
	}
	public function update()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radiosmantto');
        $return = array('ok'=>false,'msg'=>'');
        $condi = true;
        $this->manttoradios();
        $condi = $condi && $this->_validar->Int($this->_data["id"],"ID");
        $condi = $condi && $this->_validar->MinMax($this->_data["reporta"],1,200,"Reporta");
        $condi = $condi && $this->_validar->NoEmpty($this->_data["motivo"],"Motivo");
        $condi = $condi && $this->_validar->Int($this->_data["dependencia"],"Dependencia");
        $condi = $condi && $this->_validar->Int($this->_data["asignacion"],"Asignacion");
        $condi = $condi && $this->_validar->Int($this->_data["rfsi"],"RFSI");
        $condi = $condi && $this->_validar->Int($this->_data["mantenimiento"],"Mantenimiento");
        $condi = $condi && $this->_validar->Int($this->_data["diagnostico"],"Diagnostico");
        $condi = $condi && $this->_validar->NoEmpty($this->_data["comentarios"],"Comentarios De la intervencion");
        if($condi)
        {
        	unset($this->_data["folio"]);
        	unset($this->_data["anio"]);
        	$this->_data["usuarioMod"] = $_SESSION["userData"]["usuario"];
        	$mantenimiento = manttoradios::where('id',$this->_data["id"])->update($this->_data);
    		$return["msg"] = "Mantenimiento Modificado correctamente";
    		$return["ok"] = true;
        }
        else
        	$return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
	}
	public function createfile($id)
	{
		Session::regenerateId();
		Session::securitySession();
		include_once(CORE_PATH . 'mpdf/mpdf.php');
		$pdfText = "<div style='height:100%; font-family: Arial,Helvetica Neue,Helvetica,sans-serif; font-size:12px;'>";
		$pdf = new mpdf('utf8','A4', '', '',1,1,1,1);
		if(isset($id))
		{
			$folio = (integer)$id;
			$condi = true;
			$condi = $condi && $this->_validar->Int($folio,'Folio');
			if($condi)
			{
				$mantenimiento = manttoradios::select(array('CONCAT("RMTR-C4-RADIO-",manttoradios.folio,"-",manttoradios.anio)'=>'folio','manttoradios.reporta','manttoradios.motivo','manttoradios.vehiculo','manttoradios.placas','manttoradios.unidad','manttoradios.rfsi','manttoradios.comentarios','manttoradios.observaciones','manttoradios.fechaAlta','d.nombre'=>'dependencia','a.nombre'=>'asignacion','r.nslogico','r.serie','r.tipo','r.comentario1','r.comentario2','tr.descripcion'=>'descripcion','m.nombre'=>'mantenimiento','di.nombre'=>'diagnostico','CONCAT(u.nombres," ",u.apellidos)'=>'nombres'))
													->join(array('dependencias','d'),'manttoradios.dependencia','=','d.id','LEFT')
													->join(array('asignacion','a'),'manttoradios.asignacion','=','a.id','LEFT')
													->join(array('equiposradios','r'),'manttoradios.rfsi','=','r.rfsi','LEFT')
													->join(array('tipoequiporadios','tr'),'r.tipo','=','tr.clave','LEFT')
													->join(array('mantenimientos','m'),'manttoradios.mantenimiento','=','m.id','LEFT')
													->join(array('diagnostico','di'),'manttoradios.diagnostico','=','di.id','LEFT')
													->join(array('usuarios','u'),'manttoradios.usuarioAlta','=','u.usuario','LEFT')
													->where('manttoradios.id',$id)->get()->fetch_assoc();
				if($mantenimiento)
				{
					$pdfText .='<div style=" float:left;">
									<div style="float:left; width:19%; height:100px; text-align:center;"><img src="'.ROOT.'/images/cesp.jpg" style="width:100px height:100px;"></img></div>
									<div style="float:left; width:60%; height:100px; text-align:center; border:solid 1px #fff;">
										<h3 style="margin:35px 0px 0px 0px;">REPORTE DE MANTENIMIENTO</h3>
										<h5 style="margin:0px 0px 0px 0px;">TALLER DE RADIOS</h5>
									</div>
									<div style="float:left; width:19%; height:100px; text-align:center;"><img src="'.ROOT.'/images/c4.jpg" style="width:100px height:100px; margin-top:5px;"></img></div>
								</div>
								<div style="float:left; height:10px; background-color:#98A04A;"></div>
								<div style="float:left; margin-top:10px;">
									<div style="float:left; width:11.666666667%; font-weight:bold;">FECHA:</div>
									<div style="float:left; width:16.666666667%;">'.date_format(date_create($mantenimiento["fechaAlta"]),'d-m-Y').'</div>
									<div style="float:left; width:11.666666667%; font-weight:bold;">HORA:</div>
									<div style="float:left; width:16.666666667%;">'.date_format(date_create($mantenimiento["fechaAlta"]),'H:i:s').'</div>
									<div style="float:left; width:11.666666667%; font-weight:bold;">FOLIO:</div>
									<div style="float:left; width:30.666666667%;">'.$mantenimiento["folio"].'</div>
								</div>
								<div style="float:left; margin-top:10px;">
									<div style="float:left; width:10%; font-weight:bold;">REPORTA:</div>
									<div style="float:left; width:89%;">'.$mantenimiento["reporta"].'</div>
								</div>
								<div style="float:left; margin-top:10px; height:100px;">
									<div style="float:left; width:100%; font-weight:bold;">MOTIVO:</div>
									<div style="float:left; width:100%;">'.$mantenimiento["motivo"].'</div>
								</div>
								<div style="float:left; height:10px; background-color:#98A04A;"></div>
								<div style="float:left; margin-top:10px;">
									<div style="float:left; width:11.666666667%; font-weight:bold;">VEHICULO:</div>
									<div style="float:left; width:16.666666667%;">'.$mantenimiento["vehiculo"].'</div>
									<div style="float:left; width:11.666666667%; font-weight:bold;">PLACAS:</div>
									<div style="float:left; width:16.666666667%;">'.$mantenimiento["placas"].'</div>
									<div style="float:left; width:11.666666667%; font-weight:bold;">UNIDAD:</div>
									<div style="float:left; width:30.666666667%;">'.$mantenimiento["unidad"].'</div>
								</div>
								<div style="float:left; margin-top:10px;">
									<div style="float:left; width:12%; font-weight:bold;">DEPENDENCIA:</div>
									<div style="float:left; width:87%;">'.$mantenimiento["dependencia"].'</div>
								</div>
								<div style="float:left; margin-top:10px;">
									<div style="float:left; width:12%; font-weight:bold;">ASIGNACION:</div>
									<div style="float:left; width:87%;">'.$mantenimiento["asignacion"].'</div>
								</div>
								<div style="float:left; height:10px; background-color:#98A04A; margin-top:10px;"></div>
								<div style="float:left; margin-top:10px;">
									<div style="float:left; width:11.666666667%; font-weight:bold;">RFSI:</div>
									<div style="float:left; width:16.666666667%;">'.$mantenimiento["rfsi"].'</div>
									<div style="float:left; width:11.666666667%; font-weight:bold;">SERIE:</div>
									<div style="float:left; width:26.666666667%;">'.$mantenimiento["serie"].'</div>
									<div style="float:left; width:11.666666667%; font-weight:bold;">N/S LOGICO:</div>
									<div style="float:left; width:20.666666667%;">'.$mantenimiento["nslogico"].'</div>
								</div>
								<div style="float:left; margin-top:10px;">
									<div style="float:left; width:20%; font-weight:bold;">TIPO DE TERMINAL:</div>
									<div style="float:left; width:20%;">'.$mantenimiento["tipo"].'</div>
									<div style="float:left; width:59%;">'.$mantenimiento["descripcion"].'</div>
								</div>
								<div style="float:left; margin-top:10px;">
									<div style="float:left; width:100%; font-weight:bold;">COMENTARIOS DEL TERMINAL:</div>
									<div style="float:left; width:100%;">'.$mantenimiento["comentario1"].'<br>'.$mantenimiento["comentario2"].'</div>
								</div>
								<div style="float:left; margin-top:10px;">
									<div style="float:left; width:24%; font-weight:bold;">TIPO MANTENIMIENTO:</div>
									<div style="float:left; width:24%;">'.$mantenimiento["mantenimiento"].'</div>
									<div style="float:left; width:24%; font-weight:bold;">DIAGNOSTICO:</div>
									<div style="float:left; width:24%;">'.$mantenimiento["diagnostico"].'</div>
								</div>
								<div style="float:left; margin-top:10px;">
									<div style="float:left; width:35%; font-weight:bold;">PERSONAL QUE REALIZA LA INTERVENCION:</div>
									<div style="float:left; width:64%;">'.$mantenimiento["nombres"].'</div>
								</div>
								<div style="float:left; margin-top:10px; height:100px;">
									<div style="float:left; width:100%; font-weight:bold;">COMENTARIOS DE LA INTERVENCION:</div>
									<div style="float:left; width:100%;">'.$mantenimiento["comentarios"].'</div>
								</div>
								<div style="float:left; margin-top:10px; height:100px;">
									<div style="float:left; width:100%; font-weight:bold;">OBSERVACIONES:</div>
									<div style="float:left; width:100%;">'.$mantenimiento["observaciones"].'</div>
								</div>
								<div style="float:left; height:10px; background-color:#98A04A; margin-top:10px;"></div>
								<div style="float:left; width:49.99%; text-align:center; font-weight:bold;">
									<br><br><br><br><br><br><br><br><br>'.$mantenimiento["reporta"].'<br><br><br><br>_________________________________<br>NOMBRE Y FIRMA
								</div>
								<div style="float:left; width:49.99%; text-align:center; font-weight:bold;">
									<br><br><br><br><br><br><br><br><br>'.$mantenimiento["nombres"].'<br><br><br><br>_________________________________<br>NOMBRE Y FIRMA
								</div>
								';
				}
				else
					$pdfText = "Oficio no Encontrado";
			}
		}
		else
			$pdfText = "Parametro no recibido correctamente";
		$pdfText .= '</div>';
		//echo $pdfText;
		$pdf -> WriteHTML($pdfText);
  		$pdf -> Output("boletaFiscalia.pdf","I");
	}
	public function getall()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radiosmantto');
        $return = array('ok'=>false,'msg'=>'');
        $fechaIni = trim(isset($_POST["fechaIni"]) ? $_POST["fechaIni"] : "");
        $fechaFin = trim(isset($_POST["fechaFin"]) ? $_POST["fechaFin"] : "");
        $condi = true;
        if($fechaIni != "")
        	$condi = $condi && $this->_validar->Date($fechaIni,"Fecha de Inicio");
        if($fechaFin != "")
        	$condi = $condi && $this->_validar->Date($fechaFin,"Fecha Final");
        if($condi)
        {
        	$mantenimientos = manttoradios::select(array('manttoradios.id'=>'folio','CONCAT("RMTR-C4-RADIO-",manttoradios.folio,"-",manttoradios.anio)'=>'folioFull','manttoradios.reporta','d.nombre'=>'dependencia','a.nombre'=>'asignacion','manttoradios.RFSI','manttoradios.usuarioAlta'=>'capturo','manttoradios.fechaAlta'=>'fechaCreacion'))
        									->join(array('dependencias','d'),'manttoradios.dependencia','=','d.id','LEFT')
        									->join(array('asignacion','a'),'manttoradios.asignacion','=','a.id','LEFT');
        	if($fechaIni != "" || $fechaFin != "")
        	{
	        	if($fechaIni != "")
	        		$mantenimientos = $mantenimientos->where('fechaAlta','>=',$fechaIni." 00:00:00");
	        	if($fechaFin != "")
	        		$mantenimientos = $mantenimientos->where('fechaAlta','<=',$fechaFin." 23:59:59");
	        }
	        else
	        	$mantenimientos = $mantenimientos->where('fechaAlta','>=',date("Y-m-d H:i:s", strtotime ("-72 hours")));
        	$mantenimientos = $mantenimientos->get()->fetch_all();
        	if($mantenimientos)
        	{
        		$return["msg"] = $mantenimientos;
        		$return["ok"] = true;
        	}
        	else
        		$return["msg"] = "No se encontraron mantenimientos";
        }
        else
        	$return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
	}
	public function getbyid()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radiosmantto');
        $return = array('ok'=>false,'msg'=>'');
        $condi = true;
        $this->manttoradios();
        $condi = $condi && $this->_validar->Int($this->_data["id"],'Folio');
        if($condi)
        {
        	$mantenimiento = manttoradios::select(array('reporta','motivo','vehiculo','placas','unidad','dependencia','asignacion','rfsi','mantenimiento','diagnostico','comentarios','observaciones'))->where('id',$this->_data["id"])->get()->fetch_assoc();
        	if($mantenimiento)
        	{
        		$return["msg"] = $mantenimiento;
        		$return["ok"] = true;
        	}
        	else
        		$return["msg"] = "Ocurrio un error obteniendo el mantenimiento";
        }
        else
        	$return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
	}
	public function getNextFolio()
	{
		Session::regenerateId();
		Session::securitySession();
		$currentId = manttoradios::select('folio')->where('anio',date('Y'))->orderBy('folio','DESC')->first();
		return $currentId ? $currentId[0] + 1 : 1;
	}
	public function manttoradios()
	{
		$this->_data["id"] = isset($_POST["id"]) ? (integer)$_POST["id"] : 0;
		$this->_data["folio"] = isset($_POST["folio"]) ? (integer)$_POST["folio"] : 0;
		$this->_data["anio"] = isset($_POST["anio"]) ? (integer)$_POST["anio"] : 0;
		$this->_data["reporta"] = isset($_POST["reporta"]) ? $_POST["reporta"] : "";
		$this->_data["motivo"] = isset($_POST["motivo"]) ? $_POST["motivo"] : "";
		$this->_data["vehiculo"] = isset($_POST["vehiculo"]) ? $_POST["vehiculo"] : "";
		$this->_data["placas"] = isset($_POST["placas"]) ? $_POST["placas"] : "";
		$this->_data["unidad"] = isset($_POST["unidad"]) ? $_POST["unidad"] : "";
		$this->_data["dependencia"] = isset($_POST["dependencia"]) ? (integer)$_POST["dependencia"] : 0;
		$this->_data["asignacion"] = isset($_POST["asignacion"]) ? (integer)$_POST["asignacion"] : 0;
		$this->_data["rfsi"] = isset($_POST["rfsi"]) ? (integer)$_POST["rfsi"] : 0;
		$this->_data["mantenimiento"] = isset($_POST["mantenimiento"]) ? (integer)$_POST["mantenimiento"] : 0;
		$this->_data["diagnostico"] = isset($_POST["diagnostico"]) ? (integer)$_POST["diagnostico"] : 0;
		$this->_data["comentarios"] = isset($_POST["comentarios"]) ? $_POST["comentarios"] : "";
		$this->_data["observaciones"] = isset($_POST["observaciones"]) ? $_POST["observaciones"] : "";
	}
}
?>