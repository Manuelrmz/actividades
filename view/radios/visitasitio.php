<?php
	echo $this->addScript('view/radios/css/visitasitio.css','css');
	echo $this->addScript('core/kendo/js/kendo.web.min.js','js');
	echo $this->addScript('core/js/validaciones.js','js');
	echo $this->addScript('view/radios/js/visitasitio.js','js');
?>
<div class="col-sm-12 div-border">
	<div class="col-sm-12 text-center margin-form"><h3>Reporte de Visita a Sitios C4 Radios</h3></div>
	<form id="formVisita" method="post" enctype="multipart/form-data">
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Reporta</div>
			<div class="col-sm-12 margin-form"><input type="text" id="reporta" name="reporta" class="k-textbox"></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Vehiculo</div>
			<div class="col-sm-12 margin-form"><input type="text" id="vehiculo" name="vehiculo" class="k-textbox"></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Placas</div>
			<div class="col-sm-12 margin-form"><input type="text" id="placas" name="placas" class="k-textbox"></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Odometro</div>
			<div class="col-sm-12 margin-form"><input type="text" id="odometro" name="odometro" class="k-textbox"></div>
		</div>
		<div class="col-sm-7">
			<div class="col-sm-12 bold margin-form">Motivo</div>
			<div class="col-sm-12 margin-form"><input type="text" id="motivo" name="motivo" class="k-textbox"></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Fecha Visita</div>
			<div class="col-sm-12 margin-form"><input type="date" id="fechaVisita" name="fechaVisita" class="k-textbox"></div>
		</div>
		<div class="col-sm-2">
			<div class="col-sm-12 bold margin-form text-center">Imagenes</div>
			<div class="col-sm-12 margin-form glyphicon glyphicon-camera" id="input_file"><input type="file" name="files[]" multiple = multiple id="files" accept="image/*"></div>
		</div>
		<div class="col-sm-6"> 
			<div class="col-sm-12 bold margin-form">Sitio</div>
			<div class="col-sm-12 margin-form text-center"><select id="sitios" multiple=multiple style="width:100%;"></select></div>
		</div>
		<div class="col-sm-6">
			<div class="col-sm-12 bold margin-form">Personal que Participa en la visita</div>
			<div class="col-sm-12 margin-form text-center"><select id="personal" multiple=multiple style="width:100%;"></select></div>
		</div>
		<div class="col-sm-12">
			<div class="col-sm-12 bold margin-form">Comentarios</div>
			<div class="col-sm-12 margin-form"><textarea id="comentarios" name="comentarios" class="k-textbox"></textarea></div>
			<div class="col-sm-12 margin-form bold text-center text-warning" id="aviso"></div>
			<div class="col-sm-12 margin-form text-center">
				<button id="btnGuardar" class="k-button">Guardar</button>
				<div id="btnLimpiar" class="k-button" style="margin-left:15px;">Limpiar Campos</div>
				<div id="btnImprimir" class="k-button" style="margin-left:15px; display:none;">Imprimir Reporte</div>
			</div>
		</div>
	</form>
</div>
<div class="col-sm-12 div-border">
	<div class="clear">
		<div class="col-xs-12 bold text-center margin-form">Parametros de Busqueda</div>
		<div class="col-xs-3 bold text-center margin-form">Fecha de Inicio:&nbsp;<input type="date" id="fechaIni"></div>
		<div class="col-xs-3 bold text-center margin-form">Fecha Final:&nbsp;<input type="date" id="fechaFin"></div>
		<div class="col-xs-3 margin-form"><button id="btnBuscar" class="k-button">Buscar</button></div>
		<div class="col-xs-12 bold text-center text-warning" id="avisoTabla"></div>
		
	</div>
	<div id="tablavisitas"></div>
</div>