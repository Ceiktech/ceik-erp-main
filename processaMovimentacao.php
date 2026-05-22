<?php
include 'db.php';

$id_produto = $_POST['id_produto'];
$tipo = $_POST['tipo'];
$quantidade = $_POST['quantidade'];
$observacao = $_POST['observacao'];

/* 1. SALVAR MOVIMENTAÇÃO */
mysqli_query($conexao, "
INSERT INTO movimentacoes
(id_produto, tipo, quantidade, observacao)
VALUES
('$id_produto','$tipo','$quantidade','$observacao')
");

/* 2. ATUALIZAR ESTOQUE */

if($tipo == "entrada"){
    $query = "UPDATE estoque 
              SET quantidade = quantidade + $quantidade,
              atualizado_em = NOW()
              WHERE id_produto = $id_produto";
}
else{
    $query = "UPDATE estoque 
              SET quantidade = quantidade - $quantidade,
              atualizado_em = NOW()
              WHERE id_produto = $id_produto";
}

mysqli_query($conexao, $query);

header("location:index.php?pagina=movimentacoes");
?>