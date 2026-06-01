<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Sistema</title>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
        <script src="/js/scripts_chat.js"></script>

    </head>
    <body class="sb-nav-fixed">
        <?php
            if (isset($_SESSION['login'])) {
        ?>
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3">CeikTech</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="logout.php">Sair</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <a class="nav-link <?php echo (!isset($_GET['pagina']) || $_GET['pagina']==='home') ? 'active' : ''; ?>" href="?pagina=home">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                Dashboard
                            </a>
                            <a class="nav-link <?php echo (isset($_GET['pagina']) && $_GET['pagina']==='alertas') ? 'active' : ''; ?>" href="?pagina=alertas">
                                <div class="sb-nav-link-icon"><i class="fas fa-bell"></i></div>
                                Alertas
                            </a>
                            <a class="nav-link <?php echo (isset($_GET['pagina']) && in_array($_GET['pagina'],['produtos','novoProduto','detalheProduto','formEditaProduto'])) ? 'active' : ''; ?>" href="?pagina=produtos">
                                <div class="sb-nav-link-icon"><i class="fas fa-boxes"></i></div>
                                Estoque
                            </a>
                            <a class="nav-link <?php echo (isset($_GET['pagina']) && in_array($_GET['pagina'],['movimentacoes','novaMovimentacao'])) ? 'active' : ''; ?>" href="?pagina=movimentacoes">
                                <div class="sb-nav-link-icon"><i class="fas fa-exchange-alt"></i></div>
                                Movimentações
                            </a>
                            <a class="nav-link <?php echo (isset($_GET['pagina']) && $_GET['pagina']==='preNota') ? 'active' : ''; ?>" href="?pagina=preNota">
                                <div class="sb-nav-link-icon"><i class="fas fa-file-invoice"></i></div>
                                Pré-nota
                            </a>
                            <a class="nav-link <?php echo (isset($_GET['pagina']) && $_GET['pagina']==='relatorio') ? 'active' : ''; ?>" href="?pagina=relatorio">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                                Relatório
                            </a>
                            <a class="nav-link <?php echo (isset($_GET['pagina']) && $_GET['pagina']==='chatia') ? 'active' : ''; ?>" href="?pagina=chatia">
                                <div class="sb-nav-link-icon"><i class="fas fa-robot"></i></div>
                                Assistente IA
                            </a>
                            <?php if(($_SESSION['tipo'] ?? '') === 'admin'): ?>
                            <a class="nav-link <?php echo (isset($_GET['pagina']) && $_GET['pagina']==='admin') ? 'active' : ''; ?>" href="?pagina=admin">
                                <div class="sb-nav-link-icon"><i class="fas fa-users-cog"></i></div>
                                Admin
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Sistema</div>
                        Easy Automation
                    </div>
                </nav>
            </div>

            <?php
                }
            ?>
            <div id="layoutSidenav_content">