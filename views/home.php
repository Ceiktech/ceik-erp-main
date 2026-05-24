<?php
include 'db.php';

$totalProdutos = mysqli_fetch_assoc(mysqli_query($conexao,
  "SELECT COUNT(*) as total FROM produtos"));

$estoqueBaixo = mysqli_fetch_assoc(mysqli_query($conexao,
  "SELECT COUNT(*) as total FROM produtos WHERE quantidade <= qtd_minima"));

$vencendo = mysqli_fetch_assoc(mysqli_query($conexao,
  "SELECT COUNT(*) as total FROM produtos
   WHERE data_vencimento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
   AND data_vencimento IS NOT NULL"));

$alertasRecentes = mysqli_query($conexao,
  "SELECT nome, quantidade, qtd_minima, data_vencimento
   FROM produtos
   WHERE quantidade <= qtd_minima
      OR data_vencimento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
   ORDER BY data_vencimento ASC LIMIT 5");
?>

<main>
<div class="container-fluid px-4">

  <!-- Saudação -->
  <div class="page-header mt-3">
    <div>
      <h1>Olá, <?php echo htmlspecialchars($_SESSION['nome'] ?? 'usuário'); ?> 👋</h1>
      <p>Aqui está o resumo do seu estoque hoje.</p>
    </div>
  </div>

  <!-- Stat Cards -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
      <div class="stat-card">
        <div class="stat-label">Total de produtos</div>
        <div class="stat-value text-primary"><?php echo $totalProdutos['total']; ?></div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card">
        <div class="stat-label">Itens críticos</div>
        <div class="stat-value text-danger"><?php echo $estoqueBaixo['total']; ?></div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card">
        <div class="stat-label">Vencendo em 30 dias</div>
        <div class="stat-value text-warning"><?php echo $vencendo['total']; ?></div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card">
        <div class="stat-label">Ações rápidas</div>
        <div class="d-flex gap-2 mt-2">
          <a href="?pagina=novaMovimentacao" class="btn btn-primary btn-sm flex-fill">+ Entrada</a>
          <a href="?pagina=novaMovimentacao" class="btn btn-outline-secondary btn-sm flex-fill">- Saída</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Alertas Ativos -->
  <div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
      <i class="fas fa-bell" style="color: var(--warning-color);"></i>
      Alertas ativos
    </div>
    <div class="card-body">
      <?php
      $count = 0;
      while($linha = mysqli_fetch_assoc($alertasRecentes)):
        $count++;
        $diasRestantes = (strtotime($linha['data_vencimento']) - time()) / 86400;
        $isBaixo = $linha['quantidade'] <= $linha['qtd_minima'];
        $isVencendo = $linha['data_vencimento'] && $diasRestantes <= 30;
        $dotClass = ($diasRestantes < 0 || $isBaixo) ? 'critico' : 'vencendo';
      ?>
      <div class="alerta-item">
        <div class="alerta-dot <?php echo $dotClass; ?>"></div>
        <div>
          <div class="alerta-nome"><?php echo htmlspecialchars($linha['nome']); ?></div>
          <div class="alerta-sub">
            <?php if($isBaixo) echo 'Restam ' . $linha['quantidade'] . ' unidades'; ?>
            <?php if($isVencendo && $linha['data_vencimento']) echo ($isBaixo ? ' · ' : '') . 'Vence em ' . date('d/m/Y', strtotime($linha['data_vencimento'])); ?>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
      <?php if($count === 0): ?>
      <div class="text-center py-3" style="color: var(--gray-400); font-size: 0.875rem;">
        <i class="fas fa-check-circle me-2" style="color: var(--success-color);"></i>Nenhum alerta ativo
      </div>
      <?php endif; ?>
    </div>
  </div>

</div>
</main>
