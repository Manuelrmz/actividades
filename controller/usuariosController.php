<?php
class usuariosController extends Controller
{
    private $_usuario = array();
	public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        Session::regenerateId();
        Session::securitySession();
        $this->validatePermissions('usuariosadmon');
        $this->_view->renderizar('index','UCTA - Administrar Usuarios');
    }
    public function nuevo()
    {
        Session::regenerateId();
        Session::securitySession();
        $this->validatePermissions('usuariosadmon');
        $db = new queryBuilder();
        $return = array('ok'=>false,'msg'=>'');
        $condi = true;
        $this->usuario();
        $this->_usuario["capturaOf"] = isset($_POST["capof"]) ? 1 : 2;
        $this->_usuario["busquedaOf"] = isset($_POST["busof"]) ? 1 : 2;
        $this->_usuario["seguimientoOf"] = isset($_POST["segaof"]) ? 1 : 2;
        $this->_usuario["seguimientoGenOf"] = isset($_POST["seggof"]) ? 1 : 2;
        $this->_usuario["adminusers"] = isset($_POST["admuser"]) ? 1 : 2;
        $this->_usuario["historico"] = isset($_POST["historico"]) ? 1 : 2;
        $this->_usuario["acceso"] = isset($_POST["acceso"]) ? 1 : 2;
        $condi = $condi && $this->_validar->MinMax($this->_usuario["usuario"],1,50,'Usuario');
        $condi = $condi && $this->_validar->MinMax($this->_usuario["nombres"],1,50,'Nombres');
        $condi = $condi && $this->_validar->MinMax($this->_usuario["apellidos"],1,50,'Apellidos');
        $condi = $condi && $this->_validar->NoEmpty($this->_usuario["pass1"],"Contrase&ntilde;a");
        $condi = $condi && $this->_validar->NoEmpty($this->_usuario["pass2"],"Confirmar Contrase&ntilde;a");
        $condi = $condi && $this->_validar->NoEmpty($this->_usuario["area"],"Area");
        $condi = $condi && $this->_validar->Int($this->_usuario["cargo"],'Cargo');
        if($condi)
        {
            if($this->_usuario["pass1"] == $this->_usuario["pass2"])
            {
                $transaction = $db->transaction(function($q)
                {
                    $q->table('usuarios')->insert(array('usuario'=>$this->_usuario["usuario"],'contrasenia'=>MD5(SHA1($this->_usuario["pass1"])),
                                                'nombres'=>$this->_usuario["nombres"],'apellidos'=>$this->_usuario["apellidos"],
                                                'cargo'=>$this->_usuario["cargo"],'area'=>$this->_usuario["area"]));
                    $q->table('permisos')->insert(array('usuario'=>$this->_usuario["usuario"],'capturaOf'=>$this->_usuario["capturaOf"],
                                                        'busquedaOf'=>$this->_usuario["busquedaOf"],'seguimientoOf'=>$this->_usuario["seguimientoOf"],
                                                        'seguimientoGenOf'=>$this->_usuario["seguimientoGenOf"],'adminusers'=>$this->_usuario["adminusers"],'historico'=>$this->_usuario["historico"],'acceso'=>$this->_usuario["acceso"]));
                });
                if($transaction)
                {
                    $return["msg"] = 'Usuario Guardado Correctamente';
                    $return["ok"] = true;
                }
                else
                    $return["msg"] = 'Error ingresando el usuario: '.$db->getError()["string"];
            }
            else
                $return["msg"] = "Las contrase&ntilde;as no coinciden";
        }
        else
            $return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
    }
    public function modificarUsuarioPermisos()
    {
        Session::regenerateId();
        Session::securitySession();
        $this->validatePermissions('usuariosadmon');
        $db = new queryBuilder();
        $return = array('ok'=>false,'msg'=>'');
        $condi = true;
        $this->usuario();
        $this->_usuario["capturaOf"] = isset($_POST["capof"]) ? 1 : 2;
        $this->_usuario["busquedaOf"] = isset($_POST["busof"]) ? 1 : 2;
        $this->_usuario["seguimientoOf"] = isset($_POST["segaof"]) ? 1 : 2;
        $this->_usuario["seguimientoGenOf"] = isset($_POST["seggof"]) ? 1 : 2;
        $this->_usuario["adminusers"] = isset($_POST["admuser"]) ? 1 : 2;
        $this->_usuario["historico"] = isset($_POST["historico"]) ? 1 : 2;
        $this->_usuario["acceso"] = isset($_POST["acceso"]) ? 1 : 2;
        $condi = $condi && $this->_validar->MinMax($this->_usuario["usuario"],1,50,'Usuario');
        $condi = $condi && $this->_validar->MinMax($this->_usuario["nombres"],1,50,'Nombres');
        $condi = $condi && $this->_validar->MinMax($this->_usuario["apellidos"],1,50,'Apellidos');
        if($this->_usuario["pass1"] != "" || $this->_usuario["pass2"] != "")
        {
            $condi = $condi && $this->_validar->NoEmpty($this->_usuario["pass1"],"Contrase&ntilde;a");
            $condi = $condi && $this->_validar->NoEmpty($this->_usuario["pass2"],"Confirmar Contrase&ntilde;a");
            if($this->_usuario["pass1"] != $this->_usuario["pass2"])
            {
                $condi = false;
                $this->_validar->setWarnings('Las Contraseñas No coinciden');
            }
        }
        $condi = $condi && $this->_validar->NoEmpty($this->_usuario["area"],"Area");
        $condi = $condi && $this->_validar->Int($this->_usuario["cargo"],'Cargo');
        if($condi)
        {
            $transaction = $db->transaction(function($q)
            {
                $userarray = array('nombres'=>$this->_usuario["nombres"],'apellidos'=>$this->_usuario["apellidos"],'cargo'=>$this->_usuario["cargo"],'area'=>$this->_usuario["area"]);
                if($this->_usuario["pass1"] != "" && $this->_usuario["pass2"] != "")
                    $userarray['contrasenia'] = MD5(SHA1($this->_usuario["pass1"]));
                $q->table('usuarios')->where('usuario',$this->_usuario["usuario"])->update($userarray);
                $q->table('permisos')->where('usuario',$this->_usuario["usuario"])->update(array('capturaOf'=>$this->_usuario["capturaOf"],'busquedaOf'=>$this->_usuario["busquedaOf"],'seguimientoOf'=>$this->_usuario["seguimientoOf"],
                                                    'seguimientoGenOf'=>$this->_usuario["seguimientoGenOf"],'adminusers'=>$this->_usuario["adminusers"],'historico'=>$this->_usuario["historico"],'acceso'=>$this->_usuario["acceso"]));
            });
            if($transaction)
            {
                $return["msg"] = 'Usuario Actualizado Correctamente';
                $return["ok"] = true;
            }
            else
                $return["msg"] = 'Error Actualizando el usuario: '.$db->getError()["string"];
        }
        else
            $return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
    }
    public function obtenerTabla()
    {
        Session::regenerateId();
        Session::securitySession();
        $this->validatePermissions('usuariosadmon');
        $return = array('ok'=>false,'msg'=>'');
        $usuarios = usuarios::select(array('usuarios.usuario','usuarios.nombres','usuarios.apellidos','c.cargo','usuarios.ultimoAcceso','usuarios.area'))->join(array('cargosUsuario','c'),'usuarios.cargo','=','c.id','LEFT')->orderBy('usuarios.ultimoAcceso','DESC')->get()->fetch_all();
        if($usuarios)
        {
            $return["msg"] = $usuarios;
            $return["ok"] = true;
        }
        else
            $return["msg"] = "No se encontraron usuarios";
        echo json_encode($return);
    }
    public function obtenerUsuario()
    {
        Session::regenerateId();
        Session::securitySession();
        $this->validatePermissions('usuariosadmon');
        $return = array('ok'=>false,'msg'=>'');
        $this->usuario();
        $condi = true;
        $condi = $condi && $this->_validar->NoEmpty($this->_usuario["usuario"],'Usuario');
        if($condi)
        {
            $usuario = usuarios::select(array('usuarios.nombres','usuarios.apellidos','usuarios.cargo','usuarios.area','p.*'))->join(array('permisos','p'),'usuarios.usuario','=','p.usuario','LEFT')->where('usuarios.usuario',$this->_usuario["usuario"])->get()->fetch_assoc();
            if($usuario)
            {
                $return["msg"] = $usuario;
                $return["ok"] = true;
            }
            else
                $return["msg"] = "No se encontro el usuario";
        }
        else
            $return["msg"] = $this->_validar->getWarnings();
        echo json_encode($return);
    }
    public function cambiarcontrasenia()
    {
        Session::regenerateId();
        Session::securitySession();
        $data = "";
        if(isset($_POST["actual"]) && isset($_POST["pass1"]) && isset($_POST["pass2"]))
        {
            $passActual = $_POST["actual"];
            $pass1 = $_POST["pass1"];
            $pass2 = $_POST["pass2"];
            if($pass1 == $pass2 && ($pass1 != "" && $pass2 != ""))
            {
                $usuario = usuarios::select(array('contrasenia'))->where('usuario',$_SESSION["userData"]["usuario"])->where('contrasenia',MD5(SHA1($passActual)))->get()->fetch_assoc();
                if($usuario)
                {
                    $usuario = usuarios::where('usuario',$_SESSION["userData"]["usuario"])->where('contrasenia',MD5(SHA1($passActual)))->update(array('contrasenia'=>MD5(SHA1($pass1))));
                    $data = "Contrase&ntilde;a modificada correctamente";
                }
                else
                    $data = "La contrase&ntilde;a ingresada no coincide con la del usuario actual";
            }
            else
                $data = "Las contrase&ntilde;as no coinciden";
        }
        $this->_view->renderizar('cambiarcontrasenia','CESP - Cambiar Contrase&ntilde;a',"",$data);
    }
    public function login()
    {
    	if(isset($_POST["user"]) && isset($_POST["password"]))
    	{
    		$usuario = usuarios::select(array('usuarios.contrasenia','usuarios.nombres','usuarios.apellidos','usuarios.cargo','usuarios.area','permisos.*'))->join('permisos','usuarios.usuario','=','permisos.usuario','LEFT')->where('usuarios.usuario',$_POST["user"])->get()->fetch_assoc();
            if($usuario)
            {
                if($usuario["acceso"] == 1)
                {
                    if($usuario["contrasenia"] == MD5(SHA1($_POST["password"])))
                    {
                        $actu = usuarios::where('usuario',$_POST["user"])->update(array('ultimoAcceso'=>date("Y-m-d H:i:s")));
                        $_SESSION["userData"] = $usuario;
                        Session::securitySession();
                        header("Location: /".BASE_DIR.DS."principal");
                        exit();
                    }
                    else
                       $_SESSION["error"] = "Usuario o Contrase&ntilde;a no valido";
                }
                else
                    $_SESSION["error"] = "Usuario o Contrase&ntilde;a no valido";
            }
            else
                $_SESSION["error"] = "Usuario o Contrase&ntilde;a no valido";
    	}
    	else
    		$_SESSION["error"] = "No se recibieron el nombre de usuario y contrase&ntilde;a";
        header("Location: /".BASE_DIR.DS."principal/login");
    }
    public function logout()
    {
        Session::destroySession();
        header("Location: /".BASE_DIR.DS."principal/login");
    }
    public function checkuseraccount()
    {
        $return = array('ok'=>false,'msg'=>'');
        if(isset($_POST["username"]))
        {
            $userExist = usuarios::select(array('allowByCurl','hashCurl'))->where('usuario',$_POST["username"])->get()->fetch_assoc();
            if($userExist)
            {
                if($userExist["allowByCurl"] == 0)
                {
                    usuarios::where('usuario',$_POST["username"])->update(array('allowByCurl'=>1));
                    $return["ok"] = true;
                    $return["msg"] = "Usuario correcto";
                }
                else
                    $return["msg"] = "El usuario no tiene permitido el acceso";
            }
            else
                $return["msg"] = "El usuario enviado no existe";
        }
        else
            $return["msg"] = "Parametros no enviados correctamente";
        echo json_encode($return);
    }
    public function createusersession($hash)
    {
        $cryptObj = new Crypt();
        $hash = explode("¬",$cryptObj->DecryptString($hash,"c_4_S!ecret[Key]*//!"));
        if(sizeof($hash) == 2)
        {
            $userExist = usuarios::select(array('usuarios.allowByCurl','usuarios.nombres','usuarios.apellidos','usuarios.cargo','usuarios.area','permisos.*'))->join('permisos','usuarios.usuario','=','permisos.usuario','LEFT')->where('usuarios.usuario',$hash[0])->get()->fetch_assoc();
            if($userExist)
            {
                if($userExist["allowByCurl"] == 1)
                {
                    usuarios::where('usuario',$hash[0])->update(array('allowByCurl'=>0,'ultimoAcceso'=>date("Y-m-d H:i:s")));
                    unset($userExist["allowByCurl"]);
                    $_SESSION["userData"] = $userExist;
                    Session::securitySession();
                    header("Location: /".BASE_DIR.DS."principal");
                    exit();
                }
            }
        }
        header("Location: /".BASE_DIR.DS."principal/login");
    }
    public function getPersonalActivoByArea()
    {
        Session::regenerateId();
        Session::securitySession();
        $return = array('ok'=>false,'msg'=>'');
        $usuarios = usuarios::select(array('CONCAT(usuarios.nombres," ",usuarios.apellidos)'=>'text','id'=>'value'))
                    ->join('permisos','usuarios.usuario','=','permisos.usuario','LEFT')
                    //->where('usuarios.area',$_SESSION["userData"]["area"])
                    ->where(function($q)
                    {
                      $q->where('usuarios.area','Sistemas');
                      $q->orWhere('usuarios.area','Video');
                      $q->orWhere('usuarios.area','Telefonia');
                    })
                    ->where('permisos.acceso',1)
                    ->get()->fetch_all();
        if($usuarios)
        {
            $return["msg"] = $usuarios;
            $return["ok"] = true;
        }
        else
            $return["msg"] = "Error obteniendo la lista de usuarios";
        echo json_encode($return);
    }
    public function usuario()
    {
        $this->_usuario["usuario"] = isset($_POST["usuario"]) ? $_POST["usuario"] : "";
        $this->_usuario["nombres"] = isset($_POST["nombres"]) ? $_POST["nombres"] : "";
        $this->_usuario["apellidos"] = isset($_POST["apellidos"]) ? $_POST["apellidos"] : "";
        $this->_usuario["pass1"] = isset($_POST["pass1"]) ? $_POST["pass1"] : "";
        $this->_usuario["pass2"] = isset($_POST["pass2"]) ? $_POST["pass2"] : "";
        $this->_usuario["cargo"] = isset($_POST["cargo"]) ? (integer)$_POST["cargo"] : 0;
        $this->_usuario["area"] = isset($_POST["area_input"]) ? $_POST["area_input"] : "";
    }
}
?>
