<?php
/**
 * recuperarSenha.php
 * Gera token e envia e-mail de recuperação de senha via Resend API.
 *
 * ══════════════════════════════════════════════════════════════════
 *  CONFIGURAÇÃO:
 *  1. Crie uma conta gratuita em https://resend.com
 *  2. Gere uma API Key no painel do Resend
 *  3. Adicione a variável RESEND_API_KEY no Railway (Variables)
 *     ou no config.php local
 * ══════════════════════════════════════════════════════════════════
 */

session_start();
include 'db.php';

// ─── CONFIGURAÇÃO ─────────────────────────────────────────────────────────────
if (file_exists(__DIR__ . '/config.php')) {
    include __DIR__ . '/config.php';
} else {
    define('APP_URL', getenv('APP_URL') ?: 'https://ceik-erp-production.up.railway.app');
}
$resendApiKey = getenv('RESEND_API_KEY') ?: (defined('RESEND_API_KEY') ? RESEND_API_KEY : '');
// ─────────────────────────────────────────────────────────────────────────────

header('Content-Type: application/json');

$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'msg' => 'E-mail inválido.']);
    exit;
}

$emailSafe = mysqli_real_escape_string($conexao, $email);
$res       = mysqli_query($conexao, "SELECT id, nome FROM usuarios WHERE email = '$emailSafe'");
$usuario   = mysqli_fetch_assoc($res);

// Por segurança, sempre responde "ok" mesmo se o e-mail não existir
// (evita enumeração de usuários)
if (!$usuario) {
    echo json_encode(['ok' => true]);
    exit;
}

// Garante que a tabela de tokens existe
mysqli_query($conexao, "
    CREATE TABLE IF NOT EXISTS `recuperacao_senha` (
      `id`          int(11)      NOT NULL AUTO_INCREMENT,
      `id_usuario`  int(11)      NOT NULL,
      `token`       varchar(64)  NOT NULL,
      `expira_em`   datetime     NOT NULL,
      `usado`       tinyint(1)   DEFAULT 0,
      PRIMARY KEY (`id`),
      KEY `token` (`token`),
      KEY `id_usuario` (`id_usuario`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// Invalida tokens anteriores deste usuário
$uid = intval($usuario['id']);
mysqli_query($conexao, "DELETE FROM recuperacao_senha WHERE id_usuario = $uid");

// Cria novo token (64 chars hexadecimais)
$token  = bin2hex(random_bytes(32));
$expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

mysqli_query($conexao,
    "INSERT INTO recuperacao_senha (id_usuario, token, expira_em)
     VALUES ($uid, '$token', '$expira')"
);

$link        = APP_URL . '/redefinirSenha.php?token=' . $token;
$nomeUsuario = htmlspecialchars($usuario['nome']);

// ─── Envia e-mail via Resend API ──────────────────────────────────────────────
if (empty($resendApiKey)) {
    error_log('[Ceik] RESEND_API_KEY não configurada.');
    echo json_encode(['ok' => false, 'msg' => 'Erro interno ao enviar e-mail. Contate o suporte.']);
    exit;
}

$payload = json_encode([
    'from'    => 'Ceik ERP <onboarding@resend.dev>',
    'to'      => [$email],
    'subject' => 'Redefinição de senha – Ceik ERP',
    'html'    => gerarCorpoEmail($nomeUsuario, $link),
    'text'    => "Olá $nomeUsuario,\n\nClique no link abaixo para redefinir sua senha (válido por 1 hora):\n$link\n\nSe não solicitou, ignore este e-mail.\n\nCeik ERP",
]);

$context = stream_context_create([
    'http' => [
        'method'        => 'POST',
        'header'        => "Authorization: Bearer $resendApiKey\r\nContent-Type: application/json\r\n",
        'content'       => $payload,
        'ignore_errors' => true,
    ]
]);

$response   = file_get_contents('https://api.resend.com/emails', false, $context);
$httpStatus = $http_response_header[0] ?? '';

if ($response === false || strpos($httpStatus, '200') === false) {
    error_log('[Ceik] Falha ao enviar via Resend: ' . $response);
    echo json_encode(['ok' => false, 'msg' => 'Não foi possível enviar o e-mail. Tente novamente mais tarde.']);
    exit;
}

echo json_encode(['ok' => true]);
// ─────────────────────────────────────────────────────────────────────────────

function gerarCorpoEmail(string $nome, string $link): string
{
    return "
    <div style='font-family:Arial,sans-serif;max-width:480px;margin:0 auto;padding:32px 24px;'>
      <div style='font-size:20px;font-weight:700;color:#1a56db;margin-bottom:4px;'>Ceik ERP</div>
      <div style='font-size:12px;color:#6b7280;margin-bottom:24px;border-bottom:1px solid #e5e7eb;padding-bottom:16px;'>Gestão de Estoque</div>
      <p style='font-size:15px;color:#111;margin-bottom:8px;'>Olá, <strong>$nome</strong>.</p>
      <p style='font-size:14px;color:#374151;line-height:1.6;'>Recebemos uma solicitação para redefinir a senha da sua conta. Clique no botão abaixo para criar uma nova senha:</p>
      <div style='text-align:center;margin:28px 0;'>
        <a href='$link' style='background:#1a56db;color:#fff;text-decoration:none;padding:12px 28px;border-radius:6px;font-size:14px;font-weight:600;display:inline-block;'>
          Redefinir minha senha
        </a>
      </div>
      <p style='font-size:12px;color:#6b7280;'>Este link é válido por <strong>1 hora</strong>. Caso não consiga clicar, copie e cole o endereço abaixo no seu navegador:</p>
      <p style='font-size:11px;color:#9ca3af;word-break:break-all;'>$link</p>
      <hr style='border:none;border-top:1px solid #e5e7eb;margin:24px 0;'>
      <p style='font-size:11px;color:#9ca3af;'>Se você não solicitou a redefinição de senha, ignore este e-mail — sua senha permanece a mesma.</p>
    </div>";
}
?>