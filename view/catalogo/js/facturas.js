function complete()
{
  $("#gridCatalogo").kendoGrid(
	{
    dataSource:new kendo.data.DataSource(
    {
      transport:{
        read: function(e)
        {
          $.post(path+"catinventario/getfortable",function(data)
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
              updateError("Error cargando las categorias de los equipos: "+err+ " data:"+data);
            }
          });
        },
        update: function(e)
        {
          $.post(path+"catinventario/update",e.data.models[0],function(data)
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
              updateError("Error modificando la categoria: "+err+ " data:"+data);
            }
          });
        },
        create: function(e)
        {
          $.post(path+"catinventario/new",e.data.models[0],function(data)
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
              updateError("Error creando la nueva categoria del inventario: "+err+ " data:"+data);
            }
          });
        },
        parameterMap: function(options, operation) {
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
                  nombre: { validation: { required: true } },
                  estado: { type:"boolean",validation: { required: false } },
                  descripcion: { validation: { required: false } }
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
    	{ field: "nombre",title:'Nombre'},
    	{ field: "descripcion",title:'Descripcion'},
      { field: "estado", title:'Estado',template:function(e)
      {
        return e.estado ? "Activo" : "Inactivo";
      }},
      { command: ["edit"], title: "Acciones", width: 90 },
	  ],
    editable: "popup"
  });
  $("#gridEjercicios").kendoGrid({
    dataSource:new kendo.data.DataSource(
    {
      transport:
      {
        read: function(e)
        {
          $.post(path+"ejercicio/getfortable",function(data)
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
              updateError("Error cargando los ejercicios: "+err+ " data:"+data);
            }
          });
        },
        update: function(e)
        {
          $.post(path+"ejercicio/update",e.data.models[0],function(data)
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
              updateError("Error modificando el ejercicio: "+err+ " data:"+data);
            }
          });
        },
        create: function(e)
        {
          $.post(path+"ejercicio/new",e.data.models[0],function(data)
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
              updateError("Error creando el nuevo ejercicio: "+err+ " data:"+data);
            }
          });
        },
        parameterMap: function(options, operation) {
            if (operation !== "read" && options.models) {
                return {models: kendo.stringify(options.models)};
            }
        }
      },
      schema:
      {
        model:
        {
          id: "id",
          fields:
          {
            id: { editable: false, nullable: true },
            anio: { validation: { required: true } }
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
    detailTemplate: kendo.template("<div class='gridProducts'></div>"),
    detailInit: function(ev)
    {
      var idEjercicio = ev.data.id;
      ev.detailRow.find(".gridProducts").kendoGrid(
      {
        dataSource: new kendo.data.DataSource(
        {
          transport:
          {
            read: function(e)
            {
              $.post(path+'programa/getbyejercicio/'+idEjercicio,function(data)
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
                  updateError("Error cargando los programas: "+err+ " data:"+data);
                }
              });
            },
            update: function(e)
            {
              e.data.models[0].idejercicio = idEjercicio;
              $.post(path+"programa/update",e.data.models[0],function(data)
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
                  updateError("Error modificando el programa: "+err+ " data:"+data);
                }
              });
            },
            create: function(e)
            {
              e.data.models[0].idejercicio = idEjercicio;
              $.post(path+"programa/new",e.data.models[0],function(data)
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
                  updateError("Error creando el nuevo programa: "+err+ " data:"+data);
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
          schema:
          {
            model:
            {
              id: "id",
                fields:
                {
                  id: { editable: false, nullable: true },
                  idejercicio: {validation:false},
                  nombre: { validation: { required: true } },
                  descripcion: { validation: { required: false } }
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
    		height:374,
        columns: [
        	{ field: "nombre",title:'Nombre'},
        	{ field: "descripcion",title:'Descripcion'},
          { command: ["edit"], title: "Acciones", width: 90 },
    	  ],
        editable: "popup"
        });
    },
    columns: [
    	{ field: "anio",title:'Año'},
      { command: ["edit"], title: "Acciones", width: 90 },
	  ],
    editable: "popup"
  }
  );
  useBoxMessage = true;
}
$(document).ready(complete);
