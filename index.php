<?php

#iniciar sessão
session_start();

#base de dados
include 'db.php';

#cabeçalho
include 'header.php';

#conteúdo da página
if(isset($_SESSION['login'])){
	if(isset($_GET['pagina'])){
		$pagina = $_GET['pagina'];
	}
	else{
		$pagina = 'home';
	}
}
else{
	$pagina = 'login';
}

switch ($pagina) {
	
	case 'home': include 'views/produtos.php'; break;

	case 'produtos': include 'views/produtos.php'; break;
	case 'novoProduto': include 'views/novoProduto.php'; break;
	case 'formEditaProduto': include 'views/formEditaProduto.php'; break;
	case 'detalheProduto': include 'views/detalheProduto.php'; break;
	case 'editaFotoProduto': include 'views/editaFotoProduto.php'; break;

	case 'novoFabricante': include 'views/novoFabricante.php'; break;
	case 'formEditaFabricante': include 'views/formEditaFabricante.php'; break;
	case 'alertas': include 'views/alertas.php'; break;
	case 'novaMovimentacao': include 'views/novaMovimentacao.php'; break;
	default: include 'views/login.php'; break;
}

# Rodapé
include 'footer.php';