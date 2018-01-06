var deletedPrograma = [];
var bandMod = false;
function complete()
{
  // $("#anio").kendoComboBox({placeholder:"Seleccione un año",dataTextField:'text',dataValueField:'value',change:validateEjercicio});
  // $("#anio").data("kendoComboBox").value("");
  $("#gridProveedores").kendoGrid(
	{
    dataSource: new kendo.data.DataSource(
    {
      transport:{
        read: function(e)
        {
          $.post(path+"proveedores/getfortable",function(data)
          {
            try
            {
              var json = eval("("+data+")");
              if(json.ok)
                e.success(json.msg);
              else
                updateError(json.msg);
            }
            catch(err)
            {
              updateError("Error cargando los proveedores: "+err+ " data:"+data);
            }
          });
        },
        create: function(e)
        {
          $.post(path+"proveedores/add",e.data.models[0],function(data)
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
              updateError("Error creando el proveedor: "+err+ " data:"+data);
            }
          });
        },
        update: function(e)
        {
          $.post(path+"proveedores/update",e.data.models[0],function(data)
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
              updateError("Error modificando el proveedor: "+err+ " data:"+data);
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
            id: {type: "number", editable: false, nullable: true },
            rfc: {validation: { required: true } },
            nombreEmpresa: { validation: { required: true } },
            direccion: { validation: { required: true } },
            cp: { validation: { required: true } },
            activo: {type:"boolean"}
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
		height: 400,
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
    	{ field: "rfc",title:'RFC',width:115},
      { field: "nombreEmpresa",title:'Nombre de la Empresa',width:138},
      { field: "direccion",title:'Direccion',width:120},
      { field: "cp",title:'Codigo Postal',width:100},
      { field: "activo",title:'Status',width:90,template:function(e)
      {
        return e.activo ? "Activo" : "Inactivo";
      }},
      { command: ["edit"], title: "Acciones", width:90 }
	  ],
    editable: "popup"
  });
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
              if(json.ok)
                e.success(json.msg);
              else
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
  $("#gridProgramas").kendoGrid(
	{
    dataSource: new kendo.data.DataSource(
      {
        pageSize: 20,
        schema: {
            model: {
                id: "idPrograma",
                fields: {
                    idPrograma: { editable: false, nullable: true },
                    nombre: { validation: { required: true } },
                    descripcion: { validation: { required: false } }
                }
            }
        }
      }
    ),
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
      { command: "destroy", title: "&nbsp;", width: 150 },
	  ],
    remove:removePrograma,
    editable: true
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
  // $("#btnGuardar").click(saveEjercicio);
  // $("#btnCleanFields").click(clearFieldsEjercicio);
  // getEjercicios();
}
function saveEjercicio()
{
  var condi = true;
  condi = condi && validarEntero($("#anio").data("kendoComboBox").text(),"El campo año debe ser numerico");
  condi = condi && validarTamanio($("#anio").data("kendoComboBox").text(),"Año",4,4);
  if(bandMod)
    condi = condi && confirm("¿Realmente desea modificar el ejercicio?");
  if(condi)
  {
    var dataSend = new FormData();
    dataSend.set("id",$("#anio").data("kendoComboBox").value());
    dataSend.set("anio",$("#anio").data("kendoComboBox").text());
    dataSend.set("programas",JSON.stringify($("#gridProgramas").data("kendoGrid").dataSource.data()));
    if(bandMod)
      dataSend.set("deletedPrograma",deletedPrograma);
    $.ajax(
    {
      url:path+'ejercicio/'+(bandMod ? 'update' : 'new'),
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
            getEjercicios();
            clearFieldsEjercicio();
            showSuccessBox(json.msg);
          }
          else
            updateError(json.msg)
        }
        catch(err)
        {
          updateError("Error: "+err+ "\nData: "+data);
        }
      },
      error: function(data)
      {
        updateError('Error: '+data);
      }
    });
  }
}
function removePrograma(e)
{
    if(e.model.idPrograma !== null)
      deletedPrograma[deletedPrograma.length] = e.model.idPrograma;
}
function clearFieldsEjercicio()
{
  $("#anio").data("kendoComboBox").value("");
  $("#gridProgramas").data("kendoGrid").dataSource.data([]);
  $("#divProgramas").hide();
  deletedPrograma = [];
  bandMod = false;
}
function validateEjercicio()
{
  var condi = true;
  condi = condi && validarEntero($("#anio").data("kendoComboBox").text(),"El campo año debe ser numerico");
  condi = condi && validarTamanio($("#anio").data("kendoComboBox").text(),"Año",4,4);
  if(condi)
  {
    $("#divProgramas").show();
    if($("#anio").data("kendoComboBox").text().replace(/\s/g,"") !== $("#anio").data("kendoComboBox").value().replace(/\s/g,""))
    {
      $.post(path+'programa/getbyejercicio/'+$("#anio").data("kendoComboBox").value(),function(data)
      {
        try
    		{
          var json = eval("("+data+")");
          $("#gridProgramas").data("kendoGrid").dataSource.data(json.ok ? json.msg : []);
          bandMod = true;
          if(!json.ok)
            updateError(json.msg);
    		}
    		catch(err)
    		{
    			updateError("Error: "+err+ "\nData:"+data);
    		}
      });
    }
    else
      $("#gridProgramas").data("kendoGrid").dataSource.data([]);
  }
  else
  {
    if(bandMod)
      clearFieldsEjercicio();
  }
}
function getEjercicios()
{
  $.post(path+'ejercicio/getforcombo',function(data)
  {
    try
    {
      var json = eval("("+data+")");
      if(json.ok)
        $("#anio").data("kendoComboBox").setDataSource(json.msg);
      else
        updateError(json.msg);
    }
    catch (e)
    {
      updateError("Error: "+e+"\nData:"+data)
    }
  });
}
$(document).ready(complete);
