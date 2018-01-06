var bandModificar;
var currentId;
function complete()
{
	$("#rfsi").kendoComboBox({placeholder:"Seleccione el RFSI",dataTextField:'text',dataValueField:'value',change:getInfoEquipo});
	$("#rfsi").data("kendoComboBox").value("");
	$("#dependencia").kendoComboBox({placeholder:"Seleccione la dependencia",dataTextField:'nombre',dataValueField:'id'});
	$("#dependencia").data("kendoComboBox").value("");
	$("#asignacion").kendoComboBox({placeholder:"Seleccione la asignacion",dataTextField:'nombre',dataValueField:'id'});
	$("#asignacion").data("kendoComboBox").value("");
	$("#mantenimiento").kendoComboBox({placeholder:"Seleccione el tipo de mantenimiento",dataTextField:'nombre',dataValueField:'id'});
	$("#mantenimiento").data("kendoComboBox").value("");
	$("#diagnostico").kendoComboBox({placeholder:"Seleccione el diagnostico",dataTextField:'nombre',dataValueField:'id'});
	$("#diagnostico").data("kendoComboBox").value("");
	$("#tablaMantenimientos").kendoGrid(
	{
		dataSource: new kendo.data.DataSource(
		{
            schema: {
                model: {
                    id: "folio",
                    fields: {
                        folio: { editable: false, nullable: true },
                        folioFull: { validation: { required: true } },
                        reporta: { validation: { required: true } },
                        dependencia: { validation: { required: true } },
                        asignacion: { validation: { required: true } },
                        RFSI: { validation: { required: true } },
                        capturo: { validation: { required: true } },
                        fechaCreacion: { validation: { required: true } }
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
        	{ field: "folioFull",title:'Folio'},
        	{ field: "reporta",title:'Reporta'},
		    { field: "dependencia",title:'Dependencia'},
		    { field: "asignacion",title:'Asignacion'},
		    { field: "RFSI",title:'RFSI'},
		    { field: "capturo",title:'Capturo'},
		    { field: "fechaCreacion",title:'Fecha de Creacion'},
		  ],
        dataBound:eventoTabla
    });
	$("#btnGuardar").click(guardarMantenimiento);
	$("#btnLimpiar").click(limpiarCampos);
	$("#btnImprimir").click(imprimirBoleta);
	$("#btnBuscar").click(cargarTabla);
	getAsignaciones();
	getDependencias();
	getDiagnostico();
	getMantenimientos();
	getEquiposRadios();
	cargarTabla();
}
function guardarMantenimiento()
{
	var condi = true;
	condi = condi && validarTamanio($("#reporta").val(),"Reporta",1,200,$("#aviso"));
	condi = condi && validarNoVacio($("#motivo").val(),"Motivo",$("#aviso"));
	condi = condi && validarComboBox($("#dependencia option:selected"),$("#aviso"),"Seleccione una dependencia valida");
	condi = condi && validarComboBox($("#asignacion option:selected"),$("#aviso"),"Seleccione una asignacion valida");
	condi = condi && validarComboBox($("#rfsi option:selected"),$("#aviso"),"Seleccione un RFSI valido");
	condi = condi && validarComboBox($("#mantenimiento option:selected"),$("#aviso"),"Seleccione un mantenimiento valido");
	condi = condi && validarComboBox($("#diagnostico option:selected"),$("#aviso"),"Seleccione un diagnostico valido");
	condi = condi && validarNoVacio($("#comenIntervencion").val(),"Comentarios de la Intervencion",$("#aviso"));
	if(bandModificar)
		condi = confirm('¿Desea modificar el servicio seleccionado?');
	if(condi)
	{
		var request = {id:currentId,reporta:$("#reporta").val(),motivo:$("#motivo").val(),vehiculo:$("#vehiculo").val(),placas:$("#placas").val(),unidad:$("#unidad").val(),
						dependencia:$("#dependencia").data("kendoComboBox").value(),asignacion:$("#asignacion").data("kendoComboBox").value(),rfsi:$("#rfsi").data("kendoComboBox").text(),mantenimiento:$("#mantenimiento").data("kendoComboBox").value(),diagnostico:$("#diagnostico").data("kendoComboBox").value(),
						comentarios:$("#comenIntervencion").val(),observaciones:$("#observaciones").val()};
		$.post(path+'manttoradios/'+(bandModificar ? "update" : "add"),request,function(data)
		{
			try
			{
				var json = eval("("+data+")");
				if(json.ok)
				{
					if(!bandModificar)
						currentId = json.id;
					if(confirm("¿Desea abrir la papelera?"));
						imprimirBoleta();
					limpiarCampos();
					cargarTabla();
				}
				updateError(json.msg,$("#aviso"));
			}
			catch(err)
			{
				updateError("Ocurrio un error: "+err,$("#aviso"))
			}
		});
	}
}
function imprimirBoleta()
{
	if(nan(currentId,"El folio no es correcto",$("#aviso")))
		window.open(path+"manttoradios/createfile/"+currentId,"_blank");
}
function limpiarCampos()
{
	bandModificar = false;
	currentId = "";
	$("#reporta").val("");
	$("#motivo").val("");
	$("#vehiculo").val("");
	$("#placas").val("");
	$("#unidad").val("");
	$("#dependencia").data("kendoComboBox").value("");
	$("#asignacion").data("kendoComboBox").value("");
	$("#rfsi").data("kendoComboBox").value("");
	$("#serie").val("");
	$("#nslog").val("");
	$("#idterminal").html("");
	$("#terminal").val("");
	$("#comenTerminal").val("");
	$("#mantenimiento").data("kendoComboBox").value("");
	$("#diagnostico").data("kendoComboBox").value("");
	$("#comenIntervencion").val("");
	$("#observaciones").val("");
	$("#btnImprimir").hide();
}
function cargarTabla()
{
	var condi = true;
	if($("#fechaIni").val()!= "")
		condi = condi && validarFecha($("#fechaIni").val(),"La fecha de inicio es incorrecta",$("#avisoTabla"));
	if($("#fechaFin").val() != "")
		condi = condi && validarFecha($("#fechaFin").val(),"La fecha final es incorrecta",$("#avisoTabla"));
	if(condi)
	{
		$.post(path+'manttoradios/getall',{fechaIni:$("#fechaIni").val(),fechaFin:$("#fechaFin").val()},function(data)
		{
			try
			{
				var json = eval("("+data+")");
				$("#tablaMantenimientos").data("kendoGrid").dataSource.data([]);
				if(json.ok)
					$("#tablaMantenimientos").data("kendoGrid").dataSource.data(json.msg);
				else
					updateError(json.msg,$("#avisoTabla"));
			}
			catch(err)
			{
				updateError("Ocurrio un error cargando la tabla: "+err,$("#avisoTabla"));
			}
		});
	}
}
function eventoTabla()
{
	$("#tablaMantenimientos tbody tr").click(function(e)
	{
		var dataItem = $("#tablaMantenimientos").data("kendoGrid").dataItem("tr[data-uid='"+$(e.currentTarget).closest("tr").data('uid')+"'");
		$.post(path+'manttoradios/getbyid',{id:dataItem.id},function(data)
		{
			try
			{
				var json = eval("("+data+")");
				if(json.ok)
				{
					bandModificar = true;
					currentId = dataItem.id;
					$("#reporta").val(json.msg.reporta);
					$("#motivo").val(json.msg.motivo);
					$("#vehiculo").val(json.msg.vehiculo);
					$("#placas").val(json.msg.placas);
					$("#unidad").val(json.msg.unidad);
					$("#dependencia").data("kendoComboBox").value(json.msg.dependencia);
					$("#asignacion").data("kendoComboBox").value(json.msg.asignacion);
					$("#rfsi").data("kendoComboBox").value(json.msg.rfsi);
					$("#mantenimiento").data("kendoComboBox").value(json.msg.mantenimiento);
					$("#diagnostico").data("kendoComboBox").value(json.msg.diagnostico);
					$("#comenIntervencion").val(json.msg.comentarios);
					$("#observaciones").val(json.msg.observaciones);
					$("#btnImprimir").show();
					getInfoEquipo();
				}
				else
					updateError(json.msg,$("#aviso"));
			}
			catch(err)
			{
				updateError(err,$("#aviso"));
			}
		});
	});
}
function getInfoEquipo()
{
	if(validarComboBox($("#rfsi option:selected"),$("#aviso"),'Seleccione un equipo correcto'))
	{
		$.post(path+'equiposradios/getinfobyid',{id:$("#rfsi").data("kendoComboBox").value()},function(data)
		{
			try
			{
				var json = eval("("+data+")");
				if(json.ok)
				{
					$("#serie").val(json.msg.serie);
					$("#nslog").val(json.msg.nslogico);
					$("#idterminal").html(json.msg.tipo);
					$("#terminal").val(json.msg.descripcion);
					$("#comenTerminal").val(json.msg.comentario1+"\n"+json.msg.comentario2);
				}
				else
				{
					updateError(json.msg,$("#aviso"));
					$("#serie").val("");
					$("#nslog").val("");
					$("#idterminal").html("");
					$("#terminal").val("");
					$("#comenTerminal").val("");
				}
			}
			catch(err)
			{
				updateError("Ocurrio un errro cargando los datos del equipo: "+err,$("#aviso"));
			}
		});
	}
}
function getEquiposRadios()
{
	$.post(path+'equiposradios/getall',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			if(json.ok)
				$("#rfsi").data("kendoComboBox").setDataSource(json.msg);
			else
				updateError("Ocurrio un error obteniendo los equipos: "+json.msg,$("#aviso"));
		}
		catch(err)
		{
			updateError("Ocurrio un error: "+err,$("#aviso"))
		}
	});
}
function getAsignaciones()
{
	$.post(path+'asignacion/getactivas',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			if(json.ok)
				$("#asignacion").data("kendoComboBox").setDataSource(json.msg);
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
	$.post(path+'dependencias/getactivas',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			if(json.ok)
				$("#dependencia").data("kendoComboBox").setDataSource(json.msg);
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
	$.post(path+'diagnostico/getactivas',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			if(json.ok)
				$("#diagnostico").data("kendoComboBox").setDataSource(json.msg);
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
	$.post(path+'mantenimientos/getactivas',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			if(json.ok)
				$("#mantenimiento").data("kendoComboBox").setDataSource(json.msg);
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