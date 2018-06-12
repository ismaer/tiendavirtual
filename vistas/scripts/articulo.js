var tabla;
//funcion que se ejecuta al inicio

function init()
{
	mostrarform(false);
	listar();

	$("#formulario").on("submit",function(e)
	{
		guardaryeditar(e);

	})

	//Cargamos los items al select categoria
	$.post("../ajax/articulo.php?op=selectCategoria", function(r)
	{
		$("#idcategoria").html(r);
		$("#idcategoria").selectpicker('refresh');
	});
	$("#imagenmuestra").hide();
}

//Función limpiar 
function limpiar()
{
	$("#codigo").val("");
	$("#nombre").val("");
	$("#descripcion").val("");
	$("#stock").val("");
}

//Funcion mostrar formulario
function mostrarform(flag)
{
	limpiar();
	if (flag)
	{
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
	}
	else
	{
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
	}
}

//Funcion cancelarform
function cancelarform()
{
	limpiar();
	mostrarform(false);
}
//Funcion Listar
function listar()
{	
	tabla=$('#tbllistado').dataTable( 
	{
		"aProcessing": true, //Activamos el procesamiento del datatables
		"aServerSide": true, //Paginación y filtrados realizados por el servidor
		dom: 'Bfrtip', //Definimos los elementos del control de tabla
		buttons: [
					'copyHtml5',
					'excelHtml5',
					'csvHtml5',
					'pdf'
				],
		"ajax":
				{
					url:'../ajax/articulo.php?op=listar',
					type : 'get',
					dataType : "json",
					error: function(e){
						console.log(e.responseText);
					}
				},
		"bDestroy": true,
		"iDisplayLength": 5, //Paginacion
		"order": [[0,"desc"]] //Ordenar (columna,orden)

	}).DataTable();
	
}
//Funcion para guardar o editar

function guardaryeditar(e)
{
    e.preventDefault(); //No se activará la accion predeterminada del evento
    $("#btnGuardar").prop("disabled",true);
    var formData = new FormData($("#formulario")[0]);
    
    $.ajax({

    	url: "../ajax/articulo.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function(datos)
        {
            bootbox.alert(datos);
            mostrarform(false);
            tabla.ajax.reload();
        }
    });
    limpiar();
}

function mostrar(idarticulo)
{
	$.post("../ajax/articulo.php?op=mostrar",{idarticulo : idarticulo}, function(data, status)
	{
		data = 	JSON.parse(data);
		mostrarform(true);

		$("#idcategoria").val(data.idcategoria);
		$("#idcategoria").selectpicker('refresh');
		$("#codigo").val(data.codigo);
		$("#nombre").val(data.nombre);
		$("#stock").val(data.stock);
		$("#descripcion").val(data.descripcion);
		$("#imagenmuestra").show();
		$("#imagenmuestra").attr("src", "../files/articulos/"+data.imagen);
		$("#imagenactual").val(data.imagen);
		$("#idarticulo").val(data.idarticulo);

	});
}
//Funcion para desactivar registros
function desactivar(idarticulo)
{
	bootbox.confirm("¿Está seguro que desea desactivar el articulo?", function(result){
		if (result) {
			$.post("../ajax/articulo.php?op=desactivar", {idarticulo: idarticulo}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();

			});
		}

	})
	
}
//Funcion para activar registros
function activar(idarticulo)
{
	bootbox.confirm("¿Está seguro que desea activar el articulo?", function(result){
		if (result) {
			$.post("../ajax/articulo.php?op=activar", {idarticulo: idarticulo}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();

			});
		}

	})
	
}

init();