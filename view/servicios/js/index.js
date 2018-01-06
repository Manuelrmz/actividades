var bandModificar = false;
var currentId = null;
var deteledEquip = [];
function complete()
{
  $("#solicitante").kendoComboBox({placeholder:"Seleccione un solicitante",dataTextField:'text',dataValueField:'value',change:validateSolicitante})
  $("#solicitante").data("kendoComboBox").value("");
  $("#estado").kendoComboBox({placeholder:"Seleccione un estado"})
  $("#estado").data("kendoComboBox").value("");
  $("#tipoServicio").kendoComboBox({placeholder:"Seleccione un servicio",dataTextField:'text',dataValueField:'value',change:validateServicio})
  $("#tipoServicio").data("kendoComboBox").value("");
  $("#usuarioAsignado").kendoComboBox({placeholder:"Seleccione al encargado",dataTextField:'text',dataValueField:'value'})
  $("#usuarioAsignado").data("kendoComboBox").value("");
  $("#area").kendoComboBox({placeholder:"Seleccione un area",dataTextField:'text',dataValueField:'value',change:validateArea})
  $("#area").data("kendoComboBox").value("");
  $("#fechaInicio").kendoDateTimePicker({value:new Date(),dateInput:true});
  $("#areaBus").kendoComboBox({placeholder:"Seleccione un area",dataTextField:'text',dataValueField:'value',change:validateArea});
  $("#areaBus").data("kendoComboBox").value("");
  $("#solicitanteBus").kendoComboBox({placeholder:"Seleccione un solicitante",dataTextField:'text',dataValueField:'value',change:validateSolicitante});
  $("#solicitanteBus").data("kendoComboBox").value("");
  $("#estadoBus").kendoComboBox({placeholder:"Seleccione un estado"});
  $("#estadoBus").data("kendoComboBox").value("");
  $("#tablaServicios").kendoGrid(
	{
    dataSource: new kendo.data.DataSource(
    {
      schema: {
        model: {
          id: "folio",
          fields: {
            folio: {type: "number", editable: false, nullable: true },
            currentId: {validation: { required: true } },
            solicitante: { validation: { required: true } },
            tipoServicio: { validation: { required: true } },
            usuarioAsignado: { validation: { required: true } },
            fechaInicio: { validation: { required: true } },
            estado: { validation: { required: true } },
            usuarioAlta: { validation: { required: true } },
            area: { validation: { required: true } }
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
		height: 512,
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
    	{ field: "currentId",title:'Folio'},
    	{ field: "solicitante",title:'Solicitante'},
      { field: "tipoServicio",title:'Servicio Solicitado'},
      { field: "usuarioAsignado",title:'Personal'},
      { field: "fechaInicio",title:'Fecha de Inicio'},
      { field: "estado",title:'Estado',template:function(dataItem)
      {
      	valor = "";
      	if(dataItem.estado == 1)
      		valor = "Pendiente"
        else if(dataItem.estado == 2)
          valor = "Proceso"
        else if(dataItem.estado == 3)
          valor = "Finalizado"
        else
          valor = "Desconocido"
      	return valor;
      }},
      { field: "area",title:'Area'}
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
                    marca: { validation: { required: true } },
                    descripcion: { validation: { required: true } },
                    noSerie: { validation: { required: true } },
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
    	{ field: "marca",title:'Marca'},
    	{ field: "descripcion",title:'Descripcion'},
      { field: "noSerie",title:'# de serie'},
      { command: "destroy", title: "&nbsp;", width: 150 },
	  ],
    remove:removeEquip,
    editable: true
  });
  $("#formSolicitante").submit(saveSolicitante);
  $("#formTipoServicio").submit(saveTipoServicio);
  $("#formServicio").submit(saveServicio);
  $("#formBusqueda").submit(loadTable);
  $("#btnCleanFields").click(clearFieldsServicio);
  $("#btnPrint").click(printOrden);
  getAreas();
  getSolicitantes();
  getPersonalActivo();
  $("#formBusqueda").trigger("submit");
  useBoxMessage = true;
}
function printOrden()
{
  if(!isNaN(currentId))
    window.open(path+'servicios/getserviceorder/'+currentId);
}
function removeEquip(e)
{
  if(bandModificar && e.model.idEquipo !== null)
    deteledEquip[deteledEquip.length] = e.model.idEquipo;
}
function tableEvent()
{
  $("#tablaServicios tbody tr").click(function(e)
    {
        var dataItem = $("#tablaServicios").data("kendoGrid").dataItem("tr[data-uid='"+$(e.currentTarget).closest("tr").data('uid')+"'");
        $.post(path+'servicios/getbyid',{id:dataItem.folio},function(data)
        {
          try
          {
            var json = eval("("+data+")");
            if(json.ok)
            {
              currentId = dataItem.folio;
              bandModificar = true;
              $("#title").html("Modificando Servicio: "+dataItem.currentId);
              $("#btnCleanFields").show();
              $("#btnPrint").show();
              $("#fechaInicio").data("kendoDateTimePicker").value(kendo.parseDate(json.msg.fechaInicio));
              $("#solicitante").data("kendoComboBox").value(json.msg.solicitante);
              buscarDatosPersona(json.msg.solicitante);
              $("#area").data("kendoComboBox").value(json.msg.area);
              getTipoServicios(json.msg.tipoServicio)
              $("#estado").data("kendoComboBox").value(json.msg.estado);
              $("#usuarioAsignado").data("kendoComboBox").value(json.msg.usuarioAsignado);
              $("#detalles").val(json.msg.detalles);
              $("#observacion").val(json.msg.observacion);
              $("#tablaEquipos").data("kendoGrid").dataSource.data(json.msg.equipos);
            }
            else
              updateError(json.msg);
          }
          catch(err)
          {
            updateError("Error cargando la visita: "+err+ " data:"+data);
          }
        });
    });
}
function saveServicio(e)
{
  e.preventDefault();
  var condi = true;
  condi = condi && validarFechaHora(kendo.toString($("#fechaInicio").data("kendoDateTimePicker").value(),"yyyy-MM-dd HH:mm:ss"),"Seleccione una fecha y hora correcta");
  condi = condi && validarComboBox($("#solicitante option:selected"),undefined,"Seleccione un solicitante de la lista");
  condi = condi && validarComboBox($("#tipoServicio option:selected"),undefined,"Seleccione un tipo de servicio de la lista");
  condi = condi && validarComboBox($("#usuarioAsignado option:selected"),undefined,"Seleccione un usuario al servicio");
  if(bandModificar)
    condi = condi && validarComboBox($("#estado option:selected"),undefined,"Seleccione el estado actual del servicio");
  condi = condi && validarNoVacio($("#detalles").val(),"Detalles");
  if(bandModificar)
    condi = condi && confirm("¿Desea modificar el servicio seleccionado?");
  if(condi)
  {
    var dataSend = new FormData(this);
    dataSend.set("fechaInicio",kendo.toString($("#fechaInicio").data("kendoDateTimePicker").value(),"yyyy-MM-dd HH:mm:ss"));
    dataSend.set("equipos",JSON.stringify($("#tablaEquipos").data("kendoGrid").dataSource.data()));
    if(bandModificar)
    {
      dataSend.set("deletedEquip",deteledEquip);
      dataSend.set("id",currentId);
    }
		$.ajax(
		{
			url:path+(bandModificar ? 'servicios/updatebyid' :'servicios/add'),
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
            $("#formBusqueda").trigger("submit");
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
function loadTable(e)
{
  e.preventDefault();
  var condi = true;
  if($("#fechaBus").val() !== "")
    condi = condi && validarFecha($("#fechaBus").val(),"Seleccione una fecha de inicio correcta para la busqueda");
  if(condi)
  {
    var dataSend = new FormData(this);
    $.ajax(
    {
      url:path+'servicios/gettable',
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
    			   $("#tablaServicios").data("kendoGrid").dataSource.data(json.msg);
          else
          {
            $("#tablaServicios").data("kendoGrid").dataSource.data([]);
            updateError(json.msg);
          }
    		}
    		catch(err)
    		{
    			updateError("Ocurrio un error cargando la tabla: "+err+ " data:"+data);
    		}
      },
      error: function(data)
      {
        updateError('Error: '+data);
      },
      fail: function(data) {
        updateError('Error: '+data);
      }
    });
  }
}
function saveSolicitante(e)
{
  e.preventDefault();
  var condi = true;
  condi = condi && validarTamanio($("#soliNombre").val(),"Nombre",10,200);
  if(condi)
  {
    var dataSend = new FormData(this);
		$.ajax(
		{
			url:path+'solicitantes/add',
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
            getSolicitantes(json.id);
            $("#ttSolicitante").html("Cargo: "+$("#soliCargo").val()+"<br/>Area: "+$("#soliArea").val()+"</br>Edificio: "+$("#soliEdificio").val()+"<br/>Tel/Ext: "+$("#soliTelefono").val()+"/"+$("#soliExtension").val()+"</br>");
						$("#divModal").data('kendoWindow').close();
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
function saveTipoServicio(e)
{
  e.preventDefault();
  var condi = true;
  condi = condi && validarTamanio($("#servClave").val(),"Clave",1,15);
  condi = condi && validarTamanio($("#servNombre").val(),"Nombre",1,150);
  condi = condi && validarComboBox($("#area option:selected"),undefined,"Seleccione un area");

  if(condi)
  {
    var dataSend = new FormData(this);
    dataSend.set("area",$("#area").data("kendoComboBox").value());
		$.ajax(
		{
			url:path+'catservicios/add',
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
            getTipoServicios(json.id);
						$("#divModal").data('kendoWindow').close();
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
function openModal()
{
  if($("#divModal").data('kendoWindow')!=undefined)
	{
		$("#divModal").data('kendoWindow').open();
		$("#divModal").data('kendoWindow').center();
	}
	else
	{
		$("#divModal").show();
		$("#divModal").kendoWindow(
		{
			actions: ["Maximize", "Minimize", "Close"],
			draggable: true,
			height: "auto",
			width:"400px",
			modal: true,
			resizable: false,
			title: "",
      close:clearFieldsModal
		});
		$("#divModal").data('kendoWindow').center();
	}
}
function showFormSolicitante()
{
  openModal();
  $("#modalServicio").hide();
  $("#modalSolicitante").show();
}
function showFormServicio()
{
  openModal();
  $("#modalServicio").show();
  $("#modalSolicitante").hide();
}
function validateServicio()
{
  var value = this.value().replace(/\s/g,"");
	var text = this.text().replace(/\s/g,"");
	if(text != "")
	{
		if(value == text)
		{
			if(confirm('El servicio elegido no existe, tiene que agregarlo para poder guardar el servicio, ¿Desea agregarlo?'))
				showFormServicio();
      else
        $("#servicio").data("kendoComboBox").value("");
		}
	}
}
function validateSolicitante()
{
  var value = this.value().replace(/\s/g,"");
	var text = this.text().replace(/\s/g,"");
  $("#ttSolicitante").html("");
	if(text != "")
	{
		if(value == text)
		{
			if(confirm('La persona elegida no se encuentra en la base de datos, tiene que agregarla para poder guardar el servicio, ¿Desea agregarla?'))
				showFormSolicitante();
      else
        $("#solicitante").data("kendoComboBox").value("");
		}
		else
			buscarDatosPersona(value);
	}
}
function validateArea()
{
  var value = this.value().replace(/\s/g,"");
	var text = this.text().replace(/\s/g,"");
  $("#tipoServicio").data("kendoComboBox").setDataSource([]);
	if(text != "")
	{
		if(value != text)
      getTipoServicios();
	}
}
function buscarDatosPersona(id)
{
  if(validarEntero(id,"El valor enviado para la busqueda del solicitante no fue valido, intentelo nuevamente"))
  {
    $.post(path+'solicitantes/getbyid',{id:id},function(data)
    {
      try
      {
        var json = eval("("+data+")")
        if(json.ok)
        {
          $("#ttSolicitante").html("Cargo: "+json.msg.cargo+"<br/>Area: "+json.msg.area+"</br>Edificio: "+json.msg.edificio+"<br/>Tel/Ext: "+json.msg.telefono.toString()+"/"+json.msg.extension.toString()+"</br>");
        }
        else
        {
          updateError(json.msg);
          $("#solicitante").data("kendoComboBox").value("");
        }
      } catch (e) {
        updateError("Data: "+data+" Error:"+e);
      }
    });
  }
  else
    $("#solicitante").data("kendoComboBox").value("");
}
function clearFieldsServicio()
{
  $("#title").html("Nuevo Servicio");
  $("#fechaInicio").data("kendoDateTimePicker").value(new Date());
  $("#solicitante").data("kendoComboBox").value("");
  $("#area").data("kendoComboBox").value("");
  $("#tipoServicio").data("kendoComboBox").value("");
  $("#estado").data("kendoComboBox").value("");
  $("#usuarioAsignado").data("kendoComboBox").value("");
  $("#imgDetails").val("");
  $("#detalles").val("");
  $("#imgObservaciones").val("");
  $("#observacion").val("");
  $("#tablaEquipos").data("kendoGrid").dataSource.data([]);
  $("#btnCleanFields").hide();
  $("#btnPrint").hide();
  bandModificar = false;
  currentId = null;
  deteledEquip = [];
}
function clearFieldsModal()
{
  $("#soliNombre").val("");
  $("#soliDependencia").val("");
  $("#soliEdificio").val("");
  $("#soliCargo").val("");
  $("#soliArea").val("");
  $("#soliTelefono").val("");
  $("#soliExtension").val("");
  $("#servClave").val("");
  $("#servNombre").val("");
  $("#servDescripcion").val("");
}
function getSolicitantes(id)
{
  $.post(path+'solicitantes/getcombo',function(data)
  {
    try
    {
      $("#solicitante").data("kendoComboBox").setDataSource(eval("("+data+")"));
      $("#solicitanteBus").data("kendoComboBox").setDataSource(eval("("+data+")"));
      if (id !== undefined && !isNaN(id))
        $("#solicitante").data("kendoComboBox").value(id);
    }
    catch (e)
    {
      updateError("Data: "+data+" Error:"+e);
    }
  });
}
function getTipoServicios(id)
{
  $.post(path+'catservicios/getcombobyarea',{area:$("#area").data("kendoComboBox").value()},function(data)
  {
    try
    {
      $("#tipoServicio").data("kendoComboBox").setDataSource(eval("("+data+")"));
      if (id !== undefined && !isNaN(id))
        $("#tipoServicio").data("kendoComboBox").value(id);
      else
        $("#tipoServicio").data("kendoComboBox").value("");
    }
    catch (e)
    {
      updateError("Data: "+data+" Error:"+e);
    }
  });
}
function getPersonalActivo()
{
  $.post(path+'usuarios/getactivebyarea',function(data)
  {
    try
    {
      var json = eval("("+data+")");
      if(json.ok)
        $("#usuarioAsignado").data("kendoComboBox").setDataSource(json.msg);
    }
    catch (e)
    {
      updateError("Data: "+data+" Error:"+e);
    }
  });
}
function getAreas()
{
  $.post(path+'areas/operativas',function(data)
  {
    try
    {
      $("#area").data("kendoComboBox").setDataSource(eval("("+data+")"));
      $("#areaBus").data("kendoComboBox").setDataSource(eval("("+data+")"));
    }
    catch (e)
    {
      updateError("Data: "+data+" Error:"+e);
    }
  });
}
$(document).ready(complete);
