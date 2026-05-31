<?php
/**
 * recuperarSenha.php
 * Gera token e envia e-mail de recuperação de senha via PHPMailer (SMTP).
 *
 * ══════════════════════════════════════════════════════════════════
 *  ANTES DE USAR:
 *  1. Execute no terminal (dentro da pasta do projeto):
 *       composer require phpmailer/phpmailer
 *  2. Preencha as constantes SMTP abaixo com os dados reais.
 *  3. No Gmail: ative verificação em 2 etapas e gere uma Senha de App em
 *       https://myaccount.google.com/apppasswords
 *     Use essa senha de app em SMTP_PASS (não a senha normal da conta).
 * ══════════════════════════════════════════════════════════════════
 */

session_start();
include 'db.php';

// ─── CONFIGURAÇÃO SMTP ────────────────────────────────────────────────────────
include 'config.php';
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

// ─── Verifica se o PHPMailer foi instalado via Composer ───────────────────────
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    // PHPMailer não instalado — registra erro e retorna falha
    error_log('[Ceik] PHPMailer não encontrado. Execute: composer require phpmailer/phpmailer');
    echo json_encode(['ok' => false, 'msg' => 'Erro interno ao enviar e-mail. Contate o suporte.']);
    exit;
}

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Configurações do servidor SMTP
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;
    $mail->CharSet    = 'UTF-8';

    // Remetente e destinatário
    $mail->setFrom(SMTP_FROM, SMTP_NAME);
    $mail->addAddress($email, $nomeUsuario);

    // Conteúdo
    $mail->isHTML(true);
    $mail->Subject = 'Redefinição de senha – Ceik ERP';
    $mail->Body    = gerarCorpoEmail($nomeUsuario, $link);
    $mail->AltBody = "Olá $nomeUsuario,\n\n"
                   . "Clique no link abaixo para redefinir sua senha (válido por 1 hora):\n"
                   . "$link\n\n"
                   . "Se não solicitou, ignore este e-mail.\n\n"
                   . "Ceik Tech";

    $mail->send();

    echo json_encode(['ok' => true]);

} catch (Exception $e) {
    // Registra o erro real no log do servidor (não expõe ao usuário)
    error_log('[Ceik] Falha ao enviar e-mail de recuperação: ' . $mail->ErrorInfo);
    echo json_encode(['ok' => false, 'msg' => 'Não foi possível enviar o e-mail. Tente novamente mais tarde.']);
}

// ─────────────────────────────────────────────────────────────────────────────

function gerarCorpoEmail(string $nome, string $link): string
{
    return "
    <div style='font-family:Arial,sans-serif;max-width:480px;margin:0 auto;padding:32px 24px;'>
      <div style='font-size:20px;font-weight:700;color:#1a56db;margin-bottom:4px;'>Ceik Tech</div>
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