<?php
	//echo $this->addScript('view/catalogo/css/index.css','css');
	echo $this->addScript('core/kendo/js/kendo.web.min.js','js');
	echo $this->addScript('core/js/validaciones.js','js');
	echo $this->addScript('core/css/alertbox.css','css');
	echo $this->addScript('view/catalogo/js/index.js','js');
?>
<div class="div-border col-xs-6">
  <div class="clear">
    <div class="col-xs-12 margin-form text-center"><h3>Areas</h3></div>
  </div>
  <div id="gridAreas"></div>
</div>
<div class="div-border col-xs-6">
  <div class="clear">
    <div class="col-xs-12 margin-form text-center"><h3>Cargos Usuarios</h3></div>
  </div>
  <div id="gridCargos"></div>
</div>