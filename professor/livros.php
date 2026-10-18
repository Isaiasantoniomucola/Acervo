<?php
$titulo = 'Meus Livros (gestão)';
require __DIR__ . '/_topo.php';
$q = trim($_GET['q'] ?? '');
$st = db()->prepare('SELECT l.*, c.nome categoria FROM livros l LEFT JOIN categorias c ON c.id=l.categoria_id WHERE l.titulo LIKE ? OR l.autor LIKE ? ORDER BY l.criado_em DESC');
$st->execute(["%$q%", "%$q%"]); $livros = $st->fetchAll();
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h1 class="h3 mb-0">Livros do acervo</h1>
  <a class="btn btn-educa" href="<?= url('professor/livro_form.php') ?>"><i class="bi bi-plus-lg"></i> Adicionar Livro (PDF)</a>
</div>
<form class="mb-3"><input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Filtrar por título ou autor"></form>
<?php foreach ($livros as $l): ?>
  <div class="linha-livro">
    <div style="width:46px"><?= capa_html($l) ?></div>
    <div class="info"><strong><?= e($l['titulo']) ?></strong><small class="text-muted-2"><?= e($l['categoria']) ?> · <?= e($l['autor']) ?> · <?= fmt_num($l['downloads']) ?> downloads</small></div>
    <a class="btn btn-contorno btn-sm" href="<?= url('professor/livro_form.php?id=' . $l['id']) ?>" aria-label="Editar"><i class="bi bi-pencil"></i></a>
    <form method="post" action="<?= url('professor/excluir.php') ?>" data-confirmar="Excluir «<?= e($l['titulo']) ?>» e o respetivo PDF? Esta ação não pode ser desfeita.">
      <?= csrf_campo() ?><input type="hidden" name="id" value="<?= $l['id'] ?>">
      <button class="btn btn-contorno btn-sm" aria-label="Excluir"><i class="bi bi-trash"></i></button>
    </form>
  </div>
<?php endforeach; if (!$livros) echo '<div class="painel text-center">Nenhum livro encontrado.</div>'; ?>
<?php require __DIR__ . '/_base.php'; ?>
