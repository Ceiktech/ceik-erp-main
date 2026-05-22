<div class="container-fluid px-4">

<h1 class="mt-4">Novo Produto</h1>

<div class="row">
    <div class="col-md-8">
        <div class="card p-4">

            <form action="insereProduto.php" method="post" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-cube" style="margin-right: 0.5rem;"></i>Nome do Produto</label>
                    <input type="text" name="nome" class="form-control" required placeholder="Ex: Produto XYZ">
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-barcode" style="margin-right: 0.5rem;"></i>Código de Barras</label>
                    <input type="text" name="codigo_barras" class="form-control" placeholder="Ex: 1234567890">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-tags" style="margin-right: 0.5rem;"></i>Categoria</label>
                            <input type="text" name="categoria" class="form-control" placeholder="Ex: Eletrônicos">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-dollar-sign" style="margin-right: 0.5rem;"></i>Preço (R$)</label>
                            <input type="text" name="preco" class="form-control" required placeholder="Ex: 99.99">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-boxes" style="margin-right: 0.5rem;"></i>Quantidade</label>
                            <input type="number" name="quantidade" class="form-control" required placeholder="Ex: 10">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-exclamation-triangle" style="margin-right: 0.5rem;"></i>Quantidade Mínima</label>
                            <input type="number" name="qtd_minima" class="form-control" placeholder="Ex: 5">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-calendar" style="margin-right: 0.5rem;"></i>Data de Vencimento</label>
                    <input type="date" name="data_vencimento" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-image" style="margin-right: 0.5rem;"></i>Foto do Produto</label>
                    <input type="file" name="foto" class="form-control">
                    <small style="color: #6b7280;">Aceita: JPG, PNG, GIF (Máx 5MB)</small>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-success btn-lg" style="flex: 1;">
                        <i class="fas fa-save" style="margin-right: 0.5rem;"></i>Cadastrar Produto
                    </button>
                    <a href="?pagina=produtos" class="btn btn-secondary btn-lg" style="flex: 1; text-decoration: none;">
                        <i class="fas fa-arrow-left" style="margin-right: 0.5rem;"></i>Voltar
                    </a>
                </div>

            </form>

        </div>
    </div>
</div>
</div>