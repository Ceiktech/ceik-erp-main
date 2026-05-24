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
                            <a class="nav-link" href="?pagina=alertas">
                                <div class="sb-nav-link-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                Alertas
                            </a>
                            <a class="nav-link" href="?pagina=home">
                                <div class="sb-nav-link-icon">
                                    <i class="fas fa-chart-area"></i>
                                </div>
                                Dashboard
                            </a>
                                                                        
                            <a class="nav-link" href="?pagina=produtos">
                                <div class="sb-nav-link-icon"><i class="fas fa-cube"></i></div>
                                Produtos
                            </a>

                            <a class="nav-link" href="?pagina=fabricantes">
                                <div class="sb-nav-link-icon"><i class="fas fa-cubes"></i></div>
                                Fabricantes
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Sistema</div>
                        Easy Automation
                    </div>
                </nav>
            </div>
            <a class="nav-link" href="?pagina=novaMovimentacao">
                <div class="sb-nav-link-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                Nova Movimentação
            </a>
            <?php
                }
            ?>
            <div id="layoutSidenav_content">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- WIDGET DO CHAT -->
<div id="chat-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <div id="chat-window" style="display: none; width: 320px; height: 450px; background: #fff; border-radius: 15px; box-shadow: 0 8px 24px rgba(0,0,0,0.2); flex-direction: column; overflow: hidden; margin-bottom: 15px; border: 1px solid #eee;">
        <div style="background: #0d6efd; color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 600;"><i class="fas fa-robot"></i> Assistente IA</span>
            <button onclick="toggleChat()" style="background:none; border:none; color:white; font-size: 20px; cursor:pointer;">&times;</button>
        </div>
        
        <div id="chat-content" style="flex: 1; padding: 15px; overflow-y: auto; background: #fdfdfd; display: flex; flex-direction: column; gap: 10px;">
            <div style="background: #e9ecef; padding: 10px; border-radius: 10px; font-size: 14px; align-self: flex-start; max-width: 85%;">
                Olá! Como posso ajudar com o sistema hoje?
            </div>
        </div>

        <div style="padding: 15px; border-top: 1px solid #eee; display: flex; gap: 8px;">
            <input type="text" id="chat-input" placeholder="Digite sua dúvida..." style="flex: 1; border: 1px solid #ddd; padding: 8px 12px; border-radius: 20px; outline: none; font-size: 14px;">
            <button onclick="enviarMensagem()" style="background: #0d6efd; color: white; border: none; width: 35px; height: 35px; border-radius: 50%; cursor: pointer;">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <div onclick="toggleChat()" style="width: 60px; height: 60px; background: #0d6efd; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4); transition: transform 0.2s;">
        <i class="fas fa-comment-dots" style="color: white; font-size: 26px;"></i>
    </div>
</div>

<!-- COMPORTAMENTO DO CHAT (JAVASCRIPT) -->
<script>
// Abre e fecha a janelinha do chat
function toggleChat() {
    const chatWin = document.getElementById('chat-window');
    chatWin.style.display = (chatWin.style.display === 'none' || chatWin.style.display === '') ? 'flex' : 'none';
}

// Envia a mensagem para o PHP e trata o retorno da IA
async function enviarMensagem() {
    const input = document.getElementById('chat-input');
    const content = document.getElementById('chat-content');
    const texto = input.value.trim();

    if (!texto) return;

    // Adiciona a mensagem do usuário na tela
    content.innerHTML += `<div style="background: #0d6efd; color: white; padding: 10px; border-radius: 10px; font-size: 14px; align-self: flex-end; max-width: 85%; margin-bottom:10px;">${texto}</div>`;
    input.value = '';
    content.scrollTop = content.scrollHeight;

    // Texto temporário de "carregando"
    const tempId = 'temp-' + Date.now();
    content.innerHTML += `<div id="${tempId}" style="color: #999; font-size: 12px; font-style: italic; margin-bottom:10px;">IA está escrevendo...</div>`;
    content.scrollTop = content.scrollHeight;

    try {
        const formData = new FormData();
        formData.append('pergunta', texto);

        // Caminho gerado de forma absoluta e segura via PHP
        const urlEndpoint = '<?php echo "http://" . $_SERVER['HTTP_HOST'] . "/ceik-erp/views/processachat.php"; ?>';
        
        const response = await fetch(urlEndpoint, {
            method: 'POST',
            body: formData
        });

        const data = await response.text();
        
        // Remove o indicador de carregando
        const tempElement = document.getElementById(tempId);
        if(tempElement) tempElement.remove();

        // Adiciona a resposta real da IA no chat
        content.innerHTML += `<div style="background: #f1f3f5; color: #333; padding: 10px; border-radius: 10px; font-size: 14px; align-self: flex-start; max-width: 85%; border: 1px solid #dee2e6; margin-bottom:10px;">${data}</div>`;
    } catch (e) {
        const tempElement = document.getElementById(tempId);
        if(tempElement) tempElement.innerText = "Erro ao conectar com o servidor.";
    }
    content.scrollTop = content.scrollHeight;
}

// Permite enviar as mensagens pressionando a tecla ENTER
document.addEventListener('DOMContentLoaded', function() {
    const inputField = document.getElementById('chat-input');
    if(inputField) {
        inputField.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                enviarMensagem();
            }
        });
    }
});
</script>
