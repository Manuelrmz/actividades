<?php
class proveedoresController extends Controller
{
  private $_data = array();
  private $_return = array('ok'=>false,'msg'=>'');
  public function __construct()
  {
    parent::__construct();
  }
  public function index()
  {
    Session::regenerateId();
    Session::securitySession();
    $this->validatePermissions('proveedor');
    $this->_view->renderizar('index','C4 -  Proveedores');
  }
  public function add()
  {
    Session::regenerateId();
    Session::securitySession();
    $this->proveedor($_POST);
    $db = new queryBuilder();
    $condi = true;
    $condi = $condi && $this->_validar->MinMax($this->_data["rfc"],12,13,"RFC");
    $condi = $condi && $this->_validar->NoEmpty($this->_data["nombreEmpresa"],"Nombre de la empresa");
    if($condi)
    {
      $transaction = $db->transaction(function($q)
      {
        $this->_data["id"] =$q->table("proveedores")->insert(array('rfc'=>$this->_data['rfc'],'nombreEmpresa'=>$this->_data['nombreEmpresa'],'direccion'=>$this->_data['direccion'],'cp'=>$this->_data['cp'],'activo'=>$this->_data['activo']));
        $phones = null;
        if($this->_data["phones"] != "")
          $phones = json_decode($this->_data["phones"],true);
        if($phones)
        {
          foreach($phones as $phone)
          {
            unset($phone["idPhone"]);
            $phone["idproveedor"] = $this->_data["id"];
            $q->table("proveedorTelefonos")->insert($phone);
          }
        }
      });
      if($transaction)
      {
        $this->_return["msg"] = "Proveedor insertado correctamente";
        $this->_return["ok"] = true;
      }
      else
        $this->_return["msg"] = "Ocurrio un error insertando el proveedor: ".$db->getError()["string"];
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
    $db = new queryBuilder();
    $condi = true;
    $condi = $condi && $this->_validar->Int($this->_data["id"],"ID");
    $condi = $condi && $this->_validar->MinMax($this->_data["rfc"],12,13,"RFC");
    $condi = $condi && $this->_validar->NoEmpty($this->_data["nombreEmpresa"],"Nombre de la empresa");
    if($condi)
    {
      $transaction = $db->transaction(function($q)
      {
        $q->table("proveedores")->where('id',$this->_data["id"])->update(array('rfc'=>$this->_data['rfc'],'nombreEmpresa'=>$this->_data['nombreEmpresa'],'direccion'=>$this->_data['direccion'],'cp'=>$this->_data['cp'],'activo'=>$this->_data['activo']));

        $phones = null;
        if($this->_data["phones"] != "")
          $phones = json_decode($this->_data["phones"],true);
        if($phones)
        {
          foreach($phones as $phone)
          {
            $phone["id"] = $phone["idPhone"];
            $phone["idproveedor"] = $this->_data["id"];
            unset($phone["idPhone"]);
            if($phone["id"] !== null)
              $q->table("proveedorTelefonos")->where('id',$phone["id"])->update($phone);
            else
              $q->table("proveedorTelefonos")->insert($phone);
          }
        }
        $deletedPhones = explode(",",$this->_data["deletedPhones"]);
        if(sizeOf($deletedPhones) > 0)
        {
          for($i = 0; $i < sizeOf($deletedPhones); $i++)
          {
            if($deletedPhones[$i] !== "")
              $q->table('proveedorTelefonos')->where("id",$deletedPhones[$i])->where("idproveedor",$this->_data["id"])->delete();
          }
        }
      });
      if($transaction)
      {
        $this->_return["msg"] = "Proveedor actualizado correctamente";
        $this->_return["ok"] = true;
      }
      else
        $this->_return["msg"] = "Ocurrio un error actualizando el proveedor: ".$db->getError()["string"];
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
        $proveedor["phones"] = proveedorTelefonos::select(array('id'=>'idPhone','numero','tipo'))->where('idproveedor',$id)->get()->fetch_all();
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
    $this->_data["phones"] = isset($data["phones"]) ? $data["phones"] : "";
    $this->_data["deletedPhones"] = isset($data["deletedPhones"]) ? $data["deletedPhones"] : "";
  }
}
?>
