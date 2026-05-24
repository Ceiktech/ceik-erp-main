<?php
   
    include 'db.php';

    $pro_id= $_POST['pro_id'];
    $query="select * from produtos where pro_id=$pro_id";
    $consultaProdutos= mysqli_query($conexao, $query);
?>

<h3 class="text-center font-weight-light my-4">Fotos</h3>

<div style="padding:30px">
    <div class="card mb-4" id="details">
        <?php while ($linha = mysqli_fetch_array($consultaProdutos)){
            echo '<div style="display:flex; gap:20px">
                <img style="width:90px" src="'.$linha['pro_foto1_path'].'">
                <img style="width:90px" src="'.$linha['pro_foto2_path'].'">
                <img style="width:90px" src="'.$linha['pro_foto3_path'].'">
            </div>';             
        }?>
    </div>

    <form method="post" action="processaFoto.php" autocomplete="off" enctype="multipart/form-data">
        <input value="<?php echo $pro_id;?>" name="pro_id" style="display:none">

        <p id="avisoArquivo">Dar preferência por fotos pequenas e leves <i class="bi bi-cloud-upload-fill"></i></p>

        <div class="form-floating mb-3">
            <small>Foto 1</small>
            <input type="file" name="pro_foto1"/>
        </div>
        <div class="form-floating mb-3">
            <small>Foto 2</small>
            <input type="file" name="pro_foto2"/>
        </div>
        <div class="form-floating mb-3">
            <small>Foto 3</small>
            <input type="file" name="pro_foto3"/>
        </div>

        <?php
        if (isset($_GET['erroImg'])) {
            echo '
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="erroLogin">
                <strong>Erro no arquivo</strong> Formatos suportados: jpg, png e jpeg.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            ';
        }
        ?>

        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
            <!--<a class="small" href="password.html">Forgot Password?</a>-->
            <button type="post" class="btn btn-primary" href="index.html">Alterar</button>
        </div>
    </form>

    </div>
<br><br>