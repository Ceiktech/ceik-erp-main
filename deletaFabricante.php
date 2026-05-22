<?php

include 'db.php';

$fab_id= $_POST['fab_id'];

$query = "delete from fabricantes where fab_id = $fab_id";

mysqli_query($conexao, $query);

header('location:index.php?deletaOk&pagina=fabricantes');


