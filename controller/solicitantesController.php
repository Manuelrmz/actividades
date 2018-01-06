<?php
class solicitantesController extends Controller
{
  private $_data;
  public function __construct()
  {
    parent::__construct();
  }
  public function getForComboBox()
  {
    Session::regenerateId();
    Session::securitySession();
    echo json_encode(solicitante::select(array('id'=>'value','nombre'=>'text'))->get()->fetch_all());
  }
  public function add()
  {
    Session::regenerateId();
    Session::securitySession();
    $return = array('ok'=>false,'msg'=>'');
    $this->solicitante();
    $condi = true;
    $condi = $condi && $this->_validar->MinMax($this->_data["nombre"],10,200,"Folio");
    if($condi)
    {
      unset($this->_data["id"]);
      $this->_data["usuarioAlta"] = $_SESSION["userData"]["usuario"];
      $result = solicitante::insert($this->_data);
      if($result)
      {
        $return["msg"] = "Solicitante añadido correctamente";
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
  public function getbyid()
  {
    Session::regenerateId();
    Session::securitySession();
    $return = array('ok'=>false,'msg'=>'');
    $this->solicitante();
    $condi = true;
    $condi = $condi && $this->_validar->Int($this->_data["id"],"Folio");
    if($condi)
    {
      $solicitante = solicitante::select(array('id','nombre','cargo','dependencia','area','edificio','telefono','extension'))->where('id',$this->_data["id"])->get()->fetch_assoc();
      if($solicitante)
      {
        $return["msg"] = $solicitante;
        $return["ok"] = true;
      }
      else
        $return["msg"] = 'Solicitante no encontrado';
    }
    else
      $return["msg"] = $this->_validar->getWarnings();
    echo json_encode($return);
  }
  public function solicitante()
  {
    $this->_data["id"] = isset($_POST["id"]) ? (integer)$_POST["id"] : 0;
    $this->_data["nombre"] = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
    $this->_data["cargo"] = isset($_POST["cargo"]) ? $_POST["cargo"] : "";
    $this->_data["dependencia"] = isset($_POST["dependencia"]) ? $_POST["dependencia"] : "";
    $this->_data["area"] = isset($_POST["area"]) ? $_POST["area"] : "";
    $this->_data["edificio"] = isset($_POST["edificio"]) ? $_POST["edificio"] : "";
    $this->_data["telefono"] = isset($_POST["telefono"]) ? (integer)$_POST["telefono"] : 0;
    $this->_data["extension"] = isset($_POST["extension"]) ? (integer)$_POST["extension"] : 0;
  }
}
?>
