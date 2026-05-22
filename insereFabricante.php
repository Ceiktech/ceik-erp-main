<?php

include 'db.php';

$fab_nome = $_POST['fab_nome'];
$fab_obs = $_POST['fab_obs'];

$query = "insert into fabricantes(fab_nome, fab_obs) values('$fab_nome', '$fab_obs')";
mysqli_query($conexao, $query);

header('location:index.php?cadastroOk&pagina=fabricantes');
?>
