<?php
class proveedoresController extends Controller
{
  private $_data = array();
  private $_return = array('ok'=>false,'msg'=>'');
  public function __construct()
  {
    parent::__construct();
  }
  public function add()
  {
    Session::regenerateId();
    Session::securitySession();
    $this->proveedor($_POST);
    $condi = true;
    $condi = $condi && $this->_validar->MinMax($this->_data["rfc"],13,13,"RFC");
    $condi = $condi && $this->_validar->NoEmpty($this->_data["nombreEmpresa"],"Nombre de la empresa");
    if($condi)
    {
      unset($this->_data["id"]);
      $proveedor = proveedores::insert($this->_data);
      if($proveedor)
      {
        $this->_return["ok"] = true;
        $this->_return["msg"] = "Proveedor guardado correctamente";
        $this->_return["id"] = $proveedor;
      }
      else
        $this->_return["msg"] = "Ocurrio un error ingresando el nuevo proveedor";
    }
    else
      $this->_return["msg"] = $this->_validar->getWarnings();
    echo json_encode($this->_return);
  }
  public function update()
  {
    Session::regenerateId();
    Session::securitySession();
    $this->proveedor($_POST);
    $condi = true;
    $condi = $condi && $this->_validar->Int($this->_data["id"],"ID");
    $condi = $condi && $this->_validar->MinMax($this->_data["rfc"],13,13,"RFC");
    $condi = $condi && $this->_validar->NoEmpty($this->_data["nombreEmpresa"],"Nombre de la empresa");
    if($condi)
    {
      $proveedor = proveedores::where('id',$this->_data["id"])->update($this->_data);
      if($proveedor)
      {
        $this->_return["ok"] = true;
        $this->_return["msg"] = "Proveedor guardado correctamente";
      }
      else
        $this->_return["msg"] = "Ocurrio un error modificando el proveedor: ".$proveedor;
    }
    else
      $this->_return["msg"] = $this->_validar->getWarnings();
    echo json_encode($this->_return);
  }
  public function getForComboRFC()
  {
    Session::regenerateId();
    Session::securitySession();
    echo json_encode(proveedores::select(array('id'=>'value','rfc'=>'text'))->get()->fetch_all());
  }
  public function getfortable()
  {
    Session::regenerateId();
    Session::securitySession();
    $proveedores = proveedores::select(array('id','rfc','nombreEmpresa','direccion','cp','IF(activo = 1,true,false)'=>'activo'))->get()->fetch_all();
    if($proveedores)
    {
      $this->_return["msg"] = $proveedores;
      $this->_return["ok"] = true;
    }
    else
      $this->_return["msg"] = "No se encontraron proveedores";
    echo json_encode($this->_return);
  }
  public function getById($id)
  {
    Session::regenerateId();
    Session::securitySession();
    $condi = true;
    $condi = $condi && $this->_validar->Int($id,"Proveedor");
    if($condi)
    {
      $proveedor = proveedores::select(array('id','rfc','nombreEmpresa','direccion','cp','activo'))->where('id',$id)->get()->fetch_assoc();
      if($proveedor)
      {
        $this->_return["ok"] = true;
        $this->_return["msg"] = $proveedor;
      }
      else
        $this->_return["msg"] = "Proveedor no encontrados con el id enviado";
    }
    else
      $this->_return["msg"] = $this->_validar->getWarnings();
    echo json_encode($this->_return);
  }
  public function proveedor($data)
  {
    $this->_data["id"] = isset($data["id"]) ? (integer)$data["id"] : 0;
    $this->_data["rfc"] = isset($data["rfc"]) ? $data["rfc"] : "";
    $this->_data["nombreEmpresa"] = isset($data["nombreEmpresa"]) ? $data["nombreEmpresa"] : "";
    $this->_data["direccion"] = isset($data["direccion"]) ? $data["direccion"] : "";
    $this->_data["cp"] = isset($data["cp"]) ? $data["cp"] : "";
    $this->_data["activo"] = isset($data["activo"]) ? ($data["activo"] == "true" ? 1 : (integer)$data["activo"]) : 0;
  }
}
?>
