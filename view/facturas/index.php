<?php
	echo $this->addScript('view/facturas/index.css','css');
	echo $this->addScript('core/kendo/js/kendo.web.min.js','js');
	echo $this->addScript('core/js/validaciones.js','js');
  echo $this->addScript('core/css/alertbox.css','css');
	echo $this->addScript('view/facturas/index.js','js');
?>
<div class="div-border col-sm-12">
  <div class="col-sm-12 text-center margin-form"><h3>Facturas</h3></div>
  <div class="col-sm-12 margin-form bold" id="titleForm">Nueva Factura</div>
  <form enctype="multipart/form-data" id="formFactura" method="post">
    <div class="col-sm-3">
      <div class="col-sm-12 bold margin-form">Fecha Factura</div>
      <div class="col-sm-12 margin-form"><input type="date" name="fecha" id="fecha"></div>
    </div>
    <div class="col-sm-3">
      <div class="col-sm-12 bold margin-form">No. Factura</div>
      <div class="col-sm-12 margin-form"><input type="text" name="noFactura" id="noFactura" class="k-textbox"></div>
    </div>
    <div class="col-sm-3">
      <div class="col-sm-12 bold margin-form">Condicion de Pago</div>
      <div class="col-sm-12 margin-form"><input type="text" name="condiPago" id="condiPago" class="k-textbox"></div>
    </div>
    <div class="col-sm-3">
      <div class="col-sm-12 bold margin-form">Fecha de Entrega</div>
      <div class="col-sm-12 margin-form"><input type="date" name="fechaEntrega" id="fechaEntrega"></div>
    </div>
    <div class="col-sm-4">
      <div class="col-sm-12 bold margin-form">RFC Proveedor</div>
      <div class="col-sm-12 margin-form"><select id="rfc" name="rfc" style="width:100%;"></select></div>
    </div>
    <div class="col-sm-4">
      <div class="col-sm-12 bold margin-form">Nombre Empresa</div>
      <div class="col-sm-12 margin-form" id="nombreEmpresa" style="height:26px; line-height:2;"></div>
    </div>
    <div class="col-sm-4">
      <div class="col-sm-12 bold margin-form">Direccion</div>
      <div class="col-sm-12 margin-form" id="direccionEmpresa" style="height:26px; line-height:2;"></div>
    </div>
    <div class="col-sm-4">
      <div class="col-sm-12 bold margin-form">Vendedor</div>
      <div class="col-sm-12 margin-form"><input type="text" name="vendedor" id="vendedor" class="k-textbox"></div>
    </div>
    <div class="col-sm-4">
      <div class="col-sm-12 bold margin-form">Comprador</div>
      <div class="col-sm-12 margin-form"><input type="text" name="comprador" id="comprador" class="k-textbox"></div>
    </div>
    <div class="col-sm-4">
      <div class="col-sm-12 bold margin-form">Responsable</div>
      <div class="col-sm-12 margin-form"><input type="text" name="responsable" id="responsable" class="k-textbox"></div>
    </div>
    <div class="col-sm-4">
      <div class="col-sm-12 bold margin-form">Ejercicio</div>
      <div class="col-sm-12 margin-form"><select name="ejercicio" id="ejercicio" style="width:100%;"></select></div>
    </div>
    <div class="col-sm-4">
      <div class="col-sm-12 bold margin-form">Programa</div>
      <div class="col-sm-12 margin-form"><select name="programa" id="programa" style="width:100%;"></select></div>
    </div>
    <div class="col-sm-4">
      <div class="col-sm-12 bold margin-form">Area</div>
      <div class="col-sm-12 margin-form"><select name="area" id="area" style="width:100%;"></select></div>
    </div>
		<div class="col-sm-6">
			<div class="col-sm-12 margin-form bold">PDF Factura</div>
			<div class="col-sm-8 margin-form"><input type="file" name="facturapdf" id="facturapdf" accept="application/pdf"></div>
			<div class="col-sm-4 margin-form" id="fileFactura"></div>
		</div>
		<div class="col-sm-6">
			<div class="col-sm-12 margin-form bold">PDF Orden de Compra</div>
			<div class="col-sm-8 margin-form"><input type="file" name="ordenpdf" id="ordenpdf" accept="application/pdf"></div>
			<div class="col-sm-4 margin-form" id="fileOrden"></div>
		</div>
		<div class="col-sm-12">
			<div class="col-sm-12 margin-form bold">Detalles de la Factura (Equipos)</div>
	    <div class="col-sm-12 margin-form">
	      <div id="gridEquipos"></div>
	    </div>
		</div>
    <div class="col-sm-12 text-center margin-form"><button class="k-button">Guardar</button><div class="k-button" id="btnCleanFields" style="margin-left:15px;">Limpiar Campos</div></div>
  </form>
</div>
<div class="div-border col-sm-12">
	<div class="clear">
		<form enctype="multipart/form-data" id="formBusqueda" method="post">
			<div class="col-sm-3">
				<div class="col-sm-12 margin-form bold text-center">Fecha</div>
				<div class="col-sm-12 margin-form"><input type="date" name="fecha"></div>
			</div>
			<div class="col-sm-3">
				<div class="col-sm-12 margin-form bold text-center">Numero de Factura</div>
				<div class="col-sm-12 margin-form text-center"><input type="text" name="noFactura" id="noFacturaBus" class="k-textbox"></div>
			</div>
			<div class="col-sm-3">
				<div class="col-sm-12 margin-form bold text-center">RFC Proveedor</div>
				<div class="col-sm-12 margin-form"><select name="rfc" id="rfcBus" style="width:100%;"></select></div>
			</div>
			<div class="col-sm-3">
				<div class="col-sm-12 margin-form bold text-center">Ejercicio</div>
				<div class="col-sm-12 margin-form"><select name="ejercicio" id="ejercicioBus" style="width:100%;"></select></div>
			</div>
			<div class="col-sm-12 margin-form text-center"><button class="k-button">Buscar</button></div>
		</form>
	</div>
  <div id="gridFacturas"></div>
</div>
<div id="modalProveedor" style="display:none;">
  <form enctype="multipart/form-data" id="formProveedor" method="post">
    <div class="col-xs-12 text-center bold margin-form">Nuevo Proveedor</div>
    <div class="col-xs-12 bold margin-form">RFC</div>
    <div class="col-xs-12 margin-form"><input type="text" name="rfc" id="rfcProveedor" class="k-textbox"></div>
    <div class="col-xs-12 bold margin-form">Nombre de la Empresa</div>
    <div class="col-xs-12 margin-form"><input type="text" name="nombreEmpresa" id="nombreProveedor" class="k-textbox"></div>
    <div class="col-xs-12 bold margin-form">Direccion de la Empresa</div>
    <div class="col-xs-12 margin-form"><input type="text" name="direccion" id="direccionProveedor" class="k-textbox"></div>
    <div class="col-xs-12 bold margin-form">Codigo Postal</div>
    <div class="col-xs-12 margin-form"><input type="text" name="cp" id="cpProveedor" class="k-textbox"></div>
    <div class="col-xs-12 margin-form text-center"><button class="k-button">Guardar</button></div>
  </form>
</div>
