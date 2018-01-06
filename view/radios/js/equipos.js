var currendId;
var bandModificar;
function complete()
{
	$("#tipo").kendoComboBox({placeholder:"Seleccione el tipo de disposivo",dataTextField:'text',dataValueField:'value'});
	$("#tipo").data("kendoComboBox").value("");
	$("#estado").kendoComboBox({placeholder:"Seleccione el estado del disposivo"});
	$("#estado").data("kendoComboBox").value("");
	$("#tablaEquipos").kendoGrid(
	{
		dataSource: new kendo.data.DataSource(
		{
            schema: {
                model: {
                    id: "folio",
                    fields: {
                        folio: { editable: false, nullable: true },
                        rfsi: { validation: { required: true } },
                        nslogico: { validation: { required: true } },
                        serie: { validation: { required: true } },
                        tipo: { validation: { required: true } }
                    }
                }
            },pageSize:30
		}),
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
        	{ field: "rfsi",title:'RFSI'},
        	{ field: "nslogico",title:'N/S Logico'},
		    { field: "serie",title:'Serie'},
		    { field: "tipo",title:'Tipo'},
		    { field: "activo",title:'Activo',template:function(dataItem)
		    {
		    	valor = "Desactivado";
		    	if(dataItem.activo == "1")
		    		valor = "Activo"
		    	return valor;
		    }}
		  ],
        dataBound:eventoTabla
    });
	$("#btnGuardar").click(guardarEquipo);
	$("#btnLimpiar").click(limpiarCampos);
	cargarTabla();
	getTipo();
}
function guardarEquipo()
{
	var condi = true;
	condi = condi && validarTamanio($("#rfsi").val(),"RFSI",7,9,$("#aviso")); 
	condi = condi && validarTamanio($("#nslogico").val(),"N/S Logico",8,10,$("#aviso")); 
	condi = condi && validarTamanio($("#serie").val(),"Serie",15,30,$("#aviso")); 
	condi = condi && validarTamanio($("#version").val(),"Version",1,10,$("#aviso")); 
	condi = condi && validarComboBox($("#tipo option:selected"),$("#aviso"),"Seleccione un tipo de equipo");
	condi = condi && validarComboBox($("#estado option:selected"),$("#aviso"),"Seleccione el estado del equipo");
	if(bandModificar)
		condi = confirm("¿Realmente desea modificar el equipo seleccionado?");
	if(condi)
	{
		$.post(path+'equiposradios/'+(bandModificar ? 'updatebyid' : 'add'),{id : currendId, rfsi: $("#rfsi").val(),nslogico:$("#nslogico").val(),serie:$("#serie").val(),version:$("#version").val(),tipo:$("#tipo").data("kendoComboBox").text(),comentario1:$("#comentario1").val(),comentario2:$("#comentario2").val(),activo:$("#estado").data("kendoComboBox").value()},function(data)
		{
			try
			{
				var json = eval("("+data+")");
				if(json.ok)
				{
					limpiarCampos();
					cargarTabla();
				}
				updateError(json.msg,$("#aviso"));
			}
			catch(err)
			{
				updateError("Data: "+data,$("#aviso"));
			}
		});
	}
}
function eventoTabla()
{
	$("#tablaEquipos tbody tr").click(function(e)
	{
		var dataItem = $("#tablaEquipos").data("kendoGrid").dataItem("tr[data-uid='"+$(e.currentTarget).closest("tr").data('uid')+"'");
		$.post(path+'equiposradios/getinfobyid',{id:dataItem.id},function(data)
		{
			try
			{
				var json = eval("("+data+")");
				if(json.ok)
				{
					currendId = dataItem.id;
					bandModificar = true;
					$("#rfsi").val(json.msg.rfsi);
					$("#nslogico").val(json.msg.nslogico);
					$("#serie").val(json.msg.serie);
					$("#version").val(json.msg.version);
					$("#tipo").data("kendoComboBox").text(json.msg.tipo);
					$("#comentario1").val(json.msg.comentario1);
					$("#comentario2").val(json.msg.comentario2);
					$("#estado").data("kendoComboBox").value(json.msg.activo);
				}
				else
					updateError("Ocurrio un error obteniendo el equipo: "+json.msg,$("#aviso"));
			}
			catch(err)
			{
				updateError("Ocurrio un error: "+err,$("#aviso"))
			}
		});
	});
}
function cargarTabla()
{
	$.post(path+'equiposradios/getfortable',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#tablaEquipos").data("kendoGrid").dataSource.data([]);
			if(json.ok)
				$("#tablaEquipos").data("kendoGrid").dataSource.data(json.msg);
			else
				updateError(json.msg,$("#aviso"));
		}
		catch(err)
		{
			updateError("Ocurrio un error cargando la tabla: "+err,$("#aviso"));
		}
	});
}
function limpiarCampos()
{
	currendId = "";
	bandModificar = false;
	$("#rfsi").val("");
	$("#nslogico").val("");
	$("#serie").val("");
	$("#version").val("");
	$("#tipo").data("kendoComboBox").value("");
	$("#comentario1").val("");
	$("#comentario2").val("");
	$("#estado").data("kendoComboBox").value("");
}
function getTipo()
{
	$.post(path+'tipoequiporadios/getall',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			if(json.ok)
				$("#tipo").data("kendoComboBox").setDataSource(json.msg);
			else
				updateError("Ocurrio un error obteniendo los tipos de equipos: "+json.msg,$("#aviso"));
		}
		catch(err)
		{
			updateError("Ocurrio un error: "+err,$("#aviso"))
		}
	});
}
$(document).ready(complete);