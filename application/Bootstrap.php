<?php
class Bootstrap
{
    private $_router;
    private $_address;
    public function run()
    {
        $this->_router = $this->loadClass(APP_PATH,'Router');
        $this->_router->match();
        $this->_address = $this->_router->getAddressArray();
        $this->render($this->_address);
    }
    public function render($address)
    {
        $controller = $address['controlador'].'Controller';
        $rutaControlador = CONTROLLER_PATH . $controller . '.php';
        $metodo = $address['metodo'];
        $args = $address['argumentos'];
        if(is_readable($rutaControlador))
        {
            require_once $rutaControlador;
            $controller = new $controller;
            if(is_callable(array($controller, $metodo)))
            {
                if($this->isAjax() && $address['controlador'] == DEFAULT_ERROR)
                    echo json_encode(array('ok'=>false,'status'=>'error','msg'=>'Error Procesando la Ruta')); 
                else
                {
                    if(isset($args))
                        call_user_func_array(array($controller, $metodo), $args);
                    else
                        call_user_func(array($controller, $metodo));
                }
            }
            else
            {
                if($address['controlador'] == DEFAULT_ERROR)
                    throw new Exception("Error Controller Method Doesn't Exist", 1);
                $this->render(array('controlador'=>DEFAULT_ERROR,'metodo'=>'index','argumentos'=>array()));
            }
        }
        else
        {
            if($address['controlador'] == DEFAULT_ERROR)
                throw new Exception("Error Controller Doesn't Exist", 1);
            $this->render(array('controlador'=>DEFAULT_ERROR,'metodo'=>'index','argumentos'=>array())); 
        }
    }
    public function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    public static function loadClass($directorio,$clase)
    {
        if(!@include_once $directorio.DS.$clase.'.php')
        {
            throw new Exception("Error Loading Class", 1);
        }
        return new $clase();
    }
    public static function loadInclude($directorio,$clase)
    {
        if(!@include_once $directorio.DS.$clase.'.php')
        {
            throw new Exception("Error Loading Include", 1);
        }
    }
}
?>