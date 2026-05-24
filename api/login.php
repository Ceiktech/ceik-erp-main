<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("../db.php");

$dados = json_decode(file_get_contents("php://input"), true);

$email = $dados['email'];
$senha = $dados['senha'];

$sql = "SELECT * FROM usuarios
        WHERE email = '$email'
        AND senha = '$senha'";

$result = mysqli_query($conexao, $sql);

if(mysqli_num_rows($result) > 0){

    $usuario = mysqli_fetch_assoc($result);

    echo json_encode([
        "status" => "ok",
        "usuario" => $usuario['nome']
    ]);

}else{

    echo json_encode([
        "status" => "erro",
        "mensagem" => "Email ou senha inválidos"
    ]);

}