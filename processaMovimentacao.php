<?php
include 'db.php';
session_start();

if (!isset($_SESSION['login'])) {
    header('location: index.php');
    exit;
}

$id_produto  = intval($_POST['id_produto']);
$tipo        = $_POST['tipo'] === 'entrada' ? 'entrada' : 'saida';
$quantidade  = intval($_POST['quantidade']);
$data        = !empty($_POST['data']) ? $_POST['data'] : date('Y-m-d');
$observacao  = mysqli_real_escape_string($conexao, trim($_POST['observacao'] ?? ''));
$id_usuario  = intval($_SESSION['id']);

// Verifica se o produto pertence ao usuário (ou admin)
$tipo_usuario = $_SESSION['tipo'] ?? 'usuario';
if ($tipo_usuario !== 'admin') {
    $check = mysqli_query($conexao, "SELECT id FROM produtos WHERE id = $id_produto AND id_usuario = $id_usuario");
    if (mysqli_num_rows($check) === 0) {
        header('location: index.php?pagina=movimentacoes&erro=acesso');
        exit;
    }
}

// Para saída: verifica se tem estoque suficiente
if ($tipo === 'saida') {
    $estoqueRes = mysqli_query($conexao, "SELECT quantidade FROM produtos WHERE id = $id_produto");
    $estoqueAtual = mysqli_fetch_assoc($estoqueRes);
    if (!$estoqueAtual || $estoqueAtual['quantidade'] < $quantidade) {
        header('location: index.php?pagina=novaMovimentacao&erro=estoque');
        exit;
    }
}

// 1. Salva a movimentação
$dataCompleta = $data . ' ' . date('H:i:s');
mysqli_query($conexao,
    "INSERT INTO movimentacoes (id_produto, tipo, quantidade, data, observacao)
     VALUES ($id_produto, '$tipo', $quantidade, '$dataCompleta', '$observacao')"
);

// 2. Atualiza produtos.quantidade (tabela principal que a view usa)
if ($tipo === 'entrada') {
    mysqli_query($conexao,
        "UPDATE produtos SET quantidade = quantidade + $quantidade WHERE id = $id_produto"
    );
} else {
    mysqli_query($conexao,
        "UPDATE produtos SET quantidade = quantidade - $quantidade WHERE id = $id_produto"
    );
}

// 3. Atualiza tabela estoque (se existir)
if ($tipo === 'entrada') {
    mysqli_query($conexao,
        "UPDATE estoque SET quantidade = quantidade + $quantidade, atualizado_em = NOW() WHERE id_produto = $id_produto"
    );
} else {
    mysqli_query($conexao,
        "UPDATE estoque SET quantidade = quantidade - $quantidade, atualizado_em = NOW() WHERE id_produto = $id_produto"
    );
}

header('location: index.php?pagina=movimentacoes&mov=ok');
exit;