<head>
  <link href="css/styles.css" rel="stylesheet" />
</head>

<div id="layoutAuthentication">
  <div class="login-card">

    <!-- PAINEL LOGIN -->
    <div id="painel-login">
      <div class="login-title">Ceik Technology</div>
      <div class="login-sub">Gestão de estoque simplificada</div>

      <form method="post" action="login.php">
        <div class="mb-3">
          <label class="form-label">E-mail</label>
          <input class="form-control" type="email" placeholder="seu@email.com" name="email" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Senha</label>
          <input class="form-control" type="password" placeholder="••••••••" name="senha" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2 mt-1">Entrar</button>
      </form>

      <div class="login-divider">ou</div>

      <button onclick="mostrarPainel('cadastro')" class="btn btn-outline-secondary w-100 py-2">Criar nova conta</button>

      <div class="text-center mt-3">
        <a href="#" onclick="mostrarPainel('recuperar'); return false;"
           style="font-size:0.82rem; color: var(--primary-color); text-decoration: none;">
          Esqueci minha senha
        </a>
      </div>

      <?php if(isset($_GET['erro'])): ?>
      <div class="alert alert-danger mt-3" style="font-size:0.85rem; border-radius: var(--radius-sm);">
        <i class="fas fa-exclamation-circle me-2"></i>Email ou senha inválidos.
      </div>
      <?php endif; ?>

      <?php if(isset($_GET['cadastroOk'])): ?>
      <div class="alert alert-success mt-3" style="font-size:0.85rem; border-radius: var(--radius-sm);">
        <i class="fas fa-check-circle me-2"></i>Conta criada! Faça login.
      </div>
      <?php endif; ?>

      <?php if(isset($_GET['senhaRedefinida'])): ?>
      <div class="alert alert-success mt-3" style="font-size:0.85rem; border-radius: var(--radius-sm);">
        <i class="fas fa-check-circle me-2"></i>Senha redefinida com sucesso! Faça login.
      </div>
      <?php endif; ?>

      <?php if(isset($_GET['erro']) && $_GET['erro'] === 'email'): ?>
      <div class="alert alert-warning mt-3" style="font-size:0.85rem; border-radius: var(--radius-sm);">
        <i class="fas fa-exclamation-triangle me-2"></i>Este e-mail já está cadastrado.
      </div>
      <?php endif; ?>
    </div>

    <!-- PAINEL CADASTRO -->
    <div id="painel-cadastro" style="display:none;">
      <div style="display:flex; align-items:center; gap:10px; margin-bottom:1.25rem;">
        <button onclick="mostrarPainel('login')" style="background:none;border:none;color:var(--primary-color);font-size:1.2rem;cursor:pointer;padding:0;">←</button>
        <div class="login-title" style="text-align:left; font-size:1.2rem; margin:0;">Criar conta</div>
      </div>

      <form method="post" action="cadastro.php" onsubmit="return validarCadastro()">
        <div class="mb-3">
          <label class="form-label">Nome completo *</label>
          <input class="form-control" type="text" name="nome" placeholder="Seu nome" required>
        </div>
        <div class="mb-3">
          <label class="form-label">E-mail *</label>
          <input class="form-control" type="email" name="email" placeholder="seu@email.com" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Senha *</label>
          <input class="form-control" type="password" name="senha" id="cad-senha" placeholder="Mínimo 8 caracteres" required minlength="8" oninput="verificarForca()">
        </div>
          <div style="font-size:0.75rem; margin-top:5px; display:flex; gap:10px; flex-wrap:wrap; color:var(--gray-400);">
            <span id="ind-len">✓ 8+ chars</span>
            <span id="ind-mai">✓ Maiúscula</span>
            <span id="ind-num">✓ Número</span>
            <span id="ind-esp">✓ Especial (!@#...)</span>
          </div>
        <div class="mb-4">
          <label class="form-label">Confirmar senha *</label>
          <input class="form-control" type="password" name="confirma_senha" id="cad-confirma" placeholder="Repita a senha" required>
        </div>

        <div id="erro-cadastro" class="alert alert-danger mb-3" style="display:none; font-size:0.85rem; border-radius: var(--radius-sm);"></div>
        <?php if(isset($_GET['tela']) && $_GET['tela']==='cadastro' && isset($_GET['erro'])): ?>
          <?php
            $erroMap = [
              'campos'      => 'Preencha todos os campos obrigatórios.',
              'senhas'      => 'As senhas não coincidem.',
              'senha_fraca' => 'Senha fraca. Use 8+ caracteres, maiúscula, número e caractere especial (!@#$%...).',
              'email'       => 'Este e-mail já está cadastrado.',
              'db'          => 'Erro interno. Tente novamente.',
            ];
            $msgErr = $erroMap[$_GET['erro']] ?? 'Erro no cadastro.';
          ?>
          <div class="alert alert-danger mb-3" style="font-size:0.85rem; border-radius: var(--radius-sm);">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $msgErr; ?>
          </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary w-100 py-2">Cadastrar</button>
        <p style="font-size:0.75rem; color:var(--gray-400); text-align:center; margin-top:10px;">
          Ao cadastrar, você concorda com os Termos de Uso
        </p>
      </form>
    </div>

    <!-- PAINEL RECUPERAR SENHA -->
    <div id="painel-recuperar" style="display:none;">
      <div style="display:flex; align-items:center; gap:10px; margin-bottom:1.25rem;">
        <button onclick="mostrarPainel('login')" style="background:none;border:none;color:var(--primary-color);font-size:1.2rem;cursor:pointer;padding:0;">←</button>
        <div>
          <div class="login-title" style="text-align:left; font-size:1.2rem; margin:0;">Recuperar senha</div>
          <div style="font-size:0.82rem; color:var(--gray-400); margin-top:2px;">Digite seu e-mail para receber as instruções</div>
        </div>
      </div>

      <div id="recuperar-form">
        <div class="mb-4">
          <label class="form-label">E-mail cadastrado</label>
          <input class="form-control" type="email" id="email-recuperar" placeholder="seu@email.com">
        </div>
        <button onclick="enviarRecuperacao()" class="btn btn-primary w-100 py-2">Enviar instruções</button>
      </div>

      <div id="recuperar-ok" style="display:none; text-align:center; padding:1rem 0;">
        <div style="font-size:2.5rem; margin-bottom:0.75rem;">📬</div>
        <div style="font-weight:600; color:var(--gray-800); margin-bottom:6px;">Verifique seu e-mail</div>
        <div style="font-size:0.85rem; color:var(--gray-500);">Se o e-mail estiver cadastrado, você receberá as instruções em breve.</div>
        <button onclick="mostrarPainel('login')" class="btn btn-outline-secondary w-100 py-2 mt-4">Voltar ao login</button>
      </div>
    </div>

  </div>
</div>

<script>
function mostrarPainel(painel) {
  document.getElementById('painel-login').style.display     = painel === 'login'    ? 'block' : 'none';
  document.getElementById('painel-cadastro').style.display  = painel === 'cadastro' ? 'block' : 'none';
  document.getElementById('painel-recuperar').style.display = painel === 'recuperar'? 'block' : 'none';
}

function validarCadastro() {
  const senha    = document.getElementById('cad-senha').value;
  const confirma = document.getElementById('cad-confirma').value;
  const erro     = document.getElementById('erro-cadastro');

  if (senha !== confirma) {
    erro.textContent = 'As senhas não coincidem.';
    erro.style.display = 'block';
    return false;
  }
  // Requisitos de senha forte
  const requisitos = [
    { ok: senha.length >= 8,       msg: 'mínimo de 8 caracteres' },
    { ok: /[A-Z]/.test(senha),     msg: 'ao menos uma letra maiúscula' },
    { ok: /[a-z]/.test(senha),     msg: 'ao menos uma letra minúscula' },
    { ok: /[0-9]/.test(senha),     msg: 'ao menos um número' },
    { ok: /[\W_]/.test(senha),     msg: 'ao menos um caractere especial (!@#$%...)' },
  ];
  const falhou = requisitos.find(r => !r.ok);
  if (falhou) {
    erro.textContent = 'Senha fraca: exige ' + falhou.msg + '.';
    erro.style.display = 'block';
    return false;
  }
  erro.style.display = 'none';
  return true;
}

// Atualiza indicadores de força de senha em tempo real
function verificarForca() {
  const senha = document.getElementById('cad-senha').value;
  const ids = { 'ind-len': senha.length >= 8, 'ind-mai': /[A-Z]/.test(senha),
                'ind-num': /[0-9]/.test(senha), 'ind-esp': /[\W_]/.test(senha) };
  for (const [id, ok] of Object.entries(ids)) {
    const el = document.getElementById(id);
    if (el) { el.style.color = ok ? 'var(--success-color,#16a34a)' : 'var(--gray-400,#9ca3af)'; }
  }
}

function enviarRecuperacao() {
  const email = document.getElementById('email-recuperar').value.trim();
  if (!email) { alert('Digite seu e-mail.'); return; }

  const btn = document.querySelector('#recuperar-form button');
  btn.disabled = true;
  btn.textContent = 'Enviando...';

  fetch('recuperarSenha.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'email=' + encodeURIComponent(email)
  })
  .then(r => r.json())
  .then(() => {
    document.getElementById('recuperar-form').style.display = 'none';
    document.getElementById('recuperar-ok').style.display   = 'block';
  })
  .catch(() => {
    btn.disabled = false;
    btn.textContent = 'Enviar instruções';
    alert('Erro ao enviar. Tente novamente.');
  });
}

<?php if(isset($_GET['tela'])): ?>
  mostrarPainel('<?php echo htmlspecialchars($_GET['tela']); ?>');
<?php endif; ?>
</script>