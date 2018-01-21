var equipList = null;
var bandConsult = false;
function complete()
{
	$("#resguardo").kendoComboBox({placeholder:"Seleccione el resguardo",dataTextField:"text",dataValueField:"value",change:restartEquipDiv});
	$("#resguardo").data("kendoComboBox").value("");
	$("#personal").kendoComboBox({placeholder:"Seleccione el personal",dataTextField:"text",dataValueField:"value"});
	$("#personal").data("kendoComboBox").value("");
	$("#equipos").kendoComboBox({placeholder:"Ingrese el numero de Serie",dataTextField:"text",dataValueField:"value"});
	$("#equipos").data("kendoComboBox").value("");
	$("#tipo").kendoComboBox({placeholder:"Seleccione el tipo de recibo",change:loadEquip});
	$("#tipo").data("kendoComboBox").value("");
	useBoxMessage = true;
	$("#btnCleanFields").click(cleanFieldsRecibo);
    $("#btnAddEquip").click(addEquipToGrid);
    $("#formRecibos").submit(saveRecibo);
	getPersonal();
	getResguardos();
	getCategoriesInventario();
}
function saveRecibo(e)
{
	e.preventDefault();
	if(!bandConsult)
	{
		var condi = true;
		condi = condi && validarComboBox($("#resguardo option:selected"),undefined,"Seleccione un resguardo");
		condi = condi && validarComboBox($("#personal option:selected"),undefined,"Seleccione el personal que recibe los equipos");
		condi = condi && validarComboBox($("#tipo option:selected"),undefined,"Seleccione el tipo de recibo");
		condi = condi && validarFecha($("#fechaEntrega").val(),"Ingrese una fecha de entrega correcta");
		condi = condi && validarTamanio($("#nombre").val(),"Nombre Solicitante",1,200);
		condi = condi && validarTamanio($("#dependencia").val(),"Dependencia Solicitante",1,150);
		if(condi && $("#gridEquipos").data("kendoGrid").dataSource.data().length == 0)
		{
			condi = false;
			updateError("Debe seleccionar al menos un equipo para asignar al resguardo");
		}
		if(condi)
		{
			var dataSend = new FormData(this);
			dataSend.append("idresguardo",$("#resguardo").data("kendoComboBox").value());
			dataSend.append("equipos",JSON.stringify($("#gridEquipos").data("kendoGrid").dataSource.data()));
			dataSend.append("personal",$("#personal").data("kendoComboBox").value());
			dataSend.append("tipo",$("#tipo").data("kendoComboBox").value());
			dataSend.append("status",($("#tipo").data("kendoComboBox").value() == 1 ? 0 : 1));
			$.ajax(
			{
				url:path+'recibos/new',
				type:"POST",
				data:dataSend,
				processData:false,
				contentType:false,
				success: function(data)
				{
					try
					{
						console.log(data);
						/*var json = eval("("+data+")");
						if(json.ok)
						{
							cleanFieldsResguardo();
							loadTable();
							showSuccessBox(json.msg);
						}
						else
							updateError(json.msg)*/
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
}
function restartEquipDiv()
{
	$("#divEquipos").hide();
	$(".equipsFields").hide();
	$("#gridEquipos").data("kendoGrid").dataSource.data([]);
	$("#tipo").data("kendoComboBox").value("");
}
function loadEquip()
{
	var condi = true;
	condi = condi && validarComboBox($("#resguardo option:selected"),undefined,"Seleccione un resguardo de la lista");
	condi = condi && validarComboBox($("#tipo option:selected"),undefined,"Seleccione un tipo de resguardo correcto");
	if(condi)
	{
		$.post(path+'resguardos/'+($("#tipo").data("kendoComboBox").value() == 1 ? "getequipfortablebyid" : "getequipforcomboactivebyid")+'/'+$("#resguardo").data("kendoComboBox").value(),function(data)
		{
			try
			{
				$("#divEquipos").hide();
				$(".equipsFields").hide();
				var json = eval("("+data+")");
				if(json.ok)
				{
					$("#divEquipos").show();
					if($("#tipo").data("kendoComboBox").value() == 1)
					{
						$(".equipsFields").hide();
						$("#gridEquipos").data("kendoGrid").dataSource.data(json.msg);
						$("#gridEquipos").data("kendoGrid").hideColumn(7);
					}
					else
					{
						$(".equipsFields").show();
						$("#gridEquipos").data("kendoGrid").dataSource.data([]);
						$("#gridEquipos").data("kendoGrid").showColumn(7);
						$("#equipos").data("kendoComboBox").setDataSource(json.ok ? json.msg : []);
	            		equipList = json.ok ? json.msg : null;
					}
				}
				else
					updateError(json.msg);
			}
			catch (e)
			{
				updateError("Data: "+data+" Error:"+e);
			}	
		});
	}
	else
		$("#tipo").data("kendoComboBox").value("");
}
function cleanFieldsRecibo()
{
	bandConsult = false;
	$("#nombre").val("");
	$("#dependencia").val("");
	$("#departamento").val("");
	$("#cargo").val("");
	$("#nota").val("");
	$("#personal").data("kendoComboBox").value("");
	$("#equipos").data("kendoComboBox").value("");
	$("#btnGuardar").show();
	$("#gridEquipos").data("kendoGrid").dataSource.data([]);
	$("#divEquipos").hide();
	$(".equipsFields").hide();
	$("#gridEquipos").data("kendoGrid").showColumn(7);
}
function removeEquip(e)
{
	var dataItem = $("#gridEquipos").data("kendoGrid").dataItem("tr[data-uid='"+$(e.currentTarget).closest("tr").data('uid')+"'");
	$("#gridEquipos").data("kendoGrid").dataSource.remove(dataItem);
    equipList[equipList.length] = {value:dataItem.id,text:dataItem.noSerie};
    $("#equipos").data("kendoComboBox").setDataSource(equipList);
}
function addEquipToGrid()
{
    if(validarComboBox($("#equipos option:selected"),undefined,'Seleccione un equipo de la lista'))
    {
        $.post(path+'inventario/getbyid/'+$("#equipos").data("kendoComboBox").value(),function(data)
        {
            try 
            {
                var json = eval("("+data+")");
                if(json.ok)
                {
                    $("#gridEquipos").data("kendoGrid").dataSource.add(json.msg);
                    removeItemFromEquipList($("#equipos").data("kendoComboBox").value());
                    $("#equipos").data("kendoComboBox").value("");
                }
                else
                    updateError(json.msg);
            }
            catch (e) 
            {
                updateError("Error: "+e+"\nData:"+data)
            }
        });
    }
}
function removeItemFromEquipList(value)
{
    $(equipList).each(function(index){
        if(equipList[index].value == value)
        {
            equipList.splice(index,1);
            $("#equipos").data("kendoComboBox").setDataSource(equipList);
            return false;
        }
    });
}
function getResguardos()
{
	$.post(path+'resguardos/getopencombo',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#resguardo").data("kendoComboBox").setDataSource(json.ok ? json.msg : []);
			if(!json.ok)
				updateError(json.msg);
			
		}
		catch (e)
		{
			updateError("Data: "+data+" Error:"+e);
		}	
	});
}
function getPersonal()
{
	$.post(path+'usuarios/getactivebycurrentusercombo',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#personal").data("kendoComboBox").setDataSource(json.ok ? json.msg : []);
			if(!json.ok)
				updateError(json.msg);
			
		}
		catch (e)
		{
			updateError("Data: "+data+" Error:"+e);
		}	
	});
}
function getCategoriesInventario()
{
    $.post('facturas/getcatfortable',function(data)
    {
        try
        {
            var json = eval("("+data+")");
            if(json.ok)
            {
                $("#gridEquipos").kendoGrid(
                {
                    dataSource: new kendo.data.DataSource(
                    {
                        pageSize: 20,
                        schema: {
                            model: {
                                id: "id",
                                fields: {
                                    id: { editable: false, nullable: true },
                                    codigo: { validation: { required: false } },
                                    categoria: {type:"number", validation: { required: true } },
                                    tipoEquipo: {type:"number", validation: { required: true } },
                                    marca: {type:"number", validation: { required: true } },
                                    modelo: { validation: { required: false } },
                                    noSerie: { validation: { required: false } },
                                    descripcion: { validation: { required: false } }
                                }
                            }
                        }
                    }),
                    pageable:
                    {
                        refresh: true,
                        pageSizes: true,
                        buttonCount: 5
                    },
                    height:374,
                    columns: [
                        { field: "codigo",title:'Codigo',width:100},
                        { field: "categoria",title:'Categoria',values:json.msg.categoria,width:120},
                        { field: "tipoEquipo",title:'Tipo de Equipo',values:json.msg.tipoEquipo,width:120},
                        { field: "marca",title:'Marca',values:json.msg.marca,width:120},
                        { field: "modelo",title:'Modelo',width:120},
                        { field: "noSerie",title:'No. Serie',width:120},
                        { field: "descripcion",title:'Descripcion'},
                        { command: [{name:'Eliminar',click:function(e){removeEquip(e)}}],title: "&nbsp;", width: 100 },
                    ]
                });
            }
            else
                updateError("Ocurrio un error obteniendo la lista de las categorias de los equipos, por lo que no se crea la tabla de equipos");
        }
        catch (e)
        {
            updateError("Data: "+data+" Error:"+e);
        }
    });
}
$(document).ready(complete);