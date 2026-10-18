<?php
$titulo = 'Meus Livros'; $pagina = 'meus';
require_once __DIR__ . '/includes/funcoes.php'; exigir_login();
require __DIR__ . '/includes/header.php';
$aba = ($_GET['aba'] ?? '') === 'favoritos' ? 'favoritos' : 'meus';
$sql = 'SELECT l.*, c.nome AS categoria FROM biblioteca b JOIN livros l ON l.id=b.livro_id LEFT JOIN categorias c ON c.id=l.categoria_id WHERE b.usuario_id=?' . ($aba === 'favoritos' ? ' AND b.favorito=1' : '') . ' ORDER BY b.criado_em DESC';
$st = db()->prepare($sql); $st->execute([usuario()['id']]); $livros = $st->fetchAll();
?>
<h1 class="h3">Meus Livros</h1>
<p class="text-muted-2">Seus livros salvos e favoritos.</p>
<ul class="nav nav-tabs mb-3">
  <li class="nav-item"><a class="nav-link <?= $aba === 'meus' ? 'active' : '' ?>" href="?aba=meus"><i class="bi bi-bookmark"></i> Meus Livros</a></li>
  <li class="nav-item"><a class="nav-link <?= $aba === 'favoritos' ? 'active' : '' ?>" href="?aba=favoritos"><i class="bi bi-heart"></i> Favoritos</a></li>
</ul>
<?php if (!$livros): ?>
  <div class="painel text-center py-5"><p>Nada por aqui ainda. Abra um livro e toque em guardar ou favoritar.</p><a class="btn btn-educa" href="<?= url('livros.php') ?>">Explorar livros</a></div>
<?php endif; foreach ($livros as $l): ?>
  <div class="linha-livro">
    <div style="width:46px"><?= capa_html($l) ?></div>
    <div class="info"><strong><?= e($l['titulo']) ?></strong><small class="text-muted-2"><?= e($l['categoria']) ?> · <?= (int)$l['paginas'] ?> páginas · <?= e($l['tamanho_mb']) ?> MB</small></div>
    <a class="btn btn-educa btn-sm" href="<?= url('ler.php?id=' . $l['id']) ?>">Ler</a>
  </div>
<?php endforeach; require __DIR__ . '/includes/footer.php'; ?>
