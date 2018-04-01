<?php
	echo $this->addScript('view/principal/css/login.css','css');
	echo $this->addScript('view/principal/js/login.js','js');
?>
<form action="../usuarios/login" method="POST">
	<div id="formulario" class="clear">
		<div class="col-sm-12 text-center bold margin-form">Favor de Iniciar Sesion</div>
		<div class="col-sm-6 columnText bold text-center margin-form">Nombre de Usuario:</div>
		<div class="col-sm-6 margin-form text-center"><input type="text" name="user" class="k-textbox"></div>
		<div class="col-sm-6 columnText bold text-center margin-form">Contrase&ntilde;a:</div>
		<div class="col-sm-6 margin-form text-center"><input type="password" name="password" class="k-textbox"></div>
		<div class="col-sm-12 text-center text-warning bold margin-form"><?php echo $data; ?></div>
		<div class="col-sm-12 text-center margin-form"><button class="k-button" id="btnIniciar" name="btnIniciar">Iniciar Sesion</button></div>
	</div>
</form>