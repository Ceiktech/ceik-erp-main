<?php
include 'db.php';

$id = $_POST['id'];
$nome = $_POST['nome'];
$codigo_barras = $_POST['codigo_barras'];
$categoria = $_POST['categoria'];
$preco = str_replace(",", ".", $_POST['preco']);
$quantidade = $_POST['quantidade'];
$qtd_minima = $_POST['qtd_minima'];
$data_vencimento = $_POST['data_vencimento'];

$queryFoto = "";

if($_FILES['foto']['name'] != ''){

    $pasta = "arquivos/";
    $nomeArquivo = uniqid().".jpg";

    move_uploaded_file($_FILES['foto']['tmp_name'], $pasta.$nomeArquivo);

    $queryFoto = ", foto='$pasta$nomeArquivo'";
}

$query = "UPDATE produtos SET

nome='$nome',
codigo_barras='$codigo_barras',
categoria='$categoria',
preco='$preco',
quantidade='$quantidade',
qtd_minima='$qtd_minima',
data_vencimento='$data_vencimento'

$queryFoto

WHERE id='$id'";

$query = str_replace('$queryFoto', $queryFoto, $query);

mysqli_query($conexao,$query);

header('location:index.php?pagina=produtos&editaOk');
?>