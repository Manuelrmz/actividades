<?php
	echo $this->addScript('view/catalogo/css/radios.css','css');
	echo $this->addScript('core/kendo/js/kendo.web.min.js','js');
	echo $this->addScript('core/js/validaciones.js','js');
	echo $this->addScript('view/catalogo/js/radios.js','js');
?>
<div class="col-sm-12 div-border">
	<div class="col-sm-12 bold margin-form">Catalogos</div>
	<div class="col-sm-12" id="pestanias">
		<div class="col-sm-3 bold text-center" data-catalog="1"><span class="glyphicon glyphicon-list"></span>Asignaciones</div>
		<div class="col-sm-3 bold text-center" data-catalog="2"><span class="glyphicon glyphicon-list"></span>Dependencias</div>
		<div class="col-sm-3 bold text-center" data-catalog="3"><span class="glyphicon glyphicon-list"></span>Diagnosticos</div>
		<div class="col-sm-3 bold text-center" data-catalog="4"><span class="glyphicon glyphicon-list"></span>Mantenimientos</div>
	</div>
	<div class="col-sm-12" id="optionsPestanias">
		<div class="col-sm-12" id="divForm" style="display:none;">
			<div class="col-sm-4">
				<div class="col-sm-3 bold margin-form" style="line-height:2;">Nombre</div>
				<div class="col-sm-7 margin-form">
					<input type="text" class="k-textbox" id="nombre">
				</div>
			</div>
			<div class="col-sm-4">
				<div class="col-sm-3 bold margin-form" style="line-height:2;">Activo</div>
				<div class="col-sm-7 margin-form">
					<select id="activo" style="width:90%;">
						<option value="1">Activo</option>
						<option value="2">Inactivo</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div id="actions" style="display:none;">
		<div class="col-sm-12 text-center margin-form text-warning" id="aviso"></div>
		<div class="col-sm-12 margin-form text-center">
			<button class="k-button" id="btnGuardar">Guardar</button>
			<button class="k-button" id="btnLimpiar" style="margin-left:30px;">Limpiar Campos</button>
		</div>
	</div>
</div>
<div class="col-sm-12 div-border">
	<div id="tablaCatalogos"></div>
</div>