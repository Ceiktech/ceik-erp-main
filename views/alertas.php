<?php
include 'db.php';

$uid    = intval($_SESSION['id']);
$isAdm  = ($_SESSION['tipo'] ?? '') === 'admin';
$filtro = $isAdm ? '' : "AND p.id_usuario = $uid";

$alertas = mysqli_query($conexao, "
  SELECT p.id, p.nome, p.quantidade, p.qtd_minima, p.data_vencimento
  FROM produtos p
  WHERE (p.quantidade <= p.qtd_minima
     OR (p.data_vencimento IS NOT NULL AND p.data_vencimento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)))
  $filtro
  ORDER BY p.data_vencimento ASC
");
$totalAlertas = mysqli_num_rows($alertas);
?>

<main>
<div class="container-fluid px-3 px-md-4">

  <div class="page-header mt-3">
    <div>
      <h1 class="fs-5"><i class="fas fa-bell me-2" style="color:var(--warning-color);"></i>Alertas ativos</h1>
      <p class="d-none d-sm-block">Produtos com estoque baixo ou vencimento próximo</p>
    </div>
  </div>

  <?php if($totalAlertas > 0): ?>
  <div class="card">
    <div class="card-header">
      <?php echo $totalAlertas; ?> alerta<?php echo $totalAlertas > 1 ? 's' : ''; ?> ativo<?php echo $totalAlertas > 1 ? 's' : ''; ?>
    </div>
    <div class="card-body px-3 py-0">
      <?php while($linha = mysqli_fetch_assoc($alertas)):
        $diasRestantes = $linha['data_vencimento']
          ? (strtotime($linha['data_vencimento']) - time()) / 86400
          : 999;
        $isBaixo    = $linha['quantidade'] <= $linha['qtd_minima'];
        $dotClass   = ($diasRestantes < 0 || ($isBaixo && $diasRestantes > 30)) ? 'critico' : 'vencendo';
      ?>
      <div class="alerta-item">
        <div class="alerta-dot <?php echo $dotClass; ?>"></div>
        <div style="flex:1;" class="overflow-hidden">
          <div class="alerta-nome text-truncate"><?php echo htmlspecialchars($linha['nome']); ?></div>
          <div class="alerta-sub">
            <?php
            $partes = [];
            if($isBaixo) $partes[] = 'Restam ' . $linha['quantidade'] . ' un (mín: ' . $linha['qtd_minima'] . ')';
            if($linha['data_vencimento'] && $diasRestantes <= 30) {
              if($diasRestantes < 0) $partes[] = 'Vencido';
              else $partes[] = 'Vence em ' . round($diasRestantes) . ' dias (' . date('d/m/Y', strtotime($linha['data_vencimento'])) . ')';
            }
            echo implode(' · ', $partes);
            ?>
          </div>
        </div>
        <span class="badge-<?php echo $dotClass; ?> flex-shrink-0">
          <?php echo $dotClass === 'critico' ? 'Crítico' : 'Vencendo'; ?>
        </span>
      </div>
      <?php endwhile; ?>
    </div>
  </div>

  <?php else: ?>
  <div class="card">
    <div class="card-body text-center py-5">
      <i class="fas fa-check-circle fa-2x mb-2" style="color:var(--success-color);"></i>
      <h6 style="color:var(--gray-600);">Tudo em ordem!</h6>
      <p style="color:var(--gray-400);font-size:0.875rem;margin:0;">Nenhum alerta de estoque ou vencimento.</p>
    </div>
  </div>
  <?php endif; ?>

</div>
</main>