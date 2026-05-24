<?php
include 'db.php';
session_start();

$email = $_POST['email'];
$senha = $_POST['senha'];

$query = "SELECT * FROM usuarios WHERE email='$email'";
$consulta = mysqli_query($conexao,$query);

if(mysqli_num_rows($consulta) == 1){

    $usuario = mysqli_fetch_assoc($consulta);

    if($senha == $usuario['senha']){

        $_SESSION['login'] = true;
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['tipo'] = $usuario['tipo_usuario'];

        header('location:index.php');

    }else{
        header('location:index.php?erro=1');
    }

}else{
    header('location:index.php?erro=1');
}
?>
