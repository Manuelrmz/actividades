<?php
class View
{
    private $_controlador;
    private $_cuerpo; 
    private $_pagina;
    private $_trace;
    public function renderizar($file,$titulo,$menu = "",$data="")
    {
        $this->_trace = debug_backtrace();
        $rutaPlantilla = VIEW_PATH .'layout'.DS.DEFAULT_LAYOUT.'.php';
        $rutaCuerpo = VIEW_PATH . str_replace("Controller","",$this->_trace[1]["class"]) . DS . $file . '.php';
        is_readable($rutaPlantilla) or die("Error de Lectura - Plantilla no encontrada");
        $this->_pagina = $this->cargarModelo($rutaPlantilla);
        $this->_pagina = $this->replace_content('/\#TITULO\#/ms',$titulo,$this->_pagina);
        $this->_pagina = $this->replace_content('/\#NAV\#/ms',$menu,$this->_pagina);
        if(is_readable($rutaCuerpo))
            $this->_pagina = $this->replace_content('/\#CUERPO\#/ms',$this->_cuerpo = $this->cargarModelo($rutaCuerpo,$data),$this->_pagina);
        else
            $this->_pagina = $this->replace_content('/\#CUERPO\#/ms',$this->_cuerpo = "Archivo de Pagina no Encontrada",$this->_pagina);
        echo $this->_pagina;
    }
    private function replace_content($in='/\#CONTENIDO\#/ms', $out,$pagina)
    {
        return preg_replace($in, $out, $pagina);        
    }
    private function cargarModelo($ruta,$data = "")
    {
        ob_start();
        include_once($ruta);
        return ob_get_clean();
    }
    public function addScript($src,$type)
    {
        $dataReturn = '';
        if($type=="css")
            $dataReturn = '<link rel="stylesheet" href="/'.BASE_DIR.'/'.$src.'">';
        else if($type=="js")
            $dataReturn = '<script type="text/javascript" charset="utf-8" src="/'.BASE_DIR.'/'.$src.'"></script>';
        return $dataReturn;
    }
}
?>