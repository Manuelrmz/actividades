var user;
var bandModificar;
function complete()
{
	$("#tablaUsuarios").kendoGrid(
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
        	{ field: "usuario",title:'Usuario'},
		    { field: "nombres",title:'Nombres'},
            { field: "apellidos",title:'Apellidos'},
		    { field: "area",title:'Area'},
		    { field: "cargo",title:'Cargo'},
		    { field: "ultimoAcceso",title:'Ultimo Acceso'}
		  ],
        dataBound:eventoTabla
    });
	$("#area").kendoComboBox({placeholder:'Seleccione el area',dataTextField:'text',dataValueField:'value'});
	$("#area").data("kendoComboBox").value("");
	$("#cargo").kendoComboBox({placeholder:'Seleccione el cargo',dataTextField:'text',dataValueField:'value'});
	$("#cargo").data("kendoComboBox").value("");
	$("#formUsers").submit(guardarUsuario);
	$("#btnLimpiar").click(limpiarCampos);
	cargarTabla();
	cargarAreas();
	cargarCargos();
	bandModificar = false;
}
function limpiarCampos()
{
	user = "";
	bandModificar = false;
	$("#usuario").val('');
	$("#nombres").val('');
	$("#apellidos").val('');
	$("#pass1").val('');
	$("#pass2").val('');
	$("#area").data("kendoComboBox").value('');
	$("#cargo").data("kendoComboBox").value('');
	$("#formUsers input[type=checkbox]").prop('checked',false);
	$("#tablaUsuarios").data("kendoGrid").select({})
	$("#usuario").prop('disabled',false);
}
function cargarTabla()
{
	$.post(path+'usuarios/gettabla',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#tablaUsuarios").data("kendoGrid").setDataSource(new kendo.data.DataSource({data:[]}));
			if(json.ok)
			{
				if(json.msg.length > 0)
					$("#tablaUsuarios").data("kendoGrid").setDataSource(new kendo.data.DataSource({data: json.msg,pageSize:30}));
			}
			else
				updateError(json.msg,$("#aviso"));
		}	
		catch(ex)
		{
			updateError("Error cargando la tabla: "+ex,$("#aviso"));
		}
	});
}
function guardarUsuario(e)
{
	e.preventDefault();
	var condi = true;
	if(!bandModificar)
		condi = condi && validarTamanio($("#usuario").val(),'Usuario',1,50,$("#aviso"));
	condi = condi && validarTamanio($("#nombres").val(),'Nombres',1,50,$("#aviso"));
	condi = condi && validarTamanio($("#apellidos").val(),'Apellidos',1,50,$("#aviso"));
	if(!bandModificar)
	{
		condi = condi && validarNoVacio($("#pass1").val(),"Contrase&ntilde;a",$("#aviso"));
		condi = condi && validarNoVacio($("#pass2").val(),"Confirmar Contrase&ntilde;a",$("#aviso"));
	}
	condi = condi && validarComboBox($("#area option:selected"),$("#aviso"),"Seleccione un area correcta");
	condi = condi && validarComboBox($("#cargo option:selected"),$("#aviso"),"Seleccione un cargo correcta");
	if(condi)
	{
		var dataSend = new FormData(this);
		dataSend.append('usuario',(bandModificar ? user : $("#usuario").val()));
		var url = path + (bandModificar ? 'usuarios/update' : 'usuarios/new');
		$.ajax(
		{
			url:url,
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
						limpiarCampos();
						cargarTabla();
						updateError(json.msg,$("#aviso"));
					}
					else
						updateError(json.msg,$("#aviso"));
				}
				catch(err)
				{
					updateError(err,$("#aviso"));
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
function eventoTabla()
{
	$("#tablaUsuarios tbody tr").click(function()
	{
		var fila = $('td',this);
		user = $(fila[0]).text();
		buscarUsuario();
	});
}
function buscarUsuario()
{
	$.post(path+'usuarios/getuser',{usuario:user},function(data)
	{
		try
		{
			var json = eval("("+data+")");
			if(json.ok)
			{
				console.log(json.msg);
				bandModificar = true;
				$("#usuario").val(json.msg.usuario);
				$("#usuario").prop('disabled','disabled');
				$("#nombres").val(json.msg.nombres);
				$("#apellidos").val(json.msg.apellidos);
				$("#area").data("kendoComboBox").text(json.msg.area);
				$("#cargo").data("kendoComboBox").value(json.msg.cargo);
				$("#capof").prop('checked',(json.msg.capturaOf == 1 ? true : false));
				$("#busof").prop('checked',(json.msg.busquedaOf == 1 ? true : false));
				$("#segaof").prop('checked',(json.msg.seguimientoOf == 1 ? true : false));
				$("#seggof").prop('checked',(json.msg.seguimientoGenOf == 1 ? true : false));
				$("#admuser").prop('checked',(json.msg.adminusers == 1 ? true : false));
				$("#historico").prop('checked',(json.msg.historico == 1 ? true : false));
				$("#acceso").prop('checked',(json.msg.acceso == 1 ? true : false));
			}
			else
				updateError(json.msg,$("#aviso"));
		}
		catch(err)
		{
			updateError("Error buscando al usuario: "+err,$("#aviso"));
		}
	});
}
function cargarAreas()
{
	$.post(path+'areas/getall',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			if(json.ok)
			{
				$("#area").data("kendoComboBox").setDataSource(json.msg);
			}
			else
				console.log(json.msg);
		}
		catch(err)
		{
			console.log(err);
		}	
	});
}
function cargarCargos()
{
	$.post(path+'cargosUsuario/getall',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			if(json.ok)
			{
				$("#cargo").data("kendoComboBox").setDataSource(json.msg);
			}
			else
				console.log(json.msg);
		}
		catch(err)
		{
			console.log(err);
		}	
	});
}
$(document).ready(complete);