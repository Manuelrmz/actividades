<?php
class sitiosController extends Controller
{
	public function __construct()
  {
      parent::__construct();
  }
	public function index()
	{
		Session::regenerateId();
    Session::securitySession();
		$this->validatePermissions('sitioscat');
    $this->_view->renderizar('index','C4 - Edicion de sitios');
	}
	public function consulta()
	{
		Session::regenerateId();
    Session::securitySession();
		$this->validatePermissions('sitiosconsu');
    $this->_view->renderizar('consulta','C4 - Sitios de Comunicaciones');
	}
	public function add()
	{
		Session::regenerateId();
    Session::securitySession();
		$this->validatePermissions('sitioscat');
    $this->sitio();
    $db = new queryBuilder();
    $return = array('ok'=>false,'msg'=>'');
    $condi = true;
		$condi = $condi && $this->_validar->MinMax($this->_data["nombre"],1,100,"Nombre");
    $condi = $condi && $this->_validar->Int($this->_data["municipio"],"Municipio");
    if($condi)
    {
      $sitioData = array('nombre'=>$this->_data["nombre"],'direccion'=>$this->_data["direccion"],'municipio'=>$this->_data["municipio"],
                            'propietario'=>$this->_data["propietario"],'tipoTorre'=>$this->_data["tipoTorre"],'alturaTorre'=>$this->_data["alturaTorre"],
                            'cfeServicio'=>$this->_data["cfeServicio"],'cfeMedidor'=>$this->_data["cfeMedidor"],'transPropio'=>$this->_data["transPropio"],
														'transCapacidad'=>$this->_data["transCapacidad"],'plantaEmergencia'=>$this->_data["plantaEmergencia"],'comentarios'=>$this->_data["comentarios"],
														'usuarioAlta'=>$_SESSION["userData"]["usuario"]);
      $this->_data["sitioData"] = $sitioData;
      $transaccion = $db->transaction(function($q)
      {
        $sitio = $q->table('sitios')->insert($this->_data["sitioData"]);
        $this->_data["id"] = $sitio;
				$aires = null;
				$servicios = null;
				$equipos = null;
				if($this->_data["aires"] != "")
          $aires = $this->_validar->JSON($this->_data["aires"]);
        if($aires != "")
        {
          if($aires["ok"])
          {
            foreach ($aires["datos"] as $aire) {
              $q->table("sitiosAires")->insert(array('idSitio'=>$sitio,'tipo'=>$equipo->tipo,'capacidad'=>$equipo->capacidad));
            }
          }
        }
				if($this->_data["servicios"] != "")
          $servicios = $this->_validar->JSON($this->_data["servicios"]);
        if($servicios != "")
        {
          if($servicios["ok"])
          {
            foreach ($servicios["datos"] as $servicio) {
              $q->table("sitiosServicios")->insert(array('idSitio'=>$sitio,'nombreProveedor'=>$servicio->nombreProveedor,'tipoServicio'=>$servicio->tipoServicio,'noServicio'=>$servicio->noServicio,'comentarios'=>$servicio->comentarios));
            }
          }
        }
        if($this->_data["equipos"] != "")
          $equipos = $this->_validar->JSON($this->_data["equipos"]);
        if($equipos != "")
        {
          if($equipos["ok"])
          {
            foreach ($equipos["datos"] as $equipo) {
              $q->table("sitiosEquipos")->insert(array('idSitio'=>$sitio,'nombre'=>$equipo->nombre,'modelo'=>$equipo->modelo,'noSerie'=>$equipo->noSerie,'ip'=>$equipo->ip,'comentarios'=>$equipo->comentarios));
            }
          }
        }
      });
      if($transaccion)
      {
        $return["msg"].= "Sitio agregado correctamente";
        $return["ok"] = true;
        if(isset($_FILES) && sizeof($_FILES) > 0)
        {
          $fields = array_keys($_FILES);
          $permitidas = array('jpg','JPG','jpeg','jpge','png','gif');
          foreach ($fields as $field)
          {
            for($i = 0; $i < sizeof($_FILES[$field]["name"]); $i++)
            {
              if($_FILES[$field]["tmp_name"][$i] != "")
              {
                $this->_data["archivo"] = preg_replace(array('/á/','/é/','/í/','/ó/','/ú/','/Á/','/É/','/Í/','/Ó/','/Ú/'),array('a','e','i','o','u','A','E','I','O','U'),$_FILES[$field]["name"][$i]);
                $extension = explode(".",$this->_data["archivo"]);
                $extension = $extension[count($extension)-1];
                if(in_array($extension,$permitidas))
                {
                  $this->_data["filename"] = tempnam(ROOT . 'private/sitios/','');
                  unlink($this->_data["filename"]);
                  $this->_data["filename"] = explode('/',$this->_data["filename"]);
                  $this->_data["filename"] = $this->_data["filename"][count($this->_data["filename"])-1].'.'.$extension;
                  if(move_uploaded_file($_FILES[$field]['tmp_name'][$i], ROOT . 'private/sitios/'.$this->_data["filename"]))
                  {
                    $this->_data["filetype"] = $_FILES[$field]['type'][$i];
                    $this->_data["filesize"] = $_FILES[$field]['size'][$i];
                    $transaccion = $db->transaction(function($q)
                    {
                      $imagen = $q->table('archivos')->insert(array('filename'=>$this->_data["filename"],'name'=>$this->_data["archivo"],'type'=>$this->_data["filetype"],'size'=>$this->_data["filesize"],'usuarioAlta'=>$_SESSION["userData"]["usuario"],'fechaAlta'=>date('Y-m-d H:i:s')));
                      $q->table('archivosSitios')->insert(array('idarchivo'=>$imagen,'idSitio'=>$this->_data["id"],'descripcion'=>''));
                    });
                    if($transaccion)
                      $return["msg"] .= "\n".$this->_data["archivo"]." guardada correctamente";
                    else
                    {
                      $return["msg"] .= "\nNo fue posible guardar ".$this->_data["archivo"];
                      unlink(ROOT . 'private/sitios/'.$this->_data["filename"]);
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
        $return["msg"].= "Ocurrio un error insertando el sitio: ".$db->getError()["string"];
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
    $this->sitio();
    $condi = $condi && $this->_validar->Int($this->_data["id"],"Folio");
    if($condi)
    {
      $sitio = sitios::select(array('nombre','direccion','municipio','propietario','tipoTorre','alturaTorre','cfeServicio','cfeMedidor','transPropio',
																			'transCapacidad','plantaEmergencia','comentarios'))->where('id',$this->_data["id"])->get()->fetch_assoc();
      if($sitio)
      {
				$sitio["aires"] = sitiosAires::select(array('id'=>'idAire','tipo','capacidad'))->where("idSitio",$this->_data["id"])->get()->fetch_all();
				$sitio["equipos"] = sitiosEquipos::select(array('id'=>'idEquipo','nombre','modelo','noSerie','ip','comentarios'))->where("idSitio",$this->_data["id"])->get()->fetch_all();
        $sitio["servicios"] = sitiosServicios::select(array('id'=>'idServicio','nombreProveedor','tipoServicio','noServicio','comentarios'))->where("idSitio",$this->_data["id"])->get()->fetch_all();
				$sitio["images"] = archivos::select(array('filename'))->join(array('archivosSitios','ars'),'ars.idarchivo','=','archivos.id','LEFT')->where('ars.idSitio',$this->_data["id"])->get()->fetch_all();
        $return["msg"] = $sitio;
        $return["ok"] = true;
      }
      else
        $return["msg"] = "Sitio no encontrado";
    }
    else
      $return["msg"] = $this->_validar->getWarnings();
    echo json_encode($return);
	}
	public function updatebyid()
	{
		Session::regenerateId();
    Session::securitySession();
		$this->validatePermissions('sitioscat');
		$this->sitio();
		$db = new queryBuilder();
		$return = array('ok'=>false,'msg'=>'');
		$condi = true;
		$condi = $condi && $this->_validar->MinMax($this->_data["nombre"],1,100,"Nombre");
		$condi = $condi && $this->_validar->Int($this->_data["municipio"],"Municipio");
    if($condi)
    {
      $sitioData = array('nombre'=>$this->_data["nombre"],'direccion'=>$this->_data["direccion"],'municipio'=>$this->_data["municipio"],
                            'propietario'=>$this->_data["propietario"],'tipoTorre'=>$this->_data["tipoTorre"],'alturaTorre'=>$this->_data["alturaTorre"],
                            'cfeServicio'=>$this->_data["cfeServicio"],'cfeMedidor'=>$this->_data["cfeMedidor"],'transPropio'=>$this->_data["transPropio"],
														'transCapacidad'=>$this->_data["transCapacidad"],'plantaEmergencia'=>$this->_data["plantaEmergencia"],'comentarios'=>$this->_data["comentarios"],
														'usuarioMod'=>$_SESSION["userData"]["usuario"]);
      $this->_data["sitioData"] = $sitioData;
      $transaccion = $db->transaction(function($q)
      {
        $sitio = $q->table('sitios')->where('id',$this->_data["id"])->update($this->_data["sitioData"]);
				$aires = null;
				$servicios = null;
				$equipos = null;
				//aires
				if($this->_data["aires"] != "")
          $aires = $this->_validar->JSON($this->_data["aires"]);
        if($aires != "")
        {
          if($aires["ok"])
          {
            foreach ($aires["datos"] as $aire) {
              if($aire->idAire === null)
								$q->table("sitiosAires")->insert(array('idSitio'=>$this->_data["id"],'tipo'=>$aire->tipo,'capacidad'=>$aire->capacidad));
              else
                $q->table("sitiosAires")->where('id',$aire->idAire)->where('idSitio',$this->_data["id"])->update(array('tipo'=>$aire->tipo,'capacidad'=>$aire->capacidad));
            }
          }
        }
        $deletedAire = explode(",",$this->_data["deletedAire"]);
        if(sizeOf($deletedAire) > 0)
        {
          for($i = 0; $i < sizeOf($deletedAire); $i++)
          {
            if($deletedAire[$i] !== "")
              $q->table('sitiosAires')->where("id",$deletedAire[$i])->where("idSitio",$this->_data["id"])->delete();
          }
        }
				//servicios
				if($this->_data["servicios"] != "")
          $servicios = $this->_validar->JSON($this->_data["servicios"]);
        if($servicios != "")
        {
          if($servicios["ok"])
          {
            foreach ($servicios["datos"] as $servicio) {
              if($servicio->idServicio === null)
                $q->table("sitiosServicios")->insert(array('idSitio'=>$this->_data["id"],'nombreProveedor'=>$servicio->nombreProveedor,'tipoServicio'=>$servicio->tipoServicio,'noServicio'=>$servicio->noServicio,'comentarios'=>$servicio->comentarios));
              else
                $q->table("sitiosServicios")->where('id',$servicio->idServicio)->where('idSitio',$this->_data["id"])->update(array('nombreProveedor'=>$servicio->nombreProveedor,'tipoServicio'=>$servicio->tipoServicio,'noServicio'=>$servicio->noServicio,'comentarios'=>$servicio->comentarios));
            }
          }
        }
        $deletedService = explode(",",$this->_data["deletedService"]);
        if(sizeOf($deletedService) > 0)
        {
          for($i = 0; $i < sizeOf($deletedService); $i++)
          {
            if($deletedService[$i] !== "")
              $q->table('sitiosServicios')->where("id",$deletedService[$i])->where("idSitio",$this->_data["id"])->delete();
          }
        }
				//equipos
        if($this->_data["equipos"] != "")
          $equipos = $this->_validar->JSON($this->_data["equipos"]);
        if($equipos != "")
        {
          if($equipos["ok"])
          {
            foreach ($equipos["datos"] as $equipo) {
              if($equipo->idEquipo === null)
                $q->table("sitiosEquipos")->insert(array('idSitio'=>$this->_data["id"],'nombre'=>$equipo->nombre,'modelo'=>$equipo->modelo,'noSerie'=>$equipo->noSerie,'ip'=>$equipo->ip,'comentarios'=>$equipo->comentarios));
              else
                $q->table("sitiosEquipos")->where('id',$equipo->idEquipo)->where('idSitio',$this->_data["id"])->update(array('nombre'=>$equipo->nombre,'modelo'=>$equipo->modelo,'noSerie'=>$equipo->noSerie,'ip'=>$equipo->ip,'comentarios'=>$equipo->comentarios));
            }
          }
        }
        $deletedEquip = explode(",",$this->_data["deletedEquip"]);
        if(sizeOf($deletedEquip) > 0)
        {
          for($i = 0; $i < sizeOf($deletedEquip); $i++)
          {
            if($deletedEquip[$i] !== "")
              $q->table('sitiosEquipos')->where("id",$deletedEquip[$i])->where("idSitio",$this->_data["id"])->delete();
          }
        }
      });
      if($transaccion)
      {
        $return["msg"].= "Sitio actualizado correctamente";
        $return["ok"] = true;
				if(isset($_FILES) && sizeof($_FILES) > 0)
        {
          $fields = array_keys($_FILES);
          $permitidas = array('jpg','JPG','jpeg','jpge','png','gif');
          foreach ($fields as $field)
          {
            for($i = 0; $i < sizeof($_FILES[$field]["name"]); $i++)
            {
              if($_FILES[$field]["tmp_name"][$i] != "")
              {
                $this->_data["archivo"] = preg_replace(array('/á/','/é/','/í/','/ó/','/ú/','/Á/','/É/','/Í/','/Ó/','/Ú/'),array('a','e','i','o','u','A','E','I','O','U'),$_FILES[$field]["name"][$i]);
                $extension = explode(".",$this->_data["archivo"]);
                $extension = $extension[count($extension)-1];
                if(in_array($extension,$permitidas))
                {
                  $this->_data["filename"] = tempnam(ROOT . 'private/sitios/','');
                  unlink($this->_data["filename"]);
                  $this->_data["filename"] = explode('/',$this->_data["filename"]);
                  $this->_data["filename"] = $this->_data["filename"][count($this->_data["filename"])-1].'.'.$extension;
                  if(move_uploaded_file($_FILES[$field]['tmp_name'][$i], ROOT . 'private/sitios/'.$this->_data["filename"]))
                  {
                    $this->_data["filetype"] = $_FILES[$field]['type'][$i];
                    $this->_data["filesize"] = $_FILES[$field]['size'][$i];
                    $transaccion = $db->transaction(function($q)
                    {
                      $imagen = $q->table('archivos')->insert(array('filename'=>$this->_data["filename"],'name'=>$this->_data["archivo"],'type'=>$this->_data["filetype"],'size'=>$this->_data["filesize"],'usuarioAlta'=>$_SESSION["userData"]["usuario"],'fechaAlta'=>date('Y-m-d H:i:s')));
                      $q->table('archivosSitios')->insert(array('idarchivo'=>$imagen,'idSitio'=>$this->_data["id"],'descripcion'=>''));
                    });
                    if($transaccion)
                      $return["msg"] .= "\n".$this->_data["archivo"]." guardada correctamente";
                    else
                    {
                      $return["msg"] .= "\nNo fue posible guardar ".$this->_data["archivo"];
                      unlink(ROOT . 'private/sitios/'.$this->_data["filename"]);
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
        $return["msg"].= "Ocurrio un error modificar el sitio: ".$db->getError()["string"];
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
    $sitios = sitios::select(array('nombre'=>'text','id'=>'value'))->get()->fetch_all();
    if($sitios)
    {
	    $return["msg"] = $sitios;
	    $return["ok"] = true;
    }
    else
    	$return["msg"] = "Error obteniendo la lista de sitios";
    echo json_encode($return);
	}
  public function getSitiosForList()
  {
  	Session::regenerateId();
    Session::securitySession();
    $return = array('ok'=>false,'msg'=>'');
    $sitios = sitios::select(array('sitios.id'=>'folio','sitios.nombre','sitios.direccion','m.nombre'=>'municipio','sitios.propietario','sitios.tipoTorre','sitios.cfeServicio'))->join(array('municipios','m'),'sitios.municipio','=','m.id','LEFT')->get()->fetch_all();
    if($sitios)
    {
	    $return["msg"] = $sitios;
	    $return["ok"] = true;
    }
    else
    	$return["msg"] = "Error obteniendo la lista de sitios";
    echo json_encode($return);
  }
  public function getSitiosForCombo()
  {
    Session::regenerateId();
    Session::securitySession();
    $return = array('ok'=>false,'msg'=>'');
    $sitios = sitios::select(array('id'=>'value','nombre'=>'text'))->get()->fetch_all();
    if($sitios)
    {
      $return["msg"] = $sitios;
      $return["ok"] = true;
    }
    else
      $return["msg"] = "Error obteniendo la lista de sitios";
    echo json_encode($return);
  }
	public function sitio()
	{
		$this->_data["id"] = isset($_POST["id"]) ? (integer)$_POST["id"] : 0;
		$this->_data["nombre"] = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
		$this->_data["direccion"] = isset($_POST["direccion"]) ? $_POST["direccion"] : "";
		$this->_data["municipio"] = isset($_POST["municipio"]) ? (integer)$_POST["municipio"] : 0;
		$this->_data["propietario"] = isset($_POST["propietario"]) ? $_POST["propietario"] : "";
		$this->_data["tipoTorre"] = isset($_POST["tipoTorre"]) ? $_POST["tipoTorre"] : "";
		$this->_data["alturaTorre"] = isset($_POST["alturaTorre"]) ? (integer)$_POST["alturaTorre"] : 0;
		$this->_data["cfeServicio"] = isset($_POST["cfeServicio"]) ? (integer)$_POST["cfeServicio"] : 0;
		$this->_data["cfeMedidor"] = isset($_POST["cfeMedidor"]) ? (integer)$_POST["cfeMedidor"] : 0;
		$this->_data["transPropio"] = isset($_POST["transPropio"]) ? (integer)$_POST["transPropio"] : 0;
		$this->_data["transCapacidad"] = isset($_POST["transCapacidad"]) ? (integer)$_POST["transCapacidad"] : 0;
		$this->_data["plantaEmergencia"] = isset($_POST["plantaEmergencia"]) ? (integer)$_POST["plantaEmergencia"] : 0;
		$this->_data["comentarios"] = isset($_POST["comentarios"]) ? $_POST["comentarios"] : "";
		$this->_data["aires"] = isset($_POST["aires"]) ? $_POST["aires"] : "";
		$this->_data["servicios"] = isset($_POST["servicios"]) ? $_POST["servicios"] : "";
		$this->_data["equipos"] = isset($_POST["equipos"]) ? $_POST["equipos"] : "";
		$this->_data["deletedEquip"] = isset($_POST["deletedEquip"]) ? $_POST["deletedEquip"] : "";
		$this->_data["deletedService"] = isset($_POST["deletedService"]) ? $_POST["deletedService"] : "";
		$this->_data["deletedAire"] = isset($_POST["deletedAire"]) ? $_POST["deletedAire"] : "";
	}
}
?>
