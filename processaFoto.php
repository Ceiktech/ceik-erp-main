<?php

include 'db.php';

$pro_id = $_POST['pro_id'];
$pro_foto1 = $_FILES['pro_foto1'];
$pro_foto2 = $_FILES['pro_foto2'];
$pro_foto3 = $_FILES['pro_foto3'];

if ($pro_foto1) {
    
    $pasta = "arquivos/";
    $nome_arquivo1 = $pro_foto1['name'];
    $novo_nome1=uniqid();
    $default_file = "default.jpg"; // Nome do arquivo padrão

    $extensao1=strtolower(pathinfo($nome_arquivo1, PATHINFO_EXTENSION));

    if($extensao1 != "jpg" && $extensao1 != 'png' && $extensao1 != 'jpeg' && $extensao1 !=''){
        header('location:index.php?erroImg&pagina=novoProduto');
    }

    if($pro_foto1["name"] == ""){
        $path1 = $pasta . $default_file;
    }
    else{
        $path1 = $pasta . $novo_nome1. ".". $extensao1;
    }

    $sucesso1 = move_uploaded_file($pro_foto1["tmp_name"], $path1);

    if($sucesso1){
        $query = "update produtos set pro_foto1_nome='$nome_arquivo1', pro_foto1_path='$path1' where pro_id='$pro_id'";
        mysqli_query($conexao, $query);
    }
}

if ($pro_foto2) {
    
    $pasta = "arquivos/";
    $nome_arquivo2 = $pro_foto2['name'];
    $novo_nome2=uniqid();
    $default_file = "default.jpg"; // Nome do arquivo padrão

    $extensao2=strtolower(pathinfo($nome_arquivo2, PATHINFO_EXTENSION));

    if($extensao2 != "jpg" && $extensao2 != 'png' && $extensao2 != 'jpeg' && $extensao2 !=''){
        header('location:index.php?erroImg&pagina=novoProduto');
    }

    if($pro_foto2["name"] == ""){
        $path2 = $pasta . $default_file;
    }
    else{
        $path2 = $pasta . $novo_nome2. ".". $extensao2;
    }

    $sucesso2 = move_uploaded_file($pro_foto2["tmp_name"], $path2);

    if($sucesso2){
        $query = "update produtos set pro_foto2_nome='$nome_arquivo2', pro_foto2_path='$path2' where pro_id='$pro_id'";
        mysqli_query($conexao, $query);
    }
}

if ($pro_foto3) {
    
    $pasta = "arquivos/";
    $nome_arquivo3 = $pro_foto3['name'];
    $novo_nome3=uniqid();
    $default_file = "default.jpg"; // Nome do arquivo padrão

    $extensao3=strtolower(pathinfo($nome_arquivo3, PATHINFO_EXTENSION));

    if($extensao3 != "jpg" && $extensao3 != 'png' && $extensao3 != 'jpeg' && $extensao3 !=''){
        header('location:index.php?erroImg&pagina=novoProduto');
    }

    if($pro_foto3["name"] == ""){
        $path3 = $pasta . $default_file;
    }
    else{
        $path3 = $pasta . $novo_nome3. ".". $extensao3;
    }

    $sucesso3 = move_uploaded_file($pro_foto3["tmp_name"], $path3);

    if($sucesso3){
        $query = "update produtos set pro_foto3_nome='$nome_arquivo3', pro_foto3_path='$path3' where pro_id='$pro_id'";
        mysqli_query($conexao, $query);
    }
}

header('location:index.php?editaOk&pagina=produtos');

?>