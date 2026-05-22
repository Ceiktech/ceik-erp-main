<head>  
    <link rel="stylesheet" href="css/estilo.css?v=<?php echo time(); ?>">
</head>
<div id="layoutAuthentication">
    <div id="layoutAuthentication_content">
        <main>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 col-sm-10"> 
                        <div class="card shadow-lg border-0 rounded-lg mt-5">

                            <div class="card-header">
                                <h3 class="text-center font-weight-light my-4">
                                    Ceik Technology
                                </h3>
                            </div>

                            <div class="card-body">

                                <form method="post" action="login.php">

                                    <div class="form-floating mb-3">
                                        <input class="form-control"
                                               type="email"
                                               placeholder="Email"
                                               name="email"
                                               required>
                                        <label>Email</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input class="form-control"
                                               type="password"
                                               placeholder="Senha"
                                               name="senha"
                                               required>
                                        <label>Senha</label>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                        <button type="submit" class="btn btn-primary w-100">
                                            Entrar
                                        </button>
                                    </div>

                                </form>

                            </div>

                        </div>

                        <?php
                        if(isset($_GET['erro'])){
                            echo '
                            <div class="alert alert-danger mt-3">
                                Email ou senha inválidos.
                            </div>';
                        }
                        ?>

                    </div>
                </div>
            </div>
        </main>
    </div>
</div>