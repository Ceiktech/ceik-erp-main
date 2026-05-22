<?php

include 'db.php';

    $query="select * from fabricantes";
    $consultaFabricantes= mysqli_query($conexao, $query);

    if(isset($_POST['fab_id'])){

        while ($linha = mysqli_fetch_array($consultaFabricantes)) {
        if ($linha['fab_id'] == $_POST['fab_id']) {
?>



<div class="formulario">
    
    <h3 class="text-center font-weight-light my-4">Editar fabricante</h3>

    <form method="post" action="processaEditaFabricante.php" autocomplete="off" enctype="multipart/form-data">
        <input value="<?php echo $linha['fab_id'] ?>" name="fab_id" style="display: none;">
        <div class="form-floating mb-3">
            <input value="<?php echo $linha['fab_nome'] ?>" class="form-control" type="text" placeholder="Nome" name="fab_nome" required maxlength="222"/>
            <label>Nome</label>
        </div>

        <div class="form-floating mb-3">
            <input value="<?php echo $linha['fab_obs'] ?>" class="form-control" type="text" placeholder="Observação" name="fab_obs"  maxlength="222"/>
            <label>Observação</label>
        </div>

        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
            <!--<a class="small" href="password.html">Forgot Password?</a>-->
            <button type="post" class="btn btn-primary" href="index.html">Editar</button>
        </div>
    </form>
</div>

<?php
}}}