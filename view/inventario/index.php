<?php
	echo $this->addScript('view/inventario/index.css','css');
	echo $this->addScript('core/kendo/js/kendo.web.min.js','js');
	echo $this->addScript('core/js/validaciones.js','js');
	echo $this->addScript('core/css/alertbox.css','css');
	echo $this->addScript('view/inventario/index.js','js');
?>
<div class="col-sm-12 div-border">
	<div class="col-sm-12 text-center margin-form"><h3>Modulo de Inventario</h3></div>
	<div class="col-sm-12 bold margin-form" id="titleForm">Nuevo Equipo</div>
	<form enctype="multipart/form-data" id="formInventario" method="post">
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">No. Factura</div>
			<div class="col-sm-12 margin-form"><select id="noFactura" style="width:100%;"></select></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Estado del Equipo</div>
			<div class="col-sm-12 margin-form"><select name="status" id="status" style="width:100%;"></select></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Area</div>
			<div class="col-sm-12 margin-form"><select name="area" id="area" style="width:100%;"></select></div>
		</div>
		<div class="col-sm-3" style="height:49px;">
			<div class="col-sm-12 bold margin-form">Cantidad</div>
			<div class="col-sm-12 margin-form"><input type="number" id="cantidad" name="cantidad" placeholder="###" class="k-textbox" ></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Categoria</div>
			<div class="col-sm-12 margin-form"><select name="categoria" id="categoria" style="width:100%;"></select></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Tipo de Equipo</div>
			<div class="col-sm-12 margin-form"><select name="tipoEquipo" id="tipoEquipo" style="width:100%;"></select></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Marca</div>
			<div class="col-sm-12 margin-form"><select name="marca" id="marca" style="width:100%;"></select></div>
		</div>
		<div class="col-sm-3">
			<div class="col-sm-12 bold margin-form">Unidad de Medida</div>
			<div class="col-sm-12 margin-form"><select name="um" id="um" style="width:100%;"></select></div>
		</div>
		<div class="col-sm-4" style="height:49px;">
			<div class="col-sm-12 bold margin-form">Codigo</div>
			<div class="col-sm-12 margin-form"><input type="text" id="codigo" name="codigo" class="k-textbox" placeholder="Codigo"></div>
		</div>
		<div class="col-sm-4">
			<div class="col-sm-12 bold margin-form">Modelo</div>
			<div class="col-sm-12 margin-form"><input type="text" id="modelo" name="modelo" class="k-textbox" placeholder="Modelo"></div>
		</div>
		<div class="col-sm-4">
			<div class="col-sm-12 bold margin-form">No. Serie</div>
			<div class="col-sm-12 margin-form"><input type="text" id="noSerie" name="noSerie" class="k-textbox" placeholder="Numero de Serie"></div>
		</div>
		<div class="col-sm-12">
			<div class="col-sm-12 bold margin-form">Descripcion</div>
			<div class="col-sm-12 margin-form"><input type="text" id="descripcion" name="descripcion" class="k-textbox" placeholder="Descripcion"></div>
		</div>
		<div class="col-sm-12 text-center margin-form">
			<button class="k-button">Guardar</button>
			<div class="k-button" id="clearFieldsInventario" style="margin-left:30px;">Limpiar Campos</div>
			<div class="k-button" id="btnFacturaDetail" style="margin-left:30px; display:none;">Ver Factura</div>
			<div class="k-button" id="btnResguardoDetail" style="margin-left:30px; display:none;">Ver Resguardo</div>
		</div>
	</form>
</div>
<div class="col-sm-12 div-border">
	<div id="gridInventario"></div>
</div>
<div id="modalDetails" style="display:none;">
	<div id="divFactura" style="display:none;">
		<div class="col-xs-12 text-center margin-form"><h3>Datos de la Factura</h3></div>
		<div class="col-xs-6 bold margin-form">Nombre de la Empresa</div>
		<div class="col-xs-6 bold margin-form">RFC</div>
		<div class="col-xs-6 margin-form" id="nombreEmpresa"></div>
		<div class="col-xs-6 margin-form" id="rfcEmpresa"></div>
		<div class="col-xs-9 bold margin-form">Direccion de la Empresa</div>
		<div class="col-xs-3 bold margin-form">Codigo Postal</div>
		<div class="col-xs-9 margin-form" id="direccionEmpresa"></div>
		<div class="col-xs-3 margin-form" id="cpEmpresa"></div>
		<div class="col-xs-4 bold margin-form">No. Factura</div>
		<div class="col-xs-4 bold margin-form">Fecha Factura</div>
		<div class="col-xs-4 bold margin-form">Fecha Entrega</div>
		<div class="col-xs-4 margin-form" id="noFacturaEmpresa"></div>
		<div class="col-xs-4 margin-form" id="fechaFactura"></div>
		<div class="col-xs-4 margin-form" id="fechaEntregaFactura"></div>
		<div class="col-xs-6 bold margin-form">Area</div>
		<div class="col-xs-6 margin-form" id="areaFactura"></div>
	</div>
	<div id="divResguardo" style="display:none;">
		<div class="col-xs-12 text-center margin-form"><h3>Datos Resguardo</h3></div>
		<div class="col-xs-6 bold margin-form">Folio del Resguardo</div>
		<div class="col-xs-6 bold margin-form">Departamento</div>
		<div class="col-xs-6 margin-form" id="folioResguardo"></div>
		<div class="col-xs-6 margin-form" id="areaResguardo"></div>
		<div class="col-xs-8 bold margin-form">Nombre Solicitante</div>
		<div class="col-xs-4 bold margin-form">Cargo Solicitante</div>
		<div class="col-xs-8 margin-form" id="nombreSolicitante"></div>
		<div class="col-xs-4 margin-form" id="cargoSolicitante"></div>
		<div class="col-xs-6 bold margin-form">Dependencia Solicitante</div>
		<div class="col-xs-6 bold margin-form">Departamento Solicitante</div>
		<div class="col-xs-6 margin-form" id="dependenciaSolicitante"></div>
		<div class="col-xs-6 margin-form" id="departamentoSolicitante"></div>
		<div class="col-xs-12 bold margin-form">Personal que Entrego</div>
		<div class="col-xs-12 margin-form" id="personalResguardo"></div>
	</div>
</div>