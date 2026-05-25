<?php
include 'db.php';

$uid    = intval($_SESSION['id']);
$isAdm  = ($_SESSION['tipo'] ?? '') === 'admin';
$filtro = $isAdm ? '' : "WHERE id_usuario = $uid";
$join   = $isAdm ? '' : "AND p.id_usuario = $uid";

$totalProdutos = mysqli_fetch_assoc(mysqli_query($conexao,
  "SELECT COUNT(*) as total FROM produtos $filtro"));

$estoqueBaixo = mysqli_fetch_assoc(mysqli_query($conexao,
  "SELECT COUNT(*) as total FROM produtos
   WHERE quantidade <= qtd_minima " . ($isAdm ? '' : "AND id_usuario = $uid")));

$vencendo = mysqli_fetch_assoc(mysqli_query($conexao,
  "SELECT COUNT(*) as total FROM produtos
   WHERE data_vencimento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
   AND data_vencimento IS NOT NULL " . ($isAdm ? '' : "AND id_usuario = $uid")));

$alertasRecentes = mysqli_query($conexao,
  "SELECT nome, quantidade, qtd_minima, data_vencimento
   FROM produtos
   WHERE (quantidade <= qtd_minima
      OR (data_vencimento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND data_vencimento IS NOT NULL))
   " . ($isAdm ? '' : "AND id_usuario = $uid") . "
   ORDER BY data_vencimento ASC LIMIT 5");
?>

<main>
<div class="container-fluid px-3 px-md-4">

  <div class="page-header mt-3">
    <div>
      <h1 class="fs-5 fs-md-4">Olá, <?php echo htmlspecialchars($_SESSION['nome'] ?? 'usuário'); ?> 👋</h1>
      <p class="d-none d-sm-block">Aqui está o resumo do seu estoque hoje.</p>
    </div>
  </div>

  <div class="row g-2 g-md-3 mb-4">
    <div class="col-6 col-xl-3">
      <div class="stat-card h-100">
        <div class="stat-label">Total de produtos</div>
        <div class="stat-value text-primary"><?php echo $totalProdutos['total']; ?></div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card h-100">
        <div class="stat-label">Itens críticos</div>
        <div class="stat-value text-danger"><?php echo $estoqueBaixo['total']; ?></div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card h-100">
        <div class="stat-label">Vencendo em 30 dias</div>
        <div class="stat-value text-warning"><?php echo $vencendo['total']; ?></div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card h-100">
        <div class="stat-label">Ações rápidas</div>
        <div class="d-flex gap-2 mt-2">
          <a href="?pagina=novaMovimentacao" class="btn btn-primary btn-sm flex-fill">+ Entrada</a>
          <a href="?pagina=novaMovimentacao" class="btn btn-outline-secondary btn-sm flex-fill">- Saída</a>
        </div>
        <div class="mt-2">
          <a href="?pagina=preNota" class="btn btn-outline-secondary btn-sm w-100">Gerar pré-nota</a>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
      <i class="fas fa-bell" style="color:var(--warning-color);"></i>
      Alertas ativos
    </div>
    <div class="card-body px-3 py-0">
      <?php
      $count = 0;
      while($linha = mysqli_fetch_assoc($alertasRecentes)):
        $count++;
        $diasRestantes = $linha['data_vencimento']
          ? (strtotime($linha['data_vencimento']) - time()) / 86400
          : 999;
        $isBaixo    = $linha['quantidade'] <= $linha['qtd_minima'];
        $isVencendo = $linha['data_vencimento'] && $diasRestantes <= 30;
        $dotClass   = ($diasRestantes < 0 || $isBaixo) ? 'critico' : 'vencendo';
      ?>
      <div class="alerta-item">
        <div class="alerta-dot <?php echo $dotClass; ?>"></div>
        <div class="flex-grow-1 overflow-hidden">
          <div class="alerta-nome text-truncate"><?php echo htmlspecialchars($linha['nome']); ?></div>
          <div class="alerta-sub">
            <?php if($isBaixo) echo 'Restam ' . $linha['quantidade'] . ' un'; ?>
            <?php if($isVencendo && $linha['data_vencimento'])
              echo ($isBaixo ? ' · ' : '') . 'Vence em ' . date('d/m/Y', strtotime($linha['data_vencimento'])); ?>
          </div>
        </div>
        <span class="badge-<?php echo $dotClass; ?> flex-shrink-0">
          <?php echo $dotClass === 'critico' ? 'Crítico' : 'Vencendo'; ?>
        </span>
      </div>
      <?php endwhile; ?>
      <?php if($count === 0): ?>
      <div class="text-center py-3" style="color:var(--gray-400);font-size:0.875rem;">
        <i class="fas fa-check-circle me-2" style="color:var(--success-color);"></i>Nenhum alerta ativo
      </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="d-grid gap-2 d-md-none mb-4">
    <a href="?pagina=novaMovimentacao" class="btn btn-primary py-2">
      <i class="fas fa-plus me-2"></i>Nova Movimentação
    </a>
    <a href="?pagina=produtos" class="btn btn-outline-secondary py-2">
      <i class="fas fa-boxes me-2"></i>Ver Estoque
    </a>
  </div>

</div>
</main>