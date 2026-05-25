<main>
<div class="container-fluid px-3 px-md-4">

  <div class="page-header mt-3">
    <div>
      <h1 class="fs-5">Assistente IA</h1>
      <p class="d-none d-sm-block">Tire dúvidas sobre o seu estoque</p>
    </div>
  </div>

  <div class="row justify-content-start">
    <div class="col-12 col-md-9 col-lg-7">
      <div class="card" style="height:72vh;display:flex;flex-direction:column;">

        <!-- Cabeçalho -->
        <div class="card-header d-flex align-items-center gap-2">
          <div style="width:32px;height:32px;background:var(--primary-color);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fas fa-robot" style="color:#fff;font-size:14px;"></i>
          </div>
          <div>
            <div style="font-weight:600;font-size:0.9rem;">Assistente Ceik</div>
            <div style="font-size:0.72rem;color:var(--success-color);">● Online</div>
          </div>
        </div>

        <!-- Mensagens — IDs únicos com prefixo "ia-" -->
        <div id="ia-content" style="flex:1;overflow-y:auto;padding:1rem;display:flex;flex-direction:column;gap:8px;">
          <div style="background:var(--primary-light);color:var(--primary-text);padding:10px 14px;border-radius:12px 12px 12px 4px;font-size:0.875rem;max-width:85%;align-self:flex-start;">
            Olá, <strong><?php echo htmlspecialchars($_SESSION['nome'] ?? 'usuário'); ?></strong>! 👋<br>
            Sou o assistente da Ceik Technology. Como posso te ajudar hoje?
          </div>

          <!-- Sugestões rápidas -->
          <div id="ia-sugestoes" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;">
            <button onclick="iaPerguntaRapida('Quais produtos estão com estoque crítico?')"
              style="background:var(--white);border:1px solid var(--gray-200);border-radius:20px;padding:5px 12px;font-size:0.78rem;color:var(--gray-600);cursor:pointer;">
              Estoque crítico
            </button>
            <button onclick="iaPerguntaRapida('Quais produtos vencem em breve?')"
              style="background:var(--white);border:1px solid var(--gray-200);border-radius:20px;padding:5px 12px;font-size:0.78rem;color:var(--gray-600);cursor:pointer;">
              Próximos vencimentos
            </button>
            <button onclick="iaPerguntaRapida('Qual produto tem menor estoque?')"
              style="background:var(--white);border:1px solid var(--gray-200);border-radius:20px;padding:5px 12px;font-size:0.78rem;color:var(--gray-600);cursor:pointer;">
              Menor estoque
            </button>
            <button onclick="iaPerguntaRapida('Como registro uma entrada de produto?')"
              style="background:var(--white);border:1px solid var(--gray-200);border-radius:20px;padding:5px 12px;font-size:0.78rem;color:var(--gray-600);cursor:pointer;">
              Como cadastrar produto?
            </button>
          </div>
        </div>

        <!-- Input — ID único "ia-input" -->
        <div style="padding:0.75rem 1rem;border-top:1px solid var(--gray-200);display:flex;gap:8px;align-items:center;">
          <input
            type="text"
            id="ia-input"
            class="form-control"
            placeholder="Digite sua dúvida..."
            style="border-radius:20px;font-size:0.875rem;"
          >
          <button onclick="iaEnviar()"
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
async function iaEnviar() {
    const input   = document.getElementById('ia-input');
    const content = document.getElementById('ia-content');
    const texto   = input.value.trim();
    if (!texto) return;

    // Remove sugestões
    const sug = document.getElementById('ia-sugestoes');
    if (sug) sug.remove();

    // Mensagem do usuário
    content.innerHTML += `
      <div style="background:var(--primary-color);color:#fff;padding:10px 14px;border-radius:12px 12px 4px 12px;font-size:0.875rem;max-width:85%;align-self:flex-end;">
        ${iaEscape(texto)}
      </div>`;
    input.value = '';
    content.scrollTop = content.scrollHeight;

    // Indicador de digitação
    const tempId = 'ia-temp-' + Date.now();
    content.innerHTML += `
      <div id="${tempId}" style="color:var(--gray-400);font-size:0.78rem;font-style:italic;padding:4px 0;">
        IA está escrevendo...
      </div>`;
    content.scrollTop = content.scrollHeight;

    try {
        const form = new FormData();
        form.append('pergunta', texto);

        const res  = await fetch('processachat.php', { method: 'POST', body: form });
        const data = await res.text();

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

function iaPerguntaRapida(texto) {
    document.getElementById('ia-input').value = texto;
    iaEnviar();
}

function iaEscape(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// Enter para enviar
document.getElementById('ia-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') iaEnviar();
});
</script>