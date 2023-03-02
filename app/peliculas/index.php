<?php require '../config/database.php'; ?>

<?php

    // Traemos el listado de las peliculas
    $sqlPeliculas = "SELECT p.id, p.nombre, p.descripcion, g.nombre AS genero FROM pelicula AS P
    INNER JOIN genero AS g 
    ON p.id_genero = g.id";

    $peliculas = $conn->query($sqlPeliculas);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD MODAL</title>

    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/all.min.css" rel="stylesheet">
</head>
<body>
    
    <div class="container py-3">
        <h2 class="text-center">Peliculas</h2>

        <div class="row justify-content-end">
            <div class="col-auto">
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoModal"><i class="fa-solid fa-circle-plus"></i> Nuevo registro</a>
            </div>
        </div>

        <table class="table table-sm table-striped table-hover mt-4">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Descripcion</th>
                    <th>Genero</th>
                    <th>Poster</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row_pelicula = $peliculas->fetch_assoc()){ ?>
                    <tr>
                        <td> <?= $row_pelicula["id"] ?></td>
                        <td> <?= $row_pelicula["nombre"] ?></td>
                        <td> <?= $row_pelicula["descripcion"] ?></td>
                        <td> <?= $row_pelicula["genero"] ?></td>
                        <td></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editarModal" data-bs-id="<?= $row_pelicula['id']; ?>"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
                            <a href="#" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#eliminarModal" data-bs-id="<?= $row_pelicula['id']; ?>"><i class="fa-solid fa-trash"></i> Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php
        // Traemos el listado de los generos, el archivo de nuevoModal podra acceder a esta informacion de generos porque ese archivo esta incluido en este y se convierten en uno solo
        $sqlGenero = "SELECT id, nombre FROM genero";
        $generos = $conn->query($sqlGenero);
    ?>

    <?php include 'nuevoModal.php'; ?>

    <!-- Reiniciamos la consulta de generos, ya que en nuevoModal.php ya ha recorrido necesitamos que se vuelva a correr para editarModal.php -->
    <?php $generos->data_seek(0) ?>
    <?php include 'editarModal.php'; ?>
    <?php include 'eliminarModal.php'; ?>

    <script>
        // Capturamos el modal de editar
        let editarModal = document.getElementById('editarModal')
        // Capturamos el modal de eliminar
        let eliminarModal = document.getElementById('eliminarModal')

        // Escuchamos el evento del modal cuando se presione el boton y se cargue todo el modal
        editarModal.addEventListener('shown.bs.modal', event => {
            let button = event.relatedTarget //Obtenemos el boton al que se le dio clic
            let id = button.getAttribute('data-bs-id') //Obtenemos el id del registro que quiero modificar

            // Accedemos a los elementos del formulario para editar
            let inputId = editarModal.querySelector('.modal-body #id')
            let inputNombre = editarModal.querySelector('.modal-body #nombre')
            let inputDescripcion = editarModal.querySelector('.modal-body #descripcion')
            let inputGenero = editarModal.querySelector('.modal-body #genero')

            // Peticion con Ajax a Php para solicitar los datos de el registro a editar para mostrarlo en el formulario
            let url = "getPelicula.php"; //Ruta donde haremos la peticion
            let formData = new FormData(); //Objeto Para pasar los datos, elementos que necesitamos enviar
            formData.append('id', id); //Identificamos con su id que hemos definido arriba, este asi sera el que le pasaremos a getPelicula.php para que nos busque el registro con ese id

            // Peticion Ajax de forma nativa
            fetch(url, {
                method: "POST",
                body: formData
            }).then(response => response.json())
            .then(data => { //Lo que nos arroje lo guardamos en data

                // Pasamos los valores a los inputs correspondientes donde se debe mostrar para editar la pelicula
                inputId.value = data.id
                inputNombre.value = data.nombre
                inputDescripcion.value = data.descripcion
                inputGenero.value = data.id_genero

            }).catch(err => console.log(err))
            

        })

        eliminarModal.addEventListener('shown.bs.modal', event => {
            let button = event.relatedTarget //Obtenemos el boton al que se le dio clic
            let id = button.getAttribute('data-bs-id') //Obtenemos el id del registro que quiero eliminar

            // Al input escondido que tenemos en eliminarModal.php le pasaremos el id de la pelicula la cualqueremos eliminar
            eliminarModal.querySelector('.modal-footer #id').value = id;
        })
    </script>


    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>