<?php
class mantenimientosController extends Controller
{
        private $_data;
        public function __construct()
        {
                parent::__construct();
        }
        public function getMantenimientos()
        {
                Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radioscat');
        $mantenimientos = mantenimientos::select(array('id','nombre','activo'))->where('area',$_SESSION["userData"]["area"])->get()->fetch_all();
        if($mantenimientos)
                echo json_encode(array('ok'=>true,'msg'=>$mantenimientos));
        else
                echo json_encode(array('ok'=>false,'msg'=>"No se lograron obtener los diagnosticos"));
        }
        public function getMantenimientosActivos()
        {
                Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radioscat');
        $mantenimientos = mantenimientos::select(array('id','nombre','activo'))->where('activo',1)->where('area',$_SESSION["userData"]["area"])->get()->fetch_all();
        if($mantenimientos)
                echo json_encode(array('ok'=>true,'msg'=>$mantenimientos));
        else
                echo json_encode(array('ok'=>false,'msg'=>"No se lograron obtener los diagnosticos"));
        }
        public function add()
        {
                Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radioscat');
        $this->mantenimiento();
        $return = array("ok"=>false,"msg"=>"");
        $condi = true;
        $condi = $condi && $this->_validar->NoEmpty($this->_data["nombre"],"Nombre");
        $condi = $condi && $this->_validar->Int($this->_data["activo"],"Activo");
        if($condi)
        {
                $mantenimiento = mantenimientos::insert(array('nombre'=>$this->_data["nombre"],'activo'=>$this->_data["activo"],'area'=>$_SESSION["userData"]["area"]));
                if($mantenimiento)
                {
                        $return["ok"] = true;
                        $return["msg"] = "Mantenimiento guardado correctamente";
                }
                else
                        $return["msg"] = "No fue posible guardar la mantenimiento.";
        }
        else
                $return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
        }
        public function updatebyid()
        {
                Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radioscat');
        $this->mantenimiento();
        $return = array("ok"=>false,"msg"=>"");
        $condi = true;
        $condi = $condi && $this->_validar->Int($this->_data["id"],"Folio");
        $condi = $condi && $this->_validar->NoEmpty($this->_data["nombre"],"Nombre");
        $condi = $condi && $this->_validar->Int($this->_data["activo"],"Activo");
        if($condi)
        {
                $mantenimiento = mantenimientos::where('id',$this->_data["id"])->where('area',$_SESSION["userData"]["area"])->update(array('nombre'=>$this->_data["nombre"],'activo'=>$this->_data["activo"]));
                if($mantenimiento)
                {
                        $return["ok"] = true;
                        $return["msg"] = "Mantenimiento modificado correctamente.";
                }
                else
                        $return["msg"] = "No fue posible modificar el mantenimiento.";
        }
        else
                $return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
        }
        public function getbyid()
        {
                Session::regenerateId();
        Session::securitySession(); 
        $this->validatePermissions('radioscat');
        $return = array('ok'=>false,'msg'=>'');
        $this->mantenimiento();
        $mantenimiento = mantenimientos::select(array('nombre','activo'))->where('id',$this->_data["id"])->get()->fetch_assoc();
        if($mantenimiento)
        {
                $return["msg"] = $mantenimiento;
                $return["ok"] = true;
        }
        else
                $return["msg"] = "Error obteniendo el mantenimiento";
        echo json_encode($return);
        }
        public function mantenimiento()
        {
                $this->_data["id"] = isset($_POST["id"]) ? (integer)$_POST["id"] : 0;
                $this->_data["nombre"] = isset($_POST["nombre"]) ? $_POST["nombre"] : "";
                $this->_data["activo"] = isset($_POST["activo"]) ? (integer)$_POST["activo"] : 0;
        }
}

?>