<?php
include 'db.php';
session_start();

if (!isset($_SESSION['login'])) {
    header('location: index.php');
    exit;
}

$id_usuario    = intval($_SESSION['id']);
$nome          = mysqli_real_escape_string($conexao, trim($_POST['nome'] ?? ''));
$codigo_barras = mysqli_real_escape_string($conexao, trim($_POST['codigo_barras'] ?? ''));
$categoria     = mysqli_real_escape_string($conexao, trim($_POST['categoria'] ?? ''));
$preco         = floatval(str_replace(',', '.', $_POST['preco'] ?? '0'));
$quantidade    = intval($_POST['quantidade'] ?? 0);
$qtd_minima    = intval($_POST['qtd_minima'] ?? 0);
$data_venc     = !empty($_POST['data_vencimento']) ? "'" . mysqli_real_escape_string($conexao, $_POST['data_vencimento']) . "'" : "NULL";

// Upload de foto (salvo como base64 no banco)
$foto = '';
if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $mimeTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];

    if (in_array($extensao, $extensoesPermitidas) && $_FILES['foto']['size'] <= 2097152) {
        $conteudo = file_get_contents($_FILES['foto']['tmp_name']);
        $mime     = $mimeTypes[$extensao] ?? 'image/jpeg';
        $foto     = 'data:' . $mime . ';base64,' . base64_encode($conteudo);
    }
}

$fotoSafe = mysqli_real_escape_string($conexao, $foto);

$query = "INSERT INTO produtos
          (id_usuario, nome, codigo_barras, categoria, preco, qtd_minima, data_vencimento, quantidade, foto)
          VALUES
          ($id_usuario, '$nome', '$codigo_barras', '$categoria', $preco, $qtd_minima, $data_venc, $quantidade, '$fotoSafe')";

$result = mysqli_query($conexao, $query);

if ($result) {
    $id_produto = mysqli_insert_id($conexao);

    // Insere também na tabela estoque
    mysqli_query($conexao,
        "INSERT INTO estoque (id_produto, quantidade, atualizado_em)
         VALUES ($id_produto, $quantidade, NOW())
         ON DUPLICATE KEY UPDATE quantidade = $quantidade, atualizado_em = NOW()"
    );

    // Se quantidade inicial > 0, registra automaticamente movimentação de entrada
    if ($quantidade > 0) {
        $dataHoje   = date('Y-m-d H:i:s');
        $obsEntrada = mysqli_real_escape_string($conexao, 'Estoque inicial');
        mysqli_query($conexao,
            "INSERT INTO movimentacoes (id_produto, tipo, quantidade, data, observacao)
             VALUES ($id_produto, 'entrada', $quantidade, '$dataHoje', '$obsEntrada')"
        );
    }

    header('location: index.php?pagina=produtos&cadastroOk=1');
} else {
    header('location: index.php?pagina=novoProduto&erro=db');
}
exit;