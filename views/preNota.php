<?php include 'db.php'; ?>

<main>
<div class="container-fluid px-4">

  <div class="page-header mt-3">
    <div>
      <h1><a href="?pagina=home" style="color:var(--gray-400);text-decoration:none;font-size:1rem;margin-right:6px;">←</a> Pré-nota fiscal</h1>
      <p>Gere um documento simplificado de saída</p>
    </div>
  </div>

  <!-- Aviso -->
  <div class="alert mb-4" style="background:var(--warning-bg);border:1px solid var(--warning-color);border-radius:var(--radius-sm);color:var(--warning-color);font-size:0.85rem;">
    <i class="fas fa-exclamation-triangle me-2"></i>
    Este documento <strong>não substitui</strong> a Nota Fiscal Eletrônica (NF-e) oficial.
  </div>

  <div class="row g-4">

    <!-- Formulário -->
    <div class="col-12 col-lg-6">
      <div class="card">
        <div class="card-header">Dados do destinatário</div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Nome / Razão social</label>
            <input type="text" class="form-control" id="dest-nome" placeholder="Nome do cliente">
          </div>
          <div class="mb-4">
            <label class="form-label">CNPJ / CPF</label>
            <input type="text" class="form-control" id="dest-doc" placeholder="000.000.000-00">
          </div>
        </div>

        <div class="card-header">Itens da nota</div>
        <div class="card-body">
          <div class="form-section-title">Produto · Qtd · Valor</div>

          <div id="itens-lista"></div>

          <button onclick="adicionarItem()" class="btn btn-outline-secondary btn-sm mt-2">
            <i class="fas fa-plus me-1"></i> Adicionar item
          </button>

          <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top:1px solid var(--gray-200);">
            <strong style="font-size:1rem;">Total</strong>
            <strong id="total-geral" style="font-size:1.1rem; color:var(--gray-800);">R$ 0,00</strong>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <button onclick="gerarNota()" class="btn btn-primary flex-fill py-2">
          <i class="fas fa-file-alt me-2"></i>Gerar pré-nota
        </button>
        <button onclick="exportarPDF()" class="btn btn-outline-secondary flex-fill py-2">
          <i class="fas fa-file-pdf me-2"></i>Exportar PDF
        </button>
      </div>
    </div>

    <!-- Preview da nota -->
    <div class="col-12 col-lg-6">
      <div class="card" id="nota-preview" style="display:none;">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="fas fa-file-alt me-2"></i>Pré-nota gerada</span>
          <span id="nota-numero" style="font-size:0.8rem;color:var(--gray-400);"></span>
        </div>
        <div class="card-body" id="nota-conteudo" style="font-size:0.875rem;">
        </div>
      </div>
    </div>

  </div>
</div>
</main>

<!-- Template de item -->
<template id="tpl-item">
  <div class="item-row d-flex gap-2 align-items-center mb-2">
    <select class="form-select form-select-sm item-produto" style="flex:2;" onchange="atualizarPreco(this)">
      <option value="" disabled selected>Produto</option>
      <?php
        $prods = mysqli_query($conexao, "SELECT id, nome, preco FROM produtos ORDER BY nome");
        while($p = mysqli_fetch_assoc($prods)):
      ?>
      <option value="<?php echo $p['id']; ?>" data-preco="<?php echo $p['preco']; ?>" data-nome="<?php echo htmlspecialchars($p['nome']); ?>">
        <?php echo htmlspecialchars($p['nome']); ?>
      </option>
      <?php endwhile; ?>
    </select>
    <input type="number" class="form-control form-control-sm item-qtd" placeholder="Qtd" min="1" value="1" style="flex:1;" oninput="calcularTotal()">
    <span class="item-valor" style="flex:1.2;font-size:0.82rem;color:var(--gray-600);white-space:nowrap;">R$ 0,00</span>
    <button onclick="removerItem(this)" style="background:none;border:none;color:var(--danger-color);cursor:pointer;font-size:1rem;">×</button>
  </div>
</template>

<script>
const produtos = {};
<?php
  $prods2 = mysqli_query($conexao, "SELECT id, nome, preco FROM produtos ORDER BY nome");
  while($p = mysqli_fetch_assoc($prods2)):
?>
produtos[<?php echo $p['id']; ?>] = { nome: "<?php echo addslashes($p['nome']); ?>", preco: <?php echo $p['preco']; ?> };
<?php endwhile; ?>

function adicionarItem() {
  const tpl = document.getElementById('tpl-item').content.cloneNode(true);
  document.getElementById('itens-lista').appendChild(tpl);
  calcularTotal();
}

function removerItem(btn) {
  btn.closest('.item-row').remove();
  calcularTotal();
}

