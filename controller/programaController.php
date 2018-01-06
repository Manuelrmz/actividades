<?php
class programaController extends Controller{
  private $_data = array();
  private $_return = array('ok'=>false,'msg'=>"");
  public function __construct()
  {
    parent::__construct();
  }
  public function new()
  {
    Session::regenerateId();
    Session::securitySession();
    $this->programa($_POST);
    $condi = true;
    $condi = $condi && $this->_validar->Int($this->_data["idejercicio"],"Ejercicio");
    $condi = $condi && $this->_validar->MinMax($this->_data["nombre"],1,150,"Nombre");
    if($condi)
    {
      unset($this->_data["id"]);
      $programa = programa::insert($this->_data);
      if($programa)
      {
        $this->_return["ok"] = true;
        $this->_return["msg"] = "Programa insertado correctamente";
        $this->_return["id"] = $programa;
      }
      else
        $this->_return["msg"] = "Error insertando el programa";
    }
    else
      $this->_return["msg"] = $this->_validar->getWarnings();
    echo json_encode($this->_return);
  }
  public function update()
  {
    Session::regenerateId();
    Session::securitySession();
    $this->programa($_POST);
    $condi = true;
    $condi = $condi && $this->_validar->Int($this->_data["id"],"Folio Programa");
    $condi = $condi && $this->_validar->Int($this->_data["idejercicio"],"Ejercicio");
    $condi = $condi && $this->_validar->MinMax($this->_data["nombre"],1,150,"Nombre");
    if($condi)
    {
      $programa = programa::where("id",$this->_data["id"])->where("idejercicio",$this->_data["idejercicio"])->update($this->_data);
      if($programa)
      {
        $this->_return["ok"] = true;
        $this->_return["msg"] = "Programa insertado correctamente";
        $this->_return["id"] = $programa;
      }
      else
        $this->_return["msg"] = "Error actualizando el programa";
    }
    else
      $this->_return["msg"] = $this->_validar->getWarnings();
    echo json_encode($this->_return);
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
  public function programa($data)
  {
    $this->_data["id"] = isset($data["id"]) ? (integer)$data["id"] : 0;
    $this->_data["idejercicio"] = isset($data["idejercicio"]) ? (integer)$data["idejercicio"] : 0;
    $this->_data["nombre"] = isset($data["nombre"]) ? $data["nombre"] : "";
    $this->_data["descripcion"] = isset($data["descripcion"]) ? $data["descripcion"] : "";
  }
}
?>
