<?php 

    // Cremoa sessiones para indicar mensajes o errores temporales
    session_start();

    require '../config/database.php';
    
    // Con la funcion real escape evitamos que se inyecte codigo al input
    $id = $conn->real_escape_string($_POST['id']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $genero = $conn->real_escape_string($_POST['genero']);
    $imagen = $conn->real_escape_string($_FILES['poster']['name']);

    // Consulto el nombre de la imagen de la pelicula que vamos actualizar para despues de actualizar borrar la imagen que tenia anteriormente
    $sql1= "SELECT imagen FROM pelicula WHERE id = $id";
    $imagenAntigua = $conn->query($sql1);

    while($row_imagen = $imagenAntigua->fetch_assoc()){
        // Nombre de la imagen que tenia antes de actualizar (La que vamos a borrar porque la remplazaremos por otra)
        $imagenBorrar = $row_imagen['imagen'];
    }

    // Si al actualizar no subimos una nueva imagen entonces al update le pasaremos la imagen que ya teniamos antes en ese registro, de lo contrario entonces si le añadiremos la fecha a esa imagen para ser enviada
    if($imagen == ""){
        $imagen = $imagenBorrar;

    }else{
        $fecha = new DateTime();
        $imagen = $fecha->getTimestamp()."-".$imagen;
    
    }

    $sql = "UPDATE pelicula 
    SET nombre = '$nombre' , descripcion = '$descripcion', id_genero = $genero, imagen = '$imagen' WHERE id = $id";


if($conn->query($sql)){
        $_SESSION['color'] = "success";
        $_SESSION['msg'] = "Registro actualizado";
        
        if($_FILES['poster']['error'] == UPLOAD_ERR_OK){ //Validamos que la subida de la imagen no tuvo ningun error, cuando upload esta ok es igual a cero de lo contrario tendra otros valores
            $permitidos = array("image/jpeg", "image/jpg", "image/png");
            if(in_array($_FILES['poster']['type'],$permitidos)){ //Validamos que la imagen tenga algunos de los formatos que tenemos en el array de $permitidos

                $dir = "posters/";

                // Nombre de la imagen que se adjunta
                $imagen_temporal = $_FILES['poster']['tmp_name'];

                // Sino existe la carpeta entonces la creamos con mkdir
                if(!file_exists($dir)){
                    mkdir($dir, 0777); //La creamos y le damos todos los permisos con 0777
                }

                // Movemos el archivo con su nombre temporal, e indicamos su nueva ubicacion y nombre
                //Preguntamos sino se pudo guardar, a la vez que hacemos el guardado
                if(!move_uploaded_file($imagen_temporal,$dir.$imagen)){
                    // Creamos la variable de session msg y le asignamos un error para cuando no se ha guardado el archivo
                    // Usamos un punto antes del igual para concatenas el error, esto quiere decir que si habia un mensaje antes se borrara y pondra este nuevo
                    $_SESSION['msg'] .= "<br/>Error al guardar imagen"; 
                    $_SESSION['color'] = "danger"; 
                }

                // Eliminamos la imagen antigua
                unlink("posters/".$imagenBorrar);

            }else{
                $_SESSION['color'] = "danger"; 
                $_SESSION['msg'] .= "<br/>Formato de imagen no permitido";
            } 
        }
    }else{
        // Aqui no concatenamos el mensaje con punto porque en este punto seria el unico mensaje que contendria
        $_SESSION['msg'] = "<br/>Error al actualizar registro";
        $_SESSION['color'] = "danger"; 
    }

    header('location:index.php');

?>