<?php
include 'db.php';
$uid = intval($_SESSION['id']);
$isAdm = ($_SESSION['tipo'] ?? '') === 'admin';
$filtroMov = $isAdm ? '' : "WHERE id_usuario = $uid";
$produtos = mysqli_query($conexao, "SELECT * FROM produtos $filtroMov ORDER BY nome");
?>

<main>
<div class="container-fluid px-4">

  <div class="page-header mt-3">
    <div>
      <h1><a href="?pagina=home" style="color:var(--gray-400);text-decoration:none;font-size:1rem;margin-right:6px;">←</a> Movimentação</h1>
      <p>Registre uma entrada ou saída de estoque</p>
    </div>
  </div>

  <div class="card" style="max-width: 520px;">
    <div class="card-body">

      <form method="post" action="processaMovimentacao.php">

        <!-- Toggle Entrada/Saída -->
        <div class="form-section-title">Tipo</div>
        <div class="tipo-toggle mb-3">
          <input type="radio" name="tipo" id="tipo_entrada" value="entrada" checked>
          <label for="tipo_entrada" class="entrada">Entrada</label>
          <input type="radio" name="tipo" id="tipo_saida" value="saida">
          <label for="tipo_saida" class="saida">Saída</label>
        </div>

        <div class="mb-3">
          <label class="form-label">Produto *</label>
          <select name="id_produto" class="form-select" required>
            <option value="" disabled selected>Selecione um produto</option>
            <?php while($p = mysqli_fetch_assoc($produtos)): ?>
            <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nome']); ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Quantidade *</label>
          <input type="number" name="quantidade" class="form-control" min="1" placeholder="0" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Data</label>
          <input type="date" name="data" class="form-control" value="<?php echo date('Y-m-d'); ?>">
        </div>

        <div class="mb-4">
          <label class="form-label">Observação (opcional)</label>
          <textarea name="observacao" class="form-control" rows="3" placeholder="Descreva o motivo da movimentação..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2">Confirmar movimentação</button>

      </form>

    </div>
  </div>

</div>
</main>