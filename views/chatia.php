<main>
<div class="container-fluid px-3 px-md-4">

  <div class="page-header mt-3">
    <div>
      <h1 class="fs-5">Assistente IA</h1>
      <p class="d-none d-sm-block">Tire dúvidas sobre o seu estoque</p>
    </div>
  </div>

  <div class="row justify-content-start">
    <div class="col-12 col-md-8 col-lg-6">
      <div class="card" style="height: 70vh; display: flex; flex-direction: column;">

        <!-- Cabeçalho do chat -->
        <div class="card-header d-flex align-items-center gap-2">
          <div style="width:32px;height:32px;background:var(--primary-color);border-radius:50%;display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-robot" style="color:#fff;font-size:14px;"></i>
          </div>
          <div>
            <div style="font-weight:600;font-size:0.9rem;">Assistente Ceik</div>
            <div style="font-size:0.72rem;color:var(--success-color);">● Online</div>
          </div>
        </div>

        <!-- Área de mensagens -->
        <div id="chat-content" style="flex:1;overflow-y:auto;padding:1rem;display:flex;flex-direction:column;gap:8px;">
          <!-- Mensagem inicial -->
          <div style="background:var(--primary-light);color:var(--primary-text);padding:10px 14px;border-radius:12px 12px 12px 4px;font-size:0.875rem;max-width:85%;align-self:flex-start;">
            Olá, <strong><?php echo htmlspecialchars($_SESSION['nome'] ?? 'usuário'); ?></strong>! 👋<br>
            Sou o assistente da Ceik Technology. Como posso te ajudar hoje?
          </div>

          <!-- Sugestões rápidas -->
          <div id="sugestoes" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;">
            <button onclick="perguntaRapida('Quais produtos estão com estoque crítico?')"
              style="background:var(--white);border:1px solid var(--gray-200);border-radius:20px;padding:5px 12px;font-size:0.78rem;color:var(--gray-600);cursor:pointer;">
              Estoque crítico
            </button>
            <button onclick="perguntaRapida('Quais produtos vencem em breve?')"
              style="background:var(--white);border:1px solid var(--gray-200);border-radius:20px;padding:5px 12px;font-size:0.78rem;color:var(--gray-600);cursor:pointer;">
              Próximos vencimentos
            </button>
            <button onclick="perguntaRapida('Como registro uma entrada de produto?')"
              style="background:var(--white);border:1px solid var(--gray-200);border-radius:20px;padding:5px 12px;font-size:0.78rem;color:var(--gray-600);cursor:pointer;">
              Como cadastrar produto?
            </button>
          </div>
        </div>

        <!-- Input -->
        <div style="padding:0.75rem 1rem;border-top:1px solid var(--gray-200);display:flex;gap:8px;align-items:center;">
          <input
            type="text"
            id="chat-input"
            class="form-control"
            placeholder="Digite sua dúvida..."
            style="border-radius:20px;font-size:0.875rem;"
          >
          <button onclick="enviarMensagemChat()"
            style="background:var(--primary-color);border:none;border-radius:50%;width:38px;height:38px;flex-shrink:0;display:flex;align-items:center;justify-content:center;cursor:pointer;">
            <i class="fas fa-paper-plane" style="color:#fff;font-size:13px;"></i>
          </button>
        </div>

      </div>
    </div>
  </div>

</div>
</main>

<script>
async function enviarMensagemChat() {
    const input   = document.getElementById('chat-input');
    const content = document.getElementById('chat-content');
    const texto   = input.value.trim();
    if (!texto) return;

    // Esconde sugestões após primeira mensagem
    const sug = document.getElementById('sugestoes');
    if (sug) sug.remove();

    // Mensagem do usuário
    content.innerHTML += `
      <div style="background:var(--primary-color);color:#fff;padding:10px 14px;border-radius:12px 12px 4px 12px;font-size:0.875rem;max-width:85%;align-self:flex-end;">
        ${escapeHtml(texto)}
      </div>`;
    input.value = '';
    content.scrollTop = content.scrollHeight;

    // Indicador de digitação
    const tempId = 'temp-' + Date.now();
    content.innerHTML += `
      <div id="${tempId}" style="color:var(--gray-400);font-size:0.78rem;font-style:italic;padding:4px 0;">
        IA está escrevendo...
      </div>`;
    content.scrollTop = content.scrollHeight;

    try {
        const formData = new FormData();
        formData.append('pergunta', texto);

        const response = await fetch('processachat.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.text();
        const temp = document.getElementById(tempId);
        if (temp) temp.remove();

        content.innerHTML += `
          <div style="background:var(--gray-50);color:var(--gray-800);padding:10px 14px;border-radius:12px 12px 12px 4px;font-size:0.875rem;max-width:85%;align-self:flex-start;border:1px solid var(--gray-200);">
            ${data.replace(/\n/g, '<br>')}
          </div>`;
    } catch (e) {
        const temp = document.getElementById(tempId);
        if (temp) temp.innerText = 'Erro ao conectar com o servidor.';
    }

    content.scrollTop = content.scrollHeight;
}

function perguntaRapida(texto) {
    document.getElementById('chat-input').value = texto;
    enviarMensagemChat();
}

function escapeHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// Enviar com Enter
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('chat-input');
    if (input) {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') enviarMensagemChat();
        });
    }
});
</script>