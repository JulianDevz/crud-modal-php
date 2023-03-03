<?php 

    // Cremoa sessiones para indicar mensajes o errores temporales
    session_start();

    require '../config/database.php';
    
    // Con la funcion real escape evitamos que se inyecte codigo al input
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $genero = $conn->real_escape_string($_POST['genero']);

    $sql = "INSERT INTO pelicula (nombre, descripcion, id_genero, fecha_alta) VALUES ('$nombre', '$descripcion', $genero, NOW())";

    // Si se pudo hacer la insersion obtenemos el id de esta conexion
    if($conn->query($sql)){
        $id = $conn->insert_id; //Con este id guadaremos la imagen

        $_SESSION['color'] = "success";
        $_SESSION['msg'] = "Registro guardado";

        if($_FILES['poster']['error'] == UPLOAD_ERR_OK){ //Validamos que la subida de la imagen no tuvo ningun error, cuando upload esta ok es igual a cero de lo contrario tendra otros valores
            $permitidos = array("image/jpeg");
            if(in_array($_FILES['poster']['type'],$permitidos)){ //Validamos que la imagen tenga algunos de los formatos que tenemos en el array de $permitidos

                $dir = "posters"; //Carpeta donde guardaremos las imagenes

                $info_img = pathinfo($_FILES['poster']['name']); //Esto nos permite conocer toda la info de la imagen como nombre, extension, ruta, entre mas cosas
                $extension = $info_img['extension']; //Nos permite conocer la extension del archivo

                $poster = $dir.'/'.$id.'.'.$extension; //Sera el nombre con el que guardaremos el archivo, usaremos como nombre el id que definimos antes y su extension correspondiente

                // Sino existe la carpeta entonces la creamos con mkdir
                if(!file_exists($dir)){
                    mkdir($dir, 0777); //La creamos y le damos todos los permisos con 0777
                }

                // Movemos el archivo con su nombre temporal, e indicamos su nueva ubicacion y nombre (ambas cosas las tenemos en la variable $poster)
                //Preguntamos sino se pudo guardar, a la vez que hacemos el guardado
                if(!move_uploaded_file($_FILES['poster']['tmp_name'],$poster)){
                    // Creamos la variable de session msg y le asignamos un error para cuando no se ha guardado el archivo
                    // Usamos un punto antes del igual para concatenas el error, esto quiere decir que si habia un mensaje antes se borrara y pondra este nuevo
                    $_SESSION['msg'] .= "<br/>Error al guardar imagen"; 
                    $_SESSION['color'] = "danger"; 
                }

            }else{
                $_SESSION['msg'] .= "<br/>Formato de imagen no permitido";
                $_SESSION['color'] = "danger"; 
            } 
        }
    }else{
        // Aqui no concatenamos el mensaje con punto porque en este punto seria el unico mensaje que contendria
        $_SESSION['msg'] = "<br/>Error al guardar imagen";
        $_SESSION['color'] = "danger"; 
    }

    header('location:index.php');

?>