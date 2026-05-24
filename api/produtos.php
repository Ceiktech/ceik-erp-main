<?php

include("../db.php");

$sql = "SELECT * FROM produtos";
$result = mysqli_query($conexao, $sql);

$dados = [];

while($row = mysqli_fetch_assoc($result)){
    $dados[] = $row;
}

header('Content-Type: application/json');

echo json_encode($dados);

?>