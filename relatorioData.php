<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['login'])) {
    echo json_encode(['erro' => 'nao_autenticado']);
    exit;
}

$uid    = intval($_SESSION['id']);
$isAdm  = ($_SESSION['tipo'] ?? '') === 'admin';

$de      = $_GET['de']      ?? date('Y-m-01');
$ate     = $_GET['ate']     ?? date('Y-m-d');
$produto = $_GET['produto'] ?? '';

// Filtra pelo usuário logado (admin vê tudo)
$whereUser = $isAdm ? '' : "AND p.id_usuario = $uid";

$where = "WHERE DATE(m.data) BETWEEN '$de' AND '$ate' $whereUser";
if ($produto) $where .= " AND m.id_produto = " . intval($produto);

$movs = mysqli_query($conexao,
  "SELECT m.*, p.nome
   FROM movimentacoes m
   INNER JOIN produtos p ON p.id = m.id_produto
   $where
   ORDER BY m.data DESC"
);

$lista = [];
$totalEntradas = 0;
$totalSaidas   = 0;

while ($row = mysqli_fetch_assoc($movs)) {
  if ($row['tipo'] === 'entrada') $totalEntradas += $row['quantidade'];
  else                            $totalSaidas   += $row['quantidade'];
  $lista[] = [
    'nome'       => $row['nome'],
    'tipo'       => $row['tipo'],
    'quantidade' => $row['quantidade'],
    'data'       => date('d/m/Y', strtotime($row['data'])),
    'observacao' => $row['observacao'] ?? ''
  ];
}

echo json_encode([
  'total_entradas'  => $totalEntradas,
  'total_saidas'    => $totalSaidas,
  'movimentacoes'   => $lista
]);