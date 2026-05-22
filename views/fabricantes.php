<?php
include 'db.php';

// Consulta focada em fabricantes
$query = "SELECT * FROM fabricantes ORDER BY fab_nome ASC";
$consultaFabricantes = mysqli_query($conexao, $query);

// Verifica se a consulta falhou antes de prosseguir
if (!$consultaFabricantes) {
    die("Erro na consulta: " . mysqli_error($conexao));
}

$totalFabricantes = mysqli_num_rows($consultaFabricantes);
?>

<main>
    <div class="container-fluid px-4">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; margin-bottom: 2rem;">
            <div>
                <h1 style="margin-bottom: 0.5rem;">Fabricantes</h1>
                <p style="color: #6b7280; margin: 0;">Gerenciamento de fabricantes e fornecedores</p>
            </div>
            <a href="?pagina=novoFabricante" class="btn btn-success btn-lg">
                <i class="fas fa-plus" style="margin-right: 0.5rem;"></i> Novo Fabricante
            </a>
        </div>
        
        <?php
        // Bloco de Alertas
        if (isset($_GET['cadastroOk'])) {
            echo '<div class="alert alert-success" role="alert"><i class="fas fa-check-circle"></i> <strong>Sucesso!</strong> Fabricante cadastrado.</div>';
        }
        if (isset($_GET['deletaOk'])) {
            echo '<div class="alert alert-danger" role="alert"><i class="fas fa-trash"></i> <strong>Sucesso!</strong> Fabricante removido.</div>';
        }
        if (isset($_GET['editaOk'])) {
            echo '<div class="alert alert-primary" role="alert"><i class="fas fa-edit"></i> <strong>Sucesso!</strong> Fabricante atualizado.</div>';
        }
        ?>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-industry" style="margin-right: 0.75rem;"></i>
                Lista de Fabricantes (<?php echo $totalFabricantes; ?> cadastrados)
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 50%;">Nome</th>
                                <th style="width: 35%;">Observações</th>
                                <th style="width: 15%;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>              
                            <?php while ($linha = mysqli_fetch_assoc($consultaFabricantes)): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($linha['fab_nome']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($linha['fab_obs']); ?></td>                                   
                                    <td> 
                                        <div style="display: flex; gap: 0.5rem;">
                                            <!-- Botão Editar -->
                                            <form method="post" action="?pagina=formEditaFabricante">
                                                <input name="fab_id" type="hidden" value="<?php echo $linha['fab_id']; ?>">
                                                <button type="submit" class="btn btn-primary btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </form>

                                            <!-- Botão Deletar -->
                                            <form method="post" action="deletaFabricante.php" onsubmit="return confirm('Tem certeza que deseja deletar este fabricante?');">
                                                <input name="fab_id" type="hidden" value="<?php echo $linha['fab_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Deletar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>                                   
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// Opcional: liberar resultado
mysqli_free_result($consultaFabricantes);
?>