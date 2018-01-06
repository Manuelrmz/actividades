<?php
	//echo $this->addScript('view/radios/css/reporteador.css','css');
	echo $this->addScript('core/kendo/js/kendo.web.min.js','js');
	echo $this->addScript('core/js/validaciones.js','js');
	echo $this->addScript('view/radios/js/reporteador.js','js');
?>
<div class="col-sm-12 div-border">
	<div class="col-sm-12 text-center margin-form"><h3>Generador de Reportes</h3></div>
	<form id="formReporteador" method="post" enctype="multipart/form-data">
		<div class="col-sm-4">
			<div class="col-sm-12 margin-form bold text-center">Tipo de Reporte</div>
			<div class="col-sm-12 margin-form text-center">
				<select name="tipoReporte" id="tipoReporte" style="width:100%;">
					<option value="1">Cantidades de servicio por categorias</option>
					<option value="2">Intervenciones por Sitio</option>
				</select>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="col-sm-12 margin-form bold text-center">Fecha de Inicio</div>
			<div class="col-sm-12 margin-form text-center"><input type="date" name="fechaInicio" id="fechaInicio"></div>
		</div>
		<div class="col-sm-4">
			<div class="col-sm-12 margin-form bold text-center">Fecha Final</div>
			<div class="col-sm-12 margin-form text-center"><input type="date" name="fechaFin" id="fechaFin"></div>
		</div>
		<div class="col-sm-12 text-center margin-form text-warning bold" id="aviso"></div>
		<div class="col-sm-12 text-center margin-form"><button class="k-button" id="btnBuscar">Buscar</button></div> 
	</form>
</div>