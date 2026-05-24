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

  <div class="page-header mt-3">
    <div>
      <h1>Movimentações</h1>
      <p>Histórico de entradas e saídas</p>
    </div>
  </div>

  <!-- Filtro de período -->
  <div class="card mb-3" style="max-width:340px;">
    <div class="card-body py-2 px-3">
      <label class="form-label mb-1">Filtrar por período</label>
      <input type="date" class="form-control form-control-sm" id="filtroPeriodo">
    </div>
  </div>

  <div class="card">
    <div class="card-body p-0 px-3">
      <?php
      $dataAnterior = null;
      $total = mysqli_num_rows($consulta);
      if($total === 0):
      ?>
        <div class="text-center py-4" style="color:var(--gray-400);font-size:0.875rem;">
          Nenhuma movimentação registrada ainda.
        </div>
      <?php else: ?>
      <?php while($linha = mysqli_fetch_assoc($consulta)):
        $dataFormatada = date('d/m/Y', strtotime($linha['data']));
        $hoje = date('d/m/Y');
        $ontem = date('d/m/Y', strtotime('-1 day'));
        if($dataFormatada === $hoje) $label = 'HOJE';
        elseif($dataFormatada === $ontem) $label = 'ONTEM';
        else $label = strtoupper($dataFormatada);
      ?>
        <?php if($dataAnterior !== $dataFormatada): ?>
        <div style="font-size:0.7rem;font-weight:700;letter-spacing:0.8px;color:var(--gray-400);padding:14px 0 6px;">
          <?php echo $label; ?>
        </div>
        <?php $dataAnterior = $dataFormatada; endif; ?>

        <div class="mov-item">
          <div class="mov-dot <?php echo $linha['tipo']; ?>">
            <i class="fas fa-<?php echo $linha['tipo'] === 'entrada' ? 'arrow-down' : 'arrow-up'; ?>" style="font-size:13px;"></i>
          </div>
          <div style="flex:1;">
            <div class="mov-nome"><?php echo htmlspecialchars($linha['nome']); ?></div>
            <div class="mov-sub">
              <?php echo date('H:i', strtotime($linha['data'])); ?> ·
              <?php echo $linha['tipo'] === 'entrada' ? '+' : '-'; ?><?php echo $linha['quantidade']; ?> unidades
              <?php if($linha['observacao']): ?> · <?php echo htmlspecialchars($linha['observacao']); ?><?php endif; ?>
            </div>
          </div>
          <span class="badge-<?php echo $linha['tipo']; ?>">
            <?php echo $linha['tipo'] === 'entrada' ? 'Entrada' : 'Saída'; ?>
          </span>
        </div>
      <?php endwhile; ?>
      <?php endif; ?>
    </div>
  </div>

</div>
</main>
