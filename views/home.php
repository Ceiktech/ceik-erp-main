<?php
include 'db.php';

$totalProdutos = mysqli_fetch_assoc(mysqli_query($conexao,
"SELECT COUNT(*) as total FROM produtos"));

$estoqueBaixo = mysqli_fetch_assoc(mysqli_query($conexao,
"SELECT COUNT(*) as total FROM produtos WHERE quantidade <= qtd_minima"));

$vencendo = mysqli_fetch_assoc(mysqli_query($conexao,
"SELECT COUNT(*) as total FROM produtos 
WHERE data_vencimento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)"));

$usuarios = mysqli_fetch_assoc(mysqli_query($conexao,
"SELECT COUNT(*) as total FROM usuarios"));

$ultimos = mysqli_query($conexao,
"SELECT * FROM produtos ORDER BY id DESC LIMIT 5");
?>

<main>
<div class="container-fluid px-4">

<h1 class="mt-4">Dashboard</h1>

<div class="row">

<div class="col-xl-3 col-md-6">
<div class="card bg-primary text-white mb-4">
<div class="card-body">
Total Produtos: <?php echo $totalProdutos['total']; ?>
</div>
</div>
</div>

<div class="col-xl-3 col-md-6">
<div class="card bg-danger text-white mb-4">
<div class="card-body">
Estoque Baixo: <?php echo $estoqueBaixo['total']; ?>
</div>
</div>
</div>

<div class="col-xl-3 col-md-6">
<div class="card bg-warning text-white mb-4">
<div class="card-body">
Vencendo: <?php echo $vencendo['total']; ?>
</div>
</div>
</div>

<div class="col-xl-3 col-md-6">
<div class="card bg-success text-white mb-4">
<div class="card-body">
Usuários: <?php echo $usuarios['total']; ?>
</div>
</div>
</div>

</div>

<div class="card mb-4">
<div class="card-header">
Últimos Produtos Cadastrados
</div>

<div class="card-body">

<table class="table table-bordered">

<tr>
<th>ID</th>
<th>Nome</th>
<th>Categoria</th>
<th>Quantidade</th>
</tr>

<?php while($linha = mysqli_fetch_assoc($ultimos)){ ?>

<tr>
<td><?php echo $linha['id']; ?></td>
<td><?php echo $linha['nome']; ?></td>
<td><?php echo $linha['categoria']; ?></td>
<td><?php echo $linha['quantidade']; ?></td>
</tr>

<?php } ?>

</table>

</div>
</div>

</div>
</main>