<?php
class tipoequiporadiosController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	public function getAll()
	{
		Session::regenerateId();
        Session::securitySession(); 
        $return = array('ok'=>false,'msg'=>'');
        $tipoequiporadios = tipoequiporadios::select(array('clave'=>'text','id'=>'value'))->get()->fetch_all();
        if($tipoequiporadios)
        {
        	$return["msg"] = $tipoequiporadios;
        	$return["ok"] = true;
        }	
        else
        	$return["msg"] = "No se pudieron obtener los tipos de equipos de radio";
        echo json_encode($return);
	}
}
?>