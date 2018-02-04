var equipList = null;
var bandConsult = false;
var currentId = null;
var denySaveFile = false;
var filename = null;
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
	$("#gridRecibos").kendoGrid(
    {
        dataSource: new kendo.data.DataSource(
        {
            schema: {
                model: {
                    id: "idrecibo",
                    fields: {
                        idrecibo: {type: "number", editable: false, nullable: true },
                        idunico:{},
                        nombre: {validation: { required: true } },
                        dependencia: { validation: { required: true } },
                        tipo: { validation: { required: true } },
                        fechaAlta:{},
                        personal:{},
                        status:{}
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
            { field: "idunico",title:'Folio'},
            { field: "nombre",title:'Nombre'},
            { field: "dependencia",title:'Dependencia'},
            { field: "tipo",title:'Tipo Recibo',template:function(dataItem)
            {
            	return dataItem.tipo == 1 ? "Normal" : "Temporal";
            }},
            { field: "fechaAlta",title:'Fecha'},
            { field: "personal",title:'Personal'},
            { field: "status",title:"Estado",template:function(dataItem)
            {
            	return dataItem.status == 1 ? "Activo" : "Finalizado";
            }}
        ],
        dataBound:tableEvent
    });
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
                        categoria: {validation: { required: true } },
                        tipoEquipo: {validation: { required: true } },
                        marca: {validation: { required: true } },
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
        height:300,
        columns: [
            { field: "codigo",title:'Codigo',width:100},
            { field: "categoria",title:'Categoria',width:120},
            { field: "tipoEquipo",title:'Tipo de Equipo',width:120},
            { field: "marca",title:'Marca',width:120},
            { field: "modelo",title:'Modelo',width:120},
            { field: "noSerie",title:'No. Serie',width:120},
            { field: "descripcion",title:'Descripcion'},
            { command: [{name:'Eliminar',click:function(e){removeEquip(e)}}],title: "&nbsp;", width: 100 },
        ]
    });
	useBoxMessage = true;
	$("#btnCleanFields").click(cleanFieldsResguardo);
    $("#btnAddEquip").click(addEquipToGrid);
    $("#formRecibos").submit(saveRecibo);
    $("#btnFinish").click(finishRecibo);
    $("#btnImprimir").click(actionOpenPdf);
    $("#btnPdfSigned").click(actionOpenPdfSigned);
    loadTable();
	getPersonal();
	getResguardos();
}
function saveRecibo(e)
{
	e.preventDefault();
	if(!denySaveFile)
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
			if(bandConsult)
				dataSend.append('id',currentId);
			else
			{
				dataSend.append("idresguardo",$("#resguardo").data("kendoComboBox").value());
				dataSend.append("equipos",JSON.stringify($("#gridEquipos").data("kendoGrid").dataSource.data()));
				dataSend.append("personal",$("#personal").data("kendoComboBox").value());
				dataSend.append("tipo",$("#tipo").data("kendoComboBox").value());
				dataSend.append("status",($("#tipo").data("kendoComboBox").value() == 1 ? 0 : 1));
			}
			$.ajax(
			{
				url:path+( bandConsult ? 'recibos/savepdf' : 'recibos/new'),
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
							if(!bandConsult)
								openPdf(path+'recibos/getpdf/'+json.id);
							cleanFieldsResguardo();
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
}
function loadTable()
{
	$.post(path+'recibos/getfortablebyuserarea',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#gridRecibos").data("kendoGrid").dataSource.data(json.ok ? json.msg : []);
			if(!json.ok)
				updateError(json.msg);
		}
		catch(err)
		{
			updateError("Ocurrio un error cargando la tabla: "+err+ " data:"+data);
		}
	});
}
function tableEvent()
{
	$("#gridRecibos tbody tr").click(function(e)
    {
        var dataItem = $("#gridRecibos").data("kendoGrid").dataItem("tr[data-uid='"+$(e.currentTarget).closest("tr").data('uid')+"'");
        $.post(path+'recibos/getbyid/'+dataItem.idrecibo,function(data)
        {
            try 
            {
                var json = eval("("+data+")");
                if(json.ok)
                {
                    bandConsult = true;
                    $("#resguardo").data("kendoComboBox").value(json.msg.folioResguardo);
                    $("#resguardo").data("kendoComboBox").enable(false);
                    $("#personal").data("kendoComboBox").value(json.msg.personal);
                    $("#personal").data("kendoComboBox").enable(false);
                    $("#tipo").data("kendoComboBox").value(json.msg.tipo);
                    $("#tipo").data("kendoComboBox").enable(false);
                    $("#fechaEntrega").val(json.msg.fechaEntrega);
					$("#nombre").val(json.msg.nombre);
					$("#dependencia").val(json.msg.dependencia);
					$("#departamento").val(json.msg.departamento);
					$("#cargo").val(json.msg.cargo);
					$("#nota").val(json.msg.nota);
					$(".equipsFields").hide();
					$("#divEquipos").show();
					currentId = json.msg.id;
					if(json.msg.tipo == 0 && json.msg.status == 1)
						$("#btnFinish").show();
					else
						$("#btnFinish").hide();
					$("#gridEquipos").data("kendoGrid").dataSource.data(json.msg.equipos);
					$("#gridEquipos").data("kendoGrid").hideColumn(7);
					$("#btnImprimir").show();
					$("#divFile").hide();
                    $("#file").val("");
                    $("#btnGuardar").hide();
                    $("#btnPdfSigned").hide();
					if(json.msg.file)
                    {
                        filename = json.msg.file.filename
                        denySaveFile = true;
                        $("#btnPdfSigned").show();
                        $("#btnGuardar").hide();
                    }
                    else
                    {
                        $("#divFile").show();
                        $("#btnGuardar").show();
                        $("#btnPdfSigned").hide();
                        denySaveFile = false;
                    }
                }
                else
                    updateError(json.msg);
            }
            catch (e) 
            {
                updateError("Error: "+e+"\nData:"+data)
            }
        });
    });
}
function finishRecibo()
{
	var condi = true;
	condi = condi && validarEntero(currentId,"Debe enviar un recibo temporal valido");
	condi = condi && confirm('¿Realmente desea finalizar el recibo?');
	if(condi)
	{
		$.post(path+"recibos/closetemporal/"+currentId,function(data)
		{
			try
			{
				var json = eval("("+data+")");
				if(json.ok)
				{
					cleanFieldsResguardo();
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
		});
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
function cleanFieldsResguardo()
{
	bandConsult = false;
	currentId = null;
	filename = null;
	denySaveFile = false;
	$("#resguardo").data("kendoComboBox").value("");
	$("#resguardo").data("kendoComboBox").enable(true);
	$("#personal").data("kendoComboBox").value("");
	$("#personal").data("kendoComboBox").enable(true);
	$("#tipo").data("kendoComboBox").value("");
	$("#tipo").data("kendoComboBox").enable(true);
	$("#fechaEntrega").val("");
	$("#nombre").val("");
	$("#dependencia").val("");
	$("#departamento").val("");
	$("#cargo").val("");
	$("#nota").val("");
	$("#equipos").data("kendoComboBox").value("");
	$("#btnGuardar").show();
	$("#gridEquipos").data("kendoGrid").dataSource.data([]);
	$("#divEquipos").hide();
	$(".equipsFields").hide();
	$("#btnFinish").hide();
	$("#btnImprimir").hide();
	$("#gridEquipos").data("kendoGrid").showColumn(7);
	$("#divFile").hide();
    $("#file").val("");
    $("#btnGuardar").show();
    $("#btnPdfSigned").show().hide();
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
function actionOpenPdf()
{
    if(validarEntero(currentId,"Debe seleccionar un recibo correcto"))
    {
        openPdf(path+'recibos/getpdf/'+currentId);
    }
}
function openPdf(url)
{
    window.open(url,'_blank');
}
function actionOpenPdfSigned()
{
    if(filename)
    {
        openPdf(path+'private/recibos/'+filename);
    }
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
$(document).ready(complete);