function atualizarPreco(sel) {
  const opt = sel.selectedOptions[0];
  const preco = parseFloat(opt.dataset.preco) || 0;
  const row = sel.closest('.item-row');
  const qtd = parseInt(row.querySelector('.item-qtd').value) || 1;
  row.querySelector('.item-valor').textContent = 'R$ ' + (preco * qtd).toFixed(2).replace('.', ',');
  calcularTotal();
}

function calcularTotal() {
  let total = 0;
  document.querySelectorAll('.item-row').forEach(row => {
    const sel = row.querySelector('.item-produto');
    const opt = sel.selectedOptions[0];
    const preco = parseFloat(opt?.dataset?.preco) || 0;
    const qtd = parseInt(row.querySelector('.item-qtd').value) || 1;
    const subtotal = preco * qtd;
    row.querySelector('.item-valor').textContent = 'R$ ' + subtotal.toFixed(2).replace('.', ',');
    total += subtotal;
  });
  document.getElementById('total-geral').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
}

function gerarNota() {
  const nome = document.getElementById('dest-nome').value || '—';
  const doc  = document.getElementById('dest-doc').value  || '—';
  const rows = document.querySelectorAll('.item-row');

  if (rows.length === 0) {
    alert('Adicione pelo menos um item.');
    return;
  }

  const numero = 'PN-' + Date.now().toString().slice(-6);
  const data   = new Date().toLocaleDateString('pt-BR');
  let itensHtml = '';
  let total = 0;

  rows.forEach(row => {
    const sel   = row.querySelector('.item-produto');
    const opt   = sel.selectedOptions[0];
    if (!opt || !opt.value) return;
    const nomeProd = opt.dataset.nome;
    const preco    = parseFloat(opt.dataset.preco) || 0;
    const qtd      = parseInt(row.querySelector('.item-qtd').value) || 1;
    const sub      = preco * qtd;
    total += sub;
    itensHtml += `<tr>
      <td style="padding:6px 4px;">${nomeProd}</td>
      <td style="padding:6px 4px;text-align:center;">${qtd}</td>
      <td style="padding:6px 4px;text-align:right;">R$ ${preco.toFixed(2).replace('.',',')}</td>
      <td style="padding:6px 4px;text-align:right;font-weight:600;">R$ ${sub.toFixed(2).replace('.',',')}</td>
    </tr>`;
  });

  document.getElementById('nota-numero').textContent = numero + ' · ' + data;
  document.getElementById('nota-conteudo').innerHTML = `
    <div style="border-bottom:1px solid var(--gray-200);padding-bottom:12px;margin-bottom:12px;">
      <div style="font-weight:700;font-size:1rem;color:var(--primary-color);margin-bottom:2px;">Ceik Technology</div>
      <div style="font-size:0.78rem;color:var(--gray-400);">Pré-nota fiscal · ${data}</div>
    </div>
    <div class="form-section-title">Destinatário</div>
    <div style="margin-bottom:12px;font-size:0.875rem;">
      <div><strong>${nome}</strong></div>
      <div style="color:var(--gray-500);">${doc}</div>
    </div>
    <div class="form-section-title">Itens</div>
    <table style="width:100%;font-size:0.82rem;border-collapse:collapse;">
      <thead>
        <tr style="color:var(--gray-400);font-size:0.72rem;text-transform:uppercase;">
          <th style="padding:4px;">Produto</th>
          <th style="padding:4px;text-align:center;">Qtd</th>
          <th style="padding:4px;text-align:right;">Preço</th>
          <th style="padding:4px;text-align:right;">Subtotal</th>
        </tr>
      </thead>
      <tbody>${itensHtml}</tbody>
    </table>
    <div style="display:flex;justify-content:space-between;margin-top:12px;padding-top:10px;border-top:2px solid var(--gray-200);">
      <strong>Total</strong>
      <strong style="color:var(--primary-color);">R$ ${total.toFixed(2).replace('.',',')}</strong>
    </div>
  `;
  document.getElementById('nota-preview').style.display = 'block';

  // Salvar no banco
  fetch('salvaPreNota.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
      valor_total: total,
      dados: { destinatario: {nome, doc}, itens: [...rows].map(r => {
        const s = r.querySelector('.item-produto').selectedOptions[0];
        return { id: s?.value, nome: s?.dataset?.nome, qtd: r.querySelector('.item-qtd').value, preco: s?.dataset?.preco };
      })}
    })
  });
}

function exportarPDF() {
  const preview = document.getElementById('nota-preview');
  if (preview.style.display === 'none') {
    alert('Gere a nota primeiro.');
    return;
  }
  window.print();
}

// Adicionar primeiro item automaticamente
adicionarItem();
</script>

<style>
@media print {
  #layoutSidenav_nav, .sb-topnav, .page-header, .col-lg-6:first-child, footer { display: none !important; }
  .col-lg-6:last-child { width: 100% !important; flex: none !important; }
  .card { box-shadow: none !important; border: 1px solid #ccc !important; }
}
</style>