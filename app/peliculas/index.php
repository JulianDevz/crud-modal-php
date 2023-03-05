<?php 

    // Activamos la session para ver los mensajes
    session_start();

    require '../config/database.php'; 

    // Traemos el listado de las peliculas
    $sqlPeliculas = "SELECT p.id, p.nombre, p.descripcion, g.nombre AS genero, p.imagen FROM pelicula AS P
    INNER JOIN genero AS g 
    ON p.id_genero = g.id";

    $peliculas = $conn->query($sqlPeliculas);

    // Directorio de imagenes
    $dir = "posters/";

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

        <hr>

        <!-- Validamo si existe la variable de session msg es porque hay algun mensaje que esta enviando y si existe un color de mensaje, con ese color sera el que pintaremos el mensaje -->
        <?php if(isset($_SESSION['msg']) && isset($_SESSION['color'])) { ?>
                <div class="alert alert-<?= $_SESSION['color']; ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['msg']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
        
        <?php
                // Eliminamos estas variable despues de haberse usado para que no este saliendo de forma innecesaria
                unset($_SESSION['msg']);
                unset($_SESSION['color']);
            } 
        ?>

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

                        <!-- El time concatenado (es un parametro en la url) nos sirve para obtener el tiempo, asi NO se quedara mostrando una imagen antigua por la cache del navegador -->
                        <td><img src="<?=$dir.$row_pelicula["imagen"] ?>" alt="" width="70"> </td>
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
        // Capturamos los modales
        let editarModal = document.getElementById('editarModal')
        let eliminarModal = document.getElementById('eliminarModal')
        let nuevoModal = document.getElementById('nuevoModal')

        nuevoModal.addEventListener('shown.bs.modal', event => {
            nuevoModal.querySelector('.modal-body #nombre').focus();
        })

        // Limpiamos los inputs del nuevo modal al cerrarlo
        nuevoModal.addEventListener('hide.bs.modal', event => {
            nuevoModal.querySelector('.modal-body #nombre').value = ""
            nuevoModal.querySelector('.modal-body #descripcion').value = ""
            nuevoModal.querySelector('.modal-body #genero').value = ""
            nuevoModal.querySelector('.modal-body #poster').value = ""
        })

        // Limpiamos los inputs del modal editar al cerrarlo
        editarModal.addEventListener('hide.bs.modal', event => {
            editarModal.querySelector('.modal-body #nombre').value = ""
            editarModal.querySelector('.modal-body #descripcion').value = ""
            editarModal.querySelector('.modal-body #genero').value = ""
            editarModal.querySelector('.modal-body #img_poster').value = ""
            editarModal.querySelector('.modal-body #poster').value = ""
        })

        // Escuchamos el evento del modal cuando se presione el boton y se carguen todos los inputs del modal
        editarModal.addEventListener('shown.bs.modal', event => {
            let button = event.relatedTarget //Obtenemos el boton al que se le dio clic
            let id = button.getAttribute('data-bs-id') //Obtenemos el id del registro que quiero modificar

            // Accedemos a los elementos del formulario para editar
            let inputId = editarModal.querySelector('.modal-body #id')
            let inputNombre = editarModal.querySelector('.modal-body #nombre')
            let inputDescripcion = editarModal.querySelector('.modal-body #descripcion')
            let inputGenero = editarModal.querySelector('.modal-body #genero')
            let poster = editarModal.querySelector('.modal-body #img_poster')
            let Inputposter = editarModal.querySelector('.modal-body #poster')

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
                poster.src = '<?= $dir ?>' + data.imagen
                Inputposter.value = data.imagen


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