var deletedPrograma = [];
var bandMod = false
function complete()
{
  $("#anio").kendoComboBox({placeholder:"Seleccione un año",dataTextField:'text',dataValueField:'value',change:validateEjercicio});
  $("#anio").data("kendoComboBox").value("");
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
  useBoxMessage = true;
  $("#btnGuardar").click(saveEjercicio);
  $("#btnCleanFields").click(clearFieldsEjercicio);
  getEjercicios();
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
