<?php
include 'db.php';
session_start();

if (!isset($_SESSION['login'])) {
    header('location: index.php');
    exit;
}

$id           = intval($_POST['id']);
$id_usuario   = intval($_SESSION['id']);
$tipo_usuario = $_SESSION['tipo'] ?? 'usuario';

// Verifica se o produto pertence ao usuário (admin pode deletar qualquer um)
if ($tipo_usuario !== 'admin') {
    $check = mysqli_query($conexao, "SELECT id FROM produtos WHERE id = $id AND id_usuario = $id_usuario");
    if (mysqli_num_rows($check) === 0) {
        header('location: index.php?pagina=produtos&erro=acesso');
        exit;
    }
}

// Busca o caminho da foto antes de deletar
$fotoRes = mysqli_query($conexao, "SELECT foto FROM produtos WHERE id = $id");
$prodRow = mysqli_fetch_assoc($fotoRes);

// Deleta registros filhos primeiro (respeita as foreign keys)
mysqli_query($conexao, "DELETE FROM movimentacoes WHERE id_produto = $id");
mysqli_query($conexao, "DELETE FROM estoque WHERE id_produto = $id");
mysqli_query($conexao, "DELETE FROM alertas WHERE id_produto = $id");

// Agora deleta o produto
mysqli_query($conexao, "DELETE FROM produtos WHERE id = $id");

// Remove a foto do servidor se existir
if (!empty($prodRow['foto']) && file_exists(__DIR__ . '/' . $prodRow['foto'])) {
    unlink(__DIR__ . '/' . $prodRow['foto']);
}

header('location: index.php?pagina=produtos&deletaOk=1');
exit;