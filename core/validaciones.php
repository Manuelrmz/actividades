<?php
class validaciones
{
	public $_mensaje;
	private $_regEmail = '/[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/';
	private $_regHora = '/^(0[0-9]|1\d|2[0-3]):([0-5]\d)(:([0-5]\d))*$/';
	private $_regFecha = '/^\d{4,4}-\d{1,2}-\d{1,2}$/';
	private $_regEntero = '/^\d+$/';
	private $_regDecimal = '/^(-)?\d+(\.\d{1,30})?$/';
	private $_regString = '/^[a-zA-z]$/';
	private $_regFechaHora = '/^\d{4,4}-\d{1,2}-\d{1,2}\s(0[0-9]|1\d|2[0-3]):([0-5]\d)(:([0-5]\d))*$/';
	public function Date($obj,$nombre)
	{
		return $this->validarExpresion($obj,$this->_regFecha,$nombre);
	}
	public function Time($obj,$nombre)
	{
		return $this->validarExpresion($obj,$this->_regHora,$nombre);
	}
	public function DateTime($obj,$nombre)
	{
		return $this->validarExpresion($obj,$this->_regFechaHora,$nombre);
	}
	public function Email($obj,$nombre)
	{
		return $this->validarExpresion($obj,$this->_regEmail,$nombre);
	}
	public function Int($obj,$nombre)
	{
		return $this->validarExpresion($obj,$this->_regEntero,$nombre);
	}
	public function Double($obj,$nombre)
	{
		return $this->validarExpresion($obj,$this->_regDecimal,$nombre);
	}
	public function String($obj,$nombre)
	{
		return $this->validarExpresion($obj,$this->_regString,$nombre);
	}
	public function Min($obj,$min,$nombre)
	{
		if (strlen($obj) < $min ) 
		{
		    $this->_mensaje .= 'El tamaño de campo '.$nombre.' no debe ser menor a '.$min.' caracteres. </br>';
		    return false;
		}
		else
		    return true;
	}
	public function Max($obj,$max,$nombre)
	{
		if (strlen($obj) > $max ) 
		{
		    $this->_mensaje .= 'El tamaño de campo '.$nombre.' no debe ser mayor a '.$max.' caracteres. </br>';
		    return false;
		}
		else
		    return true;
	}
	public function MinInt($value,$min,$mensaje)
	{
		if ($value < $min ) 
		{
		    $this->_mensaje .= $mensaje.'</br>';
		    return false;
		}
		else
		    return true;
	}
	public function MaxInt($value,$max,$mensaje)
	{
		if ($value > $max ) 
		{
		    $this->_mensaje .= $mensaje.'</br>';
		    return false;
		}
		else
		    return true;
	}
	public function MinMaxInt($obj,$min,$max,$mensaje)
	{
		if ($obj < $min || $obj > $max ) 
		{
		    $this->_mensaje .= $mensaje;
		    return false;
		}
		else
		    return true;
	}
	public function NoEmpty($obj,$nombre)
	{
		if (strlen($obj) < 1 ) 
		{
		    $this->_mensaje .= 'El tamaño de campo '.$nombre.' no debe estar vacio. </br>';
		    return false;
		}
		else
		    return true;
	}
	public function MinMax($obj,$min,$max,$nombre)
	{
		if (strlen($obj) < $min || strlen($obj) > $max ) 
		{
		    $this->_mensaje .= 'El tamaño de campo '.$nombre.' debe estar entre '.$min.' y '.$max.' caracteres. </br>'.$obj;
		    return false;
		}
		else
		    return true;
	}
	public function validarExpresion($obj,$expre,$nombre)
	{
		if(!preg_match($expre,$obj))
        {
                $this->_mensaje .= 'El Campo '.$nombre.' no contiene un formato correcto, modifiquelo y envie los datos nuevamente</br>';
                return false;
        }
        else
            return true;
	}
	public function getWarnings()
	{
		return $this->_mensaje;
	}
	public function setWarnings($msg)
	{
		$this->_mensaje = $msg;
	}
	public function JSON($parametro)
	{
		$return = array('ok'=>false,'datos'=>array());
		$datos = json_decode($parametro);
		if(json_last_error() === JSON_ERROR_NONE)
		{
			$return["datos"] = $datos;
			$return["ok"] = true;
		}
		else
			$this->_mensaje .= 'El JSON enviado no es correcto';
		return $return;
	}
}
?>