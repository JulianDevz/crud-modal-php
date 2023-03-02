<?php 

    require '../config/database.php';
    
    // Con la funcion real escape evitamos que se inyecte codigo al input
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $genero = $conn->real_escape_string($_POST['genero']);

    $sql = "INSERT INTO pelicula (nombre, descripcion, id_genero, fecha_alta) VALUES ('$nombre', '$descripcion', $genero, NOW())";

    // Si se pudo hacer la insersion obtenemos el id de esta conexion
    if($conn->query($sql)){
        $id = $conn->insert_id;
    }

    header('location:index.php');

?>