<?php
include 'db.php';
session_start();

$id_usuario = 1;

$nome = $_POST['nome'];
$codigo_barras = $_POST['codigo_barras'];
$categoria = $_POST['categoria'];
$preco = str_replace(",", ".", $_POST['preco']);
$quantidade = $_POST['quantidade'];
$qtd_minima = $_POST['qtd_minima'];
$data_vencimento = $_POST['data_vencimento'];

$foto = "";

if($_FILES['foto']['name'] != ''){

    $pasta = "arquivos/";
    $nomeArquivo = uniqid() . ".jpg";

    move_uploaded_file($_FILES['foto']['tmp_name'], $pasta.$nomeArquivo);

    $foto = $pasta.$nomeArquivo;
}

$query = "INSERT INTO produtos
(id_usuario,nome,codigo_barras,categoria,preco,qtd_minima,data_vencimento,quantidade,foto)

VALUES

('$id_usuario','$nome','$codigo_barras','$categoria','$preco','$qtd_minima','$data_vencimento','$quantidade','$foto')";

mysqli_query($conexao,$query);

header('location:index.php?pagina=produtos&cadastroOk');
?>