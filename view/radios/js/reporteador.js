function complete()
{
	$("#tipoReporte").kendoComboBox({placeholder:"Seleccione un tipo de Reporte"});
	$("#tipoReporte").data("kendoComboBox").value("");
	$("#formReporteador").submit(generarReporte);
}
function generarReporte(e)
{
	e.preventDefault();
	var condi = true;
	condi = condi && validarComboBox($("#tipoReporte option:selected"),$("#aviso"),"Seleccione un tipo de reporte correcto");
	condi = condi && validarFecha($("#fechaInicio").val(),"La fecha de inicio no tiene un formato correcto",$("#aviso"));
	condi = condi && validarFecha($("#fechaFin").val(),"La fecha final no tiene un formato correcto",$("#aviso"));
	if(condi)
	{
		var dataSend = new FormData(this);
		$.ajax(
		{
			url:path+'reporteador/senddata',
			type:"POST",
			data:dataSend,
			processData:false,
			contentType:false,
			success: function(data)
			{
				try
				{
					console.log(data);
					var json = eval("("+data+")");
					if(json.ok)
					{
						window.open(path+'reporteador/generar');
					}
					else
						updateError("No fue posible enviar la informacion del reporte.",$("#aviso"))
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
$(document).ready(complete);