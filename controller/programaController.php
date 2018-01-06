<?php
class programaController extends Controller{
  private $_data = array();
  private $_return = array('ok'=>false,'msg'=>"");
  public function __construct()
  {
    parent::__construct();
  }
  public function getProgramasByEjercicio($id)
  {
    Session::regenerateId();
    Session::securitySession();
    $condi = true;
    $condi = $condi && $this->_validar->Int($id,"Ejercicio");
    if($condi)
    {
      $programas = programa::select(array('id','nombre','descripcion'))->where('idejercicio',$id)->get()->fetch_all();
      if($programas)
      {
        $this->_return["ok"] = true;
        $this->_return["msg"] = $programas;
      }
      else
        $this->_return["msg"] = "Programas no encontrados para el ejercicio enviado";
    }
    else
      $this->_return["msg"] = $this->_validar->getWarnings();
    echo json_encode($this->_return);
  }
  public function getProgramasForComboByEjercicio($id)
  {
    Session::regenerateId();
    Session::securitySession();
    $condi = true;
    $condi = $condi && $this->_validar->Int($id,"Ejercicio");
    if($condi)
    {
      $programas = programa::select(array('id'=>'value','nombre'=>'text'))->where('idejercicio',$id)->get()->fetch_all();
      if($programas)
      {
        $this->_return["ok"] = true;
        $this->_return["msg"] = $programas;
      }
      else
        $this->_return["msg"] = "Programas no encontrados para el ejercicio enviado";
    }
    else
      $this->_return["msg"] = $this->_validar->getWarnings();
    echo json_encode($this->_return);
  }
  public function programa()
  {
    $this->_data["id"] = isset($_POST["id"]) ? (integer)$_POST["id"] : 0;
    $this->_data["idejercicio"] = isset($_POST["idejercicio"]) ? (integer)$_POST["idejercicio"] : 0;
    $this->_data["nombre"] = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
    $this->_data["descripcion"] = isset($_POST["descripcion"]) ? $_POST["descripcion"] : "";
  }
}
?>
