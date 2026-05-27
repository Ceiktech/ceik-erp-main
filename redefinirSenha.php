<?php
/**
 * redefinirSenha.php
 * Página onde o usuário redefine a senha após clicar no link do e-mail.
 */
session_start();
include 'db.php';

$token = trim($_GET['token'] ?? '');

// ─── Processamento do formulário de nova senha ────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token    = trim($_POST['token'] ?? '');
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirma  = $_POST['confirma_senha'] ?? '';

    $erro = '';

    if ($novaSenha !== $confirma) {
        $erro = 'As senhas não coincidem.';
    } elseif (strlen($novaSenha) < 8
           || !preg_match('/[A-Z]/', $novaSenha)
           || !preg_match('/[a-z]/', $novaSenha)
           || !preg_match('/[0-9]/', $novaSenha)
           || !preg_match('/[\W_]/', $novaSenha)) {
        $erro = 'A senha não atende aos requisitos de segurança.';
    } else {
        $tokenSafe = mysqli_real_escape_string($conexao, $token);
        $res = mysqli_query($conexao,
            "SELECT r.id, r.id_usuario
             FROM recuperacao_senha r
             WHERE r.token = '$tokenSafe'
               AND r.usado = 0
               AND r.expira_em > NOW()"
        );
        $rec = mysqli_fetch_assoc($res);

        if (!$rec) {
            $erro = 'Este link é inválido ou já expirou. Solicite uma nova recuperação.';
        } else {
            $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $uid  = intval($rec['id_usuario']);
            $rid  = intval($rec['id']);
            mysqli_query($conexao, "UPDATE usuarios SET senha = '$hash' WHERE id = $uid");
            mysqli_query($conexao, "UPDATE recuperacao_senha SET usado = 1 WHERE id = $rid");
            header('location: index.php?senhaRedefinida=1');
            exit;
        }
    }
    // Mostra o formulário novamente com o erro
}
// ─────────────────────────────────────────────────────────────────────────────

// Valida o token para exibir o formulário
$tokenValido = false;
if ($token) {
    $tokenSafe = mysqli_real_escape_string($conexao, $token);
    $res = mysqli_query($conexao,
        "SELECT id FROM recuperacao_senha
         WHERE token = '$tokenSafe' AND usado = 0 AND expira_em > NOW()"
    );
    $tokenValido = mysqli_num_rows($res) > 0;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Redefinir Senha – Ceik Technology</title>
<link href="css/styles.css" rel="stylesheet">
<style>
  body { background: var(--bg-secondary, #f3f4f6); display: flex; align-items: center; justify-content: center; min-height: 100vh; }
  .box { background: #fff; border-radius: 10px; box-shadow: 0 2px 16px rgba(0,0,0,.08); padding: 36px 32px; max-width: 380px; width: 100%; }
  .titulo { font-size: 1.2rem; font-weight: 700; color: var(--primary-color, #1a56db); margin-bottom: 4px; }
  .sub    { font-size: 0.82rem; color: #6b7280; margin-bottom: 1.5rem; }
  .form-label { font-size: 0.87rem; font-weight: 500; margin-bottom: 4px; display: block; }
  .form-control { width: 100%; padding: 9px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; margin-bottom: 12px; }
  .form-control:focus { outline: 2px solid var(--primary-color, #1a56db); border-color: transparent; }
  .btn-primary { background: var(--primary-color, #1a56db); color: #fff; border: none; width: 100%; padding: 10px; border-radius: 6px; font-size: 0.95rem; font-weight: 600; cursor: pointer; margin-top: 4px; }
  .btn-primary:hover { opacity: 0.9; }
  .alert-danger  { background: #fee2e2; color: #991b1b; border-radius: 6px; padding: 10px 14px; font-size: 0.85rem; margin-bottom: 12px; }
  .hint { font-size: 0.78rem; color: #9ca3af; margin: -8px 0 12px; }
  .hint span.ok  { color: #16a34a; }
  .hint span.nok { color: #dc2626; }
</style>
</head>
<body>
<div class="box">
  <div class="titulo">Ceik Technology</div>

  <?php if (!$tokenValido): ?>
    <div class="sub">Link inválido ou expirado</div>
    <p style="font-size:0.9rem;color:#374151;margin-bottom:20px;">Este link de recuperação é inválido ou já expirou. Links são válidos por <strong>1 hora</strong>.</p>
    <a href="index.php" style="display:block;text-align:center;color:var(--primary-color,#1a56db);font-size:0.9rem;">← Voltar ao login e solicitar novo link</a>

  <?php else: ?>
    <div class="sub">Crie uma nova senha para sua conta.</div>

    <?php if (!empty($erro)): ?>
      <div class="alert-danger">⚠️ <?php echo htmlspecialchars($erro); ?></div>
    <?php endif; ?>

    <form method="post" onsubmit="return validar()">
      <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

      <label class="form-label">Nova senha *</label>
      <input class="form-control" type="password" name="nova_senha" id="nova" required
             placeholder="Mínimo 8 caracteres" oninput="verificarRequisitos()">
      <div class="hint" id="hint">
        <span id="r-len">✗ 8+ caracteres</span> &nbsp;
        <span id="r-mai">✗ Maiúscula</span> &nbsp;
        <span id="r-num">✗ Número</span> &nbsp;
        <span id="r-esp">✗ Caractere especial</span>
      </div>

      <label class="form-label">Confirmar nova senha *</label>
      <input class="form-control" type="password" name="confirma_senha" id="confirma" required
             placeholder="Repita a senha">

      <button type="submit" class="btn-primary">Redefinir senha</button>
    </form>
  <?php endif; ?>
</div>

<script>
function verificarRequisitos() {
  const v = document.getElementById('nova').value;
  set('r-len', v.length >= 8);
  set('r-mai', /[A-Z]/.test(v));
  set('r-num', /[0-9]/.test(v));
  set('r-esp', /[\W_]/.test(v));
}
function set(id, ok) {
  const el = document.getElementById(id);
  const texto = { 'r-len':'8+ caracteres','r-mai':'Maiúscula','r-num':'Número','r-esp':'Caractere especial' };
  el.textContent = (ok ? '✓ ' : '✗ ') + texto[id];
  el.className = ok ? 'ok' : 'nok';
}
function validar() {
  const nova     = document.getElementById('nova').value;
  const confirma = document.getElementById('confirma').value;
  if (nova !== confirma) { alert('As senhas não coincidem.'); return false; }
  if (nova.length < 8 || !/[A-Z]/.test(nova) || !/[0-9]/.test(nova) || !/[\W_]/.test(nova)) {
    alert('A senha não atende a todos os requisitos de segurança.');
    return false;
  }
  return true;
}
</script>
</body>
</html>
