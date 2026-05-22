<?php
include 'db.php';

$produtos = mysqli_query($conexao,"SELECT * FROM produtos");
?>

<main>
<div class="container-fluid px-4">

<h1 class="mt-4">Nova Movimentação</h1>

<div class="card p-4">

<form method="post" action="processaMovimentacao.php">

<div class="mb-3">
<label>Produto</label>
<select name="id_produto" class="form-control">

<?php while($p = mysqli_fetch_assoc($produtos)){ ?>
<option value="<?php echo $p['id']; ?>">
<?php echo $p['nome']; ?>
</option>
<?php } ?>

</select>
</div>

<div class="mb-3">
<label>Tipo</label>
<select name="tipo" class="form-control">
<option value="entrada">Entrada</option>
<option value="saida">Saída</option>
</select>
</div>

<div class="mb-3">
<label>Quantidade</label>
<input type="number" name="quantidade" class="form-control" required>
</div>

<div class="mb-3">
<label>Observação</label>
<input type="text" name="observacao" class="form-control">
</div>

<button class="btn btn-success">
Salvar Movimentação
</button>

</form>

</div>
</div>
</main>