<?php
class ejercicioController extends Controller{
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
    $this->validatePermissions('ejercicio');
    $this->_view->renderizar('index','C4 - Ejercicios');
  }
  public function new()
  {
    Session::regenerateId();
    Session::securitySession();
    $this->validatePermissions('ejercicio');
    $this->ejercicio($_POST);
    $db = new queryBuilder();
    $condi = true;
    $condi = $condi && $this->_validar->Int($this->_data["anio"],"Ejercicio");
    if($condi)
    {

      $transaccion = $db->transaction(function($q)
      {
        $ejercicio = $q->table('ejercicio')->insert(array('anio'=>$this->_data["anio"]));
        $this->_data["id"] = $ejercicio;
        // $programas = null;
        // if($this->_data["programas"] != "")
        //   $programas = $this->_validar->JSON($this->_data["programas"]);
        // if(isset($programas["ok"]) && $programas["ok"])
        // {
        //   foreach ($programas["datos"] as $programa) {
        //     $q->table("programa")->insert(array('idejercicio'=>$ejercicio,'nombre'=>$programa->nombre,'descripcion'=>$programa->descripcion));
        //   }
        // }
      });
      if($transaccion)
      {
        $this->_return["msg"] = "Ejercicio insertado correctamente";
        $this->_return["id"] = $this->_data["id"];
        $this->_return["ok"] = true;
      }
      else
        $this->_return["msg"] = "Ocurrio un error insertando el ejercicio: ".$db->getError()["string"];
    }
    else
      $this->_return["msg"] = $this->_validar->getWarnings();
    echo json_encode($this->_return);
  }
  public function update()
  {
    Session::regenerateId();
    Session::securitySession();
    $this->validatePermissions('ejercicio');
    $this->ejercicio($_POST);
    $db = new queryBuilder();
    $condi = true;
    $condi = $condi && $this->_validar->Int($this->_data["id"],"Id Ejercicio");
    $condi = $condi && $this->_validar->Int($this->_data["anio"],"Ejercicio");
    if($condi)
    {

      $transaccion = $db->transaction(function($q)
      {
        $ejercicio = $q->table('ejercicio')->where('id',$this->_data["id"])->update(array('anio'=>$this->_data["anio"]));
        // $programas = null;
        // if($this->_data["programas"] != "")
        //   $programas = $this->_validar->JSON($this->_data["programas"]);
        // if(isset($programas["ok"]) && $programas["ok"])
        // {
        //   foreach ($programas["datos"] as $programa) {
        //     if($programa->idPrograma === null)
        //       $q->table("programa")->insert(array('idejercicio'=>$this->_data["id"],'nombre'=>$programa->nombre,'descripcion'=>$programa->descripcion));
        //     else
        //       $q->table("programa")->where('id',$programa->idPrograma)->where('idejercicio',$this->_data["id"])->update(array('nombre'=>$programa->nombre,'descripcion'=>$programa->descripcion));
        //   }
        // }
        // $deletedPrograma = explode(",",$this->_data["deletedPrograma"]);
        // if(sizeOf($deletedPrograma) > 0)
        // {
        //   for($i = 0; $i < sizeOf($deletedPrograma); $i++)
        //   {
        //     if($deletedPrograma[$i] !== "")
        //       $q->table('programa')->where("id",$deletedPrograma[$i])->where("idejercicio",$this->_data["id"])->delete();
        //   }
        // }
      });
      if($transaccion)
      {
        $this->_return["msg"] = "Ejercicio actualizado correctamente";
        $this->_return["ok"] = true;
      }
      else
        $this->_return["msg"] = "Ocurrio un error actualizando el ejercicio: ".$db->getError()["string"];
    }
    else
      $this->_return["msg"] = $this->_validar->getWarnings();
    echo json_encode($this->_return);
  }
  public function getEjerciciosForCombo()
  {
    Session::regenerateId();
    Session::securitySession();
    $ejercicios = ejercicio::select(array('id'=>'value','anio'=>'text'))->get()->fetch_all();
    if($ejercicios)
    {
      $this->_return["msg"] = $ejercicios;
      $this->_return["ok"] = true;
    }
    else
      $this->_return["msg"] = "Ejercicios no encontrados";
    echo json_encode($this->_return);
  }
  public function getEjerciciosForTable()
  {
    Session::regenerateId();
    Session::securitySession();
    $ejercicios = ejercicio::select(array('id','anio'))->get()->fetch_all();
    if($ejercicios)
    {
      $this->_return["msg"] = $ejercicios;
      $this->_return["ok"] = true;
    }
    else
      $this->_return["msg"] = "Ejercicios no encontrados";
    echo json_encode($this->_return);
  }
  public function ejercicio($data)
  {
    $this->_data["id"] = isset($data["id"]) ? (integer)$data["id"] : 0;
    $this->_data["anio"] = isset($data["anio"]) ? (integer)$data["anio"] : 0;
    $this->_data["programas"] = isset($data["programas"]) ? $data["programas"] : "";
    $this->_data["deletedPrograma"] = isset($data["deletedPrograma"]) ? $data["deletedPrograma"] : "";
  }
}
?>
