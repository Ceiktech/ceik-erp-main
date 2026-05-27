<?php
session_start();
include 'db.php';

if (!isset($_SESSION['login'])) {
    header('location: index.php');
    exit;
}

$uid     = intval($_SESSION['id']);
$isAdm   = ($_SESSION['tipo'] ?? '') === 'admin';

$de      = $_GET['de']      ?? date('Y-m-01');
$ate     = $_GET['ate']     ?? date('Y-m-d');
$produto = $_GET['produto'] ?? '';

$whereUser = $isAdm ? '' : "AND p.id_usuario = $uid";
$where = "WHERE m.data BETWEEN '$de 00:00:00' AND '$ate 23:59:59' $whereUser";
if ($produto) $where .= " AND m.id_produto = " . intval($produto);

$movs = mysqli_query($conexao,
  "SELECT m.*, p.nome
   FROM movimentacoes m
   INNER JOIN produtos p ON p.id = m.id_produto
   $where
   ORDER BY m.data DESC"
);

$totalEntradas = 0;
$totalSaidas   = 0;
$linhas = [];
while ($row = mysqli_fetch_assoc($movs)) {
    if ($row['tipo'] === 'entrada') $totalEntradas += $row['quantidade'];
    else                            $totalSaidas   += $row['quantidade'];
    $linhas[] = $row;
}

$dataGeracao = date('d/m/Y H:i');
$periodoTexto = date('d/m/Y', strtotime($de)) . ' até ' . date('d/m/Y', strtotime($ate));
$nomeUsuario = htmlspecialchars($_SESSION['nome'] ?? 'Usuário');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Relatório de Movimentações</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 12px; color: #222; background: #fff; }
  .page { max-width: 800px; margin: 0 auto; padding: 32px 36px; }

  /* Cabeçalho */
  .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #1a56db; padding-bottom: 14px; margin-bottom: 18px; }
  .header-title { font-size: 20px; font-weight: 700; color: #1a56db; }
  .header-sub { font-size: 11px; color: #666; margin-top: 3px; }
  .header-right { text-align: right; font-size: 11px; color: #666; line-height: 1.6; }

  /* Aviso */
  .aviso { background: #fffbeb; border: 1px solid #f59e0b; border-radius: 4px; padding: 8px 12px; font-size: 11px; color: #92400e; margin-bottom: 16px; }

  /* Totais */
  .totais { display: flex; gap: 16px; margin-bottom: 18px; }
  .total-card { flex: 1; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px 16px; }
  .total-label { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
  .total-val { font-size: 22px; font-weight: 700; margin-top: 4px; }
  .total-val.entrada { color: #16a34a; }
  .total-val.saida   { color: #dc2626; }

  /* Tabela */
  table { width: 100%; border-collapse: collapse; margin-top: 4px; }
  thead tr { background: #f3f4f6; }
  th { padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; border-bottom: 1px solid #e5e7eb; }
  td { padding: 9px 10px; border-bottom: 1px solid #f3f4f6; font-size: 11.5px; vertical-align: middle; }
  tr:last-child td { border-bottom: none; }

  .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; text-transform: uppercase; }
  .badge.entrada { background: #dcfce7; color: #16a34a; }
  .badge.saida   { background: #fee2e2; color: #dc2626; }
  .qtd.entrada   { color: #16a34a; font-weight: 600; }
  .qtd.saida     { color: #dc2626; font-weight: 600; }

  /* Rodapé */
  .footer { margin-top: 28px; border-top: 1px solid #e5e7eb; padding-top: 12px; font-size: 10px; color: #9ca3af; text-align: center; }

  /* Print */
  @media print {
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .no-print { display: none !important; }
    .page { padding: 20px; }
  }
</style>
</head>
<body>
<div class="page">

  <!-- Botão imprimir (some no print) -->
  <div class="no-print" style="margin-bottom:16px; display:flex; gap:10px;">
    <button onclick="window.print()" style="background:#1a56db;color:#fff;border:none;padding:8px 20px;border-radius:5px;font-size:13px;cursor:pointer;">
      🖨️ Imprimir / Salvar PDF
    </button>
    <button onclick="window.close()" style="background:#f3f4f6;color:#374151;border:none;padding:8px 16px;border-radius:5px;font-size:13px;cursor:pointer;">
      ✕ Fechar
    </button>
  </div>

  <!-- Cabeçalho -->
  <div class="header">
    <div>
      <div class="header-title">Ceik Technology</div>
      <div class="header-sub">Relatório de Movimentações</div>
    </div>
    <div class="header-right">
      <div><strong>Período:</strong> <?php echo $periodoTexto; ?></div>
      <div><strong>Usuário:</strong> <?php echo $nomeUsuario; ?></div>
      <div><strong>Gerado em:</strong> <?php echo $dataGeracao; ?></div>
    </div>
  </div>

  <!-- Aviso -->
  <div class="aviso">
    ⚠️ Este relatório é um documento interno de controle e <strong>não substitui</strong> documentos fiscais oficiais.
  </div>

  <!-- Totais -->
  <div class="totais">
    <div class="total-card">
      <div class="total-label">Total entradas</div>
      <div class="total-val entrada">+<?php echo $totalEntradas; ?> un</div>
    </div>
    <div class="total-card">
      <div class="total-label">Total saídas</div>
      <div class="total-val saida">-<?php echo $totalSaidas; ?> un</div>
    </div>
    <div class="total-card">
      <div class="total-label">Registros encontrados</div>
      <div class="total-val" style="color:#1a56db;"><?php echo count($linhas); ?></div>
    </div>
  </div>

  <!-- Tabela -->
  <?php if (count($linhas) === 0): ?>
    <p style="text-align:center; color:#9ca3af; padding:32px 0;">Nenhuma movimentação no período selecionado.</p>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Data</th>
        <th>Produto</th>
        <th style="text-align:center;">Tipo</th>
        <th style="text-align:center;">Quantidade</th>
        <th>Observação</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($linhas as $row): ?>
      <tr>
        <td><?php echo date('d/m/Y H:i', strtotime($row['data'])); ?></td>
        <td><?php echo htmlspecialchars($row['nome']); ?></td>
        <td style="text-align:center;">
          <span class="badge <?php echo $row['tipo']; ?>">
            <?php echo $row['tipo'] === 'entrada' ? 'Entrada' : 'Saída'; ?>
          </span>
        </td>
        <td style="text-align:center;" class="qtd <?php echo $row['tipo']; ?>">
          <?php echo ($row['tipo'] === 'entrada' ? '+' : '-') . $row['quantidade']; ?>
        </td>
        <td><?php echo htmlspecialchars($row['observacao'] ?? '—'); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

  <div class="footer">
    Ceik Technology · Sistema de Gestão de Estoque · Documento gerado automaticamente em <?php echo $dataGeracao; ?>
  </div>

</div>
</body>
</html>
