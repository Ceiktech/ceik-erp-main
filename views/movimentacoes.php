<?php
include 'db.php';

$uid    = intval($_SESSION['id']);
$isAdm  = ($_SESSION['tipo'] ?? '') === 'admin';
$filtroUser = $isAdm ? '' : "AND p.id_usuario = $uid";

// Filtros via GET
$filtroDe  = !empty($_GET['de'])  ? mysqli_real_escape_string($conexao, $_GET['de'])  : '';
$filtroAte = !empty($_GET['ate']) ? mysqli_real_escape_string($conexao, $_GET['ate']) : '';
$filtroTipo = !empty($_GET['tipo']) && in_array($_GET['tipo'], ['entrada','saida'])
              ? $_GET['tipo'] : '';

$wherePeriodo = '';
if ($filtroDe)   $wherePeriodo .= " AND DATE(m.data) >= '$filtroDe'";
if ($filtroAte)  $wherePeriodo .= " AND DATE(m.data) <= '$filtroAte'";
if ($filtroTipo) $wherePeriodo .= " AND m.tipo = '$filtroTipo'";

$query = "
SELECT m.*, p.nome
FROM movimentacoes m
INNER JOIN produtos p ON p.id = m.id_produto
WHERE 1=1 $filtroUser $wherePeriodo
ORDER BY m.data DESC
";
$consulta = mysqli_query($conexao, $query);
?>

<main>
<div class="container-fluid px-3 px-md-4">

  <div class="page-header mt-3">
    <div>
      <h1 class="fs-5">Movimentações</h1>
      <p class="d-none d-sm-block">Histórico de entradas e saídas</p>
    </div>
    <a href="?pagina=novaMovimentacao" class="btn btn-primary btn-sm">
      <i class="fas fa-plus me-1"></i> Nova
    </a>
  </div>

  <!-- Filtros -->
  <form method="get" action="index.php" class="card mb-3">
    <input type="hidden" name="pagina" value="movimentacoes">
    <div class="card-body py-2 px-3">
      <div class="row g-2 align-items-end">
        <div class="col-6 col-md-3">
          <label class="form-label mb-1" style="font-size:0.78rem;">De</label>
          <input type="date" name="de" class="form-control form-control-sm"
                 value="<?php echo htmlspecialchars($filtroDe); ?>">
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label mb-1" style="font-size:0.78rem;">Até</label>
          <input type="date" name="ate" class="form-control form-control-sm"
                 value="<?php echo htmlspecialchars($filtroAte); ?>">
        </div>
        <div class="col-6 col-md-3">
          <label class="form-label mb-1" style="font-size:0.78rem;">Tipo</label>
          <select name="tipo" class="form-select form-select-sm">
            <option value="">Todos</option>
            <option value="entrada" <?php echo $filtroTipo === 'entrada' ? 'selected' : ''; ?>>Entradas</option>
            <option value="saida"   <?php echo $filtroTipo === 'saida'   ? 'selected' : ''; ?>>Saídas</option>
          </select>
        </div>
        <div class="col-6 col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-primary btn-sm flex-fill">
            <i class="fas fa-filter me-1"></i>Filtrar
          </button>
          <a href="?pagina=movimentacoes" class="btn btn-outline-secondary btn-sm flex-fill">
            Limpar
          </a>
        </div>
      </div>
    </div>
  </form>

  <div class="card">
    <div class="card-body p-0 px-3">
      <?php
      $total = mysqli_num_rows($consulta);
      if($total === 0): ?>
        <div class="text-center py-4" style="color:var(--gray-400);font-size:0.875rem;">
          <?php echo ($filtroDe || $filtroAte || $filtroTipo)
            ? 'Nenhuma movimentação encontrada com esses filtros.'
            : 'Nenhuma movimentação registrada ainda.'; ?>
        </div>
      <?php else:
        $dataAnterior = null;
        while($linha = mysqli_fetch_assoc($consulta)):
          $dataFormatada = date('d/m/Y', strtotime($linha['data']));
          $hoje  = date('d/m/Y');
          $ontem = date('d/m/Y', strtotime('-1 day'));
          if($dataFormatada === $hoje)      $label = 'HOJE';
          elseif($dataFormatada === $ontem) $label = 'ONTEM';
          else                              $label = strtoupper($dataFormatada);
      ?>
        <?php if($dataAnterior !== $dataFormatada): ?>
        <div style="font-size:0.7rem;font-weight:700;letter-spacing:0.8px;color:var(--gray-400);padding:14px 0 6px;">
          <?php echo $label; ?>
        </div>
        <?php $dataAnterior = $dataFormatada; endif; ?>

        <div class="mov-item">
          <div class="mov-dot <?php echo $linha['tipo']; ?>">
            <i class="fas fa-arrow-<?php echo $linha['tipo'] === 'entrada' ? 'down' : 'up'; ?>" style="font-size:13px;"></i>
          </div>
          <div class="flex-grow-1 overflow-hidden">
            <div class="mov-nome text-truncate"><?php echo htmlspecialchars($linha['nome']); ?></div>
            <div class="mov-sub">
              <?php echo date('H:i', strtotime($linha['data'])); ?> ·
              <?php echo $linha['tipo'] === 'entrada' ? '+' : '-'; ?><?php echo $linha['quantidade']; ?> un
              <?php if($linha['observacao']): ?> · <?php echo htmlspecialchars($linha['observacao']); ?><?php endif; ?>
            </div>
          </div>
          <span class="badge-<?php echo $linha['tipo']; ?> flex-shrink-0">
            <?php echo $linha['tipo'] === 'entrada' ? 'Entrada' : 'Saída'; ?>
          </span>
        </div>
      <?php endwhile; endif; ?>
    </div>
  </div>

</div>
</main>