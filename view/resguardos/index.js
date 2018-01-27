var equipList = null;
var bandConsult = false;
function complete()
{
	var listaEquipos = [];
	$("#personal").kendoComboBox({placeholder:"Seleccione el personal",dataTextField:"text",dataValueField:"value"});
	$("#personal").data("kendoComboBox").value("");
	$("#equipos").kendoComboBox({placeholder:"Ingrese el numero de Serie",dataTextField:"text",dataValueField:"value"});
	$("#equipos").data("kendoComboBox").value("");
	$("#gridResguardos").kendoGrid(
    {
        dataSource: new kendo.data.DataSource(
        {
            schema: {
                model: {
                    id: "idresguardo",
                    fields: {
                        idresguardo: {type: "number", editable: false, nullable: true },
                        idunico:{},
                        nombre: {validation: { required: true } },
                        dependencia: { validation: { required: true } },
                        departamento: { validation: { required: true } },
                        cargo: { validation: { required: true } },
                        fechaAlta:{},
                        personal:{}
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
            { field: "departamento",title:'Departamento'},
            { field: "cargo",title:'Cargo'},
            { field: "fechaAlta",title:'Fecha'},
            { field: "personal",title:'Personal'}
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
                        marca: { validation: { required: true } },
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
    $("#formResguardo").submit(saveResguardo);
    $("#btnCleanFields").click(cleanFieldsResguardo);
    $("#btnAddEquip").click(addEquipToGrid);
    loadTable();
	getPersonal();
	getAllowedForResguardoCombo();
}
function saveResguardo(e)
{
	e.preventDefault();
	if(!bandConsult)
	{
		var condi = true;
		condi = condi && validarTamanio($("#nombre").val(),"Nombre Solicitante",1,200);
		condi = condi && validarTamanio($("#dependencia").val(),"Dependencia Solicitante",1,150);
		condi = condi && validarComboBox($("#personal option:selected"),undefined,"Seleccione el personal que entrega los equipos");
		if($("#gridEquipos").data("kendoGrid").dataSource.data().length == 0)
		{
			condi = false;
			updateError("Debe seleccionar al menos un equipo para asignar al resguardo");
		}
		if(condi)
		{
			var dataSend = new FormData(this);
			dataSend.append("equipos",JSON.stringify($("#gridEquipos").data("kendoGrid").dataSource.data()));
			dataSend.append("personal",$("#personal").data("kendoComboBox").value());
			$.ajax(
			{
				url:path+'resguardos/new',
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
	$.post(path+'resguardos/getfortablebyuserarea',function(data)
	{
		try
		{
			var json = eval("("+data+")");
			$("#gridResguardos").data("kendoGrid").dataSource.data(json.ok ? json.msg : []);
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
	$("#gridResguardos tbody tr").click(function(e)
    {
        var dataItem = $("#gridResguardos").data("kendoGrid").dataItem("tr[data-uid='"+$(e.currentTarget).closest("tr").data('uid')+"'");
        $.post(path+'resguardos/getbyid/'+dataItem.idresguardo,function(data)
        {
            try 
            {
                var json = eval("("+data+")");
                if(json.ok)
                {
                    cleanFieldsResguardo();
                    bandConsult = true;
					$("#nombre").val(json.msg.nombre);
					$("#dependencia").val(json.msg.dependencia);
					$("#departamento").val(json.msg.departamento);
					$("#cargo").val(json.msg.cargo);
					$("#nota").val(json.msg.nota);
					$("#personal").data("kendoComboBox").value(json.msg.personal);
					$("#gridEquipos").data("kendoGrid").dataSource.data(json.msg.equipos);
					$("#gridEquipos").data("kendoGrid").hideColumn(7);
					$("#btnGuardar").hide();
					$(".equipsFields").hide();
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
function cleanFieldsResguardo()
{
	bandConsult = false;
	$("#nombre").val("");
	$("#dependencia").val("");
	$("#departamento").val("");
	$("#cargo").val("");
	$("#nota").val("");
	$("#personal").data("kendoComboBox").value("");
	$("#equipos").data("kendoComboBox").value("");
	$("#gridEquipos").data("kendoGrid").dataSource.data([]);
	$("#gridEquipos").data("kendoGrid").showColumn(7);
	$("#btnGuardar").show();
	$(".equipsFields").show();
	getAllowedForResguardoCombo();
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
function getAllowedForResguardoCombo()
{
	$.post(path+'inventario/getallowedforresguardocombo',function(data)
	{
		try
		{
			var json = eval("("+data+")");
            $("#equipos").data("kendoComboBox").setDataSource(json.ok ? json.msg : []);
            equipList = json.ok ? json.msg : null;
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