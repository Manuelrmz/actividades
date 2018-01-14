<?php
class cargosusuarioController extends Controller
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
        $cargos = cargosUsuario::select(array('id','cargo'))->get()->fetch_all();
        if($cargos)
        {
            $this->_return["msg"] = $cargos;
            $this->_return["ok"] = true;
        }
        else
            $this->_return["msg"] = "Cargos no encontrados";
        echo json_encode($this->_return);
    }
    public function new()
    {
        Session::regenerateId();
        Session::securitySession();
        $this->cargo($_POST);
        $condi = true;
        $condi = $condi && $this->_validar->MinMax($this->_data["cargo"],1,50,"Cargo");
        if($condi)
        {
            $cargo = cargosUsuario::insert($this->_data);
            if($cargo)
            {
                $this->_return["msg"] = "Cargo guardado correctamente";
                $this->_return["ok"] = true;
                $this->_return["id"] = $cargo;
            }
            else
                $this->_return["msg"] = "Ocurrio un error insertando el nuevo cargo";
        }
        else
            $this->_return["msg"] = $this->_validar->getWarnings();
        echo json_encode($this->_return);
    }
    public function update()
    {
        Session::regenerateId();
        Session::securitySession();
        $this->cargo($_POST);
        $condi = true;
        $condi = $condi && $this->_validar->Int($this->_data["id"],"Folio");
        $condi = $condi && $this->_validar->MinMax($this->_data["cargo"],1,50,"Cargo");
        if($condi)
        {
            $cargo = cargosUsuario::where('id',$this->_data["id"])->update($this->_data);
            if($cargo)
            {
                $this->_return["ok"] = true;
                $this->_return["msg"] = "Cargo modificado correctamente";
            }
            else
                $this->_return["msg"] = "Ocurrio un error actualizando el cargo";
        }
        else
            $this->_return["msg"] = $this->_validar->getWarnings();
        echo json_encode($this->_return);
    }
	public function getall()
	{
		Session::regenerateId();
		Session::securitySession();
		$return = array('ok'=>false,'msg'=>'');
		$cargos = cargosUsuario::select(array('id'=>'value','cargo'=>'text'))->get()->fetch_all();
		if($cargos)
		{
			$return["msg"] = $cargos;
			$return["ok"] = true;
		}
		else
			$return["msg"] = "No se encontraron los cargos del usuario";
		echo json_encode($return);
	}
	public function cargo($data)
	{
		$this->_data["id"] = isset($data["id"]) ? (integer)$data["id"] : null;
		$this->_data["cargo"] = isset($data["cargo"]) ? $data["cargo"] : "";
	}
}
?>