<?php
	class cargosusuarioController extends Controller
	{
		public function __construct()
		{
			parent::__construct();
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
	}
?>