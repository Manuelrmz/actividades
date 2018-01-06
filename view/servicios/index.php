<?php
	echo $this->addScript('view/servicios/css/index.css','css');
	echo $this->addScript('core/kendo/js/kendo.web.min.js','js');
	echo $this->addScript('core/js/validaciones.js','js');
  echo $this->addScript('core/css/alertbox.css','css');
	echo $this->addScript('view/servicios/js/index.js','js');
?>
<div class="div-border col-sm-12">
  <div class="col-sm-12 text-center margin-form bold"><h3>Formulario de Servicios</h3></div>
  <div class="col-sm-12 bold margin-form" id="title">Nuevo Servicio</div>
  <div class="col-sm-1" style="display:none;">
    <div class="col-sm-12 margin-form bold">Folio</div>
    <div class="col-sm-12 margin-form"><input type="text" id="folio" class="k-textbox" readonly></div>
  </div>
  <form enctype="multipart/form-data" id="formServicio" method="post">
    <div class="col-sm-3">
      <div class="col-sm-12 margin-form bold">Fecha y Hora</div>
      <div class="col-sm-12 margin-form"><input  id="fechaInicio"></div>
    </div>
    <div class="col-sm-3">
      <div class="col-sm-12 margin-form bold tooltip">Solicitante<span id="ttSolicitante" class="tooltiptext"></span></div>
      <div class="col-sm-12 margin-form"><select name="solicitante" id="solicitante" style="width:100%;"></select></div>
    </div>
		<div class="col-sm-3">
			<div class="col-sm-12 margin-form bold">Area</div>
      <div class="col-sm-12 margin-form"><select name="area" id="area" style="width:100%;"></select></div>
    </div>
    <div class="col-sm-3">
      <div class="col-sm-12 margin-form bold">Tipo Servicio</div>
      <div class="col-sm-12 margin-form"><select name="tipoServicio" id="tipoServicio" style="width:100%;"></select></div>
    </div>
    <div class="col-sm-4">
			<div class="col-sm-12 margin-form bold" style="height: 28px;line-height: 2;">Estado</div>
      <div class="col-sm-12 margin-form">
        <select name="estado" id="estado" style="width:100%;">
          <option value="1">Pendiente</option>
          <option value="2">Proceso</option>
          <option value="3">Finalizado</option>
        </select>
      </div>
			<div class="col-xs-12 margin-form bold" style="height: 28px;line-height: 2;">Usuario Asignado</div>
			<div class="col-xs-12 margin-form"><select name="usuarioAsignado" id="usuarioAsignado" style="width:100%;"></select></div>
      <div class="col-xs-10 margin-form bold" style="height: 28px;line-height: 2;">Detalles del Servicio</div>
      <div class="col-xs-2 margin-form text-right">
        <span class="glyphicon glyphicon-camera input_file"><input type="file" name="imgDetails[]" multiple = multiple id="imgDetails" accept="image/*"></span>
      </div>
      <div class="col-xs-12 margin-form">
        <textarea class="k-textbox" name="detalles" id="detalles"></textarea>
      </div>
      <div class="col-xs-10 margin-form bold" style="height: 28px;line-height: 2;">Observaciones del Servicio</div>
      <div class="col-xs-2 margin-form text-right">
        <span class="glyphicon glyphicon-camera input_file"><input type="file" name="imgObservaciones[]" multiple = multiple id="imgObservaciones" accept="image/*"></span>
      </div>
      <div class="col-xs-12 margin-form">
        <textarea class="k-textbox" name="observacion" id="observacion"></textarea>
      </div>
    </div>
    <div class="col-sm-8 margin-form clear">
      <div class="margin-form bold" style="height: 28px;line-height: 2;">Lista de Equipos</div>
      <div class="margin-form" id="tablaEquipos"></div>
    </div>
    <div class="col-sm-12 margin-form text-center">
      <button class="k-button">Guardar</button>
			<div style ="margin-left:20px; display:none;" class="k-button" id="btnCleanFields">Limpiar Campos</div>
			<div style ="margin-left:20px; display:none;" class="k-button" id="btnPrint">Imprimir Orden de Servicio</div>
    </div>
  </form>
