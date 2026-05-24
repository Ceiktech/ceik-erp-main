async function enviarMensagem() {
    const input = document.getElementById('chat-input');
    const content = document.getElementById('chat-content');
    const texto = input.value.trim();

    if (!texto) return;

    // Adiciona a mensagem do usuário na tela
    content.innerHTML += `<div style="background: #0d6efd; color: white; padding: 10px; border-radius: 10px; font-size: 14px; align-self: flex-end; max-width: 85%; margin-bottom:10px;">${texto}</div>`;
    input.value = '';
    content.scrollTop = content.scrollHeight;

    // Texto de "carregando"
    const tempId = 'temp-' + Date.now();
    content.innerHTML += `<div id="${tempId}" style="color: #999; font-size: 12px; font-style: italic; margin-bottom:10px;">IA está escrevendo...</div>`;

    try {
        const formData = new FormData();
        formData.append('pergunta', texto);

        // ATENÇÃO: Verifique se o caminho abaixo está correto para o seu projeto
        const response = await fetch('processachat.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.text();
        const tempElement = document.getElementById(tempId);
        if(tempElement) tempElement.remove();

        // Adiciona a resposta da IA na tela
        content.innerHTML += `<div style="background: #f1f3f5; color: #333; padding: 10px; border-radius: 10px; font-size: 14px; align-self: flex-start; max-width: 85%; border: 1px solid #dee2e6; margin-bottom:10px;">${data}</div>`;
    } catch (e) {
        const tempElement = document.getElementById(tempId);
        if(tempElement) tempElement.innerText = "Erro ao conectar com o servidor.";
    }
    content.scrollTop = content.scrollHeight;
}

// Faz o chat abrir e fechar
function toggleChat() {
    const chatWin = document.getElementById('chat-window');
    chatWin.style.display = (chatWin.style.display === 'none' || chatWin.style.display === '') ? 'flex' : 'none';
}

// Permite enviar com a tecla ENTER
document.addEventListener('DOMContentLoaded', function() {
    const inputField = document.getElementById('chat-input');
    if(inputField) {
        inputField.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                enviarMensagem();
            }
        });
    }
});
