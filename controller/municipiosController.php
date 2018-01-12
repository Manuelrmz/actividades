<?php
class municipiosController extends Controller
{
	public function __construct()
  {
      parent::__construct();
  }
  public function getForComboBox()
  {
    echo json_encode(municipios::select(array('id'=>'value','nombre'=>'text'))->get()->fetch_all());
  }
}
?>
