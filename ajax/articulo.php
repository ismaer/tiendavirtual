<?php

require_once "../modelos/Articulo.php";


$articulo=new Articulo();

$idarticulo=isset($_POST["idarticulo"])? limpiarCadena($_POST["idarticulo"]):"";
$idcategoria=isset($_POST["idcategoria"])? limpiarCadena($_POST["idcategoria"]):"";
$codigo=isset($_POST["codigo"])? limpiarCadena($_POST["codigo"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$stock=isset($_POST["stock"])? limpiarCadena($_POST["stock"]):"";
$descripcion=isset($_POST["descripcion"])? limpiarCadena($_POST["descripcion"]):"";
$imagen=isset($_POST["imagen"])? limpiarCadena($_POST["imagen"]):"";


switch ($_GET["op"]){
	case 'guardaryeditar':
		if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])) {
			$imagen ="";
		}
		else
		{
			$ext = explode(".", $_FILES["imagen"]["name"]);  //extraigo la extension del archivo
			if ($_FILES['imagen']['type']== "image/jpg" || $_FILES["imagen"]["type"]== "image/jpeg" || $_FILES["imagen"]["type"]== "image/png") 
			{
				$imagen = round(microtime(true)) . '.' . end($ext); //renombro la imagen
				move_uploaded_file($_FILES["imagen"]["tmp_name"], "../files/articulos/". $imagen);	
			}
		}

		if (empty($idarticulo)) {
			$rspta=$articulo->insertar($idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen);
			echo $rspta ? "Articulo registrado": "Articulo no se pudo registrar";
		}
		else{
			$rspta=$articulo->editar($idarticulo, $idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen);
			echo $rspta ? "Articulo actualizado": "Articulo no se pudo Actualizar";
		}
	break;
		
	case 'desactivar':
		$rspta=$articulo->desactivar($idarticulo);
		echo $rspta ? "Articulo Desactivado": "Articulo no se puede Desactivar";
	break;
	
	case 'activar':
		$rspta=$articulo->activar($idarticulo);
		echo $rspta ? "Articulo Activado": "Articulo no se pudo Activar";
	break;
	
	case 'mostrar':
		$rspta=$articulo->mostrar($idarticulo);
		//Codificar el resultado utilizando json
		echo json_encode($rspta);
	break;
		
	case 'listar':
		$rspta=$articulo->listar();
		//Vamos a declarar un array
		$data= array();
		while ($reg=$rspta->fetch_object()) {
			
			$data[]=array(
				"0"=>($reg->condicion)?'<button class="btn btn-warning" onclick="mostrar('.$reg->idarticulo.')"><i class="fa fa-pencil"> </i></button>'.
				' <button class="btn btn-danger" onclick="desactivar('.$reg->idarticulo.')"><i class="fa fa-close"> </i></button>':
				'<button class="btn btn-warning" onclick="mostrar('.$reg->idarticulo.')"><i class="fa fa-pencil"> </i></button>'.
				' <button class="btn btn-primary" onclick="activar('.$reg->idarticulo.')"><i class="fa fa-check"> </i></button>',
				"1"=>$reg->nombre,
				"2"=>$reg->categoria,
				"3"=>$reg->codigo,
				"4"=>$reg->stock,
				"5"=>"<img src= '../files/articulos/".$reg->imagen. " ' height='50px' width='50px' >",
				"6"=>($reg->condicion)?'<span class="label bg-green ">Activado</span>':'<span class="label bg-red">Desactivado</span>'
				); 
		}
		$results = array(
			"sEcho"=>1, //Informacion para el datatables
			"iTotalRecords"=>count($data), //Enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //Enviamos el total registros a visualizar
			"aaData"=>$data
			);
		echo json_encode($results);
	break;
}

?>