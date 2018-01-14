function complete()
{
	$("#gridAreas").kendoGrid(
	{
		dataSource:new kendo.data.DataSource(
		{
			transport:
			{
				read: function(e)
				{
					$.post(path+"areas/getfortable",function(data)
					{
						try
						{
							var json = eval("("+data+")");
							e.success(json.ok ? json.msg : []);
							if(!json.ok)
								updateError(json.msg);
						}
						catch(err)
						{
							updateError("Error cargando las areas: "+err+ " data:"+data);
						}
					});
				},
				update: function(e)
				{
					$.post(path+"areas/update",e.data.models[0],function(data)
					{
						try
						{
							var json = eval("("+data+")");
							if(json.ok)
								e.success();
							else
								updateError(json.msg);
						}
						catch(err)
						{
							updateError("Error modificando el area: "+err+ " data:"+data);
						}
					});
				},
				create: function(e)
				{
					$.post(path+"areas/new",e.data.models[0],function(data)
					{
						try
						{
							var json = eval("("+data+")");
							if(json.ok)
							{
								e.data.models[0].id = json.id;
								e.success(e.data.models[0]);
							}
							else
								updateError(json.msg);
						}
						catch(err)
						{
							updateError("Error creando el area: "+err+ " data:"+data);
						}
					});
				},
				parameterMap: function(options, operation) 
				{
					if (operation !== "read" && options.models) {
						return {models: kendo.stringify(options.models)};
					}
				}
			},
			schema: {
				model: {
					id: "id",
					fields: {
						id: { editable: false, nullable: true },
						clave: { validation: { required: true } },
						nombre: { validation: { required: true } },
						representante: {  }
					}
				}
			},
			error:function(e)
			{
				updateError(e.xhr.responseText);
			},
			batch:true,
			pageSize: 20
		}),
		toolbar: ["create"],
		pageable:
		{
			refresh: true,
			pageSizes: true,
			buttonCount: 5
		},
		height:400,
		columns: [
			{ field: "clave",title:'Nombre'},
			{ field: "nombre",title:'Clave'},
			{ field: "representante",title:'Representante'},
			{ command: ["edit"], title: "Acciones", width: 90 },
		],
		editable: "popup"
	});
	$("#gridCargos").kendoGrid(
	{
		dataSource:new kendo.data.DataSource(
		{
			transport:
			{
				read: function(e)
				{
					$.post(path+"cargosusuario/getfortable",function(data)
					{
						try
						{
							var json = eval("("+data+")");
							e.success(json.ok ? json.msg : []);
							if(!json.ok)
								updateError(json.msg);
						}
						catch(err)
						{
							updateError("Error cargando los cargos de usuario: "+err+ " data:"+data);
						}
					});
				},
				update: function(e)
				{
					$.post(path+"cargosusuario/update",e.data.models[0],function(data)
					{
						try
						{
							var json = eval("("+data+")");
							if(json.ok)
								e.success();
							else
								updateError(json.msg);
						}
						catch(err)
						{
							updateError("Error modificando el cargo de usuario: "+err+ " data:"+data);
						}
					});
				},
				create: function(e)
				{
					$.post(path+"cargosusuario/new",e.data.models[0],function(data)
					{
						try
						{
							var json = eval("("+data+")");
							if(json.ok)
							{
								e.data.models[0].id = json.id;
								e.success(e.data.models[0]);
							}
							else
								updateError(json.msg);
						}
						catch(err)
						{
							updateError("Error creando el cargo de usuario: "+err+ " data:"+data);
						}
					});
				},
				parameterMap: function(options, operation) 
				{
					if (operation !== "read" && options.models) {
						return {models: kendo.stringify(options.models)};
					}
				}
			},
			schema: {
				model: {
					id: "id",
					fields: {
						id: { editable: false, nullable: true },
						cargo: { validation: { required: true } }
					}
				}
			},
			error:function(e)
			{
				updateError(e.xhr.responseText);
			},
			batch:true,
			pageSize: 20
		}),
		toolbar: ["create"],
		pageable:
		{
			refresh: true,
			pageSizes: true,
			buttonCount: 5
		},
		height:400,
		columns: [
			{ field: "cargo",title:'Cargo'},
			{ command: ["edit"], title: "Acciones", width: 90 },
		],
		editable: "popup"
	});
	useBoxMessage = true;
}
$(document).ready(complete);