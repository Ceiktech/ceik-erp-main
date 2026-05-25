<?php include 'db.php'; ?>

<main>
<div class="container-fluid px-4">

  <div class="page-header mt-3">
    <div>
      <h1>Relatório</h1>
      <p>Movimentações por período</p>
    </div>
  </div>

  <!-- Filtros -->
  <div class="card mb-4" style="max-width:560px;">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-6">
          <label class="form-label">De</label>
          <input type="date" class="form-control" id="filtro-de">
        </div>
        <div class="col-6">
          <label class="form-label">Até</label>
          <input type="date" class="form-control" id="filtro-ate">
        </div>
        <div class="col-12">
          <label class="form-label">Produto (opcional)</label>
          <select class="form-select" id="filtro-produto">
            <option value="">Todos os produtos</option>
            <?php
              $prods = mysqli_query($conexao, "SELECT id, nome FROM produtos ORDER BY nome");
              while($p = mysqli_fetch_assoc($prods)):
            ?>
            <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nome']); ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-12">
          <button onclick="filtrar()" class="btn btn-outline-secondary w-100">
            <i class="fas fa-filter me-2"></i>Filtrar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Totais -->
  <div class="row g-3 mb-4" id="totais-row" style="max-width:560px;">
    <div class="col-6">
      <div class="stat-card">
        <div class="stat-label">Total entradas</div>
        <div class="stat-value text-success" id="total-entradas">—</div>
      </div>
    </div>
    <div class="col-6">
      <div class="stat-card">
        <div class="stat-label">Total saídas</div>
        <div class="stat-value text-danger" id="total-saidas">—</div>
      </div>
    </div>
  </div>

  <!-- Lista de movimentações filtradas -->
  <div class="card" style="max-width:560px;">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Movimentações</span>
      <button onclick="exportarRelatorio()" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-download me-1"></i>Exportar
      </button>
    </div>
    <div class="card-body p-0 px-3" id="lista-movs">
      <div class="text-center py-4" style="color:var(--gray-400);font-size:0.875rem;">
        Selecione um período e clique em Filtrar.
      </div>
    </div>
  </div>

</div>
</main>

<script>
function filtrar() {
  const de      = document.getElementById('filtro-de').value;
  const ate     = document.getElementById('filtro-ate').value;
  const produto = document.getElementById('filtro-produto').value;

  const params = new URLSearchParams({ de, ate, produto });

  fetch('relatorioData.php?' + params)
    .then(r => r.json())
    .then(data => {
      document.getElementById('total-entradas').textContent = '+' + data.total_entradas;
      document.getElementById('total-saidas').textContent   = '-' + data.total_saidas;

      const lista = document.getElementById('lista-movs');
      if (data.movimentacoes.length === 0) {
        lista.innerHTML = '<div class="text-center py-4" style="color:var(--gray-400);font-size:0.875rem;">Nenhuma movimentação no período.</div>';
        return;
      }

      lista.innerHTML = data.movimentacoes.map(m => `
        <div class="mov-item">
          <div class="mov-dot ${m.tipo}">
            <i class="fas fa-arrow-${m.tipo === 'entrada' ? 'down' : 'up'}" style="font-size:13px;"></i>
          </div>
          <div style="flex:1;">
            <div class="mov-nome">${m.nome}</div>
            <div class="mov-sub">${m.data} · ${m.tipo === 'entrada' ? '+' : '-'}${m.quantidade} un${m.observacao ? ' · ' + m.observacao : ''}</div>
          </div>
          <span class="badge-${m.tipo}">${m.tipo === 'entrada' ? 'Entrada' : 'Saída'}</span>
        </div>
      `).join('');
    });
}

function exportarRelatorio() {
  const de      = document.getElementById('filtro-de').value;
  const ate     = document.getElementById('filtro-ate').value;
  const produto = document.getElementById('filtro-produto').value;
  window.open('relatorioExportar.php?de=' + de + '&ate=' + ate + '&produto=' + produto, '_blank');
}

// Carregar últimos 30 dias por padrão
const hoje = new Date();
const mesPassado = new Date();
mesPassado.setDate(hoje.getDate() - 30);
document.getElementById('filtro-ate').value = hoje.toISOString().split('T')[0];
document.getElementById('filtro-de').value  = mesPassado.toISOString().split('T')[0];
filtrar();
</script>