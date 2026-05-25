<?php
include 'db.php';

$de      = $_GET['de']      ?? date('Y-m-01');
$ate     = $_GET['ate']     ?? date('Y-m-d');
$produto = $_GET['produto'] ?? '';

$where = "WHERE m.data BETWEEN '$de 00:00:00' AND '$ate 23:59:59'";
if ($produto) $where .= " AND m.id_produto = " . intval($produto);

$movs = mysqli_query($conexao,
  "SELECT m.*, p.nome
   FROM movimentacoes m
   INNER JOIN produtos p ON p.id = m.id_produto
   $where
   ORDER BY m.data DESC"
);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="relatorio_' . date('Y-m-d') . '.csv"');

$out = fopen('php://output', 'w');
fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
fputcsv($out, ['Data', 'Produto', 'Tipo', 'Quantidade', 'Observação'], ';');

while ($row = mysqli_fetch_assoc($movs)) {
  fputcsv($out, [
    date('d/m/Y H:i', strtotime($row['data'])),
    $row['nome'],
    $row['tipo'] === 'entrada' ? 'Entrada' : 'Saída',
    $row['quantidade'],
    $row['observacao'] ?? ''
  ], ';');
}
fclose($out);