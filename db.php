<?php
$host = "tramway.proxy.rlwy.net";
$user = "erp_user";
$pass = "senha_forte"; // senha do painel Railway
$db   = "railway";
$port = 52508; // porta correta

$conexao = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conexao) {
    die("Erro conexão: " . mysqli_connect_error());
}
?>
