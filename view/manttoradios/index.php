<?php
	echo $this->addScript('view/manttoradios/css/index.css','css');
	echo $this->addScript('core/kendo/js/kendo.web.min.js','js');
	echo $this->addScript('core/js/validaciones.js','js');
	echo $this->addScript('view/manttoradios/js/index.js','js');
?>
<div class="div-border col-sm-12">
	<div class="col-sm-12 text-center margin-form"><h3>Reporte de Mantenimiento</h3></div>
	<div class="col-sm-12 text-center margin-form"><h4>Taller de Radio</h4></div>
	<div class="col-sm-6 margin-form clear">
		<div class="col-xs-12 text-center bold">Datos del que Reporta</div>
		<div class="col-xs-12 bold margin-form">Reporta:</div>
		<div class="col-xs-12 margin-form"><input type="text" id="reporta" class="k-textbox"></div>
		<div class="col-xs-12 bold margin-form">Motivo del Reporte:</div>
		<div class="col-xs-12 margin-form"><textarea id="motivo" class="k-textbox"></textarea></div>
		<div class="col-xs-4 bold margin-form">Vehiculo:</div>
		<div class="col-xs-4 bold margin-form">Placas:</div>
		<div class="col-xs-4 bold margin-form">Unidad:</div>
		<div class="col-xs-4 margin-form"><input type="text" class="k-textbox" id="vehiculo"></div>
		<div class="col-xs-4 margin-form"><input type="text" class="k-textbox" id="placas"></div>
		<div class="col-xs-4 margin-form"><input type="text" class="k-textbox" id="unidad"></div>
		<div class="col-xs-6 bold margin-form">Dependencia:</div>
		<div class="col-xs-6 bold margin-form">Asignacion:</div>
		<div class="col-xs-6 margin-form"><select id="dependencia" style="width:100%;"></select></div>
		<div class="col-xs-6 margin-form"><select id="asignacion" style="width:100%;"></select></div>
	</div>
	<div class="col-sm-6 margin-form clear">
		<div class="col-xs-12 text-center bold">Datos del Mantenimiento</div>
		<div class="col-xs-4 bold margin-form">RFSI:</div>
		<div class="col-xs-4 bold margin-form">Serie:</div>
		<div class="col-xs-4 bold margin-form">N/S Logico:</div>
		<div class="col-xs-4 margin-form"><select id="rfsi" style="width:100%;"></select></div>
		<div class="col-xs-4 margin-form"><input type="text" class="k-textbox" id="serie" readonly></div>
		<div class="col-xs-4 margin-form"><input type="text" class="k-textbox" id="nslog" readonly></div>
		<div class="col-xs-4 bold margin-form" style="line-height:2;">Tipo de Terminal:</div>
		<div class="col-xs-2 margin-form text-center" id="idterminal" style="line-height:2;"></div>
		<div class="col-xs-6 margin-form"><input type="text" class="k-textbox" id="terminal" readonly></div>
		<div class="col-xs-12 bold margin-form">Comentarios del Terminal</div>
		<div class="col-xs-12 margin-form"><textarea id="comenTerminal" class="k-textbox" readonly></textarea></div>
		<div class="col-xs-6 bold margin-form">Tipo de Mantenimiento</div>
		<div class="col-xs-6 bold margin-form">Diagnostico</div>
		<div class="col-xs-6 margin-form"><select id="mantenimiento" style="width:100%;"></select></div>
		<div class="col-xs-6 margin-form"><select id="diagnostico" style="width:100%;"></select></div>
		<div class="col-xs-12 bold margin-form">Comentarios de la Intervencion</div>
		<div class="col-xs-12 margin-form"><textarea id="comenIntervencion" class="k-textbox"></textarea></div>
		<div class="col-xs-12 bold margin-form">Observaciones</div>
		<div class="col-xs-12 margin-form"><textarea id="observaciones" class="k-textbox"></textarea></div>
	</div>
	<div class="col-sm-12 text-center margin-form bold text-warning" id="aviso"></div>
	<div class="col-sm-12 text-center margin-form">
		<button class="k-button" id="btnGuardar">Guardar</button>
		<button class="k-button" id="btnLimpiar" style="margin-left:30px;">Limpiar Campos</button>
		<button class="k-button" id="btnImprimir" style="margin-left:30px;display:none;">Imprimir Boleta</button>
	</div>
</div>
<div class="col-sm-12 div-border">
	<div class="clear">
		<div class="col-xs-6 margin-form text-center bold">Fecha de Inicio</div>
		<div class="col-xs-6 margin-form text-center bold">Fecha Final</div>
		<div class="col-xs-6 margin-form text-center"><input type="date" id="fechaIni"></div>
		<div class="col-xs-6 margin-form text-center"><input type="date" id="fechaFin"></div>
		<div class="col-xs-12 margin-form text-center text-warning bold" id="avisoTabla"></div>
		<div class="col-xs-12 margin-form text-center"><button id="btnBuscar" class="k-button">Buscar</button></div>
	</div>
	<div id="tablaMantenimientos"></div>
</div>