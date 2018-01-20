var equipList = null;
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
    useBoxMessage = true;
    $("#formResguardo").submit(saveResguardo);
    $("#btnCleanFields").click(cleanFieldsResguardo);
    $("#btnAddEquip").click(addEquipToGrid);
	getPersonal();
	getAllowedForResguardoCombo();
    getCategoriesInventario();
}
function saveResguardo(e)
{
	e.preventDefault();
}
function tableEvent()
{
	$("#gridResguardos tbody tr").click(function(e)
    {
        var dataItem = $("#gridResguardos").data("kendoGrid").dataItem("tr[data-uid='"+$(e.currentTarget).closest("tr").data('uid')+"'");
        console.log(dataItem);
        /*$.post(path+'facturas/getbyid/'+dataItem.idFactura,function(data)
        {
            try 
            {
                var json = eval("("+data+")");
                if(json.ok)
                {
                    cleanFieldsResguardo();
                }
                else
                    updateError(json.msg);
            }
            catch (e) 
            {
                updateError("Error: "+e+"\nData:"+data)
            }
        });*/
    });
}
function removeEquip(e)
{
    equipList[equipList.length] = {value:e.model.id,text:e.model.noSerie};
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
                        { command: "destroy", title: "&nbsp;", width: 100 },
                    ],
                    editable:true,
                    remove:removeEquip
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