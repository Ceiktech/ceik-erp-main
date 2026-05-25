// Widget flutuante de chat — usa IDs com prefixo "widget-" para não conflitar com chatia.php

function toggleChat() {
    const win = document.getElementById('chat-window');
    win.style.display = (win.style.display === 'none' || win.style.display === '') ? 'flex' : 'none';
}

async function enviarMensagem() {
    const input   = document.getElementById('widget-input');
    const content = document.getElementById('widget-content');
    const texto   = input.value.trim();
    if (!texto) return;

    content.innerHTML += `<div style="background:#0d6efd;color:white;padding:10px;border-radius:10px;font-size:14px;align-self:flex-end;max-width:85%;margin-bottom:10px;">${texto}</div>`;
    input.value = '';
    content.scrollTop = content.scrollHeight;

    const tempId = 'w-temp-' + Date.now();
    content.innerHTML += `<div id="${tempId}" style="color:#999;font-size:12px;font-style:italic;margin-bottom:10px;">IA está escrevendo...</div>`;

    try {
        const form = new FormData();
        form.append('pergunta', texto);

        const res  = await fetch('processachat.php', { method: 'POST', body: form });
        const data = await res.text();

        const temp = document.getElementById(tempId);
        if (temp) temp.remove();

        content.innerHTML += `<div style="background:#f1f3f5;color:#333;padding:10px;border-radius:10px;font-size:14px;align-self:flex-start;max-width:85%;border:1px solid #dee2e6;margin-bottom:10px;">${data.replace(/\n/g,'<br>')}</div>`;
    } catch (e) {
        const temp = document.getElementById(tempId);
        if (temp) temp.innerText = 'Erro ao conectar com o servidor.';
    }
    content.scrollTop = content.scrollHeight;
}

document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('widget-input');
    if (input) {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') enviarMensagem();
        });
    }
});