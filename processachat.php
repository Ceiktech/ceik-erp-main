<?php
// 1. Inicia a sessão com segurança para sabermos quem é o usuário logado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Inclui a sua conexão com o banco de dados existente
include 'db.php'; 

/**
 * Função responsável por disparar a pergunta e o contexto para a API da Groq
 */
function perguntarAoGroq($pergunta, $contextoSistema) {
    // Insira a sua chave gerada no console da Groq aqui (Começa com gsk_)
    $apiKey = getenv('GROQ_API_KEY');
    $url = 'https://api.groq.com/openai/v1/chat/completions';

    // Monta a estrutura com engenharia de prompt (system) para guiar o comportamento da IA
    $data = [
        "model" => "llama-3.1-8b-instant", 
        "messages" => [
            [
                "role" => "system",
                "content" => "Você é o assistente inteligente integrado do sistema Easy Automation (CeikTech). 
                             Use estritamente as informações fornecidas no contexto abaixo sobre o estado atual do sistema para responder às dúvidas do usuário. 
                             Seja conciso, prestativo e profissional. Se o usuário perguntar sobre produtos ou estoque, use os dados fornecidos.
                             Nunca invente dados, nomes de produtos ou quantidades que não estejam listados abaixo.
                             
                             --- CONTEXTO ATUAL DO ERP ---
                             $contextoSistema
                             -----------------------------"
            ],
            [
                "role" => "user",
                "content" => $pergunta
            ]
        ],
        "temperature" => 0.4 // Mantém a IA focada e precisa nos dados reais, evitando "alucinações"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

    $response = curl_exec($ch);
    curl_close($ch);

    $decoded = json_decode($response, true);

    // Retorna mensagem de erro detalhada caso aconteça algo na API da Groq
    if (isset($decoded['error'])) {
        return 'Erro da Groq: ' . $decoded['error']['message'];
    }

    return $decoded['choices'][0]['message']['content'] ?? 'Desculpe, tive um problema ao processar sua pergunta.';
}

// 3. BLOCO PRINCIPAL: Executado quando o JavaScript envia a pergunta via POST
if (isset($_POST['pergunta'])) {
    
    // Captura o usuário logado na sessão do PHP
    $usuarioLogado = $_SESSION['login'] ?? 'Usuário Desconhecido';
    
    // Inicializa a string que guardará a lista de produtos puxados do banco
    $produtosTexto = "Nenhum produto cadastrado no momento.";
    
    try {
        /* * ABAIXO ESTÁ A BUSCA NO SEU BANCO DE DADOS:
         * Ajuste os nomes das colunas (nome, quantidade, preco) e da tabela (produtos) 
         * para baterem exatamente com a estrutura do seu banco real!
         */
        
        // --- SE SEU DB.PHP UTILIZA PDO (PADRÃO), MANTENHA ESTE BLOCO: ---
        if (isset($pdo)) {
            $stmt = $pdo->prepare("SELECT nome, quantidade, preco FROM produtos LIMIT 15");
            $stmt->execute();
            $listaProdutos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } 
        // --- SE SEU DB.PHP UTILIZA MYSQLI (EX: $conn), DESCOMENTE AS LINHAS ABAIXO E APAGUE AS DE CIMA: ---
        /*
        elseif (isset($conn)) {
            $result = mysqli_query($conn, "SELECT nome, quantidade, preco FROM produtos LIMIT 15");
            $listaProdutos = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        */
        
        // Se encontrou registros no banco, organiza tudo em formato de texto para a IA ler
        if (!empty($listaProdutos)) {
            $produtosTexto = "";
            foreach ($listaProdutos as $prod) {
                $produtosTexto .= "- Produto: {$prod['nome']} | Estoque Atual: {$prod['quantidade']} unidades | Preço: R$ {$prod['preco']}\n";
            }
        }
        
    } catch (Exception $e) {
        $produtosTexto = "Não foi possível carregar a lista de estoque devido a uma falha na conexão com o banco.";
    }

    // 4. Agrupa todas as informações do sistema que serão injetadas na IA
    $contextoSistema = "Dados de Acesso:\n";
    $contextoSistema .= "- Usuário logado operando o sistema: " . $usuarioLogado . "\n\n";
    $contextoSistema .= "Dados Gerais do Estoque:\n" . $produtosTexto;

    // 5. Envia a pergunta do usuário junto com a "cola" do banco para a Groq
    echo perguntarAoGroq($_POST['pergunta'], $contextoSistema);
}
?>