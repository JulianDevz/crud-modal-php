<?php 

    session_start();

    require '../config/database.php';
    
    // Con la funcion real escape evitamos que se inyecte codigo al input
    $id = $conn->real_escape_string($_POST['id']);

    $sql = "DELETE FROM pelicula WHERE id = $id";

    // 
    if($conn->query($sql)){

        $dir = "posters"; //Carpeta donde guardaremos las imagenes

        $poster = $dir.'/'.$id.'.jpeg';

        // Validamos si existe el archivo que queremos eliminar
        if(file_exists($poster)){ 
            unlink($poster);
        }
        $_SESSION['color'] = "success";
        $_SESSION['msg'] = "Registro Eliminado ";
    }else{
        $_SESSION['color'] = "danger";
        $_SESSION['msg'] = "Error al eliminar registro ";
    }
    header('location:index.php');

?>