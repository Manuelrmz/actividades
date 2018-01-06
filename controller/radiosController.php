<?php
class radiosController extends Controller
{
	public function __construct()
  {
      parent::__construct();
  }
  public function reporteador()
  {
      Session::regenerateId();
      Session::securitySession();
      $this->validatePermissions('reporteadorradios');
      $this->_view->renderizar('reporteador','C4 - Reporteador Radios');
  }
	public function equipos()
	{
		Session::regenerateId();
        Session::securitySession();
        $this->validatePermissions('equiposradios');
        $this->_view->renderizar('equipos','C4 - Equipos Radios');
	}
  public function visitasitio()
  {
      Session::regenerateId();
      Session::securitySession();
      $this->validatePermissions('rptvisitasitio');
      $this->_view->renderizar('visitasitio','C4 - Visita a Sitios C4');
  }
  public function getPersonalForList()
  {
      Session::regenerateId();
      Session::securitySession();
      $return = array('ok'=>false,'msg'=>'');
      $usuarios = usuarios::select(array('CONCAT(nombres," ",apellidos)'=>'text','id'=>'value'))->where('area','Radios')->where('usuario','!=','icrespo')->get()->fetch_all();
      if($usuarios)
      {
          $return["msg"] = $usuarios;
          $return["ok"] = true;
      }
      else
          $return["msg"] = "Error obteniendo la lista de usuarios";
      echo json_encode($return);
  }
}
?>
