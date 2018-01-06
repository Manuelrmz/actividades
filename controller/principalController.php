<?php
class principalController extends Controller
{
	public function __construct() 
    {
        parent::__construct();
    }
	public function index()
	{
		Session::regenerateId();
		Session::securitySession();
		$this->_view->renderizar('index','C4 Yucatan - Inicio');
	}
	public function login()
	{
		if(isset($_SESSION["userData"]))
			header("Location: /".BASE_DIR.DS."principal");
		else
		{
			$data = "";
			if(isset($_SESSION["error"]))
			{
				$data = $_SESSION["error"];
				unset($_SESSION["error"]);
			}
			$this->_view->renderizar('login','C4 Yucatan - Iniciar Sesion',"",$data);
		}
	}
}
?>