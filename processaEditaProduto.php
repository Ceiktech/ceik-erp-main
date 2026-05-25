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

// Upload de foto
$queryFoto = '';
if (!empty($_FILES['foto']['name'])) {

    $pasta = __DIR__ . '/arquivos/';

    // Cria a pasta se não existir
    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }

    $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (in_array($extensao, $extensoesPermitidas) && $_FILES['foto']['size'] <= 5242880) {
        $nomeArquivo = uniqid('prod_') . '.' . $extensao;
        $destino     = $pasta . $nomeArquivo;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
            $caminhoFoto = mysqli_real_escape_string($conexao, 'arquivos/' . $nomeArquivo);
            $queryFoto   = ", foto='$caminhoFoto'";

            // Remove foto antiga do servidor
            $fotoRes = mysqli_query($conexao, "SELECT foto FROM produtos WHERE id = $id");
            $fotoAntiga = mysqli_fetch_assoc($fotoRes);
            if (!empty($fotoAntiga['foto']) && file_exists(__DIR__ . '/' . $fotoAntiga['foto'])) {
                unlink(__DIR__ . '/' . $fotoAntiga['foto']);
            }
        }
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