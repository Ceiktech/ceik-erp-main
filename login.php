<?php
include 'db.php';
session_start();

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (!$email || !$senha) {
    header('location: index.php?erro=1');
    exit;
}

$emailSafe = mysqli_real_escape_string($conexao, $email);
$consulta  = mysqli_query($conexao, "SELECT * FROM usuarios WHERE email='$emailSafe'");

if (mysqli_num_rows($consulta) == 1) {
    $usuario = mysqli_fetch_assoc($consulta);

    // Aceita senha com hash (novos usuários) ou texto puro (usuários antigos)
    $senhaValida = password_verify($senha, $usuario['senha']) || ($senha === $usuario['senha']);

    if ($senhaValida) {
        $_SESSION['login'] = true;
        $_SESSION['id']    = $usuario['id'];
        $_SESSION['nome']  = $usuario['nome'];
        $_SESSION['tipo']  = $usuario['tipo_usuario'];
        header('location: index.php');
    } else {
        header('location: index.php?erro=1');
    }
} else {
    header('location: index.php?erro=1');
}
exit;
?>