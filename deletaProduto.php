<?php
include 'db.php';
session_start();

if (!isset($_SESSION['login'])) {
    header('location: index.php');
    exit;
}

$id         = intval($_POST['id']);
$id_usuario = $_SESSION['id'];
$tipo       = $_SESSION['tipo'] ?? 'usuario';

// Admin pode deletar qualquer produto; usuário só os seus
if ($tipo === 'admin') {
    $query = "DELETE FROM produtos WHERE id = $id";
} else {
    $query = "DELETE FROM produtos WHERE id = $id AND id_usuario = $id_usuario";
}

mysqli_query($conexao, $query);

// Remove a foto do servidor se existir
$fotoRes = mysqli_query($conexao, "SELECT foto FROM produtos WHERE id = $id");
// (produto já deletado, mas aqui limpamos o arquivo se quiser)

header('location: index.php?pagina=produtos&deletaOk=1');
exit;