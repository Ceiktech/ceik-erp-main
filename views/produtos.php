<?php
include 'db.php';

$query = "SELECT * FROM produtos ORDER BY id DESC";
$consultaProdutos = mysqli_query($conexao, $query);
$totalProdutos = mysqli_num_rows($consultaProdutos);
?>

<main>
<div class="container-fluid px-4">

  <div class="page-header mt-3">
    <div>
      <h1>Estoque</h1>
      <p><?php echo $totalProdutos; ?> produto<?php echo $totalProdutos != 1 ? 's' : ''; ?> cadastrado<?php echo $totalProdutos != 1 ? 's' : ''; ?></p>
    </div>
    <a href="?pagina=novoProduto" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> Novo produto
    </a>
  </div>

  <?php if(isset($_GET['cadastroOk'])): ?>
    <div class="alert alert-success alert-dismissible mb-3" style="border-radius: var(--radius-sm); font-size:0.875rem;">
      <i class="fas fa-check-circle me-2"></i>Produto cadastrado com sucesso!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if(isset($_GET['editaOk'])): ?>
    <div class="alert alert-primary alert-dismissible mb-3" style="border-radius: var(--radius-sm); font-size:0.875rem;">
      <i class="fas fa-edit me-2"></i>Produto atualizado.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if(isset($_GET['deletaOk'])): ?>
    <div class="alert alert-danger alert-dismissible mb-3" style="border-radius: var(--radius-sm); font-size:0.875rem;">
      <i class="fas fa-trash me-2"></i>Produto excluído.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Filtros pill -->
  <div class="filter-pills">
    <span class="filter-pill active">Todos</span>
    <span class="filter-pill" onclick="filtrarStatus('critico')">Críticos</span>
    <span class="filter-pill" onclick="filtrarStatus('vencendo')">Vencendo</span>
    <span class="filter-pill" onclick="filtrarStatus('normal')">Normal</span>
  </div>

  <!-- Busca -->
  <div class="mb-3">
    <input type="text" class="form-control" id="buscaProduto" placeholder="Buscar produto..." oninput="filtrarBusca(this.value)">
  </div>

  <!-- Lista de produtos -->
  <div class="card">
    <div class="card-body p-0">
      <table class="table mb-0" id="tabelaProdutos">
        <thead>
          <tr>
            <th>Produto</th>
            <th>Categoria</th>
            <th>Qtd</th>
            <th>Preço</th>
            <th>Vencimento</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
        <?php while($linha = mysqli_fetch_assoc($consultaProdutos)):
          $diasRestantes = $linha['data_vencimento'] ? (strtotime($linha['data_vencimento']) - time()) / 86400 : 999;
          $isBaixo = $linha['quantidade'] <= $linha['qtd_minima'];
          if($diasRestantes < 0 || $isBaixo) {
            $status = 'critico'; $statusLabel = 'Crítico';
          } elseif($diasRestantes <= 30) {
            $status = 'vencendo'; $statusLabel = 'Vencendo';
          } else {
            $status = 'normal'; $statusLabel = 'Normal';
          }
        ?>
          <tr class="produto-row" data-status="<?php echo $status; ?>">
            <td style="text-align:left !important;">
              <div style="display:flex; align-items:center; gap:10px;">
                <?php if($linha['foto']): ?>
                  <img src="<?php echo $linha['foto']; ?>" width="40" height="40" style="border-radius: var(--radius-sm); object-fit:cover; flex-shrink:0;">
                <?php else: ?>
                  <div style="width:40px;height:40px;background:var(--gray-100);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--gray-400);">
                    <i class="fas fa-image" style="font-size:14px;"></i>
                  </div>
                <?php endif; ?>
                <strong style="font-size:0.875rem;"><?php echo htmlspecialchars($linha['nome']); ?></strong>
              </div>
            </td>
            <td>
              <span style="background:var(--primary-light);color:var(--primary-text);padding:2px 10px;border-radius:var(--radius-full);font-size:0.75rem;font-weight:600;">
                <?php echo htmlspecialchars($linha['categoria']); ?>
              </span>
            </td>
            <td><strong><?php echo $linha['quantidade']; ?></strong> <span style="color:var(--gray-400);font-size:0.75rem;">un</span></td>
            <td style="color:var(--success-color);font-weight:600;">R$ <?php echo number_format($linha['preco'],2,',','.'); ?></td>
            <td style="color:var(--gray-500);font-size:0.82rem;">
              <?php echo $linha['data_vencimento'] ? date('d/m/y', strtotime($linha['data_vencimento'])) : '—'; ?>
            </td>
            <td><span class="badge-<?php echo $status; ?>"><?php echo $statusLabel; ?></span></td>
            <td>
              <div class="d-flex gap-1 justify-content-center">
                <form action="?pagina=formEditaProduto" method="post" style="display:inline;">
                  <input type="hidden" name="id" value="<?php echo $linha['id']; ?>">
                  <button class="btn btn-primary btn-sm" title="Editar"><i class="fas fa-edit"></i></button>
                </form>
                <form action="deletaProduto.php" method="post" style="display:inline;" onsubmit="return confirm('Excluir este produto?');">
                  <input type="hidden" name="id" value="<?php echo $linha['id']; ?>">
                  <button class="btn btn-danger btn-sm" title="Excluir"><i class="fas fa-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
</main>

<script>
function filtrarStatus(status) {
  document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
  event.target.classList.add('active');
  document.querySelectorAll('.produto-row').forEach(row => {
    row.style.display = (status === 'todos' || row.dataset.status === status) ? '' : 'none';
  });
}
document.querySelector('.filter-pill').addEventListener('click', function(){
  document.querySelectorAll('.produto-row').forEach(r => r.style.display = '');
  document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
  this.classList.add('active');
});
function filtrarBusca(val) {
  val = val.toLowerCase();
  document.querySelectorAll('.produto-row').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
  });
}
</script>
