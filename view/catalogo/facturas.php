<?php
	echo $this->addScript('view/catalogo/css/facturas.css','css');
	echo $this->addScript('core/kendo/js/kendo.web.min.js','js');
	echo $this->addScript('core/js/validaciones.js','js');
  echo $this->addScript('core/css/alertbox.css','css');
	echo $this->addScript('view/catalogo/js/facturas.js','js');
?>
<div class="div-border col-sm-6">
  <div class="clear">
    <div class="col-sm-12 margin-form text-center"><h3>Categorias de Equipos en Inventario</h3></div>
  </div>
  <div id="gridCatalogo"></div>
</div>
<div class="div-border col-sm-6">
  <div class="clear">
    <div class="col-sm-12 margin-form text-center"><h3>Ejercicios y Programas</h3></div>
  </div>
  <div id="gridEjercicios"></div>
</div>
