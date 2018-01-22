<?php
	echo $this->addScript('view/recibos/index.css','css');
	echo $this->addScript('core/kendo/js/kendo.web.min.js','js');
	echo $this->addScript('core/js/validaciones.js','js');
	echo $this->addScript('core/css/alertbox.css','css');
	echo $this->addScript('view/recibos/index.js','js');
?>
<div class="col-sm-12 div-border">
	<div class="col-sm-12 text-center margin-form"><h3>Modulo de Recibos para Resguardos</h3></div>
	<form id="formRecibos" method="post" enctype="multipart/form-data">
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Resguardo</div>
			<div class="col-sm-12 margin-form"><select id="resguardo" style="width:100%;"></select></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Personal que Recibe</div>
			<div class="col-sm-12 margin-form"><select id="personal" style="width:100%;"></select></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Tipo de Recibo</div>
			<div class="col-sm-12 margin-form">
				<select id="tipo" style="width:100%;">
					<option value="0">Temporal</option>
					<option value="1">Normal</option>
				</select>
			</div>
		</div>
		<div class="col-sm-3" style="height: 49px">
			<div class="col-sm-12 bold margin-form">Fecha de entrega</div>
			<div class="col-sm-12 margin-form"><input type="date" id="fechaEntrega" name="fechaEntrega"></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Nombre</div>
			<div class="col-sm-12 margin-form"><input type="text" class="k-textbox" id="nombre" name="nombre" placeholder="Nombre del que recibe"></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Dependencia</div>
			<div class="col-sm-12 margin-form"><input type="text" class="k-textbox" id="dependencia" name="dependencia" placeholder="Dependencia del que recibe"></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Departamento</div>
			<div class="col-sm-12 margin-form"><input type="text" class="k-textbox" id="departamento" name="departamento" placeholder="Departamento del eque recibe"></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Cargo</div>
			<div class="col-sm-12 margin-form"><input type="text" class="k-textbox" id="cargo" name="cargo" placeholder="Cargo del que recibe"></div>
		</div>
		<div class="col-sm-12">
			<div class="col-sm-12 bold margin-form">Nota</div>
			<div class="col-sm-12 margin-form"><textarea name="nota" id="nota" class="k-textbox"></textarea></div>
		</div>
		<div class="col-xs-12 margin-form" id="divEquipos" style="display:none;">
			<div class="col-xs-12 bold margin-form">Equipos</div>
			<div class="col-xs-10 margin-form equipsFields" style="display:none;"><select id="equipos" style="width: 100%;"></select></div>
			<div class="col-xs-2 equipsFields" style="display:none;"><div class="k-button" id="btnAddEquip">Agregar</div></div>
			<div class="col-xs-12">
				<div id="gridEquipos"></div>
			</div>
		</div>
		<div class="col-sm-12 text-center margin-form">
			<button class="k-button" id="btnGuardar">Guardar</button>
			<div style="margin-left:30px;" class="k-button" id="btnCleanFields">Limpiar Campos</div>
			<div style="margin-left:30px; display:none;" class="k-button" id="btnFinish">Finalizar Recibo</div>
		</div>
	</form>
</div>
<div class="col-sm-12 div-border">
	<div id="gridRecibos"></div>
</div>