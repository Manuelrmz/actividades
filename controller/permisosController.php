<?php
class permisosController extends Controller
{
	private $_data = array();
	private $_return = array('ok'=>false,'msg'=>'');
	public function __construct()
	{
		parent::__construct();
	}
	public function getPermissionNames()
	{
		Session::regenerateId();
        Session::securitySession();
        $this->validatePermissions('usuariosadmon');
        $permissionNames = $this->getPermissionColumnNames();
        if($permissionNames)
        {
        	$this->_return["msg"] = $permissionNames;
        	$this->_return["ok"] = true;
        }
        else
        	$this->_return["msg"] = "La tabla de Permisos no contiene los valores requeridos, contacte al administrador";
        echo json_encode($this->_return);
	}
	public function getPermissionColumnNames()
	{
		Session::regenerateId();
        Session::securitySession();
        $this->validatePermissions('usuariosadmon');
        $db = new queryBuilder();
        $columnsName = $db->query('SELECT COLUMN_NAME as `value`, COLUMN_COMMENT as `text` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`= "actividadesc4" AND `TABLE_NAME`= "permisos" AND COLUMN_COMMENT <> ""',array())->fetch_all();
        return $columnsName;
	}
	public function permiso($data)
	{
		$this->_data["usuario"] = isset($data["usuario"]) ? $data["usuario"] : "";
		$this->_data["permisos"] = isset($data["permisos"]) ? $data["permisos"] : "";
	}
}
?>