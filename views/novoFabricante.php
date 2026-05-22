<div class="formulario">
    
    <h3 class="text-center font-weight-light my-4">Fabricante</h3>

    <form method="post" action="insereFabricante.php" autocomplete="off" enctype="multipart/form-data">
        <div class="form-floating mb-3">
            <input class="form-control" type="text" placeholder="Nome" name="fab_nome" required maxlength="222"/>
            <label>Nome</label>
        </div>

        <div class="form-floating mb-3">
            <input class="form-control" type="text" placeholder="Observação" name="fab_obs"  maxlength="222"/>
            <label>Observação</label>
        </div>

        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
            <!--<a class="small" href="password.html">Forgot Password?</a>-->
            <button type="post" class="btn btn-primary" href="index.html">Cadastrar</button>
        </div>
    </form>
</div>