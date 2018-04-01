function fechaActual()
{
	var date = new Date();
	var diaMes = date.getDate();
	var dia = date.getDay();
	var mes = date.getMonth();
	var anio = date.getFullYear();
	var arregloDias = new Array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado");
	var arregloMeses = new Array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
	var valor = "";
	if(diaMes<10) diaMes="0"+diaMes;
	valor = arregloDias[dia] + " " + diaMes+ " de " + arregloMeses[mes] +" del año "+anio;
	document.write(valor);
}
function obtenerFecha()
{
	var d=new Date();
	dia = d.getDate();
	mes = d.getMonth() + 1;
	anio = d.getFullYear();
	if(dia<10){dia = "0"+dia;}
	if(mes<10){mes = "0"+mes;}
	cadenaFecha = anio+"-"+mes+"-"+dia;
	return cadenaFecha;
}
function menuAction()
{
	var menu = document.getElementById('menuSelect');
	var value = menu.options[menu.selectedIndex].value;
	if(value!="")
		 window.location.replace(value);
}
function detectMovil()
{
	if(navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/BlackBerry/i) || navigator.userAgent.match(/webOS/i))
		return true;
	else
		return false;
}
sumaFecha = function(d, fecha)
{
	var Fecha = new Date();
	var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getFullYear());
	var sep = sFecha.indexOf('/') != -1 ? '/' : '-'; 
	var aFecha = sFecha.split(sep);
	var fecha = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0];
	fecha= new Date(fecha);
	fecha.setDate(fecha.getDate()+parseInt(d));
	var anno=fecha.getFullYear();
	var mes= fecha.getMonth()+1;
	var dia= fecha.getDate();
	mes = (mes < 10) ? ("0" + mes) : mes;
	dia = (dia < 10) ? ("0" + dia) : dia;
	var fechaFinal = dia+sep+mes+sep+anno;
	return (fechaFinal);
 }