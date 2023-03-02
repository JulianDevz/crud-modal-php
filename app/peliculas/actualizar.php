<?php 

    require '../config/database.php';
    
    // Con la funcion real escape evitamos que se inyecte codigo al input
    $id = $conn->real_escape_string($_POST['id']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $genero = $conn->real_escape_string($_POST['genero']);

    $sql = "UPDATE pelicula 
    SET nombre = '$nombre' , descripcion = '$descripcion', id_genero = $genero WHERE id = $id";

    // 
    if($conn->query($sql)){

    }

    header('location:index.php');

?>