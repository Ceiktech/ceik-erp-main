<head>
  <link href="css/styles.css" rel="stylesheet" />
</head>

<div id="layoutAuthentication">
  <div class="login-card">

    <div class="login-title">Ceik Technology</div>
    <div class="login-sub">Gestão de estoque simplificada</div>

    <form method="post" action="login.php">

      <div class="mb-3">
        <label class="form-label">E-mail</label>
        <input class="form-control" type="email" placeholder="seu@email.com" name="email" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Senha</label>
        <input class="form-control" type="password" placeholder="••••••••" name="senha" required>
      </div>

      <button type="submit" class="btn btn-primary w-100 py-2 mt-1">Entrar</button>

    </form>

    <div class="login-divider">ou</div>

    <button class="btn btn-outline-secondary w-100 py-2">Criar nova conta</button>

    <div class="text-center mt-3">
      <a href="#" style="font-size:0.82rem; color: var(--primary-color); text-decoration: none;">Esqueci minha senha</a>
    </div>

    <?php if(isset($_GET['erro'])){ ?>
    <div class="alert alert-danger mt-3" style="font-size:0.85rem; border-radius: var(--radius-sm);">
      <i class="fas fa-exclamation-circle me-2"></i>Email ou senha inválidos.
    </div>
    <?php } ?>

  </div>
</div>
