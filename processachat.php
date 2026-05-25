<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php'; // usa $conexao (mysqli)

function perguntarAoGroq($pergunta, $contextoSistema) {
    $apiKey = getenv('GROQ_API_KEY');

    if (!$apiKey) {
        return 'Erro: variável GROQ_API_KEY não configurada no servidor.';
    }

    $url  = 'https://api.groq.com/openai/v1/chat/completions';

    $data = [
        "model"    => "llama-3.1-8b-instant",
        "messages" => [
            [
                "role"    => "system",
                "content" => "Você é o assistente inteligente do sistema CeikTech (Easy Automation).
Use estritamente as informações fornecidas no contexto abaixo para responder.
Seja conciso, prestativo e profissional.
Nunca invente dados que não estejam listados.

--- CONTEXTO ATUAL DO ERP ---
$contextoSistema
-----------------------------"
            ],
            [
                "role"    => "user",
                "content" => $pergunta
            ]
        ],
        "temperature" => 0.4
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POST,          true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,    json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT,        20);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return 'Erro de conexão com a API: ' . $curlError;
    }

    $decoded = json_decode($response, true);

    if (isset($decoded['error'])) {
        return 'Erro da Groq: ' . $decoded['error']['message'];
    }

    return $decoded['choices'][0]['message']['content']
        ?? 'Desculpe, não consegui processar sua pergunta.';
}

if (!isset($_POST['pergunta']) || trim($_POST['pergunta']) === '') {
    echo 'Nenhuma pergunta recebida.';
    exit;
}

// Usuário logado
$usuarioLogado = $_SESSION['nome'] ?? 'Usuário';

// Busca produtos usando MySQLi ($conexao)
$produtosTexto = "Nenhum produto cadastrado.";

$result = mysqli_query($conexao,
    "SELECT nome, quantidade, qtd_minima, preco, data_vencimento
     FROM produtos
     ORDER BY nome
     LIMIT 20"
);

if ($result && mysqli_num_rows($result) > 0) {
    $produtosTexto = "";
    while ($prod = mysqli_fetch_assoc($result)) {
        $status = $prod['quantidade'] <= $prod['qtd_minima'] ? 'CRÍTICO' : 'Normal';
        $venc   = $prod['data_vencimento'] ? date('d/m/Y', strtotime($prod['data_vencimento'])) : 'sem vencimento';
        $produtosTexto .= "- {$prod['nome']} | Estoque: {$prod['quantidade']} un (mín: {$prod['qtd_minima']}) | Preço: R$ {$prod['preco']} | Vencimento: $venc | Status: $status\n";
    }
}

$contexto  = "Usuário logado: $usuarioLogado\n\n";
$contexto .= "Estoque atual:\n$produtosTexto";

echo perguntarAoGroq($_POST['pergunta'], $contexto);