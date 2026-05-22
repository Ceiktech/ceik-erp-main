<?php
include 'db.php';

$query = "
SELECT m.*, p.nome 
FROM movimentacoes m
INNER JOIN produtos p ON p.id = m.id_produto
ORDER BY m.data DESC
";

$consulta = mysqli_query($conexao, $query);
?>

<main>
<div class="container-fluid px-4">

<h1 class="mt-4">Movimentações de Estoque</h1>

<div class="card mb-4">
<div class="card-header">
📦 Entradas e Saídas
</div>

<div class="card-body">

<table class="table table-bordered">

<thead>
<tr>
    <th>Produto</th>
    <th>Tipo</th>
    <th>Quantidade</th>
    <th>Data</th>
    <th>Observação</th>
</tr>
</thead>

<tbody>

<?php while($linha = mysqli_fetch_assoc($consulta)) { ?>

<tr>

<td><?php echo $linha['nome']; ?></td>

<td>
<?php if($linha['tipo'] == 'entrada'){ ?>
    <span class="badge bg-success">Entrada</span>
<?php } else { ?>
    <span class="badge bg-danger">Saída</span>
<?php } ?>
</td>

<td><?php echo $linha['quantidade']; ?></td>

<td><?php echo $linha['data']; ?></td>

<td><?php echo $linha['observacao']; ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>
</div>

</div>
</main>