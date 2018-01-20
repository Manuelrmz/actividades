<?php
/*Modulos Principales (Todos lo tienen)*/
$this->get('principal');
$this->get('principal/login');
//Mantenimientos Radios
$this->get('manttoradios');
$this->post('manttoradios/add');
$this->post('manttoradios/update');
$this->post('manttoradios/getall');
$this->post('manttoradios/getbyid');
$this->get('manttoradios/createfile');
/*Modulos Radios*/
$this->get('radios/equipos');
$this->get('radios/visitasitio');
$this->get('radios/reporteador');
$this->post('radios/getpersonallist','getPersonalForList');
//Modulos Usuarios
$this->get('usuarios');
$this->post('usuarios/gettabla','getForTable');
$this->post('usuarios/getuserwithpermission','getByUserWithPermission');
$this->post('usuarios/update');
$this->post('usuarios/new');
$this->post('usuarios/login');
$this->get('usuarios/logout');
$this->get('usuarios/cambiarcontrasenia');
$this->post('usuarios/cambiarcontrasenia');
$this->post('usuarios/checkuseraccount');
$this->get('usuarios/createusersession');
$this->post('usuarios/getactivebyarea','getPersonalActivoByArea');
$this->post('usuarios/getactivebycurrentusercombo','getActivePersonalAreaByCurrentUserForCombo');
//Modulos Areas
$this->post('areas/getall');
$this->post('areas/operativas','getAreasOperativas');
$this->post('areas/getfortable','getForTable');
$this->post('areas/update');
$this->post('areas/new');
//Modulos cargosUsuario
$this->post('cargosusuario/getall');
$this->post('cargosusuario/getfortable','getForTable');
$this->post('cargosusuario/update');
$this->post('cargosusuario/new');
//Modulos Radios
$this->get('catalogo');
$this->get('catalogo/radios');
$this->get('catalogo/facturas');
/*Asignaciones*/
$this->post('asignacion/getall','getAsignaciones');
$this->post('asignacion/getactivas','getAsignacionesActivas');
$this->post("asignacion/getbyid");
$this->post("asignacion/add");
$this->post("asignacion/updatebyid");
/*dependencias*/
$this->post('dependencias/getall','getDependencias');
$this->post('dependencias/getactivas','getDependenciasActivas');
$this->post("dependencias/getbyid");
$this->post("dependencias/add");
$this->post("dependencias/updatebyid");
/*diagnosticos*/
$this->post('diagnostico/getall','getDiagnosticos');
$this->post('diagnostico/getactivas','getDiagnosticosActivos');
$this->post("diagnostico/getbyid");
$this->post("diagnostico/add");
$this->post("diagnostico/updatebyid");
/*mantenimientos*/
$this->post('mantenimientos/getall','getMantenimientos');
$this->post('mantenimientos/getactivas','getMantenimientosActivos');
$this->post("mantenimientos/getbyid");
$this->post("mantenimientos/add");
$this->post("mantenimientos/updatebyid");
//equipos radios
$this->post("equiposradios/add");
$this->post("equiposradios/updatebyid");
$this->post("equiposradios/getall",'getAll');
$this->post("equiposradios/getinfobyid",'getbyId');
$this->post('equiposradios/getfortable','getForTable');
//Tipos de equipos
$this->post("tipoequiporadios/getall",'getAll');
//Sitios
$this->post("sitios/getsitioslist",'getSitiosForList');
//Visitas Sitios
$this->post("visitasitios/add");
$this->post("visitasitios/update");
$this->post("visitasitios/getvisitastable","getVisitasForTable");
$this->post("visitasitios/getbyid");
$this->get('visitasitios/createfile');
/*Modulos Reporteador*/
$this->post('reporteador/senddata','saveData');
$this->get('reporteador/generar','generarReporte');
/*Modulos Servicios*/
$this->get('servicios');
$this->post('servicios/add');
$this->post('servicios/gettable','getForTable');
$this->post('servicios/getbyid','getById');
$this->post('servicios/updatebyid','updateById');
$this->get('servicios/getserviceorder','createServiceOrder');
/*Modulos catservicios*/
$this->post('catservicios/add');
$this->post('catservicios/getcombobyarea','getForComboBoxByArea');
/*Modulos Solicitantes*/
$this->post('solicitantes/getcombo','getForComboBox');
$this->post('solicitantes/getbyid');
$this->post('solicitantes/add');
/*Modulos Sitios*/
$this->get('sitios');
$this->get('sitios/consulta');
$this->post('sitios/add');
$this->post('sitios/updatebyid');
$this->post('sitios/getfortable','getSitiosForList');
$this->post('sitios/getbyid','getById');
$this->post('sitios/getforcombo','getSitiosForCombo');
/*ejercicios*/
$this->get('ejercicio');
$this->post('ejercicio/getforcombo','getEjerciciosForCombo');
$this->post('ejercicio/getfortable','getEjerciciosForTable');
$this->post('ejercicio/new');
$this->post('ejercicio/update');
/*Programas*/
$this->post('programa/new');
$this->post('programa/update');
$this->post('programa/getbyejercicio','getProgramasByEjercicio');
$this->post('programa/getforcombobyejercicio','getProgramasForComboByEjercicio');
/*Catalogo Categorias Inventario*/
$this->post('catinventario/getfortable','getForTable');
$this->post('catinventario/update');
$this->post('catinventario/new');
$this->post('catinventario/getforcombo','getForComboBox');
/*modulos facturas*/
$this->get('facturas');
$this->post('facturas/add');
$this->post('facturas/update');
$this->post('facturas/getbyid');
$this->post('facturas/getfortable');
$this->post('facturas/getcatfortable','getCatalogsForTable');
$this->post('facturas/getforcombo','getForComboBox');
/*modulos proveedores*/
$this->get('proveedores');
$this->post('proveedores/getfortable');
$this->post('proveedores/getrfcforcombo','getForComboRFC');
$this->post('proveedores/getbyid','getById');
$this->post('proveedores/add');
$this->post('proveedores/update');
/*modulos municipios*/
$this->post('municipios/getselect','getForComboBox');
/*Modulos Permisos*/
$this->post('permisos/getpermissionnames','getPermissionNames');
/*Modulos inventario*/
$this->get('inventario');
$this->post('inventario/add');
$this->post('inventario/update');
$this->post('inventario/getbyid');
$this->post('inventario/getfortable','getForTable');
$this->post('inventario/getallowedforresguardocombo','getAllowedForResguardoCombo');
/*Catalogo Tipos de Equipo Inventario*/
$this->post('catteinventario/getfortable','getForTable');
$this->post('catteinventario/update');
$this->post('catteinventario/new');
$this->post('catteinventario/getforcombo','getForComboBox');
/*Catalogo marcas Inventario*/
$this->post('catmarcainventario/getfortable','getForTable');
$this->post('catmarcainventario/update');
$this->post('catmarcainventario/new');
$this->post('catmarcainventario/getforcombo','getForComboBox');
/*Catalogo unidades de medida Inventario*/
$this->post('catuminventario/getfortable','getForTable');
$this->post('catuminventario/update');
$this->post('catuminventario/new');
$this->post('catuminventario/getforcombo','getForComboBox');
/*Catalogo status inventarios*/
$this->post('catstainventario/getforcombo','getForComboBox');
/*Modulo Resguardos*/
$this->get('resguardos');
/*Modulo Recibos*/
$this->get('recibos');
/*Modulos Manejo de Errores*/
$this->get('error');
$this->get('error/error403');
$this->get('error/error404');
?>
