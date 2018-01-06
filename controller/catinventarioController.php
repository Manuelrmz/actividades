<?php
class catinventarioController extends Controller
{
  private $_data = array();
  private $_return = array('ok'=>false,'msg'=>'');
	public function __construct()
  {
      parent::__construct();
  }
  public function getForTable()
  {
    Session::regenerateId();
    Session::securitySession();
    $categorias = inventarioCategoria::select(array('id','nombre','descripcion','IF(estado = 1,true,false)'=>'estado'))->get()->fetch_all();
    if($categorias)
    {
      $this->_return["msg"] = $categorias;
      $this->_return["ok"] = true;
    }
    else
      $this->_return["msg"] = "Categorias no encontradas";
    echo json_encode($this->_return);
  }
  public function new()
  {
    Session::regenerateId();
    Session::securitySession();
    $this->categoria($_POST);
    $condi = true;
    $condi = $condi && $this->_validar->MinMax($this->_data["nombre"],1,100,"Nombre");
    if($condi)
    {
      $categoria = inventarioCategoria::insert(array('nombre'=>$this->_data["nombre"],'descripcion'=>$this->_data["descripcion"],'estado'=>($this->_data["estado"] ? 1 : 0)));
      if($categoria)
      {
        $this->_return["msg"] = "Categoria guardada correctamente";
        $this->_return["ok"] = true;
        $this->_return["id"] = $categoria;
      }
      else
        $this->_return["msg"] = "Ocurrio un error insertando la nueva categoria";
    }
    else
      $this->_return["msg"] = $this->_validar->getWarnings();
    echo json_encode($this->_return);
  }
  public function update()
  {
    Session::regenerateId();
    Session::securitySession();
    $this->categoria($_POST);
    $condi = true;
    $condi = $condi && $this->_validar->MinMax($this->_data["nombre"],1,100,"Nombre");
    if($condi)
    {
      $categoria = inventarioCategoria::where('id',$this->_data["id"])->update(array('nombre'=>$this->_data["nombre"],'descripcion'=>$this->_data["descripcion"],'estado'=>($this->_data["estado"] ? 1 : 0)));
      if($categoria)
      {
        $this->_return["ok"] = true;
        $this->_return["msg"] = "Categoria modificada correctamente";
      }
      else
        $this->_return["msg"] = "Ocurrio un error actualizando la categoria".$categoria;
    }
    else
      $this->_return["msg"] = $this->_validar->getWarnings();
    echo json_encode($this->_return);
  }
  public function getForComboBox()
  {
    Session::regenerateId();
    Session::securitySession();
    $categorias = inventarioCategoria::select(array('id'=>'value','nombre'=>'text'))->where('estado','1')->get()->fetch_all();
    if($categorias)
    {
      $this->_return["msg"] = $categorias;
      $this->_return["ok"] = true;
    }
    else
      $this->_return["msg"] = "Categorias no encontradas";
    echo json_encode($this->_return);
  }
  public function categoria($data)
  {
    $this->_data["id"] = isset($data["id"]) ? (integer)$data["id"] : 0;
    $this->_data["nombre"] = isset($data["nombre"]) ? $data["nombre"] : "";
    $this->_data["descripcion"] = isset($data["descripcion"]) ? $data["descripcion"] : "";
    $this->_data["estado"] = isset($data["estado"]) ? ($data["estado"] == "true" ? 1 : (integer)$data["estado"]) : 0;

  }
}
?>
