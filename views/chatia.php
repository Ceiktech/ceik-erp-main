<?php

function perguntarAoGemini($pergunta) {
    $apiKey = 'AIzaSyDeXklnplG2nmskLnjamPhblRyMOkmULa0'; 
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey;

    $data = [
        "contents" => [
            ["parts" => [["text" => $pergunta]]]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Garante que funcione no seu computador local (localhost)

    $response = curl_exec($ch);
    curl_close($ch);

    $decoded = json_decode($response, true);
    return $decoded['candidates'][0]['content']['parts'][0]['text'] ?? 'Erro ao obter resposta.';
}

if (isset($_POST['pergunta']) && !empty(trim($_POST['pergunta']))) {
    $perguntaDoUsuario = $_POST['pergunta'];
    $resposta = perguntarAoGemini($perguntaDoUsuario);
    
    echo $resposta; 
} else {
    echo "Nenhuma pergunta foi enviada pelo chat.";
}
?>