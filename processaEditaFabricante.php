<?php
include 'db.php';

$fab_id=$_POST['fab_id'];
$fab_nome=$_POST['fab_nome'];
$fab_obs=$_POST['fab_obs'];


$query="update fabricantes set fab_nome='$fab_nome', fab_obs='$fab_obs' where fab_id='$fab_id'";
mysqli_query($conexao, $query);

header('location:index.php?editaOk&pagina=fabricantes');