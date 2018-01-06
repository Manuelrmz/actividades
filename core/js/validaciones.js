var regEmail = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
var regHora = /^(0[0-9]|1\d|2[0-3]):([0-5]\d)(:([0-5]\d))*$/;
var regFecha = /^\d{4,4}-\d{1,2}-\d{1,2}$/;
var regEntero = /^\d+$/;
var regDecimal = /^-?\d+(\.(\d)*)?$/;
var regString = /^[a-zA-z]$/;
var regFechaHora = /^\d{4,4}-\d{1,2}-\d{1,2}\s(0[0-9]|1\d|2[0-3]):([0-5]\d)(:([0-5]\d))*$/;
var campoSemana = /^\d{4}-W\d{1,2}$/;
var useBoxMessage = false;
function updateError(texto,etiqueta)
{
	if(!useBoxMessage)
	{
		etiqueta.html("<b>"+texto+"</b>" );
		setTimeout(function(){etiqueta.html("")},5000);
	}
	else
	{
		showWarningBox(texto);
	}
}
function validarNoVacio(valor,nombre,etiqueta)
{
	if (valor.length < 1)
	{
		updateError("El campo " + nombre + " debe contener al menos 1 caracter.",etiqueta);
		return false;
	}
	else
	{
		return true;
	}
}
function validarTamanio(valor,nombre,min,max,etiqueta)
{
	if (valor.length < min || valor.length > max )
	{
		updateError("El tamaño del campo " + nombre + " debe estar entre " + min + " y " + max + " caracteres.",etiqueta);
		return false;
	}
	else
	{
		return true;
	}
}
function validarComboBox(obj,etiqueta,mensaje)
{
	var dato1 =""+obj.val().replace(/\s/g,"") ;
	var dato2 =""+obj.text().replace(/\s/g,"") ;
	if(dato1==dato2)
	{
		updateError(mensaje,etiqueta);
		return false;
	}
	else
	{
		return true;
	}
}
function revisarReg(valor,regexp,mensaje,etiqueta)
{
   if (!(regexp.test(valor)))
   {
	   updateError(mensaje,etiqueta);
	   return false;
   }
   else
   {
	  return true;
   }
}
//Validaciones preestablecidas
function validarEmail(valor,etiqueta)
{
    if(!regEmail.test(valor))
	{
		updateError("El email que envio no contiene un formato correcto.",etiqueta);
		return false;
	}
    else
    	return true;
}
function validarFecha(valor,mensaje,etiqueta)
{
	if(!regFecha.test(valor))
	{
		updateError(mensaje,etiqueta);
		return false;
	}
    else
		return true;
}
function validarHora(valor,mensaje,etiqueta)
{
	if(!regHora.test(valor))
	{
		updateError(mensaje,etiqueta);
		return false;
	}
    else
		return true;
}
function validarEntero(valor,mensaje,etiqueta)
{
	if(!regEntero.test(valor))
	{
		updateError(mensaje,etiqueta);
		return false;
	}
    else
		return true;
}
function validarDecimal(valor,mensaje,etiqueta)
{
	if(!regDecimal.test(valor))
	{
		updateError(mensaje,etiqueta);
		return false;
	}
    else
		return true;
}
function nan(valor,mensaje,etiqueta)
{
	if(isNaN(valor))
	{
		updateError(mensaje,etiqueta);
		return false;
	}
    else
		return true;
}
function validarString(valor,mensaje,etiqueta)
{
	if(!regString.test(valor))
	{
		updateError(mensaje,etiqueta);
		return false;
	}
    else
		return true;
}
function validarFechaHora(valor,mensaje,etiqueta)
{
	if(!regFechaHora.test(valor))
	{
		updateError(mensaje,etiqueta);
		return false;
	}
  else
		return true;
}
function validarMinMaxDate(obj,etiqueta)
{
	var fechaValidate = document.getElementById(obj);
	var aviso = document.getElementById(etiqueta);
	if(fechaValidate.min!="" && fechaValidate.max!="")
	{
		var value = new Date(fechaValidate.value),min = new Date(fechaValidate.min), max = new Date(fechaValidate.max);
        if (value < min || value > max)
        {
            aviso.innerHTML = 'La fecha debe estar entre ' + fechaValidate.min + ' y ' + fechaValidate.max;
            setTimeout(function(){aviso.innerHTML = "";},5000);
            return false;
        }
        else
            return true;
	}
	else if(fechaValidate.min!="")
		return validarMinDate(obj,etiqueta);
	else if(fechaValidate.max!="")
		return validarMaxDate(obj,etiqueta);
	else
		return true;
}
function validarMinDate(obj,etiqueta)
{
	console.log("min date");
	var fechaValidate = document.getElementById(obj);
	var aviso = document.getElementById(etiqueta);
	var value = new Date(fechaValidate.value),min = new Date(fechaValidate.min);
    if (value < min)
    {
    	console.log("Incorrecto");
        aviso.innerHTML = 'La fecha debe ser mayor a ' + fechaValidate.min;
        setTimeout(function(){aviso.innerHTML = "";},5000);
        return false;
    }
    else
    {
    	console.log("Correcto");
        return true;
    }
}
function validarMaxDate(obj,etiqueta)
{
	var fechaValidate = document.getElementById(obj);
	var aviso = document.getElementById(etiqueta);
	var value = new Date(fechaValidate.value),max = new Date(fechaValidate.max);
    if (value > max)
    {
        aviso.innerHTML = 'La fecha debe ser menor a ' + fechaValidate.max;
        setTimeout(function(){aviso.innerHTML = "";},5000);
        return false;
    }
    else
        return true;
}
function validarArchivo(extenciones,archivo,mensaje,elementoAviso)
{
	var extencion = "";
	var permitir = false;
	var extencion_permitida = extenciones.split(',');
	extencion = (archivo.val().substring(archivo.val().lastIndexOf("."))).toLowerCase();
	for(var i = 0; i < extencion_permitida.length; i++)
	{
		if(extencion_permitida[i]==extencion)
			permitir = true;
	}
	if(!permitir)
		elementoAviso.html(mensaje);
	return permitir
}
function showWarningBox(text)
{
	createAlertBox(text,"warning")
}
function showDangerBox(text)
{
	createAlertBox(text,"danger")
}
function showInfoBox(text)
{
	createAlertBox(text,"info")
}
function showSuccessBox(text)
{
	createAlertBox(text,"success")
}
function createAlertBox(text,type)
{
	var boxContainer = document.getElementsByClassName("alert-container");
	if (boxContainer !== undefined && boxContainer.length == 1)
	{
		var alertBox = document.createElement("div");
		var closebutton = document.createElement("span");
		var textContent = document.createTextNode(text);
		alertBox.className = 'alert alert-'+type;
		//alertBox.innerHTML = text;
		closebutton.className = 'closebtn';
		closebutton.innerHTML = '&times;';
		closebutton.addEventListener('click',function(){this.parentNode.remove()});
		alertBox.appendChild(closebutton);
		alertBox.appendChild(textContent);
		boxContainer[0].appendChild(alertBox);
		alertBox.style.opacity = 0;
		window.getComputedStyle(alertBox).opacity;
		alertBox.style.opacity = 1;
	}
}