</div>
<div class="div-border col-sm-12">
	<div class="clear">
		<form enctype="multipart/form-data" id="formBusqueda" method="post">
			<div class="col-xs-3">
				<div class="col-xs-12 margin-form bold">Fecha</div>
				<div class="col-xs-12 margin-form"><input type="date" name="fechaInicio" id="fechaBus"></div>
			</div>
			<div class="col-xs-3">
				<div class="col-xs-12 margin-form bold">Area</div>
				<div class="col-xs-12 margin-form"><select name="area" id="areaBus"></select></div>
			</div>
			<div class="col-xs-3">
				<div class="col-xs-12 margin-form bold">Solicitante</div>
				<div class="col-xs-12 margin-form"><select name="solicitante" id="solicitanteBus"></select></div>
			</div>
			<div class="col-xs-3">
				<div class="col-xs-12 margin-form bold">Estado</div>
				<div class="col-xs-12 margin-form">
					<select name="estado" id="estadoBus">
						<option value="1">Pendiente</option>
	          <option value="2">Proceso</option>
	          <option value="3">Finalizado</option>
					</select>
				</div>
			</div>
			<div class="col-xs-12 margin-form text-center"><button class="k-button">Buscar</button></div>
		</form>
	</div>
  <div id="tablaServicios"></div>
</div>
<div id="divModal" style="display:none;">
  <div id="modalServicio">
    <div class="margin-form text-center"><h3>Nuevo Servicio</h3></div>
    <form method="post" enctype="multipart/form-data" id="formTipoServicio">
        <div class="col-sm-6">
          <div class="bold margin-form">Clave</div>
          <div class="margin-form"><input type="text" name="clave" id="servClave" class="k-textbox"></div>
        </div>
        <div class="col-sm-6">
          <div class="bold margin-form">Nombre</div>
          <div class="margin-form"><input type="text" name="nombre" id="servNombre" class="k-textbox"></div>
        </div>
        <div class="col-sm-12">
          <div class="bold margin-form">Descripcion</div>
          <div class="margin-form"><input type="text" name="descripcion" id="servDescripcion" class="k-textbox"></div>
        </div>
        <div class="margin-form text-center"><button id="btnGuardarServicio" class="k-button">Guardar</button></div>
    </form>
  </div>
  <div id="modalSolicitante">
    <div class="clear">
      <div class="col-sm-12 margin-form text-center"><h3>Nuevo Solicitante</h3></div>
      <form method="post" enctype="multipart/form-data" id="formSolicitante">
        <div class="col-sm-12">
          <div class="bold margin-form">Nombre</div>
          <div class="margin-form"><input type="text" name="nombre" id="soliNombre" class="k-textbox"></div>
        </div>
        <div class="col-sm-6">
          <div class="bold margin-form">Dependencia</div>
          <div class="margin-form"><input type="text" name="dependencia" id="soliDependencia" class="k-textbox"></div>
        </div>
        <div class="col-sm-6">
          <div class="bold margin-form">Edificio</div>
          <div class="margin-form"><input type="text" name="edificio" id="soliEdificio" class="k-textbox"></div>
        </div>
        <div class="col-sm-6">
          <div class="bold margin-form">Cargo</div>
          <div class="margin-form"><input type="text" name="cargo" id="soliCargo" class="k-textbox"></div>
        </div>
        <div class="col-sm-6">
          <div class="bold margin-form">Area</div>
          <div class="margin-form"><input type="text" name="area" id="soliArea" class="k-textbox"></div>
        </div>
        <div class="col-sm-6">
          <div class="bold margin-form">Telefono</div>
          <div class="margin-form"><input type="text" name="telefono" id="soliTelefono" class="k-textbox"></div>
        </div>
        <div class="col-sm-6">
          <div class="bold margin-form">Extension</div>
          <div class="margin-form"><input type="text" name="extension" id="soliExtension" class="k-textbox"></div>
        </div>
        <div class="margin-form text-center"><button class="k-button">Guardar</button></div>
      </form>
    </div>
  </div>
</div>
