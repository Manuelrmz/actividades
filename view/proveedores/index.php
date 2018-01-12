<?php
	echo $this->addScript('view/proveedores/index.css','css');
	echo $this->addScript('core/kendo/js/kendo.web.min.js','js');
	echo $this->addScript('core/js/validaciones.js','js');
	echo $this->addScript('core/css/alertbox.css','css');
	echo $this->addScript('view/proveedores/index.js','js');
?>
<div class="col-xs-12 div-border">
	<div class="col-xs-12 margin-form text-center"><h3>Proveedores</h3></div>
	<div class="col-xs-12 margin-form bold" id="titleForm">Nuevo Proveedor</div>
	<form id="formProveedores" method="post" enctype="multipart/form-data">
		<div class="col-sm-6">
			<div class="col-xs-12 margin-form bold">RFC</div>
			<div class="col-xs-12 margin-form"><input type="text" id="rfc" name="rfc" class="k-textbox" placeholder="RFC"></div>
			<div class="col-xs-12 margin-form bold">Nombre de la Empresa</div>
			<div class="col-xs-12 margin-form"><input type="text" id="nombreEmpresa" name="nombreEmpresa" class="k-textbox" placeholder="Nombre de la Empresa"></div>
			<div class="col-xs-12 margin-form bold">Status</div>
			<div class="col-xs-12 margin-form">
				<select name="activo" id="activo" style="width:100%;">
					<option value="0">Inactivo</option>
					<option value="1">Activo</option>
				</select>
			</div>
			<div class="col-xs-12 margin-form bold">Direccion</div>
			<div class="col-xs-12 margin-form"><input type="text" id="direccion" name="direccion" class="k-textbox" placeholder="Direccion"></div>
			<div class="col-xs-12 margin-form bold">Codigo Postal</div>
			<div class="col-xs-12 margin-form"><input type="text" id="cp" name="cp" class="k-textbox" placeholder="Codigo Postal"></div>
		</div>
		<div class="col-sm-6">
			<div class="col-xs-12 margin-form bold">Telefonos</div>
			<div class="col-xs-12 margin-form"><div id="gridPhones"></div></div>
		</div>
		<div class="col-sm-12 text-center margin-form">
			<button class="k-button">Guardar</button>
			<div class="k-button" id="btnCleanFields" style="margin-left:30px;">Limpiar Campos</div>
		</div>
	</form>
</div>
<div class="col-xs-12 div-border">
	<div id="gridProveedores"></div>
</div>