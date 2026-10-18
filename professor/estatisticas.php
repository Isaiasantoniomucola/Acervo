<?php
$titulo = 'Estatísticas';
$scripts_extra = '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>';
require __DIR__ . '/_topo.php';
$dias = db()->query('SELECT DATE(data) dia, COUNT(*) total FROM downloads_log WHERE data >= NOW() - INTERVAL 30 DAY GROUP BY DATE(data) ORDER BY dia')->fetchAll();
$top = db()->query('SELECT titulo, downloads FROM livros ORDER BY downloads DESC LIMIT 5')->fetchAll();
?>
<h1 class="h3 mb-3">Estatísticas</h1>
<div class="row g-3">
  <div class="col-lg-7"><div class="painel"><h2>Downloads nos últimos 30 dias</h2>
    <?php if ($dias): ?><canvas id="g1" height="180"></canvas><?php else: ?><p class="text-muted-2 mb-0">Ainda não há downloads registados neste período.</p><?php endif; ?></div></div>
  <div class="col-lg-5"><div class="painel"><h2>Livros Mais Baixados</h2>
    <?php foreach ($top as $i => $t): ?><div class="linha-livro"><strong class="me-1"><?= $i + 1 ?></strong><div class="info"><strong><?= e($t['titulo']) ?></strong><small class="text-muted-2"><?= fmt_num($t['downloads']) ?> downloads</small></div></div><?php endforeach; ?></div></div>
</div>
<script>
document.addEventListener('DOMContentLoaded',()=>{ const el=document.getElementById('g1'); if(!el) return; const d=<?= json_encode($dias) ?>;
 new Chart(el,{type:'line',data:{labels:d.map(x=>x.dia.slice(5)),datasets:[{data:d.map(x=>+x.total),borderColor:'#7c5cff',backgroundColor:'rgba(124,92,255,.2)',fill:true,tension:.3}]},
 options:{plugins:{legend:{display:false}},scales:{x:{ticks:{color:'#9aa3d6'}},y:{beginAtZero:true,ticks:{color:'#9aa3d6',precision:0},grid:{color:'#1a2160'}}}}}); });
</script>
<?php require __DIR__ . '/_base.php'; ?>
