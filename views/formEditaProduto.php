<?php
include 'db.php';

$id = $_POST['id'];

$uid = intval($_SESSION['id']);
$isAdm = ($_SESSION['tipo'] ?? '') === 'admin';
$filtroEdit = $isAdm ? "WHERE id='$id'" : "WHERE id='$id' AND id_usuario=$uid";
$query = "SELECT * FROM produtos $filtroEdit";
$consulta = mysqli_query($conexao,$query);

$linha = mysqli_fetch_assoc($consulta);
?>

<div class="container-fluid px-4">

<h1 class="mt-4">Editar Produto</h1>

<div class="card p-4">

<form action="processaEditaProduto.php" method="post" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?php echo $linha['id']; ?>">

<div class="mb-3">
<label>Nome</label>
<input type="text" name="nome" class="form-control"
value="<?php echo $linha['nome']; ?>">
</div>

<div class="mb-3">
<label>Código de Barras</label>
<input type="text" name="codigo_barras" class="form-control"
value="<?php echo $linha['codigo_barras']; ?>">
</div>

<div class="mb-3">
<label>Categoria</label>
<input type="text" name="categoria" class="form-control"
value="<?php echo $linha['categoria']; ?>">
</div>

<div class="mb-3">
<label>Preço</label>
<input type="text" name="preco" class="form-control"
value="<?php echo $linha['preco']; ?>">
</div>

<div class="mb-3">
<label>Quantidade</label>
<input type="number" name="quantidade" class="form-control"
value="<?php echo $linha['quantidade']; ?>">
</div>

<div class="mb-3">
<label>Qtd Mínima</label>
<input type="number" name="qtd_minima" class="form-control"
value="<?php echo $linha['qtd_minima']; ?>">
</div>

<div class="mb-3">
<label>Vencimento</label>
<input type="date" name="data_vencimento" class="form-control"
value="<?php echo $linha['data_vencimento']; ?>">
</div>

<div class="mb-3">
<label>Nova Foto</label>
<input type="file" name="foto" class="form-control">
</div>

<button class="btn btn-primary">
Salvar Alterações
</button>

</form>

</div>
</div>