<?php
include 'db.php';

/*
  ALERTAS AUTOMÁTICOS:
  - Estoque baixo
  - Produto vencendo
*/

$alertas = mysqli_query($conexao, "
SELECT p.id, p.nome, p.quantidade, p.qtd_minima, p.data_vencimento
FROM produtos p
WHERE 
p.quantidade <= p.qtd_minima
OR p.data_vencimento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
ORDER BY p.data_vencimento ASC
");
$totalAlertas = mysqli_num_rows($alertas);
?>

<main>
<div class="container-fluid px-4">

<h1 class="mt-4"><i class="fas fa-bell" style="margin-right: 0.75rem;"></i>Alertas do Sistema</h1>
<p style="color: #6b7280; margin-bottom: 2rem;">Produtos com estoque baixo ou vencimento próximo</p>

<?php if($totalAlertas > 0) { ?>
<div class="card mb-4">
<div class="card-header" style="background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);">
    <i class="fas fa-exclamation-triangle" style="margin-right: 0.75rem;"></i>
    Alertas Ativados (<?php echo $totalAlertas; ?> produto<?php echo $totalAlertas > 1 ? 's' : ''; ?>)
</div>

<div class="card-body">

<table class="table table-bordered table-striped">

<thead>
<tr>
    <th style="width: 30%;">Produto</th>
    <th style="width: 15%;">Quantidade</th>
    <th style="width: 15%;">Mínimo</th>
    <th style="width: 20%;">Vencimento</th>
    <th style="width: 20%;">Status</th>
</tr>
</thead>

<tbody>

<?php while($linha = mysqli_fetch_assoc($alertas)) { 
    $dataVenc = strtotime($linha['data_vencimento']);
    $dataAtual = time();
    $diasRestantes = ($dataVenc - $dataAtual) / 86400;
?>

<tr style="background-color: rgba(245, 158, 11, 0.05);">

<td><strong><?php echo $linha['nome']; ?></strong></td>
<td>
    <span style="background-color: <?php echo ($linha['quantidade'] <= $linha['qtd_minima'] ? '#fee2e2' : '#f0fdf4'); ?>; color: <?php echo ($linha['quantidade'] <= $linha['qtd_minima'] ? '#991b1b' : '#166534'); ?>; padding: 0.5rem 0.75rem; border-radius: 6px; font-weight: 600;">
        <?php echo $linha['quantidade']; ?>
    </span>
</td>
<td><?php echo $linha['qtd_minima']; ?></td>
<td><?php echo $linha['data_vencimento']; ?></td>

<td>

<?php
$alerts = [];
if($linha['quantidade'] <= $linha['qtd_minima']){
    $alerts[] = "<span style='background-color: #fee2e2; color: #991b1b; padding: 0.4rem 0.8rem; border-radius: 6px; font-weight: 600; display: inline-block; margin-right: 0.5rem;'><i class='fas fa-exclamation-circle' style='margin-right: 0.3rem;'></i>Estoque Baixo</span>";
}

if($diasRestantes < 0){
    $alerts[] = "<span style='background-color: #fee2e2; color: #991b1b; padding: 0.4rem 0.8rem; border-radius: 6px; font-weight: 600; display: inline-block; margin-right: 0.5rem;'><i class='fas fa-times-circle' style='margin-right: 0.3rem;'></i>Vencido</span>";
} elseif($linha['data_vencimento'] <= date('Y-m-d', strtotime('+30 days'))){
    $alerts[] = "<span style='background-color: #fef3c7; color: #92400e; padding: 0.4rem 0.8rem; border-radius: 6px; font-weight: 600; display: inline-block; margin-right: 0.5rem;'><i class='fas fa-clock' style='margin-right: 0.3rem;'></i>" . round($diasRestantes) . " dias</span>";
}

echo implode('', $alerts);
?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>
</div>
<?php } else { ?>
<div class="card mb-4">
    <div class="card-body" style="text-align: center; padding: 3rem;">
        <i class="fas fa-check-circle" style="font-size: 3rem; color: #10b981; margin-bottom: 1rem;"></i>
        <h4 style="color: #10b981;">Tudo está em ordem!</h4>
        <p style="color: #6b7280;">Não há alertas de estoque baixo ou produtos vencendo.</p>
    </div>
</div>
<?php } ?>

</div>
</main>