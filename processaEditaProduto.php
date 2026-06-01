<?php
include 'db.php';
session_start();

if (!isset($_SESSION['login'])) {
    header('location: index.php');
    exit;
}

$id            = intval($_POST['id']);
$id_usuario    = intval($_SESSION['id']);
$tipo_usuario  = $_SESSION['tipo'] ?? 'usuario';

// Segurança: usuário só edita seus próprios produtos
if ($tipo_usuario !== 'admin') {
    $check = mysqli_query($conexao, "SELECT id FROM produtos WHERE id = $id AND id_usuario = $id_usuario");
    if (mysqli_num_rows($check) === 0) {
        header('location: index.php?pagina=produtos&erro=acesso');
        exit;
    }
}

$nome          = mysqli_real_escape_string($conexao, trim($_POST['nome'] ?? ''));
$codigo_barras = mysqli_real_escape_string($conexao, trim($_POST['codigo_barras'] ?? ''));
$categoria     = mysqli_real_escape_string($conexao, trim($_POST['categoria'] ?? ''));
$preco         = floatval(str_replace(',', '.', $_POST['preco'] ?? '0'));
$quantidade    = intval($_POST['quantidade'] ?? 0);
$qtd_minima    = intval($_POST['qtd_minima'] ?? 0);
$data_venc     = !empty($_POST['data_vencimento'])
                 ? "'" . mysqli_real_escape_string($conexao, $_POST['data_vencimento']) . "'"
                 : "NULL";

// Upload de foto (salvo como base64 no banco)
$queryFoto = '';
if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $mimeTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];

    if (in_array($extensao, $extensoesPermitidas) && $_FILES['foto']['size'] <= 2097152) { // max 2MB
        $conteudo    = file_get_contents($_FILES['foto']['tmp_name']);
        $mime        = $mimeTypes[$extensao] ?? 'image/jpeg';
        $base64      = 'data:' . $mime . ';base64,' . base64_encode($conteudo);
        $caminhoFoto = mysqli_real_escape_string($conexao, $base64);
        $queryFoto   = ", foto='$caminhoFoto'";
    }
}

$query = "UPDATE produtos SET
    nome          = '$nome',
    codigo_barras = '$codigo_barras',
    categoria     = '$categoria',
    preco         = $preco,
    quantidade    = $quantidade,
    qtd_minima    = $qtd_minima,
    data_vencimento = $data_venc
    $queryFoto
    WHERE id = $id";

mysqli_query($conexao, $query);

// Sincroniza tabela estoque
mysqli_query($conexao,
    "UPDATE estoque SET quantidade = $quantidade, atualizado_em = NOW() WHERE id_produto = $id"
);

header('location: index.php?pagina=produtos&editaOk=1');
exit;