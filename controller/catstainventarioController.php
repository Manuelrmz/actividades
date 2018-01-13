<?php
class catstainventarioController extends Controller
{
	private $_data = array();
	private $_return = array('ok'=>false,'msg'=>'');
	public function __construct()
	{
		parent::__construct();
	}
	public function getForComboBox()
	{
		Session::regenerateId();
        Session::securitySession();
        $status = inventarioStatus::select(array('id'=>'value','nombre'=>'text'))->where('estado','1')->get()->fetch_all();
        if($status)
        {
        	$this->_return["msg"] = $status;
        	$this->_return["ok"] = true;
        }
        else
        	$this->_return["msg"] = "No se obtuvieron los status del inventario";
        echo json_encode($this->_return);
	}
}
?>