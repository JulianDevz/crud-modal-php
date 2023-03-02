<?php 

    require '../config/database.php';
    
    // Con la funcion real escape evitamos que se inyecte codigo al input
    $id = $conn->real_escape_string($_POST['id']);

    $sql = "DELETE FROM pelicula WHERE id = $id";

    // 
    if($conn->query($sql)){
    }
    header('location:index.php');

?>