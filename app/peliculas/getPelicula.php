<?php

    require '../config/database.php';

    // Id de la pelicula que se debe mostrar, el dato viene limpio
    $id = $conn->real_escape_string($_POST['id']);

    // Ya que solo necesitamos traer un registro es recomendado usar limit para que apenas encuentre el primero deje de buscar, porque sql sigue buscando a ver si encuentra otro
    $sql = "SELECT id, nombre, descripcion, id_genero FROM pelicula WHERE id=$id LIMIT 1";
    $resultado = $conn->query($sql);
    $rows = $resultado->num_rows; // La funcion num_rows nos dice si el resultado trae filas

    $pelicula = []; //Creamos un array vacio para luego llenarlo con los datos de la busqueda de sql

    // Si rows es mayor a cero quiere decir que si trae informacion
    if($rows > 0){
        $pelicula = $resultado->fetch_array(); //Guardamos en el array los datos del resultado SQl con la funcion fetch_array
    }

    // Mostramos la pelicula, en caso de que traiga acentos le pasamos el formato de JSON_UNESCAPED_UNICODE para que lo pueda procesar de forma correcta
    echo json_encode($pelicula, JSON_UNESCAPED_UNICODE)
    


?>