var currendCatalog;
var bandModificar;
var currendId = 0;
function complete()
{
	$("#activo").kendoComboBox({placeholder:"Seleccione su estado"});
	$("#activo").data("kendoComboBox").value("");
	$("#tablaCatalogos").kendoGrid(
	{
		pageable:
		{
			refresh: true,
			pageSizes: true,
			buttonCount: 5
		},
		height: 450,
        sortable: true,
        selectable: true,
        resizable: true,
        filterable: 
        {
        	messages:
        	{
        		info:"Mostrar registros que...",
        		filter:"Aplicar",
        		clear:"Limpiar"
        	},
        	extra:false,
        	operators:
        	{
        		string:
        		{
        			contains:"Contenga...",
        			startswith:"Empieze con...",
        			eq:"Sea igual que..."
        		}
        	}
        },
        columnMenu:
        {
        	sortable:false,
        	filterable:true,
        	columns:true,
        	messages:
        	{
        		columns:"Columnas",
        		filter:"Busqueda"
        	}
        }, 
        columns: [
        	{ field: "id",title:'Folio'},
        	{ field: "nombre",title:'Nombre'},
		    { field: "activo",title:'Activo',template:function(obj)
		    {
		    	if(obj.activo == "1")
		    		return 'Si';
		    	else if(obj.activo == "2")
		    		return 'No';
		    	else
		    		return 'No definido';
		    }}
		  ],
        dataBound:eventoTabla
    });
	$("#pestanias div").click(changeCatalog);
	$("#btnLimpiar").click(reiniciarFormulario);
	$("#btnGuardar").click(saveCatalogData);
}
function saveCatalogData()
{
	var condi = true;
	condi = condi && validarNoVacio($("#nombre").val(),"Nombre",$("#aviso"));
	condi = condi && validarComboBox($("#activo option:selected"),$("#aviso"),"Seleccione el estado")
	if(bandModificar)
		condi = confirm("¿Desea modificar este registro?");
	if(condi)
	{
		var url = path;
		switch(currendCatalog)
		{
			case 1:
				url += bandModificar ? "asignacion/updatebyid" : "asignacion/add" ;
			break;
			case 2:
				url += bandModificar ? "dependencias/updatebyid" : "dependencias/add" ;
			break;
			case 3:
				url += bandModificar ? "diagnostico/updatebyid" : "diagnostico/add" ;
			break;
			case 4:
				url += bandModificar ? "mantenimientos/updatebyid" : "mantenimientos/add" ;
			break;
		}
		$.post(url,{id:currendId,nombre:$("#nombre").val(),activo:$("#activo").data("kendoComboBox").value()},function(data)
		{
			try
			{
				var json = eval("("+data+")");
				if(json.ok)
				{
					switch(currendCatalog)
					{
						case 1:
							getAsignaciones();
						break;
						case 2:
							getDependencias();
						break;
						case 3:
							getDiagnostico();
						break;
						case 4:
							getMantenimientos();
							
						break;
					}
					reiniciarFormulario();
				}
				updateError(json.msg,$("#aviso"));
			}
			catch(err)
			{
				updateError("Ocurro un error "+(bandModificar ? "modificando " : "agregando ")+" el registro: "+err,$("#aviso"));
			}
		});
	}
}
function eventoTabla()
{
	$("#tablaCatalogos tbody tr").click(function(e)
	{
		var dataItem = $("#tablaCatalogos").data("kendoGrid").dataItem("tr[data-uid='"+$(e.currentTarget).closest("tr").data('uid')+"'");
		currendId = dataItem.id;
		getCatalogDataById();
	});
}
function getCatalogDataById()
{
	var url = path;
	switch(currendCatalog)
	{
		case 1:
			url += "asignacion/getbyid";
		break;
		case 2:
			url += "dependencias/getbyid";
		break;
		case 3:
			url += "diagnostico/getbyid";
		break;
		case 4:
			url += "mantenimientos/getbyid";
		break;
	}
	$.post(url,{id:currendId},function(data)
	{
		try
		{
			var json = eval("("+data+")");
			if(json.ok)
			{
				$("#nombre").val(json.msg.nombre);
				$("#activo").data("kendoComboBox").value(json.msg.activo);
				bandModificar = true;
			}
			else
				updateError(json.msg,$("#aviso"))
		}
		catch(err)
		{
			updateError("Ocurrio un error cargando los detalles: "+err,$("#aviso"))
		}
	});
}
function changeCatalog()
{
	if(currendCatalog != $(this).data('catalog'))
	{
		restartCatalogs();
		currendCatalog = $(this).data('catalog');
		$("#pestanias div").removeClass("selected")
		$(this).addClass("selected")
		$("#divForm").show(500);
		$("#actions").show(500);
		switch(currendCatalog)
		{
			case 1:
				getAsignaciones();
			break;
			case 2:
				getDependencias();
			break;
			case 3:
				getDiagnostico();
			break;
			case 4:
				getMantenimientos();
			break;
		}
	}
}
function restartCatalogs()
{
	$("#divForm").hide();
	reiniciarFormulario();
}
function reiniciarFormulario()
{
	$("#nombre").val("");
	$("#activo").data("kendoComboBox").value("");
	bandModificar = false;
	currendId = 0;
}
function getAsignaciones()
{
	$.post(path+'asignacion/getall',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#tablaCatalogos").data("kendoGrid").setDataSource(new kendo.data.DataSource({data:[]}));
			if(json.ok)
				$("#tablaCatalogos").data("kendoGrid").setDataSource(new kendo.data.DataSource({data: json.msg,pageSize:30}));
			else
				updateError("Ocurrio un error obteniendo las asignaciones: "+json.msg,$("#aviso"));
		}
		catch(err)
		{
			updateError("Ocurrio un error: "+err,$("#aviso"))
		}
	});
}
function getDependencias()
{
	$.post(path+'dependencias/getall',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#tablaCatalogos").data("kendoGrid").setDataSource(new kendo.data.DataSource({data:[]}));
			if(json.ok)
				$("#tablaCatalogos").data("kendoGrid").setDataSource(new kendo.data.DataSource({data: json.msg,pageSize:30}));
			else
				updateError("Ocurrio un error obteniendo las dependencias: "+json.msg,$("#aviso"));
		}
		catch(err)
		{
			updateError("Ocurrio un error: "+err,$("#aviso"))
		}
	});
}
function getDiagnostico()
{
	$.post(path+'diagnostico/getall',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#tablaCatalogos").data("kendoGrid").setDataSource(new kendo.data.DataSource({data:[]}));
			if(json.ok)
				$("#tablaCatalogos").data("kendoGrid").setDataSource(new kendo.data.DataSource({data: json.msg,pageSize:30}));
			else
				updateError("Ocurrio un error obteniendo los diagnosticos: "+json.msg,$("#aviso"));
		}
		catch(err)
		{
			updateError("Ocurrio un error: "+err,$("#aviso"))
		}
	});
}
function getMantenimientos()
{
	$.post(path+'mantenimientos/getall',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#tablaCatalogos").data("kendoGrid").setDataSource(new kendo.data.DataSource({data:[]}));
			if(json.ok)
				$("#tablaCatalogos").data("kendoGrid").setDataSource(new kendo.data.DataSource({data: json.msg,pageSize:30}));
			else
				updateError("Ocurrio un error obteniendo los mantenimientos: "+json.msg,$("#aviso"));
		}
		catch(err)
		{
			updateError("Ocurrio un error: "+err,$("#aviso"))
		}
	});
}
$(document).ready(complete);