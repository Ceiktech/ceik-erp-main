<?php
include 'db.php';
session_start();

$nome    = trim($_POST['nome'] ?? '');
$email   = trim($_POST['email'] ?? '');
$senha   = $_POST['senha'] ?? '';
$confirma = $_POST['confirma_senha'] ?? '';

if (!$nome || !$email || !$senha) {
    header('location: index.php?tela=cadastro&erro=campos');
    exit;
}
if ($senha !== $confirma) {
    header('location: index.php?tela=cadastro&erro=senhas');
    exit;
}

// Validação de senha forte: mínimo 8 caracteres, ao menos 1 maiúscula, 1 minúscula, 1 número e 1 especial
if (strlen($senha) < 8) {
    header('location: index.php?tela=cadastro&erro=senha_fraca');
    exit;
}
if (!preg_match('/[A-Z]/', $senha)) {
    header('location: index.php?tela=cadastro&erro=senha_fraca');
    exit;
}
if (!preg_match('/[a-z]/', $senha)) {
    header('location: index.php?tela=cadastro&erro=senha_fraca');
    exit;
}
if (!preg_match('/[0-9]/', $senha)) {
    header('location: index.php?tela=cadastro&erro=senha_fraca');
    exit;
}
if (!preg_match('/[\W_]/', $senha)) {
    header('location: index.php?tela=cadastro&erro=senha_fraca');
    exit;
}

$emailSafe = mysqli_real_escape_string($conexao, $email);
$check = mysqli_query($conexao, "SELECT id FROM usuarios WHERE email='$emailSafe'");
if (mysqli_num_rows($check) > 0) {
    header('location: index.php?erro=email&tela=cadastro');
    exit;
}

$nomeSafe  = mysqli_real_escape_string($conexao, $nome);
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$insert = mysqli_query($conexao,
    "INSERT INTO usuarios (nome, email, senha, tipo_usuario)
     VALUES ('$nomeSafe', '$emailSafe', '$senhaHash', 'usuario')"
);

if ($insert) {
    header('location: index.php?cadastroOk=1');
} else {
    header('location: index.php?tela=cadastro&erro=db');
}
exit;
?>
