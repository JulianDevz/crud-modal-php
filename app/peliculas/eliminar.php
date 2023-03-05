<?php 

    session_start();

    require '../config/database.php';
    
    // Con la funcion real escape evitamos que se inyecte codigo al input
    $id = $conn->real_escape_string($_POST['id']);

    // Consulto el nombre de la imagen de la pelicula que vamos actualizar para despues de actualizar borrar la imagen que tenia anteriormente
    $sql1= "SELECT imagen FROM pelicula WHERE id = $id";
    $imagenAntigua = $conn->query($sql1);

    while($row_imagen = $imagenAntigua->fetch_assoc()){
        // Nombre de la imagen que tenia antes de actualizar (La que vamos a borrar porque la remplazaremos por otra)
        $imagenBorrar = $row_imagen['imagen'];
    }

    $sql = "DELETE FROM pelicula WHERE id = $id";

    // 
    if($conn->query($sql)){

        $dir = "posters/"; //Carpeta donde guardaremos las imagenes
        $poster = $dir.$imagenBorrar;

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