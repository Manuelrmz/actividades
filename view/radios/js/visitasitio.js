var currentId;
var bandModificar;
function complete()
{
	bandModificar = false;
	$("#sitios").kendoMultiSelect({placeholder:"Seleccione al menos un sitio",dataTextField:'text',dataValueField:'value'});
	$("#personal").kendoMultiSelect({placeholder:"Seleccione al menos un personal",dataTextField:'text',dataValueField:'value'});
	$("#tablavisitas").kendoGrid({
		dataSource: new kendo.data.DataSource(
		{
            schema: {
                model: {
                    id: "folio",
                    fields: {
                        folio: {type: "number",editable: false, nullable: true },
                        folioFull: { validation: { required: true } },
                        reporta: { validation: { required: true } },
                        motivo: { validation: { required: true } },
                        captura: { validation: { required: true } },
                        fecha: { validation: { required: true }  },
                        fechaVisita: { validation: { required: true }  }
                    }
                }
            },
            pageSize:30
		}),
		pageable:
		{
			refresh: true,
			pageSizes: true,
			buttonCount: 5
		},
		height: 350,
        sortable: true,
        resizable: true,
        selectable: true,
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
        	{ field: "reporta",title:'Persona que Reporta'},
		    { field: "motivo",title:'Motivo'},
		    { field: "captura",title:'Capturo'},
		    { field: "fechaVisita",title:'Fecha de Visita'},
		    { field: "fecha",title:'Fecha Creacion'}
		  ],
		dataBound:eventoTabla
	});	
	$("#formVisita").submit(guardarVisita);	
	$("#btnLimpiar").click(limpiarCampos);
	$("#btnImprimir").click(imprimirReporte);
	$("#btnBuscar").click(cargarTabla);
	getSitios();
	getPersonal();
	cargarTabla();
}
function guardarVisita(e)
{
	e.preventDefault();
	var condi = true;
	condi = condi && validarTamanio($("#reporta").val(),"Reporta",1,150,$("#aviso"));
	condi = condi && validarNoVacio($("#motivo").val(),"Motivo",$("#aviso"));
	condi = condi && validarFecha($("#fechaVisita").val(),"La fecha de visita no es valida",$("#avisoTabla"));
	if($("#odometro").val() != "")
		condi = condi && validarEntero($("#odometro").val(),"El campo odometro debe ser numerico",$("#aviso"));
	condi = condi && revisarReg($("#sitios").data("kendoMultiSelect").value(),/^([0-9],*)/,"Seleccione los Sitios de la lista",$("#aviso"));
	condi = condi && revisarReg($("#personal").data("kendoMultiSelect").value(),/^([0-9],*)/,"Seleccione el Personal de la lista",$("#aviso")); 
	condi = condi && validarNoVacio($("#comentarios").val(),"comentarios",$("#aviso"));
	if(bandModificar)
		condi = confirm('¿Realmente desea modificar la visita?');
	if(condi)
	{
		var dataSend = new FormData(this);
		dataSend.append('id',currentId);
		dataSend.append('sitios',$("#sitios").data("kendoMultiSelect").value());
		dataSend.append('personal',$("#personal").data("kendoMultiSelect").value());
		$.ajax(
		{
			url:path+'visitasitios/'+(bandModificar ? 'update' : 'add'),
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
						if(!bandModificar)
						{
							currentId = json.id;
							if(confirm("¿Desea Generar el Reporte en PDF?"))
								imprimirReporte();
						}
						limpiarCampos();
						cargarTabla();
					}
					updateError(json.msg,$("#aviso"))
				}
				catch(err)
				{
					updateError("Error: "+err,$("#aviso"));
				}
			},
			done:function(data)
			{
				console.log('Done: '+data);
			},
			error: function(data)
			{
				console.log('Error: '+data);
			}
		});
	}
}
function imprimirReporte()
{
	if(nan(currentId,"El folio no es correcto",$("#aviso")))
		window.open(path+"visitasitios/createfile/"+currentId,"_blank");
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
		$.post(path+'visitasitios/getvisitastable',{fechaIni:$("#fechaIni").val(),fechaFin:$("#fechaFin").val()},function(data)
		{
			try
			{
				var json = eval("("+data+")");
				$("#tablavisitas").data("kendoGrid").dataSource.data([]);
				if(json.ok)
					$("#tablavisitas").data("kendoGrid").dataSource.data(json.msg);
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
	$("#tablavisitas tbody tr").click(function(e)
    {
        var dataItem = $("#tablavisitas").data("kendoGrid").dataItem("tr[data-uid='"+$(e.currentTarget).closest("tr").data('uid')+"'");
        $.post(path+'visitasitios/getbyid',{id:dataItem.folio},function(data)
        {
        	try
        	{
        		var json = eval("("+data+")");
        		if(json.ok)
        		{
        			currentId = dataItem.folio;
					bandModificar = true;
					$("#reporta").val(json.msg.reporta);
					$("#vehiculo").val(json.msg.vehiculo);
					$("#placas").val(json.msg.placas);
					$("#odometro").val(json.msg.odometro);
					$("#motivo").val(json.msg.motivo);
					$("#fechaVisita").val(json.msg.fechaVisita);
					$("#sitios").data("kendoMultiSelect").value(json.msg.sitios.split(','));
					$("#personal").data("kendoMultiSelect").value(json.msg.personal.split(','));
					$("#comentarios").val(json.msg.comentarios);
					$("#btnImprimir").show();
        		}
        		else
        			updateError(json.msg,$("#aviso"));
        	}
        	catch(err)
        	{	
        		updateError("Error cargando la visita: "+err,$("#aviso"));
        	}
        });
    });
}
function limpiarCampos()
{
	currentId = "";
	bandModificar = false;
	$("#reporta").val("");
	$("#vehiculo").val("");
	$("#placas").val("");
	$("#odometro").val("");
	$("#motivo").val("");
	$("#fechaVisita").val("");
	$("#files").val("");
	$("#sitios").data("kendoMultiSelect").value([])
	$("#personal").data("kendoMultiSelect").value([])
	$("#comentarios").val("");
	$("#btnImprimir").hide();
}
function getSitios()
{
	$.post(path+'sitios/getforcombo',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			if(json.ok)
			{
				$("#sitios").data("kendoMultiSelect").setDataSource({data:json.msg});
			}
			else
				updateError(json.msg,$("#aviso"));
		}
		catch(err)
		{
			updateError("Error obteniendo : "+err,$("#aviso"));
		}		
	});
}
function getPersonal()
{
	$.post(path+'radios/getpersonallist',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			if(json.ok)
			{
				$("#personal").data("kendoMultiSelect").setDataSource({data:json.msg});
			}
			else
				updateError(json.msg,$("#aviso"));
		}
		catch(err)
		{
			updateError("Error obteniendo : "+err,$("#aviso"));
		}	
	});
}
$(document).ready(complete);