function complete()
{
	$("#noFactura").kendoComboBox({placeholder:"Seleccione la Factura",dataValueField:"value",dataTextField:"text"});
	$("#noFactura").data("kendoComboBox").value("");
	$("#categoria").kendoComboBox({placeholder:"Seleccione la categoria",dataValueField:"value",dataTextField:"text"});
	$("#categoria").data("kendoComboBox").value("");
	$("#status").kendoComboBox({placeholder:"Seleccione el status",dataValueField:"value",dataTextField:"text"});
	$("#status").data("kendoComboBox").value("");
	$("#tipoEquipo").kendoComboBox({placeholder:"Seleccione el tipo de equipo",dataValueField:"value",dataTextField:"text"});
	$("#tipoEquipo").data("kendoComboBox").value("");
	$("#marca").kendoComboBox({placeholder:"Seleccione la marca",dataValueField:"value",dataTextField:"text"});
	$("#marca").data("kendoComboBox").value("");
	$("#um").kendoComboBox({placeholder:"Seleccione la unidad de medida",dataValueField:"value",dataTextField:"text"});
	$("#um").data("kendoComboBox").value("");
	$("#gridInventario").kendoGrid(
  	{
		dataSource: new kendo.data.DataSource(
		{
			pageSize: 20,
			schema: {
				model: {
				id: "idInventario",
				fields: {
					idInventario: { editable: false, nullable: true },
					cantidad: { type:"number" },
					codigo: { },
					categoria: {type:"number" },
					tipoEquipo: { },
					marca: { },
					modelo: { },
					noSerie: { },
					um: { },
					descripcion: {  }
					}
				}
			}
		}),
		pageable:
		{
			refresh: true,
			pageSizes: true,
			buttonCount: 5
		},
		height:374,
		columns: [
			{ field: "cantidad",title:'Cant.',width:80},
			{ field: "codigo",title:'Codigo'},
			{ field: "categoria",title:'Categoria'},
			{ field: "tipoEquipo",title:'Tipo de Equipo'},
			{ field: "marca",title:'Marca'},
			{ field: "modelo",title:'Modelo'},
			{ field: "noSerie",title:'No. Serie'},
			{ field: "um",title:'U/M',width:100},
			{ field: "descripcion",title:'Descripcion'}
		],
		dataBound:eventTable
    });
	getCategoriesInventario();
	getTipoEquipoInventario();
	getMarcasEquipoInventario();
	getUMInventario();
}
function eventTable()
{

}
function getCategoriesInventario()
{
	$.post('catinventario/getforcombo',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#categoria").data("kendoComboBox").setDataSource(json.ok ? json.msg : []);
			if(!json.ok)
				updateError(json.msg);
		}
		catch (e)
		{
		  updateError("Data: "+data+" Error:"+e);
		}
	});
}
function getTipoEquipoInventario()
{
	$.post('catteinventario/getforcombo',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#tipoEquipo").data("kendoComboBox").setDataSource(json.ok ? json.msg : []);
			if(!json.ok)
				updateError(json.msg);
		}
		catch (e)
		{
		  updateError("Data: "+data+" Error:"+e);
		}
	});
}
function getMarcasEquipoInventario()
{
	$.post('catmarcainventario/getforcombo',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#marca").data("kendoComboBox").setDataSource(json.ok ? json.msg : []);
			if(!json.ok)
				updateError(json.msg);
		}
		catch (e)
		{
		  updateError("Data: "+data+" Error:"+e);
		}
	});
}
function getUMInventario()
{
	$.post('catuminventario/getforcombo',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#um").data("kendoComboBox").setDataSource(json.ok ? json.msg : []);
			if(!json.ok)
				updateError(json.msg);
		}
		catch (e)
		{
		  updateError("Data: "+data+" Error:"+e);
		}
	});
}
$(document).ready(complete);