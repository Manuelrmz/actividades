<?php
class serviciosController extends Controller
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
    $this->validatePermissions('servicios');
    $this->_view->renderizar('index','C4 - Servicios');
  }
  public function add()
  {
    Session::regenerateId();
    Session::securitySession();
    $this->servicio();
    $db = new queryBuilder();
    $return = array('ok'=>false,'msg'=>'');
    $newId = 0;
    $equipos = "";
    $condi = true;
    $condi = $condi && $this->_validar->Int($this->_data["solicitante"],"Solicitante");
    $condi = $condi && $this->_validar->Int($this->_data["area"],"Area");
    $condi = $condi && $this->_validar->Int($this->_data["tipoServicio"],"Tipo Servicio");
    $condi = $condi && $this->_validar->Int($this->_data["usuarioAsignado"],"Usuario Asignado");
    $condi = $condi && $this->_validar->DateTime($this->_data["fechaInicio"],"Fecha Inicio");
    $condi = $condi && $this->_validar->Int($this->_data["estado"],"Estado");
    $condi = $condi && $this->_validar->NoEmpty($this->_data["detalles"],"Observacion");
    if($condi)
    {
      $lastService = $db->table('servicios')->select('idUnico')->where('area',$this->_data["area"])->orderby('idUnico','desc')->get()->fetch_assoc();
      $newId = $lastService ? ($lastService["idUnico"] + 1) : 1;
      $serviceData = array('idUnico'=>$newId,'solicitante'=>$this->_data["solicitante"],'tipoServicio'=>$this->_data["tipoServicio"],
                            'usuarioAsignado'=>$this->_data["usuarioAsignado"],'fechaInicio'=>$this->_data["fechaInicio"],'estado'=> ($this->_data["estado"] != 0 ? $this->_data["estado"] : 1),
                            'area'=>$this->_data["area"],'detalles'=>$this->_data["detalles"],'observacion'=>$this->_data["observacion"],'usuarioAlta'=>$_SESSION["userData"]["usuario"]);
      if($this->_data["estado"] == 3)
        $serviceData  ["fechaFin"] = date("Y-m-d H:i:s");
      $this->_data["serviceData"] = $serviceData;
      $transaccion = $db->transaction(function($q)
      {
        $servicio = $q->table('servicios')->insert($this->_data["serviceData"]);
        $this->_data["id"] = $servicio;
        if($this->_data["equipos"] != "")
          $equipos = $this->_validar->JSON($this->_data["equipos"]);
        if($equipos != "")
        {
          if($equipos["ok"])
          {
            foreach ($equipos["datos"] as $equipo) {
              $q->table("equiposServicio")->insert(array('idServicio'=>$servicio,'marca'=>$equipo->marca,'descripcion'=>$equipo->descripcion,'noSerie'=>$equipo->noSerie));
            }
          }
        }
      });
      if($transaccion)
      {
        $return["msg"].= "Servicio agregado correctamente";
        $return["ok"] = true;
        if(isset($_FILES) && sizeof($_FILES) > 0)
        {
          $fields = array_keys($_FILES);
          $permitidas = array('jpg','JPG','jpeg','jpge','png','gif');
          foreach ($fields as $field)
          {
            $this->_data["correspondence"] = ($field === 'imgDetails' ? 'detalles' : 'observacion');
            for($i = 0; $i < sizeof($_FILES[$field]["name"]); $i++)
            {
              if($_FILES[$field]["tmp_name"][$i] != "")
              {
                $this->_data["archivo"] = preg_replace(array('/á/','/é/','/í/','/ó/','/ú/','/Á/','/É/','/Í/','/Ó/','/Ú/'),array('a','e','i','o','u','A','E','I','O','U'),$_FILES[$field]["name"][$i]);
                $extension = explode(".",$this->_data["archivo"]);
                $extension = $extension[count($extension)-1];
                if(in_array($extension,$permitidas))
                {
                  $this->_data["filename"] = tempnam(ROOT . 'private/servicios/','');
                  unlink($this->_data["filename"]);
                  $this->_data["filename"] = explode('/',$this->_data["filename"]);
                  $this->_data["filename"] = $this->_data["filename"][count($this->_data["filename"])-1].'.'.$extension;
                  if(move_uploaded_file($_FILES[$field]['tmp_name'][$i], ROOT . 'private/servicios/'.$this->_data["filename"]))
                  {
                    $this->_data["filetype"] = $_FILES[$field]['type'][$i];
                    $this->_data["filesize"] = $_FILES[$field]['size'][$i];
                    $transaccion = $db->transaction(function($q)
                    {
                      $imagen = $q->table('archivos')->insert(array('filename'=>$this->_data["filename"],'name'=>$this->_data["archivo"],'type'=>$this->_data["filetype"],'size'=>$this->_data["filesize"],'usuarioAlta'=>$_SESSION["userData"]["usuario"],'fechaAlta'=>date('Y-m-d H:i:s')));
                      $q->table('archivosServicio')->insert(array('idarchivo'=>$imagen,'idServicio'=>$this->_data["id"],'campo'=>$this->_data["correspondence"],'descripcion'=>''));
                    });
                    if($transaccion)
                      $return["msg"] .= "\n".$this->_data["archivo"]." guardada correctamente";
                    else
                    {
                      $return["msg"] .= "\nNo fue posible guardar ".$this->_data["archivo"];
                      unlink(ROOT . 'private/servicios/'.$this->_data["filename"]);
                    }
                  }
                  else
                    $return["msg"] .= "\nNo fue posible guardar ".$this->_data["archivo"];
                }
                else
                  $return["msg"] .= "\nLa imagen ".$this->_data["archivo"]." no viene con un formato correcto.";
              }
            }
          }
        }
      }
      else
        $return["msg"].= "Ocurrio un error insertando el servicio: ".$db->getError()["string"];
    }
    else
      $return["msg"] = $this->_validar->getWarnings();
    echo json_encode($return);
  }
  public function updateById()
  {
    Session::regenerateId();
    Session::securitySession();
    $this->servicio();
    $db = new queryBuilder();
    $return = array('ok'=>false,'msg'=>'');
    $newId = 0;
    $equipos = "";
    $condi = true;
    $condi = $condi && $this->_validar->Int($this->_data["id"],"Folio");
    $condi = $condi && $this->_validar->Int($this->_data["solicitante"],"Solicitante");
    $condi = $condi && $this->_validar->Int($this->_data["area"],"Area");
    $condi = $condi && $this->_validar->Int($this->_data["tipoServicio"],"Tipo Servicio");
    $condi = $condi && $this->_validar->Int($this->_data["usuarioAsignado"],"Usuario Asignado");
    $condi = $condi && $this->_validar->DateTime($this->_data["fechaInicio"],"Fecha Inicio");
    $condi = $condi && $this->_validar->Int($this->_data["estado"],"Estado");
    $condi = $condi && $this->_validar->NoEmpty($this->_data["detalles"],"Observacion");
    if($condi)
    {
      $serviceData = array('solicitante'=>$this->_data["solicitante"],'tipoServicio'=>$this->_data["tipoServicio"],
                            'usuarioAsignado'=>$this->_data["usuarioAsignado"],'fechaInicio'=>$this->_data["fechaInicio"],'area'=>$this->_data["area"],'estado'=> $this->_data["estado"],
                            'detalles'=>$this->_data["detalles"],'observacion'=>$this->_data["observacion"],'usuarioMod'=>$_SESSION["userData"]["usuario"]);
      if($this->_data["estado"] == 3)
        $serviceData  ["fechaFin"] = date("Y-m-d H:i:s");
      $this->_data["serviceData"] = $serviceData;
      $transaccion = $db->transaction(function($q)
      {
        $servicio = $q->table('servicios')->where('id',$this->_data["id"])->update($this->_data["serviceData"]);
        if($this->_data["equipos"] != "")
          $equipos = $this->_validar->JSON($this->_data["equipos"]);
        if($equipos != "")
        {
          if($equipos["ok"])
          {
            foreach ($equipos["datos"] as $equipo) {
              if($equipo->idEquipo === null)
                $q->table("equiposServicio")->insert(array('idServicio'=>$this->_data["id"],'marca'=>$equipo->marca,'descripcion'=>$equipo->descripcion,'noSerie'=>$equipo->noSerie));
              else
                $q->table("equiposServicio")->where('id',$equipo->idEquipo)->where('idServicio',$this->_data["id"])->update(array('marca'=>$equipo->marca,'descripcion'=>$equipo->descripcion,'noSerie'=>$equipo->noSerie));
            }
          }
        }
        $deletedEquip = explode(",",$this->_data["deletedEquip"]);
        if(sizeOf($deletedEquip) > 0)
        {
          for($i = 0; $i < sizeOf($deletedEquip); $i++)
          {
            if($deletedEquip[$i] !== "")
              $q->table('equiposServicio')->where("id",$deletedEquip[$i])->where("idServicio",$this->_data["id"])->delete();
          }
        }
      });
      if($transaccion)
      {
        $return["msg"].= "Servicio actualizado correctamente";
        $return["ok"] = true;
        if(isset($_FILES) && sizeof($_FILES) > 0)
        {
          $fields = array_keys($_FILES);
          $permitidas = array('jpg','JPG','jpeg','jpge','png','gif');
          foreach ($fields as $field)
          {
            $this->_data["correspondence"] = ($field === 'imgDetails' ? 'detalles' : 'observacion');
            for($i = 0; $i < sizeof($_FILES[$field]["name"]); $i++)
            {
              if($_FILES[$field]["tmp_name"][$i] != "")
              {
                $this->_data["archivo"] = preg_replace(array('/á/','/é/','/í/','/ó/','/ú/','/Á/','/É/','/Í/','/Ó/','/Ú/'),array('a','e','i','o','u','A','E','I','O','U'),$_FILES[$field]["name"][$i]);
                $extension = explode(".",$this->_data["archivo"]);
                $extension = $extension[count($extension)-1];
                if(in_array($extension,$permitidas))
                {
                  $this->_data["filename"] = tempnam(ROOT . 'private/servicios/','');
                  unlink($this->_data["filename"]);
                  $this->_data["filename"] = explode('/',$this->_data["filename"]);
                  $this->_data["filename"] = $this->_data["filename"][count($this->_data["filename"])-1].'.'.$extension;
                  if(move_uploaded_file($_FILES[$field]['tmp_name'][$i], ROOT . 'private/servicios/'.$this->_data["filename"]))
                  {
                    $this->_data["filetype"] = $_FILES[$field]['type'][$i];
                    $this->_data["filesize"] = $_FILES[$field]['size'][$i];
                    $transaccion = $db->transaction(function($q)
                    {
                      $imagen = $q->table('archivos')->insert(array('filename'=>$this->_data["filename"],'name'=>$this->_data["archivo"],'type'=>$this->_data["filetype"],'size'=>$this->_data["filesize"],'usuarioAlta'=>$_SESSION["userData"]["usuario"],'fechaAlta'=>date('Y-m-d H:i:s')));
                      $q->table('archivosServicio')->insert(array('idarchivo'=>$imagen,'idServicio'=>$this->_data["id"],'campo'=>$this->_data["correspondence"],'descripcion'=>''));
                    });
                    if($transaccion)
                      $return["msg"] .= "\n".$this->_data["archivo"]." guardada correctamente";
                    else
                    {
                      $return["msg"] .= "\nNo fue posible guardar ".$this->_data["archivo"];
                      unlink(ROOT . 'private/servicios/'.$this->_data["filename"]);
                    }
                  }
                  else
                    $return["msg"] .= "\nNo fue posible guardar ".$this->_data["archivo"];
                }
                else
                  $return["msg"] .= "\nLa imagen ".$this->_data["archivo"]." no viene con un formato correcto.";
              }
            }
          }
        }
      }
      else
        $return["msg"].= "Ocurrio un error insertando el servicio: ".$db->getError()["string"];
    }
    else
      $return["msg"] = $this->_validar->getWarnings();
    echo json_encode($return);
  }
  public function getForTable()
  {
    Session::regenerateId();
    Session::securitySession();
    $return = array('ok'=>false,'msg'=>'');
    $this->servicio();
    $condi = true;
    if($this->_data["fechaInicio"] !== "")
      $condi = $condi && $this->_validar->Date($this->_data["fechaInicio"],"Fecha Inicio");
    if($condi)
    {
      $servicios = servicios::select(array('servicios.id'=>'folio','servicios.idUnico'=>'currentId','s.nombre'=>'solicitante','cs.nombre'=>'tipoServicio','CONCAT(u.nombres," ",u.apellidos)'=>'usuarioAsignado','servicios.fechaInicio','servicios.estado','CONCAT(us.nombres," ",us.apellidos)'=>'usuarioAlta','a.nombre'=>'area'))
                    ->join(array('solicitante','s'),'servicios.solicitante','=','s.id','LEFT')
                    ->join(array('catservicios','cs'),'servicios.tipoServicio','=','cs.id','LEFT')
                    ->join(array('usuarios','u'),'servicios.usuarioAsignado','=','u.id','LEFT')
                    ->join(array('usuarios','us'),'servicios.usuarioAlta','=','us.usuario','LEFT')
                    ->join(array('areas','a'),'servicios.area','=','a.id');
      if($this->_data["fechaInicio"] !== "" || $this->_data["area"] != 0 || $this->_data["solicitante"] != 0 || $this->_data["estado"] != 0)
      {
        if($this->_data["fechaInicio"] !== "")
          $servicios = $servicios->where('servicios.fechaInicio','>=',$this->_data["fechaInicio"].' 00:00:00')->where('servicios.fechaInicio','<=',$this->_data["fechaInicio"].' 23:59:59');
        if($this->_data["area"] != 0)
          $servicios = $servicios->where('servicios.area',$this->_data["area"]);
        if($this->_data["solicitante"] != 0)
          $servicios = $servicios->where('servicios.solicitante',$this->_data["solicitante"]);
        if($this->_data["estado"] != 0)
          $servicios = $servicios->where('servicios.estado',$this->_data["estado"]);
      }
      else
        $servicios = $servicios->where('servicios.fechaInicio','>=',date("Y-m-d H:i:s", strtotime ("-1 week")));

      $servicios = $servicios->get()->fetch_all();
      if($servicios)
      {
        $return["ok"] = true;
        $return["msg"] = $servicios;
      }
      else
        $return["msg"] = "No se encontraron servicios";
    }
    else
      $return["msg"] = $this->_validar->getWarnings();
    echo json_encode($return);
  }
  public function getById()
  {
    Session::regenerateId();
    Session::securitySession();
    $return = array('ok'=>false,'msg'=>'');
    $condi = true;
    $this->servicio();
    $condi = $condi && $this->_validar->Int($this->_data["id"],"Folio");
    if($condi)
    {
      $servicio = servicios::select(array('solicitante','tipoServicio','usuarioAsignado','fechaInicio','estado','detalles','observacion','area'))
                    ->where('id',$this->_data["id"])->get()->fetch_assoc();
      if($servicio)
      {
        $servicio["equipos"] = equiposServicio::select(array('id'=>'idEquipo','marca','descripcion','noSerie'))->where("idServicio",$this->_data["id"])->get()->fetch_all();
        $return["msg"] = $servicio;
        $return["ok"] = true;
      }
      else
        $return["msg"] = "Servicio no encontrado";
    }
    else
      $return["msg"] = $this->_validar->getWarnings();
    echo json_encode($return);
  }
  public function createServiceOrder($id)
  {
    Session::regenerateId();
    Session::securitySession();
    $this->validatePermissions('servicios');
    $this->servicio();
    include_once(CORE_PATH . 'mpdf/mpdf.php');
		$pdfText = "<div style='height:100%; font-family: Arial,Helvetica Neue,Helvetica,sans-serif; font-size:11px;'>";
		$pdf = new mpdf('utf8','A4', '', '',1,1,12,13);
		if(isset($id))
		{
			$folio = (integer)$id;
			$condi = true;
			$condi = $condi && $this->_validar->Int($folio,'Folio');
			if($condi)
			{
        $servicio = servicios::select(array('servicios.idUnico','s.nombre'=>'solicitante','s.cargo','s.dependencia','s.area'=>'areaSolicitante','s.edificio','s.telefono','s.extension','cs.nombre'=>'tipoServicio','CONCAT(u.nombres," ",u.apellidos)'=>'usuarioAsignado','c.cargo'=>'cargoUsuario','servicios.fechaInicio','servicios.fechaFin','servicios.estado','a.nombre'=>'area','servicios.detalles','servicios.observacion','CONCAT(us.nombres," ",us.apellidos)'=>'usuarioAlta'))
                      ->join(array('solicitante','s'),'servicios.solicitante','=','s.id','LEFT')
                      ->join(array('catservicios','cs'),'servicios.tipoServicio','=','cs.id','LEFT')
                      ->join(array('usuarios','u'),'servicios.usuarioAsignado','=','u.id','LEFT')
                      ->join(array('cargosUsuario','c'),'u.cargo','=','c.id','LEFT')
                      ->join(array('areas','a'),'servicios.area','=','a.id','LEFT')
                      ->join(array('usuarios','us'),'servicios.usuarioAlta','=','us.usuario','LEFT')
                      //->where('servicios.area',$_SESSION["userData"]["area"])
                      ->where('servicios.id',$folio)
                      ->get()->fetch_assoc();
				if($servicio)
				{
          //Cargamos Equipos
          $equipos = equiposServicio::select(array('marca','descripcion','noSerie'))->where('idServicio',$id)->get()->fetch_all();
					//Cargamos sus imagenes
					$servicio["imagenes"] = archivos::select(array('archivos.filename','aserv.campo'))->join(array('archivosServicio','aserv'),'archivos.id','=','aserv.idArchivo','LEFT')->where('aserv.idServicio',$folio)->get()->fetch_all();
					//Iniciamos creacion del PDF
					$pdfText .='<pagefooter name="myFooter1Even" content-left="Elaboro: '.$servicio["usuarioAlta"].', personal tecnico del area de '.$servicio["area"].'" content-right="{PAGENO} de {nb}" footer-style="font-family:sans-serif; font-size:7pt;" footer-style-left="" line="on" />
                      <setpagefooter name="myFooter1Even" page="myFooter1Even" value="on" show-this-page="1"/>
                      <div style=" float:left;">
      									<div style="float:left; width:19%; height:120px; text-align:center;">
      										<img src="'.ROOT.'/images/c4.jpg" style="width:120px height:120px;"></img>
      									</div>
      									<div style="float:left; width:60%; height:120px; text-align:center; border:solid 1px #fff;">
      										<h2 style="margin:20px 0px 0px 0px;">Centro de Control, Comando, Comunicaciones y Computo C4 Yucatan</h2>
                          <h3 style="margin:20px 0px 0px 0px;">Departamento de: '.$servicio["area"].'</h3>
      									</div>
      									<div style="float:left; width:19%; height:120px; text-align:center;">
      									  <img src="'.ROOT.'/images/cesp.jpg" style="width:100px height:100px;"></img>
      									</div>
			                </div>
                      <div style="float:left; width:100%; font-weight:bold; text-align:center;"><h2>Orden de Servicio</h2></div>
                      <div style="float:left; width:85%; margin-left:7.5%; font-size:12px;">
                        <div style="float:left; width:10%; padding:3px 0px; border:solid 1px #000;background-color:rgb(217,217,217); box-sizing: border-box;">NOMBRE:</div>
                        <div style="float:left; width:89%; padding:3px 0px; border-bottom:solid 1px #000; border-top:solid 1px #000; border-right:solid 1px #000; box-sizing: border-box;">'.$servicio["solicitante"].'</div>
                        <div style="float:left; width:10%; padding:3px 0px; border-left:solid 1px #000; border-bottom:solid 1px #000; border-right:solid 1px #000; background-color:rgb(217,217,217);">CARGO:</div>
                        <div style="float:left; width:89%; padding:3px 0px; border-right:solid 1px #000; border-bottom:solid 1px #000;">'.$servicio["cargo"].'</div>
                        <div style="float:left; width:10%; padding:3px 0px; border-left:solid 1px #000; border-bottom:solid 1px #000; border-right:solid 1px #000; background-color:rgb(217,217,217);">AREA:</div>
                        <div style="float:left; width:89%; padding:3px 0px; border-right:solid 1px #000; border-bottom:solid 1px #000;">'.$servicio["areaSolicitante"].'</div>
                        <div style="float:left; width:10%; padding:3px 0px; border-left:solid 1px #000; border-bottom:solid 1px #000; border-right:solid 1px #000; background-color:rgb(217,217,217);">EDIFICIO:</div>
                        <div style="float:left; width:89%; padding:3px 0px; border-right:solid 1px #000; border-bottom:solid 1px #000;">'.$servicio["edificio"].'</div>
                        <div style="float:left; width:10%; padding:3px 0px; border-left:solid 1px #000; border-bottom:solid 1px #000; border-right:solid 1px #000; background-color:rgb(217,217,217);">TEL/EXT:</div>
                        <div style="float:left; width:60%; padding:3px 0px; border-bottom:solid 1px #000;">'.$servicio["telefono"].'/'.$servicio["extension"].'</div>
                        <div style="float:left; width:11%; padding:3px 0px; border-bottom:solid 1px #000; background-color:rgb(217,217,217);"># SERVICIO:</div>
                        <div style="float:left; width:18%; padding:3px 0px; border-right:solid 1px #000; border-bottom:solid 1px #000; text-align:center;">'.$servicio["idUnico"].'</div>
                        <div style="float:left; width:49.5%; padding:3px 0px; border-left:solid 1px #000; border-bottom:solid 1px #000; border-right:solid 1px #000; background-color:rgb(217,217,217); text-align:center;">FECHA / HORA INICIAL </div>
                        <div style="float:left; width:49.5%; padding:3px 0px; border-right:solid 1px #000; border-bottom:solid 1px #000; background-color:rgb(217,217,217); text-align:center;">FECHA / HORA FINAL </div>
                        <div style="float:left; width:49.5%; padding:3px 0px; border-left:solid 1px #000; border-bottom:solid 1px #000; border-right:solid 1px #000; text-align:center;">'.$servicio["fechaInicio"].'</div>
                        <div style="float:left; width:49.5%; padding:3px 0px; border-right:solid 1px #000; border-bottom:solid 1px #000; text-align:center;">'.($servicio["fechaFin"] ? $servicio["fechaFin"] : "No Finalizado").'</div>
                        <div style="float:left; width:99.2%; padding:3px 0px; border-right:solid 1px #000; border-bottom:solid 1px #000; border-left:solid 1px #000; background-color:rgb(217,217,217); text-align:center;">DESCRIPCION GENERICA</div>
                        <div style="float:left; width:33%; padding:3px 0px; border-left:solid 1px #000; border-bottom:solid 1px #000; border-right:solid 1px #000; background-color:rgb(217,217,217); text-align:center;">MARCA</div>
                        <div style="float:left; width:33%; padding:3px 0px; border-bottom:solid 1px #000; border-right:solid 1px #000; background-color:rgb(217,217,217); text-align:center;">DESCRIPCION</div>
                        <div style="float:left; width:33%; padding:3px 0px; border-bottom:solid 1px #000; border-right:solid 1px #000; background-color:rgb(217,217,217); text-align:center;">N° SERIE</div>';
                        if($equipos)
                        {
                          for($i = 0; $i < sizeof($equipos);$i++)
                          {
                            $pdfText .='<div style="float:left; width:33%; padding:3px 0px; border-left:solid 1px #000; border-bottom:solid 1px #000; border-right:solid 1px #000; text-align:center;">'.$equipos[$i]["marca"].'</div>
                                        <div style="float:left; width:33%; padding:3px 0px; border-bottom:solid 1px #000; border-right:solid 1px #000; text-align:center;">'.$equipos[$i]["descripcion"].'</div>
                                        <div style="float:left; width:33%; padding:3px 0px; border-bottom:solid 1px #000; border-right:solid 1px #000; text-align:center;">'.$equipos[$i]["noSerie"].'</div>';
                          }
                        }
            $pdfText .='<div style="float:left; width:99.2%; padding:3px 0px; border-right:solid 1px #000; border-bottom:solid 1px #000; border-left:solid 1px #000; background-color:rgb(217,217,217); text-align:center;">DETALLES DEL SERVICIO</div>
                        <div style="float:left; width:99.2%; padding:3px 0px; border-right:solid 1px #000; border-bottom:solid 1px #000; border-left:solid 1px #000;">
                          <div style="margin-bottom:15px; width:98%; margin-left:1%; font-size:13px;">'.$servicio["detalles"].'</div>
                          <div>';
                          //$arraySize = sizeof($servicio["imagenes"]);
                          for ($i=0; $i < sizeof($servicio["imagenes"]); $i++)
                          {
                            if(file_exists(ROOT . 'private/servicios/'.$servicio["imagenes"][$i]["filename"]) && $servicio["imagenes"][$i]["campo"] === "detalles")
                            {
                              $pdfText .= '<div style="float:left; width:48%;text-align:center; margin-left:1%; margin-bottom:10px;"><img src="'.ROOT.'/private/servicios/'.$servicio["imagenes"][$i]["filename"].'" style="max-width:100%; max-height:100%;"></div>';
                              //unset($servicio["imagenes"][$i]);
                            }
                          }
                          $servicio["imagenes"] = array_values($servicio["imagenes"]);
            $pdfText.='   </div>
                        </div>
                        <div style="float:left; width:99.2%; padding:3px 0px; border-right:solid 1px #000; border-bottom:solid 1px #000; border-left:solid 1px #000; background-color:rgb(217,217,217); text-align:center;">OBSERVACIONES DEL SERVICIO REALIZADO</div>
                        <div style="float:left; width:99.2%; padding:3px 0px; border-right:solid 1px #000; border-bottom:solid 1px #000; border-left:solid 1px #000;">
                          <div style="margin-bottom:15px; width:98%; margin-left:1%;  font-size:13px;">'.$servicio["observacion"].'</div>
                          <div>';
                          for ($i=0; $i < sizeof($servicio["imagenes"]); $i++)
                          {
                            if(file_exists(ROOT . 'private/servicios/'.$servicio["imagenes"][$i]["filename"]) && $servicio["imagenes"][$i]["campo"] === "observacion")
                            {
                              $pdfText .= '<div style="float:left; width:48%;text-align:center; margin-left:1%; margin-bottom:10px;"><img src="'.ROOT.'/private/servicios/'.$servicio["imagenes"][$i]["filename"].'" style="max-width:100%; max-height:100%;"></div>';
                            }
                          }
            $pdfText.='   </div>
                        </div>
                        <div style="float:left; width:49.5%; padding:3px 0px; border-left:solid 1px #000; border-bottom:solid 1px #000; border-right:solid 1px #000; background-color:rgb(217,217,217); text-align:center;">NOMBRE Y FIRMA DEL TECNICO</div>
                        <div style="float:left; width:49.5%; padding:3px 0px; border-right:solid 1px #000; border-bottom:solid 1px #000; background-color:rgb(217,217,217); text-align:center;">NOMBRE Y FIRMA DEL SOLICITANTE</div>
                        <div style="float:left; width:49.5%; padding:3px 0px; border-left:solid 1px #000; border-bottom:solid 1px #000; border-right:solid 1px #000; text-align:center; height:50px;"></div>
                        <div style="float:left; width:49.5%; padding:3px 0px; border-right:solid 1px #000; border-bottom:solid 1px #000; text-align:center; height:50px;"></div>
                        <div style="float:left; width:49.5%; padding:3px 0px; border-left:solid 1px #000; border-bottom:solid 1px #000; border-right:solid 1px #000; text-align:center;">'.$servicio["usuarioAsignado"].'</div>
                        <div style="float:left; width:49.5%; padding:3px 0px; border-right:solid 1px #000; border-bottom:solid 1px #000; text-align:center;">'.$servicio["solicitante"].'</div>
                        <div style="float:left; width:49.5%; padding:3px 0px; border-left:solid 1px #000; border-bottom:solid 1px #000; border-right:solid 1px #000; text-align:center;">'.$servicio["cargoUsuario"].'</div>
                        <div style="float:left; width:49.5%; padding:3px 0px; border-right:solid 1px #000; border-bottom:solid 1px #000; text-align:center;">'.$servicio["cargo"].'</div>
                    </div>';
				}
        else
          $pdfText = "Servicio no Encontrado";
			}
		}
		else
			$pdfText = "Parametro no recibido correctamente";
		$pdfText .= '</div>';
		//echo $pdfText;
		$pdf -> WriteHTML($pdfText);
		$pdf -> Output("boletaFiscalia.pdf","I");
  }
  public function servicio()
  {
    $this->_data["id"] = isset($_POST["id"]) ? (integer)$_POST["id"] : 0;
    $this->_data["idUnico"] = isset($_POST["idUnico"]) ? (integer)$_POST["idUnico"] : 0;
    $this->_data["solicitante"] = isset($_POST["solicitante"]) ? (integer)$_POST["solicitante"] : 0;
    $this->_data["tipoServicio"] = isset($_POST["tipoServicio"]) ? (integer)$_POST["tipoServicio"] : 0;
    $this->_data["usuarioAsignado"] = isset($_POST["usuarioAsignado"]) ? $_POST["usuarioAsignado"] : "";
    $this->_data["fechaInicio"] = isset($_POST["fechaInicio"]) ? $_POST["fechaInicio"] : "";
    $this->_data["fechaFin"] = isset($_POST["fechaFin"]) ? $_POST["fechaFin"] : "";
    $this->_data["estado"] = isset($_POST["estado"]) ? (integer)$_POST["estado"] : 1;
    $this->_data["area"] = isset($_POST["area"]) ? (integer)$_POST["area"] : 0;
    $this->_data["detalles"] = isset($_POST["detalles"]) ? $_POST["detalles"] : "";
    $this->_data["observacion"] = isset($_POST["observacion"]) ? $_POST["observacion"] : "";
    $this->_data["equipos"] = isset($_POST["equipos"]) ? $_POST["equipos"] : "";
    $this->_data["deletedEquip"] = isset($_POST["deletedEquip"]) ? $_POST["deletedEquip"] : "";

  }
}
?>
