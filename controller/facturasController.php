<?php
class facturasController extends Controller
{
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
        $this->validatePermissions('factura');
        $this->_view->renderizar('index','C4 - Facturas');
    }
    public function add()
    {
        Session::regenerateId();
        Session::securitySession();
        $this->validatePermissions('factura');
        $this->factura($_POST);
        $db = new queryBuilder();
        $condi = true;
        $condi = $condi && $this->_validar->Date($this->_data["fecha"],"Fecha Factura");
        $condi = $condi && $this->_validar->MinMax($this->_data["noFactura"],1,50,"No. Factura");
        $condi = $condi && $this->_validar->MinMax($this->_data["condiPago"],1,100,"Condicion de Pago");
        $condi = $condi && $this->_validar->Int($this->_data["rfc"],"RFC Proveedor");
        $condi = $condi && $this->_validar->Int($this->_data["ejercicio"],"Ejercicio");
        $condi = $condi && $this->_validar->Int($this->_data["programa"],"Programa");
        $condi = $condi && $this->_validar->Int($this->_data["area"],"Area");
        if($condi)
        {
            $transaccion = $db->transaction(function($q)
            {
                $factura = $q->table('facturas')->insert(array('fecha'=>$this->_data["fecha"],'rfc'=>$this->_data["rfc"],'noFactura'=>$this->_data["noFactura"],
                                              'vendedor'=>$this->_data["vendedor"],'comprador'=>$this->_data["comprador"],'fechaEntrega'=>$this->_data["fechaEntrega"],
                                              'condiPago'=>$this->_data["condiPago"],'responsable'=>$this->_data["responsable"],'ejercicio'=>$this->_data["ejercicio"],
                                              'programa'=>$this->_data["programa"],'area'=>$this->_data["area"]));
                $this->_data["id"] = $factura;
                $equipos = null;
                if($this->_data["equip"] != "")
                    $equipos = json_decode($this->_data["equip"],true);
                if($equipos)
                {
                    foreach($equipos as $equipo)
                    {
                        unset($equipo["idInventario"]);
                        $equipo["idfactura"] = $this->_data["id"];
                        $q->table("inventario")->insert($equipo);
                    }
                }
            });
            if($transaccion)
            {
                $this->_return["msg"].= "Factura agregado correctamente";
                $this->_return["ok"] = true;
                if(isset($_FILES) && sizeof($_FILES) > 0)
                {
                    $fields = array_keys($_FILES);
                    foreach ($fields as $field)
                    {
                        $this->_data["correspondence"] = ($field === 'facturapdf' ? 'factura' : 'orden');
                        if($_FILES[$field]["tmp_name"] != "")
                        {
                            $this->_data["archivo"] = preg_replace(array('/á/','/é/','/í/','/ó/','/ú/','/Á/','/É/','/Í/','/Ó/','/Ú/'),array('a','e','i','o','u','A','E','I','O','U'),$_FILES[$field]["name"]);
                            $extension = explode(".",$this->_data["archivo"]);
                            $extension = $extension[count($extension)-1];
                            if($_FILES[$field]["type"] === "application/pdf")
                            {
                                $this->_data["filename"] = tempnam(ROOT . 'private/facturas/','');
                                unlink($this->_data["filename"]);
                                $this->_data["filename"] = explode('/',$this->_data["filename"]);
                                $this->_data["filename"] = $this->_data["filename"][count($this->_data["filename"])-1].'.'.$extension;
                                if(move_uploaded_file($_FILES[$field]['tmp_name'], ROOT . 'private/facturas/'.$this->_data["filename"]))
                                {
                                    $this->_data["filetype"] = $_FILES[$field]['type'];
                                    $this->_data["filesize"] = $_FILES[$field]['size'];
                                    $transaccion = $db->transaction(function($q)
                                    {
                                        $imagen = $q->table('archivos')->insert(array('filename'=>$this->_data["filename"],'name'=>$this->_data["archivo"],'type'=>$this->_data["filetype"],'size'=>$this->_data["filesize"],'usuarioAlta'=>$_SESSION["userData"]["usuario"],'fechaAlta'=>date('Y-m-d H:i:s')));
                                        $q->table('archivosFactura')->insert(array('idarchivo'=>$imagen,'idfactura'=>$this->_data["id"],'campo'=>$this->_data["correspondence"],'descripcion'=>''));
                                    });
                                    if($transaccion)
                                        $this->_return["msg"] .= "\n".$this->_data["archivo"]." guardada correctamente";
                                    else
                                    {
                                        $this->_return["msg"] .= "\nNo fue posible guardar ".$this->_data["archivo"];
                                        unlink(ROOT . 'private/facturas/'.$this->_data["filename"]);
                                    }
                                }
                                else
                                    $return["msg"] .= "\nNo fue posible guardar ".$this->_data["archivo"];
                            }
                            else
                                $return["msg"] .= "\nEl archivo ".$this->_data["archivo"]." no viene con un formato correcto.";
                        }
                    }
                }
            }
            else
                $this->_return["msg"].= "Ocurrio un error insertando la factura: ".$db->getError()["string"];
        }
        else
            $this->_return["msg"] = $this->_validar->getWarnings();
        echo json_encode($this->_return);
    }
    public function update()
    {
        Session::regenerateId();
        Session::securitySession();
        $this->validatePermissions('factura');
        $this->factura($_POST);
        $db = new queryBuilder();
        $condi = true;
        $condi = $condi && $this->_validar->Int($this->_data["id"],"ID");
        $condi = $condi && $this->_validar->Date($this->_data["fecha"],"Fecha Factura");
        $condi = $condi && $this->_validar->MinMax($this->_data["noFactura"],1,50,"No. Factura");
        $condi = $condi && $this->_validar->MinMax($this->_data["condiPago"],1,100,"Condicion de Pago");
        $condi = $condi && $this->_validar->Int($this->_data["rfc"],"RFC Proveedor");
        $condi = $condi && $this->_validar->Int($this->_data["ejercicio"],"Ejercicio");
        $condi = $condi && $this->_validar->Int($this->_data["programa"],"Programa");
        $condi = $condi && $this->_validar->Int($this->_data["area"],"Area");
        if($condi)
        {
            $transaccion = $db->transaction(function($q)
            {
                $factura = $q->table('facturas')->where('id',$this->_data["id"])->update(array('fecha'=>$this->_data["fecha"],'rfc'=>$this->_data["rfc"],'noFactura'=>$this->_data["noFactura"],'vendedor'=>$this->_data["vendedor"],'comprador'=>$this->_data["comprador"],'fechaEntrega'=>$this->_data["fechaEntrega"],
                  'condiPago'=>$this->_data["condiPago"],'responsable'=>$this->_data["responsable"],'ejercicio'=>$this->_data["ejercicio"],'programa'=>$this->_data["programa"],'area'=>$this->_data["area"]));
                $equipos = null;
                if($this->_data["equip"] != "")
                    $equipos = json_decode($this->_data["equip"],true);
                if($equipos)
                {
                    foreach($equipos as $equipo)
                    {
                        $equipo["id"] = $equipo["idInventario"];
                        $equipo["idfactura"] = $this->_data["id"];
                        unset($equipo["idInventario"]);
                        if($equipo["id"] !== null)
                            $q->table("inventario")->where('id',$equipo["id"])->update($equipo);
                        else
                            $q->table("inventario")->insert($equipo);
                    }
                }
                $deletedEquip = explode(",",$this->_data["deletedEquip"]);
                if(sizeOf($deletedEquip) > 0)
                {
                    for($i = 0; $i < sizeOf($deletedEquip); $i++)
                    {
                        if($deletedEquip[$i] !== "")
                            $q->table('inventario')->where("id",$deletedEquip[$i])->where("idfactura",$this->_data["id"])->delete();
                    }
                }
            });
            if($transaccion)
            {
                $this->_return["msg"].= "Factura modificada correctamente";
                $this->_return["ok"] = true;
                if(isset($_FILES) && sizeof($_FILES) > 0)
                {
                    $fields = array_keys($_FILES);
                    foreach ($fields as $field)
                    {
                        $this->_data["correspondence"] = ($field === 'facturapdf' ? 'factura' : 'orden');
                        if($_FILES[$field]["tmp_name"] != "")
                        {
                            $this->_data["archivo"] = preg_replace(array('/á/','/é/','/í/','/ó/','/ú/','/Á/','/É/','/Í/','/Ó/','/Ú/'),array('a','e','i','o','u','A','E','I','O','U'),$_FILES[$field]["name"]);
                            $extension = explode(".",$this->_data["archivo"]);
                            $extension = $extension[count($extension)-1];
                            $archivo = archivos::select(array('archivos.name','archivos.size'))->join(array('archivosFactura','af'),'archivos.id','=','af.idarchivo','LEFT')->where('af.idfactura',$this->_data["id"])->where('af.campo',$this->_data["correspondence"])->get()->fetch_assoc();
                            if(!$archivo || ($archivo && ($archivo["name"] !== $_FILES[$field]["name"] || $archivo["size"] != $_FILES[$field]["size"])))
                            {
                                $this->_data["requireUpdate"] = $archivo ? true : false;
                                if($_FILES[$field]["type"] === "application/pdf")
                                {
                                    $this->_data["filename"] = tempnam(ROOT . 'private/facturas/','');
                                    unlink($this->_data["filename"]);
                                    $this->_data["filename"] = explode('/',$this->_data["filename"]);
                                    $this->_data["filename"] = $this->_data["filename"][count($this->_data["filename"])-1].'.'.$extension;
                                    if(move_uploaded_file($_FILES[$field]['tmp_name'], ROOT . 'private/facturas/'.$this->_data["filename"]))
                                    {
                                        $this->_data["filetype"] = $_FILES[$field]['type'];
                                        $this->_data["filesize"] = $_FILES[$field]['size'];
                                        $transaccion = $db->transaction(function($q)
                                        {
                                            $imagen = $q->table('archivos')->insert(array('filename'=>$this->_data["filename"],'name'=>$this->_data["archivo"],'type'=>$this->_data["filetype"],'size'=>$this->_data["filesize"],'usuarioAlta'=>$_SESSION["userData"]["usuario"],'fechaAlta'=>date('Y-m-d H:i:s')));
                                            if($this->_data["requireUpdate"])
                                                $q->table('archivosFactura')->where('idfactura',$this->_data["id"])->where('campo',$this->_data["correspondence"])->update(array('idarchivo'=>$imagen));
                                            else
                                                $q->table('archivosFactura')->insert(array('idarchivo'=>$imagen,'idfactura'=>$this->_data["id"],'campo'=>$this->_data["correspondence"],'descripcion'=>''));
                                        });
                                        if($transaccion)
                                            $this->_return["msg"] .= "\n".$this->_data["archivo"]." guardada correctamente";
                                        else
                                        {
                                            $this->_return["msg"] .= "\nNo fue posible guardar ".$this->_data["archivo"];
                                            unlink(ROOT . 'private/facturas/'.$this->_data["filename"]);
                                        }
                                    }
                                    else
                                        $return["msg"] .= "\nNo fue posible guardar ".$this->_data["archivo"];
                                }
                                else
                                    $return["msg"] .= "\nEl archivo ".$this->_data["archivo"]." no viene con un formato correcto.";
                            }
                        }
                    }
                }
            }
            else
                $this->_return["msg"].= "Ocurrio un error actualizando la factura: ".$db->getError()["string"];
        }
        else
            $this->_return["msg"] = $this->_validar->getWarnings();
        echo json_encode($this->_return);
    }
    public function getbyid($id)
    {
        Session::regenerateId();
        Session::securitySession();
        $this->validatePermissions('factura');
        $condi = true;
        $condi = $condi && $this->_validar->Int($id,'ID');
        if($condi)
        {
            $factura = facturas::where('id',$id)->get()->fetch_assoc();
            if($factura)
            {
                $factura["equip"] = inventario::select(array('id'=>'idInventario','cantidad','codigo','categoria','tipoEquipo','marca','modelo','noSerie','um','descripcion'))->where('idfactura',$id)->get()->fetch_all();
                $factura["files"] = archivos::select(array('CONCAT("private/facturas/",archivos.filename)'=>'file','af.campo'))->join(array('archivosFactura','af'),'archivos.id','=','af.idarchivo','LEFT')->where('af.idfactura',$id)->get()->fetch_all();
                $this->_return["msg"] = $factura;
                $this->_return["ok"] = true;
            }
            else
                $this->_return["msg"] = "Factura no encontrada";
        }
        else
            $this->_return["msg"] = $this->_validar->getWarnings();
        echo json_encode($this->_return);
    }
    public function getfortable()
    {
        Session::regenerateId();
        Session::securitySession();
        $this->validatePermissions('factura');
        $this->factura($_POST);
        $facturas = facturas::select(array('facturas.id'=>'idFactura','facturas.fecha','facturas.noFactura','p.rfc','p.nombreEmpresa','facturas.fechaEntrega','e.anio'=>'ejercicio','po.nombre'=>'programa','a.nombre'=>'area'))
                                ->join(array('proveedores','p'),'facturas.rfc','=','p.id','LEFT')
                                ->join(array('ejercicio','e'),'facturas.ejercicio','=','e.id','LEFT')
                                ->join(array('programa','po'),'facturas.programa','=','po.id','LEFT')
                                ->join(array('areas','a'),'facturas.area','=','a.id','LEFT');
        if($this->_data["fecha"] !== "" || $this->_data["noFactura"] !=="" || $this->_data["rfc"] !== 0 || $this->_data["ejercicio"] !== 0)
        {
            if($this->_data["fecha"] !== "")
                $facturas = $facturas->where('facturas.fecha',$this->_data["fecha"]);
            if($this->_data["noFactura"] !=="")
                $facturas = $facturas->where('facturas.noFactura',"LIKE",'%'.$this->_data["noFactura"].'%');
            if($this->_data["rfc"] !== 0)
                $facturas = $facturas->where('facturas.rfc',$this->_data["rfc"]);
            if($this->_data["ejercicio"] !== 0)
                $facturas = $facturas->where('facturas.ejercicio',$this->_data["ejercicio"]);
        }
        else
            $facturas = $facturas->where('facturas.fechaAlta','>=',date("Y-m-d H:i:s", strtotime ("-1 month")));
        $facturas = $facturas->get()->fetch_all();
        if($facturas)
        {
            $this->_return["msg"] = $facturas;
            $this->_return["ok"] = true;
        }
        else
            $this->_return["msg"] = "No se encontraron facturas";
        echo json_encode($this->_return);
    }
    public function getCatalogsForTable()
    {
        Session::regenerateId();
        Session::securitySession();
        $this->_return["msg"] = array();
        $this->_return["msg"]["categoria"] = inventarioCategoria::select(array('id'=>'value','nombre'=>'text'))->where('estado','1')->get()->fetch_all();
        $this->_return["msg"]["tipoEquipo"] = inventarioTipoEquipo::select(array('id'=>'value','nombre'=>'text'))->where('estado','1')->get()->fetch_all();
        $this->_return["msg"]["marca"] = inventarioMarca::select(array('id'=>'value','nombre'=>'text'))->where('estado','1')->get()->fetch_all();
        $this->_return["msg"]["um"] = inventarioUM::select(array('id'=>'value','nombre'=>'text'))->where('estado','1')->get()->fetch_all();
        $this->_return["ok"] = true;
        echo json_encode($this->_return);
    }
    public function factura($data)
    {
        $this->_data["id"] = isset($data["id"]) ? (integer)$data["id"] : 0;
        $this->_data["fecha"] = isset($data["fecha"]) ? $data["fecha"] : "";
        $this->_data["rfc"] = isset($data["rfc"]) ? (integer)$data["rfc"] : 0;
        $this->_data["noFactura"] = isset($data["noFactura"]) ? $data["noFactura"] : "";
        $this->_data["vendedor"] = isset($data["vendedor"]) ? $data["vendedor"] : "";
        $this->_data["comprador"] = isset($data["comprador"]) ? $data["comprador"] : "";
        $this->_data["fechaEntrega"] = isset($data["fechaEntrega"]) ? ($data["fechaEntrega"] !== "" ? $data["fechaEntrega"] : null) : null;
        $this->_data["condiPago"] = isset($data["condiPago"]) ? $data["condiPago"] : "";
        $this->_data["responsable"] = isset($data["responsable"]) ? $data["responsable"] : "";
        $this->_data["ejercicio"] = isset($data["ejercicio"]) ? (integer)$data["ejercicio"] : 0;
        $this->_data["programa"] = isset($data["programa"]) ? (integer)$data["programa"] : 0;
        $this->_data["area"] = isset($data["area"]) ? (integer)$data["area"] : 0;
        $this->_data["deletedEquip"] = isset($data["deletedEquip"]) ? $data["deletedEquip"] : "";
        $this->_data["equip"] = isset($data["equip"]) ? $data["equip"] : "";
    }
}
?>
