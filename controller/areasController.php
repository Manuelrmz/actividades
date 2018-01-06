<?php
class areasController extends Controller
{
	public function __construct()
	{
		parent::__construct();
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
}
?>
