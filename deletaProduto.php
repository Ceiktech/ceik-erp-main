<?php
include 'db.php';

$id = $_POST['id'];

$query = "DELETE FROM produtos WHERE id='$id'";

mysqli_query($conexao,$query);

header('location:index.php?pagina=produtos&deletaOk');
?>