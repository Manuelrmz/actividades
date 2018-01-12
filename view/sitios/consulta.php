<?php
	echo $this->addScript('view/sitios/css/consulta.css','css');
  echo $this->addScript('core/slideshow/slide.css','css');
  echo $this->addScript('core/slideshow/slide.js','js');
	echo $this->addScript('core/kendo/js/kendo.web.min.js','js');
	echo $this->addScript('core/js/validaciones.js','js');
  echo $this->addScript('core/css/alertbox.css','css');
	echo $this->addScript('view/sitios/js/consulta.js','js');
?>
<div class="div-border col-xs-12">
  <div class="col-sm-12 text-center margin-form"><h3>Sitios de Telecomunicaciones</h3></div>
  <div class="col-sm-5">
    <div class="col-xs-6 bold margin-form">Nombre</div>
    <div class="col-xs-6 bold margin-form">Municipio</div>
    <div class="col-xs-6 margin-form"><input type="text" name="nombre" id="nombre" class="k-textbox" placeholder="Nombre"></div>
    <div class="col-xs-6 margin-form"><select id="municipio" name="municipio" style="width:100%;"></select></div>
    <div class="col-xs-12 bold margin-form">Direccion</div>
    <div class="col-xs-12 margin-form"><input type="text" name="direccion" id="direccion" class="k-textbox" placeholder="Direccion"></div>
    <div class="col-xs-12 bold margin-form">Propietario</div>
    <div class="col-xs-12 margin-form"><input type="text" name="propietario" id="propietario" class="k-textbox" placeholder="Propietario"></div>
    <div class="col-xs-6 bold margin-form">Tipo Torre</div>
    <div class="col-xs-6 bold margin-form">Altura</div>
    <div class="col-xs-6 margin-form"><input type="text" name="tipoTorre" id="tipoTorre" class="k-textbox" placeholder="Tipo Torre"></div>
    <div class="col-xs-6 margin-form"><input type="text" name="alturaTorre" id="alturaTorre" class="k-textbox" placeholder="Altura"></div>
    <div class="col-xs-12 margin-form bold">Planta de Emergencia</div>
    <div class="col-xs-12 margin-form"><input type="text" name="plantaEmergencia" id="plantaEmergencia" class="k-textbox" placeholder="Planta de Emergencia"></div>
    <div class="col-xs-12 bold margin-form text-center">CFE</div>
    <div class="col-xs-12 margin-form bold"># de Servicio</div>
    <div class="col-xs-12 margin-form"><input type="text" name="cfeServicio" id="cfeServicio" class="k-textbox" placeholder="Numero de Servicio"></div>
    <div class="col-xs-12 margin-form bold"># de medidor</div>
    <div class="col-xs-12 margin-form"><input type="text" name="cfeMedidor" id="cfeMedidor" class="k-textbox" placeholder="Numero de Medidor"></div>
    <div class="col-xs-12 bold margin-form text-center">Transformador</div>
    <div class="col-xs-3 margin-form bold">Propio</div>
    <div class="col-xs-9 margin-form" style="text-align:left;"><input type="checkbox" name="transPropio" id="transPropio"></div>
    <div class="col-xs-12 margin-form bold">Capacidad</div>
    <div class="col-xs-12 margin-form"><input type="text" name="transCapacidad" id="transCapacidad" class="k-textbox" placeholder="Capacidad"></div>
    <div class="col-xs-12 margin-form bold">Comentarios</div>
    <div class="col-xs-12 margin-form"><textarea name="comentarios" id="comentarios" class="k-textbox"></textarea></div>
  </div>
  <div class="col-sm-7">
    <div class="bold margin-form text-center">Aires Acondicionados</div>
    <div id="tablaAires" class="margin-form"></div>
    <div class="bold margin-form text-center">Servicios Contratados</div>
    <div id="tablaServicios" class="margin-form"></div>
    <div class="bold margin-form text-center">Equipos Instalados</div>
    <div id="tablaEquipos" class="margin-form"></div>
  </div>
  <div class="col-sm-12 margin-form text-center" style="margin-top:10px;">
    <div class="k-button" id="btnAlbum" style="margin-left:15px; display:none;">Album</div>
  </div>
</div>
<div class="div-border col-xs-12">
  <div id="tablaSitios"></div>
</div>
<div id="album" hidden = hidden>
	<div id="slideshow"></div>
</div>
