var bandModificar = false;
var deletedPhones = [];
var currentId = null;
function complete()
{
  $("#activo").kendoComboBox({placeholder:"Seleccione el status"});
  $("#activo").data("kendoComboBox").value("");
  $("#gridProveedores").kendoGrid(
	{
    dataSource: new kendo.data.DataSource(
    {
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
      pageSize: 20
    }),
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
      }}
	  ],
    dataBound:tableEvent
  });
  $("#gridPhones").kendoGrid(
  {
    dataSource: new kendo.data.DataSource(
      {
        pageSize: 20,
        schema: {
            model: {
                id: "idPhone",
                fields: {
                    idPhone: { editable: false, nullable: true },
                    numero: { validation: { required: true } },
                    tipo: { validation: { required: true } }
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
    height:220,
    columns: [
      { field: "numero",title:'Numero'},
      { field: "tipo",title:'Tipo'},
      { command: "destroy", title: "&nbsp;", width: 150 },
    ],
    remove:removePhone,
    editable: true
  });
  useBoxMessage = true;
  loadTable();
  $("#formProveedores").submit(saveProveedor);
  $("#btnCleanFields").click(cleanFieldsProveedor);
}
function removePhone(e)
{
  if(bandModificar && e.model.idPhone !== null)
    deletedPhones[deletedPhones.length] = e.model.idPhone;
}
function saveProveedor(e)
{
  e.preventDefault();
  var condi = true;
  condi = condi && validarTamanio($("#rfc").val(),"RFC",12,13);
  condi = condi && validarNoVacio($("#nombreEmpresa").val(),"Nombre de la Empresa");
  if(bandModificar)
    condi = condi && confirm("¿Realmente desea modificar el proveedor?");
  if(condi)
  {
    var dataSend = new FormData(this);
    dataSend.set("phones",JSON.stringify($("#gridPhones").data("kendoGrid").dataSource.data()));
    if(bandModificar)
    {
      dataSend.append("deletedPhones",deletedPhones);
      dataSend.append("id",currentId);
    }
    $.ajax(
    {
      url:path+(bandModificar ? 'proveedores/update' :'proveedores/add'),
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
            cleanFieldsProveedor();
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
function tableEvent()
{
  $("#gridProveedores tbody tr").click(function(e)
    {
        var dataItem = $("#gridProveedores").data("kendoGrid").dataItem("tr[data-uid='"+$(e.currentTarget).closest("tr").data('uid')+"'");
        $.post(path+'proveedores/getbyid/'+dataItem.id,function(data)
        {
          try
          {
            var json = eval("("+data+")");
            if(json.ok)
            {
              cleanFieldsProveedor();
              $("#titleForm").html("Modificando Proveedor: "+dataItem.rfc);
              bandModificar = true;
              currentId = dataItem.id;
              $("#rfc").val(json.msg.rfc);
              $("#nombreEmpresa").val(json.msg.nombreEmpresa);
              $("#activo").data("kendoComboBox").value(json.msg.activo);
              $("#direccion").val(json.msg.direccion);
              $("#cp").val(json.msg.cp);
              $("#gridPhones").data("kendoGrid").dataSource.data(json.msg.phones);
            }
            else
              updateError(json.msg);
          }
          catch(err)
          {
            updateError("Error cargando el proveedor: "+err+ " data:"+data);
          }
        });
    });
}
function cleanFieldsProveedor()
{
  deletedPhones = [];
  bandModificar = false;
  currentId = null;
  $("#titleForm").html("Nuevo Proveedor");
  $("#rfc").val("");
  $("#nombreEmpresa").val("");
  $("#activo").data("kendoComboBox").value("");
  $("#direccion").val("");
  $("#cp").val("");
  $("#gridPhones").data("kendoGrid").dataSource.data([]);
}
function loadTable()
{
  $.post(path+"proveedores/getfortable",function(data)
  {
    try
    {
      var json = eval("("+data+")");
      $("#gridProveedores").data("kendoGrid").dataSource.data(json.ok ? json.msg : []);
      if(!json.ok)
        updateError(json.msg);
    }
    catch(err)
    {
      updateError("Error cargando los proveedores: "+err+ " data:"+data);
    }
  });
}
$(document).ready(complete);