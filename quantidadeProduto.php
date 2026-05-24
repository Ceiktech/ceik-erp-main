<?php
include 'db.php';

$pro_id = $_POST['pro_id'];

$query = "update produtos set pro_quantidade=pro_quantidade-1 where pro_id='$pro_id'";
mysqli_query($conexao, $query);

header('location:index.php?editaOk&pagina=produtos');
?>