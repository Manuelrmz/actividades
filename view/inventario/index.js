var bandModificar = false;
var currentId = null;
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
	loadTable();
	getFacturas();
	getStatusInventario();
	$("#formInventario").submit(saveInventario);
	$("#clearFieldsInventario").click(cleanFieldsInventario);
	useBoxMessage = true;
}
function saveInventario(e)
{
	e.preventDefault();
	var condi = true;
	condi = condi && validarEntero($("#cantidad").val(),"El campo cantidad debe ser numerico");
	condi = condi && validarMinInt($("#cantidad").val(),1,"cantidad");
	condi = condi && validarComboBox($("#categoria option:selected"),undefined,"Seleccione una categoria");
	condi = condi && validarComboBox($("#tipoEquipo option:selected"),undefined,"Seleccione un tipo de equipo");
	condi = condi && validarComboBox($("#marca option:selected"),undefined,"Seleccione una marca");
	condi = condi && validarComboBox($("#um option:selected"),undefined,"Seleccione una unidad de medida");
	if(bandModificar)
	{
		condi = condi && validarEntero(currentId,"La id del equipo es invalida");
		condi = condi && confirm("¿Realmente desea modificar el equipo?");
	}
	if(condi)
	{	
		var dataSend = new FormData(this);
		dataSend.set('idfactura',$("#noFactura").data("kendoComboBox").value());
		dataSend.set('categoria',$("#categoria").data("kendoComboBox").text());
		dataSend.set('tipoEquipo',$("#tipoEquipo").data("kendoComboBox").text());
		dataSend.set('marca',$("#marca").data("kendoComboBox").text());
		dataSend.set('um',$("#um").data("kendoComboBox").text());
		if(bandModificar)
			dataSend.append('id',currentId);
		$.ajax(
        {
            url:path+'inventario/'+(bandModificar ? 'update' : 'add'),
            type:"POST",
            data:dataSend,
            processData:false,
            contentType:false,
            success: function(data)
            {
                try
                {
                    var json = eval("("+data+")");
                    if(json.ok)
                    {
                        cleanFieldsInventario();
                        showSuccessBox(json.msg);
                        loadTable();
                    }
                    else
                        updateError(json.msg);
                }
                catch(err)
                {
                    updateError("Error: "+err+ " Data: "+data);
                }
            },
            error: function(data)
            {
                updateError('Error: '+data);
            }
        });
	}
}
function cleanFieldsInventario()
{
	currentId = null;
	bandModificar = false;
	$("#titleForm").html("Nuevo Equipo");
	$("#noFactura").data("kendoComboBox").value("");
	$("#status").data("kendoComboBox").value("");
	$("#cantidad").val("");
	$("#categoria").data("kendoComboBox").value("");
	$("#tipoEquipo").data("kendoComboBox").value("");
	$("#marca").data("kendoComboBox").value("");
	$("#um").data("kendoComboBox").value("");
	$("#codigo").val("");
	$("#modelo").val("");
	$("#noSerie").val("");
	$("#descripcion").val("");
}
function loadTable()
{
	$.post(path+'inventario/getfortable',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#gridInventario").data("kendoGrid").dataSource.data(json.ok ? json.msg : []);
			if(!json.ok)
				updateError(json.msg);
		}
		catch (e)
		{
		  updateError("Data: "+data+" Error:"+e);
		}
	});
}
function eventTable()
{
	$("#gridInventario tbody tr").click(function(e)
    {
        var dataItem = $("#gridInventario").data("kendoGrid").dataItem("tr[data-uid='"+$(e.currentTarget).closest("tr").data('uid')+"'");
        $.post(path+'inventario/getbyid/'+dataItem.idInventario,function(data)
        {
            try 
            {
                var json = eval("("+data+")");
                if(json.ok)
                {
                    cleanFieldsInventario();
                    $("#titleForm").html("Modificando equipo con numero de serie: "+json.msg.noSerie);
                	currentId = json.msg.id;
					bandModificar = true;
					$("#noFactura").data("kendoComboBox").value(json.msg.idfactura ? json.msg.idfactura : "");
					$("#status").data("kendoComboBox").value(json.msg.status);
					$("#cantidad").val(json.msg.cantidad);
					$("#categoria").data("kendoComboBox").value(json.msg.categoria);
					$("#tipoEquipo").data("kendoComboBox").value(json.msg.tipoEquipo);
					$("#marca").data("kendoComboBox").value(json.msg.marca);
					$("#um").data("kendoComboBox").value(json.msg.um);
					$("#codigo").val(json.msg.codigo);
					$("#modelo").val(json.msg.modelo);
					$("#noSerie").val(json.msg.noSerie);
					$("#descripcion").val(json.msg.descripcion);
                }
                else
                    updateError(json.msg);
            }
            catch (e) 
            {
                updateError("Error: "+e+"\nData:"+data)
            }
        });
    });
}
function getCategoriesInventario()
{
	$.post(path+'catinventario/getforcombo',function(data)
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
	$.post(path+'catteinventario/getforcombo',function(data)
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
	$.post(path+'catmarcainventario/getforcombo',function(data)
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
	$.post(path+'catuminventario/getforcombo',function(data)
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
function getFacturas()
{
	$.post(path+'facturas/getforcombo',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#noFactura").data("kendoComboBox").setDataSource(json.ok ? json.msg : []);
			if(!json.ok)
				updateError(json.msg);
		}
		catch (e)
		{
		  updateError("Data: "+data+" Error:"+e);
		}
	});
}
function getStatusInventario()
{
	$.post(path+'catstainventario/getforcombo',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#status").data("kendoComboBox").setDataSource(json.ok ? json.msg : []);
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