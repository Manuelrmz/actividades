<?php
class usuariosController extends Controller
{
    private $_usuario = array();
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
        $this->validatePermissions('usuariosadmon');
        $this->_view->renderizar('index','Actividades C4 - Administrar Usuarios');
    }
    public function new()
    {
        Session::regenerateId();
        Session::securitySession();
        $this->validatePermissions('usuariosadmon');
        $db = new queryBuilder();
        $this->usuario($_POST);
        $condi = true;
        $condi = $condi && $this->_validar->MinMax($this->_data["usuario"],1,50,'Usuario');
        $condi = $condi && $this->_validar->MinMax($this->_data["nombres"],1,50,'Nombres');
        $condi = $condi && $this->_validar->MinMax($this->_data["apellidos"],1,50,'Apellidos');
        $condi = $condi && $this->_validar->NoEmpty($this->_data["pass1"],"Contrase&ntilde;a");
        $condi = $condi && $this->_validar->NoEmpty($this->_data["pass2"],"Confirmar Contrase&ntilde;a");
        $condi = $condi && $this->_validar->NoEmpty($this->_data["area"],"Area");
        $condi = $condi && $this->_validar->Int($this->_data["cargo"],'Cargo');
        if($condi)
        {
            if($this->_data["pass1"] === $this->_data["pass2"])
            {
                $permission = $this->mapPermissionUsers();
                $permission = array_merge($permission["allowed"],$permission["denied"]);
                $newPermission = explode(",",$this->_data["permisos"]);
                for($i = 0; $i < sizeof($newPermission); $i++)
                {
                    if($newPermission[$i] !== "")
                        $permission[$newPermission[$i]] = 1;
                }
                $this->_data["permisos"] = $permission;
                $this->_data["permisos"]["usuario"] = $this->_data["usuario"];
                $transaction = $db->transaction(function($q)
                {
                    $q->table('usuarios')->insert(array('usuario'=>$this->_data["usuario"],'contrasenia'=>MD5(SHA1($this->_data["pass1"])),'nombres'=>$this->_data["nombres"],'apellidos'=>$this->_data["apellidos"],
                        'cargo'=>$this->_data["cargo"],'area'=>$this->_data["area"]));
                    $q->table('permisos')->insert($this->_data["permisos"]);
                });
                if($transaction)
                {
                    $this->_return["msg"] = 'Usuario Guardado Correctamente';
                    $this->_return["ok"] = true;
                }
                else
                    $this->_return["msg"] = 'Error ingresando el usuario: '.$db->getError()["string"];
            }
            else
                $this->_return["msg"] = "Las contrase&ntilde;as no coinciden";
        }
        else
            $this->_return["msg"] = $this->_validar->getWarnings();
        echo json_encode($this->_return);
    }
    public function getForTable()
    {
        Session::regenerateId();
        Session::securitySession();
        $this->validatePermissions('usuariosadmon');
        $usuarios = usuarios::select(array('usuarios.usuario','usuarios.nombres','usuarios.apellidos','c.cargo','usuarios.ultimoAcceso','usuarios.area'))
                    ->join(array('cargosUsuario','c'),'usuarios.cargo','=','c.id','LEFT')
                    ->orderBy('usuarios.ultimoAcceso','DESC')->get()->fetch_all();
        if($usuarios)
        {
            $this->_return["msg"] = $usuarios;
            $this->_return["ok"] = true;
        }
        else
            $this->_return["msg"] = "No se encontraron usuarios";
        echo json_encode($this->_return);
    }
    public function getByUserWithPermission()
    {
        Session::regenerateId();
        Session::securitySession();
        $this->validatePermissions('usuariosadmon');
        $this->usuario($_POST);
        $condi = true;
        $condi = $condi && $this->_validar->NoEmpty($this->_data["usuario"],'Usuario');
        if($condi)
        {
            $usuario = usuarios::select(array('usuario','nombres','apellidos','cargo','area'))
                        ->where('usuario',$this->_data["usuario"])->get()->fetch_assoc();
            $permisos = permisos::where('usuario',$this->_data["usuario"])->get()->fetch_assoc();
            if($usuario)
            {
                $usuario["permisos"] = array_keys($this->mapPermissionUsers($permisos)["allowed"]);
                $this->_return["msg"] = $usuario;
                $this->_return["ok"] = true;
            }
            else
                $this->_return["msg"] = "No se encontro el usuario";
        }
        else
            $this->_return["msg"] = $this->_validar->getWarnings();
        echo json_encode($this->_return);
    }
    public function update()
    {
        Session::regenerateId();
        Session::securitySession();
        $this->validatePermissions('usuariosadmon');
        $db = new queryBuilder();
        $this->usuario($_POST);
        $condi = true;
        $condi = $condi && $this->_validar->MinMax($this->_data["usuario"],1,50,'Usuario');
        $condi = $condi && $this->_validar->MinMax($this->_data["nombres"],1,50,'Nombres');
        $condi = $condi && $this->_validar->MinMax($this->_data["apellidos"],1,50,'Apellidos');
        if($this->_data["pass1"] !== "" || $this->_data["pass2"] !== "")
        {
            $condi = $condi && $this->_validar->NoEmpty($this->_data["pass1"],"Contrase&ntilde;a");
            $condi = $condi && $this->_validar->NoEmpty($this->_data["pass2"],"Confirmar Contrase&ntilde;a");
            if($this->_data["pass1"] !== $this->_data["pass2"])
            {
                $condi = false;
                $this->_validar->setWarnings('Las Contraseñas No coinciden');
            }
        }
        $condi = $condi && $this->_validar->NoEmpty($this->_data["area"],"Area");
        $condi = $condi && $this->_validar->Int($this->_data["cargo"],'Cargo');
        if($condi)
        {
            $permission = $this->mapPermissionUsers();
            $permission = array_merge($permission["allowed"],$permission["denied"]);
            $newPermission = explode(",",$this->_data["permisos"]);
            for($i = 0; $i < sizeof($newPermission); $i++)
            {
                if($newPermission[$i] !== "")
                    $permission[$newPermission[$i]] = 1;
            }
            $this->_data["permisos"] = $permission;
            $transaction = $db->transaction(function($q)
            {
                $userarray = array('nombres'=>$this->_data["nombres"],'apellidos'=>$this->_data["apellidos"],'cargo'=>$this->_data["cargo"],'area'=>$this->_data["area"]);
                if($this->_data["pass1"] !== "" && $this->_data["pass2"] !== "")
                    $userarray['contrasenia'] = MD5(SHA1($this->_data["pass1"]));
                $q->table('usuarios')->where('usuario',$this->_data["usuario"])->update($userarray);
                $q->table('permisos')->where('usuario',$this->_data["usuario"])->update($this->_data["permisos"]);
            });
            if($transaction)
            {
                $this->_return["msg"] = 'Usuario Actualizado Correctamente';
                $this->_return["ok"] = true;
            }
            else
                $this->_return["msg"] = 'Error Actualizando el usuario: '.$db->getError()["string"];
        }
        else
            $this->_return["msg"] = $this->_validar->getWarnings();
        echo json_encode($this->_return);
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
            $this->_return["msg"] = $usuarios;
            $this->_return["ok"] = true;
        }
        else
            $this->_return["msg"] = "Error obteniendo la lista de usuarios";
        echo json_encode($this->_return);
    }
    public function mapPermissionUsers(array $currentAllowedPermission = array())
    {
        $permissionObj = new permisosController();
        $permissionNames = $permissionObj->getPermissionColumnNames();
        $allowedPermission = array('allowed'=>array(),'denied'=>array());
        if(sizeof($permissionNames) > 0)
        {
            
            foreach($permissionNames as $value)
            {
                if(isset($currentAllowedPermission[$value["value"]]) && $currentAllowedPermission[$value["value"]] == 1)
                    $allowedPermission["allowed"][$value["value"]] = 1;
                else
                    $allowedPermission["denied"][$value["value"]] = 2;
            } 
        } 
        return $allowedPermission;
    }
    public function usuario($data)
    {
        $this->_data["usuario"] = isset($data["usuario"]) ? $data["usuario"] : "";
        $this->_data["nombres"] = isset($data["nombres"]) ? $data["nombres"] : "";
        $this->_data["apellidos"] = isset($data["apellidos"]) ? $data["apellidos"] : "";
        $this->_data["pass1"] = isset($data["pass1"]) ? $data["pass1"] : "";
        $this->_data["pass2"] = isset($data["pass2"]) ? $data["pass2"] : "";
        $this->_data["cargo"] = isset($data["cargo"]) ? (integer)$data["cargo"] : 0;
        $this->_data["area"] = isset($data["area_input"]) ? $data["area_input"] : "";
        $this->_data["permisos"] = isset($data["permisos"]) ? $data["permisos"] : "";
    }
}
?>
