var deletedEquip = [];
var bandModificar = false;
var currentId = null;
var categoriesInventario = null;
function complete()
{
    $("#rfc").kendoComboBox({placeholder:"Seleccione un RFC",dataTextField:"text",dataValueField:"value",change:getProveedor});
    $("#rfc").data("kendoComboBox").value("");
    $("#rfcBus").kendoComboBox({placeholder:"Seleccione un RFC",dataTextField:"text",dataValueField:"value"});
    $("#rfcBus").data("kendoComboBox").value("");
    $("#ejercicio").kendoComboBox({placeholder:"Seleccione el ejercico",dataTextField:"text",dataValueField:"value",change:getPrograma});
    $("#ejercicio").data("kendoComboBox").value("");
    $("#ejercicioBus").kendoComboBox({placeholder:"Seleccione el ejercico",dataTextField:"text",dataValueField:"value"});
    $("#ejercicioBus").data("kendoComboBox").value("");
    $("#programa").kendoComboBox({placeholder:"Seleccione el programa",dataTextField:"text",dataValueField:"value"});
    $("#programa").data("kendoComboBox").value("");
    $("#area").kendoComboBox({placeholder:"Seleccione el area",dataTextField:"text",dataValueField:"value"});
    $("#area").data("kendoComboBox").value("");
    $("#gridFacturas").kendoGrid(
    {
        dataSource: new kendo.data.DataSource(
        {
            schema: {
                model: {
                    id: "idFactura",
                    fields: {
                        idFactura: {type: "number", editable: false, nullable: true },
                        fecha: {validation: { required: true } },
                        noFactura: { validation: { required: true } },
                        rfc: { validation: { required: true } },
                        nombreEmpresa: { validation: { required: true } },
                        fechaEntrega: { validation: { required: true } },
                        ejercicio: { validation: { required: true } },
                        programa: { validation: { required: true } },
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
            { field: "idFactura",title:'Folio'},
            { field: "fecha",title:'Fecha'},
            { field: "noFactura",title:'Numero de Factura'},
            { field: "rfc",title:'RFC'},
            { field: "nombreEmpresa",title:'Nombre de la Empresa'},
            { field: "ejercicio",title:'Ejercicio'},
            { field: "programa",title:'Programa'},
            { field: "area",title:'Area'}
        ],
        dataBound:tableEvent
    });
    useBoxMessage = true;
    $("#formProveedor").submit(saveProveedor);
    $("#formFactura").submit(saveFactura);
    $("#formBusqueda").submit(loadTable);
    $("#btnCleanFields").click(cleanFieldsFactura);
    getEjercicios();
    getAreas();
    getProveedores();
    getCategoriesInventario();
    $("#formBusqueda").trigger("submit");
}
function saveFactura(e)
{
    e.preventDefault();
    var condi = true;
    if(bandModificar)
    condi = condi && validarEntero(currentId,"La factura que desea modificar tiene una id incorrecta");
    condi = condi && validarFecha($("#fecha").val(),"Seleccione la fecha de la factura");
    condi = condi && validarTamanio($("#noFactura").val(),"No. Factura",1,50);
    condi = condi && validarTamanio($("#condiPago").val(),"Condicion Pago",1,100);
    condi = condi && validarComboBox($("#rfc option:selected"),undefined,"Seleccione un RFC correcto de la lista");
    condi = condi && validarComboBox($("#ejercicio option:selected"),undefined,"Seleccione un ejercicio correcto");
    condi = condi && validarComboBox($("#programa option:selected"),undefined,"Seleccione un programa correcto");
    condi = condi && validarComboBox($("#area option:selected"),undefined,"Seleccione un area correcta");
    if(bandModificar)
        condi = condi && confirm('¿Realmente desea modificar la factura seleccionada?');
    if(condi)
    {
        var dataSend = new FormData(this);
        dataSend.append('equip',JSON.stringify($("#gridEquipos").data("kendoGrid").dataSource.data()));
        if(bandModificar)
        {
            dataSend.append('id',currentId);
            dataSend.append('deletedEquip',deletedEquip);
        }
        $.ajax(
        {
            url:path+'facturas/'+(bandModificar ? 'update' : 'add'),
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
                        cleanFieldsFactura();
                        $("#formBusqueda").trigger("submit");
                        showSuccessBox(json.msg);
                    }
                    else
                        updateError(json.msg);
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
function removeEquip(e)
{
    if(e.model.idInventario !== null)
        deletedEquip[deletedEquip.length] = e.model.idInventario;
}
function loadTable(e)
{
    e.preventDefault();
    var dataSend = new FormData(this);
    $.ajax(
    {
        url:path+'facturas/getfortable',
        type:"POST",
        data:dataSend,
        processData:false,
        contentType:false,
        success: function(data)
        {
            try
            {
                var json = eval("("+data+")");
                $("#gridFacturas").data("kendoGrid").dataSource.data(json.ok ? json.msg : []);
                if(!json.ok)
                    updateError(json.msg);
            }
            catch(err)
            {
                updateError("Ocurrio un error cargando la tabla: "+err+ " data:"+data);
            }
        },
        error: function(data)
        {
            updateError('Error: '+data);
        }
    });
}
function tableEvent()
{
    $("#gridFacturas tbody tr").click(function(e)
    {
        var dataItem = $("#gridFacturas").data("kendoGrid").dataItem("tr[data-uid='"+$(e.currentTarget).closest("tr").data('uid')+"'");
        $.post(path+'facturas/getbyid/'+dataItem.idFactura,function(data)
        {
            try 
            {
                var json = eval("("+data+")");
                if(json.ok)
                {
                    cleanFieldsFactura();
                    $("#titleForm").html("Modificando Factura: "+json.msg.id);
                    currentId = json.msg.id;
                    bandModificar = true;
                    $("#fecha").val(json.msg.fecha);
                    $("#noFactura").val(json.msg.noFactura);
                    $("#condiPago").val(json.msg.condiPago);
                    $("#fechaEntrega").val(json.msg.fechaEntrega);
                    $("#rfc").data("kendoComboBox").value(json.msg.rfc);
                    getProveedor();
                    $("#vendedor").val(json.msg.vendedor);
                    $("#comprador").val(json.msg.comprador);
                    $("#responsable").val(json.msg.responsable);
                    $("#ejercicio").data("kendoComboBox").value(json.msg.ejercicio);
                    getPrograma(undefined,json.msg.programa);
                    $("#area").data("kendoComboBox").value(json.msg.area);
                    $("#gridEquipos").data("kendoGrid").dataSource.data(json.msg.equip);
                    for(var i = 0; i < json.msg.files.length; i++)
                    {
                        if(json.msg.files[i].campo === "factura")
                            $("#fileFactura").html("<a href='"+path+json.msg.files[i].file+"' target='_BLANK'>Ver Archivo</a>");
                        if(json.msg.files[i].campo === "orden")
                            $("#fileOrden").html("<a href='"+path+json.msg.files[i].file+"' target='_BLANK'>Ver Archivo</a>");
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
function cleanFieldsFactura()
{
    $("#titleForm").html("Nueva Factura");
    currentId = null;
    bandModificar = false;
    deletedEquip = [];
    $("#fecha").val("");
    $("#noFactura").val("");
    $("#condiPago").val("");
    $("#fechaEntrega").val("");
    $("#rfc").data("kendoComboBox").value("");
    $("#nombreEmpresa").html("");
    $("#direccionEmpresa").html("");
    $("#vendedor").val("");
    $("#comprador").val("");
    $("#responsable").val("");
    $("#ejercicio").data("kendoComboBox").value("");
    $("#programa").data("kendoComboBox").value("");
    $("#area").data("kendoComboBox").value("");
    $("#gridEquipos").data("kendoGrid").dataSource.data([]);
    $("#facturapdf").val("");
    $("#ordenpdf").val("");
    $("#fileFactura").html("");
    $("#fileOrden").html("");
}
function saveProveedor(e)
{
    e.preventDefault();
    var condi = true;
    condi = condi && validarTamanio($("#rfcProveedor").val(),"RFC",12,13);
    condi = condi && validarNoVacio($("#nombreProveedor").val(),"Nombre de la empresa");
    if(condi)
    {
        var formProveedor = new FormData(this);
        formProveedor.append('activo',1);
        $.ajax(
        {
            url:path+'proveedores/add',
            type:"POST",
            data:formProveedor,
            processData:false,
            contentType:false,
            success: function(data)
            {
                try
                {
                    var json = eval("("+data+")");
                    if(json.ok)
                    {
                        getProveedores(json.id);
                        $("#nombreEmpresa").html($("#nombreProveedor").val());
                        $("#direccionEmpresa").html($("#direccionProveedor").val());
                        $("#modalProveedor").data('kendoWindow').close();
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
function openProveedorModal()
{
    if($("#modalProveedor").data('kendoWindow')!=undefined)
    {
        $("#modalProveedor").data('kendoWindow').open();
        $("#modalProveedor").data('kendoWindow').center();
    }
    else
    {
        $("#modalProveedor").show();
        $("#modalProveedor").kendoWindow(
        {
            actions: ["Maximize", "Minimize", "Close"],
            draggable: true,
            height: "auto",
            width:"400px",
            modal: true,
            resizable: false,
            title: "Nuevo Proveedor",
            close:clearFieldsProveedor
        });
        $("#modalProveedor").data('kendoWindow').center();
    }
}
function clearFieldsProveedor()
{
    $("#rfcProveedor").val("");
    $("#nombreProveedor").val("");
    $("#direccionProveedor").val("");
    $("#cpProveedor").val("");
}
function getProveedor()
{
    if($("#rfc").data("kendoComboBox").text().replace(/\s/g,"") !== $("#rfc").data("kendoComboBox").value().replace(/\s/g,""))
    {
        $.post(path+'proveedores/getbyid/'+$("#rfc").data("kendoComboBox").value(),function(data)
        {
            try
            {
                var json = eval("("+data+")");
                $("#nombreEmpresa").html(json.ok ? json.msg.nombreEmpresa : "");
                $("#direccionEmpresa").html(json.ok ? json.msg.direccion : "");
                if(!json.ok)
                    updateError(json.msg);
            }
            catch (e)
            {
                updateError("Error: "+e+"\nData:"+data)
            }
        });
    }
    else 
    {
        if(confirm("El rfc seleccionado no se encuentra asignado a un proveedor, ¿Desea crear el proveedor?"))
        {
            $("#rfcProveedor").val($("#rfc").data("kendoComboBox").text());
            openProveedorModal();
        }
        else
            $("#rfc").data("kendoComboBox").value("");
    }
}
function getEjercicios()
{
    $.post(path+'ejercicio/getforcombo',function(data)
    {
        try
        {
            var json = eval("("+data+")");
            $("#ejercicio").data("kendoComboBox").setDataSource(json.ok ? json.msg : []);
            $("#ejercicioBus").data("kendoComboBox").setDataSource(json.ok ? json.msg : []);
            if(!json.ok)
                updateError(json.msg);
        }
        catch (e)
        {
            updateError("Error: "+e+"\nData:"+data)
        }
    });
}
function getPrograma(e,id)
{
    if(validarComboBox($("#ejercicio option:selected"),undefined,"Seleccione un ejercicio correcto"))
    {
        $.post(path+'programa/getforcombobyejercicio/'+$("#ejercicio").data("kendoComboBox").value(),function(data)
        {
            try
            {
                var json = eval("("+data+")");
                $("#programa").data("kendoComboBox").setDataSource(json.ok ? json.msg : []);
                if(json.ok)
                    $("#programa").data("kendoComboBox").value(id !== undefined ? id : "");
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
function getAreas()
{
    $.post(path+'areas/operativas',function(data)
    {
        try
        {
            $("#area").data("kendoComboBox").setDataSource(eval("("+data+")"));
        }
        catch (e)
        {
            updateError("Data: "+data+" Error:"+e);
        }
    });
}
function getProveedores(id)
{
    $.post(path+'proveedores/getrfcforcombo',function(data)
    {
        try
        {
            $("#rfc").data("kendoComboBox").setDataSource(eval("("+data+")"));
            $("#rfcBus").data("kendoComboBox").setDataSource(eval("("+data+")"));
            if(id !== undefined)
                $("#rfc").data("kendoComboBox").value(id);
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
                console.log(json.msg);
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
                                    cantidad: { type:"number", validation: { required: true } },
                                    codigo: { validation: { required: false } },
                                    categoria: {type:"number", validation: { required: true } },
                                    tipoEquipo: {type:"number", validation: { required: true } },
                                    marca: {type:"number", validation: { required: true } },
                                    modelo: { validation: { required: true } },
                                    noSerie: { validation: { required: true } },
                                    um: {type:"number", validation: { required: true } },
                                    descripcion: { validation: { required: false } }
                                }
                            }
                        }
                    }),
                    toolbar: ["create"],
                    pageable:
                    {
                        refresh: true,
                        pageSizes: true,
                        buttonCount: 5
                    },
                    height:374,
                    columns: [
                        { field: "cantidad",title:'Cant.',width:80},
                        { field: "codigo",title:'Codigo',width:100},
                        { field: "categoria",title:'Categoria',values:json.msg.categoria,width:120},
                        { field: "tipoEquipo",title:'Tipo de Equipo',values:json.msg.tipoEquipo,width:120},
                        { field: "marca",title:'Marca',values:json.msg.marca,width:120},
                        { field: "modelo",title:'Modelo',width:120},
                        { field: "noSerie",title:'No. Serie',width:120},
                        { field: "um",title:'U/M',values:json.msg.um,width:100},
                        { field: "descripcion",title:'Descripcion'},
                        { command: "destroy", title: "&nbsp;", width: 100 },
                    ],
                    remove:removeEquip,
                    editable: true
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
