var bandModificar = false;
var currentId = null;
var deletedEquip = [];
var deletedService = [];
var deletedAire = [];
function complete()
{
  $("#municipio").kendoComboBox({placeholder:"Seleccione un municipio",dataTextField:'text',dataValueField:'value'});
  $("#municipio").data("kendoComboBox").value("");
  $("#tablaSitios").kendoGrid(
	{
    dataSource: new kendo.data.DataSource(
    {
      schema: {
        model: {
          id: "folio",
          fields: {
            folio: {type: "number", editable: false, nullable: true },
            nombre: { validation: { required: true } },
            direccion: { validation: { required: true } },
            municipio: { validation: { required: true } },
            propietario: { validation: { required: true } },
            tipoTorre: { validation: { required: true } },
            cfeServicio: { validation: { required: true } },
          }
        }
      },
      pageSize:30
    }),
		pageable:{refresh: true,pageSizes: true,buttonCount: 5},
		height: 400,
    sortable: true,
    selectable: true,
    resizable: true,
    filterable:
    {
    	messages:{info:"Mostrar registros que...",filter:"Aplicar",clear:"Limpiar"},
    	extra:false,
    	operators:
    	{
    		string:{contains:"Contenga...",startswith:"Empieze con...",eq:"Sea igual que..."}
    	}
    },
    columnMenu:
    {
    	sortable:false,
    	filterable:true,
    	columns:true,
    	messages:{columns:"Columnas",filter:"Busqueda"}
    },
    columns: [
    	{ field: "nombre",title:'Nombre'},
      { field: "direccion",title:'Direccion'},
      { field: "municipio",title:'Municipio'},
      { field: "propietario",title:'Propietario'},
      { field: "tipoTorre",title:'tipoTorre'},
      { field: "cfeServicio",title:'CFE # Servicio'}
	  ],
    dataBound:tableEvent
  });
  $("#tablaEquipos").kendoGrid(
  {
    dataSource: new kendo.data.DataSource(
      {
        pageSize: 20,
        schema: {
            model: {
                id: "idEquipo",
                fields: {
                    idEquipo: { editable: false, nullable: true },
                    nombre: { validation: { required: true } },
                    modelo: { validation: { required: true } },
                    noSerie: { validation: { required: true } },
                    ip: { validation: { required: true } },
                    comentarios: { validation: { required: true } },
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
    height:180,
    columns: [
      { field: "nombre",title:'Equipo'},
      { field: "modelo",title:'Modelo'},
      { field: "noSerie",title:'# de serie'},
      { field: "ip",title:'IP'},
      { field: "comentarios",title:'Comentarios'},
      { command: "destroy", title: "&nbsp;", width: 150 },
    ],
    remove:removeEquip,
    editable: true
  });
  $("#tablaAires").kendoGrid(
  {
    dataSource: new kendo.data.DataSource(
      {
        pageSize: 20,
        schema: {
          model: {
              id: "idAire",
              fields: {
                idAire: { editable: false, nullable: true },
                tipo: { validation: { required: true } },
                capacidad: { validation: { required: true } }
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
    height:180,
    columns: [
      { field: "tipo",title:'Tipo'},
      { field: "capacidad",title:'Capacidad'},
      { command: "destroy", title: "&nbsp;", width: 150 },
    ],
    remove:removeAire,
    editable: true
  });
  $("#tablaServicios").kendoGrid(
  {
    dataSource: new kendo.data.DataSource(
      {
        pageSize: 20,
        schema: {
            model: {
                id: "idServicio",
                fields: {
                    idServicio: { editable: false, nullable: true },
                    nombreProveedor: { validation: { required: true } },
                    tipoServicio: { validation: { required: true } },
                    noServicio: { validation: { required: true } },
                    comentarios: { validation: { required: true } },
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
    height:180,
    columns: [
      { field: "nombreProveedor",title:'Nombre del proveedor'},
      { field: "tipoServicio",title:'Tipo de Servicio'},
      { field: "noServicio",title:'# de Servicio'},
      { field: "comentarios",title:'Comentarios'},
      { command: "destroy", title: "&nbsp;", width: 150 },
    ],
    remove:removeService,
    editable: true
  });
  getMunicipios();
  useBoxMessage = true;
  $("#formSitio").submit(saveSitio);
  $("#btnCleanFields").click(clearFieldsServicio);
  $("#btnAlbum").click(showAlbum)
  $("#slideshow").slideShow('init');
  loadTable();
}
function saveSitio(e)
{
  e.preventDefault();
  var condi = true;
  condi = condi && validarTamanio($("#nombre").val(),"Detalles",1,100);
  condi = condi && validarComboBox($("#municipio option:selected"),undefined,"Seleccione un municipio de la lista");
  if(bandModificar)
    condi = condi && confirm("¿Desea modificar el sitio seleccionado?");
  if(condi)
  {
    var dataSend = new FormData(this);
    dataSend.set("aires",JSON.stringify($("#tablaAires").data("kendoGrid").dataSource.data()));
    dataSend.set("servicios",JSON.stringify($("#tablaServicios").data("kendoGrid").dataSource.data()));
    dataSend.set("equipos",JSON.stringify($("#tablaEquipos").data("kendoGrid").dataSource.data()));
    dataSend.set("transPropio",$("#transPropio").prop('checked') ? 1 : 0);
    if(bandModificar)
    {
      dataSend.set("deletedEquip",deletedEquip);
      dataSend.set("deletedService",deletedService);
      dataSend.set("deletedAire",deletedAire);
      dataSend.set("id",currentId);
    }
		$.ajax(
		{
			url:path+(bandModificar ? 'sitios/updatebyid' :'sitios/add'),
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
            clearFieldsServicio();
            loadTable();
            showSuccessBox(json.msg);
					}
					else
	         updateError(json.msg)
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
function loadTable()
{
  $.post(path+'sitios/getfortable',function(data)
  {
    try
    {
      var json = eval("("+data+")");
      $("#tablaSitios").data("kendoGrid").dataSource.data(json.ok ? json.msg : []);
      if(!json.ok)
        updateError(json.msg);
    }
    catch(err)
    {
      updateError("Ocurrio un error cargando la tabla: "+err+ " data:"+data);
    }
  });
}
function removeEquip(e)
{
  if(bandModificar && e.model.idEquipo !== null)
    deletedEquip[deletedEquip.length] = e.model.idEquipo;
}
function removeService(e)
{
  if(bandModificar && e.model.idServicio !== null)
    deletedService[deletedService.length] = e.model.idServicio;
}
function removeAire(e)
{
  if(bandModificar && e.model.idAire !== null)
    deletedAire[deletedAire.length] = e.model.idAire;
}
function tableEvent()
{
  $("#tablaSitios tbody tr").click(function(e)
  {
    var dataItem = $("#tablaSitios").data("kendoGrid").dataItem("tr[data-uid='"+$(e.currentTarget).closest("tr").data('uid')+"'");
    $.post(path+'sitios/getbyid',{id:dataItem.folio},function(data)
    {
      try {
        var json = eval("("+data+")");
        if(json.ok)
        {
          clearFieldsServicio();
          bandModificar = true;
          currentId = dataItem.folio;
          $("#titleForm").html("Modificando Sitio: "+dataItem.nombre);
          $("#nombre").val(json.msg.nombre);
          $("#municipio").data("kendoComboBox").value(json.msg.municipio);
          $("#direccion").val(json.msg.direccion);
          $("#propietario").val(json.msg.propietario);
          $("#tipoTorre").val(json.msg.tipoTorre);
          $("#alturaTorre").val(json.msg.alturaTorre);
          $("#plantaEmergencia").val(json.msg.plantaEmergencia);
          $("#cfeServicio").val(json.msg.cfeServicio);
          $("#cfeMedidor").val(json.msg.cfeMedidor);
          $("#transPropio").prop("checked",json.msg.transPropio);
          $("#transCapacidad").val(json.msg.transCapacidad);
          $("#comentarios").val(json.msg.comentarios);
          $("#tablaEquipos").data("kendoGrid").dataSource.data(json.msg.equipos);
          $("#tablaAires").data("kendoGrid").dataSource.data(json.msg.aires);
          $("#tablaServicios").data("kendoGrid").dataSource.data(json.msg.servicios);
          if(json.msg.images !== undefined && json.msg.images.length > 0)
          {
            $("#btnAlbum").show();
            var images = new Array();
	        	for(var i = 0; i < json.msg.images.length; i ++)
	        	{
	        		images[images.length] = path+'private/sitios/'+json.msg.images[i].filename;
	        	}
	        	$("#btnAlbum").css('display','inline-block');
	        	$("#slideshow").slideShow('addImagesContent',images);
          }
        }
        else
          updateError(json.msg);
      } catch (err) {
        updateError("Error: "+err+ " Data: "+data);
      }
    });
  });
}
function showAlbum()
{
  if($("#album").data("kendoWindow")!=undefined)
  {
      $("#album").data("kendoWindow").open();
      $("#album").data("kendoWindow").center();
  }
  else
  {
      $("#album").show();
      $("#album").kendoWindow(
      {
          actions: ["Maximize", "Minimize", "Close"],
          draggable: true,
          height: "450",
          modal: true,
          resizable: false,
          width: "600",
          title:"Album"
      }).data("kendoWindow").center();
  }
}
function clearFieldsServicio()
{
  bandModificar = false;
  currentId = null;
  $("#titleForm").html("Nuevo Sitio");
  $("#nombre").val("");
  $("#municipio").data("kendoComboBox").value("");
  $("#direccion").val("");
  $("#propietario").val("");
  $("#tipoTorre").val("");
  $("#alturaTorre").val("");
  $("#plantaEmergencia").val("");
  $("#cfeServicio").val("");
  $("#cfeMedidor").val("");
  $("#transPropio").prop("checked",false);
  $("#transCapacidad").val("");
  $("#comentarios").val("");
  $("#imgSitio").val("");
  $("#tablaEquipos").data("kendoGrid").dataSource.data([]);
  $("#tablaAires").data("kendoGrid").dataSource.data([]);
  $("#tablaServicios").data("kendoGrid").dataSource.data([]);
  $("#btnAlbum").hide();
  deletedEquip = [];
  deletedService = [];
  deletedAire = [];
}
function getMunicipios()
{
  $.post(path+'municipios/getselect',function(data)
  {
    try
    {
      $("#municipio").data("kendoComboBox").setDataSource(eval("("+data+")"));
    }
    catch (e)
    {
      updateError("Data: "+data+" Error:"+e);
    }
  });
}
$(document).ready(complete);
