<?php
$titulo = 'Dashboard';
$scripts_extra = '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>';
require __DIR__ . '/_topo.php';
$pdo = db();
$tot = $pdo->query('SELECT COUNT(*) FROM livros')->fetchColumn();
$dl = $pdo->query('SELECT COUNT(*) FROM downloads_log WHERE data >= NOW() - INTERVAL 30 DAY')->fetchColumn();
$nc = $pdo->query('SELECT COUNT(*) FROM categorias')->fetchColumn();
$np = $pdo->query('SELECT COUNT(*) FROM usuarios WHERE perfil="professor"')->fetchColumn();
$porCat = $pdo->query('SELECT c.nome, COUNT(l.id) total FROM categorias c LEFT JOIN livros l ON l.categoria_id=c.id GROUP BY c.id ORDER BY total DESC')->fetchAll();
$ultimos = $pdo->query('SELECT l.*, c.nome categoria FROM livros l LEFT JOIN categorias c ON c.id=l.categoria_id ORDER BY l.criado_em DESC LIMIT 5')->fetchAll();
$stats = [['bi-book', $tot, 'Total de Livros'], ['bi-download', $dl, 'Downloads (30 dias)'], ['bi-folder', $nc, 'Categorias'], ['bi-people', $np, 'Professores Cadastrados']];
?>
<h1 class="h3 mb-0">Dashboard</h1>
<p class="text-muted-2">Visão geral do seu acervo e atividades.</p>
<div class="row g-3 mb-3">
  <?php foreach ($stats as [$i, $n, $r]): ?>
  <div class="col-6 col-xl-3"><div class="painel stat"><i class="bi <?= $i ?>"></i><div><div class="num"><?= fmt_num($n) ?></div><small class="text-muted-2"><?= $r ?></small></div></div></div>
  <?php endforeach; ?>
</div>
<div class="row g-3">
  <div class="col-lg-6"><div class="painel h-100"><h2>Livros por Categoria</h2><canvas id="graf" height="200"></canvas></div></div>
  <div class="col-lg-6"><div class="painel h-100">
    <div class="d-flex justify-content-between"><h2>Últimos Livros Adicionados</h2><a href="<?= url('professor/livros.php') ?>" class="small">Ver todos</a></div>
    <?php foreach ($ultimos as $l): ?>
      <div class="linha-livro"><div style="width:46px"><?= capa_html($l) ?></div>
        <div class="info"><strong><?= e($l['titulo']) ?></strong><small class="text-muted-2"><?= e($l['categoria']) ?> · <?= (int)$l['paginas'] ?> págs · <?= e($l['tamanho_mb']) ?> MB</small></div>
        <small class="text-muted-2 d-none d-md-block"><?= date('d/m/Y', strtotime($l['criado_em'])) ?></small>
        <a class="btn btn-educa btn-sm" href="<?= url('baixar.php?id=' . $l['id']) ?>"><i class="bi bi-download"></i></a></div>
    <?php endforeach; if (!$ultimos) echo '<p class="text-muted-2">Ainda sem livros. Use "Adicionar Livro".</p>'; ?>
  </div></div>
  <div class="col-12"><div class="painel">
    <div class="d-flex justify-content-between"><h2>Categorias do Acervo</h2><a href="<?= url('professor/categorias.php') ?>" class="small">Gerir</a></div>
    <div class="table-responsive"><table class="table table-educa table-sm mb-0"><thead><tr><th>#</th><th>Categoria</th><th>Total de Livros</th></tr></thead><tbody>
      <?php foreach ($porCat as $i => $c): ?><tr><td><?= $i + 1 ?></td><td><?= e($c['nome']) ?></td><td><?= (int)$c['total'] ?></td></tr><?php endforeach; ?>
    </tbody></table></div>
  </div></div>
</div>
<script>
document.addEventListener('DOMContentLoaded',()=>{
  const d=<?= json_encode($porCat, JSON_UNESCAPED_UNICODE) ?>;
  new Chart(document.getElementById('graf'),{type:'bar',
    data:{labels:d.map(x=>x.nome),datasets:[{data:d.map(x=>+x.total),backgroundColor:'#6d3cf5',borderRadius:6}]},
    options:{plugins:{legend:{display:false}},scales:{x:{ticks:{color:'#9aa3d6'},grid:{display:false}},y:{beginAtZero:true,ticks:{color:'#9aa3d6',precision:0},grid:{color:'#1a2160'}}}}});
});
</script>
<?php require __DIR__ . '/_base.php'; ?>
