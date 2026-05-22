<?php
include 'db.php';

$query = "SELECT * FROM produtos ORDER BY id DESC";
$consultaProdutos = mysqli_query($conexao, $query);
$totalProdutos = mysqli_num_rows($consultaProdutos);
?>

<main>
<div class="container-fluid px-4">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="margin-bottom: 0.5rem;">Produtos</h1>
            <p style="color: #6b7280; margin: 0;">Gerenciamento completo de produtos</p>
        </div>
        <a href="?pagina=novoProduto" class="btn btn-success btn-lg">
            <i class="fas fa-plus" style="margin-right: 0.5rem;"></i> Novo Produto
        </a>
    </div>

    <?php
    if(isset($_GET['cadastroOk'])){
        echo '<div class="alert alert-success"><i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>Produto cadastrado com sucesso!</div>';
    }

    if(isset($_GET['editaOk'])){
        echo '<div class="alert alert-primary"><i class="fas fa-edit" style="margin-right: 0.5rem;"></i>Produto atualizado com sucesso!</div>';
    }

    if(isset($_GET['deletaOk'])){
        echo '<div class="alert alert-danger"><i class="fas fa-trash" style="margin-right: 0.5rem;"></i>Produto excluído com sucesso!</div>';
    }
    ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-cube" style="margin-right: 0.75rem;"></i>
            Lista de Produtos (<?php echo $totalProdutos; ?> produtos)
        </div>

        <div class="card-body">

            <table class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 8%;">Foto</th>
                        <th style="width: 20%;">Nome</th>
                        <th style="width: 12%;">Categoria</th>
                        <th style="width: 10%;">Preço</th>
                        <th style="width: 10%;">Qtd</th>
                        <th style="width: 12%;">Vencimento</th>
                        <th style="width: 13%;">Ações</th>
                    </tr>
                </thead>

                <tbody>

                <?php while($linha = mysqli_fetch_assoc($consultaProdutos)){ 
                    $dataVenc = $linha['data_vencimento'];
                    $dataVencTime = strtotime($dataVenc);
                    $dataAtual = time();
                    $diasRestantes = ($dataVencTime - $dataAtual) / 86400;
                    
                    $classAlerta = '';
                    if($diasRestantes < 0) {
                        $classAlerta = 'style="background-color: rgba(239, 68, 68, 0.1);"';
                    } elseif($diasRestantes < 30) {
                        $classAlerta = 'style="background-color: rgba(245, 158, 11, 0.1);"';
                    }
                ?>

                    <tr <?php echo $classAlerta; ?>>
                        <td><strong>#<?php echo $linha['id']; ?></strong></td>

                        <td>
                            <?php if($linha['foto'] != ''){ ?>
                                <img src="<?php echo $linha['foto']; ?>" width="60" style="border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <?php } else { ?>
                                <div style="width: 60px; height: 60px; background-color: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php } ?>
                        </td>

                        <td><strong><?php echo $linha['nome']; ?></strong></td>

                        <td><span style="background-color: #e0e7ff; color: #6366f1; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.9rem;"><?php echo $linha['categoria']; ?></span></td>

                        <td>
                            <strong style="color: #10b981;">R$ <?php echo number_format($linha['preco'],2,',','.'); ?></strong>
                        </td>

                        <td>
                            <span style="background-color: #f0fdf4; color: #059669; padding: 0.5rem 0.75rem; border-radius: 6px; font-weight: 600;"><?php echo $linha['quantidade']; ?></span>
                        </td>

                        <td>
                            <?php 
                            if($diasRestantes < 0) {
                                echo '<span style="color: #ef4444; font-weight: 600;"><i class="fas fa-exclamation-triangle" style="margin-right: 0.3rem;"></i>Vencido</span>';
                            } elseif($diasRestantes < 30) {
                                echo '<span style="color: #f59e0b; font-weight: 600;"><i class="fas fa-clock" style="margin-right: 0.3rem;"></i>' . round($diasRestantes) . ' dias</span>';
                            } else {
                                echo '<span style="color: #6b7280;">' . $linha['data_vencimento'] . '</span>';
                            }
                            ?>
                        </td>

                        <td>

                            <form action="?pagina=formEditaProduto" method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $linha['id']; ?>">
                                <button class="btn btn-primary btn-sm" title="Editar produto">
                                    <i class="fas fa-edit" style="margin-right: 0.3rem;"></i>Editar
                                </button>
                            </form>

                            <form action="deletaProduto.php" method="post" style="display:inline;" onclick="return confirm('Tem certeza que deseja deletar este produto?');">
                                <input type="hidden" name="id" value="<?php echo $linha['id']; ?>">
                                <button class="btn btn-danger btn-sm" title="Deletar produto">
                                    <i class="fas fa-trash" style="margin-right: 0.3rem;"></i>Deletar
                                </button>
                            </form>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>
    </div>

</div>
</main>