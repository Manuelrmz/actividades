<?php
	echo $this->addScript('view/radios/css/equipos.css','css');
	echo $this->addScript('core/kendo/js/kendo.web.min.js','js');
	echo $this->addScript('core/js/validaciones.js','js');
	echo $this->addScript('view/radios/js/equipos.js','js');
?>
<div class="col-sm-12 div-border">
	<div class="col-sm-12 margin-form"><h3>Administracion de equipos de Radios</h3></div>
	<div class="col-sm-3">
		<div class="col-sm-12 margin-form bold">RFSI</div>
		<div class="col-sm-12 margin-form"><input type="text" class="k-textbox" id="rfsi"></div>
	</div>
	<div class="col-sm-3">
		<div class="col-sm-12 margin-form bold">N/S Logico</div>
		<div class="col-sm-12 margin-form"><input type="text" class="k-textbox" id="nslogico"></div>
	</div>
	<div class="col-sm-3">
		<div class="col-sm-12 margin-form bold">Serie</div>
		<div class="col-sm-12 margin-form"><input type="text" class="k-textbox" id="serie"></div>
	</div>
	<div class="col-sm-3">
		<div class="col-sm-12 margin-form bold">Version</div>
		<div class="col-sm-12 margin-form"><input type="text" class="k-textbox" id="version"></div>
	</div>
	<div class="col-sm-3">
		<div class="col-sm-12 margin-form bold">Tipo</div>
		<div class="col-sm-12 margin-form"><select id="tipo" style="width:100%;"></select></div>
	</div>
	<div class="col-sm-3">
		<div class="col-sm-12 margin-form bold">Comentario 1</div>
		<div class="col-sm-12 margin-form"><input type="text" class="k-textbox" id="comentario1"></div>
	</div>
	<div class="col-sm-3">
		<div class="col-sm-12 margin-form bold">Comentario 2</div>
		<div class="col-sm-12 margin-form"><input type="text" class="k-textbox" id="comentario2"></div>
	</div>
	<div class="col-sm-3">
		<div class="col-sm-12 margin-form bold">Estado</div>
		<div class="col-sm-12 margin-form">
			<select id="estado" style="width:100%;">
				<option value="1">Activo</option>
				<option value="2">Inactivo</option>
			</select>
		</div>
	</div>
	<div class="col-sm-12 margin-form bold text-warning text-center" id="aviso"></div>
	<div class="col-sm-12 margin-form text-center">
		<button id="btnGuardar" class="k-button">Guardar</button>
		<button id="btnLimpiar" class="k-button" style="margin-left:15px;">Limpiar Campos</button>
	</div>
</div>
<div class="col-sm-12 div-border">
	<div id="tablaEquipos"></div>
</div>