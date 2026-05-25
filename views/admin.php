<?php
// Só admin acessa
if (($_SESSION['tipo'] ?? '') !== 'admin') {
    header('location: index.php?pagina=home');
    exit;
}
include 'db.php';

// Ações do admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        $uid = intval($_POST['uid']);
        if ($_POST['acao'] === 'deletar' && $uid !== intval($_SESSION['id'])) {
            mysqli_query($conexao, "DELETE FROM usuarios WHERE id = $uid");
        }
        if ($_POST['acao'] === 'tipo') {
            $novoTipo = $_POST['tipo'] === 'admin' ? 'admin' : 'usuario';
            mysqli_query($conexao, "UPDATE usuarios SET tipo_usuario = '$novoTipo' WHERE id = $uid");
        }
    }
}

$usuarios = mysqli_query($conexao, "SELECT id, nome, email, tipo_usuario, criado_em FROM usuarios ORDER BY id DESC");
?>

<main>
<div class="container-fluid px-3 px-md-4">

  <div class="page-header mt-3">
    <div>
      <h1 class="fs-5">Painel Administrativo</h1>
      <p class="d-none d-sm-block">Gerenciamento de usuários do sistema</p>
    </div>
  </div>

  <?php if(isset($_GET['ok'])): ?>
  <div class="alert alert-success mb-3" style="font-size:0.875rem;border-radius:var(--radius-sm);">
    <i class="fas fa-check-circle me-2"></i>Ação realizada com sucesso.
  </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-header">Usuários cadastrados</div>
    <div class="card-body p-0">
      <div style="overflow-x:auto;">
      <table class="table mb-0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Tipo</th>
            <th>Cadastro</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
        <?php while($u = mysqli_fetch_assoc($usuarios)): ?>
          <tr>
            <td><?php echo $u['id']; ?></td>
            <td style="text-align:left !important;"><?php echo htmlspecialchars($u['nome']); ?></td>
            <td style="text-align:left !important;font-size:0.82rem;"><?php echo htmlspecialchars($u['email']); ?></td>
            <td>
              <span class="badge-<?php echo $u['tipo_usuario'] === 'admin' ? 'critico' : 'normal'; ?>">
                <?php echo $u['tipo_usuario']; ?>
              </span>
            </td>
            <td style="font-size:0.78rem;color:var(--gray-500);">
              <?php echo $u['criado_em'] ? date('d/m/Y', strtotime($u['criado_em'])) : '—'; ?>
            </td>
            <td>
              <div class="d-flex gap-1 justify-content-center flex-wrap">
                <!-- Mudar tipo -->
                <form method="post" action="?pagina=admin" style="display:inline;">
                  <input type="hidden" name="acao" value="tipo">
                  <input type="hidden" name="uid" value="<?php echo $u['id']; ?>">
                  <input type="hidden" name="tipo" value="<?php echo $u['tipo_usuario'] === 'admin' ? 'usuario' : 'admin'; ?>">
                  <button class="btn btn-outline-secondary btn-sm" title="Mudar para <?php echo $u['tipo_usuario'] === 'admin' ? 'usuário' : 'admin'; ?>">
                    <i class="fas fa-<?php echo $u['tipo_usuario'] === 'admin' ? 'user-minus' : 'user-shield'; ?>"></i>
                  </button>
                </form>
                <!-- Deletar (não pode deletar a si mesmo) -->
                <?php if($u['id'] != $_SESSION['id']): ?>
                <form method="post" action="?pagina=admin" style="display:inline;"
                      onsubmit="return confirm('Deletar o usuário <?php echo htmlspecialchars($u['nome']); ?> e todos os seus produtos?');">
                  <input type="hidden" name="acao" value="deletar">
                  <input type="hidden" name="uid" value="<?php echo $u['id']; ?>">
                  <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
      </div>
    </div>
  </div>

  <!-- Resumo de produtos por usuário -->
  <div class="card mt-4">
    <div class="card-header">Produtos por usuário</div>
    <div class="card-body p-0">
      <div style="overflow-x:auto;">
      <?php
      $resumo = mysqli_query($conexao,
        "SELECT u.nome, u.email, COUNT(p.id) as total_produtos
         FROM usuarios u
         LEFT JOIN produtos p ON p.id_usuario = u.id
         GROUP BY u.id ORDER BY total_produtos DESC"
      );
      ?>
      <table class="table mb-0">
        <thead><tr><th>Usuário</th><th>E-mail</th><th>Produtos</th></tr></thead>
        <tbody>
        <?php while($r = mysqli_fetch_assoc($resumo)): ?>
          <tr>
            <td style="text-align:left !important;"><?php echo htmlspecialchars($r['nome']); ?></td>
            <td style="text-align:left !important;font-size:0.82rem;"><?php echo htmlspecialchars($r['email']); ?></td>
            <td><strong><?php echo $r['total_produtos']; ?></strong></td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
      </div>
    </div>
  </div>

</div>
</main>