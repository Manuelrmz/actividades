var user;
var bandModificar = false;
function complete()
{
	$("#tablaUsuarios").kendoGrid(
	{
		dataSource: new kendo.data.DataSource(
	    {
	      schema: {
	        model: {
	          id: "usuario",
	          fields: {
	            usuario: {type: "number", editable: false, nullable: true },
	            nombres: {validation: { required: true } },
	            apellidos: { validation: { required: true } },
	            cargo: { validation: { required: true } },
	            ultimoAcceso: { validation: { required: true } },
	            area: { validation: { required: true } },
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
	$("#permisos").kendoMultiSelect({placeholder:'Seleccione los modulos que accedera el usuario',dataTextField:'text',dataValueField:'value'});
	//$("#permisos").data("kendoMultiSelect").value([]);
	$("#formUsers").submit(guardarUsuario);
	$("#btnLimpiar").click(limpiarCampos);
	cargarTabla();
	cargarAreas();
	cargarCargos();
	cargarPermisos();
	useBoxMessage = true;
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
	$("#permisos").data("kendoMultiSelect").value([]);
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
			$("#tablaUsuarios").data("kendoGrid").dataSource.data(json.ok ? json.msg : []);
			if(!json.ok)
				updateError(json.msg,$("#aviso"));
		}	
		catch(ex)
		{
			updateError("Error cargando la tabla: "+ex);
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
		dataSend.set('permisos',$("#permisos").data("kendoMultiSelect").value());
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
				updateError('Done: '+data);
			},
			error: function(data)
			{
				updateError('Error: '+data);
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
		$.post(path+'usuarios/getuserwithpermission',{usuario:user},function(data)
		{
			try
			{
				var json = eval("("+data+")");
				if(json.ok)
				{
					bandModificar = true;
					$("#usuario").val(json.msg.usuario);
					$("#usuario").prop('disabled','disabled');
					$("#nombres").val(json.msg.nombres);
					$("#apellidos").val(json.msg.apellidos);
					$("#area").data("kendoComboBox").text(json.msg.area);
					$("#cargo").data("kendoComboBox").value(json.msg.cargo);
					$("#permisos").data("kendoMultiSelect").value(json.msg.permisos);
				}
				else
					updateError(json.msg,$("#aviso"));
			}
			catch(err)
			{
				updateError("Error buscando al usuario: "+err+ " Data: "+data,$("#aviso"));
			}
		});
	});
}
function cargarAreas()
{
	$.post(path+'areas/getall',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#area").data("kendoComboBox").setDataSource(json.ok ? json.msg : []);
			if(!json.ok)
				updateError(json.msg);
		}
		catch(err)
		{
			updateError("Error: "+ err + " Data: "+data);
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
			$("#cargo").data("kendoComboBox").setDataSource(json.ok ? json.msg : []);
			if(!json.ok)
				updateError(json.msg);
		}
		catch(err)
		{
			updateError("Error: "+ err + " Data: "+data);
		}	
	});
}
function cargarPermisos()
{
	$.post(path+'permisos/getpermissionnames',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#permisos").data("kendoMultiSelect").setDataSource(json.ok ? json.msg : []);
			if(!json.ok)
				updateError(json.msg);
		}
		catch(err)
		{
			updateError("Error: "+ err + " Data: "+data);
		}
	});
}
$(document).ready(complete);