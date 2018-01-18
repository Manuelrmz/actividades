var equipList = null;
function complete()
{
	var listaEquipos = [];
	$("#personal").kendoComboBox({placeholder:"Seleccione el personal",dataTextField:"text",dataValueField:"value"});
	$("#personal").data("kendoComboBox").value("");
	$("#equipos").kendoComboBox({placeholder:"Seleccione el equipo",dataTextField:"text",dataValueField:"value"});
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
                    id: "idInventario",
                    fields: {
                        idInventario: { editable: false, nullable: true },
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
            { field: "categoria",title:'Categoria',width:120},
            { field: "tipoEquipo",title:'Tipo de Equipo',width:120},
            { field: "marca",title:'Marca',width:120},
            { field: "modelo",title:'Modelo',width:120},
            { field: "noSerie",title:'No. Serie',width:120},
            { field: "descripcion",title:'Descripcion'},
            { command: "destroy", title: "&nbsp;", width: 100 },
        ],
        remove:removeEquip,
    });
    useBoxMessage = true;
    $("#formResguardo").submit(saveResguardo);
    $("#btnCleanFields").click(cleanFieldsResguardo);
	getPersonal();
	getAvailableInventario();
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
    if(e.model.idInventario !== null)
        deletedEquip[deletedEquip.length] = e.model.idInventario;
}
function cleanFieldsResguardo()
{

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
function getAvailableInventario()
{
	$.post(path+'inventario/getavailablebyuserarea',function(data)
	{
		try
		{
			console.log(data);
			/*var json = eval("("+data+")");
			$("#personal").data("kendoComboBox").setDataSource(json.ok ? json.msg : []);
			if(!json.ok)
				updateError(json.msg);*/
			
		}
		catch (e)
		{
			updateError("Data: "+data+" Error:"+e);
		}	
	});
}
$(document).ready(complete);