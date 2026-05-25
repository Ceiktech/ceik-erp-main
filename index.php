<?php
session_start();
include 'db.php';
include 'header.php';

if(isset($_SESSION['login'])){
    $pagina = $_GET['pagina'] ?? 'home';
} else {
    $pagina = 'login';
}

switch ($pagina) {
    case 'home':               include 'views/home.php'; break;

    case 'produtos':           include 'views/produtos.php'; break;
    case 'novoProduto':        include 'views/novoProduto.php'; break;
    case 'formEditaProduto':   include 'views/formEditaProduto.php'; break;
    case 'detalheProduto':     include 'views/detalheProduto.php'; break;
    case 'editaFotoProduto':   include 'views/editaFotoProduto.php'; break;

    case 'alertas':            include 'views/alertas.php'; break;

    case 'movimentacoes':      include 'views/movimentacoes.php'; break;
    case 'novaMovimentacao':   include 'views/novaMovimentacao.php'; break;

    case 'preNota':            include 'views/preNota.php'; break;
    case 'relatorio':          include 'views/relatorio.php'; break;
    case 'chatia':             include 'views/chatia.php'; break;

    case 'admin':              include 'views/admin.php'; break;

    default:                   include 'views/login.php'; break;
}

include 'footer.php';