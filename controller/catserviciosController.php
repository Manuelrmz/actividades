<?php
class catserviciosController extends Controller
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
    $return = array('ok'=>false,'msg'=>'');
    $this->servicio();
    $condi = true;
    $condi = $condi && $this->_validar->MinMax($this->_data["clave"],1,15,"Clave");
    $condi = $condi && $this->_validar->MinMax($this->_data["nombre"],1,150,"Nombre");
    $condi = $condi && $this->_validar->Int($this->_data["area"],"Area");
    if($condi)
    {
      unset($this->_data["id"]);
      $result = catservicios::insert($this->_data);
      if($result)
      {
        $return["msg"] = "Servicio añadido correctamente al catalogo";
        $return["id"] = $result;
        $return["ok"] = true;
      }
      else
        $return["msg"] = $result;
    }
    else
      $return["msg"] = $this->_validar->getWarnings();
    echo json_encode($return);
  }
  public function getForComboBoxByArea()
  {
    $this->servicio();
    echo json_encode(catservicios::select(array('id'=>'value','nombre'=>'text'))->where('area',$this->_data["area"])->get()->fetch_all());
  }
  public function servicio()
  {
    $this->_data["id"] = isset($_POST["id"]) ? (integer)$_POST["id"] : 0;
    $this->_data["clave"] = isset($_POST["clave"]) ? $_POST["clave"] : "";
    $this->_data["nombre"] = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
    $this->_data["descripcion"] = isset($_POST["descripcion"]) ? $_POST["descripcion"] : "";
    $this->_data["area"] = isset($_POST["area"]) ? (integer)$_POST["area"] : 0;
  }
}
?>
