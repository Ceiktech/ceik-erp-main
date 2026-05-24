<?php
   
    include 'db.php';

    $pro_id= $_POST['pro_id'];
    $query="select * from produtos where pro_id=$pro_id";
    $consultaProdutos= mysqli_query($conexao, $query);
?>

<main>
    <div class="container-fluid px-4">
        <br/><br/>
        <div class="row">
            <div class="col-xl-2 col-md-2">
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">Produtos</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" href="?pagina=produtos">Voltar</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-4" id="details">
            <?php while ($linha = mysqli_fetch_array($consultaProdutos)){
                echo '<h5>'.$linha['pro_fab'].'</h5><label style="margin-top:-10px;font-size:12px">Fabricante</label><br/>';
                echo '<h5>'.$linha['pro_desc'].'</h5><label style="margin-top:-10px;font-size:12px">Descrição</label><br/>';
                echo '<h5>'.$linha['pro_mod'].'</h5><label style="margin-top:-10px;font-size:12px">Modelo - Código</label><br/>';
                echo '<h5>'.$linha['pro_num_equip'].'</h5><label style="margin-top:-10px;font-size:12px">Número do equipamento</label><br/>';
                echo '<h5>'.$linha['pro_condicao'].'</h5><label style="margin-top:-10px;font-size:12px">Condição</label><br/>';
                echo '<h5>R$ '.$linha['pro_preco'].'</h5><label style="margin-top:-10px;font-size:12px">Preço</label><br/>';
                echo '<h5>'.$linha['pro_quantidade'].'</h5><label style="margin-top:-10px;font-size:12px">Quantidade</label><br/>';
                echo '<h5>'.$linha['pro_obs'].'</h5><label style="margin-top:-10px;font-size:12px">Observação</label><br/>';
                
                echo '<div style="display:flex; gap:20px">
                    <img style="width:90px" src="'.$linha['pro_foto1_path'].'">
                    <img style="width:90px" src="'.$linha['pro_foto2_path'].'">
                    <img style="width:90px" src="'.$linha['pro_foto3_path'].'">
                </div>';             
            }?>
        </div>
    </div>
</main>