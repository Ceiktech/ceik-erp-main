<?php
include 'db.php';
session_start();

$body = json_decode(file_get_contents('php://input'), true);
if (!$body) { http_response_code(400); exit; }

$id_usuario  = $_SESSION['id'] ?? 1;
$valor_total = floatval($body['valor_total'] ?? 0);
$dados_json  = mysqli_real_escape_string($conexao, json_encode($body['dados'] ?? []));

mysqli_query($conexao,
  "INSERT INTO pre_notas (id_usuario, valor_total, dados_json)
   VALUES ($id_usuario, $valor_total, '$dados_json')"
);

echo json_encode(['ok' => true, 'id' => mysqli_insert_id($conexao)]);