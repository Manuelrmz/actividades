<?php
require_once CORE_PATH . "basicos.php";
require_once CORE_PATH . "validaciones.php";
class Controller
{
    protected $_view;
    protected $_validar;
    public function __construct() 
    {
        $this->_view = new View();
        $this->_validar = new validaciones();
    }
    public function validatePermissions($permiso)
    {
    	$usuario = $_SESSION["userData"]["usuario"];
    	$usuario = usuarios::select(array('permisos.*'))->join('permisos','usuarios.usuario','=','permisos.usuario','LEFT')->where('usuarios.usuario',$usuario)->get()->fetch_assoc();
    	if($usuario)
    	{
    		if($usuario[$permiso] != 1)
    		{
    			header('Location: /'.BASE_DIR.'/error/error403');
    			exit();
    		}
    	}
    	else
    	{
    		header('Location: /'.BASE_DIR.'/error/error403');
    		exit();
    	}
    }
}
?>