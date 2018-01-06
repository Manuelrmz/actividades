<?php
	echo $this->addScript('view/usuarios/css/index.css','css');
	echo $this->addScript('core/kendo/js/kendo.web.min.js','js');
	echo $this->addScript('core/js/validaciones.js','js');
	echo $this->addScript('view/usuarios/js/index.js','js');
?>
<div class="col-sm-12 div-border">
	<form id="formUsers" method="post" enctype="multipart/form-data">
		<div class="col-sm-12 text-center margin-form"><h3>Administrar Usuarios</h3></div>
		<div class="col-sm-12 margin-form"><h3>Informacion del Usuario</h3></div>
		<div class="col-sm-4">
			<div class="col-sm-12 margin-form bold">Usuario</div>
			<div class="col-sm-12 margin-form"><input type="text" class="k-textbox" id="usuario" name="usuario" placeholder="Usuario inicio sesion"></div>
		</div>
		<div class="col-sm-4">
			<div class="col-sm-12 margin-form bold">Nombres</div>
			<div class="col-sm-12 margin-form"><input type="text" class="k-textbox" id="nombres" name="nombres" placeholder="Nombres del usuario"></div>
		</div>
		<div class="col-sm-4">
			<div class="col-sm-12 margin-form bold">Apellidos</div>
			<div class="col-sm-12 margin-form"><input type="text" class="k-textbox" id="apellidos" name="apellidos" placeholder="Apellidos del usuario"></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 margin-form bold">Contrase&ntilde;a</div>
			<div class="col-sm-12 margin-form"><input type="password" name="pass1" id="pass1" class="k-textbox" placeholder="Contrase&ntilde;a inicio sesion"></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 margin-form bold">Confirmar Contrase&ntilde;a</div>
			<div class="col-sm-12 margin-form"><input type="password" name="pass2" id="pass2" class="k-textbox" placeholder="Confirme Contrase&ntilde;a"></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 margin-form bold">Area</div>
			<div class="col-sm-12 margin-form"><select name="area" id="area" style="width:95%;"></select></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 margin-form bold">Cargo</div>
			<div class="col-sm-12 margin-form"><select name="cargo" id="cargo" style="width:95%;"></select></div>
		</div>
		<div class="col-sm-12 margin-form"><h3>Permisos</h3></div>
		<div class="col-2 margin-form"><label for="capof" class="bold">Captura de Oficios</label>&nbsp;<input type="checkbox" name="capof" id="capof"></div>
		<div class="col-2 margin-form"><label for="busof" class="bold">Busqueda de Oficios</label>&nbsp;<input type="checkbox" name="busof" id="busof"></div>
		<div class="col-2 margin-form"><label for="segaof" class="bold">Seguimiento Por Area</label>&nbsp;<input type="checkbox" name="segaof" id="segaof"></div>
		<div class="col-2 margin-form"><label for="seggof" class="bold">Seguimiento General</label>&nbsp;<input type="checkbox" name="seggof" id="seggof"></div>
		<div class="col-2 margin-form"><label for="admuser" class="bold">Administrar Usuarios</label>&nbsp;<input type="checkbox" name="admuser" id="admuser"></div>
		<div class="col-2 margin-form"><label for="historico" class="bold">Historico</label>&nbsp;<input type="checkbox" name="historico" id="historico"></div>
		<div class="col-2 margin-form"><label for="acceso" class="bold">Acceso</label>&nbsp;<input type="checkbox" name="acceso" id="acceso"></div>
		<div class="col-12 margin-form text-center bold text-warning" id="aviso"></div>
		<div class="col-12 margin-form text-center"><button class="k-button" id="btnGuardar">Guardar Usuario</button><div style="margin-left:25px;" class="k-button" id="btnLimpiar">Limpiar Campos</div></div>
	</form>
</div>
<div class="col-sm-12 div-border clear">
	<div id="tablaUsuarios"></div>
</div>