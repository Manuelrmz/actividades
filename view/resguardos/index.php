<?php
	echo $this->addScript('view/resguardos/index.css','css');
	echo $this->addScript('core/kendo/js/kendo.web.min.js','js');
	echo $this->addScript('core/js/validaciones.js','js');
	echo $this->addScript('core/css/alertbox.css','css');
	echo $this->addScript('view/resguardos/index.js','js');
?>
<div class="col-sm-12 div-border">
	<div class="col-sm-12 text-center margin-form"><h3>Modulo de Resguardos</h3></div>
	<form id="formResguardo" method="post" enctype="multipart/form-data">
		<div class="col-sm-6">
			<div class="col-sm-12 bold margin-form">Nombre Solicitante</div>
			<div class="col-sm-12 margin-form"><input type="text" class="k-textbox" id="nombre" name="nombre" placeholder="Nombre del que recibe"></div>
		</div>
		<div class="col-sm-6">
			<div class="col-sm-12 bold margin-form">Dependencia Solicitante</div>
			<div class="col-sm-12 margin-form"><input type="text" class="k-textbox" id="dependencia" name="dependencia" placeholder="Dependencia del que recibe"></div>
		</div>
		<div class="col-sm-4">
			<div class="col-sm-12 bold margin-form">Departamento Solicitante</div>
			<div class="col-sm-12 margin-form"><input type="text" class="k-textbox" id="departamento" name="departamento" placeholder="Departamento del eque recibe"></div>
		</div>
		<div class="col-sm-4">
			<div class="col-sm-12 bold margin-form">Cargo Solicitante</div>
			<div class="col-sm-12 margin-form"><input type="text" class="k-textbox" id="cargo" name="cargo" placeholder="Cargo del que recibe"></div>
		</div>
		<div class="col-sm-4">
			<div class="col-sm-12 bold margin-form">Personal que entrega</div>
			<div class="col-sm-12 margin-form"><select id="personal" style="width:100%;"></select></div>
		</div>
		<div class="col-sm-12">
			<div class="col-sm-12 bold margin-form">Nota</div>
			<div class="col-sm-12 margin-form"><textarea name="nota" id="nota" class="k-textbox"></textarea></div>
		</div>
		<div class="col-sm-12 margin-form">
			<div class="col-xs-12 bold margin-form">Equipos</div>
			<div class="col-xs-10 margin-form equipsFields"><select id="equipos" style="width: 100%;"></select></div>
			<div class="col-xs-2 equipsFields"><div class="k-button" id="btnAddEquip">Agregar</div></div>
			<div class="col-xs-12">
				<div id="gridEquipos"></div>
			</div>
			
		</div>
		<div class="col-sm-12 text-center margin-form">
			<button class="k-button" id="btnGuardar">Guardar</button>
			<div style="margin-left:30px;" class="k-button" id="btnCleanFields">Limpiar Campos</div>
		</div>
	</form>
</div>
<div class="col-sm-12 div-border">
	<div id="gridResguardos"></div>
</div>