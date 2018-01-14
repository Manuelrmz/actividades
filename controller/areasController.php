<?php
class areasController extends Controller
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
        $areas = areas::select(array('id','clave','nombre','representante'))->get()->fetch_all();
        if($areas)
        {
            $this->_return["msg"] = $areas;
            $this->_return["ok"] = true;
        }
        else
            $this->_return["msg"] = "Areas no encontradas";
        echo json_encode($this->_return);
    }
    public function new()
    {
        Session::regenerateId();
        Session::securitySession();
        $this->area($_POST);
        $condi = true;
        $condi = $condi && $this->_validar->MinMax($this->_data["clave"],1,15,"Clave");
        $condi = $condi && $this->_validar->MinMax($this->_data["nombre"],1,150,"Nombre");
        if($condi)
        {
            $area = areas::insert($this->_data);
            if($area)
            {
                $this->_return["msg"] = "Area guardada correctamente";
                $this->_return["ok"] = true;
                $this->_return["id"] = $area;
            }
            else
                $this->_return["msg"] = "Ocurrio un error insertando la nueva area";
        }
        else
            $this->_return["msg"] = $this->_validar->getWarnings();
        echo json_encode($this->_return);
    }
    public function update()
    {
        Session::regenerateId();
        Session::securitySession();
        $this->area($_POST);
        $condi = true;
        $condi = $condi && $this->_validar->Int($this->_data["id"],"Folio");
        $condi = $condi && $this->_validar->MinMax($this->_data["clave"],1,15,"Clave");
        $condi = $condi && $this->_validar->MinMax($this->_data["nombre"],1,150,"Nombre");
        if($condi)
        {
            $area = areas::where('id',$this->_data["id"])->update($this->_data);
            if($area)
            {
                $this->_return["ok"] = true;
                $this->_return["msg"] = "Area modificada correctamente";
            }
            else
                $this->_return["msg"] = "Ocurrio un error actualizando el area";
        }
        else
            $this->_return["msg"] = $this->_validar->getWarnings();
        echo json_encode($this->_return);
    }
	public function getall()
	{
		$return = array('ok'=>false,'msg'=>'');
		$areas = areas::select(array('id'=>'value','clave'=>'text'))->get()->fetch_all();
		if($areas)
		{
			$return["msg"] = $areas;
			$return["ok"] = true;
		}
		else
			$return["msg"] = "Error obteniendo las areas";
		echo json_encode($return);
	}
	public function getAreasOperativas()
	{
		echo json_encode(areas::select(array('id'=>'value','nombre'=>'text'))->where('clave','Sistemas')->orWhere('clave','Telefonia')->orWhere('clave','Video')->get()->fetch_all());
	}
	public function area($data)
	{
		$this->_data["id"] = isset($data["id"]) ? (integer)$data["id"] : null;
		$this->_data["clave"] = isset($data["clave"]) ? $data["clave"] : "";
		$this->_data["nombre"] = isset($data["nombre"]) ? $data["nombre"] : "";
		$this->_data["representante"] = isset($data["representante"]) ? $data["representante"] : "";
	}
}
?>
