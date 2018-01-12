<?php
	echo $this->addScript('view/ejercicio/index.css','css');
	echo $this->addScript('core/kendo/js/kendo.web.min.js','js');
	echo $this->addScript('core/js/validaciones.js','js');
  echo $this->addScript('core/css/alertbox.css','css');
	echo $this->addScript('view/ejercicio/index.js','js');
?>
<div class="div-border col-sm-12">
  <div class="col-xs-12 margin-form text-center"><h3>Ejercicios</h3></div>
  <div class="col-xs-12 margin-form bold">A&ntilde;o</div>
  <div class="col-xs-12 margin-form"><select name="anio" id="anio" style="width:100%;"></select></div>
  <div id="divProgramas" style="display:none;">
    <div class="col-xs-12 margin-form bold">Programas</div>
    <div class="col-xs-12 margin-form"><div id="gridProgramas"></div></div>
    <div class="col-xs-12 margin-form text-center">
      <button class="k-button" id="btnGuardar">Guardar</button>
      <div class="k-button" id="btnCleanFields">Limpiar Campos</div>
    </div>
  </div>
</div>
